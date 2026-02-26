<?php

namespace src\mietshop\contracts;

use src\mietshop\infrastructure\database\IDatabaseAdapter;

class GlobalRepository implements IGlobalModel
{
    private IDatabaseAdapter $databaseAdapter;

    /**
     * Sets up the Global table if it was not created yet.
     * Necessary to make the GlobalRepository safely work.
     */
    private function initDatabase(): bool
    {
        $sql = "

			CREATE TABLE IF NOT EXISTS Global
			(
				GlobalID INT(8) AUTO_INCREMENT PRIMARY KEY UNIQUE,

				CompanyName VARCHAR(32) NOT NULL UNIQUE,
				Password VARCHAR(255) NOT NULL,

				HasPermissionAdmin BOOLEAN DEFAULT FALSE
			);

			INSERT INTO Account (Username, Password, HasPermissionAdmin) VALUES ('Admin', '".password_hash(MASTER_PASSWORD, PASSWORD_DEFAULT)."', true) ON DUPLICATE KEY UPDATE Username=Username, Password=Password, HasPermissionAdmin=HasPermissionAdmin;
			";

        return $this->databaseAdapter->execute($sql);
    }

    /**
     * @param IDatabaseAdapter $databaseAdapter Database adapter. Make sure you have connected yet.
     */
    public function __construct(IDatabaseAdapter $databaseAdapter)
    {
        $this->databaseAdapter = $databaseAdapter;
        
        $this->databaseAdapter->execute("");
    }

    public function getCompanyName(): string
    {

    }

    public function setCompanyName(string $companyName): bool
    {
        // TODO: Implement setCompanyName() method.
    }

    public function getCompanyOwner(): string
    {
        // TODO: Implement getCompanyOwner() method.
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