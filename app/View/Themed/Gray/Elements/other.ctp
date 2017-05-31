<?php if($this->Common->check_permission($controller.'_@_other_tab_@_view',$arr_permission)): ?>
<div class="clear_percent" style="width:100%; margin:0px;">
	<div class="clear_percent_6a float_left">
		<div class="clear">
			<div class="clear_percent_15 float_left">
				<div class="tab_1">
				 <form id="other_record" method="POST">
					<p class="clear">
						<span class="label_1 float_left fixbor"><?php echo translate('Record entry type'); ?></span>
					<div class="width_in float_left indent_input_tp">
						<input type="text" class="input_1 float_left" name="record_type" id="record_type" value="<?php echo (isset($data['record_type'])? $data['record_type'] : ''); ?>" />
						<input type="hidden" value="<?php echo (isset($data['record_type'])? $data['record_type'] : ''); ?>" />
						<script type="text/javascript">
							$(function(){
								$("#record_type").combobox(<?php echo json_encode(array('Line entry'=>'Line entry','Production'=>'Production')); ?>);
							});
						</script>
					</div>
					<p class="clear"></p>
					</p>
					<p class="clear">
						<span class="label_1 float_left">Fax</span>
					<div class="width_in float_left indent_input_tp">
						<input class="input_1 float_left" type="text" id="fax" name="fax" value="<?php if(isset($data['fax'])) echo $data['fax'];?>">
						<span class="icon_down_pl"></span>
					</div>
					</p>
					<p class="clear">
						<span class="label_1 float_left">Heading</span>
					<div class="width_in float_left indent_input_tp">
						<input class="input_1 float_left" type="text" value="<?php if(isset($data['name'])) echo $data['name'];?>"  name="name" id="name" >
					</div>
					</p>
					<p class="clear">
						<span class="label_1 float_left">Include signature</span>
					<div class="width_in float_left indent_input_tp">
						<div class="in_active2">
							<label class="m_check2">
								<input type="checkbox" <?php if(isset($data['include_signature'])&&$data['include_signature']==1){?> checked <?php }?> id="include_signature" />
								<span class="bx_check dent_chk"></span>
								<input type="hidden" name="include_signature" class="include_signature" value="<?php echo (isset($data['include_signature'])&&$data['include_signature']==1 ? 1:0 ); ?>" />
							</label>
							<span class="inactive dent_check"></span>
							<p class="clear"></p>
						</div>
					</div>
					</p>
					<p class="clear">
						<span class="label_1 float_left">Inc. sign off section</span>
					<div class="width_in float_left indent_input_tp">
						<div class="in_active2">
							<label class="m_check2">
								<input type="checkbox" <?php if(isset($data['sign_off_section'])&&$data['sign_off_section']==1){?> checked <?php }?> id="sign_off_section" />
								<span class="bx_check dent_chk"></span>
								<input type="hidden" value="<?php echo (isset($data['sign_off_section'])&&$data['sign_off_section']==1 ? 1:0 ); ?>" class="sign_off_section" name="sign_off_section">
							</label>
							<span class="inactive dent_check"></span>
							<p class="clear"></p>
						</div>
					</div>
					</p>
					<p class="clear">
						<span class="label_1 float_left">Use own letterhead</span>
					<div class="width_in float_left indent_input_tp">
						<div class="in_active2">
							<label class="m_check2">
								<input type="checkbox"  <?php if(isset($data['own_letterhead'])&&$data['own_letterhead']==1){?> checked <?php }?> id="own_letterhead" />
								<span class="bx_check dent_chk"></span>
								<input type="hidden" value="<?php echo (isset($data['own_letterhead'])&&$data['own_letterhead']==1 ? 1:0 ); ?>" class="own_letterhead" name="own_letterhead">
							</label>
							<span class="inactive dent_check"></span>
							<p class="clear"></p>
						</div>
					</div>
					</p>
					<p class="clear">
						<span class="label_1 float_left hgt5 fixbor2" style="height:70px !important">Include images</span>
					<div class="width_in float_left indent_input_tp">
						<div class="in_active2">
							<label class="m_check2">
								<input type="checkbox" id="include_images" <?php if(isset($data['include_images'])&&$data['include_images']==1){?> checked <?php }?> />
								<span class="bx_check dent_chk"></span>
								<input type="hidden" value="<?php echo (isset($data['include_images'])&&$data['include_images']==1 ? 1:0 ); ?>" class="include_images" name="include_images" />
							</label>
							<span class="inactive dent_check"></span>
							<span class="float_left dent_check color_hidden">linked to Products module</span>
							<p class="clear"></p>
						</div>
					</div>
					</p>
					<p class="clear"></p>
					</form>
				</div><!--END Tab1 -->

			</div>
			<div class="clear_percent_14 float_left">
				<div class="tab_1 full_width">
					<span class="title_block bo_ra1">
						<span class="fl_dent"><h4>Comments on <?php echo $controller; ?></h4></span>
					</span>
					<form id="other_comment">
						<textarea class="area_t2"  style="height:165px;background-color: transparent"><?php if(isset($data['other_comment'])) echo $data['other_comment'];?></textarea>
					</form>
					<span class="title_block bo_ra2">
						<p class="cent">These details appear on the print / email version of the document</p>
					</span>
				</div><!--END Tab1 -->
			</div>
			<p class="clear"></p>
		</div>
		<div class="full_width block_dent9 " style="width: 143%">
			<?php echo $this->element('communications'); ?>
		</div>
	</div>
	<form id="commission_form">
		<div class="clear_percent_7a float_left">
			<div class="tab_1 full_width">
				<span class="title_block bo_ra1">
					<span class="float_left">
						<span class="fl_dent"><h4>Commission</h4></span>
					</span>
				</span>
				<div class="tab_2_inner">
					<p class="clear">
						<span class="label_1 float_left minw_lab2">Name</span>
					<div class="width_in3a float_left indent_input_tp">
						<input name="contact_name" id="contact_name" class="input_1 float_left" type="text" value="<?php echo (isset($data['commission']['contact_name']) ?  $data['commission']['contact_name'] : ''); ?>">
						<input name="contact_id" id="contact_id" type="hidden" value="<?php echo (isset($data['commission']['contact_id']) ?  $data['commission']['contact_id'] : '') ?>">
						<span id="open_employee_popup" class="icon_down_new float_right"></span>
						<script type="text/javascript">
							$(function(){
								window_popup('contacts','Specify Employee','_commission_employee','open_employee_popup','?is_employee=1');
							})
						</script>
					</div>
					</p>
					<?php
						$sales_amount = (isset($data['commission']['sales_amount']) ?  (float)$data['commission']['sales_amount'] : (float)$data['sum_sub_total']);
						$sales_cost = (isset($data['commission']['sales_cost']) ?  (float)$data['commission']['sales_cost'] : 0);
						$profit = (isset($data['commission']['profit']) ?  (float)$data['commission']['profit'] : 0);
						$rate = (isset($data['commission']['rate']) ?  (float)$data['commission']['rate'] : 0);
						$commission_amount = (isset($data['commission']['commission_amount']) ?  (float)$data['commission']['commission_amount'] : 0);
					?>
					<p class="clear">
						<span class="label_1 float_left minw_lab2">Sale amount</span>
					<div class="width_in3a float_left indent_input_tp">
						<input class="input_1 float_left <?php if($sales_amount<0) echo 'red_txt'; ?>" type="text" id="sales_amount" name="sales_amount" value="<?php echo $this->Common->format_currency($sales_amount); ?>">
					</div>
					</p>
					<p class="clear">
						<span class="label_1 float_left minw_lab2">Cost of sale</span>
					<div class="width_in3a float_left indent_input_tp">
						<input class="input_1 float_left bor_active <?php if($sales_cost<0) echo 'red_txt'; ?>" type="text" id="sales_cost" name="sales_cost" value="<?php echo $this->Common->format_currency($sales_cost); ?>">
					</div>
					</p>
					<p class="clear">
						<span class="label_1 float_left minw_lab2">Profit for commission</span>
					<div class="width_in3a float_left indent_input_tp">
						<input class="input_1 float_left <?php if($profit<0) echo 'red_txt'; ?>" type="text" id="profit" readonly="readonly" value="<?php echo $this->Common->format_currency($profit); ?>">
					</div>
					</p>
					<p class="clear">
						<span class="label_1 float_left minw_lab2">Rate</span>
					<div class="width_in3a float_left indent_input_tp">
						<input class="input_1 float_left <?php if($rate<0) echo 'red_txt'; ?>" type="text" id="rate" name="rate" value="<?php echo $rate.'%'; ?>" style="width: 15%" />
						<span style="float: left;font: 11px arial, verdana, sans-serif;margin-top: 2%;color: #353535;">Base on</span>
						<div class="styled_select" style="width: 60%">
							<select name="base_on">
								<option value=""></option>
								<option value="sale_amt" <?php if(isset($data['commission']['base_on']) && $data['commission']['base_on']=='sale_amt') echo 'selected="selected"' ?>>Sale amt</option>
								<option value="profit" <?php if(isset($data['commission']['base_on']) && $data['commission']['base_on']=='profit') echo 'selected="selected"' ?>>Profit</option>
							</select>
						</div>
					</div>
					</p>
					<p class="clear">
						<span class="label_1 float_left minw_lab2">Commission amount</span>
					<div class="width_in3a float_left indent_input_tp">
						<input class="input_1 float_left <?php if($commission_amount<0) echo 'red_txt'; ?>" readonly="readonly" id="commission_amount" type="text" value="<?php echo $this->Common->format_currency($commission_amount); ?>">
					</div>
					</p>
					<p class="clear">
						<span class="label_1 float_left minw_lab2" style="height:50%">Paid</span>
					<div class="width_in3a float_left indent_input_tp">
						<div class="in_active2">
							<label class="m_check2">
								<input type="checkbox" name="paid" <?php if(isset($data['commission']['paid']) && $data['commission']['paid'] == 1) echo 'checked="checked"'; ?>>
								<span class="bx_check dent_chk"></span>
							</label>
							<span class="inactive dent_check"></span>
							<p class="clear"></p>
						</div>
					</div>
					</p>
					<p class="clear"></p>
				</div>
				<input type="hidden" id="fieldchange" name="fieldchange" value="" />
				<span class="title_block bo_ra2"></span>
			</div><!--END Tab1 -->
		</div>
	</form>
</div>
<p class="clear"></p>
<script type="text/javascript">
	<?php if($this->Common->check_permission($controller.'_@_entry_@_edit',$arr_permission)): ?>
	function other_tab_auto_save(id,content){
		$.ajax({
			url: "<?php echo URL; ?>/<?php echo $controller;?>/other_tab_auto_save/",
			timeout: 15000,
			type: "POST",
			data: { id: id, content : content },
			success: function(html){
				if(html != "ok"){
					alerts("Error: ", html);
				}

			}
		});
		return false;
	}
	function after_choose_contacts_commission_employee(contact_id,contact_name,key){
		$("#contact_name","#commission_form").val(contact_name);
		$("#contact_id","#commission_form").val(contact_id).trigger("change");
		$("#window_popup_contacts_commission_employee").data("kendoWindow").close();
	}
	<?php endif; ?>
    $(function(){
    	<?php if(!$this->Common->check_permission($controller.'_@_entry_@_edit',$arr_permission)): ?>
    	$("input,textarea,select","#load_subtab").each(function(){
    		$(this).attr('disabled','disabled');
    	});
    	$(".combobox_selector","#load_subtab").remove();
    	<?php else: ?>
    	$("input,select","form#commission_form").change(function(){
    		if($(this).is(":checkbox")){
    			$(this).val(0);
    			if($(this).is(":checked"))
    				$(this).val(1);
    		}
    		$("#fieldchange").val($(this).attr("name"));
    		$.ajax({
    			url: "<?php echo URL.'/'.$controller.'/commission_auto_save' ?>",
    			type: "POST",
    			data: $("input,select","form#commission_form").serialize(),
    			success: function(result){
    				result = $.parseJSON(result);
    				var arr_value = [];
    				arr_value = ['sales_amount','sales_cost','profit','rate','commission_amount'];
    				var field = "";
    				for(var i in arr_value){
    					field = arr_value[i];
    					$("#"+field,"form#commission_form").removeClass("red_txt");
    					if(result[field] < 0)
    						$("#"+field,"form#commission_form").addClass("red_txt");
    					if(field == "rate")
    						$("#"+field,"form#commission_form").val(result[field]+"%");
    					else
    						$("#"+field,"form#commission_form").val(FortmatPrice(result[field]));
    				}
    			}
    		});
    	});
    	$("#other_record :input").change(function() {
    		var id = $(this).attr('id');
    		if(id=='include_signature' || id=='sign_off_section'
    			|| id=='own_letterhead' || id=='include_images'){
    			$('.'+id).val(0);
    			if($(this).is(":checked"))
    				$('.'+id).val(1);
    		}
    		var ids = $("#mongo_id").val();
    		data = $("input","form#other_record").serialize();
			other_tab_auto_save(ids,data);
		});
	 	$("#other_comment textarea").change(function() {
			var ids = $("#mongo_id").val();
			var data = 'other_comment='+$(this).val();
			other_tab_auto_save(ids,data);
		});
    	<?php endif; ?>
    });

</script>
<?php endif; ?>