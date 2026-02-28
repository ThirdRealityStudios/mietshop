<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IGlobalView;
use src\mietshop\contracts\IHomeView;

class HomeView implements IHomeView
{
    private IGlobalView $globalView;

    public function __construct(IGlobalView $globalView)
    {
        $this->globalView = $globalView;
    }

    public function show(string $host, string $companyName, string $companyOwner, string $street, string $zipCode, string $place, string $email, string $phoneMobile, string $phoneNormal, string $mapLocationURL, string $facebookURL, string $instagramURL, string $whatsappURL, string $kleinanzeigenURL, string $privacyPolicy, string $termsOfService): void
    {
        $body = "Hello";

        $this->globalView->show($host, $companyName, $companyOwner, $street, $zipCode, $place, $email, $phoneMobile, $phoneNormal, $mapLocationURL, $facebookURL, $instagramURL, $whatsappURL, $kleinanzeigenURL, $privacyPolicy, $termsOfService, $body);
    }
}