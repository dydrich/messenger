<?php

require_once "../../lib/start.php";

check_session();
check_permission(DIR_PERM|DSG_PERM|SEG_PERM);

if (!isset($_REQUEST['offset'])) {
	$offset = 0;
}
else {
	$offset = $_REQUEST['offset'];
}

$limit = 15;

$sel_circ = "SELECT progressivo, data_circolare, protocollo FROM rb_com_circolari WHERE id_circolare = {$_REQUEST['idc']}";
$res_circ = $db->execute($sel_circ);
$circ = $res_circ->fetch_assoc();

$sel_read = "SELECT nome, cognome, letta, DATE_FORMAT(data_lettura, '%d/%m/%Y %H:%i') AS data_lettura FROM rb_docenti JOIN rb_utenti ON id_docente = uid LEFT JOIN rb_com_lettura_circolari ON id_docente = docente AND id_circolare = ".$_REQUEST['idc']." ORDER BY cognome, nome";
if(!isset($_GET['second'])){
	try{
		$res_read = $db->executeQuery($sel_read);
	} catch (MySQLException $ex){
		$ex->redirect();
	}
	//print $sel_links;
	$count = $res_read->num_rows;
	$_SESSION['count_read'] = $count;
}
else{
	$sel_read .= " LIMIT $limit OFFSET $offset";
	$res_read = $db->execute($sel_read);
}

if ($offset == 0) {
	$page = 1;
}
else {
	$page = ($offset / $limit) + 1;
}

$pagine = ceil($_SESSION['count_read'] / $limit);
if ($pagine < 1) {
	$pagine = 1;
}

// dati per la paginazione (navigate.php)
$colspan = 3;
$link = basename($_SERVER['PHP_SELF']);
$count_name = "count_read";
$row_class = "manager_row";
$row_class_menu = " manager_row_menu";
$nav_params = "&idc=".$_REQUEST['idc'];

$drawer_label = "Lettura circolare n. ".$circ['progressivo']." del ".format_date($circ['data_circolare'], SQL_DATE_STYLE, IT_DATE_STYLE, ".");

include "lettura_circolari.html.php";
