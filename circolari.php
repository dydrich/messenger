<?php

require_once "../../lib/start.php";

check_session();
check_permission(DIR_PERM|DSG_PERM|SEG_PERM);

$drawer_label = "Gestione circolari";

$query = "SELECT rb_com_circolari.*, nome, cognome FROM rb_com_circolari, rb_utenti WHERE owner = uid AND anno = ".$_SESSION['__current_year__']->get_ID()." ORDER BY anno DESC, progressivo DESC, data_circolare DESC";

try{
	$result = $db->executeQuery($query);
} catch (MySQLException $ex){
	$ex->redirect();
}
$count = $result->num_rows;
$_SESSION['count_c'] = $count;

// dati per la paginazione (navigate.php)
$link = basename($_SERVER['PHP_SELF']);
$count_name = "count_c";

include 'circolari.html.php';
