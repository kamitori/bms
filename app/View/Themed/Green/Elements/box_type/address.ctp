<?php
/*dữ liệu khởi tạo từ controller bao gồm :
	$address_label 			= array('Address','Shipping address',...)

	$address_conner:		$address_conner[0]['top'] 		= 'hgt fixbor';
							$address_conner[0]['bottom'] 	= 'fixbor2';
							$address_conner[1]['top'] 		= 'hgt';
							$address_conner[1]['bottom'] 	= 'hgt2 fixbor3'; ...

	$address_controller 	= array('Contact','Shipping',...);

	$address_field_name:	$address_field_name['Contact'][0]['name'] = 'default_address_1';
							$address_field_name['Contact'][0]['id']   = 'DefaultAddress1';
							$address_field_name['Contact'][1]['name'] = 'default_address_2';
							$address_field_name['Contact'][1]['id']   = 'DefaultAddress2';
							........... đến 6

	$address_value:			$address_value['Contact'][0] = 'số 25'
							$address_value['Contact'][1] = 'Hoàng Hoa Thám'
							........... đến 6

	$address_more_line 		= 0
	$address_hidden_field 	= array('contact_address','shipping_address',...);
	$address_hidden_value 	= array('số 25,Hoàng Hoa Thám, P6, Q.BT,','',...);
	$address_country 		= '<option value="" label=""></option>....';
	$address_country_id		= '39';
	$address_province 		= '<option value="" label=""></option>....';
	$address_province_id	= 'OT';
	$address_company_id		= 'company_id';
	$address_contact_id		= 'contact_id';
	$address_mode			='search' : dùng bật sang trạng thái search
	nếu không set thì element sẽ set default
*/

if(!isset($address_label))
	$address_label = array('Address','Shipping address');

if(!isset($address_key))
	$address_key = array('contact','shipping');

//css các góc
if(!isset($address_conner)){
	$address_conner[0]['top'] 		= 'hgt fixbor';
	$address_conner[0]['bottom'] 	= 'fixbor2';
	$address_conner[1]['top'] 		= 'hgt';
	$address_conner[1]['bottom'] 	= 'hgt2 fixbor3';
}
if(!isset($address_controller))
	$address_controller = array('Contact','Contact');

$count_address_key = count($address_key);

// name và id của input,select
if(!isset($address_field_name)){
	for($x=0;$x<$count_address_key;$x++){
		$keys = $address_key[$x];
		$address_field_name[$keys][0]['name'] = $keys.'_address_1';
		$address_field_name[$keys][0]['id']  = ucfirst($keys).'Address1';

		$address_field_name[$keys][1]['name'] = $keys.'_address_2';
		$address_field_name[$keys][1]['id'] 	= ucfirst($keys).'Address2';

		$address_field_name[$keys][2]['name'] = $keys.'_address_3';
		$address_field_name[$keys][2]['id'] 	= ucfirst($keys).'Address3';

		$address_field_name[$keys][3]['name'] = $keys.'_town_city';
		$address_field_name[$keys][3]['id'] 	= ucfirst($keys).'TownCity';

		$address_field_name[$keys][4]['name'] = $keys.'_country';
		$address_field_name[$keys][4]['id'] 	= ucfirst($keys).'Country';

		$address_field_name[$keys][5]['name'] = $keys.'_province_state';
		$address_field_name[$keys][5]['id'] 	= ucfirst($keys).'ProvinceState';

		$address_field_name[$keys][6]['name'] = $keys.'_zip_postcode';
		$address_field_name[$keys][6]['id'] 	= ucfirst($keys).'ZipPostcode';
	}
}

//giá trị field
for($x=0;$x<$count_address_key;$x++){
	$keys = $address_key[$x];
	if(!isset($address_value[$keys]))
		$address_value[$keys]= array('','','','',39,'','');

	$temp_province[$keys] = '';
}

// set province
if(isset($address_province) && !isset($address_province[$address_key[0]]))
	$temp_province[$address_key[0]] = $address_province;
else if(isset($address_province))
	$temp_province = $address_province;

$address_province = $temp_province;


//các dòng trống thêm phía dưới
if(!isset($address_more_line))
	$address_more_line = 0;

if(!isset($address_hidden_field))
	$address_hidden_field = array('contact_address','shipping_address');

if(!isset($address_hidden_value)){
	$address_hidden_value = array('','');
	for($x=0;$x<count($address_label);$x++){
		$keys = $address_key[$x];
		for($y=0;$y<count($address_value[$keys])-3;$y++){
			if($address_value[$keys][$y]!='')
				$address_hidden_value[$x] .= $address_value[$keys][$y].',';
			else
				$address_hidden_value[$x] .= '';
		}
		if(isset($address_province_id[$x]) && isset($address_province[$keys][$address_province_id[$x]]))
			$address_hidden_value[$x] .= $address_province[$keys][$address_province_id[$x]].',';
		else if(isset($address_province_id[$x]))
			$address_hidden_value[$x] .= $address_province_id[$x].',';
		if(isset($address_country_id[$x]) && isset($address_country[$address_country_id[$x]]))
			$address_hidden_value[$x] .= $address_country[$address_country_id[$x]].',';
		else
			$address_hidden_value[$x] .= 'Canada';
	}
}

//mode search
if(isset($address_mode) && $address_mode=='search')
	$searchcss = 'jt_input_search" placeholder="1';
else
	$searchcss = '';


$add_popup = false;
if(isset($address_company_id))
	$add_popup = true;

if(isset($address_contact_id))
	$add_popup = true;

if(isset($address_lock)){
	$locktrs = 'readonly="readonly"';
}else{
	$locktrs = '';
}


?>

<?php
	// BaoNam 15/10/2013 09h
	if( !isset($address_class_div_top) )
		$address_class_div_top = 'tab_1_inner float_left';
?>

<?php
	$count_address_label = count($address_label);
	for($m=0;$m<$count_address_label;$m++){
		if($m==0)
			$lbc = '';
		else
			$lbc = '';//'jt_grey';

		$keys = $address_key[$m];
		$address_ctr = $address_controller[$m];
		$address_hidden_value[$m] = preg_replace('/\s+/',' ',$address_hidden_value[$m]);
		$google_map = str_replace(" ","+",$address_hidden_value[$m]);
		$google_map = preg_replace('/,(,+)/',',',$google_map);

?>
    <div class="<?php echo $address_class_div_top; ?> float_left" id="address_box_<?php echo $keys;?>">
    	<?php if( ($controller == 'shippings' && $address_hidden_field[$m] == 'shipping_address') || ($m>0 && (!in_array($controller, array('salesaccounts','shippings')) ) ) ){ $address_more_line --; ?>
    	<p class="clear">
    		<span class="label_1 float_left minw_lab <?php if($controller == 'shippings') echo 'fixbor' ?>">
                <span class="link_to_contact_address fixbor">Ship to</span>
            </span>
            <div class="width_in3 float_left indent_input_tp">
            	<input <?php if(isset($disabled_all)){ ?>disabled="disabled"<?php } ?> class="input_1 float_left <?php echo $searchcss;?>" type="text" value="<?php echo $shipping_contact_name;?>" name="data[<?php echo $address_ctr;?>][shipping_contact_name]" id="ShippingContactName" <?php echo $locktrs;?> style=" padding: 0 20% 0 3%; width:78%;" />
            </div>
    	</p>
    	<?php } ?>
        <p class="clear">
            <span class="label_1 float_left minw_lab hgt <?php if($controller != 'shippings' || $address_hidden_field[$m] != 'shipping_address')echo $address_conner[$m]['top'];?>">
                <span class="link_to_<?php echo $address_hidden_field[$m];?>" class="link_to_contact_address fixbor"><?php echo $address_label[$m];?></span>
                <?php if($m>0){?><span class="difr">(if different)</span><?php }?>
            </span>
            <div class="width_in3 float_left indent_input_tp">
                <input <?php if(isset($disabled_all)){ ?>disabled="disabled"<?php } ?> class="input_1 float_left <?php echo $searchcss;?>" type="text" value="<?php echo $address_value[$keys][0];?>" name="data[<?php echo $address_ctr;?>][<?php echo $address_field_name[$keys][0]['name'];?>]" id="<?php echo $address_field_name[$keys][0]['id'];?>" <?php echo $locktrs;?> style=" padding: 0 20% 0 3%; width:78%;" />

                    <span class="iconw_m indent_dw_m" title="Specify address" id="click_open_window_addresses<?php echo $keys;?>" <?php if(isset($address_lock)){?> onclick="alerts('Message','<?php msg('STATUS_LOG');?>');"<?php }?>></span>

                    <?php if(!isset($address_lock)){?>
	                    <?php if(!isset($disabled_all)){ ?>
	                    <script type="text/javascript">
	                        $(function(){
								var strtemp = '?';
								<?php if(isset($address_company_id) && $address_company_id!=''){?>
									if($("#<?php echo $address_company_id;?>").val() != undefined)
										strtemp += 'company_id='+$("#<?php echo $address_company_id;?>").val()+'&';
								<?php }?>

								<?php if(1==2&&isset($address_contact_id) && $address_contact_id!=''){?>
									if($("#<?php echo $address_contact_id;?>").val() != undefined)
										strtemp += 'contact_id='+$("#<?php echo $address_contact_id;?>").val();
								<?php }?>
                                <?php if($keys == 'shipping'): ?>
                                findShippingAddress();
                                <?php else: ?>
	                            window_popup('addresses', 'Specify address','<?php echo $keys;?>', 'click_open_window_addresses<?php echo $keys;?>',strtemp);
                                <?php endif; ?>
	                        });
	                    </script>
	                    <?php } ?>
                   	<?php } ?>


                <a title="View map" id="map_<?php echo $keys;?>" href="javascript:void()" onclick="addresses_run_map_<?php echo $keys;?>()">
                    <span class="icosp_addr indent_sp_add"  <?php //if(!$add_popup){style="margin-right:0;" }?>></span>
                </a>
                <script type="text/javascript">
                function addresses_run_map_<?php echo $keys;?>(){
                	var address_1 = $("#<?php echo $address_field_name[$keys][0]['id'];?>").val();
                	var address_2 = $("#<?php echo $address_field_name[$keys][1]['id'];?>").val();
                	var address_3 = $("#<?php echo $address_field_name[$keys][2]['id'];?>").val();
                	var town_city = $("#<?php echo $address_field_name[$keys][3]['id'];?>").val();
                	var province_state = $("#<?php echo $address_field_name[$keys][5]['id'];?>").val();
                	var zip_postcode = $("#<?php echo $address_field_name[$keys][6]['id'];?>").val();
                	var country = $("#<?php echo $address_field_name[$keys][4]['id'];?>").val();
                	window.open("https://maps.google.com/maps?q=" + address_1 + " " + address_2 + " " + address_3 + " " + town_city + " " + province_state + " " + zip_postcode + " " + country,"_blank");
                }
                </script>
            </div>

            <div class="width_in3 float_left indent_input_tp">
                <input <?php if(isset($disabled_all)){ ?>disabled="disabled"<?php } ?> class="input_1 float_left <?php echo $searchcss;?>" type="text" value="<?php echo $address_value[$keys][1];?>" name="data[<?php echo $address_ctr;?>][<?php echo $address_field_name[$keys][1]['name'];?>]" id="<?php echo $address_field_name[$keys][1]['id'];?>" <?php echo $locktrs;?> />
            </div>
            <div class="width_in3 float_left indent_input_tp">
                <input <?php if(isset($disabled_all)){ ?>disabled="disabled"<?php } ?> class="input_1 float_left <?php echo $searchcss;?>" type="text" value="<?php echo $address_value[$keys][2];?>" name="data[<?php echo $address_ctr;?>][<?php echo $address_field_name[$keys][2]['name'];?>]" id="<?php echo $address_field_name[$keys][2]['id'];?>" <?php echo $locktrs;?> />
            </div>
        </p>
        <p class="clear">
            <span class="label_1 float_left minw_lab <?php echo $lbc;?>"> <?php echo translate('Town / City');?></span>
            <div class="width_in3 float_left indent_input_tp">
                <input <?php if(isset($disabled_all)){ ?>disabled="disabled"<?php } ?> class="input_1 float_left <?php echo $searchcss;?>" type="text" value="<?php echo $address_value[$keys][3];?>" name="data[<?php echo $address_ctr;?>][<?php echo $address_field_name[$keys][3]['name'];?>]" id="<?php echo $address_field_name[$keys][3]['id'];?>" <?php echo $locktrs;?> />
            </div>
        </p>
        <p class="clear">
            <span class="label_1 float_left minw_lab <?php echo $lbc;?>"> <?php echo translate('Province / State');?></span>
            <div class="width_in3 float_left indent_input_tp" id="<?php echo $keys;?>_province">

            		<?php $readonly = '';
            			if(isset($address_country_id[$m]) && in_array( $address_country_id[$m], array('CA', 'US') )){
            				$readonly = 'readonly';
            			}
        			?>
                    <input <?php if(isset($disabled_all)){ ?>disabled="disabled"<?php } ?> name="data[<?php echo $address_ctr;?>][<?php echo $address_field_name[$keys][5]['name'];?>]" combobox_blank="1" id="<?php echo $address_field_name[$keys][5]['id'];?>" class="input_select <?php echo $searchcss;?>" type="text" value="<?php $thisvl = $address_value[$keys][5]; if(isset($address_province[$keys][$thisvl]))echo $address_province[$keys][$thisvl]; else echo $thisvl;?>" <?php echo $locktrs;?> <?php echo $readonly;?> />
                   	<input <?php if(isset($disabled_all)){ ?>disabled="disabled"<?php } ?> type="hidden" name="data[<?php echo $address_ctr;?>][<?php echo $address_field_name[$keys][5]['name'];?>_id]" id="<?php echo $address_field_name[$keys][5]['id'];?>Id" value="<?php if(isset($address_province_id[$m]))echo $address_province_id[$m];?>" />
					<?php
						$strcom = array();
						if(isset($address_province[$keys])){
							$strcom = $address_province[$keys];
						}
					?>

					<?php if(!isset($disabled_all)){ ?>
					<script type="text/javascript">
                        $(function () {
                            $("#<?php echo $address_field_name[$keys][5]['id'];?>").combobox(<?php if(!isset($address_lock)) echo json_encode($strcom);?>);
                        });
                    </script>
                    <?php } ?>

            </div>
        </p>
        <p class="clear">
            <span class="label_1 float_left minw_lab <?php echo $lbc;?>"><?php echo translate('Zip / Post code');?></span>
            <div class="width_in3 float_left indent_input_tp">
                <input <?php if(isset($disabled_all)){ ?>disabled="disabled"<?php } ?> class="input_1 float_left <?php echo $searchcss;?>" type="text" value="<?php echo $address_value[$keys][6];?>" name="data[<?php echo $address_ctr;?>][<?php echo $address_field_name[$keys][6]['name'];?>]" id="<?php echo $address_field_name[$keys][6]['id'];?>" <?php echo $locktrs;?> />
            </div>
        </p>

        <p class="clear">
            <span class="label_1 float_left minw_lab <?php if($address_more_line==0 && !isset($address_botclass)){ ?> fix_bottom_address<?php } else if(isset($address_botclass)) echo $address_botclass; ?> <?php echo $lbc;?> <?php if($address_more_line==0) echo $address_conner[$m]['bottom'];?>"><?php echo translate('Country');?></span>
            <div class="width_in3 float_left indent_input_tp" id="<?php echo $keys;?>_country">
                <input <?php if(isset($disabled_all)){ ?>disabled="disabled"<?php } ?> combobox_blank="1" name="data[<?php echo $address_ctr;?>][<?php echo $address_field_name[$keys][4]['name'];?>]" id="<?php echo $address_field_name[$keys][4]['id'];?>" class="input_select <?php echo $searchcss;?>" type="text" value="<?php $thisvl = $address_value[$keys][4]; if(isset($address_country_id[$m]) && isset($address_country[$address_country_id[$m]]))echo $address_country[$address_country_id[$m]]; else if($searchcss!='') echo ''; else echo 'Canada';?>" onchange="change_pro('<?php echo $keys;?>');" readonly="readonly" />
                <input <?php if(isset($disabled_all)){ ?>disabled="disabled"<?php } ?> type="hidden" name="data[<?php echo $address_ctr;?>][<?php echo $address_field_name[$keys][4]['name'];?>_id]" id="<?php echo $address_field_name[$keys][4]['id'];?>Id" value="<?php if(isset($address_country_id[$m]))echo $address_country_id[$m];?>" />
                <?php
                    $strcom = array('0'=>'');
                    if(isset($address_country)){
                            $strcom = $address_country;
                    }
                ?>
                	<?php if(!isset($disabled_all)){ ?>
					<script type="text/javascript">
                        $(function () {
                            $("#<?php echo $address_field_name[$keys][4]['id'];?>").combobox(<?php if(!isset($address_lock)) echo json_encode($strcom);?>);
							<?php if(isset($address_lock)){?>
								$(".combobox_button").click(function(){
									alerts('Message','<?php msg('STATUS_LOG');?>');
								});
							<?php }?>
                        });
                    </script>
                    <?php } ?>
            </div>
        </p>


		<?php if($address_more_line>0){
				for($n=0;$n<$address_more_line;$n++){?>
           		 <p class="clear">
                    <span class="label_1 float_left minw_lab <?php if($n==$address_more_line-1) echo $lbc.$address_conner[$m]['bottom'];?>">&nbsp;</span>
                    <div class="width_in3 float_left">&nbsp;</div>
                </p>
        <?php } } ?>
        <input <?php if(isset($disabled_all)){ ?>disabled="disabled"<?php } ?> name="<?php echo $address_hidden_field[$m];?>" id="<?php echo $address_hidden_field[$m];?>" type="hidden" value="<?php if(isset($address_add) && $address_add[$keys]=='0') echo $address_hidden_value[$m];?>" />


        <?php if(isset($address_more_html)) echo $address_more_html;?>

    </div>
<?php }?>


<script type="text/javascript">
	function change_pro(keys){
		var ids = keys.charAt(0).toUpperCase() + keys.slice(1);//Capitalize the first letter
		var country_id = $("#"+ids+"CountryId").val();
		var old_html = $("#"+keys+"_province").html();
		old_html = old_html.split("<span");
		old_html = old_html[1].split(">");
		old_html = old_html[1];
		old_html = old_html.replace('value','value="" title');
		$("#"+keys+"_province .combobox").remove();

		var readonly = "";
		if( country_id == "CA" || country_id == "US" ){
			readonly = 'readonly';
		}else{
			old_html = old_html.replace('readonly','');
		}
		$("#"+keys+"_province").prepend(old_html+" onchange=\"<?php if(isset($address_onchange) )echo $address_onchange;?>\" " + readonly + " / >");

		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/ajax_general_province',
			dataType: "json",
			type:"POST",
			data: {country_id:country_id},
			success: function(jsondata){
				$("#"+ids+"ProvinceState").combobox(jsondata);
			}
		});
	}

    function findShippingAddress(refresh)
    {
        var div_popup_id = 'shipping_address_popup';
        // -------------- bắt đầu code -------------------------------
        // Kiểm tra tồn tại trước khi tạo lại
        if( $("#window_popup_" + div_popup_id).attr("id") == undefined )
            $('<div id="window_popup_' + div_popup_id + '" style="display:none; min-width:300px;"></div>').appendTo("body");

        var window_popup = $("#window_popup_" + div_popup_id);
        $('#click_open_window_addressesshipping')
            .unbind('click')
            .bind("click", function() {
                window_popup.data("kendoWindow").center();
                window_popup.data("kendoWindow").open();
                $(".container_same_category", window_popup).mCustomScrollbar({
                    scrollButtons:{
                        enable:false
                    },
                    advanced:{
                        updateOnContentResize: true,
                        autoScrollOnFocus: false,
                    }
                });
            });
        var get = '?company_id='+ $('#company_id').val() +'&company_name='+ $('#company_name').val();
        window_popup.kendoWindow({
            iframe: false,
            actions: ["Maximize", "Close"],
            width: "845px",
            height: "510px",
            activate: function(e){
                if($.trim(window_popup.html()) == "" || refresh == true){
                    var html = '<span style="padding: 50%;"><img src="<?php echo URL ?>/theme/<?php echo $theme ?>/images/ajax-loader.gif" title="Loading..." /></span>';
                    window_popup.html(html);
                    $.ajax({
                        url: "<?php echo URL; ?>/companies/address_popup" + get,
                        success: function(html){
                            window_popup.parent().css({'height':'auto'});
                            window_popup.html(html);
                        }
                    })
                }
            },
            visible: false,
            title: 'Shipping Addresses',
            // activate: onActivate,
        }).data("kendoWindow").center();
    }
</script>