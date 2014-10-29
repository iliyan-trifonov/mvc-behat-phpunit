<?php

namespace Notepads\Tests;

use Notepads\Tests\PHPUnit\BaseTestClass;
use Notepads\Controllers\IndexController;
use Notepads\Lib\Request;
use Notepads\Lib\Template;
use Notepads\Lib\Router;
use Notepads\Models\ServiceFactory;
use Notepads\Models\UserService;
use \Mockery as m;


class IndexControllerTest extends BaseTestClass
{
    private $request;
    private $serviceFactory;
    private $userServiceMock;
    private $view;
    private $router;

    public function setup()
    {
        $this->request = m::mock("Request");
        $this->serviceFactory = m::mock("ServiceFactory");
        $this->userServiceMock = m::mock("UserService");
        $this->view = m::mock("Template");
        $this->router = m::mock("Router");

        $this->serviceFactory->shouldReceive("getService")
            ->with("User")
            ->andReturn($this->userServiceMock);

        $this->view->shouldReceive("setLayout")
                ->with("layout.phtml");
    }

    private function setUserNotLoggedIn()
    {
        $this->userServiceMock->shouldReceive("userIsLoggedIn")
            ->andReturn(false);
        $this->view->shouldReceive("assign")
            ->with("loggedIn", false, true);
    }

    public function testConstructorUserIsLoggedIn()
    {
        $this->userServiceMock->shouldReceive("userIsLoggedIn")
            ->andReturn(true);
        $this->router->shouldReceive("route")
            ->with("dashboard");
        $this->assertInstanceOf(
            "Notepads\\Controllers\\IndexController",
            new IndexController(
                $this->request,
                $this->serviceFactory,
                $this->view,
                $this->router
            )
        );
    }

    public function testConstructorUserIsNotLoggedIn()
    {
        $this->setUserNotLoggedIn();
        $this->assertInstanceOf(
            "Notepads\\Controllers\\IndexController",
            new IndexController(
                $this->request,
                $this->serviceFactory,
                $this->view,
                $this->router
            )
        );
    }

    public function testIndexAction()
    {
        $this->setUserNotLoggedIn();
        $this->view
            ->shouldReceive("setTemplate")
                ->with("index.phtml")
            ->shouldReceive("render");
        $controller = new IndexController(
            $this->request,
            $this->serviceFactory,
            $this->view,
            $this->router
        );
        $this->assertInstanceOf(
            "Notepads\\Controllers\\IndexController",
            $controller
        );
        $controller->indexAction();
    }

    public function testOnLoginPage()
    {
        $this->setUserNotLoggedIn();
        $this->view
            ->shouldReceive("assignMany")
                ->with(array(
                    "username" => "",
                    "password" => ""
                ))
            ->shouldReceive("setTemplate")
                ->with("login.phtml")
            ->shouldReceive("render");
        $this->request->shouldReceive("isPost")
            ->andReturn(false);
        $controller = new IndexController(
            $this->request,
            $this->serviceFactory,
            $this->view,
            $this->router
        );
        $this->assertInstanceOf(
            "Notepads\\Controllers\\IndexController",
            $controller
        );
        $controller->loginAction();
    }

    public function testLoginExistingUser()
    {
        $this->setUserNotLoggedIn();
        $username = "testusername1";
        $password = "testpassword1";
        $this->request
            ->shouldReceive("isPost")
                ->andReturn(true)
            ->shouldReceive("post")
                ->with("username")
                ->andReturn($username)
            ->shouldReceive("post")
                ->with("password")
                ->andReturn($password);
        $this->userServiceMock->shouldReceive("authenticate")
            ->with($username, $password)
            ->andReturn(true);
        $this->router->shouldReceive("route")
            ->with("dashboard");
        $this->view
            ->shouldReceive("setTemplate")
                ->with("login.phtml")
            ->shouldReceive("render");
        $controller = new IndexController(
            $this->request,
            $this->serviceFactory,
            $this->view,
            $this->router
        );
        $this->assertInstanceOf(
            "Notepads\\Controllers\\IndexController",
            $controller
        );
        $controller->loginAction();
    }

    public function testTryLoggingInWithNotExistingUser()
    {
        $this->setUserNotLoggedIn();
        $username = "testusername1";
        $password = "testpassword1";
        $this->request
            ->shouldReceive("isPost")
                ->andReturn(true)
            ->shouldReceive("post")
                ->with("username")
                ->andReturn($username)
            ->shouldReceive("post")
                ->with("password")
                ->andReturn($password);
        $error = "Wrong user!";
        $this->userServiceMock
            ->shouldReceive("authenticate")
                ->with($username, $password)
                ->andReturn(false)
            ->shouldReceive("getErrors")
                ->andReturn(array($error));
        $this->view
            ->shouldReceive("assignMany")
                ->with(array(
                    'username' => '',
                    'password' => '',
                    'errors' => array($error)
                ))
            ->shouldReceive("setTemplate")
                ->with("login.phtml")
            ->shouldReceive("render");
        $controller = new IndexController(
            $this->request,
            $this->serviceFactory,
            $this->view,
            $this->router
        );
        $this->assertInstanceOf(
            "Notepads\\Controllers\\IndexController",
            $controller
        );
        $controller->loginAction();
    }

    public function onRegisterPage()
    {
        $this->setUserNotLoggedIn();
        $this->request->shouldReceive("isPost")
            ->andReturn(false);
        $this->view
            ->shouldReceive("assign")
                ->with("username", "")
            ->shouldReceive("setTemplate")
                ->with("register.phtml")
            ->shouldReceive("render");
        $controller = new IndexController(
            $this->request,
            $this->serviceFactory,
            $this->view,
            $this->router
        );
        $this->assertInstanceOf(
            "Notepads\\Controllers\\IndexController",
            $controller
        );
        $controller->registerAction();
    }

    public function testRegisterWithGoodUserData()
    {
        $this->setUserNotLoggedIn();
        $username = "testusername1";
        $password = "testpassword1";
        $this->request
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
        $this->userServiceMock->shouldReceive("register")
            ->with($username, $password, $password)
            ->andReturn(true);
        $this->view
            ->shouldReceive("assignMany")
                ->with(array(
                    "message" => "User successfully registered!",
                    "username" => $username
                ))
            ->shouldReceive("setTemplate")
                ->with("register.phtml")
            ->shouldReceive("render");
        $controller = new IndexController(
            $this->request,
            $this->serviceFactory,
            $this->view,
            $this->router
        );
        $this->assertInstanceOf(
            "Notepads\\Controllers\\IndexController",
            $controller
        );
        $controller->registerAction();
    }
}
