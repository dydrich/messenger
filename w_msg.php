<?php
$user_type = $_SESSION['user_type'];
$uniqID = $_SESSION['__user__']->getUniqID();

$sel_msg = "SELECT COUNT(mid) FROM rb_mess_messages, rb_mess_threads, rb_mess_utenti_thread WHERE rb_mess_messages.tid = rb_mess_threads.tid AND rb_mess_threads.tid = thread AND type = 'C' AND utente = {$uniqID} AND target = rb_mess_threads.tid AND sender <> {$uniqID} AND read_timestamp IS NULL";
$unread = $db->executeCount($sel_msg);
$sel_grp = "SELECT COUNT(mid) FROM rb_mess_messages, rb_mess_threads, rb_mess_utenti_thread WHERE rb_mess_messages.tid = rb_mess_threads.tid AND rb_mess_threads.tid = thread AND type = 'G' AND utente = {$uniqID} AND target = rb_mess_threads.tid AND sender <> {$uniqID} AND send_timestamp > last_access ";
$unread += $db->executeCount($sel_grp);
$sel_files = "SELECT COUNT(id) FROM rb_com_files WHERE destinatario = {$_SESSION['__user__']->getUid()} AND data_download IS NULL";
$not_downl = $db->executeCount($sel_files);
if ($unread > 0 || $not_downl > 0) {
?>
	<div class="welcome">
		<p id="w_head">Messaggi e file</p>
		<p class="w_text">
			<?php
			if ($unread < 1) {
				echo "<span>Nessun nuovo messagggio</span>";
			}
			else {
			?>
			<a href="<?php echo $_SESSION['__path_to_root__'] ?>modules/messenger/load_module.php?module=com&area=teachers&page=threads">Ci
					sono <?php echo $unread ?> nuovi messaggi</a><br/>
			<?php
			}
			?>
			<br/>
			<?php
			if ($not_downl < 1) {
				echo "<span>Nessun nuovo file</span>";
			}
			else {
				?>
				<a href="<?php echo $_SESSION['__path_to_root__'] ?>modules/messenger/load_module.php?module=com&area=teachers&page=files">Ci
					sono <?php echo $not_downl ?> nuovi file</a><br/>
			<?php
			}
			?>
		</p>
	</div>
<?php
}
?>
