<?php

require_once "../../lib/start.php";

$param = $_REQUEST['term'];

$sel_users = "SELECT uid, cognome, nome, '' AS other FROM rb_utenti WHERE cognome LIKE '{$param}%' UNION SELECT id_alunno AS uid, cognome, nome, CONCAT(rb_classi.anno_corso, rb_classi.sezione) AS other FROM rb_alunni, rb_classi WHERE rb_classi.id_classe = rb_alunni.id_classe AND cognome LIKE '{$param}%' AND attivo = '1' ORDER BY cognome, nome";
//$sel_users = "SELECT uid, cognome, nome, '' AS other FROM rb_utenti WHERE cognome LIKE '%{$param}%' ORDER BY cognome, nome";
$res_users = $db->execute($sel_users);
$users = array();
while ($us = $res_users->fetch_assoc()){
	$name = $us['cognome']." ".$us['nome'];
	$type = "school";
	if ($us['other'] != ""){
		$name .= " ({$us['other']})";
		$type = "student";
	}
	if ($us['other'] == "") {
		$sel_children = "SELECT cognome, nome FROM rb_alunni, rb_genitori_figli WHERE rb_genitori_figli.id_alunno = rb_alunni.id_alunno AND id_genitore = {$us['uid']} ORDER BY cognome, nome";
		$res_children = $db->executeQuery($sel_children);
		$children = array();
		if ($res_children->num_rows > 0){
			while ($row = $res_children->fetch_assoc()){
				$children[] = $row['cognome']." ".$row['nome'];
			}
		}
		if (count($children) > 0){
			$name .= " (".implode(", ", $children).")";
			$type = "parent";
		}
	}
	$uniqID = $db->executeCount("SELECT id FROM rb_com_users WHERE uid = {$us['uid']} AND type = '{$type}'");
	$users[] = array('uniqID' => $uniqID, "value" => $name, "label" => $name, "type" => $type);
}

$json_users = json_encode($users);
header("Content-type: application/json");
echo $json_users;
exit;
