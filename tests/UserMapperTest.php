<?php

namespace Notepads\Tests;

use Notepads\Models\User;
use Notepads\Models\UserMapper;
use Notepads\Tests\PHPUnit\BaseTestClass;
use \Mockery as m;

class UserMapperTest extends BaseTestClass
{
    private $database;
    private $mapper;

    public function setup()
    {
        $this->database = m::mock("\\PDO");
        $this->mapper = new UserMapper($this->database);
    }

    public function testFindOneById()
    {
        $userId = 1;
        $arr = array(
            "id" => $userId,
            "username" => "testusername1",
            "password" => "testpassword1"
        );
        $user = new User($arr);
        $stmtMock = m::mock("\\PDOStatement");
        $where = "1 AND `id` = :id";
        $query = "SELECT * FROM `users` WHERE $where";
        $this->database->shouldReceive("prepare")
            ->with($query)
            ->andReturn($stmtMock);
        $stmtMock
            ->shouldReceive("bindParam")
                ->with(":id", $userId)
            ->shouldReceive("execute")
                ->andReturn(true)
            ->shouldReceive("fetchObject")
                ->with("Notepads\\Models\\User")
            ->andReturn($user);
        $this->assertEquals(
            $user,
            $this->mapper->findOne(
                new User(array(
                    "id" => $userId
                ))
            )
        );
    }

    public function testFindOneByUsername()
    {
        $username = "testusername1";
        $arr = array(
            "id" => 1,
            "username" => $username,
            "password" => "testpassword1"
        );
        $user = new User($arr);
        $stmtMock = m::mock("\\PDOStatemetnt");
        $where = "1 AND `username` = :username";
        $query = "SELECT * FROM `users` WHERE $where";
        $this->database->shouldReceive("prepare")
            ->with($query)
            ->andReturn($stmtMock);
        $stmtMock
            ->shouldReceive("bindParam")
                ->with(":username", $username)
            ->shouldReceive("execute")
                ->andReturn(true)
            ->shouldReceive("fetchObject")
                ->with("Notepads\\Models\\User")
            ->andReturn($user);
        $this->assertEquals(
            $user,
            $this->mapper->findOne(
                new User(array(
                    "username" => $username
                ))
            )
        );
    }

    public function testFindOneByPassword()
    {
        $password = "testusername1";
        $arr = array(
            "id" => 1,
            "username" => "testusername1",
            "password" => $password
        );
        $user = new User($arr);
        $stmtMock = m::mock("\\PDOStatement");
        $where = "1 AND `password` = :password";
        $query = "SELECT * FROM `users` WHERE $where";
        $this->database->shouldReceive("prepare")
            ->with($query)
            ->andReturn($stmtMock);
        $stmtMock
            ->shouldReceive("bindParam")
                ->with(":password", $password)
            ->shouldReceive("execute")
                ->andReturn(true)
            ->shouldReceive("fetchObject")
                ->with("Notepads\\Models\\User")
            ->andReturn($user);
        $this->assertEquals(
            $user,
            $this->mapper->findOne(
                new User(array(
                    "password" => $password
                ))
            )
        );
    }

    public function testfindOneNotFound()
    {
        $user = new User(array("id" => 1));
        $where = "1 AND `id` = :id";
        $query = "SELECT * FROM `users` WHERE $where";
        $stmtMock = m::mock("\\PDOStatement");
        $this->database->shouldReceive("prepare")
            ->with($query)
            ->andReturn($stmtMock);
        $stmtMock
            ->shouldReceive("bindParam")
                ->with(":id", $user->id)
            ->shouldReceive("execute")
                ->andReturn(false);
        $this->assertFalse($this->mapper->findOne($user));
    }

    public function testSaveNewUser()
    {
        $user = new User(array(
            "username" => "testusername1",
            "password" => "testpassword1"
        ));
        $stmtMock = m::mock("\\PDOStatement");
        $this->database->shouldReceive("prepare")
            ->with(
                "INSERT INTO `users` (`username`, `password`)
                  VALUES(:username, :password)"
            )
            ->andReturn($stmtMock);
        $stmtMock
            ->shouldReceive("bindParam")
                ->with(":username", $user->username)
            ->shouldReceive("bindParam")
                ->with(":password", $user->password)
            ->shouldReceive("execute")
                ->andReturn(true);
        $this->assertTrue($this->mapper->save($user));
    }

    public function testSaveUpdateUser()
    {
        $user = new User(array(
            "id" => 1,
            "name" => "testusername1",
            "text" => "testpassword1"
        ));
        $stmtMock = m::mock("\\PDOStatement");
        $this->database->shouldReceive("prepare")
            ->with(
                "UPDATE `users` SET `username` = :username,
                  `password` = :password
                  WHERE `id` = :id"
            )
            ->andReturn($stmtMock);
        $stmtMock
            ->shouldReceive("bindParam")
                ->with(":id", $user->id)
            ->shouldReceive("bindParam")
                ->with(":username", $user->username)
            ->shouldReceive("bindParam")
                ->with(":password", $user->password)
            ->shouldReceive("execute")
                ->andReturn(true);
        $this->assertTrue($this->mapper->save($user));
    }

    public function testDeleteUser()
    {
        $user = new User(array("id" => 1));
        $stmtMock = m::mock("\\PDOStatement");
        $this->database->shouldReceive("prepare")
            ->with("DELETE FROM `users` WHERE `id` = :id")
            ->andReturn($stmtMock);
        $stmtMock
            ->shouldReceive("bindParam")
                ->with(":id", $user->id)
            ->shouldReceive("execute")
                ->andReturn(true);
        $this->assertTrue($this->mapper->delete($user));
    }
}
