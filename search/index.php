<?php
	require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/php/tools/essentials/index.php';

    $essentials = new Essentials();
?>
<!DOCTYPE html>
<html lang="de">
	<head>
		<?php
			// Load HTML tag information.
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/head/index.php';
		?>
	</head>
	<body class="bg-body-secondary">
		<?php
			// Load HTML header.
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/header/index.php';

			function getCategoryDropdown(Essentials $essentials)
			{
				$preparedHTMLString = '<div class="float-end dropdown">';
				$preparedHTMLString .= '<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuCategory" data-bs-toggle="dropdown" aria-expanded="false">';
				$preparedHTMLString .= 'Kategorien';
				$preparedHTMLString .= '</button>';
				$preparedHTMLString .= '<ul class="dropdown-menu" aria-labelledby="dropdownMenuCategory">';
				
				// By default, print the category "All" (-1).
				$preparedHTMLString .= '<li><button class="dropdown-item" type="button" onclick="window.location=\'?category=-1\'">Alle</button></li>';
				
				$categoriesFetched = $essentials -> getDatabaseManager() -> select("*", "Category", NULL, NULL);
				
				if($categoriesFetched == NULL || sizeof($categoriesFetched) <= 0)
				{
					// No categories existing in database.
					return 'Derzeit existieren keine Suchfilter (Kategorien)';
				}
				
				for($i = 0; $i < sizeof($categoriesFetched); $i++)
				{
					$preparedHTMLString .= '<li><button class="dropdown-item" type="button" onclick="window.location=\'?category='.$categoriesFetched[$i]['CategoryID'].'\'">'.$categoriesFetched[$i]['Title'].'</button></li>';
				}
				
				$preparedHTMLString .= '</ul></div>';
				
				return $preparedHTMLString;
			}
		?>
		<div class="container-fluid">
			<?php
				function str_replace_from_to($needle, $replace, $haystack, $from, $to)
				{
					if($from !== false)
					{
						return substr_replace($haystack, $replace, $from, $to);
					}
					else
					{
						return $haystack;
					}
				}

				function str_replace_first($needle, $replace, $haystack)
				{
					$pos = strpos($haystack, $needle);

					return str_replace_from_to($needle, $replace, $haystack, $pos, strlen($needle));
				}

				function formatPreviewPriceData($time, $timeUnit)
				{
					if($timeUnit == 0)
					{
						return $time > 1 ? "($time Stunden)" : ($time == 1 ? "/ Stunde" : "");
					}
					else
					{
						if($time == 1)
						{
							return "/ Tag";
						}
						else if($time > 1)
						{
							return "($time Tage)";
						}
                        else
                        {
                            return '';
                        }
					}
				}

				function printArticles(Essentials $essentials, $articleQuery)
				{
					$searchToolHTMLContent = '<div class="row">';
					$searchToolHTMLContent .= '<div class="col-7 col-sm-4 col-md-8 col-lg-8 col-xl-8 col-xxl-9 align-self-center">';
					$searchToolHTMLContent .= '<span class="fs-6 fw-bold">Mietartikel finden</span>';
					$searchToolHTMLContent .= '</div>';
					$searchToolHTMLContent .= '<div class="col-5 col-sm-8 col-md-4 col-lg-4 col-xl-4 col-xxl-3 align-self-center">';
					$searchToolHTMLContent .= getCategoryDropdown($essentials);
					$searchToolHTMLContent .= '</div>';
					$searchToolHTMLContent .= '</div>';

                    $essentials -> getViewFactory() -> echoFeed(NULL, $searchToolHTMLContent);
					
					if($articleQuery !== NULL)
					{
						foreach($articleQuery as $article)
						{
							$feedHTML = "";
							
							// Filepath (URL) to the preview picture
							// (needs to be determind in code below,
							//  e.g. the preview picture or corresponding error picture if something goes wrong).
							$previewPictureFileURL = '';
								
							// Folder (not global URL like "http://...", but local relative path to the folder) of the preview picture
							// where it is saved inside on your webserver
							// (needs to be determind in code below and is fetched from database,
							//  e.g. the preview picture or corresponding error picture if something goes wrong).
							$previewPictureFolderLocalPath = '';
								
							// Local path to preview picture (not global URL like "http://...", but locally specified with $_SERVER['DOCUMENT_ROOT'] )
							// where it is saved on your webserver
							// (needs to be determind in code below and is constructed with $previewPictureFolderLocalPath above later).
							// Used for making file existence checks with file_exists(..) which is not possible with an URL.
							$previewPictureFileLocalPath = '';
								
							$articleID = $article['ArticleID'];
								
							// Skip unloadable articles
							// (although this is a very critcial error which should be examined by a system admin in database
							//  because every record should have an ID by default and it should be loadable).
							if(!is_numeric($articleID))
							{
								continue;
							}
								
							$galleryID = $article['GalleryID'];
								
							// In this case, there is likely no Gallery record linked (no valid GalleryID specified)
							// for the current article (which should never happen).
							// Show this DB error to the user for easy error examination (error picture shown instead).
							// The error picture is only assigned when no error picture assignment was made yet.
							if(!is_numeric($galleryID) && strlen($previewPictureFileURL) == 0)
							{
								$previewPictureFileURL = DOMAIN.'/img/article/noGalleryLinked.png';
							}
							
							$previewPictureFolderLocalPath = $essentials -> getDatabaseManager() -> selectFirst('Folder', 'Gallery', "GalleryID = $galleryID");
							
							// In this case, there is no (valid) value for column Folder in the current record of table Gallery (which should never happen).
							// Show this DB error to the user for easy error examination (error picture shown instead).
							// The error picture is only assigned when no error picture assignment was made yet.
							if(!is_string($previewPictureFolderLocalPath) && strlen($previewPictureFileURL) == 0)
							{
								$previewPictureFileURL = DOMAIN.'/img/article/noPictureFolder.png';
							}
								
							$amountPictures = $essentials -> getDatabaseManager() -> selectFirst('PictureMax', 'Gallery', "GalleryID = $galleryID");
								
							// Self-explaining: the amount of pictures retrieved from DB
							// should be a valid value and MUST not be negative.
							if((!is_numeric($amountPictures) || $amountPictures < 0) && strlen($previewPictureFileURL) == 0)
							{
								$previewPictureFileURL = DOMAIN.'/img/article/invalidPicAmount.png';
							}
								
							$previewPictureFileLocalPath = $_SERVER['DOCUMENT_ROOT']."/$previewPictureFolderLocalPath/0.jpg";

							// Self-explaining: there were no photos uploaded for this article,
							// so show a corresponding error picture to the user (although the error is not critical ofc).
							if($amountPictures == 0 && strlen($previewPictureFileURL) == 0)
							{
								$previewPictureFileURL = DOMAIN.'/img/article/noPhotos.png';
							}

							// In this case, the photo file does not exist on the server
							// (which is not critical because maybe the user didn't want to have pictures for his article).
							// Show this DB error to the user for easy error examination (error picture shown instead).
							// The error picture is only assigned when no error picture assignment was made yet.
							if(!file_exists($previewPictureFileLocalPath) && strlen($previewPictureFileURL) == 0)
							{
								$previewPictureFileURL = DOMAIN.'/img/article/photoNotExisting.png';
							}
							
							// In this case, the preview picture (simply the first picture) exists and no error picture assigned before.
							if(file_exists($previewPictureFileLocalPath) && strlen($previewPictureFileURL) == 0)
							{
								$previewPictureFileURL = DOMAIN."/$previewPictureFolderLocalPath/0.jpg";
							}

							$feedHTML .= "<div style=\"cursor: pointer;\" onclick=\"window.location='".DOMAIN.'/article/?id='.$articleID."'\">";

							$feedHTML .= '<a href="'.DOMAIN.'/article/?id='.$articleID.'" class="fs-4 lh-1 link-offset-1 link-offset-1-hover link-underline link-underline-opacity-0 link-underline-opacity-100-hover link-dark">';

							if(!empty($article['Title']))
							{
								$feedHTML .= $article['Title'];
							}
							else
							{
								$feedHTML .= "Anzeige ohne Titel";
							}
							
							$feedHTML .= "</a>";
							
							$previewText = $article['PreviewText'];
							
							if(!is_string($previewText))
							{
								$previewText = 'Fehler! Ungültiger Vorschautext abgefragt aus Datenbank!';
							}
								
							$feedHTML .= "<p class=\"mt-3 fs-6 lh-sm\">";

							if(!empty($article['PreviewText']))
							{
								$lineMaxLength = 64;
								$lineBreak = "\r\n";

								// Ensure that there is at least one line break for the first line (otherwise there is just a white-space-break).
								if(strlen($article['PreviewText']) >= $lineMaxLength)
								{
									$firstLine = substr($article['PreviewText'], 0, $lineMaxLength);

									if(!str_contains($firstLine, $lineBreak))
									{
										$firstLine .= $lineBreak;

										$secondLine = substr($article['PreviewText'], $lineMaxLength, strlen($article['PreviewText']));

										$article['PreviewText'] = $firstLine.$secondLine;
									}
								}

								$articlePreviewTextArray = explode($lineBreak, $article['PreviewText'], 2);

								if(sizeof($articlePreviewTextArray) > 1)
								{
									$limitedNextLine = end($articlePreviewTextArray);

									// Limit the display length of the second (and last) line in the articles preview.
									if(strlen($limitedNextLine) >= $lineMaxLength)
									{
										$limitedNextLine = substr($limitedNextLine, 0, $lineMaxLength)."...";
									}

									$article['PreviewText'] = $articlePreviewTextArray[0].str_replace($lineBreak, "", $limitedNextLine);
								}

								$feedHTML .= $article['PreviewText'];
							}
							else // There was no preview text to load.
							{
								$feedHTML .= "-";
							}
							
							$feedHTML .= "</p>";
							$feedHTML .= "<span class=\"fs-5 fw-bold lh-1\">";
							
							if(!empty($article['PriceDataID']))
							{
								$priceDataID = $article['PriceDataID'];
								$priceDataQuery = $essentials -> getDatabaseManager() -> select('*', 'PriceData', "PriceDataID = $priceDataID", NULL);

								if($priceDataQuery !== NULL)
								{
									$priceDataQuery = $essentials -> getDatabaseManager() -> select('*', 'PriceData', 'PriceDataID = '.$article['PriceDataID'], NULL);

									$processedPrice0 = number_format($priceDataQuery[0]['Price0'], 2, ",", ".");

									// Looks if there is any rental rate specified in the database to give the user more details about the (time) conditions,
									// e.g. you are able to rent an object for at least 5 days and 9€ / day.
									if($priceDataQuery !== NULL)
									{
										// TODO Search the cheapest rate and not the first one only..
										$dataset = $priceDataQuery[0];

										$time0 = $dataset['Time0'];

										$feedHTML .= 'ab '.$processedPrice0.' '.COUNTRY_CURRENCY_SYMBOL.' '.formatPreviewPriceData($time0, $dataset['Unit0']);
									}
									else // Not enough price information.
									{
										$feedHTML .= $processedPrice0.' '.COUNTRY_CURRENCY_SYMBOL;
									}
								}
								else
								{
									$feedHTML .= "Preise im Artikel";
								}
							}
							
							$feedHTML .= "</span>";

							$feedHTML .= "</div>";
							
							// (the corresponding shown preview picture or error picture is determined correctly before in code)
							$essentials -> getViewFactory() -> echoFeed($previewPictureFileURL, $feedHTML);
						}
						
						$deliveryFeedHTMLContent = 
						<<<HTML
							<p class="fs-4">Lieferung + Abholung</p><ul class="list">
								<li class="listItem">
									<span>bis</span>
									<ul class="listInner">
							HTML;
								
						$deliveryFeedHTMLContent .= "<li class=\"listInnerItem\"><span>10 ".COUNTRY_DISTANCE_UNIT_TOKEN." | 15,00 ".COUNTRY_CURRENCY_SYMBOL."</span></li>";
						$deliveryFeedHTMLContent .= "<li class=\"listInnerItem\"><span>15 ".COUNTRY_DISTANCE_UNIT_TOKEN." | 22,50 ".COUNTRY_CURRENCY_SYMBOL."</span></li>";
						$deliveryFeedHTMLContent .= "<li class=\"listInnerItem\"><span>25 ".COUNTRY_DISTANCE_UNIT_TOKEN." | 37,50 ".COUNTRY_CURRENCY_SYMBOL."</span></li>";
						$deliveryFeedHTMLContent .= "<li class=\"listInnerItem\"><span>35 ".COUNTRY_DISTANCE_UNIT_TOKEN." | 43,75 ".COUNTRY_CURRENCY_SYMBOL."</span></li>";
						$deliveryFeedHTMLContent .= "<li class=\"listInnerItem\"><span>45 ".COUNTRY_DISTANCE_UNIT_TOKEN." | 56,25 ".COUNTRY_CURRENCY_SYMBOL."</span></li>";
						$deliveryFeedHTMLContent .= "<li class=\"listInnerItem\"><span>55 ".COUNTRY_DISTANCE_UNIT_TOKEN." | 68,75 ".COUNTRY_CURRENCY_SYMBOL."</span></li>";

						$deliveryFeedHTMLContent .= 
						<<<HTML
									</ul>
								</li>
							</ul>
						HTML;
							
						$essentials -> getViewFactory() -> echoFeed(NULL, $deliveryFeedHTMLContent);
					}
					else
					{
						$nothingOnlineHTMLContent = '<p class="fs-4 mt-1">Oops</p>';
						$nothingOnlineHTMLContent .= '<p class="fs-6">Anscheinend wurden noch keine Mietartikel (in dieser Kategorie) online geschaltet ...</p>';
						$nothingOnlineHTMLContent .= '<p class="fs-6">Schauen Sie bitte später noch einmal vorbei.</p>';
						$nothingOnlineHTMLContent .= '<a class="btn btn-primary mt-3 mb-3" href="'.DOMAIN.'">Startseite</a>';
						$nothingOnlineHTMLContent .= '<a class="btn btn-primary ms-3 mt-3 mb-3" href="'.DOMAIN.'/search">Weiterstöbern</a>';
					
						$essentials -> getViewFactory() -> echoFeed(NULL, $nothingOnlineHTMLContent);
					}
				}

				$validCategory = isset($_GET['category']) && is_numeric($_GET['category']);

				$categoryQuery = $validCategory ? ($essentials -> getDatabaseManager() -> selectFirst('Title', 'Category', 'CategoryID = ' . $_GET['category'])) : NULL;

				$validCategory &= $categoryQuery != NULL;

				if($validCategory)
				{
					// Loads all articles of a category (TODO Later set maximum amount).
					$articleQuery = $essentials -> getDatabaseManager() -> select('*', 'Article', 'CategoryID = '.$_GET['category'], NULL);
					printArticles($essentials, $articleQuery);
				}
				else
				{
					// Invalid category given. Loads then all articles.. (TODO regard maximum).
					$articleQuery = $essentials -> getDatabaseManager() -> select('*', 'Article', NULL, NULL);

					printArticles($essentials, $articleQuery);
				}
		?>
		</div>
		<?php
			// // Post-initialization and loading of necessary content after main part.
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/footer/index.php';
		?>
	</body>
</html>
