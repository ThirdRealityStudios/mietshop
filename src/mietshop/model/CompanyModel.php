<?php

namespace src\mietshop\model;

use PDOException;
use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IUserInputModel;
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
    private IUserInputModel $userInputModel;

    // md5(random_bytes(6));

    /**
     * @param IDatabaseAdapter $databaseAdapter Database adapter. Make sure you have connected yet.
     * @throws PDOException
     */
    public function __construct(IUserInputModel $userInputModel, IDatabaseAdapter $databaseAdapter)
    {
        $this->databaseAdapter = $databaseAdapter;
        $this->userInputModel = $userInputModel;

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
			    
			    P19UStGApplies BOOL NOT NULL DEFAULT FALSE,
			    P36VSBGApplies BOOL NOT NULL DEFAULT FALSE,
			    
			    PhoneMobile VARCHAR(16) NOT NULL DEFAULT \"+49 123 45678\",
			    PhoneNormal VARCHAR(16) NOT NULL DEFAULT \"12345 6789123\",
			    
			    MapLocationURL VARCHAR(1000) NOT NULL DEFAULT \"\",
			    
			    FacebookURL VARCHAR(500) NOT NULL DEFAULT \"\",
			    InstagramURL VARCHAR(200) NOT NULL DEFAULT \"\",
			    WhatsAppURL VARCHAR(150) NOT NULL DEFAULT \"\",
			    KleinanzeigenURL VARCHAR(150) NOT NULL DEFAULT \"\",
			    
			    PrivacyPolicy TEXT NOT NULL DEFAULT \"\",
			    TermsOfService TEXT NOT NULL DEFAULT \"\"
			);
			";

        $this->databaseAdapter->execute($sql);

        try
        {
            $this->createDefaultDataset();
        }
        catch (PDOException $e)
        {

        }
    }

    /**
     * Creates a default dataset if not yet created.
     * Is only called in the constructor.
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
     * @return mixed Value from CompanyModel (string, int whatever).
     */
    private function readValue(string $name) : mixed
    {
        try
        {
            $columnNames = $this->databaseAdapter->readColumnNames("CompanyModel");

            // If the name is known in the DB table as a column AND if the data was not fetched yet for the array.
            if(in_array($name, $columnNames))
            {
                $dataFetched = $this->databaseAdapter->fetch("SELECT $name FROM CompanyModel");

                $firstRowKey = array_keys($dataFetched)[0]; // Key for the first row fetched
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
     * using the column name.
     *
     * @param string $name SQL column name
     * @param string|bool $value That boolean or string value to set.
     * @return bool True if updated successfully. Otherwise, false.
     */
    private function updateValue(string $name, string|bool $value) : bool
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

                    $this->databaseAdapter->execute("UPDATE CompanyModel SET $name=? WHERE CompanyModel.ID=?", [$value, $minimumID]);

                    return true;
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
        if(mb_strlen($companyName, "UTF-8") <= $this->getCompanyNameCharacterLimit())
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
        if(mb_strlen($companyOwner, "UTF-8") <= $this->getOwnerCharacterLimit() && $this->userInputModel->isValidFullName($companyOwner))
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
        if(mb_strlen($street, "UTF-8") <= $this->getStreetCharacterLimit())
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
        if(mb_strlen($companyZipCode, "UTF-8") <= $this->getZipCodeCharacterLimit() && is_numeric($companyZipCode))
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
        if(mb_strlen($companyPlace, "UTF-8") <= $this->getPlaceCharacterLimit())
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
        if(mb_strlen($email, "UTF-8") <= $this->getEmailCharacterLimit() && $this->userInputModel->isValidEmail($email))
        {
            return $this->updateValue("Email", $email);
        }
        else
        {
            return false;
        }
    }

    public function isP19UStGRelevant(): bool
    {
        $fetchValue = $this->readValue("P19UStGApplies");

        return (bool) strcmp($fetchValue, "true");
    }

    public function setP19UStGApplies(bool $isLawRelevant): bool
    {
        return $this->updateValue("P19UStGApplies", $isLawRelevant);
    }

    public function isP36VSBGRelevant(): bool
    {
        $fetchValue = $this->readValue("P36VSBGApplies");

        return (bool) strcmp($fetchValue, "true");
    }

    public function setP36VSBGApplies(bool $isLawRelevant): bool
    {
        return $this->updateValue("P36VSBGApplies", $isLawRelevant);
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
        if(mb_strlen($phoneMobile, "UTF-8") <= $this->getPhoneMobileCharacterLimit())
        {
            $preprocessedPhoneNormal = str_replace(" ", "", $phoneMobile);

            if($this->userInputModel->isValidPhoneNumber($preprocessedPhoneNormal))
            {
                return $this->updateValue("PhoneMobile", $preprocessedPhoneNormal);
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
        if(mb_strlen($phoneNormal, "UTF-8") <= $this->getPhoneNormalCharacterLimit())
        {
            $preprocessedPhoneNormal = str_replace(" ", "", $phoneNormal);

            if($this->userInputModel->isValidPhoneNumber($preprocessedPhoneNormal))
            {
                return $this->updateValue("PhoneNormal", $preprocessedPhoneNormal);
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
        if(mb_strlen($companyMapLocation, "UTF-8") <= $this->getMapLocationURLCharacterLimit())
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
        if(mb_strlen($facebookURL, "UTF-8") <= $this->getFacebookURLCharacterLimit())
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
        if(mb_strlen($instagramURL, "UTF-8") <= $this->getInstagramURLCharacterLimit())
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
        if(mb_strlen($whatsAppURL, "UTF-8") <= $this->getWhatsAppURLCharacterLimit())
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
        if(mb_strlen($kleinanzeigenURL, "UTF-8") <= $this->getKleinanzeigenURLCharacterLimit())
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
        if(mb_strlen($privacyPolicyInformation, "UTF-8") <= $this->getPrivacyPolicyCharacterLimit())
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
        if(mb_strlen($termsOfService, "UTF-8") <= $this->getTermsOfServiceCharacterLimit())
        {
            return $this->updateValue("TermsOfService", $termsOfService);
        }
        else
        {
            return false;
        }
    }

    public function getCompanyData(): array
    {
        $sql =
        "
            SELECT MIN(CompanyModel.ID) AS MinimumID FROM CompanyModel
        ";

        try
        {
            $result = $this->databaseAdapter->fetch($sql);

            if(isset($result[0]["MinimumID"]))
            {
                $minimumID = $result[0]["MinimumID"];

                $sql =
                "
                    SELECT * FROM CompanyModel WHERE CompanyModel.ID = ?
                ";

                $result = $this->databaseAdapter->fetch($sql, [$minimumID]);

                if(isset($result[0]))
                {
                    $columnNames = $this->databaseAdapter->readColumnNames("CompanyModel");

                    if(sizeof($columnNames) > 0)
                    {
                        $companyData = [];

                        for($i = 0; $i < sizeof($columnNames); $i++)
                        {
                            $columnName = $columnNames[$i];

                            if(isset($result[0][$columnName]))
                            {
                                $companyValue = $result[0][$columnName];

                                // Bugfix: make sure only the character limit is retrieved from string type columns (because it does only work them)
                                if($this->databaseAdapter->isString("CompanyModel", $columnName)) // Then very likely some usual string type (like varchar, normal char, text etc.)
                                {
                                    $maximumCharacterLimit = $this->databaseAdapter->readCharacterLimit("CompanyModel", $columnName);

                                    // Could you fetch character limit information (does not apply for booleans e.g.) ?
                                    if($maximumCharacterLimit >= 0)
                                    {
                                        $companyData[$columnName] = [$companyValue, $maximumCharacterLimit];
                                    }
                                    else
                                    {
                                        return []; // Technical issue or smth
                                    }
                                }
                                else
                                {
                                    $companyData[$columnName] = [$companyValue, 0];
                                }
                            }
                            else
                            {
                                return [];
                            }
                        }

                        return $companyData;
                    }
                    else
                    {
                        return [];
                    }
                }
                else
                {
                    return [];
                }
            }
            else
            {
                return [];
            }
        }
        catch(PDOException $e)
        {
            return [];
        }
    }
}