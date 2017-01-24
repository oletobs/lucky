<?php

use Flynsarmy\CsvSeeder\CsvSeeder;

class USServersTableSeeder extends CsvSeeder {

    public function __construct()
    {
        $this->table = 'servers';
        $this->filename = base_path().'/database/seeds/csvs/us_servers_utf8.csv';
        $this->insert_chunk_size = 100;
    }

    public function run()
    {
        // Recommended when importing larger CSVs
        DB::disableQueryLog();

        // Uncomment the below to wipe the table clean before populating
        //DB::table($this->table)->truncate();

        parent::run();
    }
}