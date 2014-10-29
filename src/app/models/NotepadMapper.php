<?php

namespace Notepads\Models;

use Exception;

/**
 * Class NotepadMapper
 * @package Notepads\Models
 */
class NotepadMapper extends Mapper
{
    /**
     * @param Notepad $notepad
     * @return Notepad
     */
    public function findOne(Notepad $notepad)
    {
        $query = $this->buildWhere($notepad);
        $stmt = $this->database->prepare(
            "SELECT * FROM `notepads` WHERE "
            . $query["where"]
        );
        foreach ($query["params"] as $name => $value) {
            $$name = $value;
            $stmt->bindParam($name, $$name);
        }
        if ($stmt->execute() && $result = $stmt->fetchAll(\PDO::FETCH_ASSOC)) {
            $res = $result[0];
            $notepad = new Notepad();
            $notepad->id = $res['id'];
            $notepad->userid = $res['userid'];
            $notepad->name = $res['name'];
            $notepad->text = $res['text'];
            return $notepad;
        }
        return false;
    }

    /**
     * @param Notepad $notepad
     * @return array
     */
    protected function buildWhere(Notepad $notepad)
    {
        $where = "1";
        $params = array();
        if ($notepad->id) {
            $where .= " AND `id` = :id";
            $params[":id"] = $notepad->id;
        }
        if ($notepad->userid) {
            $where .= " AND `userid` = :userid";
            $params[":userid"] = $notepad->userid;
        }
        if ($notepad->name) {
            $where .= " AND `name` LIKE '%:name%'";
            $params[":name"] = $notepad->name;
        }
        if ($notepad->text) {
            $where .= " AND `text` LIKE '%:text%'";
            $params[":text"] = $notepad->text;
        }
        return array("where" => $where, "params" => $params);
    }

    /**
     * @param $user
     * @return array
     */
    public function fetchAll($user)
    {
        $stmt = $this->database->prepare(
            "SELECT * FROM `notepads`
              WHERE `userid` = :userid
              ORDER BY `id` DESC"
        );
        $userId = $user->id;
        $stmt->bindParam(":userid", $userId);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $notepads = array();
        foreach ($result as $row) {
            $notepad = new Notepad();
            $notepad->id = $row['id'];
            $notepad->userid = $row['userid'];
            $notepad->name = $row['name'];
            $notepad->text = $row['text'];
            $notepads[] = $notepad;
        }
        return $notepads;
    }

    /**
     * @param Notepad $notepad
     * @return bool
     */
    public function save(Notepad $notepad)
    {
        $notepadId = $notepad->id;
        $userId = $notepad->userid;
        $name = $notepad->name;
        $text = $notepad->text;
        if ($notepad->id) {
            $stmt = $this->database->prepare(
                "UPDATE `notepads` SET `userid` = :userid,
                  `name` = :name,
                  `text` = :text
                  WHERE `id` = :id"
            );
            $stmt->bindParam(":id", $notepadId);
        } else {
            $stmt = $this->database->prepare(
                "INSERT INTO `notepads` (`userid`, `name`, `text`)
                  VALUES(:userid, :name, :text)"
            );
        }
        $stmt->bindParam(":userid", $userId);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":text", $text);
        return $stmt->execute();
    }

    /**
     * @param Notepad $notepad
     * @return bool
     */
    public function delete(Notepad $notepad)
    {
        $stmt = $this->database->prepare(
            "DELETE FROM `notepads` WHERE `id` = :id"
        );
        $notepadId = $notepad->id;
        $stmt->bindParam(":id", $notepadId);
        return $stmt->execute();
    }
}
