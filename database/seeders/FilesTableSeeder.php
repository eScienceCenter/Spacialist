<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FilesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $user = \App\User::orderBy('id')->first();
        \DB::table('files')->insert(array (
            0 =>
            array (
                'id' => 1,
                'name' => 'text3.txt',
                'modified' => '2019-03-08 13:13:11',
                'created' => '2019-03-08 13:13:11',
                'cameraname' => NULL,
                'thumb' => NULL,
                'copyright' => NULL,
                'description' => NULL,
                'mime_type' => 'text/plain',
                'user_id' => $user->id,
                'created_at' => '2019-03-08 13:13:11',
                'updated_at' => '2019-03-08 13:13:11',
            ),
            1 =>
            array (
                'id' => 2,
                'name' => 'text2.txt',
                'modified' => '2019-03-08 13:13:11',
                'created' => '2019-03-08 13:13:11',
                'cameraname' => NULL,
                'thumb' => NULL,
                'copyright' => NULL,
                'description' => NULL,
                'mime_type' => 'text/plain',
                'user_id' => $user->id,
                'created_at' => '2019-03-08 13:13:11',
                'updated_at' => '2019-03-08 13:13:11',
            ),
            2 =>
            array (
                'id' => 3,
                'name' => 'text1.txt',
                'modified' => '2019-03-08 13:13:11',
                'created' => '2019-03-08 13:13:11',
                'cameraname' => NULL,
                'thumb' => NULL,
                'copyright' => NULL,
                'description' => NULL,
                'mime_type' => 'text/plain',
                'user_id' => $user->id,
                'created_at' => '2019-03-08 13:13:11',
                'updated_at' => '2019-03-08 13:13:11',
            ),
            3 =>
            array (
                'id' => 4,
                'name' => 'spacialist_screenshot.png',
                'modified' => '2019-03-08 13:13:11',
                'created' => '2019-03-08 13:13:11',
                'cameraname' => NULL,
                'thumb' => 'spacialist_screenshot_thumb.jpg',
                'copyright' => NULL,
                'description' => NULL,
                'mime_type' => 'image/png',
                'user_id' => $user->id,
                'created_at' => '2019-03-08 13:13:11',
                'updated_at' => '2019-03-08 13:13:11',
            ),
            4 =>
            array (
                'id' => 5,
                'name' => 'test_img_edin.jpg',
                'modified' => '2019-03-08 13:13:11',
                'created' => '2017-06-18 19:46:37',
            'cameraname' => 'Canon EOS 650D (Canon)',
                'thumb' => 'test_img_edin_thumb.jpg',
                'copyright' => 'Vinzenz Rosenkranz (CC BY-NC-SA 2.0)',
                'description' => 'Edinburgh Castle',
                'mime_type' => 'image/jpeg',
                'user_id' => $user->id,
                'created_at' => '2019-03-08 13:13:11',
                'updated_at' => '2019-03-08 13:13:12',
            ),
            5 =>
            array (
                'id' => 6,
                'name' => 'test_archive.zip',
                'modified' => '2019-03-08 13:13:10',
                'created' => '2019-03-08 13:13:10',
                'cameraname' => NULL,
                'thumb' => NULL,
                'copyright' => NULL,
                'description' => NULL,
                'mime_type' => 'application/zip',
                'user_id' => $user->id,
                'created_at' => '2019-03-08 13:13:10',
                'updated_at' => '2019-03-08 13:13:10',
            ),
            6 =>
            array (
                'id' => 7,
                'name' => 'office_file.docx',
                'modified' => '2019-03-08 13:13:11',
                'created' => '2019-03-08 13:13:11',
                'cameraname' => NULL,
                'thumb' => NULL,
                'copyright' => NULL,
                'description' => NULL,
                'mime_type' => 'application/octet-stream',
                'user_id' => $user->id,
                'created_at' => '2019-03-08 13:13:11',
                'updated_at' => '2019-03-08 13:13:11',
            ),
        ));
    }
}
