<?php
	$arr_val = $arr_settings['relationship']['other']['block'];
	//pr($arr_val);die;
?>
<div class="clear_percent" style="width:100%; margin:0px;">
   <div class="clear_percent_6a" style="width:100%">
		   <div class="clear_percent_14 float_left" style="margin-top:1px; width:54%;">
				<?php echo $this->element('communications'); ?>
			</div>
			<div class="clear_percent_18 float_left height_box" style="width:44.2%; margin-left:1.3%;">
				<?php echo $this->element('box', array('key' => 'otherdetails', 'arr_val' => $arr_val['otherdetails']));?>
			</div>
</div>
<p class="clear"></p>

<!--JS Dành cho phần subtab-->
<script type="text/javascript">
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
</script>