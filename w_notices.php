<?php

$sel_notices = "SELECT * FROM rb_com_avvisi WHERE data_scadenza >= NOW()";
$res_notices = $db->executeQuery($sel_notices);

if ($res_notices->num_rows > 0){
?>
	<div class="welcome">
		<p id="w_head">Avviso <?php echo date("d/m/Y") ?></p>
		<?php 
		if ($res_notices->num_rows > 0){ 
			while ($notice = $res_notices->fetch_assoc()){
		?>
		<p class="w_text attention" style="font-weight: bold">&middot; <?php echo $notice['testo'] ?></p>
		<?php 
			}
		}
		?>
	</div>
<?php 
}
?>
