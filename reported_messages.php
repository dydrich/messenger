<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 26/07/16
 * Time: 12.05
 */

require_once "lib/Message.php";
require_once "lib/Thread.php";
require_once "../../lib/start.php";
require_once "../../lib/RBUtilities.php";

check_session();

$user_type = $_SESSION['user_type'];
$uniqID = $_SESSION['__user__']->getUniqID();

$thread = $_SESSION['threads'][$_REQUEST['tid']];

$drawer_label = $thread->getName()."::Messaggi segnalati";

$reported = $thread->getReportedMessages();

include 'reported_messages.html.php';
