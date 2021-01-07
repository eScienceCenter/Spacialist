<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users')->insert(array (
            0 =>
            array (
                'id' => 1,
                'name' => 'Admin',
                'nickname' => 'admin',
                'email' => 'admin@localhost',
                'metadata' => NULL,
                'password' => '$2y$10$GTwOUwcsqgqL/J/0dxxkIenBLR3Pna9ZfIa7sVyPkJgQDwqLU0wqa',
                'remember_token' => NULL,
                'created_at' => '2017-12-20 09:47:36',
                'updated_at' => '2017-12-20 09:47:36',
                'deleted_at' => NULL,
            ),
        ));
    }
}
