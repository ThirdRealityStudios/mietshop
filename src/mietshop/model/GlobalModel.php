<?php

namespace src\mietshop\model;

use src\mietshop\contracts\GlobalRepository;
use src\mietshop\contracts\IGlobalModel;

/**
 * Class which represents mostly company data,
 * which is displayed on almost every webpage or is legally mandatory,
 * such as imprint details (e.g. owner name, phone, e-mail),
 * your data privacy information,
 * your general business conditions (German: AGBs).
 */
class GlobalModel implements IGlobalModel
{
    private GlobalRepository $globalRepository;

    public function __construct(GlobalRepository $globalRepository)
    {
        $this->globalRepository = $globalRepository;
    }

    public function getCompanyName(): string
    {
        // TODO: Implement getCompanyName() method.
    }

    public function getCompanyNameCharacterLimit(): int
    {
        // TODO: Implement getCompanyNameCharacterLimit() method.
    }

    public function setCompanyName(string $companyName): bool
    {
        // TODO: Implement setCompanyName() method.
    }

    public function getOwner(): string
    {
        // TODO: Implement getOwner() method.
    }

    public function getOwnerCharacterLimit(): int
    {
        // TODO: Implement getOwnerCharacterLimit() method.
    }

    public function setCompanyOwner(string $companyOwner): bool
    {
        // TODO: Implement setCompanyOwner() method.
    }

    public function getStreet(): string
    {
        // TODO: Implement getStreet() method.
    }

    public function getCompanyStreetCharacterLimit(): int
    {
        // TODO: Implement getCompanyStreetCharacterLimit() method.
    }

    public function setStreet(string $companyStreet): bool
    {
        // TODO: Implement setStreet() method.
    }

    public function getZipCode(): string
    {
        // TODO: Implement getZipCode() method.
    }

    public function getZIPCodeCharacterLimit(): int
    {
        // TODO: Implement getZIPCodeCharacterLimit() method.
    }

    public function setZipCode(string $companyZipCode): bool
    {
        // TODO: Implement setZipCode() method.
    }

    public function getPlace(): string
    {
        // TODO: Implement getPlace() method.
    }

    public function getPlaceCharacterLimit(): int
    {
        // TODO: Implement getPlaceCharacterLimit() method.
    }

    public function setPlace(string $companyPlace): bool
    {
        // TODO: Implement setPlace() method.
    }

    public function getPhoneMobile(): string
    {
        // TODO: Implement getPhoneMobile() method.
    }

    public function getPhoneMobileCharacterLimit(): int
    {
        // TODO: Implement getPhoneMobileCharacterLimit() method.
    }

    public function setPhoneMobile(string $phoneMobile): bool
    {
        // TODO: Implement setPhoneMobile() method.
    }

    public function getPhoneNormal(): string
    {
        // TODO: Implement getPhoneNormal() method.
    }

    public function getPhoneNormalCharacterLimit(): int
    {
        // TODO: Implement getPhoneNormalCharacterLimit() method.
    }

    public function setPhoneNormal(string $phoneNormal): bool
    {
        // TODO: Implement setPhoneNormal() method.
    }

    public function getCompanyMapLocation(): string
    {
        // TODO: Implement getCompanyMapLocation() method.
    }

    public function getMapLocationCharacterLimit(): int
    {
        // TODO: Implement getMapLocationCharacterLimit() method.
    }

    public function setCompanyMapLocation(string $companyMapLocation): bool
    {
        // TODO: Implement setCompanyMapLocation() method.
    }

    public function getFacebookLink(): string
    {
        // TODO: Implement getFacebookLink() method.
    }

    public function getFacebookLinkCharacterLimit(): int
    {
        // TODO: Implement getFacebookLinkCharacterLimit() method.
    }

    public function setFacebookLink(string $facebookLink): bool
    {
        // TODO: Implement setFacebookLink() method.
    }

    public function getInstagramLink(): string
    {
        // TODO: Implement getInstagramLink() method.
    }

    public function getInstagramLinkCharacterLimit(): int
    {
        // TODO: Implement getInstagramLinkCharacterLimit() method.
    }

    public function setInstagramLink(string $instagramLink): bool
    {
        // TODO: Implement setInstagramLink() method.
    }

    public function getWhatsAppLink(): string
    {
        // TODO: Implement getWhatsAppLink() method.
    }

    public function getWhatsAppLinkCharacterLimit(): int
    {
        // TODO: Implement getWhatsAppLinkCharacterLimit() method.
    }

    public function setWhatsAppLink(string $whatsAppLink): bool
    {
        // TODO: Implement setWhatsAppLink() method.
    }

    public function getKleinanzeigenLink(): string
    {
        // TODO: Implement getKleinanzeigenLink() method.
    }

    public function getKleinanzeigenLinkCharacterLimit(): int
    {
        // TODO: Implement getKleinanzeigenLinkCharacterLimit() method.
    }

    public function setKleinanzeigenLink(string $kleinanzeigenLink): bool
    {
        // TODO: Implement setKleinanzeigenLink() method.
    }

    public function getPrivacyPolicyInformation(): string
    {
        // TODO: Implement getPrivacyPolicyInformation() method.
    }

    public function getPrivacyPolicyCharacterLimit(): int
    {
        // TODO: Implement getPrivacyPolicyCharacterLimit() method.
    }

    public function setPrivacyPolicyInformation(string $privacyPolicyInformation): bool
    {
        // TODO: Implement setPrivacyPolicyInformation() method.
    }

    public function getCommonBusinessConditions(): string
    {
        // TODO: Implement getCommonBusinessConditions() method.
    }

    public function getTermsOfServiceCharacterLimit(): int
    {
        // TODO: Implement getTermsOfServiceCharacterLimit() method.
    }

    public function setTermsOfService(string $termsOfService): bool
    {
        // TODO: Implement setTermsOfService() method.
    }
}