<?php

namespace App\Http\Controllers;
use Log;
use App\User;
use App\Attribute;
use App\AttributeValue;
use App\AvailableLayer;
use App\Context;
use App\ContextType;
use App\File;
use App\Geodata;
use App\Literature;
use App\Helpers;
use Phaza\LaravelPostgis\Geometries\Geometry;
use Phaza\LaravelPostgis\Geometries\Point;
use \DB;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AnalysisController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    // GET

    public function getAnalyses() {
        return response()->json(
            DB::table('stored_queries')
                ->get()
        );
    }

    public function getStringAttributes($id) {
        $ct = ContextType::with(['attributes'])
            ->whereHas('attributes', function($query) {
                $query->where('datatype', 'string');
            })
            ->find($id);
        if(isset($ct->attributes)) {
            $matchingAttrs = [];
            foreach($ct->attributes as $attr) {
                if($attr->datatype == 'string') {
                    $matchingAttrs[] = $attr;
                }
            }
            return response()->json($matchingAttrs);
        }
        return response()->json([]);
    }

    public function getAttributeOfContextTypeLayer($id, $aid) {
        $contexts = Context::with(['attributes'])
            ->whereNotNull('geodata_id')
            ->where('context_type_id', $id)
            ->get();
        $attributes = [];
        foreach($contexts as $c) {
            $attr = null;
            foreach($c->attributes as $a) {
                if($a->id == $aid) {
                    $attr = $a;
                    break;
                }
            }
            if(isset($attr)) {
                $attributes[$c->id] = $attr;
            }
        }
        return response()->json($attributes);
    }

    // POST

    public function export($type = 'csv', Request $request) {
        // TODO validate
        $origin = $request->input('origin');
        $filters = json_decode($request->input('filters', '[]'));
        $columns = json_decode($request->input('columns', '[]'));
        $orders = json_decode($request->input('orders', '[]'));
        $limit = json_decode($request->input('limit', '{}'));
        $splits = json_decode($request->input('splits', '[]'));
        $simple = Helpers::parseBoolean($request->input('simple', false));
        $distinct = Helpers::parseBoolean($request->input('distinct', false));
        $all = Helpers::parseBoolean($request->input('all', true));
        if($all) {
            // if we want all entries, remove limit before filtering
            $limit = json_decode('{}');
        }
        $result = $this->requestToQuery($origin, $filters, $columns, $orders, $limit, $splits, $simple, $distinct);
        switch($type) {
            case 'csv':
                $suffix = '.csv';
                break;
            case 'json':
                $suffix = '.json';
                break;
            default:
                return response()->json([
                    'error' => "The type $type is not supported."
                ]);
        }
        $dt = date('dmYHis');
        $tmpFile = '/tmp/export-'.$dt.$suffix;
        $handle = fopen($tmpFile, 'w');
        $firstRow = true;
        $exceptions = [];
        if($simple) {
            switch($origin) {
                case 'attribute_values':
                    $exceptions = [
                        'attribute',
                        'context',
                        'context_val',
                        'thesaurus_val'
                    ];
                    break;
                case 'contexts':
                    $exceptions = [
                        'child_contexts',
                        'context_type',
                        'geodata',
                        'root_context',
                        'literatures',
                        'attributes',
                        'files'
                    ];
                case 'files':
                    $exceptions = [
                        'contexts',
                        // 'tags'
                    ];
                    break;
                case 'geodata':
                    $exceptions = [
                        'context'
                    ];
                    break;
                case 'literature':
                    $exceptions = [
                        'contexts'
                    ];
                    break;
            }
        } else {
        }
        $splitIndex = 0;
        switch($type) {
            case 'csv':
                foreach($result['rows'] as $row) {
                    $curr = [];
                    $header = [];
                    foreach($row->getAttributes() as $k => $a) {
                        // TODO skip ambiguous attributes for now
                        if(in_array($k, $exceptions)) continue;
                        if($firstRow) {
                            $header[] = $k;
                        }
                        $curr[] = $a;
                    }
                    if(isset($result['splits'])) {
                        foreach($result['splits'] as $k => $s) {
                            if($firstRow) {
                                $header[] = $k;
                            }
                            $curr[] = $s['values'][$splitIndex];
                        }
                    }
                    if($firstRow) {
                        fputcsv($handle, $header);
                        $firstRow = false;
                    }
                    fputcsv($handle, $curr);
                    $splitIndex++;
                }
                // get raw parsed content
                $content = file_get_contents($tmpFile);
                // delete tmp file
                fclose($handle);
                unlink($tmpFile);
                break;
            case 'json':
                $content = json_encode($result['rows'], JSON_PRETTY_PRINT);
                break;
            default:
                return response()->json([
                    'error' => "The type $type is not supported."
                ]);
        }

        return response(base64_encode($content));
    }

    public function filterContexts(Request $request) {
        // TODO validate
        $origin = $request->input('origin');
        $filters = json_decode($request->input('filters', '[]'));
        $columns = json_decode($request->input('columns', '[]'));
        $orders = json_decode($request->input('orders', '[]'));
        $limit = json_decode($request->input('limit', '{}'));
        $splits = json_decode($request->input('splits', '[]'));
        $simple = Helpers::parseBoolean($request->input('simple', false));
        $distinct = Helpers::parseBoolean($request->input('distinct', false));
        $result = $this->requestToQuery($origin, $filters, $columns, $orders, $limit, $splits, $simple, $distinct);
        return response()->json($result);
    }

    // PATCH

    // PUT

    // DELETE

    // OTHER FUNCTIONS

    private function requestToQuery($origin, $filters, $columns, $orders, $limit, $splits, $simple, $distinct) {
        $query = $this->filter($origin, $filters, $columns, $orders, $distinct, $simple);

        $count = $query->count();
        $this->addLimit($query, $limit);
        $rows = $query->get();

        if($simple) {
            $splitArray = $this->addRelationSplits($rows, $splits);
        }

        if($origin === 'contexts') {
            foreach($rows as $r) {
                if(isset($r->geodata)) {
                    $r->geodata['wkt'] = $r->geodata->geom->toWKT();
                }
            }
        } else if($origin === 'geodata') {
            foreach($rows as &$r) {
                $r->geowkt = $r->geom->toWKT();
            }
        }

        $result = [
            'count' => $count,
            'rows' => $rows
        ];
        if(!$simple) {
            $result['query'] = $this->cleanSql($query->toSql());
        } else {
            $result['splits'] = $splitArray;
        }
        return $result;
    }

    private function addLimit($query, $limit) {
        if(!empty($limit)) {
            if(isset($limit->from)) {
                $query->offset($limit->from);
            }
            if(isset($limit->amount)) {
                $query->limit($limit->amount);
            }
        }
    }

    private function addRelationSplits($rows, $splits) {
        if(empty($splits)) return null;

        $splitArray = [];
        foreach($splits as $s) {
            $curr = [];
            foreach($rows as $row) {
                $rel = $row->{$s->relation};
                $value = null;
                // check if $rel is a collection
                if(is_a($rel, 'Illuminate\Database\Eloquent\Collection')) {
                    // if so, loop over all items
                    foreach($rel->all() as $r) {
                        if($r->{$s->column} == $s->value) {
                            $value = null;
                            if(isset($r->pivot->str_val)) {
                                $value = $r->pivot->str_val;
                            } else if(isset($r->pivot->int_val)) {
                                $value = $r->pivot->int_val;
                            } else if(isset($r->pivot->dbl_val)) {
                                $value = $r->pivot->dbl_val;
                            } else if(isset($r->pivot->thesaurus_val)) {
                                $value = $r->pivot->thesaurus_val;
                            } else if(isset($r->pivot->dt_val)) {
                                $value = $r->pivot->dt_val;
                            } else if(isset($r->pivot->geography_val)) {
                                $value = [
                                    'wkt' => Geometry::fromWKB($r->pivot->geography_val)->toWKT(),
                                    'geom' => Geometry::fromWKB($r->pivot->geography_val)
                                ];
                            }
                            $type = $r->datatype;
                        }
                    }
                    // otherwise, should be object
                } else if(is_object($rel)) {
                    if($rel->{$s->column} == $s->value) {
                        $value = null;
                        if(isset($rel->pivot->str_val)) {
                            $value = $rel->pivot->str_val;
                        } else if(isset($rel->pivot->int_val)) {
                            $value = $rel->pivot->int_val;
                        } else if(isset($rel->pivot->dbl_val)) {
                            $value = $rel->pivot->dbl_val;
                        } else if(isset($rel->pivot->thesaurus_val)) {
                            $value = $rel->pivot->thesaurus_val;
                        } else if(isset($rel->pivot->dt_val)) {
                            $value = $rel->pivot->dt_val;
                        } else if(isset($r->pivot->geography_val)) {
                            $value = [
                                'wkt' => Geometry::fromWKB($rel->pivot->geography_val)->toWKT(),
                                'geom' => Geometry::fromWKB($rel->pivot->geography_val)
                            ];
                        }
                        $type = $rel->datatype;
                    }
                } else {
                    // should not happen ;)
                }
                $curr[] = $value;
            }
            $relName = "$s->name";
            $keys = array_keys($splitArray);
            if(!empty($keys)) {
                $hits = 0;
                $quotedName = "|". preg_quote($relName) . "( \(\d+\))?|";
                foreach($keys as $key) {
                    if(preg_match($quotedName, $key) === 1) {
                        $hits++;
                    }
                }
                if($hits > 0) {
                    $relName .= " ($hits)";
                }
            }
            $splitArray[$relName] = [
                'values' => $curr,
                'type'   => $type
            ];
        }

        return $splitArray;
    }

    private function filter($origin, $filters, $columns, $orders, $distinct, $relations = false) {
        $hasColumnSelection = !empty($columns);

        switch($origin) {
            case 'attribute_values':
                if($relations) {
                    $query = AttributeValue::with([
                        'attribute',
                        'context',
                        'context_val',
                        'thesaurus_val'
                    ]);
                } else {
                    $query = AttributeValue::leftJoin('contexts', 'contexts.id', '=', 'context_val');
                    if(!$hasColumnSelection) {
                        $tables = ['attribute_values', 'contexts'];

                        $columnNames = [];
                        foreach($tables as $table) {
                            $columnNames[$table] = Helpers::getColumnNames($table);
                        }

                        $this->renameColumns($query, $tables, $columnNames);
                    }
                }
                break;
            case 'contexts':
                if($relations) {
                    $query = Context::with([
                        'child_contexts',
                        'context_type',
                        'geodata',
                        'root_context',
                        'literatures',
                        'attributes',
                        'files'
                    ]);
                } else {
                    $query = Context::leftJoin('contexts as child', 'child.root_context_id', '=', 'contexts.id')
                                    ->leftJoin('contexts as root', 'root.id', '=', 'contexts.root_context_id')
                                    ->leftJoin('context_types', 'context_types.id', '=', 'contexts.context_type_id')
                                    ->leftJoin('geodata', 'geodata.id', '=', 'contexts.geodata_id')
                                    ->leftJoin('attribute_values', 'attribute_values.context_id', '=', 'contexts.id')
                                    ->leftJoin('attributes', 'attributes.id', '=', 'attribute_id')
                                    ->leftJoin('context_photos as cp', 'cp.context_id', '=', 'contexts.id')
                                    ->leftJoin('photos', 'photos.id', '=', 'photo_id');
                    if(!$hasColumnSelection) {
                        $tables = ['contexts', 'child', 'root', 'context_types', 'geodata', 'attribute_values', 'attributes', 'photos'];
                        $columnNames = [];
                        foreach($tables as $table) {
                            if($table === 'child' || $table === 'root') {
                                $columnNames[$table] = Helpers::getColumnNames('contexts');
                            } else {
                                $columnNames[$table] = Helpers::getColumnNames($table);
                            }
                        }

                        $this->renameColumns($query, $tables, $columnNames);
                    }
                }
                break;
            case 'files':
                if($relations) {
                    $query = File::with([
                        'contexts',
                        // 'tags'
                    ]);
                } else {
                    $query = File::leftJoin('context_photos as cp', 'cp.photo_id', '=', 'id')
                                    ->leftJoin('contexts', 'contexts.id', '=', 'context_id')
                                    ->leftJoin('photo_tags as pt', 'pt.photo_id', '=', 'photos.id')
                                    ->leftJoin('th_concept', 'th_concept.concept_url', '=', 'pt.concept_url');
                    if(!$hasColumnSelection) {
                        $tables = ['photos', 'contexts', 'th_concept'];
                        $columnNames = [];
                        foreach($tables as $table) {
                            $columnNames[$table] = Helpers::getColumnNames($table);
                        }

                        $this->renameColumns($query, $tables, $columnNames);
                    }
                }
                break;
            case 'geodata':
                if($relations) {
                    $query = Geodata::with([
                        'context'
                    ]);
                } else {
                    $query = Geodata::leftJoin('contexts', 'contexts.geodata_id', '=', 'geodata.id');
                    if(!$hasColumnSelection) {
                        $tables = ['geodata', 'contexts'];
                        $columnNames = [];
                        foreach($tables as $table) {
                            $columnNames[$table] = Helpers::getColumnNames($table);
                        }

                        $this->renameColumns($query, $tables, $columnNames);
                    }
                }
                break;
            case 'literature':
                if($relations) {
                    $query = Literature::with([
                        'contexts'
                    ]);
                } else {
                    $query = Literature::leftJoin('sources', 'sources.literature_id', '=', 'literature.id')
                        ->leftJoin('contexts', 'sources.context_id', '=', 'contexts.id')
                        ->leftJoin('attributes', 'sources.attribute_id', '=', 'attributes.id');
                    if(!$hasColumnSelection) {
                        $tables = ['literature', 'attributes', 'contexts', 'sources'];
                        $columnNames = [];
                        foreach($tables as $table) {
                            $columnNames[$table] = Helpers::getColumnNames($table);
                        }

                        $this->renameColumns($query, $tables, $columnNames);
                    }
                }
                break;
        }
        $groups = [];
        $hasGroupBy = false;
        if(!empty($filters)) {
            foreach($filters as $f) {
                $applied = $this->applyFilter($query, $f, $groups);
                // check if it was a valid filter and a agg function
                if($applied && isset($f->func) && $this->isAggregateFunction($f->func)) {
                    $hasGroupBy = true;
                } else {
                    $groups[$f->col] = 1;
                }
            }
        }

        if(!empty($orders)) {
            foreach($orders as $o) {
                $query->orderBy($o->col, $o->dir);
            }
        }

        if($distinct) {
            $query->distinct();
        }

        if($hasColumnSelection) {
            // check if there is at least one agg function
            foreach($columns as $c) {
                if(isset($c->func) && $this->isValidFunction($c->func)) {
                    if($this->isAggregateFunction($c->func)) {
                        $hasGroupBy = true;
                    } else {
                        $groups[$c->col] = 1;
                    }
                    $select =  $this->getAsRaw($c->func, $c->col, $c->func_values, $c->as);
                } else {
                    $groups[$c->col] = 1;
                    $select = '';
                    if(isset($c->as)) {
                        $select = " AS $c->as";
                    }
                    $select = $c->col.$select;
                }
                $query->addSelect($select);
            }
            if($hasGroupBy && !empty($groups)) {
                foreach($groups as $col => $set) {
                    if($set === 1) {
                        $query->groupBy($col);
                    }
                }
            }
        }

        return $query;
    }

    // renames columns from $column to $table.$column to avoid name ambiguities
    private function renameColumns($query, $tables, $columnNames) {
        if(empty($tables)) return;

        $query->select($tables[0].".id AS ".$tables[0].".id");
        foreach($tables as $table) {
            foreach($columnNames[$table] as $c) {
                $query->addSelect("$table.$c AS $table.$c");
            }
        }
    }

    private function applyFilter($query, $filter, $groups) {
        if(!$this->isValidCompare($filter->comp)) {
            // TODO error?
            return false;
        }
        $col = $filter->col;
        $comp = strtoupper($filter->comp);
        $compValue = null;
        if(isset($filter->comp_value)) {
            $compValue = $filter->comp_value;
        }
        if(isset($filter->relation) && isset($filter->relation->name)) {
            $isRelationFilter = true;
            $relation = $filter->relation;
        } else {
            $isRelationFilter = false;
        }
        $and = $filter->and;
        $usesFunc = isset($filter->func);
        if($usesFunc) {
            $func = $filter->func;
            $funcValues = null;
            if(isset($filter->func_values)) {
                $funcValues = $filter->func_values;
            }
            if(!$this->isValidFunction($func)) {
                // TODO error?
                return false;
            }
        }
        $isAgg = $usesFunc && $this->isAggregateFunction($func);
        if($usesFunc) {
            $col = $this->getAsRaw($func, $col, $funcValues);
        }
        if($isRelationFilter) {
            if($isAgg) {
                if($and) {
                    $query->whereHas($relation->name, function($q) use($col, $comp, $compValue, $relation) {
                        $q->where('id', '=', $relation->id);
                        $q->having($col, $comp, $compValue);
                    });
                } else {
                    $query->orWhereHas($relation->name, function($q) use($col, $comp, $compValue, $relation) {
                        $q->where('id', '=', $relation->id);
                        $q->having($col, $comp, $compValue);
                    });
                }
            } else {
                if(isset($relation->comp)) {
                    if($relation->comp == 'IS NULL') {
                        if($and) $query->doesntHave($relation->name);
                        else $query->orDoesntHave($relation->name);
                    } else if($relation->comp == 'IS NOT NULL') {
                        if($and) $query->has($relation->name);
                        else $query->orHas($relation->name);
                    } else {
                        if($and) $query->has($relation->name, $relation->comp, $relation->value);
                        else $query->orHas($relation->name, $relation->comp, $relation->value);
                    }
                } else {
                    if($and) {
                        $query->whereHas($relation->name, function($q) use($col, $comp, $compValue, $relation) {
                            if(isset($relation->id)) {
                                $q->where($relation->name . '.id', '=', $relation->id);
                            }
                            if($comp != 'IS NULL' && $comp != 'IS NOT NULL') {
                                $this->applyQueryPart($q, $col, $comp, $compValue, true);
                            }
                        });
                    } else {
                        $query->orWhereHas($relation->name, function($q) use($col, $comp, $compValue, $relation) {
                            if(isset($relation->id)) {
                                $q->where($relation->name . '.id', '=', $relation->id);
                            }
                            if($comp != 'IS NULL' && $comp != 'IS NOT NULL') {
                                $this->applyQueryPart($q, $col, $comp, $compValue, true);
                            }
                        });
                    }
                }
            }
        } else {
            if($isAgg) {
                if($and) $query->having($col, $comp, $compValue);
                else $query->orHaving($col, $comp, $compValue);
            } else {
                $this->applyQueryPart($query, $col, $comp, $compValue, $and);
            }
        }
        return true;
    }

    private function applyQueryPart($query, $col, $comp, $compValue, $and) {
        switch($comp) {
            case 'BETWEEN':
                if($and) $query->whereBetween($col, $compValue);
                else $query->orWhereBetween($col, $compValue);
                break;
            case 'IN':
                if($and) $query->whereIn($col, $compValue);
                else $query->orWhereIn($col, $compValue);
                break;
            case 'IS NULL':
                if($and) $query->whereNull($col);
                else $query->orWhereNull($col);
                break;
            case 'NOT BETWEEN':
                if($and) $query->whereNotBetween($col, $compValue);
                else $query->whereNotBetween($col, $compValue);
                break;
            case 'NOT IN':
                if($and) $query->whereNotIn($col, $compValue);
                else $query->orWhereNotIn($col, $compValue);
                break;
            case 'IS NOT NULL':
                if($and) $query->whereNotNull($col);
                else $query->orWhereNotNull($col);
                break;
            default:
                if($and) $query->where($col, $comp, $compValue);
                else $query->orWhere($col, $comp, $compValue);
                break;
        }
    }

    private function isValidCompare($comp) {
        $compU = strtoupper($comp);
        switch($comp) {
            case '=':
            case '!=':
            case '>':
            case '>=':
            case '<':
            case '<=':
            case 'ILIKE':
            case 'NOT ILIKE':
            case 'BETWEEN':
            case 'NOT BETWEEN':
            case 'IS NULL':
            case 'IS NOT NULL':
            case 'IN':
            case 'NOT IN':
                return true;
            default:
                return false;
        }
    }

    private function isValidFunction($func) {
        if(!isset($func)) return false;
        $func = strtoupper($func);
        if($this->isAggregateFunction($func)) return true;
        switch($func) {
            case 'PG_DISTANCE':
            case 'PG_AREA':
                return true;
            default:
                return false;
        }
    }

    private function isAggregateFunction($func) {
        if(!isset($func)) return false;
        $func = strtoupper($func);
        switch($func) {
            case 'COUNT':
            case 'MIN':
            case 'MAX':
            case 'AVG':
            case 'SUM':
                return true;
            default:
                return false;
        }
    }

    private function getAsRaw($func, $column, $values, $alias = null) {
        $as = '';
        if(isset($alias)) {
            $as = " AS \"$alias\"";
        }
        $func = strtoupper($func);
        switch($func) {
            case 'PG_DISTANCE':
                $pos = $values[0];
                $point = new Point($pos[0], $pos[1]);
                $wkt = $point->toWKT();
                return DB::raw("ST_Distance($column, ST_GeogFromText('$wkt'), true)$as");
            case 'PG_AREA':
                // return area as sqm, sqm should be default for SRID 4326
                return DB::raw("ST_Area($column, true)$as");
            case 'COUNT':
                return DB::raw("COUNT($column)$as");
            case 'MIN':
                return DB::raw("MIN($column)$as");
            case 'MAX':
                return DB::raw("MAX($column)$as");
            case 'AVG':
                return DB::raw("AVG($column)$as");
            case 'SUM':
                return DB::raw("SUM($column)$as");
        }
    }

    private function cleanSql($queryString) {
        return str_replace('"', '', $queryString);
    }

    private function getAttributeColumn($aid) {
        try {
            $attr = Attribute::findOrFail($aid);
        } catch(ModelNotFoundException $e) {
            return null;
        }
        $datatype = $attr->datatype;
        switch($datatype) {
            case 'string':
            case 'stringf':
            case 'list':
            	return 'str_val';
            case 'double':
            	return 'dbl_val';
            case 'string-sc':
            case 'string-mc':
            	return 'thesaurus_val';
            case 'epoch':
            	return 'json_val';
            case 'date':
            	return 'dt_val';
            case 'dimension':
            	return 'json_val';
            case 'geography':
            	return 'geography_val';
            case 'integer':
            case 'boolean':
            case 'percentage':
            	return 'int_val';
            case 'context':
            	return 'context_val';
            default:
                return 'str_val';
        }

    }
}
