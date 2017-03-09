<?php

namespace App\Http\Controllers;
use Log;
use App\User;
use App\Permission;
use App\Role;
use App\Geodata;
use App\Context;
use App\Attribute;
use App\AttributeValue;
use App\ThConcept;
use Phaza\LaravelPostgis\Geometries\Geometry;
use Phaza\LaravelPostgis\Geometries\Point;
use Phaza\LaravelPostgis\Geometries\LineString;
use Phaza\LaravelPostgis\Geometries\Polygon;
use Phaza\LaravelPostgis\Exceptions\UnknownWKTTypeException;
use Zizaco\Entrust;
use \DB;
use Illuminate\Http\Request;

class ContextController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    public function getContextData($id) {
        $user = \Auth::user();
        if(!$user->can('view_concept_props')) {
            return response([
                'error' => 'You do not have the permission to call this method'
            ], 403);
        }
        return response()->json([
            'data' => $this->getData($id)
        ]);
    }

    private function getData($id) {
        $data = DB::table('attribute_values as av')->select('av.*', 'a.datatype', 'a.thesaurus_root_url')->join('attributes as a', 'av.attribute_id', '=', 'a.id')->where('context_id', $id)->get();
        foreach($data as &$attr) {
            if($attr->datatype == 'literature') {
                $attr->literature_info = DB::table('literature')->where('id', $attr->str_val)->first();
            } else if($attr->datatype == 'string-sc' || $attr->datatype == 'string-mc') {
                $attr->val = DB::table('th_concept')
                    ->select('id as narrower_id',
                        DB::raw("'".DB::table('getconceptlabelsfromurl')
                        ->where('concept_url', $attr->thesaurus_val)
                        ->where('short_name', 'de')
                        ->value('label')."' as narr")
                    )
                    ->where('concept_url', '=', $attr->thesaurus_val)
                    ->first();
            } else if($attr->datatype == 'dimension') {
                $jsonVal = json_decode($attr->json_val);
                if(!isset($jsonVal)) continue;

                if(isset($jsonVal->B)){
                    $attrVal['B'] = $jsonVal->B;
                }
                if(isset($jsonVal->H)){
                    $attrVal['H'] = $jsonVal->H;
                }
                if(isset($jsonVal->T)){
                    $attrVal['T'] = $jsonVal->T;
                }
                if(isset($jsonVal->unit)){
                    $attrVal['unit'] = $jsonVal->unit;
                }
                $attr->val = json_encode($attrVal);
            } else if($attr->datatype == 'epoch') {
                $jsonVal = json_decode($attr->json_val);
                if(!isset($jsonVal)) continue;

                if(isset($jsonVal->startLabel)){
                    $attrVal['startLabel'] = $jsonVal->startLabel;
                }
                if(isset($jsonVal->start)){
                    $attrVal['start'] = $jsonVal->start;
                }
                if(isset($jsonVal->endLabel)){
                    $attrVal['endLabel'] = $jsonVal->endLabel;
                }
                if(isset($jsonVal->end)){
                    $attrVal['end'] = $jsonVal->end;
                }
                if(isset($jsonVal->epoch)){
                    $attrVal['epoch'] = DB::table('th_concept')
                                        ->select('id as narrower_id',
                                            DB::raw("'".DB::table('getconceptlabelsfromurl')
                                            ->where('concept_url', $jsonVal->epoch)
                                            ->where('short_name', 'de')
                                            ->value('label')."' as narr")
                                        )
                                        ->where('concept_url', '=', $jsonVal->epoch)
                                        ->first();
                }
                $attr->val = json_encode($attrVal);
            } else if($attr->datatype == 'geography') {
                $tmp = AttributeValue::find($attr->id);
                $attr->val = $tmp->geography_val->toWKT();
            }
        }
        return $data;
    }

    private function parseWkt($wkt) {
        try {
            $geom = Geometry::getWKTClass($wkt);
            $parsed = $geom::fromWKT($wkt);
            return $parsed;
        } catch(UnknownWKTTypeException $e) {
            return -1;
        }
    }

    public function wktToGeojson(Request $request) {
        if(!$request->has('wkt')) return;
        $wkt = $request->get('wkt');
        $parsed = $this->parseWkt($wkt);
        if($parsed !== -1) {
            return response()->json([
                'geometry' => $parsed
            ]);
        } else {
            return response()->json([
                'error' => 'unsupported_wkt'
            ]);
        }
    }

    public function get() {
        return response()->json(
            DB::table('context_types as c')
                ->select('c.thesaurus_url as index', 'ca.context_type_id', 'ca.attribute_id as aid', 'a.datatype', 'c.type',
                    DB::raw("(select label from getconceptlabelsfromurl where concept_url = c.thesaurus_url and short_name = 'de' limit 1) as title"),
                    DB::raw("(select label from getconceptlabelsfromurl where concept_url = a.thesaurus_url and short_name = 'de' limit 1) as val")
                )
                ->leftJoin('context_attributes as ca', 'c.id', '=', 'ca.context_type_id')
                ->leftJoin('attributes as a', 'ca.attribute_id', '=', 'a.id')
                ->where('c.type', '=', '0')
                ->orderBy('val')
                ->get()
        );
    }

    public function getRecursive() {
        $user = \Auth::user();
        if(!$user->can('view_concepts')) {
            return response([
                'error' => 'You do not have the permission to call this method'
            ], 403);
        }
        $rootFields = DB::select("
        WITH RECURSIVE
        q AS (
	        SELECT  c.*, 0 as reclevel
	        FROM    contexts c
	        WHERE   root_context_id IS NULL
	        UNION ALL
	        SELECT  cc.*, reclevel+1
	        FROM    q
	        JOIN    contexts cc
	        ON      cc.root_context_id = q.id
        )
        SELECT  q.*, ct.type as typeid, ct.thesaurus_url AS typename, (select label from getconceptlabelsfromurl where concept_url = ct.thesaurus_url and short_name = 'de' limit 1) as typelabel
        FROM    q
        JOIN context_types AS ct
        ON q.context_type_id = ct.id
        ORDER BY reclevel DESC
        ");
        $children = [];
        foreach($rootFields as $key => $field) {
            if(array_key_exists($field->id, $children)) $tmpChildren = $children[$field->id];
            else $tmpChildren = array();
            $rootFields[$key]->children = $tmpChildren;
            $children[$field->root_context_id][] = $field;
            if($field->reclevel != 0) unset($rootFields[$key]);

            if(!$user->can('view_geodata')) {
                if(isset($rootFields[$key]->geodata_id)){
                    unset($rootFields[$key]->geodata_id);
                }
            }
        }
        return response()->json(array_values($rootFields));
    }

    public function linkGeodata($cid, $gid) {
        $user = \Auth::user();
        if(!$user->can('link_geodata')) {
            return response([
                'error' => 'You do not have the permission to call this method'
            ], 403);
        }
        try {
            $context = Context::findOrFail($cid);
        } catch(Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'This context does not exist'
            ]);
        }
        if(isset($context->geodata_id)) {
            return response()->json([
                'error' => 'This context is already linked to a geodata'
            ]);
        }
        try {
            Geodata::findOrFail($gid);
        } catch(Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'This geodata does not exist'
            ]);
        }
        $context->geodata_id = $gid;
        $context->save();
        return response()->json([
            'context' => $context
        ]);
    }

    public function unlinkGeodata($cid) {
        $user = \Auth::user();
        if(!$user->can('link_geodata')) {
            return response([
                'error' => 'You do not have the permission to call this method'
            ], 403);
        }
        try {
            $context = Context::findOrFail($cid);
        } catch(Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'This context does not exist'
            ]);
        }
        $context->geodata_id = null;
        $context->save();
        return response()->json([
            'context' => $context
        ]);
    }

    public function getContextParents($id) {
        $user = \Auth::user();
        if(!$user->can('view_concepts')) {
            return response([
                'error' => 'You do not have the permission to call this method'
            ], 403);
        }
        $path = DB::select("
        WITH RECURSIVE
        q AS (
	        SELECT  c.*, 0 as reclevel
	        FROM    contexts c
	        WHERE   id = $id
	        UNION ALL
	        SELECT  cc.*, reclevel+1
	        FROM    q
	        JOIN    contexts cc
	        ON      q.root_context_id = cc.id
        )
        SELECT  q.id
        FROM    q
        ORDER BY reclevel DESC
        ");
        return response()->json([
            'path' => $path
        ]);
    }

    public function getContextByGeodata($id) {
        $user = \Auth::user();
        if(!$user->can('view_geodata') || !$user->can('view_concepts')) {
            return response([
                'error' => 'You do not have the permission to call this method'
            ], 403);
        }
        $path = DB::select("
        WITH RECURSIVE
        q AS (
	        SELECT  c.*, 0 as reclevel
	        FROM    contexts c
	        WHERE   geodata_id = $id
	        UNION ALL
	        SELECT  cc.*, reclevel+1
	        FROM    q
	        JOIN    contexts cc
	        ON      q.root_context_id = cc.id
        )
        SELECT  q.id
        FROM    q
        ORDER BY reclevel DESC
        ");
        return response()->json([
            'path' => $path
        ]);
    }

    public function deleteGeodata($id) {
        $user = \Auth::user();
        if(!$user->can('upload_remove_geodata')) {
            return response([
                'error' => 'You do not have the permission to call this method'
            ], 403);
        }
        $linkedContexts = Context::where('geodata_id', '=', $id)->get();
        foreach($linkedContexts as $context) {
            $context->geodata_id = null;
            $context->save();
        }
        Geodata::find($id)->delete();
        return response()->json([
            'success' => ''
        ]);
    }

    public function addGeodata(Request $request) {
        $user = \Auth::user();
        if(!$user->can('create_edit_geodata')) {
            return response([
                'error' => 'You do not have the permission to call this method'
            ], 403);
        }
        $coords = json_decode($request->get('coords'));
        $type = $request->get('type');
        if($request->has('id')) {
            $id = $request->get('id');
            $geodata = Geodata::find($id);
        } else {
            $geodata = new Geodata();
        }
        switch($type) {
            case 'marker':
            case 'Point':
                $coords = $coords[0];
                $geodata->geom = new Point($coords->lat, $coords->lng);
                break;
            case 'polyline':
            case 'LineString':
                $lines = [];
                foreach($coords as $coord) {
                    $lines[] = new Point($coord->lat, $coord->lng);
                }
                $geodata->geom = new LineString($lines);
                break;
            case 'polygon':
            case 'Polygon':
                $lines = [];
                foreach($coords as $coord) {
                    $lines[] = new Point($coord->lat, $coord->lng);
                }
                $linestring = new LineString($lines);
                $geodata->geom = new Polygon([ $linestring ]);
                break;
        }
        $geodata->lasteditor = $user['name'];
        $geodata->save();
        return response()->json([
            'geodata' => [
                'geodata' => $geodata->geom->jsonSerialize(),
                'id' => $geodata->id
            ]
        ]);
    }

    public function getGeodata() {
        $user = \Auth::user();
        if(!$user->can('view_geodata')) {
            return response([
                'error' => 'You do not have the permission to call this method'
            ], 403);
        }
        $geoms = Geodata::all();
        $geodataList = [];
        foreach($geoms as $geom) {
            $geodataList[] = [
                'geodata' => $geom->geom->jsonSerialize(),
                'id' => $geom->id
            ];
        }
        return response()->json([
            'geodata' => $geodataList
        ]);
    }

    public function getChoices() {
        $user = \Auth::user();
        if(!$user->can('view_concepts')) {
            return response([
                'error' => 'You do not have the permission to call this method'
            ], 403);
        }
        $rows = DB::table('context_types as c')
        ->select('ca.context_type_id', 'ca.attribute_id as aid', 'a.datatype', 'a.thesaurus_root_url as root',
            DB::raw("(select label from getconceptlabelsfromurl where concept_url = C.thesaurus_url and short_name = 'de' limit 1) AS title"),
            DB::raw("(select label from getconceptlabelsfromurl where concept_url = A.thesaurus_url and short_name = 'de' limit 1) AS val")
        )
        ->leftJoin('context_attributes as ca', 'c.id', '=', 'ca.context_type_id')
        ->leftJoin('attributes as a', 'ca.attribute_id', '=', 'a.id')
        ->where('a.datatype', '=', 'string-sc')
        ->orWhere('a.datatype', '=', 'string-mc')
        ->orWhere('a.datatype', '=', 'epoch')
        ->orderBy('val')
        ->get();
        foreach($rows as &$row) {
            if(!isset($row->root)) continue;
            $rootId = DB::table('th_concept')
                ->select('id')
                ->where('concept_url', '=', $row->root)
                ->first();
            if(!isset($rootId)) continue;
            $rootId = $rootId->id;
            $row->choices = DB::select("
                WITH RECURSIVE
                top AS (
                    SELECT br.broader_id, br.narrower_id, (select label from getconceptlabelsfromid where concept_id = br.broader_id and short_name = 'de' limit 1) as broad,
                            (select label from getconceptlabelsfromid where concept_id = br.narrower_id and short_name = 'de' limit 1) as narr
                    FROM th_broaders br
                    WHERE broader_id = $rootId
                    UNION
                    SELECT br.broader_id, br.narrower_id, (select label from getconceptlabelsfromid where concept_id = br.broader_id and short_name = 'de' limit 1) as broad,
                            (select label from getconceptlabelsfromid where concept_id = br.narrower_id and short_name = 'de' limit 1) as narr
                    FROM top t, th_broaders br
                    WHERE t.narrower_id = br.broader_id
                )
                SELECT *
                FROM top
                ORDER BY narr
            ");
        }
        return response()->json($rows);
    }

    public function duplicate($id) {
        $user = \Auth::user();
        if(!$user->can('duplicate_edit_concepts')) {
            return response([
                'error' => 'You do not have the permission to call this method'
            ], 403);
        }
        $toDuplicate = DB::table('contexts')
            ->where('id', $id)
            ->first();
        unset($toDuplicate->id);
        $dupCounter = 0;
        do {
            $dupCounter++;
            $sameName = DB::table('contexts')
                ->where('name', '=', $toDuplicate->name . " ($dupCounter)")
                ->first();
        } while($sameName != null);
        $toDuplicate->name .= " ($dupCounter)";
        $cid = DB::table('contexts')
            ->insertGetId(get_object_vars($toDuplicate));
        $toDuplicate->id = $cid;
        $toDuplicateValues = DB::table('attribute_values')
            ->where('context_id', $id)
            ->get();
        foreach($toDuplicateValues as $value) {
            unset($value->id);
            $value->context_id = $cid;
            DB::table('attribute_values')
                ->insertGetId(get_object_vars($value));
        }
        $toDuplicate->data = $this->getData($cid);
        return response()->json(['obj' => $toDuplicate]);
    }

    public function getArtifacts() {
        return response()->json(
            DB::table('context_types as c')
                ->select('c.thesaurus_url as index', 'ca.context_type_id', 'ca.attribute_id as aid', 'a.datatype', 'c.type',
                    DB::raw("(select label from getconceptlabelsfromurl where concept_url = C.thesaurus_url and short_name = 'de' limit 1) AS title"),
                    DB::raw("(select label from getconceptlabelsfromurl where concept_url = A.thesaurus_url and short_name = 'de' limit 1) AS val")
                )
                ->leftJoin('context_attributes as ca', 'c.id', '=', 'ca.context_type_id')
                ->leftJoin('attributes as a', 'ca.attribute_id', '=', 'a.id')
                ->where('c.type', '=', '1')
                ->orderBy('val')
                ->get()
        );
    }

    public function getChildren($id) {
        $intId = filter_var($id, FILTER_VALIDATE_INT);
        if($intId === false || $intId <= 0) return;
        $user = \Auth::user();
        if(!$user->can('view_concept_props')) {
            return response([
                'error' => 'You do not have the permission to call this method'
            ], 403);
        }
        $rows = DB::select(
            "WITH RECURSIVE
            q AS (
                SELECT  c.*
                FROM    contexts c
                WHERE   id = $id
                UNION ALL
                SELECT  cc.*
                FROM    q
                JOIN    contexts cc
                ON      cc.root_context_id = q.id
            )
            SELECT  q.*, ct.type, ct.thesaurus_url AS typename, (select label from getconceptlabelsfromurl where concept_url = ct.thesaurus_url and short_name = 'de' limit 1) as typelabel
            FROM    q
            JOIN context_types AS ct
            ON q.context_type_id = ct.id
            ORDER BY id ASC"
        );
        $roots = array();
        foreach($rows as $row) {
            if(empty($row)) continue;
            $row->data = DB::table('attribute_values as av')->select('av.*', 'a.datatype')->join('attributes as a', 'av.attribute_id', '=', 'a.id')->where('context_id', $row->id)->get();
            if(!empty($row->root_context_id)) $roots[$row->root_context_id][] = $row;
        }
        return response()->json($roots);
    }

    public function set(Request $request) {
        $user = \Auth::user();
        $id = $request->get('id');

        if((isset($id) && !$user->can('duplicate_edit_concepts')) || (!isset($id) && !$user->can('create_concepts'))) {
            return response([
                'error' => 'You do not have the permission to call this method'
            ], 403);
        }

        $isUpdate = isset($id) && $id > 0;
        if($isUpdate) {
            $context = Context::find($id);
        } else {
            $context = new Context();
        }
        if($request->has('name')) $context->name = $request->get('name');
        if($request->has('context_type_id')) $context->context_type_id = $request->get('context_type_id');
        if($request->has('root_cid')) $context->root_context_id = $request->get('root_cid');
        $context->lasteditor = $user['name'];
        $context->save();

        $id = $context->id;
        $message = $this->updateOrInsert($request->except(['id', 'name', 'context_type_id', 'root_cid']), $id, $isUpdate, $user);
        if(isset($message['error'])){
            return response()->json($message);
        }
        return response()->json(['context' => $context]);
    }

    public function setIcon(Request $request) {
        $user = \Auth::user();
        if(!$user->can('duplicate_edit_concepts')) {
            return response([
                'error' => 'You do not have the permission to call this method'
            ], 403);
        }
        $id = $request->get('id');
        $upd = [];

        if($request->has('icon')) $upd['icon'] = $request->get('icon');
        if($request->has('color')) $upd['color'] = $request->get('color');

        DB::table('contexts')
            ->where('id', $id)
            ->update($upd);
        $icon = DB::table('contexts')
                ->where('id', $id)
                ->first();
        return response()->json([
            'icon' => $icon->icon,
            'color' => $icon->color
        ]);
    }

    public function setPossibility(Request $request) {
        $user = \Auth::user();
        if(!$user->can('duplicate_edit_concepts')) {
            return response([
                'error' => 'You do not have the permission to call this method'
            ], 403);
        }
        $user = \Auth::user();
        if($user == null) $user = ['name' => 'postgres']; //TODO remove after user auth has been fixed!
        $cid = $request->get('cid');
        $aid = $request->get('aid');
        $possibility = $request->get('possibility');
        $description = $request->get('possibility_description');

        $where = array(
            ['context_id', '=', $cid],
            ['attribute_id', '=', $aid]
        );
        $isSet = DB::table('attribute_values')
            ->where($where)
            ->count();
        if($isSet == null) { //insert
            DB::table('attribute_values')
                ->insert([
                    'context_id' => $cid,
                    'attribute_id' => $aid,
                    'possibility' => $possibility,
                    'possibility_description' => $description,
                    'lasteditor' => $user['name']
                ]);
        } else { //update
            DB::table('attribute_values')
                ->where($where)
                ->update([
                    'possibility' => $possibility,
                    'possibility_description' => $description,
                    'lasteditor' => $user['name']
                ]);
        }
        return response()->json(DB::table('attribute_values')
            ->where($where)->get());
    }


    public function delete($id) {
        $user = \Auth::user();
        if(!$user->can('delete_move_concepts')) {
            return response([
                'error' => 'You do not have the permission to call this method'
            ], 403);
        }
        DB::select("
            with recursive deletes as
            (
                select id
                from contexts
                where id = $id
                union all
                select c.id
                from contexts as c
                inner join deletes p on c.root_context_id = p.id
            )
            delete from attribute_values where context_id in (select id from deletes)
        ");
        DB::select("
            with recursive deletes as
            (
                select id
                from contexts
                where id = $id
                union all
                select c.id
                from contexts as c
                inner join deletes p on c.root_context_id = p.id
            )
            delete from contexts where id in (select id from deletes)
        ");

        return response()->json(array("id"=>$id));
    }

    public function updateOrInsert($request, $cid, $isUpdate, $user) {
        foreach($request as $key => $value) {
            if($value == 'null' || $value === null) continue;
            $ids = explode("_", $key);
            $aid = $ids[0];
            if(isset($ids[1]) && $ids[1] == 'desc') continue;
            $datatype = Attribute::find($aid)->datatype;
            $jsonArr = json_decode($value);
            if($datatype === 'string-sc') $jsonArr = [$jsonArr]; //"convert" to array

            if($datatype === 'epoch') {
                $start = $jsonArr->start;
                if(isset($jsonArr->startLabel) && $jsonArr->startLabel === 'bc') {
                    $start = -$start;
                }
                $end = $jsonArr->end;
                if(isset($jsonArr->endLabel) && $jsonArr->endLabel === 'bc') {
                    $end = -$end;
                }
                if($end < $start){
                    return [
                        'error' => 'End date should be later than start date.'
                    ];
                }
            }

            if(is_array($jsonArr)) { //only string-sc and string-mc should be arrays
                if($isUpdate) {
                    $dbEntries = array(
                        ['context_id', $cid],
                        ['attribute_id', $aid]
                    );
                    $rows = AttributeValue::where($dbEntries)->get();
                    foreach($rows as $row) {
                        $alreadySet = false;
                        foreach($jsonArr as $k => $v) {
                            if($datatype === 'list') {
                                $set = $v->name;
                                $val = $row->str_val;
                            } else {
                                try {
                                    $con = ThConcept::findOrFail($v->narrower_id);
                                    $set = $con->concept_url;
                                    $val = $row->thesaurus_val;
                                } catch(Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                                    continue;
                                }
                            }
                            if($val === $set) {
                                unset($jsonArr[$k]);
                                $alreadySet = true;
                                break;
                            }
                        }
                        if(!$alreadySet) {
                            $del = array(
                                ['context_id', $cid],
                                ['attribute_id', $aid]
                            );
                            if($datatype === 'list') $del[] = ['str_val', $row->str_val];
                            else $del[] = ['thesaurus_val', $row->thesaurus_val];
                            $AttributeValue::where($del)->delete();
                        }
                    }
                }
                foreach($jsonArr as $v) {
                    $attr = new AttributeValue();
                    $attr->context_id = $cid;
                    $attr->attribute_id = $aid;
                    $attr->lasteditor = $user['name'];
                    if($datatype === 'list') {
                        $attr->str_val = $v->name;
                    } else {
                        $set = ThConcept::find($v->narrower_id)->concept_url;
                        $attr->thesaurus_val = $set;
                    }
                    $attr->save();
                }
            } else {
                if($isUpdate) {
                    $alreadySet = false;
                    $attr;
                    $currAttrs = DB::table('attribute_values')->where('context_id', $cid)->get();
                    foreach($currAttrs as $currKey => $currVal) {
                        if($aid == $currVal->attribute_id) {
                            $alreadySet = true;
                            $attr = $currVal;
                            unset($currAttrs[$currKey]);
                            break;
                        }
                    }
                    if($alreadySet) {
                        $attrValue = AttributeValue::where([
                            ['context_id', '=', $attr->context_id],
                            ['attribute_id', '=', $attr->attribute_id],
                            ['id', '=', $attr->id]
                        ])->first();
                        if($value == '' || $value === null) {
                            AttributeValue::find($attrValue->id)->delete();
                            continue;
                        }
                    } else {
                        $attrValue = new AttributeValue();
                        $attrValue->context_id = $cid;
                        $attrValue->attribute_id = $aid;
                    }
                } else {
                    $attrValue = new AttributeValue();
                    $attrValue->context_id = $cid;
                    $attrValue->attribute_id = $aid;
                }
                $attrValue->lasteditor = $user['name'];
                if(is_object($jsonArr)) {
                    $attrValue->json_val = json_encode($jsonArr);
                } else {
                    if($datatype == 'geography') {
                        $parsed = $this->parseWkt($value);
                        if($parsed !== -1) {
                            $attrValue->geography_val = $parsed;
                        }
                    } else {
                        $attrValue->str_val = $value;
                    }
                }
                $attrValue->save();
            }
        }
    }
}
