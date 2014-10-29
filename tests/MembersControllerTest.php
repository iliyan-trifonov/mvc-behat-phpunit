<?php

namespace Notepads\Tests;

use Notepads\Tests\PHPUnit\BaseTestClass;
use Notepads\Controllers\MembersController;
use Notepads\Models\Notepad;
use Notepads\Models\User;
use \Mockery as m;

class MembersControllerTest extends BaseTestClass
{
    private $requestMock;
    private $serviceFactoryMock;
    private $userServiceMock;
    private $notepadServiceMock;
    private $viewMock;
    private $routerMock;

    public function setup()
    {
        $this->requestMock = m::mock("Request");
        $this->serviceFactoryMock = m::mock("ServiceFactory");
        $this->userServiceMock = m::mock("UserService");
        $this->notepadServiceMock = m::mock("NotepadService");
        $this->viewMock = m::mock("Template");
        $this->routerMock = m::mock("Router");

        $this->serviceFactoryMock->shouldReceive("getService")
            ->with("User")
            ->andReturn($this->userServiceMock);

        $this->viewMock->shouldReceive("setLayout")
            ->with("layout.phtml");
    }

    private function setUserIsLoggedIn()
    {
        $this->userServiceMock->shouldReceive("userIsLoggedIn")
            ->andReturn(true);
        $this->viewMock->shouldReceive("assign")
            ->with("loggedIn", true, true);
    }

    public function testConstructorUserIsLoggedIn()
    {
        $this->userServiceMock->shouldReceive("userIsLoggedIn")
            ->andReturn(false);
        $this->routerMock->shouldReceive("route")
            ->with("");
        $this->assertInstanceOf(
            "Notepads\\Controllers\\MembersController",
            new MembersController(
                $this->requestMock,
                $this->serviceFactoryMock,
                $this->viewMock,
                $this->routerMock
            )
        );
    }

    public function testIndexAction()
    {
        $this->setUserIsLoggedIn();
        $this->serviceFactoryMock->shouldReceive("getService")
            ->with("Notepad")
            ->andReturn($this->notepadServiceMock);
        $notepads = array(
            new Notepad(array(
                "id" => 1,
                "userid" => 1,
                "name" => "testname1",
                "text" => "testtext1"
            ))
        );
        $this->notepadServiceMock->shouldReceive("getNotepads")
            ->andReturn($notepads);
        $this->viewMock
            ->shouldReceive("assign")
                ->with("notepads", $notepads)
            ->shouldReceive("setTemplate")
                ->with("index.phtml")
            ->shouldReceive("render");
        $controller = new MembersController(
            $this->requestMock,
            $this->serviceFactoryMock,
            $this->viewMock,
            $this->routerMock
        );
        $this->assertInstanceOf("Notepads\\Controllers\\MembersController", $controller);
        $controller->indexAction();
    }

    public function testProfileAction()
    {
        $this->setUserIsLoggedIn();
        $this->serviceFactoryMock->shouldReceive("getService")
            ->with("User")
            ->andReturn($this->userServiceMock);
        $this->requestMock->shouldReceive("isPost")
            ->andReturn(false);
        $user = new User(array(
            "id" => 1,
            "username" => "testusername1",
            "password" => "testpassword1"
        ));
        $this->userServiceMock->shouldReceive("getCurrentUser")
            ->andReturn($user);
        $this->viewMock
            ->shouldReceive("assign")
                ->with("user", $user)
            ->shouldReceive("setTemplate")
                ->with("profile.phtml")
            ->shouldReceive("render");
        $controller = new MembersController(
            $this->requestMock,
            $this->serviceFactoryMock,
            $this->viewMock,
            $this->routerMock
        );
        $this->assertInstanceOf("Notepads\\Controllers\\MembersController", $controller);
        $controller->profileAction();
    }

    public function testProfileActionUpdateUser()
    {
        $this->setUserIsLoggedIn();
        $this->serviceFactoryMock->shouldReceive("getService")
            ->with("User")
            ->andReturn($this->userServiceMock);
        $this->requestMock->shouldReceive("isPost")
            ->andReturn(true);
        $username = "testusername1";
        $password = "testpassword1";
        $this->requestMock
            ->shouldReceive("post")
                ->with("username")
                ->andReturn($username)
            ->shouldReceive("post")
                ->with("password")
                ->andReturn($password)
            ->shouldReceive("post")
                ->with("passwordconfirm")
                ->andReturn($password);
        $this->userServiceMock->shouldReceive("updateUser")
            ->with($username, $password, $password)
            ->andReturn(true);
        $this->viewMock
            ->shouldReceive("assign")
                ->with("message", "User updated successfully!");
        $user = new User(array(
            "id" => 1,
            "username" => $username,
            "password" => $password
        ));
        $this->userServiceMock->shouldReceive("getCurrentUser")
            ->andReturn($user);
        $this->viewMock
            ->shouldReceive("assign")
                ->with("user", $user)
            ->shouldReceive("setTemplate")
                ->with("profile.phtml")
            ->shouldReceive("render");
        $controller = new MembersController(
            $this->requestMock,
            $this->serviceFactoryMock,
            $this->viewMock,
            $this->routerMock
        );
        $this->assertInstanceOf("Notepads\\Controllers\\MembersController", $controller);
        $controller->profileAction();
    }

    public function testCouldNotUpdateUser()
    {
        $this->setUserIsLoggedIn();
        $username = "testusername1";
        $password = "testpassword1";
        $this->serviceFactoryMock->shouldReceive("getService")
            ->with("User")
            ->andReturn($this->userServiceMock);
        $this->requestMock
            ->shouldReceive("isPost")
                ->andReturn(true)
            ->shouldReceive("post")
                ->with("username")
                ->andReturn($username)
            ->shouldReceive("post")
                ->with("password")
                ->andReturn($password)
            ->shouldReceive("post")
                ->with("passwordconfirm")
                ->andReturn($password);
        $this->userServiceMock->shouldReceive("updateUser")
            ->with($username, $password, $password)
            ->andReturn(false);
        $user = new User(array(
            "id" => 1,
            "username" => "testu1",
            "password" => "testp1"
        ));
        $this->userServiceMock->shouldReceive("getCurrentUser")
            ->andReturn($user);
        $this->viewMock
            ->shouldReceive("assign")
                ->with("message", "Could not update the user!")
            ->shouldReceive("assign")
                ->with("user", $user)
            ->shouldReceive("setTemplate")
                ->with("profile.phtml")
            ->shouldReceive("render");
        $controller = new MembersController(
            $this->requestMock,
            $this->serviceFactoryMock,
            $this->viewMock,
            $this->routerMock
        );
        $this->assertInstanceOf("Notepads\\Controllers\\MembersController", $controller);
        $controller->profileAction();
    }

    public function testLogoutAction()
    {
        $this->setUserIsLoggedIn();
        $this->serviceFactoryMock->shouldReceive("getService")
            ->with("User")
            ->andReturn($this->userServiceMock);
        $this->userServiceMock->shouldReceive("logoutUser");
        $this->routerMock->shouldReceive("route")
            ->with("");
        $controller = new MembersController(
            $this->requestMock,
            $this->serviceFactoryMock,
            $this->viewMock,
            $this->routerMock
        );
        $this->assertInstanceOf("Notepads\\Controllers\\MembersController", $controller);
        $controller->logoutAction();
    }

    public function testNotepadActionOpenNotepad()
    {
        $this->setUserIsLoggedIn();
        $this->requestMock->shouldReceive("isPost")
            ->andReturn(false);
        $this->serviceFactoryMock->shouldReceive("getService")
            ->with("Notepad")
            ->andReturn($this->notepadServiceMock);
        $notepadId = 1;
        $notepadResult = new Notepad(array(
            "id" => $notepadId,
            "userid" => 1,
            "name" => "test name 1",
            "text" => "test text 1",
        ));
        $notepadSearchMock = m::on(function ($param) use ($notepadResult) {
            return $param instanceof Notepad
                && $param->id === $notepadResult->id;
        });
        $this->notepadServiceMock->shouldReceive("findOne")
            ->with($notepadSearchMock)
            ->andReturn($notepadResult);
        $this->viewMock
            ->shouldReceive("assignMany")
                ->with(array(
                    'notepad' => $notepadResult,
                    'submitBtnText' => "Update"
                ))
            ->shouldReceive("setTemplate")
                ->with("notepad.phtml")
            ->shouldReceive("render");
        $controller = new MembersController(
            $this->requestMock,
            $this->serviceFactoryMock,
            $this->viewMock,
            $this->routerMock
        );
        $this->assertInstanceOf("Notepads\\Controllers\\MembersController", $controller);
        $controller->notepadAction($notepadId);
    }

    public function testNotepadActionAddNewNotepad()
    {
        $this->setUserIsLoggedIn();
        $this->requestMock->shouldReceive("isPost")
            ->andReturn(true);
        $this->serviceFactoryMock->shouldReceive("getService")
            ->with("User")
            ->andReturn($this->userServiceMock);
        $user = new User(array(
            "id" => 1,
            "username" => "testusername1",
            "password" => "testpassword1"
        ));
        $this->userServiceMock->shouldReceive("getCurrentUser")
            ->andReturn($user);
        $name = "test name 1";
        $text = "test text 1";
        $notepad = new Notepad([
            "id" => 1,
            "userid" => $user->id,
            "name" => $name,
            "text" => $text
        ]);
        $this->requestMock
            ->shouldReceive("post")
                ->with("name")
                ->andReturn($name)
            ->shouldReceive("post")
                ->with("text")
                ->andReturn($text);
        $this->serviceFactoryMock->shouldReceive("getService")
            ->with("Notepad")
            ->andReturn($this->notepadServiceMock);
        $notepadSaveMock = m::on(function ($param) use ($notepad) {
            return $param instanceof Notepad
            && $param->name === $notepad->name
            && $param->text === $notepad->text;
        });
        $assignManyMock = m::on(function ($param) {
            return is_array($param)
                && isset($param["notepad"])
                && isset($param["submitBtnText"])
                && $param["notepad"] instanceof Notepad
                && $param["notepad"]->name === ""
                && $param["notepad"]->text === ""
                && $param["submitBtnText"] === "Add";
        });
        $this->notepadServiceMock->shouldReceive("save")
            ->with($notepadSaveMock)
            ->andReturn(true);
        $this->viewMock
            ->shouldReceive("assign")
                ->with("message", "Notepad saved!")
            ->shouldReceive("assignMany")
                ->with($assignManyMock)
            ->shouldReceive("setTemplate")
                ->with("notepad.phtml")
            ->shouldReceive("render");
        $controller = new MembersController(
            $this->requestMock,
            $this->serviceFactoryMock,
            $this->viewMock,
            $this->routerMock
        );
        $this->assertInstanceOf("Notepads\\Controllers\\MembersController", $controller);
        $controller->notepadAction();
    }
    public function testNotepadActionAddNewNotepadCouldNotSave()
    {
        $this->setUserIsLoggedIn();
        $this->requestMock->shouldReceive("isPost")
            ->andReturn(true);
        $this->serviceFactoryMock->shouldReceive("getService")
            ->with("User")
            ->andReturn($this->userServiceMock);
        $user = new User(array(
            "id" => 1,
            "username" => "testusername1",
            "password" => "testpassword1"
        ));
        $this->userServiceMock->shouldReceive("getCurrentUser")
            ->andReturn($user);
        $name = "test name 1";
        $text = "test text 1";
        $notepad = new Notepad([
            "id" => 1,
            "userid" => $user->id,
            "name" => $name,
            "text" => $text
        ]);
        $this->requestMock
            ->shouldReceive("post")
                ->with("name")
                ->andReturn($name)
            ->shouldReceive("post")
                ->with("text")
                ->andReturn($text);
        $this->serviceFactoryMock->shouldReceive("getService")
            ->with("Notepad")
            ->andReturn($this->notepadServiceMock);
        $notepadSaveMock = m::on(function ($param) use ($notepad) {
            return $param instanceof Notepad
            && $param->name === $notepad->name
            && $param->text === $notepad->text;
        });
        $assignManyMock = m::on(function ($param) use ($name, $text) {
            return is_array($param)
                && isset($param["notepad"])
                && isset($param["submitBtnText"])
                && $param["notepad"] instanceof Notepad
                && $param["notepad"]->name === $name
                && $param["notepad"]->text === $text
                && $param["submitBtnText"] === "Add";
        });
        $this->notepadServiceMock->shouldReceive("save")
            ->with($notepadSaveMock)
            ->andReturn(false);
        $this->notepadServiceMock->shouldReceive("getErrors")
            ->andReturn(array("test error"));
        $this->viewMock
            ->shouldReceive("assign")
                ->with("message", "Could not save the Notepad!<br/>Error message: test error")
            ->shouldReceive("assignMany")
                ->with($assignManyMock)
            ->shouldReceive("setTemplate")
                ->with("notepad.phtml")
            ->shouldReceive("render");
        $controller = new MembersController(
            $this->requestMock,
            $this->serviceFactoryMock,
            $this->viewMock,
            $this->routerMock
        );
        $this->assertInstanceOf("Notepads\\Controllers\\MembersController", $controller);
        $controller->notepadAction();
    }

    public function testNotepadActionNoIdGivenNoPost()
    {
        $this->setUserIsLoggedIn();
        $this->requestMock->shouldReceive("isPost")
            ->andReturn(false);
        $assignManyMock = m::on(function ($param) {
            return is_array($param)
            && isset($param["notepad"])
            && isset($param["submitBtnText"])
            && $param["notepad"] instanceof Notepad
            && $param["notepad"]->name === ""
            && $param["notepad"]->text === ""
            && $param["submitBtnText"] === "Add";
        });
        $this->viewMock
            ->shouldReceive("assignMany")
                ->with($assignManyMock)
            ->shouldReceive("setTemplate")
                ->with("notepad.phtml")
            ->shouldReceive("render");
        $controller = new MembersController(
            $this->requestMock,
            $this->serviceFactoryMock,
            $this->viewMock,
            $this->routerMock
        );
        $this->assertInstanceOf("Notepads\\Controllers\\MembersController", $controller);
        $controller->notepadAction();
    }

    public function testDeleteAction()
    {
        $this->setUserIsLoggedIn();
        $notepadId = 1;
        $this->serviceFactoryMock->shouldReceive("getService")
            ->with("Notepad")
            ->andReturn($this->notepadServiceMock);
        $this->notepadServiceMock->shouldReceive("delete")
            ->with($notepadId)
            ->andReturn(true);
        $this->routerMock->shouldReceive("route")
            ->with("/members");
        $controller = new MembersController(
            $this->requestMock,
            $this->serviceFactoryMock,
            $this->viewMock,
            $this->routerMock
        );
        $this->assertInstanceOf("Notepads\\Controllers\\MembersController", $controller);
        $controller->deleteAction($notepadId);
    }
}
