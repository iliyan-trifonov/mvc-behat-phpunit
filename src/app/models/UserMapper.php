<?php

namespace Notepads\Models;

/**
 * Class UserMapper
 * @package Notepads\Models
 */
class UserMapper extends Mapper
{
    /**
     * @param User $user
     * @return bool|User
     */
    public function findOne(User $user)
    {
        $query = $this->buildWhere($user);
        $stmt = $this->database->prepare(
            "SELECT * FROM `users` WHERE"
            . $query["where"]
        );
        foreach ($query["params"] as $name => $value) {
            $$name = $value;
            $stmt->bindParam($name, $$name);
        }
        if ($stmt->execute()) {
            return $stmt->fetchObject("Notepads\\Models\\User");
        }
        return false;
    }

    /**
     * @param User $user
     * @return string
     */
    protected function buildWhere(User $user)
    {
        $where = " 1";
        $params = array();
        if ($user->id) {
            $where .= " AND `id` = :id";
            $params[":id"] = (int) $user->id;
        }
        if ($user->username) {
            $where .= " AND `username` = :username";
            $params[":username"] = $user->username;
        }
        if ($user->password) {
            $where .= " AND `password` = :password";
            $params[":password"] = $user->password;
        }
        return array("where" => $where, "params" => $params);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function save(User $user)
    {
        $userId = $user->id;
        $username = $user->username;
        $password = $user->password;
        if ($user->id) {
            $stmt = $this->database->prepare(
                "UPDATE `users` SET `username` = :username,
                  `password` = :password
                  WHERE `id` = :id"
            );
            $stmt->bindParam(":id", $userId);
        } else {
            $stmt = $this->database->prepare(
                "INSERT INTO `users` (`username`, `password`)
                  VALUES(:username, :password)"
            );
        }
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $password);
        return $stmt->execute();
    }

    public function delete(User $user)
    {
        $stmt = $this->database->prepare(
            "DELETE FROM `users` WHERE `id` = :id"
        );
        $userId = $user->id;
        $stmt->bindParam(":id", $userId);
        return $stmt->execute();
    }
}
