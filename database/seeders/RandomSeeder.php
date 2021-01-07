<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RandomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $numberSeedEntries = 20;

        $al = new App\AvailableLayer();
        $al->name = 'OpenStreetMap';
        $al->url = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        $al->type = 'xyz';
        $al->attribution = '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>';
        $al->opacity = 1;
        $al->position = 1;
        $al->save();

        $concepts = factory(App\ThConcept::class, $numberSeedEntries)->create()->each(function ($concept) {
            foreach(App\ThLanguage::get() as $language){
                factory(App\ThConceptLabel::class)->create([
                    'concept_id' => $concept->id,
                    'language_id' => $language->id,
                ]);
            }
        });

        factory(App\ThBroader::class, $numberSeedEntries)->create();
        factory(App\ContextType::class, $numberSeedEntries)->create()->each(function ($ctype) {
            factory(App\AvailableLayer::class)->create([
                'context_type_id' => $ctype->id,
            ]);
        });

        factory(App\Attribute::class, $numberSeedEntries)->create();
        factory(App\Literature::class, $numberSeedEntries)->create();
        for($i = 0; $i < $numberSeedEntries; $i++){
            // creation needs to be done in a loop in order to have root-context relationships
            factory(App\Context::class)->create();
            factory(App\ContextAttribute::class)->create();
        }
        factory(App\Source::class, $numberSeedEntries)->create();
        factory(App\AttributeValue::class, $numberSeedEntries)->create();
    }
}
