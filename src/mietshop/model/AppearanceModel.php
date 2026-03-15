<?php

namespace src\mietshop\model;

use PDOException;
use src\mietshop\contracts\IAppearanceModel;
use src\mietshop\contracts\IUserInputModel;
use src\mietshop\infrastructure\database\IDatabaseAdapter;

/**
 * CREATE TABLE IF NOT EXISTS AppearanceModel
 * (
 * ID INT(8) AUTO_INCREMENT PRIMARY KEY UNIQUE,
 *
 * ColorFrame TINYTEXT NOT NULL DEFAULT \"#0D6EFD\",
 * ColorWindow TINYTEXT NOT NULL DEFAULT \"#CFE2FF\",
 * ColorBackground TINYTEXT NOT NULL DEFAULT \"#E9ECEF\",
 * ColorText TINYTEXT NOT NULL DEFAULT \"#212529\",
 * ColorLink TINYTEXT NOT NULL DEFAULT \"#0D6EFD\",
 * ColorInteresting TINYTEXT NOT NULL DEFAULT \"#0D6EFD\",
 * ColorUninteresting TINYTEXT NOT NULL DEFAULT \"#6C757D\",
 * ColorActionExecute TINYTEXT NOT NULL DEFAULT \"#198754\",
 * ColorActionAbort TINYTEXT NOT NULL DEFAULT \"#DC3545\",
 * ImageLogo TINYTEXT NOT NULL DEFAULT \"/img/logo/company/logo.png\"
 * );
 */
class AppearanceModel implements IAppearanceModel
{
    private IUserInputModel $userInputModel;
    private IDatabaseAdapter $databaseAdapter;

    private function genTableCreateColumnSQLText(string $elementTypeName, string $elementName)
    {
        return ", $elementTypeName$elementName TINYTEXT NOT NULL DEFAULT \"\"";
    }

    private function genTableCreateColumnsSQLText(array $acceptedElements)
    {
        $sqlCreateColumns = "";

        $elementTypeNames = array_keys($acceptedElements);

        foreach($elementTypeNames as $elementTypeName)
        {
            $elementNames = $acceptedElements[$elementTypeName];

            foreach($elementNames as $elementName)
            {
                $sqlCreateColumns .= $this->genTableCreateColumnSQLText($elementTypeName, $elementName);
            }
        }

        return $sqlCreateColumns;
    }

    /**
     * @param IDatabaseAdapter $databaseAdapter Database adapter. Make sure you have connected yet.
     * @throws PDOException
     */
    public function __construct(IUserInputModel $userInputModel, IDatabaseAdapter $databaseAdapter)
    {
        $this->databaseAdapter = $databaseAdapter;
        $this->userInputModel = $userInputModel;

        // Map the IAppearanceModel::ACCEPTED_ELEMENTS to a suitable SQL database scheme text.
        $necessaryCreateTableColumns = $this->genTableCreateColumnsSQLText(IAppearanceModel::ACCEPTED_ELEMENTS);

        /**     /img/logo/company/logo.png is used internally for column ImageLogo      */
        $sql =
        "
	    CREATE TABLE IF NOT EXISTS AppearanceModel
		(
		    ID INT(8) AUTO_INCREMENT PRIMARY KEY UNIQUE
			$necessaryCreateTableColumns
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

    public function getColor(string $elementName): string
    {
        return $this->readValue("Color$elementName");
    }

    public function getColorCharacterLimit(string $elementName): int
    {
        return $this->databaseAdapter->readCharacterLimit("AppearanceModel", "Color$elementName");
    }

    public function setColor(string $colorCode, string $elementName): bool
    {
        if(($this->userInputModel->isValidHexRGBCode($colorCode) || in_array($colorCode, IAppearanceModel::ACCEPTED_COLORS)) && mb_strlen($colorCode, "UTF-8") <= $this->getColorCharacterLimit($elementName))
        {
            return $this->updateValue("Color$elementName", $colorCode);
        }
        else
        {
            return false;
        }
    }

    public function getImage(string $elementName): string
    {
        return $this->readValue("Image$elementName");
    }

    public function getImageCharacterLimit(string $elementName): int
    {
        return $this->databaseAdapter->readCharacterLimit("AppearanceModel", "Image$elementName");
    }

    public function setImage(string $elementName, string $imagePath): bool
    {
        // TODO Check if $imagePath reflects a valid path (by webserver syntax only)
        if(mb_strlen($imagePath, "UTF-8") <= $this->getImageCharacterLimit($elementName))
        {
            return $this->updateValue("Image$elementName", $imagePath);
        }
        else
        {
            return false;
        }
    }
}