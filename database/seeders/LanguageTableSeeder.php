<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LanguageTableSeeder extends Seeder
{
    public function run() {
        $user = \App\User::orderBy('id')->first();
        $de = new App\ThLanguage;
        $de->user_id = $user->id;
        $de->display_name   = 'Deutsch';
        $de->short_name     = 'de';
        $de->save();
        $en = new App\ThLanguage;
        $en->user_id = $user->id;
        $en->display_name  = 'English';
        $en->short_name    = 'en';
        $en->save();
        $es = new App\ThLanguage;
        $es->user_id = $user->id;
        $es->display_name  = 'Español';
        $es->short_name    = 'es';
        $es->save();
        $fr = new App\ThLanguage;
        $fr->user_id = $user->id;
        $fr->display_name  = 'Français';
        $fr->short_name    = 'fr';
        $fr->save();
        $it = new App\ThLanguage;
        $it->user_id = $user->id;
        $it->display_name  = 'Italiano';
        $it->short_name    = 'it';
        $it->save();
    }
}
