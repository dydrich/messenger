<?php

require_once "lib/Thread.php";
require_once "../../lib/start.php";
require_once "../../lib/RBUtilities.php";

check_session();

$drawer_label = "Conversazioni in corso";

$user_type = $_SESSION['user_type'];
$uniqID = $_SESSION['__user__']->getUniqID();

$uid = $_SESSION['__user__']->getUid();

$sel_th = "SELECT rb_com_threads.* 
		FROM rb_com_threads, rb_com_utenti_thread 
		WHERE tid = thread 
		AND utente = {$uniqID} 
		ORDER BY last_message DESC";
$res_th = $db->execute($sel_th);
$rb = RBUtilities::getInstance($db);
if ($res_th->num_rows > 0){
	$threads = array();
	while ($th = $res_th->fetch_assoc()){
		if ($th['owner'] != "" && $th['owner'] != null) {
			$owner = $rb->loadUserFromUniqID($th['owner']);
			//$other_user = $u2;
		}
		else {
			$owner = "";
		}
		try {
			$thread = new Thread($th['tid'], new MySQLDataLoader($db), $th['creation']);
			$thread->setOwner($owner);
			$thread->setName($th['name']);
			$thread->setType($th['type']);
		} catch (MySQLException $ex) {
			echo $ex->getMessage()."<br>";
			echo __FILE__."<br>";
			echo $ex->getQuery()."<br>";
			exit;
		}
		$threads[$th['tid']] = $thread;
	}
	$_SESSION['threads'] = $threads;
}


$ordered_threads = array();
$times = [];
$last_tid = 0;
$last_msg = 0;
$x = 1;
foreach ($threads as $th){
	$deleted = $th->restoreThread(new MySQLDataLoader($db));
	if ($deleted[0] == -999) {
		unset($threads[$th->getTid()]);
		unset($_SESSION['threads'][$th->getTid()]);
		continue;
	}

	if (count($th->getMessages()) == 0) {
		$ordered_threads[$th->getCreationDate()."-".$th->getTid()] = $th;
	}
	else {
		$ordered_threads[$th->getLastMessage()->getSendTimestamp()] = $th;
		if ($th->getLastMessage()->getID() > $last_msg) {
			$last_msg = $th->getLastMessage()->getID();
		}
	}
	$x++;
	if ($th->getTid() > $last_tid) {
		$last_tid = $th->getTid();
	}
}
krsort($ordered_threads);
$th_ids = array_keys($_SESSION['threads']);

include 'threads.html.php';
