<div class="float_left hbox_form" style="width:auto; margin-left:5px;">
     <a id="create-email" href="javascript:void(0)">
     	<input class="btn_pur" id="email_pdf" type="button" value="Email PDF" style="width:99%;" />
     </a>
</div>
<div class="float_left hbox_form" style="width:auto; margin-left:5px;">
<!--     <a href="javascript:void(0)" id="view_pdf">-->
     <a id="view_pdf">     
     	<input class="btn_pur" id="printexport_products" type="button" value="Export PDF" style="width:99%;" />
     </a>
</div>
<script type="text/javascript">
	$("#create-email").click(function(){
		$.ajax({
			url: "<?php echo URL.'/quotations/check_quotation' ?>",
			success: function(result){
				if(result != "ok")
					alerts("Message",result);
				else
					window.location = "<?php echo URL.'/'.$controller; ?>/create_email_pdf/0/group";
			}
		});
	});
	$("#view_pdf").click(function(){
		$.ajax({
			url: "<?php echo URL.'/quotations/check_quotation' ?>",
			success: function(result){
				if(result != "ok")
					alerts("Message",result);
				else{
					confirms3("Message","Which report do you want to create?",["Detailed","Simple",""]
			          	,function(){
			          		window.open("<?php echo URL.'/'.$controller; ?>/view_pdf");
			          	},function(){
			          		window.open("<?php echo URL.'/'.$controller; ?>/view_pdf/0/group");
			          	},function(){
			          		return false;
			          	});
				}
			}
		});

	});
</script>