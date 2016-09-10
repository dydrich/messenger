<?php

require_once "../../lib/start.php";

check_session(AJAX_CALL);
check_permission(DIR_PERM|DSG_PERM|SEG_PERM|APS_PERM|AIS_PERM|AMS_PERM);

if($_POST['action'] != 2){
	$titolo = $db->real_escape_string($_POST['titolo']);
	$testo = $db->real_escape_string(nl2br($_POST['testo']));
}

switch($_POST['action']){
	case 1:     // inserimento
		$statement = "INSERT INTO rb_com_news (data, abstract, testo, utente) VALUES (NOW(), '$titolo', '$testo', ".$_SESSION['__user__']->getUID().")";
		$msg = "News inserita correttamente";
		break;
	case 2:     // cancellazione
		$statement = "DELETE FROM rb_com_news WHERE id_news = ".$_REQUEST['_i'];
		$msg = "News cancellata";
		break;
	case 3:     // modifica
		$statement = "UPDATE rb_com_news set abstract = '$titolo', testo = '$testo' WHERE id_news = ".$_REQUEST['_i'];
		$msg = "News aggiornata correttamente";
		break;
}
header("Content-type: application/json");
try{
	$recordset = $db->executeUpdate($statement);
} catch (MySQLException $ex){
	$response = array("status" => "koslq", "msg" => $ex->getMessage(), "query" => $ex->getQuery());
	echo json_encode($response);
	exit;
}

$response = array("status" => "ok", "message" => $msg);
echo json_encode($response);
