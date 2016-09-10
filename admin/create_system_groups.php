<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 18/08/14
 * Time: 20.37
 */
require_once "../lib/Thread.php";
require_once "../../../lib/start.php";
require_once "../../../lib/RBUtilities.php";

check_session();
check_permission(ADM_PERM);

$_SESSION['__path_to_root__'] = "../../";

header("Content-type: application/json");
$response = ["status" => "ok", "message" => "Operazione completata"];

$y = $_SESSION['__current_year__']->get_ID();

try {
	/**
	 * segreteria
	 * thread type: Thread::ADMIN_GROUP
	 */
	$t = Thread::ADMIN_GROUP;
	$sel_us = "SELECT rb_utenti.uid FROM rb_utenti, rb_gruppi_utente WHERE rb_utenti.uid = rb_gruppi_utente.uid AND gid IN (3,5,6,7)";
	$res_us = $db->executeQuery($sel_us);
	$group_name = "Amministrazione";
	$tid = $db->executeUpdate("INSERT INTO rb_com_threads (owner, last_message, system, name, type, creation) VALUES (1, NULL, 1, 
'{$group_name}', {$t}, NOW())");
	$db->executeUpdate("INSERT INTO rb_com_system_threads (tid, anno, ata) VALUES ({$tid}, {$y}, 1)");
	while ($rw = $res_us->fetch_assoc()) {
		$id = $db->executeCount("SELECT id FROM rb_com_users WHERE uid = {$rw['uid']} AND type = 'school'");
		$db->executeUpdate("INSERT INTO rb_com_utenti_thread (thread, utente) VALUES ({$tid}, {$id})");
	}

	/**
	 * gruppo docenti
	 * gruppi docente per ordine di scuola
	 * * thread type: Thread::TEACHERS_GROUP
	 */
	$t = Thread::TEACHERS_GROUP;
	$sel_docs = "SELECT rb_com_users.id AS id, tipologia_scuola FROM rb_com_users, rb_utenti, rb_docenti 
				WHERE rb_com_users.uid = rb_utenti.uid AND rb_utenti.uid = rb_docenti.id_docente AND rb_utenti.attivo = 1 AND type = 'school'";
	$res_docs = $db->executeQuery($sel_docs);
	$group_name = "Docenti";
	$tid_docs = $db->executeUpdate("INSERT INTO rb_com_threads (owner, last_message, system, name, type, creation) VALUES (1, NULL, 1, '{$group_name}', 
	{$t}, NOW())");
	$db->executeUpdate("INSERT INTO rb_com_system_threads (tid, anno, docenti) VALUES ({$tid_docs}, {$y}, 0)");
	$group_name = "Docenti SM";
	$tid_docs_1 = $db->executeUpdate("INSERT INTO rb_com_threads (owner, last_message, system, name, type, creation) VALUES (1, NULL, 1, '{$group_name}', {$t}, NOW())");
	$db->executeUpdate("INSERT INTO rb_com_system_threads (tid, anno, docenti) VALUES ({$tid_docs_1}, {$y}, 1)");
	$group_name = "Docenti SP";
	$tid_docs_2 = $db->executeUpdate("INSERT INTO rb_com_threads (owner, last_message, system, name, type, creation) VALUES (1, NULL, 1, '{$group_name}', {$t}, NOW())");
	$db->executeUpdate("INSERT INTO rb_com_system_threads (tid, anno, docenti) VALUES ({$tid_docs_2}, {$y}, 2)");
	$teachers = array();
	while ($row = $res_docs->fetch_assoc()) {
		$db->executeUpdate("INSERT INTO rb_com_utenti_thread (thread, utente) VALUES ({$tid_docs}, {$row['id']})");
		if ($row['tipologia_scuola'] == 1) {
			$db->executeUpdate("INSERT INTO rb_com_utenti_thread (thread, utente) VALUES ({$tid_docs_1}, {$row['id']})");
		}
		else if ($row['tipologia_scuola'] == 2) {
			$db->executeUpdate("INSERT INTO rb_com_utenti_thread (thread, utente) VALUES ({$tid_docs_2}, {$row['id']})");
		}
	}

	/**
	 * gruppi per classe:
	 * docenti: thread type: 'D'
	 * docenti e alunni: thread type: 'AD"
	 * docenti e genitori: thread type: 'DG"
	 * genitori: thread type: 'G"
	 * alunni: thread type: 'A"
	 */
	/**
	 * scuola secondaria
	 */
	$sel_classes_lev1 = "SELECT id_classe, anno_corso, sezione FROM rb_classi WHERE ordine_di_scuola = 1";
	$res_classes_lev1 = $db->executeQuery($sel_classes_lev1);
	while ($row = $res_classes_lev1->fetch_assoc()) {
		$response['class'] = $row['anno_corso'].$row['sezione']." SM";
		/*
		 * docenti cdc
		 */
		$t = Thread::TEACHERS_GROUP;
		$response['area'] = "docenti cdc";
		$group_name = "Docenti ".$row['anno_corso'].$row['sezione']." SM";
		$tid = $db->executeUpdate("INSERT INTO rb_com_threads (owner, last_message, system, name, type, creation) VALUES (1, NULL, 1, '{$group_name}', {$t}, NOW())");
		$db->executeUpdate("INSERT INTO rb_com_system_threads (tid, anno, classe, docenti) VALUES ({$tid}, {$y}, {$row['id_classe']}, 1)");
		$res_cdc = $db->executeQuery("SELECT DISTINCT(id_docente) AS doc FROM rb_cdc WHERE id_anno = {$y} AND id_classe = {$row['id_classe']}");
		$docs = array();
		while ($r = $res_cdc->fetch_assoc()) {
			$id_doc = $db->executeCount("SELECT id FROM rb_com_users WHERE uid = {$r['doc']} AND table_name = 'rb_utenti'");
			$docs[] = $id_doc;
			$db->executeUpdate("INSERT INTO rb_com_utenti_thread (thread, utente) VALUES ({$tid}, {$id_doc})");
		}
		// sostegno
		$res_sos = $db->executeQuery("SELECT DISTINCT(docente) AS doc FROM rb_assegnazione_sostegno WHERE anno = {$y} AND classe = {$row['id_classe']}");
		while ($r = $res_sos->fetch_assoc()) {
			$id_doc = $db->executeCount("SELECT id FROM rb_com_users WHERE uid = {$r['doc']} AND table_name = 'rb_utenti'");
			$docs[] = $id_doc;
			$db->executeUpdate("INSERT INTO rb_com_utenti_thread (thread, utente) VALUES ({$tid}, {$id_doc})");
		}

		/*
		 * alunni
		 */
		$t = Thread::STUDENTS_GROUP;
		$response['area'] = "alunni";
		$group_name = "Alunni ".$row['anno_corso'].$row['sezione']." SM";
		$tid = $db->executeUpdate("INSERT INTO rb_com_threads (owner, last_message, system, name, type, creation) VALUES (1, NULL, 1, '{$group_name}', {$t}, NOW())");
		$db->executeUpdate("INSERT INTO rb_com_system_threads (tid, anno, classe, alunni) VALUES ({$tid}, {$y}, {$row['id_classe']}, 1)");
		$res_std = $db->executeQuery("SELECT id_alunno FROM rb_alunni WHERE id_classe = {$row['id_classe']} AND attivo = 1");
		$stds = array();
		$real_stsds = array();
		while ($r = $res_std->fetch_assoc()) {
			$response['stq'] = "SELECT id FROM rb_com_users WHERE uid = {$r['id_alunno']} AND table_name = 'rb_alunni'";
			$id_std = $db->executeCount("SELECT id FROM rb_com_users WHERE uid = {$r['id_alunno']} AND table_name = 'rb_alunni'");
			$stds[] = $id_std;
			$real_stsds[] = $r['id_alunno'];
			$db->executeUpdate("INSERT INTO rb_com_utenti_thread (thread, utente) VALUES ({$tid}, {$id_std})");
		}
		/**
		 * docenti e alunni
		 */
		$t = Thread::TEACHERS_AND_STUDENTS_GROUP;
		$response['area'] = "docenti e alunni";
		$group_name = "Classe ".$row['anno_corso'].$row['sezione']." SM";
		$tid = $db->executeUpdate("INSERT INTO rb_com_threads (owner, last_message, system, name, type, creation) VALUES (1, NULL, 1, '{$group_name}', {$t}, 
		NOW())");
		$db->executeUpdate("INSERT INTO rb_com_system_threads (tid, anno, classe, docenti, alunni) VALUES ({$tid}, {$y}, {$row['id_classe']}, 1, 1)");
		foreach ($docs as $d) {
			$db->executeUpdate("INSERT INTO rb_com_utenti_thread (thread, utente) VALUES ({$tid}, {$d})");
		}
		foreach ($stds as $d) {
			$db->executeUpdate("INSERT INTO rb_com_utenti_thread (thread, utente) VALUES ({$tid}, {$d})");
		}
		/**
		 * genitori
		 */
		$t = Thread::PARENTS_GROUP;
		$response['area'] = "genitori";
		$group_name = "Genitori ".$row['anno_corso'].$row['sezione']." SM";
		$tid = $db->executeUpdate("INSERT INTO rb_com_threads (owner, last_message, system, name, type, creation) VALUES (1, NULL, 1, '{$group_name}', {$t}, NOW())");
		$db->executeUpdate("INSERT INTO rb_com_system_threads (tid, anno, classe, genitori) VALUES ({$tid}, {$y}, {$row['id_classe']}, 1)");
		reset ($stds);
		$parents = array();
		//if ($row['id_classe'] == 5) echo count($stds)." students --- ";
		foreach ($real_stsds as $d) {
			//if ($row['id_classe'] == 5) echo "searching for $d --- ";
			$id_parent = $db->executeQuery("SELECT id FROM rb_genitori_figli, rb_com_users WHERE uid = id_genitore AND table_name = 'rb_utenti' AND id_alunno = {$d}");
			while ($rp = $id_parent->fetch_assoc()) {
				$p = $rp['id'];
				$parents[] = $p;
				//if ($row['id_classe'] == 5) echo "found $p for $d --- <br>\n";
				$db->executeUpdate("INSERT INTO rb_com_utenti_thread (thread, utente) VALUES ({$tid}, {$p})");
			}
		}
		/**
		 * docenti e genitori
		 */
		$t = Thread::TEACHERS_AND_PARENTS_GROUP;
		$response['area'] = "docenti e genitori";
		$group_name = "Docenti e genitori ".$row['anno_corso'].$row['sezione']." SM";
		$tid = $db->executeUpdate("INSERT INTO rb_com_threads (owner, last_message, system, name, type, creation) VALUES (1, NULL, 1, '{$group_name}', {$t}, NOW())");
		$db->executeUpdate("INSERT INTO rb_com_system_threads (tid, anno, classe, docenti, genitori) VALUES ({$tid}, {$y}, {$row['id_classe']}, 1, 1)");
		foreach ($docs as $d) {
			$db->executeUpdate("INSERT INTO rb_com_utenti_thread (thread, utente) VALUES ({$tid}, {$d})");
		}
		foreach ($parents as $p) {
			$db->executeUpdate("INSERT INTO rb_com_utenti_thread (thread, utente) VALUES ({$tid}, {$p})");
		}
	}

	/**
	 * scuola primaria
	 */
	$sel_classes_lev2 = "SELECT id_classe, anno_corso, sezione FROM rb_classi WHERE ordine_di_scuola = 2 ORDER BY anno_corso, sezione";
	$res_classes_lev2 = $db->executeQuery($sel_classes_lev2);
	while ($row = $res_classes_lev2->fetch_assoc()) {
		/*
		 * docenti cdc
		 */
		$t = Thread::TEACHERS_GROUP;
		$group_name = "Docenti ".$row['anno_corso'].$row['sezione']." SP";
		$tid = $db->executeUpdate("INSERT INTO rb_com_threads (owner, last_message, system, name, type, creation) VALUES (1, NULL, 1, '{$group_name}', {$t}, NOW())");
		$db->executeUpdate("INSERT INTO rb_com_system_threads (tid, anno, classe, docenti) VALUES ({$tid}, {$y}, {$row['id_classe']}, 2)");
		$res_cdc = $db->executeQuery("SELECT DISTINCT(id_docente) AS doc FROM rb_cdc WHERE id_anno = {$y} AND id_classe = {$row['id_classe']} AND id_docente IS NOT NULL");
		$docs = array();
		while ($r = $res_cdc->fetch_assoc()) {
			$id_doc = $db->executeCount("SELECT id FROM rb_com_users WHERE uid = {$r['doc']} AND table_name = 'rb_utenti'");
			$docs[] = $id_doc;
			$db->executeUpdate("INSERT INTO rb_com_utenti_thread (thread, utente) VALUES ({$tid}, {$id_doc})");
		}
		// sostegno
		$res_sos = $db->executeQuery("SELECT DISTINCT(docente) AS doc FROM rb_assegnazione_sostegno WHERE anno = {$y} AND classe = {$row['id_classe']}");
		while ($r = $res_sos->fetch_assoc()) {
			$id_doc = $db->executeCount("SELECT id FROM rb_com_users WHERE uid = {$r['doc']} AND table_name = 'rb_utenti'");
			$docs[] = $id_doc;
			$db->executeUpdate("INSERT INTO rb_com_utenti_thread (thread, utente) VALUES ({$tid}, {$id_doc})");
		}

		/**
		 * genitori

		$group_name = "Genitori ".$row['anno_corso'].$row['sezione']." SP";
		$tid = $db->executeUpdate("INSERT INTO rb_com_threads (owner, last_message, system, name, type, creation) VALUES (1, NULL, 1, '{$group_name}', 'G', NOW())");
		$db->executeUpdate("INSERT INTO rb_com_system_threads (tid, anno, classe, genitori) VALUES ({$tid}, {$y}, {$row['id_classe']}, 1)");
		reset ($stds);
		$parents = array();
		foreach ($stds as $d) {
			$id_parent = $db->executeQuery("SELECT id FROM rb_genitori_figli, rb_com_users WHERE uid = id_genitore AND table_name = 'rb_utenti' AND id_alunno = {$d}");
			while ($rp = $id_parent->fetch_assoc()) {
				$p = $rp['id'];
				$parents[] = $p;
				$db->executeUpdate("INSERT INTO rb_com_utenti_thread (thread, utente) VALUES ({$tid}, {$p})");
			}
		}
		 * */
		/**
		 * docenti e genitori

		$group_name = "Docenti e genitori ".$row['anno_corso'].$row['sezione']." SP";
		$tid = $db->executeUpdate("INSERT INTO rb_com_threads (owner, last_message, system, name, type, creation) VALUES (1, NULL, 1, '{$group_name}', 'DG', NOW())");
		$db->executeUpdate("INSERT INTO rb_com_system_threads (tid, anno, classe, docenti, genitori) VALUES ({$tid}, {$y}, {$row['id_classe']}, 1, 1)");
		foreach ($docs as $d) {
			$db->executeUpdate("INSERT INTO rb_com_utenti_thread (thread, utente) VALUES ({$tid}, {$d})");
		}
		foreach ($parents as $p) {
			$db->executeUpdate("INSERT INTO rb_com_utenti_thread (thread, utente) VALUES ({$tid}, {$p})");
		}
		 * */
	}
} catch (MySQLException $ex) {
	$response['status'] = "kosql";
	$response['dbg_message'] = $ex->getQuery()."  ".$ex->getMessage();
	$response['message'] = "Errore: riprovare tra qualche minuto";
	$res = json_encode($response);
	echo $res;
	//exit;
}

$res = json_encode($response);
echo $res;
exit;
