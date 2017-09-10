<?php

require_once "lib/Thread.php";
require_once "lib/Message.php";
require_once "../../lib/start.php";
require_once "../../lib/RBUtilities.php";

if ($_POST['upd'] == "msg"){
	$thread = $_SESSION['thread'];
	$thread->restoreThread(new MySQLDataLoader($db));
	$response = $thread->checkForUpdates();
	$thread->updateLastAccess($_SESSION['__user__']->getUniqID());
	$_SESSION['thread'] = $thread;
}
else if ($_POST['upd'] == "th") {
	/*
	 * threads update
	 * first step: new threads
	 */
	$last_msg = $_POST['lmsg'];
	$response = array();
	$sel_new_th = "SELECT rb_mess_threads.* FROM rb_mess_threads, rb_mess_utenti_thread WHERE tid = thread AND tid > {$_POST['tid']} AND utente = {$_SESSION['__user__']->getUniqID()} ORDER BY tid ASC";
	$res_th = $db->executeQuery($sel_new_th);
	if ($res_th->num_rows < 1){
	}
	else {
		$rb = RBUtilities::getInstance($db);
		$tids = array();
		$response = array();
		while ($row = $res_th->fetch_assoc()){
			$th = new Thread($row['tid'], new MySQLDataLoader($db), $row['creation']);
			$_SESSION['threads'][$row['tid']] = $th;
			$last = $th->getLastMessage();
			list($d, $t) = explode(" ", $last->getSendTimestamp());
			if (date("Y-m-d") == $d){
				$date = "Oggi alle";
			}
			else {
				$date = format_date($d, SQL_DATE_STYLE, IT_DATE_STYLE, "/");
			}
			$date .= " ".substr($t, 0, 5);
			//$last_msg = $last->getID();
			$tids[] = $row['tid'];
			$target_name = $th->getTargetName($_SESSION['__user__']->getUniqID());
			array_unshift($response, array("type" => "new", "tid" => $row['tid'], "mid" => $last->getID(),  "user" => $target_name, "count" => $th->getMessagesCount(), "datetime" => $date, "text" => $last->getText()));
		}
	}
	/*
	 * second step: new messages in existing threads 
	 */
	$ins = "";
	if (isset($tids) && count($tids) > 0){
		$ins = implode(",", $tids);
	}
	$sel_new_msgs = "SELECT rb_mess_messages.* FROM rb_mess_messages, rb_mess_threads, rb_mess_utenti_thread WHERE rb_mess_threads.tid = rb_mess_messages.tid AND rb_mess_threads.tid = thread AND utente = {$_SESSION['__user__']->getUniqID()} AND mid > {$last_msg}";
	//echo $sel_new_msgs;
	if ($ins != ""){
		$sel_new_msgs .= " AND rb_com_messages.tid NOT IN ({$ins})";
	}
	$res_new_msgs = $db->executeQuery($sel_new_msgs);
	if ($res_new_msgs->num_rows > 0){
		if ($response == ""){
			$response = array();
		}
		while ($row = $res_new_msgs->fetch_assoc()){
			$th = $_SESSION['threads'][$row['tid']];
			$th->restoreThread(new MySQLDataLoader($db));
			$rb = RBUtilities::getInstance($db);
			$user1 = $rb->loadUserFromUniqID($row['sender']);
			$msg = new Message($row['mid'], $th->getTid(), $user1, $th->getTid(), new MySQLDataLoader($db), $row);
			$msg->setText($row['text']);
			$th->addMessage($msg);
			$_SESSION['threads'][$row['tid']] = $th;
			
			list($d, $t) = explode(" ", $msg->getSendTimestamp());
			if (date("Y-m-d") == $d){
				$date = "Oggi alle";
			}
			else {
				$date = format_date($d, SQL_DATE_STYLE, IT_DATE_STYLE, "/");
			}
			$date .= " ".substr($t, 0, 5);
			$target_name = $th->getTargetName($_SESSION['__user__']->getUniqID());
			array_unshift($response, array("type" => "upd", "tid" => $row['tid'], "mid" => $msg->getID(), "thread_type" => $th->getType(), "sender" => $user1->getFullName(), "user" => $target_name, "count" => $th->getMessagesCount(), "datetime" => $date, "text" => $msg->getText()));
		}
	}
}

header("Content-type: application/json");
echo json_encode($response);
exit;
