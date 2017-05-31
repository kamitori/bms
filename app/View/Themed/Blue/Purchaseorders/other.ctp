<?php if($this->Common->check_permission($controller.'_@_other_tab_@_view',$arr_permission)): ?>
<style>
.clear_percent_14 {
width: 100%;
}
</style>
<div class="clear_percent" style="width:100%; margin:0px;">
	<div class="clear_percent_6a float_left">
		<div class="clear">

			<div class="clear_percent_14 float_left">
				<div class="tab_1 full_width">
					<span class="title_block bo_ra1">
						<span class="fl_dent"><h4>Comments on purchase order</h4></span>
					</span>
					<form id="other_comment">
						<textarea class="area_t2"><?php if(isset($arr_return['other_comment'])) echo $arr_return['other_comment'];?></textarea>
					</form>
					<span class="title_block bo_ra2">
						<p class="cent">These details appear on the print / email version of the document</p>
					</span>
				</div><!--END Tab1 -->
			</div>
			<p class="clear"></p>
		</div>
		<div class="full_width block_dent9 ">
			<div class="tab_1 full_width">
                <?php echo $this->element('communications'); ?>
			</div><!--END Tab1 -->
		</div>
	</div>
	<div class="clear_percent_7a float_left">
		<div class="tab_1 full_width">
			<span class="title_block bo_ra1">
				<span class="float_left">
					<span class="fl_dent"><h4>Custom field</h4></span>
				</span>
			</span>
			<div class="tab_2_inner">
				<p class="clear">
					<span class="label_1 float_left minw_lab2">Custom field 1</span>
				<div class="width_in3a float_left indent_input_tp">
					<input class="input_1 float_left" type="text" value="">
				</div>
				</p>
				<p class="clear">
					<span class="label_1 float_left minw_lab2">Custom field 2</span>
				<div class="width_in3a float_left indent_input_tp">
					<input class="input_1 float_left" type="text" value="">
				</div>
				</p>
				<p class="clear">
					<span class="label_1 float_left minw_lab2" style="height: 343px;">Custome field 3</span>
				<div class="width_in3a float_left indent_input_tp">
					<input class="input_1 float_left" type="text" value="">
				</div>
				</p>
				</p>

				<p class="clear"></p>
			</div>

			<span class="title_block bo_ra2"></span>
		</div><!--END Tab1 -->
	</div>
</div>
<p class="clear"></p>
<script type="text/javascript">
<?php if($this->Common->check_permission($controller.'_@_other_tab_@_edit',$arr_permission)): ?>
 function comm_delete(comm_id){
      confirms( "Message", "Are you sure you want to delete?",
                function(){
                    $.ajax({
                        url: '<?php echo URL; ?>/communications/comm_delete/' + comm_id,
                        timeout: 15000,
                        success: function(html){
                            if(html == "ok")
                                $("#Comm_" + comm_id).fadeOut();
                            else
                                alerts("Error: ", html);
                            console.log(html);
                        }
                    });
                },function(){
                    //else do somthing
            });
            return false;
    }
 $(function(){
      $('#comms_create').click(function(){
           if($('#comms_type').val()=='')
               alerts('Message','Please first specify a type of record you would like to create.',function(){
                   $('#_comms .combobox_selector').attr('style','position: absoluteleft: 0px;top: 16px;width: 82% !important; display: block;');
               });
            else
               add_comm_from_module();
            return false;
        });

    });
    function other_tab_auto_save(id,content){
        $.ajax({
            url: "<?php echo URL; ?>/<?php echo $controller;?>/other_tab_auto_save/",
            timeout: 15000,
            type: "POST",
            data: { id: id, content : content },
            success: function(html){
                if(html != "ok"){
                    alerts("Error: ", html);
                }

            }
        });
        return false;
    }
    $("#other_comment textarea").change(function() {
        var ids = $("#mongo_id").val();
        var data = 'other_comment='+$(this).val();
        other_tab_auto_save(ids,data);
    });
<?php else: ?>
    $(function(){
        $("input,textarea","#load_subtab").each(function(){
            $(this).attr("disabled",true).css("background-color","transparent");
        });
    })
<?php endif; ?>
</script>
<?php endif; ?>