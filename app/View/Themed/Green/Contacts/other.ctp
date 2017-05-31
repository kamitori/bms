<?php
	if(isset($arr_settings['relationship'][$sub_tab]['block']))
	foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
		echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
	}
?>
<p class="clear"></p>
<script type="text/javascript">
	$(function(){
		$('#bt_add_other_detail').click(function(){
			var ids = $("#mongo_id").val();
			$.ajax({
				url:"<?php echo URL;?>/contacts/other_add/" + ids,
				timeout: 15000,
				success: function(html){
					console.log(html);
					reload_subtab('other');
				}
			});
		})

		$("input","#container_other_detail").change(function(){
			var names = $(this).attr("name");
			var inval = $(this).val();
			var ids = names.split("_");
			ids = ids[ids.length-1];
			names = names.replace("_"+ids,"");
			var values = new Object();
			values[names] = inval;
			save_option("other",values,ids,0,"other");
			//save_option(opname,arr_value,opid,isreload,subtab,keys,handleData,fieldchage,module_id)
		});

		$('#off_report_working_hour, #anvy_support').click(function(){
			var value;
			var field = $(this).attr('id');
			if($(this).is(':checked'))
				value = 1;
			else
				value = 0;
			save_data(field,value,'','',function(txt){
				// console.log(txt);
			});
		});
	})
</script>