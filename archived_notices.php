<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 01/05/15
 * Time: 18.59
 */
require_once "../../lib/start.php";

check_session();
check_permission(DOC_PERM);

$drawer_label = "Elenco avvisi";

$sel_notices = "SELECT * FROM rb_com_avvisi WHERE data_scadenza >= '".$_SESSION['__current_year__']->get_data_apertura()."' ORDER BY data_scadenza DESC ";

try{
	$res_notices = $db->executeQuery($sel_notices);
} catch (MySQLException $ex){
	$ex->redirect();
}

include "archived_notices.html.php";
