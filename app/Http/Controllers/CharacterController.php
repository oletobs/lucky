<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Character;
use GuzzleHttp\Exception\ClientException;

class CharacterController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $char = Character::with('guild.server.region')->find($request->input('id'));

        if($char) {
            if($char->updated_at) {
                if(Carbon::now()->diffInSeconds($char->updated_at) < 120) {
                    //return response()->json(['message' => 'Enhance your calm. This character was recently updated.'], 420);
                    return $char->makeHidden('guild');
                }
            }

            // Battle.net API scraping
            $api = app('blizzapi');

            \Debugbar::startMeasure('blizz','Querying blizz');
            try {
                $body  = json_decode($api->getCharacter($char->guild->server->region->short,$char->guild->server->name,$char->name)->getBody(), true);
            } catch (ClientException $e) {
                return response()->json(['error' => 'Blizzard API Error @ Blizz'], $e->getResponse()->getStatusCode());
            } catch (\Exception $e) {
                return response()->json(['error' => 'Blizzard API Error @ Kork'], 500);
            }
            \Debugbar::stopMeasure('blizz');

            $stats = app('blizzscraper')->scrape($body);

            // WarcraftLogs API scraping

            /*
            $wlapi = app('wowlogsapi');

            \Debugbar::startMeasure('wowlogs','Querying wowlogs');
            try {
                $wlbody  = json_decode($wlapi->getRankings(
                    $char->guild->server->region->short,
                    $char->guild->server->name,
                    $char->name
                )->getBody(), true);
            } catch (ClientException $e) {
                return response()->json(['error' => 'WarcraftLogs API Error @ WowLogs'], $e->getResponse()->getStatusCode());
            } catch (Exception $e) {
                return response()->json(['error' => 'WarcraftLogs API Error @ Kork'], 500);
            }
            \Debugbar::stopMeasure('wowlogs');

            //$rankStats = app('wowlogscraper')->scrape($body);
            */

            $char->stats = $stats;



            foreach ($body['talents'] as $specc) {
                if(array_key_exists('selected', $specc)) {
                    foreach ($specc['talents'] as $talent) {
                        if(array_key_exists('spec', $talent)) {
                            $char->specc = $talent['spec']['name'];
                        }
                    }
                }
            }

            $char->save();

            return $char->makeHidden('guild');
        }

        return response()->json(['message' => 'Character not found.'], 404);
    }
}
