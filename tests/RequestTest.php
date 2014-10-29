<?php

namespace Notepads\Tests;

use Notepads\Lib\Request;
use Notepads\Tests\PHPUnit\BaseTestClass;

class RequestTest extends BaseTestClass
{
    public function setup()
    {
    }

    public function testConstructorSetsProperVars()
    {
        $get = array("getvar" => 1);
        $post = array("postvar" => 1);
        $server = array("REQUEST_METHOD" => "POST");
        $request = new Request($get, $post, $server);
        $this->assertEquals($get["getvar"], $request->get("getvar"));
        $this->assertEquals($post["postvar"], $request->post("postvar"));
        $this->assertTrue($request->isPost());
    }
}
