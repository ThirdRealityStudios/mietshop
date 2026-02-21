<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/authorize/index.php';

    require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/tools/database/manager/system/parser/index.php';

    $parser = new Parser();

    $parser -> parse();

    header("Location: ".DOMAIN."/home/page/data+structure/table/?action=success");
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
		<span>If you can read this, there must have been an internal error. Please go back and try again later. In case of further issues contact the according administration.</span>
	</body>
</html>
