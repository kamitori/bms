<?php echo $this->element('js_entry');?>

<script type="text/javascript">
$(function(){
	window_popup('contacts', 'Specify First name','contact_name','click_open_window_contactscontact_name','?company_id='+$("#company_id").val(),'force_re_install');
	<?php if($arr_settings['module_label']=='Email'){ ?>
	<?php if(isset($status)&&$status!='Sent'): ?>
	if(location.href.indexOf("#send") !== -1
		 && check_format_email($("#email").val()))
		custom_alert();
	<?php endif; ?>
	CKEDITOR.replace('email_content',
	{
		toolbar : 'Email',
		<?php if(isset($status)&&$status=='Sent'): ?>
		readOnly : true,
		<?php endif; ?>
        resize_enabled : false,
        removePlugins : 'elementspath',
        height : 400,
        allowedContent: {
            'table b i u ul ol big small span label': { styles:true },
            'div' : { styles:true},
            'h1 h2 h3 hr p blockquote li': { styles:true },
        	a: { attributes: '!href' },
            img: {
                attributes: '!src,alt',
                styles: true,
                classes: 'left,right'
            }
        },
        filebrowserImageUploadUrl : '<?php echo URL; ?>/js/kcfinder/upload.php?type=images',
        filebrowserImageBrowseUrl : '<?php echo URL; ?>/js/kcfinder/browse.php?type=images',
        height : 310,
        // enterMode:CKEDITOR.ENTER_BR,
	});
	CKEDITOR.instances['email_content'].on('blur', function(e) {
        if (e.editor.checkDirty()) {
        	var fieldname = 'content';
            var values = CKEDITOR.instances.email_content.getData();
			var ids = $("#mongo_id").val();
			$("#email_content").val(values);
			save_data(fieldname,values,ids,'');
        }
    });
    <?php } else if($arr_settings['module_label']=='Note') { ?>
    CKEDITOR.replace('note_content',
	{
		toolbar : 'Email',
        resize_enabled : false,
        removePlugins : 'elementspath',
        height : 400,
        allowedContent: {
            'table b i u ul ol big small span label': { styles:true },
            'div' : { styles:true},
            'h1 h2 h3 hr p blockquote li': { styles:true },
        	a: { attributes: '!href' },
            img: {
                attributes: '!src,alt',
                styles: true,
                classes: 'left,right'
            }
        },
        filebrowserImageUploadUrl : '<?php echo URL; ?>/js/kcfinder/upload.php?type=images',
        filebrowserImageBrowseUrl : '<?php echo URL; ?>/js/kcfinder/browse.php?type=images',
        height : 325,
        // enterMode:CKEDITOR.ENTER_BR,
	});
	CKEDITOR.instances['note_content'].on('blur', function(e) {
        if (e.editor.checkDirty()) {
        	var fieldname = 'content';
            var values = CKEDITOR.instances.note_content.getData();
			var ids = $("#mongo_id").val();
			$("#note_content").val(values);
			save_data(fieldname,values,ids,'');
        }
    });
    <?php } ?>
	<?php if(!$this->Common->check_permission($controller.'_@_entry_@_edit',$arr_permission)): ?>
		$("textarea[name=content]").attr("readonly",true);
		$("textarea[name=internal_notes]").attr("readonly",true);
		$("select",".form_communications").each(function(){
			$(this).attr("disabled",true);
		});
	<?php endif; ?>
	getLinkModule();
	window_popup('docs', 'Specify documents','','click_open_window_docs');
	//default focus
	$("#email").focus();
	$("#email").focus(function(event) {
		if(check_format_email($(this).val()))
		{
			$(this).attr('rel',$(this).val());
		}
	});
	$(".menu_control li:first a").attr('href','javascript:choice_type_comms();');
	// Xu ly save, update
	$(".form_communications, .form_communications_note").on( "change", "input, textarea", function() {
		var fixkendo = $(this).attr('class');
		var fieldname = $(this).attr("name");
		var fieldid = $(this).attr("id");
		var fieldtype = $(this).attr("type");
		modulename = 'mongo_id';
		var ids = $("#"+modulename).val();
		var values = $(this).val();
		var func = ''; var titles = new Array();

		 //check address
		 var check_address = fieldname.split("[");
		 if(check_address[0]=='data'){
		   save_address(check_address,values,fieldid,function(){
		  });
		  return '';
		 }

		if(fieldtype=='checkbox'){
			if($(this).is(':checked'))
				values = 1;
			else
				values = 0;
		}
		var idk = fieldname.replace("_name","_id");
		var valid = $('#'+idk).val();
		if(valid==undefined)
			valid = '';
		if(fieldname=='email'&& (values.trim()==''|| !check_format_email(values)) )
		{
			alerts('Message','Enter a valid email address!',function(){
				if($("#email").attr('rel')!=undefined)
					$("#email").val($("#email").attr('rel'));
				$("#email").focus();
			});
			return false;
		}
		if(fieldname == "email_cc" && $.trim($("#email_ccId").val()) != ""){
			values = $("#email_ccId").val();
		}
		save_data(fieldname,values,ids,valid);
		// change tittle, thay đổi tiêu đề của items
		var titles;
		<?php foreach($arr_settings['title_field'] as $ks=>$vls){?>
			titles[<?php echo $ks;?>] = '<?php echo $vls;?>';
		<?php }?>
		if(titles.indexOf(fieldname)!=-1){
			$("#md_"+fieldname).html(values);
		}

	});

	$(".jt_ajax_note").html('');

	$("#send_email").click(function(){
		if($("#email").val().trim()=='')
		{
			alerts('Message','Enter the email address of receiver first.');
			$("#email").focus();
			return false;
		}
		else if(!check_format_email($("#email").val()))
		{
			alerts('Message','Enter a valid email address!',function(){
				$("#email").focus();
			});
			return false;
		}
		check_user_email_setting(
			function(){

				send_email();
			},
			function(){
				custom_alert();
			}
		);
	});
	$("#attach_file").click(function(){
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/check_attachment/'+$("#mongo_id").val(),
			success: function(result){
				if(result=='true')
				{
					confirms3('Message','Would you like to add a new document or link to an exist document from the Documents module?',['Existing','New','']
						,function(){//Existing
							$("#click_open_window_docs").click();
						}
						,function(){//New
							window.location.replace("<?php echo URL;?>/docs/add/Communications/"+$("#mongo_id").val());
						}
						,function(){
							return false;
						});
				}
				else
					alerts('Message','You reached the limit of attachment file.');
			}
		});
	});
});
function getLinkModule(){
	if($("#module").val()!=undefined && $("#module_id").val()!= undefined ){
		var module_name = $("#module").val().toLowerCase();
		var last_char = module_name.substr(module_name.length-1);
		if(last_char=='h'){
			module_name += 'es';
		} else if (last_char == 'y'){
			module_name = module_name.substr(0,module_name.length-1)+'ies';
		} else
			module_name += 's';
		var id = $("#module_id").val();
		var link = '<a style="color:#353535;" href="<?php echo URL; ?>/'+module_name+'/entry/'+id+'">Module</a>';
		$(".link_to_module").html(link);
	}
}
function load_attachment()
{
	$.ajax({
		url: '<?php echo URL.'/'.$controller;?>/ajax_attachment/'+$("#mongo_id").val(),
		success: function(result){
			if(result!='')
				$("#attachment_content").html(result);
		}
	});
}
function comms_docs_delete(doc_id){

	confirms( "Message", "Are you sure you want to delete?",
		function(){
			$.ajax({
				url: '<?php echo URL.'/'.$controller;?>/documents_delete/'+$("#mongo_id").val()+'/' + doc_id,
				timeout: 15000,
				success: function(html){
					if(html == "ok"){
						load_attachment();
					}else{
						alerts("Error: ", html);
					}
				}
			});
		},function(){
			//else do somthing
	});
	return false;
}
//check setting email
function check_user_email_setting(handleSend,handlePopup){
	handlePopup(); return false;
	$.ajax({
		url: '<?php echo URL.'/'.$controller;?>/check_user_email_setting',
		type:"POST",
		success: function(retn){
			ajax_note_set('');
			if(retn=='1')
				handleSend();
			else if(retn=='0')
				handlePopup();
			else
				alerts("Message","<?php msg('COM_NOT_SETUP');?>");

		}
	});
}

//check setting email
function send_email(use_system_email){
	var url = '<?php echo URL.'/'.$controller;?>/send_email';
	if(use_system_email)
		url = '<?php echo URL.'/'.$controller;?>/send_email/use_system_email';
	ajax_note_set("Sending...");
	$("#email_content").val(CKEDITOR.instances.email_content.getData());
	$.ajax({
		url: url,
		type:"POST",
		data: $(".form_communications").serialize(),
		success: function(text_return){
			ajax_note('');
			if(text_return=='sent'){
				ajax_note("Complete.");
				location.reload();
			}
			else if(text_return=='not_valid_info')
				alerts('Message','Your infomation is not valid. Please refresh and try again!');
			else if(text_return=='contact_admin')
				alerts('Message','Your email had not been configured in the system yet. Please contact administator to be support.');
			else
				alerts("Message",text_return);
		}
	});
}

function choice_type_comms(){
	//cutom Option value
	if( $("#confirms_window").attr("id") == undefined ){
		var buttons = '';
		buttons += '<input id="choose_email" class="button_spe" type="button" value="Email" />';
		buttons += '<input id="choose_letter" class="button_spe" type="button" value="Letter" />';
		buttons += '<input id="choose_fax" class="button_spe" type="button" value="Fax" />';
		buttons += '<input id="choose_note" class="button_spe" type="button" value="Note" />';
		//buttons += '<input id="choose_sms" class="button_spe" type="button" value="SMS" />';
		buttons += '<input id="choose_message" class="button_spe" type="button" value="Message" />';
		//buttons += '<input type="button" id="choose_cancel" class="pure-button" value=" Cancel " />';

		$('<div id="confirms_window" style="display:none;"><div class="choice_type_comms">'+buttons+'</div>').appendTo("body");
	}
	var confirms_window = $("#confirms_window");
		confirms_window.kendoWindow({
			width: "400px",
			height: "240px",
			title: "Choose type to create:"
		});

	//show popup
	confirms_window.data("kendoWindow").center();
	confirms_window.data("kendoWindow").open();
	window_popup('contacts','Specify employee','_message','choose_message','?is_employee=1');
	$('#choose_email').click(function() {
       confirms_window.data("kendoWindow").destroy();
	   add_new('comms_type','Email');
    });
	$('#choose_letter').click(function() {
       confirms_window.data("kendoWindow").destroy();
	   add_new('comms_type','Letter');
    });
	$('#choose_fax').click(function() {
       confirms_window.data("kendoWindow").destroy();
	   add_new('comms_type','Fax');
    });
    $('#choose_note').click(function(){
    	create_note();
		confirms_window.data("kendoWindow").destroy();
    });
    $('#choose_message').click(function(){
		confirms_window.data("kendoWindow").destroy();
    });
	$('#choose_cancel').click(function() {
       confirms_window.data("kendoWindow").destroy();
    });
}
//Xử lý khi chọn Note
function create_note()
{
	$.ajax({
		url: '<?php echo URL.'/'.$controller;?>/create_note',
		success: function(result){
			result = jQuery.parseJSON(result);
			if(result.status!='ok')
				alerts('Message',result);
			else
				window.location.replace('<?php echo URL.'/'.$controller;?>/entry/'+result.id.$id);
			console.log(result);
		}
	});

}
//Xử lý sau khi chọn docs
function after_choose_docs(doc_id, doc_name){
	var data = $("#Doc_" + doc_id).html();
	var html = $.parseHTML( data );
	$.ajax({
		url: '<?php echo URL.'/'.$controller;?>/documents_save/'+$("#mongo_id").val()+'/' + doc_id + '/' + $("#code").val()+'/Communications',
		timeout: 15000,
		success: function(html){
			if(html != "ok"){
				alerts("Error: ", html);
			}else{
				$("#window_popup_docs").data("kendoWindow").close();
				$("#documents").click();
				load_attachment();
			}
			console.log(html);
		}
	});

	return false;
}

//Xử lý sau khi chọn contact để gửi message
function after_choose_contacts_message(ids,names,keys)
{
	window.location.replace("<?php echo URL.'/'.$controller;?>/entry_message/"+ids);
	$(".k-window").fadeOut('slow');
}
// xử lý sau khi chọn company
function after_choose_companies(ids,names,keys){
	var mongoid,func;
	mongoid = $("#mongo_id").val();
	if(mongoid!='')
		func = 'update';
	else
		func = 'add';

	$(".link_to_company_name").attr("onclick", "window.location.assign('/jobtraq/companies/entry/"+ids+"')");
    $("#company_id").val(ids);
    $("#company_name").val(names);
    $(".link_to_company_name").addClass('jt_link_on');
	$("#md_company_name").html(names);
	$(".k-window").fadeOut('slow');

	$.ajax({
		url: '<?php echo URL.'/'.$controller;?>/ajax_save',
		type:"POST",
		data: {field:'company_id',value:ids,func:func,ids:mongoid},
		success: function(text_return){
			text_return = text_return.split("||");
			save_field('company_name',names,text_return[0]);
		}
	});
	reload_cc();
	var para = '';
	var company_id = $("#company_id").val();
	var company_name = $("#company_name").val();
	if(company_id!='')
		para += '?company_id='+company_id;
	if(company_name!='')
		para += '&company_name='+company_name;
	window_popup('contacts', 'Specify First name','contact_name','click_open_window_contactscontact_name',para,'force_re_install');
}

// xử lý sau khi chọn contact
function after_choose_contacts(ids,names,keys){
	var mongoid,func;
	mongoid = $("#mongo_id").val();
	if(mongoid!='')
		func = 'update';
	else
		func = 'add';
	if(keys=='contact_name'){
		$(".link_to_contact_name").attr("onclick", "window.location.assign('/jobtraq/contacts/entry/"+ids+"')");
		$("#contact_id").val(ids);
		$("#contact_name").val(names);
		$("#md_contact_name").html(names);
		save_data('contact_name',names,'', ids,function(result){
			$("#email").val(result.email);
			reload_cc();
		});

	}
	$(".link_to_contact_name").addClass('jt_link_on');
	$(".k-window").fadeOut('slow');
}


// xử lý sau khi chọn job,
function after_choose_jobs(ids,names,keys){
	if(keys=='job_name'){
		var module_from = 'Job';
		var arr = {
					"_id"	:"job_id",
					"name"	:"job_name",
					"no"	:"job_number"
				 }; //danh sách các field cần nhận về từ module jobs, và fields cần lưu
		$(".k-window").fadeOut('slow');
		$(".link_to_job_name").addClass('jt_link_on');
		save_data_form_to(module_from,ids,arr);
	}
}
<?php if($arr_settings['module_label']=='Email'): ?>
//De show popup nhap email va password
function custom_alert(){
	//cutom Option value
	<?php
		$email = $password = '';
		if(isset($current_email))
			$email = $current_email;
		if( isset($_SESSION['arr_user']['email_setting']) ){
			if( isset($_SESSION['arr_user']['email_setting']['email']) )
				$email = $_SESSION['arr_user']['email_setting']['email'];
			if( isset($_SESSION['arr_user']['email_setting']['password']) )
				$password = $_SESSION['arr_user']['email_setting']['password'];
		}
	?>
	var html = '<div id="email_configuration" style="width: 99%;padding:0px;">';
		html +=	   '<div class="jt_box" style=" width:100%;">';
		html +=		  '<div class="jt_box_line"><div class=" jt_box_label " style=" width:25%;"></div><div id="alert_message"></div></div>';
		html +=	      '<div class="jt_box_line">';
		html +=	         '<div class=" jt_box_label " style=" width:25%;">Email&nbsp;&nbsp;<span style="color:red;font-weight:700">*</span></div>';
		html +=          '<div class="jt_box_field " style=" width:71%"><input name="config_email" placeholder="example@example.com" id="config_email" class="input_1 float_left" type="email" value="<?php echo $email; ?>" /></div>';
		html +=	      '</div>';
		html +=	      '<div class="jt_box_line">';
		html +=	         '<div class=" jt_box_label " style=" width:25%;">Password&nbsp;&nbsp;<span style="color:red;font-weight:700">*</span></div>';
		html +=	         '<div class="jt_box_field " style=" width:71%"><input name="pass" id="pass" class="input_1 float_left" type="password" value="<?php echo $password; ?>" /></div>';
		html +=	      '</div>';
		html +=	      '<div class="jt_box_line">';
		html +=	         '<div class=" jt_box_label " style=" width:25%;">Save my emai</div>';
		html +=	         '<div class="jt_box_field " style=" width:71%"><span class="float_left jtchecktype" style=" width:101%;margin-left:0%;"><label class="m_check2"><input name="save_email" id="save_email" type="checkbox"  ><span style=" margin-left:3%;"></span></label><span class="fl_dent" for="save_email" style="margin-left:5px;color: #ddd" >Tick to save your email address.</span></span></div>';
		html +=	      '</div>';
		html +=	      '<div class="jt_box_line">';
		html +=	         '<div class=" jt_box_label " style=" width:25%;height:140px"><?php !$quotation_submit ? 'Or use system email ?' : '' ?></div>';
		<?php if( !$quotation_submit ){ ?>
		html +=	         '<div class="jt_box_field " style=" width:71%"><span class="float_left jtchecktype" style=" width:101%;margin-left:0%;"><label class="m_check2"><input name="use_system_email" id="use_system_email" type="checkbox"><span style=" margin-left:3%;"></span></label><span class="fl_dent" for="use_system_email" style="margin-left:5px;color: #ddd" >Tick to use system email to send.</span></span></div>';
		<?php } ?>
		html +=	      '</div><input style="margin-top:18%" type="button" class="jt_confirms_window_cancel" id="confirms_cancel" value=" Cancel " /><input style="margin-top:18%" type="button" class="jt_confirms_window_ok" value=" Ok " id="confirms_ok" />';
		html +=	   '</div>';
		html +=	'</div>';

	check_new = false;
	if( $("#email_configuration").attr("id") == undefined ){
			$(html).appendTo("body");
			check_new = true;
	}
	<?php if( !$quotation_submit ){ ?>
	if( $("#config_email").val() == "" )
		$("#use_system_email").prop('checked',true);
	<?php } ?>
	// $("#save_email").prop('checked', false);
	var email_configuration = $("#email_configuration");
		email_configuration.kendoWindow({
			width: "425px",
			height: "236px",
			title: 'Please enter your email',
			visible: false,
			activate: function(){
			  $('#config_email').focus();
			}
		});

	//show popup
	email_configuration.data("kendoWindow").center();
	email_configuration.data("kendoWindow").open();
	function valid(obj)
	{
		var pattern = '';
		if(obj.attr('name')=='config_email')
			return check_format_email(obj.val());
		return true;
	}

	if( check_new ){
		// khi khởi tạo thì mới bink action vào html
		$("#use_system_email").click(function(){
			if($(this).is(':checked'))
			{
				$(this).val(1);
				$("#save_email").attr('checked', false);
				$("#email_configuration input[type=text],input[type=password]").each(function(){
					$(this).val('');
				});
			}
			else
				$(this).val(0);
		});
		$("#save_email").click(function(event) {
			if($(this).is(':checked'))
			{
				$(this).val(1);
				$("#use_system_email").attr('checked', false);
			}
			else
				$(this).val(0);
		});
		$("#email_configuration :input").change(function(){
			$("#alert_message").html('');
			if($(this).attr('name')=='config_email')
			{
				if(!check_format_email($(this).val()))
				{
					$("#alert_message").html('<span style="margin-left: 5px; color:red; font-weight: 700">Your email must be valid!</span');
					$("#config_email").focus();
				}
			}
		});
		$('#confirms_ok').click(function() {
			if($("#use_system_email").is(':checked')==false)
			{
				$("#email_configuration input[type=text],input[type=password]").each(function(){
					$("#alert_message").html('');
					if( ( $(this).val().trim()==''  || !valid($(this)) ) )
					{
						$("#alert_message").html('<span style="margin-left: 5px; color:red; font-weight: 700">Your configuration must be valid!</span');
						$(this).focus();
						return false;
					}

				});

				email_configuration.data("kendoWindow").close();
				var data = '';
				data = $(".form_communications").serialize();
				data += '&email_one_use='+$("#config_email").val().trim();
				data += '&password_one_use='+$("#pass").val();
				ajax_note_set("Sending...");
				$.ajax({
					url: '<?php echo URL.'/'.$controller;?>/send_email',
					type:"POST",
					data: data,
					success: function(text_return){
						ajax_note('');
						if(text_return=='sent'){
							ajax_note("Complete.");
							if( $("#save_email").is(':checked')) {
								$.ajax({
									url: '<?php echo URL.'/'.$controller;?>/save_email_setting',
									type:"POST",
									data: $("input","#email_configuration").serialize(),
									success: function(result){
										email_configuration.data("kendoWindow").close();
										if(result=='ok') {
											location.reload();
										} else
											alerts('Message',result);
									}
								});
							}
						}
						else if(text_return=='not_valid_info')
							alerts('Message','Your infomation is not valid. Please refresh and try again!');
						else if(text_return=='contact_admin')
							alerts('Message','Your email had not been configured in the system yet. Please contact administator to be support.');
						else
							alerts("Message",text_return);
					}
				});
			}//use system email;
			else if($("#use_system_email").is(':checked')==true){
	       		email_configuration.data("kendoWindow").close();
				send_email('use_system_email');
			}
	    });
	    $('#confirms_cancel').click(function() {
			$("#alert_message").html('');
	    	$("#email_configuration :input").each(function(){
	    		if($(this).attr('type')!='button')
	    			$(this).val('');
	    		if($(this).attr('type')=='checkbox')
	    			$(this).attr('checked',false);
	    	});
	       	email_configuration.data("kendoWindow").close();
	    });
	    //end
	}

}
<?php endif; ?>



function reload_cc(company_id){
	if(company_id==undefined)
		company_id = $("#company_id").val();
	if(company_id!='' && company_id.length==24){
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/reload_cc',
			dataType: "json",
			type:"POST",
			data: {company_id:company_id},
			success: function(arr){
				jsondata = JSON.stringify(arr);
				unset_combobox('email_cc');
				//change_combobox('email_cc','abd');
				$("#email_cc").combobox(arr);
				//$("#email_bcc").combobox(jsondata);
			}
		});
	}
}

function save_address(arr,values,fieldid,handleData){
	 var keys = arr[1].replace("]","");
	 var keyups = keys.charAt(0).toUpperCase() + keys.slice(1);
	 var opname = keys + "_address";
	 var address_field = arr[2].replace("]","");
	 var datas = new Object();
	 if(address_field!='invoice_country' && address_field!='shipping_country' && address_field=='invoice_province_state' && address_field=='shipping_province_state'){
	  datas[address_field] = values;

	 //luu province
	 }else if(address_field=='invoice_province_state' || address_field=='shipping_province_state'){
	  var vtemp = $("#"+fieldid+'Id').val();
	   datas[address_field] = $("#"+fieldid).val();//luu gia tri custom cua province
	   datas[address_field+'_id'] = vtemp;
	  $("#"+keyups+'ProvinceState').css('border','none');
	  $("#"+keyups+'ProvinceState').css('border-bottom','1px solid #dddddd');
	  //$("#"+keyups+'ProvinceState').focus();

	 //luu country
	 }else{
	  vtemp = $("#"+fieldid+'Id').val();
	  datas[address_field] = $("#"+fieldid).val();
	  datas[address_field+'_id'] = vtemp;
	  if(vtemp=='CA' || vtemp=='US'){
	   $("#"+keyups+'ProvinceState').css('border','1px solid #f00');
	   $("#"+keyups+'ProvinceState').focus();
	  }else{
	   $("#"+keyups+'ProvinceState').css('border','none');
	   $("#"+keyups+'ProvinceState').css('border-bottom','1px solid #dddddd');
	  }
	 }
	 var olds = $("#"+opname).val();
	 if(olds!=''){
	  olds = '';
	  idas = '0';
	 }else{
	  olds = 'add';
	  idas = '';
	  $("#"+opname).val(values+',');
	 }
	 save_option(opname,datas,idas,0,'',olds,function(arr_return){
	  if(handleData!=undefined)
	   handleData(arr_return);
	  //save tax
	 });
	 ajax_note("Saving...Saved !");
}


</script>
