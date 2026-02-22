<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/php/tools/essentials/index.php';

    $essentials = new Essentials();
?>
<!DOCTYPE html>
<html lang="de">
	<head>
		<?php
			// Load head-tag information and its configuration.
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/head/index.php';
		?>
	</head>
	<body id="body" class="bg-body-secondary">
		<?php
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/header/index.php';
		?>
		<div class="container-fluid">
		<?php

			$article = NULL;

			// The article ID will change to a number > -1 if a valid article was found in the DB.
			$_SESSION['articleID'] = -1;
			$articleTitle = '-';
			$articleDescription = '-';

			$articleStringTable = NULL;
			$articlePriceData = NULL;

			if(isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] >= 0)
			{
				// Load article.
				$articleQuery = $essentials -> getDatabaseManager() -> select('*', 'Article', 'ArticleID = '.$_GET['id'], NULL);

				if($articleQuery != NULL)
				{
					$article = $articleQuery[0];

					$_SESSION['articleID'] = $_GET['id'];

					if(!empty($article['Title']))
					{
						$articleTitle = $article['Title'];
					}

					if(is_string($article['Description']))
					{
						$articleDescription = $article['Description'];
					}

					// Check if there is a StringTable specified and save it.
					if(!empty($article['StringTableID']))
					{
						$stringTableID = $article['StringTableID'];

						$stringTableQuery = $essentials -> getDatabaseManager() -> select('*', 'StringTable', "StringTableID = $stringTableID", NULL);

						if($stringTableQuery != NULL)
						{
							$articleStringTable = $stringTableQuery[0];
						}
					}

					if(!empty($article['PriceDataID']))
					{
						$priceDataID = $article['PriceDataID'];
						$priceDataQuery = $essentials -> getDatabaseManager() -> select('*', 'PriceData', "PriceDataID = $priceDataID", NULL);

						if($priceDataQuery != NULL)
						{
							$articlePriceData = $priceDataQuery[0];
						}
					}
				}
				else
				{
					// Make sure to reset the last loaded article to prevent wrong images from showing up.
					$_SESSION['articleID'] = -1;
				}
			}
			
			////////////// PHOTO FETCHING AND OUTPUTTING GALLERY BELOW

			$feedArticleHTMLContent = '<a class="btn btn-primary mt-3 mb-3 text-center fs-6" href="'.DOMAIN.'/search"><img class="text-danger p-1 align-self-middle" src="'.DOMAIN.'/img/symbol/arrow-return-left.svg" alt="◀"></a>';
			
			// Was the ID of the requested article verified? (to make sure it is a real article, known in DB, so > -1)
			if($_SESSION['articleID'] > -1)
			{
                $galleryID = $essentials -> getDatabaseManager() -> selectFirst('GalleryID', 'Article', 'ArticleID = '.$_SESSION['articleID'], NULL);

                if(is_numeric($galleryID) && $galleryID >= 0)
                {
                    // Local path to the folder which contains all pictures of your article (usually online) on your webserver,
                    // meaning relative to $_SERVER['DOCUMENT_ROOT'] (so without something like "https://")
                    $relativeFolderPath = $essentials -> getDatabaseManager() -> selectFirst('Folder', 'Gallery', 'GalleryID = '.$galleryID, NULL);

                    if(is_string($relativeFolderPath))
                    {
                        $feedArticleHTMLContent .= $essentials -> getViewFactory() -> genCarouselForArticle($relativeFolderPath);
                    }
                    else
                    {
                        $feedArticleHTMLContent .= '<p class="text-danger fs-6">Error because the fetched photo dataset is not readable</p>';
                    }
                }
                else
                {
                    $feedArticleHTMLContent .= '<p class="text-danger fs-6">Error because current article does not know its photo dataset</p>';
                }
			}

			/////////////// PROCESSING TEXT DATA AND OUTPUTTING BELOW (TITLES, DESCRIPTIONS, TABLES ETC.)
			
			
			$feedArticleHTMLContent .= '<div class="mt-4 mb-3">';
			$feedArticleHTMLContent .= '<span class="fs-2 fw-bold">'.$articleTitle.'</span><br>';
			
			// Make sure only authorized people can access the editing tools for the article.
			if(isset($_SESSION['username']) && $essentials -> getDatabaseManager() -> getAccountManager() -> hasPermissionAdmin($_SESSION['username']))
			{
				$feedArticleHTMLContent .= '<a href="'.DOMAIN.'/edit/?id='.$_SESSION['articleID'].'">Artikel bearbeiten</a><br>';
			}
			
			$feedArticleHTMLContent .= '</div>';
			
			if($articleStringTable !== NULL)
			{
				$allowStringTable = false;
				
				// Are there filled entries in the string table? (title ignored alone)
				for($i = 0; $i < 5; $i++)
				{
					if(!empty($articleStringTable['Identifier'.$i]) || !empty($articleStringTable['Description'.$i]))
					{
						// There is at least one filled row in that string table, so generate the whole table.
						$allowStringTable = true;
						
						break;
					}
				}
				
				if($allowStringTable)
				{
					$feedArticleHTMLContent .= '<table class="bg-secondary table table-bordered"><tr class="table-secondary"><th class="border-end-0"><p class="fw-normal text-nowrap position-absolute">'.(empty($articleStringTable['Title']) ? "&nbsp;" : $articleStringTable['Title'])."</p></th><th class=\"border-start-0\">&nbsp;</th></tr>";
				
					for($i = 0; $i < 5; $i++)
					{
						if(empty($articleStringTable['Identifier'.$i]) && empty($articleStringTable['Description'.$i]))
							continue; // don't generate empty rows (usually end of table then because in DB data is sorted this way).
						
						$feedArticleHTMLContent .= '<tr class="table-light"><td class="w-50">'.$articleStringTable['Identifier'.$i].'</td><td>'.$articleStringTable['Description'.$i].'</td></tr>';
					}
					
					$feedArticleHTMLContent .= '</table>';
				}
			}
			
			/*
			 * Function to receive a string, according to the situation.
			 * Returns a string which contains information on the articles time conditions,
			 * e.g. when which rental rate is applied or how long you can rent a device.
			 * The mode (0 = return condition hint for the price table on the article page,
			 * 1 = return a (or multiple) constructed list row(s) which contain(s) a set of possible rental durations for the article request section).
			 */
			function formatArticleCondition($time, $timeUnit) : string
			{
				if($timeUnit == 0) // Hour-related?
				{
					if($time == 1) // is it 1 hour exactly?
					{
						return 'je Stunde';
					}
					else if($time > 1) // it is more than 1 hour.
					{
						return "$time Stunden";
					}
					else
					{
						return '';
					}
				}
				else if($timeUnit == 1) // Day-related?
				{
					if($time == 1) // Is it exactly 1 day?
					{
						return '1 Tag';
					}
					else if($time > 1) // Is it more than 1 day?
					{
						return "$time Tage";
					}
					else
					{
						return '';
					}
				}
                else
                {
                    return '';
                }
			}

			$priceTableHTML = "";
			
			$processedPrice = array();

			if($articlePriceData != NULL)
			{
				for($i = 0; $i < 5; $i++)
				{
					if(empty($articlePriceData['Price'.$i]))
					{
						$processedPrice[] = '&nbsp;';
					}
					else
					{
						$processedPrice[] = number_format($articlePriceData['Price' . $i], 2, ",", ".");
					}
				}
				
				$prices = array();
				
				$times = array();
				$timeUnits = array();
				
				$totalMinHours = array();
				
				for($i = 0; $i < 5; $i++)
				{
					if(!empty($articlePriceData['Price'.$i]) && !empty($articlePriceData['Time'.$i]) && $articlePriceData['Time'.$i] > 0 && $articlePriceData['Unit'.$i] >= 0)
					{
						$prices[] = $processedPrice[$i];
						$times[] = $articlePriceData['Time' . $i];
						$timeUnits[] = $articlePriceData['Unit' . $i];

						if($articlePriceData['Unit'.$i] == 1)
						{
							$totalMinHours[] = $articlePriceData['Time' . $i] * 24;
						}
						else
						{
							$totalMinHours[] = $articlePriceData['Time' . $i];
						}
					}
				}

				$totalMinHoursSorted = $totalMinHours;
				
				// Uncommented because still not know: should the user decide on his own which order the prices should be displayed in table??
				// sort($totalMinHoursSorted);
				
				// Is there price data to display at all (not just empty rows from DB)?
				if(sizeof($totalMinHoursSorted) > 0)
				{
					$priceTableHTML = '<table class="table table-bordered text-center mb-4" style="display: inline-block;">';
					$priceTableHTML .= '<tr class="table-secondary"><th class="border-end-0 fw-normal">Preise</th><th class="border-start-0"></th></tr>';
				
					for($i = 0; $i < 5; $i++)
					{
						if($i < sizeof($totalMinHoursSorted))
						{
							$hours = $totalMinHoursSorted[$i];
							
							$indexUnsorted = array_search($hours, $totalMinHours);
							
							$priceTableHTML .= "<tr class=\"table-light\"><td>".$prices[$indexUnsorted].COUNTRY_CURRENCY_SYMBOL."&nbsp;</td><td>".formatArticleCondition($times[$indexUnsorted], $timeUnits[$indexUnsorted])."</td></tr>";
						}
					}
					
					$priceTableHTML .= "</table>";
				}
				
			}
			
			$feedArticleHTMLContent .= $priceTableHTML;
			
			// The empty span with ID "focusRequestForm" is for the contact feed below in order to re-navigate to it correctly after re-loading the page.
			$feedArticleHTMLContent .= '<p class="fs-4 mb-3">Beschreibung:</p><p style="white-space: pre-wrap;" class="mb-4 fs-6 lh-1">'.$articleDescription.'<span id="focusRequestForm"></span></p>';
			$feedArticleHTMLContent .= '<kbd class="mb-3 d-inline-block lh-1 p-1">Anzeige:&nbsp;'.($_SESSION['articleID'] > -1 ? $_SESSION['articleID'] : '-').'</kbd>';
			
			$essentials -> getViewFactory() -> echoFeed(NULL, $feedArticleHTMLContent);
			
			///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// Contact form below generated in a separate feed window:
			
			use PHPMailer\PHPMailer\PHPMailer;
			use PHPMailer\PHPMailer\Exception;
			use PHPMailer\PHPMailer\SMTP;
			
			require $_SERVER['DOCUMENT_ROOT'].'/include/PHPMailer/Exception.php';
			require $_SERVER['DOCUMENT_ROOT'].'/include/PHPMailer/PHPMailer.php';
			require $_SERVER['DOCUMENT_ROOT'].'/include/PHPMailer/SMTP.php';
			
			function genDefaultRequestPage(Essentials $essentials, $id): string
            {
				$dateToday = date("Y-m-d");

				$generatedHTMLString = '<form method="POST" action="'.DOMAIN.'/article/?id='.$id.'#focusRequestForm"><input type="hidden" name="step" value="0">';

				$generatedHTMLString .= '<div class="mb-3"><label class="form-label" for="date">Mietdatum auswählen:</label><input class="form-control" id="date" name="date" type="date" value="'.$dateToday.'" required></div>';

				$generatedHTMLString .= '<div class="mb-3"><label class="form-label" for="duration">Mietdauer auswählen:</label><select class="form-select" id="duration" name="duration">';

				// Load article.
				$articleQuery = $essentials -> getDatabaseManager() -> select('*', 'Article', 'ArticleID = '.$id, NULL);

				if(!is_null($articleQuery))
				{
					$article = $articleQuery[0];

					if(!is_null($article))
					{
						$priceDataID = $article['PriceDataID'];

						if(is_numeric($priceDataID) && $priceDataID >= 0)
						{
							$priceDataQuery = $essentials -> getDatabaseManager() -> select('*', 'PriceData', "PriceDataID = $priceDataID", NULL);

							if(!is_null($priceDataQuery))
							{
								$priceData = $priceDataQuery[0];
								
								if(!empty($priceData))
								{
                                    for($i = 0; $i < 5; $i++)
                                    {
                                        $time = $priceData['Time'.$i];
                                        $unit = $priceData['Unit'.$i];

                                        if(is_numeric($time) && $time > 0 && is_numeric($unit) && ($unit == 0 || $unit == 1))
                                        {
                                            $generatedHTMLString .= '<option value="'.$time.'">';
                                            $generatedHTMLString .= formatArticleCondition($time, $unit);
                                            $generatedHTMLString .= '</option>';
                                        }
                                    }
								}
                                else
                                {
                                    return '';
                                }
							}
                            else
                            {
                                return '';
                            }
						}
                        else
                        {
                            return '';
                        }
					}
                    else
                    {
                        return '';
                    }
				}
                else
                {
                    return '';
                }

				$generatedHTMLString .= '</select></div>';
				$generatedHTMLString .= '<input class="mb-3 btn btn-primary" type="submit" value="Verfügbarkeit prüfen"></form>';
			
				return $generatedHTMLString;
			}

			function genStep0(Essentials $essentials, $id)
			{
				$generatedHTMLString = '';
				
				// Load article.
				$articleQuery = $essentials -> getDatabaseManager() -> select('*', 'Article', 'ArticleID = '.$id, NULL);

				if($articleQuery != NULL)
				{
					$article = $articleQuery[0];
					
					$available = $article['StockAmount'] > 0;
					
					if($available)
					{
						// Save the rent configuration for the completion of the request.
						$_SESSION['rentArticleDate'] = $_POST['date'];
						$_SESSION['rentArticleDuration'] = $_POST['duration'];
						
						$generatedHTMLString .= '<div class="mb-3"><span class="fs-6 text-success">'.$article['StockAmount'].' '.LANG_ARTICLE_OBJECT_NAME_SINGLE.($article['StockAmount'] == 1 ? '' : 'e').' '.($article['StockAmount'] == 1 ? 'ist' : 'sind').' verfügbar</span></div>';
						
						$generatedHTMLString .= '<form method="POST" action="'.DOMAIN.'/article/?id='.$id.'#focusRequestForm">';
						$generatedHTMLString .= '<div class="mb-3"><input type="hidden" name="step" value="1"><input class="btn btn-primary" type="submit" value="Weiter"></div>';
						$generatedHTMLString .= '</form>';
						
						$generatedHTMLString .= '<form method="POST" action="'.DOMAIN.'/article/?id='.$id.'#focusRequestForm"><input type="hidden" name="step" value="-1">';
						$generatedHTMLString .= '<div class="mb-3"><input class="btn btn-secondary" type="submit" value="Zurück"></div>';
						$generatedHTMLString .= '</form>';
					}
					else
					{
						$generatedHTMLString .= '<form method="POST" action="'.DOMAIN.'/article/?id='.$id.'#focusRequestForm">';
						$generatedHTMLString .= '<div class="mb-3"><input type="hidden" name="step" value="0"><span class="fs-6 text-danger">Nichts zurzeit leider verfügbar</span></div>';
						$generatedHTMLString .= '<div class="mb-3"><input class="btn btn-primary" type="submit" value="Zurück"></div>';
						$generatedHTMLString .= '</form>';
					}
				}
				
				return $generatedHTMLString;
			}

			function genStep1(Essentials $essentials, $id)
			{
				
				// Load article.
				$articleQuery = $essentials -> getDatabaseManager() -> select('*', 'Article', 'ArticleID = '.$id, NULL);
				
				$generatedHTMLString = '';
				
				if($articleQuery != NULL)
				{
					$article = $articleQuery[0];
					$stockAmount = $article['StockAmount'];
					
					$generatedHTMLString .= '<form method="POST" action="'.DOMAIN.'/article/?id='.$id.'#focusRequestForm"><input type="hidden" name="step" value="2">';
					$generatedHTMLString .= '<div class="mb-3"><label class="form-label" for="amountList"><span class="text-success">Anzahl</span> angefragter '.LANG_ARTICLE_OBJECT_NAME_PLURAL.':</label><select class="form-select" id="amountList" name="amount">';

					for($i = 1; $i <= $stockAmount; $i++)
					{
						$generatedHTMLString .= '<option value="'.$i.'">'.$i.' x</option>';
					}
					
					$generatedHTMLString .= '</select></div>';
					
					$generatedHTMLString .= '<div class="mb-3"><label class="form-label" for="deliveryList">Lieferung + Abholung:</label><select class="form-select" id="deliveryList" name="delivery"><option value="0">nein, Danke</option><option value="10">bis 10 km</option><option value="15">bis 15 km</option><option value="25">bis 25 km</option><option value="35">bis 35 km</option><option value="45">bis 45 km</option><option value="55">bis 55 km</option></select></div>';
					$generatedHTMLString .= '<input class="btn btn-primary mb-3" type="submit" value="Weiter">';
					
					$generatedHTMLString .= '</form>';
				}
				
				$generatedHTMLString .= '<form method="POST" action="'.DOMAIN.'/article/?id='.$id.'#focusRequestForm"><input type="hidden" name="step" value="-1"><input class="btn btn-secondary mb-3" type="submit" value="Abbrechen"></form>';
				
				return $generatedHTMLString;
			}

			function genStep2($id)
			{
				$_SESSION['rentArticleAmount'] = $_POST['amount'];
				$_SESSION['rentArticleDelivery'] = $_POST['delivery'];

				$generatedHTMLString = '<form method="POST" action="'.DOMAIN.'/article/?id='.$id.'#focusRequestForm"><input type="hidden" name="step" value="3">';
				$generatedHTMLString .= '<div class="mb-3"><label class="form-label" for="salutationList">Anrede*:</label><select class="form-select" id="salutationList" name="salutation"><option value="0">Herr</option><option value="1">Frau</option></select></div>';
				$generatedHTMLString .= '<div class="mb-3"><label class="form-label" for="firstnameField">Vorname*:</label><input class="form-control" type="text" id="firstnameField" name="firstname" required></div>';
				$generatedHTMLString .= '<div class="mb-3"><label class="form-label" for="lastnameField">Nachname*:</label><input class="form-control" type="text" id="lastnameField" name="lastname" required></div>';
				$generatedHTMLString .= '<div class="mb-3"><label class="form-label" for="streetField">Straße*:</label><input class="form-control" type="text" id="streetField" name="street" required></div>';
				$generatedHTMLString .= '<div class="mb-3"><label class="form-label" for="noField">Hausnummer*:</label><input class="form-control" type="text" id="noField" name="no" required></div>';
				$generatedHTMLString .= '<div class="mb-3"><label class="form-label" for="zipField">PLZ*:</label><input class="form-control" type="text" id="zipField" name="zip" pattern="^(?!01000|99999)(0[1-9]\d{3}|[1-9]\d{4})$" required></div>';
				$generatedHTMLString .= '<div class="mb-3"><label class="form-label" for="placeField">Ort*:</label><input class="form-control" type="text" id="placeField" name="place" required></div>';
				$generatedHTMLString .= '<div class="mb-3"><input class="btn btn-primary" type="submit" value="Weiter"></div>';
				$generatedHTMLString .= '</form>';
				
				$generatedHTMLString .= '<form method="POST" action="'.DOMAIN.'/article/?id='.$id.'#focusRequestForm"><input type="hidden" name="step" value="-1">';
				$generatedHTMLString .= '<input class="btn btn-secondary mb-3" type="submit" value="Abbrechen">';
				$generatedHTMLString .= '</form>';
			
				return $generatedHTMLString;
			}

			function genStep3($id)
			{
				$_SESSION['rentArticleSalutation'] = $_POST['salutation'];
				
				$_SESSION['rentArticleFirstname'] = $_POST['firstname'];
				$_SESSION['rentArticleLastname'] = $_POST['lastname'];
				
				$_SESSION['rentArticleStreet'] = $_POST['street'];
				$_SESSION['rentArticleNo'] = $_POST['no'];
				
				$_SESSION['rentArticleZip'] = $_POST['zip'];
				$_SESSION['rentArticlePlace'] = $_POST['place'];
				
				$generatedHTMLString = '<form method="POST" action="'.DOMAIN.'/article/?id='.$id.'#focusRequestForm"><input type="hidden" name="step" value="4">';
				$generatedHTMLString .= '<div class="mb-3"><label class="form-label" for="phoneField">Telefon*:</label><input class="form-control" type="tel" id="phoneField" name="phone" required></div>';
				$generatedHTMLString .= '<div class="mb-3"><label class="form-label" for="emailField">E-Mail*:</label><input class="form-control" type="email" id="emailField" name="email" required></div>';
				$generatedHTMLString .= '<div class="mb-3"><input class="btn btn-primary" type="submit" value="Jetzt anfragen"></div>';
				$generatedHTMLString .= '</form>';
				
				$generatedHTMLString .= '<form method="POST" action="'.DOMAIN.'/article/?id='.$id.'#focusRequestForm"><input type="hidden" name="step" value="-1">';
				$generatedHTMLString .= '<input class="btn btn-secondary mb-3" type="submit" value="Abbrechen">';
				$generatedHTMLString .= '</form>';
				
				return $generatedHTMLString;
			}

			function sendEmail(Essentials $essentials)
			{
				if(isset($_POST['email']) && !empty($_POST['email']))
				{
					$_SESSION['rentArticleEmail'] = $_POST['email'];
				}

				//Create an instance; passing `true` enables exceptions
				$mail = new PHPMailer(true);

				$css = '<style>a.email{color: white;} a.email:active{color: white;} a.email:link{color: white;} a.email:visited{color: lightgray;} a.email:hover{color: lightgray;}</style>';

				$_SESSION['rentArticlePhone'] = $_POST['phone'];

				$articleTitle = $essentials -> getDatabaseManager() -> selectFirst('Title', 'Article', 'ArticleID = '.$_SESSION['articleID']);

				$rentalDevice = 'Artikel: ';

				if($articleTitle != NULL)
				{
					$rentalDevice .= '„'.$articleTitle.'“ (Artikel-Nr.: '.$_SESSION['articleID'].')';
				}
				else
				{
					$rentalDevice .= '„ohne Titel“ (Artikel-Nr.: '.$_SESSION['articleID'].')';
				}

				$plainTextDetails = $rentalDevice."<br>";
				$htmlTextDetails = '<span>'.$rentalDevice."</span><br>";

				$timeSince1970 = strtotime($_SESSION['rentArticleDate']);
				$germanDateString = date('d.m.y', $timeSince1970);

				$rentalDate = 'Datum der Anmietung: '.$germanDateString;

				$plainTextDetails .= $rentalDate."<br>";
				$htmlTextDetails .= '<span>'.$rentalDate."</span><br>";

				if($_SESSION['rentArticleDuration'] < 24)
				{
					$rentalDuration = 'Leihzeit: '.$_SESSION['rentArticleDuration'].' Stunden';

					$plainTextDetails .= $rentalDuration."<br>";
					$htmlTextDetails .= '<span>'.$rentalDuration.'</span><br>';
				}
				else if($_SESSION['rentArticleDuration'] == 24)
				{
					$rentalDuration = 'Leihzeit: 1 Tag';

					$plainTextDetails .= $rentalDuration."<br>";
					$htmlTextDetails .= '<span>'.$rentalDuration.'</span><br>';
				}
				else if($_SESSION['rentArticleDuration'] % 24 > 0)
				{
					$hours = $_SESSION['rentArticleDuration'] % 24;
					$days = ($_SESSION['rentArticleDuration'] - $hours) / 24;

					$rentalDuration = 'Leihzeit: '.$days.' Tage und '.$hours.' Stunden';

					$plainTextDetails .= $rentalDuration."<br>";
					$htmlTextDetails .= '<span>'.$rentalDuration.'</span><br>';
				}
				else
				{
					$days = $_SESSION['rentArticleDuration'] / 24;

					$rentalDuration = 'Leihzeit: '.$days.' Tage';

					$plainTextDetails .= $rentalDuration."<br>";
					$htmlTextDetails .= '<span>'.$rentalDuration.'</span><br>';
				}

				$rentalAmount = 'Anzahl der '.LANG_ARTICLE_OBJECT_NAME_PLURAL.': '.$_SESSION['rentArticleAmount'];

				$plainTextDetails .= $rentalAmount."<br>";
				$htmlTextDetails .= '<span>'.$rentalAmount.'</span><br>';

				$km = $_SESSION['rentArticleDelivery'];

				if($km == 0)
				{
					$rentalDelivery = 'Lieferung + Abholung: keine';

					$plainTextDetails .= $rentalDelivery."<br><br>";
					$htmlTextDetails .= '<span>'.$rentalDelivery.'</span><br><br>';
				}
				else
				{
					$rentalDelivery = 'Lieferung + Abholung: bis '.$km.' km';

					$plainTextDetails .= $rentalDelivery."<br><br>";
					$htmlTextDetails .= '<span>'.$rentalDelivery.'</span><br><br>';
				}

				$customerContact = 'Kontaktdaten des Anmieters: ';

				$plainTextDetails .= $customerContact."<br><br>";
				$htmlTextDetails .= '<span>'.$customerContact.'</span><br><br>';

				if($_SESSION['rentArticleSalutation'] == 0)
				{
					$salutation = 'Herr';

					$plainTextDetails .= $salutation;
					$htmlTextDetails .= '<span>'.$salutation;
				}
				else
				{
					$salutation = 'Frau';

					$plainTextDetails .= $salutation;
					$htmlTextDetails .= '<span>'.$salutation;
				}

				$fullName = ' '.$_SESSION['rentArticleFirstname'].' '.$_SESSION['rentArticleLastname'];

				$plainTextDetails .= $fullName."<br>";
				$htmlTextDetails .= $fullName.'</span><br>';

				$fullStreet = $_SESSION['rentArticleStreet'].' '.$_SESSION['rentArticleNo'];

				$plainTextDetails .= $fullStreet."<br>";
				$htmlTextDetails .= '<span>'.$fullStreet.'</span><br>';

				$fullPlace = $_SESSION['rentArticleZip'].' '.$_SESSION['rentArticlePlace'];

				$plainTextDetails .= $fullPlace."<br>";
				$htmlTextDetails .= '<span>'.$fullPlace.'</span><br>';

				$customerPhone = $_SESSION['rentArticlePhone'];

				$plainTextDetails .= 'Telefon: '.$customerPhone;
				$htmlTextDetails .= '<span>Telefon: '.$customerPhone.'</span>';

				// Check if the customer left an e-mail address.
				if(isset($_SESSION['rentArticleEmail']) && !empty($_SESSION['rentArticleEmail']))
				{
					$plainTextDetails .= '<br><span>E-Mail: '.$_SESSION['rentArticleEmail'].'</span>';
					$htmlTextDetails .= '<br><span>E-Mail: <a class="email" href="'.$_SESSION['rentArticleEmail'].'">'.$_SESSION['rentArticleEmail'].'</a></span>';
				}

				// See if the customer also wants to receive a validatin email for his/her article request.
				if(isset($_POST['email']) && !empty($_POST['email']))
				{
					$plainText = 'Sehr geehrte';
					$htmlText = '<span>'.$plainText;

					if($_SESSION['rentArticleSalutation'] == 0)
					{
						$fullSalutation = 'r Herr '.$_SESSION['rentArticleLastname'].',';

						$plainText .= $fullSalutation;
						$htmlText .= $fullSalutation;
					}
					else
					{
						$fullSalutation = ' Frau '.$_SESSION['rentArticleLastname'].',';

						$plainText .= $fullSalutation;
						$htmlText .= $fullSalutation;
					}

					$plainText .= "</span><br><br>";
					$htmlText .= '</span><br><br><span>';

					$validationHint = 'Ihre Anfrage zu einem unserer Mietartikel ist soeben bei uns eingetroffen.';

					$plainText .= $validationHint."<br>";
					$htmlText .= $validationHint.'</span><br>';

					$informationHint = 'Folgende Informationen haben Sie uns mitgeteilt:';

					$plainText .= $informationHint."<br><br>";
					$htmlText .= '<span>'.$informationHint.'</span><br><br><br>';

					$plainText .= $plainTextDetails.'<br><br>';
					$htmlText .= '<div style="display: inline-block; padding: 8px; background-color: #526787; color: white; border-radius: 8px; box-shadow: 0px 0px 3px black;">'.$htmlTextDetails.'</div><br><br><br>';

					$correctionHintLeftPart = 'Falls Informationen nicht stimmen sollten, so können Sie uns eine E-Mail an ';

					$plainText .= $correctionHintLeftPart;
					$htmlText .= '<span>'.$correctionHintLeftPart;

					$technicalSupportEmail = EMAIL_TECHNICAL;

					$plainText .= $technicalSupportEmail;
					$htmlText .= "</span><a class=\"email\" style=\"color: white;\" href=\"mailto:$technicalSupportEmail\">$technicalSupportEmail</a><span>";

					$correctionHintRightPart = ' senden und um Korrektur bitten.';

					$plainText .= $correctionHintRightPart."<br>";
					$htmlText .= $correctionHintRightPart.'</span><br>';

					$feedbackHint = "Im Laufe des Tages erhalten Sie einen Anruf bezüglich Ihrer Anfrage (Ausnahme bilden die üblichen Ruhezeiten).";

					$plainText .= $feedbackHint."<br><br>";
					$htmlText .= '<span>'.$feedbackHint.'</span><br><br>';

					$thankYou = 'Wir bedanken uns für Ihre Anfrage und wünschen einen angenehmen Tag!';

					$plainText .= $thankYou."<br><br>";
					$htmlText .= '<span style="font-weight: bold;">'.$thankYou.'</span><br><br>';

					$yourCompany = 'Ihr Team von '.COMPANY_NAME;

					$plainText .= $yourCompany;
					$htmlText .= '<span>'.$yourCompany.'</span>';

					error_reporting(0);

					try
					{
						// Server settings
						$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
						$mail->isSMTP();                                            //Send using SMTP
						$mail->Host       = 'send.one.com';// SMTP_HOST;                     //Set the SMTP server to send through (usually pre-defined by your PHP server - see your webhoster)
						$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
						$mail->Username   = MAIL_USER;                     //SMTP username
						$mail->Password   = MAIL_PASSWORD;                               //SMTP password
						$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
						$mail->Port       = MAIL_PORT;

						$mail->setFrom(MAIL_USER, COMPANY_NAME);

						// General Content settings.
						$mail->isHTML(true);                                  //Set email format to HTML
						$mail->CharSet = 'UTF-8';

						// Turn off debug information which could give insights to the server.
						$mail->SMTPDebug = 0;

						$mail->addAddress($_POST['email'], $fullName);
						$mail->Subject = 'Anmietung - Empfangsbestätigung';
						$mail->Body    = '<DOCTYPE! html><html><head>'.$css.'</head><body><div style="display: inline-block; background-color: #102b48; color: #efd4b7; padding: 50px; border-radius: 8px; min-height: 600px; height: 100%; font-size: 13px; font-family: Arial;">'.'<img src="'.DOMAIN.'/img/logo/company/logo.png" alt="'.COMPANY_NAME.'" style="display: inline-block; border-style: solid; border-color: #ffffff77; border-width: 2px; border-radius: 25px; width: 130px; height: auto; box-shadow: 0px 0px 5px white; margin-bottom: 27px;"><br>'.$htmlText.'</div></body></html>';
						$mail->AltBody = $plainText;

						$mail->send();

						$mail->clearAddresses();

						// Make sure, no one can re-send the same request by refreshing the page.
						session_unset();
					}
					catch(Exception $e)
					{
						// Make sure, no one can re-send the same request by refreshing the page.
						session_unset();
					}
				}

                $generatedHTMLString = '';

				try
				{
					// General Content settings.
					$mail->CharSet = 'UTF-8';

					$mail->setFrom(EMAIL_NOREPLY, COMPANY_NAME.' (noreply)');
					$mail->addAddress(EMAIL_GENERAL, COMPANY_NAME);						  // Add recipient
					//$mail->addBCC('janek.piekarski.postfach@gmail.com', 'Janek Piekarski');     // Add a reply-to recipient

					$mail->Subject = 'Neue Anfrage von einem Kunden';

					$informationHint = 'Ein Kunde hat folgende Informationen hinterlassen:';

					$plainText = $informationHint."<br><br>";
					$htmlText = '<span style="font-weight: bold; font-size: 17px;">'.$informationHint.'</span><br><br>';

					$plainText .= $plainTextDetails;
					$htmlText .= '<div style="display: inline-block; padding: 15px; background-color: #526787; color: white; border-radius: 8px; box-shadow: 0px 0px 3px black;">'.$htmlTextDetails.'</div><br><br><br>';

					$htmlText .= '<span style="display: block; font-family: Arial; font-weight: bold; font-size: 17px; color: #efd4b7;">Folgende Informationen fehlen noch:</span><br>'.'<div style="display: inline-block; padding: 15px; background-color: #526787; color: white; border-radius: 8px; box-shadow: 0px 0px 3px black;">'.'<ul style="margin: 0; padding: 0; list-style-type: none; font-family: Arial; display: block; color: white;"><li>- Liefer- / Abholzeit</li><li>- Ausweisnummer (vor Ort)</li><li>- Unterschrift</li><li>- Rechnungsbetrag</li></ul>'.'</div>';

					$mail->Body    = '<DOCTYPE! html><html><head>'.$css.'</head><body><div style="display: inline-block; background-color: #102b48; color: #efd4b7; padding: 35px; border-radius: 8px;">'.$htmlText.'</div></body></html>';
					$mail->AltBody = $plainText;

					$mail->send();

					$generatedHTMLString .= '<span class="fs-6 text-success">Ihre Anfrage wurde gesendet!</span>';

					// Make sure, no one can re-send the same request by refreshing the page.
					session_unset();
				}
				catch(Exception $e)
				{
					$generatedHTMLString .= '<p class="mb-3 text-danger fs-6">Ihre Anfrage konnte leider nicht gesendet werden (technischer Fehler)</p>';
					$generatedHTMLString .= '<div class="mb-3"><p class="fs-6">Entweder ist</p><span class="fs-6">• Ihre E-Mail inkorrekt eingegeben worden <span class="fw-bold">oder</span></span><br><span class="fs-6">• unser Mail-Server derzeit gestört</span></div>';
					$generatedHTMLString .= '<p class="mb-3">Falls das Problem weiterhin besteht,<br>schreiben Sie uns bitte an <a href="mailto:'.EMAIL_TECHNICAL.'">'.EMAIL_TECHNICAL.'</a> oder rufen Sie uns an (<a href="tel:'.COMPANY_PHONE_MOBILE.'">'.COMPANY_PHONE_MOBILE.'</a>).</p>';
					$generatedHTMLString .= '<button class="btn btn-primary mb-3" onclick="window.location=\'#body\'">Nach oben</button>';

					// Make sure, no one can re-send the same request by refreshing the page.
					session_unset();
				}
				
				return $generatedHTMLString;
			}

			$feedContactFormHTMLContent = '<p class="fs-4">'.LANG_ARTICLE_OBJECT_NAME_SINGLE.' anfragen</p>';

            // If a valid article ID was verified and is essentials to use ($_SESSION['articleID])
			if(isset($_SESSION['articleID']) && is_numeric($_SESSION['articleID']) && $_SESSION['articleID'] >= 0)
			{
                if(isset($_POST['step']) && is_numeric($_POST['step']) && $_POST['step'] > -1)
                {
                    if($_POST['step'] == 0 && isset($_POST['date']) && isset($_POST['duration']))
                    {
                        $feedContactFormHTMLContent .= genStep0($essentials, $_SESSION['articleID']);
                    }
                    else if($_POST['step'] == 1 && isset($_SESSION['rentArticleDate']) && isset($_SESSION['rentArticleDuration']))
                    {
                        $feedContactFormHTMLContent .= genStep1($essentials, $_SESSION['articleID']);
                    }
                    else if($_POST['step'] == 2 && isset($_POST['amount']) && $_POST['amount'] > 0 && $_POST['amount'] <= 10 && isset($_POST['delivery']) && ($_POST['delivery'] == 0 || $_POST['delivery'] == 10 || $_POST['delivery'] == 15 || $_POST['delivery'] == 25 || $_POST['delivery'] == 35 || $_POST['delivery'] == 45 || $_POST['delivery'] == 55))
                    {
                        $feedContactFormHTMLContent .= genStep2($_SESSION['articleID']);
                    }
                    else if($_POST['step'] == 3 && isset($_POST['salutation']) && ($_POST['salutation'] == 0 || $_POST['salutation'] == 1) && isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['street']) && isset($_POST['no']) && isset($_POST['zip']) && isset($_POST['place']))
                    {
                        $feedContactFormHTMLContent .= genStep3($_SESSION['articleID']);
                    }
                    else if($_POST['step'] == 4 && isset($_SESSION['rentArticleAmount']) && isset($_SESSION['rentArticleDelivery']) && isset($_SESSION['rentArticleDate']) && isset($_SESSION['rentArticleDuration']) && isset($_SESSION['rentArticleSalutation']) && isset($_SESSION['rentArticleFirstname']) && isset($_SESSION['rentArticleLastname']) && isset($_SESSION['rentArticleStreet']) && isset($_SESSION['rentArticleNo']) && isset($_SESSION['rentArticleZip']) && isset($_SESSION['rentArticlePlace']) && isset($_POST['phone']))
                    {
                        $feedContactFormHTMLContent .= sendEmail($essentials);
                    }
                    else
                    {
                        $feedContactFormHTMLContent .= genDefaultRequestPage($essentials, $_SESSION['articleID']);
                    }
                }
                else
                {
                    $feedContactFormHTMLContent .= genDefaultRequestPage($essentials, $_SESSION['articleID']);
                }
			}
			
			$essentials -> getViewFactory() -> echoFeed(NULL, $feedContactFormHTMLContent);
		?>
		</div>
		<?php
			// // Post-initialization and loading of necessary content after main part.
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/footer/index.php';
		?>
	</body>
</html>
