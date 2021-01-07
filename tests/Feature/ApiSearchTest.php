<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithoutMiddleware;

use Illuminate\Support\Facades\Storage;

use App\File;
use App\User;
use App\ThLanguage;
use App\Bibliography;

class ApiSearchTest extends TestCase
{
    // Testing GET requests

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGlobalSearchEntityEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/search?q=!e inv.');

        $response->assertJsonCount(3);
        $response->assertJsonStructure([
            [
                'id',
                'name',
                'entity_type_id',
                'root_entity_id',
                'geodata_id',
                'rank',
                'user_id',
                'created_at',
                'updated_at',
                'relevance',
                'group',
                'parentIds'
            ]
        ]);

        $response->assertJsonFragment([
            'name' => 'Inv. 1234',
            'relevance' => 60,
            'group' => 'entities'
        ]);
        $response->assertJsonFragment([
            'name' => 'Inv. 124',
            'relevance' => 60,
            'group' => 'entities'
        ]);
        $response->assertJsonFragment([
            'name' => 'Inv. 31',
            'relevance' => 60,
            'group' => 'entities'
        ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGlobalSearchGeodataEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/search?q=!g 8.917369');

        $response->assertJsonCount(1);
        $response->assertJsonStructure([
            [
                'id',
                'geom',
                'color',
                'user_id',
                'created_at',
                'updated_at',
                'relevance',
                'group'
            ]
        ]);
        $response->assertJson([
            [
                'id' => 3,
                'geom' => [
                    'type' => 'Point',
                    'coordinates' => [
                        8.917369, 48.541572
                    ]
                ],
                'color' => null,
                'user_id' => 1,
                'relevance' => 10,
                'group' => 'geodata'
            ]
        ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGlobalSearchBibliographyEndpoint()
    {
        $fields = [
            'type' => 'article',
            'citekey' => '',
            'title' => 'Testing API',
            'author' => 'API (Inv.) Tester',
            'note' => 'Test Inv. Match Entity Name',
            'user_id' => 1,
            'year' => 2009
        ];
        $fields['citekey'] = Bibliography::computeCitationKey($fields);
        $bibEntry = new Bibliography();
        foreach($fields as $k => $v) {
            $bibEntry->{$k} = $v;
        }
        $bibEntry->save();

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/search?q=!b iNv.');

        $response->assertJsonCount(1);
        $response->assertJsonStructure([
            [
                'id',
                'type',
                'citekey',
                'title',
                'author',
                'editor',
                'journal',
                'year',
                'pages',
                'volume',
                'number',
                'booktitle',
                'publisher',
                'address',
                'misc',
                'howpublished',
                'annote',
                'chapter',
                'crossref',
                'edition',
                'institution',
                'key',
                'month',
                'note',
                'organization',
                'school',
                'series',
                'user_id',
                'created_at',
                'updated_at',
                'relevance',
                'group'
            ]
        ]);
        $response->assertJson([
            [
                'type' => 'article',
                'citekey' => 'Ap:2009',
                'title' => 'Testing API',
                'author' => 'API (Inv.) Tester',
                'year' => '2009',
                'note' => 'Test Inv. Match Entity Name',
                'user_id' => 1,
                'relevance' => 15,
                'group' => 'bibliography'
            ]
        ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGlobalSearchFileEndpoint()
    {
        $nowTs = time();
        $now = date('Y-m-d H:i:s', $nowTs);
        $fields = [
            'name' => 'test_inv.jpg',
            'thumb' => 'test_inv_thumb.jpg',
            'modified' => $now,
            'created' => $now,
            'description' => 'describes the test file',
            'mime_type' => 'image/jpg',
            'user_id' => 1
        ];
        Storage::fake('public');
        UploadedFile::fake()->image('test_inv.jpg');
        UploadedFile::fake()->image('test_inv_thumb.jpg');
        $file = new File();
        foreach($fields as $k => $v) {
            $file->{$k} = $v;
        }
        $file->save();

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/search?q=!f INV.');

        $response->assertJsonCount(1);
        $response->assertJsonStructure([
            [
                'id',
                'name',
                'modified',
                'created',
                'thumb',
                'cameraname',
                'copyright',
                'description',
                'mime_type',
                'user_id',
                'created_at',
                'updated_at',
                'relevance',
                'group',
                'url',
                'thumb_url',
                'created_unix',
                'category',
                'exif'
            ]
        ]);
        $response->assertJson([
            [
                'name' => 'test_inv.jpg',
                'modified' => $now,
                'created' => $now,
                'thumb' => 'test_inv_thumb.jpg',
                'cameraname' => null,
                'copyright' => null,
                'description' => 'describes the test file',
                'mime_type' => 'image/jpg',
                'user_id' => 1,
                'relevance' => 10,
                'group' => 'files',
                'created_unix' => $nowTs,
                'category' => 'image',
                'exif' => null
            ]
        ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGlobalSearchAllEndpoint()
    {
        $nowTs = time();
        $now = date('Y-m-d H:i:s', $nowTs);
        $fields = [
            'name' => 'test_inv.jpg',
            'thumb' => 'test_inv_thumb.jpg',
            'modified' => $now,
            'created' => $now,
            'description' => 'describes the test file',
            'mime_type' => 'image/jpg',
            'user_id' => 1
        ];
        Storage::fake('public');
        UploadedFile::fake()->image('test_inv.jpg');
        UploadedFile::fake()->image('test_inv_thumb.jpg');
        $file = new File();
        foreach($fields as $k => $v) {
            $file->{$k} = $v;
        }
        $file->save();

        $fields = [
            'type' => 'article',
            'citekey' => '',
            'title' => 'Testing API',
            'author' => 'API (Inv.) Tester',
            'note' => 'Test Inv. Match Entity Name',
            'user_id' => 1,
            'year' => 2009
        ];
        $fields['citekey'] = Bibliography::computeCitationKey($fields);
        $bibEntry = new Bibliography();
        foreach($fields as $k => $v) {
            $bibEntry->{$k} = $v;
        }
        $bibEntry->save();

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/search?q=inV.');

        $response->assertJsonCount(5);

        $response->assertJsonFragment([
            'name' => 'Inv. 1234',
            'relevance' => 60,
            'group' => 'entities'
        ]);
        $response->assertJsonFragment([
            'name' => 'Inv. 124',
            'relevance' => 60,
            'group' => 'entities'
        ]);
        $response->assertJsonFragment([
            'name' => 'Inv. 31',
            'relevance' => 60,
            'group' => 'entities'
        ]);
        $response->assertJsonFragment([
            'name' => 'test_inv.jpg',
            'modified' => $now,
            'created' => $now,
            'thumb' => 'test_inv_thumb.jpg',
            'cameraname' => null,
            'copyright' => null,
            'description' => 'describes the test file',
            'mime_type' => 'image/jpg',
            'user_id' => 1,
            'relevance' => 10,
            'group' => 'files',
            'created_unix' => $nowTs,
            'category' => 'image',
            'exif' => null
        ]);
        $response->assertJsonFragment([
            'type' => 'article',
            'citekey' => 'Ap:2009',
            'title' => 'Testing API',
            'author' => 'API (Inv.) Tester',
            'year' => '2009',
            'note' => 'Test Inv. Match Entity Name',
            'user_id' => 1,
            'relevance' => 15,
            'group' => 'bibliography'
        ]);

        $this->refreshToken($response);
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/search?q=48.541572');

        $response->assertJsonCount(1);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testEntitySearchEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/search/entity?q=Inv.');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
        $response->assertJsonStructure([
            [
                'id',
                'name',
                'entity_type_id',
                'root_entity_id',
                'geodata_id',
                'rank',
                'user_id',
                'created_at',
                'updated_at',
                'ancestors'
            ]
        ]);

        $response->assertJson([
            ['name' => 'Inv. 1234'],
            ['name' => 'Inv. 124'],
            ['name' => 'Inv. 31']
        ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLabelSearchEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/search/label?q=ind');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJsonStructure([
            [
                'id',
                'concept_url',
                'concept_scheme',
                'is_top_concept',
                'user_id',
                'created_at',
                'updated_at',
                'labels'
            ]
        ]);
        $response->assertJson([
            [
                'id' => 3,
                'is_top_concept' => true,
                'labels' => [
                    [
                        'label' => 'Fundobjekt',
                        'language_id' => 1
                    ],
                    [
                        'label' => 'Find',
                        'language_id' => 2
                    ]
                ]
            ],
            [
                'id' => 41,
                'is_top_concept' => false,
                'labels' => [
                    [
                        'label' => 'Kammeindruck',
                        'language_id' => 1
                    ]
                ]
            ],
        ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAttributeSearchEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/search/attribute?q=ung');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonStructure([
            [
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
                'thesaurus_concept'
            ]
        ]);
        $response->assertJson([
            [
                'id' => 7,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/verzierungselement#20171220105440',
                'datatype' => 'string-sc',
                'text' => null,
                'thesaurus_root_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/verzierungselement#20171220105440',
                'parent_id' => 5,
                'recursive' => TRUE,
                'root_attribute_id' => null,
                'thesaurus_concept' => [
                    'id' => 34
                ]
            ]
        ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSelectionSearchEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/search/selection/13');

        $response->assertStatus(200);
        $response->assertJsonCount(5);
        $response->assertJsonStructure([
            [
                'id',
                'concept_url',
                'concept_scheme',
                'is_top_concept',
                'user_id',
                'broader_id',
                'narrower_id',
                'created_at',
                'updated_at'
            ]
        ]);
        $response->assertJson([
            ['id' => 15],
            ['id' => 16],
            ['id' => 17],
            ['id' => 43],
            ['id' => 47]
        ]);
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
            ['url' => '', 'error' => 'You do not have the permission to search global'],
            ['url' => '/entity', 'error' => 'You do not have the permission to search for entities'],
            ['url' => '/label', 'error' => 'You do not have the permission to search for concepts'],
            ['url' => '/selection/12', 'error' => 'You do not have the permission to search for concepts'],
        ];

        foreach($calls as $c) {
            $response = $this->withHeaders([
                    'Authorization' => "Bearer $this->token"
                ])
                ->get('/api/v1/search' . $c['url']);

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
        $lang = User::first()->getLanguage();
        ThLanguage::where('short_name', $lang)->delete();
        $calls = [
            ['url' => '/label', 'error' => 'Your language does not exist in ThesauRex'],
            ['url' => '/selection/99', 'error' => 'This concept does not exist'],
        ];

        foreach($calls as $c) {
            $response = $this->withHeaders([
                    'Authorization' => "Bearer $this->token"
                ])
                ->get('/api/v1/search' . $c['url']);

            $response->assertStatus(400);
            $response->assertSimilarJson([
                'error' => $c['error']
            ]);

            $this->refreshToken($response);
        }
    }
}
