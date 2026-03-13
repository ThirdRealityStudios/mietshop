<?php

namespace src\mietshop\model;

use PDOException;
use src\mietshop\contracts\IUserInputModel;
use src\mietshop\infrastructure\database\IDatabaseAdapter;

class AppearanceModel
{
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
			CREATE TABLE IF NOT EXISTS AppearanceModel
			(
				ID INT(8) AUTO_INCREMENT PRIMARY KEY UNIQUE,
			    
			    ColorFrame VARCHAR(9) NOT NULL DEFAULT \"#0D6EFD\",
			    ColorWindow VARCHAR(9) NOT NULL DEFAULT \"#CFE2FF\",
			    ColorBackground VARCHAR(9) NOT NULL DEFAULT \"#E9ECEF\",
			    ColorText VARCHAR(9) NOT NULL DEFAULT \"#212529\",
			    ColorLink VARCHAR(9) NOT NULL DEFAULT \"#0D6EFD\",
			    ColorInteresting VARCHAR(9) NOT NULL DEFAULT \"#0D6EFD\",
			    ColorUninteresting VARCHAR(9) NOT NULL DEFAULT \"#6C757D\",
			    ColorActionExecute VARCHAR(9) NOT NULL DEFAULT \"#198754\",
			    ColorActionAbort VARCHAR(9) NOT NULL DEFAULT \"#DC3545\",
			    ImageLogo VARCHAR(255) NOT NULL DEFAULT \"/img/logo/company/logo.png\"
			);
			";

        $this->databaseAdapter->execute($sql);

        $this->createDefaultDataset();
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
        $query = $this->databaseAdapter->fetch("SELECT COUNT(*) AS RowCount FROM AppearanceModel");

        $firstRowKey = array_keys($query)[0];

        if(sizeof($query) === 1 && isset($query[$firstRowKey]["RowCount"]) && $query[$firstRowKey]["RowCount"] === 0)
        {
            $this->databaseAdapter->execute("INSERT INTO AppearanceModel() VALUES()");
        }
    }

    /**
     * Returns the corresponding value from the AppearanceModel dataset,
     * using the column name.
     *
     * @param string $name SQL column name
     * @return mixed Value from AppearanceModel (string, int whatever).
     */
    private function readValue(string $name) : mixed
    {
        try
        {
            $columnNames = $this->databaseAdapter->readColumnNames("AppearanceModel");

            // If the name is known in the DB table as a column AND if the data was not fetched yet for the array.
            if(in_array($name, $columnNames))
            {
                $dataFetched = $this->databaseAdapter->fetch("SELECT $name FROM AppearanceModel");

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
     * Sets the given value in the AppearanceModel dataset,
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
            $columnNames = $this->databaseAdapter->readColumnNames("AppearanceModel");

            if(in_array($name, $columnNames))
            {
                $fetchData = $this->databaseAdapter->fetch("SELECT MIN(AppearanceModel.ID) AS MINIMUM FROM AppearanceModel");

                if(sizeof($fetchData) === 1 && isset($fetchData[0]["MINIMUM"]))
                {
                    $minimumID = $fetchData[0]["MINIMUM"];

                    $this->databaseAdapter->execute("UPDATE AppearanceModel SET $name=? WHERE AppearanceModel.ID=?", [$value, $minimumID]);

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

    public function getColorFrame(): string
    {
        return $this->readValue("ColorFrame");
    }

    public function getColorFrameCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("AppearanceModel", "ColorFrame");
    }

    public function setColorFrame(string $colorFrame): bool
    {
        if(mb_strlen($colorFrame, "UTF-8") <= $this->getColorFrameCharacterLimit())
        {
            return $this->updateValue("ColorFrame", $colorFrame);
        }
        else
        {
            return false;
        }
    }

    public function getColorWindow(): string
    {
        return $this->readValue("ColorWindow");
    }

    public function getColorWindowCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("AppearanceModel", "ColorWindow");
    }

    public function setColorWindow(string $colorWindow): bool
    {
        if(mb_strlen($colorWindow, "UTF-8") <= $this->getColorWindowCharacterLimit())
        {
            return $this->updateValue("ColorWindow", $colorWindow);
        }
        else
        {
            return false;
        }
    }

    public function getColorBackground(): string
    {
        return $this->readValue("ColorBackground");
    }

    public function getColorBackgroundCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("AppearanceModel", "ColorBackground");
    }

    public function setColorBackground(string $colorBackground): bool
    {
        if(mb_strlen($colorBackground, "UTF-8") <= $this->getColorBackgroundCharacterLimit())
        {
            return $this->updateValue("ColorBackground", $colorBackground);
        }
        else
        {
            return false;
        }
    }

    public function getColorText(): string
    {
        return $this->readValue("ColorText");
    }

    public function getColorTextCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("AppearanceModel", "ColorText");
    }

    public function setColorText(string $colorText): bool
    {
        if(mb_strlen($colorText, "UTF-8") <= $this->getColorTextCharacterLimit())
        {
            return $this->updateValue("ColorText", $colorText);
        }
        else
        {
            return false;
        }
    }

    public function getColorLink(): string
    {
        return $this->readValue("ColorLink");
    }

    public function getColorLinkCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("AppearanceModel", "ColorLink");
    }

    public function setColorLink(string $colorLink): bool
    {
        if(mb_strlen($colorLink, "UTF-8") <= $this->getColorLinkCharacterLimit())
        {
            return $this->updateValue("ColorLink", $colorLink);
        }
        else
        {
            return false;
        }
    }

    public function getColorInteresting(): string
    {
        return $this->readValue("ColorInteresting");
    }

    public function getColorInterestingCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("AppearanceModel", "ColorInteresting");
    }

    public function setColorInteresting(string $colorInteresting): bool
    {
        if(mb_strlen($colorInteresting, "UTF-8") <= $this->getColorInterestingCharacterLimit())
        {
            return $this->updateValue("ColorInteresting", $colorInteresting);
        }
        else
        {
            return false;
        }
    }

    public function getColorUninteresting(): string
    {
        return $this->readValue("ColorUninteresting");
    }

    public function getColorUninterestingCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("AppearanceModel", "ColorUninteresting");
    }

    public function setColorUninteresting(string $colorUninteresting): bool
    {
        if(mb_strlen($colorUninteresting, "UTF-8") <= $this->getColorUninterestingCharacterLimit())
        {
            return $this->updateValue("ColorUninteresting", $colorUninteresting);
        }
        else
        {
            return false;
        }
    }

    public function getColorActionExecute(): string
    {
        return $this->readValue("ColorActionExecute");
    }

    public function getColorActionExecuteCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("AppearanceModel", "ColorActionExecute");
    }

    public function setColorActionExecute(string $colorActionExecute): bool
    {
        if(mb_strlen($colorActionExecute, "UTF-8") <= $this->getColorActionExecuteCharacterLimit())
        {
            return $this->updateValue("ColorActionExecute", $colorActionExecute);
        }
        else
        {
            return false;
        }
    }

    public function getColorActionAbort(): string
    {
        return $this->readValue("ColorActionAbort");
    }

    public function getColorActionAbortCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("AppearanceModel", "ColorActionAbort");
    }

    public function setColorActionAbort(string $colorActionAbort): bool
    {
        if(mb_strlen($colorActionAbort, "UTF-8") <= $this->getColorActionAbortCharacterLimit())
        {
            return $this->updateValue("ColorActionAbort", $colorActionAbort);
        }
        else
        {
            return false;
        }
    }

    public function getImageLogo(): string
    {
        return $this->readValue("ImageLogo");
    }

    public function getImageLogoCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("AppearanceModel", "ImageLogo");
    }

    public function setImageLogo(string $imageLogo): bool
    {
        if(mb_strlen($imageLogo, "UTF-8") <= $this->getImageLogoCharacterLimit())
        {
            return $this->updateValue("ImageLogo", $imageLogo);
        }
        else
        {
            return false;
        }
    }
}