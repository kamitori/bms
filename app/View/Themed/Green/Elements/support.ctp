<?php echo $this->element('../'.$name.'/tab_option'); ?>
<div class="percent content_indent">
    <div class="float_left bg_nav_setup fix_block">
        <ul class="nav_setup support_nav">
            <li><a class="other_dif" href="#" >Help & FAQ</a></li>
        <?php
            $arr_content = array();
            if($supports->count()){
                foreach($supports as $support){
                    $arr_content[] = array('_id'=>$support['_id'],'content'=>$support['content']);
        ?>
            <li><a href="javascript:void(0)" class="faq" id="<?php echo $support['_id'] ?>"><?php echo $support['name']; ?></a></li>
        <?php
                }
            }
        ?>
            <li><a class="active other_dif" href="#">Contact & Support </a></li>
            <li><a href="#">Contact Us</a></li>
        </ul>
    </div>
    <div class="content_store hidden">
    <?php
        foreach($arr_content as $value){
    ?>
        <div id="content_<?php echo $value['_id'] ?>"><?php echo $value['content']; ?></div>
    <?php
        }
    ?>
    </div>
    <div class="percent_content float_left container_same_category1 h_content">
        <div class="paragr">

        </div>
        <p class="clear"></p>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        $(".faq").click(function(){
            $(".faq").removeClass("active");
            $(this).addClass("active");
            var id = $(this).attr("id");
            $(".paragr").html($("#content_"+id).html());
        });
        $(".faq:first").click().addClass("active");
    })
</script>