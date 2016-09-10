<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 15/05/16
 * Time: 18.18
 */
require_once "../lib/Thread.php";
require_once "../../../lib/start.php";
require_once "../../../lib/RBUtilities.php";

check_session();
check_permission(ADM_PERM);

$_SESSION['__path_to_root__'] = "../../../";

$rb = RBUtilities::getInstance($db);

$uniqID = $_SESSION['__user__']->getUniqID();

$sel_types = "SELECT * FROM rb_com_threads_type WHERE sistema = 1 ORDER BY codice";
$res_types = $db->executeQuery($sel_types);

$drawer_label = "Nuovo gruppo di sistema";

include 'new_group.html.php';
