<?php

require_once "../../lib/start.php";

check_session();
check_permission(DIR_PERM);

$drawer_label = "Gestione avviso";

$action = 1;
$idnotice = 0;
if($_REQUEST['idn'] != 0){
	$action = 3;
	$sel_notice = "SELECT * FROM rb_com_avvisi WHERE id = ".$_REQUEST['idn'];
	try{
		$res_notice = $db->executeQuery($sel_notice);
	} catch (MySQLException $ex){
		print "Impossibile recuperare l'avviso: ".$ex->getMessage();
		exit;
	}
	$notice = $res_notice->fetch_assoc();
	$idnotice = $_REQUEST['idn'];
}

include "notice.html.php";
