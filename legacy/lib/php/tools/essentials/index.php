<?php
	/**
	 * If you want to have all tools without thinking too much,
	 * you can just require this PHP file,
	 * so you directly have the most important tools essentials to use.
	 * Make sure to require this PHP file as the first statement on your corresponding webpage.
	 * This is because a session is being started, among.
	 * This PHP file does not influence the web appearance of the webpage,
	 * as it contains only pure PHP tools which have to be called first.
     *
     * Just write something like $essentials = new Essentials(); and you can use it!
	 */
	
	// Load the logging tool. Simply log something into the log file by using function
	// logFile(yourMessage).
	// The date and time will be saved automatically always before the actual message.
	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/tools/log/index.php';
	
	// Load the class of the database manager. Also contains config as well as limits PHP file.
	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/tools/database/index.php';
	
	// Start a PHP session (used for saving values into SESSION variables and for potential logins)
	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/tools/session/start/index.php';
	
	// Load Bootstrap tools which make it easier to produce components via PHP.
	require_once $_SERVER['DOCUMENT_ROOT'] .'/lib/php/tools/page/index.php';

	// Require the language file, so your project can make use of specific terms defined.
	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/lang/DE/index.php';

    class Essentials
    {
        private DatabaseManager $databaseManager;
        private ViewFactory $viewFactory;

        public function __construct()
        {
            $this -> databaseManager = new DatabaseManager();
            $this -> viewFactory = new ViewFactory();
        }

        /**
         * @return DatabaseManager
         */
        public function getDatabaseManager() : DatabaseManager
        {
            return $this -> databaseManager;
        }

        /**
         * @return ViewFactory
         */
        public function getViewFactory() : ViewFactory
        {
            return $this -> viewFactory;
        }
    }
?>