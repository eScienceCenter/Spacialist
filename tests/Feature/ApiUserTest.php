<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;

use App\User;
use App\Role;

class ApiUserTest extends TestCase
{
    // Testing GET requests

    /**
     * Test getting the authenticated user.
     *
     * @return void
     */
    public function testGetUserEndpoint()
    {
        $user = User::find(1);
        $user->setPermissions();
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/auth/user');
            
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data'
        ]);
        $response->assertJson([
            'status' => 'success',
            'data' => [
                'id' => 1,
                'name' => 'Admin',
                'nickname' => 'admin',
                'email' => 'admin@localhost',
                'created_at' => '2017-12-20T09:47:36.000000Z',
                'updated_at' => '2017-12-20T09:47:36.000000Z',
                'permissions' => []
            ]
        ]);

        // Check permission count (30 permissions in total)
        $content = json_decode($response->getContent());
        $this->assertEquals(count(get_object_vars($content->data->permissions)), 30);
    }

    /**
     * Test getting all users.
     *
     * @return void
     */
    public function testGetUsersEndpoint()
    {
        $user = User::factory()->create();

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/user');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
        $response->assertJsonStructure([
            'users',
            'deleted_users',
            'roles'
        ]);
        $response->assertJson([
            'users' => [
                [
                    'id' => 1
                ],
                [
                    'id' => $user->id
                ]
            ],
            'roles' => []
        ]);
    }

    /**
     * Test getting all roles.
     *
     * @return void
     */
    public function testGetRolesEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/role');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJsonStructure([
            'roles',
            'permissions'
        ]);
        $response->assertJson([
            'roles' => [
                [
                    'id' => 1,
                    'name' => 'admin'
                ],
                [
                    'id' => 2,
                    'name' => 'guest'
                ]
            ],
            'permissions' => []
        ]);
    }

    /**
     * Test refreshing JWT token.
     *
     * @return void
     */
    public function testRefreshTokenEndpoint()
    {
        $oldToken = $this->token;
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/auth/refresh');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertSimilarJson([
            'status' => 'success'
        ]);
        $this->refreshToken($response);
        $this->assertTrue($oldToken != $this->token);
    }

    // Testing POST requests

    /**
     * Test login.
     *
     * @return void
     */
    public function testLoginEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/auth/login', [
                'email' => 'admin@localhost',
                'password' => 'admin'
            ]);

        $response->assertStatus(200);
        $this->assertTrue($response->headers->has('authorization'));
    }

    /**
     * Test login with nickname.
     *
     * @return void
     */
    public function testLoginWithNicknameEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/auth/login', [
                'nickname' => 'admin',
                'password' => 'admin'
            ]);

        $response->assertStatus(200);
        $this->assertTrue($response->headers->has('authorization'));
    }

    /**
     * Test login with wrong credentials.
     *
     * @return void
     */
    public function testLoginWrongCredentialsEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/auth/login', [
                'email' => 'admin@localhost',
                'password' => 'admin1337'
            ]);

        $response->assertStatus(400);
        $response->assertSimilarJson([
            'error' => 'Invalid Credentials'
        ]);
    }

    /**
     * Test creating a new user.
     *
     * @return void
     */
    public function testCreateUserEndpoint()
    {
        $cnt = User::count();
        $this->assertEquals(1, $cnt);
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/user', [
                'email' => 'test@test.com',
                'name' => 'Test User',
                'nickname' => 'tuser',
                'password' => 'test'
            ]);

        $user = User::latest()->first();
        $cnt = User::count();
        $this->assertEquals(2, $cnt);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'email',
            'name',
            'created_at',
            'updated_at'
        ]);
        $response->assertJson([
            'id' => $user->id,
            'email' => 'test@test.com',
            'name' => 'Test User',
            'nickname' => 'tuser',
            'created_at' => $user->created_at->toJSON(),
            'updated_at' => $user->updated_at->toJSON()
        ]);
    }

    /**
     * Test creating an avatar for user (id=1).
     *
     * @return void
     */
    public function testCreateAvatarEndpoint()
    {
        $user = User::find(1);
        $this->assertNull($user->avatar);
        $file = UploadedFile::fake()->image('spacialist_screenshot.png', 350, 100);
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/user/1/avatar', [
                'file' => $file
            ]);

        $user = User::find(1);
        $this->assertEquals('avatars/1.png', $user->avatar);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'email',
            'name',
            'nickname',
            'created_at',
            'updated_at',
            'avatar'
        ]);
        $response->assertJson([
            'id' => 1,
            'email' => 'admin@localhost',
            'name' => 'Admin',
            'avatar' => 'avatars/1.png'
        ]);
    }

    /**
     * Test creating an avatar for non-existing user.
     *
     * @return void
     */
    public function testCreateAvatarNonExistingUserEndpoint()
    {
        $file = UploadedFile::fake()->image('spacialist_screenshot.png', 350, 100);
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/user/99/avatar', [
                'file' => $file
            ]);

        $response->assertStatus(400);
        $response->assertSimilarJson([
            'error' => 'This user does not exist'
        ]);
    }

    /**
     * Test creating a new role.
     *
     * @return void
     */
    public function testCreateRoleEndpoint()
    {
        $cnt = Role::count();
        $this->assertEquals(2, $cnt);
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/role', [
                'name' => 'test_role',
                'display_name' => 'Test Role'
            ]);

        $role = Role::latest()->first();
        $cnt = Role::count();
        $this->assertEquals(3, $cnt);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'display_name',
            'description',
            'created_at',
            'updated_at'
        ]);
        $response->assertJson([
            'id' => $role->id,
            'name' => 'test_role',
            'display_name' => 'Test Role',
            'description' => null,
            'created_at' => $role->created_at->toJSON(),
            'updated_at' => $role->updated_at->toJSON()
        ]);
    }

    /**
     * Test user logout.
     *
     * @return void
     */
    public function testLogoutEndpoint()
    {
        $cnt = Role::count();
        $this->assertEquals(2, $cnt);
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/auth/logout');

        $response->assertStatus(200);
        if($response->headers->has('authorization')) {
            $token = $response->headers->get('authorization');

            $response = $this->withHeaders([
                'Authorization' => "$token"
            ])
            ->get('/api/v1/user');
            $response->assertStatus(302);
            $this->assertEquals("Unauthenticated.", $response->exception->getMessage());
        }
    }

    /**
     * Test patching user.
     *
     * @return void
     */
    public function testPatchUserEndpoint()
    {
        $user = User::find(1);
        $this->assertEquals('Admin', $user->name);
        $this->assertEquals('admin', $user->nickname);
        $this->assertEquals('admin@localhost', $user->email);
        $this->assertNull($user->metadata);
        $this->assertNull($user->avatar);
        $this->assertNull($user->avatar_url);

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->patch('/api/v1/user/1', [
                'roles' => [2],
                'email' => 'test@test.com',
                'name' => 'Admin Updated',
                'nickname' => 'admin1',
                'phonenumber' => '+43 123 1234',
                'orcid' => '0000-0002-1694-233X'
            ]);

        $user = User::find(1);
        $this->assertTrue(!$user->hasRole('admin'));
        $this->assertTrue($user->hasRole('guest'));
        $response->assertStatus(200);
        $response->assertSimilarJson([
            'id' => 1,
            'name' => 'Admin Updated',
            'nickname' => 'admin1',
            'email' => 'test@test.com',
            'created_at' => '2017-12-20T09:47:36.000000Z',
            'updated_at' => $user->updated_at->toJSON(),
            'deleted_at' => null,
            'avatar' => null,
            'avatar_url' => null,
            'metadata' => [
                'phonenumber' => '+43 123 1234',
                'orcid' => '0000-0002-1694-233X',
            ],
        ]);

        $this->assertEquals('Admin Updated', $user->name);
        $this->assertEquals('admin1', $user->nickname);
        $this->assertEquals('test@test.com', $user->email);
        $this->assertEquals('+43 123 1234', $user->metadata['phonenumber']);
        $this->assertEquals('0000-0002-1694-233X', $user->metadata['orcid']);
        $this->assertNull($user->avatar);
        $this->assertNull($user->avatar_url);
    }

    /**
     * Test patching a users existing metadata.
     *
     * @return void
     */
    public function testPatchUserOrcidEndpoint()
    {
        $user = User::find(1);
        $this->assertNull($user->metadata);
        $user->metadata = [
            'orcid' => '0000-0002-1694-233X',
        ];
        $user->save();
        
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
            ->patch('/api/v1/user/1', [
                'orcid' => '0000-0001-5109-3700'
            ]);

        $response->assertStatus(200);
        $user = User::find(1);
        $this->assertNotNull($user->metadata);
        $this->assertEquals('0000-0001-5109-3700', $user->metadata['orcid']);

    }

    /**
     * Test patching user with wrong orcid
     *
     * @return void
     */
    public function testPatchUserWrongOrcidEndpoint()
    {
        $user = User::find(1);

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->json('patch', '/api/v1/user/1', [
                'orcid' => '0000-0002-1694-2338'
            ]);

        $response->assertStatus(422);
    }

    /**
     * Test patching user with wrong orcid
     *
     * @return void
     */
    public function testPatchUserAnotherWrongOrcidEndpoint()
    {
        $user = User::find(1);

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->json('patch', '/api/v1/user/1', [
                'orcid' => '0000-0002-1694233X'
            ]);

        $response->assertStatus(422);
    }

    /**
     * Test patching user with wrong orcid
     *
     * @return void
     */
    public function testPatchUserAnotherSecondWrongOrcidEndpoint()
    {
        $user = User::find(1);

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->json('patch', '/api/v1/user/1', [
                'orcid' => '0000-0002-1694-23aX'
            ]);

        $response->assertStatus(422);
    }

    /**
     * Test patching user with empty request.
     *
     * @return void
     */
    public function testPatchUserWithoutDataEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->patch('/api/v1/user/1', []);

        $response->assertStatus(204);
    }

    /**
     * Test patching role.
     *
     * @return void
     */
    public function testPatchRoleEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->patch('/api/v1/role/1', [
                'permissions' => [1, 2],
                'display_name' => 'NOT Admin',
                'description' => 'No longer a Admin User'
            ]);

        $role = Role::find(1);
        $this->assertTrue($role->hasPermissionTo('create_concepts'));
        $this->assertTrue($role->hasPermissionTo('delete_move_concepts'));
        $this->assertTrue(!$role->hasPermissionTo('duplicate_edit_concepts'));
        $this->assertEquals(2, count($role->permissions));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'display_name',
            'description',
            'created_at',
            'updated_at',
            'permissions'
        ]);
        $response->assertJson([
            'id' => 1,
            'name' => 'admin',
            'display_name' => 'NOT Admin',
            'description' => 'No longer a Admin User',
            'created_at' => '2017-12-20T09:47:35.000000Z',
            'updated_at' => $role->updated_at->toJSON()
        ]);
    }

    /**
     * Test patching role with empty request.
     *
     * @return void
     */
    public function testPatchRoleWithoutDataEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->patch('/api/v1/role/1', []);

        $response->assertStatus(204);
    }

    /**
     * Test deleting and restoring a user (id=1).
     *
     * @return void
     */
    public function testDeleteUserEndpoint()
    {
        $cnt = User::count();
        $this->assertEquals(1, $cnt);
        $cnt = User::onlyTrashed()->count();
        $this->assertEquals(0, $cnt);
        $cnt = User::withoutTrashed()->count();
        $this->assertEquals(1, $cnt);
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->delete('/api/v1/user/1');

        $response->assertStatus(200);

        $cnt = User::count();
        $this->assertEquals(1, $cnt);
        $cnt = User::onlyTrashed()->count();
        $this->assertEquals(1, $cnt);
        $cnt = User::withoutTrashed()->count();
        $this->assertEquals(0, $cnt);
        $user = User::find(1);
        $this->assertNotNull($user->deleted_at);


        // Test restore
        $this->refreshToken($response);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token"
        ])
            ->patch('/api/v1/user/restore/1');

        $user = User::find(1);
        $this->assertNull($user->deleted_at);

        $response->assertStatus(204);
    }

    /**
     * Test deleting non-existing user.
     *
     * @return void
     */
    public function testDeleteNonExstingUserEndpoint()
    {
        $cnt = User::count();
        $this->assertEquals(1, $cnt);
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->delete('/api/v1/user/99');

        $cnt = User::count();
        $this->assertEquals(1, $cnt);

        $response->assertStatus(400);
        $response->assertSimilarJson([
            'error' => 'This user does not exist'
        ]);
    }

    /**
     * Test deleting role (id=1).
     *
     * @return void
     */
    public function testDeleteRoleEndpoint()
    {
        $cnt = Role::count();
        $this->assertEquals(2, $cnt);
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->delete('/api/v1/role/1');

        $cnt = Role::count();
        $this->assertEquals(1, $cnt);

        $response->assertStatus(204);
    }

    /**
     * Test deleting non-existing role.
     *
     * @return void
     */
    public function testDeleteNonExstingRoleEndpoint()
    {
        $cnt = Role::count();
        $this->assertEquals(2, $cnt);
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->delete('/api/v1/role/99');

        $cnt = Role::count();
        $this->assertEquals(2, $cnt);

        $response->assertStatus(400);
        $response->assertSimilarJson([
            'error' => 'This role does not exist'
        ]);
    }

    /**
     * Test deleting avatar (user id=1).
     *
     * @return void
     */
    public function testDeleteAvatarEndpoint()
    {
        $user = User::find(1);
        $this->assertNull($user->avatar);
        $file = UploadedFile::fake()->image('spacialist_screenshot.png', 350, 100);
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/user/1/avatar', [
                'file' => $file
            ]);

        $user = User::find(1);
        $this->assertEquals('avatars/1.png', $user->avatar);

        $this->refreshToken($response);

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->delete('/api/v1/user/1/avatar');

        $user = User::find(1);
        $this->assertNull($user->avatar);
        $this->assertNull($user->avatar_url);

        $response->assertStatus(204);
    }

    /**
     * Test deleting avatar (user id=1).
     *
     * @return void
     */
    public function testDeleteAvatarNonExistingUserEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->delete('/api/v1/user/99/avatar');

        $response->assertStatus(400);
        $response->assertSimilarJson([
            'error' => 'This user does not exist'
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
            ['url' => '/user', 'error' => 'You do not have the permission to view users', 'verb' => 'get'],
            ['url' => '/role', 'error' => 'You do not have the permission to view roles', 'verb' => 'get'],
            ['url' => '/user', 'error' => 'You do not have the permission to add new users', 'verb' => 'post'],
            ['url' => '/role', 'error' => 'You do not have the permission to add roles', 'verb' => 'post'],
            ['url' => '/user/1', 'error' => 'You do not have the permission to set user roles', 'verb' => 'patch'],
            ['url' => '/role/1', 'error' => 'You do not have the permission to set role permissions', 'verb' => 'patch'],
            ['url' => '/user/restore/1', 'error' => 'You do not have the permission to delete users', 'verb' => 'patch'],
            ['url' => '/user/1', 'error' => 'You do not have the permission to delete users', 'verb' => 'delete'],
            ['url' => '/role/1', 'error' => 'You do not have the permission to delete roles', 'verb' => 'delete'],
            ['url' => '/user/1/avatar', 'error' => 'You do not have the permission to delete users', 'verb' => 'delete'],
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
            ['url' => '/user/99', 'error' => 'This user does not exist', 'verb' => 'patch'],
            ['url' => '/role/99', 'error' => 'This role does not exist', 'verb' => 'patch'],
            ['url' => '/user/restore/99', 'error' => 'This user does not exist', 'verb' => 'patch'],
        ];

        foreach($calls as $c) {
            $response = $this->withHeaders([
                    'Authorization' => "Bearer $this->token"
                ])
                ->json($c['verb'], '/api/v1' . $c['url'], [
                    'description' => 'does not matter'
                ]);

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
        $user = new User();
        $user->name = 'Test';
        $user->nickname = 'test';
        $user->email = 'mail@example.com';
        $user->password = 'not_safe';
        $user->save();

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->patch('/api/v1/user/' . $user->id, [
                'email' => 'admin@localhost'
            ]);

        $this->assertEquals('The given data was invalid.', $response->exception->getMessage());
    }
}
