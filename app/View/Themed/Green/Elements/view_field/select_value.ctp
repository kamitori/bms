<?php
	if(isset($arr_vls) && is_array($arr_vls) && isset($viewkeys) && isset($arr_vls['_id'])){
		if(isset($arr_view_st['morekey']))
			$morekey = $arr_view_st['morekey'];
		else
			$morekey = '';
?>

		<div class="select_inner" style="width:90%;">
            <div class="styled_select <?php echo $morekey;?>rowedit <?php echo $morekey;?>rowedit_<?php echo $arr_vls['_id'];?>" id="box_edit_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>">
                <select name="<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" id="<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" class="viewselect viewselect_<?php echo  $viewkeys;?>" style="width:110%;">
					<?php foreach($arr_vls['sl_'.$viewkeys] as $kss=>$vss){?>
                		<option value="<?php echo $vss['op_value'];?>" label="<?php echo $vss['valuelist_name'];?>" <?php if($vss['op_value']==$arr_vls[$viewkeys] || $vss['default']==1 ){?>selected="selected"<?php }?>><?php echo $vss['valuelist_name'];?></option>
                    <?php }?>
                </select>
            </div>
        </div>


        <script>
            $(document).ready(function() {
                $("#box_test_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>").click(function(){
                    var vls = $("#box_edit_<?php echo $viewkeys.'_'.$arr_vls['_id'];?> input").val();
					$(".<?php echo $morekey;?>rowedit_<?php echo $arr_vls['_id'];?>").css('display','block');
					$(".<?php echo $morekey;?>rowtest_<?php echo $arr_vls['_id'];?>").css('display','none');
                    $("#box_edit_<?php echo $viewkeys.'_'.$arr_vls['_id'];?> input").focus();
					$("#box_edit_<?php echo $viewkeys.'_'.$arr_vls['_id'];?> input").select();
                });
                $("#box_edit_<?php echo $viewkeys.'_'.$arr_vls['_id'];?> input").focusout(function(){
                    $("#box_edit_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>").css('display','none');
                    $("#box_test_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>").css('display','block');
                });

            });
        </script>

<?php }?>