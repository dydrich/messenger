<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 15/08/14
 * Time: 11.47
 */
require_once "lib/Thread.php";
require_once "../../lib/start.php";
require_once "../../lib/RBUtilities.php";

check_session();

$_SESSION['__path_to_root__'] = "../../";

$rb = RBUtilities::getInstance($db);

$is_system_group = false;

$uniqID = $_SESSION['__user__']->getUniqID();
if ($_REQUEST['tid'] != 0) {
	$thread = $_SESSION['threads'][$_REQUEST['tid']];
	$drawer_label = "Gestione gruppo: ".$thread->getName();
	if($thread->getType() != Thread::USER_GROUP && $thread->getType() != Thread::CONVERSATION) {
		$is_system_group = true;
		$drawer_label .= " (gruppo di sistema)";
	}
}
else {
	$drawer_label = "Crea nuovo gruppo";
	$thread = new Thread(0, new MySQLDataLoader($db), date("Y-m-d H:i:s"), null, Thread::USER_GROUP, array($_SESSION['__user__']));
	$thread->setOwner($_SESSION['__user__']);
}

$_SESSION['thread'] = $thread;

include "group.html.php";
