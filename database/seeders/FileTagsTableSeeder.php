<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FileTagsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('file_tags')->delete();

        \DB::table('file_tags')->insert(array (
            0 =>
            array (
                'id' => 1,
                'file_id' => 5,
                'concept_id' => 25,
                'created_at' => '2019-03-08 13:19:43',
                'updated_at' => '2019-03-08 13:19:43',
            ),
            1 =>
            array (
                'id' => 2,
                'file_id' => 5,
                'concept_id' => 20,
                'created_at' => '2019-03-08 13:19:43',
                'updated_at' => '2019-03-08 13:19:43',
            ),
            2 =>
            array (
                'id' => 3,
                'file_id' => 2,
                'concept_id' => 26,
                'created_at' => '2019-03-08 13:19:51',
                'updated_at' => '2019-03-08 13:19:51',
            ),
            3 =>
            array (
                'id' => 4,
                'file_id' => 2,
                'concept_id' => 19,
                'created_at' => '2019-03-08 13:19:51',
                'updated_at' => '2019-03-08 13:19:51',
            ),
            4 =>
            array (
                'id' => 5,
                'file_id' => 2,
                'concept_id' => 18,
                'created_at' => '2019-03-08 13:19:51',
                'updated_at' => '2019-03-08 13:19:51',
            ),
        ));


    }
}
