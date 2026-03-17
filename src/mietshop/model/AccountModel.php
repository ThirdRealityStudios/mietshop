<?php

namespace src\mietshop\model;

use PDOException;
use src\mietshop\contracts\IAccountModel;
use src\mietshop\contracts\IUserInputModel;
use src\mietshop\infrastructure\database\IDatabaseAdapter;

class AccountModel implements IAccountModel
{
    private IUserInputModel $userInputModel;
    private IDatabaseAdapter $databaseAdapter;

    /**
     * @param IDatabaseAdapter $databaseAdapter
     * @throws PDOException
     */
    public function __construct(IUserInputModel $userInputModel, IDatabaseAdapter $databaseAdapter)
    {
        $this->userInputModel = $userInputModel;
        $this->databaseAdapter = $databaseAdapter;

        $sql =
        "
            CREATE TABLE IF NOT EXISTS AccountModel
            (
                Email VARCHAR(100) UNIQUE PRIMARY KEY,
                PasswordHash VARCHAR(255) NOT NULL,
                PermissionLevel INT(1) NOT NULL DEFAULT ?
            );
        ";

        // Create the account table if necessary and always give by default the lowest permission role.
        $this->databaseAdapter->execute($sql, [min(array_keys(IAccountModel::PERMISSION_ROLE))]);

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
        $sql = "SELECT COUNT(PermissionLevel) AS Amount FROM AccountModel WHERE PermissionLevel = ?";

        $maxPermissionLevel = max(array_keys(IAccountModel::PERMISSION_ROLE)); // Determine admin level (highest) because roles can be changed.

        $result = $this->databaseAdapter->fetch($sql, [$maxPermissionLevel]);

        if(isset($result[0]["Amount"]) && $result[0]["Amount"] === 0)
        {
            $sql =
            "
                INSERT INTO AccountModel(Email, PasswordHash, PermissionLevel) VALUES('janek.piekarski.postfach@gmail.com', ?, ?)
            ";

            $passwordHashed = password_hash("Admin2026!", PASSWORD_DEFAULT);

            $this->databaseAdapter->execute($sql, [$passwordHashed, $maxPermissionLevel]);
        }
    }

    public function createAccount(string $supervisorEmail, string $supervisorPassword, string $email, string $password, int $permissionLevel) : int
    {
        $lowerCaseSupervisorEmail = strtolower($supervisorEmail);
        $lowerCaseEmail = strtolower($email);

        $supervisorAccountData = $this->readAccount($lowerCaseSupervisorEmail);

        // Does the account exist?
        if(!empty($supervisorAccountData))
        {
            // Can the person verify the action (as a supervisor or does he know the credentials at least) ?
            if(password_verify($supervisorPassword, $supervisorAccountData["PasswordHash"]))
            {
                $accountPermissionLevel = $this->readPermissionLevel($lowerCaseSupervisorEmail);

                // Check if the given permission level exists
                if($this->isSupervisor($lowerCaseSupervisorEmail) && $accountPermissionLevel >= $permissionLevel)
                {
                    if($this->userInputModel->isValidEmail($lowerCaseEmail))
                    {
                        if(strlen($password) > 0)
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
                                    return $this->databaseAdapter->execute($sql, [$lowerCaseEmail, $passwordHashed, $permissionLevel]) === 1 ? 1 : -4;
                                }
                                catch(PDOException $e)
                                {
                                    // Execution failed
                                    return -4;
                                }
                            }
                            else
                            {
                                // No valid Password hash was processed (execution error)
                                return -4;
                            }
                        }
                        else
                        {
                            return -2;
                        }
                    }
                    else
                    {
                        // No valid email provided
                        return -1;
                    }
                }
                else
                {
                    // Cannot manage accounts! (missing permissions)
                    return -5;
                }
            }
            else
            {
                // Could not verify the action (most likely supervisor password incorrect)
                return -3;
            }
        }
        else
        {
            // Account does not exist (most likely) or execution error
            return -4;
        }
    }

    public function readAccount(string $email): array
    {
        $lowerCaseEmail = strtolower($email);

        $sql =
        "
            SELECT * FROM AccountModel WHERE Email = ?
        ";

        try
        {
            $fetchData = $this->databaseAdapter->fetch($sql, [$lowerCaseEmail]);

            if(sizeof($fetchData) === 1) // there should only be exactly one account with that email address.
            {
                if(isset($fetchData[0]["Email"]) && isset($fetchData[0]["PasswordHash"]) && isset($fetchData[0]["PermissionLevel"]))
                {
                    $datasetEmail = $fetchData[0]["Email"];
                    $datasetPasswordHash = $fetchData[0]["PasswordHash"];
                    $datasetPermissionLevel = $fetchData[0]["PermissionLevel"];

                    if(is_string($datasetEmail) && is_string($datasetPasswordHash) && is_numeric($datasetPermissionLevel))
                    {
                        return ["Email" => $datasetEmail, "PasswordHash" => $datasetPasswordHash, "PermissionLevel" => $datasetPermissionLevel];
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

    public function readAccounts(int $page) : array
    {
        $accountsPerPage = IAccountModel::ACCOUNTS_PER_PAGE;
        $pageCalculated = $page * IAccountModel::ACCOUNTS_PER_PAGE;

        $sql =
        "
            SELECT * FROM AccountModel ORDER BY Email ASC LIMIT $accountsPerPage OFFSET $pageCalculated
        ";

        try
        {
            return $this->databaseAdapter->fetch($sql);
        }
        catch(PDOException $e)
        {
            return [];
        }
    }

    public function readAmountOfPages() : int
    {
        $sql =
        "
            SELECT COUNT(*) AS DatasetAmount FROM AccountModel
        ";

        try
        {
            $fetchData = $this->databaseAdapter->fetch($sql);

            if(isset($fetchData[0]["DatasetAmount"]) && is_int($fetchData[0]["DatasetAmount"]))
            {
                $accountAmount = $fetchData[0]["DatasetAmount"];

                $pagesAbsolute = (int) ($accountAmount / IAccountModel::ACCOUNTS_PER_PAGE);
                $pagesFloatingPoint = (float) $accountAmount / IAccountModel::ACCOUNTS_PER_PAGE;

                if(((float) $pagesAbsolute) === $pagesFloatingPoint) // You can evenly determine the amount of pages in both cases (which means there are no partial pages left to consider at the end)
                {
                    $finalPagesAmount = $pagesAbsolute;
                }
                else
                {
                    // One more pages to consider to allow to load because there is a partial account page at the end.
                    $finalPagesAmount = $pagesAbsolute + 1;
                }

                return $finalPagesAmount;
            }
            else
            {
                return 0;
            }
        }
        catch(PDOException $e)
        {
            return 0;
        }
    }

    public function readPermissionLevel(string $email): int
    {
        $lowerCaseEmail = strtolower($email);

        $sql =
        "
            SELECT PermissionLevel FROM AccountModel WHERE Email = ?
        ";

        try
        {
            $result = $this->databaseAdapter->fetch($sql, [$lowerCaseEmail]);

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
        $lowerCaseEmail = strtolower($email);

        try
        {
            $sql =
            "
                SELECT PasswordHash FROM AccountModel WHERE Email = ?
            ";

            $fetchData = $this->databaseAdapter->fetch($sql, [$lowerCaseEmail]);

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

                    return $this->databaseAdapter->execute($sql, [$passwordNewHashed, $lowerCaseEmail]) === 1;
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

        $lowerCaseEmail = strtolower($currentEmail);
        $lowerCaseNewMail = strtolower($newEmail);

        $sql =
        "
            SELECT PasswordHash FROM AccountModel WHERE Email = ?
        ";

        try
        {
            $result = $this->databaseAdapter->fetch($sql, [$lowerCaseEmail]);

            if(isset($result[0]["PasswordHash"]) && is_string($result[0]["PasswordHash"]))
            {
                $passwordHash = $result[0]["PasswordHash"];

                if(password_verify($currentPassword, $passwordHash))
                {
                    // Password verified. Now can change email.

                    if(strcmp($lowerCaseEmail, $lowerCaseNewMail) === 0) // No update needed (remains the same)
                    {
                        return true;
                    }
                    else
                    {
                        if($this->userInputModel->isValidEmail($lowerCaseNewMail))
                        {
                            $sql =
                            "
                                UPDATE AccountModel SET Email = ? WHERE Email = ?
                            ";

                            return $this->databaseAdapter->execute($sql, [$lowerCaseNewMail, $lowerCaseEmail]) === 1;
                        }
                        else
                        {
                            return false;
                        }
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

    public function updatePermissionLevel(string $supervisorEmail, string $supervisorPassword, string $email, int $newPermissionLevel): int
    {
        $lowerCaseEmail = strtolower($email);
        $lowerCaseSupervisorEmail = strtolower($supervisorEmail);

        $sql =
        "
            SELECT PasswordHash FROM AccountModel WHERE Email = ?
        ";

        try
        {
            $result = $this->databaseAdapter->fetch($sql, [$lowerCaseSupervisorEmail]);

            if(!empty($result))
            {
                if(isset($result[0]["PasswordHash"]) && is_string($result[0]["PasswordHash"]))
                {
                    $supervisorPasswordHash = $result[0]["PasswordHash"];

                    if(password_verify($supervisorPassword, $supervisorPasswordHash))
                    {
                        // Supervisor access was verified to change the permission level of an account.

                        $supervisorCurrentPermissionLevel = $this->readPermissionLevel($lowerCaseSupervisorEmail);
                        $targetAccountCurrentPermissionLevel = $this->readPermissionLevel($lowerCaseEmail);

                        if($targetAccountCurrentPermissionLevel > -1)
                        {
                            // Make sure the user is generally permitted (a verified supervisor) and cannot give a higher permission level than his own.
                            // Also make sure you cannot modify ranks of accounts with the same permission level.
                            $canManageAccounts = $this->isSupervisor($lowerCaseSupervisorEmail);
                            $isNewPermissionLevelOK = $newPermissionLevel <= $supervisorCurrentPermissionLevel;
                            $isTargetAccountLowerRank = $targetAccountCurrentPermissionLevel < $supervisorCurrentPermissionLevel;

                            if($canManageAccounts && $isNewPermissionLevelOK && $isTargetAccountLowerRank)
                            {
                                $adminPermissionLevel = max(array_keys(IAccountModel::PERMISSION_ROLE));

                                // Make sure with "PermissionLevel < IAccountModel::PERMISSION_ROLE" that no supervisor can degrade admin accounts (also for safety reasons).
                                // Imagine the website buyer does not want to use an owner account but an admin account.
                                // In this case, it would be a security risk if an owner could degrade an admin account.
                                $sql =
                                "
                                    UPDATE AccountModel SET PermissionLevel = ? WHERE Email = ? AND PermissionLevel < ?
                                ";

                                return $this->databaseAdapter->execute($sql, [$newPermissionLevel, $lowerCaseEmail, $adminPermissionLevel]) === 1 ? 1 : 1;
                            }
                            else
                            {
                                // Insufficient permissions.
                                return -5;
                            }
                        }
                        else
                        {
                            // Account most likely does not exist (or general execution error)
                            return -2;
                        }
                    }
                    else
                    {
                        // Could not verify the action (most likely supervisor password incorrect)
                        return -3;
                    }
                }
                else
                {
                    // No valid Password hash was processed (execution error)
                    return -4;
                }
            }
            else
            {
                // Account does not exist (most likely)
                return -2;
            }
        }
        catch(PDOException $e)
        {
            // Account does not exist (most likely) or execution error
            return -4;
        }
    }

    public function deleteAccount(string $email, string $password): bool
    {
        $lowerCaseEmail = strtolower($email);

        $sql =
        "
            SELECT PermissionLevel FROM AccountModel WHERE Email = ?
        ";

        try
        {
            $result = $this->databaseAdapter->fetch($sql, [$lowerCaseEmail]);

            $adminPermissionLevel = max(array_keys(IAccountModel::PERMISSION_ROLE));

            // Make sure there is no admin who accidentally deletes his account
            // (it is not dramatic because it gets automatically re-created - but then it has reset credentials which is e.g. a security risk)
            if(isset($result[0]["PermissionLevel"]) && ($result[0]["PermissionLevel"] < $adminPermissionLevel))
            {
                $sql =
                "
                    SELECT PasswordHash FROM AccountModel WHERE Email = ?
                ";

                $result = $this->databaseAdapter->fetch($sql, [$lowerCaseEmail]);

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

                        return $this->databaseAdapter->execute($sql, [$lowerCaseEmail]) === 1;
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

    public function deleteAccountBySupervisor(string $supervisorEmail, string $supervisorPassword, string $email) : int
    {
        $lowerCaseEmail = strtolower($email);
        $lowerCaseSupervisorEmail = strtolower($supervisorEmail);

        // Make sure a supervisor does not want to delete his own account.
        if(strcmp($lowerCaseSupervisorEmail, $lowerCaseEmail) !== 0)
        {
            $sql =
            "
                SELECT PasswordHash FROM AccountModel WHERE Email = ? AND PermissionLevel >= ?
            ";

            $ownerPermissionLevel = max(array_keys(IAccountModel::PERMISSION_ROLE))-1;

            try
            {
                $result = $this->databaseAdapter->fetch($sql, [$lowerCaseSupervisorEmail, $ownerPermissionLevel]);

                if(isset($result[0]["PasswordHash"]) && is_string($result[0]["PasswordHash"])) // password of supervisor can be read, so the action is generally granted (still need to check if the user has the correct password below).
                {
                    $supervisorPasswordHash = $result[0]["PasswordHash"];

                    // Make sure the supervisor can verify himself to delete the other account.
                    if(password_verify($supervisorPassword, $supervisorPasswordHash)) // the deletion action is now granted, judging by the supervisor account itself (access verification and permission level)
                    {
                        $supervisorPermissionLevel = $this->readPermissionLevel($lowerCaseSupervisorEmail);
                        $userPermissionLevel = $this->readPermissionLevel($lowerCaseEmail);

                        if($userPermissionLevel > -1) // Does the account exist with that email?
                        {
                            // Make sure the supervisor always has a higher permission level than the account to delete (for safety reasons)
                            if($supervisorPermissionLevel > $userPermissionLevel)
                            {
                                $sql =
                                "
                                    DELETE FROM AccountModel WHERE Email = ?
                                ";

                                return $this->databaseAdapter->execute($sql, [$lowerCaseEmail]) === 1 ? 1 : -1;
                            }
                            else
                            {
                                return -5; // you are not allowed to delete the given user (you need a higher rank).
                            }
                        }
                        else
                        {
                            return -1; // Email-related account not found, so the email is incorrect
                        }
                    }
                    else
                    {
                        return -3; // you cannot verify the credentials of the supervisor for deletion.
                    }
                }
                else
                {
                    return -5; // The performing user is no supervisor (because the user is not at least an owner or even admin). Hence, action was denied.
                }
            }
            catch(PDOException $e)
            {
                return -5; // The supervisor is actually no supervisor because the permission level is not even an owner or even admin. Hence, action denied.
            }
        }
        else
        {
            return -1; // A supervisor cannot delete his own account (at least not with this function)
        }
    }

    public function isSupervisor(string $email) : bool
    {
        $lowerCaseEmail = strtolower($email);

        $ownerPermissionLevel = max(array_keys(IAccountModel::PERMISSION_ROLE))-1;

        return $this->readPermissionLevel($lowerCaseEmail) >= $ownerPermissionLevel;
    }
}