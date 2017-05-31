<?php
	if(isset($arr_settings['relationship'][$sub_tab]['block']))
	foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
		echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
	}
?>
<p class="clear"></p>
<style>
.area_t{
height:100%;
}
</style>
<script type="text/javascript">

$(function(){
    // $("#shipper_id_tracking").val($("#shipper_id").val());
    $("#shipper_tracking").val($("#shipper").val());

<?php if($this->Common->check_permission('shippings_@_entry_@_edit',$arr_permission)){?>
 $("#editview_box_tracking_detail input").change(function() {
        var fieldname = $(this).attr("name");
        var values = $(this).val();
        var ids = $("#mongo_id").val();
        save_data(fieldname,values,'',ids,function(arr_ret){
            if(fieldname=='tracking_no')
                reload_subtab('tracking');
        });
    });
<?php } else {?>
   $("input,select,textarea", "#load_subtab").each(function() {
        $(this).attr("disabled", true).css("background-color", "transparent");
    });
   $("#editview_box_tracking_detail").find("span[title]").each(function(){
        $(this).remove();
    });
   $(".combobox_selector","#load_subtab").remove();
    <?php } ?>
})
</script>