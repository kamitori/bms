<?php echo $this->element('../'.$name.'/tab_option'); $m = 0;?>
<div id="content">
	<form method="POST" id="sort_form">
    	<div class="w_ul2 ul_res2">
            <!--Phần tiêu đề-->
            <ul class="ul_mag clear bg top_header_inner2 ul_res2" id="sort">
            	<li class="hg_padd" style="width:.5%"></li>
            	 <?php foreach ($list_field as $ks => $vls){?>
                 	<li class="hg_padd" style="cursor:pointer;<?php if(isset($vls['css'])) echo $vls['css']; ?>" title="Sort">
                		<label style="cursor:pointer;"> <?php if(isset($arr_set['name'][$ks])) echo $arr_set['name'][$ks]; ?></label>
                        <?php //if(!isset($vls['sort']) || (isset($vls['sort']) && $vls['sort']!=0)){?>
                        <?php if(isset($vls['sort'])){?>
                        <span id="<?php echo $ks ?>" class="desc<?php //if(isset($sort_type) && isset($sort_key) && $sort_key==$ks && $sort_type =='desc') echo 'asc'; else echo 'desc';?>" <?php //style="display:if(isset($sort_key) && $sort_key==$ks) echo 'block'; else echo 'none';;"?>></span>
                        <?php }?>
                	</li>
                 <?php } ?>
                 <li class="hg_padd bor_mt" style="width:.5%"></li>
            </ul>


            <!-- Phần thay data : load n=LIST_LIMIT cái đầu tiên, sau đó chạy ajax qv_ajax() để load tiếp n=LIST_LIMIT cái lần 2-->
            <div id="lists_view_content">
                <?php echo $this->element('lists_ajax'); ?>
            </div>
        </div>
    </form>
</div>
<?php echo $this->element('js/lists_view'); ?>
<?php echo $this->element('js_list');?>