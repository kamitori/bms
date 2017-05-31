<?php
	if(isset($arr_settings['relationship'][$sub_tab]['block']))
	foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
		echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
	}
?>
<p class="clear"></p>

<script type="text/javascript">
	$(function(){
		$('#bt_add_jobs').click(function(){
			job_add();
		})

		$("input",'#editview_box_default').change(function(){
			var ids = $("#mongo_id").val();
			var names = $(this).attr("name");
			var inval = $(this).val();
			save_data(names,inval,'',function(){})
		});

		$(".del_jobs").click(function(){
            var names = $(this).attr("id");
            var ids = names.split("_");
            ids = ids[ ids.length - 1];
            confirms( "Message", "Are you sure you want to delete?",
            function(){
                $.ajax({
                    url: '<?php echo URL; ?>/companies/jobs_delete/' + ids,
                    success: function(html){
                        if(html == "ok"){
                            $(".del_jobs_" + ids).fadeOut();
                            reload_subtab('jobs');
                        }else{
                            console.log(html);
                        }
                    }
                });
            },function(){
                //else do somthing
            });
        });
	})

	function job_add(){
		$.ajax({
			url:"<?php echo URL;?>/companies/jobs_add",
			success: function(result){
				if(result.indexOf('not_add') != -1)
					password_popup(result.split("||")[1]);
				else
					window.location = result;
				return false;
			}
		});
	}

	function password_popup(payment_date){
		if( payment_date == undefined )
			payment_date = 90;
		$("#confirms_window").val('').removeAttr("name");
		if( $("#confirms_window" ).attr("id") == undefined ){
			var html = '<div id="password_confirm" >';
				html +=	   '<div class="jt_box" style=" width:100%;">';
				//html +=		  '<div class="jt_box_line"><div class=" jt_box_label " style=" width:25%;"></div><div style="height: 10px;" id="alert_message"></div></div>';
				html +=		  '<div class="jt_box_line"><div class=" jt_box_label " style=" width:25% ;height: 39px;"></div><div>This company has unpaid invoices more than '+payment_date+' days, please check with HR to enter a password before creating a new job.</div></div>';
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
		//$("#password").val("");
		confirms_window.data("kendoWindow").center();
		confirms_window.data("kendoWindow").open();
		//$("#confirms_ok").unbind("click");
		$("#confirms_ok").click(function() {
			$("#alert_message").html("");
			if( $("#password").val().trim()==''  ){
				$("#alert_message").html('<span style="margin-left: 5px; color:red; font-weight: 700">Password must not be empty.</span');
				$("#password").focus();
				return false;
			}
			$("#password_jobs").val($("#password").val());
			$.ajax({
				url:"<?php echo URL;?>/companies/jobs_add",
				type:"POST",
				data: {password : $("#password").val()},
				success: function(result){
					if(result.indexOf("<?php echo URL.'/jobs/entry/' ?>") != -1){
						window.location = result;
					} else {
						alerts("Message",result);
					}
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