<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ThConceptLabelTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $user = \App\User::orderBy('id')->first();
        \DB::table('th_concept_label')->insert(array (
            0 =>
            array (
                'id' => 1,
                'user_id' => $user->id,
                'label' => 'Fundstelle',
                'concept_id' => 1,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 09:49:11',
                'updated_at' => '2017-12-20 09:49:11',
            ),
            1 =>
            array (
                'id' => 2,
                'user_id' => $user->id,
                'label' => 'Befund',
                'concept_id' => 2,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 09:49:16',
                'updated_at' => '2017-12-20 09:49:16',
            ),
            2 =>
            array (
                'id' => 3,
                'user_id' => $user->id,
                'label' => 'Fundobjekt',
                'concept_id' => 3,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 09:49:21',
                'updated_at' => '2017-12-20 09:49:21',
            ),
            3 =>
            array (
                'id' => 9,
                'user_id' => $user->id,
                'label' => 'Keramik',
                'concept_id' => 9,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 09:56:51',
                'updated_at' => '2017-12-20 09:56:51',
            ),
            4 =>
            array (
                'id' => 10,
                'user_id' => $user->id,
                'label' => 'Stein',
                'concept_id' => 10,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 09:57:19',
                'updated_at' => '2017-12-20 09:57:19',
            ),
            5 =>
            array (
                'id' => 11,
                'user_id' => $user->id,
                'label' => 'Gefäßtyp',
                'concept_id' => 11,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 09:57:39',
                'updated_at' => '2017-12-20 09:57:39',
            ),
            6 =>
            array (
                'id' => 12,
                'user_id' => $user->id,
                'label' => 'Art Fundobjekt',
                'concept_id' => 12,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 09:58:07',
                'updated_at' => '2017-12-20 09:58:07',
            ),
            7 =>
            array (
                'id' => 13,
                'user_id' => $user->id,
                'label' => 'Site',
                'concept_id' => 1,
                'language_id' => 2,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:00:16',
                'updated_at' => '2017-12-20 10:00:16',
            ),
            8 =>
            array (
                'id' => 14,
                'user_id' => $user->id,
                'label' => 'Feature',
                'concept_id' => 2,
                'language_id' => 2,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:00:22',
                'updated_at' => '2017-12-20 10:00:22',
            ),
            9 =>
            array (
                'id' => 15,
                'user_id' => $user->id,
                'label' => 'Find',
                'concept_id' => 3,
                'language_id' => 2,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:00:27',
                'updated_at' => '2017-12-20 10:00:27',
            ),
            10 =>
            array (
                'id' => 16,
                'user_id' => $user->id,
                'label' => 'Pottery',
                'concept_id' => 9,
                'language_id' => 2,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:00:36',
                'updated_at' => '2017-12-20 10:00:36',
            ),
            11 =>
            array (
                'id' => 17,
                'user_id' => $user->id,
                'label' => 'Stone',
                'concept_id' => 10,
                'language_id' => 2,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:00:41',
                'updated_at' => '2017-12-20 10:00:41',
            ),
            12 =>
            array (
                'id' => 19,
                'user_id' => $user->id,
                'label' => 'Eigenschaften',
                'concept_id' => 13,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:02:51',
                'updated_at' => '2017-12-20 10:02:51',
            ),
            13 =>
            array (
                'id' => 22,
                'user_id' => $user->id,
                'label' => 'Erhaltung',
                'concept_id' => 16,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:04:37',
                'updated_at' => '2017-12-20 10:04:37',
            ),
            14 =>
            array (
                'id' => 21,
                'user_id' => $user->id,
                'label' => 'Formen',
                'concept_id' => 15,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:04:27',
                'updated_at' => '2017-12-20 10:04:50',
            ),
            15 =>
            array (
                'id' => 23,
                'user_id' => $user->id,
                'label' => 'Farbe',
                'concept_id' => 17,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:05:06',
                'updated_at' => '2017-12-20 10:05:06',
            ),
            16 =>
            array (
                'id' => 24,
                'user_id' => $user->id,
                'label' => 'rot',
                'concept_id' => 18,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:05:16',
                'updated_at' => '2017-12-20 10:05:16',
            ),
            17 =>
            array (
                'id' => 25,
                'user_id' => $user->id,
                'label' => 'blau',
                'concept_id' => 19,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:05:19',
                'updated_at' => '2017-12-20 10:05:19',
            ),
            18 =>
            array (
                'id' => 26,
                'user_id' => $user->id,
                'label' => 'grün',
                'concept_id' => 20,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:05:24',
                'updated_at' => '2017-12-20 10:05:24',
            ),
            19 =>
            array (
                'id' => 29,
                'user_id' => $user->id,
                'label' => 'Gefäßformen',
                'concept_id' => 23,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:05:59',
                'updated_at' => '2017-12-20 10:05:59',
            ),
            20 =>
            array (
                'id' => 31,
                'user_id' => $user->id,
                'label' => 'Offene Gefäßform',
                'concept_id' => 25,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:06:38',
                'updated_at' => '2017-12-20 10:06:38',
            ),
            21 =>
            array (
                'id' => 32,
                'user_id' => $user->id,
                'label' => 'Geschlossene Gefäßform',
                'concept_id' => 26,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:06:48',
                'updated_at' => '2017-12-20 10:06:48',
            ),
            22 =>
            array (
                'id' => 33,
                'user_id' => $user->id,
                'label' => 'Befundform im Profil',
                'concept_id' => 27,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:07:06',
                'updated_at' => '2017-12-20 10:07:06',
            ),
            23 =>
            array (
                'id' => 30,
                'user_id' => $user->id,
                'label' => 'Befundformen',
                'concept_id' => 24,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:06:07',
                'updated_at' => '2017-12-20 10:07:15',
            ),
            24 =>
            array (
                'id' => 34,
                'user_id' => $user->id,
                'label' => 'Befundform im Planum',
                'concept_id' => 28,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:07:24',
                'updated_at' => '2017-12-20 10:07:24',
            ),
            25 =>
            array (
                'id' => 35,
                'user_id' => $user->id,
                'label' => 'Dokumentation und Administration',
                'concept_id' => 29,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:09:06',
                'updated_at' => '2017-12-20 10:09:06',
            ),
            26 =>
            array (
                'id' => 36,
                'user_id' => $user->id,
                'label' => 'Aktion',
                'concept_id' => 30,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:09:27',
                'updated_at' => '2017-12-20 10:09:27',
            ),
            27 =>
            array (
                'id' => 37,
                'user_id' => $user->id,
                'label' => 'Zeichnung angefertigt',
                'concept_id' => 31,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:09:33',
                'updated_at' => '2017-12-20 10:09:33',
            ),
            28 =>
            array (
                'id' => 38,
                'user_id' => $user->id,
                'label' => 'Verzierung',
                'concept_id' => 32,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:54:21',
                'updated_at' => '2017-12-20 10:54:21',
            ),
            29 =>
            array (
                'id' => 39,
                'user_id' => $user->id,
                'label' => 'Gefäßposition',
                'concept_id' => 33,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:54:34',
                'updated_at' => '2017-12-20 10:54:34',
            ),
            30 =>
            array (
                'id' => 40,
                'user_id' => $user->id,
                'label' => 'Verzierungselement',
                'concept_id' => 34,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:54:40',
                'updated_at' => '2017-12-20 10:54:40',
            ),
            31 =>
            array (
                'id' => 41,
                'user_id' => $user->id,
                'label' => 'Rand',
                'concept_id' => 35,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:54:47',
                'updated_at' => '2017-12-20 10:54:47',
            ),
            32 =>
            array (
                'id' => 42,
                'user_id' => $user->id,
                'label' => 'Hals',
                'concept_id' => 36,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:54:54',
                'updated_at' => '2017-12-20 10:54:54',
            ),
            33 =>
            array (
                'id' => 43,
                'user_id' => $user->id,
                'label' => 'Schulter',
                'concept_id' => 37,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:55:00',
                'updated_at' => '2017-12-20 10:55:00',
            ),
            34 =>
            array (
                'id' => 44,
                'user_id' => $user->id,
                'label' => 'Bauch',
                'concept_id' => 38,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:55:05',
                'updated_at' => '2017-12-20 10:55:05',
            ),
            35 =>
            array (
                'id' => 45,
                'user_id' => $user->id,
                'label' => 'Boden',
                'concept_id' => 39,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:55:08',
                'updated_at' => '2017-12-20 10:55:08',
            ),
            36 =>
            array (
                'id' => 46,
                'user_id' => $user->id,
                'label' => 'Riefung',
                'concept_id' => 40,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:55:13',
                'updated_at' => '2017-12-20 10:55:13',
            ),
            37 =>
            array (
                'id' => 47,
                'user_id' => $user->id,
                'label' => 'Kammeindruck',
                'concept_id' => 41,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:55:20',
                'updated_at' => '2017-12-20 10:55:20',
            ),
            38 =>
            array (
                'id' => 48,
                'user_id' => $user->id,
                'label' => 'Notizen',
                'concept_id' => 42,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:56:03',
                'updated_at' => '2017-12-20 10:56:03',
            ),
            39 =>
            array (
                'id' => 49,
                'user_id' => $user->id,
                'label' => 'Abmessungen',
                'concept_id' => 43,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:56:53',
                'updated_at' => '2017-12-20 10:56:53',
            ),
            40 =>
            array (
                'id' => 50,
                'user_id' => $user->id,
                'label' => 'Datum',
                'concept_id' => 44,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:57:25',
                'updated_at' => '2017-12-20 10:58:08',
            ),
            41 =>
            array (
                'id' => 51,
                'user_id' => $user->id,
                'label' => 'Eigenschaften der Keramik',
                'concept_id' => 45,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:59:00',
                'updated_at' => '2017-12-20 10:59:00',
            ),
            42 =>
            array (
                'id' => 52,
                'user_id' => $user->id,
                'label' => 'Wandungsdicke',
                'concept_id' => 46,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:59:06',
                'updated_at' => '2017-12-20 10:59:06',
            ),
            43 =>
            array (
                'id' => 53,
                'user_id' => $user->id,
                'label' => 'Gewicht',
                'concept_id' => 47,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 10:59:15',
                'updated_at' => '2017-12-20 10:59:15',
            ),
            44 =>
            array (
                'id' => 54,
                'user_id' => $user->id,
                'label' => 'Fundstellentyp',
                'concept_id' => 48,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 11:00:55',
                'updated_at' => '2017-12-20 11:00:55',
            ),
            45 =>
            array (
                'id' => 55,
                'user_id' => $user->id,
                'label' => 'Befundtyp',
                'concept_id' => 49,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 11:01:00',
                'updated_at' => '2017-12-20 11:01:00',
            ),
            46 =>
            array (
                'id' => 56,
                'user_id' => $user->id,
                'label' => 'Siedlung',
                'concept_id' => 50,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 11:01:09',
                'updated_at' => '2017-12-20 11:01:09',
            ),
            47 =>
            array (
                'id' => 57,
                'user_id' => $user->id,
                'label' => 'Höhle',
                'concept_id' => 51,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 11:01:13',
                'updated_at' => '2017-12-20 11:01:13',
            ),
            48 =>
            array (
                'id' => 58,
                'user_id' => $user->id,
                'label' => 'Grube',
                'concept_id' => 52,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 11:01:18',
                'updated_at' => '2017-12-20 11:01:18',
            ),
            49 =>
            array (
                'id' => 59,
                'user_id' => $user->id,
                'label' => 'Graben',
                'concept_id' => 53,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 11:01:23',
                'updated_at' => '2017-12-20 11:01:23',
            ),
            50 =>
            array (
                'id' => 60,
                'user_id' => $user->id,
                'label' => 'Schicht',
                'concept_id' => 54,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 11:01:27',
                'updated_at' => '2017-12-20 11:01:27',
            ),
            51 =>
            array (
                'id' => 61,
                'user_id' => $user->id,
                'label' => 'Alternativer Name',
                'concept_id' => 55,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 16:50:47',
                'updated_at' => '2017-12-20 16:50:47',
            ),
            52 =>
            array (
                'id' => 62,
                'user_id' => $user->id,
            'label' => 'Koordintaten (WKT)',
                'concept_id' => 56,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 16:51:52',
                'updated_at' => '2017-12-20 16:51:52',
            ),
            53 =>
            array (
                'id' => 63,
                'user_id' => $user->id,
                'label' => 'Chronologie',
                'concept_id' => 57,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 16:53:37',
                'updated_at' => '2017-12-20 16:53:37',
            ),
            54 =>
            array (
                'id' => 64,
                'user_id' => $user->id,
                'label' => 'Phasen',
                'concept_id' => 58,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 16:53:46',
                'updated_at' => '2017-12-20 16:53:46',
            ),
            55 =>
            array (
                'id' => 65,
                'user_id' => $user->id,
                'label' => 'Steinzeit',
                'concept_id' => 59,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 16:53:55',
                'updated_at' => '2017-12-20 16:53:55',
            ),
            56 =>
            array (
                'id' => 66,
                'user_id' => $user->id,
                'label' => 'Bronzezeit',
                'concept_id' => 60,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 16:54:05',
                'updated_at' => '2017-12-20 16:54:05',
            ),
            57 =>
            array (
                'id' => 67,
                'user_id' => $user->id,
                'label' => 'Eisenzeit',
                'concept_id' => 61,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 16:54:09',
                'updated_at' => '2017-12-20 16:54:09',
            ),
            58 =>
            array (
                'id' => 68,
                'user_id' => $user->id,
                'label' => 'Lagerstätte',
                'concept_id' => 62,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 16:57:27',
                'updated_at' => '2017-12-20 16:57:27',
            ),
            59 =>
            array (
                'id' => 69,
                'user_id' => $user->id,
                'label' => 'Aufbewahrung',
                'concept_id' => 63,
                'language_id' => 1,
                'concept_label_type' => 1,
                'created_at' => '2017-12-20 17:07:55',
                'updated_at' => '2017-12-20 17:07:55',
            ),
        ));
    }
}
