<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: messaggi</title>
	<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,400italic,600,600italic,700,700italic,900,200' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/reg.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/communication.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" /><script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="../../js/page.js"></script>
<script>
	$(function(){
		load_jalert();
		setOverlayEvent();
		$('#newmsg_lnk').click(function(event){
			event.preventDefault();
			$('#txt').val("");
			$('#message').show(1500);
			$('#newmsg').hide(1500);
			$('#viewlist').show(1500);
			$('#sel_thread').hide(1500);
			window.setTimeout(function(){
				$('#txt').focus();
			}, 1500);
		});
		$('#viewlist_lnk').click(function(event){
			event.preventDefault();
			$('#sel_thread').show(1500);
			$('#message').hide(1500);
			$('#newmsg').show(1500);
			$('#viewlist').hide(1500);
		});
		$('#send_lnk').click(function(event){
			event.preventDefault();
			send_message();
		});
		$('#get_target').click(function(event){
			event.preventDefault();
			$('#targets').show();
		});

	});
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
		<div id="navbar">
			<div id="username">I tuoi gruppi</div>
			<div id="newmsg">
				<a href="#" id="newmsg_lnk"><img src="theme/group.png" style="margin-top: 4px" /></a>
			</div>
			<div id="viewlist">
				<a href="#" id="viewlist_lnk"><img src="theme/view-list-icon.png" style="width: 32px; height: 32px; margin-top: 4px" /></a>
			</div>
		</div>
		<div id="groups">
			<div class="list_header" style="margin-top: 15px">
				<div class="outline_cell wd_40" style="text-align: left"><span style="padding-left: 20px">Gruppo</span></div>
				<div class="outline_cell wd_40" style="text-align: left">Amministratore</div>
				<div class="outline_cell wd_20" style="text-align: left">Iscritti</div>
			</div>
			<table>
				<thead>
				<tbody>
				<?php
				if (isset($threads)) {
					foreach ($threads as $thread) {
						if ($thread->getType() == 'G') {
							$owner = $thread->getOwner();
							if ($owner != null) {
								$owner_name = $owner->getFullName();
							}
							else {
								$owner_name = "Admin";
							}
							?>
							<tr>
								<td style="width: 40%" class="bold_"><a
										href="group.php?tid=<?php echo $thread->getTid() ?>"><?php echo $thread->getName() ?></a>
								</td>
								<td style="width: 40%"><?php echo $owner_name ?></td>
								<td style="width: 40%"><?php echo count($thread->getUsers()) ?> iscritti</td>
							</tr>
						<?php
						}
					}
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
	<p class="spacer"></p>
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
