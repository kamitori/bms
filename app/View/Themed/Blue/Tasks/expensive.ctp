<div class="clear_percent">
	<div class="clear_percent_18 float_left">
		<div class="tab_1 full_width">
			<span class="title_block bo_ra1">
				<span class="float_left h_form">
					<span class="fl_dent"><h4><?php echo translate('Expenses for this tasks'); ?></h4></span>
					<?php echo $this->Js->link( '<span class="icon_down_tl top_f"></span>', '/tasks/expensive_add/'.$task_id,
						array(
							'update' => '#tasks_sub_content',
							'title' => 'Add new line',
							'escape' => false
						) );
					?>
				</span>
			</span>
			<ul class="ul_mag clear bg3">
				<li class="hg_padd" style="width:30%;">Heading</li>
				<li class="hg_padd" style="width:64%;">Details</li>
				<li class="hg_padd bor_mt" style="width:1.8%;"></li>
			</ul>
			<div id="tasks_expense">
				<?php $count = 0;

				$i = 1;  $k = 0;
				if(isset($arr_task['expense'])){

					foreach($arr_task['expense'] as $key => $value){
						$k=$k+1;
						if( $value['deleted'] )continue;
					?>

					<?php echo $this->Form->create('Expense', array('id' => 'OtherEntryForm_'.$key)); ?>
					<?php echo $this->Form->hidden('Expense.key', array( 'value' => $key )); ?>
					<?php echo $this->Form->hidden('Expense._id', array( 'value' => $task_id )); ?>

					<ul class="ul_mag clear bg<?php echo $i; ?>" id="tasks_expense_<?php echo $key; ?>">

						<li class="hg_padd" style="width:30%;">
							<?php echo $this->Form->input('Expense.heading', array(
								'class' => 'input_inner input_inner_w bg'.$i,
								'value' => $value['heading']
							)); ?>

						</li>
						<li class="hg_padd" style="width:64%;">
							<?php echo $this->Form->input('Expense.details', array(
								'class' => 'input_inner input_inner_w bg'.$i,
								'value' => $value['details']
							)); ?>
						</li>
						<li class="hg_padd bor_mt" style="width:1.8%">
							<div class="middle_check">
								<a title="Delete link" href="javascript:void(0)" onclick="tasks_expense_delete(<?php echo $key; ?>)">
									<span class="icon_remove2"></span>
								</a>
							</div>
						</li>
					</ul>

					<?php echo $this->Form->end(); ?>

				<?php $i = 3 - $i; $count += 1;
					}
				}

				$count = 11 - $count;
				if( $count > 0 ){
					for ($j=0; $j < $count; $j++) { ?>
						<ul class="ul_mag clear bg<?php echo $i; ?>">
							<li class="hg_padd" style="width:30%;"></li>
							<li class="hg_padd" style="width:64%;"></li>
							<li class="hg_padd bor_mt" style="width:1.8%;"></li>
						</ul>
			  <?php $i = 3 - $i;
					}
				}
				?>
			</div>
			<p class="clear"></p>
			<span class="hit"></span>
			<span class="title_block bo_ra2"></span>
		</div><!--END Tab1 -->
	</div>
	<div class="clear_percent_16 float_left">
		<div class="tab_1 full_width">
			<span class="title_block bo_ra1">
				<span class="float_left h_form">
					<span class="fl_dent"><h4>Group linked to this copany</h4></span>
					<!-- <a title="Link a contact" href="javascript:void(0)">
						<span class="icon_down_tl top_f"></span>
					</a> -->
				</span>
			</span>
			<ul class="ul_mag clear bg3">
				<li class="hg_padd bor_mt" style="width:98%;">Group</li>
			</ul>
			<?php

			$count = 0; $i = 1;

			$count = 11 - $count;
			if( $count > 0 ){ $i = 3 - $i;
				for ($j=0; $j < $count; $j++) { ?>
					<ul class="ul_mag clear bg<?php echo $i; ?>">
						<li class="hg_padd bor_mt" style="width:98%;"></li>
					</ul>
		  <?php $i = 3 - $i;
				}
			}
			?>

			<p class="clear"></p>
			<span class="hit"></span>
			<span class="title_block bo_ra2"></span>
		</div><!--END Tab1 -->
	</div>
	<div class="clear_percent_17 float_right">
		<form method="POST" id="profile_form">
						<div class="tab_1 full_width" id="block_full_otherpricing">
						   <!-- Header-->
						   <span class="title_block bo_ra1">
							  <span class="fl_dent">
								 <h4>Profile</h4>
							  </span>
						   </span>
						   <!--CONTENTS-->
						   <div class="jt_subtab_box_cont" style=" height:209px;">
							  <div class="tab_2_inner">

									<p class="clear">
									   <span class="label_1 float_left minw_lab2">Type</span>
									</p>
									<div class="width_in3 float_left indent_input_tp" id="shipping_province" style="width:61.5%">



									   <input name="profile_type" value="<?php if(isset($arr_return['profile_type'])) echo $arr_return['profile_type'];?>" class="input_select" readonly="readonly" type="text" id="profile_type">

									   <script type="text/javascript">
										$(function () {
											$("#profile_type").combobox(<?php echo json_encode($arr_company_type); ?>);
										});
									   </script>


									</div>
									<p></p>


									<p class="clear">
									   <span class="label_1 float_left minw_lab2">Category</span>
									</p>
									<div class="width_in3 float_left indent_input_tp" id="shipping_province" style="width:61.5%">


									   <input name="category" value="<?php if(isset($arr_return['category'])) echo $arr_return['category'];?>" id="category" class="input_select" readonly="readonly" type="text">
									   <script type="text/javascript">
										$(function () {
											$("#category").combobox(<?php echo json_encode($arr_company_category); ?>);
										});
									   </script>


									</div>
									<p></p>


									<p class="clear">
									   <span class="label_1 float_left minw_lab2">Rating</span>
									</p>
									<div class="width_in3 float_left indent_input_tp" id="shipping_province" style="width:61.5%">


									  <input name="rating" value="<?php if(isset($arr_return['rating'])) echo $arr_return['rating'];?>"  id="rating" class="input_select" readonly="readonly" type="text">
									   <script type="text/javascript">
										$(function () {
											$("#rating").combobox(<?php echo json_encode($arr_company_rating); ?>);
										});
									   </script>


									</div>
									<p></p>





									<p class="clear">
									   <span class="label_1 float_left minw_lab2">No of staff</span>
									</p>
									<div class="width_in3 float_left indent_input_tp" style=" width: 61.5%; ">
									   <input class="input_1 float_left" id="no_of_staff" name="no_of_staff" type="text" value="<?php if(isset($arr_return['no_of_staff'])) echo $arr_return['no_of_staff'];?>">
									</div>
									<p></p>





									<p class="clear"></p>



									<p></p>





									<div style="overflow:hidden;">

											<span class="title_block">
											  <span class="fl_dent">
												 <h4>Phone Related</h4>
											  </span>
										   </span>
										   <p class="clear">
											  <span class="label_1 float_left minw_lab2">Speed Dial </span>
										   </p>
										   <div class="width_in3 float_left indent_input_tp" id="shipping_province" style="width:61.5%">
											  <input class="input_1 float_left" id="speed_dial" name="speed_dial" type="text" value="<?php if(isset($arr_return['speed_dial'])) echo $arr_return['speed_dial'];?>" style="width: 35%;"><p style="float: left;margin-top: 4%;" class="for">For</p>
												   <div class="width_in3 float_left indent_input_tp for" id="shipping_province" style="width: 45.5%;">
		<style>
		.for .combobox {
		position: absolute !important;
		width: 6.4%;
		}
		</style>

																 <input name="phone_type" value="<?php if(isset($arr_return['phone_type'])) echo $arr_return['phone_type'];?>"  id="phone_type" class="input_select" readonly="readonly" type="text">

																 <script type="text/javascript">
																	$(function () {
																		$("#phone_type").combobox(<?php echo json_encode($arr_phone_type); ?>);
																	});
																 </script>


													</div>
													<div class="clear"></div>
										   </div>
										   <p></p>


										   <p class="clear">
											 <span class="label_1 float_left minw_lab2" style=" height: 50%;">Include in phonebook </span>
										  </p>
										  <div class="width_in3 float_left indent_input_tp" id="shipping_province" style="width:61.5%">


													<div class="width_in3 float_left indent_input_tp" id="shipping_province" style="width: 35.5%;">


													  <div class="in_active2">
																<label class="m_check2">
																	<input type="checkbox" id="include_in_phone_book" name="include_in_phone_book" value="<?php if(isset($arr_return['include_in_phone_book']))echo $arr_return['include_in_phone_book'];?>"  <?php if(isset($arr_return['include_in_phone_book'])&&$arr_return['include_in_phone_book']==1){?> checked <?php }?>>
																	<span class="bx_check dent_chk"></span>
																</label>
																<span class="inactive dent_check"></span>
																<p class="clear"></p>
															</div>
													  </div>
													   <p style="float: left;margin-top: 4%;margin-left: 5%;">Sort by</p>
													 <input class="input_1 float_left" id="phone_sort_by" name="phone_sort_by" type="text" value="<?php if(isset($arr_return['phone_sort_by'])) echo $arr_return['phone_sort_by'];?>" style="width: 30%;margin-top: 8px;">


													 <div class="clear"></div>
													</div>
										 <p></p>
									   <p></p>
									</div>
							  </div>
							  <div class="tab_2_inner">


							  </div><!--END Tab2  inner-->


							<div class="tab_2_inner">
										<span class="title_block">
										  <span class="fl_dent">
											 <h4>Custom field</h4>
										  </span>
									   </span>
									   <p class="clear">
										  <span class="label_1 float_left minw_lab2">Custom 1 </span>
									   </p>
									   <div class="width_in3 float_left indent_input_tp" id="shipping_province" style="width:61.5%">

												<input name="custom_field_1" value="<?php if(isset($arr_return['custom_field_1'])) echo $arr_return['custom_field_1'];?>"  id="custom_field_1" class="input_select" readonly="readonly" type="text">
									   </div>
									   <p></p>
									   <p class="clear">
										  <span class="label_1 float_left minw_lab2">Custom 2</span>
									   </p>
									   <div class="width_in3 float_left indent_input_tp" style=" width: 61.5%; ">
											  <input name="custom_field_2" value="<?php if(isset($arr_return['custom_field_2'])) echo $arr_return['custom_field_2'];?>"  id="custom_field_2" class="input_select" readonly="readonly" type="text">



									   </div>
									   <p></p>
									   <p class="clear">
										  <span class="label_1 float_left minw_lab2" style="height:25px" >Custom 3</span>
									   </p>
									   <div class="width_in3 float_left indent_input_tp" style=" width: 61.5%; ">
											<input name="custom_field_3" value="<?php if(isset($arr_return['custom_field_3'])) echo $arr_return['custom_field_3'];?>"  id="custom_field_3" class="input_select" readonly="readonly" type="text">


											   <script type="text/javascript">
												$(function () {
												   $("#custom_field_1").combobox(<?php echo json_encode($arr_custom_field); ?>);
													$("#custom_field_2").combobox(<?php echo json_encode($arr_custom_field); ?>);
													 $("#custom_field_3").combobox(<?php echo json_encode($arr_custom_field); ?>);
												});
											 </script>
									   </div>
									   <p></p>

							</div><!--END Tab2  inner-->


						   </div>
						   <!--<span class="hit"></span>-->
						   <!--Footer-->
						   <span class="title_block bo_ra2">
						   </span>
						</div><!--END Tab1 -->
						</form>
	</div>
</div>

<script type="text/javascript">
$(function(){

	$("form :input", "#tasks_expense").change(function() {

		var contain = $(this).closest('form');

		$.ajax({
			url: '<?php echo URL; ?>/tasks/expense_auto_save',
			timeout: 15000,
			type:"post",
			data: $(this).closest('form').serialize(),
			success: function(html){
			console.log(html);
				if( html != "ok" )alerts("Error: ", html);
			}
		});

	});
	$("form#profile_form input,select").change(function() {
		var fieldname =$(this).attr("name");
		var values = $(this).val();
		var ids = $("#TaskId").val();
		var fieldtype = $(this).attr("type");

		if(fieldtype=='checkbox'){
			if($(this).is(':checked'))
				values = 1;
			else
				values = 0;
		}

		$.ajax({
			url: '<?php echo URL; ?>/<?php echo $controller;?>/save_data_for_non_model',
			timeout: 15000,
			type: "POST",
			data: { fieldname : fieldname,values:values,ids:ids },
			success: function(html){
				console.log(html);
			}
		});
		return false;
	});


});

function tasks_expense_delete(key){

	confirms( "Message", "Are you sure you want to delete?",
		function(){
			$.ajax({
				url: '<?php echo URL; ?>/tasks/expense_delete/'+ key + '/<?php echo $task_id; ?>',
				success: function(html){
					if(html == "ok"){
						$("#OtherEntryForm_" + key).fadeOut();
					}
					console.log(html);
				}
			});
		},function(){
			console.log("Cancel 123");
			return false;
		}
	);

}
</script>