<?php echo $this->element('tab_option'); ?>

<script type="text/javascript">
	$(function(){
        // $("#closing_month").attr("onclick","closing_month()");
        $('#duplicate_current_quotation').attr('onclick', 'confirm_duplicate_current_quotation()');
        $('#create_job').attr({'onclick':'createJob()',"href":"javascript:void(0)"});
        $('#create_sales_invoice').attr({'onclick':'create_sales_invoice();',"href":"javascript:void(0)"});
        $('#create_sales_order').attr({'onclick':'create_sales_order()',"href":"javascript:void(0)"});
	});
	function ajax_request(type,url){
		$.ajax({
			url: url,
			type: 'POST',
			data: {type:type},
			success: function(result){
				result = jQuery.parseJSON(result);
				if(result.status=='error')
					alerts('Message',result.message);
				else if(result.status=='ok')
					window.location.replace(result.url);
			}
		});

	}
	 function confirm_duplicate_current_quotation(){
	 	var arr = ['Duplicate','New rev',''];
	 	confirms3("Message","Create a 'New Revision' or a 'Duplicate' of this record?",arr
	 		,function(){//Duplicate
	 			ajax_request('duplicate','<?php echo URL ?>/quotations/duplicate_revise_quotation/');
	 		}
	 		,function(){//New rev
	 			ajax_request('new_rev','<?php echo URL ?>/quotations/duplicate_revise_quotation/');
	 		}
	 		,function(){
	 			return false;
	 		});
	}
	function createJob(){
		$.ajax({
			url : '<?php echo URL ?>/quotations/check_condition_create_job/',
			success: function(result){
				result = jQuery.parseJSON(result);
				if(result.status=='error')
					alerts('Message',result.message);
				else if(result.str == 'not_add'){
					password_popup();
				}
				else
				{
					if(result.confirm=='yes')
					{
						confirms("Message","Set the status of the Quotation to 'Approved'?"
				 		,function(){//Yes
				 			ajax_request('change_status','<?php echo URL ?>/quotations/create_job/');
				 		}
				 		,function(){//No
				 			ajax_request('none','<?php echo URL ?>/quotations/create_job/');
				 		});
					}
					else
					{
						ajax_request('none','<?php echo URL ?>/quotations/create_job/');
					}
				}
			}
		});
	}
	function create_sales_invoice(){
		$.ajax({
			url : '<?php echo URL ?>/quotations/check_condition_create_salesinvoice/',
			success: function(result){
				result = jQuery.parseJSON(result);
				if(result.status=='error')
					alerts('Message',result.message);
				else{
					if(result.confirm=='yes'){
						confirms("Message","Set the status of the Quotation to 'Approved'?"
				 		,function(){//Yes
				 			ajax_request('change_status','<?php echo URL ?>/quotations/create_salesinvoice/');
				 		}
				 		,function(){//No
				 			ajax_request('none','<?php echo URL ?>/quotations/create_salesinvoice/');
				 		});
					}
					else
						ajax_request('none','<?php echo URL ?>/quotations/create_salesinvoice/');
				}
			}
		});
	}
	function create_sales_order(){
		$.ajax({
			url : '<?php echo URL ?>/quotations/create_salesorder/',
			success: function(result){
				result = jQuery.parseJSON(result);
				if(result.status=='ok')
					window.location.replace(result.url);
				else
					alerts('Message',result.message);
			}
		});
	}
	function closing_month(){
        if( $("#closing_month_window" ).attr("id") == undefined )
            $('<div id="closing_month_window" style="display:none; min-width:300px;"></div>').appendTo("body");
        var loading = '<span style="padding: 19% 0 0 50%;float: left;font-size: xx-large;" >Loading...</span>';
        $("#closing_month_window").html(loading);
        closing_month_window = $("#closing_month_window");
        closing_month_window.kendoWindow({
            iframe: false,
            actions: ["Close"],
            content: "<?php echo URL.'/quotations/closing_months' ?>",
            visible: false,
            resizable: false,
            draggable: false,
            width: "auto",
            title: 'Closing Month',
            pinned: true,
        }).data("kendoWindow").open();
        closing_month_window.data("kendoWindow").maximize();
    }

    function password_popup(){
		$("#confirms_window").val('').removeAttr("name");
		if( $("#confirms_window" ).attr("id") == undefined ){
			var html = '<div id="password_confirm" >';
				html +=	   '<div class="jt_box" style=" width:100%;">';
				//html +=		  '<div class="jt_box_line"><div class=" jt_box_label " style=" width:25%;"></div><div style="height: 10px;" id="alert_message"></div></div>';
				html +=		  '<div class="jt_box_line"><div class=" jt_box_label " style=" width:25% ;height: 39px;"></div><div>This company has unpaid invoices more than 90 days, please check with HR to enter a password before creating a new job.</div></div>';
				html +=	      '<div class="jt_box_line">';
				html +=	         '<div class=" jt_box_label " style=" width:25%;height: 61px">Password</div>';

				html +=	         '<div class="jt_box_field " style=" width:71%"><input name="password" id="password" class="input_1 float_left" type="password" value=""></div><input style="margin-top:2%" type="button" class="jt_confirms_window_cancel" id="confirms_cancel" value=" Cancel " /><input style="margin-top:2%" type="button" class="jt_confirms_window_ok" value=" Ok " id="confirms_ok" />';
				html +=	      '</div>';
				html +=	      '</div>';
				html +=	   '</div>';
				html +=	'</div>';
			$('<div id="confirms_window" style="width: 99%; padding: 0px; overflow: auto;">'+html+'</div>').appendTo("body");
		}
		var confirms_window = $("#confirms_window");
		confirms_window.kendoWindow({
			width: "500px",
			height: "100px",
			title: "Enter password",
			visible: false,
			activate: function(){
			  $('#password').focus();
			}
		});
		$("#password").val("");
		confirms_window.data("kendoWindow").center();
		confirms_window.data("kendoWindow").open();
		$("#confirms_ok").unbind("click");
		$("#confirms_ok").click(function() {
			$("#alert_message").html("");
			if( $("#password").val().trim()==''  ){
				$("#alert_message").html('<span style="margin-left: 5px; color:red; font-weight: 700">Password must not be empty.</span');
				$("#password").focus();
				return false;
			}
			$.ajax({
				url:"<?php echo URL;?>/quotations/check_condition_create_job",
				type:"POST",
				data: {password : $("#password").val()},
				success: function(result){
					if(result == "ok"){
						createJob();
					} else
						alerts("Message",result);
					return false;
				}
			});
	       	confirms_window.data("kendoWindow").destroy();
		});
		$('#confirms_cancel').click(function() {
			$("#alert_message").html("");
			$("#JobStatus").val('Completed');
			$("#JobStatusId").val('Completed');
	       	confirms_window.data("kendoWindow").destroy();
	    });
	}
</script>