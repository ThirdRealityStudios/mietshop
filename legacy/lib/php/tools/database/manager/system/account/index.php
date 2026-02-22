<?php
	class AccountManager
	{
		private DatabaseManager $databaseManager;

		public function __construct(DatabaseManager $databaseManager)
		{
			$this -> databaseManager = $databaseManager;
		}

		/**
		 * @return DatabaseManager
		 */
		private function getDatabaseManager(): DatabaseManager
		{
			return $this->databaseManager;
		}

		// Sets up the Account table if it was not created yet.
		// Necessary to make the AccountManager work.
		// The administrator account is being created as the first account, using the master password.
		public function initDatabase(): bool
        {
			$sqlSetupCheck = "

			CREATE TABLE IF NOT EXISTS Account
			(
				AccountID INT(8) AUTO_INCREMENT PRIMARY KEY UNIQUE,

				Username VARCHAR(32) NOT NULL UNIQUE,
				Password VARCHAR(255) NOT NULL,

				HasPermissionAdmin BOOLEAN DEFAULT FALSE
			);

			INSERT INTO Account (Username, Password, HasPermissionAdmin) VALUES ('Admin', '".password_hash(MASTER_PASSWORD, PASSWORD_DEFAULT)."', true) ON DUPLICATE KEY UPDATE Username=Username, Password=Password, HasPermissionAdmin=HasPermissionAdmin;
			";

			return $this -> getDatabaseManager() -> perform($sqlSetupCheck);
		}

		// Registers directly the given account.
		// Make sure to check invalid entries before executing, e.g. username registered yet.
		// Returns false if SQL execution went wrong, e.g. SQL table does not exist.
		public function register($username, $password)
		{
			$sqlRegister =
			"
				INSERT INTO Account (Username, Password, HasPermissionAdmin) VALUES ('".$username."', '".password_hash($password, PASSWORD_DEFAULT)."', false) ON DUPLICATE KEY UPDATE Username=Username, Password=Password, HasPermissionAdmin=HasPermissionAdmin;
			";

			return $this -> getDatabaseManager() -> perform($sqlRegister);
		}

		// https://www.php-einfach.de/mysql-tutorial/crashkurs-pdo/
		// Returns the password (ideally hashed) of a given user.
		public function getPassword($username): ?string
        {
			// Password can be 'NULL' if not found for the given user (=> incorrect).
			return $this -> getDatabaseManager() -> selectFirst("Password", "Account", "Username=\"$username\"");
		}

		// Tries to log in to the given account and remembers the login by starting new session.
		// Returns 0 if successful, otherwise -1.
		public function login($username, $password)
		{
			$passwordDB = $this -> getPassword($username);
			
			if(is_string($passwordDB) && password_verify($password, $passwordDB))
			{
				return 0;
			}
			
			return -1;
		}

		// Returns whether the given user has Admin permissions or not (true, or false if not).
		public function hasPermissionAdmin($username)
		{
			$value = $this -> getDatabaseManager() -> selectFirst("HasPermissionAdmin", "Account", "Username=\"$username\"");

			$hasPermissionAdmin = $value !== NULL ? $value : false;

			return $hasPermissionAdmin;
		}
	}
?>
