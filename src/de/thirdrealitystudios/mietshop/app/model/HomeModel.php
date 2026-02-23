<?php

namespace src\de\thirdrealitystudios\mietshop\app\model;

use src\de\thirdrealitystudios\mietshop\framework\interface\IModel;

class HomeModel implements IModel
{
    public function __construct()
    {

    }

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