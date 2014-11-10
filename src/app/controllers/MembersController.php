<?php

namespace Notepads\Controllers;

use Notepads\Models\Notepad;

/**
 * Class MembersController
 * @package Notepads\Controllers
 */
class MembersController extends BaseController
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
        if (!$service->userIsLoggedIn()) {
            $this->router->route("");//index/index
        } else {
            $this->view->assign("loggedIn", true, true);
        }
    }

    /**
     *
     */
    public function indexAction()
    {
        $service = $this->serviceFactory->getService("Notepad");
        $notepads = $service->getNotepads();
        $this->view->assign("notepads", $notepads);
        $this->view->setTemplate('index.phtml');
        $this->view->render();
    }

    /**
     *
     */
    public function profileAction()
    {
        $service = $this->serviceFactory->getService("User");
        if ($this->request->isPost()) {
            $username = $this->request->post("username");
            $password = $this->request->post("password");
            $passwordconfirm = $this->request->post("passwordconfirm");
            if (!$service->updateUser($username, $password, $passwordconfirm)) {
                $message = "Could not update the user!";
            } else {
                $message = "User updated successfully!";
            }
            $this->view->assign("message", $message);
        }
        $user = $service->getCurrentUser();
        $this->view->assign("user", $user);
        $this->view->setTemplate("profile.phtml");
        $this->view->render();
    }

    /**
     *
     */
    public function logoutAction()
    {
        $service = $this->serviceFactory->getService("User");
        $service->logoutUser();
        $this->router->route("");
    }

    /**
     * @param null $notepadId
     */
    public function notepadAction($notepadId = null)
    {
        if ($this->request->isPost()) {
            $service = $this->serviceFactory->getService("User");
            $user = $service->getCurrentUser();
            $notepad = new Notepad(array(
                "id" => $notepadId,
                "userid" => $user->id,
                "name" => $this->request->post("name"),
                "text" => $this->request->post("text")
            ));
            $service = $this->serviceFactory->getService("Notepad");
            if ($service->save($notepad)) {
                $message = "Notepad saved!";
                $notepad = new Notepad(array(
                    "name" => "",
                    "text" => ""
                ));
            } else {
                $errors = $service->getErrors();
                $message = "Could not save the Notepad!<br/>Error message: "
                    . $errors[0];
                $notepad = new Notepad(array(
                    "name" => $this->request->post("name"),
                    "text" => $this->request->post("text")
                ));
            }
            $this->view->assign("message", $message);
        }
        //
        if (!is_null($notepadId)) {
            $notepadId = (int) $notepadId;
            $service = $this->serviceFactory->getService("Notepad");
            $notepad = new Notepad(array("id" => $notepadId));
            $notepad = $service->findOne($notepad);
            $submitBtnText = "Update";
        } else {
            if (!isset($notepad)) {
                $notepad = new Notepad(array(
                    "name" => "",
                    "text" => ""
                ));
            }
            $submitBtnText = "Add";
        }
        $this->view->assignMany(array(
            'notepad' => $notepad,
            'submitBtnText' => $submitBtnText
        ));
        $this->view->setTemplate("notepad.phtml");
        $this->view->render();
    }

    /**
     * @param null $notepadId
     */
    public function deleteAction($notepadId = null)
    {
        if (!is_null($notepadId)) {
            $notepadId = (int) $notepadId;
            $service = $this->serviceFactory->getService("Notepad");
            if (!$service->delete($notepadId)) {
                //TODO: show error: flash or stay on the page
            }
        }
        $this->router->route("/members");
    }
}
