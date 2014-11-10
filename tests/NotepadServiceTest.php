<?php

namespace Notepads\Tests\PHPUnit;

use Notepads\Models\Notepad;
use Notepads\Models\NotepadService;
use Notepads\Models\User;
use Notepads\Models\NotepadMapper;
use Notepads\Models\SessionMapper;
use \Mockery as m;

/**
 * Class NotepadServiceTest
 * @package Notepads\Tests
 */
class NotepadServiceTest extends BaseTestClass
{
    private $service;
    private $mapper;
    private $session;

    public function setup()
    {
        $this->mapper = m::mock("NotepadMapper");
        $this->session = m::mock("SessionMapper");
        $this->service = new NotepadService($this->mapper, $this->session);
    }

    public function testGetNotepadsReturnsArrayOfValidNotepadObjects()
    {
        $user = new User(array("id" => 1));
        $this->session->shouldReceive("getUser")
            ->andReturn($user);
        $this->mapper->shouldReceive("fetchAll")
            ->with($user)
            ->andReturn(array(
                new Notepad(array(
                    "id" => 1,
                    "userid" => $user->id,
                    "name" => "test name 1",
                    "text" => "test text 1"
                )),
                new Notepad(array(
                    "id" => 2,
                    "userid" => $user->id,
                    "name" => "test name 2",
                    "text" => "test text 2"
                ))
        ));
        $notepads = $this->service->getNotepads();
        foreach ($notepads as $notepad) {
            $this->assertInstanceOf("Notepads\\Models\\Notepad", $notepad);
            $this->assertTrue($notepad->valid());
        }
    }

    public function testFindOneReturnsTheSameOrSimilarNotepadObject()
    {
        $notepad = new Notepad(array(
            "id" => 2,
            "userid" => 1,
        ));
        $this->mapper->shouldReceive("findOne")
            ->with($notepad) //the same notepad object is passed
            ->andReturn(new Notepad(array(
                "id" => $notepad->id,
                "userid" => $notepad->userid,
                "name" => "test name 1",
                "text" => "test text 1",
            )));
        $found = $this->service->findOne($notepad);
        $this->assertInstanceOf("Notepads\\Models\\Notepad", $found);
        $this->assertTrue($found->id == $notepad->id);
    }

    public function testFindOneReturnsAnErrorOnNotEnoughParams()
    {
        $this->assertFalse($this->service->findOne(new Notepad(array())));
        $errors = $this->service->getErrors();
        $this->assertEquals("Not enough data!", $errors[0]);
    }

    public function testSaveNotepadWithGoodData()
    {
        $notepad = new Notepad(array(
            "id" => 1,
            "userid" => 1,
            "name" => "test name 1",
            "text" => "test text 1"
        ));
        $this->mapper->shouldReceive("save")
            ->with($notepad)
            ->andReturn(true);
        $this->assertTrue($this->service->save($notepad));
        $this->assertEmpty($this->service->getErrors());
    }

    public function testSaveNotepadWithBadData()
    {
        //for insert but no userid is given
        $notepad = new Notepad(array(
            "name" => "test name 1",
            "text" => "test text 1",
        ));
        $this->assertFalse($this->service->save($notepad));
        $errors = $this->service->getErrors();
        $this->assertEquals("Invalid Notepad params!", $errors[0]);
        //for update but no userid is given
        $notepad = new Notepad(array(
            "id" => 1,
            "name" => "test name 1",
            "text" => "test text 1",
        ));
        $this->assertFalse($this->service->save($notepad));
        $errors = $this->service->getErrors();
        $this->assertEquals("Invalid Notepad params!", $errors[1]);
    }

    public function testSanitizeAGoodText()
    {
        $goodText = "test text 1";
        $notepad = new Notepad(array(
            "userid" => 1,
            "name" => "test name 1",
            "text" => $goodText
        ));
        $notepadSanitized = null;
        $notepadMock = m::on(function ($notepadparam) use (&$notepadSanitized) {
            $notepadSanitized = $notepadparam;
            return true;
        });
        $this->mapper
            ->shouldReceive("save")
                ->with($notepadMock) //with the sanitized text
                ->andReturn(true);
        $this->assertTrue($this->service->save($notepad));
        $this->assertEmpty($this->service->getErrors());
        $this->assertInstanceOf("Notepads\\Models\\Notepad", $notepadSanitized);
        $this->assertEquals($goodText, $notepadSanitized->text);
    }

    public function testSanitizeABadText()
    {
        $badText = "  <span><strong>test</strong> text 1</span>  2 3 ";
        $notepad = new Notepad(array(
            "userid" => 1,
            "name" => "test name 1",
            "text" => $badText
        ));
        $notepadSanitized = null;
        $notepadMock = m::on(function ($notepadparam) use (&$notepadSanitized) {
            $notepadSanitized = $notepadparam;
            return true;
        });
        $this->mapper
            ->shouldReceive("save")
                ->with($notepadMock) //with the sanitized text
                ->andReturn(true);
        $this->assertTrue($this->service->save($notepad));
        $this->assertEmpty($this->service->getErrors());
        $this->assertInstanceOf("Notepads\\Models\\Notepad", $notepadSanitized);
        $this->assertNotEquals($badText, $notepadSanitized->text);
    }

    public function testDeleteNotepadWithGoodData()
    {
        $notepadId = 1;
        $notepadMock = m::on(function ($notepadparam) use ($notepadId) {
            return $notepadId === $notepadparam->id;
        });
        $this->mapper->shouldReceive("delete")
            ->with($notepadMock)
            ->andReturn(true);
        $this->assertTrue($this->service->delete($notepadId));
        $this->assertEmpty($this->service->getErrors());
    }

    public function testDeleteNotepadWithBadData()
    {
        $notepadId = -1;
        $this->assertFalse($this->service->delete($notepadId));
        $this->assertNotEmpty($this->service->getErrors());
        $errors = $this->service->getErrors();
        $this->assertEquals("Invalid id '$notepadId' given!", $errors[0]);
    }
}
