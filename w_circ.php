<?php 

// controllo circolari non lette o pubblicate dopo l'ultimo accesso
$sel_post = "SELECT * FROM rb_com_circolari WHERE data_inserimento > (SELECT previous_access FROM rb_utenti WHERE uid = ".$_SESSION['__user__']->getUid().")";
$res_post = $db->execute($sel_post);
$post_circ = $res_post->num_rows;
if($post_circ == 0){
	$_SESSION['__circular_msg__'] = "Nessuna circolare pubblicata dal tuo ultimo accesso";
}
else{
	$_SESSION['__circular_msg__'] = "<span class='attention'>Dal tuo ultimo accesso sono state pubblicate $post_circ circolari</span>";
}

$sel_unread = "SELECT COUNT(*) AS count FROM rb_com_circolari WHERE anno = ".$_SESSION['__current_year__']->get_ID();
$res_unread = $db->execute($sel_unread);
$_unr = $res_unread->fetch_assoc();
$unread_count = $_unr['count'];

$sel_read = "SELECT COUNT(rb_com_lettura_circolari.id_circolare) AS count FROM rb_com_lettura_circolari, rb_com_circolari WHERE rb_com_lettura_circolari.id_circolare = rb_com_circolari.id_circolare AND docente = ".$_SESSION['__user__']->getUid()." AND anno = ".$_SESSION['__current_year__']->get_ID()." AND letta = 1";
$res_read = $db->execute($sel_read);
$_rd = $res_read->fetch_assoc();
$unread_count -= $_rd['count'];
$_SESSION['__unread_count__'] = $unread_count;
if ($unread_count > 0) {
	?>
	<div class="welcome">
		<p id="w_head">Circolari</p>
		<p class="w_text">
			<?php print $_SESSION['__circular_msg__'] ?>.<br/>
			<?php
			if ($_SESSION['__unread_count__'] > 0): ?>
				<a href="<?php echo $_SESSION['__path_to_root__'] ?>modules/communication/load_module.php?module=com&area=teachers&page=vedi_circolari">Sono
					presenti <span id="num_circ"><?php print $_SESSION['__unread_count__'] ?></span> circolari non lette.</a>
			<?php else: ?>
				Sono presenti <span id="num_circ"><?php print $_SESSION['__unread_count__'] ?></span> circolari non lette.
			<?php endif; ?>
		</p>
	</div>
<?php
}
?>
