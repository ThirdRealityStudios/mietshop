<?php

namespace src\mietshop\model;

use src\mietshop\contracts\IGlobalModel;
use src\mietshop\infrastructure\database\IDatabaseAdapter;

/**
 * Class which represents mostly company data,
 * which is displayed on almost every webpage or is legally mandatory,
 * such as imprint details (e.g. owner name, phone, e-mail),
 * your data privacy information,
 * your general business conditions (German: AGBs).
 */
class GlobalModel implements IGlobalModel
{
    public function __construct(IDatabaseAdapter $databaseAdapter)
    {
        
    }

    public function getCompanyName(): string
    {
        return "";
    }

    public function setCompanyName(string $companyName): bool
    {
        return true;
    }

    public function getCompanyOwner(): string
    {
        return "";
    }

    public function setCompanyOwner(string $companyOwner): bool
    {
        // TODO: Implement setCompanyOwner() method.
    }

    public function getCompanyStreet(): string
    {
        // TODO: Implement getCompanyStreet() method.
    }

    public function setCompanyStreet(string $companyStreet): bool
    {
        // TODO: Implement setCompanyStreet() method.
    }

    public function getCompanyZipCode(): string
    {
        // TODO: Implement getCompanyZipCode() method.
    }

    public function setCompanyZipCode(string $companyZipCode): bool
    {
        // TODO: Implement setCompanyZipCode() method.
    }

    public function getCompanyPlace(): string
    {
        // TODO: Implement getCompanyPlace() method.
    }

    public function setCompanyPlace(string $companyPlace): bool
    {
        // TODO: Implement setCompanyPlace() method.
    }

    public function getPhoneMobile(): string
    {
        // TODO: Implement getPhoneMobile() method.
    }

    public function setPhoneMobile(string $phoneMobile): bool
    {
        // TODO: Implement setPhoneMobile() method.
    }

    public function getPhoneNormal(): string
    {
        // TODO: Implement getPhoneNormal() method.
    }

    public function setPhoneNormal(string $phoneNormal): bool
    {
        // TODO: Implement setPhoneNormal() method.
    }

    public function getCompanyMapLocation(): string
    {
        // TODO: Implement getCompanyMapLocation() method.
    }

    public function setCompanyMapLocation(string $companyMapLocation): bool
    {
        // TODO: Implement setCompanyMapLocation() method.
    }

    public function getFacebookLink(): string
    {
        // TODO: Implement getFacebookLink() method.
    }

    public function setFacebookLink(string $facebookLink): bool
    {
        // TODO: Implement setFacebookLink() method.
    }

    public function getInstagramLink(): string
    {
        // TODO: Implement getInstagramLink() method.
    }

    public function setInstagramLink(string $instagramLink): bool
    {
        // TODO: Implement setInstagramLink() method.
    }

    public function getWhatsAppLink(): string
    {
        // TODO: Implement getWhatsAppLink() method.
    }

    public function setWhatsAppLink(string $whatsAppLink): bool
    {
        // TODO: Implement setWhatsAppLink() method.
    }

    public function getKleinanzeigenLink(): string
    {
        // TODO: Implement getKleinanzeigenLink() method.
    }

    public function setKleinanzeigenLink(string $kleinanzeigenLink): bool
    {
        // TODO: Implement setKleinanzeigenLink() method.
    }

    public function getDataPrivacyInformation(): string
    {
        // TODO: Implement getDataPrivacyInformation() method.
    }

    public function setDataPrivacyInformation(string $dataPrivacyInformation): bool
    {
        // TODO: Implement setDataPrivacyInformation() method.
    }

    public function getCommonBusinessConditions(): string
    {
        // TODO: Implement getCommonBusinessConditions() method.
    }

    public function setCommonBusinessConditions(string $commonBusinessConditions): bool
    {
        // TODO: Implement setCommonBusinessConditions() method.
    }
}