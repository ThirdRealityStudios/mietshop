<?php

namespace src\de\thirdrealitystudios\mietshop\infrastructure\abstract;

use src\de\thirdrealitystudios\mietshop\domain\model\GlobalModel;
use src\de\thirdrealitystudios\mietshop\infrastructure\HTTPRequest;
use src\de\thirdrealitystudios\mietshop\infrastructure\interface\IModel;
use src\de\thirdrealitystudios\mietshop\infrastructure\interface\IView;

/**
 * You can use this abstract class to implement your own presenter which guides everything.
 * It will ensure that the view will stay "dumb" (as MVP pattern describes).
 * Meanwhile,
 * it was a design decision that the model will stay optional.
 * A model might not be necessary for static views which don't have data evaluation or access
 * (e.g. a webpage which has a simple legal note to data privacy).
 * Oppositely,
 * a model is usually necessary to implement things like an account view
 * (e.g. a webpage which shows your amount of friends or your username).
 * In the end,
 * it is up to you.
 */
abstract class AbstractPresenter
{
    private ?IModel $model;

    private GlobalModel $globalModel;

    private IView $view;

    /**
     * Create your presenter object.
     * You must call this constructor from the inheriting class,
     * as this is a super constructor.
     *
     * A view object is always expected.
     * Only exception is a model object if you don't need it (then pass NULL simply).
     *
     * @param IModel|NULL $model The model to be used but not necessary.
     * @param IView $view The view the presenter should call.
     */
    public function __construct(?IModel $model, GlobalModel $globalModel, IView $view)
    {
        $this -> model = $model;
        $this -> globalModel = $globalModel;
        $this -> view = $view;
    }

    protected function getModel() : ?IModel
    {
        return $this -> model;
    }

    protected function getGlobalModel() : GlobalModel
    {
        return $this -> globalModel;
    }

    protected function getView() : IView
    {
        return $this -> view;
    }

    /**
     * Processes a request or user input.
     *
     * Uses necessary data from a prepared HTTP request to trigger the appropriate view or model,
     * as described in MVP patterns.
     * This method may directly invoke or select the suitable view,
     * or first interact with the correct model,
     * await its response,
     * and then decide which view to render or which data to provide.
     * The chosen view will not have direct access to the model (MVP style - so only access to provided data).
     *
     * It was a language-independent design decision not to work with the PHP globals inside the function process(..),
     * e.g. $_SERVER, $_GET or $_POST.
     * Hence,
     * you could easily implement everything in a different language too.
     * You only have to adjust the creation ob a HTTPRequest object.
     *
     * @param HTTPRequest $request
     * @return void
     */
    public abstract function process(HTTPRequest $request) : void;
}