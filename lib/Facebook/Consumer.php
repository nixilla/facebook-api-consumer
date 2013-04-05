<?php

namespace Facebook;

use Buzz\Message\RequestInterface;
use Facebook\Object\User;
use Facebook\Exception\GraphMethodException;
use Facebook\Exception\OAuthException;
use Buzz\Message\Response;

class Consumer
{
    const API_ENDPOINT = 'https://graph.facebook.com';

    private $client, $access_token;
    private $converters = array();

    public function __construct($client)
    {
        $this->client = $client;
    }

    public function setAccessToken($token)
    {
        $this->access_token = $token;
    }

    public function call($api_method, $http_method = RequestInterface::METHOD_GET, array $query_string = array(), $headers = array(), $content = '')
    {
        if($this->access_token) $query_string = array_merge($query_string, array('access_token' => $this->access_token));

        if(preg_match('/^\//',$api_method)) $api_method = substr($api_method, 1);

        $url = sprintf('%s/%s?%s', self::API_ENDPOINT, $api_method, http_build_query($query_string));

        $response = $this->client->call($url, $http_method, $headers, $content);

        if($response->isSuccessful())
        {
            $converter = $this->getConverter($api_method);
            return $converter->convert($response->getContent());
        }
        else return $this->handleException($response);
    }

    protected function handleException(Response $response)
    {
        $json_string = $response->getContent();

        $output = json_decode($json_string, true);

        if (
            isset($output['error']['message']) &&
            isset($output['error']['type']) &&
            isset($output['error']['code'])
        ) {
            $clazz = sprintf('Facebook\Exception\%s', $output['error']['type']);

            if (class_exists($clazz))
                throw new $clazz($output['error']['message'], $output['error']['code']);
            else
                throw new \Exception($output['error']['message'], $output['error']['code']);
        } else throw new \Exception(sprintf('Unknown error: %s', $json_string));
    }

    public function getConverter($api_method)
    {
        if(isset($this->converters[$api_method]))
            return $this->converters[$api_method];
        else
            return new DefaultConverter();
    }

    public function setConverter($api_method, $converter)
    {
        if( ! $converter instanceof Converter)
            throw new \InvalidArgumentException('Second argument must implement Converter interface');

        $this->converters[$api_method] = $converter;
    }
}