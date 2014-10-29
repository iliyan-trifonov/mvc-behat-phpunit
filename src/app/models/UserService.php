<?php

namespace Notepads\Models;

/**
 * Class UserService
 * @package Notepads\Models
 */
class UserService
{
    /**
     * @var
     */
    private $mapper;
    /**
     * @var
     */
    private $session;
    /**
     * @var array
     */
    private $errors = array();

    /**
     * @param $mapper
     * @param $session
     */
    public function __construct($mapper, $session)
    {
        $this->mapper = $mapper;
        $this->session = $session;
    }

    /**
     * @param $error
     */
    protected function addError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->mapper->fetchAll();
    }

    /**
     * @param $username
     * @param $password
     * @param $passwordconfirm
     * @return bool
     */
    public function register($username, $password, $passwordconfirm)
    {
        if ($password != $passwordconfirm) {
            $this->addError("Passwords do not match!");
            return false;
        }
        try {
            $user = new User(array(
                'username' => $username,
                'password' => $password
            ));
        } catch (\InvalidArgumentException $exc) {
            $this->addError("Invalid username or password!");
            return false;
        }
        if ($this->mapper->findOne($user)) {
            $this->addError("User already exists!");
            return false;
        }
        return $this->mapper->save($user);
    }

    /**
     * @param $username
     * @param $password
     * @return bool
     */
    public function authenticate($username, $password)
    {
        if (!$username || !$password) {
            $this->addError("No username or password given!");
            return false;
        }
        try {
            $user = new User(array(
                'username' => $username,
                'password' => $password
            ));
        } catch (\InvalidArgumentException $exc) {
            $this->addError($exc->getMessage());
            return false;
        }
        if ($user = $this->mapper->findOne($user)) {
            $this->session->storeUser($user);
            return true;
        } else {
            $this->addError("Wrong user!");
            return false;
        }
    }

    /**
     * @return bool
     */
    public function userIsLoggedIn()
    {
        if ($user = $this->session->getUser()) {
            return boolval($this->mapper->findOne($user));
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getCurrentUser()
    {
        return $this->session->getUser();
    }

    /**
     *
     */
    public function logoutUser()
    {
        $this->session->remove("user");
    }

    /**
     * @param $username
     * @param $password
     * @param $passwordconfirm
     * @return bool
     */
    public function updateUser($username, $password, $passwordconfirm)
    {
        if (!$username || !$password || !$passwordconfirm
            || ($password != $passwordconfirm)
        ) {
            $this->addError("Not enough params or passwords do not match!");
            return false;
        }
        $user = $this->getCurrentUser();
        if ($username == $user->username && $password == $user->password) {
            return true; //no need to update
        }
        if ($this->checkUsernameUsed($user->id, $username)) {
            $this->addError("User with that name exists!");
            return false;
        }
        $user->username = $username;
        $user->password = $password;
        return $this->mapper->save($user) && $this->session->storeUser($user);
    }

    protected function checkUsernameUsed($userId, $newUsername)
    {
        if ($userDB = $this->mapper->findOne(
            new User(array("username" => $newUsername))
        )) {
            if ($userDB->id != $userId) {
                return true;
            }
        }
        return false;
    }
}
