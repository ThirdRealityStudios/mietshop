<?php

namespace src\mietshop\model;

use src\mietshop\contracts\IAccountModel;

class AccountModel implements IAccountModel
{
    public function createAccount(string $email, string $password, int $permissionLevel): bool
    {
        // TODO: Implement createAccount() method.
    }

    public function readAccount(string $email): array
    {
        // TODO: Implement readAccount() method.
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