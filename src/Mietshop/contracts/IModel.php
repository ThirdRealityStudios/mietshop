<?php

namespace src\mietshop\contracts;

/**
 * This contracts defines typical CRUD functions for data access to an MVP model.
 */
interface IModel
{
    /**
     * Inserts the given new values.
     * The exact behavior depends on the implementation (e.g. append value at the end or at the beginning)
     *
     * @param array $values Values used to insert.
     * @return bool True if the insertion was successful. Otherwise, false.
     */
    public function create(array $values) : bool;

    /**
     * Fetches the corresponding array of values (e.g. dataset),
     * using the given key for identification.
     *
     * @param mixed $key The key used to fetch the value
     * @return array|null The array of all values if fetching was successful. Empty array if no results. NULL if the process of fetching was unsuccessful for technical reasons
     */
    public function read(mixed $key) : ?array;

    /**
     * Fetches all datasets.
     *
     * @return array|null An (empty or filled) array of all data if fetching was successful. NULL if the process of fetching was unsuccessful for technical reasons
     */
    public function readAll() : ?array;

    /**
     * Returns all keys to access all data elements.
     *
     * @return array|null An (empty or filled) array of all keys for data access if successful. NULL if fetching the keys was unsuccessful for technical reasons
     */
    public function readKeys() : ?array;

    /**
     * Updates the corresponding value with the given key.
     *
     * @param mixed $key The key used to find the old value.
     * @param mixed $values The values used to update the old one.
     * @return bool True if the update was successful. Otherwise, false.
     */
    public function update(mixed $key, array $values) : bool;

    /**
     * Deletes the corresponding the value with the given key.
     *
     * @param mixed $key The key used to delete the desired value.
     * @param mixed $destination Where to delete the value, using key for identification.
     * @return bool True if the deletion was successful. Otherwise, false.
     */
    public function delete(mixed $key, mixed $destination) : bool;

    /**
     * Deletes all data regardlessly at the given destination (handle with caution!)
     *
     * @param mixed $destination Where to delete everything at.
     * @return bool True if the deletion was successful. Otherwise, false.
     */
    public function deleteAll(mixed $destination) : bool;
}