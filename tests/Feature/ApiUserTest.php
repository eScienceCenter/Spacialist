<?php

namespace Tests\Feature;

use Tests\TestCase;

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
        $response->assertJsonCount(2);
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
                'created_at' => '2017-12-20 09:47:36',
                'updated_at' => '2017-12-20 09:47:36',
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
        $user = factory(User::class)->create();

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/user');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJsonStructure([
            'users',
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
        $response->assertExactJson([
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
        $response->assertExactJson([
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
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at
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
            'created_at' => $role->created_at,
            'updated_at' => $role->updated_at
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
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->patch('/api/v1/user/1', [
                'roles' => [2],
                'email' => 'test@test.com'
            ]);

        $user = User::find(1);
        $this->assertTrue(!$user->hasRole('admin'));
        $this->assertTrue($user->hasRole('guest'));
        $response->assertStatus(200);
        $response->assertExactJson([
            'id' => 1,
            'name' => 'Admin',
            'nickname' => 'admin',
            'email' => 'test@test.com',
            'created_at' => '2017-12-20 09:47:36',
            'updated_at' => "$user->updated_at"
        ]);
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
                'description' => 'No longer a Admin User',
                'moderated' => true
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
            'moderated' => true,
            'created_at' => '2017-12-20 09:47:35',
            'updated_at' => "$role->updated_at"
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
     * Test deleting user (id=1).
     *
     * @return void
     */
    public function testDeleteUserEndpoint()
    {
        $cnt = User::count();
        $this->assertEquals(1, $cnt);
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->delete('/api/v1/user/1');

        $cnt = User::count();
        $this->assertEquals(0, $cnt);

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
        $response->assertExactJson([
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
        $response->assertExactJson([
            'error' => 'This role does not exist'
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
            ['url' => '/user/1', 'error' => 'You do not have the permission to delete users', 'verb' => 'delete'],
            ['url' => '/role/1', 'error' => 'You do not have the permission to delete roles', 'verb' => 'delete'],
        ];

        foreach($calls as $c) {
            $response = $this->withHeaders([
                    'Authorization' => "Bearer $this->token"
                ])
                ->json($c['verb'], '/api/v1' . $c['url']);

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
            ['url' => '/user/99', 'error' => 'This user does not exist', 'verb' => 'patch'],
            ['url' => '/role/99', 'error' => 'This role does not exist', 'verb' => 'patch'],
        ];

        foreach($calls as $c) {
            $response = $this->withHeaders([
                    'Authorization' => "Bearer $this->token"
                ])
                ->json($c['verb'], '/api/v1' . $c['url'], [
                    'description' => 'does not matter'
                ]);

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
