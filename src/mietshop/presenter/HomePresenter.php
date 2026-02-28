<?php

namespace src\mietshop\presenter;

use src\mietshop\contracts\ICategoryModel;
use src\mietshop\contracts\ICompanyModel;
use src\mietshop\contracts\IHomeView;
use src\mietshop\contracts\IPresenter;
use src\mietshop\infrastructure\http\HTTPRequest;
use src\mietshop\view\HomeView;

class HomePresenter implements IPresenter
{
    private ICompanyModel $companyModel;
    private ICategoryModel $categoryModel;
    private HomeView $view;

    public function __construct(ICompanyModel $companyModel, ICategoryModel $categoryModel, IHomeView $view)
    {
        $this->companyModel = $companyModel;
        $this->categoryModel = $categoryModel;
        $this->view = $view;
    }

    public function process(HTTPRequest $request): void
    {
        $host = $request->getHeaderValue("host");

        if(is_string($host) && strlen($host) > 0)
        {
            // $this->companyModel->setCompanyName("Sehr cooles Unternehmen GmbH");
            // $this->companyModel->setOwner("Gutfried Alman");

            $companyName = $this->companyModel->getCompanyName();
            $owner = $this->companyModel->getOwner();

            $this->companyModel->setStreet("Kurwa Mac Str. 3a");

            $street = $this->companyModel->getStreet();
            $zipCode = $this->companyModel->getZipCode();
            $place = $this->companyModel->getPlace();

            $email = $this->companyModel->getEmail();

            $companyPhoneMobile = $this->companyModel->getPhoneMobile();
            $phoneNormal = $this->companyModel->getPhoneNormal();

            $mapLocationURL = $this->companyModel->getMapLocationURL();
            $facebookURL = $this->companyModel->getFacebookURL();
            $instagramURL = $this->companyModel->getInstagramURL();
            $whatsAppURL = $this->companyModel->getWhatsAppURL();
            $kleinanzeigenURL = $this->companyModel->getKleinanzeigenURL();

            $privacyPolicy = $this->companyModel->getPrivacyPolicy();
            $termsOfService = $this->companyModel->getTermsOfService();

            $categories = $this->categoryModel->readAllCategories();

            $this->view->show($host, $companyName, $owner, $street, $zipCode, $place, $email, $companyPhoneMobile, $phoneNormal, $mapLocationURL, $facebookURL, $instagramURL, $whatsAppURL, $kleinanzeigenURL, $privacyPolicy, $termsOfService, $categories);
        }
    }
}