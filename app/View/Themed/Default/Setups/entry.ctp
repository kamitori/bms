<?php echo $this->element('../'.$name.'/tab_option');?>
<div id="content">
	<div class="percent content_indent">
		
        <div class="clear_percent_3 float_left bg_nav_setup">
			<ul class="nav_setup" id="settings_ul_nav_setup">
            	<?php 
					// Setup list
				if(isset($arr_settings['relationship']) && count($arr_settings['relationship'])>0 && is_array($arr_settings['relationship'])){
					foreach($arr_settings['relationship'] as $key=>$values){
						reset($values['block']); $first_key = key($values['block']);
				?>
                    <li>
                        <a id="<?php echo $key; ?>" href="javascript:void(0)" onclick="settings_list(this,'<?php echo $key; ?>');" class="<?php if($first_key==$active) { ?>active<?php } ?>"><?php if(isset($values['name'])) echo $values['name']; ?></a>
                    </li>
                <?php } }?>

				<li><a href="javascript:void(0)" style="background-color: #797979;;cursor:default">&nbsp;</a></li>
				<li><a href="javascript:void(0)" style="background-color: #797979;;cursor:default">&nbsp;</a></li>
			</ul>
		</div>
        
        
		<div class="percent_content block_dent_a float_left">
            <div id="detail_for_main_menu">
				<?php
                    if($active!=''){
                        if(file_exists(APP.'View'.DS.'Themed'.DS.'Default'.DS.$name.DS.$active.'.ctp' ))
                            echo $this->element('..'.DS.$name.DS.$active);
                        else
                            echo $this->element('../Elements/box_type/subtab_box_default');
                    }
                ?>
            </div>
			<p class="clear"></p>
		</div>
        
        
        
	</div>
	<p class="clear"></p>
</div>
<?php echo $this->element('../'.$name.'/js'); ?>