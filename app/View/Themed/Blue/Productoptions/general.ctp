<?php 
		foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
			
			echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
			
		}
?>
<script type="text/javascript">
	$(function(){
		$("#bt_add_valuelist").click(function() {
			var d = new Date();
			var n = d.getTime();
			n = String(n);
			var datas = {
				'op_value' : n, 
				'valuelist_name' : 'Edit lable',
				'default' : 0
			};
			save_option('valuelist',datas,'',1,'general','add');
		});
		
		$(".rowedit input,.viewcheck_default").change(function(){
			//nhan id
			var isreload=0;
			var names = $(this).attr("name");
			var intext = 'box_test_'+names;
			var inval = $(this).val();
			var ids  = names.split("_");
			var index = ids.length;
			var ids = ids[index-1];
			
			//khoi tao gia tri luu
			names = names.replace("_"+ids,"");
			names = names.replace("cb_","");
			if(names=='default'){
				if ($(this).attr("checked") == "checked") 
					inval = 0;
				else
					inval = 1;
				isreload=1;
			}
			var values = new Object();
				values[names]=inval;
			//ajax_note_set(" testing "+names+'='+inval);
			
			//luu lai
			save_option("valuelist",values,ids,isreload,'general');
			$('#'+intext).html(inval);
		});
		
	});
</script>