<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 18/08/14
 * Time: 12.47
 */

require_once "../../../lib/start.php";
require_once "../../../lib/RBUtilities.php";
require_once "../lib/Thread.php";

check_session();
check_permission(ADM_PERM);

$_SESSION['__path_to_root__'] = "../../../";
$_SESSION['__path_to_mod_home__'] = "../../";

$navigation_label = "gestione moduli";
$drawer_label = "Modulo: messaggistica";

include "index.html.php";
