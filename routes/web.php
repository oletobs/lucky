<?php

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise;
use App\Guild;
use App\Character;
use App\Server;
use App\Region;

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

Route::get('/', function() {
    $regions = Region::select('name','short','id')->with(['servers' => function ($query) {
        $query->select('name','region_id');
    }])->get();

    return view('home.index', ['regions' => $regions]);
});

Route::get('guilds', 'GuildController@index')->name('guilds.index');

Route::get('guilds/{region}/{server}/{guild}', 'GuildController@show')->name('guilds.show');

Route::post('guilds', 'GuildController@store')->name('guilds.store');

Route::put('guilds', 'GuildController@update')->name('guilds.update');

Route::put('char', 'CharacterController@update')->name('characters.update');
