<?php if($this->Common->check_permission('tasks_@_entry_@_view',$arr_permission)): ?>
<?php if( !isset($remove_js) ){ ?>
<?php 
	function sec2hour($productiontime){
		$hours = floor($productiontime / 3600);
		$mins = floor(($productiontime - ($hours*3600)) / 60);
		$hours = str_pad($hours, 2, '0', STR_PAD_LEFT);
		$mins = str_pad($mins, 2, '0', STR_PAD_LEFT);
		return "$hours:$mins";
	}
?>
<style type="text/css">
ul.ul_mag li.hg_padd {
	overflow: visible !important;
}
ul.ul_mag li.hg_padd {
	margin-top: 0;
}
#load_subtab .combobox {
	margin-top: 1px;
}
input.input_inner{
	background-color: transparent;
}
</style>
<div class="tab_1 full_width">
	<span class="title_block bo_ra1">
		<span class="fl_dent">
			<h4>
				<?php echo translate('Tasks relating to this sales order'); ?></h4>
		</span>
		<div class="float_left hbox_form dent_left_form">
			<?php if($this->Common->check_permission('tasks_@_entry_@_add',$arr_permission)): ?>
			<input id="click_open_window_contacts_tab_tasks" onclick="$('#task_id').val('');" class="btn_pur size_width2" type="button" value="Add contact">
			<input id="click_open_window_equipments_tab_tasks" onclick="$('#task_id').val('');" class="btn_pur size_width2" type="button" value="Add asset">
			<?php endif; ?>
		</div>
		<div style="float:right; margin-right: 15px">
		    <a href="javascript:void(0)" id="asset_tag_report" target="_blank">
		        <input class="btn_pur" id="asset_tag_pdf" type="button" value="Generate Job Tickets" style="width:99%;">
		    </a>
		</div>
		<script type="text/javascript">
		    $("#asset_tag_report").click(function(){
		        $.ajax({
		            url: '<?php echo URL; ?>/salesorders/get_uncompleted_docket/',
		            success: function(result){
		                result = $.parseJSON(result);
		                if(result.length){
		                    var content = '';
		                    for(var i = 0; i < result.length; i++){
		                        content += '<ul class="ul_mag clear bg'+(i%2==0? 1 : 2)+'""><li class="class="hg_padd" style="text-align:left;width:40%;">'+result[i].product_name+'</li><li class="class="hg_padd" style="text-align:right;width:5.5%;"><span style="margin-right: 10px" id="current_qty_'+result[i].key+'">'+result[i].sizew+'</span></li><li class="class="hg_padd" style="text-align:right;width:5.5%;"><span style="margin-right: 10px" id="current_qty_'+result[i].key+'">'+result[i].sizeh+'</span></li><li class="class="hg_padd" style="text-align:right;width:15%;"><span style="margin-right: 10px" id="current_qty_'+result[i].key+'">'+result[i].quantity+'</span></li><li class="hg_padd" style="text-align:right;width:28%;"><input type="text" name="repair_qty_'+result[i].key+'" id="repair_qty_'+result[i].key+'" rel="'+result[i].key+'" class="input_inner jt_box_save repair_qty_docket" style="text-align: right" onkeypress="return isPrice(event);" value="'+result[i].quantity+'" /></li></ul>';
		                    }
		                    appendPopup(content);
		                } else
		                    alerts("Message","All asset tags are completed.");
		            }
		        })


		    });
		    function appendPopup(content){
		        var job_ticket = [
		            '<div id="job_ticket_popup">',
		                '<ul class="ul_mag clear bg3" style="margin-top: 30px;">',
		                    '<li class="hg_padd" style="text-align:left;width:39.5%;">',
		                        'Product Name',
		                    '</li>',
		                    '<li class="hg_padd" style="text-align:right;width:5%;">',
		                        'Size-W',
		                    '</li>',
		                    '<li class="hg_padd" style="text-align:right;width:5%;">',
		                        'Size-H',
		                    '</li><li class="hg_padd" style="text-align:right;width:15%;">',
		                       ' Current Quantity',
		                   ' </li>',
		                    '<li class="hg_padd" style="text-align:right;width:28%;">',
		                        'Repair Quantity',
		                    '</li>',
		                '</ul>',
		                '<form id="job_ticket_form">',
		                    '<div id="job_ticket_content">',
		                        content,
		                    '</div>',
		               ' </form>',
		            '</div>'].join("");
		        var generate_job_ticket_popup = $(job_ticket);
		        generate_job_ticket_popup.kendoWindow({
		            width: "60%",
		            height: "35%",
		            title: 'Docket',
		            visible: false,
		            close: function(){
		                $("#job_ticket_popup").data("kendoWindow").destroy();
		            }
		        });
		        //show popup
		        generate_job_ticket_popup.data("kendoWindow").center();
		        generate_job_ticket_popup.data("kendoWindow").open();
	            var html = '<ul id="button_group_job_tickets" class="menu_control float_right" style="margin-right: 50px; margin-bottom: 5px;">';
	            html +=     '<li style="position: absolute;margin-top: 25px;left: 88%;background-color: #003256;width: 10%;text-align: center;">';
	            html +=          '<a style=" cursor:pointer;"  href="javascript:void(0)" id="docket_cancel" >Cancel</a>';
	            html +=     '</li>';
	            html +=     '<li style="position: absolute;margin-top: 25px;left: 77%;background-color: #003256;width: 10%;text-align: center;">';
	            html +=          '<a style=" cursor:pointer;"  href="javascript:void(0)" id="docket_ok"  >Ok</a>';
	            html +=     '</li>';
	            html += '</ul>';
	            $("#job_ticket_popup_wnd_title").after(html);
		        var error = false;
		        $(".repair_qty_docket").change(function(){
		            error = false;
		            $(this).removeClass("error_input");
		            var id = $(this).attr("rel");
		            var current_qty = parseInt($("#current_qty_"+id).html());
		            var value = $(this).val();
		            if(value>current_qty){
		                error = true;
		                alerts("Message","Please enter valid quantity.");
		                $(this).addClass("error_input");
		            }
		        });
		        $("#docket_ok").click(function(){
		            if(error)
		                return false;
		            var data = $("input","#job_ticket_form").serialize();
		            $.ajax({
		                url: "<?php echo URL.'/'.$controller.'/docket_repair_save' ?>",
		                type: "POST",
		                data: data,
		                async: false,
		                success: function(result){
		                    if(result!="ok")
		                        alerts("Message",result);
		                    else{
		                        generate_job_ticket_popup.data("kendoWindow").destroy();
		                        window.open("<?php echo URL.'/salesorders/docket_report/'?>"+$("#mongo_id").val());
		                    }
		                }
		            });

		        });
		        $("#docket_cancel").click(function(){
		            generate_job_ticket_popup.data("kendoWindow").close();
		        });
		    }
		</script>
	</span>
	<p class="clear"></p>
	<ul class="ul_mag clear bg3">
		<li class="hg_padd" style="width:1%"></li>

		<li class="hg_padd center_txt" style="width:3%">
			<?php echo translate('No'); ?>
			<span class="click_sort desc" field="no" type="desc" ></span>
		</li>

		<li class="hg_padd line_mg center_txt" style="width:15%">
			<?php echo translate('Task'); ?>
			<span class="click_sort desc" field="name" type="desc" ></span>
		</li>

		<li class="hg_padd line_mg center_txt" style="width:10%">
			<?php echo translate('Responsible'); ?>
			<span class="click_sort desc" field="our_rep" type="desc" ></span>
		</li>

		<li class="hg_padd center_txt" style="width:8%">
			<?php echo translate('Type'); ?>
			<span class="click_sort desc" field="type" type="desc" ></span>
		</li>

		<li class="hg_padd line_mg center_txt" style="width:10%">
			<?php echo translate('Work start'); ?>
			<span class="click_sort desc" field="work_start" type="desc" ></span>
		</li>
		<li class="hg_padd line_mg center_txt" style="width:8%">
			<?php echo translate('Production time'); ?>
		</li>
		<li class="hg_padd line_mg center_txt" style="width:10%">
			<?php echo translate('Work end'); ?>
			<span class="click_sort desc" field="work_end" type="desc" ></span>
		</li>

		<li class="hg_padd line_mg center_txt" style="width:6%;">
			<?php echo translate('Status'); ?>
			<span class="click_sort desc" field="status" type="desc" ></span>
		</li>

		<li class="hg_padd line_mg center_txt" style="width:16%">
			<?php echo translate('Note'); ?></li>

		<li class="hg_padd bor_mt" style="width:1%">
			<input type="hidden" id="task_id" value="">
			<script type="text/javascript">
				$(function(){
					// kiểm tra xem đã chọn company chưa
					var parameter_get = "?is_employee=1";
					window_popup("contacts", "Specify contact", "_tab_tasks", "", parameter_get);
					window_popup("equipments", "Specify assets", "_tab_tasks");
				});
				function after_choose_contacts_tab_tasks(contact_id, contact_name){
					$("#window_popup_contacts_tab_tasks").data("kendoWindow").close();
					var task_id = $('#task_id').val();

					// update
					if( task_id != '' ){
						var ul_contain = $("#Salesorder_Task_" + task_id);
						$("#TaskOurRepType", ul_contain).val("contacts");
						$("#TaskOurRepId", ul_contain).val(contact_id);
						$("#TaskOurRep", ul_contain).val(contact_name).trigger("change");

					}else{// add new
						$.ajax({
							url: "<?php echo URL; ?>/salesorders/tasks_add/<?php echo $salesorder_id; ?>/contacts/" + contact_id + "/" + contact_name,
							timeout: 15000,
							success: function(html){
								$("#load_subtab").html(html);
							}
						});
					}
					return false;

				}
				function after_choose_equipments_tab_tasks(equipment_id, equipment_name){
					$("#window_popup_equipments_tab_tasks").data("kendoWindow").close();
					var task_id = $('#task_id').val();
					// update
					if( task_id != '' ){
						var ul_contain = $("#Salesorder_Task_" + task_id);
						$("#TaskOurRepType", ul_contain).val("assets");
						$("#TaskOurRepId", ul_contain).val(equipment_id);
						$("#TaskOurRep", ul_contain).val(equipment_name).trigger("change");
					}else{// add new
						$.ajax({
							url: "<?php echo URL; ?>/salesorders/tasks_add/<?php echo $salesorder_id; ?>/assets/" + equipment_id + "/" + equipment_name,
							timeout: 15000,
							success: function(html){
								$("#load_subtab").html(html);
							}
						});
					}
					return false;
				}
			</script>
		</li>
	</ul>

<div id="so_task_update_sort">
<?php } // end remove_js ?>
		<?php

		for ($i=0; $i < 24; $i++) {
			$j = $i;
			if($j < 10)$j = '0'.$j;
			if($i > 7 && $i < 18){
				$arr_hour[$j.':00'] = array('name'=> $j.':00', 'value'=> $j.':00', 'class'=>'BgOptionHour');
				$arr_hour[$j.':30'] = array('name'=> $j.':30', 'value'=> $j.':30', 'class'=>'BgOptionHour');
			}else{
				$arr_hour[$j.':00'] = $j.':00';
				$arr_hour[$j.':30'] = $j.':30';
			}
		}

		$i = 1; $count = 0; $stt = 1;
		$view = $this->Common->check_permission('tasks_@_entry_@_view',$arr_permission);
		$delete = false;
		if($this->Common->check_permission('tasks_@_entry_@_delete',$arr_permission) )
			$delete = true;
		$total_production_time = 0;
		foreach ($arr_task as $key => $value) {
		?>
		<ul class="ul_mag clear bg<?php echo $i; ?>" id="Salesorder_Task_<?php echo $value['_id']; ?>">

			<?php echo $this->Form->hidden('Task._id', array( 'value' => $value['_id'] )); ?>
			<?php echo $this->Form->hidden('Task.salesorder_id', array( 'value' => $salesorder_id )); ?>

			<li class="hg_padd" style="width:1%">
				<?php if($view): ?>
				<a href="<?php echo URL; ?>/tasks/entry/<?php echo $value['_id']; ?>">
					<span class="icon_emp"></span>
				</a>
			<?php endif; ?>
			</li>
			<li class="hg_padd center_txt" style="width:3%">
				<?php echo $this->Form->input('Task.no', array(
						'class' => 'input_inner input_inner_w bg'.$i,
						'value' => $value['no'],
						'style' => 'text-align:center;'
				)); ?>
			</li>
			<li class="hg_padd line_mg" style="width:15%">
				<?php echo $this->Form->input('Task.name', array(
						'class' => 'input_inner input_inner_w bg'.$i,
						'value' => $value['name'],
						'style' => 'text-align:left;'
				)); ?>
			</li>
			<li class="hg_padd line_mg" style="width:10%">
				<?php echo $this->Form->hidden('Task.our_rep_type', array('value' => (isset($value['our_rep_type'])?$value['our_rep_type']:''))); ?>
				<?php echo $this->Form->input('Task.our_rep', array(
						'class' => 'input_inner float_left bg'.$i,
						'value' => $value['our_rep'],
						'readonly' => true,
				)); ?>
				<?php echo $this->Form->hidden('Task.our_rep_id', array(
						'value' => $value['our_rep_id']
				)); ?>
				<span class="iconw_m indent_dw_m" onclick="$('#task_id').val('<?php echo $value['_id']; ?>');$('#window_popup_contacts_tab_tasks').data('kendoWindow').open().center()"></span>
			</li>
			<li class="hg_padd" style="width:8%">
				<?php echo $this->Form->input('Task.type', array(
						'class' => 'input_select bg'.$i,
						'value' => (isset($value['type'])?$value['type']:''),
				)); ?>
				<?php echo $this->Form->hidden('Task.type_id', array('value' => $value['type_id'])); ?>
				<script type="text/javascript">
					$(function () {
						$("#TaskType", "#Salesorder_Task_<?php echo $value['_id']; ?>").combobox(<?php echo json_encode($arr_tasks_type); ?>);
					});
				</script>
			</li>
			<li class="hg_padd line_mg center_txt" style="width:10%">
				<div class="date">
					<span class="float_left" style="width: 47%;">
						<?php echo $this->Form->hidden('Task.work_start_old', array('value' => $value['work_start']->sec )); ?>
						<?php echo $this->Form->input('Task.work_start', array(
								'class' => 'JtSelectDate force_reload input_inner input_inner_w bg'.$i,
								'style' => 'width: 70px',
								'id' => 'Taskwork_start'.rand(0,99999999),
								'readonly' => true,
								'value' => date('m/d/Y', $value['work_start']->sec)
						)); ?>
					</span>
				</div>
				<div class="select_inner width_select" style="width: 41%; margin: 0;">
					<div class="styled_select" style="margin: 0;">
						<?php echo $this->Form->input('Task.work_start_hour', array(
									'style' => 'margin-top: -3px;',
									'class' => 'force_reload',
									'options' => $arr_hour,
									'value' => date('H:i', $value['work_start']->sec)
							)); ?>
					</div>
				</div>
			</li>
			<li class="hg_padd line_mg center_txt" style="width:8%">
				<?php 
					$productiontime = $value['work_end']->sec - $value['work_start']->sec;
					$total_production_time += $productiontime;

				?>
				<?php echo $this->Form->input('Task.productiontime', array(
						'class' => 'input_inner input_inner_w bg'.$i,
						'value' => sec2hour($productiontime),
						'disabled' => true,
						'style' => 'text-align:right;'
				)); ?>
			</li>
			<li class="hg_padd line_mg center_txt" style="width:10%">
				<div class="date">
					<span class="float_left" style="width: 47%;">
						<?php echo $this->Form->hidden('Task.work_end_old', array('value' => $value['work_end']->sec )); ?>
						<?php 
							$array = array(
								'class' => 'JtSelectDate force_reload input_inner input_inner_w bg'.$i,
								'style' => 'width: 70px',
								'readonly' => true,
								'id' => 'Taskwork_end'.rand(0,99999999),
								'value' => date('m/d/Y', $value['work_end']->sec)
							);
							echo $this->Form->input('Task.work_end', $array); 
						?>
					</span>
				</div>

				<div class="select_inner width_select" style="width: 41%; margin: 0;">
					<div class="styled_select" style="margin: 0;">
						<?php 
							$array = array(
								'options' => $arr_hour,
								'style' => 'margin-top: -3px;',
								'class' => 'force_reload',
								'value' => date('H:i', $value['work_end']->sec)
							);
							echo $this->Form->input('Task.work_end_hour', $array); 
						?>
					</div>
				 </div>
			</li>
			<li class="hg_padd" style="width:6%;">
				<?php echo $this->Form->input('Task.status', array(
						'class' => 'input_select bg'.$i,
						'value' => (isset($value['status'])?$value['status']:''),
				)); ?>
				<?php echo $this->Form->hidden('Task.status_id', array('value' => $value['status_id'])); ?>
				<script type="text/javascript">
					$(function () {
						$("#TaskStatus", "#Salesorder_Task_<?php echo $value['_id']; ?>").combobox(<?php echo json_encode($arr_tasks_status); ?>);
					});
				</script>
			</li>
			<li class="hg_padd line_mg center_txt" style="width:16%">
				<?php
					$arr_noteactivity = $model_noteactivity->select_one(array('module' => 'Task', 'module_id' => $value['_id']), array('_id', 'content'), array('_id' => -1));
					$content_task = '';
					$content_noteactivity_id = '';
					if(isset($arr_noteactivity['content'])){
						$content_task = $arr_noteactivity['content'];
						$content_noteactivity_id = $arr_noteactivity['_id'];
					}
					echo $this->Form->input('Task.content', array(
							'class' => 'input_inner input_inner_w bg'.$i,
							'value' => $content_task,
							'style' => 'text-align:left;'
					)); 
					echo $this->Form->hidden('Task.noteactivity_id', array(
							'value' => $content_noteactivity_id,
					)); 
				?>
			</li>
			<li class="hg_padd bor_mt center_txt" style="width:1%">
				<?php if($delete): ?>
				<div class="middle_check">
					<a title="Delete link" href="javascript:void(0)" onclick="salesorders_task_delete('<?php echo $value['_id']; ?>')">
						<span class="icon_remove2"></span>
					</a>
				</div>
				<?php endif; ?>
			</li>
		</ul>

		<?php $i = 3 - $i; $count += 1;
			}

			$count = 8 - $count;
			if( $count > 0 ){
				for ($j=0; $j < $count; $j++) { ?>
				<ul class="ul_mag clear bg<?php echo $i; ?>"></ul>
		  <?php $i = 3 - $i;
				}
			}
		?>
		<script type="text/javascript">
			$(function(){
				<?php if(!$this->Common->check_permission('tasks_@_entry_@_edit',$arr_permission)): ?>
				$("input,select","#so_task_update_sort").each(function(){
					$(this).attr('readonly',true);
				    $(this).attr('disabled',true);
				});
				$(".combobox","#so_task_update_sort").each(function(){
					$(this).remove();
				});
				$("#so_task_update_sort").find('[onclick]').each(function(){
					$(this).remove();
			  	});
				<?php endif; ?>
				<?php if(!$this->Common->check_permission('tasks_@_entry_@_view',$arr_permission)): ?>
				$("#so_task_update_sort").find('a').each(function(){
					$(this).remove();
			  	});
				<?php endif; ?>
			});
		</script>
<?php if( !isset($remove_js) ){ ?>
</div>

	<span class="title_block bo_ra2">
		<span class="float_left bt_block">
			<?php echo translate('Click to view full details'); ?>
		</span>
		<span style="float: right; margin: -2px 37% 0 0; width: 31%;">
			<input type="text" id="total_production_time" style="float: right;width: 31%; text-align:right;" disabled="disabled" value="<?php echo sec2hour($total_production_time); ?>" />
			<span style="float: right; margin:5px 3px 0 0;">Total time </span>
		</span>
	</span>
</div>

<?php if($this->request->is('ajax')){ ?>
<script type="text/javascript">
$(function(){
	<?php if($this->Common->check_permission('tasks_@_entry_@_add',$arr_permission)): ?>
	input_show_select_calendar(".JtSelectDate");
	<?php endif; ?>
});
</script>
<?php } ?>

<script type="text/javascript">

$(function() {
	$(":input", "#load_subtab").change(function() {
		salesorders_tasks_auto_save(this);
	});

	$("ul.bg3 li.line_mg", "#load_subtab").click(function(){
		var span = $("span", this);

		var field = span.attr("field");
		if(field == undefined)return false;
		var type = span.attr("type");
		if( type == "asc" ){
			span.attr("type", "desc");
			span.addClass("desc").removeClass("asc");
		}else{
			span.attr("type", "asc");
			span.addClass("asc").removeClass("desc");
		}

		$.ajax({
			url: '<?php echo URL; ?>/salesorders/tasks/' + $("#mongo_id").val(),
			timeout: 15000,
			type: 'POST',
			data: { field: field, type: type },
			success: function(html){
				$("#so_task_update_sort").html(html);
				$(":input", "#so_task_update_sort").change(function() {
					salesorders_tasks_auto_save(this);
				});
				<?php if($this->Common->check_permission('tasks_@_entry_@_add',$arr_permission)): ?>
				input_show_select_calendar(".JtSelectDate", "#so_task_update_sort");
				<?php endif; ?>
			}
		});
	});
});

var so_task_check_working_hour_out = 1;
function salesorders_tasks_auto_save(object, moreData) {

	var contain = $(object).parents('ul');
	var data = $(":input", contain).serialize();
	if(moreData != undefined)
		data += "&"+moreData;

	$("#TaskTypeId", contain).val($("#TaskType", contain).val());
	$("#TaskStatusId", contain).val($("#TaskStatus", contain).val());

	$.ajax({
		url: '<?php echo URL; ?>/salesorders/tasks_auto_save/' + $(object).attr("name") + '/' + so_task_check_working_hour_out,
		timeout: 15000,
		type: "post",
		data: data,
		success: function(html) {
			if(html == 'refresh')
				location.reload();
			else if(html == "out_of_working_hour"){
				$(object).addClass('error_input');
				confirms("Warning: ", 'Work time is out of working hour of this employee !!!<br> Are you sure to force to assign time to work?',
						 function(){
							so_task_check_working_hour_out = 1;
							salesorders_tasks_auto_save(object);
						},
						 function(){
							$("#tasks").click();
						});

			}else if(html == "work_start_salesorder_date"){
				$(object).addClass('error_input');
				alerts("Error: ", '"Work start" can not less than "Order date" of this SO');

			}else if(html == "work_end_payment_due_date"){
				$(object).addClass('error_input');
				alerts("Error: ", '"Work End" can not greater than "Due date" of this SO');

			}else if(html == "work_start_due_date"){
				$(object).addClass('error_input');
				alerts("Error: ", '"Work start" can not greater than "Due date" of this SO');

			}else if(html == "error_work_start"){
				$(object).addClass('error_input');
				alerts("Error: ", '"Work start" can not set into the past');

			}else if(html == "error_work_end"){
				$(object).addClass('error_input');
				alerts("Error: ", '"Work End" can not set into the past');

			}else if(html == "error_time"){
				$(object).addClass('error_input');
				alerts("Error: ", '"Work End" can not less than "Work Start"');
			}else if(html == "ok_refresh"){
				$("#tasks").click();
			} else if(html.indexOf('multi_task') != -1){
				html = html.split("||");
				var our_rep = $("#TaskOurRep",contain).val();
				confirms("Message",our_rep+" is not available from "+html[1]+" to "+html[2]+"? Are you sure that "+our_rep+" can take these multi-tasks?",
					function(){
						salesorders_tasks_auto_save(object,"is_multi_task=1");
					}, function(){
						$("#tasks").click();
						return false;
					});
			}else if(html != "ok"){
				alerts("Error: ", html);
			}else{
				if( $(object).hasClass("force_reload") ){
					$("#tasks").click();
				}
			}
			console.log(html);
		}
	});
}
<?php if($this->Common->check_permission('tasks_@_entry_@_delete',$arr_permission)): ?>
function salesorders_task_delete(key){

	confirms( "Message", "Are you sure you want to delete?",
		function(){
			$.ajax({
				url: '<?php echo URL; ?>/tasks/module_delete/'+ key,
				success: function(html){
					if(html == "ok"){
						$("#Salesorder_Task_" + key).remove();
					}
					console.log(html);
				}
			});
		},function(){
			//else do somthing
	});

}
<?php endif; ?>
</script>
<?php }  // end remove_js ?>
<?php endif; ?>