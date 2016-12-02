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
        if($m->character->level == 110 && $m->rank == 3) {
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
        $mythicIds = [2,5,8,11,20,23,26,27,28];

        $member->totalMythicRuns = 0;

        for ($i = 0; $i < count($mythicIds); $i++) {
            $member->totalMythicRuns += $member->statistics->statistics->subCategories[5]->subCategories[6]->statistics[$mythicIds[$i]]->quantity;
        }
    }

    //dd($members[1]->statistics->statistics->subCategories[5]->subCategories[6]->statistics);


    return view('lucky', ['members' => $members]);
});
