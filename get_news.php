<?php

require_once "../../lib/start.php";

check_session();

header("Content-type: application/json");

$sel_news = "SELECT * FROM rb_com_news WHERE id_news = ".$_POST['id'];
try{
	$res_news = $db->executeQuery($sel_news);
} catch (MySQLException $ex){
	$response = array("status" => "koslq", "msg" => $ex->getMessage(), "query" => $ex->getQuery());
	echo json_encode($response);
	exit;
}
$news = $res_news->fetch_assoc();

$response = array("status" => "ok", "message" => $news['testo'], "abstract" => $news['abstract']);
echo json_encode($response);
