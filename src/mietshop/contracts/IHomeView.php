<?php

namespace src\mietshop\contracts;

interface IHomeView
{
    /**
     * Shows the landing page,
     * using an IGlobalView instance provided in the constructor.
     */
    public function show(string $host, string $companyName, string $companyOwner, string $street, string $zipCode, string $place, string $email, string $phoneMobile, string $phoneNormal, string $mapLocationURL, string $facebookURL, string $instagramURL, string $whatsappURL, string $kleinanzeigenURL, string $privacyPolicy, string $termsOfService) : void;
}