		<script>
		$(function() {
			$( "#dialog" ).dialog({
				autoOpen: false,
				show: {
					effect: "fade",
					duration: 1000
				},
				hide: {
					effect: "fade",
					duration: 1000
				},
				modal: true
			});

		});

		function show_news(id, path){			
			$.ajax({
				type: "POST",
				url: path+"/modules/communication/get_news.php",
				data: {id: id},
				dataType: 'json',
				error: function(data, status, errore) {
					alert("Si e' verificato un errore");
					return false;
				},
				succes: function(result) {
					alert("ok");
				},
				complete: function(data, status){
					r = data.responseText;
					var json = $.parseJSON(r);
					if(json.status == "kosql"){
						alert("Errore SQL. \nQuery: "+json.query+"\nErrore: "+json.message);
						return;
		      		}
					else {
						$(function() {
							$('#dialog').html(json.message);
							$('#dialog').dialog({
								title: json.abstract
							});
							$( "#dialog" ).dialog( "open" );
		     				
		     			});
					}
				}
			});
		}
				
		</script>
		<div class="welcome">
			<p id="w_head">News <?php echo date("d/m/Y") ?></p>
			<p class="w_text">
			<?php
			$year = $_SESSION['__current_year__'];
			$inizio_anno = format_date($year->get_data_apertura(), IT_DATE_STYLE, SQL_DATE_STYLE, "-");
			$sel_news = "SELECT * FROM rb_com_news WHERE data >= '{$inizio_anno}' ORDER BY data DESC, ora DESC LIMIT ".$_SESSION['__config__']['num_news'];
			$res_news = $db->executeQuery($sel_news);
			if($res_news->num_rows < 1) {
				echo "<span>Non &egrave; presente nessuna news.</span>";
			}
			else {
				while($news = $res_news->fetch_assoc()){
					$now = new DateTime(date("Y-m-d"));
					$news_date = new DateTime($news['data']);
					$diff = $now->diff($news_date);
					$red = $bold = false;
					$interval = intval($diff->format("%a"));
					if ($interval < 7){
						$red = true;
						$bold = true;
					}
					else if ($interval < 15){
						$bold = true;
					}
			?>
				<a href="#" onclick="show_news(<?php print $news['id_news'] ?>, '<?php echo $_SESSION['__path_to_root__'] ?>')" class="open_news<?php if($red) echo " attention"; if ($bold) echo " _bold" ?>">
                    <?php print truncateString($news['abstract'], 80) ?>
                </a><br />
			<?php 
				}
			}
			?>
			</p>
			<div id="dialog"></div>
		</div>
