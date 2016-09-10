<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 24/04/16
 * Time: 23.07
 */
require_once "../lib/Thread.php";
require_once "../../../lib/start.php";
require_once "../../../lib/RBUtilities.php";

check_session();
check_permission(ADM_PERM);

$_SESSION['__path_to_root__'] = "../../../";

$rb = RBUtilities::getInstance($db);

$uniqID = $_SESSION['__user__']->getUniqID();

$now = date("Y-m-d H:i:s");
$res = $db->executeQuery("SELECT * FROM rb_com_threads WHERE tid = {$_REQUEST['tid']}");
$group = $res->fetch_assoc();
$thread = new Thread($_REQUEST['tid'], new MySQLDataLoader($db), $group['creation'], $group['name'], $group['type']);
$_SESSION['thread'] = $thread;

$sel_types = "SELECT * FROM rb_com_threads_type WHERE sistema = 1 ORDER BY codice";
$res_types = $db->executeQuery($sel_types);

/*
utenti da inserire
*/
$t = $thread->getType();
$users = $thread->getUserIDs();
$perms = $thread->getPerms();
if ($t == Thread::STUDENTS_GROUP || $t == Thread::TEACHERS_AND_STUDENTS_GROUP) {
	$add_students = [];
	$cls = $perms['class'];
	/*
	 * add students not in list
	 */
	$sel_st = "SELECT id, cognome, nome FROM rb_com_users, rb_alunni WHERE table_name = 'rb_alunni' AND uid = id_alunno AND rb_alunni.id_classe = {$cls} ORDER BY cognome, nome";
	$sts = [];
	$ids = [];
	$res_st = $db->executeQuery($sel_st);
	while ($r = $res_st->fetch_assoc()) {
		$ids[] = $r['id'];
		$sts[$r['id']] = $r['cognome']." ".$r['nome'];
	}
	$add_st = array_diff($ids, $users);
	if (count($add_st) > 0) {
		foreach ($add_st as $item) {
			$add_students[$item] = "<a href='#' class='to_add' data-group='students' data-user='".$item."'>".$sts[$item]."</a>";
		}
	}
}
if ($t == Thread::TEACHERS_AND_STUDENTS_GROUP || $t == Thread::TEACHERS_AND_PARENTS_GROUP || ($t == Thread::TEACHERS_GROUP && $perms['class'] != -1)) {
	$add_teachers = [];
	$cls = $perms['class'];
	/*
	 * add teachers of class
	 */
	$sel_te = "SELECT id, cognome, nome FROM rb_com_users, rb_cdc, rb_utenti WHERE table_name = 'rb_utenti' AND rb_com_users.uid = id_docente AND 
	id_docente = rb_utenti.uid AND  id_classe = {$cls} AND id_anno = {$_SESSION['__current_year__']->get_ID()} ORDER BY cognome, nome";
	$sts = [];
	$ids = [];
	$res_te = $db->executeQuery($sel_te);
	while ($r = $res_te->fetch_assoc()) {
		$ids[] = $r['id'];
		$sts[$r['id']] = $r['cognome']." ".$r['nome'];
	}
	$add_st = array_diff($ids, $users);
	if (count($add_st) > 0) {
		foreach ($add_st as $item) {
			$add_teachers[$item] = "<a href='#' class='to_add' data-group='teachers' data-user='".$item."'>".$sts[$item]."</a>";
		}
	}
}
if ($t == Thread::TEACHERS_GROUP && $perms['class'] == -1) {
	$add_teachers = [];
	$order = "";
	$label = "Docenti ";
	if ($perms['teachers'] != 0) {
		$order = "AND tipologia_scuola = ".$perms['teachers'];
		if ($perms['teachers'] == 1) {
			$label .= "scuola secondaria";
		}
		else {
			$label .= "scuola primaria";
		}
	}
	$sel_te = "SELECT id, cognome, nome FROM rb_com_users, rb_docenti, rb_utenti WHERE table_name = 'rb_utenti' AND rb_com_users.uid = id_docente AND 
	id_docente = rb_utenti.uid $order ORDER BY cognome, nome";
	$sts = [];
	$ids = [];
	$res_te = $db->executeQuery($sel_te);
	while ($r = $res_te->fetch_assoc()) {
		$ids[] = $r['id'];
		$sts[$r['id']] = $r['cognome']." ".$r['nome'];
	}
	$add_st = array_diff($ids, $users);
	if (count($add_st) > 0) {
		foreach ($add_st as $item) {
			$add_teachers[$item] = "<a href='#' class='to_add' data-group='teachers' data-user='".$item."'>".$sts[$item]."</a>";
		}
	}
}
if ($t == Thread::TEACHERS_AND_PARENTS_GROUP || $t == Thread::PARENTS_GROUP) {
	$add_parents = [];
	$cls = $perms['class'];
	$sel = "SELECT id, rb_utenti.cognome, rb_utenti.nome FROM rb_com_users, rb_utenti, rb_genitori_figli, rb_alunni WHERE table_name = 'rb_utenti' AND rb_com_users.uid = 
id_genitore AND id_genitore = rb_utenti.uid AND rb_genitori_figli.id_alunno = rb_alunni.id_alunno AND rb_alunni.id_classe = {$cls} ORDER BY cognome, nome";
	$sts = [];
	$ids = [];
	$res = $db->executeQuery($sel);
	while ($r = $res->fetch_assoc()) {
		$ids[] = $r['id'];
		$sts[$r['id']] = $r['cognome']." ".$r['nome'];
	}
	$add_st = array_diff($ids, $users);
	if (count($add_st) > 0) {
		foreach ($add_st as $item) {
			$add_parents[$item] = "<a href='#' class='to_add' data-group='parents' data-user='".$item."'>".$sts[$item]."</a>";
		}
	}
}

$drawer_label = "Gestione gruppo di sistema";

include "group.html.php";
