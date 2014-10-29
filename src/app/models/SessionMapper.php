<?php

namespace Notepads\Models;

/**
 * Class SessionMapper
 * @package Notepads\Models
 */
class SessionMapper
{
    /**
     * @var
     */
    private $session;

    /**
     *
     */
    public function __construct()
    {
        if (!session_id()) {
            session_start();
        }
        //TODO: make it without using the superglobal var here
        $this->session = &$_SESSION;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function storeUser(User $user)
    {
        $this->set('user', $user->toArray());
        return $this;
    }

    /**
     * @return bool|User
     */
    public function getUser()
    {
        return ($user = $this->get('user'))
            ? new User($user)
            : false;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function set($name, $value)
    {
        $this->session[$name] = $value;
        return $this;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function get($name)
    {
        return isset($this->session[$name])
            ? $this->session[$name]
            : null;
    }

    /**
     * @param $name
     * @return $this
     */
    public function remove($name)
    {
        unset($this->session[$name]);
        return $this;
    }
}
