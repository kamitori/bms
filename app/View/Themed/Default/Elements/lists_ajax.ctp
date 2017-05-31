<?php
	$arr_companies = array();
	if(in_array($controller, array('quotations','salesinvoices','salesorders'))){
		$origin_minimum  = $companies_class->get_product_minimum();
		$product_id = $origin_minimum['product_id'];
		$origin_minimum = $origin_minimum['minimum'];
	}
?>
<?php if($this->Common->check_permission($controller.'_@_entry_@_delete',$arr_permission)): ?>
<br><?php $n=0;  foreach ($arr_list as $value) {  if($n%2>0) $nclass=" bg1"; else $nclass=" bg2";?>
	<ul class="ul_mag clear <?php echo $nclass;?>">

		<li class="hg_padd" style="width:.5%" onclick=" window.location.assign('<?php echo URL.'/'.$controller; ?>/entry/<?php if(isset($value['_id'])) echo $value['_id']; ?>');">
			<span class="icon_emp"></span>
		</li>
		<?php foreach ($list_field as $ks => $vls){?>
			<li class="hg_padd" style=" <?php if(isset($vls['css'])) echo $vls['css']; ?><?php if(isset($vls['align'])) echo 'text-align:'.$vls['align']; ?>">
				<?php
					// hien thi noi dung theo loai field
					if(isset($value[$ks]) ){
						if($arr_set['type'][$ks]=='select' && isset($opt_select[$ks][$value[$ks]]))
							echo $opt_select[$ks][$value[$ks]];

						else if(($arr_set['type'][$ks]=='price' || $arr_set['type'][$ks]=='price_notchange')){
							if(in_array($ks, array('sum_amount','sum_tax','sum_sub_total'))
							   && in_array($controller, array('quotations','salesinvoices','salesorders'))){
								if(isset($value['invoice_status']) && $value['invoice_status'] == 'Cancelled')
									$value['sum_sub_total'] = $value['sum_amount'] = $value['sum_tax'] = 0;
								else {
									$company_id = '';
									if(isset($value['company_id']))
										$company_id = $value['company_id'];
									if(isset($arr_companies[(string)$company_id]))
										$minimum = $arr_companies[(string)$company_id];
									else
										$minimum = $companies_class->get_minimum_order($company_id,$product_id,$origin_minimum,$arr_companies);
									$value['sum_sub_total'] = (isset($value['sum_sub_total']) ? $value['sum_sub_total'] : 0);
									$value['sum_amount'] = (isset($value['sum_amount']) ? $value['sum_amount'] : 0);
									$value['taxval'] = (isset($value['taxval']) ? $value['taxval'] : 0);
									if($value['sum_sub_total']<$minimum){
						    			$more_sub_total = $minimum - (float)$value['sum_sub_total'];
						    			$sub_total = $more_sub_total;
						                $tax = $sub_total*(float)$value['taxval']/100;
						                $amount = $sub_total+$tax;
						    			$value['sum_sub_total'] += $sub_total;
						    			$value['sum_amount'] += $amount;
						    			$value['sum_tax'] = $value['sum_amount']-$value['sum_sub_total'];
						    		}
								}
							}
							echo $this->Common->format_currency((float)$value[$ks]);
						}

						else if($arr_set['type'][$ks]=='percent')
							echo $this->Common->format_currency((float)$value[$ks]*100).'%';

						else if($arr_set['type'][$ks]=='date' && is_object($value[$ks]))
							echo $this->Common->format_date($value[$ks]->sec);

						else if($arr_set['type'][$ks]=='checkbox')
							echo '<div class="middle_check"><input type="checkbox" disabled="disabled" checked="checked"></div>';


						else if(strpos($arr_set['type'][$ks], 'relationship')!==false){
							$is_price = false;
							if(strpos($arr_set['type'][$ks], 'price')!==false)
								$is_price = true;
							$ids_key = $arr_set['id'][$ks];
							$ids_value = $value[$ids_key];
							$coclass = $arr_set['cls'][$ks].'_class';
							if(isset($list_syncname[$ks]))
								$syncname[$ks] = $list_syncname[$ks];
							if(!is_array($value[$ks]) && $ids_value!=''){
								$link = $value[$syncname[$ks]];
								if($is_price){
									$price = $$coclass->select_one(array('_id'=>$ids_value),array($syncname[$ks],'company_id'));
									$company_id = '';
									if(isset($price['company_id']))
										$company_id = $price['company_id'];
									$price = $price[$syncname[$ks]];
									if(isset($arr_companies[(string)$company_id]))
										$minimum = $arr_companies[(string)$company_id];
									else
										$minimum = $companies_class->get_minimum_order($company_id,$product_id,$origin_minimum,$arr_companies);
									if($price<$minimum)
										$price = $minimum;
									$link = $this->Common->format_currency((float)$price);
								}
								echo '<a style="text-decoration:none;" href="'.URL.'/'.$arr_set['cls'][$ks].'/entry/'.$ids_value.'">'.$link.'</a>';
							}
							else if(!is_array($value[$ks]))
								echo $value[$ks];

						}

						else if(isset($value[$ks]) && is_array($value[$ks]))
							echo 'Data array';

						else if(isset($value[$ks]))
							echo $value[$ks];
					}
				?>
			</li>
		<?php } ?>

		<li class="hg_padd bor_mt" style="width:.5%">
			<div class="middle_check">
				<a title="Delete link" style="cursor:pointer;" class="delete_link" onclick="delete_record('del_<?php if(isset($value['_id'])) echo $value['_id']; ?>');" id="del_<?php if(isset($value['_id'])) echo $value['_id']; ?>">
					<span class="icon_remove2"></span>
				</a>
			</div>
		</li>

	</ul>
<?php $n++; } ?>
<?php else: ?>
<br><?php $n=0; foreach ($arr_list as $value) { if($n%2>0) $nclass=" bg1"; else $nclass=" bg2";?>

	<ul class="ul_mag clear <?php echo $nclass;?>">

		<li class="hg_padd" style="width:.5%" onclick=" window.location.assign('<?php echo URL.'/'.$controller; ?>/entry/<?php if(isset($value['_id'])) echo $value['_id']; ?>');">
			<span class="icon_emp"></span>
		</li>
		<?php foreach ($list_field as $ks => $vls){?>
			<li class="hg_padd" style=" <?php if(isset($vls['css'])) echo $vls['css']; ?><?php if(isset($vls['align'])) echo 'text-align:'.$vls['align']; ?>">
				<?php
					// hien thi noi dung theo loai field
					if(isset($value[$ks]) ){
						if($arr_set['type'][$ks]=='select' && isset($opt_select[$ks][$value[$ks]]))
							echo $opt_select[$ks][$value[$ks]];

						else if(($arr_set['type'][$ks]=='price' || $arr_set['type'][$ks]=='price_notchange')
						        && in_array($controller, array('quotations','salesinvoices','salesorders'))){
							if(isset($value['invoice_status']) && $value['invoice_status'] == 'Cancelled')
								$value['sum_sub_total'] = $value['sum_amount'] = $value['sum_tax'] = 0;
							else {
								$company_id = '';
								if(isset($value['company_id']))
									$company_id = $value['company_id'];
								if(isset($arr_companies[(string)$company_id]))
									$minimum = $arr_companies[(string)$company_id];
								else
									$minimum = $companies_class->get_minimum_order($company_id,$product_id,$origin_minimum,$arr_companies);
								$value['sum_sub_total'] = (isset($value['sum_sub_total']) ? $value['sum_sub_total'] : 0);
								$value['sum_amount'] = (isset($value['sum_amount']) ? $value['sum_amount'] : 0);
								$value['taxval'] = (isset($value['taxval']) ? $value['taxval'] : 0);
								if($value['sum_sub_total']<$minimum){
					    			$more_sub_total = $minimum - (float)$value['sum_sub_total'];
					    			$sub_total = $more_sub_total;
					                $tax = $sub_total*(float)$value['taxval']/100;
					                $amount = $sub_total+$tax;
					    			$value['sum_sub_total'] += $sub_total;
					    			$value['sum_amount'] += $amount;
					    			$value['sum_tax'] = $value['sum_amount']-$value['sum_sub_total'];
					    		}
							}
							echo $this->Common->format_currency((float)$value[$ks]);
						}

						else if($arr_set['type'][$ks]=='percent')
							echo $this->Common->format_currency((float)$value[$ks]*100).'%';

						else if($arr_set['type'][$ks]=='date' && is_object($value[$ks]))
							echo $this->Common->format_date($value[$ks]->sec);

						else if($arr_set['type'][$ks]=='checkbox')
							echo '<div class="middle_check"><input type="checkbox" disabled="disabled" checked="checked"></div>';

						else if(strpos($arr_set['type'][$ks], 'relationship')!==false){
							$is_price = false;
							if(strpos($arr_set['type'][$ks], 'price')!==false)
								$is_price = true;
							$ids_key = $arr_set['id'][$ks];
							$ids_value = $value[$ids_key];
							$coclass = $arr_set['cls'][$ks].'_class';
							if(isset($list_syncname[$ks]))
								$syncname[$ks] = $list_syncname[$ks];
							if(!is_array($value[$ks]) && $ids_value!=''){
								$link = $value[$syncname[$ks]];
								if($is_price){
									$price = $$coclass->select_one(array('_id'=>$ids_value),array($syncname[$ks],'company_id'));
									$company_id = '';
									if(isset($price['company_id']))
										$company_id = $price['company_id'];
									$price = $price[$syncname[$ks]];
									if(isset($arr_companies[(string)$company_id]))
										$minimum = $arr_companies[(string)$company_id];
									else
										$minimum = $companies_class->get_minimum_order($company_id,$product_id,$origin_minimum,$arr_companies);
									if($price<$minimum)
										$price = $minimum;
									$link = $this->Common->format_currency((float)$price);
								}
								echo '<a style="text-decoration:none;" href="'.URL.'/'.$arr_set['cls'][$ks].'/entry/'.$ids_value.'">'.$link.'</a>';
							}
							else if(!is_array($value[$ks]))
								echo $value[$ks];

						}

						else if(isset($value[$ks]) && is_array($value[$ks]))
							echo 'Data array';

						else if(isset($value[$ks]))
							echo $value[$ks];
					}
				?>
			</li>
		<?php } ?>
	</ul>
<?php $n++; } ?>

<?php endif; ?>
<?php echo $this->element('popup/pagination_lists'); ?>