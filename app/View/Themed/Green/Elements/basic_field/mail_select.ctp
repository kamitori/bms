<?php
	if(isset($arr_field['default']))
		$thisval_key =  $arr_field['default'];
	else
		$thisval_key = '';
	if(isset($arr_options_custom[$keys][$thisval_key]))
		$thisval = $arr_options_custom[$keys][$thisval_key];
	else if(isset($arr_options[$keys][$thisval_key]))
		$thisval = $arr_options[$keys][$thisval_key];
	else
		$thisval = $thisval_key;
?>
<select name="<?php echo $keys;?>" id="<?php echo $keys;?>" <?php if(isset($arr_field['element_input'])) echo $arr_field['element_input'];?> <?php if((isset($arr_field['not_custom']) && $arr_field['not_custom']=='1')|| (isset($arr_field['lock']) && $arr_field['lock']=='1')){?>readonly="readonly"<?php }?>  value="<?php echo $thisval;?>" ></select>
<script>
    $(function() {
        // create ComboBox from input HTML element
        var comboBox =  $("#<?php echo $keys;?>").kendoComboBox({
            dataTextField: "text",
            dataValueField: "value",
            placeholder: "Select ...",
            dataSource: [
                { text: "", value: "" },
                { text: "Cotton", value: "1" },
                { text: "Polyester", value: "2" },
                { text: "Rib Knit", value: "4" }
            ],
            filter: "contains",
            suggest: true,
            index: 3,
            ignoreCase: false
        });
        $("#<?php echo $keys;?>").focus(function(){
        	comboBox.open();
        });
    });
</script>