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
				url:"<?php echo URL;?>/companies/other_add/" + ids,
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

		$("input, checkbox","#editview_box_profile").change(function(){
			var names = $(this).attr("name");
			var inval = $(this).val();
			var ids = $("#mongo_id").val();
			var values = new Object();
			if($("#"+names).attr("type") == "checkbox"){
				if($(this).is(':checked')){
					inval = 1;
				}else{
					inval = 0;
				}
			}
			save_data(names,inval,'',function(){});
		})
	})
</script>