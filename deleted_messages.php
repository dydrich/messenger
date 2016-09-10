<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 01/08/16
 * Time: 17.28
 */
require_once "lib/Message.php";
require_once "lib/Thread.php";
require_once "../../lib/start.php";
require_once "../../lib/RBUtilities.php";

check_session();

$user_type = $_SESSION['user_type'];
$uniqID = $_SESSION['__user__']->getUniqID();

$thread = $_SESSION['threads'][$_REQUEST['tid']];

$drawer_label = $thread->getName()."::Messaggi cancellati";

$reported = $thread->getDeletedMessages();

include 'deleted_messages.html.php';
