<?php // ?>
<!--<form class="float_left">
    <input class="top_m float_left indent_txt_right" type="button" value="Print all RFQ's " style="width:auto; display:block; cursor:pointer;">
    <a href="" title="Create">
        <span class="flag_cana fix_fl"></span>
    </a>
</form>-->
<span class="float_right">
    <span class="title_small2 float_left">Sort by</span>
    <div class="float_right">
        <span class="block_sear bl_t no_size">
            <span class="bg_search_1"></span>
            <span class="bg_search_2"></span>
            <div class="box_main_filter">
                <div class="menu_1">
                    <input type="radio" name="sortby" id="" value="supplier" class="radio_left" checked/>
                   	<label for="sortby_supplier" style="cursor:pointer"><span class="inactive">Supplier</span></label>
                    
                </div>
                <div class="menu_1 no_right">
                    <input type="radio" name="sortby" id="" value="quote_line" class="radio_left" />
                    <label for="sortby_quote_line" style="cursor:pointer"><span class="inactive">Quote line</span></label>
                </div>
                <p class="clear"></p>
            </div>
        </span>
    </div>
</span>

<script type="text/javascript">
    $(function(){
        $('.radio_left').change(function(event) {
            var sort_val = $(this).val();
            $.ajax({
                url: '<?php echo URL ?>/quotations/rfqs',
                method: 'post',
                timeout: 5000,
                data: {sort: sort_val},
                success: function(html){
                    console.log(html);
                }
            })
        });
    })
</script>
