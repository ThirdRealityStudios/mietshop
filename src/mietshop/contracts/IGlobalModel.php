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
     * @return bool True if set successfully. Otherwise, false (technical issues or company name requirements might apply).
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
     * @return bool True if set successfully. Otherwise, false (technical issues or company owner requirements might apply).
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
     * @return bool True if set successfully. Otherwise, false (technical issues or street information requirements might apply).
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
     *
     * @return bool True if set successfully. Otherwise, false (technical issues or ZIP code requirements might apply).
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
     * @return bool True if set successfully. Otherwise, false (technical issues or place name requirements might apply).
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
     * @return bool True if set successfully. Otherwise, false (technical issues or phone number requirements might apply).
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
     * @return bool True if set successfully. Otherwise, false (technical issues or phone number requirements might apply).
     */
    public function setPhoneNormal(string $phoneNormal) : bool;

    /**
     * Gets the URL of a maps service to show customers the location of your company.
     * If Google Maps used,
     * you could expect here e.g. an embed link for your webpage.
     * Customers will directly see and maybe also recognize the way to your company.
     *
     * @return string Company location URL string. E.g. https://www.google.com/maps/embed?pb=!LocationOfMyCompany!2sde. Can be an empty string of no URL was set.
     */
    public function getCompanyMapLocation() : string;

    /**
     * Sets the URL to specify the location of your company;
     * usually a Google Maps embeddable link.
     *
     * @param string $companyMapLocation Company location URL
     * @return bool True if set successfully. Otherwise, false (technical issues or URL requirements might apply).
     */
    public function setCompanyMapLocation(string $companyMapLocation) : bool;

    /**
     * Returns the link to your Facebook webpage.
     *
     * @return string Link to your Facebook webpage. Can be an empty string if no link was set.
     */
    public function getFacebookLink() : string;

    /**
     * Sets the link to your Facebook webpage.
     * You can pass an empty string to signalize,
     * there is no Facebook webpage.
     *
     * @param string $facebookLink Link to your Facebook webpage.
     * @return bool True if set successfully. Otherwise, false (technical issues or URL requirements might apply).
     */
    public function setFacebookLink(string $facebookLink) : bool;

    /**
     * Returns the link to your Instagram page.
     *
     * @return string Link to your Instagram page. Can be an empty string if no link was set.
     */
    public function getInstagramLink() : string;

    /**
     * Sets the link to your Instagram page.
     * You can pass an empty string to signalize,
     * there is no Instagram page.
     *
     * @param string $instagramLink Link to your Instagram page.
     * @return bool True if set successfully. Otherwise, false (technical issues or URL requirements might apply).
     */
    public function setInstagramLink(string $instagramLink) : bool;

    /**
     * Returns the link to your WhatsApp contact.
     *
     * @return string Link to your WhatsApp contact. Can be an empty string if no link was set.
     */
    public function getWhatsAppLink() : string;

    /**
     * Sets the link to your WhatsApp contact.
     * You can pass an empty string to signalize,
     * there is no WhatsApp contact.
     *
     * @param string $whatsAppLink Link to your WhatsApp page.
     * @return bool True if set successfully. Otherwise, false (technical issues or URL requirements might apply).
     */
    public function setWhatsAppLink(string $whatsAppLink) : bool;

    /**
     * Returns the link to your Kleinanzeigen sellers page.
     *
     * @return string Link to your Kleinanzeigen sellers page. Can be an empty string if no link was set.
     */
    public function getKleinanzeigenLink() : string;

    /**
     * Sets the link to your Kleinanzeigen sellers page.
     * You can pass an empty string to signalize,
     * there is no sellers page for Kleinanzeigen.
     *
     * @param string $kleinanzeigenLink Link to your Kleinanzeigen page.
     * @return bool True if set successfully. Otherwise, false (technical issues or URL requirements might apply).
     */
    public function setKleinanzeigenLink(string $kleinanzeigenLink) : bool;

    /**
     * Returns the full description or information text of your data privacy.
     *
     * @return string Data privacy information. Or empty string if there is no such information (legally seen not good for your public presence).
     */
    public function getDataPrivacyInformation() : string;

    /**
     * Sets the full description or information text of your data privacy.
     *
     * @param string $dataPrivacyInformation Data privacy text to set.
     * @return bool True if set successfully. Otherwise, false (technical issues or text requirements might apply).
     */
    public function setDataPrivacyInformation(string $dataPrivacyInformation) : bool;

    /**
     * Returns the full description or information text of your common business conditions.
     *
     * @return string Common business conditions information. Or empty string if there is no such information (it is legal, but there is a higher risk of being sueable when providing products and services).
     */
    public function getCommonBusinessConditions() : string;

    /**
     * Sets the full description or information text of your common business conditions.
     *
     * @param string $commonBusinessConditions Common business conditions text to set.
     * @return bool True if set successfully. Otherwise, false (technical issues or text requirements might apply).
     */
    public function setCommonBusinessConditions(string $commonBusinessConditions) : bool;
}