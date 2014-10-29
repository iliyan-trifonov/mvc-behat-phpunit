<?php

namespace Notepads\Models;

/**
 * Class NotepadService
 * @package Notepads\Models
 */
class NotepadService
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
    public function getNotepads()
    {
        $user = $this->session->getUser();
        return $this->mapper->fetchAll($user);
    }

    /**
     * @param Notepad $notepad
     * @throws \Exception
     * @return mixed
     */
    public function findOne(Notepad $notepad)
    {
        if (!$notepad->id && !$notepad->userid && !$notepad->text) {
            $this->addError("Not enough data!");
            return false;
        }
        return $this->mapper->findOne($notepad);
    }

    /**
     * @param Notepad $notepad
     * @return bool
     */
    public function save(Notepad $notepad)
    {
        $notepad->name = $this->sanitize($notepad->name);
        $notepad->text = $this->sanitize($notepad->text);
        if (!$notepad->valid()) {
            $this->addError("Invalid Notepad params!");
            return false;
        }
        return $this->mapper->save($notepad);
    }

    /**
     * @param $text
     * @return mixed
     */
    protected function sanitize($text)
    {
        return filter_var(trim($text), FILTER_SANITIZE_STRING);
    }

    /**
     * @param $notepadId
     * @return mixed
     */
    public function delete($notepadId)
    {
        try {
            $notepad = new Notepad(array("id" => $notepadId));
        } catch (\InvalidArgumentException $exc) {
            $this->addError($exc->getMessage());
            return false;
        }
        return $this->mapper->delete($notepad);
    }
}
