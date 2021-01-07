<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FixSequencesSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        // Auto-incrementing IDs/PKEYs sequences are not properly updated
        // when rows inserted with ID/PKEY.
        // This query sets the sequence to MAX + 1 for each table/sequence
        \DB::statement("
            DO $$
            DECLARE
            rec RECORD;
            LAST_ID integer;
            BEGIN
            FOR rec IN SELECT
            table_name
            FROM information_schema.columns WHERE table_schema='public' and column_name='id' and data_type='integer'
            LOOP
            execute 'SELECT (id + 1) as id FROM public.' || rec.table_name || ' ORDER BY id DESC LIMIT 1' into LAST_ID;
            execute 'ALTER SEQUENCE public.'|| rec.table_name||'_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 START 1 RESTART '||COALESCE(LAST_ID, 1)||' CACHE 1 NO CYCLE;';
            END LOOP;
            END; $$
        ");
    }
}
