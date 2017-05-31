<?php 
		foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
			
			echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
		}
?>
<p class="clear"></p>

<script type="text/javascript">
	
	$(document).ready(function(){

		$("#container_working_hour").delegate(".rowedit input,.rowedit select","change",function(){
			//nhan id
			//alert('avc');
			var names = $(this).attr("name");
			var intext = 'box_test_'+names;
			var inval = $(this).val();
			var ids  = names.split("_");
			var index = ids.length;
			var ids = ids[index-1];
			//khoi tao gia tri luu
			console.log('names luc dau = ' + names);
			names = names.replace("_"+ids,"");
			var datas = new Object();
				datas[names]=inval;
			//luu lai
			console.log('names sau khi replace = ' + names);
			save_option('working_hour',datas,ids,0,'working','update',function(){});
		});


		$("#container_working_hour select").change(function(){
			var names = $(this).attr("name");//alert(names); // names = time1_hour_0
			var inval = $(this).val(); //alert(inval); // inval = 08:30
			var ids  = names.split("_");
			var ids = ids[ids.length-1]; // ids = 0
				//alert(ids);
			//khoi tao gia tri luu
			console.log('names luc dau = ' + names);
			names = names.replace("_"+ids,""); //alert(names);// names = time1_hour
			var datas = new Object();
				datas[names]=inval;  // datas['time1_hour']=10:30
			//luu lai
			console.log('names sau khi replace = ' + names);
			save_option('working_hour',datas,ids,0,'working','update',function(){});
		});
	});

	
</script>
