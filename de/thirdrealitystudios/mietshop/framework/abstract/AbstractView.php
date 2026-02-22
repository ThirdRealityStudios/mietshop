<?php

namespace de\thirdrealitystudios\mietshop\framework\abstract;

use de\thirdrealitystudios\mietshop\framework\interface\IModel;
use de\thirdrealitystudios\mietshop\framework\interface\IView;

abstract class AbstractView implements IView
{
    public function __construct(IModel $model)
    {

    }
}