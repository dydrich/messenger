<?php

require_once "../../lib/start.php";

check_session(AJAX_CALL);
check_permission(DIR_PERM|DSG_PERM|SEG_PERM|APS_PERM|AIS_PERM|AMS_PERM);

if($_POST['action'] != 2){
	$data_evento = format_date($_POST['data_evento'], IT_DATE_STYLE, SQL_DATE_STYLE, "-");
	$data_evento .= " ".$_POST['ora_evento'];
	$titolo = $db->real_escape_string($_POST['titolo']);
	$testo = nl2br($db->real_escape_string($_POST['testo']));
	$id_padre = $_POST['evento_padre'];
	$ordine_di_scuola = $_POST['ordine_di_scuola'];
	if ($ordine_di_scuola == 0){
		$ordine_di_scuola = "";
	}
	$classe = $_POST['classe'];
	$modificabile = $pubblico = 0;
	$has_sons = 0;
	if(isset($_POST['pub']) && $_POST['pub'] == 'on')
		$pubblico = 1;
	if(isset($_POST['upr']) && $_POST['upr'] == 'on')
		$modificabile = 1;
}

switch($_POST['action']){
	case 1:     // inserimento
		$statement = "INSERT INTO rb_com_eventi (abstract, testo, owner, data_evento, data_modifica, data_inserimento, pubblico, modificabile, id_padre, classe, has_sons, ordine_di_scuola) VALUES ('$titolo', '$testo', ".$_SESSION['__user__']->getUID().", '$data_evento', NULL, CURRENT_TIMESTAMP, $pubblico, $modificabile, ".field_null($id_padre, false).", ".field_null($classe, true).", $has_sons, ".field_null($ordine_di_scuola, false).")";
		//print $statement;
		$msg = "Evento inserito correttamente";
		break;
	case 2:     // cancellazione
		$statement = "DELETE FROM rb_com_eventi WHERE id_evento = ".$_POST['_i'];
		//print $statement;
		$msg = "Evento cancellato correttamente";
		break;
	case 3:     // modifica
		$statement = "UPDATE rb_com_eventi SET abstract = '$titolo', testo = '$testo', data_evento = '$data_evento', data_modifica = CURRENT_TIMESTAMP, pubblico = $pubblico, modificabile = $modificabile, id_padre = ".field_null($id_padre, false).", classe = ".field_null($classe, true).", has_sons = $has_sons, ordine_di_scuola = ".field_null($ordine_di_scuola, false)." WHERE id_evento = ".$_POST['_i'];
		//print $statement;
		$msg = "Evento aggiornato correttamente";
		break;
}
header("Content-type: application/json");
try{
	$recordset = $db->executeUpdate($statement);
	/*
	 * gestione flag has_sons nell'evento padre
	*/
	if ($_REQUEST['action'] != 2) {
		if ($id_padre != "") {
			if ($_POST['action'] == 1 || $_POST['action'] == 3) {
				$upd = "UPDATE rb_com_eventi SET has_sons = 1 WHERE id_evento = " . $id_padre;
				$r_upd = $db->executeUpdate($upd);
			}
		}
	}
} catch (MySQLException $ex){
	$response = array("status" => "koslq", "msg" => $ex->getMessage(), "query" => $ex->getQuery());
	echo json_encode($response);
	exit;
}

$response = array("status" => "ok", "message" => $msg);
echo json_encode($response);
