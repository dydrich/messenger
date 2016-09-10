<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php echo $_SESSION['__config__']['intestazione_scuola'] ?>:: messaggi cancellati</title>
	<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,400italic,600,600italic,700,700italic,900,200' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="../../font-awesome/css/font-awesome.min.css">
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
			
			$('.approve').on('click', function (event) {
				event.preventDefault();
				msg_id = $(this).data('msg');
				approve_msg(msg_id);
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
		});
		
		var approve_msg = function(msid){
			//alert($('#targetID').val());
			$.ajax({
				type: "POST",
				url: "controller.php",
				data: {action: 'restore', mid: msid, tid: <?php echo $thread->getTid()?>},
				error: function() {
					
				},
				succes: function(data) {
					alert(data);
				},
				complete: function(data){
					r = data.responseText;
					if(r == ""){
						return false;
					}
					var json = $.parseJSON(r);
					if (json.status == 'ok') {
						j_alert("alert", json.message);
					}
					$('div.card[data-msg='+msid+']').hide(1200);
				}
			});
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
		<div id="msgs" class="card_container" style="margin-top: 20px">
			<?php
			if (isset($reported) && count($reported) > 0){
				foreach ($reported as $k => $msg){
					$reason = null;
					if ($msg->getState() == Message::DELETED_FOR_SPAM) {
						$reason = 'SPAM';
					}
					else {
						$reason = 'Linguaggio non appropriato';
					}
					$from = $msg->getFrom()->getFullName();
			
					?>
					<div class="card" data-msg="<?php echo $msg->getID() ?>" style="<?php if(!$thread->isActive()) echo 'background-color: #EEEEEE' ?>">
						<div class="card_title">
							<div class="thread_user">
								<?php echo $from ?>
							</div>
							<div class="thread_msg_count">
								<?php echo $reason ?>
							</div>
						</div>
						<div class="card_varcontent" style="color: #1E4389; overflow: auto">
							<div style="width: 75%; float: left"><?php echo $msg->getText() ?></div>
							<div style="width: 15%" class="fright">
								<div class="rb_button" style="width: 25px; height: 25px">
									<a href="#" class="approve" data-msg="<?php echo $msg->getID() ?>" title="Approva il messaggio">
										<i class="fa fa-check" style="font-size: 1em; margin-top: 6px; margin-left: 6px; color: black"></i>
									</a>
								</div>
							</div>
						</div>
					</div>
					<?php
				}
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
</body>
</html>
<a href="#" id="top_btn" class="rb_button float_button top_button">
	<i class="fa fa-arrow-up"></i>
</a>
