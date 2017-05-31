<?php
	//for bug
	//pr($blockname);
	//pr($arr_subsetting);
	//pr($subdatas);
?>


<textarea class="area_t"  id="<?php if(isset($blockname)) echo $blockname;?>" name="<?php if(isset($blockname)) echo $blockname;?>" style=" <?php if(isset($arr_subsetting[$blockname]['textarea_css'])) echo $arr_subsetting[$blockname]['textarea_css'];?>"><?php if(isset($subdatas[$blockname][$blockname])) echo $subdatas[$blockname][$blockname];?></textarea>

