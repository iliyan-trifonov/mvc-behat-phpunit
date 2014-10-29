<?php

namespace Notepads\Controllers;

/**
 * Class BaseController
 * @package Notepads\Controllers
 */
class BaseController
{
    /**
     * @var
     */
    protected $request;
    /**
     * @var
     */
    protected $serviceFactory;
    /**
     * @var
     */
    protected $view;
    /**
     * @var
     */
    protected $router;

    /**
     * @param $request
     * @param $serviceFactory
     * @param $view
     * @param $router
     */
    public function __construct($request, $serviceFactory, $view, $router)
    {
        $this->request = $request;
        $this->serviceFactory = $serviceFactory;
        $this->view = $view;
        $this->router = $router;

        $this->view->setLayout('layout.phtml');
    }

    /**
     *
     */
    public function indexAction()
    {

    }
}
