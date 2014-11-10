<?php

namespace Notepads\Tests\PHPUnit;

use Notepads\Models\UserService;
use Notepads\Models\User;
use Notepads\Models\UserMapper;
use Notepads\Models\SessionMapper;
use \Mockery as m;

/**
 * Class UserServiceTest
 * @package Notepads\Tests
 */
class UserServiceTest extends BaseTestClass
{
    private $service;
    private $mapper;
    private $session;
    private $username;
    private $password;
    private $user;
    private $userMock;

    public function setup()
    {
        $this->mapper = m::mock("UserMapper");
        $this->session = m::mock("SessionMapper");
        $this->service = new UserService($this->mapper, $this->session);
        //
        $this->username = "testusername1";
        $this->password = "testpassword1";
        $arr = array(
            "username" => $this->username,
            "password" => $this->password
        );
        $user = new User($arr);
        $this->userMock = m::on(function ($userparam) use ($user) {
            //if the test $user is given as param
            if ($userparam instanceof User
                && $userparam->username == $user->username
                && $userparam->password == $user->password
            ) {
                return true;
            }
            return false;
        });
        $this->user = $user;
    }

    public function testGetUsersReturnsArrayOfValidUserObjects()
    {
        $this->mapper->shouldReceive("fetchAll")->andReturn(array(
            new User(array(
                "id" => 1,
                "username" => "testuser1",
                "password" => "testpassword1"
            )),
            new User(array(
                "id" => 2,
                "username" => "testuser2",
                "password" => "testpassword2"
            ))
        ));
        $users = $this->service->getUsers();
        foreach ($users as $user) {
            $this->assertInstanceOf("Notepads\\Models\\User", $user);
            $this->assertTrue($user->valid());
        }
    }

    public function testNonExistingUserCanBeRegisteredWithValidData()
    {
        $this->mapper->shouldReceive("findOne")
            ->with($this->userMock)
            ->andReturn(false);
        $this->mapper->shouldReceive("save")
            ->with($this->userMock)
            ->andReturn(true);
        $this->assertTrue($this->service->register($this->username, $this->password, $this->password));
        $this->assertEmpty($this->service->getErrors());
    }

    public function testRegisterWithWrongDataAddsErrors()
    {
        $this->mapper->shouldReceive("findOne")
            ->with($this->userMock)
            ->andReturn($this->user);
        //wrong params:
        $this->assertFalse($this->service->register($this->username, $this->password, $this->password . "::diff"));
        $errors = $this->service->getErrors();
        $this->assertEquals("Passwords do not match!", $errors[0]);
        //
        $this->assertFalse($this->service->register("123 " . $this->username, $this->password, $this->password));
        $errors = $this->service->getErrors();
        $this->assertEquals("Invalid username or password!", $errors[1]);
        //
        $this->assertFalse($this->service->register($this->username, "as", "as"));
        $errors = $this->service->getErrors();
        $this->assertEquals("Invalid username or password!", $errors[2]);
        //user exists:
        $this->assertFalse($this->service->register($this->username, $this->password, $this->password));
        $errors = $this->service->getErrors();
        $this->assertEquals("User already exists!", $errors[3]);
    }

    public function testAuthErrors()
    {
        $username = "1asf";
        $this->assertFalse($this->service->authenticate($username, "testpassword1"));
        $errors = $this->service->getErrors();
        $this->assertEquals("Invalid username '$username' given!", $errors[0]);
        //
        $this->mapper->shouldReceive("findOne")
            ->with($this->userMock)
            ->andReturn(false);
        $this->assertFalse($this->service->authenticate("", $this->password));
        $errors = $this->service->getErrors();
        $this->assertEquals("No username or password given!", $errors[1]);
        $this->assertFalse($this->service->authenticate($this->username, ""));
        $errors = $this->service->getErrors();
        $this->assertEquals("No username or password given!", $errors[2]);
        $this->assertFalse($this->service->authenticate($this->username, $this->password));
        $errors = $this->service->getErrors();
        $this->assertEquals("Wrong user!", $errors[3]);
    }

    public function testAuthWithGoodParams()
    {
        $this->mapper->shouldReceive("findOne")
            ->with($this->userMock)
            ->andReturn($this->user);
        $this->session->shouldReceive("storeUser")
            ->with($this->user)
            ->andReturn(true);
        $this->assertTrue($this->service->authenticate($this->username, $this->password));
        $this->assertEmpty($this->service->getErrors());
    }

    public function testCheckUserLoggedIn()
    {
        $this->session->shouldReceive("getUser")
            ->andReturn($this->user);
        $this->mapper->shouldReceive("findOne")
            ->with($this->user)
            ->andReturn($this->user);
        $this->assertTrue($this->service->userIsLoggedIn());
    }

    public function testCheckUserNOTLoggedIn()
    {
        $this->session->shouldReceive("getUser")
            ->andReturn(false);
        $this->assertFalse($this->service->userIsLoggedIn());
    }

    public function testCheckUserNOTLoggedInAndNotExists()
    {
        $this->session->shouldReceive("getUser")
            ->andReturn($this->user);
        $this->mapper->shouldReceive("findOne")
            ->with($this->user)
            ->andReturn(false);
        $this->assertFalse($this->service->userIsLoggedIn());
    }

    public function testGetCurrentUserRetursValidUser()
    {
        $this->session->shouldReceive("getUser")
            ->andReturn($this->user);
        $user = $this->service->getCurrentUser();
        $this->assertInstanceOf("Notepads\\Models\\User", $user);
        $this->assertTrue($user->valid());
    }

    public function testLogoutUser()
    {
        $this->session->shouldReceive("remove")
            ->with("user");
        $this->assertNull($this->service->logoutUser());
    }

    public function testUpdateUserWithNotEnoughParams()
    {
        $this->assertFalse($this->service->updateUser($this->username, $this->password, $this->password . "::diff"));
        $this->assertFalse($this->service->updateUser(null, $this->password, $this->password));
        $this->assertFalse($this->service->updateUser($this->username, null, $this->password));
        $this->assertFalse($this->service->updateUser($this->username, $this->password, null));
    }

    public function testUpdateUserNewUserNameExists()
    {
        $user = new User(array(
            "id" => 1,
            "username" => "username1",
            "password" => "password1",
        ));
        $this->session->shouldReceive("getUser")
            ->andReturn($user);
        $newusername = "newusername";
        $newUser = new User(array(
            "id" => 2,
            "username" => $newusername,
            "password" => "password2",
        ));
        $newUserMock = m::on(function ($userparam) use ($newusername) {
            return $userparam instanceof User && $userparam->username == $newusername;
        });
        $this->mapper->shouldReceive("findOne")
            ->with($newUserMock)
            ->andReturn($newUser);
        $this->assertFalse($this->service->updateUser($newusername, $user->password, $user->password));
        $errors = $this->service->getErrors();
        $this->assertEquals("User with that name exists!", $errors[0]);
    }

    public function testUpdateUserSuccessfullyWithGoodParams()
    {
        $user = new User(array(
            "id" => 1,
            "username" => "username1",
            "password" => "password1",
        ));
        $userMock = m::on(function ($userparam) use ($user) {
            return $userparam instanceof User
                    && $userparam->id == $user->id
                    && $userparam->username == $user->username
                    && $userparam->password == $user->password;
        });
        $newUsername = "username2";
        $newUserMock = m::on(function ($userparam) use ($newUsername) {
            return $userparam instanceof User && $userparam->username === $newUsername;
        });
        $this->mapper->shouldReceive("findOne")
                ->with($newUserMock)
                ->andReturn($user)
            ->shouldReceive("save")
                ->with($newUserMock)
                ->andReturn(true);
        $this->session->shouldReceive("getUser")
                ->andReturn($user)
            ->shouldReceive("storeUser")
                ->with($userMock)
                ->andReturn(true);
        $this->assertTrue($this->service->updateUser($newUsername, $user->password, $user->password));
        $this->assertEmpty($this->service->getErrors());
    }

    public function testUpdateUserNoNeedToUpdate()
    {
        $user = new User(array(
            "id" => 1,
            "username" => "username1",
            "password" => "password1",
        ));
        $this->session->shouldReceive("getUser")
            ->andReturn($user);
        $this->assertTrue($this->service->updateUser($user->username, $user->password, $user->password));
    }
}
