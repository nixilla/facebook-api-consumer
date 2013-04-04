<?php

namespace Facebook;

use Facebook\Object\User;
use Facebook\Exception\GraphMethodException;
use Facebook\Exception\OAuthException;
use Buzz\Message\Response;

class Consumer
{
    const API_ENDPOINT = 'https://graph.facebook.com';

    private $app_key, $app_secret, $client, $access_token;

    public function __construct($client, $app_key, $app_secret)
    {
        $this->app_key = $app_key;
        $this->app_secret = $app_secret;
        $this->client = $client;
    }

    public function setAccessToken($token)
    {
        $this->access_token = $token;
    }

    public function getUser($identifier = 'me', array $query_string = array())
    {
        if($this->access_token)
            $query_string = array_merge($query_string, array('access_token' => $this->access_token));

        $url = sprintf('%s/%s?%s', self::API_ENDPOINT, $identifier, http_build_query($query_string));

        $response = $this->client->get($url);

        if($response->isSuccessful())
        {
            $user = new User();
            $user->fromArray(json_decode($response->getContent(), true));
            return $user;
        }
        else return $this->handleException($response);
    }

    protected function handleException(Response $response)
    {
        if($response->isClientError())
        {
            $output = json_decode($response->getContent(), true);

            if(
                isset($output['error']['message']) &&
                isset($output['error']['type']) &&
                isset($output['error']['code'])
            )
            {
                $clazz = sprintf('Facebook\Exception\%s', $output['error']['type']);

                if(class_exists($clazz))
                    throw new $clazz($output['error']['message'], $output['error']['code']);
                else
                    throw new \Exception($output['error']['message'], $output['error']['code']);
            }
            else return null;

        }
        else if($response->isServerError())
        {
           throw new \Exception(sprintf('Facebook return 500: %s', $response->getContent()), 500);
        }

        throw new \Exception('Unknown error');
    }
}