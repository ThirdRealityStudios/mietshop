<?php

namespace src\mietshop\model;

use PDOException;
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
     * @throws PDOException
     */
    public function __construct(IDatabaseAdapter $databaseAdapter)
    {
        $this->databaseAdapter = $databaseAdapter;

        $sql =
            "
			CREATE TABLE IF NOT EXISTS CompanyModel
			(
				ID INT(8) AUTO_INCREMENT PRIMARY KEY UNIQUE,
			    
				CompanyName VARCHAR(50) NOT NULL DEFAULT \"Mustermann GmbH\",
			    Owner VARCHAR(40) NOT NULL DEFAULT \"Max Mustermann\",
			    
			    Street VARCHAR(40) NOT NULL DEFAULT \"Sesamstr. 91b\",
			    ZipCode VARCHAR(5) NOT NULL DEFAULT \"12345\",
			    Place VARCHAR(20) NOT NULL DEFAULT \"Musterstadt\",
			    
			    Email VARCHAR(100) NOT NULL DEFAULT \"mail@website.com\",
			    
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

        $this->createDefaultDataset();
    }

    /**
     * Creates a default dataset if not yet created.
     *
     * @return void
     * @throws PDOException
     */
    private function createDefaultDataset() : void
    {
        $query = $this->databaseAdapter->fetch("SELECT COUNT(*) AS RowCount FROM CompanyModel");

        $firstRowKey = array_keys($query)[0];

        if(sizeof($query) === 1 && isset($query[$firstRowKey]["RowCount"]) && $query[$firstRowKey]["RowCount"] === 0)
        {
            $this->databaseAdapter->execute("INSERT INTO CompanyModel() VALUES()");
        }
    }

    /**
     * Returns the corresponding value from the CompanyModel dataset,
     * using the column name.
     *
     * @param string $name SQL column name
     * @return string Value from CompanyModel.
     */
    private function readValue(string $name) : string
    {
        try
        {
            $columnNames = $this->databaseAdapter->readColumnNames("CompanyModel");

            if(in_array($name, $columnNames))
            {
                $dataFetched = $this->databaseAdapter->fetch("SELECT $name FROM CompanyModel");

                $firstRowKey = array_keys($dataFetched)[0];
                $firstRow = $dataFetched[$firstRowKey];

                if(isset($firstRow[$name]))
                {
                    return $firstRow[$name];
                }
                else
                {
                    return "";
                }
            }
            else
            {
                return "";
            }
        }
        catch(PDOException $e)
        {
            return "";
        }
    }

    /**
     * Sets the given value in the CompanyModel dataset,
     * using the column nam.
     *
     * @param string $name SQL column name
     * @return bool True if updated successfully. Otherwise, false.
     */
    private function updateValue(string $name, string $value) : bool
    {
        try
        {
            $columnNames = $this->databaseAdapter->readColumnNames("CompanyModel");

            if(in_array($name, $columnNames))
            {
                $fetchData = $this->databaseAdapter->fetch("SELECT MIN(CompanyModel.ID) AS MINIMUM FROM CompanyModel");

                if(sizeof($fetchData) === 1 && isset($fetchData[0]["MINIMUM"]))
                {
                    $minimumID = $fetchData[0]["MINIMUM"];

                    $updatedRows = $this->databaseAdapter->execute("UPDATE CompanyModel SET $name=? WHERE CompanyModel.ID=?", [$value, $minimumID]);

                    return $updatedRows === 1;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        catch(PDOException $e)
        {
            return false;
        }
    }

    public function getCompanyName(): string
    {
        return $this->readValue("CompanyName");
    }

    public function getCompanyNameCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "CompanyName");
    }

    public function setCompanyName(string $companyName): bool
    {
        if(strlen($companyName) <= $this->getCompanyNameCharacterLimit())
        {
            return $this->updateValue("CompanyName", $companyName);
        }
        else
        {
            return false;
        }
    }

    public function getOwner(): string
    {
        return $this->readValue("Owner");
    }

    public function getOwnerCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "Owner");
    }

    public function setOwner(string $companyOwner): bool
    {
        if(strlen($companyOwner) <= $this->getOwnerCharacterLimit())
        {
            return $this->updateValue("Owner", $companyOwner);
        }
        else
        {
            return false;
        }
    }

    public function getStreet(): string
    {
        return $this->readValue("Street");
    }

    public function getStreetCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "Street");
    }

    public function setStreet(string $street): bool
    {
        if(strlen($street) <= $this->getStreetCharacterLimit())
        {
            return $this->updateValue("Street", $street);
        }
        else
        {
            return false;
        }
    }

    public function getZipCode(): string
    {
        return $this->readValue("ZipCode");
    }

    public function getZipCodeCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "ZipCode");
    }

    public function setZipCode(string $companyZipCode): bool
    {
        if(strlen($companyZipCode) <= $this->getZipCodeCharacterLimit())
        {
            return $this->updateValue("ZipCode", $companyZipCode);
        }
        else
        {
            return false;
        }
    }

    public function getPlace(): string
    {
        return $this->readValue("Place");
    }

    public function getPlaceCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "Place");
    }

    public function setPlace(string $companyPlace): bool
    {
        if(strlen($companyPlace) <= $this->getPlaceCharacterLimit())
        {
            return $this->updateValue("Place", $companyPlace);
        }
        else
        {
            return false;
        }
    }

    public function getEmail(): string
    {
        return $this->readValue("Email");
    }

    public function getEmailCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "Email");
    }

    public function setEmail(string $email): bool
    {
        if(strlen($email) <= $this->getEmailCharacterLimit())
        {
            return $this->updateValue("Email", $email);
        }
        else
        {
            return false;
        }
    }

    public function getPhoneMobile(): string
    {
        return $this->readValue("PhoneMobile");
    }

    public function getPhoneMobileCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "PhoneMobile");
    }

    public function setPhoneMobile(string $phoneMobile): bool
    {
        if(strlen($phoneMobile) <= $this->getPhoneMobileCharacterLimit())
        {
            return $this->updateValue("PhoneMobile", $phoneMobile);
        }
        else
        {
            return false;
        }
    }

    public function getPhoneNormal(): string
    {
        return $this->readValue("PhoneNormal");
    }

    public function getPhoneNormalCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "PhoneNormal");
    }

    public function setPhoneNormal(string $phoneNormal): bool
    {
        if(strlen($phoneNormal) <= $this->getPhoneNormalCharacterLimit())
        {
            return $this->updateValue("PhoneNormal", $phoneNormal);
        }
        else
        {
            return false;
        }
    }

    public function getMapLocationURL(): string
    {
        return $this->readValue("MapLocationURL");
    }

    public function getMapLocationURLCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "MapLocation");
    }

    public function setMapLocationURL(string $companyMapLocation): bool
    {
        if(strlen($companyMapLocation) <= $this->getMapLocationURLCharacterLimit())
        {
            return $this->updateValue("MapLocationURL", $companyMapLocation);
        }
        else
        {
            return false;
        }
    }

    public function getFacebookURL(): string
    {
        return $this->readValue("FacebookURL");
    }

    public function getFacebookURLCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "FacebookURL");
    }

    public function setFacebookURL(string $facebookURL): bool
    {
        if(strlen($facebookURL) <= $this->getFacebookURLCharacterLimit())
        {
            return $this->updateValue("FacebookURL", $facebookURL);
        }
        else
        {
            return false;
        }
    }

    public function getInstagramURL(): string
    {
        return $this->readValue("InstagramURL");
    }

    public function getInstagramURLCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "InstagramURL");
    }

    public function setInstagramURL(string $instagramURL): bool
    {
        if(strlen($instagramURL) <= $this->getInstagramURLCharacterLimit())
        {
            return $this->updateValue("InstagramURL", $instagramURL);
        }
        else
        {
            return false;
        }
    }

    public function getWhatsAppURL(): string
    {
        return $this->readValue("WhatsAppURL");
    }

    public function getWhatsAppURLCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "WhatsAppURL");
    }

    public function setWhatsAppURL(string $whatsAppURL): bool
    {
        if(strlen($whatsAppURL) <= $this->getWhatsAppURLCharacterLimit())
        {
            return $this->updateValue("WhatsAppURL", $whatsAppURL);
        }
        else
        {
            return false;
        }
    }

    public function getKleinanzeigenURL(): string
    {
        return $this->readValue("KleinanzeigenURL");
    }

    public function getKleinanzeigenURLCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "KleinanzeigenURL");
    }

    public function setKleinanzeigenURL(string $kleinanzeigenURL): bool
    {
        if(strlen($kleinanzeigenURL) <= $this->getKleinanzeigenURLCharacterLimit())
        {
            return $this->updateValue("KleinanzeigenURL", $kleinanzeigenURL);
        }
        else
        {
            return false;
        }
    }

    public function getPrivacyPolicy(): string
    {
        return $this->readValue("PrivacyPolicy");
    }

    public function getPrivacyPolicyCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "PrivacyPolicy");
    }

    public function setPrivacyPolicy(string $privacyPolicyInformation): bool
    {
        if(strlen($privacyPolicyInformation) <= $this->getPrivacyPolicyCharacterLimit())
        {
            return $this->updateValue("PrivacyPolicy", $privacyPolicyInformation);
        }
        else
        {
            return false;
        }
    }

    public function getTermsOfService(): string
    {
        return $this->readValue("TermsOfService");
    }

    public function getTermsOfServiceCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CompanyModel", "TermsOfService");
    }

    public function setTermsOfService(string $termsOfService): bool
    {
        if(strlen($termsOfService) <= $this->getTermsOfServiceCharacterLimit())
        {
            return $this->updateValue("TermsOfService", $termsOfService);
        }
        else
        {
            return false;
        }
    }
}