<?php
	/*$arr_val 	: array sub setting(many box in one sub)
	$key 		: box name
   	$subdatas	: array data
   	$sub_tab	: subtab name
    $panel 		: array box setting. If use as panel*/
    //pr($sub_tab);
	//pr($panel);
	//pr($subdatas);
	//pr($key);
	//pr($arr_val);die;
?>

<div class="float_left <?php if(isset($arr_val['moreclass'])) echo $arr_val['moreclass'];?>" style=" <?php if(isset($arr_val['css'])) echo $arr_val['css'];?>">
    <div class="tab_1 full_width" id="block_full_<?php echo $key;?>">

        <!-- Header-->
         <span class="title_block bo_ra1">
            <span class="fl_dent">
                <h4><?php if(isset($arr_val['title'])) echo $arr_val['title'];?></h4>
            </span>
            <?php if(isset($arr_val['add'])){?>
                <a title="<?php echo $arr_val['add'];?>" id="bt_add_<?php echo $key;?>">
                	<span class="icon_down_tl top_f"></span>
               	</a>
            <?php }?>
            <?php if(isset($arr_val['upload'])){?>
                <?php if( isset($arr_val['button']) ){ ?>
                <span class="icon_down_tl top_f" style="cursor:pointer;">
                    <input name="button" class="float_left" type="button" value=" <?php echo $arr_val['button'] ?> " id="button" style="cursor:pointer;">
                </span>
                <?php } else { ?>
                <span class="icon_down_tl top_f" style="cursor:pointer;">
                	<form action="<?php echo URL.'/'.$controller;?>/upload" id="<?php echo $controller;?>_upload_form" method="post" enctype="multipart/form-data">
                    	<input type="file" name="<?php echo $controller;?>_upload" id="<?php echo $controller;?>_upload" class="jt_upload" title="<?php echo $arr_val['upload'];?>" style="cursor:pointer;" />
                    </form>
                </span>
                <?php } ?>
            <?php }?>
            <?php if(isset($arr_val['emailexport'])){?>
            	<div class="float_left hbox_form" style="width:auto;">
                    <input class="btn_pur" id="emailexport_<?php echo $key;?>" type="button" value="<?php echo $arr_val['emailexport'];?>" style="width:99%;" />
                </div>
            <?php }?>
            <?php if(isset($arr_val['printexport'])){?>
               <div class="float_left hbox_form" style="width:auto; margin-left:5px;">
                    <input class="btn_pur" id="printexport_<?php echo $key;?>" type="button" value="<?php echo $arr_val['printexport'];?>" style="width:99%;" />
                </div>
            <?php }?>
            <?php if(isset($arr_val['print'])){?>
                <a href="" title="<?php echo $arr_val['print'];?>"><span class="icon_print top_f"></span></a>
            <?php }?>
            <?php if(isset($arr_val['search'])){?>
                <form class="float_left hbox_form" style="width:150px;">
                    <input type="text" class="top_m float_left" />
                    <a href="<?php echo URL.'/'.$controller;?>/add" title="Create"><span class="icosp_sea indent_sea"></span></a>
                </form>
            <?php }?>
            <?php if(isset($arr_val['custom_box_top']))
            		echo $this->element('../'.$name.'/'.$key.'_custom_box_top');
            ?>
            <?php if(isset($arr_val['checkbox'])){?>
                <span class="float_right">
                    <span class="fl_dent">
                        <h6><?php if(isset($arr_val['checkbox']['label'])) echo $arr_val['checkbox']['label'];?></h6>
                    </span>
                    <label class="m_check">
                        <input type="checkbox">
                        <span></span>
                    </label>
                </span>
            <?php }?>
            <?php if(isset($arr_val['searchselect'])){?>
                <span class="float_right">
                    <span class="block_sear bl_t">
                        <span class="bg_search_1"></span>
                        <span class="bg_search_2"></span>
                        <div class="box_inner_search float_left">
                            <a href=""><span class="icon_search"></span></a>
                            <select class="sele3_fix_s float_left">
                                <option selected="selected"></option>
                                <option>Designer</option>
                                <option>Developer</option>
                            </select>
                            <a href=""><span class="icon_show"></span></a>
                            <a href=""><span class="icon_closef"></span></a>
                        </div>
                    </span>
                </span>
            <?php }?>
         </span>


         <!--CONTENTS-->
         <div class="jt_subtab_box_cont" style=" <?php if(isset($arr_val['height'])) echo 'height:'.$arr_val['height'].'px;';?>">
             <?php
			 	if(isset($panel) && isset($arr_val['type']) && isset($key))
					echo $this->element('box_type/'.$arr_val['type'], array('arr_subsetting'=>$panel,'blockname'=>$key));
             	else if(isset($arr_val['type']) && isset($sub_tab) && isset($key) && isset($subdatas[$key]) )
					echo $this->element('box_type/'.$arr_val['type'], array('arr_subsetting'=>$arr_settings['relationship'][$sub_tab]['block'],'blockname'=>$key));

             ?>
         </div>


         <!--<span class="hit"></span>-->

         <!--Footer-->
         <span class="title_block bo_ra2">
            <?php if(isset($arr_val['footlink'])){?>
                <span class="icon_vwie jt_footer_link">
                    <a href="<?php if(isset($arr_val['footlink']['link'])) echo $arr_val['footlink']['link']; else if(isset($arr_val['link']['cls'])) echo URL.'/'.$arr_val['link']['cls'].'/lists/';?>">
                        <?php if(isset($arr_val['footlink']['label'])) echo $arr_val['footlink']['label'];?>
                    </a>
                </span>
            <?php }?>
            <?php if(isset($arr_val['foottext'])){?>
                <span class="jt_footer_link" style=" <?php if(isset($arr_val['foottext']['css'])) echo $arr_val['foottext']['css'];?>">
                	<?php if(isset($arr_val['foottext']['label'])) echo $arr_val['foottext']['label'];?>
                </span>
            <?php }?>
             <?php if(isset($arr_val['total'])){?>
                <span class="bt_block float_right no_bg" style=" <?php if(isset($arr_val['total_css'])) echo $arr_val['total_css'];?>">
                    <?php if($arr_val['total']!='') echo $arr_val['total'];else echo 'Total';?>
                    <input class="input_w2" id="total_<?php echo $key;?>" type="text" value="<?php if(isset($total) && !isset($arr_val['total_noprice'])) echo $this->Common->format_currency($total); else echo $total;?>" style="color:#000; font-weight:bold;text-align:right;" readonly="readonly" />
                </span>
            <?php }?>
            <?php if(isset($arr_val['custom_box_bottom']))
            		echo $this->element('../'.$name.'/'.$key.'_custom_box_bottom');
            ?>
        </span>
    </div>
</div>
