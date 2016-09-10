<?php

$teach_params = "";
if($_SESSION['__user__']->getPerms()&DOC_PERM) {
	$id_classi = array();
	//print_r($_SESSION['__user__']->getClasses());
	if(count($_SESSION['__user__']->getClasses()) > 0) {
		foreach($_SESSION['__user__']->getClasses() as $k => $a)
			array_push($id_classi, $k);
		$in = join(",", $id_classi);
		$teach_params = "AND (classe IN ($in) OR classe IS NULL)";
	}
}
$max_events = 9;

$q_ord = "";
if ($_SESSION['__user__']->getSchoolOrder() != ""){
	$q_ord = "AND (ordine_di_scuola IS NULL OR ordine_di_scuola = {$_SESSION['__user__']->getSchoolOrder()})";
}

$sel_impegni = "SELECT rb_com_eventi.*, CASE WHEN DATE_ADD(data_evento, INTERVAL '-7' DAY)  > NOW() THEN 'no' ELSE 'yes' END AS red_style FROM rb_com_eventi WHERE data_evento > NOW() {$teach_params} AND has_sons = 0 {$q_ord} ORDER BY data_evento ASC LIMIT $max_events";
//print $sel_impegni;
$res_impegni = $db->executeQuery($sel_impegni);
if($res_impegni->num_rows > 0){
?>

<div class="welcome">
<p id="w_head">Prossimi impegni</p>
<?php
$shown_events = 0;
while($impegno = $res_impegni->fetch_assoc()){
	$shown_events++;
	list($data, $ora) = explode(" ", $impegno['data_evento']);
	$curr_date = new DateTime($data);
	$max_date = new DateTime(date("Y-m-d"));
	$max_date = $max_date->add(new DateInterval('P7D'));
	if ($curr_date->format("Y-m-d") > $max_date->format("Y-m-d") && $shown_events > $max_events) {
		continue;
	}
	if ($impegno['id_padre'] != ""){
		$sel_text = "SELECT testo FROM rb_com_eventi WHERE id_evento = {$impegno['id_padre']}";
		$testo = $db->executeCount($sel_text);
	}
	else {
		$testo = $impegno['testo'];
	}
?>
<p class="w_text" style="margin: 0">
<a href="#" onclick="$('#testo_<?php echo $impegno['id_evento'] ?>').toggle(1000)" class="no_decoration">
<strong>
    <?php if($impegno['red_style'] == "yes") print "<span class='attention'>" ?>
    <?php print format_date($data, SQL_DATE_STYLE, IT_DATE_STYLE, "/")." ".substr($ora, 0, 5) ?>
    <?php if($impegno['red_style'] == "yes") print "</span>" ?>
</strong><br />
<?php print $impegno['abstract'] ?>
</a>
</p>
<p id="testo_<?php echo $impegno['id_evento'] ?>" class="evento" style="display: none"><?php echo nl2br($testo) ?></p>
<?php
	}
?>
</div>
<?php } ?>
