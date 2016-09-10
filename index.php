<?php

require_once "../../lib/start.php";

check_session();

$drawer_label = "Home page";
$_SESSION['__path_to_root__'] = "../../";

include "index.html.php";
