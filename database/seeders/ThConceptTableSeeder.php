<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ThConceptTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $user = \App\User::orderBy('id')->first();
        \DB::table('th_concept')->insert(array (
            0 =>
            array (
                'id' => 1,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/fundstelle#20171220094911',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 09:49:11',
                'updated_at' => '2017-12-20 09:49:11',
                'is_top_concept' => true,
            ),
            1 =>
            array (
                'id' => 2,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/befund#20171220094916',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 09:49:16',
                'updated_at' => '2017-12-20 09:49:16',
                'is_top_concept' => true,
            ),
            2 =>
            array (
                'id' => 3,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/fundobjekt#20171220094921',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 09:49:21',
                'updated_at' => '2017-12-20 09:49:21',
                'is_top_concept' => true,
            ),
            3 =>
            array (
                'id' => 28,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/befundform_im_planum#20171220100724',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:07:24',
                'updated_at' => '2017-12-20 10:07:24',
                'is_top_concept' => false,
            ),
            4 =>
            array (
                'id' => 29,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/dokumentation_und_administration#20171220100906',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:09:06',
                'updated_at' => '2017-12-20 10:09:06',
                'is_top_concept' => true,
            ),
            5 =>
            array (
                'id' => 30,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/aktion#20171220100926',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:09:26',
                'updated_at' => '2017-12-20 10:09:26',
                'is_top_concept' => false,
            ),
            6 =>
            array (
                'id' => 31,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/zeichnung_angefertigt#20171220100933',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:09:33',
                'updated_at' => '2017-12-20 10:09:33',
                'is_top_concept' => false,
            ),
            7 =>
            array (
                'id' => 32,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/verzierung#20171220105421',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:54:21',
                'updated_at' => '2017-12-20 10:54:21',
                'is_top_concept' => false,
            ),
            8 =>
            array (
                'id' => 11,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/gefasstyp#20171220095739',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 09:57:39',
                'updated_at' => '2017-12-20 09:57:39',
                'is_top_concept' => false,
            ),
            9 =>
            array (
                'id' => 12,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/art_fundobjekt#20171220095807',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 09:58:07',
                'updated_at' => '2017-12-20 09:58:07',
                'is_top_concept' => false,
            ),
            10 =>
            array (
                'id' => 9,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/keramik#20171220095651',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 09:56:51',
                'updated_at' => '2017-12-20 09:56:51',
                'is_top_concept' => false,
            ),
            11 =>
            array (
                'id' => 10,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/stein#20171220095719',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 09:57:19',
                'updated_at' => '2017-12-20 09:57:19',
                'is_top_concept' => false,
            ),
            12 =>
            array (
                'id' => 13,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/eigenschaften#20171220100251',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:02:51',
                'updated_at' => '2017-12-20 10:02:51',
                'is_top_concept' => true,
            ),
            13 =>
            array (
                'id' => 15,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/form#20171220100427',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:04:27',
                'updated_at' => '2017-12-20 10:04:27',
                'is_top_concept' => false,
            ),
            14 =>
            array (
                'id' => 16,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/erhaltung#20171220100437',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:04:37',
                'updated_at' => '2017-12-20 10:04:37',
                'is_top_concept' => false,
            ),
            15 =>
            array (
                'id' => 17,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/farbe#20171220100506',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:05:06',
                'updated_at' => '2017-12-20 10:05:06',
                'is_top_concept' => false,
            ),
            16 =>
            array (
                'id' => 18,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/rot#20171220100515',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:05:15',
                'updated_at' => '2017-12-20 10:05:15',
                'is_top_concept' => false,
            ),
            17 =>
            array (
                'id' => 19,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/blau#20171220100519',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:05:19',
                'updated_at' => '2017-12-20 10:05:19',
                'is_top_concept' => false,
            ),
            18 =>
            array (
                'id' => 20,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/grun#20171220100524',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:05:24',
                'updated_at' => '2017-12-20 10:05:24',
                'is_top_concept' => false,
            ),
            19 =>
            array (
                'id' => 23,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/gefassformen#20171220100559',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:05:59',
                'updated_at' => '2017-12-20 10:05:59',
                'is_top_concept' => false,
            ),
            20 =>
            array (
                'id' => 24,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/grubenformen#20171220100607',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:06:07',
                'updated_at' => '2017-12-20 10:06:07',
                'is_top_concept' => false,
            ),
            21 =>
            array (
                'id' => 25,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/offene_gefassform#20171220100638',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:06:38',
                'updated_at' => '2017-12-20 10:06:38',
                'is_top_concept' => false,
            ),
            22 =>
            array (
                'id' => 26,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/geschlossene_gefassform#20171220100648',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:06:48',
                'updated_at' => '2017-12-20 10:06:48',
                'is_top_concept' => false,
            ),
            23 =>
            array (
                'id' => 27,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/befundform_im_profil#20171220100706',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:07:06',
                'updated_at' => '2017-12-20 10:07:06',
                'is_top_concept' => false,
            ),
            24 =>
            array (
                'id' => 33,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/gefassposition#20171220105434',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:54:34',
                'updated_at' => '2017-12-20 10:54:34',
                'is_top_concept' => false,
            ),
            25 =>
            array (
                'id' => 34,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/verzierungselement#20171220105440',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:54:40',
                'updated_at' => '2017-12-20 10:54:40',
                'is_top_concept' => false,
            ),
            26 =>
            array (
                'id' => 35,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/rand#20171220105447',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:54:47',
                'updated_at' => '2017-12-20 10:54:47',
                'is_top_concept' => false,
            ),
            27 =>
            array (
                'id' => 36,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/hals#20171220105454',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:54:54',
                'updated_at' => '2017-12-20 10:54:54',
                'is_top_concept' => false,
            ),
            28 =>
            array (
                'id' => 37,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/schulter#20171220105500',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:55:00',
                'updated_at' => '2017-12-20 10:55:00',
                'is_top_concept' => false,
            ),
            29 =>
            array (
                'id' => 38,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/bauch#20171220105505',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:55:05',
                'updated_at' => '2017-12-20 10:55:05',
                'is_top_concept' => false,
            ),
            30 =>
            array (
                'id' => 39,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/boden#20171220105508',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:55:08',
                'updated_at' => '2017-12-20 10:55:08',
                'is_top_concept' => false,
            ),
            31 =>
            array (
                'id' => 40,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/riefung#20171220105513',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:55:13',
                'updated_at' => '2017-12-20 10:55:13',
                'is_top_concept' => false,
            ),
            32 =>
            array (
                'id' => 41,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/kammeindruck#20171220105520',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:55:20',
                'updated_at' => '2017-12-20 10:55:20',
                'is_top_concept' => false,
            ),
            33 =>
            array (
                'id' => 42,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/notizen#20171220105603',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:56:03',
                'updated_at' => '2017-12-20 10:56:03',
                'is_top_concept' => false,
            ),
            34 =>
            array (
                'id' => 43,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/abmessungen#20171220105653',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:56:53',
                'updated_at' => '2017-12-20 10:56:53',
                'is_top_concept' => false,
            ),
            35 =>
            array (
                'id' => 44,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/datum#20171220105725',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:57:25',
                'updated_at' => '2017-12-20 10:57:25',
                'is_top_concept' => false,
            ),
            36 =>
            array (
                'id' => 45,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/eigenschaften_der_keramik#20171220105900',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:59:00',
                'updated_at' => '2017-12-20 10:59:00',
                'is_top_concept' => false,
            ),
            37 =>
            array (
                'id' => 46,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/wandungsdicke#20171220105906',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:59:06',
                'updated_at' => '2017-12-20 10:59:06',
                'is_top_concept' => false,
            ),
            38 =>
            array (
                'id' => 47,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/gewicht#20171220105915',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 10:59:15',
                'updated_at' => '2017-12-20 10:59:15',
                'is_top_concept' => false,
            ),
            39 =>
            array (
                'id' => 48,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/fundstellentyp#20171220110055',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 11:00:55',
                'updated_at' => '2017-12-20 11:00:55',
                'is_top_concept' => false,
            ),
            40 =>
            array (
                'id' => 49,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/befundtyp#20171220110100',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 11:01:00',
                'updated_at' => '2017-12-20 11:01:00',
                'is_top_concept' => false,
            ),
            41 =>
            array (
                'id' => 50,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/siedlung#20171220110108',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 11:01:08',
                'updated_at' => '2017-12-20 11:01:08',
                'is_top_concept' => false,
            ),
            42 =>
            array (
                'id' => 51,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/hohle#20171220110113',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 11:01:13',
                'updated_at' => '2017-12-20 11:01:13',
                'is_top_concept' => false,
            ),
            43 =>
            array (
                'id' => 52,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/grube#20171220110118',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 11:01:18',
                'updated_at' => '2017-12-20 11:01:18',
                'is_top_concept' => false,
            ),
            44 =>
            array (
                'id' => 53,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/graben#20171220110123',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 11:01:23',
                'updated_at' => '2017-12-20 11:01:23',
                'is_top_concept' => false,
            ),
            45 =>
            array (
                'id' => 54,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/schicht#20171220110127',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 11:01:27',
                'updated_at' => '2017-12-20 11:01:27',
                'is_top_concept' => false,
            ),
            46 =>
            array (
                'id' => 55,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/alternativer_name#20171220165047',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 16:50:47',
                'updated_at' => '2017-12-20 16:50:47',
                'is_top_concept' => false,
            ),
            47 =>
            array (
                'id' => 56,
            'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/koordintaten_(wkt)#20171220165152',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 16:51:52',
                'updated_at' => '2017-12-20 16:51:52',
                'is_top_concept' => false,
            ),
            48 =>
            array (
                'id' => 57,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/chronologie#20171220165337',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 16:53:37',
                'updated_at' => '2017-12-20 16:53:37',
                'is_top_concept' => true,
            ),
            49 =>
            array (
                'id' => 58,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/phasen#20171220165346',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 16:53:46',
                'updated_at' => '2017-12-20 16:53:46',
                'is_top_concept' => false,
            ),
            50 =>
            array (
                'id' => 59,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/steinzeit#20171220165355',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 16:53:55',
                'updated_at' => '2017-12-20 16:53:55',
                'is_top_concept' => false,
            ),
            51 =>
            array (
                'id' => 60,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/bronzezeit#20171220165405',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 16:54:05',
                'updated_at' => '2017-12-20 16:54:05',
                'is_top_concept' => false,
            ),
            52 =>
            array (
                'id' => 61,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/eisenzeit#20171220165409',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 16:54:09',
                'updated_at' => '2017-12-20 16:54:09',
                'is_top_concept' => false,
            ),
            53 =>
            array (
                'id' => 62,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/lagerstatte#20171220165727',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 16:57:27',
                'updated_at' => '2017-12-20 16:57:27',
                'is_top_concept' => false,
            ),
            54 =>
            array (
                'id' => 63,
                'concept_url' => 'https://spacialist.escience.uni-tuebingen.de/<user-project>/aufbewahrung#20171220170755',
                'concept_scheme' => 'https://spacialist.escience.uni-tuebingen.de/schemata#newScheme',
                'user_id' => $user->id,
                'created_at' => '2017-12-20 17:07:55',
                'updated_at' => '2017-12-20 17:07:55',
                'is_top_concept' => false,
            ),
        ));
    }
}
