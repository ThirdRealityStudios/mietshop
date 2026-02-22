<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/config/index.php';

	// Require this PHP file if you want to log errors to a preserved file
	// instead of outputing them to the user, e.g. if a DB value is invalid (user does not care or understand).
	// Also, this is better to understand multiple errors if this website runs on a webserver of a customer.
	
	/**
	 * Logs an error message to the specified file in config.php (ERROR_LOG_FILEPATH).
	 * It will log the time and string parameter $errorMsg as single line in the log file.
	 * A line-break is automatically being created at the end of the given string.
	 * So each call to this function will create its own line in the error log file.
	 *
	 * @param string $errorMsg The description of your error.
	 * @author Janek Piekarski <janek.piekarski.postfach@gmail.com>
	 */
	function logFile($errorMsg)
	{
		error_log('['.date(DATE_RFC822)."]: $errorMsg\n", 3, $_SERVER['DOCUMENT_ROOT'].'/'.ERROR_LOG_FILEPATH);
	}
?>