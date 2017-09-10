<?php
switch ($_SESSION['__mod_area__']) {
	case "teachers":
		$lb = "docenti";
		break;
	case "alunni":
		$lb = "studenti";
		break;
	case "manager":
		$lb = "segreteria";
		break;
	case "genitori":
	case "parents":
		$lb = "genitori";
		break;
    default:
        $lb = $_SESSION['__mod_area__'];
}
?>
<nav id="navigation">
	<div id="head_label">
		<img src="<?php echo $_SESSION['__path_to_root__'] ?>images/ic_navigation_drawer3.png" id="open_drawer" style="float: left; position: relative; top: 18px" />
		<p id="drawer_label" style="margin-top: 17px; vertical-align: top; font-weight: bold; ; margin-left: 10px; float: left; color: white"><?php echo $drawer_label ?></p>
	</div>
	<div class="nav_div" style="float: right; margin-right: 50px; position: relative; top: 20px; text-align: right">Area messaggistica::<span id="navlabel"><?php echo $lb ?></span></div>
	<div class="nav_div" style="clear: both"></div>
</nav>
