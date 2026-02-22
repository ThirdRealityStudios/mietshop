<?php
	// This file contains system-related messages in German.
	// You can edit or copy the file for your own messages in your language.
	// If you want to use your own language file,
	// you will need to link your language file in the "databaseManager.php",
	// so it will be recognized.
	// IF THERE ARE DISPLAY ERRORS, TRY TO CHANGE ENCODING OF THIS FILE!
	
	// The name of your (rental) objects in general /
	// a classification or rough word for your objects.
	// Leave it just as "object" or "device" (or "(Miet-)Objekt" / "Gerät" in German) if you don't know anything else.
	// The word will be shown requesting an object (e.g. "3 objects available currently").
	const LANG_ARTICLE_OBJECT_NAME_SINGLE = "Objekt"; // e.g. "Rental object" (= German: "Mietobjekt")
	const LANG_ARTICLE_OBJECT_NAME_PLURAL = "Objekte"; // e.g. "Rental objects" (= German: "Mietobjekte")

	// Simply the translation of "Invalid HTTPRequestHeader"
	const LANG_INVALID_REQUEST = "Ungültige Anfrage";

	// Simply the translation of "Internal Processing Error"
	const LANG_INTERNAL_PROCESSING_ERROR = "Interner Verarbeitungsfehler";
	
	/*
	 *
	 *  FROM HERE:
	 *
     *  Rental post related messages,
     *  usually about your rental objects.
     *
	 */

	// Editing or updating your rental post
	const LANG_RENTAL_POST_UPDATE_FAILED = "Aktualisieren fehlgeschlagen ❌";

	//
	// Editing or updating your rental post,
	// but tech-related issues (e.g. invalid data transmission in a way if was not intended)
	//
	
	// You tried to update a post but the post id was not sent or has a wrong syntax (not numeric or negative)
	const LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_ID = LANG_INVALID_REQUEST.": Artikel-ID nicht übermittelt oder ungültig";
	// You tried to update a post but the post title has a wrong syntax (not a string)
	const LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_TITLE_NOT_STRING = LANG_INVALID_REQUEST.": Titel ungültig (kein String)";
	// You tried to update a post but the post title has a wrong syntax (too long)
	const LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_TITLE_TOO_LONG = LANG_INVALID_REQUEST.": Titel ungültig (zu lang)";
	// You tried to update a post but the post category was not sent or has a wrong syntax (not numeric - negative values allowed though)
	const LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_CATEGORY = LANG_INVALID_REQUEST.": Kategorie nicht übermittelt oder ungültig";
	// You tried to update a post but the post preview text has a wrong syntax (too long for the website or not a text)
	const LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_PREVIEW_TEXT = LANG_INVALID_REQUEST.": Vorschautext zu lang für Website oder ist kein Text";
	// You tried to update a post but the post description has a wrong syntax (too long for the website or not a text)
	const LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_DESCRIPTION = LANG_INVALID_REQUEST.": die ausführliche Beschreibung für den Artikel ist zu lang für die Website oder ist kein Text";
	// You tried to update a post but the post stock amount has a wrong syntax (is not a number or is negative)
	const LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_STOCK_AMOUNT = LANG_INVALID_REQUEST.": die Anzahl der verfügbaren Einheiten ist keine richtige Zahl oder ist negativ";
	
	// You tried to update a post but the title of the string table has a wrong syntax (is too long or not a text)
	const LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_STRING_TABLE_TITLE = LANG_INVALID_REQUEST.": der Titel für die Texttabelle für den Artikel ist zu lang für die Website oder ist kein Text";
	// You tried to update a post but text of stringTableIdentifierXYZ of the string table has a wrong syntax (is too long or not a text)
	const LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_STRING_TABLE_IDENTIFIER_XYZ = LANG_INVALID_REQUEST.": stringTableIdentifier für die Texttabelle des Artikels ist zu lang für die Website oder ist kein Text";
	// You tried to update a post but text of stringTableDescriptionXYZ of the string table has a wrong syntax (is too long or not a text)
	const LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_STRING_TABLE_DESCRIPTION_XYZ = LANG_INVALID_REQUEST.": stringTableDescription für die Texttabelle des Artikels ist zu lang für die Website oder ist kein Text";
	
	// You tried to update a post but the corresponding value of the price data has a wrong syntax (is too long or not a number)
	const LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_PRICE_DATA_PRICE_XYZ = LANG_INVALID_REQUEST.": der jeweilige Wert (i.d.R. der Preis) für die Preistabelle für den Artikel ist keine Zahl, erfüllt nicht den Mindestbetrag oder ist zu lang für die Website";
	
	// You tried to update a post but the empty rows of the price table could not be removed after changes were processed successfully.
	const LANG_RENTAL_POST_UPDATE_INTERNAL_ERROR_PRICE_DATA_SORTING = "Konnte Daten einer PriceData nach Upload bzw. Edit nicht sortieren.";
	
	// You tried to update a post but the empty rows of the string table could not be removed after changes were processed successfully.
	const LANG_RENTAL_POST_UPDATE_INTERNAL_ERROR_STRING_TABLE_SORTING = "Konnte Daten einer StringTable nach Upload bzw. Edit nicht sortieren.";
	
	// You tried to update a post but the corresponding ID, belonging to a price data, has a wrong syntax (does not exist in HTML document or is not a number)
	const LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_ELEMENT_PRICE_DATA_PRICE_ID = LANG_INVALID_REQUEST.": übermittelte ID ist keine Zahl, nicht mind. 0 oder existiert nirgendwo als Definition im HTML-Dokument (in der Form PriceXYZ)";
	// You tried to update a post but the corresponding ID, belonging to a time amount, has a wrong syntax (does not exist in HTML document or is not a number)
	const LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_ELEMENT_PRICE_DATA_ID = LANG_INVALID_REQUEST.": übermittelte ID ist keine Zahl, nicht mind. 0 oder existiert nirgendwo als Definition im HTML-Dokument (in der Form timeXYZ)";

	// You tried to update a post but the corresponding value of the price data has a wrong syntax (is too long or not a number)
	const LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_PRICE_DATA_TIME_XYZ = LANG_INVALID_REQUEST.": der jeweilige Wert (i.d.R. die Mietzeit) für timeXYZ in PriceData-Tabelle für den Artikel ist keine ganze Zahl > 0, größer als HOURS_ONE_YEAR oder zu lang für die Website";
	// You tried to update a post but the corresponding value of the price data has a wrong syntax (is not a number, or does not exist as a time unit option)
	const LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_PRICE_DATA_UNIT_XYZ = LANG_INVALID_REQUEST.": der jeweilige Wert (i.d.R. die Zeiteinheit) für unitXYZ in PriceData-Tabelle für den Artikel ist keine ganze Zahl >= 0, oder der Wert existiert nicht als Zeiteinheit-Option";
	
	// You tried to update a post but the system could find the id of post in database.
	// Likely, the post does not exist then (anymore) for whatever reason.
	const LANG_RENTAL_POST_UPDATE_POST_ID_NOT_FOUND = LANG_INTERNAL_PROCESSING_ERROR.": Artikel-ID existiert nicht in Datenbank (Artikel existiert nicht)";
	// You tried to update a post but the system could find the string table id of post in database.
	const LANG_RENTAL_POST_UPDATE_POST_STRING_TABLE_ID_NOT_FOUND = LANG_INTERNAL_PROCESSING_ERROR.": Artikel besitzt keine String Table ID (Datensatz existiert nicht?)";
	// You tried to update a post but the system could find the price data id of post in database.
	const LANG_RENTAL_POST_UPDATE_POST_PRICE_DATA_ID_NOT_FOUND = LANG_INTERNAL_PROCESSING_ERROR.": Artikel besitzt keine PriceData ID (Datensatz existiert nicht?)";

	// You tried to update a post but the system could not update
	// the category in the database (although syntax of category value should have been correct).
	const LANG_RENTAL_POST_UPDATE_CATEGORY_FAILED = LANG_INTERNAL_PROCESSING_ERROR.": Kategorie konnte nicht aktualisiert werden (existiert die Kategorie in Datenbank?)";
	// You tried to update a post but the system could not update
	// the title in the database (although syntax of category value should have been correct).
	const LANG_RENTAL_POST_UPDATE_TITLE_FAILED = LANG_INTERNAL_PROCESSING_ERROR.": Titel konnte nicht aktualisiert werden (existiert der Artikel noch?)";
	// You tried to update a post but the system could not update
	// the preview text in the database (although syntax of preview text should have been correct).
	const LANG_RENTAL_POST_UPDATE_PREVIEW_TEXT_FAILED = LANG_INTERNAL_PROCESSING_ERROR.": Vorschautext für die Suche konnte nicht aktualisiert werden (existiert der Artikel noch?)";
	// You tried to update a post but the system could not update
	// the (brief) description in the database (although syntax of description should have been correct).
	const LANG_RENTAL_POST_UPDATE_DESCRIPTION_FAILED = LANG_INTERNAL_PROCESSING_ERROR.": die ausführliche Beschreibung für den Artikel konnte nicht aktualisiert werden (existiert der Artikel noch?)";
	// You tried to update a post but the system could not update
	// the stock amount in the database (although the value has been correct).
	const LANG_RENTAL_POST_UPDATE_STOCK_AMOUNT_FAILED = LANG_INTERNAL_PROCESSING_ERROR.": die Anzahl der verfügbaren Einheiten für den Artikel konnte nicht aktualisiert werden (existiert der Artikel noch?)";
	
	// You tried to update a post but the system could not update
	// the title of the string table in the database (although the value has been correct).
	const LANG_RENTAL_POST_UPDATE_STRING_TABLE_TITLE_FAILED = LANG_INTERNAL_PROCESSING_ERROR.": Titel der Texttabelle für den Artikel konnte nicht aktualisiert werden (existiert der Artikel noch?)";
	// You tried to update a post but the system could not update
	// the stringTableIdentifierXYZ of the string table in the database (although the value has been correct).
	const LANG_RENTAL_POST_UPDATE_STRING_TABLE_IDENTIFIER_XYZ_FAILED = LANG_INTERNAL_PROCESSING_ERROR.": jeweiliger stringTableIdentifier der Texttabelle für den Artikel konnte nicht aktualisiert werden (existiert der Artikel noch?)";
	// You tried to update a post but the system could not update
	// the stringTableDescriptionXYZ of the string table in the database (although the value has been correct).
	const LANG_RENTAL_POST_UPDATE_STRING_TABLE_DESCRIPTION_XYZ_FAILED = LANG_INTERNAL_PROCESSING_ERROR.": jeweiliger stringTableIdentifier der Texttabelle für den Artikel konnte nicht aktualisiert werden (existiert der Artikel noch?)";

	// You tried to update a post but the system could not update
	// the corresponding value of the price data in the database (although the value has been correct).
	const LANG_RENTAL_POST_UPDATE_PRICE_DATA_PRICE_XYZ_FAILED = LANG_INTERNAL_PROCESSING_ERROR.": entsprechender Wert der Preistabelle für den Artikel konnte nicht aktualisiert werden (existiert der Artikel noch?)";

	// You tried to update a post but the system could not update
	// the corresponding value of unitXYZ (radio button value) for a rent option in the database (although the value has been correct).
	const LANG_RENTAL_POST_UPDATE_PRICE_DATA_UNIT_XYZ_FAILED = LANG_INTERNAL_PROCESSING_ERROR.": entsprechender Wert timeUnitXYZ (durch gewählten Radio Button) einer Mietzeitangabe für den Artikel konnte nicht aktualisiert werden (Bezug: DB-Table 'Rent'; existiert der Artikel noch?)";
?>