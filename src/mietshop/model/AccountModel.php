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
            CREATE IF NOT EXISTS AccountModel
            (
                Email VARCHAR(100) UNIQUE PRIMARY_KEY NOT NULL,
                PasswordHash VARCHAR(255) NOT NULL,
                PermissionLevel INT(2) NOT NULL DEFAULT '0'
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
            SELECT COUNT(PermissionLevel) FROM AccountModel WHERE PermissionLevel = 5
        ";

        // TODO Check if there is at least one admin account. If so, then create one.. below..

        $sql =
        "
            INSERT IGNORE INTO AccountModel(Email, PasswordHash, PermissionLevel) VALUES('admin@admin.admin', ?, 5)
        ";

        $passwordHashed = password_hash("Admin2026!", PASSWORD_DEFAULT);

        $this->databaseAdapter->execute($sql, [$passwordHashed]);
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

    public function updatePassword(string $email, string $oldPassword, string $newPassword): bool
    {
        // TODO: Implement updatePassword() method.
    }

    public function updateEmail(string $currentPassword, string $currentEmail, string $newEmail): bool
    {
        // TODO: Implement updateEmail() method.
    }

    public function updatePermissionLevel(string $supervisorEmail, string $supervisorPassword, string $email, int $permissionLevel): bool
    {
        // TODO: Implement updatePermissionLevel() method.
    }

    public function deleteAccount(string $email, string $password): bool
    {
        // TODO: Implement deleteAccount() method.
    }

    public function deleteAccountBySupervisor(string $supervisorEmail, string $supervisorPassword, string $email): bool
    {
        // TODO: Implement deleteAccountBySupervisor() method.
    }
}