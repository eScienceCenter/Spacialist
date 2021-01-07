<?php

namespace Database\Factories;

use MStaack\LaravelPostgis\Geometries\Point;
use MStaack\LaravelPostgis\Geometries\LineString;
use MStaack\LaravelPostgis\Geometries\Polygon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Faker\Generator as Faker;


/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/



/*
| Factory for the test user
*/
$factory->defineAs(App\User::class, 'guest', function () {
    return [
        'name' => "Guest User",
        'nickname' => "g_user",
        'email' => "udontneedtoseehisidentification@rebels.tld",
        'password' => Hash::make("thesearentthedroidsuarelookingfor"),
    ];
});

/*
| Factories for entries with completely random values
*/
$factory->define(App\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'nickname' => Str::lower($faker->firstName),
        'email' => $faker->email,
        'password' => $faker->password,
    ];
});

$factory->define(App\ThConcept::class, function(Faker $faker) {
    $user = App\User::inRandomOrder()->first();
    if(!isset($user)){
        $user = factory(App\User::class)->create();
    }
    return [
        'concept_url' => $faker->unique()->url,
        'concept_scheme' => '',
        'user_id' => $user->id,
    ];
});

$factory->define(App\ThBroader::class, function(Faker $faker) {
    $broader = App\ThConcept::inRandomOrder()->first();
    if(!isset($broader)){
        $broader = factory(App\ThConcept::class)->create();
    }
    $narrower = App\ThConcept::inRandomOrder()->first();
    if(!isset($narrower)){
        $narrower = factory(App\ThConcept::class)->create();
    }
    return [
        'broader_id' => $broader->id,
        'narrower_id' => $narrower->id,
    ];
});

$factory->define(App\ThConceptLabel::class, function(Faker $faker) {
    $language = App\ThLanguage::first();
    $user = App\User::inRandomOrder()->first();
    if(!isset($user)){
        $user = factory(App\User::class)->create();
    }
    if(!isset($language)){
        $de = new App\ThLanguage;
        $de->user_id = $user->id;
        $de->display_name = 'Deutsch';
        $de->short_name = 'de';
        $de->save();
        $language = $de;
    }

    $concept = App\ThConcept::inRandomOrder()->first();
    if(!isset($concept)){
        $concept = factory(App\ThConcept::class)->create();
    }

    return [
        'concept_id' => $concept->id,
        'language_id' => $language->id,
        'label' => $faker->word,
        'concept_label_type' => 1,
        'user_id' => $user->id
    ];
});

$factory->define(App\ContextType::class, function(Faker $faker) {
    $concept = App\ThConcept::inRandomOrder()->first();
    if(!isset($concept)){
        $concept = factory(App\ThConcept::class)->create();
    }
    return [
        'thesaurus_url' => $concept->concept_url,
        'type' => $faker->randomElement([0,1]),
    ];
});

$factory->define(App\AvailableLayer::class, function(Faker $faker) {
    return [
        'name' => '',
        'url' => '',
        'type' => $faker->randomElement(App\Geodata::availableGeometryTypes),
        'opacity' => 1,
        'visible' => true,
        'is_overlay' => true,
        'position' => App\AvailableLayer::where('is_overlay', '=', true)->max('position') + 1,
        'color' => sprintf('#%06X', mt_rand(0, 0xFFFFFF)),
    ];
});

$factory->define(App\Attribute::class, function(Faker $faker) {
    $concept = App\ThConcept::inRandomOrder()->first();
    if(!isset($concept)){
        $concept = factory(App\ThConcept::class)->create();
    }
    $root_concept = App\ThConcept::inRandomOrder()->first();
    if(!isset($root_concept)){
        $root_concept = factory(App\ThConcept::class)->create();
    }
    return [
        'thesaurus_url' => $concept->concept_url,
        'thesaurus_root_url' => $faker->optional()->randomElement([$root_concept->concept_url]),
        'datatype' => $faker->randomElement(['string', 'list', 'date', 'stringf', 'epoch', 'timeperiod', 'string-sc', 'string-mc', 'dimension', 'integer', 'boolean', 'percentage', 'context', 'geography', 'double']),
    ];
});

$factory->define(App\ContextAttribute::class, function(Faker $faker) {
    $contextType = App\ContextType::inRandomOrder()->first();
    if(!isset($contextType)){
        $contextType = factory(App\ContextType::class)->create();
    }
    $attribute = App\Attribute::inRandomOrder()->first();
    if(!isset($attribute)){
        $attribute = factory(App\Attribute::class)->create();
    }
    $position = App\ContextAttribute::where('context_type_id', $contextType->id)->max('position') + 1;
    return [
        'context_type_id' => $contextType->id,
        'attribute_id' => $attribute->id,
        'position' => $position
    ];
});

$factory->define(App\Literature::class, function(Faker $faker) {
    return [
        'author' => $faker->optional($weight=0.9)->name,
        'editor'=> $faker->optional($weight=0.1)->name,
        'title' => $faker->sentence($faker->numberBetween(3, 10)),
        'journal' => $faker->optional($weight=0.9)->word,
        'year' => $faker->optional($weight=0.9)->year,
        'pages' => $faker->optional($weight=0.9)->randomDigit(3),
        'volume' => $faker->optional($weight=0.5)->randomDigit,
        'number' => $faker->optional($weight=0.5)->randomDigit,
        'booktitle' => $faker->optional($weight=0.9)->sentence($faker->numberBetween(3, 10)),
        'publisher' => $faker->optional($weight=0.9)->name,
        'address' => $faker->optional($weight=0.9)->address,
        'type' => $faker->randomElement(['book', 'article', 'web', 'misc']),
        'howpublished' => $faker->optional($weight=0.9)->sentence($faker->numberBetween(3,10)),
        'annote' => $faker->optional($weight=0.1)->sentence($faker->numberBetween(3,5)),
        'chapter' => $faker->optional()->randomNumber($faker->numberBetween(1,4)),
        'crossref' => $faker->optional($weight=0.9)->word,
        'edition' => $faker->optional($weight=0.9)->word,
        'institution' => $faker->optional($weight=0.9)->company,
        'key' => $faker->optional($weight=0.9)->word,
        'month' => $faker->optional($weight=0.9)->date('M'),
        'note' => $faker->optional($weight=0.9)->sentence($faker->numberBetween(3, 10)),
        'organization' => $faker->optional($weight=0.9)->company,
        'school' => $faker->optional($weight=0.9)->company . ' ' . $faker->optional($weight=0.9)->companySuffix,
        'series' => $faker->optional($weight=0.9)->word,
        'citekey' => $faker->unique()->randomNumber
    ];
});

$factory->define(App\Geodata::class, function(Faker $faker) {
    $lat = $faker->latitude($min = 47, $max = 56);
    $lng = $faker->longitude($min = 6, $max = 15);

    switch ($faker->randomElement(['point', 'polygon', 'linestring'])) {
    case 'point':
        $geom = new Point($lat, $lng);
        break;
    case 'polygon':
        for($i = 0; $i < $faker->numberBetween($min = 3, $max = 6); $i++){
            $deltaLat = $faker->randomFloat($nbMaxDecimals = NULL, $min = 0, $max = 0.5);
            $deltaLng = $faker->randomFloat($nbMaxDecimals = NULL, $min = 0, $max = 0.5);
            $points[$i] = new Point($lat + $deltaLat, $lng + $deltaLng);
        }
        $points[] = $points[0]; // polygon must be closed
        $geom = new Polygon([new LineString($points)]);
        break;
    case 'linestring':
        for($i = 0; $i < $faker->numberBetween($min = 3, $max = 6); $i++){
            $deltaLat = $faker->randomFloat($nbMaxDecimals = NULL, $min = 0, $max = 0.5);
            $deltaLng = $faker->randomFloat($nbMaxDecimals = NULL, $min = 0, $max = 0.5);
            $points[$i] = new Point($lat + $deltaLat, $lng + $deltaLng);
        }
        $geom = new LineString($points);
        break;
    }

    return [
        'geom' => $geom,
        'color' => $faker->randomElement([$faker->hexcolor]),
    ];
});

$factory->define(App\Context::class, function(Faker $faker) {
    $geodata = factory(App\Geodata::class)->create();
    if($geodata->geom instanceof Point || $geodata->geom instanceof MultiPoint) {
        $geomtype = 'Point';
    } else if ($geodata->geom instanceof LineString || $geodata->geom instanceof MultiLineString){
        $geomtype = 'Linestring';
    } else if ($geodata->geom instanceof Polygon || $geodata->geom instanceof MultiPolygon) {
        $geomtype = 'Polygon';
    } else {
        $geomtype = '';
    }
    try {
        // first we need to retrieve a layer which matches the created geodata's type
        $validLayer = App\AvailableLayer::where('type', $geomtype)->inRandomOrder()->firstOrFail();
        // now get the associated contexttype, this should usually not fail since each contexttype seeds its own layer
        $contextType = App\ContextType::findOrFail($validLayer->context_type_id);
        // if it was successfull, we may link the created geodata object to this context
        $geodataId = $geodata->id;
    } catch (ModelNotFoundException $e) {
        // if we couldn't find a matching layer + contexttype, we may not link the geodata to this context
        $geodataId = NULL;
        // however the context needs to have a contexttype
        try {
            $contextType = App\ContextType::inRandomOrder()->first();
        } catch (ModelNotFoundException $e) {
            $contextType = factory(App\ContextType::class)->create();
        }

    }
    $rootContext = App\Context::inRandomOrder()->first();
    if(!isset($rootContext)){
        // if there is no entry in the Contexts table, do not recursively call this factory
        $rootContext = new App\Context();
    }
    $root_context_id = $faker->optional()->randomElement([$rootContext->id]);
    $rank = App\Context::where('root_context_id', $root_context_id)->max('rank') + 1;
    return [
        'context_type_id' => $contextType->id,
        'root_context_id' => $root_context_id,
        'name' => $faker->word,
        'geodata_id' => $geodataId,
        'rank' => $rank,
        'lasteditor' => $faker->name,
    ];
});

$factory->define(App\Source::class, function(Faker $faker) {
    $context = App\Context::inRandomOrder()->first();
    if(!isset($context)){
        $context = factory(App\Context::class)->create();
    }
    $attribute = App\Attribute::inRandomOrder()->first();
    if(!isset($attribute)){
        $attribute = factory(App\Attribute::class)->create();
    }
    $literature = App\Literature::inRandomOrder()->first();
    if(!isset($literature)){
        $literature = factory(App\Literature::class)->create();
    }
    return [
        'context_id' => $context->id,
        'attribute_id' => $attribute->id,
        'literature_id' => $literature->id,
        'description' => $faker->sentence($nbWords = 6, $variableNbWords = true),
        'lasteditor' => $faker->name,
    ];
});


$factory->define(App\AttributeValue::class, function(Faker $faker) {
    $context = App\Context::inRandomOrder()->first();
    if(!isset($context)){
        $context = factory(App\Context::class)->create();
    }
    $attribute = App\Attribute::inRandomOrder()->first();
    if(!isset($attribute)){
        $attribute = factory(App\Attribute::class)->create();
    }
    $val;

    switch($attribute->datatype) {
        case 'string':
        case 'stringf':
        case 'list':
            $dt = 'str_val';
            $val = $faker->sentence($nbWords = 6, $variableNbWords = true);
            break;
        case 'string-sc':
        case 'string-mc':
            $dt = 'thesaurus_val';
            $thesaurus_val = App\ThConcept::inRandomOrder()->first();
            if(!isset($thesaurus_val)){
                $thesaurus_val = factory(App\ThConcept::class)->create();
            }
            $val = $thesaurus_val->concept_url;
            break;
        case 'integer':
        case 'percentage':
            $dt = 'int_val';
            $val = $faker->randomDigit;
            break;
        case 'boolean':
            $dt = 'int_val';
            $val = $faker->randomElement([0,1]);
            break;
        case 'epoch':
        case 'timeperiod':
        case 'dimension':
            $dt = 'json_val';
            // TODO: json_val
            $val = json_encode('{"Error": "Not implemented"}');
            break;
        case 'date':
            $dt = 'dt_val';
            $val = $faker->dateTime();
            break;
        case 'context':
            $dt = 'context_val';
            $context_val = App\Context::inRandomOrder()->first();
            if(!isset($context_val)){
                $context_val = factory(App\Context::class)->create();
            }
            $val = $context_val->id;
            break;
        case 'double':
            $dt = 'dbl_val';
            $val = $faker->randomFloat($nbMaxDecimals = NULL, $min = 0, $max = 42);
            break;
        case 'geography':
            $dt = 'geography_val';
            $gd = factory(App\Geodata::class)->make();
            $val = $gd->geom;
            break;
        default:
            $dt = 'str_val';
            $val = 'Something strange happened in the RandomSeeder.';
    }

    return [
        'context_id' => $context->id,
        'attribute_id' => $attribute->id,
        'certainty' => $faker->numberBetween($min = 0, $max = 100),
        'lasteditor' => $faker->name,
        $dt => $val
    ];
});

//TODO seed photos?
$factory->define(App\File::class, function(Faker $faker) {
    return [
        'name' => '',
        'modified' => $faker->dateTime(),
        'cameraname' => '',
        'photographer_id' => $faker->randomDigit,
        'created' => $faker->dateTime(),
        'thumb' => '',
        'orientation' => $faker->randomElement([0,1]),
        'copyright' => '',
        'description' => $faker->sentence($nbWords = 6, $variableNbWords = true),
    ];
});
