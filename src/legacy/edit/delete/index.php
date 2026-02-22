<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/authorize/index.php';

    $essentials = new Essentials();

	function deleteGallery(Essentials $essentials, $articleID)
	{
		$galleryID = $essentials -> getDatabaseManager() -> selectFirst('GalleryID', 'Article', "ArticleID = $articleID");
		
		if($galleryID === NULL)
		{
			logFile("Abfrage von Bildgalerie-Verlinkungswert für Artikel fehlgeschlagen");

			return;
		}
		
		if(!is_numeric($galleryID))
		{
			logFile("Bildgalerie-Verlinkungswert für Artikel ist keine Zahl");
			
			return;
		}
		
		if($galleryID < 0)
		{
			logFile("Bildgalerie-Verlinkungswert für Artikel ist negativ");

			return;
		}
		
		$pictureAmount = $essentials -> getDatabaseManager() -> selectFirst('PictureMax', 'Gallery', "GalleryID = $galleryID");
		
		if($pictureAmount === NULL)
		{
			logFile("Abfrage von Bilderanzahl für Artikel fehlgeschlagen");

			return;
		}
		
		if(!is_numeric($pictureAmount))
		{
			logFile("Bildanzahl für Artikel ist keine Zahl");

			return;
		}
		
		if($pictureAmount < 0)
		{
			logFile("Bildanzahl für Artikel ist negativ");

			return;
		}
		
		$folder = $essentials -> getDatabaseManager() -> selectFirst('Folder', 'Gallery', "GalleryID = $galleryID");
		
		if($folder === NULL)
		{
			logFile("Abfrage von Bildgalerie-Ordner für Artikel fehlgeschlagen");

			return;
		}
		
		if(!is_string($folder))
		{
			logFile("Bildgalerie-Ordner ist kein gültiger Pfad");

			return;
		}
		
		if(strlen($folder) == 0)
		{
			logFile("Bildgalerie-Ordner für Artikel ist kein Pfad (leer in DB)");
			
			return;
		}
		
		$folderPath = $_SERVER['DOCUMENT_ROOT']."/$folder";
		
		// Try to delete the article picture folder
		// (and if it does not exist, don't do anything / ignore it simply).
		if(is_dir($folderPath))
		{
			$unprocessedFolder = "$folderPath/unprocessed";
			
			// Check if there is still an folder for unprocessed pictures,
			// used during uploads internally.
			// The folder is always empty because the upload program
			// will delete all files inside when it is done.
			// Anyway, the "unprocessed" folder usually remains
			// for future use (e.g. if you want to upload more photos).
			if(is_dir($unprocessedFolder))
			{
				// Try to delete.
				if(!rmdir($unprocessedFolder))
				{
					logFile("Konnte Ordner \"unprocessed\" nicht löschen trotz Existenz (fehlende Rechte?)");
					
					return;
				}
			}
			
			// Try to delete all article pictures.
			for($i = 0; $i < $pictureAmount; $i++)
			{
				$pictureFilePath = "$folderPath/$i.jpg";
				
				// Try to delete currently regarded file.
				// If it does not exist, don't care and go on with next picture.
				if(file_exists($pictureFilePath))
				{
					// If deletion does not work,
					// it might be due to missing write priviliges.
					if(!unlink($pictureFilePath))
					{
						logFile("Konnte Bilddatei nicht löschen trotz Existenz (fehlende Rechte?)");
						
						return;
					}
				}
			}
			
			// Because the existence of folder was proven,
			// the deletion should also be no problem.
			// If it fails anyway, this is very critical due to missing
			// write rights.
			if(!rmdir($folderPath))
			{
				logFile('Konnte Bilderordner nicht löschen trotz Existenz (fehlende Rechte?)');
				
				return;
			}
		}
		
		// Clean up database (delete old record).		
		$recordGalleryDeletionSuccess = $essentials -> getDatabaseManager() -> delete('Gallery', "GalleryID = $galleryID");
	
		if(!$recordGalleryDeletionSuccess)
		{
			logFile('Konnte Gallery Datensatz nicht löschen in DB trotz Existenz');

			return;
		}
	}
	
	function deleteStringTable(Essentials $essentials, $articleID)
	{
		$stringTableID = $essentials -> getDatabaseManager() -> selectFirst('StringTableID', 'Article', "ArticleID = $articleID");
		
		if($stringTableID === NULL)
		{
			logFile('Abfrage von Texttabelle-Verlinkungswert für Artikel fehlgeschlagen');
			
			return;
		}
		
		if(!is_numeric($stringTableID))
		{
			logFile('Texttabelle-Verlinkungswert für Artikel ist keine Zahl');
			
			return;
		}
		
		if($stringTableID < 0)
		{
			logFile('Texttabelle-Verlinkungswert für Artikel ist negativ');
			
			return;
		}
		
		// Clean up database (delete old record).		
		$recordStringTableDeletionSuccess = $essentials -> getDatabaseManager() -> delete('StringTable', "StringTableID = $stringTableID");
		
		if(!$recordStringTableDeletionSuccess)
		{
			logFile('Konnte StringTable Datensatz nicht löschen in DB trotz Existenz');
			
			return;
		}
	}
	
	function deletePriceData(Essentials $essentials, $articleID)
	{
		$priceDataID = $essentials -> getDatabaseManager() -> selectFirst('PriceDataID', 'Article', "ArticleID = $articleID");
	
		if($priceDataID === NULL)
		{
			logFile('Abfrage von Preistabelle-Verlinkungswert für Artikel fehlgeschlagen');
			
			return;
		}
		
		if(!is_numeric($priceDataID))
		{
			logFile('Preistabelle-Verlinkungswert für Artikel ist keine Zahl');
			
			return;
		}
		
		if($priceDataID < 0)
		{
			logFile('Preistabelle-Verlinkungswert für Artikel ist negativ');
			
			return;
		}
		
		// Clean up database (delete old record).		
		$recordPriceDataDeletionSuccess = $essentials -> getDatabaseManager() -> delete('PriceData', "PriceDataID = $priceDataID");
		
		if(!$recordPriceDataDeletionSuccess)
		{
			logFile('Konnte PriceData Datensatz nicht löschen in DB trotz Existenz');
			
			return;
		}
	}
	
	function processRequest(Essentials $essentials)
	{
		$articleID = NULL;
		$invalidRequestText = 'ungültige Anfrage zur Löschung eines Artikels';
		
		if(!isset($_POST['id']))
		{
			logFile($invalidRequestText);
			
			return false;
		}
		
		$articleID = $_POST['id'];
		
		if(!is_numeric($articleID))
		{
			logFile($invalidRequestText);
			
			return false;
		}
		
		if($articleID < 0)
		{
			logFile($invalidRequestText);
			
			return false;
		}
		
		deleteGallery($articleID);
		deleteStringTable($articleID);
		deletePriceData($articleID);
		
		// Now try to delete the article record after all necessary files / photos have been deleted from the disk as well as dependent records.
		$recordArticleDeletionSuccess = $essentials -> getDatabaseManager() -> delete('Article', "ArticleID = $articleID");
		
		if($recordArticleDeletionSuccess)
		{
			return true;
		}
        else
        {
            return false;
        }
	}
?>
<!DOCTYPE html>
<html lang="de">
	<head>
	</head>
	<body>
		<?php
			processRequest();
			echo '<script>window.location="', DOMAIN, '/home";</script>';
		?>
	</body>
</html>