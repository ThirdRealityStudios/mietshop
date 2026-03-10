<?php

namespace src\mietshop\contracts;

/**
 * Interface for accessing global business data.
 *
 * Covers company details (name, owner, address, contact info) and legal notes
 * (privacy policy, general business conditions).
 * Typically,
 * this information is displayed on most views or webpages.
 * If you implement this interface,
 * it is recommended to optimize the get functions for faster data access.
 */
interface ICompanyModel
{
    /**
     * Returns the full company name, including legal form (e.g., "Inc.", "AG").
     *
     * @return string Full company name.
     */
    public function getCompanyName(): string;

    /**
     * Returns the maximum amount of characters for your company name.
     *
     * @return int Company name text limit. -1 if there was a technical error.
     */
    public function getCompanyNameCharacterLimit(): int;

    /**
     * Sets the full company name, including legal form.
     *
     * @param string $companyName Full company name.
     * @return bool True on success, false on failure.
     */
    public function setCompanyName(string $companyName): bool;

    /**
     * Returns the company owner's full name (first and last name).
     *
     * @return string Owner's name.
     */
    public function getOwner(): string;

    /**
     * Returns the maximum amount of characters for your company owner.
     *
     * @return int Company owner text limit. -1 if there was a technical error.
     */
    public function getOwnerCharacterLimit(): int;

    /**
     * Sets the company owner's full name.
     *
     * @param string $companyOwner Owner's full name.
     * @return bool True on success, false on failure.
     */
    public function setOwner(string $companyOwner): bool;

    /**
     * Returns the full street address of the company (including house number).
     *
     * @return string Street address.
     */
    public function getStreet(): string;

    /**
     * Returns the maximum amount of characters for your company street name.
     *
     * @return int Company street name text limit. -1 if there was a technical error.
     */
    public function getStreetCharacterLimit(): int;

    /**
     * Sets the company's street address.
     *
     * @param string $street Full street address.
     * @return bool True on success, false on failure.
     */
    public function setStreet(string $street): bool;

    /**
     * Returns the company's ZIP/postal code.
     *
     * @return string ZIP code.
     */
    public function getZipCode(): string;

    /**
     * Returns the maximum amount of characters for your company ZIP code.
     *
     * @return int Company ZIP code text limit. -1 if there was a technical error.
     */
    public function getZipCodeCharacterLimit(): int;

    /**
     * Sets the company's ZIP/postal code.
     *
     * @param string $companyZipCode ZIP code.
     * @return bool True on success, false on failure.
     */
    public function setZipCode(string $companyZipCode): bool;

    /**
     * Returns the company location/city.
     *
     * @return string City or place name.
     */
    public function getPlace(): string;

    /**
     * Returns the maximum amount of characters for your company place name.
     *
     * @return int Company place character limit. -1 if there was a technical error.
     */
    public function getPlaceCharacterLimit(): int;

    /**
     * Sets the company location/city.
     *
     * @param string $companyPlace City or place name.
     * @return bool True on success, false on failure.
     */
    public function setPlace(string $companyPlace): bool;

    /**
     * Returns the company's email address.
     *
     * @return string Email contact address.
     */
    public function getEmail(): string;

    /**
     * Returns the maximum amount of characters for your company's email address.
     *
     * @return int Email address character limit. -1 if there was a technical error.
     */
    public function getEmailCharacterLimit(): int;

    /**
     * Sets the company's email address.
     *
     * @param string $email Email address.
     * @return bool True on success, false on failure.
     */
    public function setEmail(string $email): bool;

    /**
     * Tells you if paragraph 19 of German UStG is needed (e.g. for the imprint),
     * based on what setting was set.
     *
     * @return bool True if the paragraph 19 was set to 'true' internally. Otherwise, false (also if execution failed and such).
     */
    public function isP19UStGNeeded(): bool;

    /**
     * Sets whether 'true' or 'false' for German paragraph 19 UStG if it is needed,
     * e.g. to automatically generate the suitable hint text for the imprint page.
     * It will then be remembered.
     *
     * @param bool $isNeeded Is the paragraph 19 UStG needed or does it apply to your company?
     * @return bool True if it could be set, otherwise false on failure (e.g. execution).
     */
    public function setP19UStGNeeded(bool $isNeeded): bool;

    /**
     * Tells you if paragraph 19 of German UStG is needed (e.g. for the imprint),
     * based on what setting was set.
     *
     * @return bool True if the paragraph 19 was set to 'true' internally. Otherwise, false (also if execution failed and such).
     */
    public function isP36VSBGNeeded(): bool;

    /**
     * Sets whether 'true' or 'false' for German paragraph 36 VSBG if it is needed,
     * e.g. to automatically generate the suitable hint text for the imprint page.
     * It will then be remembered.
     *
     * @param bool $isNeeded Is the paragraph 36 VSBG needed or does it apply to your company?
     * @return bool True if it could be set, otherwise false on failure (e.g. execution).
     */
    public function setP36VSBGNeeded(bool $isNeeded): bool;

    /**
     * Returns the company's mobile phone number.
     *
     * @return string Mobile number (with optional country code).
     */
    public function getPhoneMobile(): string;

    /**
     * Returns the maximum amount of characters for your company mobile phone number.
     *
     * @return int Company mobile phone number character limit. -1 if there was a technical error.
     */
    public function getPhoneMobileCharacterLimit(): int;

    /**
     * Sets the company's mobile phone number.
     *
     * @param string $phoneMobile Mobile number (with optional country code).
     * @return bool True on success, false on failure.
     */
    public function setPhoneMobile(string $phoneMobile): bool;

    /**
     * Returns the company's landline/office phone number.
     *
     * @return string Landline number.
     */
    public function getPhoneNormal(): string;

    /**
     * Returns the maximum amount of characters for your landline/office phone number.
     *
     * @return int Landline/office phone number character limit. -1 if there was a technical error.
     */
    public function getPhoneNormalCharacterLimit(): int;

    /**
     * Sets the company's landline/office phone number.
     *
     * @param string $phoneNormal Landline number.
     * @return bool True on success, false on failure.
     */
    public function setPhoneNormal(string $phoneNormal): bool;

    /**
     * Returns a URL for embedding the company's location in a map service.
     *
     * @return string Map URL, empty string if not set.
     */
    public function getMapLocationURL(): string;

    /**
     * Returns the maximum amount of characters for your company's location in a map service.
     *
     * @return int Character limit for your company's location in a map service. -1 if there was a technical error.
     */
    public function getMapLocationURLCharacterLimit(): int;

    /**
     * Sets a URL for embedding the company's location in a map service.
     *
     * @param string $companyMapLocation Map embed URL.
     * @return bool True on success, false on failure.
     */
    public function setMapLocationURL(string $companyMapLocation): bool;

    /**
     * Returns the company's Facebook page URL.
     *
     * @return string Facebook URL, empty if not set.
     */
    public function getFacebookURL(): string;

    /**
     * Returns the maximum amount of characters for your company's Facebook page URL.
     *
     * @return int Maximum amount of characters for your Facebook page URL. -1 if there was a technical error.
     */
    public function getFacebookURLCharacterLimit(): int;

    /**
     * Sets the company's Facebook page URL.
     *
     * @param string $facebookURL Facebook URL or empty string.
     * @return bool True on success, false on failure.
     */
    public function setFacebookURL(string $facebookURL): bool;

    /**
     * Returns the company's Instagram page URL.
     *
     * @return string Instagram URL, empty if not set.
     */
    public function getInstagramURL(): string;

    /**
     * Returns the maximum amount of characters for your company's Instagram page URL.
     *
     * @return int Maximum amount of characters for your Instagram page URL. -1 if there was a technical error.
     */
    public function getInstagramURLCharacterLimit(): int;

    /**
     * Sets the company's Instagram page URL.
     *
     * @param string $instagramURL Instagram URL or empty string.
     * @return bool True on success, false on failure.
     */
    public function setInstagramURL(string $instagramURL): bool;

    /**
     * Returns the company's WhatsApp contact link.
     *
     * @return string WhatsApp link, empty if not set.
     */
    public function getWhatsAppURL(): string;

    /**
     * Returns the maximum amount of characters for your company's WhatsApp contact URL.
     *
     * @return int Maximum amount of characters for your WhatsApp contact URL. -1 if there was a technical error.
     */
    public function getWhatsAppURLCharacterLimit(): int;

    /**
     * Sets the company's WhatsApp contact link.
     *
     * @param string $whatsAppURL WhatsApp link or empty string.
     * @return bool True on success, false on failure.
     */
    public function setWhatsAppURL(string $whatsAppURL): bool;

    /**
     * Returns the company's Kleinanzeigen (classifieds) sellers page URL.
     *
     * @return string URL, empty if not set.
     */
    public function getKleinanzeigenURL(): string;

    /**
     * Returns the maximum amount of characters for your company's Kleinanzeigen (classifieds) sellers page URL.
     *
     * @return int Maximum amount of characters for your Kleinanzeigen (classifieds) sellers page URL. -1 if there was a technical error.
     */
    public function getKleinanzeigenURLCharacterLimit(): int;

    /**
     * Sets the company's Kleinanzeigen (classifieds) sellers page URL.
     *
     * @param string $kleinanzeigenURL URL or empty string.
     * @return bool True on success, false on failure.
     */
    public function setKleinanzeigenURL(string $kleinanzeigenURL): bool;

    /**
     * Returns the company's privacy policy text.
     *
     * @return string Privacy text, empty if not set.
     */
    public function getPrivacyPolicy(): string;

    /**
     * Returns the maximum amount of characters for your company's privacy policy text.
     *
     * @return int Maximum amount of characters for your company's privacy policy text. -1 if there was a technical error.
     */
    public function getPrivacyPolicyCharacterLimit(): int;

    /**
     * Sets the company's privacy policy text.
     *
     * @param string $privacyPolicyInformation Privacy text.
     * @return bool True on success, false on failure.
     */
    public function setPrivacyPolicy(string $privacyPolicyInformation): bool;

    /**
     * Returns the company's general business conditions (terms of service) text.
     *
     * @return string Terms text, empty if not set.
     */
    public function getTermsOfService(): string;

    /**
     * Returns the maximum amount of characters for your common business conditions text.
     *
     * @return int Maximum amount of characters for your common business conditions text. -1 if there was a technical error.
     */
    public function getTermsOfServiceCharacterLimit(): int;

    /**
     * Sets the company's general business conditions (terms of service) text.
     *
     * @param string $termsOfService Terms text.
     * @return bool True on success, false on failure.
     */
    public function setTermsOfService(string $termsOfService): bool;

    /**
     * Returns all data of the company,
     * followed always by the maximum amount of characters possible.
     * The returned array always looks like this:
     *
     * [
     *
     *  "CompanyName" => ["My company Inc.", 50],
     *
     *  "Owner" => ["John Smith", 40],
     *
     *  "Street" => ["Frankfurt Avenue 21st", 40],
     *
     *  "ZipCode" => ["12345", 5],
     *
     *  "Place" => ["German Town", 20],
     *
     *  "Email" => ["mymail@web.com", 100],
     *
     *  "P19UStG" => false,
     *
     *  "P36VSBG" => true,
     *
     *  "PhoneMobile" => ["+49 123 4567891", 16],
     *
     *  "PhoneNormal" => ["123 555 4567891", 16],
     *
     *  "MapLocationURL" => ["https://maps.google.com/whatever", 1000],
     *
     *  "FacebookURL" => ["https://facebook.com/mypage", 500],
     *
     *  "InstagramURL" => ["https://instagram.com/mycookingpage", 200],
     *
     *  "WhatsAppURL" => ["https://whatsapp.com/mynumber", 150],
     *
     *  "KleinanzeigenURL" => ["https://kleinanzeigen.de/mysellerpage", 150],
     *
     *  "PrivacyPolicy" => ["We process this and this for better user experience", 65535],
     *
     *  "TermsOfService" => ["If you rent our objects, make sure to clean it before return", 65535]
     *
     * ]
     *
     * Instead,
     * you could get an empty array if there was an error while fetching everything.
     *
     * @return array Associative array, containing all company data and maximum character limit hints. Empty array if there was an error while fetching everything.
     */
    public function getCompanyData() : array;
}