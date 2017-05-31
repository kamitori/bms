<?php echo $this->element('js/permission_entry'); ?>
<input type="hidden" id="oldStatus" value="" />
<script type="text/javascript">
	$(function() {
		<?php if(isset($closing_month)) { ?>
		$(".menu_control").append('<li><a id="open-module" style="background: #6A1515; color: #fff" href="javascript:void(0)" onclick="callPasswordPopup(function(){location.reload()},{open:1})" title="Click to open current Job.">Open Job</a></li>');
		$("input,select,textarea","#content").prop("readonly",true);
		$(".JtSelectDate").prop("disabled",true);
		$("span[id^=click_open_window_]","#content").remove();
		$("span[class^=combobox_],div[class^=combobox_]","#content").remove();
		$(".ul_tab > li").unbind("click").click(function(e){
			var id = $(this).attr("id");
			// window.location.hash = $(this).attr("id");
        	e.preventDefault();
			$.ajax({
				url : "<?php echo URL.'/jobs/sub_tab/' ?>"+id+"/<?php echo $this->data['Job']['_id'] ?>",
				success: function(html){
					$("li.active",".ul_tab").removeClass("active");
					$("#"+id,".ul_tab").addClass("active");
					$("#jobs_sub_content").html(html);
					$("input[readonly!=readonly],select,textarea","#jobs_sub_content").prop("disabled",true);
					$("a > span[class!=icon_emp]","#jobs_sub_content").parent().remove();
					$("span[id^=click_open_window_],input[id^=click_open_window_]","#jobs_sub_content").remove();
					$("span[class^=combobox_],div[class^=combobox_]","#jobs_sub_content").remove();
				}
			})
		});
		<?php if(IS_LOCAL) {?>
		$(":input", "#<?php echo $controller; ?>_form_auto_save").click(function(){
			$("#open-module").click();
		});
		<?php } ?>
		<?php } else if(isset($_SESSION['JobsOpen_'.$this->data['Job']['_id']])){ ?>
		if(!$("#close-module").length)
			$(".menu_control").append('<li><a id="close-module" style="background: #6A1515; color: #fff" href="javascript:void(0)" onclick="closeAll()" title="Click to close.">Close Job</a></li>');
		<?php } ?>
		<?php if(IS_LOCAL) {?>
		$("#open-module,#close-module").hide();
		<?php } ?>
		<?php if(!isset($_SESSION['JobsOpen_'.$this->data['Job']['_id']])){ ?>
		if($("#JobStatusId").val() == "Paid"){
			$(":input", "#<?php echo $controller; ?>_form_auto_save").each(function(){
				var id = $(this).attr("id");
				if((id== undefined || id.match("JobStatus") == null) && id!= "password_store")
					$(this).attr("disabled", true);
			});
			$(".combobox_button, .indent_dw_m, .icon_down_new", "#<?php echo $controller; ?>_form_auto_save").each(function(){
				var id = $(this).closest("span").find("input").attr("id");
				if(id == undefined || id.match("JobStatus") == null){
					$(this).remove();
				}
			});
		}
		<?php } ?>
		jobs_update_entry_header();
		$("#JobNo").focus(function(){
			$(this).attr("rel",$(this).val());
		});
		var oldStatus = "";
		$("#JobStatus").focus(function(){
			oldStatus = $("#JobStatus").val();
			$("#oldStatus").val(oldStatus);
		});
		<?php if(!isset($_SESSION['JobsOpen_'.$this->data['Job']['_id']])){ ?>
		$("#JobStatus").change(function(){
			if(oldStatus=="Completed" && $("#JobStatusId").val()!="Completed"
			   ||oldStatus=="Paid" && $("#JobStatusId").val()!="Paid")
				password_popup();
		});
		<?php } ?>
	});
	function closeAll(){
		$.ajax({
			url : "<?php echo URL.'/'.$controller.'/close_module' ?>",
			success : function(){
				location.reload();
			}
		});
	}
	function password_popup(){
		$("#password_store").val('').removeAttr("name");
		if( $("#password_window" ).attr("id") == undefined ){
			var html = '<div id="password_confirm" >';
				html +=	   '<div class="jt_box" style=" width:100%;">';
				html +=		  '<div class="jt_box_line"><div class=" jt_box_label " style=" width:25%;"></div><div id="alert_message"></div></div>';
				html +=	      '<div class="jt_box_line">';
				html +=	         '<div class=" jt_box_label " style=" width:25%;height: 61px">Password</div>';
				html +=	         '<div class="jt_box_field " style=" width:71%"><input name="password" id="password" class="input_1 float_left" type="password" value=""></div><input style="margin-top:2%" type="button" class="jt_confirms_window_cancel" id="confirms_cancel" value=" Cancel " /><input style="margin-top:2%" type="button" class="jt_confirms_window_ok" value=" Ok " id="confirms_ok" />';
				html +=	      '</div>';
				html +=	      '</div>';
				html +=	   '</div>';
				html +=	'</div>';
			$('<div id="password_window" style="width: 99%; padding: 0px; overflow: auto;">'+html+'</div>').appendTo("body");
		}
		var password_window = $("#password_window");
		password_window.kendoWindow({
			width: "355px",
			height: "100px",
			title: "Enter password",
			visible: false,
			activate: function(){
			  $('#password').focus();
			}
		});
		$("#password").keypress(function(evt){
			var keyCode = (evt.which) ? evt.which : event.keyCode
		    if(keyCode==13)
		    	$("#confirms_ok").click();
		});
		$("#password").val("");
		password_window.data("kendoWindow").center();
		password_window.data("kendoWindow").open();
		$("#confirms_ok").unbind("click");
		$("#confirms_ok").click(function() {
			$("#alert_message").html("");
			if( $("#password").val().trim()==''  ){
				$("#alert_message").html('<span style="margin-left: 5px; color:red; font-weight: 700">Password must not be empty.</span');
				$("#password").focus();
				return false;
			}
			$("#password_store").val($("#password").val()).attr("name","password");
	       	password_window.data("kendoWindow").destroy();
			jobs_auto_save_entry($("#JobName"),function(){location.reload()});
		});
		$('#confirms_cancel').click(function() {
			var oldStatus = $("#oldStatus").val();
			$("#alert_message").html("");
			$("#JobStatus").val(oldStatus);
			$("#JobStatusId").val(oldStatus);
	       	password_window.data("kendoWindow").destroy();
	    });
	}
	function jobs_update_entry_header() {
		$("#job_name_header").html($("#JobName").val());
		if ($.trim($("#JobCompanyName").val()) == "") {
			$("#jobs_right_h1_header").html($("#JobType option[value='" + $("#JobType").val() + "']").text());

		} else {
			$("#jobs_right_h1_header").html('<span id="job_company_name_header"></span> | <span id="job_status_header"></span>');
			$("#job_company_name_header").html($("#JobCompanyName").val());
			$("#job_status_header").html($("#JobStatus option[value='" + $("#JobStatus").val() + "']").text());
		}
	}

	function jobs_auto_save_entry(object,callBack) {
		<?php if(!isset($_SESSION['JobsOpen_'.$this->data['Job']['_id']])){ ?>
		if($(object).attr("id")=='JobStatus'
		   && ($("#oldStatus").val()=="Completed" && $("#JobStatusId").val()!="Completed"
		       ||$("#oldStatus").val()=="Paid" && $("#JobStatusId").val()!="Paid"))
			return false;
		<?php } ?>
		if ($.trim($("#JobHeading").val()) == "") {
			$("#JobHeading").val("#" + $("#JobNo").val() + "-" + $("#JobCompanyName").val());
		}

		jobs_update_entry_header();

		$("form :input", "#jobs_form_auto_save").removeClass('error_input');
		var data = $("form", "#jobs_form_auto_save").serialize();
		data += "&field_change="+$(object).attr("id");
		$.ajax({
			url: '<?php echo URL; ?>/jobs/auto_save',
			timeout: 15000,
			type: "post",
			data: data,
			success: function(result) {
				result = $.parseJSON(result);
				if(result.status != 'ok'){
					var oldStatus = $("#oldStatus").val();
					if (result.message == 'must_be_numberic'){
						$("#JobNo").addClass('error_input');
						alerts('Message', 'This ref no must be a number.');

					}else if (result.message == "so_completed") {
						$("#JobStatus").addClass('error_input');
						alerts('Message', 'All Sales orders must be completed first.');

					}else if (result.message == "si_invoiced") {
						$("#JobStatus").addClass('error_input');
						alerts('Message', 'All Sales orders must be invoiced first.');

					}else if (result.message == "sum_different") {
						$("#JobStatus").addClass('error_input');
						alerts('Message', 'Totals of sales orders are different from sales invoices.');

					}else if (result.message == "ref_no_existed") {
						$("#JobNo").addClass('error_input');
						alerts('Message', 'This ref no existed');

					} else if (result.message == "date_work") {
						$("#JobWorkStart").addClass('error_input');
						$("#JobWorkEnd").addClass('error_input');
						alerts("Message", '<b>Work start</b> can not greater than <b>Work end</b>');

					} else if (result.message == "wrong_pass") {
						alerts("Message", 'Wrong password.');
						$("#JobStatus").val(oldStatus);
						$("#JobStatusId").val(oldStatus);

					} else if (result.message == "need_pass") {
						// alerts("Message", 'You need enter password to change Job no.');
						$("#JobStatus").val(oldStatus);
						$("#JobStatusId").val(oldStatus);
						callPasswordPopup(function(){location.reload();}, {open:1});
					}
					else if(result.message.indexOf("not_add") != -1 ){
						password_popup_job(result.message.split("||")[1]);
						return false;
					} else if(result.message == "wrong_pass_company"){
						alerts("Message", 'Wrong password');
						$("#JobCompanyName").val("");
						$("#JobCompanyId").val("");
						$("#password_jobs").val("");
					}
				}
				else if (result.contact_id!=undefined) {
					$("#JobContactId").val(result.contact_id);
                    $("#JobContactName").val(result.contact_name);
				}
				if(result.status =="ok" && callBack != undefined)
					callBack();
			}
		});
	}

	function password_popup_job(payment_term){
		if( payment_term == undefined ){
			payment_term = 90;
		}
		$("#confirms_window").val('').removeAttr("name");
		if( $("#confirms_window" ).attr("id") == undefined ){
			var html = '<div id="password_confirm" >';
				html +=	   '<div class="jt_box" style=" width:100%;">';
				//html +=		  '<div class="jt_box_line"><div class=" jt_box_label " style=" width:25%;"></div><div style="height: 10px;" id="alert_message"></div></div>';
				html +=		  '<div class="jt_box_line"><div class=" jt_box_label " style=" width:25% ;height: 39px;"></div><div>This company has unpaid invoices more than '+payment_term+' days, please check with HR to enter a password before creating a new job.</div></div>';
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
		$("#password").keypress(function(evt){
			var keyCode = (evt.which) ? evt.which : event.keyCode
		    if(keyCode==13)
		    	$("#confirms_ok").click();
		});
		$("#confirms_ok").unbind("click");
		$("#confirms_ok").click(function() {
			$("#alert_message").html("");
			if( $("#password").val().trim()==''  ){
				$("#alert_message").html('<span style="margin-left: 5px; color:red; font-weight: 700">Password must not be empty.</span');
				$("#password").focus();
				return false;
			}
			$("#password_jobs").val($("#password").val());
			jobs_auto_save_entry($("#JobCompanyName"),function(){
				$.ajax({
					url: '<?php echo URL; ?>/jobs/save_company_address/',
					success: function(result){
						if(result!='ok')
							alerts('Message',result);
						else
							location.reload();
					}
				});
			});
	       	confirms_window.data("kendoWindow").destroy();
		});
		$('#confirms_cancel').click(function() {
			$("#JobCompanyName").val("");
			$("#JobCompanyId").val("");
			$("#password_jobs").val("");
	       	confirms_window.data("kendoWindow").destroy();
	    });
	}

	function callPasswordPopup(callBack,extraData){
		if( $("#confirms_window" ).attr("id") == undefined ){
			var height = "100px;"
			var html = '<div id="password_confirm" >';
				html +=	   '<div class="jt_box" style=" width:100%;">';
				html +=		  '<div class="jt_box_line"><div class=" jt_box_label " style=" width:25%; <?php if(isset($closing_month)) echo 'height: 75px;'; ?>"></div><div id="alert_message" style="margin-left: 28%">';
				<?php if(isset($closing_month)){ ?>
				html +=		  'This job is closed because the <?php echo date('F',strtotime($this->data['Job']['work_end'])); ?> accounting balances were all verified, changing of this job may lead wrong balances for all accounting reports of <?php echo date('F',strtotime($this->data['Job']['work_end'])); ?>, please enter password to proceed.';
				var height = "150px;"
				<?php } ?>
				html +=		  '</div></div>';
				html +=	      '<div class="jt_box_line">';
				html +=	         '<div class=" jt_box_label " style=" width:25%;height: 75px">Password</div>';
				html +=	         '<div class="jt_box_field " style=" width:71%"><input name="password" id="password" class="input_1 float_left" type="password" value=""></div><input style="margin-top:2%" type="button" class="jt_confirms_window_cancel" id="confirms_cancel" value=" Cancel " /><input style="margin-top:2%" type="button" class="jt_confirms_window_ok" value=" Ok " id="confirms_ok" />';
				html +=	      '</div>';
				html +=	      '</div>';
				html +=	   '</div>';
				html +=	'</div>';
			$('<div id="confirms_window" style="width: 99%; padding: 0px; overflow: auto;">'+html+'</div>').appendTo("body");
		}
		var confirms_window = $("#confirms_window");
		confirms_window.kendoWindow({
			width: "355px",
			height: height,
			title: "Enter password",
			visible: false,
			activate: function(){
			  $('#password').focus();
			}
		});
		$("#password").keypress(function(evt){
			var keyCode = (evt.which) ? evt.which : event.keyCode
		    if(keyCode==13)
		    	$("#confirms_ok").click();
		});
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
			var data = {};
			data['password'] = $("#password").val();
			if(typeof extraData == "object"){
				$.extend(data,extraData);
			}
			$.ajax({
				url: '<?php echo URL.'/'.$controller;?>/check_password',
				type:"POST",
				data: data,
				success: function(text_return){
					if(text_return=='wrong_pass'){
						ajax_note("Wrong password.");
					}else if(text_return=='success'){
						callBack();
					}
				}
			});
	       	confirms_window.data("kendoWindow").destroy();
		});
		$('#confirms_cancel').click(function() {
			$("#alert_message").html("");
			if($("#code").attr("rel")!="" || $("#code").attr("rel")!= undefined)
				$("#code").val($("#code").attr("rel"));
	       	confirms_window.data("kendoWindow").destroy();
	    });
	    return false;
	}
</script>