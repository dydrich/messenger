<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<title>Elenco gruppi</title>
	<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,400italic,600,600italic,700,700italic,900,200' rel='stylesheet' type='text/css'>
	<link href="../../../css/general.css" rel="stylesheet" />
	<link href="../../../font-awesome/css/font-awesome.min.css" rel="stylesheet" />
	<link href="../../../css/site_themes/<?php echo getTheme() ?>/reg.css" rel="stylesheet" />
	<link href="../../../css/site_themes/<?php echo getTheme() ?>/communication.css" rel="stylesheet"  type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" />
	<script type="text/javascript" src="../../../js/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="../../../js/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="../../../js/jquery.jeditable.mini.js"></script>
	<script type="text/javascript" src="../../../js/page.js"></script>
	<script type="text/javascript">
        var _user = 0;
        var _user_group = "";
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

			$('.edit').editable('group_manager.php?action=env', {
				indicator : 'Saving...',
				tooltip   : 'Click to edit...'
			});

            $("#mytarget").autocomplete({
                source: "../get_users.php",
                minLength: 2,
                select: function(event, ui){
                    uid = ui.item.uniqID;
                    tp = ui.item.type;
                    if (tp == 'school') {
                        _user_group = 'teachers';
                    }
                    else {
                        _user_group = tp+"s";
                    }
                    $('#targetID').val(uid);
                }
            });

            $('#add_selected_user').on('click', function(event) {
               event.preventDefault();
                manage_user("add_user", $('#targetID').val(), '');
            });

            $('#trash').on('click', function(event) {
                event.preventDefault();
                delete_group();
            });

            var delete_group = function() {
                if (!confirm('Eliminare il gruppo?')) {
                    return false;
                }
                $.ajax({
                    type: "POST",
                    url: "group_manager.php",
                    data: {action: 'delete_group'},
                    error: function() {

                    },
                    succes: function(data) {
                        alert(data);

                    },
                    complete: function(data){
                        //dati = data.responseText.split("|");
                        r = data.responseText;
                        if(r == ""){
                            return false;
                        }
                        var json = $.parseJSON(r);
                        if (json.status == "kosql"){
                            j_alert("error", json.message);
                            console.log(json.dbg_message);
                        }
                        else {
                            j_alert("alert", 'Gruppo eliminato');
                            window.setTimeout(function() {
                                document.location.href = 'groups.php';
                            }, 2000);
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
		<div style="position: absolute; top: 75px; left: 53%; margin-bottom: -5px" class="rb_button">
			<a href="#" id="trash">
				<i class="fa fa-trash" style="color: black; font-size: 1.7em; padding: 9px 0 0 12px"></i>
			</a>
		</div>
		<fieldset style="width: 95%; border-radius: 2px; margin: auto">
			<legend>Dati generali</legend>
			<div style="width: 100%; display: -webkit-flex; display: flex; flex-flow: row nowrap; -webkit-flex-flow: row nowrap;">
				<div style="-webkit-flex: 2; flex: 2">Tipologia</div>
				<select id="th_type" style="-webkit-flex: 3; flex: 3">
					<option value="0">.</option>
					<?php
					while($row = $res_types->fetch_array()) {
					?>
					<option <?php if($row['id_tipo'] == $thread->getType()) echo "selected" ?> value="<?php echo $row['id_tipo'] ?>">
						<?php echo $row['descrizione'] ?>
					</option>
					<?php
					}
					?>
				</select>
				<div style="-webkit-flex: 1.5; flex: 1.5"></div>
				<div style="-webkit-flex: 2; flex: 2">Attivo</div>
				<div style="-webkit-flex: 1; flex: 1" id="active_label">
					<?php
					$active = 0;
					if ($thread->isActive()) {
						echo "SI";
						$active = 1;
					}
					else {
						echo 'NO';
					}
					?>
				</div>
				<div style="-webkit-flex: 2; flex: 2">
					<a href="#" data-active="<?php echo $active ?>" id="active_th" title="Un gruppo non attivo non permette di inviare messaggi">
					<?php
					if ($active) {
						echo "Disattiva il gruppo";
					}
					else {
						echo 'Attiva il gruppo';
					}
					?>
					</a>
				</div>
			</div>
			<div style="width: 100%; margin-top: 15px; display: -webkit-flex; display: flex; flex-flow: row nowrap; -webkit-flex-flow: row nowrap;">
				<div style="-webkit-flex: 2; flex: 2">Nome</div>
				<div style="-webkit-flex: 3; flex: 3" id="name" class="edit"><?php echo $thread->getName() ?></div>
				<div style="-webkit-flex: 1.5; flex: 1.5"></div>
				<div style="-webkit-flex: 2; flex: 2">Bloccato</div>
				<div style="-webkit-flex: 1; flex: 1" id="block_label">
					<?php
					$blocked = 0;
					if ($thread->isBlocked()) {
						$blocked = 1;
						echo "SI";
					}
					else {
						echo 'NO';
					}
					?>
				</div>
				<div style="-webkit-flex: 2; flex: 2">
					<a href="#" id="block_th" data-blocked="<?php echo $blocked ?>" title="Un gruppo bloccato non permette all'amministratore di modificare
					in alcun modo gli utenti">
					<?php
					if ($blocked) {
						echo "Sblocca il gruppo";
					}
					else {
						echo 'Blocca il gruppo';
					}
					?>
					</a>
				</div>
			</div>
            <div style="width: 100%; margin-top: 15px; display: -webkit-flex; display: flex; flex-flow: row nowrap; -webkit-flex-flow: row nowrap;">
                <div style="-webkit-flex: 2; flex: 2">Messaggi</div>
                <div style="-webkit-flex: 3; flex: 3"><?php echo $thread->getMessagesCount() ?></div>
                <div style="-webkit-flex: 1.5; flex: 1.5"></div>
                <div style="-webkit-flex: 2; flex: 2">Ultimo messaggio</div>
                <div style="-webkit-flex: 3; flex: 3" id="block_label">
                    <?php
                    $last = $thread->getLastMessage();
                    if($last) {
                        $ts = $last->getSendTimestamp();
                        $last_dt = new DateTime($ts);
                        echo RBUtilities::getDateTimeDistance($last_dt);
                    }
                    else {
                        echo "mai";
                    }
                    ?>
                </div>
            </div>
		</fieldset>
		<div style="width: 95%; margin: 30px auto 0 auto" class="">
			<p class="_bold accent_decoration">Amministratori</p>
			<p id="adm_list">
			<?php
			if (count($thread->getAdmins()) < 1) {
				echo "Nessuno";
			}
			else {
				$names = [];
				foreach ($thread->getAdmins() as $user) {
					$names[$user->getUniqID()] = $user->getFullName(0);
				}
				asort($names);
				$str = '';
				foreach ($names as $id => $name) {
					$str .= "<a href='#' class='admin' data-user='".$id."'>".$name."</a>, ";
				}
				echo substr($str, 0, (strlen($str) - 2));
			}
			?>
			</p>
		</div>
		<div style="width: 95%; margin: 30px auto 0 auto; line-height: 25px" class="">
			<p class="_bold accent_decoration">Utenti (<span id="counter"><?php echo count($thread->getUsers()) ?></span>)</p>
            <div id="users_list">
			<?php
            if (count($thread->getUsers()) > 0) {
                $names = [];
                foreach ($thread->getUsers() as $user) {
                    $names[$user->getUniqID()] = $user->getFullName(0);
                }
                asort($names);
                $str = '';
                foreach ($names as $id => $name) {
                    $str .= "<a href='#' class='user' data-user='" . $id . "'>" . $name . "</a>, ";
                }
                echo substr($str, 0, (strlen($str) - 2));
            }
			?>
            </div>
		</div>
        <div style="width: 95%; margin: 30px auto 0 auto" class="">
            <p class="_bold accent_decoration">Aggiungi utenti</p>
            <?php if($perms['class'] != -1 && $perms['students'] != -1): ?>
            <div  id="st_list">
                <p>Studenti della classe: <?php if (count($add_students) == 0) echo 'inseriti'; else echo join(', ', $add_students); ?></p>
            </div>
            <?php endif; ?>
            <?php if($perms['teachers'] != -1 && $perms['class'] != -1): ?>
                <div  id="te_list">
                    <p>Docenti del CDC: <?php if (count($add_teachers) == 0) echo 'inseriti'; else echo join(', ', $add_teachers); ?></p>
                </div>
            <?php endif; ?>
            <?php if($perms['teachers'] != -1 && $perms['class'] == -1): ?>
                <div  id="te_list_only">
                    <p><?php echo $label ?>: <?php if (count($add_teachers) == 0) echo 'inseriti'; else echo join(', ', $add_teachers); ?></p>
                </div>
            <?php endif; ?>
            <?php if($perms['parents'] != -1): ?>
                <div  id="pa_list">
                    <p>Genitori: <?php if (count($add_parents) == 0) echo 'inseriti'; else echo join(', ', $add_parents); ?></p>
                </div>
            <?php endif; ?>
            <p style="margin: 0">Seleziona</p>
            <div style="width: 500px">
                <input type="text" name="mytarget" id="mytarget" style="width: 350px" placeholder="Scrivi almeno 2 lettere del cognome" />
                <input type="hidden" name="targetID" id="targetID" />
                <a href="#" id="add_selected_user" style="margin-left: 15px">Aggiungi</a>
            </div>
        </div>
		<script>
			$('.admin').on('mouseenter', function(event) {
				$(this).addClass('material_link');
			}).on('mouseleave', function(event) {
				$(this).removeClass('material_link');
			}).on('click', function(event) {
				event.preventDefault();
				if (!confirm('Rimouovere i privilegi di amministratore di '+$(this).text()+'?')) {
					return false;
				}
				admin('remove_admin', $(this).data('user'));
			});

            $('.to_add').on('mouseenter', function(event) {
                $(this).addClass('material_link');
            }).on('mouseleave', function(event) {
                $(this).removeClass('material_link');
            }).on('click', function(event) {
                event.preventDefault();
                manage_user("add_user", $(this).data('user'), $(this).data('group'));
            });

			$('#th_type').on('change', function (event) {
				upd_type($(this).val());
			});

			var admin = function (action, user) {
                $('#context_menu').slideUp(300);
				var url = 'group_manager.php';
				if (user == 0) {
					user = _user;
				}
				$.ajax({
					type: "POST",
					url: url,
					data: {action: action, user: user},
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
							$('#adm_list').html(json.name);
						}
					}
				});
			};

            var manage_user = function (action, user, group) {
                $('#context_menu').slideUp(300);
                if (user == 0) {
                    user = _user;
                }
                if (group == '') {
                    group = _user_group;
                }
                var url = 'group_manager.php';
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {action: action, user: user, group: group},
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
                            $('#users_list').html(json.users);
                            $('#counter').text(json.counter);
                            if (json.st_list) {
                                $('#st_list').html(json.st_list);
                            }
                            if (json.te_list) {
                                $('#te_list').html(json.te_list);
                            }
                            if (json.te_list_only) {
                                $('#te_list_only').html(json.te_list_only);
                            }
                            if (json.pa_list) {
                                $('#pa_list').html(json.pa_list);
                            }

                            $('.to_add').on('mouseenter', function(event) {
                                $(this).addClass('material_link');
                            }).on('mouseleave', function(event) {
                                $(this).removeClass('material_link');
                            }).on('click', function(event) {
                                event.preventDefault();
                                manage_user("add_user", $(this).data('user'), $(this).data('group'));
                            });
                            $('.user').on('mouseenter', function(event) {
                                $(this).addClass('material_link');
                                if ($('#context_menu').is(":visible")) {
                                    $('#context_menu').slideUp(300);
                                    return false;
                                }
                            }).on('mouseleave', function(event) {
                                $(this).removeClass('material_link');
                            }).on('click', function(event) {
                                event.preventDefault();
                                $('#sel_us').text($(this).text());
                                var offset = $(this).offset();
                                offset.top = offset.top + $(this).height();
                                var uid = $(this).data("user");
                                _user_group = $(this).data('group');
                                show_menu(event, uid, offset);
                            });
                            $('#mytarget').val('');
                        }
                    }
                });
            };

            var delete_group = function() {
                if (!confirm("Cancellare il gruppo e tutti i messaggi relativi?")) {
                    return false;
                }
                var url = 'group_manager.php';
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {action: 'delete_group'},
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
                    }
                });
            };

			var upd_type = function (type) {
				var url = 'group_manager.php';
				$.ajax({
					type: "POST",
					url: url,
					data: {action: 'update_type', type: type},
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
					}
				});
			};

			$('.user').on('mouseenter', function(event) {
				$(this).addClass('material_link');
				if ($('#context_menu').is(":visible")) {
					$('#context_menu').slideUp(300);
					return false;
				}
			}).on('mouseleave', function(event) {
				$(this).removeClass('material_link');
			}).on('click', function(event) {
				event.preventDefault();
				$('#sel_us').text($(this).text());
				var offset = $(this).offset();
				offset.top = offset.top + $(this).height();
				var uid = $(this).data("user");
                _user_group = $(this).data('group');
				show_menu(event, uid, offset);
			});
			var show_menu = function(e, _stid, offset){
				if ($('#context_menu').is(":visible")) {
					$('#context_menu').slideUp(300);
					return false;
				}
				$('#context_menu').css({'top': offset.top+"px"}).css({'left': offset.left+"px"}).slideDown(500);
				_user = _stid;
				return false;
			};

			$('#block_th').on('click', function (event) {
				event.preventDefault();
				bparam = 0;
				if ($(this).data('blocked') == 0) {
					bparam = 1;
				}
				block_thread(bparam);
			});

			$('#active_th').on('click', function (event) {
				event.preventDefault();
				aparam = 0;
				if ($(this).data('active') == 0) {
					aparam = 1;
				}
				active_thread(aparam);
			});

			var block_thread = function (param) {
				var url = 'group_manager.php';
				$.ajax({
					type: "POST",
					url: url,
					data: {action: 'block', param: param},
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
							$('#block_label').text(json.label);
							$('#block_th').text(json.link).data('blocked', param);
						}
					}
				});
			};

			var active_thread = function (param) {
				var url = 'group_manager.php';
				$.ajax({
					type: "POST",
					url: url,
					data: {action: 'activate', param: param},
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
							$('#active_label').text(json.label);
							$('#active_th').text(json.link).data('active', param);
						}
					}
				});
			};
		</script>
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
<!-- menu contestuale -->
<div id="context_menu" style="position: absolute; width: 160px; height: 60px; display: none; line-height: 18px" class="context_menu">
	<span id="sel_us" class="_bold"></span><br />
	<a style="font-weight: normal" href="#" onclick="admin('add_admin', 0)">Rendi amministratore</a><br />
	<a style="font-weight: normal" href="#" onclick="manage_user('remove_user', 0, '')">Cancella dal gruppo</a><br />
</div>
<!-- fine menu contestuale -->
</body>
</html>
