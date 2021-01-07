<?php

namespace Tests\Feature;

use Tests\TestCase;

use App\AvailableLayer;
use App\Entity;
use App\Geodata;
use App\User;

class ApiMapTest extends TestCase
{
    private static $layerAttributes = [
        'id',
        'name',
        'url',
        'type',
        'subdomains',
        'attribution',
        'opacity',
        'layers',
        'styles',
        'format',
        'version',
        'visible',
        'is_overlay',
        'api_key',
        'layer_type',
        'position',
        'entity_type_id',
        'color',
        'created_at',
        'updated_at',
    ];

    private static $geodataAttributes = [
        'id',
        'geom',
        'color',
        'user_id',
        'created_at',
        'updated_at',
    ];

    // Testing GET requests

    /**
     * Test get all data (layers and geodata).
     *
     * @return void
     */
    public function testGetDataEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/map');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'layers' => [
                '*' => self::$layerAttributes
            ],
            'geodata' => [
                '*' => self::$geodataAttributes
            ],
        ]);
        $content = $response->decodeResponseJson();
        $this->assertEquals(8, count($content['layers']));
        $this->assertEquals(4, count($content['geodata']));
    }

    /**
     * Test get all layers.
     *
     * @return void
     */
    public function testGetAllLayersEndpoint()
    {
        // basic aka not-entity-type layers only
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/map/layer?basic=true');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'baselayers' => [
                '*' => self::$layerAttributes
            ],
            'overlays' => [
                '*' => self::$layerAttributes
            ],
        ]);
        $response->assertJson([
            'baselayers' => [
                ['id' => 9],
            ],
            'overlays' => [],
        ]);

        // basic aka not-entity-type layers only (as dictionary)
        $this->refreshToken($response);
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/map/layer?basic=true&d=true');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'baselayers' => [
                '*' => self::$layerAttributes
            ],
            'overlays' => [
                '*' => self::$layerAttributes
            ],
        ]);
        $response->assertJson([
            'baselayers' => [
                '9' => ['id' => 9],
            ],
            'overlays' => [],
        ]);

        // all layers
        $this->refreshToken($response);
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/map/layer');

        $response->assertStatus(200);
        $response->assertJson([
            'baselayers' => [
                ['id' => 9],
            ],
            'overlays' => [
                ['id' => 1],
                ['id' => 4],
                ['id' => 5],
                ['id' => 6],
                ['id' => 7],
                ['id' => 8],
            ],
        ]);
    }

    /**
     * Test get all entity-type layers.
     *
     * @return void
     */
    public function testGetEntityTypeLayersEndpoint()
    {
        // basic aka not-entity-type layers only
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/map/layer/entity');

        $response->assertStatus(200);
        $response->assertJsonCount(6);
        $response->assertJsonStructure([
            '*' => self::$layerAttributes
        ]);
        $response->assertJson([
            ['id' => 1],
            ['id' => 4],
            ['id' => 5],
            ['id' => 6],
            ['id' => 7],
            ['id' => 8],
        ]);
    }

    /**
     * Test get a layer (id=5)
     *
     * @return void
     */
    public function testGetLayerEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/map/layer/5');

        $response->assertStatus(200);
        $response->assertJsonStructure(
            array_merge(
                self::$layerAttributes,
                [
                    'entity_type'
                ]
            )
        );
        $response->assertSimilarJson([
            'id' => 5,
            'name' => '',
            'url' => '',
            'type' => 'Polygon',
            'subdomains' => null,
            'attribution' => null,
            'opacity' => "1",
            'layers' => null,
            'styles' => null,
            'format' => null,
            'version' => null,
            'visible' => true,
            'is_overlay' => true,
            'api_key' => null,
            'layer_type' => null,
            'position' => 3,
            'entity_type_id' => 4,
            'entity_type' => [
                'id' => 4,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/befund#20171220094916',
                'is_root' => false,
                'created_at' => '2017-12-20T10:03:15.000000Z',
                'updated_at' => '2017-12-20T10:03:15.000000Z',
            ],
            'color' => '#3E798B',
            'created_at' => '2017-12-20T10:03:15.000000Z',
            'updated_at' => '2017-12-20T10:03:15.000000Z',
        ]);
    }

    /**
     * Test get geometries of a layer (id=4)
     *
     * @return void
     */
    public function testGetLayerGeometriesEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/map/layer/4/geometry');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJsonStructure([
            '*' => array_merge(
                self::$geodataAttributes,
                [
                    'entity'
                ]
            )
        ]);
        $response->assertJson([
            [
                'id' => 2,
                'entity' => [
                    'id' => 1
                ]
            ],
            [
                'id' => 3,
                'entity' => [
                    'id' => 7
                ]
            ],
        ]);
    }

    /**
     * Test get geometries of unlinked layer (id=1)
     *
     * @return void
     */
    public function testGetUnlinkedLayerGeometriesEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/map/layer/1/geometry');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonStructure([
            '*' => array_merge(
                self::$geodataAttributes,
                [
                    'entity'
                ]
            )
        ]);
        $response->assertJson([
            [
                'id' => 6,
                'entity' => null,
                'created_at' => '2019-03-18T09:46:11.000000Z',
                'updated_at' => '2019-03-18T09:46:11.000000Z',
            ],
        ]);
    }

    /**
     * Test get definition of an srid code (EPSG:4826 (WGS84))
     *
     * @return void
     */
    public function testGetEpsgEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/map/epsg/4826');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'srid',
            'auth_name',
            'auth_srid',
            'srtext',
            'proj4text',
        ]);
        $response->assertSimilarJson([
            'srid' => 4826,
            'auth_name' => 'EPSG',
            'auth_srid' => 4826,
            'srtext' => 'PROJCS["WGS 84 / Cape Verde National",GEOGCS["WGS 84",DATUM["WGS_1984",SPHEROID["WGS 84",6378137,298.257223563,AUTHORITY["EPSG","7030"]],AUTHORITY["EPSG","6326"]],PRIMEM["Greenwich",0,AUTHORITY["EPSG","8901"]],UNIT["degree",0.0174532925199433,AUTHORITY["EPSG","9122"]],AUTHORITY["EPSG","4326"]],PROJECTION["Lambert_Conformal_Conic_2SP"],PARAMETER["standard_parallel_1",15],PARAMETER["standard_parallel_2",16.66666666666667],PARAMETER["latitude_of_origin",15.83333333333333],PARAMETER["central_meridian",-24],PARAMETER["false_easting",161587.83],PARAMETER["false_northing",128511.202],UNIT["metre",1,AUTHORITY["EPSG","9001"]],AXIS["M",EAST],AXIS["P",NORTH],AUTHORITY["EPSG","4826"]]',
            'proj4text' => '+proj=lcc +lat_1=15 +lat_2=16.66666666666667 +lat_0=15.83333333333333 +lon_0=-24 +x_0=161587.83 +y_0=128511.202 +datum=WGS84 +units=m +no_defs ',
        ]);
    }

    /**
     * Test exporting an layer (id=4) as geojson (default)
     *
     * @return void
     */
    public function testExportLayerEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/map/export/4');
        $response->assertStatus(200);
        $collection = json_decode(base64_decode($response->getContent()), true);
        $sub = [
            'type' => 'FeatureCollection',
            'features' => [
                [
                    'type' => 'Feature',
                    'properties' => [
                        'id' => 3,
                        'color' => null
                    ],
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [
                            0 => 8.917369,
                            1 => 48.541572
                        ]
                    ]
                ],
                [
                    'type' => 'Feature',
                    'properties' => [
                        'id' => 2,
                        'color' => null
                    ],
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [
                            0 => 8.911312,
                            1 => 48.544157
                        ]
                    ]
                ],
            ]
        ];
        $this->assertArraySubset($sub, $collection);
    }

    /**
     * Test exporting unlinked layer (id=1) as csv
     *
     * @return void
     */
    public function testExportUnlinkedLayerEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/map/export/1?type=csv&srid=31467');

        $response->assertStatus(200);
        $csv = base64_decode($response->getContent());

        $rows = str_getcsv($csv, "\n");
        $header = $rows[0];
        $data = str_getcsv($rows[1]);
        $this->assertEquals('X,Y,Z,id,color,created_at,updated_at,user_id', $header);
        $this->assertEqualsWithDelta(3493381.810734, round(floatval($data[0]), 6), 0.99);
        $this->assertEqualsWithDelta(5378579.860542, round(floatval($data[1]), 6), 0.99);
        $this->assertEqualsWithDelta(-52.268126, round(floatval($data[2]), 6), 0.49);
        $this->assertEquals('6', $data[3]);
        $this->assertEquals('', $data[4]);
        $this->assertEquals('2019/03/18 09:46:11', $data[5]);
        $this->assertEquals('2019/03/18 09:46:11', $data[6]);
        $this->assertEquals(1, $data[7]);
    }

    // Testing POST requests

    /**
     * Test adding a new geometry
     *
     * @return void
     */
    public function testAddGeometryEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/map', [
                'collection' => '{"type": "FeatureCollection", "features": [{"type": "Feature", "geometry": {"type": "Point", "coordinates": [6.123, 55.357]}}]}',
                'srid' => 4326
            ]);
        $response->assertStatus(200);
        $geodata = Geodata::latest()->first();
        $this->assertEquals(55.3570, $geodata->geom->getLat());
        $this->assertEquals(6.123, $geodata->geom->getLng());
    }

    /**
     * Test get definition of an srid code by it's text (EPSG:4826 (WGS84))
     *
     * @return void
     */
    public function testGetEpsgByTextEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/map/epsg/text', [
                'srtext' => 'PROJCS["WGS 84 / Cape Verde National",GEOGCS["WGS 84",DATUM["WGS_1984",SPHEROID["WGS 84",6378137,298.257223563,AUTHORITY["EPSG","7030"]],AUTHORITY["EPSG","6326"]],PRIMEM["Greenwich",0,AUTHORITY["EPSG","8901"]],UNIT["degree",0.0174532925199433,AUTHORITY["EPSG","9122"]],AUTHORITY["EPSG","4326"]],PROJECTION["Lambert_Conformal_Conic_2SP"],PARAMETER["standard_parallel_1",15],PARAMETER["standard_parallel_2",16.66666666666667],PARAMETER["latitude_of_origin",15.83333333333333],PARAMETER["central_meridian",-24],PARAMETER["false_easting",161587.83],PARAMETER["false_northing",128511.202],UNIT["metre",1,AUTHORITY["EPSG","9001"]],AXIS["M",EAST],AXIS["P",NORTH],AUTHORITY["EPSG","4826"]]'
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'srid',
            'auth_name',
            'auth_srid',
            'srtext',
            'proj4text',
        ]);
        $response->assertSimilarJson([
            'srid' => 4826,
            'auth_name' => 'EPSG',
            'auth_srid' => 4826,
            'srtext' => 'PROJCS["WGS 84 / Cape Verde National",GEOGCS["WGS 84",DATUM["WGS_1984",SPHEROID["WGS 84",6378137,298.257223563,AUTHORITY["EPSG","7030"]],AUTHORITY["EPSG","6326"]],PRIMEM["Greenwich",0,AUTHORITY["EPSG","8901"]],UNIT["degree",0.0174532925199433,AUTHORITY["EPSG","9122"]],AUTHORITY["EPSG","4326"]],PROJECTION["Lambert_Conformal_Conic_2SP"],PARAMETER["standard_parallel_1",15],PARAMETER["standard_parallel_2",16.66666666666667],PARAMETER["latitude_of_origin",15.83333333333333],PARAMETER["central_meridian",-24],PARAMETER["false_easting",161587.83],PARAMETER["false_northing",128511.202],UNIT["metre",1,AUTHORITY["EPSG","9001"]],AXIS["M",EAST],AXIS["P",NORTH],AUTHORITY["EPSG","4826"]]',
            'proj4text' => '+proj=lcc +lat_1=15 +lat_2=16.66666666666667 +lat_0=15.83333333333333 +lon_0=-24 +x_0=161587.83 +y_0=128511.202 +datum=WGS84 +units=m +no_defs ',
        ]);
    }

    /**
     * Test adding a new layer
     *
     * @return void
     */
    public function testAddLayerEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/map/layer', [
                'name' => 'Test Layer',
                'is_overlay' => false
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'url',
            'type',
            'subdomains',
            'attribution',
            'opacity',
            'layers',
            'styles',
            'format',
            'version',
            'visible',
            'is_overlay',
            'api_key',
            'layer_type',
            'position',
            'entity_type_id',
            'color',
            'created_at',
            'updated_at',
        ]);
        $response->assertJson([
            'name' => 'Test Layer',
            'url' => '',
            'type' => '',
            'subdomains' => null,
            'attribution' => null,
            'opacity' => '1',
            'layers' => null,
            'styles' => null,
            'format' => null,
            'version' => null,
            'visible' => true,
            'is_overlay' => false,
            'api_key' => null,
            'layer_type' => null,
            'position' => 3,
            'entity_type_id' => null,
        ]);
    }

    /**
     * Test unlinking a geodate (id=2) from entity (id=1) and re-linking it to another entity (id=3)
     *
     * @return void
     */
    public function testLinkAndUnlinkEndpoint()
    {
        $oldEntity = Entity::find(1);
        $this->assertEquals(2, $oldEntity->geodata_id);
        $newEntity = Entity::find(3);
        $this->assertNull($newEntity->geodata_id);
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->delete('/api/v1/map/link/2/1');

        $response->assertStatus(204);
        $oldEntity = Entity::find(1);
        $this->assertNull($oldEntity->geodata_id);

        $this->refreshToken($response);
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/map/link/2/3');

        $newEntity = Entity::find(3);
        $this->assertEquals(2, $newEntity->geodata_id);
    }

    /**
     * Test patching a geometry (id=2)
     *
     * @return void
     */
    public function testPatchGeodataEndpoint()
    {
        $geodata = Geodata::find(2);
        $this->assertEquals(48.544157, $geodata->geom->getLat());
        $this->assertEquals(8.911312, $geodata->geom->getLng());
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->patch('/api/v1/map/2', [
                'geometry' => '{"type": "Point", "coordinates": [8.915, 48.48]}',
                'srid' => 4326
            ]);
        $response->assertStatus(204);
        $geodata = Geodata::find(2);
        $this->assertEquals(48.48, $geodata->geom->getLat());
        $this->assertEquals(8.915000, $geodata->geom->getLng());
    }

    /**
     * Test patching a layer (id=10)
     *
     * @return void
     */
    public function testPatchLayerEndpoint()
    {
        $layerOne = AvailableLayer::find(9);
        $layerTwo = AvailableLayer::find(10);
        $this->assertEquals('OSM', $layerTwo->name);
        $this->assertTrue(!$layerTwo->visible);
        $this->assertTrue($layerOne->visible);
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->patch('/api/v1/map/layer/10', [
                'name' => 'Was OSM',
                'url' => 'https://{a-c}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                'type' => 'xyz',
                'visible' => true,
            ]);

        $response->assertStatus(204);
        $layerOne = AvailableLayer::find(9);
        $layerTwo = AvailableLayer::find(10);
        $this->assertEquals('Was OSM', $layerTwo->name);
        $this->assertTrue($layerTwo->visible);
        $this->assertTrue(!$layerOne->visible);
    }

    // Testing DELETE requests

    /**
     * Test deleting a geometry (id=3)
     *
     * @return void
     */
    public function testDeleteGeodataEndpoint()
    {
        $cnt = Geodata::count();
        $this->assertEquals(4, $cnt);
        $linkedEntity = Entity::find(7);
        $this->assertEquals(3, $linkedEntity->geodata_id);
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->delete('/api/v1/map/3');

        $response->assertStatus(204);
        $cnt = Geodata::count();
        $this->assertEquals(3, $cnt);
        $linkedEntity = Entity::find(7);
        $this->assertNull($linkedEntity->geodata_id);
    }

    /**
     * Test deleting a layer (id=10)
     *
     * @return void
     */
    public function testDeleteLayerEndpoint()
    {
        $cnt = AvailableLayer::count();
        $this->assertEquals(8, $cnt);
        $this->assertTrue(AvailableLayer::find(10)->exists());
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->delete('/api/v1/map/layer/10');

        $response->assertStatus(204);
        $cnt = AvailableLayer::count();
        $this->assertEquals(7, $cnt);
        $this->assertNull(AvailableLayer::find(10));
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
            ['url' => '', 'error' => 'You do not have the permission to view the geo data', 'verb' => 'get'],
            ['url' => '/layer', 'error' => 'You do not have the permission to view layers', 'verb' => 'get'],
            ['url' => '/layer/entity', 'error' => 'You do not have the permission to view layers', 'verb' => 'get'],
            ['url' => '/layer/1', 'error' => 'You do not have the permission to view a layer', 'verb' => 'get'],
            ['url' => '/layer/1/geometry', 'error' => 'You do not have the permission to view geodata', 'verb' => 'get'],
            ['url' => '/export/1', 'error' => 'You do not have the permission to export layers', 'verb' => 'get'],
            ['url' => '', 'error' => 'You do not have the permission to add geometric data', 'verb' => 'post'],
            ['url' => '/layer', 'error' => 'You do not have the permission to add layers', 'verb' => 'post'],
            ['url' => '/link/1/1', 'error' => 'You do not have the permission to link geo data', 'verb' => 'post'],
            ['url' => '/1', 'error' => 'You do not have the permission to edit geometric data', 'verb' => 'patch'],
            ['url' => '/layer/1', 'error' => 'You do not have the permission to update layers', 'verb' => 'patch'],
            ['url' => '/1', 'error' => 'You do not have the permission to delete geo data', 'verb' => 'delete'],
            ['url' => '/layer/1', 'error' => 'You do not have the permission to delete layers', 'verb' => 'delete'],
            ['url' => '/link/1/1', 'error' => 'You do not have the permission to unlink geo data', 'verb' => 'delete'],
        ];

        foreach($calls as $c) {
            $response = $this->withHeaders([
                    'Authorization' => "Bearer $this->token"
                ])
                ->json($c['verb'], '/api/v1/map' . $c['url']);

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
            ['url' => '/layer/99', 'error' => 'This layer does not exist', 'verb' => 'get'],
            ['url' => '/layer/99/geometry', 'error' => 'This layer does not exist', 'verb' => 'get'],
            ['url' => '/export/99', 'error' => 'This layer does not exist', 'verb' => 'get'],
            ['url' => '/export/9', 'error' => 'This layer does not support export', 'verb' => 'get'],
            ['url' => '/link/99/1', 'error' => 'This geodata does not exist', 'verb' => 'post'],
            ['url' => '/link/3/99', 'error' => 'This entity does not exist', 'verb' => 'post'],
            ['url' => '/link/3/2', 'error' => 'This entity is already linked to a geo object', 'verb' => 'post'],
            ['url' => '/99', 'error' => 'This geodata does not exist', 'verb' => 'patch'],
            ['url' => '/layer/99', 'error' => 'This layer does not exist', 'verb' => 'patch'],
            ['url' => '/99', 'error' => 'This geodata does not exist', 'verb' => 'delete'],
            ['url' => '/layer/99', 'error' => 'This layer does not exist', 'verb' => 'delete'],
            ['url' => '/layer/4', 'error' => 'This layer can not be deleted', 'verb' => 'delete'],
            ['url' => '/link/99/1', 'error' => 'This geodata does not exist', 'verb' => 'delete'],
            ['url' => '/link/3/99', 'error' => 'This entity does not exist', 'verb' => 'delete'],
            ['url' => '/link/3/1', 'error' => 'The entity is not linked to the provided geo object', 'verb' => 'delete'],
        ];

        foreach($calls as $c) {
            $response = $this->withHeaders([
                    'Authorization' => "Bearer $this->token"
                ])
                ->json($c['verb'], '/api/v1/map' . $c['url'], [
                    'geometry' => '{}',
                    'srid' => 4326
                ]);

            $response->assertStatus(400);
            $response->assertSimilarJson([
                'error' => $c['error']
            ]);

            $this->refreshToken($response);
        }
    }
}
