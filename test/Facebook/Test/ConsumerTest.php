<?php

namespace Facebook\Test;

use Facebook\Consumer;
use Facebook\Object\User;

class ConsumerTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $client = $this->getClient();
        $consumer = new Consumer($client);
        $this->assertAttributeEquals($client, 'client', $consumer);
    }

    public function testSetAccessToken()
    {
        $client = $this->getClient();
        $consumer = new Consumer($client);
        $consumer->setAccessToken('sdjfnskdjnfskdjnf');
        $this->assertAttributeEquals('sdjfnskdjnfskdjnf', 'access_token', $consumer);
    }

    public function testGetUserOK()
    {
        $client = $this->getClient();
        $response = $this->getResponse(200);
        $access_token = 'sdjfnskdjnfskdjnf';

        $client
            ->expects($this->once())
            ->method('call')
            ->with(sprintf('%s/me?access_token=%s', Consumer::API_ENDPOINT, $access_token), 'GET')
            ->will($this->returnValue($response));

        $consumer = new Consumer($client);
        $consumer->setAccessToken($access_token);

        $user = $consumer->call('/me');
        
        $this->assertTrue(is_array($user), 'variable $user is not instance of array');
    }

    /**
     * @expectedException Facebook\Exception\OAuthException
     */
    public function testGetUserException1()
    {
        $client = $this->getClient();
        $response = $this->getResponse(400);
        $access_token = 'sdjfnskdjnfskdjnf';

        $client
            ->expects($this->once())
            ->method('call')
            ->with(sprintf('%s/me?access_token=%s', Consumer::API_ENDPOINT, $access_token), 'GET')
            ->will($this->returnValue($response));

        $consumer = new Consumer($client);
        $consumer->setAccessToken($access_token);

        $consumer->call('/me');
    }

    /**
     * @expectedException Facebook\Exception\GraphMethodException
     */
    public function testGetUserException2()
    {
        $client = $this->getClient();
        $response = $this->getResponse(400, 'GraphMethodException');
        $access_token = 'sdjfnskdjnfskdjnf';

        $client
            ->expects($this->once())
            ->method('call')
            ->with(sprintf('%s/me?access_token=%s', Consumer::API_ENDPOINT, $access_token), 'GET')
            ->will($this->returnValue($response));

        $consumer = new Consumer($client);
        $consumer->setAccessToken($access_token);

        $consumer->call('/me');
    }

    /**
     * @expectedException \Exception
     */
    public function testGetUserException3()
    {
        $client = $this->getClient();
        $response = $this->getResponse(500, 'UnknownException');
        $access_token = 'sdjfnskdjnfskdjnf';

        $client
            ->expects($this->once())
            ->method('call')
            ->with(sprintf('%s/me?access_token=%s', Consumer::API_ENDPOINT, $access_token), 'GET')
            ->will($this->returnValue($response));

        $consumer = new Consumer($client);
        $consumer->setAccessToken($access_token);

        $consumer->call('/me');
    }

    /**
     * @expectedException \Exception
     */
    public function testGetUserException4()
    {
        $client = $this->getClient();
        $response = $this->getResponse(500);
        $access_token = 'sdjfnskdjnfskdjnf';

        $client
            ->expects($this->once())
            ->method('call')
            ->with(sprintf('%s/me?access_token=%s', Consumer::API_ENDPOINT, $access_token), 'GET')
            ->will($this->returnValue($response));

        $consumer = new Consumer($client);
        $consumer->setAccessToken($access_token);

        $user = $consumer->call('/me');
    }

    /**
     * @expectedException \Exception
     */
    public function testGetUserException5()
    {
        $client = $this->getClient();
        $response = $this->getResponse(600);
        $access_token = 'sdjfnskdjnfskdjnf';

        $client
            ->expects($this->once())
            ->method('call')
            ->with(sprintf('%s/me?access_token=%s', Consumer::API_ENDPOINT, $access_token), 'GET')
            ->will($this->returnValue($response));

        $consumer = new Consumer($client);
        $consumer->setAccessToken($access_token);

        $consumer->call('/me');
    }

    /**
     * @expectedException \Exception
     */
    public function testGetUserNull()
    {
        $client = $this->getClient();
        $response = $this->getResponse(400, 'UnknownException', array('with wrong structure'));
        $access_token = 'sdjfnskdjnfskdjnf';

        $client
            ->expects($this->once())
            ->method('call')
            ->with(sprintf('%s/me?access_token=%s', Consumer::API_ENDPOINT, $access_token), 'GET')
            ->will($this->returnValue($response));

        $consumer = new Consumer($client);
        $consumer->setAccessToken($access_token);

        $user = $consumer->call('/me');

        $this->assertNull($user);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetConverterException()
    {
        $client = $this->getClient();
        $consumer = new Consumer($client);

        $consumer->setConverter('/me', new \stdClass());
    }

    public function testSetConverter()
    {
        $client = $this->getClient();
        $consumer = new Consumer($client);
        $converter = $this->getMock('Facebook\Converter', array('convert'));
        $consumer->setConverter('/me', $converter);
    }

    private function getClient()
    {
        return $this->getMock('Buzz\Browser', array('call', 'get'));
    }

    private function getResponse($code, $exception = 'OAuthException', $content = null)
    {
        $response = $this->getMock('Buzz\Message\Response', array('isSuccessful', 'isClientError', 'isServerError', 'getContent'));

        $response
            ->expects($this->once())
            ->method('isSuccessful')
            ->will($this->returnValue($code == 200));

        if( ! $content) $content = array('error' => array('type' => $exception, 'code' => 2500, 'message' => 'Test exception message'));

        $response
            ->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue(json_encode($code == 200 ? array('id'=>'123321') : $content)));

        return $response;
    }
}