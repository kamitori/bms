<?php if(isset($arr_field['afterinline'])) echo $arr_field['afterinline'];?>
<input name="<?php echo $keys;?>" class="float_left" type="button" value="<?php if(isset($arr_field['value'])) echo $arr_field['value'];?>" id="<?php echo $keys;?>" style=" <?php if(isset($arr_field['css'])) echo $arr_field['css'];?>" <?php if(isset($arr_field['element_input'])) echo $arr_field['element_input'];?> />

<?php if(isset($arr_field['moreinline'])) echo $arr_field['moreinline'];?>