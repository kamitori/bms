<?php
	if(!isset($arr_view_st['edit'])){
		if(isset($arr_vls[$viewkeys]) && $arr_vls[$viewkeys]!=''){?>
			<span id="txt_<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>">
				<?php
					if(isset($option_select[$viewkeys][$arr_vls[$viewkeys]]))
						echo $option_select[$viewkeys][$arr_vls[$viewkeys]];
					else
						echo $arr_vls[$viewkeys];
				?>
            </span>

<?php	}
	}else if(isset($arr_vls) && is_array($arr_vls) && isset($viewkeys) && isset($arr_vls['_id'])){
			if(isset($arr_view_st['morekey']))
				$morekey = $arr_view_st['morekey'];
			else
				$morekey = '';
?>

		<div class="select_inner" style="width:90%;">
            <div class="styled_select <?php echo $morekey;?>rowedit <?php echo $morekey;?>rowedit_<?php echo $arr_vls['_id'];?>" id="box_edit_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>">
                <select name="<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" id="<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" class="viewselect viewselect_<?php echo  $viewkeys;?>">
				<?php
					if(isset($option_select[$viewkeys])){
					$str_temp = str_replace("selected=\"selected\"","",$option_select[$viewkeys]);
						if(isset($arr_vls[$viewkeys]))
					$str_temp = str_replace("value=\"".$arr_vls[$viewkeys]."\"","value=\"".$arr_vls[$viewkeys]."\" selected=\"selected\"",$option_select[$viewkeys]);
					echo $str_temp;
					}
                ?>
                </select>
            </div>
        </div>


		<!--<span class="float_left rowedit rowedit_<?php echo $arr_vls['_id'];?>" id="box_edit_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>" style="display:none; width:100%;">
            <input type="text" name="<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" id="<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" class="input_inner jt_box_save viewprice_<?php echo  $viewkeys;?>" value="<?php if(isset($arr_vls[$viewkeys])) echo $arr_vls[$viewkeys];?>" style=" text-align:<?php if(isset($arr_view_st['align'])) echo $arr_view_st['align'];else echo 'left';?>; <?php if(isset($arr_view_st['css'])) echo $arr_view_st['css'];?>" />
        </span>
        <span class="float_left jt_inedit rowtest_<?php echo $arr_vls['_id'];?>" style="width:100%;" id="box_test_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>" title="Click for edit">
            <?php if(isset($arr_vls[$viewkeys])) echo $arr_vls[$viewkeys];?>
        </span>-->
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