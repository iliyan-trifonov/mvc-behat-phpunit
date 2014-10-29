<?php

namespace Notepads\Models;

/**
 * Class MapperFactory
 * @package Notepads\Models
 */
class MapperFactory
{

    /**
     * @var string
     */
    private $prefix = "Notepads\\Models\\";
    /**
     * @var
     */
    private $connection;
    /**
     * @var
     */
    private $cache;

    /**
     * @param $connection
     */
    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param $mapperClassName
     * @return mixed
     * @throws \Exception
     */
    public function build($mapperClassName)
    {
        $class = $this->prefix . $mapperClassName . "Mapper";
        if (!class_exists($class)) {
            throw new \Exception("Class '$class' not found!");
        }
        if (!isset($this->cache[$mapperClassName])) {
            if ("Session" === $mapperClassName) {
                $this->cache[$mapperClassName] = new $class();
            } else {
                $this->cache[$mapperClassName] = new $class($this->connection);
            }
        }
        return $this->cache[$mapperClassName];
    }
}
