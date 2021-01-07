<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

use App\User;
use App\Preference;
use App\UserPreference;

class ApiPreferenceTest extends TestCase
{
    // Testing GET requests

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testPreferenceEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/preference');

        $response->assertJsonCount(10);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'label',
                'value',
                'allow_override',
                'created_at',
                'updated_at'
            ]
        ]);

        $response->assertJson([
            'prefs.gui-language' => [
                'id' => 1,
                'label' => 'prefs.gui-language',
                'value' => 'en',
                'allow_override' => true
            ],
            'prefs.columns' => [
                'id' => 2,
                'label' => 'prefs.columns',
                'value' => [
                    'left' => 2,
                    'center' => 5,
                    'right' => 5
                ],
                'allow_override' => true
            ],
            'prefs.show-tooltips' => [
                'id' => 3,
                'label' => 'prefs.show-tooltips',
                'value' => true,
                'allow_override' => true
            ],
            'prefs.tag-root' => [
                'id' => 4,
                'label' => 'prefs.tag-root',
                'value' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/eigenschaften#20171220100251',
                'allow_override' => false
            ],
            'prefs.load-extensions' => [
                'id' => 5,
                'label' => 'prefs.load-extensions',
                'value' => [
                    'map' => true,
                    'files' => true,
                    'data-analysis' => true,
                ],
                'allow_override' => false
            ],
            'prefs.link-to-thesaurex' => [
                'id' => 6,
                'label' => 'prefs.link-to-thesaurex',
                'value' => '',
                'allow_override' => false
            ],
            'prefs.project-name' => [
                'id' => 7,
                'label' => 'prefs.project-name',
                'value' => 'Spacialist',
                'allow_override' => false
            ],
            'prefs.project-maintainer' => [
                'id' => 8,
                'label' => 'prefs.project-maintainer',
                'value' => [
                    'name' => '',
                    'email' => '',
                    'public' => false,
                    'description' => ''
                ],
                'allow_override' => false
            ],
            'prefs.map-projection' => [
                'id' => 9,
                'label' => 'prefs.map-projection',
                'value' => [
                    'epsg' => 4326,
                    'proj4' => '+proj=longlat +datum=WGS84 +no_defs '
                ],
                'allow_override' => false
            ],
            'prefs.enable-password-reset-link' => [
                'id' => 10,
                'label' => 'prefs.enable-password-reset-link',
                'value' => false,
                'allow_override' => false
            ]
        ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUserPreferenceEndpoint()
    {
        $cnt = UserPreference::count();
        $this->assertEquals($cnt, 0);

        $fields = [
            'pref_id' => 1,
            'user_id' => 1,
            'value' => '{"language_key":"de"}'
        ];
        $up = new UserPreference();
        foreach($fields as $k => $v) {
            $up->{$k} = $v;
        }
        $up->save();

        $cnt = UserPreference::count();
        $this->assertEquals($cnt, 1);

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/preference/1');

        $response->assertJsonCount(10);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'label',
                'value',
                'allow_override',
                'created_at',
                'updated_at'
            ]
        ]);

        $response->assertJson([
            'prefs.gui-language' => [
                'id' => 1,
                'label' => 'prefs.gui-language',
                'value' => 'de',
                'allow_override' => true
            ],
            'prefs.columns' => [
                'id' => 2,
                'label' => 'prefs.columns',
                'value' => [
                    'left' => 2,
                    'center' => 5,
                    'right' => 5
                ],
                'allow_override' => true
            ],
            'prefs.show-tooltips' => [
                'id' => 3,
                'label' => 'prefs.show-tooltips',
                'value' => true,
                'allow_override' => true
            ],
            'prefs.tag-root' => [
                'id' => 4,
                'label' => 'prefs.tag-root',
                'value' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/eigenschaften#20171220100251',
                'allow_override' => false
            ],
            'prefs.load-extensions' => [
                'id' => 5,
                'label' => 'prefs.load-extensions',
                'value' => [
                    'map' => true,
                    'files' => true,
                    'data-analysis' => true,
                ],
                'allow_override' => false
            ],
            'prefs.link-to-thesaurex' => [
                'id' => 6,
                'label' => 'prefs.link-to-thesaurex',
                'value' => '',
                'allow_override' => false
            ],
            'prefs.project-name' => [
                'id' => 7,
                'label' => 'prefs.project-name',
                'value' => 'Spacialist',
                'allow_override' => false
            ],
            'prefs.project-maintainer' => [
                'id' => 8,
                'label' => 'prefs.project-maintainer',
                'value' => [
                    'name' => '',
                    'email' => '',
                    'public' => false,
                    'description' => ''
                ],
                'allow_override' => false
            ],
            'prefs.map-projection' => [
                'id' => 9,
                'label' => 'prefs.map-projection',
                'value' => [
                    'epsg' => 4326,
                    'proj4' => '+proj=longlat +datum=WGS84 +no_defs '
                ],
                'allow_override' => false
            ],
            'prefs.enable-password-reset-link' => [
                'id' => 10,
                'label' => 'prefs.enable-password-reset-link',
                'value' => false,
                'allow_override' => false
            ]
        ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testOtherUserPreferenceEndpoint()
    {
        $user = User::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->get('/api/v1/preference/' . $user->id);

        $response->assertStatus(403);
        $response->assertSimilarJson([
            'error' => 'You are not allowed to access preferences of another user'
        ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testMissingUserPreferenceEndpoint()
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->get('/api/v1/preference/99');

        $response->assertStatus(400);
        $response->assertSimilarJson([
            'error' => 'This user does not exist'
        ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testPatchPreferenceEndpoint()
    {
        $cnt = UserPreference::count();
        $this->assertEquals($cnt, 0);

        $user = User::factory()->create();
        $fields = [
            'pref_id' => 1,
            'user_id' => $user->id,
            'value' => '{"language_key":"de"}'
        ];
        $up = new UserPreference();
        foreach($fields as $k => $v) {
            $up->{$k} = $v;
        }
        $up->save();

        $cnt = UserPreference::count();
        $this->assertEquals($cnt, 1);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->patch('/api/v1/preference/1', [
            'label' => 'prefs.gui-language',
            'value' => 'fr',
            'allow_override' => 'false'
        ]);

        // Because allow_override is set to false,
        // user prefs should get deleted
        $cnt = UserPreference::count();
        $this->assertEquals($cnt, 0);
        $response->assertStatus(204);
        $pref = Preference::find(1);
        $this->assertEquals('{"language_key": "fr"}', $pref->default_value);
    }

    /**
     * Test patching a users preference.
     *
     * @return void
     */
    public function testPatchUserPreferenceEndpoint()
    {
        $data = [
            'user_id' => 1,
            'value' => '{"left": 2, "right": 2, "center": 8}',
            'label' => 'prefs.columns',
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->patch('/api/v1/preference/2', $data);

        $pref = User::with('preferences')->first()->preferences[0];
        $this->assertEquals(2, $pref->pref_id);
        $this->assertEquals(1, $pref->user_id);
        $this->assertEquals('{"left": 2, "right": 2, "center": 8}', $pref->value);
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
            ['url' => '/99', 'error' => 'You do not have the permission to edit preferences', 'verb' => 'patch'],
        ];

        foreach($calls as $c) {
            $response = $this->withHeaders([
                    'Authorization' => "Bearer $this->token"
                ])
                ->json($c['verb'], '/api/v1/preference' . $c['url']);

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
            ['url' => '/99', 'error' => 'This preference does not exist', 'verb' => 'patch'],
        ];

        foreach($calls as $c) {
            $response = $this->withHeaders([
                    'Authorization' => "Bearer $this->token"
                ])
                ->json($c['verb'], '/api/v1/preference' . $c['url'], [
                    'label' => 'prefs.columns'
                ]);

            $response->assertStatus(400);
            $response->assertSimilarJson([
                'error' => $c['error']
            ]);

            $this->refreshToken($response);
        }
    }
}
