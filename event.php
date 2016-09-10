<?php

require_once "../../lib/start.php";

check_session();
check_permission(DIR_PERM|DSG_PERM|SEG_PERM|APS_PERM|AIS_PERM|AMS_PERM);

$drawer_label = "Dettaglio evento";

// estrazione eventi per id_padre
$sel_eventi_p = "SELECT id_evento, abstract FROM rb_com_eventi WHERE (data_evento > NOW() OR data_evento IS NULL) AND id_padre IS NULL ";
if(isset($_REQUEST['id']) && $_REQUEST['id'] != 0)
	$sel_eventi_p .= "AND id_evento != ".$_REQUEST['id'];
$res_eventi_p = $db->execute($sel_eventi_p);

// classi
$sel_classi = "SELECT id_classe, CONCAT_WS('', anno_corso, sezione) AS cls, rb_sedi.nome FROM rb_classi, rb_sedi WHERE sede = id_sede ORDER BY rb_sedi.ordine_di_scuola, sezione, anno_corso";
$res_classi = $db->execute($sel_classi);

// ordini di scuola
$sel_ord = "SELECT * FROM rb_tipologia_scuola WHERE has_admin = 1 AND attivo = 1";
$res_ord = $db->executeQuery($sel_ord);

$action = 1;
$idnews = 0;
if($_REQUEST['id'] != 0){
	$action = 3;
	$sel_evs = "SELECT * FROM rb_com_eventi WHERE id_evento = ".$_REQUEST['id'];
    $res_evs = $db->execute($sel_evs);
    $evs = $res_evs->fetch_assoc();
    $evs['testo'] = ereg_replace("<br />", "", $evs['testo']);
    list($data, $my_ora) = explode(" ", $evs['data_evento']);
    $my_date = format_date($data, SQL_DATE_STYLE, IT_DATE_STYLE, "/");
    $_i = $_REQUEST['id'];
    $pubblico = $evs['pubblico'];
    $modificabile = $evs['modificabile'];
}
else{
    $my_date = date("d/m/Y");
    $my_ora = date("H:i:s");
    $_i = 0;
    $modificabile = $pubblico = 1;
}

include "event.html.php";
