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
        if($m->character->level == 110) {
            return true;
        }
    });

    //dd($members);

    return view('lucky', ['members' => $members]);
});
