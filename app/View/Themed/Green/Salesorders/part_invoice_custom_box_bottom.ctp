<span class="title_block bo_ra2">
    <span class="bt_block float_right no_bg">
        <div class="dent_input float_right">
            <input type="radio" name="type_sales" onchange="handleChange();" value="invoice" id="create_invoice" ><label for="create_invoice">Create invoice</label>
            <input type="radio" name="type_sales" onchange="handleChange();" value="shipping" id="create_shipment"><label for="create_shipment">Create shipment</label>
            <input type="radio" name="type_sales" onchange="handleChange();" value="both" checked="1" id="create_both"><label for="create_both">Create both</label>
        </form>

        <input style="margin-top: -4.5%;" type="button" class="btn_pur" id="bt_cancel" onclick="window.location.replace('<?php echo URL; ?>/salesorders/entry')" name="cancel" value="Cancel" style="width: 100px; cursor: pointer">
        <input style="margin-top: -4.5%;" type="button" class="btn_pur" onclick="fr_submit();" id="bt_continue" name="continue" value="Continue" style="width: 100px; cursor: pointer">

        </div>
    </span>
</span>

<script type="text/javascript">

    $(function(){
        $("form#form_shipped input").change(function(){
            var id=$(this).attr("id");
            var name=$(this).attr("name");
            var value= $(this).val();
            var location = id.split("shipped_");
            var org="#quantity_"+location[1];
            var bl_i="#balance_invoiced_"+location[1];
            var bl_s="#balance_shipped_"+location[1];
            var vl_org=$(org).val();
            var vl_bl_i=$(bl_i).val();
            alert(vl_bl_i);
            var vl_bl_s=$(bl_s).val();
            var v_org = parseInt(value);
            var v_vl_org=parseInt(vl_org);

            if(v_org>v_vl_org){
                j_bool.push(parseInt(location[1]));
                $(this).css({"border" : "1px solid red"});
                $(this).focus();
            }
            else if(v_org>vl_bl_i && $("input:radio[name='type_sales']:checked").val()=='invoice'){
                j_bool.push(parseInt(location[1]));
                $(this).css({"border" : "1px solid red"});
                $(this).focus();
            }
            else if(v_org>vl_bl_s && $("input:radio[name='type_sales']:checked").val()=='shipping'){
                j_bool.push(parseInt(location[1]));
                $(this).css({"border" : "1px solid red"});
                $(this).focus();
            }
            else if($("input:radio[name='type_sales']:checked").val()=='both'){
                if(v_org>vl_bl_s || v_org>vl_bl_i){
                    j_bool.push(parseInt(location[1]));
                    $(this).css({"border" : "1px solid red"});
                    $(this).focus();
                }
                else{
                    for(var i = j_bool.length - 1; i >= 0; i--) {
                        if(j_bool[i] == location[1]) {
                           j_bool.splice(i, 1);
                        }
                    }
                    $(this).css({"border" : "0px"});
                    $(this).focus();
                }
            }
            else{
                   for(var i = j_bool.length - 1; i >= 0; i--) {
                       if(j_bool[i] == location[1]) {
                          j_bool.splice(i, 1);
                       }
                   }
                $(this).css({"border" : "0px"});
                $(this).focus();
            }
        });
    })
    var j_bool=new Array();
    function fr_submit() {
        if(j_bool.length==0){
            $.ajax({
                url: "<?php echo URL; ?>/salesorders/receive_item/",
                type: 'POST',
                data: $('#form_shipped').serialize(),
                success: function(result) {
                    if(result!='')
                        window.location.assign("<?php echo URL; ?>"+result+"");
                }
            });
            return false;
        }
        else{
            alert("Please enter valid quantities");
        }
    }

    function handleChange(){
        $.ajax({
            url: "<?php echo URL; ?>/salesorders/check_full_balance/",
            type: 'POST',
            data: $('#form_shipped').serialize(),
            success: function(result) {
                ret = result.split(",");
                for(var i=0 ; i<ret.length-1;i++){
                    res=ret[i].split("_");
                    if(res[1]!=undefined){
                        $("#shipped_"+i).css({"border" : "1px solid red"});

                        j_bool.push(i);
                    }
                    else{
                         for(var j = j_bool.length - 1; j >= 0; j--) {
                             if(j_bool[j] == i) {
                                j_bool.splice(j, 1);
                             }
                         }
                        $("#shipped_"+i).css({"border" : "0px"});
                    }
                }
            }
        });
    }
</script>