<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);

        $this->call(ThLanguageTableSeeder::class);
        $this->call(ThConceptTableSeeder::class);
        $this->call(ThConceptMasterTableSeeder::class);
        $this->call(ThConceptLabelTableSeeder::class);
        $this->call(ThConceptLabelMasterTableSeeder::class);
        $this->call(ThBroadersTableSeeder::class);
        $this->call(ThBroadersMasterTableSeeder::class);
        $this->call(ThConceptNotesTableSeeder::class);
        $this->call(ThConceptNotesMasterTableSeeder::class);

        $this->call(PermissionsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(PermissionRoleTableSeeder::class);
        $this->call(RoleUserTableSeeder::class);

        $this->call(BibliographyTableSeeder::class);
        $this->call(GeodataTableSeeder::class);
        $this->call(AttributesTableSeeder::class);
        $this->call(EntityTypesTableSeeder::class);
        $this->call(EntitiesTableSeeder::class);
        $this->call(FilesTableSeeder::class);
        $this->call(FileTagsTableSeeder::class);
        $this->call(AvailableLayersTableSeeder::class);
        $this->call(EntityTypeRelationsTableSeeder::class);
        $this->call(EntityAttributesTableSeeder::class);
        $this->call(AttributeValuesTableSeeder::class);
        $this->call(EntityFilesTableSeeder::class);
        $this->call(ReferencesTableSeeder::class);

        // Set different root for tags
        \DB::table('preferences')
            ->where('label', 'prefs.tag-root')
            ->update(['default_value' => '{"uri": "https://spacialist.escience.uni-tuebingen.de/<user-project>/eigenschaften#20171220100251"}']);

        // Move seeded test files to storage
        $testFiles = [
            'text1.txt',
            'text1.2.txt',
            'text2.txt',
            'text3.txt',
            'office_file.docx',
            'spacialist_screenshot.png',
            'spacialist_screenshot_thumb.jpg',
            'test_img_edin.jpg',
            'test_img_edin_thumb.jpg',
            'test_archive.zip',
        ];
        $path = storage_path() . "/framework/testing/";
        foreach($testFiles as $f) {
            $filehandle = fopen("$path$f", 'r');
            Storage::put(
                $f,
                $filehandle
            );
            fclose($filehandle);
        }

        if(\DB::connection()->getDriverName() === 'pgsql') {
            $this->call(FixSequencesSeeder::class);
        }
    }
}
