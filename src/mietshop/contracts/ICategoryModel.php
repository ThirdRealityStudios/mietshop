<?php

namespace src\mietshop\contracts;

interface ICategoryModel
{
    /**
     * Returns a category by its name as a string,
     * using the given ID to find it.
     *
     * @return string Category name as a string. If not found or other issues, empty string.
     */
    public function readCategoryName(int $id) : string;

    /**
     * Returns all existing categories as an associative array,
     * having the structure:
     *
     * [
     *  0 => "Category1",
     *  1 => "Category2",
     *  4 => "Category3",
     *  9 => "Category4",
     *  10 => "Category5"
     * ]
     *
     * The number represents the ID in the database table,
     * followed by the category name value.
     *
     * @return array Associative array representing all categories. Empty array if query has no result or technical issues.
     */
    public function readAllCategories() : array;

    /**
     * Returns the character limit for a category name you can create or update.
     *
     * @return int The maximum amount of characters for a category name. -1 if there was a technical error.
     */
    public function readCategoryCharacterLimit() : int;

    /**
     * Creates a category for articles.
     *
     * @param string $name The name of your new category.
     * @return bool True, if category successfully created. Otherwise, false (e.g. if technical issues).
     */
    public function createCategory(string $name) : bool;
}