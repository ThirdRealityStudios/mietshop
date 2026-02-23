<?php
	/*
	 *
	 * COUNTRY RELATED SETTINGS,
	 * such as currency applied to your website.
	 *
	 */
	// The currency token, e.g. shown next to your prices on your website.
	const COUNTRY_CURRENCY_TOKEN = "EUR";
	// The currency symbol, e.g. shown next to your prices on your website.
	const COUNTRY_CURRENCY_SYMBOL = "€";
	// The distance unit token, e.g. shown next to your delivery prices on your website.
	const COUNTRY_DISTANCE_UNIT_TOKEN = "km";
	
	/*
	 *
	 * DATABASE CONFIG
	 *
	 */

	/*
	 *
	 * SMTP SERVER CONFIG
	 * (e.g. to notify you if a customer wants something)
	 *
	 */
	// The SMTP server used to send mails.
	const MAIL_HOST = "send.one.com";
	
	// The SMTP server port used to send mails (integer!).
	const MAIL_PORT = 465;
	
	// The mail server account name, e.g. "noreply@mymail.com" or "info@mymail.com".
	const MAIL_USER = "noreply@mymail.com";
	
	// The mail server account's password, e.g. "mypassword123".
	const MAIL_PASSWORD = "myPassword";
	
	/*
	 *
	 * DOMAIN
	 *
	 */
	// Domain name inclusive protocol, sub-domain, TLD (if run locally, also just "https://localhost" possible).
	// HTTPS is definitvely a must these days, as browsers will block resources otherwise!
	// Why this value is important? Because this way the website knows how to link picture ressources, hyperlinks etc.
	// Also, JavaScript code gets for example prepared with this value defined here (for redirections).
	const DOMAIN = "https://192.168.178.44";
	
	/*
	 *
	 * WEBSITE DECORATION AND LOOK
	 *
	 */
	// Path to your background image,
	// used for most web pages (such as start page, search page etc.).
	// Path is not bound to your own domain (see DOMAIN),
	// so you can enter eny image link you want for background
	// (including images on your own webserver).
	const BACKGROUND_URL = "https://storage.needpix.com/rsynced_images/sea-1547609_1280.jpg"; // "/img/background.jpeg";
	
	/*
	 *
	 * EMAIL ADRESSES
	 *
	 */
	// General email used to receive any kind of issue or topic from customers, lawyers, employees etc.
	const EMAIL_GENERAL = "info@mymail.com";
	
	// Email used to notify users only about their actions, such as reserving an rental object.
	// Must be set.
	const EMAIL_NOREPLY = "noreply@mymail.com";
	
	// Email used for privacy issues, e.g. European data privacy violations or deletion requests of personal data from website visitors.
	const EMAIL_PRIVACY = EMAIL_GENERAL;
	
	// Email used technical issues, e.g. reserving an article was not possible.
	const EMAIL_TECHNICAL = EMAIL_GENERAL;
	
	// Email used for your imprint webpage. Required in Germany to provide lawyers but also customers transparency on how to contact your business.
	const EMAIL_IMPRINT = EMAIL_GENERAL;
	
	/*
	 *
	 * COMPANY DETAILS
	 *
	 */
	const COMPANY_NAME = "Mein Mietshop (Einzelunternehmen)"; // e.g. "Regioverleih Emsland"
	const COMPANY_SLOGAN = "Meine ausführliche Beschreibung für Suchmaschinenoptimierung"; // e.g. "Mieten Sie Bau- und Arbeitsgeräte zum perfekten Preis-Leistungsverhältnis » Bautrocknungsgeräte, Abbruchhammer, Rüttelplatten - Rabatte nutzen ✓"
	const COMPANY_PURPOSE = "Vermietung"; // very short word, describing the purpose of your company (only shown in tab title), e.g. "Maschinenvermietung"
	const COMPANY_SHORT_DESCRIPTION = "Mieten Sie Bau- und Arbeitsgeräte 😎"; // max. 38 symbols recommended for SEO, e.g. "Mieten Sie Bau- und Arbeitsgeräte 😎"
	const COMPANY_BOSS = "Max Mustermann"; // Legal head of your business, aka boss.
	const COMPANY_STREET = "Sesam Str. 38b"; // The street inclusive building number of your company.
	const COMPANY_ZIP = "12345"; // The postal code (ZIP code) of your company.
	const COMPANY_PLACE = "Bielefeld"; // The place's name.
	const LEGAL_NEGOTIATION_PLACE = "Lingen"; // The place of the court where to do legal negotiations (if you wouldn't set it, then you would have to go the customers place far away maybe - by EU law).
	const COMPANY_PHONE_MOBILE = "+491234567890"; // Your mobile number (without space and best with your country number +..)
	const COMPANY_PHONE_STATIONARY = "+490987654321"; // Your house / stationary phone number (without space and best with your country number +..)
	const COMPANY_GOOGLE_MAPS_EMBED_LINK = "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1327.7500381095056!2d7.3032388251568685!3d52.38666032545992!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47b82f5109e67e3b%3A0x9eb4cd50587644ad!2sRegioverleih%20Emsland!5e0!3m2!1sde!2sde!4v1637168063046!5m2!1sde!2sde"; // "Embed link for Google Maps", usually very long link and possible to generate via Google itself.
	
	/*
	 *
	 * SOCIAL MEDIA LINKS
	 *
	 */
	const SOCIALMEDIA_FACEBOOK = "https://www.facebook.com/Regioverleih-Emsland-101836642333476/";
	const SOCIALMEDIA_INSTAGRAM = "https://www.instagram.com/rvemsland";
	const SOCIALMEDIA_WHATSAPP = "https://wa.me/491777897545";
	const SOCIALMEDIA_KLEINANZEIGEN = "https://www.kleinanzeigen.de/s-bestandsliste.html?userId=89115570";
	
	/*
	 *
	 * ACCOUNT
	 *
	 */
	// Default password for the Admin account when database was reset or website is installed just.
	const MASTER_PASSWORD = "admin";
	
	// Log filepath, where errors are saved to on your webserver
	// ( relative to $_SERVER['DOCUMENT_ROOT'] ).
	const ERROR_LOG_FILEPATH = 'errorLog.txt';
	?>
