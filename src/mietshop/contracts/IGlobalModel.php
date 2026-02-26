<?php

namespace src\mietshop\contracts;

/**
 * This interface defines typical CRUD functions for data access for global / common business data,
 * including company details (e.g. name of business, owner, contact) and legal notes (e.g. data privacy information text, common business conditions [German: AGBs]).
 * This information is usually visible on most views (or in this context: webpages).
 */
interface IGlobalModel
{
    /**
     * Returns the complete name of your company,
     * usually including the legal note,
     * e.g. the "Inc." in "Microsoft Inc.".
     * Or another German example is "Bayer AG",
     * whereas "AG" is a hint it is a stock-traded company.
     *
     * @return string The complete name of your company.
     */
    public function getCompanyName() : string;

    /**
     * Sets the complete name of your company,
     * e.g. "Microsoft Inc." or "Bayer AG".
     *
     * @param string $companyName The complete name of your company.
     * @return bool True if the company name could bet set. Otherwise, false (regard potential limitations or name requirements).
     */
    public function setCompanyName(string $companyName) : bool;

    /**
     * Returns the owner of your company,
     * e.g. "John Smith" or "Max Mustermann".
     *
     * @return string The surname with the lastname of the company owner.
     */
    public function getCompanyOwner() : string;

    /**
     * Sets the name (surname and lastname) of the company owner.
     *
     * @param string $companyOwner Name of the owner.
     * @return bool True if it could be set successfully. Otherwise, false (name requirements or limitations might apply).
     */
    public function setCompanyOwner(string $companyOwner) : bool;

    /**
     * Returns the street of your company,
     * including house number etc. (country-dependent).
     * In Germany e.g.: "Gustav-Str. 44b".
     *
     * @return string The street of the company, including number etc. (country-dependent).
     */
    public function getCompanyStreet() : string;

    /**
     * Sets the street of your company.
     * The street not only includes the name only here,
     * but also additional information,
     * such as the house number (it depends on the country).
     * So,
     * the full street information is required here.
     *
     * @param string $companyStreet The full information of the street of the company.
     * @return bool True if set successfully. Otherwise, false (limitations or requirements might apply to the given street information).
     */
    public function setCompanyStreet(string $companyStreet) : bool;

    /**
     * Gets the ZIP code of the company.
     * Usually a short sequence of numbers but might be country-dependent.
     * Hence,
     * you receive a string (also for better information processing).
     * In Germany e.g. the format: "12345".
     *
     * @return string The ZIP code of the company as a string.
     */
    public function getCompanyZipCode() : string;

    /**
     * Sets the ZIP code of the company.
     * Usually a short sequence of numbers but might be country-dependent.
     * Hence,
     * you set a string.
     * In Germany e.g. the format: "12345".
     */
    public function setCompanyZipCode(string $companyZipCode) : bool;

    /**
     * Returns the place name of the company,
     * e.g. "Munich".
     *
     * @return string Place of the company.
     */
    public function getCompanyPlace() : string;

    /**
     * Sets the place name of the company.
     *
     * @param string $companyPlace Name of the place.
     * @return bool True if set successfully. Otherwise, false (limitations or requirements might apply to the given place name).
     */
    public function setCompanyPlace(string $companyPlace) : bool;

    /**
     * Returns the mobile phone number which should be visible on most views (or webpages).
     * It is usually the one to easily call the company owner,
     * e.g. for potential customers to ask for a service or product quickly.
     *
     * @return string The mobile phone number.
     */
    public function getPhoneMobile() : string;

    /**
     * Sets the mobile phone number for your company.
     * It is the one which is shown on most views (or webpages).
     * It enables customers the opportunity to easily ask for services or products.
     * Usually,
     * the number of the company owner.
     * You might want to include the country code,
     * like "+49",
     * if necessary.
     *
     * @param string $phoneMobile The mobile phone number.
     * @return bool True if set successfully. Otherwise, false (limitations or requirements might apply to the given phone number).
     */
    public function setPhoneMobile(string $phoneMobile) : bool;

    /**
     * Returns the normal phone number (not mobile - usually stationary or in your office room) which should be visible on most views (or webpages).
     * It is usually the one to easily call the company owner,
     * e.g. for potential customers to ask for a service or product.
     *
     * @return string The normal phone number.
     */
    public function getPhoneNormal() : string;

    /**
     * Sets the mobile phone number for your company.
     * It is the one which is shown on most views (or webpages).
     * It enables customers the opportunity to easily ask for services or products.
     * Usually,
     * you would use the number to let customers call you in your office room.
     * You might want to include the country code,
     * like "+49",
     * if necessary.
     *
     * @param string $phoneNormal The normal phone number.
     * @return bool True if set successfully. Otherwise, false (limitations or requirements might apply to the given phone number).
     */
    public function setPhoneNormal(string $phoneNormal) : bool;

    /**
     * Gets the URL of a maps service to show customers the location of your company.
     * If Google Maps used,
     * you could expect here e.g. an embed link for your webpage.
     * Customers will directly see and maybe also recognize the way to your company.
     *
     * @return string Company location maps service URL string. E.g. https://www.google.com/maps/embed?pb=!LocationOfMyCompany!2sde
     */
    public function getCompanyMapLocation() : string;

    /**
     *
     *
     * @param string $companyMapLocation
     * @return bool
     */
    public function setCompanyMapLocation(string $companyMapLocation) : bool;

    public function getFacebookLink() : string;

    public function setFacebookLink(string $facebookLink) : bool;

    public function getInstagramLink() : string;

    public function setInstagramLink(string $instagramLink) : bool;

    public function getWhatsAppLink() : string;

    public function setWhatsAppLink(string $whatsAppLink) : bool;

    public function getKleinanzeigenLink() : string;

    public function setKleinanzeigenLink(string $kleinanzeigenLink) : bool;

    public function getDataPrivacyInformation() : string;

    public function setDataPrivacyInformation(string $dataPrivacyInformation) : bool;

    public function geTCommonBusinessConditions() : string;

    public function setCommonBusinessConditions(string $commonBusinessConditions) : bool;
}