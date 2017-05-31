<style type="text/css">
	.combobox_selector li{
		min-height: 10px;
	}
</style>
<?php
    if(isset($arr_settings['relationship'][$sub_tab]['block']))
    foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
        echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
    }
?>
<p class="clear"></p>

<script type="text/javascript">
	$(function(){
		fixHiddenCombobox("container_contacts");
		$('#bt_add_contacts').click(function(){
			var ids = $("#mongo_id").val();
			$.ajax({
				url:"<?php echo URL;?>/companies/contacts_add/" + ids,
				timeout: 15000,
				success: function(html){
					console.log(html);
					reload_subtab('contacts');
				}
			});
		})

		$("input","#block_full_contacts").change(function(){
			var names = $(this).attr("name");
			var inval = $(this).val();
			var ids = names.split("_");
			ids = ids[ids.length - 1];
			names = names.replace("_"+ids,"");
			names = names.replace("cb_","");
			if(names == "contact_default"){
					$('.viewcheck_contact_default').prop('checked',false);
					$(this).prop("checked",true);
					save_data('contact_default_id',ids,'',function(){
				});
				return false;
			}
			if($(this).is(':checkbox')){
				if($(this).is(':checked')){
					inval = 1;
				}else{
					inval = 0;
				}
			}
			save_data(names,inval,ids,'',function(){},"contacts");
			if(names == 'first_name' || names == 'last_name'){
				var first_name = $.trim($("#first_name_"+ids).val());
				var last_name = $.trim($("#last_name_"+ids).val());
				save_data('full_name',first_name+" "+last_name,ids,'',function(){},"contacts");
			}
		});

		$(".del_contacts").click(function(){
			var names = $(this).attr("id");
			var ids = names.split("_");
			ids = ids[ ids.length - 1];
			confirms( "Message", "Are you sure you want to delete?",
			function(){
				$.ajax({
					url: '<?php echo URL; ?>/companies/contacts_delete/' + ids,
					success: function(html){
						if(html == "ok"){
							$(".del_contacts_" + ids).fadeOut();
							reload_subtab('contacts');
						}else{
							console.log(html);
						}
					}
				});
			},function(){
				//else do somthing
			});
		});
	})

	function deleteopt(contact_id){
		confirms( "Message", "Are you sure you want to delete?",
			function(){
				$.ajax({
					url: '<?php echo URL; ?>/companies/contacts_delete/' + contact_id,
					success: function(html){
						if(html == "ok"){
							$("#del_contacts_" + contact_id).fadeOut();
						}else{
							console.log(html);
						}
					}
				});
			},function(){
				//else do somthing
		});
	}
</script>
