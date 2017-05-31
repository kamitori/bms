<style type="text/css">
ul.ul_mag li.hg_padd {
overflow: visible !important;
}
</style>
<div class="clear_percent_11 float_left right_pc" style="width: 76%;height: 330px;">
	<div class="tab_1 full_width">
		<span class="title_block bo_ra1">
			<span class="fl_dent"><h4><?php echo translate('Leave / holidays for this employee'); ?></h4></span>
			<a class="add-item" href="<?php echo URL.'/contacts/leave_add/'.$contact_id ?>" data-container-id="contacts_leave_left" title="Add new leave / holiday"><span class="icon_down_tl top_f"></span></a>
			<form>
				<div class="float_left hbox_form dent_left_form">
					<input class="btn_pur size_width" type="button" value="Leave chart">
				</div>
			</form>
		</span>
		<p class="clear"></p>
		<ul class="ul_mag clear bg3">
			<li class="hg_padd" style="width:1.5%"></li>
			<li class="hg_padd" style="width:9%"><?php echo translate('Purpose'); ?></li>
			<li class="hg_padd center_txt" style="width:5%"><?php echo translate('Used'); ?></li>
			<li class="hg_padd center_txt" style="width:9%;"><?php echo translate('Date from'); ?></li>
			<li class="hg_padd center_txt" style="width:9%;"><?php echo translate('Date to'); ?></li>
			<li class="hg_padd center_txt" style="width:3%;"><?php echo translate('Clash'); ?></li>
			<li class="hg_padd" style="width:9%;"><?php echo translate('Status'); ?></li>
			<li class="hg_padd" style="width:33%;"><?php echo translate('Details'); ?></li>
			<li class="hg_padd" style="width:7%;"><?php echo translate('Don\'t deduct'); ?></li>
			<li class="hg_padd bor_mt" style="width:1.5%"></li>
		</ul>

		<div class="container_same_category" id="contacts_leave_left">
		<?php
			$i = 2; $count = $total_left = 0;
				foreach ($arr_leave as $key => $value) {
					if( $value['deleted'] )continue;

					$total_left += $value['used'];
				?>

				<ul class="ul_mag clear bg<?php echo $i; ?>" id="Contacts_Leave_<?php echo $key; ?>" rel="<?php echo $key; ?>">
					<li class="hg_padd" style="width:1.5%">
						 <a href="<?php echo URL; ?>/contacts/leave_view/<?php echo $contact_id;?>/<?php echo $key;?>">
							<span class="icon_emp"></span>
						</a>
					</li>
					<li class="hg_padd" style="width:9%">
						<?php echo $this->Form->input('Leave.purpose', array(
							'class' => 'input_select bg'.$i,
							'rel' => $key,
							'value' => (isset($value['purpose'])?$value['purpose']:''),
						)); ?>
						<?php echo $this->Form->hidden('Leave.purpose_id', array('value' => $value['purpose_id'])); ?>
						<script type="text/javascript">
							$(function () {
								$("#LeavePurpose", "#Contacts_Leave_<?php echo $key; ?>").combobox(<?php echo json_encode($arr_leave_purpose); ?>);
							});
						</script>
					</li>
					<li class="hg_padd center_txt" style="width:5%">
						<?php echo $this->Form->input('Leave.used', array(
							'class' => 'input_inner input_inner_w bg'.$i,
							'value' => (is_numeric($value['used'])?(int)$value['used']:''),
							'style' => 'text-align:center;'
						)); ?>
					</li>
					<li class="hg_padd center_txt" style="width:9%;">
						<?php echo $this->Form->input('Leave.date_from', array(
							'class' => 'JtSelectDate input_inner input_inner_w bg'.$i,
							'style' => 'width: 70px',
							'id' => 'Leave_date_from'.rand(0,99999999),
							'readonly' => true,
							'value' => date('m/d/Y', $value['date_from']->sec)
						)); ?>
					</li>
					<li class="hg_padd center_txt" style="width:9%;">
						<?php echo $this->Form->input('Leave.date_to', array(
							'class' => 'JtSelectDate input_inner input_inner_w bg'.$i,
							'style' => 'width: 70px',
							'id' => 'Leave_date_to'.rand(0,99999999),
							'readonly' => true,
							'value' => date('m/d/Y', $value['date_to']->sec)
						)); ?>
					</li>
					<li class="hg_padd center_txt" style="width:3%;">
						<!-- <span class="Late">X</span> -->
					</li>
					<li class="hg_padd" style="width:9%;">
						<?php echo $this->Form->input('Leave.status', array(
							'class' => 'input_select bg'.$i,
							'rel' => $key,
							'value' => (isset($value['status'])?$value['status']:''),
						)); ?>
						<?php echo $this->Form->hidden('Leave.status_id', array('value' => $value['status_id'])); ?>
						<script type="text/javascript">
							// $(function () {
							// 	$("#LeaveStatus", "#Contacts_Leave_<?php echo $key; ?>").combobox(<?php echo json_encode($arr_leave_status); ?>);
							// });
						</script>
					</li>
					<li class="hg_padd" style="width:33%;">
						<?php echo $this->Form->input('Leave.details', array(
							'class' => 'input_inner input_inner_w bg'.$i,
							'value' => $value['details']
						)); ?>
					</li>
					<li class="hg_padd" style="width:7%;">
						<input type="hidden" name="data[Leave][dontdeduct]" id="LeaveDefault_" value="0">
						<div class="select_inner width_select" style="width: 100%; margin: 0;margin-left: 23px;">
							<label class="m_check2">
								<?php echo $this->Form->input('Leave.dontdeduct', array(
											'type' => 'checkbox',
											'checked' => (isset($value['dontdeduct'])?$value['dontdeduct']:''),
											'class' => 'checkbox-default'
								));?>
								<span></span>
							</label>
						</div>
					</li>
					<li class="hg_padd bor_mt" style="width:1.5%">
						<div class="middle_check">
							<a title="Delete link" href="javascript:void(0)" onclick="contacts_leave_delete(<?php echo $key; ?>)">
								<span class="icon_remove2"></span>
							</a>
						</div>
					</li>
				</ul>

				<?php $i = 3 - $i; $count += 1;
				}

			$count = 8 - $count;
			if( $count > 0 ){
				for ($j=0; $j < $count; $j++) { ?>
				<ul class="ul_mag clear bg<?php echo $i; ?>">
				</ul>
			  <?php $i = 3 - $i;
					}
				}
			?>
		</div>
		<span class="title_block bo_ra2">
			<span class="bt_block float_right no_bg">
				<?php echo translate('Days leave due to'); ?>
				<input class="input_w2" readonly="true" type="text" id="days_leave_due_to">
			</span>
			<span class="bt_block float_right no_bg dent_bl_txt3">
				<?php echo translate('Total'); ?>
				<input class="input_w2" type="text" readonly="true" id="total_left" value="<?php echo (int)$total_left; ?>">
			</span>
		</span>
	</div>
</div>
<div class="clear_percent_10 float_left no_right" style="width: 23%;">
	<div class="tab_1 full_width">
		<span class="title_block bo_ra1">
			<span class="fl_dent"><h4><?php echo translate('Days accummulated'); ?></h4></span>
			<a class="add-item" href="<?php echo URL.'/contacts/leave_accummulated_add/'.$contact_id ?>" data-container-id="contacts_leave_right" title="Add new day accummulated"><span class="icon_down_tl top_f"></span></a>
		</span>
		<p class="clear"></p>
		<ul class="ul_mag clear bg3">
			<li class="hg_padd" style="width:23%"><?php echo translate('Per month'); ?></li>
			<li class="hg_padd center_txt" style="width:23%"><?php echo translate('Start'); ?></li>
			<li class="hg_padd center_txt" style="width:23%"><?php echo translate('End'); ?></li>
			<li class="hg_padd center_txt" style="width:15%"><?php echo translate('Total'); ?></li>
			<li class="hg_padd bor_mt" style="width:3%"></li>
		</ul>

		<div id="contacts_leave_right" class="container_same_category" >
		<?php
		$i = 2; $count = $total_right = 0;
			foreach ($arr_accummulated as $key => $value) {
				if( $value['deleted'] )continue;
			?>

			<ul class="ul_mag clear bg<?php echo $i; ?>" id="Contacts_Accummulated_<?php echo $key; ?>" rel="<?php echo $key; ?>">
				<li class="hg_padd" style="width:23%">
					<?php echo $this->Form->input('Accummulated.per_month', array(
						'class' => 'input_inner center_txt input_inner_w bg'.$i,
						'value' => (is_numeric($value['per_month'])?$this->Common->format_currency($value['per_month'],1):'')
					)); ?>
				</li>
				<li class="hg_padd center_txt" style="width:23%">
					<?php echo $this->Form->input('Accummulated.start', array(
						'class' => 'JtSelectDate input_inner input_inner_w bg'.$i,
						'style' => 'width: 70px',
						'id' => 'Accummulated_start'.rand(0,99999999),
						'readonly' => true,
						'value' => date('m/d/Y', $value['start']->sec)
					)); ?>
				</li>
				<li class="hg_padd center_txt" style="width:23%">
					<?php echo $this->Form->input('Accummulated.end', array(
						'class' => 'JtSelectDate input_inner input_inner_w bg'.$i,
						'style' => 'width: 70px',
						'id' => 'Accummulated_end'.rand(0,99999999),
						'readonly' => true,
						'value' => date('m/d/Y', $value['end']->sec)
					)); ?>
				</li>
				<li class="hg_padd center_txt" style="width:15%">
					<?php echo $this->Form->input('Accummulated.total', array(
						'class' => 'input_inner center_txt input_inner_w bg'.$i,
						'value' => (is_numeric($value['total'])?$this->Common->format_currency($value['total'],1):''),
					));

						$total_right += $value['total'];
					?>
				</li>
				<li class="hg_padd bor_mt" style="width:3%">
					<div class="middle_check">
						<a title="Delete link" href="javascript:void(0)" onclick="contacts_leave_accummulated_delete(<?php echo $key; ?>)">
							<span class="icon_remove2"></span>
						</a>
					</div>
				</li>
			</ul>
			<?php $i = 3 - $i; $count += 1;
				}

			$count = 8 - $count;
			if( $count > 0 ){
				for ($j=0; $j < $count; $j++) { ?>
				<ul class="ul_mag clear bg<?php echo $i; ?>">
				</ul>
			  <?php $i = 3 - $i;
					}
				}
			?>
		</div>
		<span class="title_block bo_ra2">
			<span class="bt_block float_right no_bg">
				<?php echo translate('Total'); ?>
				<input class="input_w2" type="text" readonly="true" id="total_right" value="<?php echo $this->Common->format_currency($total_right, 1); ?>">
			</span>
		</span>
	</div>
</div>

<script type="text/javascript">
$(function(){

	 $(function(){
        $(".container_same_category").mCustomScrollbar({
            scrollButtons:{
                enable:false
            },
            advanced:{
                updateOnContentResize: true,
                autoScrollOnFocus: false,
            }
        });
    })
	$(".add-item").click(function(){
		var url = $(this).attr("href");
		var div_id = $(this).attr("data-container-id");
		$.ajax({
			url : url,
			success: function(result){
				$("#load_subtab").html(result);
			}
		})
		return false;
	});

	<?php if( $this->request->is('ajax') ){ ?>
	input_show_select_calendar(".JtSelectDate", "#load_subtab");
	contacts_leave_total();
	<?php }else{ ?>
	$("#days_leave_due_to").val("<?php echo ($total_right - $total_left); ?>");
	<?php } ?>



	$(":input", "#contacts_leave_left").change(function() {

		var ul_contain = $(this).parents("ul");

		object = $(this);
		$.ajax({
			url: '<?php echo URL; ?>/contacts/leave_auto_save/<?php echo $contact_id; ?>/' + ul_contain.attr("rel"),
			timeout: 15000,
			type:"post",
			data: $(":input", ul_contain).serialize(),
			success: function(html){
				if( html != "ok" )
					alerts("Error", html);
				else
					if( $(object).attr("name") == "data[Leave][used]" || $(object).attr("name") == "data[Leave][dontdeduct]" )
						contacts_leave_total();
			}
		});
	});

	$(":input", "#contacts_leave_right").change(function() {

		var ul_contain = $(this).parents("ul");

		object = $(this);
		$.ajax({
			url: '<?php echo URL; ?>/contacts/leave_accummulated_auto_save/<?php echo $contact_id; ?>/' + ul_contain.attr("rel"),
			timeout: 15000,
			type:"post",
			data: $(":input", ul_contain).serialize(),
			success: function(html){
				if( html != "ok" )
					alerts("Error", html);
				else
					if( $(object).attr("name") == "data[Accummulated][total]" )
						contacts_leave_total();
			}
		});
	});
});

function contacts_leave_total(){
	$.ajax({
		url: '<?php echo URL; ?>/contacts/leave_total/<?php echo $contact_id; ?>',
		timeout: 15000,
		success: function(html){
			var json = JSON.parse(html);
			$("#days_leave_due_to").val(json.days_leave_due_to);
			$("#total_left").val(json.total_left);
			$("#total_right").val(json.total_right);
		}
	});
}

function contacts_leave_delete(key){
	confirms( "Message", "Are you sure you want to delete?",
	    function(){
	        $.ajax({
				url: '<?php echo URL; ?>/contacts/leave_delete/'+ key + '/<?php echo $contact_id; ?>',
				success: function(html){
					$("#Contacts_Leave_" + key).remove();
				}
			});
	    },function(){
	        //else do somthing
	});

}

function contacts_leave_accummulated_delete(key){
	confirms( "Message", "Are you sure you want to delete?",
	    function(){
	        $.ajax({
				url: '<?php echo URL; ?>/contacts/leave_accummulated_delete/'+ key + '/<?php echo $contact_id; ?>',
				success: function(html){
					$("#Contacts_Accummulated_" + key).remove();
				}
			});
	    },function(){
	        //else do somthing
	});

}
</script>
<?php echo $this->Js->writeBuffer(); ?>