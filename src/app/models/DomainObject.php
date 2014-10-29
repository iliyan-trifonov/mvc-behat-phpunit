<?php

namespace Notepads\Models;

/**
 * Class DomainObject
 * @package Notepads\Models
 */
abstract class DomainObject
{
    /**
     * @param $property
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->{$property};
        } else {
            throw new \InvalidArgumentException(
                "Invalid property name '$property'!"
            );
        }
    }

    /**
     * @param $property
     * @param $value
     */
    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            $this->{$property} = $value;
        } else {
            throw new \InvalidArgumentException(
                "Invalid property name '$property'!"
            );
        }
    }
}
