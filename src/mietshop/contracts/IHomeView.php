<?php

namespace src\mietshop\contracts;

interface IHomeView
{
    /**
     * Shows the landing page,
     * using an IGlobalView instance provided in the constructor.
     */
    public function show() : void;
}