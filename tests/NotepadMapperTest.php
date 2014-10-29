<?php

namespace Notepads\Tests;

use Notepads\Models\Notepad;
use Notepads\Models\NotepadMapper;
use Notepads\Models\User;
use Notepads\Tests\PHPUnit\BaseTestClass;
use \Mockery as m;

class NotepadMapperTest extends BaseTestClass
{
    private $database;
    private $mapper;

    public function setup()
    {
        $this->database = m::mock("\\PDO");
        $this->mapper = new NotepadMapper($this->database);
    }

    public function testFindOneById()
    {
        $notepadId = 1;
        $arr = array(
            "id" => $notepadId,
            "userid" => 1,
            "name" => "test name 1",
            "text" => "test text 1"
        );
        $notepad = new Notepad($arr);
        $where = "1 AND `id` = :id";
        $stmtMock = m::mock("\\PDOStatement");
        $query = "SELECT * FROM `notepads` WHERE $where";
        $this->database->shouldReceive("prepare")
            ->with($query)
            ->andReturn($stmtMock);
        $stmtMock
            ->shouldReceive("bindParam")
                ->with(":id", $notepadId)
            ->shouldReceive("execute")
                ->andReturn(true)
            ->shouldReceive("fetchAll")
                ->with(\PDO::FETCH_ASSOC)
                ->andReturn(array(array(
                    "id" => $notepad->id,
                    "userid" => $notepad->userid,
                    "name" => $notepad->name,
                    "text" => $notepad->text
                )));
        $this->assertEquals(
            $notepad,
            $this->mapper->findOne(
                new Notepad(array(
                    "id" => $notepadId
                ))
            )
        );
    }

    public function testFindOneByUserId()
    {
        $userId = 1;
        $arr = array(
            "id" => 1,
            "userid" => $userId,
            "name" => "test name 1",
            "text" => "test text 1"
        );
        $notepad = new Notepad($arr);
        $where = "1 AND `userid` = :userid";
        $stmtMock = m::mock("\\PDOStatement");
        $query = "SELECT * FROM `notepads` WHERE $where";
        $this->database->shouldReceive("prepare")
            ->with($query)
            ->andReturn($stmtMock);
        $stmtMock
            ->shouldReceive("bindParam")
            ->with(":userid", $userId)
            ->shouldReceive("execute")
            ->andReturn(true)
            ->shouldReceive("fetchAll")
            ->with(\PDO::FETCH_ASSOC)
            ->andReturn(array(array(
                "id" => $notepad->id,
                "userid" => $notepad->userid,
                "name" => $notepad->name,
                "text" => $notepad->text
            )));
        $this->assertEquals(
            $notepad,
            $this->mapper->findOne(
                new Notepad(array(
                    "userid" => $userId
                ))
            )
        );
    }

    public function testFindOneByName()
    {
        $name = "test name 1";
        $arr = array(
            "id" => 1,
            "userid" => 1,
            "name" => $name,
            "text" => "test text 1"
        );
        $notepad = new Notepad($arr);
        $where = "1 AND `name` LIKE '%:name%'";
        $stmtMock = m::mock("\\PDOStatement");
        $query = "SELECT * FROM `notepads` WHERE $where";
        $this->database->shouldReceive("prepare")
            ->with($query)
            ->andReturn($stmtMock);
        $stmtMock
            ->shouldReceive("bindParam")
            ->with(":name", $name)
            ->shouldReceive("execute")
            ->andReturn(true)
            ->shouldReceive("fetchAll")
            ->with(\PDO::FETCH_ASSOC)
            ->andReturn(array(array(
                "id" => $notepad->id,
                "userid" => $notepad->userid,
                "name" => $notepad->name,
                "text" => $notepad->text
            )));
        $this->assertEquals(
            $notepad,
            $this->mapper->findOne(
                new Notepad(array(
                    "name" => $name
                ))
            )
        );
    }

    public function testFindOneByText()
    {
        $text = "test text 1";
        $arr = array(
            "id" => 1,
            "userid" => 1,
            "name" => "test name 1",
            "text" => $text
        );
        $notepad = new Notepad($arr);
        $where = "1 AND `text` LIKE '%:text%'";
        $stmtMock = m::mock("\\PDOStatement");
        $query = "SELECT * FROM `notepads` WHERE $where";
        $this->database->shouldReceive("prepare")
            ->with($query)
            ->andReturn($stmtMock);
        $stmtMock
            ->shouldReceive("bindParam")
            ->with(":text", $text)
            ->shouldReceive("execute")
            ->andReturn(true)
            ->shouldReceive("fetchAll")
            ->with(\PDO::FETCH_ASSOC)
            ->andReturn(array(array(
                "id" => $notepad->id,
                "userid" => $notepad->userid,
                "name" => $notepad->name,
                "text" => $notepad->text
            )));
        $this->assertEquals(
            $notepad,
            $this->mapper->findOne(
                new Notepad(array(
                    "text" => $text
                ))
            )
        );
    }

    public function testfindOneNotFound()
    {
        $notepad = new Notepad(array("id" => 1));
        $where = "1 AND `id` = :id";
        $query = "SELECT * FROM `notepads` WHERE $where";
        $stmtMock = m::mock("\\PDOStatement");
        $this->database->shouldReceive("prepare")
            ->with($query)
            ->andReturn($stmtMock);
        $stmtMock
            ->shouldReceive("bindParam")
                ->with(":id", $notepad->id)
            ->shouldReceive("execute")
                ->andReturn(false);
        $this->assertFalse($this->mapper->findOne($notepad));
    }

    public function testFetchAll()
    {
        $userId = 1;
        $user = new User(array("id" => $userId));
        $stmtMock = m::mock("\\PDOStatement");
        $arr = array(
            "id" => 1,
            "userid" => $userId,
            "name" => "test name 1",
            "text" => "test text 1"
        );
        $notepad = new Notepad($arr);
        $this->database->shouldReceive("prepare")
            ->with(
                "SELECT * FROM `notepads`
              WHERE `userid` = :userid
              ORDER BY `id` DESC"
            )
            ->andReturn($stmtMock);
        $stmtMock->shouldReceive("bindParam")
                ->with(":userid", $userId)
            ->shouldReceive("execute")
                ->andReturn(true)
            ->shouldReceive("fetchAll")
                ->with(\PDO::FETCH_ASSOC)
                ->andReturn(array($arr));
        $this->assertEquals(array($notepad), $this->mapper->fetchAll($user));
    }

    public function testSaveNewNotepad()
    {
        $notepad = new Notepad(array(
            "userid" => 1,
            "name" => "test name 1",
            "text" => "test text 1"
        ));
        $stmtMock = m::mock("PDOStatement");
        $this->database->shouldReceive("prepare")
            ->with(
                "INSERT INTO `notepads` (`userid`, `name`, `text`)
                  VALUES(:userid, :name, :text)"
            )
            ->andReturn($stmtMock);
        $stmtMock
            ->shouldReceive("bindParam")
                ->with(":userid", $notepad->userid)
            ->shouldReceive("bindParam")
                ->with(":name", $notepad->name)
            ->shouldReceive("bindParam")
                ->with(":text", $notepad->text)
            ->shouldReceive("execute")
                ->andReturn(true);
        $this->assertTrue($this->mapper->save($notepad));
    }

    public function testSaveUpdateNotepad()
    {
        $notepad = new Notepad(array(
            "id" => 1,
            "userid" => 1,
            "name" => "test name 1",
            "text" => "test text 1"
        ));
        $stmtMock = m::mock("PDOStatement");
        $this->database->shouldReceive("prepare")
            ->with(
                "UPDATE `notepads` SET `userid` = :userid,
                  `name` = :name,
                  `text` = :text
                  WHERE `id` = :id"
            )
            ->andReturn($stmtMock);
        $stmtMock
            ->shouldReceive("bindParam")
                ->with(":id", $notepad->id)
            ->shouldReceive("bindParam")
                ->with(":userid", $notepad->userid)
            ->shouldReceive("bindParam")
                ->with(":name", $notepad->name)
            ->shouldReceive("bindParam")
                ->with(":text", $notepad->text)
            ->shouldReceive("execute")
                ->andReturn(true);
        $this->assertTrue($this->mapper->save($notepad));
    }

    public function testDeleteNotepad()
    {
        $notepad = new Notepad(array("id" => 1));
        $stmtMock = m::mock("PDOStatement");
        $this->database->shouldReceive("prepare")
            ->with("DELETE FROM `notepads` WHERE `id` = :id")
            ->andReturn($stmtMock);
        $stmtMock
            ->shouldReceive("bindParam")
                ->with(":id", $notepad->id)
            ->shouldReceive("execute")
                ->andReturn(true);
        $this->assertTrue($this->mapper->delete($notepad));
    }
}
