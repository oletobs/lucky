<?php

namespace App\Services;

use GuzzleHttp\Client;

class BlizzardApiClient
{
    const BASE_URL = 'https://%s.api.battle.net/wow/%s/%s/%s';

    private $apiKey;

    private $client;

    public function __construct($apiKey, Client $client)
    {
        $this->apiKey = $apiKey;
        $this->client = $client;
    }

    public function get($url, array $queries)
    {
        $queries['apikey'] = $this->apiKey;

        return $this->client->get($url, ['query' => $queries]);
    }

    public function getGuild($region, $server, $name)
    {
        $url = sprintf(self::BASE_URL, $region, 'guild', $server, $name);

        $queries = ['fields' => 'members'];

        return $this->get($url, $queries);
    }

    public function getCharacter($region, $server, $name)
    {
        $url = sprintf(self::BASE_URL, $region, 'character', $server, $name);

        $queries = ['fields' => 'statistics,items,achievements'];

        return $this->get($url, $queries);
    }
}