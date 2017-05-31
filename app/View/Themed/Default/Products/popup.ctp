<?php
	// pr($arr_where_popro);
	if(!isset($ppkey)) $ppkey = 'pops';
	if(!isset($arr_where_popro)) $arr_where_popro = array();
?>

<!-- BaoNam dời ra ngoài để bỏ form pagination vào -->
<form id="<?php echo $controller;?>_popup_form_<?php echo $ppkey;?>" style="float: right; " method="post" accept-charset="utf-8" onsubmit="popup_products_<?php if(isset($keys))echo $keys;?>();return false;">

	<!-- BaoNam INPUT TO SUBMIT FORM WHEN ENTER -->
	<input id="<?php echo $controller;?>_submit_<?php if(isset($keys))echo $keys;?>" style="height:1px; width:1px;opacity:0.1" type="submit" value="Search">

	<?php foreach($arr_off as $kk=>$vv){?>
    	<input type="hidden" name="arr_off[<?php echo $kk;?>]" id="arr_off_<?php echo $kk;?>" value="<?php echo $vv;?>" />
	<?php }?>

	<div style="margin-right: 0%;width: 100%;">
        <?php
			$n=0;
			foreach($keysearch as $kss=>$arr){
				$idk = $controller.'_'.$kss;
				if(isset($arr_where_popro[$kss]))
					$temp = $arr_where_popro[$kss]['values'];
				else
					$temp = '';
		?>

		<?php if(isset($arr['search_type']) && $arr['search_type']=='hidden'){?>
        	<input tabindex=<?php echo $n+1 ?> type="hidden" name="<?php echo $idk;?>" id="<?php echo $idk;?>" value="<?php echo $temp;?>" />


        <?php }else if(isset($arr['search_type']) && $arr['search_type']=='checkbox'){?>
       		<div class="box_inner_search float_left" style=" <?php if(isset($arr['css'])) echo $arr['css'];?>">
                <h6 class="float_left" style="margin-top:2px"><?php if(isset($arr['name'])) echo $arr['name'];?> &nbsp;</h6>
                <!-- <input type="hidden" name="<?php echo $idk;?>" id="<?php echo $idk;?>"  value="<?php echo $temp;?>"> -->
                <label class="m_check2">
                    <input tabindex=<?php echo $n+1 ?> type="checkbox" <?php if($idk == 'products_prefer_customer_id' && isset($prefer_check) ) echo 'checked';  ?> name="<?php echo $idk;?>" id="<?php echo $idk;?>"  value="<?php echo $temp;?>" />
                    <span style="margin: 4px 0 0 0;"></span>
                </label>
            </div>


        <?php }else{?>

            <div class="float_left">
                <h6 class="float_left" style="margin-top:2px"><?php if(isset($arr['name'])) echo $arr['name'];?>&nbsp;</h6>
                <span class="float_left" style=" <?php if($n!=count($keysearch)-1) echo 'margin-right:20px';?>">
                    <span class="block_sear  block1" style=" <?php if(isset($arr['width'])) echo 'width:'.(45+(int)$arr['width']).'px;';?>">
                        <span class="bg_search_1"></span>
                        <span class="bg_search_2"></span>
                        <div class="box_inner_search float_left" id="box_<?php echo $idk;?>" >
                            <a href="javascript:void(0)" onclick="$('#<?php echo $controller;?>_submit_<?php echo $ppkey;?>').click();">
                                <span class="icon_search"></span>
                            </a>
                            <div class="styled_select2 float_left" style=" <?php if(isset($arr['width'])) echo 'width:'.$arr['width'].'px;';?>;">
                                <?php if(isset($arr['search_type']) && $arr['search_type']=='select' && isset($select_data[$kss])){?>
                                	<select tabindex=<?php echo $n+1 ?> name="<?php echo $idk;?>" id="<?php echo $idk;?>" style="background: #636363;color: #fff;margin-left: -2px;margin-top:-2px;<?php if(isset($arr['width'])) echo 'width:'.$arr['width'].'px;';?>" onchange="$('#<?php echo $controller;?>_submit_<?php echo $ppkey;?>').click(); pagination_remove_num_<?php echo $controller.$key; ?>();">
                                    	<option value=""></option>
                                    	<?php
                                    	if (isset($select_data[$kss]))
                                    	 foreach($select_data[$kss] as $skk=>$svv){ ?>
                                        	<option value="<?php echo str_replace('_jt@_', '', $skk);?>" <?php if($skk==$temp){?>selected="selected"<?php }?>><?php echo $svv;?></option>
										<?php } ?>
                                    </select>

                                <?php }else if($kss=='company_name'){// Trường hợp loại popup  ?>
                                	<input tabindex=<?php echo $n+1 ?> name="<?php echo $idk;?>" id="<?php echo $idk;?>" class="<?php echo $idk;?>" style="background: #636363;color: #fff;margin-left: 7px;margin-top:-2px;<?php if(isset($arr['width'])) echo 'width:'.$arr['width'].'px;';?>" type="text" value="<?php echo $temp;?>" onkeypress=" pagination_remove_num_<?php echo $controller.$key; ?>();" />

								<?php }else{?>
                                	<input tabindex=<?php echo $n+1 ?> name="<?php echo $idk;?>" id="<?php echo $idk;?>" style="background: #636363;color: #fff;margin-left: -2px;margin-top:-2px;<?php if(isset($arr['width'])) echo 'width:'.$arr['width'].'px;';?>" type="text" value="<?php echo $temp;?>" onkeypress=" pagination_remove_num_<?php echo $controller.$key; ?>();" onchange="$('#<?php echo $controller;?>_submit_<?php echo $ppkey;?>').click(); pagination_remove_num_<?php echo $controller.$key; ?>();" />

                                 <?php }?>

                                 <?php if($kss=='company_name'){?>
                                 	<a href="javascript:void(0)" onclick="$('#<?php echo $idk;?>', '#window_popup_<?php echo $controller.$key; ?>').val(''); $('#<?php echo $controller;?>_submit_<?php echo $ppkey;?>').click(); pagination_remove_num_<?php echo $controller.$key; ?>();">
                                        	<span class="icon_closef" style="margin:-17px 0 0 0;"></span>
                                	</a>
                                <?php } else if($kss == 'prefer_customer') {?>
                                	<a href="javascript:void(0)" onclick="$('#<?php echo $idk;?>', '#window_popup_<?php echo $controller.$key; ?>').val(''); $('#<?php echo $idk;?>_id', '#window_popup_<?php echo $controller.$key; ?>').val(''); $('#<?php echo $controller;?>_submit_<?php echo $ppkey;?>').click(); pagination_remove_num_<?php echo $controller.$key; ?>();">
                                        	<span class="icon_closef" style="margin:-16px 0 0 -13px;"></span>
                                	</a>
                                <?php } ?>
                            </div>


							<?php if($kss=='company_name'){?>

                                <span class="iconw_m indent_dw_m" onclick='$("#click_open_window_companiessearch_company_name").click()' title="Specify Current supplier" style="margin-top:2px;" id="box_products_supplier_new_window"></span>
                            <?php } else if($kss == 'prefer_customer'){ ?>
                            	<span class="iconw_m indent_dw_m" onclick='$("#click_open_window_companiessearch_prefer_customer").click()' title="Specify prefer customer" style="margin-top:2px;" id="box_products_prefer_customer_new_window"></span>
                            <?php }else{?>

                                <a href="javascript:void(0)" onclick="$('#<?php echo $idk;?>', '#window_popup_<?php echo $controller.$key; ?>').val(''); $('#<?php echo $controller;?>_submit_<?php echo $ppkey;?>').click(); pagination_remove_num_<?php echo $controller.$key; ?>();">
                                        <span class="icon_closef" style="margin-left:0"></span>
                                </a>
							<?php }?>

                        </div>
                    </span>
                </span>
            </div>
        <?php $n++; } } ?>
       	<input id="<?php echo $controller;?>_submit_<?php echo $ppkey;?>" style="height:1px; width:1px;opacity:0.1" type="submit" value="Search" />

    </div>

    <div style="clear:both;height:6px"></div>

<div class="block_dent2" style="overflow: auto;max-width:1000px; margin: 0 auto; height:400px;width:auto" id="list_view_<?php echo $controller;?>">

	<!-- <div style="float:left; width:22%; height:30px;">
    	<div class="jt_ajax_note" style="width:100%; height:30px; display:block; position:inherit; float:left; text-align:left;">
        </div>
    </div> -->

	<?php //chan hien cac cot
		$expand = array('on_po','on_so','qty_in_stock','company','under_over','special_order','approved');
	?>
    <table class="jt_tb" id="Form_add" style="width:100%; font-size:12px;">


       <?php //Liet ke tieu de ?>
        <thead id="pagination_sort">
            <tr>
                <?php foreach ($list_field as $ks => $vls){ if(!in_array($ks,$expand)){?>
                    <th <?php if(isset($vls['popup_width'])) echo 'width="'.$vls['popup_width'].'"'; ?>>
                        <?php
							if(isset($ispo) && isset($arr_set['name'][$ks]) && $arr_set['name'][$ks] == 'Name')
								echo 'Anvy Name';

							else if(isset($arr_set['name'][$ks]))
								echo $arr_set['name'][$ks];
						?>

                        <?php if(isset($vls['sort']) && $vls['sort']){
                    			echo ' <span id="sort_'.$ks.'" rel="'.$ks.'" class="desc">&nbsp;</span>';
                        } ?>

                    </th>
                    <?php if($ks=='name' && isset($ispo)){?>
                    	<th> SKU</th>
                    	<th> Supplier name</th>
                        <input type="hidden" name="company_id" value="<?php echo $po_supplier;?>" />
                        <input type="hidden" name="group_type" value="BUY" />
                        <input type="hidden" name="ispo" value="1" />
                    <?php }?>

                <?php } } ?>
            </tr>
        </thead>



		<?php //Nội dung chính ?>
        <tbody>

        <?php
		$n=0;
		//============ TRƯỜNG HỢP LÀ PO ====================================================================
		if(isset($ispo)){

			// loop dòng
			foreach ($arr_list as $value){ //lap vong tung product
			if(isset($value['supplier']) && is_array($value['supplier']) && count($value['supplier'])>0){ //kiem tra supplier
				foreach ($value['supplier'] as $stt => $supplier){ //lap vong supplier
				if(isset($supplier['company_id']) && $supplier['company_id']==$po_supplier){ //nếu đúng là company đang xét
				if($n%2>0) $nclass="jt_line_light"; else $nclass="jt_line_black";

			?>
	                <tr class="<?php echo $nclass;?>" onclick="after_choose_<?php echo $controller;?>('<?php if(isset($value['_id'])) echo $value['_id']; ?>','','<?php if(isset($keys)) echo $keys; ?>')">
						<input type="hidden" id="after_choose_products<?php echo $key; ?><?php echo $value['_id']; ?>" value="<?php $value['_id'] = (string)$value['_id']; echo htmlentities(json_encode($value)); ?>">
						<?php
							// loop cột
							foreach ($list_field as $ks => $vls){ if(!in_array($ks,$expand)){?>
	                        <td align="<?php if(isset($vls['align'])) echo $vls['align']; ?>" <?php if(isset($vls['popup_width'])) echo 'width="'.$vls['popup_width'].'"'; ?> >
	                            <?php
									// hien thi noi dung theo loai field
									if(isset($value[$ks]) && $value[$ks]!=''){
										if($arr_set['type'][$ks]=='select' && isset($opt_select[$ks][$value[$ks]]))
											echo $opt_select[$ks][$value[$ks]];

										else if(($arr_set['type'][$ks]=='price' || $arr_set['type'][$ks]=='price_notchange'))
											echo $this->Common->format_currency((float)$value[$ks]);

										else if($arr_set['type'][$ks]=='percent')
											echo $this->Common->format_currency((float)$value[$ks]*100).'%';

										else if($arr_set['type'][$ks]=='date' && is_object($value[$ks]))
											echo $this->Common->format_date($value[$ks]->sec);

										else if($arr_set['type'][$ks]=='relationship'){
											$ids_key = $arr_set['id'][$ks];
											$ids_value = $value[$ids_key];
											$coclass = $arr_set['cls'][$ks].'_class';
											if(!is_array($value[$ks]))
											echo '<a style="text-decoration:none;color: black;" href="'.URL.'/'.$arr_set['cls'][$ks].'/entry/'.$ids_value.'">'.$$coclass->find_name($ids_value,$syncname[$ks]).'</a>';
										}

										else if(isset($value[$ks]) && is_array($value[$ks]))
											echo 'Data array';

										else if(isset($value[$ks]))
											echo $value[$ks];
									}
								?>
	                        </td>

							<?php if($ks=='name'){ // thêm 2 cột scode và sname ?>
                                <td width="5%">
                                    <?php
                                        if(isset($supplier['company_code']))
                                            echo $supplier['company_code'];
                                    ?>
                                </td>
                                <td align="<?php if(isset($vls['align'])) echo $vls['align']; ?>" <?php if(isset($vls['popup_width'])) echo 'width="'.$vls['popup_width'].'"'; ?> >
                                    <?php
                                        if(isset($supplier['sname']))
                                            echo $supplier['sname'];
                                    ?>
                                </td>
                            <?php }?>
	                    <?php } }//end cột  ?>
	                </tr>

	           <?php $n++; } } } } // end dòng (end if is supplier, end for supplier, end if, end for product ) ?>








       <?php
		//============ TRƯỜNG HỢP KHÁC PO ====================================================================
		}else{
			  	// loop dòng
			  	foreach ($arr_list as $value){
				if($n%2>0) $nclass="jt_line_light"; else $nclass="jt_line_black";
			 ?>
	                <tr class="<?php echo $nclass;?>" onclick="after_choose_<?php echo $controller;?>('<?php if(isset($value['_id'])) echo $value['_id']; ?>','','<?php if(isset($keys)) echo $keys; ?>')">
						<input type="hidden" id="after_choose_products<?php echo $key; ?><?php echo $value['_id']; ?>" value="<?php $value['_id'] = (string)$value['_id']; echo htmlentities(json_encode($value)); ?>">
						<?php
							// loop cột
							foreach ($list_field as $ks => $vls){ if(!in_array($ks,$expand)){?>
	                        <td align="<?php if(isset($vls['align'])) echo $vls['align']; ?>" <?php if(isset($vls['popup_width'])) echo 'width="'.$vls['popup_width'].'"'; ?> >
	                            <?php
									// hien thi noi dung theo loai field
									if(isset($value[$ks]) && $value[$ks]!=''){
										if($arr_set['type'][$ks]=='select' && isset($opt_select[$ks][$value[$ks]]))
											echo $opt_select[$ks][$value[$ks]];

										else if(($arr_set['type'][$ks]=='price' || $arr_set['type'][$ks]=='price_notchange'))
											echo $this->Common->format_currency((float)$value[$ks]);

										else if($arr_set['type'][$ks]=='percent')
											echo $this->Common->format_currency((float)$value[$ks]*100).'%';

										else if($arr_set['type'][$ks]=='date' && is_object($value[$ks]))
											echo $this->Common->format_date($value[$ks]->sec);

										else if($arr_set['type'][$ks]=='relationship'){
											$ids_key = $arr_set['id'][$ks];
											$ids_value = $value[$ids_key];
											if(!is_array($value[$ks]))
											echo '<a style="text-decoration:none;color: black;" href="'.URL.'/'.$arr_set['cls'][$ks].'/entry/'.$ids_value.'">'.$value[$ks].'</a>';
										}

										else if(isset($value[$ks]) && is_array($value[$ks]))
											echo 'Data array';

										else if(isset($value[$ks]))
											echo $value[$ks];
									}
								?>
	                        </td>

	                    <?php } }//end cột  ?>

	                </tr>

	           <?php $n++; } // end dòng ?>



			<?php } // ================KẾT THÚC NỘI DUNG CHÍNH==================================  ?>





            <?php if( $n > 0 && $n < 10 ){ // chỉ khi nào số lượng nhỏ hơn 10 mới add thêm mà thôi
                $loop_for = $limit - $n;
                for ($j=0; $j < $loop_for; $j++) { ?>
                <tr class="jt_line_<?php if($n%2>0) { ?>black<?php } else { ?>light<?php } ?>">
                	<?php foreach ($list_field as $ks => $vls){ if(!in_array($ks,$expand)){ ?><td>&nbsp;</td><?php } } ?>
                    <?php if(isset($ispo)){?>
                        <td>&nbsp;</td><td>&nbsp;</td>
                    <?php }?>
                </tr>

            <?php
                $n++; }
            } ?>
        </tbody>
    </table>
    <input type="hidden" name="key_return" id="key_return" value="<?php if(isset($keys)) echo $keys; ?>" />
</div>

<?php echo $this->element('popup/pagination', array('popup_id_submit_button' => $controller.'_submit_'.(isset($keys)?$keys:'') ) ); ?>


<?php if(isset($lockproduct)){?>
	<input type="hidden" name="lockproduct" value="1" />
<?php }?>
</form>
<script>
	function focusFirstInput(){
		setTimeout( '$("input[type=text]:first","#<?php echo $controller;?>_popup_form_<?php echo $ppkey;?>").focus()', 1000)
	}
	(function($){
		$(window).load(function(){
			$("#list_view_<?php echo $controller;?>").mCustomScrollbar({
				scrollButtons:{
					enable:false
				}
			});

		});
		<?php if(isset($ispo) || isset($lockproduct)){?>
			$("#products_company_name").attr('readonly','readonly');
			$("#box_products_company_name a").attr('onclick','');
			$("#box_products_supplier_new_window").attr('onclick','');
		<?php }?>

	})(jQuery);

	// BaoNam
	function popup_products_<?php if(isset($keys)) echo $keys;?>(){
		$.ajax({
			data:$("#<?php echo $controller;?>_submit_<?php if(isset($keys)) echo $keys;?>").closest("form").serialize(),
			success:function (data, textStatus) {
				$("#window_popup_<?php echo $controller;?><?php if(isset($keys)) echo $keys;?>").html(data);
			},
			type:"post",
			url:"<?php echo URL; ?>/products/popup"
		});
		return false;
	};

	$(function(){
		$("#products_prefer_customer_id", "#<?php echo $controller;?>_popup_form_<?php echo $ppkey;?>").change(function(){
			$(this).val($("#company_id").val());
			if( $(this).is(":checked") ) {
				$("#products_product_type", "#<?php echo $controller;?>_popup_form_<?php echo $ppkey;?>").val("");
				$('#<?php echo $controller;?>_submit_<?php echo $ppkey;?>').click();
				pagination_remove_num_<?php echo $controller.$key; ?>();
			} else {
				$("#products_product_type", "#<?php echo $controller;?>_popup_form_<?php echo $ppkey;?>").val("Product").trigger("change");
			}
		})
	    $("#products_name").kendoAutoComplete({
	        minLength: 3,
	        dataTextField: "name",
	        dataSource: new kendo.data.DataSource({
	            transport: {
	                read:{
	                    dataType: "json",
	                    url: "<?php echo URL.'/'.$controller.'/autocomplete/'; ?>",
	                    type:"POST",
	                    data: {
					       data: function(){
					       		return JSON.stringify({name:$("#products_name").val(),type:$("#products_product_type").find("option:selected").val(),category:$("#products_category").find("option:selected").val()});
					       },
					   	},
					    parameterMap: function(options, operation) {
			                return {
			                    StartsWith: options.filter.filters[0].value
			                }
			            }
					}
	            },
	            schema: {
	               data: "data"
	           	},
	        	serverFiltering: true
	        }),
	    });
	});

</script>