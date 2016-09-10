<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 11/08/14
 * Time: 10.09
 */
require_once "../../lib/start.php";

check_session();

$sel_threads = "SELECT * FROM rb_com_threads ORDER BY tid";
$res_threads = $db->executeQuery($sel_threads);
while ($row = $res_threads->fetch_assoc()) {
	$un1 = $db->executeCount("SELECT id FROM rb_com_users WHERE uid = ".$row['user1']." AND type = '{$row['user1_group']}'");
	$db->executeUpdate("INSERT INTO rb_com_utenti_chat (chat, utente) VALUES ({$row['tid']}, {$un1})");
	$db->executeUpdate("UPDATE rb_com_messages SET sender = {$un1} WHERE tid = {$row['tid']} AND sender = {$row['user1']}");
	$db->executeUpdate("UPDATE rb_com_messages SET target = {$un1} WHERE tid = {$row['tid']} AND target = {$row['user1']}");

	$un2 = $db->executeCount("SELECT id FROM rb_com_users WHERE uid = ".$row['user2']." AND type = '{$row['user2_group']}'");
	$db->executeUpdate("INSERT INTO rb_com_utenti_chat (chat, utente) VALUES ({$row['tid']}, {$un2})");
	$db->executeUpdate("UPDATE rb_com_messages SET sender = {$un2} WHERE tid = {$row['tid']} AND sender = {$row['user2']}");
	$db->executeUpdate("UPDATE rb_com_messages SET target = {$un2} WHERE tid = {$row['tid']} AND target = {$row['user2']}");

	echo "insert chat {$row['user1']}->{$row['user2']} ==== {$un1}->{$un2} </br>";
}
