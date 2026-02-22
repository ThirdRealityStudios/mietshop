<?php

namespace de\thirdrealitystudios\mietshop\framework\interface;

interface IView
{
    /**
     * Displays the view.
     *
     * Expects an associative array containing all necessary data for rendering.
     * The exact structure of the array depends on the specific implementation,
     * and should be further documented in the implementing class.
     *
     * @param array $data Associative array of data to be used by the view.
     * @return void
     */
    public function show(array $data) : void;
}