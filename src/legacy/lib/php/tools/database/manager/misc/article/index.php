<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/limits/index.php';

	class ArticleManager
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

        // Sets up the Article table if it was not created yet.
		// Necessary to make the ArticleManager work.
		public function initDatabase() : bool
        {
            $sqlSetupCheck = "

			CREATE TABLE IF NOT EXISTS Article
			(
				ArticleID INT(8) AUTO_INCREMENT PRIMARY KEY,
				UploadHash VARCHAR(32),

				CategoryID INT(8) NOT NULL,
				Title VARCHAR(" . LIMIT_POST_TITLE_LENGTH . ") NOT NULL,
				PreviewText VARCHAR(" . LIMIT_POST_PREVIEW_TEXT_LENGTH . "),
				Description VARCHAR(" . LIMIT_POST_DESCRIPTION_LENGTH . "),
				GalleryID INT(8) NOT NULL,
				StringTableID INT(8) NOT NULL,
				PriceDataID INT(8) NOT NULL,
				StockAmount INT(8) NOT NULL
			);

			";

            return $this -> getDatabaseManager() -> perform($sqlSetupCheck);
        }
		
		/**
		 * Uploads the photos (if any sent) and moves them to their own corresponding folder,
		 * named by the UploadHash of table Gallery.
		 * Also,
		 * the Article specified by parameter $articleID gets updated with the photos,
		 * so they can be loaded..
		 * This upload function will not delete or overwrite old photos,
		 * yet existing.
		 * Instead,
		 * newly uploaded photos will always be attached or added to existing ones
		 * in linear order.
		 * Hence,
		 * this method can be and is used for both,
		 * uploading photos to new posts and editing of existing posts.
		 * If something goes wrong (upload of pictures failed or overall update of entries failed),
		 * it will save a detailed error message in $_SESSION['UPDATE_ERROR_MSG'].
		 *
		 * @param integer $articleID The id of the post / article (needs to verified before method call if valid)
		 * @author Janek Piekarski <janek.piekarski.postfach@gmail.com>
		 * @return 'true' if successfully processed (value $_POST['...'] has correct syntax or was not transmitted because no desire for change). Otherwise returns 'false' (value $_POST['...'] was sent but DB update failed or syntax was incorrect).
		 */
		public function uploadPhotos($articleID)
		{
			// UPLOAD_ERR_OK         Value: 0
			// There is no error, the file uploaded with success.

			// UPLOAD_ERR_INI_SIZE   Value: 1
			// The uploaded file exceeds the upload_max_filesize directive in php.ini.

			// UPLOAD_ERR_FORM_SIZE  Value: 2
			// The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.

			// UPLOAD_ERR_PARTIAL    Value: 3
			// The uploaded file was only partially uploaded.

			// UPLOAD_ERR_NO_FILE    Value: 4
			// No file was uploaded.

			// UPLOAD_ERR_NO_TMP_DIR Value: 6
			// Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.

			// UPLOAD_ERR_CANT_WRITE Value: 7
			// Failed to write file to disk. Introduced in PHP 5.1.0.

			// UPLOAD_ERR_EXTENSION  Value: 8
			// A PHP extension stopped the file upload. Introduced in PHP 5.2.0.
			
			$insertSuccess = false;
		  
			$galleryID = $this -> getDatabaseManager() -> selectFirst('GalleryID', 'Article', "ArticleID = $articleID");

			if($galleryID == NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = "<p>"."Abfragen GalleryID für Artikel mit ID $articleID fehlgeschlagen"."</p>";
				
				return false;
			}
			
			$uploadedPicturesAmount = 0;
			
			if(isset($_FILES['image']))
			{
				$uploadedPicturesAmount = sizeof($_FILES['image']['name']);
				
				// Sometimes there is a "file" stored in array,
				// although nothing was uploaded (array size is 1 then).
				// Anyway, it only contains information about the upload in some cases (metadata).
				// Hence, there is a need for check whether this is a real file or not.
				// The execution of the further upload code will continue
				// with the adjusted amount of photos in reality (= 0, as intended).
				if($uploadedPicturesAmount == 1 && $_FILES['image']['error'][0] == UPLOAD_ERR_NO_FILE)
				{
					$uploadedPicturesAmount = 0; // in reality no file was uploaded (only metadata about the upload).
				}
			}
			
			$picturesAmountOld = $this -> getDatabaseManager() -> selectFirst('PictureMax', 'Gallery', "GalleryID = $galleryID");
			
			if($picturesAmountOld == NULL)
			{
				// Not yet initialized (which is in this case no reason to give an error)
				$picturesAmountOld = 0;
			}
			
			$totalPictureAmount = $picturesAmountOld + $uploadedPicturesAmount;
			
			$updatePictureMaxFailed = !$this -> getDatabaseManager() -> update("Gallery", "PictureMax", $totalPictureAmount, "GalleryID = $galleryID");
			
			if($updatePictureMaxFailed)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = "<p>"."Aktualisieren von Datensatz $galleryID (ID) in Gallery fehlgeschlagen (für Wert PictureMax)"."</p>";
				
				return false;
			}
			
			// Use the UploadHash also for creating a folder for the pictures,
			// which is itself unpredictable
			// (not linear chosen folder name, e.g. 0, 1, 2, 3)
			// for human and web crawlers.
			$uploadHash = $this -> getDatabaseManager() -> selectFirst('UploadHash', 'Gallery', "GalleryID = $galleryID");
			
			if($uploadHash == NULL)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = "<p>"."Abfrage von Datensatz $galleryID (ID) in Gallery fehlgeschlagen (UploadHash gebraucht)"."</p>";
				
				return false;
			}
			
			$pictureFolderRelativePath = "img/article/$uploadHash";
			
			$pictureFolderAbsolutePath = $_SERVER['DOCUMENT_ROOT'].'/'.$pictureFolderRelativePath;

			$unprocessedFolderPath = "$pictureFolderAbsolutePath/unprocessed";

			// Recursively create the needed folder structure if not done yet,
			// meaning for the articles picture directory and its subdirectory "unprocessed".
			if(!is_dir($unprocessedFolderPath))
			{
				$unprocessedFolderCreationFailed = !mkdir($unprocessedFolderPath, 0777, true);
				
				if($unprocessedFolderCreationFailed)
				{
					$_SESSION['UPDATE_ERROR_MSG'] = "<p>"."Erstellung von Ordnerstruktur für Bildordner und dessen Unterordner \"unprocessed\" fehlgeschlagen.</p>";
					
					// Creating "unprocessed" directory failed..
					return false;
				}
			}
			
			// Updates the articles Gallery record,
			// so the website knows where to load pictures from the webserver (or in future if you have not uploaded any pictures yet).
			$updateFolderSuccess = $this -> getDatabaseManager() -> update("Gallery", "Folder", $pictureFolderRelativePath, "GalleryID = $galleryID");
			
			if(!$updateFolderSuccess)
			{
				$_SESSION['UPDATE_ERROR_MSG'] = "<p>"."Aktualisieren des Web-Pfades (Spalte ist Folder) für Datensatz in Gallery fehlgeschlagen"."</p>";
				
				return false;
			}
			
			if($uploadedPicturesAmount == 0)
			{
				// No pictures were transmitted but pre-processing above was necessary
				// for picture folder creation and such for the article in future.
				// If no folder path would be specified for an article,
				// the article search page and view page would interpete this as an
				// invalid value in the DB (shown with red error text instead of picture in place).
				// This is not what you want actually for the visitors and yourself.
				return true;
			}
			
			for($i = 0; $i < $uploadedPicturesAmount; $i++)
			{
				$fileName = $_FILES['image']['name'][$i];
				$fileTmpName = $_FILES['image']['tmp_name'][$i];
				$fileType = $_FILES['image']['type'][$i];
				$fileSize = $_FILES['image']['size'][$i];
				$uploadError = $_FILES['image']['error'][$i];
				
				if($uploadError == UPLOAD_ERR_NO_FILE || $uploadError == UPLOAD_ERR_PARTIAL)
				{
					continue;
				}

				if(!($uploadError == UPLOAD_ERR_OK || $uploadError == UPLOAD_ERR_NO_FILE || $uploadError == UPLOAD_ERR_INI_SIZE || $uploadError == UPLOAD_ERR_FORM_SIZE || $uploadError == UPLOAD_ERR_NO_TMP_DIR || $uploadError == UPLOAD_ERR_CANT_WRITE || $uploadError == UPLOAD_ERR_EXTENSION))
				{
					$_SESSION['UPDATE_ERROR_MSG'] = "<p>"."Hochladen an sich von Bildern fehlgeschlagen (".$_FILES['image']['error'][$i].")"."</p>";
					
					// Creating directory failed..
					return false;
				}
				
				$fileExtension = getFileExtension($fileName);
				
				// This ensures old (yet uploaded pictures in past) do not get overwritten.
				$finalFilename = ''.($picturesAmountOld + $i); // Without file extension; only identifier.

				// Upload filepath where all unprocessed images are.
				$uploadFilepath = "$unprocessedFolderPath/$finalFilename.$fileExtension";

				if(!move_uploaded_file($fileTmpName, $uploadFilepath))
				{
					$_SESSION['UPDATE_ERROR_MSG'] = "<p>Hochladen von Bildern erfolgreich, aber Bilddatei konnte nicht aus temporärer Datei erzegt werden. (Anzahl alter Bilder: $picturesAmountOld, Anzahl hochgeladener Bilder: $uploadedPicturesAmount, insgesamte Anzahl: $totalPictureAmount, Dateiname: ".$_FILES['image']['name'].", Errorcode: $uploadError"."</p>";
										
					// Moving temporary file to server folder failed..
					return false;
				}
				
				// if-statement is true: the funtion convertImage(..) fails somehow to treat ALL uploaded files (only one)
				if(!convertImage($uploadFilepath, $pictureFolderAbsolutePath."/$finalFilename.jpg", 90))
				{
					$_SESSION['UPDATE_ERROR_MSG'] = "<p>"."Hochladen von Bildern erfolgreich sowie Temp-Datei-Konvertierung, aber gezielte Konvertierung in JPEG-Datei fehlgeschlagen."."</p>";
					
					// Converting the uploaded picture file to JPEG failed.
					return false;
				}
				
				// Delete original uploaded file.
				$deleteOriginalUploadFileSuccess = unlink($uploadFilepath);
				
				if(!$deleteOriginalUploadFileSuccess)
				{
					$_SESSION['UPDATE_ERROR_MSG'] = "<p>"."Originale Upload-Datei konnte intern nicht gelöscht werden nach Konvertierung (Uploadprozess fehlgeschlagen)."."</p>";
					
					return false;
				}
			}
			
			$unprocessedFolderFiles = scandir($unprocessedFolderPath);
			
			// Check if there are still unprocessed picture files in "unprocessed" folder for whatever reason.
			// If scandir(..) determined, the given path does not represent a directory, it will return false.
			// Otherwise it returns the array of files inside.
			// So a check is needed to match that scenario too.
			if(is_array($unprocessedFolderFiles) && count($unprocessedFolderFiles) > 0)
			{
				// Delete all photo files inside folder "unprocessed".
				// $filename is the file name with file extension, e.g. "myphoto.jpg".
				// But directories can also be contained, so further checks are being made in for-loop.
				foreach($unprocessedFolderFiles as $filename)
				{
					$unprocessedPhotoFilepath = $unprocessedFolderPath."/$filename";
					
					// Try to delete photo (and if unsuccessful, log the error into the log file).
					if(!is_dir($unprocessedPhotoFilepath) && !unlink($unprocessedPhotoFilepath))
					{
						logFile("Could not delete unprocessed photo file $unprocessedPhotoFilepath (not needed anymore)");
					}
				}
			}
			
			// Delete temporary folder again,
			// because all uploaded pictures have been normally procssed and deleted in that folder yet.
			// Bugfix: there was no check if the folder is indeed empty, so I added code before this if-statement for treatment.
			if(!rmdir($unprocessedFolderPath))
			{
				logFile('Temporärer Ordner für unverarbeitete Bilder konnte nicht gelöscht werden, obwohl alle Uploads verarbeitet.');
				
				return false;
			}

			return true; // Upload succeeded of all pictures.
		}
		
		/**
		 * Uploads all sent values (and pictures) to an EXISTING post / article,
		 * specified by the parameter $articleID.
		 * All uploaded entries will overwrite the older ones.
		 * An exception is the upload of photos.
		 * Uploaded photos will always just be added and will not overwrite old photos.
		 * Hence,
		 * with this function the amount of photos will steadily grow.
		 * If you don't upload photos,
		 * this will simply have no impact (old photos will remain too).
		 * If something goes wrong (upload of pictures failed or overall update of entries failed),
		 * it will save a detailed error message in $_SESSION['UPDATE_ERROR_MSG'].
		 *
		 * @param integer $articleID The id of the post / article (needs to verified before method call if valid)
		 * @author Janek Piekarski <janek.piekarski.postfach@gmail.com>
		 */
		public function upload($articleID)
		{
			if($this -> getDatabaseManager() -> getCategoryManager() -> processCategory($articleID, false))
			{
				if($this -> getDatabaseManager() -> getArticleManager() -> processTitle($articleID, false))
				{
					if($this -> getDatabaseManager() -> getArticleManager() -> processPreviewText($articleID, false))
					{
						if($this -> getDatabaseManager() -> getArticleManager() -> processDescription($articleID, false))
						{
							if($this -> getDatabaseManager() -> getArticleManager() -> processStockAmount($articleID, false))
							{
								if($this -> getDatabaseManager() -> getStringTableManager() -> processStringTable($articleID, false))
								{
									if($this -> getDatabaseManager() -> getPriceDataManager() -> processPriceData($articleID, false))
									{
										if($this -> uploadPhotos($articleID))
										{
											// Redirect to the search page after successful article creation (no single error).
											echo "<script>window.location='", DOMAIN, "/article/?id=", $articleID, "'</script>";
										}
									}
								}
							}
						}
					}
				}
			}
			
			if(strlen($_SESSION['UPDATE_ERROR_MSG'].'') > 0)
				$_SESSION['UPDATE_ERROR_MSG'] = "[Upload] ".$_SESSION['UPDATE_ERROR_MSG'];
		}

        /**
         * Creates an empty record in table Article (a new article / post).
         * If something goes wrong (e.g.creation of record in database or fetching of record ID failed),
         * it will save a detailed error message in $_SESSION['UPDATE_ERROR_MSG'].
         *
         * @param integer $galleryID The id of the Gallery record created for the post / article (needs to verified before method call if valid)
         * @param integer $stringTableID The id of the StringTable record created for the post / article (needs to verified before method call if valid)
         * @param integer $priceDataID The id of the PriceData record created for the post / article (needs to verified before method call if valid)
         * @author Janek Piekarski <janek.piekarski.postfach@gmail.com>
         * @return integer ID of the created record or NULL if creation failed (detailed error message saved then in $_SESSION['UPDATE_ERROR_MSG'])
         */
        public function createArticle($galleryID, $stringTableID, $priceDataID)
        {
            $uploadHash = md5(random_bytes(RANDOM_BYTES_VALUE)); // For recognition of record after creation (random hash generation).
            $creationSuccess = $this -> getDatabaseManager() -> insert('Article', $uploadHash, -1, "", "", "", $galleryID, $stringTableID, $priceDataID);

            if(is_bool($creationSuccess) && $creationSuccess)
            {
                $articleID = $this -> getDatabaseManager() -> selectFirst('ArticleID', 'Article', "UploadHash LIKE \"%$uploadHash%\"", $galleryID, $stringTableID, $priceDataID);

                if(is_numeric($articleID) && $articleID >= 0)
                {
                    return $articleID;
                }
                else
                {
                    $_SESSION['UPDATE_ERROR_MSG'] = 'Artikel ID abfragen nach Erstellung von Artikel Datensatz fehlgeschlagen (DB Fehler?)';
                }
            }
            else
            {
                $_SESSION['UPDATE_ERROR_MSG'] = 'Artikel Datensatz erstellen fehlgeschlagen (DB Fehler)';
            }

            return NULL;
        }

		/**
		* Processes the title of a post,
		* depending on variable $checkSyntaxOnly.
		* If something goes wrong (syntax of transmitted value invalid or update in database failed),
		* it will save a detailed error message in $_SESSION['UPDATE_ERROR_MSG'].
		*
		* @param integer $id The id of the post / article (needs to verified before method call if valid; and parameter is ignored when $checkSyntaxOnly = true !)
		* @param bool $checkSyntaxOnly If 'true' the function will only check if the syntax of transmitted value is correct (no update in database). If 'false', both syntax check and update in database is performed (if syntax was correct).
		* @author Janek Piekarski <janek.piekarski.postfach@gmail.com>
		* @return 'true' if successfully processed ($_POST['title'] has correct syntax or was not transmitted because no desire for change). Otherwise returns 'false' ($_POST['title'] was sent but DB update failed or syntax was incorrect).
		*/
		public function processTitle($id, $checkSyntaxOnly)
		{
			if(isset($_POST['title']))
			{
				// Provided value must be a string (and not too long for website).
				// It is also allowed to be empty.
				if(is_string($_POST['title']))
				{
					if(strlen($_POST['title']) > LIMIT_POST_TITLE_LENGTH)
					{
						$_POST['title'] = substr($_POST['title'], 0, LIMIT_POST_TITLE_LENGTH);
					}

					if(strlen($_POST['title']) <= LIMIT_POST_TITLE_LENGTH)
					{
						if($checkSyntaxOnly)
							return true;
					
						$updateSuccessful = $this -> getDatabaseManager()->update('Article', 'Title', $_POST['title'], "ArticleID = $id");
						
						if($updateSuccessful)
						{
							// The update in database was succesful.
							return true;
						}
						else
						{
							// The update in database failed.
							$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_TITLE_FAILED."</p>";

                            return false;
						}
					}
					else
					{
						// Title provided does not match valid syntax.
						$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_TITLE_TOO_LONG."</p>";

                        return false;
					}
				}
				else
				{
					// Title provided does not match valid syntax.
					$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_TITLE_NOT_STRING."</p>";

                    return false;
				}
			}
			else
			{
				// Hence no title was provided to change,
				// return true,
				// as it has no effect (old value remains).
				return true;
			}
		}
		
		/**
		* Processes the preview text of a post,
		* depending on variable $checkSyntaxOnly.
		* If something goes wrong (syntax of transmitted value invalid or update in database failed),
		* it will save a detailed error message in $_SESSION['UPDATE_ERROR_MSG'].
		*
		* @param integer $id The id of the post / article (needs to verified before method call if valid; and parameter is ignored when $checkSyntaxOnly = true !)
		* @param bool $checkSyntaxOnly If 'true' the function will only check if the syntax of transmitted value is correct (no update in database). If 'false', both syntax check and update in database is performed (if syntax was correct).
		* @author Janek Piekarski <janek.piekarski.postfach@gmail.com>
		* @return 'true' if successfully processed ($_POST['previewText'] has correct syntax or was not transmitted because no desire for change). Otherwise returns 'false' ($_POST['previewText'] was sent but DB update failed or syntax was incorrect).
		*/
		public function processPreviewText($id, $checkSyntaxOnly)
		{
			if(isset($_POST['previewText']))
			{
				if(is_string($_POST['previewText']) && strlen($_POST['previewText']) > LIMIT_POST_PREVIEW_TEXT_LENGTH)
				{
					$_POST['previewText'] = substr($_POST['previewText'], 0, LIMIT_POST_PREVIEW_TEXT_LENGTH);
				}
				
				// Provided value must be a string (and not too long for website).
				// It is also allowed to be empty.
				if(is_string($_POST['previewText']) && strlen($_POST['previewText']) <= LIMIT_POST_PREVIEW_TEXT_LENGTH)
				{
					if($checkSyntaxOnly)
						return true;
					
					$updateSuccessful = $this -> getDatabaseManager()->update('Article', 'PreviewText', $_POST['previewText'], "ArticleID = $id");
					
					if($updateSuccessful)
					{
						// The update in database was succesful.
						return true;
					}
					else
					{
						// The update in database failed.
						$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_PREVIEW_TEXT_FAILED."</p>";

                        return false;
					}
				}
				else
				{
					// Preview text provided does not match valid syntax.
					$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_PREVIEW_TEXT."</p>";

                    return false;
				}
			}
			else
			{
				// Hence no preview text was provided to change,
				// return true,
				// as it has no effect (old value remains).
				return true;
			}
		}

		/**
		* Processes the description of a post.
		* depending on variable $checkSyntaxOnly.
		* If something goes wrong (syntax of transmitted value invalid or update in database failed),
		* it will save a detailed error message in $_SESSION['UPDATE_ERROR_MSG'].
		*
		* @param integer $id The id of the post / article (needs to verified before method call if valid; and parameter is ignored when $checkSyntaxOnly = true !)
		* @param bool $checkSyntaxOnly If 'true' the function will only check if the syntax of transmitted value is correct (no update in database). If 'false', both syntax check and update in database is performed (if syntax was correct).
		* @author Janek Piekarski <janek.piekarski.postfach@gmail.com>
		* @return 'true' if successfully processed ($_POST['description'] has correct syntax or was not transmitted because no desire for change). Otherwise returns 'false' ($_POST['description'] was sent but DB update failed or syntax was incorrect).
		*/
		public function processDescription($id, $checkSyntaxOnly)
		{
			if(isset($_POST['description']))
			{
				if(is_string($_POST['description']) && strlen($_POST['description']) > LIMIT_POST_DESCRIPTION_LENGTH)
				{
					$_POST['description'] = substr($_POST['description'], 0, LIMIT_POST_DESCRIPTION_LENGTH);
				}

				// Provided value must be a string (and not too long for website).
				// It is also allowed to be empty.
				if(is_string($_POST['description']) && strlen($_POST['description']) <= LIMIT_POST_DESCRIPTION_LENGTH)
				{
					if($checkSyntaxOnly)
						return true;
					
					$updateSuccessful = $this -> getDatabaseManager() -> update('Article', 'Description', $_POST['description'], "ArticleID = $id");
					
					if($updateSuccessful)
					{
						// The update in database was succesful.
						return true;
					}
					else
					{
						// The update in database failed.
						$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_DESCRIPTION_FAILED."</p>";

                        return false;
					}
				}
				else
				{
					// Description provided does not match valid syntax.
					$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_DESCRIPTION."</p>";

                    return false;
				}
			}
			else
			{
				// Hence no description was provided to change,
				// return true,
				// as it has no effect (old value remains).
				return true;
			}
		}
	
		/**
		* Processes the stock amount of a post,
		* depending on variable $checkSyntaxOnly.
		* If something goes wrong (syntax of transmitted value invalid or update in database failed),
		* it will save a detailed error message in $_SESSION['UPDATE_ERROR_MSG'].
		*
		* @param integer $id The id of the post / article (needs to verified before method call if valid; and parameter is ignored when $checkSyntaxOnly = true !)
		* @param bool $checkSyntaxOnly If 'true' the function will only check if the syntax of transmitted value is correct (no update in database). If 'false', both syntax check and update in database is performed (if syntax was correct).
		* @author Janek Piekarski <janek.piekarski.postfach@gmail.com>
		* @return 'true' if successfully processed ($_POST['amount'] has correct syntax or was not transmitted because no desire for change). Otherwise returns 'false' ($_POST['amount'] was sent but DB update failed or syntax was incorrect).
		*/
		public function processStockAmount($id, $checkSyntaxOnly)
		{
			if(isset($_POST['amount']))
			{
				// Provided value must be numeric (not negative and at least 0).
				if(is_numeric($_POST['amount']) && $_POST['amount'] >= 0)
				{
					if($checkSyntaxOnly)
						return true;
					
					$updateSuccessful = $this -> getDatabaseManager()->update('Article', 'StockAmount', $_POST['amount'], "ArticleID = $id");
					
					if($updateSuccessful)
					{
						// The update in database was succesful.
						return true;
					}
					else
					{
						// The update in database failed.
						$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_STOCK_AMOUNT_FAILED."</p>";

                        return false;
					}
				}
				else
				{
					// Stock amount provided does not match valid syntax.
					$_SESSION['UPDATE_ERROR_MSG'] = "<p>".LANG_RENTAL_POST_UPDATE_INVALID_REQUEST_STOCK_AMOUNT."</p>";

                    return false;
				}
			}
			else
			{
				// Hence no stock amount was provided to change,
				// return true,
				// as it has no effect (old value remains).
				return true;
			}
		}
		
		/**
		 * Generates the necessary HTML form directly on the web page (where called),
		 * used for uploading and editing articles.
		 * The generation function has the big advantage
		 * that it will reduce redundancy of code on long-term
		 * and it will always synchronize the HTML form with latest
		 * technical limits defined in "limits.php".
		 * 
		 * @param bool $isUploadForm When 'true', generates HTML form for uploading an article. Otherwise generates form HTML for updating an existing article.
		 * @param bool $article When upload: specify any value, e.g. null (will be ignored). When update of existing article: specify correct article object (retrieved from database record).
		 * @author Janek Piekarski <janek.piekarski.postfach@gmail.com>
		 */
		public function genHTMLForm($isUploadForm, $article)
		{
			$formAction = ($isUploadForm ? 'upload.php' : 'update.php');
			
			$articleID = null;
			$categoryID = null;
			$title = null;
			$previewText = null;
			$description = null;
			$amount = null;
			
			$stringTableQuery = null; // only used on existing articles
			$stringTable = null; // only used on existing articles
			$stringTableID = null; // only used on existing articles
			$stringTableTitle = null;
			
			$identifierXPlaceholder = null;
			$descriptionXPlaceholder = null;
			$identifierXValue = null;
			$descriptionXValue = null;
			$identifierXValueHTML = null;
			$descriptionXValueHTML = null;
			
			$priceDataQuery = null; // only used on existing articles
			$priceData = null; // only used on existing articles
			$priceDataID = null; // only used on existing articles
			
			$valueXPlaceholder = null;
			$valueX = null;
			$valueXHTML = null;

			$timeXPlaceholder = null;
			$timeXValue = null;
			$timeXValueHTML = null;
			$unitXValue = null;
			$unitXValueHTMLHour = null;
			$unitXValueHTMLDay = null;
			
			// A record in table StringTable has 13 elements:
			// StringTableID, UploadHash, Title, Identifier0, Description0, ..., Identifier4, Description4
			$stringTableRecordSize = 13;
			
			// A record in table PriceData has 17 elements:
			// PriceDataID, UploadHash, Price0, Price1, ..., Price4, Time0, ..., Time4, Unit0, ..., Unit4
			$priceDataRecordSize = 17;
			
			if($isUploadForm)
			{
				$articleID = -1;
				$categoryID = -1;
				$title = '';
				$previewText = '';
				$description = '';
				$amount = 0;
				
				$stringTableTitle = '';
			}
			else
			{
				if($article === NULL || !is_array($article) || sizeof($article) == 0)
				{
					return '<span class="fs-6 text-danger">Ungültiges Artikelobjekt für HTML-Formular-Erzeugung</span>';
				}

				$articleID = $article['ArticleID'];

				// This is very critical if the ArticleID is fucked up.
				// Because normally the DB will handle IDs and make sure they can't be changed to some invalid shit.
				if(!is_numeric($articleID))
				{
					return '<span class="fs-6 text-danger">Ungültige Artikel-ID für HTML-Formular-Erzeugung</span>';
				}
				
				$categoryID = $article['CategoryID'];
				
				// Every Article record normally needs a reference to an existing Category record.
				// If it is not linked,
				// this CAN be healed but it should be examined why this happened.
				// Instead,
				// -1 is assigned until the user changes the category.
				if($categoryID === NULL)
				{
					$categoryID = -1;
				}
				
				if(!is_numeric($categoryID))
				{
					return '<span class="fs-6 text-danger">Ungültige Kategorie-ID für HTML-Formular-Erzeugung</span>';
				}
				
				$title = $article['Title'];
				
				// If you expect an empty string to be saved into DB,
				// you're wrong.
				// The DB will save NULL instead which is shitty.
				// So I have to re-convert NULL to an empty string.
				if($title === NULL)
				{
					$title = '';
				}
				
				if(!is_string($title))
				{
					return '<span class="fs-6 text-danger">Ungültiger Artikel-Titel für HTML-Formular-Erzeugung</span>';
				}
				
				$previewText = $article['PreviewText'];
				
				// If you expect an empty string to be saved into DB,
				// you're wrong.
				// The DB will save NULL instead which is shitty.
				// So I have to re-convert NULL to an empty string.
				if($previewText === NULL)
				{
					$previewText = '';
				}
				
				if(!is_string($previewText))
				{
					return '<span class="fs-6 text-danger">Ungültiger Artikel-Vorschautext für HTML-Formular-Erzeugung</span>';
				}
				
				$description = $article['Description'];
				
				// If you expect an empty string to be saved into DB,
				// you're wrong.
				// The DB will save NULL instead which is shitty.
				// So I have to re-convert NULL to an empty string.
				if($description === NULL)
				{
					$description = '';
				}
				
				if(!is_string($description))
				{
					return '<span class="fs-6 text-danger">Ungültige Artikel-Beschreibung für HTML-Formular-Erzeugung</span>';
				}
				
				$amount = $article['StockAmount'];
				
				// If you expect an empty string to be saved into DB,
				// you're wrong.
				// The DB will save NULL instead which is shitty.
				// So I have to re-convert NULL to an empty string.
				if($amount === NULL)
				{
					$amount = 0;
				}
				
				if(!is_numeric($amount))
				{
					return '<span class="fs-6 text-danger">Ungültige Artikel-Lageranzahl für HTML-Formular-Erzeugung</span>';
				}
				
				$stringTableID = $article['StringTableID'];
				
				// Every Article record needs a reference to an existing StringTable record.
				// If it is not linked,
				// this can't be healed because then a whole StringTable record is lost or unknown!
				// Hence,
				// the execution will stop at this point.
				// The user will have to delete the article and recreate it.
				if(!is_numeric($stringTableID))
				{
					return '<span class="fs-6 text-danger">Ungültige Artikel-Texttabelle-ID für HTML-Formular-Erzeugung</span>';
				}
				
				$stringTableQuery = $this -> getDatabaseManager() -> select('*', 'StringTable', "StringTableID = $stringTableID", NULL);

				if($stringTableQuery === NULL)
				{
					return '<span class="fs-6 text-danger">Fehlgeschlagene Abfrage von Artikel-Texttabelle in Datenbank für HTML-Formular-Erzeugung</span>';
				}
				
				$stringTable = $stringTableQuery[0];

				if($stringTable === NULL || !is_array($stringTable) || sizeof($stringTable) != $stringTableRecordSize)
				{
					return '<span class="fs-6 text-danger">Ungültiges Artikel-Texttabelle-Objekt für HTML-Formular-Erzeugung</span>';
				}
				
				$stringTableTitle = $stringTable['Title'];
				
				// If you expect an empty string to be saved into DB,
				// you're wrong.
				// The DB will save NULL instead which is shitty.
				// So I have to re-convert NULL to an empty string.
				if($stringTableTitle === NULL)
				{
					$stringTableTitle = '';
				}
				
				if(!is_string($stringTableTitle))
				{
					return '<span class="fs-6 text-danger">Ungültiger Artikel-Texttabelle-Titel für HTML-Formular-Erzeugung</span>';
				}
				
				$priceDataID = $article['PriceDataID'];
				
				// Every Article record needs a reference to an existing PriceData record.
				// If it is not linked,
				// this can't be healed because then a whole PriceData record is lost or unknown!
				// Hence,
				// the execution will stop at this point.
				// The user will have to delete the article and recreate it.
				if(!is_numeric($priceDataID))
				{
					return '<span class="fs-6 text-danger">Ungültige Texttabelle-ID für HTML-Formular-Erzeugung</span>';
				}
				
				$priceDataQuery = $this -> getDatabaseManager() -> select('*', 'PriceData', "PriceDataID = $priceDataID", NULL);
				
				if($priceDataQuery === NULL)
				{
					return '<span class="fs-6 text-danger">Fehlgeschlagene Abfrage von Artikel-Preistabelle in Datenbank für HTML-Formular-Erzeugung</span>';
				}
				
				$priceData = $priceDataQuery[0];
				
				if($priceData === NULL || !is_array($priceData) || sizeof($priceData) != $priceDataRecordSize)
				{
					$errorMessage = "unbekannter Fehler";
					
					if($priceData === NULL)
						$errorMessage = "NULL";
					
					if(!is_array($priceData))
						$errorMessage = "kein brauchbares Format - kein Array";
					
					if(sizeof($priceData) !== $priceDataRecordSize)
						$errorMessage = "unerwartete Größenabweichung vom erhaltenen Datensatz";
					
					return '<span class="fs-6 text-danger">Ungültiges Artikel-Preistabelle-Objekt für HTML-Formular-Erzeugung ('.$errorMessage.')</span>';
				}
			}

			$formHTMLContent = '<form action="'.$formAction.'" method="POST" enctype="multipart/form-data">';
			$formHTMLContent .= '<div class="mb-5">';
			
			// Only needed when updating existing articles with new entry values.
			if(!$isUploadForm)
			{
				$formHTMLContent .= '<input type="hidden" name="id" value="'.$articleID.'">';
			}

            $comboBoxHTML = $this -> getDatabaseManager() -> getHTMLComboBox('Category', 'Title', 'category', null, $categoryID);

            // If data could not be loaded for the combo box from DB
            if(is_null($comboBoxHTML))
            {
                // Then say so:
                $comboBoxHTML = '<p class="text-danger">Sie haben noch keine Kategorien erstellt</p>';
            }
			
			$formHTMLContent .= '<div class="mb-3"><label class="form-label" for="category">Kategorie auswählen:</label>';
			$formHTMLContent .= $comboBoxHTML.'</div>';
			
			$formHTMLContent .= '<div class="mb-3"><label class="form-label" for="title">Titel (max. '.LIMIT_POST_TITLE_LENGTH.' Zeichen):</label>';
			$formHTMLContent .= '<input class="form-control" type="text" maxlength="'.LIMIT_POST_TITLE_LENGTH.'" id="title" name="title" value="'.$title.'" placeholder="Titel des Artikels" required></div>';
			
			$formHTMLContent .= '<div class="mb-3"><label class="form-label" for="previewText">Kurzbeschreibung für Suchvorschau auf Website (max. '.LIMIT_POST_PREVIEW_TEXT_LENGTH.' Zeichen):</label>';
			$formHTMLContent .= '<textarea class="form-control" maxlength="'.LIMIT_POST_PREVIEW_TEXT_LENGTH.'" id="previewText" name="previewText" placeholder="Eine kurze Beschreibung für die Vorschau in der Suche..">'.$previewText.'</textarea></div>';
			
			$formHTMLContent .= '<div class="mb-3"><label class="form-label" for="description">Artikelbeschreibung (max. '.LIMIT_POST_DESCRIPTION_LENGTH.' Zeichen):</label>';
			$formHTMLContent .= '<textarea id="description" class="form-control" name="description" placeholder="Eine ausführliche Beschreibung.." maxlength="'.LIMIT_POST_DESCRIPTION_LENGTH.'">'.$description.'</textarea></div>';
			
			$formHTMLContent .= '<div class="mb-3"><table class="table"><tr class="table-secondary"><td>Texttabelle (ideal für technische Details):</td><td></td><td></td></tr><tr class="table-light"><td><input maxlength="'.LIMIT_POST_STRING_TABLE_FIELD_LENGTH.'" type="text" class="form-control" name="stringTableTitle" placeholder="z.B. Eckdaten" value="'.$stringTableTitle.'"></td><td></td><td></td></tr>';
			
			if($isUploadForm)
			{
				$identifierXValueHTML = '';
				$descriptionXValueHTML = '';
			}
			
			$rowsStringTable = 5;
			// It iterates to < 5 because there are
			// 5 elements for the columns Identifier and Description in table StringTable,
			// meaning once: Identifier0, ..., Identifier4 and Description0, ..., Description4 (5 rows in total).
			for($i = 0; $i < $rowsStringTable; $i++)
			{
				switch($i)
				{
					case 0:
						$identifierXPlaceholder = 'z.B. Leistung';
						$descriptionXPlaceholder = 'z.B. 500 W';
					break;
					
					default:
						$identifierXPlaceholder = '...';
						$descriptionXPlaceholder = '...';
				}
				
				$identifierXValue = '';
				$descriptionXValue = '';
				
				if(!$isUploadForm)
				{
					$identifierXValue = $stringTable['Identifier'.$i];
					
					// In SQL DB a NULL value might be the case when you just transmit an empty string to the DB cell.
					// Hence, such an empty string should not be detected as "not a string" by PHP method is_string here.
					// (because it is simply OK to have empty fields saved for the string table in DB).
					if($identifierXValue !== NULL && !is_string($identifierXValue))
					{
						return '<span class="fs-6 text-danger">Abgefragter Texttabelle-'.'Identifier'.$i.' ungültig für HTML-Formular-Erzeugung</span>';
					}
					
					$descriptionXValue = $stringTable['Description'.$i];
					
					// Same logic here as just above and same reason.
					if($descriptionXValue !== NULL && !is_string($descriptionXValue))
					{
						return '<span class="fs-6 text-danger">Abgefragter Texttabelle-'.'Description'.$i.' ungültig für HTML-Formular-Erzeugung</span>';
					}
				}
				
				$identifierXValueHTML = 'value="'.$identifierXValue.'"';
				$descriptionXValueHTML = 'value="'.$descriptionXValue.'"';

				$lastColumnHTML = '';
				$allowAddRowButton = false;
				$hideCurrentRow = empty($identifierXValue) && empty($descriptionXValue);
				
				// Prepare the collpase button for a new row (button appears in the last column always for use).
				if($i < ($rowsStringTable - 1))
				{
					// Add these buttons for every row because it is a new uploaded article (no existing data or content to draw attention to).
					if($isUploadForm)
					{
						$allowAddRowButton = true;
					}
					else // Existing article to be edited, so buttons have to be added only prior to rows where there is still no content.
					{
						$nextIdentifierXValue = $stringTable['Identifier'.($i+1)];
						$nextDescriptionXValue = $stringTable['Description'.($i+1)];
						
						if(empty($nextIdentifierXValue) && empty($nextDescriptionXValue))
						{
							$allowAddRowButton = true;
						}
					}
				}
				
				if($allowAddRowButton)
					$lastColumnHTML = '<a id="collapseDetailRowButton'.$i.'" style="width: 37px; height: 37px;" class="btn btn-success rounded float-end" data-bs-toggle="collapse" href="#collapseDetailRow'.($i+1).'" onclick="$(\'#collapseDetailRowButton'.$i.'\').hide();" role="button" aria-expanded="false" aria-controls="collapseDetailRow'.($i+1).'">+</a>';
				
				if($i > 0 && $hideCurrentRow)
					$formHTMLContent .= '<tr class="table-light collapse" id="collapseDetailRow'.$i.'">';
				else
					$formHTMLContent .= '<tr class="table-light">';
				
				$formHTMLContent .= '<td><input maxlength="'.LIMIT_POST_STRING_TABLE_FIELD_LENGTH.'" type="text" class="form-control" name="stringTableIdentifier'.$i.'" placeholder="'.$identifierXPlaceholder.'" '.$identifierXValueHTML.'></td><td><input maxlength="'.LIMIT_POST_STRING_TABLE_FIELD_LENGTH.'" type="text" class="form-control" name="stringTableDescription'.$i.'" placeholder="'.$descriptionXPlaceholder.'" '.$descriptionXValueHTML.'></td><td>'.$lastColumnHTML.'</td></tr>';
			}
			
			$formHTMLContent .= '</table></div>';
			
			$formHTMLContent .= '<div class="mb-3"><table class="table"><tr class="table-secondary"><td>Preis</td><td>Leihzeit</td><th></th><th></th></tr>';
			
			if($isUploadForm)
			{
				$valueXHTML = '';
				$timeXValueHTML = '';
				
				$valueX = ''; // On upload page nothing filled in by default for price fields.
				$timeXValue = ''; // On upload page nothing filled in by default for time amount fields.
				
				$unitXValue = 0;
			}
			
			$rowsPriceTable = 5;
			// It iterates to < 5 because there are
			// 5 elements for the columns Identifier and Description in table PriceData,
			// meaning once: Price0, ..., Price4, Time0, ..., Time4 and so on (5 rows in total).
			for($i = 0; $i < $rowsPriceTable; $i++)
			{
				switch($i)
				{
					case 0:
						$valueXPlaceholder = 'x '.COUNTRY_CURRENCY_SYMBOL;
						$timeXPlaceholder = 'y Std.';
					break;
					
					default:
						$valueXPlaceholder = '...';
						$timeXPlaceholder = '...';
				}

				$unitX = 0; // default value before maybe fetching the real value from DB
				
				if(!$isUploadForm)
				{
					$timeXValue = $priceData['Time'.$i];
					
					// It is possible the DB only contains an empty string,
					// which is processed as NULL in PHP
					// (so I need to convert the empty field of the DB to the number 0 in PHP).
					if($timeXValue === NULL)
					{
						$timeXValue = 0;
					}
					
					if(!is_numeric($timeXValue))
					{
						return '<span class="fs-6 text-danger">Abgefragter Preistabelle-Wert Time X ungültig für HTML-Formular-Erzeugung</span>';
					}
					
					$valueX = $priceData['Price'.$i];
					
					// This situation can only happen if someone fucks up
					// the value in the database and erases the number,
					// so the DB saves NULL to the field.
					// With this check,
					// the form will work anyway and uses 0 instead of NULL as value.
					if($valueX === NULL)
					{
						$valueX = 0;
					}
					
					if(!is_numeric($valueX))
					{
						return '<span class="fs-6 text-danger">Abgefragter Preistabelle-Price X ungültig für HTML-Formular-Erzeugung</span>';
					}
					
					$unitX = $priceData['Unit'.$i];
					
					// Same conversion goes for the
					// time unit value from DB (which could be an empty field).
					if($unitX === NULL)
					{
						// If empty DB field, assume tbe first radio box is checked for the time unit.
						$unitX = 0;
					}
					
					// It is a radio box which can only be off and on (so value is whether 0 or 1).
					if(!is_numeric($unitX) || $unitX < 0 || $unitX > 1)
					{
						return '<span class="fs-6 text-danger">Abgefragter Miettabelle-TimeUnit X ungültig für HTML-Formular-Erzeugung</span>';
					}
				}
				
				// There is a check necessary for 0 because the website normally specifies a minimum value.
				// This on the other hand would force you to always enter a number > 0 (if configured this way).
				// Hence, it is better to replace a 0 with an empty string instead (which will have no negative effect on the saved values in DB later).
				$valueX = ($valueX == 0 ? '' : $valueX);
				
				$valueXHTML = 'value="'.$valueX.'"';
				
				// By default,
				// if you send an empty string to a DB with a integer column,
				// then the DB will save 0 instead (although if not wanted).
				// Hence,
				// a logical check is needed at this point (and use empty string instead if so).
				// Otherwise,
				// the user will possibly have to enter all time fields with a correct minimum value > 0 (although not wanted maybe).
				$timeXValue = ($timeXValue == 0 ? '' : $timeXValue);
				
				$timeXHTML = 'value="'.$timeXValue.'"';
				
				$unitXHourHTML = $unitX == 0 ? 'checked' : '';
				$unitXDayHTML = $unitX == 1 ? 'checked' : '';
				
				$lastColumnHTML = '';
				$allowAddRowButton = false;
				$hideCurrentRow = empty($valueX) && empty($timeXValue);
				
				// Prepare the collpase button for a new row (button appears in the last column always for use).
				if($i < ($rowsPriceTable - 1))
				{
					// Add these buttons for every row because it is a new uploaded article (no existing data or content to draw attention to).
					if($isUploadForm)
					{
						$allowAddRowButton = true;
					}
					else // Existing article to be edited, so buttons have to be added only prior to rows where there is still no content.
					{
						$nextValueX = $priceData['Price'.($i+1)];
						$nextTimeXValue = $priceData['Time'.($i+1)];
						
						if(empty($nextValueX) && empty($nextTimeXValue))
						{
							$allowAddRowButton = true;
						}
					}
				}
				
				$lastColumnHTML = '';
				
				// Prepare the collpase button for a new row (button appears in the column always for use).
				if($allowAddRowButton)
					$lastColumnHTML = '<a id="collapsePriceRowButton'.$i.'" style="width: 37px; height: 37px;" class="btn btn-success rounded float-end" data-bs-toggle="collapse" href="#collapsePriceRow'.($i+1).'" onclick="$(\'#collapsePriceRowButton'.$i.'\').hide();" role="button" aria-expanded="false" aria-controls="collapsePriceRow'.($i+1).'">+</a>';
				
				// Generation of price entry column.
				if($i > 0 && $hideCurrentRow)
					$formHTMLContent .= '<tr class="table-light collapse" id="collapsePriceRow'.$i.'">';
				else
					$formHTMLContent .= '<tr class="table-light" id="collapsePriceRow'.$i.'">';
				
				$formHTMLContent .= '<td><input min="'.LIMIT_POST_PRICE_DATA_FIELD_PRICE_MIN.'" type="number" step="any" class="form-control" name="Price'.$i.'" placeholder="'.$valueXPlaceholder.'" '.$valueXHTML.'></td>'; // price
				
				// Generation of rental time column.
				$formHTMLContent .= '<td><input min="'.LIMIT_POST_PRICE_DATA_FIELD_TIME_MIN.'" max="'.HOURS_ONE_YEAR.'" type="number" class="form-control" name="Time'.$i.'" placeholder="'.$timeXPlaceholder.'" '.$timeXHTML.'></td>'; // time

				// Generation of radio boxes for selecting time unit.
				$formHTMLContent .= '<td><div class="form-check"><input class="form-check-input" type="radio" id="hours'.$i.'" name="Unit'.$i.'" value="0" '.$unitXHourHTML.'><label class="form-check-label" for="hours'.$i.'">Std.</label></div><div class="form-check"><input class="form-check-input" type="radio" id="days'.$i.'" name="Unit'.$i.'" value="1" '.$unitXDayHTML.'><label class="form-check-label" for="days'.$i.'">Tag(e)</label></div></td><td>'.$lastColumnHTML.'</td></tr>';
			}
			
			$formHTMLContent .= '</table></div>';
			
			$formHTMLContent .= '<div class="mb-3"><label for="amount" class="form-label">Stückzahl (auf Lager):</label><input min="0" type="number" class="form-control" id="amount" name="amount" value="'.$amount.'"></div>';
			
			$formHTMLContent .= '<div class="mb-3"><label class="form-label" for="images">';
			
			if($isUploadForm)
			{
				$formHTMLContent .= 'Bilder hochladen:';
			}
			else
			{
				$formHTMLContent .= 'Weitere Bilder hochladen:';
			}
			
			$formHTMLContent .= '</label><input class="form-control" type="file" id="images" name="image[]" multiple="multiple"></div>';
			
			$formHTMLContent .= '</div><input class="btn btn-primary mb-3" name="updateButton" type="submit" value="Inserieren">';
			
			$formHTMLContent .= '</form>';
			$formHTMLContent .= '<div class="mb-3">';
			
			// Generation of HTML button (a form) to abort the upload or update of article.
			// If edit page, there will also be a deletion button generated.
			if($isUploadForm)
			{
				$formHTMLContent .= '<form action="'.DOMAIN."/home/\" method=\"GET\"><input class=\"btn btn-secondary\" type=\"submit\" value=\"Abbrechen\"></form>";
			}
			else
			{
				$formHTMLContent .= '<form action="'.DOMAIN."/article/\" method=\"GET\"><input type=\"hidden\" name=\"id\" value=\"".$articleID."\"><input class=\"btn btn-secondary\" type=\"submit\" value=\"Abbrechen\"></form>";
				
				// Deletion button for edit page: transmits the id and action to the same page,
				// so a popup will show up to ask the user if he really wants to delete the article.
				$formHTMLContent .= '<br><form action="'.DOMAIN."/edit/\" method=\"POST\"><input type=\"hidden\" name=\"id\" value=\"".$articleID."\"><input type=\"hidden\" name=\"action\" value=\"delete\"><input class=\"btn btn-secondary\" type=\"submit\" value=\"Löschen\"></form>";
			}
			
			$formHTMLContent .= '</div>';
			
			return $formHTMLContent;
		}
	}
?>