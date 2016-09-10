<?php

require_once "../../lib/start.php";

check_session();

$drawer_label = "File privati";

$sel_received_files = "SELECT rb_com_files.*, CONCAT_WS(' ', cognome, nome) AS nome FROM rb_com_files, rb_utenti WHERE rb_com_files.mittente = rb_utenti.uid AND rb_com_files.destinatario = {$_SESSION['__user__']->getUid()} AND data_download IS NULL ORDER BY data_invio DESC ";
$sel_sent_files = "SELECT rb_com_files.*, CONCAT_WS(' ', cognome, nome) AS nome FROM rb_com_files, rb_utenti WHERE rb_com_files.destinatario = rb_utenti.uid AND rb_com_files.mittente = {$_SESSION['__user__']->getUid()} AND data_download IS NULL ORDER BY data_invio DESC ";

try{
	$res_received = $db->executeQuery($sel_received_files);
	$res_sent = $db->executeQuery($sel_sent_files);
} catch (MySQLException $ex){
	$ex->redirect();
}

include "files.html.php";
