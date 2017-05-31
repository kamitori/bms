<?php
	$timed = '';
	if(isset($arr_view_st['fulltime']) && $arr_view_st['fulltime']=='1')
		$timed = 'H:i, ';

	if(!isset($arr_view_st['edit'])){
		if(isset($arr_vls[$viewkeys])){?>

			<span id="txt_<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>">
            	<?php
					if(isset($arr_vls[$viewkeys]) && is_object($arr_vls[$viewkeys]) )
						echo date($timed."d M, Y",$arr_vls[$viewkeys]->sec);
					else if($arr_vls[$viewkeys]!='')
						echo date($timed."d M, Y",$arr_vls[$viewkeys]);
				?>
           	</span>

<?php }	}else if(isset($arr_vls) && is_array($arr_vls) && isset($viewkeys) && isset($arr_vls['_id'])){
			if(isset($arr_view_st['morekey']))
				$morekey = $arr_view_st['morekey'];
			else
				$morekey = '';
?>
			<span class="float_left <?php echo $morekey;?>rowedit <?php echo $morekey;?>rowedit_<?php echo $arr_vls['_id'];?>" id="box_edit_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>" style="width:100%;">
                <input type="text" readonly name="<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" id="<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" class="input_inner jt_box_save viewprice_<?php echo  $viewkeys;?>" value="<?php if(isset($arr_vls[$viewkeys]) && is_object($arr_vls[$viewkeys]) ) echo $this->Common->format_date($arr_vls[$viewkeys]->sec); else if($arr_vls[$viewkeys]!='') echo $this->Common->format_date($arr_vls[$viewkeys]); ?>" style=" text-align:<?php if(isset($arr_view_st['align'])) echo $arr_view_st['align'];else echo 'left';?>; <?php if(isset($arr_view_st['css'])) echo $arr_view_st['css'];?>" <?php if(isset($arr_vls['attr'][$viewkeys])) echo $arr_vls['attr'][$viewkeys];?> />
            </span>

		<script>
            $(function() {
                $( "#<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" ).datepicker({dateFormat: 'dd M, yy', changeMonth: true, changeYear: true, yearRange: "c-70:c+3" });
                // $( this ).datepicker({ changeMonth: true, changeYear: true, yearRange: "c-70:c+3"});
            });
        </script>

<?php }?>