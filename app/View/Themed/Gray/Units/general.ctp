<div >
	<!--History / movement for this item //chua dc dinh nghia-->
	<div class="clear_percent_18 float_left" style="width:39%">
        <div class="tab_1 full_width">
            <span class="title_block bo_ra1">
                <span class="float_left h_form">
                    <span class="fl_dent"><h4><?php echo translate('History / movements for this item'); ?></h4></span>
                </span>
            </span>
            <ul class="ul_mag clear bg3">
            	<li class="hg_padd" style="width:1%;"></li>
                <li class="hg_padd" style="width:5%;">Type</li>
                <li class="hg_padd" style="width:9%;">Code</li>
                <li class="hg_padd" style="width:35%;">Products</li>
                <li class="hg_padd" style="width:15%">Date</li>
                <li class="hg_padd" style="width:10%;">Quantity</li>
                <li class="hg_padd" style="width:14%;">Our csr</li>
            </ul>
            <div class="container_same_category">
                <?php $i = 1; $count = 0; ?>
                <?php
                    if(isset($product_in_so)){
                        foreach($product_in_so as $key => $value){?>
                    <ul class="ul_mag clear bg<?php echo $i; ?>" id="salesorders_<?php echo $value['code'] ?>">
                        <li class="hg_padd center_txt" style="width:1%;">
                            <a href="<?php echo URL; ?>/salesorders/entry/<?php echo $value['code']; ?>">
                                <span class="icon_emp"></span>
                            </a>
                        </li>
                        <li class="hg_padd " style="width:5%; text-align: center"><?php if(isset($value['type'])) echo $value['type'];?></li>
                        <li class="hg_padd " style="width:9%;"><?php if(isset($value['code'])) echo $value['code'];?></li>
                        <li class="hg_padd " style="width:35%; "><?php if(isset($value['name'])) echo $value['name'];?></li>
                        <li class="hg_padd " style="width:15%; "><?php if(is_object($value['date_modified'])) echo $this->Common->format_date($value['date_modified']->sec,false);?></li>
                        <li class="hg_padd" style="width:10%; text-align: right "><?php if(isset($value['quantity'])) echo $value['quantity'];?></li>
                        <li class="hg_padd " style="width:14%; "><?php if(isset($value['our_csr'])) echo $value['our_csr'];?></li>
                    </ul>

                    <?php
                    $i = 3 - $i; $count += 1;
                }
                        $count = 8 - $count;
                        if( $count > 0 ){
                            for ($j=0; $j < $count; $j++) { ?>
                            <ul class="ul_mag clear bg<?php echo $i; ?>">
                            </ul>
                      <?php $i = 3 - $i;
                            }
                        }
                }
            ?>
        </div>
        <span class="title_block bo_ra2">
            <span class="float_left bt_block">Click to view full details</span>
            <span class="bt_block float_left no_bg" style="float: left;margin-left:26%">
                <span class="float_left">Total Quantity</span>
                <input class="input_7 right_txt" style="width:59px" value="<?php echo number_format($total_quantity,2); ?>" readonly="readonly" type="text">
            </span>
            <p class="clear"></p>
        </span>
        </div><!--END History -->
    </div>
    <!-- Communication - Dung ham dung chung-->
     <div class="clear_percent_16 float_left" style="width:60%">
        <div class="tab_1 full_width">
        	<?php echo $this->element('communications'); ?>
        </div><!--End Comms-->
    </div>

<div>

<script type="text/javascript">
    $(function(){
        $(".container_same_category").mCustomScrollbar({
            scrollButtons:{
                enable:false
            },
            advanced:{
                updateOnContentResize: true,
                autoScrollOnFocus: false,
            }
        });
    })
</script>
