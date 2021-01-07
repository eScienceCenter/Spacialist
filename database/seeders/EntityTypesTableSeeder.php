<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EntityTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('entity_types')->insert(array (
            0 =>
            array (
                'id' => 3,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/fundstelle#20171220094911',
                'created_at' => '2017-12-20 10:03:06',
                'updated_at' => '2017-12-20 10:03:06',
                'is_root' => true,
            ),
            1 =>
            array (
                'id' => 4,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/befund#20171220094916',
                'created_at' => '2017-12-20 10:03:15',
                'updated_at' => '2017-12-20 10:03:15',
                'is_root' => false,
            ),
            2 =>
            array (
                'id' => 5,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/keramik#20171220095651',
                'created_at' => '2017-12-20 10:03:23',
                'updated_at' => '2017-12-20 10:03:23',
                'is_root' => false,
            ),
            3 =>
            array (
                'id' => 6,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/stein#20171220095719',
                'created_at' => '2017-12-20 10:03:30',
                'updated_at' => '2017-12-20 10:03:30',
                'is_root' => false,
            ),
            4 =>
            array (
                'id' => 7,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/lagerstatte#20171220165727',
                'created_at' => '2017-12-20 16:57:41',
                'updated_at' => '2017-12-20 16:57:41',
                'is_root' => true,
            ),
        ));
    }
}
