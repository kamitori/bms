<style type="text/css">
	ul.ul_mag li.hg_padd {
	overflow: visible !important;
	}
	.bg4 {
	background: none repeat scroll 0 0 #949494;
	color: #fff;
	}
	.bg4 span h4 {
	margin-left: 1%;
	width: 100%;
	}
	.k-insertImage{
		background-image: none!important;
	}
</style>
<?php echo $this->element('window'); ?>
<div id="loading" style="display:none;"></div>
<div class="tab_1 full_width">
	<span class="title_block bo_ra1">
		<span class="fl_dent">
			<h4>General</h4>
		</span>
	</span>
	<ul class ="ul_mag clear bg3">
		<li class="hg_padd" style="width:20%"><?php echo translate('Name'); ?></li>
		<li class="hg_padd" style="width:75%"></li>
	</ul>
	<div class="container_same_category" style="height: 449px;overflow-y: auto">
		<ul class="ul_mag clear bg1" style="height:30px;line-height: 30px">
			<li class="hg_padd line_mg" style="width:20%;line-height:19px;height:22px;">
				<input type="hidden" id="_idAccountant" value="<?php if(isset($accountant['_id'])) echo $accountant['_id']; ?>" />
				Accountant
				<span class="iconw_m"  id="click_open_window_contactsaccountant" title="Specify Accountant"></span>
			</li>
			<li class="hg_padd line_mg" style="width:75%;line-height:19px;height:22px;;">
				<input type="text" id="Accountant" class="input_inner input_inner_w float_left" name"data[accountant]" value="<?php if(isset($accountant['accountant'])) echo $accountant['accountant']; ?>" />
                <input type="hidden" id="AccountantId" name"data[accountant_id]" value="<?php if(isset($accountant['accountant_id'])) echo $accountant['accountant_id']; ?>" />
                 <script type="text/javascript">
                        $(function(){
                            window_popup('contacts', 'Specify Accountant','accountant','click_open_window_contactsaccountant','?is_employee=1');
                        });
                </script>
			</li>
		</ul>
		<ul class="ul_mag clear bg2" style="height:30px;line-height: 30px">
			<li class="hg_padd line_mg" style="width:20%;line-height:19px;height:22px;">
				<input type="hidden" id="_idProduct" value="<?php if(isset($product['_id'])) echo $product['_id']; ?>" />
				Minimum Order Adjustment
				<span class="iconw_m"  id="click_open_window_productsminimum" title="Specify Products"></span>
			</li>
			<li class="hg_padd line_mg" style="width:75%;line-height:19px;height:22px;;">
				<input type="text" id="ProductName" class="input_inner input_inner_w float_left bg2" name"data[product]" value="<?php if(isset($product['product_name'])) echo htmlentities($product['product_name']); ?>" />
                <input type="hidden" id="ProductId" name"data[product_id]" value="<?php if(isset($product['product_id'])) echo $product['product_id']; ?>" />
                 <script type="text/javascript">
                        $(function(){
                            window_popup('products', 'Specify Products','minimum_order','click_open_window_productsminimum');
                        });
                </script>
			</li>
		</ul>
		<ul class="ul_mag clear bg1" style="height:30px;line-height: 30px">
			<li class="hg_padd line_mg" style="width:20%;line-height:19px;height:22px;">
				<input type="hidden" id="_idChangingCode" value="<?php if(isset($changing_code['_id'])) echo $changing_code['_id']; ?>" />
				Changing Code
			</li>
			<li class="hg_padd line_mg" style="width:75%;line-height:19px;height:22px;;">
				<input type="password" id="ChangingCodePassword" onchange="save_changing_code();" class="input_inner input_inner_w float_left bg1" name"data[product]" value="password" />
			</li>
		</ul>
		<ul class="ul_mag clear bg2" style="height:60px;line-height: 60px">
			<li class="hg_padd line_mg" style="width:20%;line-height:55px;height:55px;">
				<input type="hidden" id="_idLogo" value="<?php if(isset($logo['_id'])) echo $logo['_id']; ?>" />
				Logo
			</li>
			<li class="hg_padd line_mg" style="width:75%;line-height:55px;height:55px;">
				<input <?php if(!isset($logo['image_path']) || $logo['image_path'] == '') { ?>type="text"<?php } else { ?>type="hidden"<?php } ?> readonly id="logo" onchange="save_logo()" data-id="logo_link" style="float:left;" value="<?php if(isset($logo['image_path'])) echo $logo['image_path']; ?>" />
				<img id="logo_link" style="max-height: 50px;<?php if(!isset($logo['image_path']) || $logo['image_path'] == '') echo 'display: none;'; ?>" src="<?php if(isset($logo['image_path'])) echo $logo['image_path']; ?>" />
				<span id="logo_link_delete" onclick="removeImg(this,'logo_link')" style="background: url('<?php echo URL.'/theme/'.$theme.'/images/icon.png'; ?>') no-repeat scroll -670px -75px transparent;cursor: pointer;height: 21px;width: 8px;float: left;<?php if(!isset($logo['image_path']) || $logo['image_path'] == ''){ ?>display: none<?php } ?>"></span>
				<script type="text/javascript">
                    $("#logo").kendoEditor({
                        tools: [
                            "insertImage"
                        ],
                        imageBrowser: {
                            dataType:'json',
                            transport: {
                                read: {
                                    url: "/images/list_images",
                                    type: "POST",
                                    dataType:'json'
                                },
                                destroy: {
                                    url: "/images/delete_image",
                                    type: "POST",
                                    dataType:'json'
                                },
                                uploadUrl: "/images/upload_image",
                                thumbnailUrl: "/images/thumb_image",
                                imageUrl: function(imagename){
                                	var image_path = "<?php echo URL.'/theme/'.$theme.'/images/';?>"+imagename;
                                    $("#logo").val(image_path).attr('type','hidden').trigger("change");
                                    var img_id = $("#logo").attr("data-id");
                                    $("#"+img_id).attr("src",image_path).show();
                                    $("#"+img_id+"_delete").show();
                                    event.preventDefault();
                                },
                            }
                        }
                    }).data("kendoEditor");
					function removeImg(obj,img_id){
				        $("#logo").val("");
				        $("#"+img_id).attr("src","").hide();
				        $(obj).hide();
				        var input_id = img_id.replace("_link","");
				        $("#"+input_id).val("").attr("type","text");
				    }
                </script>
			</li>
		</ul>
        <ul class="ul_mag clear bg1" style="height:30px;line-height: 30px">
            <li class="hg_padd line_mg" style="width:20%;line-height:19px;height:22px;">
                <input type="hidden" id="_idFormatCurrency" value="<?php if(isset($format_currency['_id'])) echo $format_currency['_id']; ?>" />
                Format Currency (No. after comma)
            </li>
            <li class="hg_padd line_mg" style="width:75%;line-height:19px;height:22px;;">
                <input type="text" id="FormatCurrency" onchange="save_format_currency();" class="input_inner input_inner_w float_left bg1" name"format_currency" value="<?php echo (isset($format_currency['format_currency']) ? $format_currency['format_currency'] : 2);  ?>" />
            </li>
        </ul>
        <ul class="ul_mag clear bg2" style="height:30px;line-height: 30px">
            <li class="hg_padd line_mg" style="width:20%;line-height:19px;height:22px;">
                <?php echo isset($current_default['name'])?$current_default['name']:'Currency default';?>
            </li>
            <li class="hg_padd line_mg" style="width:75%;line-height:19px;height:22px;;">
                <input type="text" id="CurrencyDefault" onchange="save_currency_default();" class="input_inner input_inner_w float_left bg2" name"format_date" value="<?php echo $current_list[$current_default['value']];?>" />
                <input type="hidden" id="CurrencyDefaultId" value="<?php echo $current_default['value'];?>" />
                <input type="hidden" id="CurrencyDefault_id" value="<?php echo (string)$current_default['_id'];?>" />
                <script type="text/javascript">
                    $("#CurrencyDefault").combobox(<?php echo json_encode($current_list); ?>);
                </script>
            </li>
        </ul>
        <ul class="ul_mag clear bg1" style="height:30px;line-height: 30px">
            <li class="hg_padd line_mg" style="width:20%;line-height:19px;height:22px;">
                <input type="hidden" id="_idFormatDate" value="<?php if(isset($format_date['_id'])) echo $format_date['_id']; ?>" />
                Format Date
            </li>
            <li class="hg_padd line_mg" style="width:75%;line-height:19px;height:22px;;">
                <input type="text" id="FormatDate" onchange="save_format_date();" class="input_inner input_inner_w float_left bg1" name"format_date" value="<?php echo (isset($format_date['format_date']) ? date($format_date['format_date']) : '');  ?>" />
                <input type="hidden" id="FormatDateId" value="<?php echo (isset($format_date['format_date']) ? $format_date['format_date'] : '');  ?>" />
                <script type="text/javascript">
                    $("#FormatDate").combobox(<?php echo $format_date_list ?>);
                </script>
            </li>
        </ul>
        <ul class="ul_mag clear bg2" style="height:30px;line-height: 30px">
            <li class="hg_padd line_mg" style="width:20%;line-height:19px;height:22px;">
                Preferred Customer Default Search
            </li>
            <li class="hg_padd line_mg" style="width:75%;line-height:19px;height:22px;;">
                <input type="text" id="default-prefer-customer-search" onchange="save_default_prefer_customer_search();" class="input_inner input_inner_w float_left bg2" readonly name"default_prefer_customer_search" value="<?php echo (isset($default_search['default_search']) && $default_search['default_search'] ? 'Yes' : 'No'); ?>" />
                <input type="hidden" id="default-prefer-customer-search-id" value="<?php echo (string)(isset($default_search['_id']) ? $default_search['_id'] : '');?>" />
                <script type="text/javascript">
                    $("#default-prefer-customer-search").combobox({1 : "Yes", 0 : "No"});
                </script>
            </li>
        </ul>
        <ul class="ul_mag clear bg1" style="height:30px;line-height: 30px">
            <li class="hg_padd line_mg" style="width:20%;line-height:19px;height:22px;">
                <input type="hidden" id="_idOpenAccount" value="<?php if(isset($open_account['_id'])) echo $open_account['_id']; ?>" />
                Open Account
                <span class="iconw_m"  id="click_open_window_contactsopen_account" title="Specify Open Account"></span>
            </li>
            <li class="hg_padd line_mg" style="width:75%;line-height:19px;height:22px;;">
                <input type="text" id="OpenAccount" class="input_inner input_inner_w float_left" style="width: 80%;" name"data[open_account]" value="<?php if(isset($open_account['open_account'])) echo $open_account['open_account']; ?>" />
                <input type="hidden" id="OpenAccountId" name"data[open_account_id]" value="<?php if(isset($open_account['open_account_id'])) echo $open_account['open_account_id']; ?>" />
                <button class="btn_pur" style="width: 150px; position: absolute; margin-top: 17px;" onclick="generateCompany()"> Generate Company </button>
                 <script type="text/javascript">
                        $(function(){
                            window_popup('contacts', 'Specify Open Account','open_account','click_open_window_contactsopen_account','?is_employee=1');
                        });
                </script>
            </li>
        </ul>
        <ul class="ul_mag clear bg2" style="height:30px;line-height: 30px">
            <li class="hg_padd line_mg" style="width:20%;line-height:19px;height:22px;">
                <input type="hidden" id="_idSaleManager" value="<?php if(isset($sale_manager['_id'])) echo $sale_manager['_id']; ?>" />
                Sale Manager
                <span class="iconw_m"  id="click_open_window_contactssalemanager" title="Specify Sale Manager"></span>
            </li>
            <li class="hg_padd line_mg" style="width:75%;line-height:19px;height:22px;;">
                <input type="text" id="SaleManager" class="input_inner input_inner_w float_left bg2" name"data[sale_manager]" value="<?php if(isset($sale_manager['sale_manager'])) echo $sale_manager['sale_manager']; ?>" />
                <input type="hidden" id="SaleManagerId" name"data[sale_manager_id]" value="<?php if(isset($sale_manager['sale_manager_id'])) echo $sale_manager['sale_manager_id']; ?>" />
                 <script type="text/javascript">
                        $(function(){
                            window_popup('contacts', 'Specify Sale Manager','sale_manager','click_open_window_contactssalemanager','?is_employee=1');
                        });
                </script>
            </li>
        </ul>
        <ul class="ul_mag clear bg1" style="height:30px;line-height: 30px">
            <li class="hg_padd line_mg" style="width:20%;line-height:19px;height:22px;">
                <input type="hidden" id="_idProductionTime" value="<?php if(isset($production_time['_id'])) echo $production_time['_id']; ?>" />
                Regular Production Time
            </li>
            <li class="hg_padd line_mg" style="width:75%;line-height:19px;height:22px;;">
                <input type="number" id="ProductionTimeDay" onchange="save_production_time();" class="input_inner input_inner_w float_left bg1" value="<?php echo (isset($production_time['day']) ? $production_time['day'] : 0) ?>" />
            </li>
        </ul>
	</div>
	<span class="title_block bo_ra2">
	</span>
</div>
<script type="text/javascript">
function save_logo(){
	 $.ajax({
            url: '<?php echo URL ?>/settings/save_logo',
            type: 'POST',
            data: {'logo':$("#logo").val(),'_id':$("#_idLogo").val()},
            success: function(result){
                if(result!='ok')
                    alerts('Message',result);
            }
        });
}
function after_choose_contacts(contact_id,contact_name, key){
    if( key == 'accountant' ) {
        $("#AccountantId").val(contact_id);
        $("#Accountant").val(contact_name);
        save_accountant();
        $("#window_popup_contactsaccountant").data("kendoWindow").close();
    } else if( key == 'open_account' ) {
        $("#OpenAccountId").val(contact_id);
        $("#OpenAccount").val(contact_name);
        save_open_account();
        $("#window_popup_contactsopen_account").data("kendoWindow").close();
    } else if( key == 'sale_manager' ) {
        $("#SaleManagerId").val(contact_id);
        $("#SaleManager").val(contact_name);
        save_sale_manager();
        $("#window_popup_contactssale_manager").data("kendoWindow").close();
    }

}
function after_choose_products(product_id){
    $("#ProductId").val(product_id);
    var data = JSON.parse($("#after_choose_productsminimum_order"+product_id).val());
    $("#ProductName").val(data.code+' - '+data.name);
    save_product();
    $("#window_popup_productsminimum_order").data("kendoWindow").close();
}
function save_production_time(){
     $.ajax({
            url: '<?php echo URL ?>/settings/save_production_time',
            type: 'POST',
            data: {'day':$("#ProductionTimeDay").val(),'_id':$("#_idProductionTime").val()},
            success: function(result){
                if(result!='ok')
                    alerts('Message',result);
            }
        });
}
function save_format_date(){
     $.ajax({
            url: '<?php echo URL ?>/settings/save_format_date',
            type: 'POST',
            data: {'format_date':$("#FormatDateId").val(),'_id':$("#_idFormatDate").val()},
            success: function(result){
                if(result!='ok')
                    alerts('Message',result);
            }
        });
}
function save_format_currency(){
     $.ajax({
            url: '<?php echo URL ?>/settings/save_format_currency',
            type: 'POST',
            data: {'format_currency':$("#FormatCurrency").val(),'_id':$("#_idFormatCurrency").val()},
            success: function(result){
                if(result!='ok')
                    alerts('Message',result);
            }
        });
}
function save_accountant(){
     $.ajax({
            url: '<?php echo URL ?>/settings/save_accountant',
            type: 'POST',
            data: {'accountant':$("#Accountant").val(),'accountant_id':$("#AccountantId").val(),'_id':$("#_idAccountant").val()},
            success: function(result){
                if(result!='ok')
                    alerts('Message',result);
            }
        });
}
function save_open_account(){
     $.ajax({
            url: '<?php echo URL ?>/settings/save_open_account',
            type: 'POST',
            data: {'open_account':$("#OpenAccount").val(),'open_account_id':$("#OpenAccountId").val(),'_id':$("#_idOpenAccount").val()},
            success: function(result){
                if(result!='ok')
                    alerts('Message',result);
            }
        });
}
function save_sale_manager(){
     $.ajax({
            url: '<?php echo URL ?>/settings/save_sale_manager',
            type: 'POST',
            data: {'sale_manager':$("#SaleManager").val(),'sale_manager_id':$("#SaleManagerId").val(),'_id':$("#_idSaleManager").val()},
            success: function(result){
                if(result!='ok')
                    alerts('Message',result);
            }
        });
}
function save_product(){
     $.ajax({
            url: '<?php echo URL ?>/settings/save_product',
            type: 'POST',
            data: {'product_name':$("#ProductName").val(),'product_id':$("#ProductId").val(),'_id':$("#_idProduct").val()},
            success: function(result){
                if(result!='ok')
                    alerts('Message',result);
            }
        });
}
function save_changing_code(){
     $.ajax({
            url: '<?php echo URL ?>/settings/save_changing_code',
            type: 'POST',
            data: {'password':$("#ChangingCodePassword").val(),'_id':$("#_idChangingCode").val()},
            success: function(result){
                if(result!='ok')
                    alerts('Message',result);
            }
        });
}
function save_currency_default(){
    $.ajax({
            url: '<?php echo URL ?>/settings/update_stuffs',
            type: 'POST',
            data: {'value':$("#CurrencyDefaultId").val(),'_id':$("#CurrencyDefault_id").val()},
            success: function(result){
                if(result!='ok')
                    alerts('Message',result);
            }
        });
}
function save_default_prefer_customer_search(){
    $.ajax({
            url: '<?php echo URL ?>/settings/save_default_search',
            type: 'POST',
            data: {'default_search':$("#default-prefer-customer-search").val(),'_id':$("#default-prefer-customer-search-id").val()},
            success: function(result){
                if(result!='ok')
                    alerts('Message',result);
            }
        });
}

function generateCompany()
{
    $.ajax({
        url: '<?php echo URL.'/companies/generate' ?>',
        success: function(result) {
            var result = $.parseJSON(result);
            if( result.message ) {
                alerts('Message', result.message.replace("\n", '<br />'));
            }
        }
    })
}
</script>