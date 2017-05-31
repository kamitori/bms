<div class="clear_percent_6a float_left" style="width:100%">
	<div class="full_width">
		<div class="tab_1 full_width">
			<span class="title_block bo_ra1">
				<span class="float_left h_form">
					<span class="fl_dent"><h4><?php echo translate('End result'); ?> / <?php echo translate('comments'); ?></h4></span>
					<!-- <a href="" title="Link a contact">
						<span class="icon_notes top_f"></span>
					</a> -->
				</span>
			</span>
			<?php echo $this->Form->input('Enquiry.result_conmments', array(
						'class' => 'area_t4',
						'rows' => 3,
						'onchange' => 'enquiry_end_result_conmments(this)'
			)); ?>
			<script type="text/javascript">
			<?php if($this->Common->check_permission('enquiries_@_entry_@_edit', $arr_permission)){ ?>
			function enquiry_end_result_conmments(object){
				$.ajax({
					url: '<?php echo URL; ?>/enquiries/other_result_conmments/'+ $("#EnquiryId").val(),
					type: 'POST',
					data: { result_conmments: $(object).val() },
					success: function(html){
						if( html != "ok" ){
							alerts( "Error: ", html );
						}
					}
				});
			});
			<?php  }else{ ?>
				$(function(){
					$(".icon_notes").remove();
					$("#EnquiryResultConmments").attr("disabled",true);
				});
			<?php } ?>

			</script>
			<span class="title_block bo_ra2"></span>
		</div><!--END Tab1 -->
	</div>
</div>
