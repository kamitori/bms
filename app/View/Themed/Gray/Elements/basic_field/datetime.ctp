<!--<input name="<?php echo $keys;?>" id="<?php echo $keys;?>" class="input_1 float_left <?php if(isset($arr_field['more_in_class'])) echo $arr_field['more_in_class'];?> <?php if(isset($search_class)) echo $search_class;?>" <?php if(isset($search_flat)) echo $search_flat;?> type="text" value="<?php if(isset($arr_field['default']) && strtotime($arr_field['default'])>0) echo $this->Common->format_date(strtotime($arr_field['default'])); else if(isset($search_flat)) echo ''; else if(isset($arr_field['default']) && $arr_field['default']!='') echo ''; else echo $this->Common->format_date(); ?>" <?php if(isset($arr_field['element_input'])) echo $arr_field['element_input'];?> style=" <?php if(isset($arr_field['css'])) echo $arr_field['css'];?>" <?php if(isset($arr_field['lock']) && $arr_field['lock']=='0') echo ''; else{?>readonly="readonly"<?php }?> />

<?php if(isset($arr_field['moreinline'])) echo $arr_field['moreinline'];?>

<?php if(isset($arr_field['lock']) && $arr_field['lock']=='1') echo ''; else{?>
<script>
	$(function() {
		$( "#<?php echo $keys;?>" ).datepicker({dateFormat: 'dd M, yy' });
	});
</script>
<?php }?>-->



    <div class="two_colum border_right">
        <input name="<?php echo $keys;?>" class="input_1 float_left <?php if(isset($arr_field['more_in_class'])) echo $arr_field['more_in_class'];?> <?php if(isset($search_class)) echo $search_class;?>" <?php if(isset($search_flat)) echo $search_flat;?> style="width: 70px" readonly="readonly" type="text" value="<?php if(isset($arr_field['default']) && strtotime($arr_field['default'])>0) echo $this->Common->format_date(strtotime($arr_field['default'])); else if(isset($search_flat)) echo ''; else if(isset($arr_field['default']) && $arr_field['default']!='') echo ''; else echo $this->Common->format_date(); ?>" id="<?php echo $keys;?>" />
        <script>
			$(function() {
				$( "#<?php echo $keys;?>" ).datepicker({dateFormat: 'dd M, yy' });
			});
		</script>
    </div>
    <div class="once_colum top_se">
        <div class="styled_select">
			<select name="<?php echo $keys;?>_hour" class="input_1 float_left <?php if(isset($search_class)) echo $search_class;?>" <?php if(isset($search_flat)) echo $search_flat;?> style="margin: 0; margin-left:7px;" id="<?php echo $keys;?>Hour">

            <?php

			if(isset($datetime) && isset($datetime[$keys])){
				$thisval = $datetime[$keys];
			}else
				$thisval = '08:00';

			for($m=0;$m<24;$m++){
				if($m<10)
					$timestr = '0'.$m;
				else
					$timestr = ''.$m;
				if($m>7 && $m<18)
					$bgh = 'class="BgOptionHour"';
				else
					$bgh = '';
				if($thisval==$timestr.':00')
					$sl = ' selected="selected"';
				else
					$sl = '';

				if($thisval==$timestr.':30')
					$sl2 = ' selected="selected"';
				else
					$sl2 = '';
				if(isset($search_flat))
				echo '<option value="" selected="selected"></option>';
			?>
                <option value="<?php echo $timestr;?>:00" <?php echo $bgh;?> <?php echo $sl;?>><?php echo $timestr;?>:00</option>
                <option value="<?php echo $timestr;?>:30" <?php echo $bgh;?> <?php echo $sl2;?>><?php echo $timestr;?>:30</option>
            <?php }?>
           </select>
		</div>
	</div>
