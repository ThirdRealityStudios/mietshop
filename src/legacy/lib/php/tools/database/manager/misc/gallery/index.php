<?php
	class GalleryManager
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

		// Sets up the Gallery table if it was not created yet.
		// Necessary to make the GalleryManager work.
		public function initDatabase() : bool
		{
			$sqlSetupCheck = "

      		CREATE TABLE IF NOT EXISTS Gallery
			(			
				GalleryID INT(8) AUTO_INCREMENT PRIMARY KEY,
				UploadHash VARCHAR(32),

				Folder VARCHAR(2048),
				PictureMax INT(4) DEFAULT 0
			);

			";

			return $this -> getDatabaseManager() -> perform($sqlSetupCheck);
		}

        /**
         * Creates an empty record in table Gallery of a post.
         * If something goes wrong (e.g.creation of record in database or fetching of record ID failed),
         * it will save a detailed error message in $_SESSION['UPDATE_ERROR_MSG'].
         *
         * @return int|NULL ID of the created record or NULL if creation failed (detailed error message saved then in $_SESSION['UPDATE_ERROR_MSG'])
         * @throws \Random\RandomException
         * @author Janek Piekarski <janek.piekarski.postfach@gmail.com>
         */
		public function createGallery() : int|NULL
		{
			$uploadHash = md5(random_bytes(RANDOM_BYTES_VALUE)); // For recognition of record after creation (random hash generation).
			$creationSuccess = $this -> getDatabaseManager() -> insert('Gallery', $uploadHash);

			if($creationSuccess)
			{
				$galleryID = $this -> getDatabaseManager() -> selectFirst('GalleryID', 'Gallery', "UploadHash LIKE \"%$uploadHash%\"");

				if(is_numeric($galleryID))
				{
					return $galleryID;
				}
				else
				{
					$_SESSION['UPDATE_ERROR_MSG'] = 'Gallery ID abfragen nach Erstellung von Gallery Datensatz fehlgeschlagen (DB Fehler?)';
					return NULL;
				}
			}
			else
			{
				$_SESSION['UPDATE_ERROR_MSG'] = 'Gallery Datensatz erstellen fehlgeschlagen (DB Fehler)';
				return NULL;
			}
		}
	}
?>
