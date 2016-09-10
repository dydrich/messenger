<?php

require_once "../../lib/start.php";

check_session();
check_permission(DIR_PERM);

$drawer_label = "Elenco avvisi";

$sel_notices = "SELECT * FROM rb_com_avvisi ORDER BY data_scadenza DESC ";

try{
	$res_notices = $db->executeQuery($sel_notices);
} catch (MySQLException $ex){
	$ex->redirect();
}
//print $sel_links;
$count = $res_notices->num_rows;
$_SESSION['count_notices'] = $count;

// dati per la paginazione (navigate.php)
$colspan = 3;
$link = basename($_SERVER['PHP_SELF']);
$count_name = "count_notices";

include "notices.html.php";
