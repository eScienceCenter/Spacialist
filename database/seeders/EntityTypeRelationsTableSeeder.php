<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EntityTypeRelationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('entity_type_relations')->insert(array (
            0 =>
            array (
                'id' => 1,
                'parent_id' => 3,
                'child_id' => 3,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 =>
            array (
                'id' => 2,
                'parent_id' => 3,
                'child_id' => 4,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 =>
            array (
                'id' => 3,
                'parent_id' => 3,
                'child_id' => 5,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 =>
            array (
                'id' => 4,
                'parent_id' => 3,
                'child_id' => 6,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 =>
            array (
                'id' => 5,
                'parent_id' => 3,
                'child_id' => 7,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            5 =>
            array (
                'id' => 11,
                'parent_id' => 5,
                'child_id' => 3,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            6 =>
            array (
                'id' => 12,
                'parent_id' => 5,
                'child_id' => 4,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            7 =>
            array (
                'id' => 13,
                'parent_id' => 5,
                'child_id' => 5,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            8 =>
            array (
                'id' => 14,
                'parent_id' => 5,
                'child_id' => 6,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            9 =>
            array (
                'id' => 15,
                'parent_id' => 5,
                'child_id' => 7,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            10 =>
            array (
                'id' => 16,
                'parent_id' => 6,
                'child_id' => 3,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            11 =>
            array (
                'id' => 17,
                'parent_id' => 6,
                'child_id' => 4,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            12 =>
            array (
                'id' => 18,
                'parent_id' => 6,
                'child_id' => 5,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            13 =>
            array (
                'id' => 19,
                'parent_id' => 6,
                'child_id' => 6,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            14 =>
            array (
                'id' => 20,
                'parent_id' => 6,
                'child_id' => 7,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            15 =>
            array (
                'id' => 21,
                'parent_id' => 7,
                'child_id' => 3,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            16 =>
            array (
                'id' => 22,
                'parent_id' => 7,
                'child_id' => 4,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            17 =>
            array (
                'id' => 23,
                'parent_id' => 7,
                'child_id' => 5,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            18 =>
            array (
                'id' => 24,
                'parent_id' => 7,
                'child_id' => 6,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            19 =>
            array (
                'id' => 25,
                'parent_id' => 7,
                'child_id' => 7,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
    }
}
