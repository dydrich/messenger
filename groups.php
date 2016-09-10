<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 15/08/14
 * Time: 11.15
 */
require_once "lib/Thread.php";
require_once "../../lib/start.php";
require_once "../../lib/RBUtilities.php";

check_session();

$drawer_label = "Gruppi di conversazione";

$user_type = $_SESSION['user_type'];
$uniqID = $_SESSION['__user__']->getUniqID();

if (isset($_SESSION['threads'])) {
	$threads = $_SESSION['threads'];
}

include 'groups.html.php';
