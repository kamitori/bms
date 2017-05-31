<?php echo $this->element('../' . $name . '/tab_option'); ?>
<?php echo $this->element('js/lists_view'); ?>
<div id="content">

    <form method="POST" id="sort_form">
        <input type="hidden" name="offset" id="offset" value="<?php echo LIST_LIMIT ?>" />
        <input type="hidden" name="sort_type" id="sort_type" value="desc" />
        <input type="hidden" name="sort_key" id="sort_key" value="_id" />
    </form>
    <div class="w_ul2 ul_res2">

        <ul class="ul_mag clear bg top_header_inner2 ul_res2" id="sort">
            <li class="hg_padd" style="width:.5%"></li>
            <?php foreach ($list_field as $ks => $vls) { ?>
                <li class="hg_padd" style="cursor:pointer;<?php if (isset($vls['css'])) echo $vls['css']; ?>" title="Sort">
                    <label><?php echo $arr_set['name'][$ks]; ?></label>
                    <span id="<?php echo $ks ?>" class="desc"></span>
            	</li>
             <?php } ?>
             <li class="hg_padd bor_mt" style="width:.5%"></li>
        </ul>


        <div id="lists_view_content"><br>
            <!-- goi lists ajax -->
            <?php echo $this->element('../Communications/lists_ajax'); ?>
        </div>

    </div>
</div>

<?php echo $this->element('js_list'); ?>
<?php echo $this->element('../'.$name.'/js'); ?>
