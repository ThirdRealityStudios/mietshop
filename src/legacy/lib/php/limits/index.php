<?php
	// 
	// This file is relevant for limits when processing any kind of data,
	// where limits but general values should apply.
	// E.g. it specifies how long values (strings) can be for records
	// when setting up the database.
	// Also among, these values limit the amount of symbols
	// that can be entered for an article on a webpage.
	// Hence,
	// these values are very important for synchronization of technical limits.
	// 
	
	const FEED_COL_SIDE = 1;
	const FEED_COL_XL_SIDE = 3;
	const FEED_COL_XXL_SIDE = 4;
	
	const FEED_COL_WIDTH = 10;
	const FEED_COL_XL_WIDTH = 6;
	const FEED_COL_XXL_WIDTH = 4;
	
	// The value used for generating random hashes (all across the website PHP code),
	// using a generated sequence of random bytes of the length specified below.
	// E.g. the value is 6,
	// then random_bytes(..) will generate 6 random bytes.
	// In this there would be:
	// (2^8)^6 = 2^48 = 281 trillion different outputs possible by random_bytes(6).
	const RANDOM_BYTES_VALUE = 6;

	// Maximum amount of symbols for a preview text of a post,
	// shown in search results on website.
	const LIMIT_POST_PREVIEW_TEXT_LENGTH = 128;
	// Maximum amount of symbols for a title of a post.
	const LIMIT_POST_TITLE_LENGTH = 42;
	// Maximum amount of symbols for a (brief) description of a post.
	const LIMIT_POST_DESCRIPTION_LENGTH = 1500;

	// Maximum amount of symbols for any field of the string table.
	const LIMIT_POST_STRING_TABLE_FIELD_LENGTH = 18;
	// Maximum amount of symbols for any field of the price data table.
	const LIMIT_POST_PRICE_DATA_FIELD_LENGTH = 20;
	
	// The minimum amount or value required for a field in a PriceData table (usually your price definitions in your posts / articles),
	// at least 0.01 EUR.
	const LIMIT_POST_PRICE_DATA_FIELD_PRICE_MIN = 0.01;
	// The minimum time required for all rate options which want to be defined,
	// e.g. a value of 1 is interpreted as 1 hour, 0.01 as 0.01 hours and so on.
	const LIMIT_POST_PRICE_DATA_FIELD_TIME_MIN = 1;
	
	// The amount of time units currently implemented when renting something.
	const LIMIT_POST_PRICE_DATA_AMOUNT_TIME_UNITS = 2; // e.g. a value of 2 when there is hours and days only.
	
	// One year in days but there may be need to adjust.
	// This value could vary in your branch or country,
	// depending on rules and laws.
	// 365 days is used here by standard because it is (to my knowledge) standard in Germany for financial things.
	const DAYS_ONE_YEAR = 365;
	// One year in hours.
	const HOURS_ONE_YEAR = 24 * DAYS_ONE_YEAR;
?>
