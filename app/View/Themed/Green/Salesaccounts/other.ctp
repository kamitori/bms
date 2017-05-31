<?php
    if(isset($arr_settings['relationship'][$sub_tab]['block']))
    foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
        echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
    }
?>
<p class="clear"></p>

<script type="text/javascript">
	$(function(){
		$("input",'#block_full_detail').change(function(){
			var ids = $("#mongo_id").val();
			var names = $(this).attr("name");
			var inval = $(this).val();
			save_option('account_details',inval,names);
		});

		/*$("#approved").click(function(){
			if($(this).is(':checked')){
				$("#approved_by").val("<?php echo $_SESSION['arr_user']['contact_name'];?>");
				$("#approved_date").val("<?php echo date('M d, Y',time()); ?>");
			}else{
				$("#approved_by").val('');
				$("#approved_date").val("");
			}
		});*/

		$("#editview_box_detail input").change(function(){
			var names = $(this).attr("name");
			var value = $(this).val();
			var data = {};
			data[names] = value;
			save_option('account_details',data,'0','other');
		});

		$("#editview_box_trade1 input").change(function(){
			var names = $(this).attr("name");
			var value = $(this).val();
			var data  =  {};
			data[names] = value;
			save_option('trade1',data,'0','other');
		});

		$("#editview_box_trade2 input").change(function(){
			var names = $(this).attr("name");
			var value = $(this).val();
			var data  =  {};
			data[names] = value;
			save_option('trade1',data,'1','other');
		});

		$("#editview_box_account_app input").change(function(){
			var names = $(this).attr("name");
			var value = $(this).val();
			var types = $(this).attr("type");
			var data = {};
			if(types == 'checkbox'){
				if($(this).is(':checked')){
					value = 1;
					data['approved_by'] = "<?php echo $_SESSION['arr_user']['contact_name'];?>";
					data['approved_date'] = "<?php echo strtotime(date('Y-m-d')); ?>";
					$("#approved_date").val("<?php echo date('M d, Y'); ?>");

				}
				else{
					value = 0;
					data['approved_by'] =  data['approved_date'] = "";
					$("#approved_date").val("");
				}
				$("#approved_by").val(data['approved_by'] );
			}
			data[names] = value;
			save_option('account_app',data,'0','other');
		});
	})
</script>