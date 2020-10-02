<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

use App\AttributeValue;
use App\Entity;
use App\Reference;
use App\User;

class ApiEntityTest extends TestCase
{
    // Testing GET requests

    /**
     * Test getting all top entities.
     *
     * @return void
     */
    public function testTopEntityEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/entity/top');

        $response->assertJsonCount(3);
        $response->assertJsonStructure([
            [
                'id',
                'name',
                'entity_type_id',
                'root_entity_id',
                'geodata_id',
                'rank',
                'lasteditor',
                'created_at',
                'updated_at',
                'parentIds',
                'parentNames'
            ]
        ]);
    }

    /**
     * Test getting entity (id=1).
     *
     * @return void
     */
    public function testEntityEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/entity/1');

        $response->assertExactJson([
            'id' => 1,
            'name' => 'Site A',
            'entity_type_id' => 3,
            'root_entity_id' => null,
            'geodata_id' => 2,
            'rank' => 1,
            'lasteditor' => 'Admin',
            'created_at' => '2017-12-20 17:10:34',
            'updated_at' => '2017-12-31 16:10:56',
            'parentIds' => [1],
            'parentNames' => ['Site A']
        ]);
    }

    /**
     * Test getting entity (id=2).
     *
     * @return void
     */
    public function testEntityEndpointId()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/entity/2');

        $response->assertJson([
            'id' => 2,
        ]);
    }

    /**
     * Test getting non-existing entity.
     *
     * @return void
     */
    public function testEntityWrongIdEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/entity/-1');

        $response->assertStatus(404);
    }

    /**
     * Test getting attribute values (id=15) of an entity-type (id=3).
     *
     * @return void
     */
    public function testEntityTypeDataEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/entity/entity_type/3/data/15');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'entity_id',
                'attribute_id',
                'value',
                'lasteditor',
                'created_at',
                'updated_at'
            ]
        ]);
        $response->assertJson([
            1 => [
                'value' => ['Fundstelle A']
            ],
            7 => [
                'value' => ['Kirchberg']
            ],
        ]);
    }

    /**
     * Test getting data of an entity (id=1).
     *
     * @return void
     */
    public function testEntityDataEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/entity/1/data');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonStructure([
            15 => [
                'id',
                'entity_id',
                'attribute_id',
                'value',
                'lasteditor',
                'created_at',
                'updated_at'
            ]
        ]);
        $response->assertJson([
            15 => [
                'value' => ['Fundstelle A']
            ]
        ]);
    }

    /**
     * Test getting moderated data of an entity (id=2).
     *
     * @return void
     */
    public function testEntityDataModeratedEndpoint()
    {
        $attr1 = AttributeValue::find(60);
        $attr2 = AttributeValue::find(61);
        $attr1->moderate('pending-delete', true);
        $modAttr2 = $attr2->moderate('pending', false, true);
        $modAttr2->geography_val = 'POINT(1.23 45.67)';
        $modAttr2->save();

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
            ->get('/api/v1/entity/2/data');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    /**
     * Test getting data of an non-existing entity.
     *
     * @return void
     */
    public function testEntityDataWithWrongIdEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/entity/99/data');

        $response->assertStatus(400);
        $response->assertExactJson([
            'error' => 'This entity does not exist'
        ]);
    }

    /**
     * Test getting data of an attribute (id=15) of an entity (id=1).
     *
     * @return void
     */
    public function testEntityDataWithAttributeEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/entity/1/data/15');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonStructure([
            15 => [
                'id',
                'entity_id',
                'attribute_id',
                'value',
                'lasteditor',
                'created_at',
                'updated_at'
            ]
        ]);
        $response->assertJson([
            15 => [
                'value' => ['Fundstelle A']
            ]
        ]);
    }

    /**
     * Test getting data with wrong attribute ID.
     *
     * @return void
     */
    public function testEntityDataWithWrongAttributeEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/entity/1/data/99');

        $response->assertStatus(400);
        $response->assertExactJson([
            'error' => 'This attribute does not exist'
        ]);
    }

    /**
     * Test getting IDs of all parents (and own id) of an entity (id=5).
     *
     * @return void
     */
    public function testEntityParentIdEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/entity/5/parentIds');

        $response->assertJsonCount(3);
        $response->assertExactJson([
            1, 2, 5
        ]);
    }

    /**
     * Test getting all references for an entity (id=1).
     *
     * @return void
     */
    public function testEntityReferencesEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/entity/1/reference');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJsonStructure([
            '*' => [
                [
                    'id',
                    'entity_id',
                    'attribute_id',
                    'bibliography_id',
                    'description',
                    'lasteditor',
                    'created_at',
                    'updated_at',
                    'bibliography'
                ]
            ]
        ]);
        $response->assertJson([
            'https://spacialist.escience.uni-tuebingen.de/<user-project>/alternativer_name#20171220165047' => [
                [
                    'id' => 1,
                    'entity_id' => 1,
                    'attribute_id' => 15,
                    'bibliography_id' => 1318,
                    'description' => 'See Page 10',
                    'lasteditor' => 'Admin',
                    'created_at' => '2019-03-08 13:36:36',
                    'updated_at' => '2019-03-08 13:36:36',
                    'bibliography' => [
                        'id' => 1318
                    ],
                ],
                [
                    'id' => 2,
                    'entity_id' => 1,
                    'attribute_id' => 15,
                    'bibliography_id' => 1319,
                    'description' => 'Picture on left side of page 12',
                    'lasteditor' => 'Admin',
                    'created_at' => '2019-03-08 13:36:48',
                    'updated_at' => '2019-03-08 13:36:48',
                    'bibliography' => [
                        'id' => 1319
                    ],
                ],
            ],
            'https://spacialist.escience.uni-tuebingen.de/<user-project>/notizen#20171220105603' => [
                [
                    'id' => 3,
                    'entity_id' => 1,
                    'attribute_id' => 13,
                    'bibliography_id' => 1323,
                    'description' => 'Page 10ff is interesting',
                    'lasteditor' => 'Admin',
                    'created_at' => '2019-03-08 13:37:09',
                    'updated_at' => '2019-03-08 13:37:09',
                    'bibliography' => [
                        'id' => 1323
                    ],
                ],
            ]
        ]);
    }

    /**
     * Test getting all sub-entities/children of an entity (id=2).
     *
     * @return void
     */
    public function testEntityParentEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/entity/byParent/2');

        $response->assertJsonCount(3);
        $response->assertJsonStructure([
            [
                'id',
                'name',
                'entity_type_id',
                'root_entity_id',
                'geodata_id',
                'rank',
                'lasteditor',
                'created_at',
                'updated_at',
                'parentIds'
            ]
        ]);
        $response->assertJson([
            [
                'id' => 3
            ],
            [
                'id' => 4
            ],
            [
                'id' => 5
            ],
        ]);
    }

    // Testing POST requets

    /**
    * Test adding a new entity.
    *
    * @return void
    */
    public function testNewEntityEndpoint()
    {
        $cnt = Entity::count();
        $this->assertEquals($cnt, 8);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->post('/api/v1/entity', [
            'name' => 'Unit-Test Entity I',
            'entity_type_id' => 3,
            'root_entity_id' => 1
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'name',
            'entity_type_id',
            'root_entity_id',
            'geodata_id',
            'rank',
            'lasteditor',
            'created_at',
            'updated_at',
            'parentIds'
        ]);
        $response->assertJson([
            'name' => 'Unit-Test Entity I',
            'entity_type_id' => 3,
            'root_entity_id' => 1,
            'rank' => 2,
        ]);

        $cnt = Entity::count();
        $this->assertEquals($cnt, 9);
    }

    /**
    * Test adding a new root entity.
    *
    * @return void
    */
    public function testNewRootEntityEndpoint()
    {
        $cnt = Entity::count();
        $this->assertEquals($cnt, 8);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->post('/api/v1/entity', [
            'name' => 'Unit-Test Entity I',
            'entity_type_id' => 3,
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'name',
            'entity_type_id',
            'root_entity_id',
            'geodata_id',
            'rank',
            'lasteditor',
            'created_at',
            'updated_at',
            'parentIds'
        ]);
        $response->assertJson([
            'name' => 'Unit-Test Entity I',
            'entity_type_id' => 3,
            'root_entity_id' => null,
            'rank' => 4,
        ]);

        $cnt = Entity::count();
        $this->assertEquals($cnt, 9);
    }

    /**
    * Test adding a new reference to an entity (id=2).
    *
    * @return void
    */
    public function testNewReferenceEndpoint()
    {
        $cnt = Reference::count();
        $this->assertEquals($cnt, 3);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->post('/api/v1/entity/2/reference/14', [
            'bibliography_id' => 1337,
            'description' => 'This is a simple test',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'entity_id',
            'attribute_id',
            'bibliography_id',
            'description',
            'lasteditor',
            'created_at',
            'updated_at',
            'bibliography',
        ]);
        $response->assertJson([
            'entity_id' => 2,
            'attribute_id' => 14,
            'bibliography_id' => 1337,
            'description' => 'This is a simple test',
            'lasteditor' => 'Admin',
            'bibliography' => [
                'id' => 1337,
                'type' => 'article',
                'citekey' => 'ScSh:20'
            ],
        ]);

        $cnt = Reference::count();
        $this->assertEquals($cnt, 4);
    }

    // Testing PATCH requets

    /**
     * Test modifying (add, replace, remove) attributes of an entity (id=4).
     *
     * @return void
     */
    public function testPatchAttributesEndpoint()
    {
        $entity = Entity::with('attributes')->find(4);

        foreach($entity->attributes as $attr) {
            if($attr->id == 11) {
                $this->assertEquals(10, $attr->pivot->int_val);
            }
            else if($attr->id == 9) {
                $val = json_decode($attr->pivot->json_val);
                $this->assertEquals(45, $val->B);
                $this->assertEquals(40, $val->H);
                $this->assertEquals('cm', $val->unit);
            }
        }

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->patch('/api/v1/entity/4/attributes', [
            [
                'params' => [
                    'aid' => 9,
                ],
                'op' => 'remove'
            ],
            [
                'params' => [
                    'aid' => 11,
                ],
                'op' => 'replace',
                'value' => 2
            ],
            [
                'params' => [
                    'aid' => 19,
                ],
                'op' => 'add',
                'value' => 'Test'
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'entity_type_id',
            'root_entity_id',
            'geodata_id',
            'rank',
            'lasteditor',
            'created_at',
            'updated_at',
            'parentIds'
        ]);

        $entity = Entity::with('attributes')->find(4);
        foreach($entity->attributes as $attr) {
            if($attr->id == 37) {
                $this->assertEquals(2, $attr->value);
            }
            else if($attr->id == 39) {
                $this->assertTrue(false);
            } else if($attr->attribute_id == 19) {
                $this->assertEquals('Test', $attr->value);
            }
        }
    }

    /**
     * Test modifying (add, replace, remove) attributes of an entity (id=3) with moderation enabled.
     *
     * @return void
     */
    public function testPatchAttributesModeratedEndpoint()
    {
        $user = User::find(1);

        // set all user roles as moderated
        foreach($user->roles as $role) {
            $role->moderated = true;
            $role->save();
        }

        $attrs = AttributeValue::where('entity_id', 3)
            ->withModerated()
            ->get();
        $this->assertEquals(6, count($attrs));

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
            ->patch('/api/v1/entity/3/attributes', [
                [
                    'params' => [
                        'aid' => 2,
                    ],
                    'op' => 'remove'
                ],
                [
                    'params' => [
                        'aid' => 4,
                    ],
                    'op' => 'replace',
                    'value' => 2
                ],
                [
                    'params' => [
                        'aid' => 19,
                    ],
                    'op' => 'add',
                    'value' => 'Test'
                ]
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'entity_type_id',
            'root_entity_id',
            'geodata_id',
            'rank',
            'lasteditor',
            'created_at',
            'updated_at',
            'parentIds'
        ]);

        $attrs = AttributeValue::where('entity_id', 3)
            ->withModerated()
            ->get();
        $this->assertEquals(8, count($attrs));
        $attrs = AttributeValue::where('entity_id', 3)
            ->onlyModerated()
            ->get();
        $this->assertEquals(3, count($attrs));
    }

    /**
     * Test accepting/denying a moderated value.
     *
     * @return void
     */
    public function testHandleModerationEndpoint()
    {
        $attrs = AttributeValue::where('entity_id', 3)
            ->withModerated()
            ->get();
        $this->assertEquals(6, count($attrs));

        $attr1 = AttributeValue::where('entity_id', 3)
            ->where('attribute_id', 2)
            ->first();
        $attr2 = AttributeValue::where('entity_id', 3)
            ->where('attribute_id', 4)
            ->first();
        $this->assertEquals(1, $attr2->int_val);
        $attr1->moderate('pending-delete', true);
        $updAttr2 = $attr2->moderate('pending', false, true);
        $updAttr2->int_val = 42;
        $updAttr2->save();
        $attr2 = AttributeValue::where('entity_id', 3)
            ->where('attribute_id', 4)
            ->onlyModerated()
            ->first();
        $this->assertEquals(42, $attr2->int_val);

        $attrs = AttributeValue::where('entity_id', 3)
            ->withModerated()
            ->get();
        $this->assertEquals(7, count($attrs));

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
            ->patch('/api/v1/entity/3/attribute/2/moderation', [
                'action' => 'accept'
            ]);

        $response->assertStatus(204);

        $this->refreshToken($response);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
            ->patch('/api/v1/entity/3/attribute/4/moderation', [
                'action' => 'deny'
            ]);

        $response->assertStatus(204);

        $attrs = AttributeValue::where('entity_id', 3)
            ->withModerated()
            ->get();
        $this->assertEquals(5, count($attrs));

        $attr1 = AttributeValue::where('entity_id', 3)
            ->where('attribute_id', 2)
            ->first();
        $attr2 = AttributeValue::where('entity_id', 3)
            ->where('attribute_id', 4)
            ->first();
        $this->assertNull($attr1);
        $this->assertEquals(1, $attr2->int_val);
    }

    /**
    * Test setting wrong values for epoch attribute of an entity (id=2).
    *
    * @return void
    */
    public function testPatchWrongAttributesEndpoint()
    {
        $entity = Entity::with('attributes')->find(2);

        foreach($entity->attributes as $attr) {
            if($attr->id == 17) {
                $val = json_decode($attr->pivot->json_val);
                $this->assertEquals(340, $val->start);
                $this->assertEquals(300, $val->end);
                $this->assertEquals('bc', $val->startLabel);
                $this->assertEquals('bc', $val->endLabel);
            }
        }

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->patch('/api/v1/entity/2/attributes', [
            [
                'params' => [
                    'id' => 62,
                    'aid' => 17,
                    'cid' => 2
                ],
                'op' => 'replace',
                'value' => [
                    'startLabel' => 'ad',
                    'endLabel' => 'bc',
                    'start' => 400,
                    'end' => 150,
                    'epoch' => [
                        'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/eisenzeit#20171220165409'
                    ]
                ]
            ],
        ]);

        $response->assertStatus(422);
        $response->assertExactJson([
            'error' => 'Start date of a time period must not be after it\'s end date'
        ]);
        $this->refreshToken($response);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->patch('/api/v1/entity/2/attributes', [
            [
                'params' => [
                    'id' => 62,
                    'aid' => 17,
                    'cid' => 2
                ],
                'op' => 'replace',
                'value' => [
                    'startLabel' => 'ad',
                    'endLabel' => 'ad',
                    'start' => 400,
                    'end' => 150,
                    'epoch' => [
                        'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/eisenzeit#20171220165409'
                    ]
                ]
            ],
        ]);

        $response->assertStatus(422);
        $response->assertExactJson([
            'error' => 'Start date of a time period must not be after it\'s end date'
        ]);
        $this->refreshToken($response);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->patch('/api/v1/entity/2/attributes', [
            [
                'params' => [
                    'id' => 62,
                    'aid' => 17,
                    'cid' => 2
                ],
                'op' => 'replace',
                'value' => [
                    'startLabel' => 'bc',
                    'endLabel' => 'bc',
                    'start' => 100,
                    'end' => 150,
                    'epoch' => [
                        'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/eisenzeit#20171220165409'
                    ]
                ]
            ],
        ]);

        $response->assertStatus(422);
        $response->assertExactJson([
            'error' => 'Start date of a time period must not be after it\'s end date'
        ]);
        $this->refreshToken($response);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->patch('/api/v1/entity/2/attributes', [
            [
                'params' => [
                    'id' => 62,
                    'aid' => 17,
                    'cid' => 2
                ],
                'op' => 'replace',
                'value' => [
                    'startLabel' => 'bc',
                    'endLabel' => 'bc',
                    'start' => 400,
                    'end' => 300,
                    'epoch' => [
                        'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/eisenzeit#20171220165409'
                    ]
                ]
            ],
        ]);

        $response->assertStatus(200);

        $entity = Entity::with('attributes')->find(2);
        foreach($entity->attributes as $attr) {
            if($attr->id == 17) {
                $val = json_decode($attr->pivot->json_val);
                $this->assertEquals(400, $val->start);
                $this->assertEquals(300, $val->end);
                $this->assertEquals('bc', $val->startLabel);
                $this->assertEquals('bc', $val->endLabel);
            }
        }
    }

    /**
    * Test changing certainty of an attribute (id=5) of an entity (id=8).
    *
    * @return void
    */
    public function testPatchAttributeEndpoint()
    {
        $attrValue = AttributeValue::where('entity_id', 8)
            ->where('attribute_id', 5)
            ->first();
        $this->assertEquals(100, $attrValue->certainty);
        $this->assertNull($attrValue->certainty_description);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->patch('/api/v1/entity/8/attribute/5', [
            'certainty' => 37,
            'certainty_description' => 'This is a test'
        ]);

        $response->assertStatus(204);

        $attrValue = AttributeValue::where('entity_id', 8)
            ->where('attribute_id', 5)
            ->first();
        $this->assertEquals(37, $attrValue->certainty);
        $this->assertEquals('This is a test', $attrValue->certainty_description);
    }

    /**
    * Test renaming an entity from 'Site A' to 'Site A_renamed'.
    *
    * @return void
    */
    public function testPatchRenameEntityEndpoint()
    {
        $entity = Entity::find(1);
        $this->assertEquals('Site A', $entity->name);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->patch('/api/v1/entity/1/name', [
            'name' => 'Site A_renamed'
        ]);

        $response->assertStatus(204);

        $entity = Entity::find(1);
        $this->assertEquals('Site A_renamed', $entity->name);
    }

    /**
    * Test moving an entity (id=1) to another entity (id=7).
    *
    * @return void
    */
    public function testPatchMoveEntityEndpoint()
    {
        $entity = Entity::find(1);
        $this->assertEquals('Site A', $entity->name);
        $this->assertNull($entity->root_entity_id);
        $anotherEntity = Entity::find(7);
        $this->assertEquals('Site B', $anotherEntity->name);
        $this->assertNull($anotherEntity->root_entity_id);
        $this->assertEquals(2, $anotherEntity->rank);
        $YetAnotherEntity = Entity::find(8);
        $this->assertEquals('Fund 12', $YetAnotherEntity->name);
        $this->assertEquals(1, $YetAnotherEntity->rank);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->patch('/api/v1/entity/1/rank', [
            'rank' => 1,
            'parent_id' => 7
        ]);

        $response->assertStatus(204);

        $entity = Entity::find(1);
        $anotherEntity = Entity::find(7);
        $YetAnotherEntity = Entity::find(8);
        $this->assertEquals(7, $entity->root_entity_id);
        $this->assertEquals(7, $YetAnotherEntity->root_entity_id);
        $this->assertEquals(1, $entity->rank);
        $this->assertEquals(1, $anotherEntity->rank);
        $this->assertEquals(2, $YetAnotherEntity->rank);
    }

    /**
    * Test editing the description of an entity (id=2).
    *
    * @return void
    */
    public function testPatchReferenceEndpoint()
    {
        $reference = Reference::find(2);
        $this->assertEquals(1, $reference->entity_id);
        $this->assertEquals(15, $reference->attribute_id);
        $this->assertEquals(1319, $reference->bibliography_id);
        $this->assertEquals('Picture on left side of page 12', $reference->description);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->patch('/api/v1/entity/reference/2', [
            'description' => 'Page 12 was wrong, it is Page 15!',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'entity_id',
            'attribute_id',
            'bibliography_id',
            'description',
            'lasteditor',
            'created_at',
            'updated_at',
        ]);
        $response->assertJson([
            'entity_id' => 1,
            'attribute_id' => 15,
            'bibliography_id' => 1319,
            'description' => 'Page 12 was wrong, it is Page 15!',
            'lasteditor' => 'Admin',
        ]);
    }

    // Testing DELETE requets

    /**
    * Test deleting an entity (id=1) and all it's sub-entities.
    *
    * @return void
    */
    public function testDeleteEntityEndpoint()
    {
        $cnt = Entity::count();
        $this->assertEquals($cnt, 8);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->delete('/api/v1/entity/1');

        $response->assertStatus(204);

        $cnt = Entity::count();
        $this->assertEquals($cnt, 3);
    }

    /**
    * Test deleting an entity (id=8) with no sub-entities.
    *
    * @return void
    */
    public function testDeleteEntityEightEndpoint()
    {
        $cnt = Entity::count();
        $this->assertEquals($cnt, 8);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->delete('/api/v1/entity/8');

        $response->assertStatus(204);

        $cnt = Entity::count();
        $this->assertEquals($cnt, 7);
    }

    /**
    * A basic test example.
    *
    * @return void
    */
    public function testDeleteEntityWrongIdEndpoint()
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->delete('/api/v1/entity/99');

        $response->assertStatus(400);
        $response->assertExactJson([
            'error' => 'This entity does not exist'
        ]);
    }

    /**
    * Test deleting an reference (id=1).
    *
    * @return void
    */
    public function testDeleteReferenceEndpoint()
    {
        $cnt = Reference::count();
        $this->assertEquals($cnt, 3);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->delete('/api/v1/entity/reference/1');

        $response->assertStatus(204);

        $cnt = Reference::count();
        $this->assertEquals($cnt, 2);
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
            ['url' => '/top', 'error' => 'You do not have the permission to get entities', 'verb' => 'get'],
            ['url' => '/1', 'error' => 'You do not have the permission to get a specific entity', 'verb' => 'get'],
            ['url' => '/entity_type/3/data/14', 'error' => 'You do not have the permission to get an entity\'s data', 'verb' => 'get'],
            ['url' => '/1/data/14', 'error' => 'You do not have the permission to get an entity\'s data', 'verb' => 'get'],
            ['url' => '/1/parentIds', 'error' => 'You do not have the permission to get an entity\'s parent id\'s', 'verb' => 'get'],
            ['url' => '', 'error' => 'You do not have the permission to add a new entity', 'verb' => 'post'],
            ['url' => '/1/attributes', 'error' => 'You do not have the permission to modify an entity\'s data', 'verb' => 'patch'],
            ['url' => '/1/attribute/13', 'error' => 'You do not have the permission to modify an entity\'s data', 'verb' => 'patch'],
            ['url' => '/1/attribute/13/moderation', 'error' => 'You do not have the permission to modify an entity\'s data', 'verb' => 'patch'],
            ['url' => '/1/name', 'error' => 'You do not have the permission to modify an entity\'s data', 'verb' => 'patch'],
            ['url' => '/1/rank', 'error' => 'You do not have the permission to modify an entity', 'verb' => 'patch'],
            ['url' => '/1', 'error' => 'You do not have the permission to delete an entity', 'verb' => 'delete'],

            ['url' => '/99/reference', 'error' => 'You do not have the permission to view references', 'verb' => 'get'],
            ['url' => '/99/reference/99', 'error' => 'You do not have the permission to add references', 'verb' => 'post'],
            ['url' => '/reference/99', 'error' => 'You do not have the permission to edit references', 'verb' => 'patch'],
            ['url' => '/reference/99', 'error' => 'You do not have the permission to delete references', 'verb' => 'delete'],
        ];

        foreach($calls as $c) {
            $response = $this->withHeaders([
                    'Authorization' => "Bearer $this->token"
                ])
                ->json($c['verb'], '/api/v1/entity' . $c['url']);

            $response->assertStatus(403);
            $response->assertExactJson([
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
            ['url' => '/99', 'error' => 'This entity does not exist', 'verb' => 'get'],
            ['url' => '/entity_type/99/data/14', 'error' => 'This entity type does not exist', 'verb' => 'get'],
            ['url' => '/entity_type/3/data/99', 'error' => 'This attribute does not exist', 'verb' => 'get'],
            ['url' => '/99/parentIds', 'error' => 'This entity does not exist', 'verb' => 'get'],
            ['url' => '', 'error' => 'This type is not an allowed sub-type.', 'verb' => 'post', 'data' => [
                    'name' => 'Test Entity',
                    'entity_type_id' => 3,
                    'root_entity_id' => 2,
                ]
            ],
            ['url' => '', 'error' => 'This type is not an allowed root-type.', 'verb' => 'post', 'data' => [
                    'name' => 'Test Entity',
                    'entity_type_id' => 4,
                ]
            ],
            ['url' => '/99/attributes', 'error' => 'This entity does not exist', 'verb' => 'patch'],
            ['url' => '/99/attribute/13', 'error' => 'This entity does not exist', 'verb' => 'patch'],
            ['url' => '/1/attribute/99', 'error' => 'This attribute does not exist', 'verb' => 'patch'],
            ['url' => '/99/name', 'error' => 'This entity does not exist', 'verb' => 'patch', 'data' => [
                    'name' => 'Test'
                ]
            ],
            ['url' => '/99/rank', 'error' => 'This entity does not exist', 'verb' => 'patch', 'data' => [
                    'rank' => 1,
                ]
            ],

            ['url' => '/99/reference', 'error' => 'This entity does not exist', 'verb' => 'get'],
            ['url' => '/99/reference/99', 'error' => 'This entity does not exist', 'verb' => 'post'],
            ['url' => '/1/reference/99', 'error' => 'This attribute does not exist', 'verb' => 'post'],
            ['url' => '/reference/99', 'error' => 'This reference does not exist', 'verb' => 'patch'],
            ['url' => '/reference/99', 'error' => 'This reference does not exist', 'verb' => 'delete'],
        ];

        $dummyData = [
            'bibliography_id' => 1318,
            'description' => 'Test'
        ];
        foreach($calls as $c) {
            $data = array_key_exists('data', $c) ? array_merge($dummyData, $c['data']) : $dummyData;
            $response = $this->withHeaders([
                    'Authorization' => "Bearer $this->token"
                ])
                ->json($c['verb'], '/api/v1/entity' . $c['url'], $data);

            $response->assertStatus(400);
            $response->assertExactJson([
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
    public function testModerationExceptions()
    {
        $attr = AttributeValue::find(69);
        $updAttr = $attr->moderate('pending', false, true);
        $updAttr->save();
        $calls = [
            ['url' => '/1/attributes', 'error' => 'This attribute value does either not exist or is in moderation state.', 'verb' => 'patch', 'data' => [
                    [
                        'op' => 'remove',
                        'params' => ['aid' => 99]
                    ]
                ],
            ],
            ['url' => '/1/attributes', 'error' => 'There is already a value set for this attribute or it is in moderation state.', 'verb' => 'patch', 'data' => [
                    [
                        'op' => 'add',
                        'params' => ['aid' => 15]
                    ]
                ],
            ],
            ['url' => '/1/attributes', 'error' => 'This attribute value is in moderation state. Please accept/deny it or ask a user with appropriate permissions to do so.', 'verb' => 'patch', 'data' => [
                    [
                        'op' => 'replace',
                        'params' => ['aid' => 15]
                    ]
                ],
            ],
            ['url' => '/1/attributes', 'error' => 'Unknown operation', 'verb' => 'patch', 'data' => [
                    [
                        'op' => 'not_implemented',
                        'params' => ['aid' => 15]
                    ]
                ],
            ],
            ['url' => '/99/attribute/13/moderation', 'error' => 'This entity does not exist', 'verb' => 'patch', 'data' => [
                'action' => 'accept'
            ]],
            ['url' => '/1/attribute/99/moderation', 'error' => 'This attribute does not exist', 'verb' => 'patch', 'data' => [
                'action' => 'accept'
            ]],
            ['url' => '/1/attribute/19/moderation', 'error' => 'This attribute value does not exist', 'verb' => 'patch', 'data' => [
                'action' => 'accept'
            ]],
        ];
        foreach($calls as $c) {
            $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
                ->json($c['verb'], '/api/v1/entity' . $c['url'], $c['data']);

            $response->assertStatus(400);
            $response->assertExactJson([
                'error' => $c['error']
            ]);

            $this->refreshToken($response);
        }

        $attrValue = AttributeValue::where('entity_id', 1)
            ->where('attribute_id', 15)
            ->first();
        $attrValue->moderate('pending-delete');

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
            ->patch('/api/v1/entity/1/attribute/15/moderation', [
                'action' => 'not_implemented'
            ]);

        $response->assertStatus(400);
        $response->assertExactJson([
            'error' => 'Unsupported moderation action'
        ]);
    }
}
