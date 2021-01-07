<?php

namespace Database\Seeders;

use App\User;
use App\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GuestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $guestName = 'Guest User';
        $guest = User::where('name', $guestName)->first();
        if($guest === null) {
            $guest = new User();
            $guest->name = $guestName;
            $guest->email = 'udontneedtoseehisidentification@rebels.tld';
            $guest->password = Hash::make('thesearentthedroidsuarelookingfor');
            $guest->save();
        }

        $guestRole = Role::where('name', '=', 'guest')->first();
        $guest->attachRole($guestRole);
    }
}
