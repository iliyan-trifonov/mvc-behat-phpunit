<?php

namespace Notepads\Models;

/**
 * Class Notepad
 * @package Notepads\Models
 */
class Notepad extends DomainObject
{
    /**
     * @var
     */
    protected $id;
    /**
     * @var
     */
    protected $userid;
    /**
     * @var
     */
    protected $name;
    /**
     * @var
     */
    protected $text;

    /**
     * @param array $params
     */
    public function __construct(array $params = null)
    {
        if (!is_null($params)) {
            foreach (array('id', 'userid', 'name', 'text') as $name) {
                if (isset($params[$name])) {
                    $this->__set($name, $params[$name]);
                }
            }
        }
    }

    /**
     * @param $property
     * @param $value
     */
    public function __set($property, $value)
    {
        if (in_array($property, array("id", "userid"))) {
            if (!is_numeric($value) || intval($value) <= 0) {
                throw new \InvalidArgumentException("Invalid $property '$value' given!");
            }
            $value = intval($value);
        }
        parent::__set($property, $value);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->id,
            'userid' => $this->userid,
            'name' => $this->name,
            'text' => $this->text,
        );
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return (
            (!is_null($this->userid) && is_numeric($this->userid))
            && (!is_null($this->name) && !empty($this->name))
            && (!is_null($this->text) && !empty($this->text))
        );
    }
}
