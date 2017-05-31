<?php
	$arr_val = $arr_settings['relationship']['other']['block'];
?>
<table cellpadding="0" cellspacing="0" border="0" style="margin:0; width:100%; border:none;padding-top:0;">
	<tr>
    	<td style="width:35%; padding-top:0; vertical-align:top;" valign="top">
        	<?php echo $this->element('box', array('key' => 'otherdetails', 'arr_val' => $arr_val['otherdetails']));?>
        	<?php echo $this->element('box', array('key' => 'noteactive', 'arr_val' => $arr_val['noteactive']));?>
        </td>
        <td rowspan="2" style="width:55%;padding-top:0; vertical-align:top;" valign="top">
        	<?php echo $this->element('box', array('key' => 'production_step', 'arr_val' => $arr_val['production_step']));?>
        </td>
    </tr>
</table>
<input type="hidden" id="your_user_name" value="<?php if(isset($your_user_name)) echo $your_user_name;?>" />
<input type="hidden" id="your_user_id" value="<?php if(isset($your_user_id)) echo $your_user_id;?>" />
<p class="clear"></p>


<script>
$(document).ready(function() {

	$("#bt_add_otherdetails").click(function() {
		var datas = new Object;
			datas['heading'] = 'This is new record. Click for edit';
			datas['details'] = '';
		save_option('otherdetails',datas,'',1,'other','add');
	});


	$(".del_otherdetails").focusin(function(){
		ajax_note_set("");
		var ids = $(this).attr("id");
		ids  = ids.split("_");
		var ind = ids.length;
		var idfield =  parseInt(ids[ind-1]);
		ajax_note_set(" Press ENTER to delete the line:"+(idfield+1));
	});



	$(".del_otherdetails").focusout(function(){
		ajax_note("");
		var ids = $(this).attr("id");
			ids = ids.split("_");
		var index = ids.length;
		ids  = parseInt(ids[index-1])+1;
		$(".jt_line_over").removeClass('jt_line_over');
		$("#listbox_sellprices_"+ids).addClass('jt_line_over');
	});


	$("#container_otherdetails").delegate(".rowedit input,.rowedit select","change",function(){
		//nhan id
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
		save_option('otherdetails',datas,ids,0,'other','update');

	});


	$("#bt_add_production_step").click(function() {
		var datas = new Object;
			datas['tag'] = '';
			datas['factor'] = '';
			save_option('production_step',datas,'',1,'other','add');
	});


	//kei, chỉnh sửa lại để phù hợp với cấu trúc mới
	$("#container_production_step").delegate(".rowedit input,.rowedit select","change",function(){
		$(this).removeClass('error_input');
		//nhan id
		var names = $(this).attr("name");
		var intext = 'box_test_'+names;
		var inval = $(this).val();
		var ids  = names.split("_");
		var index = ids.length;
		var key = ids[index-1];
		var ids = ids[index-2];
		var current = $(this);
		//khoi tao gia tri luu
		names = names.replace("_"+ids+"_"+key,"");
		var datas = new Object();
		datas[names] = inval;
		var dem =0;
		if(names=='tag_key'){
			//kiem tra xem co trung gia tri trong box ko
			var test =$("#container_production_step").find(".viewprice_tag_key");
			for(var i =0;i<test.length;i++){
				if(test[i].name.indexOf('tag_key_'+ids)!=-1 && test[i].value == inval)
				dem++;
			}
			datas['tag_key']=$("#"+$(this).attr("name")+"Id").val();
			datas['tag']=inval;
		}
		if(dem>1){
			$("#"+$(this).attr("name")+"Id").val(datas['tag_key']);
			$("#"+$(this).attr("name")).val(datas['tag']).addClass('error_input');
			alerts('Message','This tag has been included in the list. Please select another one.');
			return false;
		}else{
			save_option('production_step',datas,key,0,'other','update',function(){
				if(names=='tag'){
					$("#"+names+"_"+ids+"_"+key+"Id").val(datas['tag']);
					$("#"+names+"_"+ids+"_"+key).val(inval);
					$("#"+names+"_"+ids).removeClass('error_input');
				}
				else if(names=='factor' || names=='cost_per_hour' || names=='min_of_uom'){
					current.val(FortmatPrice(inval));
				}
			},names,ids);
		}

	});



	$("#bt_add_noteactive").click(function(){
		var datas = new Object;
		var user_name = $("#your_user_name").val();
		var user_id = $("#your_user_id").val();
			datas['note_type'] = 'Note';
			datas['note_dates'] = '<?php echo date('d M, Y');?>';
			datas['note_by'] = user_name;
			datas['note_by_id'] = user_id;
			datas['note_details'] = 'This is new record. Click for edit';
		save_option('noteactive',datas,'',1,'other','add');
		save_to_other_module('communications',datas,'',function(ret){
		});
	});
	$("#container_noteactive").delegate(".rowedit input,.rowedit select","change",function(){
		//nhan id
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
		save_option('noteactive',datas,ids,0,'other','update');

	});
});
</script>