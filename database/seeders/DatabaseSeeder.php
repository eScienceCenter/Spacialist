<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call('RolesPermissionsSeeder');
        $this->call('AdminUserSeeder');
        $this->call('LanguageTableSeeder');
        $this->call('MapSeeder');
    }
}
