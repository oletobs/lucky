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



Route::get('/', function () {
    $minutes = 5;

    $guild = Cache::remember('guild', $minutes, function () {
        $wow = App::make('wow');

        $response = $wow->getGuild('Darksorrow', 'Fat Balls Final Release', [
            'fields' => 'members',
        ]);

        return json_decode($response->getBody());
    });

    $members = array_filter($guild->members, function($m) {
        if($m->character->level == 110 && ($m->rank == 0 || $m->rank == 1 || $m->rank == 2 || $m->rank == 3 || $m->rank == 4)) {
            $m->statistics = Cache::remember($m->character->name, 100, function() use ($m) {
                $wow = App::make('wow');

                $response = $wow->getCharacter('Darksorrow', $m->character->name, [
                    'fields' => 'statistics',
                ]);

                return json_decode($response->getBody());
            });
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
            $member->totalMythicRuns += $member->statistics->statistics->subCategories[5]->subCategories[6]->statistics[$mythicDungeonIds[$i]]->quantity;
        }

        for ($i = 0; $i < count($lfrIds); $i++) {
            $member->totalLFR += $member->statistics->statistics->subCategories[5]->subCategories[6]->statistics[$lfrIds[$i]]->quantity;
        }

        for ($i = 0; $i < count($normalIds); $i++) {
            $member->totalNormal += $member->statistics->statistics->subCategories[5]->subCategories[6]->statistics[$normalIds[$i]]->quantity;
        }

        for ($i = 0; $i < count($heroicIds); $i++) {
            $member->totalHeroic += $member->statistics->statistics->subCategories[5]->subCategories[6]->statistics[$heroicIds[$i]]->quantity;
        }

        for ($i = 0; $i < count($mythicIds); $i++) {
            $member->totalMythic += $member->statistics->statistics->subCategories[5]->subCategories[6]->statistics[$mythicIds[$i]]->quantity;
        }
    }

    //dd($members[1]->statistics->statistics->subCategories[5]->subCategories[6]);


    return view('lucky', ['members' => $members]);
});
