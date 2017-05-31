<?php 
	if(!isset($arr_view_st['edit'])){
		if(isset($arr_vls[$viewkeys]))?>
        
			<span id="txt_<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>">
				<?php if($arr_vls[$viewkeys]!='' && isset($arr_view_st['relid']) && isset($arr_view_st['cls'])){?>
						<a href="<?php echo URL.'/'.$arr_view_st['cls'].'/entry/'.$arr_vls[$arr_view_st['relid']];?>" class="jt_edit">
                        	<?php echo $arr_vls[$viewkeys];?>
                        </a>
				<?php }?>
           	</span>

<?php	}else if(isset($arr_vls) && is_array($arr_vls) && isset($viewkeys) && isset($arr_vls['_id'])){
			if(isset($arr_view_st['morekey']))
				$morekey = $arr_view_st['morekey'];
			else
				$morekey = '';
?>
		

<?php }?>