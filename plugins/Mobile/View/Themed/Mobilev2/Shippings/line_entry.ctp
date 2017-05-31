<style type="text/css">
    .ui-popup-screen.in {
        position: fixed;
    }
    #list-view{
        background-color: #fff;
        border-left: solid 1px rgba(0,0,0,.15);
    }
    .popup-line{
        background-color: #38c !important;
        color: #fff !important;
    }
    .ui-block-b > span{
        color: #fff;
        font-weight: 900;
        font-size: 14px;
    }
    .parent-line{
        margin-top: 5px !important;
        border-left: none !important;
        border-top: solid 2px rgba(0,0,0,.15) !important;
    }
</style>
<ul id="list-view" data-role="listview" data-inset="true">
<?php
    if(!empty($arr_ret)){
        foreach ($arr_ret['products'] as $key => $value){
?>
    <li id="list-<?php echo $value['_id'] ?>" <?php if(isset($value['option_for']) && $value['_id']!='Extra_Row') echo 'style="width: 89%; margin-left: 11%;" data-theme="b" data-option-for="'.$value['option_for'].'"'; ?> class="<?php if(!isset($value['option_for']) || $value['_id']=='Extra_Row') echo 'parent-line'; ?> list-line-item ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:30%">
                <a class="open-popup" data-id="<?php echo $value['_id'] ?>" <?php if(!isset($value['option_for'])){ ?> onclick="open_product_popup(this);"<?php } ?> href="javascript:void(0)"><?php echo (isset($value['sku']) ? $value['sku'] : '').'&nbsp;&nbsp;&nbsp;'; ?></a>
            </div>
            <div class="ui-block-b" style="width:60%"><?php echo $value['products_name'];?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
            <?php if(!isset($value['xempty']['sku'])){ ?>
            <li>
                <div class="ui-block-a" style="width: 30%"><b>SKU</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo (isset($value['sku']) ? $value['sku'] : ''); ?>
                </div>
            </li>
            <?php } ?>
            <?php if(!isset($value['xempty']['products_name'])){ ?>
            <li>
                <div class="ui-block-a" style="width: 30%;"><b>Name / details</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="products_name" id="products_name_<?php echo $value['_id'] ?>" <?php if(isset($value['xlock']['products_name'])) echo 'readonly="readonly"'; ?> data-theme="a" value="<?php echo isset($value['products_name']) ? $value['products_name'] : ''; ?>" />
                </div>
            </li>
            <?php } ?>
            <?php if(!isset($value['option_for']) || (!isset($value['same_parent']) || !$value['same_parent']) ){ ?>
            <?php if(!isset($value['xempty']['option'])) {?>
            <li>
                 <div class="ui-block-a" style="width: 30%;"><b></b></div>
                 <div class="ui-block-b" style="width:70%">
                    <a href="<?php echo M_URL.'/'.$controller.'/option_list/'.$value['_id']; ?>" rel="external" id="options_<?php echo $value['_id'] ?>" class="popup-line" data-role="button" data-theme="a" data-mini="true">Option</a>
                </div>
            </li>
            <?php } ?>
            <?php if(!isset($value['xempty']['view_costing'])) {?>
            <li>
                 <div class="ui-block-a" style="width: 30%;"><b></b></div>
                 <div class="ui-block-b" style="width:70%">
                    <a href="<?php echo M_URL.'/'.$controller.'/costing_list/'.$value['_id']; ?>" rel="external" id="costings_<?php echo $value['_id'] ?>"  class="popup-line" data-role="button"  data-theme="a" data-mini="true">Costing</a>
                </div>
            </li>
            <?php } ?>
            <?php if(!isset($value['xempty']['sizew'])){ ?>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Size-W</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="sizew" class="sizew" id="sizew_<?php echo $value['_id'] ?>" data-theme="a" value="<?php echo isset($value['sizew']) ? $value['sizew'] : ''; ?>" />
                </div>
            </li>
            <?php } ?>
            <?php if(!isset($value['xempty']['sizew_unit'])){ ?>
            <li>
                <div class="ui-block-a" style="width:30%"><b></b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $this->Form->input('sizew_unit',array(
                            'name'      => 'sizew_unit',
                            'id'        => 'sizew_unit_'.$value['_id'],
                            'empty'     => '',
                            'options'   => $options['product_oum_size'],
                            'value'     => (isset($value['sizew_unit']) ? $value['sizew_unit'] : ''),
                            'data-theme'=>'a'
                        ));
                    ?>
                </div>
            </li>
            <?php } ?>
            <?php if(!isset($value['xempty']['sizeh'])){ ?>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Size-H</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="sizeh" class="sizeh" id="sizeh_<?php echo $value['_id'] ?>" data-theme="a" value="<?php echo isset($value['sizeh']) ? $value['sizeh'] : ''; ?>" />
                </div>
            </li>
            <?php } ?>
            <?php if(!isset($value['xempty']['sizeh_unit'])){ ?>
            <li>
                <div class="ui-block-a" style="width:30%"><b></b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $this->Form->input('sizeh_unit',array(
                            'name'  => 'sizeh_unit',
                            'id'    => 'sizeh_unit_'.$value['_id'],
                            'empty' => '',
                            'options' => $options['product_oum_size'],
                            'value'   => (isset($value['sizeh_unit']) ? $value['sizeh_unit'] : ''),
                            'data-theme'=>'a'
                        ));
                    ?>
                </div>
            </li>
            <?php } ?>
            <?php if(!isset($value['xempty']['sell_by'])){ ?>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Sold by</b></div>
                <div class="ui-block-b" style="width:70%">
                <?php if(!isset($value['xlock']['sell_by'])) {
                        echo $this->Form->input('sell_by',array(
                            'name'  => 'sell_by',
                            'id'    => 'sell_by_'.$value['_id'],
                            'empty' => '',
                            'options' => $options['product_sell_by'],
                            'value'   => (isset($value['sell_by']) ? $value['sell_by'] : ''),
                            'data-theme'=>'a'
                        ));
                } else { ?>
                    <span id="sell_by_<?php echo $value['_id']; ?>"><?php echo $value['sell_by']; ?></span>
                <?php } ?>
                </div>
            </li>
            <?php } ?>
            <?php if(!isset($value['xempty']['oum'])){ ?>
            <li>
                <div class="ui-block-a" style="width:30%"><b>OUM</b></div>
                <div class="ui-block-b" style="width:70%">
                    <span id="oum_<?php echo $value['_id'] ?>"><?php echo $value['oum']; ?></span>
                </div>
            </li>
            <?php } ?>
            <?php if(!isset($value['xempty']['custom_unit_price'])){ ?>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Est. Unit price</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="custom_unit_price" id="custom_unit_price_<?php echo $value['_id'] ?>"  <?php if(isset($value['xlock']['unit_price'])) echo 'readonly="readonly"'; ?>data-theme="a" value="<?php echo isset($value['custom_unit_price']) ? $value['custom_unit_price'] : ''; ?>" />
                </div>
            </li>
            <?php } ?>
            <?php if(!isset($value['xempty']['quantity'])){ ?>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Quantity</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php if(isset($value['xlock']['quantity'])) { ?>
                    <span id="quantity_<?php echo $value['_id'] ?>"><?php echo $value['quantity']; ?></span>
                    <?php } else { ?>
                    <input type="text" name="quantity" class='qtty_line' id="quantity_<?php echo $value['_id'] ?>" data-theme="a" value="<?php echo isset($value['quantity']) ? $value['quantity'] : ''; ?>" />
                    <?php } ?>
                </div>
            </li>
            <?php } ?>
            <?php if(!isset($value['xempty']['unit_price'])){ ?>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Unit price</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="unit_price" class="unit_price" id="unit_price_<?php echo $value['_id'] ?>" data-theme="a" value="<?php echo isset($value['unit_price']) ? $value['unit_price'] : ''; ?>" />
                </div>
            </li>
            <?php } ?>
            <?php if(!isset($value['xempty']['adj_qty'])){ ?>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Adj Qty</b></div>
                <div class="ui-block-b" style="width:70%">
                    <span id="adj_qty_<?php echo $value['_id']; ?>"><?php echo $value['adj_qty']; ?></span>
                </div>
            </li>
            <?php } ?>
            <?php if(!isset($value['xempty']['sub_total'])){ ?>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Sub total</b></div>
                <div class="ui-block-b" style="width:70%">
                    <span id="sub_total_<?php echo $value['_id']; ?>"><?php echo $value['sub_total']; ?></span>
                </div>
            </li>
            <?php } ?>
            <?php if(!isset($value['xempty']['tax'])){ ?>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Tax </b></div>
                <div class="ui-block-b" style="width:70%">
                    <span id="tax_<?php echo $value['_id']; ?>"><?php echo $value['tax']; ?></span>
                </div>
            </li>
            <?php } ?>
            <?php if(!isset($value['xempty']['amount'])){ ?>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Amount </b></div>
                <div class="ui-block-b" style="width:70%">
                    <span id="amount_<?php echo $value['_id']; ?>"><?php echo $value['amount']; ?></span>
                </div>
            </li>
            <?php } ?>
            <?php if(!isset($value['remove_deleted'])){ ?>
            <li>
                <a href="#popupDialog" class="callDelete" data-id="<?php echo $value['_id']; ?>" data-rel="popup" data-position-to="window" data-transition="pop" class="ui-btn ui-shadow ui-btn-inline ui-icon-delete ui-btn-icon-left ui-btn-b">Delete</a>
            </li>
            <?php } ?>
            <?php } ?>
        </ul>
    </li>
<?php } ?>
</ul>
<br />
<?php
    } else {
?>
<div class="ui-block-a" id="no-data" style="width:100%; text-align: center;"><b>No Data</b></div>
<?php
    }
?>
<div data-role="popup" id="popupDialog" data-overlay-theme="b" data-theme="b" data-dismissible="false" style="max-width:300px;">
    <div role="main" class="ui-content">
        <h2 class="ui-title">Are you sure you want to delete this record?</h2>
    <p>This action cannot be undone.</p>
        <input type="hidden" id="hiddenId" value="" />
        <a href="#" id="cancel_button" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b" data-rel="back">Cancel</a>
        <a href="#" id="delete_button" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b" data-rel="back" data-transition="flow">Delete</a>
    </div>
</div>

<?php echo $this->element('js_line'); ?>
<script type="text/javascript">
$(function(){
    $("#list-view").on("click",".callDelete",function(){
        var value = $(this).attr("data-id");
        $("#hiddenId").val(value);
    });
    $("#delete_button").click(function(){
        var ids = $("#hiddenId").val();
        var data = {};
        data['deleted'] = true;
        saveOption({opname: "products", data: data, key : ids, controller:'<?php echo $controller ?>', callBack: function(result){
                $("#list-"+ids).remove();
                $(".list-line-item[data-option-for='"+ids+"']").remove();
                $("#sum-amount").text(number_format(result.sum_amount,2));
                $("#sum-tax").text(number_format(result.sum_tax,3));
                $("#sum-sub-total").text(number_format(result.sum_sub_total,2));
            }
        });
    });

    jQuery.fn.ForceNumericOnly =
    function()
    {
        return this.each(function()
        {
            $(this).keydown(function(e)
            {
                var key = e.charCode || e.keyCode || 0;
                // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
                // home, end, period, and numpad decimal
                return (
                    key == 8 || 
                    key == 9 ||
                    key == 13 ||
                    key == 46 ||
                    key == 110 ||
                    key == 190 ||
                    (key >= 35 && key <= 40) ||
                    (key >= 48 && key <= 57) ||
                    (key >= 96 && key <= 105));
            });
        });
    };
    $(".qtty_line,.unit_price,.sizew,.sizeh").ForceNumericOnly();

    var attrObj = {"href":"javascript:void(0)","onclick":"return false;"};
    attrObj["title"] = "Sum Amount";
    /*$("#list-record").text('<?php echo number_format($arr_ret['sum_amount'],2); ?>').attr(attrObj).css("text-align","right").attr("id","sum-amount");
    attrObj["title"] = "Sum Tax";
    $("#entry-record").text('<?php echo number_format($arr_ret['sum_tax'],3); ?>').attr(attrObj).css("text-align","right").attr("id","sum-tax");
    attrObj["title"] = "Sum Sub Total";
    $("#find-record").text('<?php echo number_format($arr_ret['sum_sub_total'],2); ?>').attr(attrObj).css("text-align","right").attr("id","sum-sub-total");
    $("#delete-record").text("Entry").attr("href","<?php echo M_URL.'/'.$controller.'/entry/'; ?>");*/
})
</script>