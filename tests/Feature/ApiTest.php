<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

use App\Preference;
use App\VersionInfo;

class ApiTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testApiRoot()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * Test welcome page.
     *
     * @return void
     */
    public function testWelcomePage()
    {
        $response = $this->get('/welcome');

        $response->assertStatus(200);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUnauthPreRequest()
    {
        // Unauthenticated request redirects to /login
        $response = $this->get('/api/v1/pre');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAuthPreRequestWithoutToken()
    {
        $response = $this->actingAs($this->user)
            ->get('/api/v1/pre');

        $response->assertStatus(401);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAuthPreRequest()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/pre');

        $response->assertStatus(200);
        // returns array with 'preferences', 'concepts' and 'entityTypes'
        $response->assertJsonCount(4);
        $response->assertJsonStructure([
            'preferences',
            'concepts',
            'entityTypes',
            'users',
        ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testVersionRequest()
    {
        $vi = new VersionInfo();
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/version');

        $response->assertStatus(200);
        $content = $response->decodeResponseJson();
        $this->assertMatchesRegularExpression('/^\d+$/', $content['time']);
        $this->assertMatchesRegularExpression('/^[A-ZÄÖÜ][a-zäöüß]+$/', $content['name']);
        $this->assertMatchesRegularExpression('/^v\d\.\d\.\d$/', $content['release']);
        $this->assertMatchesRegularExpression('/^v\d\.\d\.\d \([A-ZÄÖÜ][a-zäöüß]+\)$/', $content['readable']);
        $this->assertMatchesRegularExpression('/^v\d\.\d\.\d-[a-zäöüß]+(-g[a-f0-9]{8})?$/', $content['full']);
        $this->assertEquals($content['release'], 'v' . $vi->getMajor() . "." . $vi->getMinor() . "." . $vi->getPatch());

        $hash = $vi->getReleaseHash();
        if(isset($hash)) {
            $this->assertTrue(Str::endsWith($content['full'], $hash));
        }
    }
}
