<?php

namespace src\mietshop\contracts;

use PDOException;
use Random\RandomException;
use src\mietshop\infrastructure\database\IDatabaseAdapter;

class GlobalRepository implements IGlobalModel
{
    private IDatabaseAdapter $databaseAdapter;

    /**
     * @param IDatabaseAdapter $databaseAdapter Database adapter. Make sure you have connected yet.
     */
    public function __construct(IDatabaseAdapter $databaseAdapter)
    {
        $this->databaseAdapter = $databaseAdapter;

        $sql =
            "
			CREATE TABLE IF NOT EXISTS GlobalModel
			(
				ID INT(8) AUTO_INCREMENT PRIMARY KEY UNIQUE,

				CompanyName VARCHAR(50) NOT NULL DEFAULT \"Mustermann GmbH\",
			    CompanyOwner VARCHAR(40) NOT NULL DEFAULT \"Max Mustermann\",
			    
			    CompanyStreet VARCHAR(40) NOT NULL DEFAULT \"Sesamstr. 91b\",
			    CompanyZipCode VARCHAR(5) NOT NULL DEFAULT \"12345\",
			    CompanyPlace VARCHAR(20) NOT NULL DEFAULT \"Musterstadt\",
			    
			    CompanyPhoneMobile VARCHAR(16) NOT NULL DEFAULT \"+49 123 45678\",
			    CompanyPhoneNormal VARCHAR(16) NOT NULL DEFAULT \"12345 6789123\",
			    
			    CompanyMapLocation VARCHAR(1000) NOT NULL DEFAULT \"\",
			    
			    FacebookLink VARCHAR(500) NOT NULL DEFAULT \"\",
			    InstagramLink VARCHAR(200) NOT NULL DEFAULT \"\",
			    WhatsAppLink VARCHAR(150) NOT NULL DEFAULT \"\",
			    KleinanzeigenLink VARCHAR(150) NOT NULL DEFAULT \"\",
			    
			    PrivacyPolicy VARCHAR(65535) NOT NULL DEFAULT \"\",
			    CommonBusinessConditions VARCHAR(65535) NOT NULL DEFAULT \"\"
			);
			";

        $this->databaseAdapter->execute($sql);
    }

    private function readCharacterLimit($columnName) : int
    {
        return 0;
    }

    /**
     * @return string
     * @throws RandomException
     */
    private function getRandomHash() : string
    {
        return md5(random_bytes(6));
    }

    public function getCompanyName(): string
    {

    }

    /**
     * @throws RandomException
     * @throws PDOException
     */
    public function getCompanyNameCharacterLimit(): int
    {

    }

    public function setCompanyName(string $companyName): bool
    {

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