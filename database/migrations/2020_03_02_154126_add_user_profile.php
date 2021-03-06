<?php

use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class AddUserProfile extends Migration
{
    private $withLasteditor = [
        'attribute_values',
        'bibliography',
        'entities',
        'entity_files',
        'files',
        'geodata',
        'references',
        'th_concept',
        'th_concept_label',
        'th_language',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('avatar')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->softDeletes();
        });

        foreach($this->withLasteditor as $le) {
            $entries = $this->getElements($le);

            Schema::table($le, function (Blueprint $table) {
                $table->dropColumn('lasteditor');
                $table->integer('user_id')->nullable();
            });

            foreach($entries as $e) {
                try {
                    $user = User::where('name', $e->lasteditor)->firstOrFail();
                } catch(ModelNotFoundException $exc) {
                    $user = User::orderBy('id')->first();
                }
                $e->user_id = $user->id;
                $e->save();
            }

            Schema::table($le, function (Blueprint $table) {
                $table->integer('user_id')->nullable(false)->change();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });

        }

        Storage::makeDirectory('avatars');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar');
            $table->dropColumn('metadata');
            $table->dropSoftDeletes();
        });

        foreach($this->withLasteditor as $le) {
            $entries = $this->getElements($le);

            Schema::table($le, function (Blueprint $table) {
                $table->dropColumn('user_id');
                $table->text('lasteditor')->nullable();
            });

            foreach($entries as $e) {
                try {
                    $user = User::findOrFail($e->user_id);
                } catch(ModelNotFoundException $e) {
                    $user = User::orderBy('id')->first();
                }
                $e->lasteditor = $user->name;
                $e->save();
            }

            Schema::table($le, function (Blueprint $table) {
                $table->text('lasteditor')->nullable(false)->change();
            });
        }

        Storage::deleteDirectory('avatars');
    }

    private function getElements($tableName) {
        switch($tableName) {
            case 'attribute_values':
                return \App\AttributeValue::all();
            case 'bibliography':
                return \App\Bibliography::all();
            case 'entities':
                return \App\Entity::all();
            case 'entity_files':
                return \App\EntityFile::all();
            case 'files':
                return \App\File::all();
            case 'geodata':
                return \App\Geodata::all();
            case 'references':
                return \App\Reference::all();
            case 'th_concept':
                return \App\ThConcept::all();
            case 'th_concept_label':
                return \App\ThConceptLabel::all();
            case 'th_language':
                return \App\ThLanguage::all();
        }
    }
}
