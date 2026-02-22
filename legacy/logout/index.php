<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/php/tools/essentials/index.php';

session_destroy();

header("Location: ".DOMAIN."/login");
?>