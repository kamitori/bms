
<!--Dùng cho công thức tính-->

<input name="<?php echo $keys;?>" id="<?php echo $keys;?>" type="hidden" value="<?php if(isset($arr_field['default'])) echo htmlspecialchars($arr_field['default'], ENT_QUOTES);?>" />

<div id="map_<?php echo $keys;?>" class="jt_box_input" style=" background:#EBEBEB; <?php if(isset($arr_field['css'])) echo $arr_field['css'];?>"><?php if(isset($map_formula)) echo $map_formula;?></div>

<?php if(isset($arr_field['moreinline'])) echo $arr_field['moreinline'];?>