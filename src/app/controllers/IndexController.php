<?php

namespace Notepads\Controllers;

/**
 * Class IndexController
 * @package Notepads\Controllers
 */
class IndexController extends BaseController
{

    /**
     * @param $request
     * @param $serviceFactory
     * @param $view
     * @param $router
     */
    public function __construct($request, $serviceFactory, $view, $router)
    {
        parent::__construct($request, $serviceFactory, $view, $router);
        $service = $this->serviceFactory->getService("User");
        if ($service->userIsLoggedIn()) {
            $this->router->route("dashboard");
        } else {
            $this->view->assign("loggedIn", false, true);
        }
    }

    /**
     *
     */
    public function indexAction()
    {
        $this->view->setTemplate('index.phtml');
        $this->view->render();
    }

    /**
     *
     */
    public function loginAction()
    {
        if ($this->request->isPost()) {
            $username = $this->request->post("username");
            $password = $this->request->post("password");
            $service = $this->serviceFactory->getService("User");
            if ($service->authenticate($username, $password)) {
                $this->router->route("dashboard");
                return;
            } else {
                $this->view->assignMany(array(
                    'username' => '',
                    'password' => '',
                    'errors' => $service->getErrors()
                ));
            }
        } else {
            $this->view->assignMany(array(
                'username' => '',
                'password' => '',
            ));
        }
        $this->view->setTemplate('login.phtml');
        $this->view->render();
    }

    /**
     *
     */
    public function registerAction()
    {
        if ($this->request->isPost()) {
            $username = $this->request->post("username");
            $password = $this->request->post("password");
            $passwordconfirm = $this->request->post("passwordconfirm");
            $service = $this->serviceFactory->getService("User");
            if (!$service->register($username, $password, $passwordconfirm)) {
                $message = "Could not register!<br/>Errors found: ";
                foreach ($service->getErrors() as $error) {
                    $message .= "$error, ";
                }
                $message = rtrim($message, ", ");
            } else {
                //TODO: flash = success
                //TODO: redirect to public index or members index
                $message = "User successfully registered!";
            }
            $this->view->assignMany(
                array(
                    "message" => $message,
                    "username" => $username
                )
            );
        } else {
            $this->view->assign("username", "");
        }
        $this->view->setTemplate("register.phtml");
        $this->view->render();
    }
}
