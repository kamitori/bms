<a title="Insert timestamp" id="req" >
	<span class="icon_notes top_f"></span>
</a>

<a href="<?php echo URL ;?>/contacts/print_requirements_pdf/<?php echo $contact_id;?>" target="_blank">
	<input class="btn_pur" id="printexport_products" type="button" value="Export PDF" style="width: 66px;">
</a>


<script type="text/javascript">
$(document).ready(function() {
	$("#req").click(function(){
		
		var text = $("#requirement").val();
		if( $.trim(text) != "" ){
			text = text + "\n" + "<?php echo $_SESSION['arr_user']['contact_name'];?> - <?php echo date('M d, Y H:i',time()); ?>:";
		}else {
			text = "<?php echo $_SESSION['arr_user']['contact_name'];?> - <?php echo date('M d, Y H:i'); ?>:";
		}
		$('#requirement').val(text);

		var id_mongo = $("#mongo_id").val();
		var name = 'requirement';
		var value = $('textarea#requirement').val();
		save_field(name,value,id_mongo);
	});

});

</script>

		

