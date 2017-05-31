<option value="">   </option>
<?php foreach( $arr_province as $value ){?>
		<option value="<?php echo $value['_id']; ?>"><?php echo $value['name']; ?></option>
<?php } ?>