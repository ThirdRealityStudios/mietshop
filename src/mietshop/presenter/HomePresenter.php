<?php

namespace src\mietshop\presenter;

use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IPresenter;
use src\mietshop\infrastructure\http\HTTPRequest;
use src\mietshop\view\HomeView;

class HomePresenter implements IPresenter
{
    private ICompanyModel $model;
    private HomeView $view;

    public function __construct(ICompanyModel $model, HomeView $view)
    {
        $this->model = $model;
        $this->view = $view;
    }

    public function process(HTTPRequest $request): void
    {
        $host = $request->getHeaderValue("host");

        if(is_string($host) && strlen($host) > 0)
        {
            $this->model->setCompanyName("Sehr cooles Unternehmen GmbH");
            $this->model->setOwner("Gutfried Alman");

            $companyName = $this->model->getCompanyName();
            $owner = $this->model->getOwner();

            $this->model->setStreet("Maximiliand Str. 3a");

            $street = $this->model->getStreet();
            $zipCode = $this->model->getZipCode();
            $place = $this->model->getPlace();

            $email = $this->model->getEmail();

            $companyPhoneMobile = $this->model->getPhoneMobile();
            $phoneNormal = $this->model->getPhoneNormal();

            $mapLocationURL = $this->model->getMapLocationURL();
            $facebookURL = $this->model->getFacebookURL();
            $instagramURL = $this->model->getInstagramURL();
            $whatsAppURL = $this->model->getWhatsAppURL();
            $kleinanzeigenURL = $this->model->getKleinanzeigenURL();

            $privacyPolicy = $this->model->getPrivacyPolicy();
            $termsOfService = $this->model->getTermsOfService();

            $this->view->show($host, $companyName, $owner, $street, $zipCode, $place, $email, $companyPhoneMobile, $phoneNormal, $mapLocationURL, $facebookURL, $instagramURL, $whatsAppURL, $kleinanzeigenURL, $privacyPolicy, $termsOfService);
        }
    }
}