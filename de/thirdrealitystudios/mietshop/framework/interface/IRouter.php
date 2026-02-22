<?php

namespace de\thirdrealitystudios\mietshop\framework\interface;

interface IRouter
{
    public function route(array $get, array $post) : void;
}