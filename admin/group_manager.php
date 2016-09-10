<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 25/04/16
 * Time: 18.04
 */
require_once "../lib/Thread.php";
require_once "../../../lib/start.php";
require_once "../../../lib/RBUtilities.php";

check_session();
check_permission(ADM_PERM);

$_SESSION['__path_to_root__'] = "../../../";

function create_list($thread, $db, $list) {
	$adds = [];
	$users = $thread->getUserIDs();
	$perms = $thread->getPerms();
	$cls = $perms['class'];
	if ($list == "st_list") {
		/*
		 * add students not in list
		 */
		$sel_st = "SELECT id, cognome, nome FROM rb_com_users, rb_alunni WHERE table_name = 'rb_alunni' AND uid = id_alunno AND rb_alunni.id_classe = 
				{$cls} ORDER BY cognome, nome";
		$sts = [];
		$ids = [];
		$res_st = $db->executeQuery($sel_st);
		while ($r = $res_st->fetch_assoc()) {
			$ids[] = $r['id'];
			$sts[$r['id']] = $r['cognome'] . " " . $r['nome'];
		}
		$add_st = array_diff($ids, $users);
		if (count($add_st) > 0) {
			foreach ($add_st as $item) {
				$adds[$item] = "<a href='#' class='to_add' data-group='students' data-user='" . $item . "'>" . $sts[$item] . "</a>";
			}
		}
		asort($adds);
		return "<p>Studenti della classe: ".join(', ', $adds)."</p>";
	}
	else if ($list == "te_list") {
		/*
		 * add teachers not in list
		 */
		$sel_te = "SELECT id, cognome, nome FROM rb_com_users, rb_cdc, rb_utenti WHERE table_name = 'rb_utenti' AND rb_com_users.uid = id_docente AND 
	id_docente = rb_utenti.uid AND id_classe = {$cls} AND id_anno = {$_SESSION['__current_year__']->get_ID()} ORDER BY cognome, nome";
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
				$adds[$item] = "<a href='#' class='to_add' data-group='teachers' data-user='".$item."'>".$sts[$item]."</a>";
			}
		}
		asort($adds);
		return "<p>Docenti della classe: ".join(', ', $adds)."</p>";
	}
	else if ($list == "te_list_only") {
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
				$adds[$item] = "<a href='#' class='to_add' data-group='teachers' data-user='".$item."'>".$sts[$item]."</a>";
			}
		}
		asort($adds);
		return "<p>".$label.": ".join(', ', $adds)."</p>";
	}
	else if ($list == "pa_list") {
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
				$adds[$item] = "<a href='#' class='to_add' data-group='parents' data-user='".$item."'>".$sts[$item]."</a>";
			}
		}
		asort($adds);
		return "<p>Genitori: ".join(', ', $adds)."</p>";
	}
}

$rb = RBUtilities::getInstance($db);

$action = $_REQUEST['action'];
if ($action != 'new') {
	$thread = $_SESSION['thread'];
	$thread->restoreThread(new MySQLDataLoader($db));
}

$response = array("status" => "ok", "message" => "");
$rb = \RBUtilities::getInstance($db);
header("Content-type: application/json");

switch ($action) {
	case 'new':
		$type = $_REQUEST['type'];
		$name = $db->real_escape_string($_REQUEST['nm']);
		$now = date("Y-m-d H:i:s");
		$thread = new Thread(0, new MySQLDataLoader($db), $now, $name, $type, array());
		$thread->setOwner($_SESSION['__user__']);
		$thread->save();
		$thread->setBlock(1);
		$response['tid'] = $thread->getTid();
		break;
	case 'delete_group':
		$thread->deleteAll();
		break;
	case 'add_admin':
		$user = $_REQUEST['user'];
		$thread->addAdministrator($user);
		$us = $rb->loadUserFromUniqID($user);
		if (count($thread->getAdmins()) < 1) {
			$response['name'] = "Nessuno";
		}
		else {
			$names = [];
			foreach ($thread->getAdmins() as $user) {
				$names[$user->getUniqID()] = $user->getFullName(0);
			}
			asort($names);
			$str = '';
			foreach ($names as $id => $name) {
				$str .= "<a href='#' class='admin' data-user='".$id."'>".$name."</a>, ";
			}
			$response['name'] = substr($str, 0, (strlen($str) - 2));
		}
		break;
	case 'remove_admin':
		$user = $_REQUEST['user'];
		$thread->removeAdministrator($user);
		if (count($thread->getAdmins()) < 1) {
			$response['name'] = "Nessuno";
		}
		else {
			$names = [];
			foreach ($thread->getAdmins() as $user) {
				$names[$user->getUniqID()] = $user->getFullName(0);
			}
			asort($names);
			$str = '';
			foreach ($names as $id => $name) {
				$str .= "<a href='#' class='admin' data-user='".$id."'>".$name."</a>, ";
			}
			$response['name'] = substr($str, 0, (strlen($str) - 2));
		}
		break;
	case 'remove_user':
		$user = $_REQUEST['user'];
		$thread->deleteUser($user);
		if (count($thread->getUsers()) < 1) {
			$response['users'] = "Nessuno";
		}
		else {
			$users = [];
			foreach ($thread->getUsers() as $us) {
				$users[$us->getUniqID()] = $us->getFullName(0);
			}
			asort($users);
			$str = '';
			foreach ($users as $id => $name) {
				$str .= "<a href='#' class='user' data-user='".$id."'>".$name."</a>, ";
			}
			$response['users'] = substr($str, 0, (strlen($str) - 2));
			$response['counter'] = count($thread->getUsers());
		}
		/*
		 * calcolo gruppi di inserimento in base al tipo di utente cancellato
		 */
		$reload = [];
		$del = $rb->loadUserFromUniqID($user);
		$perms = $thread->getPerms();
		if ($del instanceof StudentBean) {
			$reload[] = 'st_list';
			$response['st_list'] = create_list($thread, $db, "st_list");
		}
		else if ($del instanceof SchoolUserBean && $perms['teachers'] != -1) {
			$reload[] = 'te_list';
			$response['te_list'] = create_list($thread, $db, "te_list");
		}
		else if ($del instanceof SchoolUserBean && $perms['class'] == -1) {
			$reload[] = 'te_list_only';
			$response['te_list_only'] = create_list($thread, $db, "te_list_only");
		}
		else if ($del instanceof ParentBean || ($del instanceof SchoolUserBean && $perms['parents'] == 1 && $perms['teachers'] == -1)) {
			$reload[] = 'pa_list';
			$response['pa_list'] = create_list($thread, $db, "pa_list");
		}
		$response['reload'] = $reload;
		break;
	case 'add_user':
		$user = $_REQUEST['user'];
		$thread->addUser($user);
		$group = $_REQUEST['group'];
		$users = [];
		foreach ($thread->getUsers() as $us) {
			$users[$us->getUniqID()] = $us->getFullName(0);
		}
		asort($users);
		$str = '';
		foreach ($users as $id => $name) {
			$str .= "<a href='#' class='user' data-user='".$id."'>".$name."</a>, ";
		}
		$response['users'] = substr($str, 0, (strlen($str) - 2));
		$response['counter'] = count($thread->getUsers());
		/*
		 * calcolo gruppi di inserimento in base al tipo di utente cancellato
		 */
		$reload = [];
		$del = $rb->loadUserFromUniqID($user);
		$perms = $thread->getPerms();
		if ($del instanceof StudentBean) {
			// studente -> studenti della classe
			$reload[] = 'st_list';
			$response['st_list'] = create_list($thread, $db, "st_list");
		}
		else if ($del instanceof SchoolUserBean && $perms['class'] != -1 && ($perms['parents'] == -1 || $group == 'teachers')) {
			$reload[] = 'te_list';
			$response['te_list'] = create_list($thread, $db, "te_list");
		}
		else if ($del instanceof SchoolUserBean && $perms['class'] == -1) {
			$reload[] = 'te_list_only';
			$response['te_list_only'] = create_list($thread, $db, "te_list_only");
		}
		else if ($del instanceof ParentBean || $perms['parents'] != -1) {
			$reload[] = 'pa_list';
			$response['pa_list'] = create_list($thread, $db, "pa_list");
		}
		$response['reload'] = $reload;
		break;
	case 'block':
		$param = $_REQUEST['param'];
		$thread->setBlock($param);
		if ($param == 1) {
			$response['label'] = 'SI';
			$response['link'] = 'Sblocca il gruppo';
		}
		else {
			$response['label'] = 'NO';
			$response['link'] = 'Blocca il gruppo';
		}
		break;
	case 'activate':
		$param = $_REQUEST['param'];
		$thread->activate($param);
		if ($param == 1) {
			$response['label'] = 'SI';
			$response['link'] = 'Disattiva il gruppo';
		}
		else {
			$response['label'] = 'NO';
			$response['link'] = 'Attiva il gruppo';
		}
		break;
	case 'env':
		$field = $_REQUEST['id'];
		$value = $_REQUEST['value'];
		$thread->setName($value);
		echo $value;
		exit;
		break;
	case 'update_type':
		$type = $_REQUEST['type'];
		$thread->setType($type);
}

$res = json_encode($response);
echo $res;
