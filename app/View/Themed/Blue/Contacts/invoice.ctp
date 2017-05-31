<?php 
		foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
			
			echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
		}
?>
<p class="clear"></p>

<script type="text/javascript">
	
	$(document).ready(function(){

		$('#bt_add_sale_invoice').click(function(){
			var id = $("#mongo_id").val();
			var url = "<?php echo URL; ?>/contacts/invoices_add/" + id;
			$(location).attr('href',url);
		})

	});

	
</script>
