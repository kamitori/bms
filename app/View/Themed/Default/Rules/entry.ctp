<?php echo $this->element('../'.$name.'/tab_option'); ?>
<div id="content">
	<div class="jt_ajax_note">Loading...</div>
    <!-- Title -->
    <div class="jbcont">
        <div class="jt_module_title float_left jt_t_left">
            <h2>
                <span id="md_<?php if(isset($arr_settings['title_field'][0])) $tit_0 = $arr_settings['title_field'][0]; echo $tit_0;?>">
                    <?php if(isset($item_title[$tit_0])) echo $item_title[$tit_0];?>
                </span> |
                <span id="md_<?php if(isset($arr_settings['title_field'][1])) $tit_1 = $arr_settings['title_field'][1]; echo $tit_1;?>">
                    <?php if(isset($item_title[$tit_1])) echo $item_title[$tit_1];?>
                </span>
             </h2>
        </div>
        <div class="jt_module_title float_right jt_t_right">
            <h2>Type:
                <span id="md_<?php if(isset($arr_settings['title_field'][2])) $tit_2 = $arr_settings['title_field'][2]; echo $tit_2;?>">
                    <?php if(isset($item_title[$tit_2])) echo $item_title[$tit_2];?>
                </span> |
                <span id="md_<?php if(isset($arr_settings['title_field'][3])) $tit_3 = $arr_settings['title_field'][3]; echo $tit_3;?>">
                    <?php if(isset($item_title[$tit_3])) echo $item_title[$tit_3];?>
                </span>
            </h2>
        </div>
    </div>

    <!-- Add form -->
    <form class="form_<?php echo $controller;?>" action="" method="post" class="float_left">
        <div class="clear_percent">
            <!--Elememt Panel type 02-->
            <?php echo $this->element('panel_group',array('datas'=>array('panel_2'),'css'=>'panel_2')); ?>

            <!--Elememt Panel type 01-->
            <?php echo $this->element('panel_group',array('datas'=>array('panel_1'),'css'=>'panel_1')); ?>
        </div>
        <div class="clear"></div>

        <!--Elememt Sub Tab -->
        <?php echo $this->element('sub_tab');?>
    </form>


    <!--Load cont of sub tab -->
    <div class="clear_percent" id="load_subtab">
		<?php if($sub_tab!='') echo $this->element('../'.$name.'/'.$sub_tab);?>
    </div>

    <div id="list_field_<?php echo $controller;?>_popup" style="display:none; min-width:300px;">

		<?php $n=1;
			foreach($product_arrfield as $keyp => $vlp){?>
        	<input type="button" value="  <?php echo $keyp;?> " name="<?php echo $keyp;?>_prname" id="<?php echo $keyp;?>_prid" style="margin:2px; padding:5px; cursor:pointer;" onclick="StringPlus('<?php echo $keyp;?>','');" />
			<?php if($n%3==0) echo '<br />'; $n++;

			}?>


    </div>
	<?php echo $this->element('../'.$name.'/js'); ?>

</div>