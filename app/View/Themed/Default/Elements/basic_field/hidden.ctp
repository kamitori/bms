<input name="<?php echo $keys;?>" id="<?php echo $keys;?>" class="jthidden" type="hidden" value="<?php if(isset($arr_field['default'])) echo $arr_field['default'];?>" <?php if(isset($arr_field['element_input'])) echo $arr_field['element_input'];?> />

<?php if(isset($arr_field['moreinline'])) echo $arr_field['moreinline'];?>