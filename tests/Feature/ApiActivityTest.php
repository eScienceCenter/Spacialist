<?php

namespace Tests\Feature;

use App\Entity;
use App\User;

use Tests\TestCase;
use Spatie\Activitylog\Models\Activity;

class ApiActivityTest extends TestCase
{
    // Testing GET requests

    /**
     * Test getting all logged activity.
     *
     * @return void
     */
    public function testGetAllActivityEndpoint()
    {
        $actCnt = Activity::count();
        $this->assertEquals(0, $actCnt);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->post('/api/v1/entity', [
            'name' => 'Unit-Test Entity I',
            'entity_type_id' => 3,
            'root_entity_id' => 1
        ]);
        $entity = Entity::latest()->first();

        $this->refreshToken($response);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->patch('/api/v1/entity/4/attributes', [
            [
                'params' => [
                    'aid' => 19,
                    'cid' => 4
                ],
                'op' => 'add',
                'value' => 'Test'
            ]
        ]);

        $this->refreshToken($response);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->patch('/api/v1/entity/4/attributes', [
            [
                'params' => [
                    'aid' => 19,
                    'cid' => 4
                ],
                'op' => 'replace',
                'value' => 'Test2'
            ]
        ]);

        $this->refreshToken($response);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->delete('/api/v1/entity/'.$entity->id);

        $response->assertStatus(204);
        $actCnt = Activity::count();
        $this->assertGreaterThanOrEqual(5, $actCnt);
        $this->assertLessThanOrEqual(6, $actCnt);

        $this->refreshToken($response);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->get('/api/v1/activity');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            [
                'id',
                'log_name',
                'description',
                'subject_id',
                'subject',
                'subject_type',
                'causer_id',
                'causer',
                'properties',
                'created_at',
                'updated_at',
            ]
        ]);
        $response->assertJson([
            [
                'log_name' => 'default',
                'description' => 'created',
                'causer_id' => 1,
                'subject_id' => $entity->id,
                'subject_type' => 'App\\Entity',
            ]
        ]);

        // With start and end before now
        $this->refreshToken($response);
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->post('/api/v1/activity', [
            'timespan' => [
                'from' => '2017-12-20 09:30:00',
                'to' => '2018-12-20 09:30:00',
            ]
        ]);

        $response->assertStatus(200);
        $content = json_decode($response->getContent());
        $this->assertEquals([], $content->data);

        // With start before now
        $this->refreshToken($response);
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->post('/api/v1/activity', [
            'timespan' => [
                'from' => '2017-12-20 09:30:00',
            ]
        ]);

        $response->assertStatus(200);
        $content = json_decode($response->getContent());
        $this->assertGreaterThanOrEqual(5, count($content->data));
        $this->assertLessThanOrEqual(6, count($content->data));

        // With single text search string
        $this->refreshToken($response);
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->post('/api/v1/activity', [
            'text' => 'tEst'
        ]);

        $response->assertStatus(200);
        $content = json_decode($response->getContent());
        $this->assertEquals(4, count($content->data));

        // With multiple text search strings
        $this->refreshToken($response);
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->post('/api/v1/activity', [
            'text' => 'unit entity'
        ]);

        $response->assertStatus(200);
        $content = json_decode($response->getContent());
        $this->assertEquals(2, count($content->data));

        // With file only
        $this->refreshToken($response);
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->post('/api/v1/activity', [
            'text' => 'Entity FooBar'
        ]);

        $response->assertStatus(200);
        $content = json_decode($response->getContent());
        $this->assertEquals(0, count($content->data));

        // With user id=2 and id=3
        $this->refreshToken($response);
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->post('/api/v1/activity', [
            'users' => [2, 3]
        ]);

        $response->assertStatus(200);
        $content = json_decode($response->getContent());
        $this->assertEquals(0, count($content->data));

        // With user id=1 only
        $this->refreshToken($response);
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->post('/api/v1/activity', [
            'users' => [1]
        ]);

        $response->assertStatus(200);
        $content = json_decode($response->getContent());
        $this->assertGreaterThanOrEqual(5, count($content->data));
        $this->assertLessThanOrEqual(6, count($content->data));

        // With user id=1,2,5
        $this->refreshToken($response);
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
        ->post('/api/v1/activity', [
            'users' => [1, 2, 5]
        ]);

        $response->assertStatus(200);
        $content = json_decode($response->getContent());
        $this->assertGreaterThanOrEqual(5, count($content->data));
        $this->assertLessThanOrEqual(6, count($content->data));
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
            ['url' => '/activity', 'error' => 'You do not have the permission to view activity logs', 'verb' => 'get'],
            ['url' => '/activity/2', 'error' => 'You do not have the permission to view activity logs', 'verb' => 'get'],
            ['url' => '/activity', 'error' => 'You do not have the permission to view activity logs', 'verb' => 'post'],
        ];

        foreach($calls as $c) {
            $response = $this->withHeaders([
                    'Authorization' => "Bearer $this->token"
                ])
                ->json($c['verb'], '/api/v1' . $c['url']);

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
            ['url' => '/activity/99', 'error' => 'This user does not exist', 'verb' => 'get'],
        ];

        foreach($calls as $c) {
            $response = $this->withHeaders([
                    'Authorization' => "Bearer $this->token"
                ])
                ->json($c['verb'], '/api/v1' . $c['url']);

            $response->assertStatus(400);
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
    public function testValidations()
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
            ->post('/api/v1/activity', [
                'users' => 1
            ]);

        $this->assertEquals('The given data was invalid.', $response->exception->getMessage());

        $this->refreshToken($response);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
            ->post('/api/v1/activity', [
                'timespan' => '2017-12-20 09:30:00'
            ]);

        $this->assertEquals('The given data was invalid.', $response->exception->getMessage());
    }
}
