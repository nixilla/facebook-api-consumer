<?php

namespace Facebook\Test;

use Facebook\Consumer;
use Facebook\Object\User;

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

    public function testGetUserOK()
    {
        $client = $this->getClient();
        $response = $this->getResponse(200);
        $access_token = 'sdjfnskdjnfskdjnf';

        $client
            ->expects($this->once())
            ->method('get')
            ->with(sprintf('%s/me?access_token=%s', Consumer::API_ENDPOINT, $access_token))
            ->will($this->returnValue($response));

        $consumer = new Consumer($client, 'key', 'secret');
        $consumer->setAccessToken($access_token);

        $user = $consumer->getUser();

        $this->assertTrue($user instanceof User, 'variable $user is not instance of User');
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
            ->method('get')
            ->with(sprintf('%s/me?access_token=%s', Consumer::API_ENDPOINT, $access_token))
            ->will($this->returnValue($response));

        $consumer = new Consumer($client, 'key', 'secret');
        $consumer->setAccessToken($access_token);

        $user = $consumer->getUser();
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
            ->method('get')
            ->with(sprintf('%s/me?access_token=%s', Consumer::API_ENDPOINT, $access_token))
            ->will($this->returnValue($response));

        $consumer = new Consumer($client, 'key', 'secret');
        $consumer->setAccessToken($access_token);

        $user = $consumer->getUser();
    }

    /**
     * @expectedException \Exception
     */
    public function testGetUserException3()
    {
        $client = $this->getClient();
        $response = $this->getResponse(400, 'UnknownException');
        $access_token = 'sdjfnskdjnfskdjnf';

        $client
            ->expects($this->once())
            ->method('get')
            ->with(sprintf('%s/me?access_token=%s', Consumer::API_ENDPOINT, $access_token))
            ->will($this->returnValue($response));

        $consumer = new Consumer($client, 'key', 'secret');
        $consumer->setAccessToken($access_token);

        $user = $consumer->getUser();
    }

    public function testGetUserNull()
    {
        $client = $this->getClient();
        $response = $this->getResponse(400, 'UnknownException', array('with wrong structure'));
        $access_token = 'sdjfnskdjnfskdjnf';

        $client
            ->expects($this->once())
            ->method('get')
            ->with(sprintf('%s/me?access_token=%s', Consumer::API_ENDPOINT, $access_token))
            ->will($this->returnValue($response));

        $consumer = new Consumer($client, 'key', 'secret');
        $consumer->setAccessToken($access_token);

        $user = $consumer->getUser();

        $this->assertNull($user);
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
            ->method('get')
            ->with(sprintf('%s/me?access_token=%s', Consumer::API_ENDPOINT, $access_token))
            ->will($this->returnValue($response));

        $consumer = new Consumer($client, 'key', 'secret');
        $consumer->setAccessToken($access_token);

        $user = $consumer->getUser();
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
            ->method('get')
            ->with(sprintf('%s/me?access_token=%s', Consumer::API_ENDPOINT, $access_token))
            ->will($this->returnValue($response));

        $consumer = new Consumer($client, 'key', 'secret');
        $consumer->setAccessToken($access_token);

        $user = $consumer->getUser();
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

        if($code != 200)
        {
            $response
                ->expects($this->once())
                ->method('isClientError')
                ->will($this->returnValue($code == 400));


            if ($code >= 500 && $code < 600)
            {
                $response
                    ->expects($this->once())
                    ->method('isServerError')
                    ->will($this->returnValue(true));
            }
        }

        if( ! $content) $content = array('error' => array('type' => $exception, 'code' => 2500, 'message' => 'Test exception message'));

        $error = $code == 400 ? $content : array();

        if($code < 600)
        {
            $response
                ->expects($this->once())
                ->method('getContent')
                ->will($this->returnValue(json_encode($code == 200 ? array('id'=>'123321') : $error)));
        }

        return $response;
    }
}