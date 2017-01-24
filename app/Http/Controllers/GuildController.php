<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Guild;
use App\Character;
use GuzzleHttp\Exception\ClientException;


class GuildController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Guild::with('server.region')->withCount('members')->paginate(15);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:30|min:2',
            'region' => 'required|string|max:30|min:2',
            'server' => 'required|string|max:30|min:2'
        ]);

        $name = $request->input('name');
        $server = $request->input('server');
        $region = $request->input('region');

        $guild = \DB::table('guilds')
            ->join('servers', 'guilds.server_id', '=', 'servers.id')
            ->join('regions', 'servers.region_id', '=', 'regions.id')
            ->select('servers.id as server_id', 'regions.id as region_id')
            ->where('servers.name', $server)
            ->where('guilds.name', $name)
            ->where('regions.short', $region)
            ->first();

        if(!$guild) {
            $api = app('blizzapi');

            \Debugbar::startMeasure('blizz','Querying blizz');
            try {
                $body  = json_decode($api->getGuild($region,$server,$name)->getBody(), true);
            } catch (ClientException $e) {
                return response()->json(['error' => 'Blizzard API Error @ Blizz'], $e->getResponse()->getStatusCode());
            } catch (Exception $e) {
                return response()->json(['error' => 'Blizzard API Error @ Kork'], 500);
            }
            \Debugbar::stopMeasure('blizz');

            $guild = Guild::create([
                'name' => $body['name'],
                'slug' => str_slug($body['name'], '-'),
                'last_modified' => date('Y-m-d H:i:s', $body['lastModified']/1000),
                'server_id' => \DB::table('servers')
                    ->join('regions', 'servers.region_id', '=', 'regions.id')
                    ->where('regions.short', $region)
                    ->where('servers.name', $body['realm'])
                    ->value('servers.id'),
            ]);

            $members = [];

            foreach ($body['members'] as $member) {
                if($member['character']['level'] == 110) {
                    $members[] = [
                        'name' => $member['character']['name'],
                        'w_o_w_class_id' => $member['character']['class'],
                        'specc' => (array_key_exists('spec',$member['character'])) ? $member['character']['spec']['name'] : 'Undefined',
                        'guild_id' => $guild->id,
                        'guild_rank' => $member['rank'],
                        'created_at' => Carbon::now()
                    ];
                }
            }

            Character::insert($members);

            $guild->load('members.wowClass');

            return $guild;
        }

        return response()->json(['Error' => 'Guild is already cached.'], 500);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($region,$server,$guild)
    {
        $g = Guild::whereHas('server', function ($query) use ($server, $region) {
                $query->whereHas('region', function ($query) use ($region) {
                    $query->where('short', $region);
                })->where('name', $server);
            })
            ->where('slug', $guild)
            ->with('members.wowClass')
            ->first();

        if(!$g) {
            return response()->json(['error' => 'Guild not cached.'], 404);
        }

        return $g;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:30|min:2',
            'region' => 'required|string|max:30|min:2',
            'server' => 'required|string|max:30|min:2'
        ]);

        $name = $request->input('name');
        $server = $request->input('server');
        $region = $request->input('region');

        $g = Guild::whereHas('server', function ($query) use ($server, $region) {
            $query->whereHas('region', function ($query) use ($region) {
                $query->where('short', $region);
            })->where('name', $server);
        })
            ->where('name', $name)
            ->withCount('members')
            ->first();

        if($g) {

            if(Carbon::now()->diffInSeconds($g->updated_at) < 120) {
                return response()->json(['message' => 'Enhance your calm. This guild was recently updated.'], 420);
            } else {
                $g->touch();

                $api = app('blizzapi');

                \Debugbar::startMeasure('blizz','Querying blizz');
                try {
                    $body  = json_decode($api->getGuild($region,$server,$name)->getBody(), true);
                } catch (ClientException $e) {
                    return response()->json(['error' => 'Blizzard API Error @ Blizz'], $e->getResponse()->getStatusCode());
                } catch (Exception $e) {
                    return response()->json(['error' => 'Blizzard API error @ Kork'], 500);
                }
                \Debugbar::stopMeasure('blizz');

                $members = collect($body['members']);

                $oldMembers = Character::where('guild_id', $g->id)->select('name')->get();

                $newMembers = $members->filter(function ($newMember) use ($oldMembers) {
                    if($newMember['character']['level'] == 110) {
                        return !$oldMembers->contains(function ($oldMember) use ($newMember) {
                            return $oldMember->name == $newMember['character']['name'];
                        });
                    }
                    return false;
                });

                foreach ($newMembers as $member) {
                    $g->members()->create([
                        'name' => $member['character']['name'],
                        'w_o_w_class_id' => $member['character']['class'],
                        'specc' => (array_key_exists('spec',$member['character'])) ? $member['character']['spec']['name'] : 'Undefined',
                        'guild_rank' => $member['rank'],
                    ]);
                }

                $g->members_count += $newMembers->count();

                return $g;
            }
        }

        return response()->json(['Error' => 'Guild is not cached and cannot be updated.'], 500);
    }
}
