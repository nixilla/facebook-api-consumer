<?php

namespace Facebook\Test;

use Facebook\Consumer;

class ConsumerTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $client = $this->getClient();

        $consumer = new Consumer($client, 'key', 'secret');

        $this->assertAttributeEquals($client, 'client', $consumer);
        $this->assertAttributeEquals('key', 'app_key', $consumer);
        $this->assertAttributeEquals('secret', 'app_secret', $consumer);
    }

    public function testSetAccessToken()
    {
        $client = $this->getClient();
        $consumer = new Consumer($client, 'key', 'secret');
        $consumer->setAccessToken('sdjfnskdjnfskdjnf');

        $this->assertAttributeEquals('sdjfnskdjnfskdjnf', 'access_token', $consumer);
    }

    private function getClient()
    {
        return $this->getMock('Buzz\Browser', array('call', 'get'));
    }
}