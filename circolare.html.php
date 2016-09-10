<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: circolari</title>
	<link rel="stylesheet" href="../../font-awesome/css/font-awesome.min.css">
	<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,400italic,600,600italic,700,700italic,900,200' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/reg.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/communication.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" />
	<script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="../../js/jquery.show_char_limit-1.2.0.js"></script>
	<script type="text/javascript" src="../../js/page.js"></script>
	<script type="text/javascript">
		$(function(){
			load_jalert();
			setOverlayEvent();
			$('#data').datepicker({
				dateFormat: "dd/mm/yy"
			});
		});


		var registra = function(){
			$.ajax({
				type: "POST",
				url: "circ_manager.php",
				data: $('#my_form').serialize(true),
				dataType: 'json',
				error: function(data, status, errore) {
					j_alert("error", "Si è verificato un errore di rete");
					return false;
				},
				succes: function(result) {

				},
				complete: function(data, status){
					r = data.responseText;
					var json = $.parseJSON(r);
					if(json.status == "kosql"){
						j_alert("error", "Errore SQL. \nQuery: "+json.query+"\nErrore: "+json.message);
		            }
					else {
						j_alert("alert", json.message);
						setTimeout(function(){
							if ($('#idc').val() == 0) {
								document.location.href = "circolare.php?idc=0";
							}
							else {
								document.location.href = "circolari.php";
							}
						}, 1000);
					}
				}
			});
		};


		var del_file = function(){
			if($('#server_file').val() == ""){
				j_alert("error", "Non hai ancora fatto l'upload di nessun file");
				return false;
			}
			//var url = "../../admin/adm_docs/document_manager.php";
			var url = "../../modules/documents/document_manager.php";

			$.ajax({
				type: "POST",
				url: url,
				data: {server_file: $('#server_file').val(), action: "4", tipo: "files", doc_type: "document"},
				dataType: 'json',
				error: function() {
					j_alert("error", "Errore di trasmissione dei dati");
				},
				succes: function() {

				},
				complete: function(data){
					r = data.responseText;
					if(r == "null"){
						return false;
					}
					var json = $.parseJSON(r);
					if (json.status == "kosql"){
						j_alert("error", json.message);
						console.log(json.dbg_message);
					}
					else {
						j_alert("alert", "File cancellato");
						$('#aframe').attr('src', '../../modules/documents/upload_manager.php?upl_type=document&area=teachers&tipo=files');
						$('#server_file').val("");
					}
				}
		    });
		};

		var loading = function(vara){
			background_process("Attendere il caricamento del file", 30, false);
		};

		var loading_done = function(r){
			loaded("Caricamento completato");
			$('#del_upl').show();
			$('#server_file').val(r);
		};
	</script>
</head>
<body>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/header.php" ?>
<?php include "navigation.php" ?>
<div id="main">
<div id="right_col">
<?php include "menu.php" ?>
</div>
<div id="left_col">
	<div id="not1" class="notification"></div>
 	<form id="my_form" method="post" class="reg_form" action="../../admin/adm_docs/circ_manager.php" style="border-radius: 10px; margin-top: 20px; text-align: left; width: 80%; margin-left: auto; margin-right: auto">
	<table style="width: 90%; margin-left: auto; margin-right: auto; margin-top: 30px; margin-bottom: 20px">
		<tr>			
			<td style='width: 20%' id='lab1'>Circolare n. *</td>
			<td style="width: 80%">
				<input style='width: 95%; text-align: right' name='num_c' id='num_c' value="<?php if(isset($circ)) echo $circ['progressivo'] ?>" />
			</td>
		</tr>
		<tr>
			<td style='width: 20%' id='lab2' class='label'>Protocollo </td>
			<td style="width: 80%">
				<input style='width: 95%; text-align: right' name='prot' id='prot' value="<?php if(isset($circ)) echo $circ['protocollo'] ?>" />
			</td>
		</tr>
		<tr>
			<td style='width: 20%' id='lab3' class='label'>Data *</td>
			<td style="width: 80%">
				<input style='width: 95%; text-align: right' name='data' id='data' value="<?php if(isset($circ)) echo format_date($circ['data_circolare'], SQL_DATE_STYLE, IT_DATE_STYLE, "/"); ?>" />
			</td>	
		</tr>
		<tr>
			<td style='width: 20%' id='lab4' class='label'>Oggetto *</td>
			<td style="width: 80%">
				<input style='width: 95%' name='obj' id='obj' value="<?php if(isset($circ)) echo $circ['oggetto'] ?>" />
			</td>
		</tr>
		<tr>
			<td style='width: 20%' id='lab5' class='label'>Destinatari *</td>
			<td style="width: 80%">
				<textarea style='width: 95%; height: 50px' name='dest' id='dest'><?php if(isset($circ)) echo $circ['destinatari'] ?></textarea>
			</td>
		</tr>
		<tr>
			<td style='width: 20%' id='lab6' class='label'>Testo *</td>
			<td style="width: 80%">
				<textarea style='width: 95%; height: 100px' name='txt' id='txt' ><?php if(isset($circ)) echo $circ['testo'] ?></textarea>
			</td>
		</tr>
		<tr>
			<td style='width: 20%' class='label'>Allegato</td>
			<td style="width: 80%">
			<?php if (isset($circ) && $allegato != ""){ ?>
				<input class="form_input" type="text" name="fname" id="fname" style="width: 95%" readonly value="<?php print $allegato ?>"/>
			<?php } else if (!isset($circ)){ ?>
				<div id="iframe"><iframe src="../../modules/documents/upload_manager.php?upl_type=document&area=teachers&tipo=allegati" style="border: none; width: 95%;  margin: 0px; height: 80px" id="aframe"></iframe></div>
				<a href="#" onclick="del_file()" id="del_upl" style="float: right; padding-top: 45px; padding-right: 20px; display: none; text-decoration: none">Annulla upload</a>
			<?php } else { ?>
				Nessun file allegato
			<?php } ?>
			</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;
				<input type="hidden" name="action" id="action" value="<?php echo $action ?>" />
    			<input type="hidden" name="idc" id="idc" value="<?php echo $idc ?>" />
    			<input type="hidden" name="server_file" id="server_file" />
			</td> 
		</tr>
		<tr>
			<td colspan="2" style="text-align: right; margin-right: 50px">
				<a href="#" onclick="registra()" style="text-decoration: none; text-transform: uppercase">Registra</a>
			</td> 
		</tr>
	</table>
	</form>
</div>
<p class="spacer"></p>	
</div>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/footer.php" ?>
<div id="drawer" class="drawer" style="display: none; position: absolute">
	<div style="width: 100%; height: 430px">
		<div class="drawer_link"><a href="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>intranet/<?php echo $_SESSION['__mod_area__'] ?>/index.php"><img src="../../images/6.png" style="margin-right: 10px; position: relative; top: 5%" />Home</a></div>
		<div class="drawer_link"><a href="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>intranet/<?php echo $_SESSION['__mod_area__'] ?>/profile.php"><img src="../../images/33.png" style="margin-right: 10px; position: relative; top: 5%" />Profilo</a></div>
		<?php if (!$_SESSION['__user__'] instanceof ParentBean) : ?>
			<div class="drawer_link"><a href="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>modules/documents/load_module.php?module=docs&area=<?php echo $_SESSION['__mod_area__'] ?>"><img src="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>images/11.png" style="margin-right: 10px; position: relative; top: 5%" />Documenti</a></div>
		<?php endif; ?>
		<?php if(is_installed("com")){ ?>
			<div class="drawer_link"><a href="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>modules/communication/load_module.php?module=com&area=<?php echo $_SESSION['__mod_area__'] ?>"><img src="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>images/57.png" style="margin-right: 10px; position: relative; top: 5%" />Comunicazioni</a></div>
		<?php } ?>
	</div>
	<?php if (isset($_SESSION['__sudoer__'])): ?>
		<div class="drawer_lastlink"><a href="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>admin/sudo_manager.php?action=back"><img src="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>images/14.png" style="margin-right: 10px; position: relative; top: 5%" />DeSuDo</a></div>
	<?php endif; ?>
	<div class="drawer_lastlink"><a href="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>shared/do_logout.php"><img src="<?php echo $_SESSION['__modules__']['com']['path_to_root'] ?>images/51.png" style="margin-right: 10px; position: relative; top: 5%" />Logout</a></div>
</div>
</body>
</html>
