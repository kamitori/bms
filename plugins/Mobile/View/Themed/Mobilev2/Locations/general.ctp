<style type="text/css">
    .ui-popup-screen.in {
        position: fixed;
    }
</style>
<ul id="list-view" data-role="listview" data-inset="true"><?php
    if(!empty($arr_product)){
        foreach ($arr_product as $value):
?>
    <li id="list-<?php echo $value['_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:30%">
                <a class="link-to-entry" href="<?php echo URL; ?>/mobile/products/entry/<?php echo $value['_id']; ?>"><?php echo $value['code']; ?></a>
            </div>
            <div class="ui-block-b" style="width:60%"><?php echo isset($value['status'])?$value['name'] : '';?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
        	<li>
        		<div class="ui-block-a" style="width: 30%"><b>Code</b></div>
        		<div class="ui-block-b" style="width:70%">
        			<?php echo $value['code']; ?>
        		</div>
        	</li>
        	<li>
        		<div class="ui-block-a" style="width: 30%;"><b>SKU</b></div>
        		<div class="ui-block-b" style="width:70%">
        			<input readonly type="text" name="sku_<?php echo $value['_id'] ?>" id="sku_<?php echo $value['_id'] ?>" data-theme="a" value="<?php echo isset($value['sku']) ? $value['sku'] : ''; ?>"  />
        		</div>
        	</li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Category </b></div>
                <div class="ui-block-b" style="width:70%">
                	<input readonly type="text" name="category_<?php echo $value['_id'] ?>" id="category_<?php echo $value['_id'] ?>" data-theme="a" value="<?php echo isset($value['category']) ? $value['category'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Cost price </b></div>
                <div class="ui-block-b" style="width:70%">
                    <input readonly type="text" name="sell_price_<?php echo $value['_id'] ?>" id="sell_price_<?php echo $value['_id'] ?>" data-theme="a" value="<?php echo isset($value['sell_price']) ? $value['sell_price'] : ''; ?>" />
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>OUM</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input readonly type="text" name="oum_<?php echo $value['_id'] ?>" id="oum_<?php echo $value['_id'] ?>" data-theme="a" value="<?php echo isset($value['oum']) ? $value['oum'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Unit price </b></div>
                <div class="ui-block-b" style="width:70%">
                    <input readonly type="text" name="unit_price_<?php echo $value['_id'] ?>" id="unit_price_<?php echo $value['_id'] ?>" data-theme="a" value="<?php echo isset($value['unit_price']) ? $value['unit_price'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>OUM depend</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input readonly type="text" name="oum_depend_<?php echo $value['_id'] ?>" id="oum_depend_<?php echo $value['_id'] ?>" data-theme="a" value="<?php echo isset($value['oum_depend']) ? $value['oum_depend'] : ''; ?>" />
                </div>
            </li>

        </ul>
    </li>
<?php endforeach; ?>
</ul>
<br />
<?php
    } else {
?>
<div class="ui-block-a" id="no-data" style="width:100%; text-align: center;"><b>No Data</b></div>
<?php
    }
?>
<?php echo $this->element('js_subtab'); ?>


<script type="text/javascript">
$(function(){

    $(".ui-li-static").delegate("input,select","change",function(){
        var names = $(this).attr("name");
        var inval = $(this).val();
        var ids = names.split("_");
        ids = ids[ids.length - 1];
        names = names.replace("_"+ids,"");
        names = names.replace("cb_","");
        if(names=='product_default'){
            if($(this).is(':checked')){
                inval = 1;
            }else{
                inval = 0;
            }
        }
        save_sub_tab(names,inval,ids,'',function(){},"products");
    });

    $(".link-to-entry").click(function(){
        window.location.assign($(this).attr("href"));
    });
})
</script>