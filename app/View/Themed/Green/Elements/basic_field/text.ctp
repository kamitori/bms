<input name="<?php echo $keys;?>" id="<?php echo $keys;?>" class="input_1 float_left <?php if(isset($arr_field['more_in_class'])) echo $arr_field['more_in_class'];?> <?php if(isset($search_class)) echo $search_class;?>" <?php if(isset($search_flat)) echo $search_flat;?> type="<?php echo $arr_field['type'];?>" value="<?php if(isset($arr_field['default'])) echo htmlspecialchars($arr_field['default'], ENT_QUOTES);?>" <?php if(isset($arr_field['element_input'])) echo $arr_field['element_input'];?> style=" <?php if(isset($arr_field['css'])) echo $arr_field['css'];?>" <?php if(isset($arr_field['lock']) && $arr_field['lock']=='1'){?>readonly="readonly"<?php }?> />

<?php if(isset($arr_field['moreinline'])) echo $arr_field['moreinline'];?>