<?php

namespace src\mietshop\infrastructure\database;

use PDOException;

/**
 * This interface describes the most important functions to establish a connection
 * to your database and use it via SQL statements.
 *
 * Due to efficiency reasons and if PDO used,
 * you might want to save your PDO object for your implementation,
 * once created.
 * Implementations may store the PDO instance in a private property to avoid reconnecting on every query.
 *
 * **Also Remember:**
 *
 * it is generally recommended to save unescaped raw user-input,
 * as it is.
 * But when reading AND outputting the fetched content from your DB,
 * you should use encoders like htmlspecialchars(...) to prevent XSS attacks or such.
 *
 * Also,
 * inside JS code like
 *
 *     <script>var data = "<?= $text ?>";</script>
 *
 * you should use in advance the PHP function
 *
 *     json_encode($text)
 *
 * for the variable $text to prevent malicious JS execution.
 * The attacker could try to save JS code in your database which is then saved later inside your PHP variable,
 * called $text here.
 * Obviously,
 * you could try to escape the JS variable "var data" by putting double-quotes and then write your malicious JS code.
 * json_encode(...) will prevent this by treating everything as a pure and harmless JavaScript string,
 * which it will give you.
 *
 * The same hazard goes for URLs,
 * e.g.
 *
 *     <a href="<?php $myFacebookLink ?>">
 *
 *
 * In this case,
 * you would expect a link to your Facebook webpage.
 * But instead,
 * an attacker could try to save a manipulated "link" inside your database (maybe he found a bug where you don't check links correctly).
 * Now,
 * the hacker could break the link element with something like:
 *
 *     "><script>window.location='malicious-website.com'</script>
 *
 * Hence,
 * ***always use urlencode() for fetched URLs from your database***
 * because it could try to escape the link element to inject harmful HTML or JS code.
 *
 * After using urlencode(...),
 * you will always have **hexadecimal characters for special characters**:
 *
 *     %3E%3Cscript%3Ewindow.location%3D%27malicious-website.com%27%3C%2Fscript%3E
 *
 * This way,
 * at least such attacks are always useless.
 *
 *
 * ***If you do everything this way (especially in your View implementations),
 * you do not restrict database content.
 * This is very important,
 * e.g. for administrators or if you really intend to save executable code.
 * In the end YOU decide where code should be executed.***
 *
 *
 * **Also, below is a short guide on how to set up your database with XAMPP:**
 *
 * 1. open console / Shell via XAMPP GUI
 *
 *
 * 2. first time using XAMPP - set up your root password:
 *
 *
 *        mysqladmin --user=root password "myPassword"
 *
 *
 * 2. yet using XAMPP - optionally change your root password with:
 *
 *
 *        mysqladmin --user=root --password=myPassword password "myNewPassword"
 *
 *
 * 3. create database for your website with:
 *
 *
 *        mysql --password=myPassword -u root -e "create database myWebsite";
 *
 *
 * 4. now you can use this information for the function setCredentials(...) in your implementation.
 */
interface IDatabaseAdapter
{
    /**
     * Creates a database connection.
     * Usually,
     * only one call to this function is necessary for a keep-alive connection to your DB.
     * You can anyway call this function more than one time for a completely new connection (e.g. with different credentials).
     * Uses the charset "utf8mb4" for the connection.
     *
     * The PDO object is used for preparing statements,
     * executing queries,
     * and fetching results.
     * Saves the PDO object in a private variable for re-use internally.
     *
     * Can throw an PDOException.
     *
     * @param string $host Your database host, e.g. "database-0123456789.webspace-host.com"
     * @param string $databaseName Your database name, e.g. "xampp" (usually is if just installed)
     * @param string $username Your username, e.g. "root" or "postgres"
     * @param string $password Your password, e.g. "myPassword123" or "myPassword"
     * @throws PDOException If the database connection fails (e.g., wrong credentials set).
     */
    public function connect(string $host, string $databaseName, string $username, string $password) : void;

    /**
     * Executes a given SQL statement to read from the database (e.g., SELECT).
     * Parameters (if any) are bound to the statement in the order of the question marks (?).
     * Safe against SQL injection since the statement is prepared before execution.
     * PDO automatically detects PHP variable types; you usually do not need to worry about them.
     *
     * Example:
     *
     * $sql = "SELECT * FROM User WHERE Username = ? AND Password = ?";
     *
     * $rows = $this->fetch($sql, [$username, $password]);
     *
     * Notes:
     * - Each row is returned as an **associative array** indexed by column name (PDO::FETCH_ASSOC).
     * - If multiple columns have the same name, only one value per column name is returned.
     *
     * Can throw an PDOException.
     *
     * @param string $sql    SQL statement, optionally containing ? placeholders for parameters.
     * @param array  $params Optional array of parameters to replace the ? placeholders.
     * @return array         Resulting rows as an array of associative arrays.
     * @throws PDOException  If connection fails or SQL execution fails.
     */
    public function fetch(string $sql, array $params = []): array;

    /**
     * Executes a given SQL statement to create, update or delete something in the database (e.g., INSERT).
     * Parameters (if any) are bound to the statement in the order of the question marks (?).
     * Safe against SQL injection since the statement is prepared before execution.
     * PDO automatically detects PHP variable types; you usually do not need to worry about them.
     *
     * Example:
     *
     * $sql = "INSERT INTO User(Username, Password) VALUES(?, ?)";
     *
     * $rows = $this -> execute($sql, [$username, $password]);
     *
     * Can throw an PDOException.
     *
     * @param string $sql    SQL statement, optionally containing ? placeholders for parameters.
     * @param array  $params Optional array of parameters to replace the ? placeholders.
     * @return int The amount of affected rows, e.g. created, updated or deleted.
     * @throws PDOException  If connection fails or SQL execution fails.
     */
    public function execute(string $sql, array $params = []) : int;
}