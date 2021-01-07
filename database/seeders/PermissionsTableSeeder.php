<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('permissions')->insert(array (
            0 =>
            array (
                'id' => 1,
                'name' => 'create_concepts',
                'display_name' => 'Create concepts',
                'description' => 'create concepts',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:35',
                'updated_at' => '2017-12-20 09:47:35',
            ),
            1 =>
            array (
                'id' => 2,
                'name' => 'delete_move_concepts',
                'display_name' => 'Delete and move concepts',
                'description' => 'delete and move previously added concepts',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:35',
                'updated_at' => '2017-12-20 09:47:35',
            ),
            2 =>
            array (
                'id' => 3,
                'name' => 'duplicate_edit_concepts',
                'display_name' => 'Duplicate and edit concepts',
                'description' => 'duplicate and edit previously added concepts',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:35',
                'updated_at' => '2017-12-20 09:47:35',
            ),
            3 =>
            array (
                'id' => 4,
                'name' => 'view_concepts',
                'display_name' => 'View concepts',
                'description' => 'View a list of previously added concepts',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:35',
                'updated_at' => '2017-12-20 09:47:35',
            ),
            4 =>
            array (
                'id' => 5,
                'name' => 'view_concept_props',
                'display_name' => 'View concepts',
                'description' => 'View a list of previously added concepts',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:35',
                'updated_at' => '2017-12-20 09:47:35',
            ),
            5 =>
            array (
                'id' => 13,
                'name' => 'view_geodata',
                'display_name' => 'View geodata',
                'description' => 'view geodata',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:35',
                'updated_at' => '2017-12-20 09:47:35',
            ),
            6 =>
            array (
                'id' => 14,
                'name' => 'create_edit_geodata',
                'display_name' => 'Create and edit geodata',
                'description' => 'create and edit uploaded geodata',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:35',
                'updated_at' => '2017-12-20 09:47:35',
            ),
            7 =>
            array (
                'id' => 15,
                'name' => 'upload_remove_geodata',
                'display_name' => 'Upload and remove geodata',
                'description' => 'upload new geodata files and remove existing geodata layers',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:35',
                'updated_at' => '2017-12-20 09:47:35',
            ),
            8 =>
            array (
                'id' => 16,
                'name' => 'link_geodata',
                'display_name' => 'Link geodata',
                'description' => 'link geodata to concepts or other elements',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:36',
                'updated_at' => '2017-12-20 09:47:36',
            ),
            9 =>
            array (
                'id' => 17,
                'name' => 'view_users',
                'display_name' => 'View users',
                'description' => 'view all existing users',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:36',
                'updated_at' => '2017-12-20 09:47:36',
            ),
            10 =>
            array (
                'id' => 18,
                'name' => 'create_users',
                'display_name' => 'Create users',
                'description' => 'create new users',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:36',
                'updated_at' => '2017-12-20 09:47:36',
            ),
            11 =>
            array (
                'id' => 19,
                'name' => 'delete_users',
                'display_name' => 'Delete users',
                'description' => 'delete existing users',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:36',
                'updated_at' => '2017-12-20 09:47:36',
            ),
            12 =>
            array (
                'id' => 20,
                'name' => 'add_remove_role',
                'display_name' => 'Add and remove roles',
                'description' => 'add and remove roles from a user',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:36',
                'updated_at' => '2017-12-20 09:47:36',
            ),
            13 =>
            array (
                'id' => 21,
                'name' => 'change_password',
                'display_name' => 'Change password',
                'description' => 'change the password of a user',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:36',
                'updated_at' => '2017-12-20 09:47:36',
            ),
            14 =>
            array (
                'id' => 22,
                'name' => 'add_edit_role',
                'display_name' => 'Add and edit roles',
                'description' => 'add and edit existing roles',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:36',
                'updated_at' => '2017-12-20 09:47:36',
            ),
            15 =>
            array (
                'id' => 23,
                'name' => 'delete_role',
                'display_name' => 'Delete roles',
                'description' => 'delete existing roles',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:36',
                'updated_at' => '2017-12-20 09:47:36',
            ),
            16 =>
            array (
                'id' => 24,
                'name' => 'add_remove_permission',
                'display_name' => 'Add and remove permissions',
                'description' => 'add and remove permissions to/from roles',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:36',
                'updated_at' => '2017-12-20 09:47:36',
            ),
            17 =>
            array (
                'id' => 25,
                'name' => 'edit_preferences',
                'display_name' => 'Edit system preferences',
                'description' => 'edit system preferences',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:36',
                'updated_at' => '2017-12-20 09:47:36',
            ),
            18 =>
            array (
                'id' => 26,
                'name' => 'add_move_concepts_th',
                'display_name' => 'Add, move and relations of thesaurus concepts',
                'description' => 'add, move and add relations to concepts in thesaurex',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:36',
                'updated_at' => '2017-12-20 09:47:36',
            ),
            19 =>
            array (
                'id' => 27,
                'name' => 'delete_concepts_th',
                'display_name' => 'Delete thesaurus concepts',
                'description' => 'delete concepts in thesaurex',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:36',
                'updated_at' => '2017-12-20 09:47:36',
            ),
            20 =>
            array (
                'id' => 28,
                'name' => 'edit_concepts_th',
                'display_name' => 'Edit thesaurus concepts',
                'description' => 'edit (modify labels) concepts in thesaurex',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:36',
                'updated_at' => '2017-12-20 09:47:36',
            ),
            21 =>
            array (
                'id' => 29,
                'name' => 'export_th',
                'display_name' => 'Export thesaurus concepts',
                'description' => 'export (sub-)trees in thesaurex',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:36',
                'updated_at' => '2017-12-20 09:47:36',
            ),
            22 =>
            array (
                'id' => 30,
                'name' => 'view_concepts_th',
                'display_name' => 'View thesaurus concepts',
                'description' => 'view concepts in thesaurex',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:36',
                'updated_at' => '2017-12-20 09:47:36',
            ),
            23 =>
            array (
                'id' => 8,
                'name' => 'manage_files',
                'display_name' => 'Manage files',
                'description' => 'upload and remove files',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:35',
                'updated_at' => '2017-12-20 09:47:35',
            ),
            24 =>
            array (
                'id' => 9,
                'name' => 'link_files',
                'display_name' => 'Link files',
                'description' => 'link files to concepts',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:35',
                'updated_at' => '2017-12-20 09:47:35',
            ),
            25 =>
            array (
                'id' => 10,
                'name' => 'edit_file_props',
                'display_name' => 'Edit file properties',
                'description' => 'edit the properties of uploaded files',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:35',
                'updated_at' => '2017-12-20 09:47:35',
            ),
            26 =>
            array (
                'id' => 11,
                'name' => 'view_files',
                'display_name' => 'View files',
                'description' => 'view uploaded files',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:35',
                'updated_at' => '2017-12-20 09:47:35',
            ),
            27 =>
            array (
                'id' => 12,
                'name' => 'export_files',
                'display_name' => 'Export files',
                'description' => 'export files in different resolutions and formats',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:35',
                'updated_at' => '2017-12-20 09:47:35',
            ),
            28 =>
            array (
                'id' => 6,
                'name' => 'edit_bibliography',
                'display_name' => 'Edit bibliography',
                'description' => 'edit bibliography entries',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:35',
                'updated_at' => '2017-12-20 09:47:35',
            ),
            29 =>
            array (
                'id' => 7,
                'name' => 'add_remove_bibliography',
                'display_name' => 'Add and remove bibliography',
                'description' => 'add and remove bibliography entries',
                'guard_name' => 'web',
                'created_at' => '2017-12-20 09:47:35',
                'updated_at' => '2017-12-20 09:47:35',
            ),
        ));
    }
}
