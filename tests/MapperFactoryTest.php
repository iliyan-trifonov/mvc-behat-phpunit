<?php

namespace Notepads\Tests;

use Notepads\Tests\PHPUnit\BaseTestClass;
use Notepads\Models\MapperFactory;

class MapperFactoryTest extends BaseTestClass
{

    private $mapperFactory;

    public function setup()
    {
        $this->mapperFactory = new MapperFactory(null);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf("Notepads\\Models\\MapperFactory", new MapperFactory(null));
    }

    public function testBuildClassNotExists()
    {
        $classname = "BadClass";
        $this->setExpectedException("\\Exception", "Class 'Notepads\\Models\\{$classname}Mapper' not found!");
        $this->mapperFactory->build($classname);
    }

    /**
     * @runInSeparateProcess
     */
    public function testBuildSessionMapper()
    {
        $this->assertInstanceOf("Notepads\\Models\\SessionMapper", $this->mapperFactory->build("Session"));
    }

    public function testBuildMapper()
    {
        $this->assertInstanceOf("Notepads\\Models\\NotepadMapper", $this->mapperFactory->build("Notepad"));
    }
}
