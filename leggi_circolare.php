<?php

require_once "../../lib/start.php";

check_session();
check_permission(DIR_PERM|DSG_PERM|SEG_PERM|DOC_PERM);

$sel_circ = "SELECT * FROM rb_com_circolari WHERE id_circolare = {$_REQUEST['idc']}";
$res_circ = $db->execute($sel_circ);
$circ = $res_circ->fetch_assoc();
$sel_allegati = "SELECT id, file FROM rb_com_allegati_circolari WHERE id_circolare = ".$_REQUEST['idc'];
$res_allegati = $db->executeQuery($sel_allegati);
if ($res_allegati->num_rows > 0){
	$row = $res_allegati->fetch_assoc();
	$circ['link'] = "download/allegati/{$row['file']}";
	$circ['id_allegato'] = $row['id'];
	$circ['allegato'] = $row['file'];
}

if (isset($_REQUEST['read']) && $_REQUEST['read'] == 1){
	//echo "INSERT INTO rb_com_lettura_circolari VALUES ({$circ['id_circolare']}, {$_SESSION['__user__']->getUId()}, 1, NOW())";
	try{
		$db->executeUpdate("INSERT INTO rb_com_lettura_circolari VALUES ({$circ['id_circolare']}, {$_SESSION['__user__']->getUId()}, 1, NOW())");
	} catch (MySQLException $ex){
		$ex->redirect();
	}
}

$drawer_label = "Circolare n. ". $circ['progressivo'] ." del ". format_date($circ['data_circolare'], SQL_DATE_STYLE, IT_DATE_STYLE, "/") ;

include "leggi_circolare.html.php";
