<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <title>Crea nuovo gruppo</title>
    <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,400italic,600,600italic,700,700italic,900,200' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="../../font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/reg.css" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/communication.css" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" /><script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
    <script type="text/javascript" src="../../js/jquery.show_char_limit-1.2.0.js"></script>
    <script type="text/javascript" src="../../js/jquery.jeditable.mini.js"></script>
    <script type="text/javascript" src="../../js/page.js"></script>
    <script type="text/javascript">
        var _user = 0;
        var _user_group = "";
        var owner = <?php echo $thread->getOwner()->getUniqID() ?>;
        var me = <?php echo $_SESSION['__user__']->getUniqID(); ?>;
        var blocked = <?php echo $thread->isBlocked() ?>;
        $(function(){
            load_jalert();
            setOverlayEvent();

            $('#register').on('click', function (event) {
                event.preventDefault();
                save_data();
            });

            var save_data = function () {
                var url = 'group_manager.php';

                var n = trim($('#th_name').val());
                if (n == '') {
                    j_alert("error", err);
                    return false;
                }

                $.ajax({
                    type: "POST",
                    url: url,
                    data: {action: 'new', nm: n},
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

            $('.edit').editable('group_manager.php?action=env', {
                indicator : 'Saving...',
                tooltip   : 'Click to edit...'
            });

            $("#mytarget").autocomplete({
                source: "get_users.php",
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
<?php include "../../intranet/{$_SESSION['__mod_area__']}/header.php" ?>
<?php include "navigation.php" ?>
<div id="main">
    <div id="right_col">
        <?php include "menu.php" ?>
    </div>
    <div id="left_col">
        <div class="rb_button" style="top: -5px; margin-left: 20px" title="Torna indietro">
            <a href="thread.php">
                <i class="fa fa-reply" style="color: black; font-size: 1.6em; padding: 10px 0 0 12px"></i>
            </a>
        </div>
        <?php if ($_REQUEST['tid'] == 0): ?>
        <fieldset style="width: 80%; border-radius: 2px; margin: auto">
            <legend>Dati generali</legend>
            <div style="width: 80%; margin-top: 15px; display: -webkit-flex; display: flex; flex-flow: row nowrap; -webkit-flex-flow: row nowrap;">
                <label for="th_name" style="-webkit-flex: 2; flex: 2">Nome</label>
                <input type="text" style="-webkit-flex: 3; flex: 3" id="th_name" id="th_name" />
            </div>
        </fieldset>
        <div style="width: 80%; margin: 25px auto 0 auto;" class="_right">
            <a href="#" id="register">Registra il gruppo</a>
        </div>
        <?php endif; ?>
    <?php if ($_REQUEST['tid'] != 0): ?>
        <fieldset style="width: 95%; border-radius: 2px; margin: auto; <?php if(!$thread->isOwner($_SESSION['__user__'])) echo 'display: none' ?>">
            <legend>Dati generali</legend>
            <div style="width: 100%; margin-top: 15px; display: -webkit-flex; display: flex; flex-flow: row nowrap; -webkit-flex-flow: row nowrap;">
                <div style="-webkit-flex: 2; flex: 2">Nome</div>
                <div style="-webkit-flex: 3; flex: 3" id="name" class="edit"><?php echo $thread->getName() ?></div>
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
                <?php if (!$is_system_group){ ?>
                if ($(this).data('user') == owner && owner != me) {
                    j_alert('error', "Impossibile modificare i permessi dell'utente proprietario del gruppo");
                    return false;
                }
                else if (!confirm('Rimuovere i privilegi di amministratore di '+$(this).text()+'?')) {
                    return false;
                }
                admin('remove_admin', $(this).data('user'));
                <?php } else{ ?>
                return false;
                <?php } ?>
            });

            $('.to_add').on('mouseenter', function(event) {
                $(this).addClass('material_link');
            }).on('mouseleave', function(event) {
                $(this).removeClass('material_link');
            }).on('click', function(event) {
                event.preventDefault();
                manage_user("add_user", $(this).data('user'), $(this).data('group'));
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

                if (action == 'remove_user' && user == owner) {
                    j_alert('error', "Impossibile eliminare l'utente proprietario del gruppo");
                    return false;
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

            $('#active_th').on('click', function (event) {
                event.preventDefault();
                aparam = 0;
                if ($(this).data('active') == 0) {
                    aparam = 1;
                }
                active_thread(aparam);
            });

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
    <?php endif; ?>
    <p class="spacer"></p>
    </div>
</div>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/footer.php" ?>
<div id="drawer" class="drawer" style="display: none; position: absolute">
    <div style="width: 100%; height: 430px">
        <div class="drawer_link"><a href="../../index.php"><img src="../../images/6.png" style="margin-right: 10px; position: relative; top: 5%" />Home</a></div>
        <div class="drawer_link"><a href="../../admin/index.php"><img src="../../images/31.png" style="margin-right: 10px; position: relative; top: 5%" />Admin</a></div>
        <div class="drawer_link"><a href="http://www.istitutoiglesiasserraperdosa.it"><img src="../../images/78.png" style="margin-right: 10px; position: relative; top: 5%" />Home Page Nivola</a></div>
    </div>
    <div class="drawer_lastlink"><a href="../../shared/do_logout.php"><img src="../../images/51.png" style="margin-right: 10px; position: relative; top: 5%" />Logout</a></div>
</div>
<!-- menu contestuale -->
<div id="context_menu" style="position: absolute; width: 160px; height: 60px; display: none; line-height: 18px" class="context_menu">
    <span id="sel_us" class="_bold"></span><br />
    <?php if(!$is_system_group): ?>
    <a style="font-weight: normal" href="#" onclick="admin('add_admin', 0)">Rendi amministratore</a><br />
    <?php endif; ?>
    <a style="font-weight: normal" href="#" onclick="manage_user('remove_user', 0, '')">Cancella dal gruppo</a><br />
</div>
<!-- fine menu contestuale -->
</body>
</html>
