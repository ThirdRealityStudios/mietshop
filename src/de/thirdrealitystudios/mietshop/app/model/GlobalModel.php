<?php

namespace src\de\thirdrealitystudios\mietshop\app\model;

use src\de\thirdrealitystudios\mietshop\framework\interface\IModel;

/**
 * Class which represents mostly company data,
 * which is displayed on almost every webpage or is legally mandatory,
 * such as imprint details (e.g. owner name, phone, e-mail),
 * your data privacy information,
 * your general business conditions (German: AGBs).
 */
class GlobalModel implements IModel
{
    public function fetchAll(): ?array
    {
        // TODO: Implement fetchAll() method.
    }

    public function getKeys(): ?array
    {
        // TODO: Implement getKeys() method.
    }

    public function fetch(mixed $key): mixed
    {
        // TODO: Implement fetch() method.
    }

    public function update(mixed $key, mixed $value): bool
    {
        // TODO: Implement update() method.
    }

    public function insert(mixed $value): bool
    {
        // TODO: Implement insert() method.
    }

    public function delete(mixed $key): bool
    {
        // TODO: Implement delete() method.
    }
}