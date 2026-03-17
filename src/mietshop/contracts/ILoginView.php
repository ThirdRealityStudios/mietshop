<?php

namespace src\mietshop\contracts;

interface ILoginView
{
    /**
     * Generates the normal login page for registered users.
     * Users are not able to register themselves.
     * Only administrators or owners can register new users inside the private system.
     * So registration is not public.
     * Optionally,
     * you can also pass an error message as a string (otherwise just pass null).
     *
     * @param array $companyData Company data array as described in ICompanyModel
     * @param array $appearanceData
     * @param string|null $optionalErrorMessage Optional error message (e.g. "Wrong credentials"). Pass null if you don't need an error message.
     * @return void
     */
    public function show(array $companyData, array $appearanceData, ?string $optionalErrorMessage) : void;
}