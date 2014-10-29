<?php

namespace Notepads\Tests\PHPUnit;

use Notepads\Models\User;

/**
 * Class UserTests
 * @package Notepads\Tests
 */
class UserTests extends BaseTestClass
{
    public function testUserConstructorSetsTheCorrectData()
    {
        $userId = 1;
        $username = "testuser1";
        $password = "testpassword1";
        $arr = array(
            "id" => $userId,
            "username" => $username,
            "password" => $password
        );
        $user = new User($arr);
        $this->assertEquals($userId, $user->id);
        $this->assertEquals($username, $user->username);
        $this->assertEquals($password, $user->password);
        $this->assertEquals($arr, $user->toArray());
    }

    public function testUserIsValidWithValidParams()
    {
        $userId = 1;
        $username = "testuser1";
        $password = "testpassword1";
        $arr = array(
            "id" => $userId,
            "username" => $username,
            "password" => $password
        );
        $user = new User($arr);
        $this->assertTrue($user->valid());
        //
        $username = "testuser1";
        $password = "testpassword1";
        $arr = array(
            "username" => $username,
            "password" => $password
        );
        $user = new User($arr);
        $this->assertTrue($user->valid());
    }

    public function testUserIsNotValidWithInvalidParams()
    {
        $userId = -1;
        $this->setExpectedException("\\InvalidArgumentException", "Invalid id '$userId' given!");
        new User(array(
            "id" => $userId,
            "username" => "testusername1",
            "password" => "testpassword1"
        ));
        //
        $username = '1ho';
        $this->setExpectedException("\\InvalidArgumentException", "Invalid username '$username' given!");
        new User(array(
            "id" => -1,
            "username" => $username,
            "password" => "1"
        ));
        //
        $password = 'v';
        $this->setExpectedException("\\InvalidArgumentException", "Invalid password '$password' given!");
        new User(array(
            "id" => -1,
            "username" => "testusername1",
            "password" => "1"
        ));
    }

    public function testSettingBadUserNameThrowsException()
    {
        $username = "1testuser";
        $user = new User();
        $this->setExpectedException("InvalidArgumentException", "Invalid username '$username' given!");
        $user->username = $username;
    }

    public function testSettingBadPasswordThrowsException()
    {
        $password = "pw";
        $user = new User();
        $this->setExpectedException("InvalidArgumentException", "Invalid password '$password' given!");
        $user->password = $password;
    }

    public function testSettingInvalidPropertyName()
    {
        $user = new User();
        $badpropName = "bad";
        $this->setExpectedException("InvalidArgumentException", "Invalid property name '$badpropName'!");
        $user->$badpropName = "test";
    }

    public function testGettingInvalidPropertyName()
    {
        $user = new User();
        $badpropName = "bad";
        $this->setExpectedException("InvalidArgumentException", "Invalid property name '$badpropName'!");
        $user->$badpropName;//access the var
    }
}
