<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<title>Nuovo gruppo</title>
	<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,400italic,600,600italic,700,700italic,900,200' rel='stylesheet' type='text/css'>
	<link href="../../../css/general.css" rel="stylesheet" />
	<link href="../../../font-awesome/css/font-awesome.min.css" rel="stylesheet" />
	<link href="../../../css/site_themes/<?php echo getTheme() ?>/reg.css" rel="stylesheet" />
	<link href="../../../css/site_themes/<?php echo getTheme() ?>/communication.css" rel="stylesheet"  type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" />
	<script type="text/javascript" src="../../../js/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="../../../js/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="../../../js/page.js"></script>
	<script type="text/javascript">
		var _user = 0;
		var _user_group = "";
		$(function(){
			load_jalert();
			setOverlayEvent();

			$('#register').on('click', function (event) {
				event.preventDefault();
				save_data();
			});

			var save_data = function () {
				var url = 'group_manager.php';

				var error = [];
				var idx = 1;
				var t = $('#th_type').val();
				if (t == 0) {
					error.push(idx+'. Scegli una tipologia');
					idx++;
				}
				var n = trim($('#th_name').val());
				if (n == '') {
					error.push(idx+'. Scegli un nome');
					idx++;
				}
				if (error.length > 0) {
					var err = error.join("<br />");
					j_alert("error", err);
					return false;
				}

				$.ajax({
					type: "POST",
					url: url,
					data: {action: 'new', type: t, nm: n},
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
							document.location.href='group.php?tid='+json.tid;
						}
					}
				});
			};
		});
	</script>
	<style>
		form {
			margin: 0;
			padding: 0;
			border: 0
		}
	</style>
</head>
<body>
<?php include $_SESSION['header'] ?>
<?php include "../navigation.php" ?>
<div id="main">
	<div id="right_col">
		<?php include "menu.php" ?>
	</div>
	<div id="left_col">
		<fieldset style="width: 80%; border-radius: 2px; margin: auto">
			<legend>Dati generali</legend>
			<div style="width: 80%; display: -webkit-flex; display: flex; flex-flow: row nowrap; -webkit-flex-flow: row nowrap;">
				<label for="th_type" style="-webkit-flex: 2; flex: 2">Tipologia</label>
				<select id="th_type" style="-webkit-flex: 3; flex: 3">
					<option value="0">.</option>
					<?php
					while($row = $res_types->fetch_array()) {
						?>
						<option value="<?php echo $row['id_tipo'] ?>">
							<?php echo $row['descrizione'] ?>
						</option>
						<?php
					}
					?>
				</select>
			</div>
			<div style="width: 80%; margin-top: 15px; display: -webkit-flex; display: flex; flex-flow: row nowrap; -webkit-flex-flow: row nowrap;">
				<label for="th_name" style="-webkit-flex: 2; flex: 2">Nome</label>
				<input type="text" style="-webkit-flex: 3; flex: 3" id="th_name" id="th_name" />
			</div>
		</fieldset>
		<div style="width: 80%; margin: 25px auto 0 auto;" class="_right">
			<a href="#" id="register">Registra il gruppo</a>
		</div>
	</div>
	<p class="spacer"></p>
</div>
<?php include $_SESSION['footer'] ?>
<div id="drawer" class="drawer" style="display: none; position: absolute">
	<div style="width: 100%; height: 430px">
		<div class="drawer_link"><a href="../../../index.php"><img src="../../../images/6.png" style="margin-right: 10px; position: relative; top: 5%" />Home</a></div>
		<div class="drawer_link"><a href="../../../admin/index.php"><img src="../../../images/31.png" style="margin-right: 10px; position: relative; top: 5%" />Admin</a></div>
		<div class="drawer_link"><a href="http://www.istitutoiglesiasserraperdosa.it"><img src="../../../images/78.png" style="margin-right: 10px; position: relative; top: 5%" />Home Page Nivola</a></div>
	</div>
	<div class="drawer_lastlink"><a href="../../../shared/do_logout.php"><img src="../../../images/51.png" style="margin-right: 10px; position: relative; top: 5%" />Logout</a></div>
</div>
</body>
</html>
