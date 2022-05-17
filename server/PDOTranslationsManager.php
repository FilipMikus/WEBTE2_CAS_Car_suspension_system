<?php

class PDOTranslationsManager {
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

    public function readAllByLanguageCode ($languageCode) {
        try {
            $connection = new PDO(
                "mysql:host=$this->serverName;dbname=$this->databaseName",
                $this->userName,
                $this->userPassword
            );
            $statement = $connection->prepare(
                "SELECT t1.id as id, t1.element_id as element_id, t2.code as language_code, t3.translation as translation
                    FROM translations t3
                    JOIN languages t2
                    ON t3.language_id = t2.id
                    JOIN elements t1
                    ON t3.element_id = t1.id
                    WHERE t2.code LIKE ?"
            );
            $statement->execute([$languageCode]);
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
}