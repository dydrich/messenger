<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 30/03/16
 * Time: 9.57
 * manage system groups
 */
require_once "../../../lib/start.php";
require_once "../../../lib/RBUtilities.php";
require_once "../../../modules/messenger/lib/Thread.php";

check_session();
check_permission(ADM_PERM);

$_SESSION['__path_to_root__'] = "../../../";
$_SESSION['__path_to_mod_home__'] = "../../";

include "system_groups.html.php";
