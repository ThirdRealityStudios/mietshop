<?php

namespace src\mietshop\contracts;

/**
 * Mainly generates a basic and valid HTML document,
 * including yet the header and footer.
 * In between the header and footer,
 * the IGlobalView expects the body content which you provide to the function show(...) simply.
 */
interface IGlobalView
{
    /**
     * Shows a default view,
     * including a static header, custom body and static footer.
     * A prepared associative array is expected as a parameter,
     * which expects e.g. the following associative array:
     *
     * [
     *  "host" => "mydomain.com",
     *  "CompanyName" => "Microsoft Inc.",
     *  "CompanyOwner" => "William Smith",
     *  "CompanyStreet" => "Ireland Street 50",
     *  "CompanyZipCode" => "12345",
     *  "CompanyPlace" => "Neustadt",
     *  "CompanyPhoneMobile" => "+49 123 45678",
     *  "CompanyPhoneNormal" => "12345 6789123",
     *  "MapLocationURL" => "https://google.com/myloactionWhatver",
     *  "FacebookURL" => "https://facebook.com/pageWhatever",
     *  "InstagramURL" => "https://instagram.com/pageWhatever",
     *  "WhatsAppURL" => "https://whatsapp.com/?phone=1234567890",
     *  "KleinanzeigenURL" => "https://kleinanzeigen.de/?page=whatever",
     *  "PrivacyPolicy" => "My privacy policy - briefly",
     *  "TermsOfService" => "My brief terms of service"
     * ]
     *
     * Of course,
     * the associative values can be an empty string
     * (which will, depending on the value, determine the exact view output).
     *
     *
     * @param array $data
     * @return void
     */
    public function show(array $data) : void;
}