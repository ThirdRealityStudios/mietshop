<?php

namespace src\mietshop\contracts;

/**
 * This is the interface which defines the most important user input validation functions
 * for across most software components,
 * so they can access these.
 */
interface IValidationModel
{
    /**
     * Tells you if an email is valid and using the common format,
     * ignoring Chinese symbols and such.
     * Internally it uses filter_var(...) and a regular expression.
     *
     * @param string $email Email to check.
     * @return bool True if email is valid. Otherwise, false.
     */
    public function isValidEmail(string $email): bool;

    /**
     * Checks if the given phone number matches an usual international number.
     * In the beginning,
     * the plus symbol is optional ( \+? ).
     * After that you must choose any symbol at least once,
     * meaning any digit, a space, curly brackets, a dot or dash symbol.
     * Note: regular expressions begin in this case with #^ and do end with $# .
     *
     * @param string $phoneNumber The phone number to check.
     * @return bool True if the phone number matches the usual international phone number format (no matter if mobile or normal number). Otherwise, false.
     */
    public function isValidPhoneNumber(string $phoneNumber): bool;

    /**
     * Validates full names,
     * allowing UTF-8 alphabetic symbols (of many different language alphabets) and special chars like (,),-,',. and space symbol.
     *
     * @param string $fullName Full name of person.
     * @return bool True if the string represents a valid usual full name. Otherwise, false.
     */
    public function isValidFullName(string $fullName): bool;

    /**
     * Will tell you if the given hex code is a valid hex RGB(A) code,
     * beginning with # and in the format #RRGGBBAA,
     * e.g. #11223344.
     * The format #RRGGBB is also accepted,
     * so without alpha.
     *
     * @param string $hexCode RGB(A) code to test
     * @return bool True if it is a valid hex RGB(A) code. Otherwise, false.
     */
    public function isValidHexRGBCode(string $hexCode): bool;
}