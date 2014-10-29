<?php

namespace Notepads\Models;

/**
 * Class Mapper
 * @package Notepads\Models
 */
class Mapper
{
    /**
     * @var
     */
    protected $database;

    /**
     * @param $database
     */
    public function __construct($database)
    {
        $this->database = $database;
    }
}
