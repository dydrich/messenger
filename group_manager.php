<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 25/04/16
 * Time: 18.04
 */
require_once "lib/Thread.php";
require_once "../../lib/start.php";
require_once "../../lib/RBUtilities.php";

check_session();

$_SESSION['__path_to_root__'] = "../../";


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
		$name = $db->real_escape_string($_REQUEST['nm']);
		$now = date("Y-m-d H:i:s");
		$thread = new Thread(0, new MySQLDataLoader($db), $now, $name, Thread::USER_GROUP, array($_SESSION['__user__']->getUniqID()));
		$thread->setOwner($_SESSION['__user__']);
		$thread->save();
		$thread->addAdministrator($_SESSION['__user__']->getUniqID());
		$response['tid'] = $thread->getTid();
		$_SESSION['threads'][$thread->getTid()] = $thread;
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
}

$res = json_encode($response);
echo $res;
