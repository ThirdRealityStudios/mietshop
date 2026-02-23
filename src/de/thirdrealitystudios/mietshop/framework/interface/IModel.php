<?php

namespace src\de\thirdrealitystudios\mietshop\framework\interface;

interface IModel
{
    /**
     * Fetches all data from the corresponding source
     *
     * @return array|null An (empty or filled) array of all data if fetching was successful. NULL if fetching data was unsuccessful
     */
    public function fetchAll() : ?array;

    /**
     * Returns all keys to access all data elements.
     *
     * @return array|null An (empty or filled) array of all keys for data access if successful. NULL if getting the keys was unsuccessful
     */
    public function getKeys() : ?array;

    /**
     * Fetches the corresponding value,
     * using the given key.
     *
     * @param mixed $key The key used to fetch the value
     * @return mixed The value if fetching was successful. NULL if fetching was unsuccessful (e.g. value does not exist)
     */
    public function fetch(mixed $key) : mixed;

    /**
     * Updates the corresponding value with the given key.
     *
     * @param mixed $key The key used to find the old value.
     * @param mixed $value The value used to update the old one.
     * @return bool True if the update was successful. Otherwise, false.
     */
    public function update(mixed $key, mixed $value) : bool;

    /**
     * Inserts the given new value to the data.
     * The exact behavior depends on the implementation (e.g. append value at the end or at the beginning)
     *
     * @param array $value Value used to insert.
     * @return bool True if the insertion was successful. Otherwise, false.
     */
    public function insert(mixed $value) : bool;

    /**
     * Deletes the corresponding the value with the given key.
     *
     * @param mixed $key The key used to delete the desired value.
     * @return bool True if the deletion was successful. Otherwise, false.
     */
    public function delete(mixed $key) : bool;
}