<?php

namespace src\mietshop\model;

use src\mietshop\contracts\IGlobalModel;

/**
 * Class which represents mostly company data,
 * which is displayed on almost every webpage or is legally mandatory,
 * such as imprint details (e.g. owner name, phone, e-mail),
 * your data privacy information,
 * your general business conditions (German: AGBs).
 */
class GlobalGlobalModel implements IGlobalModel
{
    /**
     * @param mixed $value
     * @return bool
     */
    public function create(mixed $value): bool
    {
        // TODO: Implement create() method.
    }

    /**
     * @param mixed $key
     * @return mixed
     */
    public function read(mixed $key): mixed
    {
        // TODO: Implement read() method.
    }

    /**
     * @return array|null
     */
    public function readAll(): ?array
    {
        // TODO: Implement readAll() method.
    }

    /**
     * @return array|null
     */
    public function readKeys(): ?array
    {
        // TODO: Implement readKeys() method.
    }

    /**
     * @param mixed $key
     * @param mixed $value
     * @return bool
     */
    public function update(mixed $key, mixed $value): bool
    {
        // TODO: Implement update() method.
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function delete(mixed $key): bool
    {
        // TODO: Implement delete() method.
    }

    /**
     * @return bool
     */
    public function deleteAll(): bool
    {
        // TODO: Implement deleteAll() method.
    }
}