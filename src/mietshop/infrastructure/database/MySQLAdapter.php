<?php

namespace src\mietshop\infrastructure\database;

use PDO;

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
}