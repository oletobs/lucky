<?php

use Illuminate\Database\Seeder;

class WOWCLassesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('w_o_w_classes')->insert([
            'id' => 1,
            'name' => 'Warrior',
        ]);

        DB::table('w_o_w_classes')->insert([
            'id' => 2,
            'name' => 'Paladin',
        ]);

        DB::table('w_o_w_classes')->insert([
            'id' => 3,
            'name' => 'Hunter',
        ]);

        DB::table('w_o_w_classes')->insert([
            'id' => 4,
            'name' => 'Rogue',
        ]);

        DB::table('w_o_w_classes')->insert([
            'id' => 5,
            'name' => 'Priest',
        ]);

        DB::table('w_o_w_classes')->insert([
            'id' => 6,
            'name' => 'Death Knight',
        ]);

        DB::table('w_o_w_classes')->insert([
            'id' => 7,
            'name' => 'Shaman',
        ]);

        DB::table('w_o_w_classes')->insert([
            'id' => 8,
            'name' => 'Mage',
        ]);

        DB::table('w_o_w_classes')->insert([
            'id' => 9,
            'name' => 'Warlock',
        ]);

        DB::table('w_o_w_classes')->insert([
            'id' => 10,
            'name' => 'Monk',
        ]);

        DB::table('w_o_w_classes')->insert([
            'id' => 11,
            'name' => 'Druid',
        ]);

        DB::table('w_o_w_classes')->insert([
            'id' => 12,
            'name' => 'Demon Hunter',
        ]);
    }
}
