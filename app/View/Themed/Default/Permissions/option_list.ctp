
<?php 
		foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
			
			echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
			
		}
?>
<!--JS Dành cho phần Pricing-->
<script>
$(document).ready(function() {
	//tạo thêm 1 products line mới
	$("#bt_add_optiondata").click(function(){
		var groupkey = $("#groupkey").val();
		var groupname = $("#groupname").val();
		if(groupkey=='' && groupname==''){
			alerts('Message','Please input into Group Key and Group Name');
		}else{
			var datas = {
				groupkey :[{'name':'123','codekey':'123'}]
			};
			save_option('option_list',datas,'',1,'option_list','add');
		}
	});
});



</script>