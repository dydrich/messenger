<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php print $_SESSION['__config__']['intestazione_scuola'] ?>:: messaggi</title>
	<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,400italic,600,600italic,700,700italic,900,200' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="../../font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/reg.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/communication.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" /><script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="../../js/jquery.show_char_limit-1.2.0.js"></script>
	<script type="text/javascript" src="../../js/page.js"></script>
	<script>
        var mid = 0;
        $(function(){
            load_jalert();
            setOverlayEvent();
            <?php if(!$thread->isActive()): ?>
            $('#newmsg_lnk').click(function(event){
                event.preventDefault();
                j_alert("alert", "Il thread risulta impostato in sola lettura dall'amministratore del sistema. Impossibile inviare messaggi");
                return false;
            });
            <?php else: ?>
            $('#newmsg_lnk').click(function(event){
                event.preventDefault();
                $('#message').slideDown(500);
                $('#newmsg').hide(500);
                $('#viewlist').show(500);
                $('#txt').val("").focus();
            });
            <?php endif; ?>
            $('#viewlist_lnk').click(function(event){
                event.preventDefault();
                $('#message').slideUp(500);
                $('#newmsg').show(500);
                $('#viewlist').hide(500);
            });
            $('#send_lnk').click(function(event){
                event.preventDefault();
                send_message();
            });
            $('#signout_lnk').click(function(event){
                event.preventDefault();
                sign_out();
            });
            $('#get_target').click(function(event){
                event.preventDefault();
                $('#targets').show();
            });
    
            $('#txt').show_char_limit({
                status_element: '#char_left',
                status_style: 'chars_left',
                maxlength: 400
            });
    
            $('.ban').on('click', function(event) {
                event.preventDefault();
                var offset = $(this).offset();
                offset.top = offset.top + $(this).height();
                mid = $(this).data('mid');
                show_menu(event, offset);
            });
    
            $('#delete_lnk').on('click', function (event) {
                event.preventDefault();
                delete_group();
            });
            
            $('#spam_abuse_report').on('click', function(event) {
                report_message(2);
                $('#abuse').slideUp(300);
            });
    
            $('#nude_abuse_report').on('click', function(event) {
                report_message(3);
                $('#abuse').slideUp(300);
            });
    
            $('#spam_abuse_delete').on('click', function(event) {
                report_message(4);
                $('#abuse').slideUp(300);
            });
    
            $('#nude_abuse_delete').on('click', function(event) {
                report_message(5);
                $('#abuse').slideUp(300);
            });
    
            $('#restore_link').on('click', function(event) {
                restore_message();
                $('#abuse').slideUp(300);
            });
    
            interval = window.setInterval(check_for_updates, 5000);
    
        });
    
        var show_menu = function(e, offset){
            if ($('#abuse').is(":visible")) {
                $('#abuse').slideUp(300);
                return false;
            }
            $('#abuse').css({'top': offset.top+"px"}).css({'left': offset.left+"px"}).slideDown(500);
            return false;
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
                    j_alert('alert', 'Gruppo cancellato');
                    window.setTimeout(function() {
                        document.location.href = 'threads.php';
                    }, 2000);
                }
            });
        };
    
        var check_for_updates = function(){
            last_msg = <?php if (count($thread->getMessages()) > 0) echo $thread->getLastMessage()->getID(); else echo 0 ?>;
            tid = <?php echo $thread->getTid() ?>;
            upd = "msg";
            var p = document.getElementsByTagName("audio")[0];
            $.ajax({
                type: "POST",
                url: "check_for_updates.php",
                data: {msg: last_msg, tid: tid, upd: upd},
                dataType: 'json',
                error: function() {
    
                },
                succes: function() {
    
                },
                complete: function(data){
                    r = data.responseText;
                    if(r == "null"){
                        return false;
                    }
                    var json = $.parseJSON(r);
                    $.each(json, function(){
                        var t = this;
                        if (this.type == "new"){
                            div_msg = document.createElement("div");
                            div_msg.setAttribute("id", "msg_"+t.mid);
                            div_msg.setAttribute("display", "none");
                            div_msg.setAttribute("class", "message_detail target_msg");
    
                            div_h = document.createElement("div");
                            div_h.setAttribute("class", "msg_header");
    
                            div_send = document.createElement("div");
                            div_send.setAttribute("class", "msg_send");
                            if (t.t_t == '1') {
                                div_send.appendChild(document.createTextNode(t.send));
                            }
                            else {
                                div_send.appendChild(document.createTextNode(t.sender));
                            }
    
                            div_read = document.createElement("div");
                            div_read.setAttribute("class", "msg_read");
                            if (t.t_t == '1') {
                                div_read.appendChild(document.createTextNode("Letto "+t.read));
                            }
                            else {
                                div_read.appendChild(document.createTextNode(t.send));
                                a_ban = document.createElement("a");
                                a_ban.setAttribute("class", "ban material_link");
                                a_ban.setAttribute("href", "#");
                                i_ban = document.createElement("i");
                                i_ban.setAttribute("class", "fa fa-ban fright _bold attention");
                                i_ban.setAttribute("style", "margin-left: 20px; padding-top: 2px; font-size: 1.3em");
                                a_ban.appendChild(i_ban);
                                div_read.appendChild(a_ban);
                                $(a_ban).on('click', function(event) {
                                    event.preventDefault();
                                    var offset = $(this).offset();
                                    offset.top = offset.top + $(this).height();
                                    mid = $(this).data('mid');
                                    show_menu(event, offset);
                                });
                            }
    
                            div_txt = document.createElement("div");
                            div_txt.setAttribute("class", "msg_text");
                            div_txt.appendChild(document.createTextNode(t.text));
    
                            div_h.appendChild(div_send);
                            div_h.appendChild(div_read);
    
                            div_msg.appendChild(div_h);
                            div_msg.appendChild(div_txt);
    
                            $('#sel_thread').prepend(div_msg);
                            $('#msg_'+t.mid).hide().toggle({effect: 'scale', percent: 150});
                            p.play();
                        }
                        else {
                            mid = t.mid;
                            $('#read_'+mid).text("Letto "+t.read);
                        }
                    });
                }
            });
        };
    
        var send_message = function() {
            $.ajax({
                type: "POST",
                url: "controller.php?action=send&tid=<?php echo $thread->getTid() ?>",
                data: $('form').serialize(),
                error: function () {
    
                },
                succes: function () {
                    alert("Message sent");
                },
                complete: function (data) {
                    //$('#target').val("");
                    r = data.responseText;
                    if (r == "null") {
                        return false;
                    }
                    var json = $.parseJSON(r);
                    if (json.status == 'inactive') {
                        j_alert("error", "Il gruppo risulta contrassegnato come in sola lettura dall'amministratore di sistema. Impossibile inviare messaggi");
                        return false;
                    }
    
                    div_msg = document.createElement("div");
                    div_msg.setAttribute("id", "msg_" + json.mid);
                    div_msg.setAttribute("display", "none");
                    div_msg.setAttribute("class", "message_detail my_msg");
    
                    div_h = document.createElement("div");
                    div_h.setAttribute("class", "msg_header");
    
                    div_send = document.createElement("div");
                    div_send.setAttribute("class", "msg_send");
                    div_send.appendChild(document.createTextNode(json.sender));
    
                    div_read = document.createElement("div");
                    div_read.setAttribute("class", "msg_read");
                    div_read.setAttribute("id", "read_" + json.mid);
                    if (json.t_t == 1) {
                        div_read.appendChild(document.createTextNode("Letto: no"));
                    }
                    else {
                        div_read.appendChild(document.createTextNode(json.date));
                    }
    
                    div_txt = document.createElement("div");
                    div_txt.setAttribute("class", "msg_text");
                    div_txt.appendChild(document.createTextNode(json.text));
    
                    div_h.appendChild(div_send);
                    div_h.appendChild(div_read);
    
                    div_msg.appendChild(div_h);
                    div_msg.appendChild(div_txt);
    
                    $('#sel_thread').prepend(div_msg).show();
                    $('#message').hide();
                    $('#newmsg').show();
                    $('#viewlist').hide();
    
                    $('#msg_' + json.mid).show(1500);
                }
            });
        };
    
        var report_message = function(state) {
            $.ajax({
                type: "POST",
                url: "controller.php?action=report&mid="+mid+"&tid=<?php echo $thread->getTid() ?>&state="+state,
                data: $('form').serialize(),
                error: function () {
    
                },
                succes: function () {
                    alert("Message sent");
                },
                complete: function (data) {
                    //$('#target').val("");
                    r = data.responseText;
                    if (r == "null") {
                        return false;
                    }
                    var json = $.parseJSON(r);
    
                    if (json.status == 'ok') {
                        if (state == 2 || state == 3) {
                            $('#msg_' + mid).addClass("reported").data('state', state);
                        }
                        else {
                            $('#msg_' + mid).addClass("deleted").data('state', state);
                        }
                        $('#msg_' + mid + " i").removeClass('fa-ban').addClass('fa-warning');
                        j_alert("alert", "Messaggio cancellato");
                    }
                }
            });
        };
    
        var restore_message = function() {
            $.ajax({
                type: "POST",
                url: "controller.php",
                data: {action: 'restore', mid: mid, tid: <?php echo $thread->getTid() ?>},
                error: function () {
    
                },
                succes: function () {
                    alert("Message sent");
                },
                complete: function (data) {
                    //$('#target').val("");
                    r = data.responseText;
                    if (r == "null") {
                        return false;
                    }
                    var json = $.parseJSON(r);
    
                    if (json.status == 'ok') {
                        $('#msg_' + mid).hide().removeClass('reported').removeClass('deleted').show(500);
                        $('#msg_' + mid + " i").removeClass('fa-warning').addClass('fa-ban');
                        $('#msg_' + mid + " div.msg_text").text(json.text);
                    }
                }
            });
        };
        var delete_message = function(state) {
            $.ajax({
                type: "POST",
                url: "controller.php",
                data: {action: 'delete', mid: mid, tid: <?php echo $thread->getTid() ?>}, state: state,
                error: function () {

                },
                succes: function () {
                    alert("Message deleted");
                },
                complete: function (data) {
                    //$('#target').val("");
                    r = data.responseText;
                    if (r == "null") {
                        return false;
                    }
                    var json = $.parseJSON(r);

                    if (json.status == 'ok') {
                        $('#msg_' + mid).hide(500);
                        j_alert("alert", "Messaggio cancellato");
                    }
                }
            });
        };

        var sign_out = function() {
            <?php if ($thread->isBlocked()): ?>
            j_alert("error", "Gruppo bloccato. Impossibile abbandonare");
            return false;
            <?php else: ?>
            if (!confirm('Abbandonare il gruppo?')) {
                return false;
            }
            $.ajax({
                type: "POST",
                url: "controller.php?action=leave&tid=<?php echo $thread->getTid() ?>",
                data: {},
                error: function () {
    
                },
                succes: function () {
                    alert("User deleted");
                },
                complete: function (data) {
                    //$('#target').val("");
                    r = data.responseText;
                    if (r == "null") {
                        return false;
                    }
                    var json = $.parseJSON(r);
                    if (json.status == 'blocked') {
                        j_alert("error", "Gruppo bloccato. Impossibile abbandonare");
                        return false;
                    }
                    j_alert("alert", "Hai abbandonato il gruppo");
                    setTimeout(function(){
                        document.location.href = 'threads.php';
                    }, 2000);
                }
            });
        <?php endif; ?>
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
	<div id="navbar">
		<div id="username"><?php echo $thread->getTargetName($uniqID) ?></div>
		<div id="newmsg" class="rb_button" style="top: 20px">
			<a href="#" id="newmsg_lnk" title="Nuovo messaggio">
                <i class="fa fa-plus" style="color: black; font-size: 1.6em; padding: 11px 0 0 12px"></i>
            </a>
		</div>
		<div id="viewlist" class="rb_button" style="top: 20px">
			<a href="#" id="viewlist_lnk" title="Cancella">
                <i class="fa fa-close" style="color: black; font-size: 1.6em; padding: 10px 0 0 12px"></i>
            </a>
		</div>
        <div id="leavegroup" class="rb_button fleft" style="top: 20px; margin-left: 20px" title="Abbandona il gruppo">
            <a href="#" id="signout_lnk">
                <i class="fa fa-sign-out" style="color: black; font-size: 1.6em; padding: 11px 0 0 12px"></i>
            </a>
        </div>
        <?php if($thread->isAdministrator($_SESSION['__user__']->getUniqID())): ?>
        <div class="rb_button fleft" style="top: 20px; margin-left: 20px" title="Gestisci il gruppo">
            <a href="group.php?tid=<?php echo $thread->getTid() ?>" id="manage_lnk">
                <i class="fa fa-gear" style="color: black; font-size: 1.6em; padding: 10px 0 0 12px"></i>
            </a>
        </div>
        <?php endif; ?>
        <?php if($thread->isOwner($_SESSION['__user__'])): ?>
        <div class="rb_button fleft" style="top: 20px; margin-left: 20px" title="Cancella il gruppo">
            <a href="#" id="delete_lnk">
                <i class="fa fa-trash" style="color: black; font-size: 1.6em; padding: 10px 0 0 12px"></i>
            </a>
        </div>
        <?php endif; ?>
	</div>
    <div id="message" style="margin-bottom: 15px">
        <form class="no_border">
            <input type="hidden" name="target" id="target" readonly value="<?php echo $thread->getTargetName($uniqID) ?>" />
            <div id="msgtxt">
                <textarea id="txt" name="txt" placeholder="Componi il messaggio (max 400 caratteri)" ></textarea>
            </div>
            <input type="hidden" name="targetID" id="targetID" value="<?php echo $thread->getTid() ?>" />
        </form>
        <span style="padding-left: 5px; color: #AAAAAA">Rimangono <span id="char_left">400</span> caratteri</span>
        <a href="#" id="send_lnk"><i class="fa fa-arrow-circle-right" style="font-size: 1.8em; padding-top: 5px"
            ></i></a>
    </div>
	<div id="sel_thread" style="margin-top: 25px">
	<?php
	if (count($thread->getMessages()) > 0) {
		foreach ($thread->getMessages() as $k => $msg){
			list($date, $time) = explode(" ", $msg->getSendTimestamp());
			if (date("Y-m-d") == $date){
				$date = " oggi alle";
			}
			else {
				$date = "il ". format_date($date, SQL_DATE_STYLE, IT_DATE_STYLE, "/");
			}
			if ($msg->getReadTimestamp() == null){
				$rdate = ": no";
				$rtime = "";
			}
			else {
				list($rdate, $rtime) = explode(" ", $msg->getReadTimestamp());
				if (date("Y-m-d") == $rdate){
					$rdate = " oggi alle";
					$rtime = substr($rtime, 0, 5);
				}
				else {
					$rdate = " il ". format_date($rdate, SQL_DATE_STYLE, IT_DATE_STYLE, "/");
					$rtime = substr($rtime, 0, 5);
				}
			}
			$msg_send = $msg_read = "";
			if ($thread->getType() != Thread::CONVERSATION) {
				if ($msg->getFrom()->getUniqID() == $uniqID) {
					$msg_send = "Tu";
				}
				else {
					$msg_send = $msg->getFrom()->getFullName();
				}
				$msg_read = "Inviato ". $date." ".substr($time, 0, 5);
			}
			else {
				$msg_send = "Inviato ". $date." ".substr($time, 0, 5);
				$msg_read = "Letto ".$rdate." ".$rtime;
			}
            if ($msg->isReported()) {
                $reason = ($msg->getState() == 2 ? 'per SPAM' : 'per linguaggio non appropriato');
                if ($msg->getFrom()->getUid() == $uid || $thread->isAdministrator($_SESSION['__user__']->getUniqID())) {
                    $text = $msg->getText(). " <br />(messaggio segnalato $reason)";
                }
                else {
                    $text  ="Messaggio segnalato in attesa di approvazione";
                }
            }
            else {
                $text = $msg->getText();
            }
            if ($msg->isDeleted()) {
                $reason = ($msg->getState() == 4 ? 'per SPAM' : 'per linguaggio non appropriato');
                if ($msg->getFrom()->getUid() == $uid || $thread->isAdministrator($_SESSION['__user__']->getUniqID())) {
                    $text = $msg->getText(). " <br />(messaggio cancellato $reason)";
                }
                else {
                    $text  ="Messaggio cancellato $reason";
                }
            }
            else {
                $text = $msg->getText();
            }
	?>
		<div id="msg_<?php echo $k; ?>" data-state="<?php echo $msg->getState() ?>" class="message_detail <?php if ($msg->getFrom()->getUid() == $uid) echo
        "my_msg"; else echo "target_msg" ?>
<?php if
        ($msg->isReported()) echo " reported"; else if($msg->isDeleted()) echo ' deleted' ?>">
			<div class="msg_header">
				<div class="msg_send"><?php echo $msg_send ?></div>
				<div class="msg_read" id="read_<?php echo $k ?>">
                    <?php echo $msg_read ?>
                    <?php if ($msg->getFrom()->getUid() != $uid): ?>
                    <a href="#" class="ban material_link" data-mid="<?php echo $k ?>">
                        <i class="fa <?php if ($msg->isReported()) echo " fa-warning"; else echo "fa-ban _bold" ?> fright attention" style="margin-left: 20px;
                        padding-top: 2px; font-size: 1.3em"></i>
                    </a>
                    <?php endif; ?>
                </div>
			</div>
			<div class="msg_text <?php if ($msg->getFrom()->getUid() == $uid) echo "_right"?>"><?php echo $text ?></div>
		</div>
	<?php
		}
	}
	?>
	</div>
	<div id="targets">Elenco utenti</div>
</div>
<audio src="theme/new_msg.ogg" preload="auto" id="mp3"></audio>
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
<div class="context_menu" id="abuse" style="position: absolute; width: 190px; height: 60px; display: none; background-color: white">
    <?php if ($thread->isAdministrator($_SESSION['__user__']->getUniqID())): ?>
    <a href="#" id="spam_abuse_delete" class="material_link">Cancella per SPAM</a><br />
    <a href="#" id="nude_abuse_delete" class="material_link">Cancella per volgarita</a><br />
    <a href="#" id="restore_link" class="material_link">Approva il messaggio</a><br />
    <?php else : ?>
    <a href="#" id="spam_abuse_report" class="material_link">Segnala per SPAM</a><br />
    <a href="#" id="nude_abuse_report" class="material_link">Segnala per volgarita</a><br />
    <?php endif; ?>
</div>
</body>
</html>
