<?php

namespace Facebook;

class Consumer
{
    const API_ENDPOINT = 'https://graph.facebook.com';

    private $app_key, $app_secret, $client, $access_token;

    public function __construct($app_key, $app_secret, $client)
    {
        $this->app_key = $app_key;
        $this->app_secret = $app_secret;
        $this->client = $client;
    }

    public function setAccessToken($token)
    {
        $this->access_token = $token;
    }

    public function getUser($identifier = 'me', array $fields = array())
    {
        $fields = array_merge($fields, array('access_token' => $this->access_token));
        $url = sprintf('%s/%s?%s', self::API_ENDPOINT, $identifier, http_build_query($fields));

        $json_string = $this->client->get($url);

    }
}