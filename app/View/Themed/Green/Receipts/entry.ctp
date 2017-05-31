
</style>
<?php echo $this->element('../'.$name.'/tab_option'); ?>
<div id="content">
	<div class="jt_ajax_note">Loading...</div>
    <!-- Title -->
    <div class="jbcont">
        <div class="jt_module_title float_left jt_t_left">
            <h1>
                <span id="md_<?php if(isset($arr_settings['title_field'][0])) $tit_0 = $arr_settings['title_field'][0]; echo $tit_0;?>">
                    <?php if(isset($item_title[$tit_0])) echo $item_title[$tit_0];?>
                </span>
                <span class="md_center">
					<?php if(isset($item_title[$tit_0]) && isset($item_title[$arr_settings['title_field'][1]])) echo '-';?>
                </span>
                <span id="md_<?php if(isset($arr_settings['title_field'][1])) $tit_1 = $arr_settings['title_field'][1]; echo $tit_1;?>">
                    <?php if(isset($item_title[$tit_1])) echo $item_title[$tit_1];?>
                </span>
             </h1>
        </div>
        <div class="jt_module_title float_right jt_t_right">
            <h1>
                <span id="md_<?php if(isset($arr_settings['title_field'][2])) $tit_2 = $arr_settings['title_field'][2]; echo $tit_2;?>">
                    <?php if(isset($item_title[$tit_2])) echo $item_title[$tit_2];?>
                </span>
                <span class="md_center">
					<?php if(isset($item_title[$tit_2])) echo '-';else echo '&nbsp;';?>
                </span>
                <span id="md_<?php if(isset($arr_settings['title_field'][3])) $tit_3 = $arr_settings['title_field'][3]; echo $tit_3;?>">
                    <?php if(isset($item_title[$tit_3])) echo $item_title[$tit_3];?>
                </span>
            </h1>
        </div>
    </div>

    <div id="<?php echo $controller;?>_form_auto_save">
        <!-- Add form -->
            <div class="clear_percent">
                <form class="form_<?php echo $controller;?>" action="" method="post" class="float_left">
                    <?php echo $this->element('panel_group',array('datas'=>array('panel_1'),'css'=>'panel_1')); ?>
                </form>
				<?php echo $this->element('box',array('panel'=>$arr_settings['field']['panel_2']['block'],'key'=>'allocation','arr_val'=>$arr_settings['field']['panel_2']['block']['allocation'])); ?>
                <?php

                echo $this->element('box',array('panel'=>$arr_settings['field']['panel_3']['block'],'key'=>'outstanding','arr_val'=>$arr_settings['field']['panel_3']['block']['outstanding']));

                ?>
            </div>

            <div class="clear_percent">
            	<?php echo $this->element('box',array('panel'=>$arr_settings['field']['panel_4']['block'],'key'=>'notes','arr_val'=>$arr_settings['field']['panel_4']['block']['notes'])); ?>
                <?php echo $this->element('box',array('panel'=>$arr_settings['field']['panel_5']['block'],'key'=>'comments','arr_val'=>$arr_settings['field']['panel_5']['block']['comments'])); ?>
            </div>

            <div class="clear"></div>

            <!--Elememt Sub Tab -->
            <?php //echo $this->element('sub_tab');?>
	</div>

    <!--Load cont of sub tab -->
    <div class="clear_percent" id="load_subtab">
		<?php //if($sub_tab!='') echo $this->element('../'.$name.'/'.$sub_tab);?>
    </div>
	<?php echo $this->element('../'.$name.'/js'); ?>

</div>
<script type="text/javascript">
    $(function(){
        $('.datainput_allocation .combobox_button').click(function(){
            $('.hg_padd datainput_allocation .combobox_selector').attr('style','width: 100px !important; left: -10px; top: 14px;');
        });

    });
</script>