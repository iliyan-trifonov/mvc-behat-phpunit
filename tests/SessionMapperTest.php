<?php

namespace Notepads\Tests;

use Notepads\Models\SessionMapper;
use Notepads\Models\User;
use Notepads\Tests\PHPUnit\BaseTestClass;

class SessionMapperTest extends BaseTestClass
{
    /**
     * @runInSeparateProcess
     */
    public function testGetAndStoreUser()
    {
        $mapper = new SessionMapper();
        $user = new User(array(
            "id" => 1,
            "name" => "testusername1",
            "password" => "testpassword1",
        ));
        $this->assertInstanceOf("Notepads\\Models\\SessionMapper", $mapper->storeUser($user));
        $this->assertEquals($user, $mapper->getUser());
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetAndSet()
    {
        $mapper = new SessionMapper();
        $testvar = "testvar";
        $testvalue = "testvalue";
        $this->assertInstanceOf("Notepads\\Models\\SessionMapper", $mapper->set($testvar, $testvalue));
        $this->assertEquals($testvalue, $mapper->get($testvar));
    }

    /**
     * @runInSeparateProcess
     */
    public function testRemove()
    {
        $mapper = new SessionMapper();
        $testvar = "testvar";
        $this->assertInstanceOf("Notepads\\Models\\SessionMapper", $mapper->set($testvar, "something"));
        $this->assertInstanceOf("Notepads\\Models\\SessionMapper", $mapper->remove($testvar));
        $this->assertTrue(is_null($mapper->get($testvar)));
    }
}
