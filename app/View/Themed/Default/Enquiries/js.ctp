<?php echo $this->element('js/permission_entry'); ?>
<script type="text/javascript">
$(function(){

	enquiries_update_entry_header();
});

function enquiries_update_entry_header(){
	$("#enquiry_company_header").html($("#EnquiryCompany").val());

	if( $.trim($("#EnquiryContactName").val()) != "" )
		$("#enquiry_contact_header").html(" - " + $("#EnquiryContactName").val());

	if( $.trim($("#EnquiryStatus").val()) != "" )
		$("#enquiry_status_header").html("Status: " + $("#EnquiryStatus").val());

	if( $.trim($("#EnquiryOurRep").val()) != "" )
		$("#enquiry_responsible_header").html(" | " + $("#EnquiryOurRep").val());
}

function enquiries_auto_save_entry(object){
	if( $.trim($("#EnquiryHeading").val()) == "" ){
		$("#EnquiryHeading").val( "#" + $("#EnquiryNo").val() + "-" + $("#EnquiryEnquiryName").val());
	}

	if( $.trim($("#EnquiryWeb").val()).length > 0 ){
		var web = $("#EnquiryWeb").val();
		if( web.substring(0, 7) != "http://" ){
			web = "http://" + web;
		}
		$("#enquiries_web").attr("href", web);
	}

	enquiries_update_entry_header();

	$("form :input", "#enquiries_form_auto_save").removeClass('error_input');

	$.ajax({
		url: '<?php echo URL; ?>/enquiries/auto_save',
		timeout: 15000,
		type:"post",
		data: $("form", "#enquiries_form_auto_save").serialize(),
		success: function(html){
			if(html == "ref_no"){
				$("#EnquiryNo").addClass('error_input');
				alerts('Message', 'This "no" existed, please choose another');

			}else if( html != "ok" ){
				alerts('Message', html);
			}
			console.log(html); // view log when debug
		}
	});
}
</script>