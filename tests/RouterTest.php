<?php

namespace Notepads\Tests;

use Notepads\Lib\Router;
use Notepads\Tests\PHPUnit\BaseTestClass;

class RouterTest extends BaseTestClass
{
    public function testParseUrl()
    {
        $url = "/";
        $router = new Router($url);
        $router->parseUrl();
        $this->assertEquals("IndexController", $router->controller);
        $this->assertEquals("indexAction", $router->action);
        $this->assertEquals(array(), $router->params);

        $url = "/dashboard";
        $router = new Router($url);
        $router->parseUrl();
        $this->assertEquals("MembersController", $router->controller);
        $this->assertEquals("indexAction", $router->action);
        $this->assertEquals(array(), $router->params);

        $url = "/notepad/1";
        $router = new Router($url);
        $router->parseUrl();
        $this->assertEquals("MembersController", $router->controller);
        $this->assertEquals("notepadAction", $router->action);
        $this->assertEquals(array(1), $router->params);

        $url = "/notepad/0";
        $router = new Router($url);
        $router->parseUrl();
        $this->assertEquals("IndexController", $router->controller);
        $this->assertEquals("indexAction", $router->action);
        $this->assertEquals(array(), $router->params);

        $url = "/notepad/10";
        $router = new Router($url);
        $router->parseUrl();
        $this->assertEquals("MembersController", $router->controller);
        $this->assertEquals("notepadAction", $router->action);
        $this->assertEquals(array(10), $router->params);

        $url = "/unknown";
        $router = new Router($url);
        $router->parseUrl();
        $this->assertEquals("IndexController", $router->controller);
        $this->assertEquals("indexAction", $router->action);
        $this->assertEquals(array(), $router->params);

        $url = "/index";
        $router = new Router($url);
        $router->parseUrl();
        $this->assertEquals("IndexController", $router->controller);
        $this->assertEquals("indexAction", $router->action);
        $this->assertEquals(array(), $router->params);

        $url = "";
        $router = new Router($url);
        $router->parseUrl();
        $this->assertEquals("IndexController", $router->controller);
        $this->assertEquals("indexAction", $router->action);
        $this->assertEquals(array(), $router->params);
    }

    /**
     * @runInSeparateProcess
     */
    public function testRoute()
    {
        $url = "login";
        $router = new Router("/");
        $router->route($url);
        $headers = xdebug_get_headers();
        $this->assertEquals(array("Location: /login"), $headers);

        $url = "unknown";
        $router = new Router("/");
        $router->route($url);
        $headers = xdebug_get_headers();
        $this->assertEquals(array("Location: /index/index"), $headers);
    }
}
