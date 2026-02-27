<?php

namespace src\mietshop\contracts;

interface IView
{

    public function show(array $data = []) : void;
}