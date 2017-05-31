<?php echo $this->element('js_entry');?>
<script type="text/javascript">
$(function(){
	<?php if(!$this->Common->check_permission($controller.'_@_entry_@_edit',$arr_permission)): ?>
	<?php $this->Common->unlink_modules($arr_link,$arr_permission); ?>
	$("input,textarea","#receipts_form_auto_save").each(function(){
		// $(this).removeAttr('name');
		$(this).attr('readonly',true);
		if($(this).attr('type')=='checkbox')
			$(this).attr('disabled',true);
	});
	$(".txt_receipt_this_lv,.jt_right_check","#receipts_form_auto_save").each(function(){
		$(this).remove();
	});
	<?php endif;?>
	<?php if(!$this->Common->check_permission('salesinvoices_@_entry_@_view',$arr_permission)): ?>
		$("a","#container_outstanding").each(function(){
			$(this).remove();
		});
		$("#container_allocation").find('[onclick]').each(function(){
			$(this).removeAttr('onclick');
			$(this).removeAttr('title');
			$(this).html('');
		});
	<?php endif; ?>
	var parent = $(".link_to_salesaccount_name",".form_receipts").parent();
	parent.html('<?php echo $link; ?>'+parent.html());
	$("#pst_tax").change(function(){
		var ids = $(this).attr("id");
		var val = $(this).val();
		ids = "mx_"+ids;
		$("#"+ids).html(val);
	});
	//default focus
	$("#rel_amount_received").focus();

	if(parseFloat($("#unallocated").val())!=0){
		$("#unallocated").css('color','red');
	}
	$('#rel_amount_received').keypress(function(){
		if($('#salesaccount_id').val()==''&&$('#salesaccount_name').val()==''){
			$(this).val('');
			$("#click_open_window_salesaccountssalesaccount_name").click();
			return false;
		}
	});

	$("#receipts_form_auto_save").on("change",".viewcheck_inactive",function(){
		var check;
		var id = $(this).attr("id");
		id = id.split("_");
		id = id[id.length - 1];
		if($(this).is(":checked"))
			check = 1;
		else check = 0;
		$.ajax({
			url: "<?php echo URL.'/receipts/save_inactive_outstanding' ?>",
			type: "POST",
			data: {invoice_id: id, check: check},
			success: function(result){
				if(result == "ok")
					reload_box('outstanding',function(){
						outstandingTotal();
					});
			}
		})
	});

	// Xu ly save, update
	$(".form_receipts input,#notes,#comments").change(function() {
		var fieldclass = $(this).attr('class');
		var fieldname = $(this).attr("name");
		var fieldid = $(this).attr("id");
		var fieldtype = $(this).attr("type");
			modulename = 'mongo_id';
		var ids = $("#"+modulename).val();
		var values = $(this).val();
		var func = ''; var titles = new Array();
		if(ids!='')
			func = 'update'; //add,update
		else
			func = 'add';
		//check box
		if(fieldtype=='checkbox'){
			if($(this).is(':checked'))
				values = 1;
			else
				values = 0;
		}
		//select box
		if(fieldclass!=undefined){
			var fieldclass1 = fieldclass.split(" ");
			fieldclass1 = fieldclass1[0];
			if(fieldclass1=='input_select'){
				values = $("#"+fieldname+"Id").val();
			}
		}
		fieldname = fieldname.replace("_cb","");
		var val = 0;
		$(".jt_ajax_note").html("Saving...       ");
		var value = parseFloat($('#amount_received').val());
		var salesaccount_id = $('#salesaccount_id').val();
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/ajax_save',
			type:"POST",
			data: {field:fieldname,value:values,func:func,ids:ids},
			success: function(text_return){
				text_return = text_return.split("||");
				$("#"+modulename).val(text_return[0]);
				//price
				if(fieldname=='amount_received'){
					var total_allocated = $("#total_allocated").val();
					total_allocated = parseFloat(total_allocated.replace(/[,]/g,''));
					var amount_received = parseFloat(values);
					var unallocated = FortmatPrice(amount_received - total_allocated);
					$("#unallocated").val(unallocated);
					values = FortmatPrice(values);
					$("#rel_"+fieldname).val(values);
				}

				// change tittle, thay đổi tiêu đề của items
				<?php foreach($arr_settings['title_field'] as $ks=>$vls){?>
					titles[<?php echo $ks;?>] = '<?php echo $vls;?>';
				<?php }?>
				if(titles.indexOf(fieldname)!=-1){
					$("#md_"+fieldname).html(values);
					$(".md_center").html("-");
				}
				ajax_note("Saving...Saved !");
			}
		});

	});

	$(".jt_ajax_note").html('');

	$('#bt_add_allocation').click(function(){
		if($('#salesaccount_id').val()==''){
			alerts('Message','An account needs to be linked to this receipt first.');
			return false;
		}
		var datas = {
			'salesinvoice_code' : '',
			'salesinvoice_id' : '',
			'amount' : 0,
		};
		save_option('allocation',datas,'',0,'allocation','add',function(){
			reload_box('allocation',function(){
				focus_end('allocation','amount','');
			});
		});
	});
	$("#block_full_allocation").on("change","input",function(){
		var names = $(this).attr("name");
		var intext = 'box_test_'+names;
		var inval = $(this).val();
		var ids  = names.split("_");
		var index = ids.length;
		var ids = ids[index-1];

		//khoi tao gia tri luu
		names = names.replace("_"+ids,"");
		names = names.replace("cb_","");
		if(names=='write_off'){
			if ($(this).attr("checked") == "checked"){
				inval = 0;
				$('#cb_write_off_'+ids).removeAttr("checked");
			}else{
				inval = 1;
				$('#cb_write_off_'+ids).attr("checked","checked");
			}
		}
		var values =  {};
		values[names]=inval;
		if(names=="salesinvoice_code"){
	        values["amount"] = $("#amount_"+ids).val();
			confirms3('Message',"<?php msg('RECEIPT_CONFIRM_CHOICE');?>",["Yes","No",""]
	          	,function(){ //YES
	          		values["mod"] = "Fully";
		      		save_option('allocation',values,ids,0,'allocation','update',function(result){
		      			result = JSON.parse(result);
		      			reload_box('allocation',function(){
		      				$("#total_allocated").val(FortmatPrice(result.total_allocated));
		      				$("#unallocated").val(FortmatPrice(result.unallocated));
		      			});
						reload_box('outstanding',function(){
							$("#total_allocated").val(FortmatPrice(result.total_allocated));
		      				$("#unallocated").val(FortmatPrice(result.unallocated));
		      				outstandingTotal();
						});
					},names);
	          	},function(){ //NO
	          		values["mod"] = "Part";
	          		save_option('allocation',values,ids,0,'allocation','update',function(result){
	          			result = JSON.parse(result);
	          			$("#total_allocated").val(FortmatPrice(result.total_allocated));
		      			$("#unallocated").val(FortmatPrice(result.unallocated));
						reload_box('outstanding', function(){
		      				outstandingTotal();
						});
					},names);
	          	},function(){
	          		return false;
			});
		} else {
			save_option('allocation',values,ids,0,'allocation','update',function(result){
				result = JSON.parse(result);
      			if(names=="amount")
      				$("#"+names+"_"+ids).val(FortmatPrice(inval));
      			$("#total_allocated").val(FortmatPrice(result.total_allocated));
		      	$("#unallocated").val(FortmatPrice(result.unallocated));
				reload_box('outstanding', function(){
		      		outstandingTotal();
				});
				reload_box('allocation');
			},names);
		}
	});
});


function outstandingTotal() {
	var total = 0, receipt = 0, balance = 0;
	if($(".viewcheck_inactive","#container_outstanding").length) {
		$(".viewcheck_inactive","#container_outstanding").not(":checked").each(function(){
			var id = $(this).attr("id");
			id = id.split("_");
			id = id[id.length - 1];
			total += parseFloat($("#txt_total_"+id).text().replace(/[,]/g,''));
			receipt += parseFloat($("#txt_receipts_"+id).text().replace(/[,]/g,''));
			balance += parseFloat($("#txt_balance_"+id).text().replace(/[,]/g,''));
		});
	} else if($(".price_total_lv","#container_outstanding").length){
		$(".price_total_lv","#container_outstanding").each(function(){
			var id = $(this).attr("id");
			id = id.split("_");
			id = id[id.length - 1];
			total += parseFloat($("#txt_total_"+id).text().replace(/[,]/g,''));
			receipt += parseFloat($("#txt_receipts_"+id).text().replace(/[,]/g,''));
			balance += parseFloat($("#txt_balance_"+id).text().replace(/[,]/g,''));
		});
	}
	console.log(total);
	$("#total_total").val(FortmatPrice(total));
	$("#total_receipt").val(FortmatPrice(receipt));
	$("#total_balance").val(FortmatPrice(balance));
}

function option_delete(allocation_key){
	allocation_key = allocation_key.split("_");
	allocation_key = allocation_key[ allocation_key.length - 1 ];
	var mod = $("#mod_"+allocation_key).val();
	var message = "";
	if(mod=="Fully")
		message = "<?php msg('DELETE_FULLY_RECEIPT');?>";
	else
		message = "Are you sure you want to delete this record?";
	confirms("Message",message
         ,function(){
			delete_allocation(allocation_key);
         },function(){
         	return false;
    });
}
function delete_allocation(allocation_key){
	$.ajax({
		url : "<?php echo URL.'/'.$controller.'/delete_allocation' ?>",
		type: "POST",
		data: {allocation_key: allocation_key},
		success : function(result){
			result = JSON.parse(result);
			$("#"+allocation_key).parent().parent().parent().animate({
			  opacity:'0.1',
			  height:'1px'
			},500,function(){$(this).remove();});
			reload_box('outstanding',function(){
				$("#total_allocated").val(FortmatPrice(result.total_allocated));
		      	$("#unallocated").val(FortmatPrice(result.unallocated));
		      	outstandingTotal();
			});
			reload_box('allocation');
		}
	});
}

function cal_sums(keychange,values){
	var arr = ['amount_received','unallocated','total_allocated'];
	var vals = [];
	for(var i in arr){
		var kss = arr[i];
		if(kss==keychange)
			vals[kss] = values;
		else
			vals[kss] = $("#"+kss).val();
	}
	vals['unallocated'] = vals['amount_received'] - vals['total_allocated'];

	for(var i in arr){
		var kss = arr[i];
		$("#"+kss).val(FortmatPrice(vals[kss]));
	}
	$("#unallocated").change();
	reloadCode();
}
function after_choose_salesaccounts(type,id,name,key){
	if( $("li:first", "#container_allocation").attr("class") != undefined ){
		$("#window_popup_salesaccounts" + key).data("kendoWindow").close();
		alerts("Message: ", "This cannot be changed as receipts allocations have already been added. Please delete those first if necessary");
		return false;
	}
	var ids = $("#mongo_id").val();
	var value = $("#after_choose_salesaccounts"+key+id).val();
	save_data('salesaccount_name',value,ids,'',function(){
		location.reload();
	});
}

function after_choose_contacts(ids,names,keys){
	var mongoid,func;
	mongoid = $("#mongo_id").val();
	if(mongoid!='')
		func = 'update';
	else
		func = 'add';

	if(keys=='our_rep'){
		$("#window_popup_contactsour_rep").data("kendoWindow").close();
		$(".link_to_our_rep").attr("onclick", "window.location.assign('<?php echo URL;?>/contacts/entry/"+ids+"')");
		$("#our_rep_id").val(ids);
		$("#our_rep").val(names);
		$("#md_our_rep").html(names);
		$(".link_to_our_rep").addClass('jt_link_on');
		save_data('our_rep',names,'',ids);

	}else if(keys=='our_csr'){
		$("#window_popup_contactsour_csr").data("kendoWindow").close();
		$(".link_to_our_csr").attr("onclick", "window.location.assign('<?php echo URL;?>/contacts/entry/"+ids+"')");
		$("#our_csr_id").val(ids);
		$("#our_csr").val(names);
		$("#md_our_csr").html(names);
		$(".link_to_our_csr").addClass('jt_link_on');
		save_data('our_csr',names,'',ids);
	}
}

function confirm_add(ids){
	var arr = new Array();
	arr = ['Part','Fully',''];
	var names = $("#txt_code_"+ids).html();
	var sum = $("#txt_balance_"+ids).html();
	sum = sum.replace(/[,]/g,'');
	//console.log(sum);
	if(sum == 0)
		alerts('Message','There is no balance on this invoice to take a receipt for.');
	else{
		var arr_value = {
					'salesinvoice_code' : names,
					'salesinvoice_id' : ids,
					'note' 		: '',
					'write_off' : 0,
					'amount' 	: 0,
					'mod' 		: 'Fully'
				};
		confirms3('Message',"<?php msg('CONFIRM_ADD_ALLOCATION');?>",arr
		    ,function(){//Part
				arr_value['mod'] = 'Part';
				save_option('allocation',arr_value,'',0,'allocation','update',function(){
					reload_box('allocation');
					reload_box('outstanding');
				},'outstanding');
			},function(){//Full
				arr_value['mod'] = 'Fully';
				save_option('allocation',arr_value,'',0,'allocation','update',function(result){
					result = JSON.parse(result);
					reload_box('allocation',function(){
						$("#total_allocated").val(FortmatPrice(result.total_allocated));
		      			$("#unallocated").val(FortmatPrice(result.unallocated));
					});
					reload_box('outstanding', function(){
						outstandingTotal();
					});
				},'outstanding');
			},function(){
				return false;//
		});

	}
}


</script>