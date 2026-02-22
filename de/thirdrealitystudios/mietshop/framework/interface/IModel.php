<?php

namespace de\thirdrealitystudios\mietshop\framework\interface;

interface IModel
{
    public function fetchAll() : ?array;

    public function fetch(mixed $key) : ?array;

    public function insert(array $data) : bool;

    public function delete(mixed $key) : bool;
}