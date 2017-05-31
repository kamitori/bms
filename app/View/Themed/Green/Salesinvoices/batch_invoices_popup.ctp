<?php echo $this->Form->create($model, array('id' => $controller.'_popup_form' . $key)); ?>

<div style="float: left;">
    <div class="float_left" style="margin-left: 10px">
        <h6 class="float_left" style="margin-top:2px"><?php echo translate('Filter by') . ': ' . translate('Ref no'); ?> &nbsp;</h6>
        <span class="float_left" style="margin-right: 8px;">
            <span class="block_sear  block1">
                <span class="bg_search_1"></span>
                <span class="bg_search_2"></span>
                <div class="box_inner_search float_left">
                    <a href="javascript:void(0)" onclick="$('#<?php echo $controller; ?>_popup_submit_subtton_<?php echo $key; ?>').click();"><span class="icon_search"></span></a>
                    <div class="styled_select2 float_left">
                        <?php
                        echo $this->Form->input($model.'.code', array(
                            'id' => 'window_popup_'.$controller.'_RefNo_' . $key,
                            'onkeypress' => 'pagination_remove_num_'.$controller.$key.'();',
                            'style' => 'background: #636363;color: #fff;margin-left: -2px;margin-top:-2px;'
                        ));
                        ?>
                    </div>
                    <a href="javascript:void(0)" onclick="$('#window_popup_<?php echo $controller; ?>_ContactName_<?php echo $key; ?>').val('');
                            $('#<?php echo $controller; ?>_popup_submit_subtton_<?php echo $key; ?>').click();"><span class="icon_closef" style="margin-left:0"></span></a>

                </div>
            </span>
        </span>
    </div>
    <div class="float_left">
        <h6 class="float_left" style="margin-top:2px"><?php echo translate('Company'); ?> &nbsp;</h6>
        <span class="float_left" style=" margin-right:20px">
            <span class="block_sear  block1" style=" ">
                <span class="bg_search_1"></span>
                <span class="bg_search_2"></span>
                <div class="box_inner_search float_left" id="box_products_company_name">
                    <a href="javascript:void(0)" onclick="$('#products_submit_choice_code_option').click();">
                        <span class="icon_search"></span>
                    </a>
                    <div class="styled_select2 float_left" style=" ;">
                        <?php
                        echo $this->Form->input($model.'.company', array(
                            'id' => 'window_popup_'.$controller.'_Company_' . $key,
                            'value'=> (isset($company_name) ? $company_name : ''),
                            'style' => 'background: #636363;color: #fff;margin-left: -2px;margin-top:-2px;'
                        ));
                        ?>
                        <a href="javascript:void(0)" onclick="$('#window_popup_salesinvoices_Company_<?php echo $key; ?>').val(''); $('#<?php echo $controller; ?>_popup_submit_subtton_<?php echo $key; ?>').click();">
                            <span class="icon_closef" style="margin:-17px 0 0 0;"></span>
                        </a>
                        <a href="javascript:void(0)" onclick="$('#window_popup_<?php echo $controller; ?>_Company_<?php echo $key; ?>').val('');
                            $('#<?php echo $controller; ?>_popup_submit_subtton_<?php echo $key; ?>').click();">
                        </a>
                    </div>
                    <span class="iconw_m indent_dw_m" title="Specify Company" style="margin-top:2px;" id="box_company_new_window"></span>
                </div>
            </span>
        </span>
    </div>

    <div class="float_left">
        <h6 class="float_left" style="margin-top:2px">&nbsp;<?php echo translate('Status'); ?> &nbsp;</h6>
        <span class="float_left">
            <span class="block_sear  block1">
                <span class="bg_search_1"></span>
                <span class="bg_search_2"></span>
                <div class="box_inner_search float_left">
                    <a href="javascript:void(0)" onclick="$('#<?php echo $controller; ?>_popup_submit_subtton_<?php echo $key; ?>').click();"><span class="icon_search"></span></a>
                        <div class="styled_select2 float_left">
                        	<select name="data[Salesinvoice][invoice_status]" style="background: #636363;color: #fff;margin-left: -2px;margin-top:-2px;width: 100px;" onchange="$('#window_popup_<?php echo $controller; ?>_Company_<?php echo $key; ?>').val('');$('#<?php echo $controller; ?>_popup_submit_subtton_<?php echo $key; ?>').click();" >
                        		<option value=""></option>
    	                		<?php
    	                			$status = (isset($invoice_status) ? $invoice_status : '');
    	                			foreach($arr_invoice_status as $op_key=>$value)
    	                				echo '<option value="'.$op_key.'" '.($status==$value ? 'selected ' : '').'>'.$value.'</option>';
    	                		?>
    	                	</select>
                        </div>
                    <a href="javascript:void(0)" onclick="$('#window_popup_<?php echo $controller; ?>_Company_<?php echo $key; ?>').val('');
                            $('#<?php echo $controller; ?>_popup_submit_subtton_<?php echo $key; ?>').click();"><span class="icon_closef" style="margin-left:0"></span></a>

                </div>
            </span>
        </span>
    </div>
    <?php
// Ẩn nút submit này
    echo $this->Js->submit('Search', array(
        'id' => $controller.'_popup_submit_subtton_' . $key,
        'style' => 'height:1px; width:1px;opacity:0.1',
        'success' => '$("#window_popup_'.$controller . $key . '").html(data);'
    ));
    ?>

</div>
 <ul class="menu_control float_right">
    <li >
        <a href="javascript:void(0)" id="submit_batch<?php echo $key ?>">Submit</a>
    </li>
</ul>
<!-- END SEARCH POPUP -->

<div style="clear:both;height:6px"></div>

<div class="block_dent2 container_same_category" style="overflow: auto;overflow-x: hidden;max-width:1000px; margin: 0 auto; height:430px;" id="list_view_salesinvoices">
    <table class="jt_tb" id="batch_salesinvoices<?php echo $key ?>" style="font-size:12px;">
        <thead style="position: fixed;" id="pagination_sort">
            <tr>
            	<th style="width:20px"></th>
                <th style="width:100px"><?php echo translate('Ref no'); ?><span id="sort_code" rel="code" class="desc"></span></th>
                <th style="width:150px"><?php echo translate('Date'); ?><span id="sort_invoice_date" rel="invoice_date" class="desc"></span></th>
                <th style="width:375px"><?php echo translate('Company name'); ?><span id="sort_company_name" rel="company_name" class="desc"></span></th>
                <th style="width: 130px"><?php echo translate('Status'); ?><span id="sort_invoice_status" rel="invoice_status" class="desc"></span></th>
            </tr>
        </thead>
        <tbody>
            <tr>
            	<th style="width:20px"></th>
                <th style="width:100px ">&nspb;</th>
                <th style="width:150px ">&nspb;</th>
                <th style="width:375px"></th>
                <th style="width:130px"></th>
            </tr>
            <?php
            $i = 0; $STT = 0;
            foreach ($arr_salesinvoices as $value) {
                $i = 1 - $i; $STT += 1;
                ?>
                <tr class="jt_line_<?php if ($i == 1) { ?>black<?php } else { ?>light<?php } ?>" id="tr_<?php echo $value['_id']; ?>">
                    <td align="center"><input class="check_salesinvoices" name="data[batch_salesinvoices][<?php echo $value['_id'] ?>]" type="checkbox" id="<?php echo $value['_id'] ?>" /></td>
                    <td align="center" onclick="$('input[type=checkbox]',$(this).parent()).click()"><?php if(isset($value['code']))echo $value['code']; ?>&nbsp;</td>
                    <td align="center" onclick="$('input[type=checkbox]',$(this).parent()).click()"><?php if(isset($value['invoice_date']))echo $this->Common->format_date($value['invoice_date']->sec); ?>&nbsp;</td>
                    <td align="left" onclick="$('input[type=checkbox]',$(this).parent()).click()"><?php if(isset($value['company_name']))echo $value['company_name']; ?>&nbsp;</td>
                    <td align="left" onclick="$('input[type=checkbox]',$(this).parent()).click()"><?php if(isset($value['invoice_status']))echo $value['invoice_status']; ?>&nbsp;</td>
                </tr>
            <?php } ?>

            <?php if( $STT > 0 && $STT < 10 ){ // chỉ khi nào số lượng nhỏ hơn 10 mới add thêm mà thôi
                $loop_for = $limit - $STT;
                for ($j=0; $j < $loop_for; $j++) {
                    $i = 1 - $i;
                  ?>
                <tr class="jt_line_<?php if ($i == 1) { ?>black<?php } else { ?>light<?php } ?>"><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
            <?php
                }
            } ?>
        </tbody>
    </table>

    <?php if( $STT == 0 ){ ?>
    <center style="margin-top:30px">(No data)</center>
    <?php } ?>
</div>
<script type="text/javascript">
$(function(){
	var store = localStorage['batch_salesinvoice<?php echo $key ?>'];
	if(store!=undefined){
		data = JSON.parse(store);
		var html = '';
		for(i in data){
			$("#tr_"+i).remove();
			html += data[i];
		}
		$("tbody tr:first","#batch_salesinvoices<?php echo $key ?>").after(html);
		for(i in data){
			$("#tr_"+i).removeClass();
			$("#"+i).attr("checked",true);
		}

	}
	$(".check_salesinvoices","#batch_salesinvoices<?php echo $key ?>").change(function(){
		var data = localStorage['batch_salesinvoice<?php echo $key ?>'];
		if(data == undefined)
			data = {};
		else
			data = JSON.parse(data);
		var id = $(this).attr("id");
		if($(this).is(":checked"))
			data[id] = $("#tr_"+id)[0].outerHTML;
		else if(data[id]!=undefined)
			delete(data[id]);
		localStorage['batch_salesinvoice<?php echo $key ?>'] = JSON.stringify(data);
	});
    <?php if($key=='pdf_only'){ ?>
    $("#submit_batch<?php echo $key ?>").click(function(){
        var data = $("input:checked","#batch_salesinvoices<?php echo $key ?>").serialize();
        $.ajax({
            url: "<?php echo URL.'/salesinvoices/batch_invoices_pdf_only' ?>",
            data: data,
            type: "POST",
            success: function(result){
                if( result=="ok"){
                    localStorage.removeItem('batch_salesinvoice<?php echo $key ?>');
                    $("input[type=checkbox]","#batch_salesinvoices<?php echo $key ?>").prop("checked",false);
                    $("#window_popup_salesinvoices<?php echo $key ?>").data("kendoWindow").close();
                    alerts("Message","Batch Mail-Out Successful");
                } else
                    alerts("Message",result);
            }
        });
    })
    <?php }else{ ?>
    var callAjax = function(data) {
        <?php if(!IS_LOCAL){ ?>
        notifyTop("Sending Email. Please wait for a moment...");
        <?php } ?>
        var url = "<?php echo IS_LOCAL ? URL.'/salesinvoices/get_batch_invoices' : URL.'/salesinvoices/batch_invoices' ?>";
        $.ajax({
            url: url,
            data: data,
            type: "POST",
            success: function(result){
                <?php if(!IS_LOCAL){ ?>
                if( result.indexOf("$id")!=-1){
                    $("#notifyTop").fadeOut(600);
                    result = JSON.parse(result);
                    var html = "";
                    for(i in result)
                        html += "<a style=\"color:blue\" target=\"_blank\" href=\"<?php echo URL.'/communications/entry/' ?>"+result[i].$id+"\">Email "+(parseInt(i)+1)+"</a><br />";
                    localStorage.removeItem('batch_salesinvoice<?php echo $key ?>');
                    $("input[type=checkbox]","#batch_salesinvoices<?php echo $key ?>").prop("checked",false);
                    alerts("Email Lists",html);
                } else
                    alerts("Message",result);
                <?php } else { ?>
                if( $("#new_batch_invoice" ).attr("id") == undefined )
                    $('<div id="new_batch_invoice" style="display:none; min-width:300px;"></div>').appendTo("body");
                var html = '<span style="padding: 50%;"><img src="<?php echo URL ?>/theme/<?php echo $theme ?>/images/ajax-loader.gif" title="Loading..." /></span>';
                $("#new_batch_invoice" ).html(html);
                $("#new_batch_invoice" ).kendoWindow({
                    iframe: false,
                    actions: ["Maximize", "Close"],
                    width: "845px",
                    height: "510px",
                    activate: function(e){
                        $("#new_batch_invoice" ).parent().css({'height':'auto'});
                        $("#new_batch_invoice" ).html(result);
                    },
                    visible: false,
                    title: "Batch invoice",
                }).data("kendoWindow").center().open();
                <?php } ?>
            }
        });
    }
    $("#submit_batch<?php echo $key ?>").click(function(){
        $("#window_popup_salesinvoices<?php echo $key ?>").data("kendoWindow").close();
        var data = $("input:checked","#batch_salesinvoices<?php echo $key ?>").serialize();
        if(data == ""){
            alerts("Message", "Please choose at least one invoice.");
            return false;
        }
        <?php if(!IS_LOCAL){ ?>
        confirms3("Message","Do you want to CC email to Order Contact?",["Yes","No",""]
                 ,function(){ //Yes
                    data += "&cc_to_contact=true";
                    callAjax(data);
                 }, function(){ //No
                    callAjax(data);
                 }, function(){
                    return false;
                 });
        <?php } else { ?>
            callAjax(data);
        <?php } ?>

    })
    <?php } ?>
})
</script>
<?php echo $this->element('popup/pagination'); ?>

<?php echo $this->Form->end(); ?>