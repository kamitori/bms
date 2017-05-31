<?php echo $this->element('../'.$name.'/tab_rfqs');?>
<div id="content">
	<div class="jt_ajax_note">Loading...</div>
    <!-- Title -->
    <div class="jbcont">
        <div class="jt_module_title float_left jt_t_left">
            <h1>
                <span id="md_code">
                   <?php if(isset($quote_pro_code)) echo $quote_pro_code;?>
                </span>
                <span class="md_center">:</span>
                <span id="md_name">
                    <?php if(isset($quote_pro_name)) echo $quote_pro_name;?>
                </span>
             </h1>
        </div>
        <div class="jt_module_title float_right jt_t_right"><h1>&nbsp;</h1></div>
    </div>

    <div id="rfqs_list_form_auto_save">
        <!-- Add form -->
        <form class="form_<?php echo $controller;?>" action="" method="post" class="float_left">
            <div class="clear_percent">
                <div style="width:33%; float:left;">
                    <!--Elememt1 -->
                    <?php echo $this->element('../'.$name.'/rfqs_details');?>
                    <!--Elememt2 -->
                    <?php echo $this->element('box',array('panel'=>$box_note,'key'=>'internal_notes','arr_val'=>$box_note['internal_notes'])); ?>
                </div>
               	<div style="width:66%; float:right;">
                    <?php echo $this->element('box',array('panel'=>$box_detail,'key'=>'details_for_request','arr_val'=>$box_detail['details_for_request'])); ?>
               	</div>
            </div>
            <div class="clear"></div>
            <input type="hidden" id="idrfq" value="<?php if(isset($idrfq)) echo $idrfq;?>" />
        </form>
	</div>
</div>
<?php echo $this->element('js_entry');?>
<script type="text/javascript">
	$(function(){
		<?php if(!$this->Common->check_permission($controller.'_@_entry_@_edit',$arr_permission)): ?>
		$("#internal_notes").attr('readonly',true);
		$("#details_for_request").attr('readonly',true);
		<?php else: ?>
		$(".jt_ajax_note").html('');
		$(".jt_link_on").click(function(){
			var cls = $(this).attr('cls');
			var idf = $(this).attr('rel');
				idf = idf.replace("name","id");
				idf = idf.replace("first","first_name");
			var idcls = $('#'+idf).val();
			window.location.assign("<?php echo URL;?>/"+cls+"/entry/"+idcls);
		});
		<?php endif; ?>
		$(".entry_menu_email_rfqs").click(function(){
			$.ajax({
				url: "<?php echo URL.'/'.$controller.'/send_rfq_email/' ?>",
				type: "POST",
				data: {rfq_id : <?php echo $idrfq ?>},
				success: function(result){
					location.replace(result);
				}
			});
		});
		$(".entry_menu_create_po").click(function(){
			$.ajax({
				url: "<?php echo URL.'/'.$controller.'/create_purchaseorder_from_rfq/' ?>",
				type: "POST",
				data: {rfq_id : <?php echo $idrfq ?>},
				success: function(result){
					result = $.parseJSON(result);
					if(result.status == "ok")
						location.replace(result.url);
					else {
						if(result.url != undefined){
							alerts("Message", result.message, function(){
								location.replace(result.url);
							});
						} else {
							alerts("Message", result.message);
						}
					}
				}
			});
		});
	});
	<?php if($this->Common->check_permission($controller.'_@_entry_@_edit',$arr_permission)): ?>
	$( 'form' ).delegate("input, select, #internal_notes, #details_for_request","change",function() {
		var datas = new Object();
		var fiels = $(this).attr('id');
		var fieldtype = $(this).attr("type");
		var values = $(this).val();
		var idrfq = $('#idrfq').val();
		if(fieldtype=='checkbox'){
			if($(this).is(':checked'))
				values = 1;
			else
				values = 0;
		}
		if(fiels=='rfq_date' || fiels=='deadline_date'){
			var rfq_date = convert_date_to_num($('#rfq_date').val());
			var deadline_date = convert_date_to_num($('#deadline_date').val());
			if(rfq_date>deadline_date){
				$('#deadline_date').css('color','#f00');
			}else{
				$('#deadline_date').css('color','#545353');
			}

			values = convert_date_to_num(values);
			values = (parseInt(values)/1000)+86400;
		}
		fiels = fiels.replace("rel_", "");

		datas[fiels] = values;
		save_option('rfqs',datas,idrfq,0,'rfqs','update');
	});


	// xử lý sau khi chọn job,
	function after_choose_companies(ids,names,keys){
		if(keys=='company_name'){
			var idrfq = $('#idrfq').val();
			var arr = {
						"company_id"	:ids,
						"company_name"	:names,
					 };
			$(".k-window").fadeOut('slow');
			$('#company_id').val(ids);
			$('#company_name').val(names);
			$(".link_to_company_name").addClass('jt_link_on');
			save_option('rfqs',arr,idrfq,0,'rfqs','update');
		}
	}

	// xử lý sau khi chọn job,
	function after_choose_contacts(ids,names,keys){
		if(keys=='first_name'){
			var idrfq = $('#idrfq').val();
			var arr = {
						"first_name_id"	:ids,
						"first_name"	:names,
					 };
			$(".k-window").fadeOut('slow');
			$('#first_name_id').val(ids);
			$('#first_name').val(names);
			$(".link_to_first_name").addClass('jt_link_on');
			save_option('rfqs',arr,idrfq,0,'rfqs','update');

		}else if(keys=='employee_name'){
			var idrfq = $('#idrfq').val();
			var arr = {
						"employee_id"	:ids,
						"employee_name"	:names,
					 };
			$(".k-window").fadeOut('slow');
			$('#employee_id').val(ids);
			$('#employee_name').val(names);
			$(".link_to_employee_name").addClass('jt_link_on');
			save_option('rfqs',arr,idrfq,0,'rfqs','update');
		}
	}
	<?php endif; ?>
</script>