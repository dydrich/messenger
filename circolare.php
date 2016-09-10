<?php

require_once "../../lib/start.php";

check_session();
check_permission(DIR_PERM|DSG_PERM|SEG_PERM);

$drawer_label = "Dettaglio circolare";

$action = "new";
$idc = 0;
if($_REQUEST['idc'] != 0){
	$action = "update";
	$sel_circ = "SELECT * FROM rb_com_circolari WHERE id_circolare = ".$_REQUEST['idc'];
	$sel_allegati = "SELECT file FROM rb_com_allegati_circolari WHERE id_circolare = ".$_REQUEST['idc'];
	try{
		$res_circ = $db->executeQuery($sel_circ);
		$allegato = $db->executeCount($sel_allegati);
	} catch (MySQLException $ex){
		print "Impossibile recuperare la circolare: ".$ex->getMessage();
		exit;
	}
	$circ = $res_circ->fetch_assoc();
	$idc = $_REQUEST['idc'];
}

include "circolare.html.php";
