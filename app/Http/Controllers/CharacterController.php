<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Character;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;

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

            //\Debugbar::startMeasure('blizzAndWlogsAsync','Querying blizz and wlogs async');
            $client = new Client();
            $bapi = app('blizzapi');
            $wlapi = app('wowlogsapi');
            $bapi->setClient($client);
            $wlapi->setClient($client);

            $charPromises = $bapi->getCharacterPromises($char->guild->server->region->short,$char->guild->server->name,$char->name);
            $ranksPromises = $wlapi->getRankingsPromises($char->guild->server->region->short,$char->guild->server->name,$char->name);

            $charData = [];
            $ranksData = [];
            try {
                $results1 = Promise\unwrap($charPromises);
                $results2 = Promise\unwrap($ranksPromises);
                foreach($charPromises as $key => $value) {
                    $charData[$key] = json_decode($results1[$key]->getBody(),true);
                }

                foreach($ranksPromises as $key => $value) {
                    $ranksData[$key] = json_decode($results2[$key]->getBody(),true);
                }
            } catch (ClientException $e) {
                return response()->json(['error' => 'External API Error @ WowLogs or Blizz'], $e->getResponse()->getStatusCode());
            } catch (Exception $e) {
                return response()->json(['error' => 'External API Error @ Kork'], 500);
            }

            //\Debugbar::stopMeasure('blizzAndWlogsAsync');


            // Battle.net API
            /*$api = app('blizzapi');

            \Debugbar::startMeasure('blizz','Querying blizz');
            try {
                $body  = json_decode($api->getCharacter($char->guild->server->region->short,$char->guild->server->name,$char->name)->getBody(), true);
            } catch (ClientException $e) {
                return response()->json(['error' => 'Blizzard API Error @ Blizz'], $e->getResponse()->getStatusCode());
            } catch (\Exception $e) {
                return response()->json(['error' => 'Blizzard API Error @ Kork'], 500);
            }
            \Debugbar::stopMeasure('blizz');

            // Battle.net API async
            $api = app('blizzapi');
            \Debugbar::startMeasure('blizzasync','Querying blizz async');
            try {
                $body  = $api->getCharacterAsync($char->guild->server->region->short,$char->guild->server->name,$char->name);
            } catch (ClientException $e) {
                return response()->json(['error' => 'Blizzard API Error @ Blizz'], $e->getResponse()->getStatusCode());
            } catch (\Exception $e) {
                return response()->json(['error' => 'Blizzard API Error @ Kork'], 500);
            }
            \Debugbar::stopMeasure('blizzasync');

            // WarcraftLogs API
            $wlapi = app('wowlogsapi');
            \Debugbar::startMeasure('wowlogs','Querying wowlogs');
            try {
                $wlbodies = $wlapi->getRankingsAsync(
                    $char->guild->server->region->short,
                    $char->guild->server->name,
                    $char->name
                );
            } catch (ClientException $e) {
                return response()->json(['error' => 'WarcraftLogs API Error @ WowLogs'], $e->getResponse()->getStatusCode());
            } catch (Exception $e) {
                return response()->json(['error' => 'WarcraftLogs API Error @ Kork'], 500);
            }
            \Debugbar::stopMeasure('wowlogs');*/

            // Scraping relevant data from all the results
            //\Debugbar::startMeasure('scraping','Scraping results');
            $stats = app('blizzscraper')->scrape($charData);
            $rankStats = app('wowlogscraper')->scrape($ranksData);
            $stats['ranks'] = $rankStats;

            $char->stats = $stats;

            foreach ($charData['talents']['talents'] as $specc) {
                if(array_key_exists('selected', $specc)) {
                    foreach ($specc['talents'] as $talent) {
                        if(array_key_exists('spec', $talent)) {
                            $char->specc = $talent['spec']['name'];
                        }
                    }
                }
            }
            //\Debugbar::stopMeasure('scraping');

            $char->save();

            return $char->makeHidden('guild');
        }

        return response()->json(['message' => 'Character not found.'], 404);
    }
}
