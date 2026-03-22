<?php

namespace src\mietshop\model;

use src\mietshop\contracts\IValidationModel;

class ValidationModel implements IValidationModel
{
    public function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false && preg_match('#^[\p{L}0-9\.\-\_\+]+@[\p{L}0-9\.\-]+\.[A-Za-z]{2,63}$#u', $email) === 1;
    }

    public function isValidPhoneNumber(string $phoneNumber): bool
    {
        return preg_match('#^\+?[0-9 ().\-]+$#', $phoneNumber) === 1;
    }

    public function isValidFullName(string $fullName): bool
    {
        /**
         * Note: \p{L} is used to allow every alphabetic international Unicode character. u in the end stands for Unicode mode to basically allow the use of [\p{L} .
         */
        return preg_match('#^[\p{L}\s\-\.\'()]+$#u', $fullName) === 1;
    }

    public function isValidHexRGBCode(string $hexCode): bool
    {
        return preg_match('#^\#[0-9A-Fa-f]{6}$#', $hexCode) === 1 || preg_match('#^\#[0-9A-Fa-f]{8}$#', $hexCode) === 1;
    }
}