<?php

namespace src\mietshop\infrastructure\database;

use PDO, PDOException;

class MySQLAdapter implements IDatabaseAdapter
{
    private string $host;
    private string $databaseName;
    private string $username;
    private string $password;

    public function setCredentials(string $host, string $databaseName, string $username, string $password): void
    {
        $this->host = $host;
        $this->databaseName = $databaseName;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Everything as described in the interface.
     *
     * @return PDO
     * @throws PDOException
     */
    public function connect(): PDO
    {
        return new PDO("mysql:host=$this->host;dbname=$this->databaseName", $this->username, $this->password);
    }

    /**
     * Everything as described in the interface.
     *
     * @param string $sql
     * @param array  $params
     * @return array
     * @throws PDOException
     */
    public function fetch(string $sql, array $params = []): array
    {
        // TODO: Implement fetch() method.
        return [];
    }

    /**
     * Everything as described in the interface.
     *
     * @param string $sql
     * @param array  $params
     * @return int
     * @throws PDOException
     */
    public function execute(string $sql, array $params = []): int
    {
        // TODO: Implement execute() method.
        return 0;
    }
}