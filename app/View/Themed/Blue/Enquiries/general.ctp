<div >
	<div class="block_dent8">
		<div class="clear_percent_7 float_left right_pc" style="width: 20%; margin-right:1%">
			<div class="tab_1 full_width">
				<span class="title_block bo_ra1">
					<span class="float_left">
						<span class="fl_dent">
							<span class="fl_dent">
								<h4>Requirements</h4>
							</span>
							<a title="Insert timestamp" id="req" >
								<span class="icon_notes top_f"></span>
							</a>
							<a href="<?php echo URL ;?>/enquiries/print_requirements_pdf/<?php echo $enquiry_id;?>" target="_blank">
								<input class="btn_pur" id="printexport_products" type="button" value="Export PDF" style="width: 66px;">
							</a>
						</span>
					</span>
				</span>
			<form>
				<textarea id="tex" class="area_t3" style="height: 188px" onchange="enquiries_general_details_save(this)"><?php if(isset($arr_enquiry['detail']))echo $arr_enquiry['detail']; ?></textarea>
				<script type="text/javascript">
				function enquiries_general_details_save(object){
					var enquiry_id = "";
					$.ajax({
						url: "<?php echo URL; ?>/enquiries/general_auto_save/<?php echo $enquiry_id; ?>",
						timeout: 15000,
						type: "POST",
						data: { detail: $(object).val() },
						success: function(html){
							if(html != "ok"){
								alerts("Error: ", html);
							}
						}
					});
					return false;
				}
				</script>
			</form>
				<span class="title_block bo_ra2"></span>
			</div><!--END Tab1 -->
		</div>
		<div class="clear_percent_6 float_left no_right" style="width: 79%; margin-right: 0px !important;">
			<div class="clear_percent_10 float_left right_pc" style="width: 30.5%; margin-right: 1.2%;">
				<div class="tab_1 full_width">
					<span class="title_block bo_ra1">
						<span class="float_left">
							<span class="fl_dent">
								<span class="fl_dent">
									<h4>Keywords</h4>
								</span>
							</span>
						</span>
					</span>
					<div class="select_down" style="width: 100%">
					<?php for($i = 0; $i<8; $i++): ?>
					<p class="clear">
						<div class="indent_input_tp" style="height: 21px;">
							<?php echo $this->Form->input('Enquiry.kw'.$i, array(
								'class' => 'input_select input_se keywords',
								'readonly' => true,
								'rel'	=> $i,
								'value' => isset($arr_enquiry['keywords'][$i])?$arr_enquiry['keywords'][$i]:'',
							)); ?>
							<?php echo $this->Form->hidden('Enquiry.kw'.$i.'Id'); ?>

							<script type="text/javascript">
								$(function () {
									$("#EnquiryKw<?php echo $i; ?>").combobox(<?php echo json_encode($arr_enquiry_keywords); ?>);
								});
							</script>
						</div>
					</p>
				<?php endfor; ?>
					</div>
					<p class="clear" style="height:14px;"></p>
					<span class="title_block bo_ra2">
						<span class="bt_block">Enter search keywords</span>
					</span>
				</div><!--END Tab1 -->
			</div>

			<div class="clear_percent_11 float_left no_right" style="width: 68.1%;">
				<?php echo $this->element('communications'); ?>
			</div>
		</div>
	</div>
</div>
<div style="clear:both"></div>
<script type="text/javascript">

$(function(){
	<?php if($this->Common->check_permission('enquiries_@_entry_@_edit', $arr_permission)){ ?>
	$("#req").click(function(){
		var text = $("#tex").val();
		if( $.trim(text) != "" ){
			text = text + "\n" + "<?php echo $_SESSION['arr_user']['contact_name'];?> - <?php echo date('M d, Y H:i',time()); ?>:";
		}else{
			text = "<?php echo $_SESSION['arr_user']['contact_name'];?> - <?php echo date('M d, Y H:i'); ?>:";
		}
		$("#tex").val(text);
	});

	$(".keywords").change(function(){
		var key = $(this).attr('rel');
		var value = $(this).val();
		var enquiry_id = "";
		$.ajax({
			url: "<?php echo URL; ?>/enquiries/general_keywords_auto_save/<?php echo $enquiry_id; ?>",
			timeout: 15000,
			type: "POST",
			data: { key: key,value: value},
			success: function(html){
				if(html != "ok"){
					alerts("Error: ", html);
				}
			}
		});
	});

	<?php }else{ ?>

	$(".container_same_category").find("a").each(function(){
		$(this).remove();
	});
	$("form :input", "#<?php echo $controller; ?>_sub_content").each(function() {
		$(this).attr("disabled", true).css("background-color", "transparent");
	});
	$(".combobox_selector, .icon_notes", "#<?php echo $controller; ?>_sub_content").each(function() {
		$(this).remove();
	});

	<?php } ?>
})
</script>