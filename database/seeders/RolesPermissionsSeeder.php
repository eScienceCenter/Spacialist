<?php

namespace Database\Seeders;

use App\Role;
use App\Permission;
use Illuminate\Database\Seeder;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Roles
        // Admin
        $admin = Role::where('name', 'admin')->first();
        if($admin === null) {
            $admin = new Role();
            $admin->name = 'admin';
            $admin->display_name = 'Administrator';
            $admin->description = 'Project Administrator';
            $admin->guard_name = 'web';
            $admin->save();
        }
        // Add all permissions to admin

        // Guest
        $guest = Role::where('name', 'guest')->first();
        if($guest === null) {
            $guest = new Role();
            $guest->name = 'guest';
            $guest->display_name = 'Guest';
            $guest->description = 'Guest User';
            $guest->guard_name = 'web';
            $guest->save();
        }

        // Permissions
        // Create concepts
        if(Permission::where('name', 'create_concepts')->first() === null) {
            $create_concepts = new Permission();
            $create_concepts->name = 'create_concepts';
            $create_concepts->display_name = 'Create concepts';
            $create_concepts->description = 'create concepts';
            $create_concepts->guard_name = 'web';
            $create_concepts->save();
            $admin->givePermissionTo($create_concepts);
        }
        // Delete/move concepts
        if(Permission::where('name', 'delete_move_concepts')->first() === null) {
            $delete_move_concepts = new Permission();
            $delete_move_concepts->name = 'delete_move_concepts';
            $delete_move_concepts->display_name = 'Delete and move concepts';
            $delete_move_concepts->description = 'delete and move previously added concepts';
            $delete_move_concepts->guard_name = 'web';
            $delete_move_concepts->save();
            $admin->givePermissionTo($delete_move_concepts);
        }
        // Dpulicate/edit concepts
        if(Permission::where('name', 'duplicate_edit_concepts')->first() === null) {
            $duplicate_edit_concepts = new Permission();
            $duplicate_edit_concepts->name = 'duplicate_edit_concepts';
            $duplicate_edit_concepts->display_name = 'Duplicate and edit concepts';
            $duplicate_edit_concepts->description = 'duplicate and edit previously added concepts';
            $duplicate_edit_concepts->guard_name = 'web';
            $duplicate_edit_concepts->save();
            $admin->givePermissionTo($duplicate_edit_concepts);
        }
        // View concepts
        if(Permission::where('name', 'view_concepts')->first() === null) {
            $view_concepts = new Permission();
            $view_concepts->name = 'view_concepts';
            $view_concepts->display_name = 'View concepts';
            $view_concepts->description = 'View a list of previously added concepts';
            $view_concepts->guard_name = 'web';
            $view_concepts->save();
            $admin->givePermissionTo($view_concepts);
            $guest->givePermissionTo($view_concepts);
        }
        // View concept properties
        if(Permission::where('name', 'view_concept_props')->first() === null) {
            $view_concept_props = new Permission();
            $view_concept_props->name = 'view_concept_props';
            $view_concept_props->display_name = 'View concepts';
            $view_concept_props->description = 'View a list of previously added concepts';
            $view_concept_props->guard_name = 'web';
            $view_concept_props->save();
            $admin->givePermissionTo($view_concept_props);
            $guest->givePermissionTo($view_concept_props);
        }
        // Edit bibliography
        if(Permission::where('name', 'edit_bibliography')->first() === null) {
            $edit_bibliography = new Permission();
            $edit_bibliography->name = 'edit_bibliography';
            $edit_bibliography->display_name = 'Edit bibliography';
            $edit_bibliography->description = 'edit bibliography entries';
            $edit_bibliography->guard_name = 'web';
            $edit_bibliography->save();
            $admin->givePermissionTo($edit_bibliography);
        }
        // Add/remove bibliography
        if(Permission::where('name', 'add_remove_bibliography')->first() === null) {
            $add_remove_bibliography = new Permission();
            $add_remove_bibliography->name = 'add_remove_bibliography';
            $add_remove_bibliography->display_name = 'Add and remove bibliography';
            $add_remove_bibliography->description = 'add and remove bibliography entries';
            $add_remove_bibliography->guard_name = 'web';
            $add_remove_bibliography->save();
            $admin->givePermissionTo($add_remove_bibliography);
        }
        // Manage files
        if(Permission::where('name', 'manage_files')->first() === null) {
            $manage_files = new Permission();
            $manage_files->name = 'manage_files';
            $manage_files->display_name = 'Manage files';
            $manage_files->description = 'upload and remove files';
            $manage_files->guard_name = 'web';
            $manage_files->save();
            $admin->givePermissionTo($manage_files);
        }
        // Link files
        if(Permission::where('name', 'link_files')->first() === null) {
            $link_files = new Permission();
            $link_files->name = 'link_files';
            $link_files->display_name = 'Link files';
            $link_files->description = 'link files to concepts';
            $link_files->guard_name = 'web';
            $link_files->save();
            $admin->givePermissionTo($link_files);
        }
        // Edit file properties
        if(Permission::where('name', 'edit_file_props')->first() === null) {
            $edit_file_props = new Permission();
            $edit_file_props->name = 'edit_file_props';
            $edit_file_props->display_name = 'Edit file properties';
            $edit_file_props->description = 'edit the properties of uploaded files';
            $edit_file_props->guard_name = 'web';
            $edit_file_props->save();
            $admin->givePermissionTo($edit_file_props);
        }
        // View files
        if(Permission::where('name', 'view_files')->first() === null) {
            $view_files = new Permission();
            $view_files->name = 'view_files';
            $view_files->display_name = 'View files';
            $view_files->description = 'view uploaded files';
            $view_files->guard_name = 'web';
            $view_files->save();
            $admin->givePermissionTo($view_files);
            $guest->givePermissionTo($view_files);
        }
        // Export files
        if(Permission::where('name', 'export_files')->first() === null) {
            $export_files = new Permission();
            $export_files->name = 'export_files';
            $export_files->display_name = 'Export files';
            $export_files->description = 'export files in different resolutions and formats';
            $export_files->guard_name = 'web';
            $export_files->save();
            $admin->givePermissionTo($export_files);
        }
        // View geodata
        if(Permission::where('name', 'view_geodata')->first() === null) {
            $view_geodata = new Permission();
            $view_geodata->name = 'view_geodata';
            $view_geodata->display_name = 'View geodata';
            $view_geodata->description = 'view geodata';
            $view_geodata->guard_name = 'web';
            $view_geodata->save();
            $admin->givePermissionTo($view_geodata);
            $guest->givePermissionTo($view_geodata);
        }
        // Create/edit geodata
        if(Permission::where('name', 'create_edit_geodata')->first() === null) {
            $create_edit_geodata = new Permission();
            $create_edit_geodata->name = 'create_edit_geodata';
            $create_edit_geodata->display_name = 'Create and edit geodata';
            $create_edit_geodata->description = 'create and edit uploaded geodata';
            $create_edit_geodata->guard_name = 'web';
            $create_edit_geodata->save();
            $admin->givePermissionTo($create_edit_geodata);
        }
        // Upload/remove geodata
        if(Permission::where('name', 'upload_remove_geodata')->first() === null) {
            $upload_remove_geodata = new Permission();
            $upload_remove_geodata->name = 'upload_remove_geodata';
            $upload_remove_geodata->display_name = 'Upload and remove geodata';
            $upload_remove_geodata->description = 'upload new geodata files and remove existing geodata layers';
            $upload_remove_geodata->guard_name = 'web';
            $upload_remove_geodata->save();
            $admin->givePermissionTo($upload_remove_geodata);
        }
        // Link geodata
        if(Permission::where('name', 'link_geodata')->first() === null) {
            $link_geodata = new Permission();
            $link_geodata->name = 'link_geodata';
            $link_geodata->display_name = 'Link geodata';
            $link_geodata->description = 'link geodata to concepts or other elements';
            $link_geodata->guard_name = 'web';
            $link_geodata->save();
            $admin->givePermissionTo($link_geodata);
        }
        // View users
        if(Permission::where('name', 'view_users')->first() === null) {
            $view_users = new Permission();
            $view_users->name = 'view_users';
            $view_users->display_name = 'View users';
            $view_users->description = 'view all existing users';
            $view_users->guard_name = 'web';
            $view_users->save();
            $admin->givePermissionTo($view_users);
        }
        // Create users
        if(Permission::where('name', 'create_users')->first() === null) {
            $create_users = new Permission();
            $create_users->name = 'create_users';
            $create_users->display_name = 'Create users';
            $create_users->description = 'create new users';
            $create_users->guard_name = 'web';
            $create_users->save();
            $admin->givePermissionTo($create_users);
        }
        // Delete users
        if(Permission::where('name', 'delete_users')->first() === null) {
            $delete_users = new Permission();
            $delete_users->name = 'delete_users';
            $delete_users->display_name = 'Delete users';
            $delete_users->description = 'delete existing users';
            $delete_users->guard_name = 'web';
            $delete_users->save();
            $admin->givePermissionTo($delete_users);
        }
        // Add/remove role
        if(Permission::where('name', 'add_remove_role')->first() === null) {
            $add_remove_role = new Permission();
            $add_remove_role->name = 'add_remove_role';
            $add_remove_role->display_name = 'Add and remove roles';
            $add_remove_role->description = 'add and remove roles from a user';
            $add_remove_role->guard_name = 'web';
            $add_remove_role->save();
            $admin->givePermissionTo($add_remove_role);
        }
        // Change password
        if(Permission::where('name', 'change_password')->first() === null) {
            $change_password = new Permission();
            $change_password->name = 'change_password';
            $change_password->display_name = 'Change password';
            $change_password->description = 'change the password of a user';
            $change_password->guard_name = 'web';
            $change_password->save();
            $admin->givePermissionTo($change_password);
        }
        // Add and edit roles
        if(Permission::where('name', 'add_edit_role')->first() === null) {
            $add_edit_role = new Permission();
            $add_edit_role->name = 'add_edit_role';
            $add_edit_role->display_name = 'Add and edit roles';
            $add_edit_role->description = 'add and edit existing roles';
            $add_edit_role->guard_name = 'web';
            $add_edit_role->save();
            $admin->givePermissionTo($add_edit_role);
        }
        // Delete roles
        if(Permission::where('name', 'delete_role')->first() === null) {
            $delete_role = new Permission();
            $delete_role->name = 'delete_role';
            $delete_role->display_name = 'Delete roles';
            $delete_role->description = 'delete existing roles';
            $delete_role->guard_name = 'web';
            $delete_role->save();
            $admin->givePermissionTo($delete_role);
        }
        // Add and remove permissions
        if(Permission::where('name', 'add_remove_permission')->first() === null) {
            $add_remove_permission = new Permission();
            $add_remove_permission->name = 'add_remove_permission';
            $add_remove_permission->display_name = 'Add and remove permissions';
            $add_remove_permission->description = 'add and remove permissions to/from roles';
            $add_remove_permission->guard_name = 'web';
            $add_remove_permission->save();
            $admin->givePermissionTo($add_remove_permission);
        }
        // Edit system preferences
        if(Permission::where('name', 'edit_preferences')->first() === null) {
            $edit_preferences = new Permission();
            $edit_preferences->name = 'edit_preferences';
            $edit_preferences->display_name = 'Edit system preferences';
            $edit_preferences->description = 'edit system preferences';
            $edit_preferences->guard_name = 'web';
            $edit_preferences->save();
            $admin->givePermissionTo($edit_preferences);
        }
        // Add & Move thesaurus concepts
        if(Permission::where('name', 'add_move_concepts_th')->first() === null) {
            $add_move_concepts_th = new Permission();
            $add_move_concepts_th->name = 'add_move_concepts_th';
            $add_move_concepts_th->display_name = 'Add, move and relations of thesaurus concepts';
            $add_move_concepts_th->description = 'add, move and add relations to concepts in thesaurex';
            $add_move_concepts_th->guard_name = 'web';
            $add_move_concepts_th->save();
            $admin->givePermissionTo($add_move_concepts_th);
        }
        // Delete thesaurus concepts
        if(Permission::where('name', 'delete_concepts_th')->first() === null) {
            $delete_concepts_th = new Permission();
            $delete_concepts_th->name = 'delete_concepts_th';
            $delete_concepts_th->display_name = 'Delete thesaurus concepts';
            $delete_concepts_th->description = 'delete concepts in thesaurex';
            $delete_concepts_th->guard_name = 'web';
            $delete_concepts_th->save();
            $admin->givePermissionTo($delete_concepts_th);
        }
        // Edit thesaurus concepts
        if(Permission::where('name', 'edit_concepts_th')->first() === null) {
            $edit_concepts_th = new Permission();
            $edit_concepts_th->name = 'edit_concepts_th';
            $edit_concepts_th->display_name = 'Edit thesaurus concepts';
            $edit_concepts_th->description = 'edit (modify labels) concepts in thesaurex';
            $edit_concepts_th->guard_name = 'web';
            $edit_concepts_th->save();
            $admin->givePermissionTo($edit_concepts_th);
        }
        // Export thesaurus concepts
        if(Permission::where('name', 'export_th')->first() === null) {
            $export_th = new Permission();
            $export_th->name = 'export_th';
            $export_th->display_name = 'Export thesaurus concepts';
            $export_th->description = 'export (sub-)trees in thesaurex';
            $export_th->guard_name = 'web';
            $export_th->save();
            $admin->givePermissionTo($export_th);
        }
        // View thesaurus concepts
        if(Permission::where('name', 'view_concepts_th')->first() === null) {
            $view_concepts_th = new Permission();
            $view_concepts_th->name = 'view_concepts_th';
            $view_concepts_th->display_name = 'View thesaurus concepts';
            $view_concepts_th->description = 'view concepts in thesaurex';
            $view_concepts_th->guard_name = 'web';
            $view_concepts_th->save();
            $admin->givePermissionTo($view_concepts_th);
            $guest->givePermissionTo($view_concepts_th);
        }
    }
}
