<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;

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

    public function setClient($client) {
        $this->client = $client;
    }

    public function get($url, array $queries)
    {
        $queries['apikey'] = $this->apiKey;

        return $this->client->get($url, ['query' => $queries]);
    }

    public function getAsync($url, array $queries)
    {
        $queries['apikey'] = $this->apiKey;

        return $this->client->getAsync($url, ['query' => $queries]);
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

        $queries = ['fields' => 'statistics,items,achievements,talents'];

        return $this->get($url, $queries);
    }

    public function getCharacterAsync($region, $server, $name)
    {
        $fields = ['statistics','items','achievements','talents'];

        $promises = $this->getCharacterPromises($region, $server, $name);

        $data = [];

        $results = Promise\unwrap($promises);
        for($i = 0; $i < count($fields); $i++) {
            $body = json_decode($results[$i]->getBody(),true);
            $data[] = $body;
        }

        return $data;
    }

    public function getCharacterPromises($region, $server, $name)
    {
        $url = sprintf(self::BASE_URL, $region, 'character', $server, $name);

        $fields = ['statistics','items','achievements','talents'];

        $promises = [];
        foreach ($fields as $field) {
            $queries['fields'] = $field;
            $promises[$field] = $this->getAsync($url, $queries);
        }

        return $promises;
    }
}