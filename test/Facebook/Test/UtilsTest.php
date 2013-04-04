<?php

namespace Utils\Test;

use Facebook\Utils;

class UtilsTest extends \PHPUnit_Framework_TestCase
{

    public function testBase64UrlEncodeAndDecode()
    {
        // &copy; http://www.deloreanipsum.com/
        $values = array(
            'Does your mom know about tomorrow night?',
            'Right.',
            'Lou, gimme a milk, chocolate. Lorraine, my density has popped me to you.',
            'Now, Biff, um, can I assume that your insurance is gonna pay for the damage?',
            'Why thank you, Marty.',
            'George.',
            'Good morning, sleepyhead, Good morning, Dave, Lynda C\'mon, c\'mon.',
            'Okay.',
            'Don\'t say a word.',
            'I got enough practical jokes for one evening.',
            'Good night, future boy.',
            'Excuse me.',
            'Something wrong with the starter, so I hid it.'
        );

        foreach($values as $input)
        {
            $coded = Utils::base64UrlEncode($input);

            $this->assertEquals(urlencode($coded), $coded, 'String is not urlencoded');
            $this->assertEquals($input, Utils::base64UrlDecode($coded), 'Input has changes, method is not working');
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMakeSignedRequestException()
    {
        Utils::makeSignedRequest('','secret');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testParseSignedRequestInvalidArgumentException1()
    {
        Utils::parseSignedRequest('invalid format','secret');
    }

    /**
     * @expectedException Facebook\Exception\InvalidEncryptionAlgorithmException
     */
    public function testParseSignedRequestInvalidArgumentException2()
    {
        Utils::parseSignedRequest('invalid.format','secret');
    }

    /**
     * @expectedException Facebook\Exception\InvalidEncryptionSignatureException
     */
    public function testSignedRequestException()
    {
        $input = array(
            'user_id' => '1234',
            'algorithm' => 'HMAC-SHA256',
            'issued_at' => time()
        );

        $sr = Utils::makeSignedRequest($input,'secret');

        $this->assertEquals($input, Utils::parseSignedRequest($sr, 'wrong'));
    }

    public function testSignedRequest()
    {
        $input = array(
            'user_id' => '1234',
            'algorithm' => 'HMAC-SHA256',
            'issued_at' => time()
        );

        $sr = Utils::makeSignedRequest($input,'secret');

        $this->assertEquals($input, Utils::parseSignedRequest($sr, 'secret'));
    }
}