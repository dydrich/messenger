<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: circolare</title>
	<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,400italic,600,600italic,700,700italic,900,200' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/reg.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/communication.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" /><script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="../../js/page.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
	<script>
		$(function(){
			load_jalert();
			setOverlayEvent();
		});
	var dwl = function (id, url){
		document.location.href = url;
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
	<div style="width: 75%; margin: 15px auto; padding: 15px; border: 1px solid; overflow: hidden">
		<div style="width: 40%; margin-left: 20px; float: left">Circolare n. <span class="_bold"><?php echo $circ['progressivo'] ?> del <?php echo format_date($circ['data_circolare'], SQL_DATE_STYLE, IT_DATE_STYLE, "/") ?></span></div>
		<div style="width: 40%; margin-left: 20px">Protocollo <span class="_bold"><?php echo $circ['protocollo'] ?></span></div>
		<div style="width: 40%; margin-right: 30px; float: right; text-align: left; margin-bottom: 20px; margin-top: 10px">Destinatari:
			<ul>
				<?php
				$dests = preg_split("/\n/", $circ['destinatari']);
				foreach ($dests as $d){
				?>
				<li style=""><?php echo $d ?>
					<?php } ?>
			</ul>
		</div>
		<div style="width: 90%; margin-left: 20px; margin-top: 20px; padding: 0; clear: both">Oggetto: <span class="_bold"><?php echo $circ['oggetto'] ?></span></div>
		<div style="width: 90%; margin-left: 20px; margin-top: 25px; line-height: 18px"><?php echo text2html($circ['testo']) ?></div>
		<div style="width: 90%; margin-left: 20px; margin-top: 40px; line-height: 18px"><?php if (isset($circ['allegato'])): ?>Allegato: <a class="dwl" href="#" onclick="dwl(<?php echo $circ['id_allegato'] ?>, '../../modules/documents/download_manager.php?doc=allegato&id=<?php echo $circ['id_allegato'] ?>')"><?php echo $circ['allegato'] ?></a><?php endif; ?></div>
		<p class="spacer"></p>
	</div>
	
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
