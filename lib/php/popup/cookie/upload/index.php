<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/config/index.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/php/tools/essentials/index.php';

    if(isset($_POST['save']) && is_numeric($_POST['save'])) // user wants to save cookie settings (so create necessary cookie(s) for wanted user-experience)
    {
        unset($_SESSION['isEditingCookieSettings']); // editing cookie settings done.

        switch($_POST['save'])
        {
            // User probably does not want all of the services activated (like Google Maps), so respect this.
            case 0:
                // Check if the user has not used or requested all checkboxes for the corresponding services.
                // Then prepare them (by default those checkboxes are assumed unchecked - which is also the legal perspective usually)
                if(!isset($_SESSION['enableGoogleMaps']))
                {
                    $_SESSION['enableGoogleMaps'] = 0;
                }

                if(!isset($_SESSION['enableKleinanzeigen']))
                {
                    $_SESSION['enableKleinanzeigen'] = 0;
                }

                if(!isset($_SESSION['enableInstagram']))
                {
                    $_SESSION['enableInstagram'] = 0;
                }

                if(!isset($_SESSION['enableWhatsApp']))
                {
                    $_SESSION['enableWhatsApp'] = 0;
                }

                if(!isset($_SESSION['enableFacebook']))
                {
                    $_SESSION['enableFacebook'] = 0;
                }

                if(is_numeric($_SESSION['enableGoogleMaps']) && is_numeric($_SESSION['enableKleinanzeigen']) && is_numeric($_SESSION['enableInstagram']) && is_numeric($_SESSION['enableWhatsApp']) && is_numeric($_SESSION['enableFacebook']))
                {
                    // Check if overall processed and saved user settings have valid decisions (always 0 - off, or 1 - on, for some service, like Google Maps).
                    if($_SESSION['enableGoogleMaps'] == 1 && $_SESSION['enableKleinanzeigen'] == 1 && $_SESSION['enableInstagram'] == 1 && $_SESSION['enableWhatsApp'] == 1 && $_SESSION['enableFacebook'] == 1)
                    {
                        setcookie("enableAll", 1, time()+3600, "/");
                    }
                    else if($_SESSION['enableGoogleMaps'] == 0 && $_SESSION['enableKleinanzeigen'] == 0 && $_SESSION['enableInstagram'] == 0 && $_SESSION['enableWhatsApp'] == 0 && $_SESSION['enableFacebook'] == 0)
                    {
                        setcookie("disableAll", 1, time()+3600, "/");
                    }
                    else // mixed settings met by the user for the desired services.
                    {
                        if($_SESSION['enableGoogleMaps'] == 1)
                        {
                            setcookie("enableGoogleMaps", 1, time()+3600, "/");
                        }

                        if($_SESSION['enableKleinanzeigen'] == 1)
                        {
                            setcookie("enableKleinanzeigen", 1, time()+3600, "/");
                        }

                        if($_SESSION['enableInstagram'] == 1)
                        {
                            setcookie("enableInstagram", 1, time()+3600, "/");
                        }

                        if($_SESSION['enableWhatsApp'] == 1)
                        {
                            setcookie("enableWhatsApp", 1, time()+3600, "/");
                        }

                        if($_SESSION['enableFacebook'] == 1)
                        {
                            setcookie("enableFacebook", 1, time()+3600, "/");
                        }
                    }
                }
            break;

            case 1: // easiest case here: user simply wants all services to be active.
                setcookie("enableAll", 1, time()+3600, "/");
            break;

            default: // A wrong value has been sent (tried manipulation from a user?)
                    // User will not be able to visit the page until he does not anymore try to manipulate it.
                    // So he will have to accept the cookie settings again on the website.
        }

        header('Location: '.DOMAIN);
		exit(0);
    }
    else if(isset($_POST['page']) && is_numeric($_POST['page']) && $_POST['page'] >= 0 && $_POST['page'] <= 6)
    {
        switch($_POST['page'])
        {
            // user wants to edit cookie settings (remember he / she is in edit mode - e.g. to improve user-experience and cookkie settings text)
            case 1:
                // If I have not yet edited settings, then prepare the cookie settings for changes.
                if(!isset($_SESSION['isEditingCookieSettings']))
                {
                    $_SESSION['isEditingCookieSettings'] = 1;
                }
            break;

            case 2:
                // Assumption: you come maybe from step 1 and go to step 2
                if(isset($_POST['enableGoogleMaps']) && is_string($_POST['enableGoogleMaps']) && strcmp($_POST['enableGoogleMaps'], 'on') === 0)
                {
                    $_SESSION['enableGoogleMaps'] = 1;
                }
                else
                {
                    $_SESSION['enableGoogleMaps'] = 0;
                }
            break;

            case 3:
                // Assumption: you come maybe from step 1 and go to step 2
                if(isset($_POST['enableKleinanzeigen']) && is_string($_POST['enableKleinanzeigen']) && strcmp($_POST['enableKleinanzeigen'], 'on') === 0)
                {
                    $_SESSION['enableKleinanzeigen'] = 1;
                }
                else
                {
                    $_SESSION['enableKleinanzeigen'] = 0;
                }
            break;

            case 4:
                // Assumption: you come maybe from step 1 and go to step 2
                if(isset($_POST['enableInstagram']) && is_string($_POST['enableInstagram']) && strcmp($_POST['enableInstagram'], 'on') === 0)
                {
                    $_SESSION['enableInstagram'] = 1;
                }
                else
                {
                    $_SESSION['enableInstagram'] = 0;
                }
            break;

            case 5:
                // Assumption: you come maybe from step 1 and go to step 2
                if(isset($_POST['enableWhatsApp']) && is_string($_POST['enableWhatsApp']) && strcmp($_POST['enableWhatsApp'], 'on') === 0)
                {
                    $_SESSION['enableWhatsApp'] = 1;
                }
                else
                {
                    $_SESSION['enableWhatsApp'] = 0;
                }
            break;

            case 6:
                // Assumption: you come maybe from step 1 and go to step 2
                if(isset($_POST['enableFacebook']) && is_string($_POST['enableFacebook']) && strcmp($_POST['enableFacebook'], 'on') === 0)
                {
                    $_SESSION['enableFacebook'] = 1;
                }
                else
                {
                    $_SESSION['enableFacebook'] = 0;
                }

                // page 6 does not exist but it means the user went through all cookie settings pages.
                // So redirect him to the first default page again.
                header('Location: '.DOMAIN.'/?page=0');
		        exit(0);
            break;

            default:
        }

        header('Location: '.DOMAIN.'/?page='.$_POST['page']);
		exit(0);
    }
?>