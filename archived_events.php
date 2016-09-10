<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 28/04/15
 * Time: 17.16
 */

require_once "../../lib/start.php";

check_session();
check_permission(DIR_PERM|DSG_PERM|SEG_PERM|APS_PERM|AIS_PERM|AMS_PERM|DOC_PERM);

$drawer_label = "Elenco eventi";

$sel_evs = "SELECT id_evento, abstract, testo, data_evento, data_inserimento, data_modifica FROM rb_com_eventi WHERE data_evento >= '".$_SESSION['__current_year__']->get_data_apertura()."' ORDER BY data_evento DESC, id_evento DESC ";
$res_evs = $db->execute($sel_evs);

include "archived_events.html.php";

