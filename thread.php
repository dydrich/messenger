<?php

require_once "lib/Thread.php";
require_once "../../lib/start.php";
require_once "../../lib/RBUtilities.php";
require_once "../../lib/data_source.php";

check_session();

$uniqID = $_SESSION['__user__']->getUniqID();

$now = date("Y-m-d H:i:s");
$thread = $_SESSION['thread'];
$thread->restoreThread(new MySQLDataLoader($db));
$thread->updateLastAccess($_SESSION['__user__']->getUniqID());
$_SESSION['thread'] = $thread;
$_SESSION['threads'][$thread->getTid()] = $thread;

try{
	if ($thread->getType() == Thread::CONVERSATION) {
		$thread->readAll($_SESSION['__user__']);
	}
	$_SESSION['thread'] = $thread;
} catch (MySQLException $ex){
	echo $ex->getMessage();
}

$drawer_label = "Dettaglio conversazione";

include "thread.html.php";
