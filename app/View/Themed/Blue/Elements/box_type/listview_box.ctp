<style type="text/css">
.animated {
	-webkit-transition: height 0.2s;
	-moz-transition: height 0.2s;
	transition: height 0.2s;
}</style>
<?php if($blockname=='products'){ ?>
<input type="hidden" id="product_choice_sku" value="" />
<span id="click_open_window_companiessearch_company_name" style=" display:none;"></span>
<!-- <span id="click_open_window_companiessearch_prefer_customer" style=" display:none;"></span> -->
<?php if( !isset($no_js) ):  ?>
<script type="text/javascript">
	$(function(){
		<?php if($controller == 'purchaseorders'){ ?>
		window_popup('products', 'Specify Products','change', 'product_choice_sku', "?ispo=1&products_company_id=" + $("#company_id").val() + "&products_company_name=" + $("#company_name").val() + "&products_product_type=Vendor Stock");
		<?php } else { ?>
		var para = "?products_product_type=Product";
		var force_re_install = "";
		<?php if(isset($default_prefer_customer)){ ?>
			para += "&prefer_customer_id=" + $("#company_id").val();
			force_re_install = "force_re_install";
		<?php } ?>
		window_popup('products', 'Specify Products','change', 'product_choice_sku', para, force_re_install);
		<?php } ?>
		window_popup('companies', 'Specify Current supplier','search_company_name','click_open_window_companiessearch_company_name','?is_supplier=1','no_auto_close');
		/*window_popup('companies', 'Specify Prefer customer','search_prefer_customer','click_open_window_companiessearch_prefer_customer','','no_auto_close');*/
	})
<?php endif; ?>
</script>
<?php } ?>
<?php
	//FOR TESTING
	//echo $blockname;
	//pr($arr_subsetting[$blockname]);
	//pr($arr_subsetting[$blockname]['field']);
	// pr($blockname);
	// pr($subdatas);
	$rowH = 21;
	if(isset($arr_subsetting[$blockname]['linecss']))
		$rowH = 60;


	$block =  $arr_subsetting[$blockname]['field'];
	if(isset($arr_subsetting[$blockname]['delete']))
		$del = $arr_subsetting[$blockname]['delete'];
	else
		$del = 0;

	if(isset($arr_subsetting[$blockname]['link'])){
		$linkw = $arr_subsetting[$blockname]['link']['w'];
		$linkc = $arr_subsetting[$blockname]['link']['cls'];

	}else{
		$linkw = 0; $linkc = '';
	}

	if(isset($arr_subsetting[$blockname]['link']['field'])){
		$linkf = $arr_subsetting[$blockname]['link']['field'];
	}else
		$linkf = '';

	if(isset($arr_subsetting[$blockname]['cellcss']))
		$cellcss = $arr_subsetting[$blockname]['cellcss'];
	else
		$cellcss = '';

	if(isset($arr_subsetting[$blockname]['reltb'])){
		$exp = explode('@',$arr_subsetting[$blockname]['reltb']);
		$exp = trim($exp[1]);
	}else
		$exp = '';

	if(isset($arr_subsetting[$blockname]['height'])){
		$contH =  $arr_subsetting[$blockname]['height'];
		$contH = (int)$contH;
		$contH = $contH - $rowH;
	}else
		$contH = 0;

	// caculator width
	$n = $width = 0;
	$sum = 93;//%
	foreach ($block as $ks => $vls){
		if(isset($vls['width'])){
			$sum = $sum - (int)$vls['width'];
		}else{
			$n++;
		}
		if($n!=0)
		$width = round(($sum-$n)/$n,3);
	}

	//text entry
	if(isset($arr_subsetting[$blockname]['linecss'])){
		$linecss = $arr_subsetting[$blockname]['linecss'];
	}else
		$linecss = '';

	if(isset($arr_subsetting[$blockname]['cellcss'])){
		$cellcss = $arr_subsetting[$blockname]['cellcss'];
	}else
		$cellcss = '';
	if(isset($arr_subsetting[$blockname]['customid'])){
		$customid = $arr_subsetting[$blockname]['customid'];
	}else
		$customid = '_id';

?>
<ul class="ul_mag clear bg3">
	<?php if($linkw!=0){?><li class="hg_padd" style="width:<?php echo $linkw;?>%;"></li><?php }?>
    <?php $sumww = $ww =0;
		foreach ($block as $ks => $vls){
		if( !isset($vls['type']) || (isset($vls['type']) && $vls['type']!='id' && $vls['type']!='hidden' ) ){
			if(isset($vls['width'])) $ww = (int)$vls['width']; else $ww = $width;
			$sumww += $ww;
	?>
        <li class="hg_padd" style="text-align:<?php if(isset($vls['align'])) echo $vls['align'];else echo 'left'; ?>;width:<?php echo $ww;?>%;<?php echo $cellcss;?><?php if(isset($vls['type']) && $vls['type'] == 'hidden') echo 'display:none;'; ?>" <?php if(isset($vls['title'])) echo 'title="'.$vls['title'].'"'; ?>>
            <?php if(isset($vls['name'])) echo $vls['name']; ?>
        </li>

    <?php } } ?>

    <?php if($del!=0){?><li class="hg_padd bor_mt" style="width:<?php echo $del;?>%;" <?php if(isset($vls['title'])) echo 'title="'.$vls['title'].'"'; ?>></li><?php }?>
</ul>
<div class="clear" id="container_<?php echo $blockname;?>" style=" <?php if(isset($arr_subsetting[$blockname]['full_height'])) echo ''; else{?>overflow-y:hidden;<?php }?><?php if($contH>0) echo 'height:'.$contH.'px;';?>">

	<?php $n=0;
		foreach ($subdatas[$blockname] as $kss => $value) {
			if(!isset($value['_id'])){
				$value['_id'] = $kss;
			}
			if(isset($value['_id']) && ((isset($value['deleted'])&&!$value['deleted']) || !isset($value['deleted'])) ){
			if($n%2>0) $nclass="bg1"; else $nclass="bg2";?>

            <!--DÒNG -->
            <input type="hidden" id="<?php echo $blockname.$value['_id'];?>" value="<?php echo $value['_id'];?>" />
            <ul class="ul_mag clear <?php echo $nclass.' '.$linecss;?> line_box <?php if(isset($value['xcss'])) echo 'xcss'; ?> <?php if(isset($value['xclass'])) echo $value['xclass']; ?> <?php if(isset($value['combo_id'])) echo 'combo'.$value['combo_id']; ?>" id="listbox_<?php echo $blockname.'_'.$value['_id'];?>" rel="<?php echo $n;?>" style="<?php if(isset($value['xcss'])) echo $value['xcss']; ?>" title="<?php if(isset($value['combo_id'])) echo 'Combo #'.($value['combo_id']+1); ?>" >

                <?php if($exp!='' && $linkw!=0){?>
                	<li class="hg_padd <?php echo $cellcss;?>" style="width:<?php echo $linkw;?>%;" title="View detail" <?php if(isset($value['set_link'])) echo $value['set_link']; else {?> onclick=" window.location.assign('<?php echo URL.'/'.$linkc; ?>/entry/<?php if(isset($value[$linkf])) echo $value[$linkf]; else if(isset($value[$exp.'_id'])) echo $value[$exp.'_id']; ?>');" <?php }?>>
                    	<?php if( ( isset($value[$linkf]) && ( strlen($value[$linkf]) == 24 ||is_numeric($value[$linkf]))) || ( isset($value[$exp.'_id']) && is_numeric($value[$exp.'_id'])) ){?>
                        	<span class="icon_emp"></span>
                        <?php }else{?>
                        	<!--<span class="icon_plus_opt" style="margin-top:0px;"></span>-->
                        <?php }?>
                    </li>

				<?php }else if($linkw!=0){?>
                	<li class="hg_padd <?php echo $cellcss;?>" style="width:<?php echo $linkw;?>%;" title="View detail" onclick=" window.location.assign('<?php echo URL.'/'.$linkc; ?>/entry/<?php if(isset($value['_id'])) echo $value['_id']; ?>');">
                    	<?php if(isset($value['_id']) && $value['_id']!=''){?>
                        	<span class="icon_emp"></span>
                        <?php }?>
                    </li>
				<?php }?>

                <!--CỘT-->
                <?php foreach ($block as $ks => $vls){?>
                	<?php if(isset($vls['type']) && ($vls['type'] =='hidden' || $vls['type'] =='id')){
						echo $this->element('view_field/'.$vls['type'],array('arr_vls'=>$value,'viewkeys'=>$ks,'arr_view_st'=>$vls));

                    }else{?>

                        <li class="hg_padd datainput_<?php echo $blockname;?> <?php echo $cellcss;?>" style=" <?php if(isset($vls['type']) && ($vls['type']=='select' || $vls['type']=='select_dynamic')){?>overflow: visible !important;<?php }?> text-align:<?php if(isset($vls['align'])) echo $vls['align'];else echo 'left'; ?>; width:<?php if(isset($vls['width'])) echo $vls['width']; else echo $width;?>%;<?php echo $cellcss;?>">
                            <?php // lựa chọn kiểu hiển thị
                                if(isset($vls['type']))
                                    $link_field = 'view_field/'.$vls['type'];
                                else
                                    $link_field = 'view_field/text';
                                echo $this->element($link_field,array('arr_vls'=>$value,'viewkeys'=>$ks,'arr_view_st'=>$vls));
                            ?>
                       </li>
                   <?php }?>
                <?php }?>



                <?php if($exp!='' && $del!=0){
					$idlink = $kss;
					if($customid!='')
						$idlink = $value[$customid];
				?>
                	<li class="hg_padd bor_mt <?php echo $cellcss;?>" style="width:<?php echo $del;?>%;">
                	    <?php if(!isset($value['remove_deleted'])){?>
                            <div class="jt_right_check">
                                <a title="Delete" rel="<?php echo $idlink; ?>@<?php echo $exp;?>" rev="<?php echo $blockname;?>" id="del_<?php echo $blockname."_".$idlink; ?>" class="deleteopt_link del_<?php echo $blockname; ?>" onclick="ajax_delete('deleteopt','del_<?php echo $blockname."_".$idlink; ?>');" href="javascript:void(0);">
                                    <span class="icon_remove2"></span>
                                </a>
                            </div>
                        <?php }?>
                    </li>

                <?php }else if($del!=0){?>
                    <li class="hg_padd bor_mt <?php echo $cellcss;?>" style="width:<?php echo $del;?>%;">
                        <?php if(!isset($value['remove_deleted'])){?>
                            <div class="jt_right_check">
                                <a title="Delete" class="delete_link" rel="<?php if(isset($value['_id'])) echo $value['_id']; ?>" rev="<?php echo $blockname;?>" onclick="ajax_delete('delete','del_<?php echo $blockname."_".$value['_id']; ?>');" id="del_<?php echo $blockname."_".$value['_id']; ?>" href="javascript:void(0);">
                                    <span class="icon_remove2"></span>
                                </a>
                            </div>
                        <?php }?>
                    </li>
                <?php }?>

            </ul>

     	<?php $n++; } // end if?>
    <?php } // end for?>

	<?php
		$hadH = $rowH*$n;
		$moreH = $contH - $hadH;
		if($moreH>$rowH){
			$more = $moreH/$rowH;
			for($m=2;$m<$more;$m++){
				if(($m+$n)%2>0) $cls = 'bg1'; else $cls = 'bg2';
	?>
    			<ul class="ul_mag clear <?php echo $cls;?> <?php echo $linecss;?>">
                </ul>
    <?php } } ?>
</div>
<?php if(!isset($arr_subsetting[$blockname]['full_height'])){?>
<script>
	// $(window).load(function(){
	// 	$("#container_<?php echo $blockname;?>").mCustomScrollbar({
	// 		scrollButtons:{
	// 			enable:false
	// 		}
	// 	});
	// });

	$(function(){
		$("#container_<?php echo $blockname;?>").mCustomScrollbar({
			scrollButtons:{
				enable:false
			},
			advanced:{
		        updateOnContentResize: true,
		        autoScrollOnFocus: false,
		    }
		});
	});
</script>
<?php }?>
<script type="text/javascript">
	$(function(){
		$("textarea","#container_products").parent().parent().parent().each(function(){
			$("li.hg_padd",this).css("height","98%");
		});
		$('.animated').autosize({append: "\n"});
		$(".animated").each(function(){
			var id = $(this).attr('id');
			id = id.split('_');
			id = id[id.length - 1];
			var value = $("#products_costing_name_"+id).val();
			if(value!=undefined)
				$(this).parent().append($("<span>"+value+"</span>"));
			resizeUl(this);
		});
		$(".animated").keydown(function(event) {
			var value = $(this).val();
			value = value.replace(/[\r\n]{1,}/g,"\r\n");
			$(this).val(value);
		});
		$(".animated").keyup(function(event) {
			resizeUl(this);
		});
		$(".animated").change(function(){
			resizeUl();
		});
	});
	function resizeUl(Object){
		var height = $(Object).height();
		if($(Object).next().height()!=undefined)
			height += $(Object).next().height();
		$(Object).parent().parent().parent().height(height+5);
	}
</script>