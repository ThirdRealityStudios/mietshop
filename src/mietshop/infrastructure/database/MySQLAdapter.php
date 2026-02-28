<?php

namespace src\mietshop\infrastructure\database;

use PDO;
use PDOException;

class MySQLAdapter implements IDatabaseAdapter
{
    private PDO $pdo;

    public function connect(string $host, string $databaseName, string $username, string $password) : void
    {
        $this -> pdo = new PDO("mysql:host={$host};dbname={$databaseName};charset=utf8mb4", $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
    }

    public function fetch(string $sql, array $params = []): array
    {
        // The prepared PDO statement.
        $response = $this -> pdo -> prepare($sql);

        if(sizeof($params) > 0) // there are parameters to consider. SQL fetch is a little more tricky.
        {
            $response -> execute($params);
        }
        else // there is no parameter to consider. So SQL fetch is easier.
        {
            $response -> execute();
        }

        return $response -> fetchAll();
    }

    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute($params);
        return $stmt -> rowCount();
    }

    public function readColumnNames(string $tableName) : array
    {
        // Very rough validity check for the SQL table name
        if(!empty($tableName) && !str_contains($tableName, " "))
        {
            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?";

            $fetchData = $this->fetch($sql, [$tableName]);

            $columnNames = [];

            foreach($fetchData as $row)
            {
                if(strcmp(strtolower($row['COLUMN_NAME']), "id") === 0)
                {
                    continue;
                }

                $columnNames[] = $row['COLUMN_NAME'];
            }

            return $columnNames;
        }

        return [];
    }

    public function readCharacterLimit(string $tableName, string $columnName): int
    {
        // Very rough validity check for the SQL table name and column name
        if(!empty($tableName) && !str_contains($tableName, " ") && !empty($columnName) && !str_contains($columnName, " "))
        {
            $sql = "SELECT character_maximum_length FROM information_schema.columns WHERE table_name = '$tableName' AND column_name = '$columnName'";

            $fetchData = $this->fetch($sql);

            // There should be exactly one row-related dataset returned.
            if(sizeof($fetchData) === 1)
            {
                $firstRowKey = array_keys($fetchData)[0];
                $fetchDataFirstRow = $fetchData[$firstRowKey];

                if(isset($fetchDataFirstRow["character_maximum_length"]) && is_numeric($fetchDataFirstRow["character_maximum_length"]))
                {
                    return $fetchDataFirstRow["character_maximum_length"];
                }
                else
                {
                    // Cannot fetch column information. Technical MySQL database issue (fetched information does not have expected usual data structure).
                    return -1;
                }
            }
            else
            {
                // Cannot fetch column information. Technical MySQL database issue.
                return -1;
            }
        }
        else
        {
            // The given column name is roughly not even close to a valid column name.
            return -1;
        }
    }
}