<?php

namespace Notepads\Lib;

/**
 * Class Router
 * @package Notepads\Lib
 */
class Router
{

    /**
     * @var string
     */
    private $prefix = "Notepads\\Controllers\\";
    /**
     * @var
     */
    private $url;
    /**
     * @var array
     */
    private $urlMap = array();
    /**
     * @var
     */
    public $controller;
    /**
     * @var
     */
    public $action;
    /**
     * @var
     */
    public $params;

    /**
     * @param $url
     */
    public function __construct($url)
    {
        $this->url = $url;
        $this->urlMap = array(
            '/' => array(
                'controller' => 'Index',
                'action' => 'index',
            ),
            '/register' => array(
                'controller' => 'Index',
                'action' => 'register',
            ),
            '/login' => array(
                'controller' => 'Index',
                'action' => 'login',
            ),
            '/logout' => array(
                'controller' => 'Members',
                'action' => 'logout',
            ),
            '/dashboard' => array(
                'controller' => 'Members',
                'action' => 'index',
            ),
            '/profile' => array(
                'controller' => 'Members',
                'action' => 'profile',
            ),
            '/notepad' => array(
                'controller' => 'Members',
                'action' => 'notepad',
            ),
            '/notepad/([1-9]+[0]?)' => array(
                'controller' => 'Members',
                'action' => 'notepad',
            ),
            '/delete/([1-9]+[0]?)' => array(
                'controller' => 'Members',
                'action' => 'delete',
            ),
        );
    }

    /**
     *
     */
    public function parseUrl()
    {
        $parts = $this->findMatches();
        $props = $this->getFromParts($parts);
        $this->controller = $props["controller"];
        $this->action = $props["action"];
        $this->params = $props["params"];
        $this->checkClass();
        $this->checkValidAction();
        $this->populateParams();
    }

    protected function findMatches()
    {
        $parts = array();
        $matched = false;
        foreach ($this->urlMap as $route => $data) {
            if (preg_match("#^".$route."$#", $this->url, $matches)) {
                $parts[1] = $data['controller'];
                $parts[2] = $data['action'];
                $parts[3] = isset($matches[1]) ? $matches[1] : null;
                $matched = true;
                break;
            }
        }
        if (!$matched) {
            $parts = explode("/", $this->url);
        }
        return $parts;
    }

    /**
     * @param array $parts
     * @return array
     */
    protected function getFromParts($parts = array())
    {
        if (empty($parts) || empty($parts[1])) {
            $controller = "Index";
            $action = "index";
            $params = null;
        } else {
            $controller = ucfirst($parts[1]);
            if (empty($parts[2])) {
                $action = "index";
            } else {
                $action = $parts[2];
            }
            if (isset($parts[3])) {
                $params = $parts[3];
            } else {
                $params = null;
            }
        }
        $controller .= "Controller";
        $action .= "Action";
        return array(
            "controller" => $controller,
            "action" => $action,
            "params" => $params
        );
    }

    protected function checkClass()
    {
        if (!class_exists($this->prefix . $this->controller)) {
            $this->controller = "IndexController";
            $this->action = "indexAction";
            $this->params = null;
        }
    }

    /**
     *
     */
    protected function checkValidAction()
    {
        try {
            $class = new \ReflectionClass($this->prefix . $this->controller);
            $class->getMethod($this->action);
        } catch (\ReflectionException $rex) {
            $this->action = "indexAction";
            $this->params = null;
        }
    }

    protected function populateParams()
    {
        if (is_null($this->params)) {
            $this->params = array();
        } else {
            $this->params = explode("/", $this->params);
        }
    }

    /**
     * @param $url
     */
    public function route($url)
    {
        $this->url = "/" . $url;
        if (array_key_exists($this->url, $this->urlMap)) {
            header("Location: " . $this->url);
            return;
        }
        $this->parseUrl();
        $controller = lcfirst(str_replace("Controller", "", $this->controller));
        $action = lcfirst(str_replace("Action", "", $this->action));
        header("Location: /" . $controller . "/" . $action);
    }
}
