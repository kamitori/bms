<style type="text/css">
    #tag_content .active{
        background-color: #852020;
        color: #fff;
        font-weight: bold;
    }
    #tag_content ul.tag_list:hover{
        cursor: pointer;
        background-color: #852020;
        color: #fff;
        font-weight: bold;
    }
    #product_tag_content ul.hidden{
        display: none;
    }
</style>
<div class="percent_content block_dent_a float_left" style="width:100%;height: 300px;margin-top: 0%;">
   <div id="detail_for_main_menu">
      <div class="clear_percent">

         <div class="clear_percent_19 float_left" style="margin-left: -1.5%; width:20%;">
            <div class="tab_1 full_width">
               <span class="title_block bo_ra1">
                  <span class="fl_dent">
                     <h4><?php echo translate('Asset Tags'); ?></h4>
                  </span>
               </span>
               <div id="quotation_tag" class="container_same_category" style="height: 200px;overflow-y: auto">
                    <ul class="ul_mag clear bg3">
                      <li class="hg_padd center_text" style="width:60%;cursor: pointer;text-align: left; font-style:padding-left:5%;"><?php echo translate('Tag'); ?></li>
                      <li class="hg_padd" style="width:30%;text-align: right;cursor: pointer"><?php echo translate('Total Factor'); ?></li>
                    </ul>
                    <div id="tag_content">
                        <?php
                            $i = 0;
                            if(!empty($group)):
                                foreach($group as $key=>$value):
                                    $bg = ($i%2==0 ? 'bg2':'bg1');
                         ?>
                        <ul class="tag_list ul_mag clear <?php echo $bg; if($i==0) echo ' active';?>">
                            <li id="tag_key" class="hg_padd center_text" style="width:60%;text-align: left; padding-left:5%;" ><?php echo $key ?></li>
                            <li class="hg_padd" style="width:30%;text-align: right;"><?php echo $this->Common->format_currency($value['total_factor']); ?></li>
                        </ul>
                        <?php
                                    $i++;
                                endforeach;
                            endif;
                            if($i<9)
                            {
                                for($j = $i; $j < 8; $j++)
                                {
                                    $bg = ($j%2==0 ? 'bg2':'bg1');
                                    echo '<ul class="ul_mag clear '.$bg.'"></ul>';
                                }
                            }
                        ?>
                    </div>
               </div>
               <span class="title_block bo_ra2"></span>
            </div>
            <!--END Tab1 -->
         </div>


         <div class="clear_percent_9_arrow float_left" style="margin-right:0;">
            <div class="full_width box_arrow">
               <span class="icon_emp"></span>
            </div>
         </div>


         <div class="clear_percent_11 float_left" id="list_and_menu_detail" style="width:80%;">
            <div class="tab_1 full_width">
               <span class="title_block bo_ra1">
                  <span class="fl_dent">
                     <h4 id="setting_name">
                        <?php echo translate('Products'); ?>
                    </h4>
                  </span>
               </span>
               <ul class="ul_mag clear bg3">
                  <li class="hg_padd center_text" style="width:5%"><?php echo translate('Code'); ?></li>
                  <li class="hg_padd" style="width:25%"><?php echo translate('Name'); ?></li>
                  <li class="hg_padd" style="width:8%"><?php echo translate('Type'); ?></li>
                  <li class="hg_padd" style="width:7%;text-align: right"><?php echo translate('Factor'); ?></li>
                  <li class="hg_padd" style="width:10%;text-align: right"><?php echo translate('Min minute/UOM'); ?></li>
                  <li class="hg_padd" style="width:5%;text-align: right"><?php echo translate('Width'); ?></li>
                  <li class="hg_padd" style="width:5%;text-align: right"><?php echo translate('Height'); ?></li>
                  <li class="hg_padd" style="width:5%"><?php echo translate('Sold by'); ?></li>
                  <li class="hg_padd" style="width:5%;text-align: right"><?php echo translate('Quantity'); ?></li>
                  <li class="hg_padd" style="width:10%;text-align: right"><?php echo translate('Production time'); ?></li>
               </ul>
               <div class="container_same_category" style="overflow-y: auto" id="product_tag_content">
                    <?php
                        if(!empty($group)):
                            foreach($group as $key=>$value):
                                $i=0;
                                if(is_array($value)):
                                    foreach($value['product'] as $k=>$v):
                                        $bg = ($i%2==0?'bg2':'bg1');
                    ?>
                    <ul class="ul_mag clear <?php echo $bg; ?> hidden" rel="<?php echo $key; ?>">
                      <li class="hg_padd center_text" style="width:5%"><?php echo $k; ?></li>
                      <li class="hg_padd" style="width:25%"><?php echo $v['products_name']; ?></li>

                      <li class="hg_padd" style="width:8%"><?php echo $v['product_type']; ?></li>
                      <li class="hg_padd" style="width:7%;text-align: right"><?php echo $this->Common->format_currency($v['factor']); ?></li>
                      <li class="hg_padd" style="width:10%;text-align: right"><?php echo $this->Common->format_currency($v['min_of_uom']); ?></li>
                      <li class="hg_padd" style="width:5%;text-align: right"><?php echo $v['sizew']; ?> <?php echo $v['sizew_unit']; ?></li>
                      <li class="hg_padd" style="width:5%;text-align: right"><?php echo $v['sizeh']; ?> <?php echo $v['sizeh_unit']; ?></li>
                      <li class="hg_padd" style="width:5%"><?php echo $v['oum']; // BaoNam fix ?></li>
                      <li class="hg_padd" style="width:5%;text-align: right"><?php echo $v['quantity']; ?></li>
                      <li class="hg_padd" style="width:10%;text-align: right"><?php echo $this->Common->format_currency($v['production_time']); ?></li>

                    </ul>
                    <?php
                                        $i++;
                                    endforeach;
                                endif;
                            endforeach;
                        endif;
                    ?>
               </div>
               <span class="title_block bo_ra2">
               </span>
            </div>
         </div>
      </div>
   </div>
   <p class="clear"></p>
</div>
<script type="text/javascript">
    $(function(){
        if($('#product_tag_content').html().trim()=='')
            fillContent();
        var active_ul = '';
        $(".tag_list").each(function(){
            if($(this).hasClass('active'))
                active_ul = $(this);
        });
        displayContent($('li#tag_key', active_ul).html());
        $(".tag_list").click(function(){
            $(".tag_list").each(function(){
                $(this).removeClass('active');
            });
            $(this).addClass('active');
            displayContent($("li#tag_key",this).html());
        });
    })
    function displayContent(tag_key)
    {
        if(tag_key!=''&&tag_key!=undefined)
        {
            var i = 0;
            $('#product_tag_content ul').each(function(){
                $(this).addClass('hidden');
                if($(this).attr('rel')==tag_key)
                {
                    $(this).removeClass('hidden');
                    i++;
                }
            });
            if(i<9)
                fillContent(i);
        }
    }
    function fillContent(start,end)
    {
        $("ul#tmp_ul").each(function(){
            $(this).remove();
        });
        if(start==undefined)
            start = 0;
        if(end==undefined)
            end = 8;
        var html = '';
        for(var j = start; j < end; j++)
        {
            var bg = 'bg1';
            if(j%2==0)
                bg = 'bg2';
            html += '<ul id="tmp_ul" class="ul_mag clear '+bg+'"></ul>';
        }
        $('#product_tag_content').append(html);
    }
</script>