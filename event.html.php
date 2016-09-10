<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: evento</title>
	<link rel="stylesheet" href="../../font-awesome/css/font-awesome.min.css">
	<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,400italic,600,600italic,700,700italic,900,200' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/reg.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/communication.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" /><script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui-timepicker-addon.js"></script>
	<script type="text/javascript" src="../../js/page.js"></script>
	<script type="text/javascript">

		$(function(){
			load_jalert();
			setOverlayEvent();
			$('#sel3').datetimepicker({
				dateFormat: "dd/mm/yy",
				altField: "#time",
				altFieldTimeOnly: true,
				altTimeFormat: "HH:mm",
				currentText: "Ora",
				closeText: "Chiudi"
			});

			$('#time').timepicker({
				currentText: "Ora",
				closeText: "Chiudi"
			});
		});


		var registra = function(){
			if(trim(document.forms[0].titolo.value) == ""){
				j_alert("error", "Il titolo è obbligatorio.");
				return false;
			}

			$.ajax({
				type: "POST",
				url: "events_manager.php",
				data: $('#my_form').serialize(),
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
					}
				}
			});
		}

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
	<div style="top: -10px; margin-left: 35px; margin-bottom: -10px" class="rb_button">
		<a href="events.php">
			<img src="../../images/47bis.png" style="padding: 12px 0 0 12px" />
		</a>
	</div>
 	<form id="my_form" method="post" action="events_manager.php" style="border: 1px solid #666666; border-radius: 10px; margin-top: 20px; text-align: left; width: 80%; margin-left: auto; margin-right: auto">
	<table style="width: 90%; margin-left: auto; margin-right: auto; margin-top: 30px; margin-bottom: 20px">
		<tr>
            <td style="width: 30%"><label for="titolo">Titolo</label></td>
            <td style="width: 70%">
                <input type="text" id="titolo" autofocus style="width: 350px" value="<?php if(isset($evs)) print $evs['abstract'] ?>" name="titolo" />
            </td>
        </tr>
        <tr>
            <td style="width: 30%"><label for="evento_padre">Evento padre</label></td>
            <td style="width: 70%">
                <select style="width: 350px" class="form_input" name="evento_padre" id="evento_padre">
                	<option value="" selected="selected">Nessuno</option>
                <?php
				while($ev_p = $res_eventi_p->fetch_assoc()){
                ?>
                	<option <?php if(isset($evs) && $ev_p['id_evento'] == $evs['id_padre']) print("selected='selected'") ?> value="<?php print $ev_p['id_evento'] ?>"><?php print $ev_p['abstract'] ?></option>
                <?php 
				} 
				?>
                </select>
            </td>
        </tr>
        <tr>
            <td style="width: 30%"><label for="ordine_di_scuola">Ordine di scuola</label></td>
            <td style="width: 70%">
                <select style="width: 350px" class="form_input" name="ordine_di_scuola" id="ordine_di_scuola">
                	<option value="0" selected="selected">Tutti</option>
                <?php
				foreach ($_SESSION['__school_level__'] as $k => $level){
                ?>
                	<option <?php if(isset($evs['ordine_di_scuola']) && $k == $evs['ordine_di_scuola']) print("selected='selected'") ?> value="<?php print $k ?>"><?php print $level ?></option>
                <?php 
				} 
				?>
                </select>
            </td>
        </tr>
        <tr>
            <td style="width: 30%"><label for="classe">Classe</label></td>
            <td style="width: 70%">
                <select style="width: 350px" name="classe" id="classe">
                	<option value="" selected="selected">Nessuna</option>
                <?php
				while($cls = $res_classi->fetch_assoc()){
                ?>
                	<option <?php if(isset($evs['classe']) && $cls['id_classe'] == $evs['classe']) print("selected='selected'") ?> value="<?php print $cls['id_classe'] ?>"><?php print $cls['cls']." (".$cls['nome'].")" ?></option>
                <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td style="width: 30%"><label for="data_evento">Data e ora evento</label></td>
            <td>
                <input type="text" name="data_evento" style="width: 70px" value="<?php print $my_date ?>" id="sel3" readonly="readonly" />
                <script type="text/javascript">
                
	        	</script>
                <input class="form_input" type="text" name="ora_evento" id="time" style="width: 35px; margin-left: 10px" value="<?php print substr($my_ora, 0, 5) ?>" />
                <label for="pub" style="margin-left: 5px; margin-right: 8px; " class="popup_title">Pubblico</label>
                <input type="checkbox" name="pub" <?php if($pubblico == 1) print "checked='true'" ?> />
                <label for="upr" style="margin-left: 7px; margin-right: 8px; " class="popup_title">Modificabile</label>
                <input type="checkbox" name="upr" <?php if($modificabile == 1) print "checked='true'" ?> />
            </td>
        </tr>
        <tr>
            <td style="width: 30%; vertical-align: middle"><label for="testo">Testo</label></td>
            <td style="width: 70%">
                <textarea style="width: 350px; height: 100px" name="testo" id="testo"><?php if(isset($evs)) print $evs['testo'] ?></textarea>
            </td>
        </tr>
		<tr>
			<td colspan="2">&nbsp;
				<input type="hidden" name="action" id="action" value="<?php echo $action ?>" />
    			<input type="hidden" name="_i" id="_i" value="<?php echo $_i ?>" />
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
