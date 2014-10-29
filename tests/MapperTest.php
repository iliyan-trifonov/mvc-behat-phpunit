<?php

namespace Notepads\Tests;

use Notepads\Tests\PHPUnit\BaseTestClass;
use Notepads\Models\Mapper;

class MapperTest extends BaseTestClass
{
    public function testConstructor()
    {
        $this->assertInstanceOf("Notepads\\Models\\Mapper", new Mapper(null));
    }
}
