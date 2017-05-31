<?php echo $this->element('../'.$name.'/tab_option'); ?>
<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<div id="content">
	<div class="jt_ajax_note">Loading...</div>
    <!-- Title -->
    <div class="jbcont">
        <div class="jt_module_title float_left jt_t_left">
            <h1>
                <span id="md_<?php if(isset($arr_settings['title_field'][0])) $tit_0 = $arr_settings['title_field'][0]; echo $tit_0;?>">
                    <?php if(isset($item_title[$tit_0])) echo $item_title[$tit_0];?>:
                </span>
                <span id="md_<?php if(isset($arr_settings['title_field'][1])) $tit_1 = $arr_settings['title_field'][1]; echo $tit_1;?>">
                    <?php if($arr_settings['module_label']=='Message' || $arr_settings['module_label']=='Note') echo $contact; ?>
                    <?php if(isset($item_title[$tit_1])) echo $item_title[$tit_1];?>
                </span>
             </h1>
        </div>
        <div class="jt_module_title float_right jt_t_right">
            <h1>
                <span id="md_<?php if(isset($arr_settings['title_field'][2])) $tit_2 = $arr_settings['title_field'][2]; echo $tit_2;?>">&nbsp;
                    <?php if(isset($item_title[$tit_2])) echo $item_title[$tit_2];?>
                </span>
                <span id="md_<?php if(isset($arr_settings['title_field'][3])) $tit_3 = $arr_settings['title_field'][3]; echo $tit_3;?>">
                    <?php if(isset($item_title[$tit_3])) echo $item_title[$tit_3];?>
                </span>
            </h1>
        </div>
    </div>

    <!-- Add form -->
    <form class="form_<?php echo $controller;?><?php if($arr_settings['module_label']=='Note') echo '_note'; ?>" action="" method="post" class="float_left">
        <div class="clear_percent">
            <!--Elememt Panel type 01-->
            <?php if($arr_settings['module_label']=='Message' || $arr_settings['module_label']=='Note'){?>
            <div class="clear_percent_1 float_left">
            <?php }?>
                <?php echo $this->element('panel_group',array('datas'=>array('panel_1'),'css'=>'panel_1')); ?>
            <?php if($arr_settings['module_label']=='Message' || $arr_settings['module_label']=='Note'){?>
            </div>
            <?php } ?>
            <!--Elememt Panel type 02-->
            <?php if($arr_settings['module_label']!='Message' && $arr_settings['module_label']!='Note') echo $this->element('panel_group',array('datas'=>array('panel_2','panel_3','panel_4'),'css'=>'panel_2')); ?>
        </div>
        <?php
            if($arr_settings['module_label']!='Message'&&$arr_settings['module_label']!='Note')
                echo '<div class="clear"></div>';
        ?>
    <div class="<?php if($arr_settings['module_label']=='Message' || $arr_settings['module_label']=='Note') echo 'width69'; else echo 'width62'; ?> mar_gin float_left" style="margin-bottom:10px;<?php if($arr_settings['module_label']=='Message' || $arr_settings['module_label']=='Note') echo 'margin-top: 0%'; else echo 'height:450px;' ?>">
        <div class="full_width">
            <span class="title_block bo_ra1" style="display: block">
                <span class="fl_dent" style="display: block"><h4><?php if(isset($arr_settings['module_label'])) echo $arr_settings['module_label'];?> text</h4></span>
                    <div class="float_left hbox_form dent_left_form width37">
                        <?php if($arr_settings['module_label']=='Letter' || $arr_settings['module_label']=='Fax'){?>
                        	<input class="btn_pur size_width2" id="preview" type="button" value="Preview/print" />
                        <?php }else if($arr_settings['module_label']=='Note'){?>
                            <input class="btn_pur size_width2" id="spell_check" type="button" style="color:red" value="Spell check" />
                        <?php }else if($arr_settings['module_label']=='Message'){?>
                            <a class="btn_pur size_width2" id="reply_message" href="<?php echo URL.'/'.$controller.'/entry_message/'.$contact_id.'/'.$message_id;?>" style="text-align: center;text-decoration: none" /><?php echo translate('Reply'); ?></a>
                        <?php }else{?>
                        	<input class="btn_pur size_width2"  id="send_email" <?php if(isset($status)&&$status=='Sent') echo 'disabled type="hidden"'; else echo 'type="button"'; ?> value="Send Email" />
                        <?php }?>
                    </div>
            </span>
            <p class="clear"></p>
            <textarea class="w_text_area <?php if($arr_settings['module_label']=='Email') echo 'ckeditor" id="email_content'; else if($arr_settings['module_label']=='Note') echo 'ckeditor" id="note_content'; ?>" name="content" <?php if(isset($status)&&$status=='Sent') echo 'readonly="readonly"'; ?><?php if($arr_settings['module_label']=='Message') echo 'readonly="readonly"'; ?><?php if($arr_settings['module_label']=='Message') echo 'style="height: 92px;"'; else if($arr_settings['module_label']=='Note') echo 'style="height:365px;"'; ?>><?php if(isset($content)) echo $content;?></textarea>
            <span class="title_block bo_ra2" style="display: block">
                <span class="bt_block float_right no_bg" style="display: block"></span>
            </span>
        </div><!--END Tab1 -->
    </div>
    <?php if($arr_settings['module_label']=='Message'): ?>
    <div class="float_left width69 mar_gin" style="margin-top:0; margin-bottom:30px">
        <?php echo $this->element('../Communications/related_messages'); ?>
    </div>
    <div class="clear"></div>
    <?php endif; ?>
    <?php if($arr_settings['module_label']!='Message' && $arr_settings['module_label']!='Note'): ?>
    <div class="float_left width35 mar_gin" style="height: 520px;">
        <div class="tab_1 full_width">
        	<?php if($arr_settings['module_label']=='Letter' || $arr_settings['module_label']=='Fax'){ $styles = 'height:215px;';?>
            	<span class="title_block bo_ra1">
                    <span class="fl_dent"><h4>Internal Notes</h4></span>
                </span>
			<?php }else{ $styles = '';?>
                <span class="title_block bo_ra1">
                    <span id="click_open_window_docs" style="display: hidden"></span>
                    <span class="fl_dent"><h4><?php echo translate('Attachment') ?></h4></span>
                    <a <?php if(isset($status)&&$status=='Sent') echo 'style="display:none"'; ?> title="Add attachment" href="javascript:void(0)">
                        <span id="attach_file" class="icon_down_tl top_f"></span>
                    </a>
                </span>
                <p class="clear"></p>
                <ul class="ul_mag clear bg3" style="margin-bottom: 2px">
                    <li class="hg_padd" style="width:1.5%">&nbsp;</li>
                    <li class="hg_padd" style="width:40%"><?php echo translate('Document / file name'); ?></li>
                    <li class="hg_padd" style="width:35%"><?php echo translate('Location'); ?></li>
                    <li class="hg_padd" style="width:13%"><?php echo translate('Error'); ?></li>
                </ul>
                <div id="attachment_content" style="margin:0px; padding:0px;width:100%;height:65px">
                    <?php
                        $i = 0;
                        if(isset($attachment)&&$attachment!=''&&$attachment->count()>0):
                            foreach($attachment as $value):
                                $bg=($i%2==0? 'bg1':'bg2')
                     ?>
                        <ul class="ul_mag clear <?php echo $bg ?>" id="DocUse_<?php echo $value['_id']; ?>">
                            <li class="hg_padd" style="width:1.5%"><a href="<?php echo URL; ?>/docs/entry/<?php echo $value['_id']; ?>" title="View attachment"><span class="icon_emp"></span></a></li>
                            <li class="hg_padd" style="width:40%"><?php echo $value['name']; ?></li>
                            <li class="hg_padd" style="width:35%"><?php echo (isset($value['location'])?$value['location']:''); ?></li>
                            <li class="hg_padd center_txt" style="width:13%"><input type="checkbox" disabled readonly="readonly" /></li>
                            <li class="hg_padd center_txt" style="width:4%">
                                <div class="middle_check">
                                    <a <?php if(isset($status)&&$status=='Sent') echo 'style="display:none"'; ?> title="Delete link" href="javascript:void(0)" onclick="comms_docs_delete('<?php echo $value['_id'] ?>')">
                                        <span class="icon_remove2"></span>
                                    </a>
                                </div>
                            </li>
                        </ul>
                     <?php
                                $i++;
                            endforeach;
                        endif;
                        if($i<3)
                        {
                            for($j=$i;$j<3;$j++)
                            {
                                $bg=($j%2==0? 'bg1':'bg2');
                                echo '<ul class="ul_mag clear '.$bg.'"></ul>';
                            }
                        }
                     ?>
                </div>
                <span class="title_block"><h4>Internal Notes</h4></span>
			<?php } ?>

            <textarea <?php if(isset($status)&&$status=='Sent') echo 'readonly="readonly"'; ?>class="wr_text_area" name="internal_notes" id="internal_notes" style="<?php echo $styles;?>;height: 190px;"><?php if(isset($arr_settings['field']['panel_4']['internal_notes']['default'])) echo $arr_settings['field']['panel_4']['internal_notes']['default'];?></textarea>
            <div>
                    <span class="title_block"><h4>Custom fields</h4></span>
                        <div class="tab_2_inner">
                            <p class="clear">
                                <span class="label_1 float_left minw_lab2">Custom1</span>
                                </p><div class="width_in3a float_left indent_input_tp">
                                    <div class="styled_select">
                                        <select <?php if(isset($status)&&$status=='Sent') echo 'disabled'; ?>>
                                            <option select="selected"></option>
                                            <option>Developper</option>
                                            <option>Production</option>
                                            <option>Manuafacture</option>
                                        </select>
                                    </div>
                                </div>
                            <p></p>
                            <p class="clear">
                                <span class="label_1 float_left minw_lab2">Custom 2</span>
                                </p><div class="width_in3a float_left indent_input_tp">
                                    <div class="styled_select">
                                        <select <?php if(isset($status)&&$status=='Sent') echo 'disabled'; ?>>
                                            <option select="selected"></option>
                                            <option>Developper</option>
                                            <option>Production</option>
                                            <option>Manuafacture</option>
                                        </select>
                                    </div>
                                </div>
                            <p></p>
                            <p class="clear">
                                <span class="label_1 float_left minw_lab2 fixbor3">Custom 3</span>
                                </p><div class="width_in3a float_left indent_input_tp">
                                    <div class="styled_select">
                                        <select <?php if(isset($status)&&$status=='Sent') echo 'disabled'; ?>>
                                            <option select="selected"></option>
                                            <option>Developper</option>
                                            <option>Production</option>
                                            <option>Manuafacture</option>
                                        </select>
                                    </div>
                                </div>
                            <p></p>
                        <p class="clear"></p>
                    </div>
                </div>
            <span class="title_block bo_ra2">
            </span>
        </div><!--END Tab1 -->
    <?php endif; ?>
    </div>
</form>
	<?php echo $this->element('../'.$name.'/js');?>

</div>