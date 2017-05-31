<?php 
		foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
			
			echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
		}
?>
<p class="clear"></p>

<script type="text/javascript">
	
	$(document).ready(function(){
		
		$("#bt_add_review").click(function(){
			var datas = new Object;
			//var user_name = $("#your_user_name").val();
			var user_name = $("#your_user_name").val();
			var user_id = $("#your_user_id").val();
				datas['heading'] = '';
				datas['details'] = 'This is new record. Click for edit !';
			save_option('review',datas,'',1,'rate','add');
			save_to_other_module('communications',datas,'',function(ret){
			});
		});

		$("#container_review").delegate(".rowedit input,.rowedit select","change",function(){
			//nhan id
			//alert('avc');
			var names = $(this).attr("name");
			var intext = 'box_test_'+names;
			var inval = $(this).val();
			var ids  = names.split("_");
			var index = ids.length;
			var ids = ids[index-1];
			//khoi tao gia tri luu
			names = names.replace("_"+ids,"");
			var datas = new Object();
				datas[names]=inval;
			//luu lai
			console.log('[vu]names = ' + names);
			save_option('review',datas,ids,0,'rate','update',function(){});
		});

		
		$("input, checkbox","#editview_box_profile").change(function(){
			var names = $(this).attr("name");
			var inval = $(this).val();
			var ids = $("#mongo_id").val();
			var values = new Object();
			if(names == 'include_in_phone_book'){
				if($(this).is(':checked')){
					inval = 1;
				}else{
					inval = 0;
				}
			}
			save_data(names,inval,'',function(){});
		})

	});

	
</script>
