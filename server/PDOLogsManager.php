<?php

class PDOLogsManager {
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

    public function readAllLogs() {
        try {
            $connection = new PDO(
                "mysql:host=$this->serverName;dbname=$this->databaseName",
                $this->userName,
                $this->userPassword
            );
            $statement = $connection->prepare(
                "SELECT * FROM cas_logs");
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $result = $statement->fetchAll();

            if ($result == false)
                $result = null;

            $connection = null;
        } catch (PDOException $exception) {
            return null;
        }
        return $result;
    }

    public function createLog($command, $status) {
        try {
            $connection = new PDO(
                "mysql:host=$this->serverName;dbname=$this->databaseName",
                $this->userName,
                $this->userPassword
            );

            $connection->beginTransaction();
            $statement = $connection->prepare(
                "INSERT INTO cas_logs(command, status) VALUES(?, ?)");
            $statement->execute([$command, $status]);
            $lastInsertId = $connection->lastInsertId();
            $connection->commit();

            $statement = $connection->prepare(
                "SELECT * FROM cas_logs WHERE id = ?");
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