<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GeodataTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $user = \App\User::orderBy('id')->first();
        \DB::table('geodata')->insert(array (
            0 =>
            array (
                'id' => 2,
                'geom' => '0101000020E61000009BE7887C97D22140DA71C3EFA6454840',
                'created_at' => '2017-12-31 16:10:30',
                'updated_at' => '2017-12-31 16:10:30',
                'user_id' => $user->id,
                'color' => NULL,
            ),
            1 =>
            array (
                'id' => 3,
                'geom' => '0101000020E61000007CBABA63B1D52140F436363B52454840',
                'created_at' => '2017-12-31 16:10:34',
                'updated_at' => '2017-12-31 16:10:34',
                'user_id' => $user->id,
                'color' => NULL,
            ),
            2 =>
            array (
                'id' => 5,
                'geom' => '0103000020E61000000100000011000000ACE4637781D22140AB21718FA5454840A6272CF180D22140C37DE4D6A4454840ACE4637781D2214065A9F57EA3454840C3D8429083D22140728BF9B9A145484015FF774485D2214013B70A62A0454840A087DA368CD221408B4F01309E4548408D800A4790D22140BC07E8BE9C454840FB57569A94D22140C2F869DC9B45484007D2C5A695D2214004E8F7FD9B454840D9E9077591D221402D7B12D89C4548402FE065868DD221402C47C8409E454840EA03C93B87D2214055A69883A0454840D40FEA2285D2214042075DC2A145484040FA264D83D2214006A1BC8FA34548407C2C7DE882D2214094F947DFA445484045B75ED383D221404C1938A0A5454840ACE4637781D22140AB21718FA5454840',
                'created_at' => '2017-12-31 16:12:44',
                'updated_at' => '2017-12-31 16:12:44',
                'user_id' => $user->id,
                'color' => NULL,
            ),
            3 =>
            array (
                'id' => 6,
                'geom' => '0101000020E610000066F4A3E194D12140EC1681B1BE454840',
                'created_at' => '2019-03-18 09:46:11',
                'updated_at' => '2019-03-18 09:46:11',
                'user_id' => $user->id,
                'color' => NULL,
            ),
        ));
    }
}
