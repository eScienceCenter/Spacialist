<?php

namespace Database\Seeders;

use App\AvailableLayer;
use Illuminate\Database\Seeder;

class MapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Unlinked Layer
        AvailableLayer::createFromArray([
            'name' => 'Unlinked',
            'url' => '',
            'type' => 'unlinked',
            'subdomains' => NULL,
            'attribution' => NULL,
            'opacity' => '1',
            'layers' => NULL,
            'styles' => NULL,
            'format' => NULL,
            'version' => NULL,
            'visible' => true,
            'is_overlay' => true,
            'api_key' => NULL,
            'layer_type' => NULL,
            'entity_type_id' => NULL,
        ]);

        // Create OSM Layer
        AvailableLayer::createFromArray([
            'name' => 'OpenStreetMap',
            'url' => 'https://{a-c}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            'type' => 'xyz',
            'subdomains' => NULL,
            'attribution' => '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
            'opacity' => '1',
            'layers' => NULL,
            'styles' => NULL,
            'format' => NULL,
            'version' => NULL,
            'visible' => true,
            'is_overlay' => false,
            'api_key' => NULL,
            'layer_type' => NULL,
            'entity_type_id' => NULL,
        ]);
    }
}
