<?php echo $this->Html->script('jquery.fileDownload'); ?>
<div class="clear_percent">
	<div class="clear_percent_19 float_left">
		<div class="tab_1 full_width">
			<span class="title_block bo_ra1">
				<span class="fl_dent"><h4><?php echo translate('Tasks'); ?></h4></span>
			</span>
			<div class="title_small_once">
				<ul class="ul_mag clear bg3">
					<li class="hg_padd">
						<?php echo translate('Messsage type'); ?> <span class="normal">(<?php echo translate('click a task name to do on right'); ?>)</span>
					</li>
				</ul>
			</div>
			<div id="list_and_menu_height" class="container_same_category" style="height: 449px;overflow-y: auto">
				<ul class="find_list setup_menu">
					<li onclick="show_administrator_detail(this,'administrator_backup_restore')">
						<a href="javascript:void(0)" class="active">
							<?php echo translate('Backup - Restore');?>
						</a>
					</li>
					<li id="export_contact" class="download_file">
						<a href="javascript:void(0)" class="">
							<?php echo translate('Export Contact');?>
						</a>
					</li>
					<li id ="export_company" class="download_file">
						<a href="javascript:void(0)" class="">
							<?php echo translate('Export Company');?>
						</a>
					</li>
					<?php if(!in_array(URL, array('http://jobtraq.anvy.net','http://jt.anvy.net'))){ ?>
					<li id ="check_update">
						<a href="javascript:void(0)">
							<?php echo translate('Check update');?>
						</a>
					</li>
					<?php } ?>
				</ul>
			</div>
			<span class="title_block bo_ra2"></span>
		</div><!--END Tab1 -->
	</div>
	<div class="clear_percent_9_arrow float_left">
		<div class="full_width box_arrow">
			<span class="icon_emp" style="cursor:default"></span>
		</div>
	</div>
	<div class="clear_percent_11 float_left" id="administrator_detail">
		<!-- Detail -->
	</div>
</div>
<script type="text/javascript">

	$(function(){
		$("li:first", "#list_and_menu_height").click(); // click menu li dau tien khi page load xong
		$(".download_file").click(function(){
			var id = $(this).attr("id");
			var url = "<?php echo URL.'/settings/' ?>"+id+"/";
			confirms3("Message","Which type file you want to export?",["CSV","Excel",""]
			          ,function(){
			          	$.fileDownload(url+"csv", {
					        preparingMessageHtml: "Preparing your file, please wait...",
					        failMessageHtml: "There was a problem generating your file, please refresh and try again."
					    });
					    return false;
			          },function(){//Excel
						$.fileDownload(url+"excel", {
					        preparingMessageHtml: "Preparing your file, please wait...",
					        failMessageHtml: "There was a problem generating your file, please refresh and try again."
					    });
					    return false;
			          },function(){
			          	return false;
			          });
		});
		$("#check_update").click(function(){
			$.ajax({
				url:  "<?php echo URL.'/update/check_update' ?>",
				success: function(result){
					result = $.parseJSON(result);
					if(result.Status == "Up to date"){
						alerts("Message",result.Message);
					} else {
						confirms("Message",result.Message+"<br />Do you want to update?"
						         ,function(){
						         	$.ajax({
						         		url : "<?php echo URL.'/update/do_update' ?>",
						         		success: function(result){
						         			alerts("Message",result);
						         		}
						         	});
						         },function(){
						         	return false;
						         });
					}
				}
			});
		});
	});

	function show_administrator_detail(object,id){
		$("#list_and_menu_height a").removeClass("active");
		$("a", object).addClass("active");
		$.ajax({
			url: '<?php echo URL; ?>/settings/'+id+'/',
			type:"POST",
			timeout: 15000,
			success: function(html){
				if(id == "export_contact" || id == "export_company")
					window.open(html);
				else
					$("#administrator_detail").html(html);
			}
		});
	}

</script>