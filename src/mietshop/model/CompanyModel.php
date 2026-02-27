<?php

namespace src\mietshop\model;

use src\mietshop\contracts\ICompanyModel;
use src\mietshop\infrastructure\database\IDatabaseAdapter;

/**
 * Class which represents mostly company data,
 * which is displayed on almost every webpage or is legally mandatory,
 * such as imprint details (e.g. owner name, phone, e-mail),
 * your data privacy information,
 * your general business conditions (German: AGBs).
 */
class CompanyModel implements ICompanyModel
{
    private IDatabaseAdapter $databaseAdapter;

    // md5(random_bytes(6));

    /**
     * @param IDatabaseAdapter $databaseAdapter Database adapter. Make sure you have connected yet.
     */
    public function __construct(IDatabaseAdapter $databaseAdapter)
    {
        $this->databaseAdapter = $databaseAdapter;

        $sql =
            "
			CREATE TABLE IF NOT EXISTS CompanyModel
			(
				ID INT(8) AUTO_INCREMENT PRIMARY KEY UNIQUE,
			    
				Name VARCHAR(50) NOT NULL DEFAULT \"Mustermann GmbH\",
			    Owner VARCHAR(40) NOT NULL DEFAULT \"Max Mustermann\",
			    
			    Street VARCHAR(40) NOT NULL DEFAULT \"Sesamstr. 91b\",
			    ZipCode VARCHAR(5) NOT NULL DEFAULT \"12345\",
			    Place VARCHAR(20) NOT NULL DEFAULT \"Musterstadt\",
			    
			    PhoneMobile VARCHAR(16) NOT NULL DEFAULT \"+49 123 45678\",
			    PhoneNormal VARCHAR(16) NOT NULL DEFAULT \"12345 6789123\",
			    
			    MapLocationURL VARCHAR(1000) NOT NULL DEFAULT \"\",
			    
			    FacebookURL VARCHAR(500) NOT NULL DEFAULT \"\",
			    InstagramURL VARCHAR(200) NOT NULL DEFAULT \"\",
			    WhatsAppURL VARCHAR(150) NOT NULL DEFAULT \"\",
			    KleinanzeigenURL VARCHAR(150) NOT NULL DEFAULT \"\",
			    
			    PrivacyPolicy VARCHAR(65535) NOT NULL DEFAULT \"\",
			    TermsOfService VARCHAR(65535) NOT NULL DEFAULT \"\"
			);
			";

        $this->databaseAdapter->execute($sql);
    }

    public function getName(): string
    {
        return "Meine Mietshop GmbH";
    }

    public function getNameCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "Name");
    }

    public function setName(string $companyName): bool
    {
        return false;
    }

    public function getOwner(): string
    {
        return "Max Mustermann";
    }

    public function getOwnerCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "Owner");
    }

    public function setOwner(string $companyOwner): bool
    {
        return false;
    }

    public function getStreet(): string
    {
        return "Sesamstrasse 1";
    }

    public function getStreetCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "Street");
    }

    public function setStreet(string $companyStreet): bool
    {
        return false;
    }

    public function getZipCode(): string
    {
        return "12345";
    }

    public function getZipCodeCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "ZipCode");
    }

    public function setZipCode(string $companyZipCode): bool
    {
        return false;
    }

    public function getPlace(): string
    {
        return "Neustadt";
    }

    public function getPlaceCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "Place");
    }

    public function setPlace(string $companyPlace): bool
    {
        return false;
    }

    public function getPhoneMobile(): string
    {
        return "+49 123 45678";
    }

    public function getPhoneMobileCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "PhoneMobile");
    }

    public function setPhoneMobile(string $phoneMobile): bool
    {
        return false;
    }

    public function getPhoneNormal(): string
    {
        return "12345 6789123";
    }

    public function getPhoneNormalCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "PhoneNormal");
    }

    public function setPhoneNormal(string $phoneNormal): bool
    {
        return false;
    }

    public function getMapLocation(): string
    {
        return "";
    }

    public function getMapLocationCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "MapLocation");
    }

    public function setMapLocation(string $companyMapLocation): bool
    {
        return false;
    }

    public function getFacebookURL(): string
    {
        return "";
    }

    public function getFacebookURLCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "FacebookURL");
    }

    public function setFacebookURL(string $facebookURL): bool
    {
        return false;
    }

    public function getInstagramURL(): string
    {
        return "";
    }

    public function getInstagramURLCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "InstagramURL");
    }

    public function setInstagramURL(string $instagramURL): bool
    {
        return false;
    }

    public function getWhatsAppURL(): string
    {
        return "";
    }

    public function getWhatsAppURLCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "WhatsAppURL");
    }

    public function setWhatsAppURL(string $whatsAppURL): bool
    {
        return false;
    }

    public function getKleinanzeigenURL(): string
    {
        return "";
    }

    public function getKleinanzeigenURLCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "KleinanzeigenURL");
    }

    public function setKleinanzeigenURL(string $kleinanzeigenURL): bool
    {
        return false;
    }

    public function getPrivacyPolicyInformation(): string
    {
        return "Meine Datenschutzerklärung";
    }

    public function getPrivacyPolicyCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "PrivacyPolicy");
    }

    public function setPrivacyPolicyInformation(string $privacyPolicyInformation): bool
    {
        return false;
    }

    public function getTermsOfService(): string
    {
        return "Meine Allgemeinen Geschäftsbedingungen";
    }

    public function getTermsOfServiceCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "TermsOfService");
    }

    public function setTermsOfService(string $termsOfService): bool
    {
        return false;
    }
}