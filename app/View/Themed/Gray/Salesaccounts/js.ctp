<?php echo $this->element('js_entry');?>
<script type="text/javascript">
$(function(){
	//default focus
	<?php $this->Common->check_lock_sub_tab($controller,$arr_permission); ?>
	$("#module_name").focus();
	// Xu ly save, update
	$("form input,form select").change(function() {
		var fixkendo = $(this).attr('class');
		var fieldname = $(this).attr("name");
		var fieldid = $(this).attr("id");
		var fieldtype = $(this).attr("type");
			modulename = 'mongo_id';
		var ids = $("#"+modulename).val();
		var values = $(this).val();
		if(fieldtype=='checkbox'){
			if($(this).is(':checked'))
				values = 1;
			else
				values = 0;
		}

		$(".jt_ajax_note").html("Saving...");
		save_data(fieldname,values,ids);
		ajax_note("Saving...Saved !");

	});

	$(".jt_ajax_note").html('');

});

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

	function after_choose_companies(ids,names,keys){
		if(keys=='company_name'){
			$("#company_id").val(ids);
			$("#company_name").val(names);
			$("#md_company_name").html(names);
			$("#window_popup_companiescompany_name").data("kendoWindow").close();
			$(".link_to_company_name").addClass('jt_link_on');
			save_data('company_name',names,'',ids,function(arr_ret){

				$(".link_to_contact_name").removeClass('jt_link_on');
				if(arr_ret['contact_id']!='')
					$(".link_to_contact_name").addClass('jt_link_on');
				$("#md_contact_name").html('');
				if(arr_ret['contact_name']!='')
					$("#md_contact_name").html(arr_ret['contact_name']);

				if(arr_ret['tax']!='' && arr_ret['taxtext']!=''){
					$("#tax").val(arr_ret['taxtext']);
				}

				window_popup('contacts', 'Specify Contact','contact_name','click_open_window_contactscontact_name',get_para_contact(),'force_re_install');
				reload_address('invoice_');
				reload_address('shipping_');

				// BaoNam
				reload_payment_term_tax_company(ids);
				reload_subtab('line_entry');
			});

		}
	}
</script>