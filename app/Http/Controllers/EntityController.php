<?php

namespace App\Http\Controllers;

use App\Attribute;
use App\AttributeValue;
use App\Entity;
use App\EntityAttribute;
use App\EntityType;
use App\Geodata;
use App\ThConcept;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class EntityController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    // GET

    public function getTopEntities() {
        $user = auth()->user();
        if(!$user->can('view_concepts')) {
            return response()->json([
                'error' => __('You do not have the permission to get entities')
            ], 403);
        }
        $roots = Entity::getEntitiesByParent(null);

        return response()->json($roots);
    }

    public function getEntity($id) {
        $user = auth()->user();
        if(!$user->can('view_concepts')) {
            return response()->json([
                'error' => __('You do not have the permission to get a specific entity')
            ], 403);
        }
        try {
            $entity = Entity::findOrFail($id);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This entity does not exist')
            ], 400);
        }

        return response()->json($entity);
    }

    public function getDataForEntityType(Request $request, $ctid, $aid) {
        $user = auth()->user();
        if(!$user->can('view_concepts')) {
            return response()->json([
                'error' => __('You do not have the permission to get an entity\'s data')
            ], 403);
        }
        try {
            $entityType = EntityType::findOrFail($ctid);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This entity type does not exist')
            ], 400);
        }
        try {
            Attribute::findOrFail($aid);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This attribute does not exist')
            ], 400);
        }
        $constraints = $request->query();
        $entities = Entity::where('entity_type_id', $ctid);
        foreach($constraints as $relatiion => $cons) {
            if($cons == 'has') {
                $entities->whereHas($relatiion);
            } else if($cons == 'hasnot') {
                $entities->whereDoesntHave($relatiion);
            }
        }
        $entities = $entities->get();
        $entityIds = $entities->pluck('id')->toArray();
        $values = AttributeValue::whereHas('attribute', function(Builder $q) {
                $q->where('datatype', '!=', 'sql');
            })
            ->whereIn('entity_id', $entityIds)
            ->where('attribute_id', $aid)
            ->get();
        $data = [];
        foreach($values as $value) {
            switch($value->attribute->datatype) {
                case 'entity':
                    $value->name = Entity::find($value->entity_val)->name;
                    $value->value = Entity::find($value->entity_val)->name;
                    break;
                default:
                    $value->value = $value->getValue();
                    break;
            }
            $data[$value->entity_id] = $value;
        }

        $sqls = EntityAttribute::whereHas('attribute', function(Builder $q) {
                $q->where('datatype', 'sql');
            })
            ->where('entity_type_id', $ctid)
            ->where('attribute_id', $aid)
            ->get();

        foreach($sqls as $sql) {
            // if entity_id is referenced several times
            // add an incrementing counter, so the
            // references are unique (required by PDO)
            $cnt = substr_count($sql->attribute->text, ':entity_id');
            if($cnt > 1) {
                $i = 0;
                $text = preg_replace_callback('/:entity_id/', function($matches) use (&$i) {
                    return $matches[0].'_'.$i++;
                }, $sql->attribute->text);
            } else {
                $text = $sql->attribute->text;
            }
            foreach($entityIds as $eid) {
                $safes = [];
                if($cnt > 1) {
                    for($i=0; $i<$cnt; $i++) {
                        $safes[':entity_id_'.$i] = $eid;
                    }
                } else {
                    $safes = [
                        ':entity_id' => $eid
                    ];
                }
                $sqlValue = \DB::select($text, $safes);
                // Check if only one result exists
                if(count($sqlValue) === 1) {
                    // Get all column indices (keys) using the first row
                    $valueKeys = array_keys(get_object_vars($sqlValue[0]));
                    // Check if also only one key/column exists
                    if(count($valueKeys) === 1) {
                        // If only one row and one column exist,
                        // return plain value instead of array
                        $firstKey = $valueKeys[0];
                        $sqlValue = $sqlValue[0]->{$firstKey};
                    }
                }
                $data[$eid] = [
                    'value' => $sqlValue
                ];
            }
        }

        return response()->json($data);
    }

    public function getData($id, $aid = null) {
        $user = auth()->user();
        if(!$user->can('view_concepts')) {
            return response()->json([
                'error' => __('You do not have the permission to get an entity\'s data')
            ], 403);
        }
        try {
            $entity = Entity::findOrFail($id);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This entity does not exist')
            ], 400);
        }
        if(isset($aid)) {
            try {
                Attribute::findOrFail($aid);
            } catch(ModelNotFoundException $e) {
                return response()->json([
                    'error' => __('This attribute does not exist')
                ], 400);
            }
            $attributes = AttributeValue::whereHas('attribute', function(Builder $q) {
                    $q->where('datatype', '!=', 'sql');
                })
                ->where('entity_id', $id)
                ->where('attribute_id', $aid)
                ->get();
        } else {
            $attributes = AttributeValue::whereHas('attribute', function(Builder $q) {
                    $q->where('datatype', '!=', 'sql');
                })
                ->where('entity_id', $id)
                ->get();
        }

        $data = [];
        foreach($attributes as $a) {
            switch($a->attribute->datatype) {
                case 'string-sc':
                    $a->thesaurus_val = ThConcept::where('concept_url', $a->thesaurus_val)->first();
                    break;
                case 'entity':
                    $a->name = Entity::find($a->entity_val)->name;
                    break;
                default:
                    break;
            }
            $value = $a->getValue();
            $a->value = $value;
            $data[$a->attribute_id] = $a;
        }

        $sqls = EntityAttribute::whereHas('attribute', function(Builder $q) {
                $q->where('datatype', 'sql');
            })
            ->where('entity_type_id', $entity->entity_type_id);
        if(isset($aid)) {
            $sqls->where('attribute_id', $aid);
        }
        $sqls = $sqls->get();

        foreach($sqls as $sql) {
            // if entity_id is referenced several times
            // add an incrementing counter, so the
            // references are unique (required by PDO)
            $cnt = substr_count($sql->attribute->text, ':entity_id');
            if($cnt > 1) {
                $safes = [];
                for($i=0; $i<$cnt; $i++) {
                    $safes[':entity_id_'.$i] = $id;
                }
                $i = 0;
                $text = preg_replace_callback('/:entity_id/', function($matches) use (&$i) {
                    return $matches[0].'_'.$i++;
                }, $sql->attribute->text);
            } else {
                $text = $sql->attribute->text;
                $safes = [
                    ':entity_id' => $id
                ];
            }
            $sqlValue = \DB::select($text, $safes);
            // Check if only one result exists
            if(count($sqlValue) === 1) {
                // Get all column indices (keys) using the first row
                $valueKeys = array_keys(get_object_vars($sqlValue[0]));
                // Check if also only one key/column exists
                if(count($valueKeys) === 1) {
                    // If only one row and one column exist,
                    // return plain value instead of array
                    $firstKey = $valueKeys[0];
                    $sqlValue = $sqlValue[0]->{$firstKey};
                }
            }
            $data[$sql->attribute_id] = [
                'value' => $sqlValue
            ];
        }

        return response()->json($data);
    }

    public function getParentIds($id) {
        $user = auth()->user();
        if(!$user->can('view_concepts')) {
            return response()->json([
                'error' => __('You do not have the permission to get an entity\'s parent id\'s')
            ], 403);
        }

        try {
            $entity = Entity::findOrFail($id);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This entity does not exist')
            ], 400);
        }
        return response()->json($entity->parentIds);
    }

    public function getEntitiesByParent($id) {
        return Entity::getEntitiesByParent($id);
    }

    // POST

    public function addEntity(Request $request) {
        $user = auth()->user();
        if(!$user->can('create_concepts')) {
            return response()->json([
                'error' => __('You do not have the permission to add a new entity')
            ], 403);
        }
        $this->validate($request, Entity::rules);

        $fields = $request->only(array_keys(Entity::rules));
        $etid = $request->get('entity_type_id');
        $reid = $request->get('root_entity_id');

        $res = Entity::create($fields, $etid, $user, $reid);

        if($res['type'] === 'entity') {
            return response()->json($res['entity'], 201);
        } else {
            return response()->json([
                'error' => $res['msg']
            ], $res['code']);
        }
    }

    // PATCH

    public function patchAttributes($id, Request $request) {
        $user = auth()->user();
        if(!$user->can('duplicate_edit_concepts')) {
            return response()->json([
                'error' => __('You do not have the permission to modify an entity\'s data')
            ], 403);
        }

        try {
            $entity = Entity::findOrFail($id);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This entity does not exist')
            ], 400);
        }

        foreach($request->request as $patch) {
            $op = $patch['op'];
            $aid = $patch['params']['aid'];
            $attr = Attribute::find($aid);
            switch($op) {
                case 'remove':
                    $attrval = AttributeValue::where([
                        ['entity_id', '=', $id],
                        ['attribute_id', '=', $aid]
                    ])->first();
                    $attrval->delete();
                    break;
                case 'add':
                    $value = $patch['value'];
                    $attrval = new AttributeValue();
                    $attrval->entity_id = $id;
                    $attrval->attribute_id = $aid;
                    break;
                case 'replace':
                    $value = $patch['value'];
                    $attrval = AttributeValue::where([
                        ['entity_id', '=', $id],
                        ['attribute_id', '=', $aid]
                    ])->first();
                    break;
                default:
                    return response()->json([
                        'error' => __('Unknown operation')
                    ], 400);
            }

            // no further action required for deleted attribute values, continue with next patch
            if($op == 'remove') continue;

            switch($attr->datatype) {
                // for primitive types: just save them to the db
                case 'stringf':
                case 'string':
                case 'iconclass':
                    $attrval->str_val = $value;
                    break;
                case 'double':
                    $attrval->dbl_val = $value;
                    break;
                case 'boolean':
                case 'percentage':
                case 'integer':
                    $attrval->int_val = $value;
                    break;
                case 'date':
                    $attrval->dt_val = $value;
                    break;
                case 'string-sc':
                    $thesaurus_url = $value['concept_url'];
                    $attrval->thesaurus_val = $thesaurus_url;
                    break;
                case 'string-mc':
                    $thesaurus_urls = [];
                    foreach($value as $val) {
                        $thesaurus_urls[] = [
                            "concept_url" => $val['concept_url'],
                            "id" => $val['id']
                        ];
                    }
                    $attrval->json_val = json_encode($thesaurus_urls);
                    break;
                case 'epoch':
                case 'timeperiod':
                case 'dimension':
                case 'list':
                case 'table':
                    // check for invalid time spans
                    if($attr->datatype == 'epoch' || $attr->datatype == 'timeperiod') {
                        $sl = isset($value['startLabel']) ? strtoupper($value['startLabel']) : null;
                        $el = isset($value['endLabel']) ? strtoupper($value['endLabel']) : null;
                        $s = $value['start'];
                        $e = $value['end'];
                        if(
                            (isset($s) && !isset($sl))
                            ||
                            (isset($e) && !isset($el))
                        ) {
                            return response()->json([
                                'error' => __('You have to specify if your date is BC or AD.')
                            ], 422);
                        }
                        if(
                            ($sl == 'AD' && $el == 'BC')
                            ||
                            ($sl == 'BC' && $el == 'BC' && $s < $e)
                            ||
                            ($sl == 'AD' && $el == 'AD' && $s > $e)
                        ) {
                            return response()->json([
                                'error' => __('Start date of a time period must not be after it\'s end date')
                            ], 422);
                        }
                    }
                    $attrval->json_val = json_encode($value);
                    break;
                case 'entity':
                    $attrval->entity_val = $value;
                    break;
                case 'geography':
                    $attrval->geography_val = Geodata::parseWkt($value);
                    break;
            }
            $attrval->user_id = $user->id;
            $attrval->save();
        }

        // Save model if last editor changed
        // Only update timestamps otherwise
        $entity->user_id = $user->id;
        if($entity->isDirty()) {
            $entity->save();
        } else {
            $entity->touch();
        }

        $entity->load('user');

        return response()->json($entity);
    }

    public function patchAttribute($id, $aid, Request $request) {
        $user = auth()->user();
        if(!$user->can('duplicate_edit_concepts')) {
            return response()->json([
                'error' => __('You do not have the permission to modify an entity\'s data')
            ], 403);
        }
        $this->validate($request, AttributeValue::patchRules);

        try {
            Entity::findOrFail($id);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This entity does not exist')
            ], 400);
        }
        try {
            Attribute::findOrFail($aid);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This attribute does not exist')
            ], 400);
        }

        $attrValue = AttributeValue::firstOrCreate([
            'entity_id' => $id,
            'attribute_id' => $aid,
        ], [
            'user_id' => $user->id
        ]);
        // When attribute value already exists and nothing changed
        // (same certainty)
        if(
            !$attrValue->wasRecentlyCreated
            &&
            ($request->has('certainty') && $request->get('certainty') == $attrValue->certainty)
        ) {
            return response()->json($attrValue);
        }
        $attrValue->user_id = $user->id;
        $values = $request->only(array_keys(AttributeValue::patchRules));
        $attrValue->patch($values);

        return response()->json($attrValue, 201);
    }

    public function patchName($id, Request $request) {
        $user = auth()->user();
        if(!$user->can('duplicate_edit_concepts')) {
            return response()->json([
                'error' => __('You do not have the permission to modify an entity\'s data')
            ], 403);
        }
        $this->validate($request, [
            'name' => 'required|string'
        ]);

        try {
            $entity = Entity::findOrFail($id);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This entity does not exist')
            ], 400);
        }

        $entity->name = $request->get('name');
        $entity->user_id = $user->id;

        $entity->save();
        
        $entity->load('user');

        return response()->json($entity);
    }

    public function moveEntity(Request $request, $id) {
        $user = auth()->user();
        if(!$user->can('delete_move_concepts')) {
            return response()->json([
                'error' => __('You do not have the permission to modify an entity')
            ], 403);
        }
        $this->validate($request, [
            'rank' => 'required|integer',
            'parent_id' => 'nullable|integer|exists:entities,id',
        ]);

        try {
            $entity = Entity::findOrFail($id);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This entity does not exist')
            ], 400);
        }

        $rank = $request->get('rank');
        $parent_id = $request->get('parent_id');

        Entity::patchRanks($rank, $id, $parent_id, $user);
        return response()->json(null, 204);
    }

    // DELETE

    public function deleteEntity($id) {
        $user = auth()->user();
        if(!$user->can('delete_move_concepts')) {
            return response()->json([
                'error' => __('You do not have the permission to delete an entity')
            ], 403);
        }
        try {
            $entity = Entity::findOrFail($id);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This entity does not exist')
            ], 400);
        }
        $entity->delete();

        return response()->json(null, 204);
    }
}
