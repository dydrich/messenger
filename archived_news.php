<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 28/04/15
 * Time: 16.55
 */

require_once "../../lib/start.php";

check_session();
check_permission(DIR_PERM|DSG_PERM|SEG_PERM|APS_PERM|AIS_PERM|AMS_PERM|DOC_PERM);

$drawer_label = "Archivio news";

$sel_news = "SELECT id_news, abstract, data, testo, ora FROM rb_com_news ORDER BY data DESC, id_news DESC  ";
try{
	$res_news = $db->executeQuery($sel_news);
} catch (MySQLException $ex){
	$ex->redirect();
}

include "archived_news.html.php";
