<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 15/08/14
 * Time: 19.27
 */
require_once "../lib/Thread.php";
require_once "../../../lib/start.php";
require_once "../../../lib/RBUtilities.php";

check_session();
check_permission(ADM_PERM);

$_SESSION['__path_to_root__'] = "../../../";

$rb = RBUtilities::getInstance($db);

$sel_th = "SELECT rb_com_threads.* FROM rb_com_threads WHERE type != ".Thread::CONVERSATION." ORDER BY name";
try {
	$res_th = $db->executeQuery($sel_th);
} catch (MySQLException $ex) {
	$ex->redirect();
}
$threads = array();
while ($th = $res_th->fetch_assoc()){
	$res_users = $db->executeQuery("SELECT utente FROM rb_com_utenti_thread WHERE thread = {$th['tid']}");
	$users = array();
	while ($row = $res_users->fetch_assoc()) {
		$users[] = $row['utente'];
	}

	$thread = new Thread($th['tid'], new MySQLDataLoader($db), $th['creation']);
	if ($th['type'] != Thread::CONVERSATION) {
		$thread->setName($th['name']);
		$thread->setType($th['type']);
	}

	$thread->setUsers($users);
	$threads[$th['tid']] = $thread;
}
$_SESSION['threads'] = $threads;
$_SESSION['count_groups'] = count($threads);

$navigation_label = "gestione moduli";
$drawer_label = "Amministrazione modulo: elenco gruppi";

include "groups.html.php";
