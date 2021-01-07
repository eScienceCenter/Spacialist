<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AttributesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('attributes')->insert(array (
            0 =>
            array (
                'id' => 2,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/erhaltung#20171220100437',
                'datatype' => 'percentage',
                'thesaurus_root_url' => NULL,
                'created_at' => '2017-12-20 10:07:45',
                'updated_at' => '2017-12-20 10:07:45',
                'parent_id' => NULL,
                'text' => NULL,
                'recursive' => true,
            ),
            1 =>
            array (
                'id' => 3,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/farbe#20171220100506',
                'datatype' => 'string-mc',
                'thesaurus_root_url' => NULL,
                'created_at' => '2017-12-20 10:07:53',
                'updated_at' => '2017-12-20 10:07:53',
                'parent_id' => NULL,
                'text' => NULL,
                'recursive' => true,
            ),
            2 =>
            array (
                'id' => 4,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/zeichnung_angefertigt#20171220100933',
                'datatype' => 'boolean',
                'thesaurus_root_url' => NULL,
                'created_at' => '2017-12-20 10:09:45',
                'updated_at' => '2017-12-20 10:09:45',
                'parent_id' => NULL,
                'text' => NULL,
                'recursive' => true,
            ),
            3 =>
            array (
                'id' => 5,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/verzierung#20171220105421',
                'datatype' => 'table',
                'thesaurus_root_url' => NULL,
                'created_at' => '2017-12-20 10:56:32',
                'updated_at' => '2017-12-20 10:56:32',
                'parent_id' => NULL,
                'text' => NULL,
                'recursive' => true,
            ),
            4 =>
            array (
                'id' => 6,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/gefassposition#20171220105434',
                'datatype' => 'string-sc',
                'thesaurus_root_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/gefassposition#20171220105434',
                'created_at' => '2017-12-20 10:56:32',
                'updated_at' => '2017-12-20 10:56:32',
                'parent_id' => 5,
                'text' => NULL,
                'recursive' => true,
            ),
            5 =>
            array (
                'id' => 7,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/verzierungselement#20171220105440',
                'datatype' => 'string-sc',
                'thesaurus_root_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/verzierungselement#20171220105440',
                'created_at' => '2017-12-20 10:56:32',
                'updated_at' => '2017-12-20 10:56:32',
                'parent_id' => 5,
                'text' => NULL,
                'recursive' => true,
            ),
            6 =>
            array (
                'id' => 8,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/notizen#20171220105603',
                'datatype' => 'string',
                'thesaurus_root_url' => NULL,
                'created_at' => '2017-12-20 10:56:32',
                'updated_at' => '2017-12-20 10:56:32',
                'parent_id' => 5,
                'text' => NULL,
                'recursive' => true,
            ),
            7 =>
            array (
                'id' => 9,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/abmessungen#20171220105653',
                'datatype' => 'dimension',
                'thesaurus_root_url' => NULL,
                'created_at' => '2017-12-20 10:57:10',
                'updated_at' => '2017-12-20 10:57:10',
                'parent_id' => NULL,
                'text' => NULL,
                'recursive' => true,
            ),
            8 =>
            array (
                'id' => 10,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/datum#20171220105725',
                'datatype' => 'date',
                'thesaurus_root_url' => NULL,
                'created_at' => '2017-12-20 10:57:33',
                'updated_at' => '2017-12-20 10:57:33',
                'parent_id' => NULL,
                'text' => NULL,
                'recursive' => true,
            ),
            9 =>
            array (
                'id' => 11,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/wandungsdicke#20171220105906',
                'datatype' => 'integer',
                'thesaurus_root_url' => NULL,
                'created_at' => '2017-12-20 10:59:33',
                'updated_at' => '2017-12-20 10:59:33',
                'parent_id' => NULL,
                'text' => NULL,
                'recursive' => true,
            ),
            10 =>
            array (
                'id' => 12,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/gewicht#20171220105915',
                'datatype' => 'double',
                'thesaurus_root_url' => NULL,
                'created_at' => '2017-12-20 10:59:42',
                'updated_at' => '2017-12-20 10:59:42',
                'parent_id' => NULL,
                'text' => NULL,
                'recursive' => true,
            ),
            11 =>
            array (
                'id' => 13,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/notizen#20171220105603',
                'datatype' => 'stringf',
                'thesaurus_root_url' => NULL,
                'created_at' => '2017-12-20 11:00:31',
                'updated_at' => '2017-12-20 11:00:31',
                'parent_id' => NULL,
                'text' => NULL,
                'recursive' => true,
            ),
            12 =>
            array (
                'id' => 14,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/befundtyp#20171220110100',
                'datatype' => 'string-sc',
                'thesaurus_root_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/befundtyp#20171220110100',
                'created_at' => '2017-12-20 11:01:47',
                'updated_at' => '2017-12-20 11:01:47',
                'parent_id' => NULL,
                'text' => NULL,
                'recursive' => true,
            ),
            13 =>
            array (
                'id' => 15,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/alternativer_name#20171220165047',
                'datatype' => 'list',
                'thesaurus_root_url' => NULL,
                'created_at' => '2017-12-20 16:50:56',
                'updated_at' => '2017-12-20 16:50:56',
                'parent_id' => NULL,
                'text' => NULL,
                'recursive' => true,
            ),
            14 =>
            array (
                'id' => 16,
            'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/koordintaten_(wkt)#20171220165152',
                'datatype' => 'geography',
                'thesaurus_root_url' => NULL,
                'created_at' => '2017-12-20 16:52:01',
                'updated_at' => '2017-12-20 16:52:01',
                'parent_id' => NULL,
                'text' => NULL,
                'recursive' => true,
            ),
            15 =>
            array (
                'id' => 17,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/chronologie#20171220165337',
                'datatype' => 'epoch',
                'thesaurus_root_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/phasen#20171220165346',
                'created_at' => '2017-12-20 16:54:27',
                'updated_at' => '2017-12-20 16:54:27',
                'parent_id' => NULL,
                'text' => NULL,
                'recursive' => true,
            ),
            16 =>
            array (
                'id' => 18,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/lagerstatte#20171220165727',
                'datatype' => 'entity',
                'thesaurus_root_url' => NULL,
                'created_at' => '2017-12-20 16:57:50',
                'updated_at' => '2017-12-20 16:57:50',
                'parent_id' => NULL,
                'text' => NULL,
                'recursive' => true,
            ),
            17 =>
            array (
                'id' => 19,
                'thesaurus_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/aufbewahrung#20171220170755',
                'datatype' => 'string',
                'thesaurus_root_url' => NULL,
                'created_at' => '2017-12-20 17:08:03',
                'updated_at' => '2017-12-20 17:08:03',
                'parent_id' => NULL,
                'text' => NULL,
                'recursive' => true,
            ),
        ));
    }
}
