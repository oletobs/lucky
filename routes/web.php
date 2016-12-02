<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/update/stats', function () {
    $guild = Cache::remember('guild', 10000, function () {
        $wow = App::make('wow');

        $response = $wow->getGuild('Darksorrow', 'Fat Balls Final Release', [
            'fields' => 'members',
        ]);

        return json_decode($response->getBody());
    });

    foreach ($guild->members as $m) {
        if($m->character->level == 110 && ($m->rank == 0 || $m->rank == 1 || $m->rank == 2 || $m->rank == 3 || $m->rank == 4 || $m->character->name == 'Soursuzy')) {
            $wow = App::make('wow');

            $response = $wow->getCharacter('Darksorrow', $m->character->name, [
                'fields' => 'statistics',
            ]);

            Cache::put($m->character->name . '_stats', json_decode($response->getBody())->statistics, 10000);
        }
    }
});

Route::get('/update/achievements', function () {
    $guild = Cache::remember('guild', 10000, function () {
        $wow = App::make('wow');

        $response = $wow->getGuild('Darksorrow', 'Fat Balls Final Release', [
            'fields' => 'members',
        ]);

        return json_decode($response->getBody());
    });

    foreach ($guild->members as $m) {
        if($m->character->level == 110 && ($m->rank == 0 || $m->rank == 1 || $m->rank == 2 || $m->rank == 3 || $m->rank == 4 || $m->character->name == 'Soursuzy')) {
            $wow = App::make('wow');

            $response = $wow->getCharacter('Darksorrow', $m->character->name, [
                'fields' => 'achievements',
            ]);

            Cache::put($m->character->name . '_achies', json_decode($response->getBody())->achievements, 10000);
        }
    }
});


Route::get('/update/items', function () {
    $guild = Cache::remember('guild', 10000, function () {
        $wow = App::make('wow');

        $response = $wow->getGuild('Darksorrow', 'Fat Balls Final Release', [
            'fields' => 'members',
        ]);

        return json_decode($response->getBody());
    });

    foreach ($guild->members as $m) {
        if($m->character->level == 110 && ($m->rank == 0 || $m->rank == 1 || $m->rank == 2 || $m->rank == 3 || $m->rank == 4 || $m->character->name == 'Soursuzy')) {
            $wow = App::make('wow');

            $response = $wow->getCharacter('Darksorrow', $m->character->name, [
                'fields' => 'items',
            ]);

            Cache::put($m->character->name . '_items', json_decode($response->getBody())->items, 10000);
        }
    }
});

Route::get('/', function () {
    ini_set('max_execution_time', 300);
    $guild = Cache::remember('guild', 10000, function () {
        $wow = App::make('wow');

        $response = $wow->getGuild('Darksorrow', 'Fat Balls Final Release', [
            'fields' => 'members',
        ]);

        return json_decode($response->getBody());
    });

    $members = array_filter($guild->members, function($m) {
        if($m->character->level == 110 && ($m->rank == 0 || $m->rank == 1 || $m->rank == 2 || $m->rank == 3 || $m->rank == 4 || $m->character->name == 'Soursuzy')) {
            $m->statistics = Cache::remember($m->character->name . '_stats', 10000, function() use ($m) {
                $wow = App::make('wow');

                $response = $wow->getCharacter('Darksorrow', $m->character->name, [
                    'fields' => 'statistics',
                ]);

                return json_decode($response->getBody())->statistics;
            });

            $m->items = Cache::get($m->character->name . '_items');
            $m->achievements = Cache::get($m->character->name . '_achies');
            return true;
        }
    });

    foreach ($members as $member) {
        $mythicDungeonIds = [2,5,8,11,20,23,26,27,28,29];
        $lfrIds = [30,34,38,42,46,50,54,58,62,66];
        $normalIds = [31,35,39,43,47,51,55,59,63,67];
        $heroicIds = [32,36,40,44,48,52,56,60,64,68];
        $mythicIds = [33,37,41,45,49,53,57,61,65,69];

        $member->totalMythicRuns = 0;
        $member->totalLFR = 0;
        $member->totalNormal = 0;
        $member->totalHeroic = 0;
        $member->totalMythic = 0;


        for ($i = 0; $i < count($mythicDungeonIds); $i++) {
            $member->totalMythicRuns += $member->statistics->subCategories[5]->subCategories[6]->statistics[$mythicDungeonIds[$i]]->quantity;
        }

        for ($i = 0; $i < count($lfrIds); $i++) {
            $member->totalLFR += $member->statistics->subCategories[5]->subCategories[6]->statistics[$lfrIds[$i]]->quantity;
        }

        for ($i = 0; $i < count($normalIds); $i++) {
            $member->totalNormal += $member->statistics->subCategories[5]->subCategories[6]->statistics[$normalIds[$i]]->quantity;
        }

        for ($i = 0; $i < count($heroicIds); $i++) {
            $member->totalHeroic += $member->statistics->subCategories[5]->subCategories[6]->statistics[$heroicIds[$i]]->quantity;
        }

        for ($i = 0; $i < count($mythicIds); $i++) {
            $member->totalMythic += $member->statistics->subCategories[5]->subCategories[6]->statistics[$mythicIds[$i]]->quantity;
        }

        $member->totalRaid = $member->totalLFR + $member->totalNormal + $member->totalHeroic + $member->totalMythic;

        $keyAP = array_search('30103', $member->achievements->criteria);
        $keyArtLvl = array_search('29395', $member->achievements->criteria);
        $keyWQ = array_search('33094', $member->achievements->criteria);

        $member->totalAP = $member->achievements->criteriaQuantity[$keyAP];
        $member->maxArtLvl = $member->achievements->criteriaQuantity[$keyArtLvl];
        $member->totalWQ = $member->achievements->criteriaQuantity[$keyWQ];
    }

    //dd($members[1]->character->spec->name);

    $class = [
        1 => 'Warrior',
        2 => 'Paladin',
        3 => 'Hunter',
        4 => 'Rogue',
        5 => 'Priest',
        6 => 'Death Knight',
        7 => 'Shaman',
        8 => 'Mage',
        9 => 'Warlock',
        10 => 'Monk',
        11 => 'Druid',
        12 => 'Demon Hunter'
    ];

    return view('lucky', ['members' => $members, 'class' => $class]);
});
