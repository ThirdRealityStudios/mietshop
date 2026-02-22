<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/authorize/index.php';

    $essentials = new Essentials();

	function genMenuButton($href, $title, $openNewPage) : string
	{
		if($openNewPage)
		{
			return '<a target="_blank" class="btn btn-primary" href="'.$href.'">'.$title.'</a>"';
		}
		else
		{
			return '<a class="btn btn-primary" href="'.$href.'">'.$title.'</a>';
		}
	}
	
	function genExpertModeButtons(Essentials $essentials, $getValue) : string
	{
		$expertModeButtonsHTMLContent = genMenuButton("?target=settings&$getValue", '⚙️ Einstellungen', false);
        $expertModeButtonsHTMLContent .= genMenuButton("?target=maintenance&$getValue", '🛠️ Wartung', false);
        $expertModeButtonsHTMLContent .= genMenuButton("?target=data%2bstructure&$getValue", '🏗️ Datenstruktur', false);
        $expertModeButtonsHTMLContent .= genMenuButton("?target=account&$getValue", '🔑 Konten', false);

		$homeActionManager = $essentials -> getDatabaseManager() -> getHomeActionManager();

		$tables = $homeActionManager -> getTableNames();

		if($tables != NULL)
		{
			$expertModeButtonsHTMLContent .= '<div class="menuDelimiter"></div>';

			foreach($tables as $table)
			{
				if($table == NULL)
					continue;

                $expertModeButtonsHTMLContent .= genMenuButton('?target='.$homeActionManager -> getTargetProperty($table)."&$getValue", $homeActionManager -> getTableAlias($table), false);
			}
		}
		
		return $expertModeButtonsHTMLContent;
	}
?>
<!DOCTYPE html>
<html lang="de">
	<head>
		<?php
			// Load head-tag information and its configuration.
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/head/index.php';
		?>
	</head>
	<body class="bg-body-secondary">
		<?php
			// Load HTML header.
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/header/index.php';
			
			echo '<div class="container-fluid">';
			
			/////////////////////////////////////////////////////////////////////////
			////////// Below generation of a hint, so user knows he is logged into his account with his username.

            $homeHintHTMLContent = '';

			if(isset($_SESSION['username']) && is_string($_SESSION['username']))
			{
				$homeHintHTMLContent = '<div class="row mb-3">';
				$homeHintHTMLContent .= '<div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">';
				$homeHintHTMLContent .= '<p class="fs-1 m-0 p-0 w-100 fw-bold text-center">Mein Kontobereich</p>';
				$homeHintHTMLContent .= '</div>';
				$homeHintHTMLContent .= '</div>';

				$homeHintHTMLContent .= '<div class="row mb-3">';
				$homeHintHTMLContent .= '<div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12 text-center">';
				$homeHintHTMLContent .= '<span class="fs-6 fw-bold">Eingeloggt als: </span><span>';

				$homeHintHTMLContent .= $_SESSION['username'].' ';
				
				if($essentials -> getDatabaseManager() -> getAccountManager() -> hasPermissionAdmin($_SESSION['username']))
				{
					$homeHintHTMLContent .= '<span class="text-danger">(Administrator-Rechte)</span>';
				}
				else
				{
					$homeHintHTMLContent .= '<span class="fs-6 text-secondary">(Inhaber-Rechte)</span>';
				}
				
				$homeHintHTMLContent .= '</span>';
				$homeHintHTMLContent .= '</div>';
				$homeHintHTMLContent .= '</div>';
			}

			$essentials -> getViewFactory() -> echoFeed(NULL, $homeHintHTMLContent);
			
			//////////////////////////////////////////////////////////////////////////
			///////// Below generation of all selectable buttons for the interface:

			$managementInterface = '<div class="row">';
			$managementInterface .= '<div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12"><p class="fs-2 text-center m-0">Meine Funktionen</p></div>';
			$managementInterface .= '<div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12"><hr class="border-2 border-primary w-100"></div>';
			
			$managementInterface .= '<div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">';
			$managementInterface .= '<div class="d-grid gap-2">';
			$managementInterface .= '<a class="btn btn-primary" href="'.DOMAIN.'/upload">📁 Inserieren</a>';
			$managementInterface .= '<a class="btn btn-primary" href="'.DOMAIN.'/logout">🔒 Abmelden</a>';
			$managementInterface .= '</div>';
			$managementInterface .= '</div>';
			
			$managementInterface .= '<div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12"><hr class="border-2 border-primary w-100"></div>';
			$managementInterface .= '<div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">';
			$managementInterface .= '<div class="d-grid gap-2">';
			
			// The title for the mode button,
			// which ofc depends on the mode you are in currently.
			// It can be "Normal Mode" or "Expert Mode".
			// By default, "Normal Mode" is assumed.
			// It will get adjusted below if necessary
			// (to match the current mode).
			$modeButtonTitle = '👨‍💻 Expertenmodus';
						
			// This variable remembers the current mode,
			// so it does not get lost or forgotten
			// if you click on a button.
			// Usually,
			// this variable simply gets set to the
			// value of the GET variable 'mode' sent.
			$getValue = 'mode=default';
						
			// GET value that is set for the mode button.
			// It depends on the current mode you are in
			// (whether expert or default mode).
			// It gives you the opportunity
			// switch to the other mode
			// (so it always has to be "opposite" value of the current mode).
			// The exact suitable value is determined below in code.
			$modeButtonGetValue = 'mode=expert';
						
			// Make sure if you chose the expert mode,
			// you will stay in there.
			// So it is necessary to adjust $getValue.
			// Also, it is necessary to make sure
			// the user can still switch to normal mode again.
			// Additionally, advanced buttons are generated for expert mode.
			if(isset($_GET['mode']) && strcmp($_GET['mode'], 'expert') == 0)
			{
				$modeButtonTitle = '👨‍💻 Normalmodus';
				$modeButtonGetValue = 'mode=default';

				$getValue = 'mode=expert';
			}

            $managementInterface .= genMenuButton(DOMAIN."/home/?$modeButtonGetValue", $modeButtonTitle, false);
			
			// If expert mode requested, show advanced buttons for management of database etc.
			if(isset($_GET['mode']) && strcmp($_GET['mode'], 'expert') == 0)
			{
				$managementInterface .= genExpertModeButtons($essentials, $getValue);
			}
			
			$managementInterface .= '</div>';
			$managementInterface .= '</div>';
			
			$managementInterface .= '</div>';
		
			$essentials -> getViewFactory() -> echoFeed(NULL, $managementInterface);
			
			////////////////////////////////////////////////////////////////////////////////////
			//////// Below is generation of iFrame content (or error message if loading fails)
			
			$managementViewHTMLContent = '';
			
			if(isset($_GET['target']))
			{
                $homeActionManager = $essentials -> getDatabaseManager() -> getHomeActionManager();
				
				if($homeActionManager !== NULL)
				{
					$managementViewHTMLContent .= '<div class="ratio ratio-1x1 bg-light rounded border border-2 border-primary">';
					$managementViewHTMLContent .= '<iframe src="';
					
					// Make sure no hacker can access any page but the given ones below.
					if(strcmp($_GET['target'], 'account') == 0)
					{
						$managementViewHTMLContent .= 'page/dbview?table=Account';
					}
					else if(strcmp($_GET['target'], 'data+structure') == 0)
					{
						$managementViewHTMLContent .= DOMAIN.'/home/page/data%2bstructure';
					}
					else if(strcmp($_GET['target'], 'maintenance') == 0)
					{
						$managementViewHTMLContent .= 'page/maintenance';
					}
					else if(strcmp($_GET['target'], 'settings') == 0)
					{
						$managementViewHTMLContent .= 'page/settings';
					}
					else if($homeActionManager -> isTargetOnMenu($_GET['target']))
					{
						$tableName = $homeActionManager -> getTableByTarget($_GET['target']);
						
						$managementViewHTMLContent .= 'page/dbview?table='.$tableName;
					}
					
					$managementViewHTMLContent .= '"></iframe>';
					$managementViewHTMLContent .= '</div>';
					
					echo $essentials -> getViewFactory() -> preGenDefaultRow($managementViewHTMLContent, false);
				}
				else
				{
					$managementViewHTMLContent .= '<span class="text-danger">Fehler: kann ein internes Modul nicht laden!</span>';
					
					$essentials -> getViewFactory() -> echoFeed(NULL, $managementViewHTMLContent);
				}
			}
			
			echo '</div>';
			
			// Load HTML footer information.
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/footer/index.php';
		?>
	</body>
</html>
