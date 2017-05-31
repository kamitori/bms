<?php
		foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){

			echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));

		}
?>
<p class="clear"></p>
<script type="text/javascript">
$(function(){
	window_popup("salesorders","Specify Sales order","salesorder_options","bt_add_salesorders");
});
function after_choose_salesorders(ids,names,keys){
	if(keys=='salesorder_options'){
		$(".k-window").fadeOut('slow');
		$.ajax({
			url: "<?php echo URL.'/salesinvoices/add_salesorder/' ?>",
			type: "POST",
			data: {ids:ids},
			success: function(result){
				if(result=='no_company')
	                alerts('Message','This function cannot be performed as there is no company or contact linked to this record.');
	            else if(result=='no_product')
	                alerts('Message','No items have been entered on this transaction yet.');
	            else if(result!='ok')
	            	alerts("Message",result);
	            else
	            	reload_subtab("salesorders");
			}
		})
	}
}
</script>