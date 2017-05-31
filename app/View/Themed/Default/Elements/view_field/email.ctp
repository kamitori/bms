<input name="<?php echo $keys;?>" id="<?php echo $keys;?>" class="jt_box_input" type="<?php echo $arr_field['type'];?>" value="<?php if(isset($arr_field['default'])) echo $arr_field['default'];?>" <?php if(isset($arr_field['element_input'])) echo $arr_field['element_input'];?> style=" <?php if(isset($arr_field['css'])) echo $arr_field['css'];?>" maxlength="20" />
<a href="" title="Dial phone"><span class="jt_icon_email"></span></a>

<?php if(isset($arr_field['moreinline'])) echo $arr_field['moreinline'];?>