<?php

namespace src\mietshop\contracts;

interface IHomeView
{
    /**
     * Shows the landing page,
     * using an IGlobalView instance provided in the constructor.
     * Also expects the host, one associative array of company data and a numerical-indexed array of categories available on the website for articles.
     * The associative array of company data should look like this (make sure to check elsewhere in code if this array is outdated - then you are 100% safe):
     *
     *  [
     *
     *   "CompanyName" => ["My company Inc.", 50],
     *
     *   "Owner" => ["John Smith", 40],
     *
     *   "Street" => ["Frankfurt Avenue 21st", 40],
     *
     *   "ZipCode" => ["12345", 5],
     *
     *   "Place" => ["German Town", 20],
     *
     *   "Email" => ["mymail@web.com", 100],
     *
     *   "PhoneMobile" => ["+49 123 4567891", 16],
     *
     *   "PhoneNormal" => ["123 555 4567891", 16],
     *
     *   "MapLocationURL" => ["https://maps.google.com/whatever", 1000],
     *
     *   "FacebookURL" => ["https://facebook.com/mypage", 500],
     *
     *   "InstagramURL" => ["https://instagram.com/mycookingpage", 200],
     *
     *   "WhatsAppURL" => ["https://whatsapp.com/mynumber", 150],
     *
     *   "KleinanzeigenURL" => ["https://kleinanzeigen.de/mysellerpage", 150],
     *
     *   "PrivacyPolicy" => ["We process this and this for better user experience", 65535],
     *
     *   "TermsOfService" => ["If you rent our objects, make sure to clean it before return", 65535]
     *
     *  ]
     *
     *
     * @param array $companyData
     * @param array $appearanceData
     * @param array $categories
     */
    public function show(array $companyData, array $appearanceData, array $categories): void;
}