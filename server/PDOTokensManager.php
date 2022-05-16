<?php

class PDOTokensManager {
    private string $serverName;
    private string $userName;
    private string $userPassword;
    private string $databaseName;

    public function __construct($serverName, $userName, $userPassword, $databaseName) {
        $this->serverName = $serverName;
        $this->userName = $userName;
        $this->userPassword = $userPassword;
        $this->databaseName = $databaseName;
    }

    public function readByToken($token) {
        try {
            $connection = new PDO(
                "mysql:host=$this->serverName;dbname=$this->databaseName",
                $this->userName,
                $this->userPassword
            );
            $statement = $connection->prepare(
                "SELECT * FROM authentication_tokens WHERE token = ?");
            $statement->execute([$token]);
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $result = $statement->fetch();

            if ($result == false)
                $result = null;

            $connection = null;
        } catch (PDOException $exception) {
            return null;
        }
        return $result;
    }

    public function createToken() {
        try {
            $connection = new PDO(
                "mysql:host=$this->serverName;dbname=$this->databaseName",
                $this->userName,
                $this->userPassword
            );
            while (true) {
                $generatedToken = bin2hex(random_bytes(20));
                $statement = $connection->prepare(
                    "SELECT * FROM authentication_tokens WHERE token = ?");
                $statement->execute([$generatedToken]);
                $statement->setFetchMode(PDO::FETCH_ASSOC);
                $result = $statement->fetch();
                if ($result == null | $result == false)
                    break;
            }

            $connection->beginTransaction();
            $statement = $connection->prepare(
                "INSERT INTO authentication_tokens(token) VALUES(?)");
            $statement->execute([$generatedToken]);
            $lastInsertId = $connection->lastInsertId();
            $connection->commit();

            $statement = $connection->prepare(
                "SELECT * FROM authentication_tokens WHERE id = ?");
            $statement->execute([$lastInsertId]);
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $result = $statement->fetch();

            if ($result == false)
                $result = null;

            $connection = null;
        } catch (PDOException $exception) {
            return null;
        }
        return $result;
    }
}