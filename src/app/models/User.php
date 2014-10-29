<?php

namespace Notepads\Models;

/**
 * Class User
 * @package Notepads\Models
 */
class User extends DomainObject
{
    /**
     * @var
     */
    protected $id;
    /**
     * @var
     */
    protected $username;
    /**
     * @var
     */
    protected $password;

    /**
     * @param array $params
     */
    public function __construct(array $params = null)
    {
        if (!is_null($params)) {
            foreach (array('id', 'username', 'password') as $name) {
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
        if ("username" == $property && !$this->checkUsername($value)) {
            throw new \InvalidArgumentException(
                "Invalid username '$value' given!"
            );
        } elseif ("password" == $property && !$this->checkPassword($value)) {
            throw new \InvalidArgumentException(
                "Invalid password '$value' given!"
            );
        } elseif ("id" == $property) {
            if (!is_numeric($value) || intval($value) <= 0) {
                throw new \InvalidArgumentException(
                    "Invalid id '$value' given!"
                );
            }
            $value = intval($value);
        }
        parent::__set($property, $value);
    }

    /**
     * @param null $username
     * @return int
     */
    protected function checkUsername($username = null)
    {
        return preg_match(
            "/^[a-z][a-zA-Z0-9]{3,20}$/",
            !is_null($username) ? $username : $this->username
        );
    }

    /**
     * @param null $password
     * @return int
     */
    protected function checkPassword($password = null)
    {
        return preg_match(
            "/.{6,20}/",
            !is_null($password) ? $password : $this->password
        );
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->id,
            'username' => $this->username,
            'password' => $this->password,
        );
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return (
            (is_null($this->id) || (is_int($this->id) && $this->id > 0))
            && $this->username && $this->password
            && $this->checkUsername() && $this->checkPassword()
        );
    }
}
