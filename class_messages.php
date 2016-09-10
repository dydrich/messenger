<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 26/02/16
 * Time: 19.21
 */
require_once "../../lib/start.php";

check_session();
check_permission(GEN_PERM);

$drawer_label = "Comunicazioni ai genitori della classe";

include 'class_messages.html.php';
