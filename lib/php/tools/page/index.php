<?php
    /**
     * This PHP class delivers functions to easily generate Bootstrap webpages,
     * such as with pre-defined and pre-programmed Bootstrap carousels.
     * So, this PHP file also combines program logic with Bootstrap,
     * so you don't have to care about things like "How many carousel items do I have to write in HTML?".
     * Simply read the doc of the single functions and you will know exactly how to use them.
     *
     * Technical requirements: Bootstrap 5.3, JQuery and Popper.
     */
    class ViewFactory
    {
        /**
         * Pre-generates a Bootstrap row in HTML with a top-margin of 5 Bootstrap units (mt-5).
         * So the pre-generated HTML will be returned (NOT outputted).
         * DO NOT use this function outside this PHP file unless you know what you do!
         * Make sure you place the returned HTML code inside a div element of this form
         * (note the correct PHP function call).
         *
         * <div class="container-fluid"><?php echo preGenRow(..); ?></div>
         *
         * @param string $rowContentHTML Simply the HTML code for your Bootstrap row.
         * @param string $applyTopMargin When 'true', a top-margin of 5 Bootstrap units is applied for the generated row. Otherwise not if 'false'.
         * @param int $leftCol Default value for left Bootstrap column (1 to 12)
         * @param int $leftColXL Value for left Bootstrap column on XL viewport (1 to 12)
         * @param int $leftColXXL Value for left Bootstrap column on XXL viewport (1 to 12)
         * @param int $middleCol Default value for middle Bootstrap column (1 to 12)
         * @param int $middleColXL Value for middle Bootstrap column on XL viewport (1 to 12)
         * @param int $middleColXXL Value for middle Bootstrap column on XXL viewport (1 to 12)
         * @param int $rightCol Default value for right Bootstrap column (1 to 12)
         * @param int $rightColXL Value for right Bootstrap column on XL viewport (1 to 12)
         * @param int $rightColXXL Value for right Bootstrap column on XXL viewport (1 to 12)
         * @return string Row HTML as described.
         */
        public function preGenRow($rowContentHTML, $applyTopMargin, $leftCol, $leftColXL, $leftColXXL, $middleCol, $middleColXL, $middleColXXL, $rightCol, $rightColXL, $rightColXXL) : string
        {
            if(!is_bool($applyTopMargin))
            {
                logFile("preGenRow(..): expected boolean but got something different (applyTopMargin)");

                return '';
            }

            if(!is_int($leftCol) || !is_int($leftColXL) || !is_int($leftColXXL) || !is_int($middleCol) || !is_int($middleColXL) || !is_int($middleColXXL) || !is_int($rightCol) || !is_int($rightColXL) || !is_int($rightColXXL))
            {
                logFile("preGenRow(..): expected int but got something different (value for Bootstrap column)");

                return '';
            }

            $appliedTopMarginCode = ($applyTopMargin) ? ' mt-5' : '';

            // Use of Bootstrap classes for columns here explained:
            // first "col-..." is applied,
            // after that XL viewport (col-xl-...) is fulfilled,
            // and after that the specifications for XXL viewport are used.

            $bootstrapRowString = '<div class="row'.$appliedTopMarginCode.'">';

            if($leftCol > 0 && $leftColXL > 0 && $leftColXXL > 0)
                $bootstrapRowString .= '<div class="col-'.$leftCol.' col-xl-'.$leftColXL.' col-xxl-'.$leftColXXL.'"></div>';

            $bootstrapRowString .= '<div class="col-'.$middleCol.' col-xl-'.$middleColXL.' col-xxl-'.$middleColXXL.'">'.$rowContentHTML.'</div>';

            if($rightCol > 0 && $rightColXL > 0 && $rightColXXL > 0)
                $bootstrapRowString .= '<div class="col-'.$rightCol.' col-xl-'.$rightColXL.' col-xxl-'.$rightColXXL.'"></div>';

            $bootstrapRowString .= '</div>';

            // Returns the pre-generated row HTML code.
            return $bootstrapRowString;
        }

        /**
         * Is the same function as preGenRow(..)
         * but you do NOT need to specify the Bootstrap column parameters anymore.
         * Instead,
         * the default parameters are used,
         * defined in "config.php"
         * (FEED_COL_WIDTH, FEED_COL_XL_WIDTH, FEED_COL_XXL_WIDTH, FEED_COL_SIDE, FEED_COL_XL_SIDE and FEED_COL_XXL_SIDE).
         *
         * @param string $rowContentHTML Simply the HTML code for your Bootstrap row.
         * @param string $applyTopMargin When 'true', a top-margin of 5 Bootstrap units is applied for the generated row. Otherwise not if 'false'.
         * @return string Row HTML as described.
         */
        public function preGenDefaultRow($rowContentHTML, $applyTopMargin) : string
        {
            return $this -> preGenRow($rowContentHTML, $applyTopMargin, FEED_COL_SIDE, FEED_COL_XL_SIDE, FEED_COL_XXL_SIDE, FEED_COL_WIDTH, FEED_COL_XL_WIDTH, FEED_COL_XXL_WIDTH, FEED_COL_SIDE, FEED_COL_XL_SIDE, FEED_COL_XXL_SIDE);
        }

        /**
         * Generates a pre-defined Bootstrap feed / post in HTML,
         * which has an optional image you can display (150px by 150px) and an HTML description
         * (any HTML of your choice theoretically but be careful what you want to embed as HTML code).
         * IMPORTANT: The existence of the given image URL is not checked!
         * This function will DIRECTLY output the feed HTML on your webpage (by calling this function).
         * Only make sure you place the function inside a Bootstrap container like this:
         *
         * <div class="container-fluid"><?php echoFeed(..); ?></div>
         *
         * @param string $pictureURL Simply the URL to your picture which should be displayed in the post. Otherwise pass NULL here (then no picture generated / embedded).
         * @param string $descriptionHTML The HTML code you want to show in your feed (usually formatted text, a button etc.).
         */
        public function echoFeed($pictureURL, $descriptionHTML) : void
        {
            $html = '';

            if(is_string($pictureURL))
            {
                $html = '<div class="card container border-1 border-primary bg-primary-subtle">';
                $html .= '<div class="row pt-3 pb-3">';

                $html .= '<div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3 col-xxl-4 align-self-center">';
                $html .= '<div class="border border-primary rounded bg-light" style="display: flex; justify-content: center; align-items: center; text-align: center; min-width: 120px; width: 120px; min-height: 120px; max-width: 120px; height: 120px;">';
                $html .= '<img class="img-fluid" style="max-width: 118px; max-height: 118px;" src="'.$pictureURL.'" alt="'.$pictureURL.'">';
                $html .= '</div>';
                $html .= '</div>';

                $html .= '<div class="col-6 col-sm-8 col-md-9 col-lg-9 col-xl-9 col-xxl-8 align-self-center"><div>'.$descriptionHTML.'</div></div>';

                $html .= '</div>';
                $html .= '</div>';
            }
            else
            {
                $html = '<div class="card container border-0 bg-primary-subtle">';

                $html .= '<div class="col-md">';
                $html .= '<div class="card-body">'.$descriptionHTML.'</div>';
                $html .= '</div>';

                $html .= '</div>';
            }

            echo $this -> preGenDefaultRow($html, true);
        }

        /**
         * Pre-generates a pre-defined Bootstrap card in HTML.
         * So, this function will ONLY produce the card HTML but it will NOT output it.
         *
         * @param string $title Simply the title of your Bootstrap card.
         * @param string $contentText Simply the content text of your Bootstrap card (followed after title). Can be HTML also of course.
         * @return string HTML of your Bootstrap card.
         */
        public function preGenCard($title, $contentText) : string
        {
            return "<div class=\"card w-100\" style=\"width: 18rem;\"><div class=\"card-body\"><p class=\"fs-4\">$title</p><p class=\"fs-6\">$contentText</p></div></div>";
        }

        /**
         * Note: ignore this function. Do not use it. It is yet used by genSlides() as a sub-routine only.
         *       So for building Bootstrap webpages this is actually useless.
         *
         * Pre-generates a single slide for Bootstrap with an image file specified (carousel-item in HTML).
         * The complete Bootstrap HTML for the slide gallery is NOT generated by this function alone but by the calling function genSlides().
         * If an error occurs during function (returned no string value but: 'false' = error occured - file does not exist etc.),
         * it will also be logged into the file,
         * specified by ERROR_LOG_FILEPATH in "config.php".
         * If function returns 'true',
         * this is no error but file was simply ignored - so don't worry.
         * Also,
         * if you specify a file like 'pngFile.dat' but it is actually a PNG file (judging by MIME analsysis),
         * then this function will not fail but it is bad practice,
         * so avoid such files.
         *
         * @param string $relativeDirectoryPath The relative path to your folder with the slide images inside (so only something like 'my/images/folder' - so without 'C:\' or 'http://').
         * @param string $fileName Simply the picture's file name (like 'myNice.jpeg').
         * @param int $breakDuration How long wait in ms until trigger ride animation? Only generated if $breakDuration is > 0.
         * @param string $divBootstrapClasses Relates to the background of the images displayed in your carousel, more specific: the div working behind. Here, you can apply e.g. bg-dark etc.
         * @param string $imgStyle Relates to the direct image displayed in your carousel (note: object-fit is cover and so on!). You can specifiy CSS stylesheet here. E.g. you can apply 'height: 512px;' etc.
         * @return string|bool Returns the pre-generated HTML code or 'true' (given $fileName was ignored because: parent directory '..' or current directory '.' specified as value, or too because the file is not an actual image file like JPEG etc. - so file ignored) or returns 'false' (because at least one parameter has an invalid value / datatype, not specified a valid existing file, or rarely too if the file type could not be analyzed - like JPEG).
         */
        public function preGenSlide($relativeDirectoryPath, $fileName, $isFirstPicture, $breakDuration, $divBootstrapClasses, $imgStyle) : string|bool
        {
            if(!is_string($fileName) || !is_string($relativeDirectoryPath) || !is_bool($isFirstPicture))
            {
                logFile("preGenSlide(..): unexpected, invalid type of parameter.");

                return false;
            }

            // Abort without error log,
            // as these dots are always contained when indexing a directory.
            if(strcmp($fileName, '.') == 0 || strcmp($fileName, '..') == 0)
            {
                return true; // So return success
            }

            // Construct absolute file path to check file existence.
            $filePath = $_SERVER['DOCUMENT_ROOT']."/$relativeDirectoryPath/$fileName";

            // Used for the image source specification later.
            $filePathURL = DOMAIN."/$relativeDirectoryPath/$fileName";

            // Abort again with error log because the given file path represents no file.
            if(!is_file($filePath))
            {
                logFile("preGenSlide(..): parameter \"$filePath\" does not represent an existing file.");

                return false;
            }

            $mimeFormat = mime_content_type($filePath);

            // Make sure the MIME format could be extracted to determine the real file type.
            // Note: even if you would rename a file like "myPng.png" to "myPng.dat",
            // it will be interpreted as a PNG file because MIME is always looking into the file,
            // not only on the file extension!
            if(!is_string($mimeFormat))
            {
                logFile("preGenSlide(..): Couldn't determine if \"$filePath\" is an image.");

                return false;
            }

            // Images usually begin with "image/".
            if(!str_starts_with($mimeFormat, 'image/'))
            {
                // Simply ignore the current non-image file and return success (so 'true' and no error log is needed).
                return true;
            }

            // Now safe to generate and return the slide with provided image file path.
            $slideHTML = '<div '.($breakDuration > 0 ? "data-bs-interval=\"$breakDuration\" " : '').'class="carousel-item '.$divBootstrapClasses.($isFirstPicture ? ' active' : '').'">';
            $slideHTML .= '<img class="d-block" style="width: 100%; object-position: center; object-fit: contain; overflow: hidden; '.$imgStyle.'" src="'.$filePathURL.'" alt="'.$filePathURL.'">';
            $slideHTML .= '</div>';

            return $slideHTML;
        }

        /**
         * Generates a Bootstrap image gallery in HTML (aka 'carousel slide'),
         * which has arrow buttons to go to the next or previous picture.
         * The gallery will be returned as a Bootstrap row,
         * essentials to print.
         * Place it correctly inside the following kind of div
         * (only for example, but needs to be called inside a Bootstrap container div):
         *
         * <div class="container-fluid"><?php echo genSlides(..); ?></div>
         *
         * Also,
         * indicators are being generated which show which picture is currently selected (kinda highlighted).
         * Indicators and arrow buttons also depend on the amount of pictures contained in the gallery.
         * If there is e.g. only 1 picture,
         * then no arrow buttons or indicators are being shown.
         * At least two pictures are necessary for that.
         * Additionally,
         * this generator function will always look into the folder
         * 'img/slide' for valid pictures ( where your website is placed and configured, so $_SERVER['DOCUMENT_ROOT'] ).
         * You can simply put pictures in there (name irrelevant) like JPEG, PNG, GIF etc.
         * Images are being determined by the MIME type,
         * not by the file extension.
         * So if you have pictures named like 'abc.dat',
         * they will be recognized anyway (e.g. you rename 'abc.jpg' to 'abc.dat').
         * Anyway,
         * this is generally bad practice and should be avoided,
         * although this won't break this function.
         * One more thing is that you decide whether you want to show the indicators and next / previous buttons
         * by setting the flag $showElements to 'true'.
         *
         * @param string $relativeDirectoryPath The relative path to all the pictures contained (relative to $_SERVER['DOCUMENT_ROOT'], e.g. "img/slide" simply)
         * @param bool $showElements When 'true', then the BS carousel will dash indicators and arrow buttons to go to the next or previous picture. Otherwise not when 'false'.
         * @param bool $isAutoplayOn When 'true', then the BS carousel will automatically load the next image of the gallery with a "ride animation". Otherwise not when 'false'.
         * @param int $breakDuration How long wait in ms until trigger ride animation? A good value might be 3000 ms. Only matters when $isAutoplayOn was set to 'true'. Otherwise specify any integer value (will be ignored).
         * @param string $htmlID A must parameter! Specify a unique HTML ID for the generated Bootstrap gallery (anyway, does not give you the opportunity to generate multiple galleries - generation still only once supported!).
         * @param bool $applyTopMargin Applies top margin, as described by function preGenDefaultRow(..).
         * @param string $divBootstrapClasses Relates to the background of the images displayed in your carousel, more specific: the div working behind. Here, you can apply e.g. bg-dark etc.
         * @param string $imgStyle Relates to the direct image displayed in your carousel (note: object-fit is cover and so on!). You can specifiy CSS stylesheet here. E.g. you can apply 'height: 512px;' etc.
         * @param int $leftCol
         * @param int $leftColXL
         * @param int $leftColXXL
         * @param int $middleCol
         * @param int $middleColXL
         * @param int $middleColXXL
         * @param int $rightCol
         * @param int $rightColXL
         * @param int $rightColXXL
         * @return string Bootstrap carousel HTML string, essentials to print.
         */
        public function genSlides($relativeDirectoryPath, $showElements, $isAutoplayOn, $breakDuration, $htmlID, $applyTopMargin, $divBootstrapClasses, $imgStyle, $leftCol, $leftColXL, $leftColXXL, $middleCol, $middleColXL, $middleColXXL, $rightCol, $rightColXL, $rightColXXL) : string
        {
            if(!is_bool($showElements) || !is_bool($isAutoplayOn) || !is_int($breakDuration))
            {
                logFile("genSlides(..): one parameter is not of a valid datatype - aborted.");

                return '';
            }

            // Make sure, the break duration will not be generated
            // in the sub-routine function preGenSlide(..) if no carousel ride wanted.
            if(!$isAutoplayOn)
            {
                $breakDuration = 0;
            }

            // $relativeDirectoryPath = 'img/slide';
            $slideDirectoryPath = $_SERVER['DOCUMENT_ROOT']."/$relativeDirectoryPath";

            if(!is_dir($slideDirectoryPath))
            {
                logFile("genSlides(..): \"$slideDirectoryPath\" is not a folder or does not exist [0].");

                return '';
            }

            $slideDirContent = scandir($slideDirectoryPath);

            if(!is_array($slideDirContent))
            {
                logFile("genSlides(..): \"$slideDirectoryPath\" is not a folder [1].");

                return '';
            }

            // A logical switch to remember when to use the property 'active' for the generated Bootstrap gallery,
            // usually only once in the beginning.
            $isFirstPicture = true;

            // Represents the completely pre-generated Bootstrap gallery HTML (including all slides).
            $preGeneratedBootstrapGalleryHTML = NULL;

            // Remembers the amount of pre-generated Bootstrap slides - in order to easily pre-generate
            // the same amount of Bootstrap 'dashed indicators' after that.
            // In different words: this is (also) the amount of detected valid images for the slide animation.
            $amountPreGeneratedSlides = 0;

            // Represents the completely pre-generated Bootstrap HTML for the whole HTML gallery (represented by given ID $htmlID),
            // belonging to the slide gallery.
            $preGeneratedBootstrapArrowIndicatorsHTML = NULL;
            foreach($slideDirContent as $fileName)
            {
                $preGeneratedBootstrapSlideHTML = $this -> preGenSlide($relativeDirectoryPath, $fileName, $isFirstPicture, $breakDuration, $divBootstrapClasses, $imgStyle);

                // See if something was pre-generated.
                if(is_string($preGeneratedBootstrapSlideHTML))
                {
                    // Construct the slides.
                    $preGeneratedBootstrapGalleryHTML .= $preGeneratedBootstrapSlideHTML;

                    if($showElements)
                    {
                        $preGeneratedBootstrapArrowIndicatorsHTML .= "<button type=\"button\" style=\"filter: invert(100%)\" data-bs-target=\"#$htmlID\" data-bs-slide-to=\"$amountPreGeneratedSlides\"";

                        if($isFirstPicture)
                        {
                            $preGeneratedBootstrapArrowIndicatorsHTML .= ' class="active"></button>';
                        }
                        else
                        {
                            $preGeneratedBootstrapArrowIndicatorsHTML .= '></button>';
                        }
                    }

                    if($isFirstPicture)
                        $isFirstPicture = false;

                    $amountPreGeneratedSlides++;
                }
            }

            // Make sure at least one image has been detected.
            // Otherwise a generation of the Bootstrap gallery makes no sense of course.
            // So check this and abort execution otherwise.
            if($amountPreGeneratedSlides <= 0)
            {
                return '';
            }

            $galleryHTML = '<div id="'.$htmlID.'" class="carousel slide"'.($isAutoplayOn ? ' data-bs-ride="carousel" data-bs-pause="false"' : '').'>';

            // Only show the dashed indicators to navigate to any picture in the gallery,
            // if there are at least 2 pictures in the slide folder on webserver
            // (which makes sense ofc).
            if($amountPreGeneratedSlides > 1 && $showElements)
                $galleryHTML .= '<div class="carousel-indicators z-1">'.$preGeneratedBootstrapArrowIndicatorsHTML.'</div>';

            // Always show pictures (in this it was yet checked if there is at least one photo).
            $galleryHTML .= '<div class="carousel-inner">'.$preGeneratedBootstrapGalleryHTML.'</div>';

            // And only show the arrow buttons if there are at least two pictures AND if autoplay is turned off.
            if($amountPreGeneratedSlides > 1 && $showElements && !$isAutoplayOn)
                $galleryHTML .= '<button class="carousel-control-prev" style="filter: invert(100%)" type="button" data-bs-target="#'.$htmlID.'" data-bs-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Previous</span></button><button class="carousel-control-next" style="filter: invert(100%)" type="button" data-bs-target="#'.$htmlID.'" data-bs-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Next</span></button>';

            $galleryHTML .= '</div>';

            return $this -> preGenRow($galleryHTML, $applyTopMargin, $leftCol, $leftColXL, $leftColXXL, $middleCol, $middleColXL, $middleColXXL, $rightCol, $rightColXL, $rightColXXL);
        }

        public function genCarouselForBeginning() : string
        {
            return $this -> genSlides('img/slide', false, true, 3000, 'carousel0', true, '', '', FEED_COL_SIDE, FEED_COL_XL_SIDE, FEED_COL_XXL_SIDE, FEED_COL_WIDTH, FEED_COL_XL_WIDTH, FEED_COL_XXL_WIDTH, FEED_COL_SIDE, FEED_COL_XL_SIDE, FEED_COL_XXL_SIDE);
        }

        public function genCarouselForArticle($picturesRelativePath) : string
        {
            //genSlides($relativeDirectoryPath, $showElements, $isAutoplayOn, $breakDuration, $htmlID, $applyTopMargin, $divBootstrapClasses, $imgStyle, $leftCol, $leftColXL, $leftColXXL, $middleCol, $middleColXL, $middleColXXL, $rightCol, $rightColXL, $rightColXXL)
            return $this -> genSlides($picturesRelativePath, true, false, 3000, 'photoGallery', false, 'bg-light pt-5 pb-5 border border-light', 'width: 100%; height: 30vh; max-height: 400px;', 0, 0, 0, 12, 12, 12, 0, 0, 0);
        }
    }
?>