<?php

namespace Notepads\Tests;

use Notepads\Tests\PHPUnit\BaseTestClass;
use Notepads\Models\ServiceFactory;

class ServiceFactoryTest extends BaseTestClass
{

    /**
     * @runInSeparateProcess
     */
    public function testConstructor()
    {
        $this->assertInstanceOf("Notepads\\Models\\ServiceFactory", new ServiceFactory(null));
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetServiceClassNotExists()
    {
        $classname = "BadClass";
        $this->setExpectedException("\\Exception", "Class 'Notepads\\Models\\{$classname}Service' not found!");
        $serviceFactory = new ServiceFactory(null);
        $serviceFactory->getService($classname);
    }

    /**
     * @runInSeparateProcess
     */
    public function testBuildService()
    {
        $serviceFactory = new ServiceFactory(null);
        $this->assertInstanceOf("Notepads\\Models\\NotepadService", $serviceFactory->getService("Notepad"));
    }
}
