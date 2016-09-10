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
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" /><script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="../../js/page.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript">
		$(function(){
			load_jalert();
			setOverlayEvent();
		});
        $('#top_btn').click(function() {
            $('html,body').animate({
                scrollTop: 0
            }, 700);
            return false;
        });

        var amountScrolled = 200;

        $(window).scroll(function() {
            if ($(window).scrollTop() > amountScrolled) {
                $('#plus_btn').fadeOut('slow');
                $('#float_btn').fadeIn('slow');
                $('#top_btn').fadeIn('slow');
            } else {
                $('#float_btn').fadeOut('slow');
                $('#plus_btn').fadeIn();
                $('#top_btn').fadeOut('slow');
            }
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
		<div id="not1" class="notification"></div>
			<div class="card_container">
 	    <?php 
 	    if($result->num_rows < 1){
 	    ?>
 	    <div style="width: 90%; margin: auto; font-weight: bold; font-size: 1.1em; text-align: center">Nessuna circolare presente</div>
 	    <?php 
 	    }
 	    else{
			while ($circolare = $result->fetch_assoc()){
				$sel_read = "SELECT data_lettura FROM rb_com_lettura_circolari WHERE id_circolare = {$circolare['id_circolare']} AND docente = {$_SESSION['__user__']->getUId()}";
				$read = $db->executeCount($sel_read);
 	    ?>
				<a href="leggi_circolare.php?idc=<?php echo $circolare['id_circolare']; if ($read == null) echo "&read=1"  ?>">
					<div class="card" id="row_<?php echo $circolare['id_circolare'] ?>" style="<?php if ($read != null) echo " font-weight: normal !important" ?>">
						<div class="card_title" style="<?php if ($read != null) echo " font-weight: normal !important" ?>">
							<?php echo truncateString($circolare['oggetto'], 90) ?>
						</div>
						<div class="card_varcontent" style="overflow: hidden">
							<div class="card_row">
								Circolare n. <?php echo $circolare['progressivo'] ?> del <?php echo format_date($circolare['data_circolare'], SQL_DATE_STYLE, IT_DATE_STYLE, "/") ?>
							</div>
							<div class="minicard">
								Inserita da <?php echo $circolare['nome']." ".$circolare['cognome'] ?>
							</div>
							<div class="minicard" style="margin-left: 7.5%">
								Protocollo: <?php echo $circolare['protocollo'] ?>
							</div>
						</div>
					</div>
				</a>
 	    <?php
 	    	}
 	    ?>

        <?php
 	    }
 	    ?>
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
<a href="#" id="top_btn" class="rb_button float_button top_button">
    <i class="fa fa-arrow-up"></i>
</a>
</body>
</html>
