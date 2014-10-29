<?php

namespace Notepads\Tests\PHPUnit;

use Mockery\Matcher\Not;
use Notepads\Models\Notepad;

/**
 * Class NotepadTest
 * @package Notepads\Tests
 */
class NotepadTest extends BaseTestClass
{
    public function testNotepadConstructorSetsTheCorrectData()
    {
        $notepadId = 1;
        $userId = 1;
        $name = "test notepad 1";
        $text = "test text 1";
        $arr = array(
            "id" => $notepadId,
            "userid" => $userId,
            "name" => $name,
            "text" => $text
        );
        $notepad = new Notepad($arr);
        $this->assertEquals($notepadId, $notepad->id);
        $this->assertEquals($name, $notepad->name);
        $this->assertEquals($text, $notepad->text);
        $this->assertEquals($arr, $notepad->toArray());
    }
    public function testNotepadIsValidWithValidParams()
    {
        $notepadId = 1;
        $userid = 1;
        $name = "test name";
        $text = "test text";
        $arr = array(
            "id" => $notepadId,
            "userid" => $userid,
            "name" => $name,
            "text" => $text
        );
        $notepad = new Notepad($arr);
        $this->assertTrue($notepad->valid());
        //
        unset($arr['id']);
        $notepad = new Notepad($arr);
        $this->assertTrue($notepad->valid());
    }

    public function testNotepadIsNotValidWithInvalidParams()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Invalid id \'-1\' given!');
        new Notepad(array(
            "id" => -1,
            "userid" => 1,
            "name" => "test name",
            "text" => "test text"
        ));
        //
        $this->setExpectedException('InvalidArgumentException', 'Invalid userid \'-1\' given!');
        new Notepad(array(
            "id" => 1,
            "userid" => -1,
            "name" => "test name",
            "text" => "test text"
        ));
        //
        $notepad = new Notepad(array(
            "id" => 1,
            "userid" => 1,
        ));
        $this->assertFalse($notepad->valid());
        //
        $notepad = new Notepad(array(
            "id" => 1,
            "userid" => 1,
            "name" => "test name"
        ));
        $this->assertFalse($notepad->valid());
        //
        $notepad = new Notepad(array(
            "id" => 1,
            "userid" => 1,
            "text" => "test text"
        ));
        $this->assertFalse($notepad->valid());
    }

    public function testSettingInvalidPropertyName()
    {
        $notepad = new Notepad();
        $badpropName = "bad";
        $this->setExpectedException("InvalidArgumentException", "Invalid property name '$badpropName'!");
        $notepad->$badpropName = "test";//try to set the var
    }

    public function testGettingInvalidPropertyName()
    {
        $notepad = new Notepad();
        $badpropName = "bad";
        $this->setExpectedException("InvalidArgumentException", "Invalid property name '$badpropName'!");
        $notepad->$badpropName;//try to access the var
    }
}
