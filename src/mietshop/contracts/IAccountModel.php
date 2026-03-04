<?php

namespace src\mietshop\contracts;

/**
 * This interface describes the behavior of how accounts should be managed.
 *
 * The email of an account replaces the need of a numerical ID for every account (recommended).
 *
 * There are supervisors (the owner and admin) who can give permission levels to users.
 *
 * The following permission levels do exist:
 * -----------------------------------------
 *
 * - 0 is guest level (no account permissions - like watching the website without account)
 *
 * - 1 is moderator level (create, edit or delete articles but NO feeds)
 *
 * - 2 is supporter level (create, edit or delete feeds and articles)
 *
 * - 3 is owner level (manage all your website content, but no database management tools - for safety reasons)
 *
 * - 4 is root / admin permission (manage all website content and database management tools).
 */
interface IAccountModel
{
    public const ROLE_GUEST = 0;
    public const ROLE_MODERATOR = 1;
    public const ROLE_SUPPORTER = 2;
    public const ROLE_OWNER = 3;
    public const ROLE_ADMIN = 4;

    /**
     * Creates an account with the email and password given.
     * Also,
     * a permission level is expected:
     *
     * 0 is guest level (no account permissions - like watching the website without account)
     *
     * 1 is moderator level (create, edit or delete articles but NO feeds)
     *
     * 2 is supporter level (create, edit or delete feeds and articles)
     *
     * 3 is owner level (manage all your website content, but no database management tools - for safety reasons)
     *
     * 4 is root / admin permission (manage all website content and database management tools).
     *
     * @param string $email Email used for registration.
     * @param string $password Password in clear-text used for registration.
     * @param int $permissionLevel Permission-level as described.
     * @return bool True if account successfully created. Otherwise, false (technical issues or wrong user-input)
     */
    public function createAccount(string $email, string $password, int $permissionLevel) : bool;

    /**
     * Returns the dataset of the account as an associative array with that given e-mail address.
     * If there is no account with that e-mail address,
     * then an empty array is returned.
     *
     * The associative array looks like this:
     *
     * [
     *
     *  "Email" => "mymail@web.com",
     *
     *  "Password" => "1fe12as9129fd129sadk123",
     *
     *  "PermissionLevel" => 0
     *
     * ]
     *
     * @param string $email Email address used to get the account data.
     * @return array Account dataset as an associative array. Empty array if fetching fails technically or account does not exist.
     */
    public function readAccount(string $email) : array;

    /**
     * Returns the permission level of an (existing) account.
     * If the account does not exist (mostly) or if execution fails generally,
     * then -1 is returned.
     *
     * @param string $email Email of the account to get permission level from.
     * @return int The permission level (0 to 5) or -1 if the given account does not exist (mostly) or if there was an execution error.
     */
    public function readPermissionLevel(string $email) : int;

    /**
     * Updates the password of a given account with the email address given.
     * Expects the current password in clear-text and the new password in clear-text.
     * If the given password matches the current password stored,
     * then the new password will be used for the account.
     * The new password will then be hashed safely,
     * so it is not stored in clear-text later on the server.
     *
     * @param string $email Email address of the account to change the password for.
     * @param string $oldPassword Old password in clear-text.
     * @param string $newPassword New password in clear-text to set.
     * @return bool True if password could be set successfully. Otherwise, false (mostly when password verification fails or for technical reasons)
     */
    public function updatePassword(string $email, string $oldPassword, string $newPassword) : bool;

    /**
     * Updates the current e-mail address.
     * But only if there is an account with that given e-mail address and if the given password could be verified.
     *
     * @param string $currentPassword Current password in clear-text.
     * @param string $currentEmail Current e-mail address of your account.
     * @param string $newEmail New e-mail address to set if password is correct.
     * @return bool True if the e-mail could be changed. Otherise, false (mostly when password verification fails or for technical reasons).
     */
    public function updateEmail(string $currentPassword, string $currentEmail, string $newEmail) : bool;

    /**
     * Updates the permission level of a given account
     * if the credentials of a supervisor registered are correct.
     * A supervisor can be an owner or admin and can only give
     * his own permission level as a maximum to another user.
     *
     * @param string $supervisorEmail Email address of that supervisor.
     * @param string $supervisorPassword Clear-text password of that supervisor.
     * @param string $email Email address of the account to change the permission level for.
     * @param int $permissionLevel Permission level (numerical, at least 0 - maximum level is the supervisor level 4 or 5)
     * @return bool True if permission level could be set successfully. Otherwise, false (mostly if supervisor credentials are wrong - but can also be a wrong permission level or execution error)
     */
    public function updatePermissionLevel(string $supervisorEmail, string $supervisorPassword, string $email, int $permissionLevel) : bool;

    /**
     * Deletes an account,
     * linked to the email given.
     * The deletion is successful when the given password matches.
     *
     * @param string $email Email of that account.
     * @param string $password Clear-text password of that account.
     * @return bool True if the account could be deleted successfully. Otherwise, false (mostly if the given password is incorrect - but can also be an execution error).
     */
    public function deleteAccount(string $email, string $password) : bool;

    /**
     * Deletes an account,
     * linked to the email given.
     * The deletion is successful when the supervisor credentials get verified.
     * It is not possible that a supervisor deletes his own account (for safety reasons).
     *
     * @param string $supervisorEmail Email address of supervisor.
     * @param string $supervisorPassword Clear-text password of supervisor.
     * @param string $email Email address of the account to delete.
     * @return bool True if the account could be deleted successfully. Otherwise, false (mostly if the supervisor credentials are incorrect - but could also be an execution error).
     */
    public function deleteAccountBySupervisor(string $supervisorEmail, string $supervisorPassword, string $email) : bool;
}