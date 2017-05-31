<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" >
<head>
	<?php echo $this->Html->charset(); ?>
	<title>JobTraq</title>
	<link href="<?php echo URL; ?>/favicon.ico" type="image/x-icon" rel="icon">
	<link href="<?php echo URL; ?>/favicon.ico" type="image/x-icon" rel="shortcut icon">
	<?php
		echo $this->Minify->css(array('reset','style','jt_screen','jt_vunguyen','jobtraq_chat','jquery-ui-1.10.3.custom.min','kendo/kendo.common.min','kendo/kendo.anvy.min','jquery.mCustomScrollbar'));
		/*echo $this->Html->css('reset');
		echo $this->Html->css('style');

		echo $this->Html->css('jt_screen');
		echo $this->Html->css('jt_vunguyen');
		echo $this->Html->css('jobtraq_chat');

		echo $this->Html->css('jquery-ui-1.10.3.custom.min');
		echo $this->Html->css('kendo/kendo.common.min');
		echo $this->Html->css('kendo/kendo.anvy.min');
		echo $this->Html->css('jquery.mCustomScrollbar');*/
		// jQuery
		echo $this->Minify->script(array('jquery-1.10.2.min','jquery-ui-1.10.3.custom.min','jquery.combobox','jquery.autosize.min','jquery.mousewheel.min','jquery.mCustomScrollbar.concat.min'));

		/*echo $this->Html->script('jquery-1.10.2.min');
		//kendo plugin
		echo $this->Html->script('jquery-ui-1.10.3.custom.min');
		echo $this->Html->script('jquery.combobox');
		echo $this->Html->script('jquery.autosize.min');
		//Scrollbar
		echo $this->Html->script('jquery.mousewheel.min');
		echo $this->Html->script('jquery.mCustomScrollbar.concat.min');*/

	?>
	<link href="/plugins/select2/select2.min.css" rel="stylesheet" />
	<script src="/plugins/select2/select2.min.js"></script>
</head>

<body>
	<!-- <iframe id="manifest_iframe_hack" style="display: none;" src="<?php echo URL; ?>/kei.html"></iframe> -->
	<div id="wrapper">
		<?php echo $this->element('header'); ?>
		<?php echo $this->fetch('content'); ?>

		<?php if(!isset($set_footer))
				$set_footer = 'footer';
			  echo $this->element($set_footer);
		?>
	</div>
	<script type="text/javascript">
		localStorage.setItem("format_date","<?php echo $_SESSION['format_date']; ?>");
	</script>
	<?php
		echo $this->element('loading');
		echo $this->element('window');
		// echo $this->element('sql_dump');
		/*echo $this->Html->script('main.js');
		echo $this->Html->script('kendo/kendo.web.min');*/
		echo $this->Minify->script(array('main.js','kendo/kendo.web.min'));
		echo $this->Js->writeBuffer();
		echo $this->element('chat');
	?>
	<script type="text/javascript">
	$(function(){
		$.ajax({
			url: '<?php echo URL; ?>/homes/alerts_check',
			success: function(html){
				var json = JSON.parse(html);

				if( json.has_alert == 1){
					set_alert_footer(json);
				}else{
					remove_alert_footer();
				}

				set_interval_check_alert();
			}
		});

	});
	function set_interval_check_alert(){
		var stillAlive = setInterval(function () {
			var now = new Date();
			var sec = now.getSeconds();
			if( sec == 0 ){
				$.ajax({
					url: '<?php echo URL; ?>/homes/alerts_check',
					success: function(html){
						var json = JSON.parse(html);
						if( json.has_alert == 1){
							set_alert_footer(json);
						}else{
							remove_alert_footer();
						}
					}
				});
			}
		}, 3000);
	}


	function set_alert_footer(json){
		var alerts_footer = $("#alerts_footer");
		$("#alerts_title", alerts_footer).addClass("color_ac");
		alerts_footer.addClass("active").attr("onclick", "open_alerts()");

		var str = "";
		if( json.leave > 0 ){
			str += "(" + json.leave + ") ";
		}
		if( json.communication > 0 ){
			str += "(" + json.communication + ") ";
		}
		if( json.task > 0 ){
			str += "(" + json.task + ") ";
		}
		$("#alerts_num", alerts_footer).html(": " +str);
	}
	function remove_alert_footer(){
		var alerts_footer = $("#alerts_footer");
		$("#alerts_title", alerts_footer).removeClass("color_ac");
		alerts_footer.removeClass("active").attr("onclick", "return false;");
		$("#alerts_num", alerts_footer).html("");
	}
	function open_alerts(){
		$.ajax({
			url: '<?php echo URL; ?>/homes/alerts_open',
			success: function(html){
				popup_show("Your alerts", html);
			}
		});
	}

	</script>
	<div id="jobtraq_loading" style="display:none;"><img src="<?php echo URL ?>/theme/default/images/loading.gif" style=" background-repeat: no-repeat; background-position: 214px 156px;" /></div>
</body>
</html>