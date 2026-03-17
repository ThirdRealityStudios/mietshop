<?php

namespace src\mietshop\model;

use PDOException;
use src\mietshop\contracts\IAppearanceModel;
use src\mietshop\contracts\IUserInputModel;
use src\mietshop\infrastructure\database\IDatabaseAdapter;

class AppearanceModel implements IAppearanceModel
{
    private IUserInputModel $userInputModel;
    private IDatabaseAdapter $databaseAdapter;

    private function genTableCreateColumnSQLText(string $elementTypeName, string $elementName) : string
    {
        return ", $elementTypeName$elementName TINYTEXT NOT NULL";
    }

    private function genTableCreateColumnsSQLText(array $acceptedElements) : string
    {
        $sqlCreateColumns = "";

        $elementTypes = array_keys($acceptedElements);

        foreach($elementTypes as $elementType)
        {
            $elementNames = $acceptedElements[$elementType];

            $elementNameKeys = array_keys($elementNames);

            foreach($elementNameKeys as $elementNameKey)
            {
                $sqlCreateColumns .= $this->genTableCreateColumnSQLText($elementType, $elementNameKey);
            }
        }

        return $sqlCreateColumns;
    }

    /**
     * Constructs an array of all concatenated column names,
     * e.g. concatenating "Image" with "Logo" (= "ImageLogo" as one element of the returned array).
     *
     * @param array $acceptedElements
     * @return array
     */
    private function genSQLColumnNames(array $acceptedElements) : array
    {
        $columns = [];

        $elementTypeNames = array_keys($acceptedElements);

        foreach($elementTypeNames as $elementTypeName)
        {
            $elementNames = array_keys($acceptedElements[$elementTypeName]);

            foreach($elementNames as $elementName)
            {
                $columns[] = "$elementTypeName$elementName";
            }
        }

        return $columns;
    }

    private function genValuesForInsert(array $acceptedElements) : string
    {
        $sql = "";

        $columnNames = $this->genSQLColumnNames($acceptedElements);

        for($count = 0; $count < sizeof($columnNames); $count++)
        {
            $bootstrapColorChosen = IAppearanceModel::ACCEPTED_BOOTSTRAP_COLORS[$count % sizeof(IAppearanceModel::ACCEPTED_BOOTSTRAP_COLORS)];

            $sql .= ($count < (sizeof($columnNames)-1)) ? "'$bootstrapColorChosen', " : "'$bootstrapColorChosen'";
        }

        return $sql;
    }

    private function genColumnsForInsert(array $acceptedElements) : string
    {
        $sql = "";

        $columns = $this->genSQLColumnNames($acceptedElements);

        for($count = 0; $count < sizeof($columns); $count++)
        {
            if($count < (sizeof($columns)-1))
            {
                $sql .= $columns[$count].', ';
            }
            else
            {
                $sql .= $columns[$count];
            }
        }

        return $sql;
    }

    private function insertDefaultDatasetIfNotExists(array $acceptedElements) : string
    {
        $columns = $this->genColumnsForInsert($acceptedElements);
        $values = $this->genValuesForInsert($acceptedElements);

        $sql =
        "
            INSERT IGNORE INTO AppearanceModel($columns) VALUES($values)
        ";

        return $sql;
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
            $sql = $this->insertDefaultDatasetIfNotExists(IAppearanceModel::ACCEPTED_ELEMENTS);

            $this->databaseAdapter->execute($sql);
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
        // See if the given color is valid color (whether an accepted Bootstrap color name or an accepted RGB(A) hex color code).
        if( (($this->userInputModel->isValidHexRGBCode($colorCode) || in_array($colorCode, IAppearanceModel::ACCEPTED_BOOTSTRAP_COLORS)) && (mb_strlen($colorCode, "UTF-8") <= $this->getColorCharacterLimit($elementName))) )
        {
            if(empty($colorCode))
            {
                if(sizeof(IAppearanceModel::ACCEPTED_BOOTSTRAP_COLORS) > 0)
                {
                    $colorCode = IAppearanceModel::ACCEPTED_BOOTSTRAP_COLORS[0];
                }
                else
                {
                    $colorCode = "#000000";
                }
            }

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

    public function getColorElements() : array
    {
        return array_keys(IAppearanceModel::ACCEPTED_ELEMENTS["Color"]);
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

    public function getAppearanceData(): array
    {
        $colorElements = $this->getColorElements();

        $preparedArray = [];

        foreach($colorElements as $colorElement)
        {
            $savedColor = $this->getColor($colorElement);
            $isCustom = $this->userInputModel->isValidHexRGBCode($savedColor);

            $classCode = "";
            $styleCode = "";

            if(strcmp($colorElement, "Frame") === 0)
            {
                if($isCustom)
                {
                    $styleCode = "border-color: $savedColor;";
                }
                else
                {
                    $classCode = "border-$savedColor";
                }

            }
            else if(strcmp($colorElement, "Window") === 0 || strcmp($colorElement, "Background") === 0)
            {
                if($isCustom)
                {
                    $styleCode = "background-color: $savedColor;";
                }
                else
                {
                    $classCode = "bg-$savedColor";
                }
            }
            else if(strcmp($colorElement, "Text") === 0)
            {
                if($isCustom)
                {
                    $styleCode = "color: $savedColor;";
                }
                else
                {
                    $classCode = "text-$savedColor";
                }
            }
            else if(strcmp($colorElement, "Link") === 0)
            {
                if($isCustom)
                {
                    $styleCode = "color: $savedColor;";
                }
                else
                {
                    $classCode = "link-$savedColor";
                }
            }
            else if(strcmp($colorElement, "Interesting") === 0 || strcmp($colorElement, "Uninteresting") === 0 || strcmp($colorElement, "ActionExecute") === 0 || strcmp($colorElement, "ActionAbort") === 0)
            {
                if($isCustom)
                {
                    $styleCode = "background-color: $savedColor;";
                }
                else
                {
                    $classCode = "btn-$savedColor";
                }
            }

            $preparedArray["Color"][$colorElement] = [$classCode, $styleCode];
        }

        return $preparedArray;
    }
}