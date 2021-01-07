<?php

namespace Tests\Feature;

use Tests\TestCase;

use App\Attribute;
use App\AvailableLayer;
use App\AttributeValue;
use App\Entity;
use App\EntityAttribute;
use App\EntityType;
use App\ThConcept;
use App\User;

class ApiEditorTest extends TestCase
{
    // Testing GET requests

    /**
     * Test number of occurrences of an entity type (id=5).
     *
     * @return void
     */
    public function testEntityOccurCountEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/editor/dm/entity_type/occurrence_count/5');

        $response->assertStatus(200);
        $response->assertSimilarJson([3]);
    }

    /**
     * Test number of occurrences of an attribute (id=9).
     *
     * @return void
     */
    public function testAttributeOccurCountEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/editor/dm/attribute/occurrence_count/9');

        $response->assertStatus(200);
        $response->assertSimilarJson([4]);
    }

    /**
     * Test number of occurrences of an attribute (id=9) of an entity (id=5).
     *
     * @return void
     */
    public function testAttributeOfEntityTypeOccurCountEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/editor/dm/attribute/occurrence_count/9/5');

        $response->assertStatus(200);
        $response->assertSimilarJson([3]);
    }

    /**
     * Test getting top-level entities.
     *
     * @return void
     */
    public function testTopEntityTypeCountEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/editor/dm/entity_type/top');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJsonStructure([
            [
                'id',
                'thesaurus_url',
                'is_root',
                'created_at',
                'updated_at'
            ]
        ]);
        $response->assertSimilarJson([
            [
                'id' => 3,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/fundstelle#20171220094911',
                'is_root' => true,
                'created_at' => '2017-12-20T10:03:06.000000Z',
                'updated_at' => '2017-12-20T10:03:06.000000Z'
            ],
            [
                'id' => 7,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/lagerstatte#20171220165727',
                'is_root' => true,
                'created_at' => '2017-12-20T16:57:41.000000Z',
                'updated_at' => '2017-12-20T16:57:41.000000Z'
            ]
        ]);
    }

    /**
     * Test get all attributes.
     *
     * @return void
     */
    public function testGetAttributeEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/editor/dm/attribute');

        $response->assertStatus(200);
        $response->assertJsonCount(15);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'thesaurus_url',
                'datatype',
                'text',
                'thesaurus_root_url',
                'parent_id',
                'created_at',
                'updated_at',
                'recursive',
                'root_attribute_id',
                'columns'
            ]
        ]);
        $response->assertJsonFragment([
            'id' => 5,
            'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/verzierung#20171220105421',
            'datatype' => 'table',
            'text' => null,
            'thesaurus_root_url' => null,
            'parent_id' => null,
            'created_at' => '2017-12-20T10:56:32.000000Z',
            'updated_at' => '2017-12-20T10:56:32.000000Z',
            'recursive' => true,
            'root_attribute_id' => null,
            'columns' => []
        ]);

        $content = json_decode($response->getContent());
        $cols = $content[3]->columns;
        $this->assertEquals(6, $cols[0]->id);
        $this->assertEquals(7, $cols[1]->id);
        $this->assertEquals(8, $cols[2]->id);
    }

    /**
     * Test getting all attribute types.
     *
     * @return void
     */
    public function testGetAttributeTypesEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/editor/dm/attribute_types');

        $response->assertStatus(200);
        $response->assertJsonCount(19);
        $response->assertJsonStructure([
            '*' => [
                'datatype'
            ]
        ]);
        $response->assertSimilarJson([
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

    /**
     * Test getting an entity type (id=3).
     *
     * @return void
     */
    public function testGetEntityTypeEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/editor/entity_type/3');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'thesaurus_url',
            'is_root',
            'created_at',
            'updated_at',
            'sub_entity_types'
        ]);
        $response->assertJsonFragment([
            'id' => 3,
            'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/fundstelle#20171220094911',
            'is_root' => true,
            'created_at' => '2017-12-20T10:03:06.000000Z',
            'updated_at' => '2017-12-20T10:03:06.000000Z'
        ]);

        $content = json_decode($response->getContent());
        $subs = $content->sub_entity_types;
        $this->assertEquals(3, $subs[0]->id);
        $this->assertEquals(4, $subs[1]->id);
        $this->assertEquals(5, $subs[2]->id);
        $this->assertEquals(6, $subs[3]->id);
        $this->assertEquals(7, $subs[4]->id);
    }

    /**
     * Test getting attributes of an entity type (id=4) including dropdown menu selections and dependencies.
     *
     * @return void
     */
    public function testGetEntityTypeAttributesEndpoint()
    {
        // Get an attribute (attribute_id=14) of the entity type to be tested (4);
        $ea = EntityAttribute::find(9);
        $ea->depends_on = '{"17": {"value": "placeholder", "operator": "=", "dependant": "14"}}';
        $ea->save();

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/editor/entity_type/4/attribute');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
        $response->assertJsonStructure([
            'attributes' => [
                [
                    'id',
                    'entity_type_id',
                    'attribute_id',
                    'position',
                    'created_at',
                    'updated_at'
                ]
            ],
            'selections' => [
                '*' => [
                    [
                        'broader_id',
                        'narrower_id',
                        'id',
                        'concept_url',
                        'concept_scheme',
                        'is_top_concept',
                        'user_id',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ],
            'dependencies' => [
                '*' => [
                    [
                        'value',
                        'operator',
                        'dependant'
                    ]
                ]
            ]
        ]);

        $content = json_decode($response->getContent());
        $sels = $content->selections->{17};
        $deps = $content->dependencies->{17};
        $this->assertEquals(59, $sels[0]->id);
        $this->assertEquals(60, $sels[1]->id);
        $this->assertEquals(61, $sels[2]->id);
        $this->assertEquals(14, $deps[0]->dependant);
        $this->assertEquals('=', $deps[0]->operator);
        $this->assertEquals('placeholder', $deps[0]->value);
    }

    /**
     * Test getting entries of a dropdown attribute (id=14).
     *
     * @return void
     */
    public function testGetAttributeSelectionEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/editor/attribute/14/selection');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
        $response->assertJson([
            ['id' => 52],
            ['id' => 53],
            ['id' => 54],
        ]);
    }

    /**
     * Test getting available geometry types.
     *
     * @return void
     */
    public function testGetGeometryTypesEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/editor/dm/geometry');

        $response->assertStatus(200);
        $response->assertJsonCount(6);
        $response->assertSimilarJson([
            'Point',
            'LineString',
            'Polygon',
            'MultiPoint',
            'MultiLineString',
            'MultiPolygon'
        ]);
    }

    // Testing POST requests

    /**
     * Test adding a new entity type and modifying it's relations afterwards.
     *
     * @return void
     */
    public function testAddEntityTypeEndpoint()
    {
        $concept = ThConcept::first();
        $layMax = AvailableLayer::where('is_overlay', true)->max('position');

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/editor/dm/entity_type', [
                'concept_url' => $concept->concept_url,
                'is_root' => true,
                'geomtype' => 'any'
            ]);

        $entityType = EntityType::latest()->first();
        $entityTypeLayer = AvailableLayer::latest()->first();

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'thesaurus_url',
            'is_root',
            'created_at',
            'updated_at'
        ]);
        $response->assertSimilarJson([
            'id' => $entityType->id,
            'thesaurus_url' => $concept->concept_url,
            'is_root' => true,
            'created_at' => $entityType->created_at->toJSON(),
            'updated_at' => $entityType->updated_at->toJSON()
        ]);

        $this->assertEquals($entityType->id, $entityTypeLayer->entity_type_id);
        $this->assertEquals($layMax+1, $entityTypeLayer->position);

        $this->refreshToken($response);

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post("/api/v1/editor/dm/$entityType->id/relation", [
                'is_root' => false,
                'sub_entity_types' => [
                    4, 6, 7
                ]
            ]);

        $response->assertStatus(204);

        $entityType = EntityType::find($entityType->id)->load('sub_entity_types');
        $this->assertTrue(!$entityType->is_root);
        $this->assertArraySubset([
            ['id' => 4],
            ['id' => 6],
            ['id' => 7]
        ], $entityType->sub_entity_types->toArray());
    }

    /**
     * Test adding a new entity type and modifying it's relations afterwards.
     *
     * @return void
     */
    public function testAddAttributeEndpoint()
    {
        $concept = ThConcept::first();

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/editor/dm/attribute', [
                'label_id' => $concept->id,
                'datatype' => 'string-sc',
                'root_id' => $concept->id,
                'recursive' => false
            ]);

        $attribute = Attribute::latest()->first();

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'thesaurus_url',
            'datatype',
            'text',
            'thesaurus_root_url',
            'parent_id',
            'created_at',
            'updated_at',
            'recursive',
            'root_attribute_id'
        ]);
        $response->assertSimilarJson([
            'id' => $attribute->id,
            'thesaurus_url' => $concept->concept_url,
            'datatype' => 'string-sc',
            'text' => null,
            'thesaurus_root_url' => $concept->concept_url,
            'parent_id' => null,
            'created_at' => $attribute->created_at->toJSON(),
            'updated_at' => $attribute->updated_at->toJSON(),
            'recursive' => false,
            'root_attribute_id' => null
        ]);
    }

    /**
     * Test adding an attribute (id=2) to an entity type (id=3).
     *
     * @return void
     */
    public function testAddAttributeToEntityTypeEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/editor/dm/entity_type/3/attribute', [
                'attribute_id' => 2,
                'position' => 1
            ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'entity_type_id',
            'attribute_id',
            'position',
            'depends_on',
            'created_at',
            'updated_at',
            'attribute' => [
                'id',
                'thesaurus_url',
                'datatype',
                'text',
                'thesaurus_root_url',
                'parent_id',
                'created_at',
                'updated_at',
                'recursive',
                'root_attribute_id'
            ]
        ]);
        $response->assertJson([
            'entity_type_id' => 3,
            'attribute_id' => 2,
            'position' => 1,
            'depends_on' => null,
            'attribute' => [
                'id' => 2,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/erhaltung#20171220100437',
                'datatype' => 'percentage',
                'text' => null,
                'thesaurus_root_url' => null,
                'parent_id' => null,
                'recursive' => true,
                'root_attribute_id' => null
            ]
        ]);

        $this->refreshToken($response);

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/editor/dm/entity_type/3/attribute', [
                'attribute_id' => 3
            ]);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'entity_type_id',
            'attribute_id',
            'position',
            'depends_on',
            'created_at',
            'updated_at',
            'attribute' => [
                'id',
                'thesaurus_url',
                'datatype',
                'text',
                'thesaurus_root_url',
                'parent_id',
                'created_at',
                'updated_at',
                'recursive',
                'root_attribute_id'
            ]
        ]);
        $response->assertJson([
            'entity_type_id' => 3,
            'attribute_id' => 3,
            'position' => 4,
            'depends_on' => null,
            'attribute' => [
                'id' => 3,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/farbe#20171220100506',
                'datatype' => 'string-mc',
                'text' => null,
                'thesaurus_root_url' => null,
                'parent_id' => null,
                'recursive' => true,
                'root_attribute_id' => null
            ]
        ]);
    }

    /**
     * Test duplicate an entity type.
     *
     * @return void
     */
    public function testDuplicateEntityTypeEndpoint()
    {
        $entityType = EntityType::find(3)->load('sub_entity_types');
        $layMax = AvailableLayer::where('is_overlay', true)->max('position');
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/editor/dm/entity_type/3/duplicate');

        $newEntityType = EntityType::latest()->first();
        $newEntityTypeLayer = AvailableLayer::latest()->first();

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'thesaurus_url',
            'is_root',
            'created_at',
            'updated_at',
            'sub_entity_types'
        ]);
        $response->assertJson([
            'id' => $newEntityType->id,
            'thesaurus_url' => $entityType->thesaurus_url,
            'is_root' => $entityType->is_root,
            'created_at' => $newEntityType->created_at->toJSON(),
            'updated_at' => $newEntityType->updated_at->toJSON()
        ]);

        $content = json_decode($response->getContent());
        $etSubTypeIds = $entityType->sub_entity_types->pluck('id');
        $this->assertEquals($newEntityType->id, $newEntityTypeLayer->entity_type_id);
        $this->assertEquals($layMax+1, $newEntityTypeLayer->position);
        $this->assertEquals(count($etSubTypeIds), count($content->sub_entity_types));
    }

    // Testing PATCH requests

    /**
     * Test changing label of an entity type.
     *
     * @return void
     */
    public function testRenamingEntityTypeEndpoint()
    {
        $entityType = EntityType::find(3);
        $this->assertEquals('https://spacialist.escience.uni-tuebingen.de/<user-project>/fundstelle#20171220094911', $entityType->thesaurus_url);

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->patch('/api/v1/editor/dm/entity_type/3/label', [
                'label' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/erhaltung#20171220100437'
            ]);

        $response->assertStatus(204);

        $entityType = EntityType::find(3);
        $this->assertEquals('https://spacialist.escience.uni-tuebingen.de/<user-project>/erhaltung#20171220100437', $entityType->thesaurus_url);
    }

    /**
     * Test reordering attributes of an entity type (id=4).
     *
     * @return void
     */
    public function testRoorderEntityTypeAttributesEndpoint()
    {
        $entityType = EntityType::find(3)->load('attributes');
        foreach($entityType->attributes as $a) {
            if($a->attribute_id == 14) {
                $this->assertEquals(1, $a->pivot->position);
            } else if($a->attribute_id == 16) {
                $this->assertEquals(2, $a->pivot->position);
            } else if($a->attribute_id == 17) {
                $this->assertEquals(3, $a->pivot->position);
            } else if($a->attribute_id == 10) {
                $this->assertEquals(4, $a->pivot->position);
            } else if($a->attribute_id == 13) {
                $this->assertEquals(5, $a->pivot->position);
            }
        }

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->patch('/api/v1/editor/dm/entity_type/4/attribute/14/position', [
                'position' => 4
            ]);

        $response->assertStatus(204);

        $entityType = EntityType::find(3)->load('attributes');
        foreach($entityType->attributes as $a) {
            if($a->attribute_id == 14) {
                $this->assertEquals(4, $a->pivot->position);
            } else if($a->attribute_id == 16) {
                $this->assertEquals(1, $a->pivot->position);
            } else if($a->attribute_id == 17) {
                $this->assertEquals(2, $a->pivot->position);
            } else if($a->attribute_id == 10) {
                $this->assertEquals(3, $a->pivot->position);
            } else if($a->attribute_id == 13) {
                $this->assertEquals(5, $a->pivot->position);
            }
        }

        // Testing again with higher position
        $this->refreshToken($response);

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->patch('/api/v1/editor/dm/entity_type/4/attribute/13/position', [
                'position' => 1
            ]);

        $response->assertStatus(204);

        $entityType = EntityType::find(3)->load('attributes');
        foreach($entityType->attributes as $a) {
            if($a->attribute_id == 14) {
                $this->assertEquals(5, $a->pivot->position);
            } else if($a->attribute_id == 16) {
                $this->assertEquals(2, $a->pivot->position);
            } else if($a->attribute_id == 17) {
                $this->assertEquals(3, $a->pivot->position);
            } else if($a->attribute_id == 10) {
                $this->assertEquals(4, $a->pivot->position);
            } else if($a->attribute_id == 13) {
                $this->assertEquals(1, $a->pivot->position);
            }
        }


        // Testing again with same position (nothing should happen)
        $this->refreshToken($response);

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->patch('/api/v1/editor/dm/entity_type/4/attribute/14/position', [
                'position' => 4
            ]);

        $response->assertStatus(204);

        $entityType = EntityType::find(3)->load('attributes');
        foreach($entityType->attributes as $a) {
            if($a->attribute_id == 14) {
                $this->assertEquals(4, $a->pivot->position);
            } else if($a->attribute_id == 16) {
                $this->assertEquals(1, $a->pivot->position);
            } else if($a->attribute_id == 17) {
                $this->assertEquals(2, $a->pivot->position);
            } else if($a->attribute_id == 10) {
                $this->assertEquals(3, $a->pivot->position);
            } else if($a->attribute_id == 13) {
                $this->assertEquals(5, $a->pivot->position);
            }
        }
    }

    /**
     * Test adding dependency to an attribute of an entity type (id=4).
     *
     * @return void
     */
    public function testAddDependencyToEntiyTypeAttributeEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->patch('/api/v1/editor/dm/entity_type/4/attribute/14/dependency', [
                'd_attribute' => 13,
                'd_operator' => '=',
                'd_value' => 'Test Value'
            ]);

        $response->assertStatus(204);

        // id for entity_type_id = 4 AND attribute_id = 14 is 9
        $entityAttribute = EntityAttribute::find(9)->load('attribute');
        $dep = json_decode($entityAttribute->depends_on);
        $exp = new \stdClass;
        $expDep = new \stdClass;
        $expDep->operator = '=';
        $expDep->value = 'Test Value';
        $expDep->dependant = 14;
        $exp->{13} = $expDep;
        $this->assertEquals($exp, $dep);
    }

    /**
     * Test deleting an entity type (id=4).
     *
     * @return void
     */
    public function testDeleteEntityTypeEndpoint()
    {
        $etCnt = EntityType::count();
        $this->assertEquals(5, $etCnt);
        $eaCnt = EntityAttribute::count();
        $this->assertEquals(23, $eaCnt);
        $eCnt = Entity::count();
        $this->assertEquals(8, $eCnt);
        $avCnt = AttributeValue::count();
        $this->assertEquals(25, $avCnt);
        $alCnt = AvailableLayer::count();
        $this->assertEquals(8, $alCnt);

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->delete('/api/v1/editor/dm/entity_type/4');

        $response->assertStatus(204);

        $etCnt = EntityType::count();
        $this->assertEquals(4, $etCnt);
        $eaCnt = EntityAttribute::count();
        $this->assertEquals(18, $eaCnt);
        $eCnt = Entity::count();
        $this->assertEquals(4, $eCnt);
        $avCnt = AttributeValue::count();
        $this->assertEquals(7, $avCnt);
        $alCnt = AvailableLayer::count();
        $this->assertEquals(7, $alCnt);
    }

    /**
     * Test deleting an attribute (id=12).
     *
     * @return void
     */
    public function testDeleteAttributeEndpoint()
    {
        $eaCnt = EntityAttribute::count();
        $this->assertEquals(23, $eaCnt);
        $avCnt = AttributeValue::count();
        $this->assertEquals(25, $avCnt);
        $aCnt = Attribute::count();
        $this->assertEquals(18, $aCnt);

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->delete('/api/v1/editor/dm/attribute/12');

        $response->assertStatus(204);

        $eaCnt = EntityAttribute::count();
        $this->assertEquals(21, $eaCnt);
        $avCnt = AttributeValue::count();
        $this->assertEquals(21, $avCnt);
        $aCnt = Attribute::count();
        $this->assertEquals(17, $aCnt);
    }

    /**
     * Test deleting an attribute (id=11) from an entity type (id=5).
     *
     * @return void
     */
    public function testDeleteAttributeFromEntityTypeEndpoint()
    {
        $eaCnt = EntityAttribute::count();
        $this->assertEquals(23, $eaCnt);
        $avCnt = AttributeValue::count();
        $this->assertEquals(25, $avCnt);
        $entityType = EntityType::find(5)->load('attributes');
        foreach($entityType->attributes as $a) {
            if($a->id == 12) {
                $this->assertEquals(1, $a->pivot->position);
            } else if($a->id == 9) {
                $this->assertEquals(2, $a->pivot->position);
            } else if($a->id == 11) {
                $this->assertEquals(3, $a->pivot->position);
            } else if($a->id == 2) {
                $this->assertEquals(4, $a->pivot->position);
            } else if($a->id == 3) {
                $this->assertEquals(5, $a->pivot->position);
            } else if($a->id == 5) {
                $this->assertEquals(6, $a->pivot->position);
            } else if($a->id == 19) {
                $this->assertEquals(7, $a->pivot->position);
            } else if($a->id == 4) {
                $this->assertEquals(8, $a->pivot->position);
            } else if($a->id == 13) {
                $this->assertEquals(9, $a->pivot->position);
            }
        }

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->delete('/api/v1/editor/dm/entity_type/5/attribute/11');

        $response->assertStatus(204);

        $eaCnt = EntityAttribute::count();
        $this->assertEquals(22, $eaCnt);
        $avCnt = AttributeValue::count();
        $this->assertEquals(22, $avCnt);
        $entityType = EntityType::find(5)->load('attributes');
        foreach($entityType->attributes as $a) {
            if($a->id == 12) {
                $this->assertEquals(1, $a->pivot->position);
            } else if($a->id == 9) {
                $this->assertEquals(2, $a->pivot->position);
            } else if($a->id == 2) {
                $this->assertEquals(3, $a->pivot->position);
            } else if($a->id == 3) {
                $this->assertEquals(4, $a->pivot->position);
            } else if($a->id == 5) {
                $this->assertEquals(5, $a->pivot->position);
            } else if($a->id == 19) {
                $this->assertEquals(6, $a->pivot->position);
            } else if($a->id == 4) {
                $this->assertEquals(7, $a->pivot->position);
            } else if($a->id == 13) {
                $this->assertEquals(8, $a->pivot->position);
            }
        }
    }

    // Testing exceptions and permissions

    /**
     *
     *
     * @return void
     */
    public function testPermissions()
    {
        User::first()->roles()->detach();

        $calls = [
            ['url' => '/entity_type/1', 'error' => 'You do not have the permission to get an entity type\'s data', 'verb' => 'get'],
            ['url' => '/entity_type/1/attribute', 'error' => 'You do not have the permission to view entity data', 'verb' => 'get'],
            ['url' => '/attribute/1/selection', 'error' => 'You do not have the permission to view entity data', 'verb' => 'get'],
            ['url' => '/dm/entity_type/top', 'error' => 'You do not have the permission to view entity data', 'verb' => 'get'],
            ['url' => '/dm/attribute', 'error' => 'You do not have the permission to view entity data', 'verb' => 'get'],
            ['url' => '/dm/entity_type', 'error' => 'You do not have the permission to create a new entity type', 'verb' => 'post'],
            ['url' => '/dm/1/relation', 'error' => 'You do not have the permission to modify entity relations', 'verb' => 'post'],
            ['url' => '/dm/attribute', 'error' => 'You do not have the permission to add attributes', 'verb' => 'post'],
            ['url' => '/dm/entity_type/1/attribute', 'error' => 'You do not have the permission to add attributes to an entity type', 'verb' => 'post'],
            ['url' => '/dm/entity_type/1/duplicate', 'error' => 'You do not have the permission to duplicate an entity type', 'verb' => 'post'],
            ['url' => '/dm/entity_type/1/label', 'error' => 'You do not have the permission to modify entity-type labels', 'verb' => 'patch'],
            ['url' => '/dm/entity_type/1/attribute/1/position', 'error' => 'You do not have the permission to reorder attributes', 'verb' => 'patch'],
            ['url' => '/dm/entity_type/1/attribute/1/dependency', 'error' => 'You do not have the permission to add/modify attribute dependencies', 'verb' => 'patch'],
            ['url' => '/dm/entity_type/1', 'error' => 'You do not have the permission to delete entity types', 'verb' => 'delete'],
            ['url' => '/dm/attribute/1', 'error' => 'You do not have the permission to delete attributes', 'verb' => 'delete'],
            ['url' => '/dm/entity_type/1/attribute/1', 'error' => 'You do not have the permission to remove attributes from entity types', 'verb' => 'delete'],
        ];

        foreach($calls as $c) {
            $response = $this->withHeaders([
                    'Authorization' => "Bearer $this->token"
                ])
                ->json($c['verb'], '/api/v1/editor' . $c['url']);

            $response->assertStatus(403);
            $response->assertSimilarJson([
                'error' => $c['error']
            ]);

            $this->refreshToken($response);
        }
    }
    /**
     *
     *
     * @return void
     */
    public function testExceptions()
    {
        $calls = [
            ['url' => '/entity_type/99', 'error' => 'This entity-type does not exist', 'verb' => 'get'],
            ['url' => '/attribute/99/selection', 'error' => 'This attribute does not exist', 'verb' => 'get'],
            ['url' => '/dm/99/relation', 'error' => 'This entity-type does not exist', 'verb' => 'post'],
            ['url' => '/dm/entity_type/99/duplicate', 'error' => 'This entity-type does not exist', 'verb' => 'post'],
            ['url' => '/dm/entity_type/99/label', 'error' => 'This entity-type does not exist', 'verb' => 'patch'],
            ['url' => '/dm/entity_type/1/attribute/99/position', 'error' => 'Entity Attribute not found', 'verb' => 'patch'],
            ['url' => '/dm/entity_type/1/attribute/99/dependency', 'error' => 'Entity Attribute not found', 'verb' => 'patch'],
            ['url' => '/dm/entity_type/99', 'error' => 'This entity-type does not exist', 'verb' => 'delete'],
            ['url' => '/dm/attribute/99', 'error' => 'This attribute does not exist', 'verb' => 'delete'],
            ['url' => '/dm/entity_type/1/attribute/99', 'error' => 'Entity Attribute not found', 'verb' => 'delete'],
        ];

        foreach($calls as $c) {
            $response = $this->withHeaders([
                    'Authorization' => "Bearer $this->token"
                ])
                ->json($c['verb'], '/api/v1/editor' . $c['url'], [
                    'label' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/fundstelle#20171220094911',
                    'position' => 1,
                    'd_attribute' => 15,
                    'd_operator' => '=',
                    'd_value' => 'NoValue',
                ]);

            $response->assertStatus(400);
            $response->assertSimilarJson([
                'error' => $c['error']
            ]);

            $this->refreshToken($response);
        }
    }
}
