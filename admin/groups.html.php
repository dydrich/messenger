<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<title>Elenco gruppi</title>
	<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,400italic,600,600italic,700,700italic,900,200' rel='stylesheet' type='text/css'>
	<link href="../../../css/general.css" rel="stylesheet" />
    <link href="../../../font-awesome/css/font-awesome.min.css" rel="stylesheet" />
	<link href="../../../css/site_themes/<?php echo getTheme() ?>/reg.css" rel="stylesheet" />
	<link rel="stylesheet" href="../../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" />
	<script type="text/javascript" src="../../../js/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="../../../js/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="../../../js/page.js"></script>
	<script type="text/javascript">
		$(function(){
			load_jalert();
			setOverlayEvent();
            $('#top_btn').click(function() {
                $('html,body').animate({
                    scrollTop: 0
                }, 700);
                return false;
            });

            var amountScrolled = 200;

            $(window).scroll(function() {
                if ($(window).scrollTop() > amountScrolled) {
                    $('#top_btn').fadeIn('slow');
                } else {
                   $('#top_btn').fadeOut('slow');
                }
            });
		});
	</script>
</head>
<body>
<?php include $_SESSION['header'] ?>
<?php include "../navigation.php" ?>
<div id="main">
	<div id="right_col">
		<?php include "menu.php" ?>
	</div>
	<div id="left_col">
        <div style="position: absolute; top: 75px; left: 53%; margin-bottom: -5px" class="rb_button">
            <a href="new_group.php" id="new_group">
                <i class="fa fa-plus" style="color: black; font-size: 1.6em; padding: 10px 0 0 12px"></i>
            </a>
        </div>
		<table class="admin_table">
			<thead>
			<tr>
				<td style="width: 30%" class="_bold">Nome</td>
				<td style="width: 40%" class="_center _bold">Amministratori</td>
				<td style="width: 30%" class="_center _bold">Membri</td>
			</tr>
			<tr class="admin_row_before_text">
				<td colspan="3"></td>
			</tr>
			</thead>
			<tbody>
			<?php
			$x = 1;

			foreach ($threads as $th) {
                $th->restoreThread(new MySQLDataLoader($db));
                $admins = $th->getAdmins();
                $str_admins = 'Admin';
                if (count($th->getAdmins()) > 0) {
                    $str_admins = '';
                    $names = [];
                    foreach ($th->getAdmins() as $user) {
                        $names[] = $user->getFullName(0);
                    }
                    asort($names);
                    $str_admins = join(', ', $names);
                }
                $us_array = array();

                ?>
					<tr style="height: 25px" id="row_<?php echo $th->getTid() ?>">
						<td style="padding-left: 4px; ">
							<a href="group.php?tid=<?php echo $th->getTid() ?>" class="normal"><?php echo $th->getName() ?></a>
						</td>
						<td><?php echo $str_admins ?></td>
						<td class="_center"><?php echo count($th->getUsers()); ?></td>
					</tr>
				<?php
			}
			?>
			</tbody>
			<tfoot>
			</tfoot>
		</table>
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
<a href="#" id="top_btn" class="rb_button float_button top_button">
    <i class="fa fa-arrow-up"></i>
</a>
</body>
</html>
