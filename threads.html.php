<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title><?php echo $_SESSION['__config__']['intestazione_scuola'] ?>:: messaggi</title>
    <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,400italic,600,600italic,700,700italic,900,200' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="../../font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/reg.css" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="../../css/general.css" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/communication.css" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="../../css/site_themes/<?php echo getTheme() ?>/jquery-ui.min.css" type="text/css" media="screen,projection" /><script type="text/javascript" src="../../js/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
    <script type="text/javascript" src="../../js/page.js"></script>
    <script>
    var last_tid = <?php echo $last_tid ?>;
    var last_msg = <?php echo $last_msg ?>;
    $(function(){
        load_jalert();
        setOverlayEvent();
        <?php if($_SESSION['__mod_area__'] != 'alunni'): ?>
        $('#newmsg_lnk').click(function(event){
            event.preventDefault();
            $('#msg_container').slideDown(500);
            $('#message').slideDown(500);
            $('#newmsg').hide(500);
            $('#viewlist').show(500);
            $('#target').focus();

        });
        $('#viewlist_lnk').click(function(event){
            event.preventDefault();
            $('#txt').val("");
            $('#target').val("");
            $('#msg_container').slideUp(500);
            $('#newmsg').show(500);
            $('#viewlist').hide(500);
        });
        <?php else: ?>
        $('#newmsg').hide(1);
        $('#viewlist').hide(1);
        <?php endif; ?>
        $('#send_lnk').click(function(event){
            event.preventDefault();
            send_message();
        });
        $('#get_target').click(function(event){
            event.preventDefault();
            $('#targets').show(500);
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

        //autocomplete
        $("#target").autocomplete({
            source: "get_users.php",
            minLength: 2,
            select: function(event, ui){
                uid = ui.item.uniqID;
                tp = ui.item.type;
                $('#targetID').val(uid);
            }
        });

        $('.pc').on('click', function (event) {
            event.preventDefault();
            ccc();
        });

        interval = window.setInterval(check_for_updates, 5000);
    });

    var send_message = function(){
        alert($('#targetID').val());
        $.ajax({
            type: "POST",
            url: "controller.php?action=send&tid=0",
            data: $('form').serialize(),
            error: function() {

            },
            succes: function(data) {
                alert(data);

            },
            complete: function(data){
                $('#txt').val("");
                $('#target').val("");
                //dati = data.responseText.split("|");
                r = data.responseText;
                if(r == ""){
                    return false;
                }
                var json = $.parseJSON(r);

                lnk = document.createElement("A");
                lnk.setAttribute("href", "controller.php?action=show_thread&tid="+json.thread);
                lnk.setAttribute("class", "th_link");

                div_th = document.createElement("DIV");
                div_th.setAttribute("id", "thread_"+json.thread);
                div_th.setAttribute("class", "card");

                div_h = document.createElement("div");
                div_h.setAttribute("class", "card_title");

                div_user = document.createElement("div");
                div_user.setAttribute("class", "thread_user");
                div_user.appendChild(document.createTextNode(json.target));

                div_count = document.createElement("div");
                div_count.setAttribute("class", "thread_msg_count");
                div_count.appendChild(document.createTextNode(json.date));

                //div_lm = document.createElement("div");
                //div_lm.setAttribute("class", "thread_lm");
                //div_lm.appendChild(document.createTextNode(json.date));

                div_txt = document.createElement("div");
                div_txt.setAttribute("class", "card_content");
                div_txt.appendChild(document.createTextNode(json.text));

                div_h.appendChild(div_user);
                div_h.appendChild(div_count);
                //div_h.appendChild(div_lm);

                div_th.appendChild(div_h);
                div_th.appendChild(div_txt);

                lnk.appendChild(div_th);

                $('#threads').prepend(lnk);

                $('#msg_container').slideUp(500);
                $('#newmsg').show();
                $('#viewlist').hide();
                last_tid = json.thread;
                last_msg = json.mid;
            }
        });
    };

    var prova_confirm = function() {
        j_alert("confirm", "Eliminare il gruppo? La maremma maiala segnala che il gruppo continene molti messaggi");
    };

    var ccc = function () {
        if (!prova_confirm()) {
            alert('false');
        }
        else {
            alert('true');
        }
    };

    var check_for_updates = function(){

        tid = last_tid;
        lmsg = last_msg;
        upd = "th";
        var p = document.getElementsByTagName("audio")[0];
        $.ajax({
            type: "POST",
            url: "check_for_updates.php",
            data: {tid: tid, upd: upd, lmsg: lmsg},
            dataType: 'json',
            error: function() {

            },
            succes: function() {

            },
            complete: function(data){
                r = data.responseText;
                if(r == ""){
                    return false;
                }
                var json = $.parseJSON(r);
                if(json.status == "no_upd"){
                    return false;
                }
                $.each(json, function(){
                    var t = this;
                    if (this.type == "del_new"){
                        // delete element
                        //alert($("ln_"+t.tid));
                        $("#ln_"+t.tid).hide();
                        $('#thread_'+t.tid).hide();
                    }
                    else if (t.type == "upd") {
                        //alert(t.count);
                        //$('#count_thr_'+t.tid).text(t.count);
                        $('#date_thr_'+t.tid).text(t.datetime);
                        if (t.thread_type == 'G') {
                            $('#txt_thr_'+t.tid).text(t.sender+": "+t.text);
                        }
                        else {
                            $('#txt_thr_'+t.tid).text(">> "+t.text);
                        }
                        $('#head_thr_'+ t.tid).addClass("bold_");
                        txt = $('#thread_user_'+ t.tid).text();
                        $('#thread_user_'+ t.tid).html(t.user+"<span class='new_msg_sign'>(nuovi messaggi)</span>")
                    }
                    else {
                        a_ln = document.createElement("a");
                        a_ln.setAttribute("href", "controller.php?action=show_thread&tid="+t.tid);
                        a_ln.setAttribute("id", "ln_"+t.tid);
                        a_ln.setAttribute("class", "th_link");

                        div_th = document.createElement("div");
                        div_th.setAttribute("id", "thread_"+t.tid);
                        div_th.setAttribute("display", "none");
                        div_th.setAttribute("class", "card");

                        div_h = document.createElement("div");
                        div_h.setAttribute("class", "card_title bold_");

                        div_user = document.createElement("div");
                        div_user.setAttribute("class", "thread_user");
                        div_user.appendChild(document.createTextNode(t.user));

                        span = document.createElement("span");
                        span.setAttribute("class", "new_msg_sign");
                        span.appendChild(document.createTextNode("(nuovi messaggi)"));

                        div_count = document.createElement("div");
                        div_count.setAttribute("class", "thread_msg_count");
                        div_count.appendChild(document.createTextNode(t.datetime));

                        //div_lm = document.createElement("div");
                        //div_lm.setAttribute("class", "thread_lm");
                        //div_lm.appendChild(document.createTextNode(t.datetime));

                        div_txt = document.createElement("div");
                        div_txt.setAttribute("class", "card_content");
                        div_txt.appendChild(document.createTextNode(t.text));

                        div_user.appendChild(span);
                        div_h.appendChild(div_user);
                        div_h.appendChild(div_count);
                        //div_h.appendChild(div_lm);
                        div_th.appendChild(div_h);
                        div_th.appendChild(div_txt);
                        a_ln.appendChild(div_th);

                        $('#threads').prepend(a_ln);
                        $('#thread_'+t.tid).hide().toggle({effect: 'scale', percent: 150});
                    }
                    p.play();
                    if (this.type == "new"){
                        last_tid = t.tid;
                    }
                    last_msg = t.mid;
                });
            }
        });
    };

    </script>
</head>
<body>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/header.php" ?>
<?php include "navigation_th.php" ?>
<div id="main">
<div id="right_col">
<?php include "menu.php" ?>
</div>
<div id="left_col">
    <div style="position: absolute; top: 75px; left: 47%; margin-bottom: -5px" class="rb_button">
        <a href="group.php?tid=0" id="new_group" title="Crea un nuovo gruppo">
            <i class="fa fa-group" style="font-size: 1.6em; margin-top: 9px; margin-left: 9px; color: black"></i>
        </a>
    </div>
    <div id="msg_container" style="width: 95%; height: 200px; display: none; margin-top: 10px">
        <div id="message" style="height: 195px">
            <form class="no_border">
                <div id="to"><input type="text" name="target" id="target" /></div>
                <div id="get_to">
                    <a href="#" id="get_target">
                        <i class="fa fa-plus-circle" style="margin-top: 4px; margin-right: 2px"></i>
                    </a>
                </div>
                <div id="msgtxt">
                    <textarea id="txt" name="txt" placeholder="Componi il messaggio (max 400 caratteri)" maxlength="400"></textarea>
                </div>
                <input type="hidden" name="targetID" id="targetID" />
            </form>
            <span style="padding-left: 5px; color: #AAAAAA">Rimangono <span id="char_left">400</span> caratteri</span>
            <a href="#" id="send_lnk">
                <i class="fa fa-arrow-circle-right" style="font-size: 1.8em; padding-top: 5px"></i>
            </a>
        </div>
    </div>
	<div id="threads" class="card_container" style="margin-top: 20px">
	<?php
	if (isset($threads) && count($threads) > 0){
		foreach ($ordered_threads as $k => $thread){
			list($date, $time) = explode(" ", $k);
			if (date("Y-m-d") == $date){
				$date = "Oggi alle";
			}
			else if ($date == date('Y-m-d',time() - (24 * 60 * 60))){
				$date = " Ieri alle";
			}
			else {
				$date = format_date($date, SQL_DATE_STYLE, IT_DATE_STYLE, "/");
			}
			$text = "Nessun messaggio";
			if (count($thread->getMessages()) > 0) {
				$text = "";
				if ($thread->getType() != Thread::CONVERSATION) {
					if ($thread->getLastMessage()->getFrom()->getUniqID() != $uniqID) {
						$text = $thread->getLastMessage()->getFrom()->getFullName().": ";
					}
					else {
						$text = "Tu: ";
					}
				}
				else {
					if ($thread->getLastMessage()->getFrom()->getUniqID() != $uniqID) {
						$text = $thread->getLastMessage()->getFrom()->getFullName().": ";
					}
				}
				$text .= truncateString($thread->getLastMessage()->getText(), 200);
			}
	?>
		<div id="thread_<?php echo $thread->getTid(); ?>" class="card" style="<?php if(!$thread->isActive()) echo 'background-color: #EEEEEE' ?>">
			<div id="head_thr_<?php echo $thread->getTid() ?>" class="card_title <?php if (!$thread->isRead($_SESSION['__user__'])) echo "bold_" ?>">
				<div id="thread_user_<?php echo $thread->getTid() ?>" class="thread_user">
                    <?php echo $thread->getTargetName($_SESSION['__user__']->getUniqID()); if ($thread->isRead($_SESSION['__user__']) === false): ?>
                        <span class="new_msg_sign">(nuovi messaggi)</span>
                    <?php endif; ?>
                    <?php if($thread->isAdministrator($_SESSION['__user__']->getUniqID())): ?>
                        <span class="material_dark_bg" style="width: 75px; margin-left: 10px;
                        border-radius: 3px; padding: 2px 4px; color: white; font-size: 0.8em;
                        text-transform: lowercase; font-weight: bold">Amministratore</span>
                    <?php endif; ?>
                    <?php if($thread->isOwner($_SESSION['__user__']) && $thread->getType() != Thread::CONVERSATION): ?>
                        <a href="#" class="pc"><span class="material_dark_bg" style="width: 75px; margin-left: 10px;
                        border-radius: 3px; padding: 2px 4px; color: white; font-size: 0.8em;
                        text-transform: lowercase; font-weight: bold">Proprietario</span></a>
                    <?php endif; ?>
                    <?php if($thread->isAdministrator($_SESSION['__user__']->getUniqID()) && $thread->hasReportedMessages()): ?>
                        <a href="reported_messages.php?tid=<?php echo $thread->getTid(); ?>">
                            <span class="attention_bg" style="width: 75px; margin-left: 10px; border-radius: 3px; padding: 2px 4px; color: white; font-size: 0.8em;
                            text-transform: lowercase; font-weight: bold">Segnalato</span>
                        </a>
                    <?php endif; ?>
                    <?php if($thread->isAdministrator($_SESSION['__user__']->getUniqID()) && $thread->hasDeletedMessages()): ?>
                        <a href="deleted_messages.php?tid=<?php echo $thread->getTid(); ?>">
                            <span class="attention_bg" style="width: 75px; margin-left: 10px; border-radius: 3px; padding: 2px 4px; color: white; font-size: 0.8em;
                            text-transform: lowercase; font-weight: bold">Moderato</span>
                        </a>
                    <?php endif; ?>
                </div>
				<div id="date_thr_<?php echo $thread->getTid() ?>" class="thread_msg_count">
                    <?php echo $date." ".substr($time, 0, 5) ?>
                    <?php if($thread->isOwner($_SESSION['__user__']) && $thread->getType() != Thread::CONVERSATION): ?>
                    <a href="group.php?tid=<?php echo $thread->getTid() ?>">
                        <i class="fa fa-gear normal th_admin" style="font-size: 1.4em; margin-left: 25px"></i>
                    </a>
                    <?php endif; ?>
                    <!--<i class="fa fa-sign-out normal th_leave" style="font-size: 1.4em; margin-left: 15px"></i>-->
                </div>
			</div>
            <a href="controller.php?action=show_thread&tid=<?php echo $thread->getTid() ?>" id="ln_<?php echo $thread->getTid() ?>" class="th_link">
			    <div id="txt_thr_<?php echo $thread->getTid() ?>" class="card_content" style="color: #1E4389"><?php echo $text ?></div>
            </a>
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
</div>
<?php include "../../intranet/{$_SESSION['__mod_area__']}/footer.php" ?>
<div id="drawer" class="drawer" style="display: none; position: absolute">
	<div style="width: 100%; height: 430px">
		<div class="drawer_link"><a href="<?php echo $_SESSION['__modules__']['messenger']['path_to_root'] ?>intranet/<?php echo $_SESSION['__mod_area__'] ?>/index.php"><img src="../../images/6.png" style="margin-right: 10px; position: relative; top: 5%" />Home</a></div>
		<div class="drawer_link"><a href="<?php echo $_SESSION['__modules__']['messenger']['path_to_root'] ?>intranet/<?php echo $_SESSION['__mod_area__'] ?>/profile.php"><img src="../../images/33.png" style="margin-right: 10px; position: relative; top: 5%" />Profilo</a></div>
		<?php if (!$_SESSION['__user__'] instanceof ParentBean) : ?>
			<div class="drawer_link"><a href="<?php echo $_SESSION['__modules__']['messenger']['path_to_root'] ?>modules/documents/load_module.php?module=docs&area=<?php echo $_SESSION['__mod_area__'] ?>"><img src="<?php echo $_SESSION['__modules__']['messenger']['path_to_root'] ?>images/11.png" style="margin-right: 10px; position: relative; top: 5%" />Documenti</a></div>
		<?php endif; ?>
		<?php if(is_installed("com")){ ?>
			<div class="drawer_link"><a href="<?php echo $_SESSION['__modules__']['messenger']['path_to_root'] ?>modules/communication/load_module.php?module=com&area=<?php echo $_SESSION['__mod_area__'] ?>"><img src="<?php echo $_SESSION['__modules__']['messenger']['path_to_root'] ?>images/57.png" style="margin-right: 10px; position: relative; top: 5%" />Comunicazioni</a></div>
		<?php } ?>
	</div>
	<?php if (isset($_SESSION['__sudoer__'])): ?>
		<div class="drawer_lastlink"><a href="<?php echo $_SESSION['__modules__']['messenger']['path_to_root'] ?>admin/sudo_manager.php?action=back"><img src="<?php echo $_SESSION['__modules__']['messenger']['path_to_root'] ?>images/14.png" style="margin-right: 10px; position: relative; top: 5%" />DeSuDo</a></div>
	<?php endif; ?>
	<div class="drawer_lastlink"><a href="<?php echo $_SESSION['__modules__']['messenger']['path_to_root'] ?>shared/do_logout.php"><img src="<?php echo $_SESSION['__modules__']['messenger']['path_to_root'] ?>images/51.png" style="margin-right: 10px; position: relative; top: 5%" />Logout</a></div>
</div>
</body>
</html>
<a href="#" id="top_btn" class="rb_button float_button top_button">
    <i class="fa fa-arrow-up"></i>
</a>
