<?php

namespace src\mietshop\contracts;

/**
 * Interface for accessing global business data.
 *
 * Covers company details (name, owner, address, contact info) and legal notes
 * (privacy policy, general business conditions). Typically, this information
 * is displayed on most views or webpages.
 */
interface IGlobalModel
{
    /**
     * Returns the full company name, including legal form (e.g., "Inc.", "AG").
     *
     * @return string Full company name.
     */
    public function getCompanyName(): string;

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
    public function getCompanyOwner(): string;

    /**
     * Sets the company owner's full name.
     *
     * @param string $companyOwner Owner's full name.
     * @return bool True on success, false on failure.
     */
    public function setCompanyOwner(string $companyOwner): bool;

    /**
     * Returns the full street address of the company (including house number).
     *
     * @return string Street address.
     */
    public function getCompanyStreet(): string;

    /**
     * Sets the company's street address.
     *
     * @param string $companyStreet Full street address.
     * @return bool True on success, false on failure.
     */
    public function setCompanyStreet(string $companyStreet): bool;

    /**
     * Returns the company's ZIP/postal code.
     *
     * @return string ZIP code.
     */
    public function getCompanyZipCode(): string;

    /**
     * Sets the company's ZIP/postal code.
     *
     * @param string $companyZipCode ZIP code.
     * @return bool True on success, false on failure.
     */
    public function setCompanyZipCode(string $companyZipCode): bool;

    /**
     * Returns the company location/city.
     *
     * @return string City or place name.
     */
    public function getCompanyPlace(): string;

    /**
     * Sets the company location/city.
     *
     * @param string $companyPlace City or place name.
     * @return bool True on success, false on failure.
     */
    public function setCompanyPlace(string $companyPlace): bool;

    /**
     * Returns the company's mobile phone number.
     *
     * @return string Mobile number (with optional country code).
     */
    public function getPhoneMobile(): string;

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
    public function getCompanyMapLocation(): string;

    /**
     * Sets a URL for embedding the company's location in a map service.
     *
     * @param string $companyMapLocation Map embed URL.
     * @return bool True on success, false on failure.
     */
    public function setCompanyMapLocation(string $companyMapLocation): bool;

    /**
     * Returns the company's Facebook page URL.
     *
     * @return string Facebook URL, empty if not set.
     */
    public function getFacebookLink(): string;

    /**
     * Sets the company's Facebook page URL.
     *
     * @param string $facebookLink Facebook URL or empty string.
     * @return bool True on success, false on failure.
     */
    public function setFacebookLink(string $facebookLink): bool;

    /**
     * Returns the company's Instagram page URL.
     *
     * @return string Instagram URL, empty if not set.
     */
    public function getInstagramLink(): string;

    /**
     * Sets the company's Instagram page URL.
     *
     * @param string $instagramLink Instagram URL or empty string.
     * @return bool True on success, false on failure.
     */
    public function setInstagramLink(string $instagramLink): bool;

    /**
     * Returns the company's WhatsApp contact link.
     *
     * @return string WhatsApp link, empty if not set.
     */
    public function getWhatsAppLink(): string;

    /**
     * Sets the company's WhatsApp contact link.
     *
     * @param string $whatsAppLink WhatsApp link or empty string.
     * @return bool True on success, false on failure.
     */
    public function setWhatsAppLink(string $whatsAppLink): bool;

    /**
     * Returns the company's Kleinanzeigen (classifieds) sellers page URL.
     *
     * @return string URL, empty if not set.
     */
    public function getKleinanzeigenLink(): string;

    /**
     * Sets the company's Kleinanzeigen (classifieds) sellers page URL.
     *
     * @param string $kleinanzeigenLink URL or empty string.
     * @return bool True on success, false on failure.
     */
    public function setKleinanzeigenLink(string $kleinanzeigenLink): bool;

    /**
     * Returns the company's privacy policy text.
     *
     * @return string Privacy text, empty if not set.
     */
    public function getDataPrivacyInformation(): string;

    /**
     * Sets the company's privacy policy text.
     *
     * @param string $dataPrivacyInformation Privacy text.
     * @return bool True on success, false on failure.
     */
    public function setDataPrivacyInformation(string $dataPrivacyInformation): bool;

    /**
     * Returns the company's general business conditions (terms of service) text.
     *
     * @return string Terms text, empty if not set.
     */
    public function getCommonBusinessConditions(): string;

    /**
     * Sets the company's general business conditions (terms of service) text.
     *
     * @param string $commonBusinessConditions Terms text.
     * @return bool True on success, false on failure.
     */
    public function setCommonBusinessConditions(string $commonBusinessConditions): bool;
}