<?php

namespace Notepads\Lib;

/**
 * Class Request
 * @package Notepads\Lib
 */
class Request
{

    /**
     * @var
     */
    private $get;
    /**
     * @var
     */
    private $post;
    /**
     * @var
     */
    private $server;

    /**
     * @param $get
     * @param $post
     * @param $server
     */
    public function __construct($get, $post, $server)
    {
        $this->get = $get;
        $this->post = $post;
        $this->server = $server;
    }

    /**
     * @param $name
     * @return null
     */
    public function get($name)
    {
        return isset($this->get[$name]) ? $this->get[$name] : null;
    }

    /**
     * @param $name
     * @return null
     */
    public function post($name)
    {
        return isset($this->post[$name]) ? $this->post[$name] : null;
    }

    /**
     * @return bool
     */
    public function isPost()
    {
        return $this->server['REQUEST_METHOD'] === 'POST';
    }
}
