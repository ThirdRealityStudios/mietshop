<?php

namespace src\mietshop\model;

use PDOException;
use src\mietshop\contracts\ICategoryModel;
use src\mietshop\infrastructure\database\IDatabaseAdapter;

class CategoryModel implements ICategoryModel
{
    private $databaseAdapter;

    /**
     * @param IDatabaseAdapter $databaseAdapter
     * @throws PDOException
     */
    public function __construct(IDatabaseAdapter $databaseAdapter)
    {
        $this->databaseAdapter = $databaseAdapter;

        $sql =
        "
        CREATE TABLE IF NOT EXISTS CategoryModel
        (
            ID INT(8) UNIQUE AUTO_INCREMENT PRIMARY KEY,
            
            CategoryName VARCHAR(20) NOT NULL DEFAULT \"Default\"
        );
        ";

        $this->databaseAdapter->execute($sql);
    }

    public function readCategoryName(int $id): string
    {
        $sql = "SELECT CCategoryModel.CategoryName AS Name FROM CategoryModel WHERE CategoryModel.ID = ?";

        $fetchData = $this->databaseAdapter->fetch($sql, [$id]);

        if(sizeof($fetchData) > 0)
        {
            return $fetchData[0]["Name"];
        }

        return "";
    }

    public function readAllCategories(): array
    {
        $sql = "SELECT * FROM CategoryModel";

        $fetchData = $this->databaseAdapter->fetch($sql);

        $categoryData = [];

        if(sizeof($fetchData) > 0)
        {
            foreach($fetchData as $dataset)
            {
                $categoryData[$dataset["ID"]] = $dataset["CategoryName"];
            }
        }

        return $categoryData;
    }

    public function createCategory(string $name): bool
    {
        $sql = "INSERT INTO CategoryModel(CategoryName) VALUES(?)";

        return $this->databaseAdapter->execute($sql, [$name]);
    }

    public function readCategoryCharacterLimit(): int
    {
        return $this->databaseAdapter->readCharacterLimit("CategoryModel", "CategoryName");
    }

    public function deleteCategory(int $id): bool
    {
        $sql = "DELETE FROM CategoryModel WHERE ID = ?";

        return $this->databaseAdapter->execute($sql, [$id]);
    }
}