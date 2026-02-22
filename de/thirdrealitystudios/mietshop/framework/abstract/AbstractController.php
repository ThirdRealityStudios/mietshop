<?php

namespace de\thirdrealitystudios\mietshop\framework\abstract;

use de\thirdrealitystudios\mietshop\framework\interface\IController;
use de\thirdrealitystudios\mietshop\framework\interface\IModel;
use de\thirdrealitystudios\mietshop\framework\interface\IView;

abstract class AbstractController implements IController
{
    public function __construct(IModel $model, IView $view)
    {
        
    }
}