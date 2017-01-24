<?php

use Illuminate\Database\Seeder;

class RegionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('regions')->insert([
            'id' => 1,
            'name' => 'Europe',
            'short' => 'eu'
        ]);

        DB::table('regions')->insert([
            'id' => 2,
            'name' => 'United States',
            'short' => 'us'
        ]);
    }
}
