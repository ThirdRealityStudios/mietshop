<?php

namespace de\thirdrealitystudios\mietshop\framework\interface;

interface IController
{
    /**
     * Processes a request or user input.
     *
     * Prepares and handles necessary data from GET and POST arrays to
     * trigger the appropriate view or model,
     * as described in MVC patterns.
     * This method may directly invoke or select the suitable view,
     * or first interact with the correct model,
     * await its response,
     * and then decide which view to render or which data to provide.
     *
     * @param array $get  Associative array representing GET parameters.
     * @param array $post Associative array representing POST parameters.
     * @return void
     */
    public function process(array $get, array $post) : void;

    public function __construct(IModel $model, IView $view);
}