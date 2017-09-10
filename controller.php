<?php

require_once "lib/Thread.php";
require_once "lib/Message.php";
require_once "../../lib/start.php";
require_once "../../lib/RBUtilities.php";

check_session();

$action = $_REQUEST['action'];

switch ($action){
	case "send":
		$sender = $_SESSION['__user__'];
		$target = $_POST['targetID'];
		$text = $db->real_escape_string($_POST['txt']);
		$th = null;
		$rb = RBUtilities::getInstance($db);
		if ($_REQUEST['tid'] == 0){
			$th = new Thread(0, new MySQLDataLoader($db), date("Y-m-d H:i:s"), "", Thread::CONVERSATION, array($sender->getUniqID(), $target));
			$th->setOwner($sender);
			$th->save();
			$_SESSION['threads'][$th->getTid()] = $th;
		}
		else {
			$th = $_SESSION['thread'];
			$th->restoreThread(new MySQLDataLoader($db));
			if (!$th->isActive()) {
				$response = ['status' => 'inactive'];
				echo json_encode($response);
				exit;
			}
		}
		$msg = new Message(0, $th->getTid(), $sender, $th->getTid(), new MySQLDataLoader($db), null);
		$msg->setText($text);
		$msg->send();
		if($th->getType() != Thread::CONVERSATION) {
			$msg->read();
		}
		$th->addMessage($msg);
		$_SESSION['threads'][$th->getTid()] = $th;
		krsort($_SESSION['threads']);

		list($date, $time) = explode(" ", $msg->getSendTimestamp());
		if ($_REQUEST['tid'] == 0){
			if (date("Y-m-d") == $date){
				$date = "Oggi alle";
			}
			else {
				$date = format_date($date, SQL_DATE_STYLE, IT_DATE_STYLE, "/")." alle ";
			}
		}
		else {
			if (date("Y-m-d") == $date){
				$date = "Inviato oggi alle";
			}
			else {
				$date = "Inviato il ".format_date($date, SQL_DATE_STYLE, IT_DATE_STYLE, "/")." alle ";
			}
		}
		$date .= " ".substr($time, 0, 5);
		$_SESSION['thread'] = $th;

		//header("Content-type: text/plain");
		//echo "ok|".$th->getTid()."|".$th->isRead($sender)."|".$target_user->getFullName(1, 1)."|".$th->getMessagesCount()."|".$date."|".$text."|".$msg->getID();

		header("Content-type: application/json");
		$t = $th->getOtherUser($sender->getUniqID());
		$target_name = "";
		if ($t instanceof Thread) {
			$target_name = $t->getName();
		}
		else {
			$target_name = $t->getFullName();
		}
		$response = array("status" => "ok", "message" => "", "thread" => $th->getTid(), "sender" => "Tu", "target" => $target_name, "count" => $th->getMessagesCount(), "date" => "{$date}", "text" => "{$msg->getText()}", "mid" => $msg->getID());
		$response['t_t'] = $th->getType();
		echo json_encode($response);
		exit;
		break;
	case "list_threads":
		unset($_SESSION['thread']);
		header("Location: threads.php");
		break;	
	case "show_thread":
		$tid = $_REQUEST['tid'];
		$thread = $_SESSION['threads'][$tid];
		$_SESSION['thread'] = $thread;
		header("Location: thread.php");
		break;
	case "leave":
		$tid = $_REQUEST['tid'];
		$us = $_SESSION['__user__']->getUniqID();
		$thread = $_SESSION['threads'][$tid];
		$thread->restoreThread(new MySQLDataLoader($db));
		$thread->deleteUser($us);
		unset($_SESSION['threads'][$tid]);
		header("Content-type: application/json");
		$response = ["status" => 'ok'];
		echo json_encode($response);
		exit;
		break;
	case "report":
		$mid = $_REQUEST['mid'];
		$state = $_REQUEST['state'];
		$tid = $_REQUEST['tid'];
		$us = $_SESSION['__user__']->getUniqID();
		$thread = $_SESSION['threads'][$tid];
		$thread->restoreThread(new MySQLDataLoader($db));
		$thread->reportMessage($mid, $state);
		$db->executeUpdate("INSERT INTO rb_log (utente, tipo_evento, numeric1, numeric2) VALUES ({$us}, 6, {$mid}, {$state})");
		header("Content-type: application/json");
		$response = ["status" => 'ok'];
		echo json_encode($response);
		exit;
		break;
	case "delete":
		$mid = $_REQUEST['mid'];
		$state = $_REQUEST['state'];
		$tid = $_REQUEST['tid'];
		$us = $_SESSION['__user__']->getUniqID();
		$thread = $_SESSION['threads'][$tid];
		$thread->restoreThread(new MySQLDataLoader($db));
		$thread->deleteMessage($mid, $state);
		$db->executeUpdate("INSERT INTO rb_log (utente, tipo_evento, numeric1, numeric2) VALUES ({$us}, 7, {$mid}, {$state})");
		header("Content-type: application/json");
		$response = ["status" => 'ok', "message" => 'Messaggio cancellato'];
		echo json_encode($response);
		exit;
		break;
	case "restore":
		$mid = $_REQUEST['mid'];
		$tid = $_REQUEST['tid'];
		$us = $_SESSION['__user__']->getUniqID();
		$thread = $_SESSION['threads'][$tid];
		$thread->restoreThread(new MySQLDataLoader($db));
		$txt = $thread->restoreMessage($mid);
		$db->executeUpdate("INSERT INTO rb_log (utente, tipo_evento, numeric1, numeric2) VALUES ({$us}, 7, {$mid}, 1)");
		header("Content-type: application/json");
		$response = ["status" => 'ok', "text" => $txt, "message" => 'Messaggio approvato'];
		echo json_encode($response);
		exit;
		break;
}
