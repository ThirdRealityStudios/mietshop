<?php

namespace src\de\thirdrealitystudios\mietshop\infrastructure\interface;

/**
 * This interface defines typical CRUD functions for data access to an MVP model.
 */
interface IModel
{
    /**
     * Inserts the given new value at the given destination.
     * The exact behavior depends on the implementation (e.g. append value at the end or at the beginning)
     *
     * @param mixed $destination Where to insert the value at.
     * @param mixed $value Value used to insert.
     * @return bool True if the insertion was successful. Otherwise, false.
     */
    public function create(mixed $destination, mixed $value) : bool;

    /**
     * Fetches the corresponding value from the given source,
     * using the given key for identification.
     *
     * @param mixed $key The key used to fetch the value
     * @param mixed $source From where to get the value from
     * @return mixed The value if fetching was successful. NULL if fetching was unsuccessful (e.g. value does not exist)
     */
    public function read(mixed $key, mixed $source) : mixed;

    /**
     * Fetches all data from the given source.
     *
     * @return array|null An (empty or filled) array of all data if fetching was successful. NULL if fetching data was unsuccessful
     */
    public function readAll(mixed $source) : ?array;

    /**
     * Returns all keys to access all data elements from $source.
     *
     * @param mixed $source Where to read all keys from
     * @return array|null An (empty or filled) array of all keys for data access if successful. NULL if getting the keys was unsuccessful
     */
    public function readKeys(mixed $source) : ?array;

    /**
     * Updates the corresponding value with the given key at the given destination.
     *
     * @param mixed $key The key used to find the old value.
     * @param mixed $value The value used to update the old one.
     * @param mixed $destination Where to update the old value at with the given key.
     * @return bool True if the update was successful. Otherwise, false.
     */
    public function update(mixed $key, mixed $value, mixed $destination) : bool;

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