<?php

namespace Facebook\Test\Object;

use Facebook\Consumer;
use Facebook\Object\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    public function testArrayAccess()
    {
        $user = new User();

        $this->assertNull($user['id']);

        $user['id'] = 1;

        $this->assertEquals(1, $user['id'], 'User id do not match');
        $this->assertTrue(isset($user['id']), 'User id not set');

        unset($user['id']);

        $this->assertNull($user['id']);
    }

    public function testToArrayFromArray()
    {
        $user = new User();

        $input = array('id' => 'some id');

        $user->fromArray($input);

        $this->assertTrue(isset($user['id']), 'User id not set');

        $this->assertEquals($input, $user->toArray());
    }

    public function testToString()
    {
        $user = new User();
        $user->fromArray(array('first_name' => 'Doctor', 'last_name' => 'Who'));

        $this->assertEquals('Doctor Who', $user);
    }
}