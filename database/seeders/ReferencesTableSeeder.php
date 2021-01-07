<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ReferencesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $user = \App\User::orderBy('id')->first();
        \DB::table('references')->insert(array (
            0 =>
            array (
                'id' => 1,
                'entity_id' => 1,
                'attribute_id' => 15,
                'bibliography_id' => 1318,
                'description' => 'See Page 10',
                'user_id' => $user->id,
                'created_at' => '2019-03-08 13:36:36',
                'updated_at' => '2019-03-08 13:36:36',
            ),
            1 =>
            array (
                'id' => 2,
                'entity_id' => 1,
                'attribute_id' => 15,
                'bibliography_id' => 1319,
                'description' => 'Picture on left side of page 12',
                'user_id' => $user->id,
                'created_at' => '2019-03-08 13:36:48',
                'updated_at' => '2019-03-08 13:36:48',
            ),
            2 =>
            array (
                'id' => 3,
                'entity_id' => 1,
                'attribute_id' => 13,
                'bibliography_id' => 1323,
                'description' => 'Page 10ff is interesting',
                'user_id' => $user->id,
                'created_at' => '2019-03-08 13:37:09',
                'updated_at' => '2019-03-08 13:37:09',
            ),
        ));
    }
}
