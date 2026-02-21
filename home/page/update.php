<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/authorize/index.php';

    $essentials = new Essentials();

	require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/php/tools/database/formUpdate/index.php';

    if(isset($_SESSION['ReturnPage']))
    {
        $update = new Update($essentials);

        $update -> update($_SESSION['ReturnPage']);
    }
?>

<!DOCTYPE html>
<html lang="de">
	<head>
		<?php
			// Load HTML tag information.
			require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/head/index.php';
		?>
	</head>
	<body>
		<span>If you can read this, there must have been an internal error. Please go back and try again later. In case of further issues contact the according the administration.</span>
	</body>
</html>
