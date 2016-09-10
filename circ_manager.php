<?php

require_once "../../lib/start.php";

check_session();
check_permission(ADM_PERM|DIR_PERM|DSG_PERM|SEG_PERM);

function insert($request, $db){
	$num_c = $request['num_c'];
	$protocollo = $request['prot'];
	$object = $db->real_escape_string($request['obj']);
	$dest = $db->real_escape_string($request['dest']);
	$data_circolare = format_date($request['data'], IT_DATE_STYLE, SQL_DATE_STYLE, "-");
	$text = $db->real_escape_string($request['txt']);
	$anno = $_SESSION['__current_year__']->get_ID();
	$owner = $_SESSION['__user__']->getUid();
	$file = $_POST['server_file'];
	$query = "INSERT INTO rb_com_circolari (anno, progressivo, protocollo, data_circolare, data_inserimento, owner, destinatari, oggetto, testo) VALUES ($anno, $num_c, '$protocollo', '$data_circolare', NOW(), $owner, '$dest', '$object', '$text')";
	try{
		$res = $db->executeUpdate($query);
		if ($file != ""){
			$db->executeUpdate("INSERT INTO rb_com_allegati_circolari (id_circolare, file) VALUES ({$res}, '{$file}')");
		}
	} catch (MySQLException $ex){
		$response = array("status" => "koslq", "msg" => $ex->getMessage(), "query" => $ex->getQuery());
		echo json_encode($response);
		exit;
	}
	return "Circolare inserita";
}

function update($request, $db){
	$num_c = $request['num_c'];
	$protocollo = $request['prot'];
	$object = $db->real_escape_string($request['obj']);
	$dest = $db->real_escape_string($request['dest']);
	$data_circolare = format_date($request['data'], IT_DATE_STYLE, SQL_DATE_STYLE, "-");
	//$text = text2html($request['txt']);
	//$text = text2html(htmlentities($request['txt']));
	$text = $db->real_escape_string($request['txt']);
	$anno = $_SESSION['__current_year__']->get_ID();
	$owner = $_SESSION['__user__']->getUid();
	$idc = $request['idc'];
	$query = "UPDATE rb_com_circolari SET anno = $anno, progressivo = $num_c, protocollo = '$protocollo', data_circolare = '$data_circolare', destinatari = '$dest', oggetto = '$object', testo = '$text' WHERE id_circolare = $idc";
	try{
		$res = $db->executeUpdate($query);
	} catch (MySQLException $ex){
		$response = array("status" => "koslq", "msg" => $ex->getMessage(), "query" => $ex->getQuery());
		echo json_encode($response);
		exit;
	}
	return "Circolare aggiornata";
}

function delete($request, $db){
	$idc = $request['idc'];
	//$del_letture = $db->executeUpdate("DELETE FROM rb_lettura_circolari WHERE id_circolare = $idc");
	$query = "DELETE FROM rb_com_circolari WHERE id_circolare = $idc";
	$del_files = "DELETE FROM rb_com_allegati_circolari WHERE id_circolare = $idc";
	try{
		$res = $db->executeUpdate($query);
		$db->executeUpdate($del_files);
	} catch (MySQLException $ex){
		$response = array("status" => "koslq", "msg" => $ex->getMessage(), "query" => $ex->getQuery());
		echo json_encode($response);
		exit;
	}
	return "Circolare eliminata";
}

$action = $_REQUEST['action'];
header("Content-type: text/plain");
switch($action){
	case "new":
		$msg = insert($_REQUEST, $db);
		break;
	case "update":
		$msg = update($_REQUEST, $db);
		break;
	case "delete":
		$msg = delete($_REQUEST, $db);
		break;
	default:
		usage();
		break;
}

$response = array("status" => "ok", "message" => $msg);
echo json_encode($response);