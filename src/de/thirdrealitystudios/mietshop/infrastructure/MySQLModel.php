<?php

namespace src\de\thirdrealitystudios\mietshop\infrastructure;

use PDO;
use PDOException;
use src\de\thirdrealitystudios\mietshop\infrastructure\interface\IModel;

class MySQLModel implements IModel
{
    /**
     * Setup description before being able to use a database connection:
     * -----------------------------------------------------------------
     *
     * For XAMPP after first installation:
     *
     * 1. open console / Shell via XAMPP interface
     *
     * 2. set your root password with:
     *
     *    mysqladmin --user=root password "myPassword"
     *
     *    OR change an existing root password with:
     *
     *    mysqladmin --user=root --password=myPassword password "myNewPassword"
     *
     * 3. create database for your website with:
     *
     *    mysql --password=myPassword -u root -e "create database myWebsite";
     */

    private string $host = "localhost";

    /**
     * "root" is default user when installed phpMyAdmin.
     *
     * @var string
     */
    private string $user = "root";

    /**
     * "xampp" is default empty database when installed phpMyAdmin.
     *
     * @var string
     */
    private string $dbName = "myWebsite";

    /**
     * Password to connect to the DB.
     *
     * @var string
     */
    private string $password = "myPassword";

    /**
     * @return PDO
     * @throws PDOException
     */
    private function connect(): PDO
    {
        return new PDO("mysql:host=".$this -> host.";dbname=".$this -> dbName, $this -> user, $this -> password);
    }

    /**
     * Executes a SQL statement by preparing the statement and creating a PDO object for each call to this function.
     *
     * Attention!
     *
     * Not suitable for queries, such as "SELECT", because there is no usable array returned!
     * For such cases use "select($column, $from, $where, $additionalSQL)" instead.
     *
     * @param string $sql SQL statement.
     * @return bool true if SQL execution on server was successful, otherwise false.
     * @throws PDOException
     */
    private function perform(string $sql): bool
    {
        $pdo = $this -> connect();

        $prepared = $pdo -> prepare($sql);

        if($prepared !== false)
        {
            $success = $prepared -> execute();

            // Close connection and give other statements the chance to be executed.
            $success &= $prepared -> closeCursor();

            return $success;
        }
        else
        {
            return false;
        }
    }

    /**
     * SELECTs or SHOWs all elements of either a column or property by the where-condition given.
     * Uses PDO::FETCH_ASSOC.
     *
     * @param string $operation SQL operation. Can be either SELECT or SHOW.
     * @param string $target Either SQL column name or property name, e.g. INDEXES (for SHOW).
     * @param string $from SQL table.
     * @param string|null $where SQL condition (not including WHERE, only condition). Use NULL to ignore.
     *
     * @return array|null Either the query results or NULL if there was an DB execution error or an obvious SQL syntax error in your passed parameters.
     * @throws PDOException
     */
    private function query(string $operation, string $target, string $from, string|null $where): ?array
    {
        if((strcmp(strtolower($operation), "select") === 0 || strcmp(strtolower($operation), "show") === 0) && !str_contains($target, " ") && !str_contains($from, " "))
        {
            $queryString = "$operation $target FROM $from".($where === null ? "" : (" WHERE ".$where)).";";

            $pdo = $this -> connect();

            $prepared = $pdo -> prepare($queryString);

            if($prepared !== false && $prepared -> execute())
            {
                $fetchedArray = $prepared -> fetchAll(PDO::FETCH_ASSOC);

                // If the connection could not be closed successfully
                // (although data might be fetched),
                // it is preferred not to return the fetched data in order
                // to let the dev or admin fix the actual closure problem.
                // He will know because NULL is returned only at such moments.
                if($prepared -> closeCursor())
                {
                    return $fetchedArray;
                }
                else
                {
                    return null;
                }
            }
            else
            {
                return null;
            }
        }
        else
        {
            return null;
        }
    }

    /**
     * Selects all elements of a given column by the where-condition given.
     *
     * @param string $column SQL table column.
     * @param $from string SQL table.
     * @param string|null $where SQL condition (not including WHERE, only condition). Use NULL to ignore.
     *
     * @return array|null Either the query results or NULL if the column or table was not found.
     */
    private function select(string $column, string $from, string|null $where): ?array
    {
        return $this -> query("SELECT", $column, $from, $where);
    }

    /**
     * Returns all column names of a table from DB
     * (without the first ID column, as it is known usually).
     *
     * @param string $tableName SQL table name.
     * @return string[]|null Either the column names or NULL (e.g. no results or SQL execution failed).
     */
    private function getColumnNames(string $tableName): ?array
    {
        if(!empty($tableName) && !str_contains($tableName, " "))
        {
            $fetchedArray = $this -> select("DISTINCT COLUMN_NAME", "INFORMATION_SCHEMA.COLUMNS", "TABLE_NAME = '$tableName'");

            // "> 1" because there is also an ID column, among the other columns.
            if(is_array($fetchedArray) && sizeof($fetchedArray) > 1)
            {
                $rows = array_values($fetchedArray);

                if(sizeof($rows) > 0)
                {
                    $columnNames = [];

                    foreach($rows as $row)
                    {
                        if(is_array($row) && sizeof($row) > 0) // maybe is_array(..) will cause a bug if only one value is contained in $row = one string
                        {
                            $columnName = array_values($row)[0];

                            $columnNames[] = $columnName;
                        }
                        else
                        {
                            return null;
                        }
                    }

                    // Cut off the first column "ID" if it exists and directly return.
                    if(sizeof($columnNames) > 1 && strcmp(strtolower($columnNames[0]), "id") === 0)
                    {
                        return array_slice($columnNames, 1, sizeof($columnNames) - 1);
                    }
                    else
                    {
                        return null;
                    }
                }
                else
                {
                    return null;
                }
            }
            else
            {
                return null;
            }
        }
        else
        {
            return null;
        }
    }

    /**
     * @param string $tableName
     * @param string ...$insertionValues
     * @return string|null Returns only NULL if no columns could be found for the given table as string parameter.
     */
    public function genInsertSQL(string $tableName, string ...$insertionValues): ?string
    {
        $insertSQL = 'INSERT INTO '.$tableName.'(';

        $columnNames = $this -> getColumnNames($tableName);

        if(is_array($columnNames) && sizeof($columnNames) > 0)
        {
            for($i = 0; $i < sizeof($columnNames) && $i < sizeof($insertionValues); $i++)
            {
                $columnName = $columnNames[$i];

                if(is_string($columnName) && !empty($columnName))
                {
                    // Add criterias to the current columns SQL (removed later before transmission).
                    // $_SESSION['insert'.$columnName.'Criteria'] = $this -> getInsertionCriterias($tableName, $columnName);

                    if($i < (sizeof($insertionValues) - 1))
                    {
                        $insertSQL .= "$columnName, ";
                    }
                    else
                    {
                        $insertSQL .= "$columnName) VALUES ('";
                    }
                }
                else
                {
                    return null;
                }
            }

            for($i = 0; $i < sizeof($columnNames) && $i < sizeof($insertionValues); $i++)
            {
                $insertionValue = $insertionValues[$i];

                if(is_string($insertionValue) && !empty($insertionValue))
                {
                    if($i < (sizeof($columnNames) - 2) && $i < (sizeof($insertionValues) - 1))
                    {
                        $insertSQL .= ($insertionValue."', '");
                    }
                    else
                    {
                        $insertSQL .= ($insertionValue."') ON DUPLICATE KEY UPDATE ");
                    }
                }
                else
                {
                    return null;
                }
            }

            for($i = 0; $i < sizeof($columnNames) && $i < sizeof($insertionValues); $i++)
            {
                $columnName = $columnNames[$i];

                if(is_string($columnName) && !empty($columnName))
                {
                    $insertSQL .= "$columnName = $columnName";

                    if($i < (sizeof($insertionValues) - 1))
                    {
                        $insertSQL .= ", ";
                    }
                    else
                    {
                        $insertSQL .= ";";
                    }
                }
                else
                {
                    return null;
                }
            }

            return $insertSQL;
        }
        else
        {
            return null;
        }
    }

    /**
     * Creates a new dataset at the given destination with the given value(s).
     *
     * @param string $destination Where to insert the values at.
     * @param string|string[] $value Comma-separated values, e.g. the string "123, '', 'Hello World!', -123.2323" or also just "Hello".
     * @return bool True if the dataset could be created successfully. Otherwise, false (e.g. syntax error or DB error).
     * @throws PDOException
     */
    public function create(mixed $destination, mixed $value): bool
    {
        if(is_string($destination) && strlen($destination) > 0 && !str_contains($destination, " ") && is_string($value))
        {
            return $this -> perform("INSERT INTO $destination VALUES($value)");
        }
        else
        {
            return false;
        }
    }

    /**
     * @param mixed $key
     * @param mixed $source
     * @return mixed
     */
    public function read(mixed $key, mixed $source): mixed
    {
        // TODO: Implement read() method.
    }

    /**
     * @param mixed $source
     * @return array|null
     */
    public function readAll(mixed $source): ?array
    {
        // TODO: Implement readAll() method.
    }

    /**
     * @param mixed $source
     * @return array|null
     */
    public function readKeys(mixed $source): ?array
    {
        // TODO: Implement readKeys() method.
    }

    /**
     * @param mixed $key
     * @param mixed $value
     * @param mixed $destination
     * @return bool
     */
    public function update(mixed $key, mixed $value, mixed $destination): bool
    {
        // TODO: Implement update() method.
    }

    /**
     * @param mixed $key
     * @param mixed $destination
     * @return bool
     */
    public function delete(mixed $key, mixed $destination): bool
    {
        // TODO: Implement delete() method.
    }

    /**
     * @param mixed $destination
     * @return bool
     */
    public function deleteAll(mixed $destination): bool
    {
        // TODO: Implement deleteAll() method.
    }
}