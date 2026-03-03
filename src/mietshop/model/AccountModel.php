<?php

namespace src\mietshop\model;

use PDOException;
use src\mietshop\contracts\IAccountModel;
use src\mietshop\infrastructure\database\IDatabaseAdapter;

class AccountModel implements IAccountModel
{
    private IDatabaseAdapter $databaseAdapter;

    /**
     * @param IDatabaseAdapter $databaseAdapter
     * @throws PDOException
     */
    public function __construct(IDatabaseAdapter $databaseAdapter)
    {
        $this->databaseAdapter = $databaseAdapter;

        $sql =
        "
            CREATE TABLE IF NOT EXISTS AccountModel
            (
                Email VARCHAR(100) UNIQUE PRIMARY KEY,
                PasswordHash VARCHAR(255) NOT NULL,
                PermissionLevel INT(1) NOT NULL DEFAULT 0
            );
        ";

        $this->databaseAdapter->execute($sql);

        $this->ensureDefaultAdminAccount();
    }

    /**
     * Ensures that there is always a default admin account with permission level 5.
     * This is because the owner could have technical issues or unforeseeable bugs while operating the whole web app.
     * For guaranteed help it is important to check here if exactly one admin account always exists.
     * If there is yet an admin account (with its own password),
     * then nothing will happen here.
     *
     * @return void
     * @throws PDOException
     */
    private function ensureDefaultAdminAccount()
    {
        $sql =
        "
            SELECT COUNT(PermissionLevel) AS Amount FROM AccountModel WHERE PermissionLevel = 5
        ";

        $result = $this->databaseAdapter->fetch($sql);

        if(isset($result[0]["Amount"]) && $result[0]["Amount"] === 0)
        {
            $sql =
            "
                INSERT INTO AccountModel(Email, PasswordHash, PermissionLevel) VALUES('admin@admin.admin', ?, 5)
            ";

            $passwordHashed = password_hash("Admin2026!", PASSWORD_DEFAULT);

            $this->databaseAdapter->execute($sql, [$passwordHashed]);
        }
    }

    public function createAccount(string $email, string $password, int $permissionLevel): bool
    {
        if($permissionLevel >= 0 && $permissionLevel <= 5)
        {
            $emailFormatValidationResult = filter_var($email, FILTER_VALIDATE_EMAIL);

            if(is_bool($emailFormatValidationResult) && $emailFormatValidationResult)
            {
                $passwordHashed = password_hash($password, PASSWORD_DEFAULT);

                if(is_string($passwordHashed))
                {
                    $sql =
                        "
                    INSERT INTO AccountModel(Email, PasswordHash, PermissionLevel) VALUES(?, ?, ?)
                ";

                    try
                    {
                        return $this->databaseAdapter->execute($sql, [$email, $passwordHashed, $permissionLevel]) === 1;
                    }
                    catch(PDOException $e)
                    {
                        return false;
                    }
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    public function readAccount(string $email): array
    {
        $sql =
        "
            SELECT * FROM AccountModel WHERE Email = ?
        ";

        try
        {
            $fetchData = $this->databaseAdapter->fetch($sql, [$email]);

            if(sizeof($fetchData) === 1) // there should only be exactly one account with that email address.
            {
                if(isset($fetchData[0]["Email"]) && isset($fetchData[0]["PasswordHash"]) && isset($fetchData[0]["PermissionLevel"]))
                {
                    if(is_string($fetchData[0]["Email"]) && is_string($fetchData[0]["PasswordHash"]) && is_numeric($fetchData[0]["PermissionLevel"]))
                    {
                        return [$fetchData[0]["Email"], $fetchData[0]["PasswordHash"], $fetchData[0]["PermissionLevel"]];
                    }
                    else
                    {
                        return [];
                    }
                }
                else
                {
                    return [];
                }
            }
            else
            {
                return [];
            }
        }
        catch(PDOException $e)
        {
            return [];
        }
    }

    public function readPermissionLevel(string $email): int
    {
        $sql =
        "
            SELECT PermissionLevel FROM AccountModel WHERE Email = ?
        ";

        try
        {
            $result = $this->databaseAdapter->fetch($sql, [$email]);

            if(sizeof($result) === 1 && isset($result[0]["PermissionLevel"]) && is_numeric($result[0]["PermissionLevel"]))
            {
                return $result[0]["PermissionLevel"];
            }
            else
            {
                return -1;
            }
        }
        catch(PDOException $e)
        {
            return -1;
        }
    }

    public function updatePassword(string $email, string $oldPassword, string $newPassword): bool
    {
        // Check permission for password change

        try
        {
            $sql =
            "
                SELECT PasswordHash FROM AccountModel WHERE Email = ?
            ";

            $fetchData = $this->databaseAdapter->fetch($sql, [$email]);

            if(isset($fetchData[0]["PasswordHash"]) && is_string($fetchData[0]["PasswordHash"]))
            {
                $currentPasswordHashed = $fetchData[0]["PasswordHash"];

                if(password_verify($oldPassword, $currentPasswordHashed))
                {
                    // Now allowed to change password.

                    $sql =
                    "
                        UPDATE AccountModel SET PasswordHash = ? WHERE Email = ?
                    ";

                    $passwordNewHashed = password_hash($newPassword, PASSWORD_DEFAULT);

                    return $this->databaseAdapter->execute($sql, [$passwordNewHashed, $email]) === 1;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        catch(PDOException $e)
        {
            return false;
        }
    }

    public function updateEmail(string $currentPassword, string $currentEmail, string $newEmail): bool
    {
        // First check permission

        $sql =
        "
            SELECT PasswordHash FROM AccountModel WHERE Email = ?
        ";

        try
        {
            $result = $this->databaseAdapter->fetch($sql, [$currentEmail]);

            if(isset($result[0]["PasswordHash"]) && is_string($result[0]["PasswordHash"]))
            {
                $passwordHash = $result[0]["PasswordHash"];

                if(password_verify($currentPassword, $passwordHash))
                {
                    // Password verified. Now can change email.

                    $sql =
                    "
                        UPDATE AccountModel SET Email = ? WHERE Email = ?
                    ";

                    return $this->databaseAdapter->execute($sql, [$newEmail, $currentEmail]) === 1;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        catch(PDOException $e)
        {
            return false;
        }
    }

    public function updatePermissionLevel(string $supervisorEmail, string $supervisorPassword, string $email, int $permissionLevel): bool
    {
        $sql =
        "
            SELECT PasswordHash FROM AccountModel WHERE Email = ?
        ";

        try
        {
            $result = $this->databaseAdapter->fetch($sql, [$supervisorEmail]);

            if(isset($result[0]["PasswordHash"]) && is_string($result[0]["PasswordHash"]))
            {
                $supervisorPasswordHash = $result[0]["PasswordHash"];

                if(password_verify($supervisorPassword, $supervisorPasswordHash))
                {
                    // Supervisor access was verified to change the permission level of an account.

                    $supervisorPermissionLevel = $this->readPermissionLevel($supervisorEmail);

                    // Make sure the supervisor cannot give a higher permission level than his own.
                    if($supervisorPermissionLevel > -1 && $permissionLevel <= $supervisorPermissionLevel)
                    {
                        // Make sure with "PermissionLevel < 5" that no supervisor can degrade admin accounts (also for safety reasons).
                        // Imagine the website buyer does not want to use an owner account but an admin account.
                        // In this case, it would be a security risk if an owner could degrade an admin account.
                        $sql =
                        "
                            UPDATE AccountModel SET PermissionLevel = ? WHERE Email = ? AND PermissionLevel < 5
                        ";

                        return $this->databaseAdapter->execute($sql, [$permissionLevel, $email]) === 1;
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        catch(PDOException $e)
        {
            return false;
        }

    }

    public function deleteAccount(string $email, string $password): bool
    {
        $sql =
        "
            SELECT PermissionLevel FROM AccountModel WHERE Email = ?
        ";

        try
        {
            $result = $this->databaseAdapter->fetch($sql, [$email]);

            // Make sure there is no admin who accidentally deletes his account
            // (it is not dramatic because it gets automatically re-created - but then it has reset credentials which is e.g. a security risk)
            if(isset($result[0]["PermissionLevel"]) && ($result[0]["PermissionLevel"] < 5))
            {
                $sql =
                "
                    SELECT PasswordHash FROM AccountModel WHERE Email = ?
                ";

                $result = $this->databaseAdapter->fetch($sql, [$email]);

                if(isset($result[0]["PasswordHash"]) && is_string($result[0]["PasswordHash"]))
                {
                    $passwordHashed = $result[0]["PasswordHash"];

                    if(password_verify($password, $passwordHashed))
                    {
                        // User could be verified successfully.
                        // Now the user can delete his account.

                        $sql =
                        "
                            DELETE FROM AccountModel WHERE Email = ?
                        ";

                        return $this->databaseAdapter->execute($sql, [$email]) === 1;
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        catch(PDOException $e)
        {
            return false;
        }
    }

    public function deleteAccountBySupervisor(string $supervisorEmail, string $supervisorPassword, string $email): bool
    {
        // Make sure a supervisor does not want to delete his own account.
        if(strcmp($supervisorEmail, $email) !== 0)
        {
            $sql =
            "
                SELECT PasswordHash FROM AccountModel WHERE Email = ? AND PermissionLevel >= 4
            ";

            try
            {
                $result = $this->databaseAdapter->fetch($sql, [$supervisorEmail]);

                if(isset($result[0]["PasswordHash"]) && is_string($result[0]["PasswordHash"]))
                {
                    $supervisorPasswordHash = $result[0]["PasswordHash"];

                    // Make sure the supervisor can verify himself to delete the other account.
                    if(password_verify($supervisorPassword, $supervisorPasswordHash))
                    {
                        $supervisorPermissionLevel = $this->readPermissionLevel($supervisorEmail);
                        $userPermissionLevel = $this->readPermissionLevel($email);

                        // Make sure the supervisor always has a higher permission level than the account to delete (for safety reasons)
                        if($supervisorPermissionLevel >-1 && $userPermissionLevel > -1 && $supervisorPermissionLevel > $userPermissionLevel)
                        {
                            $sql =
                            "
                                DELETE FROM AccountModel WHERE Email = ?
                            ";

                            return $this->databaseAdapter->execute($sql, [$email]) === 1;
                        }
                        else
                        {
                            return false;
                        }
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    return false;
                }
            }
            catch(PDOException $e)
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
}