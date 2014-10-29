<?php

namespace Notepads\Models;

/**
 * Class ServiceFactory
 * @package Notepads\Models
 */
class ServiceFactory
{
    /**
     * @var string
     */
    private $prefix = "Notepads\\Models\\";
    /**
     * @var MapperFactory
     */
    private $mapperFactory;
    /**
     * @var SessionMapper
     */
    private $session;
    /**
     * @var array
     */
    private $cache = array();

    /**
     * @param $connection
     */
    public function __construct($connection)
    {
        $this->mapperFactory = new MapperFactory($connection);
        $this->session = new SessionMapper();
    }

    /**
     * @param $serviceClassName
     * @return mixed
     * @throws \Exception
     */
    public function getService($serviceClassName)
    {
        $class = $this->prefix . $serviceClassName . "Service";
        if (!class_exists($class)) {
            throw new \Exception("Class '$class' not found!");
        }
        if (!isset($this->cache[$serviceClassName])) {
            $mapper = $this->mapperFactory->build($serviceClassName);
            $this->cache[$serviceClassName] = new $class($mapper, $this->session);
        }
        return $this->cache[$serviceClassName];
    }
}
