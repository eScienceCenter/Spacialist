<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// Inverse of CreateDbFunctions (2017_01_11_141536_create-db-functions.php)
class DropDbViews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::getConnection()->statement('DROP VIEW getConceptLabelsFromID');
        Schema::getConnection()->statement('DROP VIEW getConceptLabelsFromUrl');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::getConnection()->statement('CREATE OR REPLACE VIEW getConceptLabelsFromUrl AS
            SELECT  lbl.label,
                    con.concept_url,
                    lng.short_name
            FROM th_concept_label lbl
                JOIN th_language lng ON lbl.language_id = lng.id
                JOIN th_concept con ON con.id = lbl.concept_id
            ORDER BY con.id, lbl.concept_label_type
        ');

        Schema::getConnection()->statement('CREATE OR REPLACE VIEW getConceptLabelsFromID AS
            SELECT  lbl.label,
                    lng.short_name,
                    lbl.concept_id
            FROM th_concept_label lbl
                    JOIN th_language lng ON lbl.language_id = lng.id
            ORDER BY lbl.concept_label_type
        ');
    }
}