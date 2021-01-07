<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EntityFilesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $user = \App\User::orderBy('id')->first();
        \DB::table('entity_files')->insert(array (
            0 =>
            array (
                'file_id' => 3,
                'entity_id' => 2,
                'user_id' => $user->id,
            ),
            1 =>
            array (
                'file_id' => 5,
                'entity_id' => 1,
                'user_id' => $user->id,
            ),
            2 =>
            array (
                'file_id' => 6,
                'entity_id' => 3,
                'user_id' => $user->id,
            ),
            3 =>
            array (
                'file_id' => 4,
                'entity_id' => 3,
                'user_id' => $user->id,
            ),
            4 =>
            array (
                'file_id' => 2,
                'entity_id' => 4,
                'user_id' => $user->id,
            ),
        ));
    }
}
