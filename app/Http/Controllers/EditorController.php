<?php

namespace App\Http\Controllers;

// use App\AvailableLayer;
use \DB;
use App\Attribute;
use App\AttributeValue;
use App\AvailableLayer;
use App\Entity;
use App\EntityAttribute;
use App\EntityType;
use App\EntityTypeRelation;
use App\Geodata;
use App\ThConcept;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EditorController extends Controller {
    // GET

    public function getEntityTypeOccurrenceCount($id) {
        $cnt = Entity::where('entity_type_id', $id)->count();
        return response()->json($cnt);
    }

    public function getAttributeValueOccurrenceCount($aid, $ctid = -1) {
        $query = AttributeValue::where('attribute_id', $aid);
        if($ctid > -1) {
            $query->where('entity_type_id', $ctid);
            $query->join('entities', 'entities.id', '=', 'entity_id');
        }
        $cnt = $query->count();
        return response()->json($cnt);
    }

    public function getEntityType($id) {
        $user = auth()->user();
        if(!$user->can('view_concepts')) {
            return response()->json([
                'error' => __('You do not have the permission to get an entity type\'s data')
            ], 403);
        }
        try {
            $entityType = EntityType::with('sub_entity_types')->findOrFail($id);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This entity-type does not exist')
            ], 400);
        }
        return response()->json($entityType);
    }

    public function getEntityTypeAttributes($id) {
        $user = auth()->user();
        if(!$user->can('view_concept_props')) {
            return response()->json([
                'error' => __('You do not have the permission to view entity data')
            ], 403);
        }
        $attributes = DB::table('entity_types as c')
            ->where('c.id', $id)
            ->whereNull('a.parent_id')
            ->join('entity_attributes as ca', 'c.id', '=', 'ca.entity_type_id')
            ->join('attributes as a', 'ca.attribute_id', '=', 'a.id')
            ->orderBy('ca.position', 'asc')
            ->get();
        $selections = [];
        $dependencies = [];
        foreach($attributes as $a) {
            if(isset($a->depends_on)) {
                $dependsOn = json_decode($a->depends_on);
                foreach($dependsOn as $depAttr => $dep) {
                    if(!isset($dependencies[$depAttr])) {
                        $dependencies[$depAttr] = [];
                    }
                    $dependencies[$depAttr][] = $dep;
                }
            }
            unset($a->depends_on);
            switch($a->datatype) {
                case 'string-sc':
                case 'string-mc':
                case 'epoch':
                    $selections[$a->id] = ThConcept::getChildren($a->thesaurus_root_url, $a->recursive);
                    break;
                case 'table':
                    $a->columns = Attribute::where('parent_id', $a->id)->get()->keyBy('id');
                    // Only string-sc is allowed in tables
                    $columns = Attribute::where('parent_id', $a->id)
                        ->where('datatype', 'string-sc')
                        ->get();
                    foreach($columns as $c) {
                        $selections[$c->id] = ThConcept::getChildren($c->thesaurus_root_url, $c->recursive);
                    }
                    break;
                default:
                    break;
            }
        }
        return response()->json([
            'attributes' => $attributes,
            'selections' => $selections,
            'dependencies' => $dependencies
        ]);
    }

    public function getAttributeSelection($id) {
        $user = auth()->user();
        if(!$user->can('view_concept_props')) {
            return response()->json([
                'error' => __('You do not have the permission to view entity data')
            ], 403);
        }
        try {
            $attribute = Attribute::findOrFail($id);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This attribute does not exist')
            ], 400);
        }

        $attribute->columns = Attribute::where('parent_id', $attribute->id)->get();
        $selection = [];
        switch($attribute->datatype) {
            case 'string-sc':
            case 'string-mc':
            case 'epoch':
                $selection = ThConcept::getChildren($attribute->thesaurus_root_url, $attribute->recursive);
                break;
            case 'table':
                // Only string-sc is allowed in tables
                $columns = Attribute::where('parent_id', $attribute->id)
                    ->where('datatype', 'string-sc')
                    ->get();
                foreach($columns as $c) {
                    $selection = ThConcept::getChildren($c->thesaurus_root_url, $c->recursive);
                }
                break;
            default:
                break;
        }

        return response()->json(array_values($selection));
    }

    public function getTopEntityTypes() {
        $user = auth()->user();
        if(!$user->can('view_concept_props')) {
            return response()->json([
                'error' => __('You do not have the permission to view entity data')
            ], 403);
        }
        $entityTypes = EntityType::where('is_root', true)->get();
        return response()->json($entityTypes);
    }

    public function getAttributes() {
        $user = auth()->user();
        if(!$user->can('view_concept_props')) {
            return response()->json([
                'error' => __('You do not have the permission to view entity data')
            ], 403);
        }
        $attributes = Attribute::whereNull('parent_id')->orderBy('id')->get();
        foreach($attributes as $a) {
            $a->columns = Attribute::where('parent_id', $a->id)->get();
        }
        return response()->json($attributes);
    }

    public function getAttributeTypes() {
        return response()->json([
            [
                'datatype' => 'string'
            ],
            [
                'datatype' => 'stringf'
            ],
            [
                'datatype' => 'double'
            ],
            [
                'datatype' => 'integer'
            ],
            [
                'datatype' => 'boolean'
            ],
            [
                'datatype' => 'string-sc'
            ],
            [
                'datatype' => 'string-mc'
            ],
            [
                'datatype' => 'epoch'
            ],
            [
                'datatype' => 'timeperiod'
            ],
            [
                'datatype' => 'date'
            ],
            [
                'datatype' => 'dimension'
            ],
            [
                'datatype' => 'list'
            ],
            [
                'datatype' => 'geography'
            ],
            [
                'datatype' => 'percentage'
            ],
            [
                'datatype' => 'entity'
            ],
            [
                'datatype' => 'table'
            ],
            [
                'datatype' => 'sql'
            ],
            [
                'datatype' => 'serial'
            ],
            [
                'datatype' => 'iconclass'
            ]
        ]);
    }

    public function getAvailableGeometryTypes() {
        $types = Geodata::getAvailableGeometryTypes();
        return response()->json($types);
    }

    // POST

    public function addEntityType(Request $request) {
        $user = auth()->user();
        if(!$user->can('create_concepts')) {
            return response()->json([
                'error' => __('You do not have the permission to create a new entity type')
            ], 403);
        }
        $this->validate($request, [
            'concept_url' => 'required|exists:th_concept',
            'is_root' => 'required|boolean_string',
            'geomtype' => 'required|geometry'
        ]);

        $curl = $request->get('concept_url');
        $is_root = sp_parse_boolean($request->get('is_root'));
        $geomtype = $request->get('geomtype');
        $cType = new EntityType();
        $cType->thesaurus_url = $curl;
        $cType->is_root = $is_root;
        $cType->save();
        $cType = EntityType::find($cType->id);

        $layer = AvailableLayer::createFromArray([
            'name' => '',
            'url' => '',
            'type' => $geomtype,
            'opacity' => 1,
            'visible' => true,
            'is_overlay' => true,
            'entity_type_id' => $cType->id
        ]);

        return response()->json($cType, 201);
    }

    public function setRelationInfo(Request $request, $id) {
        $user = auth()->user();
        if(!$user->can('duplicate_edit_concepts')) {
            return response()->json([
                'error' => __('You do not have the permission to modify entity relations')
            ], 403);
        }
        $this->validate($request, [
            'is_root' => 'boolean_string',
            'sub_entity_types' => 'array'
        ]);
        try {
            $entityType = EntityType::findOrFail($id);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This entity-type does not exist')
            ], 400);
        }
        $is_root = $request->get('is_root');
        $subs = $request->get('sub_entity_types');
        $entityType->setRelationInfo($is_root, $subs);
        return response()->json(null, 204);
    }

    public function addAttribute(Request $request) {
        $user = auth()->user();
        if(!$user->can('duplicate_edit_concepts')) {
            return response()->json([
                'error' => __('You do not have the permission to add attributes')
            ], 403);
        }
        $this->validate($request, [
            'label_id' => 'required|integer|exists:th_concept,id',
            'datatype' => 'required|string',
            'root_id' => 'nullable|integer|exists:th_concept,id',
            'root_attribute_id' => 'nullable|integer|exists:attributes,id',
            'columns' => 'nullable|json',
            'text' => 'string',
            'recursive' => 'nullable|boolean_string'
        ]);

        $lid = $request->get('label_id');
        $datatype = $request->get('datatype');
        $curl = ThConcept::find($lid)->concept_url;
        $attr = new Attribute();
        $attr->thesaurus_url = $curl;
        $attr->datatype = $datatype;
        $attr->recursive = $request->has('recursive') && sp_parse_boolean($request->get('recursive'));
        if($request->has('root_id')) {
            $pid = $request->get('root_id');
            $purl = ThConcept::find($pid)->concept_url;
            $attr->thesaurus_root_url = $purl;
        } else if($request->has('root_attribute_id')) {
            $frid = $request->get('root_attribute_id');
            $attr->root_attribute_id = $frid;
        }
        if($request->has('text')) {
            $attr->text = $request->get('text');
        }
        $attr->save();

        if($datatype == 'table') {
            $cols = json_decode($request->get('columns'));
            foreach($cols as $col) {
                if(!isset($col->label_id) && !isset($col->datatype)) continue;
                $curl = ThConcept::find($col->label_id)->concept_url;
                $childAttr = new Attribute();
                $childAttr->thesaurus_url = $curl;
                $childAttr->datatype = $col->datatype;
                $childAttr->recursive = sp_parse_boolean($col->recursive);
                if(isset($col->root_id)) {
                    $pid = $col->root_id;
                    $purl = ThConcept::find($pid)->concept_url;
                    $childAttr->thesaurus_root_url = $purl;
                }
                $childAttr->parent_id = $attr->id;
                $childAttr->save();
            }
        }
        $attr->columns = Attribute::where('parent_id', $attr->id)->get();

        $attr = Attribute::find($attr->id);
        return response()->json($attr, 201);
    }

    public function addAttributeToEntityType(Request $request, $ctid) {
        $user = auth()->user();
        if(!$user->can('duplicate_edit_concepts')) {
            return response()->json([
                'error' => __('You do not have the permission to add attributes to an entity type')
            ], 403);
        }
        $this->validate($request, [
            'attribute_id' => 'required|integer|exists:attributes,id',
            'position' => 'integer'
        ]);

        $aid = $request->get('attribute_id');
        $pos = $request->get('position');
        if(!isset($pos)) {
            $attrsCnt = EntityAttribute::where('entity_type_id', '=', $ctid)->count();
            $pos = $attrsCnt + 1; // add new attribute to the end
        } else {
            $successors = EntityAttribute::where('entity_type_id', $ctid)
                ->where('position', '>=', $pos)
                ->get();
            foreach($successors as $s) {
                $s->position++;
                $s->save();
            }
        }
        $ca = new EntityAttribute();
        $ca->entity_type_id = $ctid;
        $ca->attribute_id = $aid;
        $ca->position = $pos;
        $ca->save();
        $ca = EntityAttribute::find($ca->id);

        $a = Attribute::find($aid);

        // If new attribute is serial, add attribute to all existing entities
        if($a->datatype == 'serial') {
            $entites = Entity::where('entity_type_id', $ctid)
                ->orderBy('created_at', 'asc')
                ->get();
            $ctr = 1;
            foreach($entites as $e) {
                Entity::addSerial($e->id, $aid, $a->text, $ctr, $user->id);
                $ctr++;
            }
        }

        $ca->load('attribute');
        return response()->json($ca, 201);
        // return response()->json(DB::table('entity_types as c')
        //         ->where('ca.id', $ca->id)
        //         ->join('entity_attributes as ca', 'c.id', '=', 'ca.entity_type_id')
        //         ->join('attributes as a', 'ca.attribute_id', '=', 'a.id')
        //         ->first(), 201);
    }

    public function duplicateEntityType(Request $request, $ctid) {
        $user = auth()->user();
        if(!$user->can('duplicate_edit_concepts')) {
            return response()->json([
                'error' => __('You do not have the permission to duplicate an entity type')
            ], 403);
        }

        try {
            $entityType = EntityType::findOrFail($ctid);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This entity-type does not exist')
            ], 400);
        }

        $duplicate = $entityType->replicate();
        $duplicate->save();

        $newLayer = $entityType->layer->replicate();
        $newLayer->entity_type_id = $duplicate->id;
        $newLayer->position = AvailableLayer::where('is_overlay', true)->max('position') + 1;
        $newLayer->save();

        foreach($entityType->attributes as $attribute) {
            $newAttribute = EntityAttribute::where('attribute_id', $attribute->pivot->attribute_id)
                ->where('entity_type_id', $attribute->pivot->entity_type_id)
                ->first()
                ->replicate();
            $newAttribute->entity_type_id = $duplicate->id;
            $newAttribute->save();
        }

        $relations = EntityTypeRelation::where('parent_id', $ctid)
            ->orWhere('child_id', $ctid)
            ->get();
        foreach($relations as $rel) {
            $newSet = $rel
                ->replicate();
            if($newSet->parent_id == $ctid) {
                $newSet->parent_id = $duplicate->id;
            }
            if($newSet->child_id == $ctid) {
                $newSet->child_id = $duplicate->id;
            }
            $newSet->save();
        }

        $duplicate->load('sub_entity_types');

        return response()->json($duplicate);
    }

    // PATCH

    public function patchLabel(Request $request, $ctid) {
        $user = auth()->user();
        if(!$user->can('duplicate_edit_concepts')) {
            return response()->json([
                'error' => __('You do not have the permission to modify entity-type labels')
            ], 403);
        }
        $this->validate($request, [
            'label' => 'required|string|exists:th_concept,concept_url'
        ]);

        try {
            $entityType = EntityType::findOrFail($ctid);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This entity-type does not exist')
            ], 400);
        }
        $entityType->thesaurus_url = $request->get('label');
        $entityType->save();

        return response()->json(null, 204);
    }

    public function reorderAttribute(Request $request, $ctid, $aid) {
        $user = auth()->user();
        if(!$user->can('duplicate_edit_concepts')) {
            return response()->json([
                'error' => __('You do not have the permission to reorder attributes')
            ], 403);
        }
        $this->validate($request, [
            'position' => 'required|integer|exists:entity_attributes,position'
        ]);

        $pos = $request->get('position');
        $ca = EntityAttribute::where([
            ['attribute_id', '=', $aid],
            ['entity_type_id', '=', $ctid]
        ])->first();

        if($ca === null){
            return response()->json([
                'error' => __('Entity Attribute not found')
            ], 400);
        }

        // Same position, nothing to do
        if($ca->position == $pos) {
            return response()->json(null, 204);
        }
        if($ca->position < $pos) {
            $successors = EntityAttribute::where([
                ['position', '>', $ca->position],
                ['position', '<=', $pos],
                ['entity_type_id', '=', $ctid]
            ])->get();
            foreach($successors as $s) {
                $s->position--;
                $s->save();
            }
        } else { // $ca->position > $pos
            $predecessors = EntityAttribute::where([
                ['position', '<', $ca->position],
                ['position', '>=', $pos],
                ['entity_type_id', '=', $ctid]
            ])->get();
            foreach($predecessors as $p) {
                $p->position++;
                $p->save();
            }
        }
        $ca->position = $pos;
        $ca->save();
        return response()->json(null, 204);
    }

    public function patchDependency(Request $request, $ctid, $aid) {
        $user = auth()->user();
        if(!$user->can('duplicate_edit_concepts')) {
            return response()->json([
                'error' => __('You do not have the permission to add/modify attribute dependencies')
            ], 403);
        }
        $this->validate($request, [
            'd_attribute' => 'required|nullable|integer|exists:entity_attributes,attribute_id',
            'd_operator' => 'required|nullable|in:<,>,=',
            'd_value' => 'required|nullable'
        ]);

        $entityAttribute = EntityAttribute::where([
            ['attribute_id', '=', $aid],
            ['entity_type_id', '=', $ctid]
        ])->first();

        if($entityAttribute === null){
            return response()->json([
                'error' => __('Entity Attribute not found')
            ], 400);
        }

        $dAttribute = $request->get('d_attribute');
        $dOperator = $request->get('d_operator');
        $dValue = $request->get('d_value');

        if(
            !(
                isset($dAttribute) &&
                isset($dOperator) &&
                isset($dValue)
            ) &&
            !(
                !isset($dAttribute) &&
                !isset($dOperator) &&
                !isset($dValue)
            )
        ) {
            return response()->json([
                'error' => __('Please provide either all dependency fields or none')
            ], 400);
        }

        $dependsOn = [
            $dAttribute => [
                'operator' => $dOperator,
                'value' => $dValue,
                'dependant' => $aid
            ]
        ];

        $entityAttribute->depends_on = json_encode($dependsOn);
        $entityAttribute->save();
        return response()->json(null, 204);
    }

    // DELETE

    public function deleteEntityType($id) {
        $user = auth()->user();
        if(!$user->can('delete_move_concepts')) {
            return response()->json([
                'error' => __('You do not have the permission to delete entity types')
            ], 403);
        }

        try {
            $entityType = EntityType::findOrFail($id);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This entity-type does not exist')
            ], 400);
        }

        $entityType->delete();
        return response()->json(null, 204);
    }

    public function deleteAttribute($id) {
        $user = auth()->user();
        if(!$user->can('delete_move_concepts')) {
            return response()->json([
                'error' => __('You do not have the permission to delete attributes')
            ], 403);
        }

        try {
            $attribute = Attribute::findOrFail($id);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'error' => __('This attribute does not exist')
            ], 400);
        }
        $attribute->delete();
        return response()->json(null, 204);
    }

    public function removeAttributeFromEntityType($ctid, $aid) {
        $user = auth()->user();
        if(!$user->can('duplicate_edit_concepts')) {
            return response()->json([
                'error' => __('You do not have the permission to remove attributes from entity types')
            ], 403);
        }
        $ca = EntityAttribute::where([
            ['attribute_id', '=', $aid],
            ['entity_type_id', '=', $ctid]
        ])->first();

        if($ca === null){
            return response()->json([
                'error' => __('Entity Attribute not found')
            ], 400);
        }

        $pos = $ca->position;
        $ca->delete();

        $successors = EntityAttribute::where([
                ['position', '>', $pos],
                ['entity_type_id', '=', $ctid]
            ])->get();
        foreach($successors as $s) {
            $s->position--;
            $s->save();
        }

        $entityIds = Entity::where('entity_type_id', $ctid)
            ->pluck('id')
            ->toArray();
        AttributeValue::where('attribute_id', $aid)
            ->whereIn('entity_id', $entityIds)
            ->delete();

        return response()->json(null, 204);
    }
}
