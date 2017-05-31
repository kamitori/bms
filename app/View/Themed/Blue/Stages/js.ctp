<script type="text/javascript">
$(function(){

	stages_change_header();

	$("li", "#stages_ul_sub_content").click(function() {
		var val = $(this).attr("id");
		$("li", "#stages_ul_sub_content").removeClass("active");
		$("#"+val).addClass("active");
		$.ajax({
			url: '<?php echo URL; ?>/stages/sub_tab/'+val + "/<?php echo $this->data['Stage']['_id']; ?>",
			success: function(html){
				$("#stages_sub_content").html(html);
			}
		});

	});

	$("form :input", "#stages_form_auto_save").change(function() {
		stages_auto_save_entry();
	});
});

function stages_change_header(){
	$("#stage_stage_header").html($("#StageStage").val());
	if( $.trim( $("#StageJob").val() ) != "" ){
		$( "#stage_job_header" ).html( " | Job: " + $("#StageJob").val() );
	}
	$("#stage_status_header").html($("#StageStatus").val());
	if( $.trim( $("#StageOurRep").val() ) != "" ){
		$( "#stage_responsible_header" ).html( " | " +  $("#StageOurRep").val() );
	}

	var oneDay = 24*60*60*1000; // hours*minutes*seconds*milliseconds
	var firstDate = new Date($("#StageWorkEnd").val());
	var secondDate = new Date();
	var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime())/(oneDay)));
	if( firstDate.getTime() < secondDate.getTime() ){
		diffDays = -diffDays;
		$("#stages_days_left").attr("style", "color: red");

	}else{
		$("#stages_days_left").removeAttr("style");

	}
	$("#stages_days_left").val(diffDays);
}

function stages_auto_save_entry(){
	if( $.trim($("#StageHeading").val()) == "" ){
		$("#StageHeading").val( "#" + $("#StageNo").val() + "-" + $("#StageCompanyName").val());
	}

	stages_change_header();

	$("form :input", "#stages_form_auto_save").removeClass('error_input');

	$.ajax({
		url: '<?php echo URL; ?>/stages/auto_save',
		timeout: 15000,
		type:"post",
		data: $("form", "#stages_form_auto_save").serialize(),
		success: function(html){
			if(html == "date_work"){
				$("#StageWorkStart").addClass('error_input');
				$("#StageWorkEnd").addClass('error_input');
				alerts("Error:", '"Work start" can not greater than "Work end"');

			}else if(html != "ok"){

				alerts("Error: ", html);
			}

			console.log(html); // view log when debug
		}
	});
}
</script>