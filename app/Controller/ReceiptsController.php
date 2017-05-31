<?php
App::uses('AppController', 'Controller');
class ReceiptsController extends AppController {

	var $name = 'Receipts';
	var $modelName = 'Receipt';
	public $helpers = array();
	public $opm; //Option Module

	public function beforeFilter(){
		parent::beforeFilter();
		$this->set_module_before_filter("Receipt");
	}

	public function rebuild_setting($arr_setting=array()){
        $arr_setting = $this->opm->arr_settings;
        if(!$this->check_permission($this->name.'_@_entry_@_edit'))
            foreach($arr_setting['field']['panel_1'] as $key=>$value)
            	$arr_setting['field']['panel_1'][$key]['lock'] = 1;
        $receipt = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())));
        $html = '<span style="cursor:default" >Customer</span>';
        if(isset($receipt['company_id'])&&is_object($receipt['company_id'])){
        	$html = '<span class="jt_box_line_span jt_link_on"><a style="color:#444;" href="'.URL.'/companies/entry/'.$receipt['company_id'].'">Customer</a></span>';
        } else if(isset($receipt['contact_id'])&&is_object($receipt['contact_id'])){
        	$html = '<span class="jt_box_line_span jt_link_on"><a style="color:#444;" href="'.URL.'/contacts/entry/'.$receipt['contact_id'].'">Customer</a></span>';
        }
        if(IS_LOCAL){
        	$arr_setting['field']['panel_3']['block']['outstanding']['field']['invoice_type']['width'] = 10;
        	$arr_setting['field']['panel_3']['block']['outstanding']['field']['total']['width'] = 14;
        	$arr_setting['field']['panel_3']['block']['outstanding']['field']['receipts']['width'] = 14;
        	$arr_setting['field']['panel_3']['block']['outstanding']['field']['inactive'] = array(
																		'name' =>  __('Inactive'),
																		'type' => 'hidden',
																		'align' => 'center',
																		'edit' => 1,
																		'width' => 6,
																	);
        }
        $this->set('link',$html);
        $this->opm->arr_settings = $arr_setting;
	    $arr_tmp = $this->opm->arr_field_key('cls');
	    $arr_link = array();
	    if(!empty($arr_tmp))
	    	foreach($arr_tmp as $key=>$value)
	    		$arr_link[$value][] = $key;
	    $this->set('arr_link',$arr_link);
    }

	//Các điều kiện mở/khóa field trong entry
	public function check_lock(){
		// if($this->get_id()!=''){
		// 	$arr_tmp = $this->opm->select_one(array('_id' => new MongoId($this->get_id())),array('ext_accounts_sync'));
		// 	if(isset($arr_tmp['ext_accounts_sync']) && $arr_tmp['ext_accounts_sync']==1)
		// 		return true;
		// }else
			return false;
	}
	public function swith_options($keys='')
	{
        parent::swith_options($keys);
		if ($keys == 'receipts_today') {
            $current_date = strtotime(date("Y-m-d"));
			$current_date_end = $current_date + DAY - 1;
			$arr_where['receipt_date']['>='] = array('values' => new MongoDate($current_date), 'operator' => 'day>=');
			$arr_where['receipt_date']['<='] = array('values' => new MongoDate($current_date_end), 'operator' => 'day<=');
			$this->Session->write($this->name . '_where', $arr_where);
			echo URL . '/' . $this->params->params['controller'] . '/lists';
        } else if ($keys == 'receipts_this_week') {
        	$week_start = strtotime("this week");
			$week_end = strtotime("this week +6 days") + DAY -1;
			$arr_where['receipt_date']['>='] = array('values' => new MongoDate($week_start), 'operator' => 'day>=');
			$arr_where['receipt_date']['<='] = array('values' => new MongoDate($week_end), 'operator' => 'day<=');
			$this->Session->write($this->name . '_where', $arr_where);
			echo URL . '/' . $this->params->params['controller'] . '/lists';
        }
        else if($keys == 'receipts_this_month'){
        	$month_ini = strtotime("first day of this month");
			$month_end = strtotime("last day of this month") + DAY -1;
			$arr_where['receipt_date']['>='] = array('values' => new MongoDate($month_ini), 'operator' => 'day>=');
			$arr_where['receipt_date']['<='] = array('values' => new MongoDate($month_end), 'operator' => 'day<=');
			$this->Session->write($this->name . '_where', $arr_where);
			echo URL . '/' . $this->params->params['controller'] . '/lists';
        }
        else if($keys == 'receipts_this_year'){
        	$year_start = strtotime("first day of january");
			$year_end = strtotime("last day of this year") + DAY - 1;
			$arr_where['receipt_date']['>='] = array('values' => new MongoDate($year_start), 'operator' => 'day>=');
			$arr_where['receipt_date']['<='] = array('values' => new MongoDate($year_end), 'operator' => 'day<=');
			$this->Session->write($this->name . '_where', $arr_where);
			echo URL . '/' . $this->params->params['controller'] . '/lists';
        }
        else if($keys == 'unallocated_receipts'){
        	$arr_where['unallocated'] = array('values' => 0, 'operator' => 'exists@!=');
			$this->Session->write($this->name . '_where', $arr_where);
			echo URL . '/' . $this->params->params['controller'] . '/lists';
        }
        else if($keys == 'payment_receive_report')
        	echo URL . '/' . $this->params->params['controller'] . '/payment_receive_report';
        else if($keys == 'customer_detailed_report')
        	$this->customer_detailed_report();
        else if($keys == 'customer_summary_report')
        	$this->customer_summary_report();
        else if($keys == 'email_receipt'){
        	$this->create_email_pdf(true);
        }
        else echo URL . '/' . $this->params->params['controller'] . '/entry';
        die;
	}
	//entry main
	public function entry(){
		$mod_lock = '0';
		if($this->check_lock()){
			$this->opm->set_lock(array('name'),'out');
			$mod_lock = '1';
		}
		$arr_set = $this->opm->arr_settings;
		// Get value id
		$iditem = $this->get_id();
		if($iditem=='')
			$iditem = $this->get_last_id();

		$this->set('iditem',$iditem);
		//Load record by id
		if($iditem!=''){
			$arr_tmp = $this->opm->select_one(array('_id' => new MongoId($iditem)));
			foreach($arr_set['field'] as $ks => $vls){
				if(isset($arr_set['field'][$ks]['setup'])) // nếu là loại panel, không phải loại box
				foreach($vls as $field => $values){
					if(isset($arr_tmp[$field])){
						$arr_set['field'][$ks][$field]['default'] = $arr_tmp[$field];
						if(preg_match("/_date$/",$field) && is_object($arr_tmp[$field]))
							$arr_set['field'][$ks][$field]['default'] = date('m/d/Y',$arr_tmp[$field]->sec);
						//chế độ lock, hiện name của các relationship custom
						else if($field=='company_name' && $mod_lock=='1')
							$arr_set['field'][$ks][$field]['default'] = $arr_tmp[$field];

						else if($this->opm->check_field_link($ks,$field)){
							$field_id = $arr_set['field'][$ks][$field]['id'];
							if(!isset($arr_set['field'][$ks][$field]['syncname']))
								$arr_set['field'][$ks][$field]['syncname'] = 'name';
							$arr_set['field'][$ks][$field]['default'] = $this->get_name($this->ModuleName($arr_set['field'][$ks][$field]['cls']),$arr_tmp[$field_id],$arr_set['field'][$ks][$field]['syncname']);

						}else if($field=='company_name' && isset($arr_tmp['company_id']) && $arr_tmp['company_id']!=''){
							$arr_set['field'][$ks][$field]['default'] = $this->get_name('Company',$arr_tmp['company_id']);

						}

						if(in_array($field,$arr_set['title_field']))
							$item_title[$field] = $arr_tmp[$field];
					}
				}
			}

			//echo $arr_set['field']['panel_1']['our_csr']['default'];die;
			$arr_set['field']['panel_1']['mongo_id']['default'] = $iditem;
			$this->Session->write($this->name.'ViewId',$iditem);

			//BEGIN custom
			//$arr_set['field']['panel_1']['code']['default'] =1;
			if(isset($arr_set['field']['panel_1']['code']['default']))
				$item_title['code'] = $arr_set['field']['panel_1']['code']['default'];
			else
				$item_title['code'] = '1';
			$this->set('item_title',$item_title);
			if(isset($arr_set['field']['panel_1']['our_bank_account']['after']))
			$arr_set['field']['panel_1']['our_bank_account']['after'] = str_replace("&nbsp;",$arr_set['field']['panel_1']['our_bank_account']['default']."&nbsp;",$arr_set['field']['panel_1']['our_bank_account']['after']);
			//END custom

			//Set allocation
			$subdatas['allocation'] = $this->allocation();

			//Set outstanding
			$subdatas['outstanding'] = array();
			if(isset($arr_tmp['company_id'])){
				$subdatas['outstanding'] = $this->outstanding($arr_tmp['company_id']);

			}
			if(isset($arr_tmp['notes']))
			$subdatas['notes']['notes'] = $arr_tmp['notes'];
			//Set allocation
			if(isset($arr_tmp['comments']))
			$subdatas['comments']['comments'] = $arr_tmp['comments'];

			if(isset($arr_tmp['amount_received']))
			$this->set('amount_allocated',(float)$arr_tmp['amount_received']);
			//show footer info
			$this->show_footer_info($arr_tmp);


		//add, setup field tự tăng
		}else{
			$this->redirect(URL.'/receipts/add');
		}
		$this->set('arr_settings',$arr_set);
		$this->set('subdatas', $subdatas);
		parent::entry();
	}

	public function entry_search(){
		//parent
		$arr_set = $this->opm->arr_settings;

		$arr_set['field']['panel_1']['code']['lock'] = '';
		$arr_set['field']['panel_1']['amount_received']['default'] = '';
		$arr_set['field']['panel_1']['receipt_date']['default'] = '';
		$arr_set['field']['panel_1']['paid_by']['default'] = '';
		$arr_set['field']['panel_1']['our_bank_account']['default'] = '';
		$arr_set['field']['panel_1']['name']['default'] = '';
		$arr_set['field']['panel_1']['our_rep']['default'] = '';
		$arr_set['field']['panel_1']['our_csr']['default'] = '';
		$this->set('arr_settings',$arr_set);
		$where = array();
		if($this->Session->check($this->name.'_where'))
			$where = $this->Session->read($this->name.'_where');
		if(count($where)>0){
			foreach($arr_set['field'] as $ks => $vls){
				foreach($vls as $field => $values){
					if(isset($where[$field])){
						$arr_set['field'][$ks][$field]['default'] = $where[$field]['values'];
					}
				}
			}
		}
		//end parent
	}



	//allocation
	public function allocation(){
		$ret = array();
		$total_allocated = 0;
		$option_select=array();
		if($this->get_id()!=''){
			$arr_tmp = $this->opm->select_one(array('_id' => new MongoId($this->get_id())));
			if(isset($arr_tmp['allocation']) && !empty($arr_tmp['allocation'])){
				foreach($arr_tmp['allocation'] as $key=>$allocation){
					if(isset($allocation['deleted'])&&$allocation['deleted']==true) continue;
					if(!isset($allocation['salesinvoice_code']))
						$allocation['salesinvoice_code'] = '';
					$ret[$key] = $allocation;
					$ret[$key]['salesinvoice_code_lock_field'] = true;
					$ret[$key]['salesinvoice_code_name'] = $allocation['salesinvoice_code'];
					$total_allocated	   += $allocation['amount'];
				}
			}
		}
		$this->set('total_allocated',$total_allocated);
		return $ret;
	}

	//outstanding
	public function outstanding($company_id=''){
		$total_all = 0;
		$balance_all = 0;
		$receipt_all = 0;
		$current_date = strtotime(date('d-m-y'));
		$salesinvoice_code = array();
		if($company_id!='')
			$allocation=$this->sum_allocated($company_id);
		else
			$allocation = array();
		$obj_invoice = $this->outstanding_data($company_id);
		$arr = array();
		$arr_companies = Cache::read('arr_companies');
	    if(!$arr_companies)
	        $arr_companies = array();
		$this->selectModel('Company');
		$orgirin_minimum = Cache::read('minimum');
    	$product = Cache::read('minimum_product');
    	if(!$orgirin_minimum){
	        $this->selectModel('Product');
        	$this->selectModel('Stuffs');
	        $orgirin_minimum = 50;
	        $product = $this->Stuffs->select_one(array('value'=>"Minimum Order Adjustment"),array('product_id'));
	        $p = $this->Product->select_one(array('_id'=> new MongoId($product['product_id'])),array('sell_price'));
	        if(isset($p['sell_price']))
	            $orgirin_minimum = (float)$p['sell_price'];
	        Cache::write('minimum',$orgirin_minimum);
	        Cache::write('minimum_product',$product);
	    }
	    $query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())),array('inactive_outstading'));
	    if(!isset($query['inactive_outstading']))
	    	$query['inactive_outstading'] = array();
		if($obj_invoice->count()){
			foreach($obj_invoice as $key=>$invoice){
				$arr[$key]['code'] = $invoice['code'];
				$arr[$key]['invoice_type'] = $invoice['invoice_type'];
				if(isset($invoice['invoice_date']) && is_object($invoice['invoice_date']))
					$arr[$key]['invoice_date'] = $this->opm->format_date($invoice['invoice_date']->sec);
				if(isset($invoice['payment_due_date']) && is_object($invoice['payment_due_date'])){
					$arr[$key]['due'] = 0;
					if($current_date > $invoice['payment_due_date']->sec)
						$arr[$key]['due'] = 1;
				}
				//id saleinvoice
				$ids = (string)$invoice['_id'];
				$arr[$key]['_id'] = $key;
				$arr[$key]['total'] 		= 0;
				$arr[$key]['receipts'] 	= 0;
				$arr[$key]['balance'] 	= 0;
				$allocation[$ids] = isset($allocation[$ids]) ? $allocation[$ids] : 0;
				//find receipts
				if($invoice['invoice_status'] == 'Credit'){
					if($invoice['taxval']  > 0)
						$invoice['taxval'] *= -1;
					if($invoice['sum_amount']  > 0)
						$invoice['sum_amount'] *= -1;
					if($invoice['sum_sub_total']  > 0)
						$invoice['sum_sub_total'] *= -1;
				} else {
					if(!isset($arr_companies[(string)$invoice['company_id']])){
                    	$minimum = $orgirin_minimum;
                    	if(is_object($invoice['company_id'])){
	                        $company = $this->Company->select_one(array('_id'=>new MongoId($invoice['company_id'])),array('pricing'));
	                        if(isset($company['pricing'])&&!empty($company['pricing'])){
	                            foreach($company['pricing'] as $pricing){
	                                if(isset($pricing['deleted'])&&$pricing['deleted']) continue;
	                                if((string)$pricing['product_id']!=(string)$product['product_id']) continue;
	                                if(!isset($pricing['price_break']) || empty($pricing['price_break'])) continue;
	                                $price_break = reset($pricing['price_break']);
	                                $minimum = (float)$price_break['unit_price']; break;
	                            }
	                        }
	                    } else
                        	$minimum = $orgirin_minimum;
                    	$arr_companies[(string)$invoice['company_id']] = $minimum;
                    } else
                    	$minimum = $arr_companies[(string)$invoice['company_id']];
                    $minimum += isset($invoice['shipping_cost']) ? $invoice['shipping_cost'] : 0;
					if($invoice['sum_sub_total']<$minimum){
						$invoice['taxval'] = (isset($invoice['taxval']) ? $invoice['taxval'] : 0);
						$invoice['sum_amount'] = $minimum + ($minimum*$invoice['taxval']/100);
					}
				}
				$arr[$key]['receipts'] 	= $allocation[$ids];
				$arr[$key]['total'] = $invoice['sum_amount'];
				$arr[$key]['balance'] = round($arr[$key]['total'] - $arr[$key]['receipts'] ,2);
				if($arr[$key]['balance'] < 0.02 && $arr[$key]['balance'] > -0.01){
					$arr[$key]['deleted'] = true;
					continue;
				}
				if(in_array($invoice['_id'],$query['inactive_outstading'])){
					$arr[$key]['inactive'] = 1;
					$arr[$key]['xcss'] = 'background-color: rgb(247, 215, 134)';
					continue;
				}
				$total_all += $arr[$key]['total'];
				//select option
				if(isset($invoice['code']) )
					$salesinvoice_code[$invoice['code']] = $invoice['code'];

				$receipt_all += $arr[$key]['receipts'];
				$balance_all += $arr[$key]['balance'];
				//pr($balance_all);

				$arr[$key]['receipt_this'] = '<span class="icon_empleft add_receipt_link" title="Add receipt allocation" id="'.$ids.'" onclick="confirm_add(\''.$ids.'\');"></span>';
				if(isset($arr[$key]['invoice_type']))
					$arr[$key]['invoice_type'] .= '<a href="'.URL.'/salesinvoices/entry/'.$ids.'"><span class="icon_linkleft" title="View" onclick=""></span></a>';
				else
					$arr[$key]['invoice_type'] = '<a href="'.URL.'/salesinvoices/entry/'.$ids.'"><span class="icon_linkleft" title="View" onclick=""></span></a>';
			}
		}
		$balance_all = round($balance_all,2);
		$option_select = array();
		$option_select['salesinvoice_code'] = $salesinvoice_code;
		$this->set('option_select',$option_select);
		$this->set('total',$total_all);
		$this->set('receipts',$receipt_all);
		$this->set('balance',$balance_all);
		// pr($option_select);
		//die;
		return $arr;
	}

	//outstanding data
	public function outstanding_data($company_id=''){
		$arr_query = array();
		if($company_id!=''){
			$company_id = new MongoId($company_id);
			$this->selectModel('Salesinvoice');
			$arr_query = $this->Salesinvoice->select_all(array(
				'arr_where' => array(
					'company_id' => $company_id,
					'invoice_status'=>array('$in' => array('Invoiced','Credit')),
				),
				'arr_field' => array('_id','code','company_id','company_name','invoice_date','invoice_type','invoice_status','name','payment_due_date','payment_terms','sum_amount','sum_sub_total','taxval', 'shipping_cost'),
				'arr_order' => array('_id' => -1)
			));
		}
		return $arr_query;
	}

	public function sum_allocated($company_id=''){
		$datas = array();
		if($company_id!=''){
			if(!is_object($company_id))
				$company_id = new MongoId($company_id);
			$arr_query = $this->opm->select_all(array(
				'arr_where' => array(
						'company_id' => $company_id,
				),
				'arr_order' => array('_id' => -1)
			));
			$ret = array();
			foreach($arr_query as $kss=>$vss){
				if(isset($vss['allocation']) && !empty($vss['allocation'])){
					foreach($vss['allocation'] as $kk=>$vv){
						if(isset($vv['deleted'])&&$vv['deleted']) continue;
						if(isset($vv['salesinvoice_id']) && is_object($vv['salesinvoice_id'])){
							$ids = (string)$vv['salesinvoice_id'];
						}else
							$ids = $kss;
						if(isset($datas[$ids]) && isset($vv['amount']))
							$datas[$ids] += (float)$vv['amount'];
						else if(isset($vv['amount']))
							$datas[$ids] = (float)$vv['amount'];
					}
				}
			}
		}
		return $datas;
	}


	public function reload_box($boxname=''){
		if(!$this->check_permission($this->name.'_@_entry_@_edit') || !$this->check_permission($this->name.'_@_entry_@_add'))
			die;
		if(isset($_POST['boxname']))
			$boxname = $_POST['boxname'];

		if($this->get_id()!=''){
			$arr_tmp = $this->opm->select_one(array('_id' => new MongoId($this->get_id())));
			$subdatas = array(); $panel = 'panel_2';
			if($boxname=='outstanding'){
				$panel = 'panel_3';
				$paid_received = array();
				if(isset($arr_tmp['allocation']) && count($arr_tmp['allocation'])>0)
					$allocation = $arr_tmp['allocation'];
				if(isset($arr_tmp['company_id']))
					$subdatas[$boxname] = $this->outstanding($arr_tmp['company_id'],$allocation);
				else
					$subdatas[$boxname] = array();

			}else if($boxname=='allocation'){
				$panel = 'panel_2';
				if(isset($arr_tmp['allocation']) && count($arr_tmp['allocation'])>0)
					$subdatas[$boxname] = $this->allocation();
				else
					$subdatas[$boxname] = array();

				//Set outstanding again
				$subdatas['outstanding'] = array();
				if(isset($arr_tmp['company_id'])){
					$subdatas['outstanding'] = $this->outstanding($arr_tmp['company_id']);
				}
			}
			$arr_settings = $this->opm->arr_settings;


			if(isset($arr_tmp['amount_received']))
			$this->set('amount_allocated',(float)$arr_tmp['amount_received']);

			$this->set('blockname', $boxname);
			$this->set('arr_subsetting',$arr_settings['field'][$panel]['block']);
			$this->set('subdatas', $subdatas);
			$this->set('box_type', $arr_settings['field'][$panel]['block'][$boxname]['type']);
		}else
			die;
	}



	//Khi $field thay đổi thì các field này cũng thay đổi theo
	public function arr_associated_data($field='',$value='',$ids='',$field_change = ''){
		if(!$this->check_permission($this->name.'_@_entry_@_edit')){
			echo 'You do not have permission on this action.';
			die;
		}
		$arr_return = array();
		if($field=='salesaccount_name'){
			$receipt = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())),array('company_id','amount_received'));
			$arr_value = (array)json_decode($value);
			$salesaccount_id = (array)$arr_value['_id'];
			$salesaccount_id = $salesaccount_id['$id'];
			$arr_return['salesaccount_id'] = new MongoId($salesaccount_id);
			$this->selectModel('Salesaccount');
			if(isset($arr_value['company_id'])&&is_object($arr_value['company_id'])){
				//Nếu có company cũ
				if(isset($receipt['company_id']))
					$this->Salesaccount->update_account($receipt['company_id'], array(
													'model' => 'Company',
													'balance' => (isset($receipt['amount_received']) ? (float)$receipt['amount_received'] : 0) ,
													'receipts' => -(isset($receipt['amount_received']) ? (float)$receipt['amount_received'] : 0),
													));
				$company_id = (array)$arr_value['company_id'];
				$company_id = $company_id ['$id'];
				$arr_return['company_id'] = $company_id;
				$this->Salesaccount->update_account($arr_return['company_id'], array(
													'model' => 'Company',
													'balance' => -(isset($receipt['amount_received']) ? (float)$receipt['amount_received'] : 0) ,
													'receipts' => (isset($receipt['amount_received']) ? (float)$receipt['amount_received'] : 0),
													));

				$arr_return['salesaccount_name'] = $arr_return['company_name'] = $this->get_name('Company',$arr_return['company_id'],'name');
				$arr_return['company_id'] = new MongoId($arr_return['company_id']);
			}
			return $arr_return;
		} else if( $field=='allocation') {
			$this->selectModel('Salesinvoice');
			$receipt = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())),array('amount_received','allocation'));
			if($field_change == 'salesinvoice_code'){
				$salesinvoice_code = $value[$ids]['salesinvoice_code'];
				$invoice = $this->Salesinvoice->select_one(array('code'=>$salesinvoice_code),array('total_receipt','sum_amount','company_id','sum_sub_total','taxval','job_id','invoice_status','invoice_status_old', 'shipping_cost'));
				$value[$ids]['salesinvoice_id'] = $invoice['_id'];
				if($invoice['invoice_status'] != 'Credit') {
					$this->selectModel('Company');
					$original_minimum = Cache::read('minimum');
    				$product = Cache::read('minimum_product');
    				$arr_companies = Cache::read('arr_companies');
				    if(!$arr_companies)
				        $arr_companies = array();
    				if(!$original_minimum){
				        $this->selectModel('Product');
				        $this->selectModel('Stuffs');
				        $original_minimum = 50;
				        $product = $this->Stuffs->select_one(array('value'=>"Minimum Order Adjustment"),array('product_id'));
				        $p = $this->Product->select_one(array('_id'=> new MongoId($product['product_id'])),array('sell_price'));
				        if(isset($p['sell_price']))
				            $original_minimum = (float)$p['sell_price'];
				        Cache::write('minimum',$original_minimum);
				        Cache::write('minimum_product',$product);
				    }
					if(!isset($arr_companies[(string)$invoice['company_id']])){
		                $minimum = $original_minimum;
		                if(is_object($invoice['company_id'])){
		                    $company = $this->Company->select_one(array('_id'=>new MongoId($invoice['company_id'])),array('pricing'));
		                    if(isset($company['pricing'])&&!empty($company['pricing'])){
		                        foreach($company['pricing'] as $pricing){
		                            if(isset($pricing['deleted'])&&$pricing['deleted']) continue;
		                            if((string)$pricing['product_id']!=(string)$product['product_id']) continue;
		                            if(!isset($pricing['price_break']) || empty($pricing['price_break'])) continue;
		                            $price_break = reset($pricing['price_break']);
		                            $minimum = (float)$price_break['unit_price']; break;
		                        }
		                    }
		                } else {
		                    $minimum = $original_minimum;
		                }
		                $arr_companies[(string)$invoice['company_id']] = $minimum;
		            } else
		                $minimum = $arr_companies[(string)$invoice['company_id']];
		            $minimum += isset($invoice['shipping_cost']) ? $invoice['shipping_cost'] : 0;
					if($invoice['sum_sub_total']<$minimum){
						$invoice['taxval'] = (isset($invoice['taxval']) ? $invoice['taxval'] : 0);
						$invoice['sum_amount'] = $minimum + ($minimum*$invoice['taxval']/100);
					}
				}
				$invoice['invoice_status_old'] = $invoice['invoice_status'];
				if($invoice['invoice_status'] == 'Credit' ) {
					if($invoice['sum_amount'] > 0)
						$invoice['sum_amount'] *= -1;
					if($value[$ids]['amount'] > 0)
						$value[$ids]['amount'] *= -1;
				}
				if($value[$ids]['mod'] == 'Fully') {
					$invoice['invoice_status'] = 'Paid';
				}
				else if($value[$ids]['mod'] == 'Part'
				   	&&isset($value[$ids]['salesinvoice_id']) && is_object($value[$ids]['salesinvoice_id'])){
					$total_amount = 0;
					foreach($value as $allocation){
						if(isset($allocation['deleted']) && $allocation['deleted']) continue;
						if(!isset($allocation['salesinvoice_id']) || !is_object($allocation['salesinvoice_id'])) continue;
						if($allocation['salesinvoice_id'] != $value[$ids]['salesinvoice_id']) continue;
						$total_amount += (isset($allocation['amount']) ? (float)$allocation['amount'] : 0);
					}
					if($invoice['invoice_status'] == 'Credit') {
						if($invoice['sum_amount'] >= $total_amount){
								$value[$ids]['amount'] = -$invoice['sum_amount'] + ($total_amount + $value[$ids]['amount']);
							$invoice['invoice_status'] = 'Paid';
						}
					} else {
						if($total_amount >= $invoice['sum_amount']
						   	&&isset($value[$ids]['amount']) && $value[$ids]['amount'] > 0){
								$value[$ids]['amount'] = $invoice['sum_amount'] - ($total_amount - $value[$ids]['amount']);
							$invoice['invoice_status'] = 'Paid';
						}
					}
				}
				if(isset($receipt['allocation'][$ids]['salesorder_code'])
				   &&is_object($receipt['allocation'][$ids]['salesorder_code'])
				   &&$receipt['allocation'][$ids]['salesorder_code']!=$value[$ids]['salesorder_code']){
					$old_invoice = $this->Salesinvoice->select_one(array('_id'=>new MongoId($receipt['allocation'][$ids]['salesorder_id'])),array('total_receipt'));
					$old_invoice['total_receipt'] -= (float)$receipt['allocation'][$ids]['amount'];
					$this->Salesinvoice->save($old_invoice);
				}
				if($invoice['invoice_status'] == 'Paid') {
					if(is_object($invoice['job_id'])){
						$this->selectModel('Job');
						$this->Job->save(array('_id' => $invoice['job_id'],'status' => 'Paid'));
					}
					$invoice['total_receipt'] = ($invoice['sum_amount'] < 0 ? -$invoice['sum_amount'] : $invoice['sum_amount']);
				}
				else
					$invoice['total_receipt'] = ($total_amount < 0 ? -$total_amount : $total_amount);
				if($invoice['sum_amount'] < 0)
					$invoice['sum_amount'] *= -1;
				$this->Salesinvoice->save($invoice);
				$arr_return[$field] = $value;
			} else if($field_change == 'amount'){
				if(isset($value[$ids]['salesinvoice_id'])&&is_object($value[$ids]['salesinvoice_id'])){
					$invoice = $this->Salesinvoice->select_one(array('_id'=>new MongoId($value[$ids]['salesinvoice_id'])),array('total_receipt','sum_amount','company_id','taxval','sum_sub_total','invoice_status','job_id','invoice_status_old', 'shipping_cost'));
					if($invoice['invoice_status'] != 'Credit') {
						$this->selectModel('Company');
						$original_minimum = Cache::read('minimum');
	    				$product = Cache::read('minimum_product');
	    				$arr_companies = Cache::read('arr_companies');
					    if(!$arr_companies)
					        $arr_companies = array();
	    				if(!$original_minimum){
					        $this->selectModel('Product');
					        $this->selectModel('Stuffs');
					        $original_minimum = 50;
					        $product = $this->Stuffs->select_one(array('value'=>"Minimum Order Adjustment"),array('product_id'));
					        $p = $this->Product->select_one(array('_id'=> new MongoId($product['product_id'])),array('sell_price'));
					        if(isset($p['sell_price']))
					            $original_minimum = (float)$p['sell_price'];
					        Cache::write('minimum',$original_minimum);
					        Cache::write('minimum_product',$product);
					    }
						if(!isset($arr_companies[(string)$invoice['company_id']])){
			                $minimum = $original_minimum;
			                if(is_object($invoice['company_id'])){
			                    $company = $this->Company->select_one(array('_id'=>new MongoId($invoice['company_id'])),array('pricing'));
			                    if(isset($company['pricing'])&&!empty($company['pricing'])){
			                        foreach($company['pricing'] as $pricing){
			                            if(isset($pricing['deleted'])&&$pricing['deleted']) continue;
			                            if((string)$pricing['product_id']!=(string)$product['product_id']) continue;
			                            if(!isset($pricing['price_break']) || empty($pricing['price_break'])) continue;
			                            $price_break = reset($pricing['price_break']);
			                            $minimum = (float)$price_break['unit_price']; break;
			                        }
			                    }
			                } else {
			                    $minimum = $original_minimum;
			                }
			                $arr_companies[(string)$invoice['company_id']] = $minimum;
			            } else
			                $minimum = $arr_companies[(string)$invoice['company_id']];
			            $minimum += isset($invoice['shipping_cost']) ? $invoice['shipping_cost'] : 0;
						if($invoice['sum_sub_total']<$minimum){
							$invoice['taxval'] = (isset($invoice['taxval']) ? $invoice['taxval'] : 0);
							$invoice['sum_amount'] = $minimum + ($minimum*$invoice['taxval']/100);
						}
					}
					$invoice['invoice_status_old'] = $invoice['invoice_status'];
					if($value[$ids]['mod'] == 'Part'){
						if($invoice['invoice_status'] == 'Credit' ) {
							if($invoice['sum_amount'] > 0)
								$invoice['sum_amount'] *= -1;
							if($value[$ids]['amount'] > 0)
								$value[$ids]['amount'] *= -1;
						}
						$total_amount = 0;
						foreach($value as $key => $allocation){
							if(isset($allocation['deleted']) && $allocation['deleted']) continue;
							if(!isset($allocation['salesinvoice_id']) || !is_object($allocation['salesinvoice_id'])) continue;
							if($allocation['salesinvoice_id'] != $value[$ids]['salesinvoice_id']) continue;
							$total_amount += (isset($allocation['amount']) ? (float)$allocation['amount'] : 0);
						}
						if($invoice['invoice_status'] == 'Credit') {
							if($invoice['sum_amount'] >= $total_amount){
								$value[$ids]['amount'] = -$invoice['sum_amount'] + ($total_amount + $value[$ids]['amount']);
								$invoice['invoice_status'] = 'Paid';
							}
						} else {
							if($total_amount >= $invoice['sum_amount']
							   	&&isset($value[$ids]['amount']) && $value[$ids]['amount'] > 0){
								$value[$ids]['amount'] = $invoice['sum_amount'] - ($total_amount - $value[$ids]['amount']);
								$invoice['invoice_status'] = 'Paid';
							}
						}
					}
					if($invoice['invoice_status'] == 'Paid') {
						if(is_object($invoice['job_id'])){
							$this->selectModel('Job');
							$this->Job->save(array('_id' => $invoice['job_id'],'status' => 'Paid'));
						}
						$invoice['total_receipt'] = ($invoice['sum_amount'] < 0 ? -$invoice['sum_amount'] : $invoice['sum_amount']);
					}
					else
						$invoice['total_receipt'] = ($total_amount < 0 ? -$total_amount : $total_amount);
					if($invoice['sum_amount'] < 0)
						$invoice['sum_amount'] *= -1;
					$this->Salesinvoice->save($invoice);
					$arr_return[$field] = $value;
				}
			} else if($field_change == 'outstanding'){
				if($value[$ids]['mod'] == 'Fully'){
					$invoice = $this->Salesinvoice->select_one(array('_id'=>$value[$ids]['salesinvoice_id']),array('total_receipt','sum_sub_total','sum_amount','company_id','taxval','invoice_status','job_id', 'shipping_cost'));
					$invoice['invoice_status_old'] = $invoice['invoice_status'];
					if($invoice['invoice_status'] != 'Credit'){
						$this->selectModel('Company');
						$original_minimum = Cache::read('minimum');
	    				$product = Cache::read('minimum_product');
	    				$arr_companies = Cache::read('arr_companies');
					    if(!$arr_companies)
					        $arr_companies = array();
	    				if(!$original_minimum){
					        $this->selectModel('Product');
					        $this->selectModel('Stuffs');
					        $original_minimum = 50;
					        $product = $this->Stuffs->select_one(array('value'=>"Minimum Order Adjustment"),array('product_id'));
					        $p = $this->Product->select_one(array('_id'=> new MongoId($product['product_id'])),array('sell_price'));
					        if(isset($p['sell_price']))
					            $original_minimum = (float)$p['sell_price'];
					        Cache::write('minimum',$original_minimum);
					        Cache::write('minimum_product',$product);
					    }
						if(!isset($arr_companies[(string)$invoice['company_id']])){
			                $minimum = $original_minimum;
			                if(is_object($invoice['company_id'])){
			                    $company = $this->Company->select_one(array('_id'=>new MongoId($invoice['company_id'])),array('pricing'));
			                    if(isset($company['pricing'])&&!empty($company['pricing'])){
			                        foreach($company['pricing'] as $pricing){
			                            if(isset($pricing['deleted'])&&$pricing['deleted']) continue;
			                            if((string)$pricing['product_id']!=(string)$product['product_id']) continue;
			                            if(!isset($pricing['price_break']) || empty($pricing['price_break'])) continue;
			                            $price_break = reset($pricing['price_break']);
			                            $minimum = (float)$price_break['unit_price']; break;
			                        }
			                    }
			                } else {
			                    $minimum = $original_minimum;
			                }
			                $arr_companies[(string)$invoice['company_id']] = $minimum;
			            } else
			                $minimum = $arr_companies[(string)$invoice['company_id']];
			            $minimum += isset($invoice['shipping_cost']) ? $invoice['shipping_cost'] : 0;
						if($invoice['sum_sub_total']<$minimum){
							$invoice['taxval'] = (isset($invoice['taxval']) ? $invoice['taxval'] : 0);
							$invoice['sum_amount'] = $minimum + ($minimum*$invoice['taxval']/100);
						}
					}
					$value[$ids]['amount'] = 0;
					$total_amount = 0;
					foreach($value as $key => $allocation){
						if(isset($allocation['deleted']) && $allocation['deleted']) continue;
						if(!isset($allocation['salesinvoice_id']) || !is_object($allocation['salesinvoice_id'])) continue;
						if($allocation['salesinvoice_id'] != $value[$ids]['salesinvoice_id']) continue;
						$total_amount += (isset($allocation['amount']) ? (float)$allocation['amount'] : 0);
					}
					if($invoice['invoice_status'] == 'Credit') {
						$value[$ids]['amount'] = -($invoice['sum_amount'] + $total_amount);
					} else {
						$value[$ids]['amount'] = $invoice['sum_amount'] - $total_amount;
					}
					$invoice['invoice_status'] = 'Paid';
					$invoice['total_receipt'] = ($invoice['sum_amount']<0 ? -$invoice['sum_amount'] : $invoice['sum_amount']);
					$this->Salesinvoice->save($invoice);
					if($invoice['invoice_status'] == 'Paid' && is_object($invoice['job_id'])){
						$this->selectModel('Job');
						$this->Job->save(array('_id' => $invoice['job_id'],'status' => 'Paid'));
					}
					$arr_return[$field] = $value;
				}
			}else if($field == 'our_csr'){
				 $arr_return['our_csr_id'] = new MongoId($ids);
			}else if($field == 'our_rep'){
				$arr_return['our_rep_id'] = new MongoId($ids);
			}
			$total_allocated = $unallocated = 0;
			$receipt['allocation'] = $value;
			foreach($receipt['allocation'] as $allocation){
				if(isset($allocation['deleted']) && $allocation['deleted']) continue;
				if(!isset($allocation['amount'])) continue;
				$total_allocated += $allocation['amount'];
			}
			$unallocated = (isset($receipt['amount_received']) ? (float)$receipt['amount_received'] : 0);
			$unallocated -= $total_allocated;
			$arr_return['total_allocated'] = $total_allocated;
			$arr_return['unallocated'] = $unallocated;
		}
		$arr_return[$field] = $value;
		return $arr_return;
	}
	function ajax_save(){
		if (isset($_POST['field']) && $_POST['field'] == "amount_received"){
			$amount_received = $_POST['value'];
			$receipt = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())),array('amount_received','company_id','total_allocated'));
			$receipt['unallocated'] = $amount_received - (isset($receipt['total_allocated']) ? $receipt['total_allocated'] : 0);
			$this->opm->save($receipt);
			$this->selectModel('Salesaccount');
			if(isset($receipt['company_id']) && is_object($receipt['company_id'])){
				$this->Salesaccount->update_account($receipt['company_id'], array(
												'model' 	=> 'Company',
												'balance' 	=> $amount_received - (isset($receipt['amount_received']) ? (float)$receipt['amount_received'] : 0) ,
												'receipts' 	=> $amount_received - (isset($receipt['amount_received']) ? (float)$receipt['amount_received'] : 0),
												));
			}
		}
		parent::ajax_save();
	}
	function delete_allocation(){
		if(isset($_POST['allocation_key'])){
			$allocation_key = $_POST['allocation_key'];
			$receipt = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())),array('allocation','amount_received'));
			$salesinvoice_id = $receipt['allocation'][$allocation_key]['salesinvoice_id'];
			if(!is_object($salesinvoice_id)){
				$total_allocated = $unallocated = 0;
				$receipt['allocation'][$allocation_key] = array('deleted'=>true);
				foreach($receipt['allocation'] as $allocation){
					if(isset($allocation['deleted']) && $allocation['deleted']) continue;
					$total_allocated += (isset($allocation['amount']) ? (float)$allocation['amount'] : 0);
				}
				$unallocated = (isset($receipt['amount_received']) ? (float)$receipt['amount_received'] : 0);
				if($this->opm->save($receipt)){
					echo json_encode(array('total_allocated'=>$total_allocated,'unallocated'=>$unallocated));
				}
				die;
			}
			$this->selectModel('Salesinvoice');
			$salesinvoice = $this->Salesinvoice->select_one(array('_id'=>$salesinvoice_id),array('total_receipt','invoice_status_old'));
			if(!isset($salesinvoice['total_receipt']))
				$salesinvoice['total_receipt'] = 0;
			$salesinvoice['total_receipt'] -= $receipt['allocation'][$allocation_key]['amount'];
			if(isset($receipt['allocation'][$allocation_key]['mod']) && $receipt['allocation'][$allocation_key]['mod'] == "Fully") {
				if(!isset($salesinvoice['invoice_status_old']))
					$salesinvoice['invoice_status']  = 'Invoiced';
				else
					$salesinvoice['invoice_status'] = $salesinvoice['invoice_status_old'];
			}
			else {
				if($receipt['allocation'][$allocation_key]['amount'] > 0){
					if(!isset($salesinvoice['invoice_status_old']))
						$salesinvoice['invoice_status']  = 'Invoiced';
					else
						$salesinvoice['invoice_status'] = $salesinvoice['invoice_status_old'];
				} else {
					$empty = true;
					foreach($receipt['allocation'] as $allocation){
						if(isset($allocation['deleted'])&&$allocation['deleted']) continue;
						if($allocation['salesinvoice_id']==$salesinvoice_id){
							$empty = false;
							break;
						}
					}
					if($empty) {
						if(!isset($salesinvoice['invoice_status_old']))
							$salesinvoice['invoice_status']  = 'Invoiced';
						else
							$salesinvoice['invoice_status'] = $salesinvoice['invoice_status_old'];
					}
				}
			}
			$total_allocated = $unallocated = 0;
			$receipt['allocation'][$allocation_key] = array('deleted'=>true);
			foreach($receipt['allocation'] as $allocation){
				if(isset($allocation['deleted']) && $allocation['deleted']) continue;
				$total_allocated += (isset($allocation['amount']) ? (float)$allocation['amount'] : 0);
			}
			$unallocated = (isset($receipt['amount_received']) ? (float)$receipt['amount_received'] : 0);
			$salesinvoice['total_receipt'] -= (isset($receipt['allocation'][$allocation_key]['amount']) ? (float)$receipt['allocation'][$allocation_key]['amount'] : 0);
			if($this->Salesinvoice->save($salesinvoice)){
				if($this->opm->save($receipt)){
					echo json_encode(array('total_allocated'=>$total_allocated,'unallocated'=>$unallocated));
				}
			}
		}
		die;
	}
	public function save_unallocated()
	{
		if(!$this->check_permission($this->name.'_@_entry_@_edit') || !$this->check_permission($this->name.'_@_entry_@_add')){
			echo 'You do not have permission on this action.';
			die;
		}
		if(isset($_POST['unallocated']))
		{
			if(is_numeric($_POST['unallocated']))
			{
				$unallocated = (float)$_POST['unallocated'];
				$this->selectModel('Receipt');
				$receipt = $this->Receipt->select_one(array('_id'=> new MongoId($this->get_id())));
				$receipt['unallocated'] = $unallocated;
				if($this->Receipt->save($receipt))
					echo 'ok';
				else
					echo 'error';

			}
			else
				echo 'is not numeric';
		}
		die;
	}

	public function check_condition_receipt($email=false)
	{
		$receipt = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())));
		if(!isset($receipt['allocation']) || !is_object($receipt['company_id']))
			echo json_encode(array('status'=>'error','message'=>'No allocation have been entered on this receipt yet.'));
		else if(!isset($receipt['unallocated']) || $receipt['unallocated']>= 1)
			echo json_encode(array('status'=>'error','message'=>'The amount receipt does not balance with the total allocated.'));
		else if(!$email)
			echo json_encode(array('status'=>'ok','url'=>$this->print_receipt($receipt)));
		else
			$this->print_receipt($receipt,'',true);
		die;
	}
	public function loading()
	{

	}
	public function print_receipts()
	{
		if(!$this->check_permission($this->name.'_@_options_@_print_receipts'))
			die;
		$file = array();
		$i = 0;
		$receipts = $this->opm->select_all(array('arr_where'=>array('allocation'=>array('$ne'=>''))));
		foreach($receipts as $value)
		{
			if(!empty($value['allocation']))
			{
				$i++;
				$file[] = '<a style="text-decoration:none; color: #b22626;font-weight: bold;font-size: 15px;line-height:25px;font-family: Arial;" target="_blank" href="'.$this->print_receipt($value,'print_receipts').'">Receipt Report '.$i.'</a><br />';
			}

		}
		echo json_encode($file);
		die;
	}
	public function view_pdf($getfile=true){
		$this->autoRender = false;
		$receipt = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())));
		return $this->print_receipt($receipt,'',$getfile);
	}
	public function print_receipt($receipt=array(),$option='',$getfile=false)
	{
		if(!$this->check_permission($this->name.'_@_options_@_print_receipt'))
			die;
		if(!empty($receipt))
		{
			$group = array();
			$this->selectModel('Salesinvoice');
			$this->selectModel('Salesaccount');
			$this->selectModel('Company');
			$company = $this->Company->select_one(array('_id'=> new MongoId($receipt['company_id'])));
			$total = 0;
			if(!empty($receipt['allocation']))
			{
				foreach($receipt['allocation'] as $key=>$value)
				{
					if(isset($value['deleted'])&&$value['deleted']==true) continue;
					if(isset($value['salesinvoice_id'])&&is_object($value['salesinvoice_id']))
					{
						$amount = (isset($value['amount'])&&$value['amount']!='' ? $value['amount']: 0);
						$total += $amount;
						$si = $this->Salesinvoice->select_one(array('_id'=>new MongoId($value['salesinvoice_id'])),array('invoice_date','sum_amount','code','invoice_type','customer_po_no'));
						$group[$key]['date'] = (isset($si['invoice_date'])&&$si['invoice_date']!='' ? date('M d, Y',$si['invoice_date']->sec) : '');
						$group[$key]['code'] = $si['code'];
						$group[$key]['type'] = $si['invoice_type'];
						$group[$key]['po_no'] = isset($si['customer_po_no']) ? $si['customer_po_no'] : '';
						$group[$key]['amount'] = (isset($si['sum_amount']) ? $this->opm->format_currency((float)$si['sum_amount']) : 0);
						$group[$key]['paid'] = $this->opm->format_currency($amount);
					}
				}
				if($option=='print_receipts')
					$group['total'] = $this->opm->format_currency($total);
				else
					$group['total'] = (isset($receipt['amount_received'])&&$receipt['amount_received']!= '' ? $this->opm->format_currency($receipt['amount_received']) : 0);
				$salesaccount = $this->Salesaccount->select_one(array('company_id'=>new MongoId($receipt['company_id'])),array('balance'));
				$group['account_balance'] = (isset($salesaccount['balance'])&&$salesaccount['balance']!='' ? $salesaccount['balance'] : 0);
				$group['account_balance'] = $this->opm->format_currency($group['account_balance']);
				$html_loop = '<table cellpadding="3" cellspacing="0" class="maintb">
								<tr>
			                     <td width="15%" class="first top">
			                        Date
			                     </td>
			                     <td width="10%" class="top">
			                        Ref #
			                     </td>
			                     <td width="15%" class="top" align="left">
			                        Type
			                     </td>
			                     <td width="10%" class="top" align="left">
			                        PO no
			                     </td>
			                     <td width="25%" class="top" align="right">
			                        Amount
			                     </td>
			                     <td width="25%" colspan="3" class="end top" align="right">
			                        Paid
			                     </td>
			                  </tr>
				';
				$i = 0;
				foreach($group as $value)
				{
					if(is_array($value))
					{
						$color = ($i % 2 == 0 ? '#fdfcfa': '#eeeeee');
						$html_loop .= '
							<tr style="background-color:'.$color.'">
								<td class="first content">'.$value['date'].'</td>
								<td class="content">'.$value['code'].'</td>
								<td class="content">'.$value['type'].'</td>
								<td class="content" align="right">'.$value['po_no'].'</td>
								<td class="content" align="right">'.$value['amount'].'</td>
								<td colspan="3" class="content" align="right">'.$value['paid'].'</td>
							</tr>
						';
						$i++;
					}
				}
				$color = ($i % 2 == 0 ? '#fdfcfa': '#eeeeee');
				$html_loop .= '
						<tr style="background-color:'.$color.'">
							<td class="first" colspan="5" align="right"><strong>Total:</strong></td>
							<td class="end" colspan="3" align="right">'.$group['total'].'</td>
						</tr>';
				$html_loop .='
						<tr style="background-color:'.$color.'">
							<td class="first bottom" colspan="2" align="left">'.$i.' record(s) listed.</td>
							<td class="bottom" colspan="3" align="right"><strong>Account Balance:</strong></td>
							<td class="end bottom" colspan="3" align="right">'.$group['account_balance'].'</td>
						</tr>
					</table>';
				//========================================
				$pdf['customer_name'] = '<span style="color:#b32017">'.$company['name'].'</span>';
				$address_key = (isset($company['addresses_default_key'])? $company['addresses_default_key'] : 0);
				$address_tmp = (isset($company['addresses'][$address_key]) ?  $company['addresses'][$address_key] : '');
				$address='';
				if($address_tmp!=''){
					$address = (isset($address_tmp['address_1']) && $address_tmp['address_1']!=''? $address_tmp['address_1'].' ' : '').(isset($address_tmp['address_2']) && $address_tmp['address_2']!=''?$address_tmp['address_2'].' ' : '').(isset($address_tmp['address_3']) && $address_tmp['address_3']!=''? $address_tmp['address_3'].', ' : '').(isset($address_tmp['province_state']) && $address_tmp['province_state']!='' ? $address_tmp['province_state'].', ': '').(isset($address_tmp['town_city']) && $address_tmp['town_city']!='' ? $address_tmp['town_city'].', ':'').(isset($address_tmp['country']) && $address_tmp['country']!='' ? $address_tmp['country'] : '');
				}
				$pdf['customer_address'] = $address;
		        $pdf['current_time'] = date('h:i a m/d/Y');
		        $pdf['title'] = '<span style="color:#b32017">R</span>eceipt <span style="color:#b32017">R</span>eport';
		        $this->layout = 'pdf';
		        //set header
		        $pdf['logo_link'] = 'img/logo_anvy.jpg';
		        $pdf['company_address'] = '3145 - 5th Ave NE<br />Calgary  AB  T2A  6A3<br />';
		        $pdf['html_loop'] = $html_loop;
		        $pdf['filename'] = 'RE_' . md5($pdf['current_time'].$company['_id']);
		        $pdf['code'] = $receipt['code'];
		        $pdf['date'] = date('M d, Y',$receipt['receipt_date']->sec);
		        $this->report_pdf($pdf);
		        if($getfile)
		        	return $pdf['filename'].'.pdf';
		        return URL.'/upload/' . $pdf['filename'] . '.pdf';
	    	}
		}
		return URL.'/receipts/entry';
		die;
	}
	public function update_receipt_salesaccount(){
		if(isset($_POST['salesaccount_id'])){
			$value = (float)$_POST['value'];
			$receipt = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())));
			$this->selectModel('Salesaccount');
			//Nếu là company
			if(isset($receipt['company_id']))
				$this->Salesaccount->update_account($receipt['company_id'], array(
												'model' => 'Company',
												'balance' => $value - (isset($receipt['amount_received']) ? (float)$receipt['amount_received'] : 0) ,
												'receipts' => $value - (isset($receipt['amount_received']) ? (float)$receipt['amount_received'] : 0),
												));
			else if(isset($receipt['contact_id'])) //Nếu là contact
				$this->Salesaccount->update_account($arr_return['contact_id'], array(
												'model' => 'Contact',
												'balance' => $value -  (isset($receipt['amount_received']) ? (float)$receipt['amount_received'] : 0) ,
												'receipts' => $value - (isset($receipt['amount_received']) ? (float)$receipt['amount_received'] : 0),
												));
			echo 'ok';

		}
		die;
	}
	public function update_receipt_salesinvoice()
	{
		if(isset($_POST['si_id']))
		{
			$this->selectModel('Salesinvoice');
			$si = $this->Salesinvoice->select_one(array('_id'=>new MongoId($_POST['si_id'])));
			if($_POST['type']=='plus')
			{
				if(!isset($si['total_receipt']))
					$si['total_receipt'] = (float)$_POST['value'];
				else
					$si['total_receipt'] = $si['total_receipt'] - (float)$_POST['old'] + (float)$_POST['value'];
			}
			else if ($_POST['type']=='minus'){
				$si['total_receipt'] -= $_POST['value'];
			}
			else if($_POST['type']=='update')
			{
				$receipt = $this->opm->select_one(array('_id'=> new MongoId($this->get_id())));
				if(isset($_POST['key'])&&is_numeric($_POST['key']))
				{
					$old = (isset($receipt['allocation'][$_POST['key']]['amount'])&&is_numeric($receipt['allocation'][$_POST['key']]['amount']) ? $receipt['allocation'][$_POST['key']]['amount'] : 0);
					$si['total_receipt'] = $si['total_receipt'] - (float)$old + (float)$_POST['value'];
				}
			}

			if( $this->Salesinvoice->save($si) ){
				echo 'ok';die;
			}
		}
		die;
	}
	public function delete_all_associate($ids='',$opname =''){
		if(!$this->check_permission($this->name.'_@_entry_@_delete')){
			echo 'You do not have permission on this action.';
			die;
		}
		$ids = $this->get_id();
		$receipt = $this->opm->select_one(array('_id'=>new MongoId($ids)),array('amount_received','company_id'));
		$this->selectModel('Salesaccount');
		if(is_object($receipt['company_id']) && isset($receipt['amount_received'])){
			if($receipt['amount_received']=='')
				$receipt['amount_received'] = 0;
			$this->Salesaccount->update_account($receipt['company_id'], array(
												'model' => 'Company',
												'balance' =>  - (isset($receipt['amount_received']) ? (float)$receipt['amount_received'] : 0) ,
												'receipts' =>  - (isset($receipt['amount_received']) ? (float)$receipt['amount_received'] : 0),
												));
		}
	}
	public function report_pdf($data)
	{
		App::import('Vendor', 'xtcpdf');
        $pdf = new XTCPDF();
        $textfont = 'freesans'; // looks better, finer, and more condensed than 'dejavusans'
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Anvy Digital');
        $pdf->SetTitle('Anvy Digital Quotation');
        $pdf->SetSubject('Quotation');
        $pdf->SetKeywords('Quotation, PDF');

        // set default header data
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(true);

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(2);

        // set margins
        $pdf->SetMargins(10, 3, 10);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // ---------------------------------------------------------
        // set font
        $pdf->SetFont($textfont, '', 9);

        // add a page
        $pdf->AddPage();


        // writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
        // writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
        // create some HTML content


        $html = '
        <table cellpadding="2" cellspacing="0" style="width:100%; margin: 0px auto">
           <tbody>
              <tr>
                 <td width="32%" valign="top" style="color:#1f1f1f;">
                    <img src="img/logo_anvy.jpg" alt="" margin-bottom:0px>
                    <p style="margin-bottom:5px; margin-top:0px;">3145 - 5th Ave NE<br/ >Calgary  AB  T2A  6A3</p>
                 </td>
                 <td width="68%" valign="top" align="right">
                    <table>
                       <tbody>
                          <tr>
                             <td width="25%">&nbsp;</td>
                             <td width="75%">
                                <span style="text-align:right; font-size:21px; font-weight:bold; color: #919295;">
                                    ' . $data['title'];
        if (isset($data['date_equals']))
            $date = '<br /><span style="font-size:12px; font-weight:normal">' . $data['date_equals'] . '</span>';
        else {
            if (isset($data['date_from']) && isset($data['date_to']))
                $date = '<br /><span style="font-size:12px; font-weight:normal">( ' . $data['date_from'] . ' - ' . $data['date_to'] . ' )</span>';
            else if (isset($data['date_from']))
                $date = '<br /><span style="font-size:12px; font-weight:normal">From ' . $data['date_from'] . '</span>';
            else if (isset($data['date_to']))
                $date = '<br /><span style="font-size:12px; font-weight:normal">To ' . $data['date_to'] . '</span>';
            else
                $date = '';
        }
        $html .= $date;
        $html .= '
                                </span>
                                <div style=" border-bottom: 1px solid #cbcbcb;height:5px;width:50%">&nbsp;</div>
                             </td>
                          </tr>
                          <tr>
                             <td colspan="2">
                                    <span style="font-weight:bold;">Printed at: </span>' . $data['current_time'] . '
                             </td>
                          </tr>
                       </tbody>
                    </table>
                 </td>
              </tr>';
        if(isset($data['customer_name'])&&$data['customer_name']!='')
        	$html .= '<tr>
        				<td width="50%"><div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both"></div>'.$data['customer_name'].'<br />'.(isset($data['customer_address']) ? $data['customer_address'] : ''
        				).'</td>
        				<td width="50%" align="right"><div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both"></div>'.(isset($data['code'])? 'Ref no: '.$data['code'].'<br />' : '').(isset($data['date']) ? 'Date: '.$data['date'] : '').'</td>
        			</tr>';
        $html .='</tbody>
        </table>';
        if(isset($data['heading'])&&$data['heading']!='')
        	$html .=	'<div class="option">' . $data['heading'] . '</div>
					        <br />
					        <br />';
        $html .= '<div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both"></div>
        <br />
        <style>
           td{
           line-height:2px;
           }
           td.first{
            text-align: center;
           border-left:1px solid #e5e4e3;
           }
           td.end{
           border-right:1px solid #e5e4e3;
           }
           td.top{
           color:#fff;
           text-align: center;
           font-weight:bold;
           background-color:#911b12;
           border-top:1px solid #e5e4e3;
           }
           td.bottom{
           border-bottom:1px solid #e5e4e3;
           }
           td.content{
            border-right: 1px solid #E5E4E3;
            text-align: center;
           }
           .option{
           color: #3d3d3d;
           font-weight:bold;
           font-size:20px;
           text-align: center;
           width:100%;
           }
           table.maintb{
           }
        </style>
        <br />
        ';
        $html .= $data['html_loop'];

        $pdf->writeHTML($html, true, false, true, false, '');



        // reset pointer to the last page
        $pdf->lastPage();



        // ---------------------------------------------------------
        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        //$pdf->Output('example_001.pdf', 'I');




        $pdf->Output('upload/' . $data['filename'] . '.pdf', 'F');
	}
	public function payment_receive_report()
	{
		if(!$this->check_permission($this->name.'_@_options_@_payment_receive_report'))
			die;
		$arr_data = array();
		$arr_data['receipts_paid_by'] = $this->Setting->select_option_vl(array('setting_value' => 'receipts_paid_by'));
		if(isset($_POST)&&!empty($_POST))
		{
			$data = array();
			$arr_where = array();
			parse_str($_POST['data'],$data);
			$data = $this->Common->strip_search($data);
			if(isset($data['company_id'])&&strlen($data['company_id'])==24)
				$arr_where['company_id'] = new MongoId($data['company_id']);
			if(isset($data['company'])&&$data['company']!='')
				$arr_where['company_name'] = new MongoRegex('/'.trim($data['company']).'/i');
			//tim chinh xac ngay
            if (isset($data['date_equals'])&&$data['date_equals'] != '') {
                $date_equals = $data['date_equals'];
                $date_equals = new MongoDate(strtotime(date('Y-m-d', strtotime($date_equals))));
                $date_equals_to = new MongoDate($date_equals->sec + DAY - 1);
                $arr_where['receipt_date']['$gte'] = $date_equals;
                $arr_where['receipt_date']['$lt'] = $date_equals_to;
            }
            //ngay nam trong khoang
            else if (isset($data['date_equals'])&&$data['date_equals'] == '') {
                //neu chi nhap date from
                if (isset($data['date_from'])&&$data['date_from']!='') {
                    $arr_where['receipt_date']['$gte'] =  new MongoDate(strtotime(date('Y-m-d', strtotime($data['date_from']))));
                }
                //neu chi nhap date to
                if (isset($data['date_to'])&&$data['date_to']!='') {
                    $date_to = new MongoDate(strtotime(date('Y-m-d', strtotime($data['date_to']))));
                    $date_to = new MongoDate($date_to->sec + DAY - 1);
                    $arr_where['receipt_date']['$lte'] = $date_to;
                }
            }
            if(isset($data['amount_received'])&&$data['amount_received']!='')
            	$arr_where['amount_received'] = trim($data['amount_received']);
            if(isset($data['paid_by_id'])&&$data['paid_by_id']!='')
            	$arr_where['paid_by'] = $data['paid_by_id'];
            if(isset($data['notes'])&&$data['notes']!='')
            	$arr_where['notes'] = $data['notes'];
            if(isset($data['employee'])&&$data['employee']!='')
			{
				$arr_where['$or'][]['our_rep'] = new MongoRegex('/'.trim($data['employee']).'/i');
				$arr_where['$or'][]['our_csr'] = new MongoRegex('/'.trim($data['employee']).'/i');
			}
			if(isset($data['employee_id'])&&$data['employee_id']!='')
			{
				$arr_where['$or'][]['our_rep_id'] = new MongoRegex('/'.trim($data['employee_id']).'/i');
				$arr_where['$or'][]['our_csr_id'] = new MongoRegex('/'.trim($data['employee_id']).'/i');
			}
			if($data['allocation_amount']!=''
				||$data['sales_invoice_no']!=''
				||isset($data['write_off'])&&$data['write_off']!=0
				||isset($data['not_write_off'])&&$data['not_write_off']!=0)
			{
				$arr_where['allocation']['$elemMatch']['deleted'] = false;
				if($data['allocation_amount']!='')
					$arr_where['allocation']['$elemMatch']['amount'] = trim($data['allocation_amount']);
				if($data['sales_invoice_no'])
					$arr_where['allocation']['$elemMatch']['salesinvoice_code'] = trim($data['sales_invoice_no']);
				if(isset($data['write_off'])&&$data['write_off']!=0)
					$arr_where['allocation']['$elemMatch']['write_off'] = 1;
				else if(isset($data['not_write_off'])&&$data['not_write_off']!=0)
					$arr_where['allocation']['$elemMatch']['write_off'] = 0;
			}
			$arr_where['allocation']['$ne'] = '';
			$receipts = $this->opm->select_all(array(
								'arr_where'=>$arr_where,
								'arr_order'=>array('_id'=>1),
								'arr_field'=>array('_id','allocation','company_name','reference','receipt_date')
				));
			if($receipts->count()==0)
			{
				echo 'empty';
				die;
			}
			else
			{
				$i = 0;
				$total_amount = 0;
				$html_loop = '
							<table cellpadding="3" cellspacing="0" class="maintb">
			                  <tr>
			                     <td width="15%" class="first top">
			                        Date
			                     </td>
			                     <td width="9%" class="top">
			                        Inv #
			                     </td>
			                     <td width="20%" class="top" align="left">
			                        Customer
			                     </td>
			                     <td width="11%" class="top" align="left">
			                        Reference
			                     </td>
			                     <td width="20%" class="top" align="left">
			                        Notes allocation
			                     </td>
			                     <td width="10%" class="top">
			                        Write off
			                     </td>
			                     <td width="15%" colspan="3" class="end top" align="right">
			                        Amount
			                     </td>
			                  </tr>
				';
				foreach($receipts as $value)
				{
					if(!empty($value['allocation']))
					foreach($value['allocation'] as $val)
					{
						//allocation đã xóa ko cần kiểm tra
						if(isset($val['deleted'])&&$val['deleted'])continue;
						// nếu người dùng search allocation_amount
						if($data['allocation_amount']!=''){
							if( $data['allocation_amount']!=$val['amount'] )continue;
						}

						// nếu người dùng search salesinvoice_code
						if($data['sales_invoice_no']!=''){
							if( $data['sales_invoice_no']!=$val['salesinvoice_code'] )continue;
						}

						// nếu người dùng search write_off
						if(isset($data['write_off'])&&$data['write_off']){
							if( !$val['write_off'] )continue;
						}
						if(isset($data['not_write_off'])&&$data['not_write_off']){
							if($val['write_off'])continue;
						}
						$bg = ( $i % 2 == 0? 'background-color:#eeeeee':'background-color:#fdfcfa');
						$amount = (isset($val['amount'])&&$val['amount']!='' ? $val['amount'] : 0);
						$total_amount += $amount;
						$html_loop .='
							<tr style="'.$bg.'">
								<td class="first content">'.date('M d, Y',$value['receipt_date']->sec).'</td>
								<td class="content">'.(isset($val['salesinvoice_code']) ? $val['salesinvoice_code'] : '').'</td>
								<td class="content" align="left">'.(isset($value['company_name']) ? $value['company_name'] : '').'</td>
								<td class="content" align="left">'.(isset($val['reference']) ? $val['reference'] : '').'</td>
								<td class="content" align="left">'.(isset($val['note']) ? $val['note'] : '').'</td>
								<td class="content">'.(isset($val['write_off'])&&$val['write_off']==1 ? '<strong>X</strong>' : '').'</td>
								<td class="end content" colspan="3" align="right">'.$this->opm->format_currency($amount).'</td>
							</tr>
							';
						$i++;
					}
				}
				$bg = ( $i % 2 == 0? 'background-color:#eeeeee':'background-color:#fdfcfa');
				$html_loop .='	<tr style="'.$bg.'">
									<td colspan="3" class="first bottom" align="left">'.$i.' record(s) listed</td>
									<td colspan="3" class="bottom" align="right">
										<span style="font-weight:bold; padding-left:20px">Total:</span>
									</td>
									<td colspan="3" class="end bottom" align="right">'.$this->opm->format_currency($total_amount).'</td>
								</tr>
							</table>';
				//========================================
		        $pdf['current_time'] = date('h:i a m/d/Y');
		        $pdf['title'] = '<span style="color:#b32017">S</span>ales <span style="color:#b32017">R</span>eceipt <span style="color:#b32017">L</span>isting';
		        $this->layout = 'pdf';
		        //set header
		        $pdf['logo_link'] = 'img/logo_anvy.jpg';
		        $pdf['company_address'] = '3145 - 5th Ave NE<br />Calgary  AB  T2A  6A3<br />';
		        $pdf['heading'] = $data['heading'];
		        if (isset($data['date_equals']) && $data['date_equals'] != '') {
		            $pdf['date_equals'] = $data['date_equals'];
		        } else {
		            if (isset($data['date_from']) && $data['date_from'] != '')
		                $pdf['date_from'] = $data['date_from'];
		            if (isset($data['date_to']) && $data['date_to'] != '')
		                $pdf['date_to'] = $data['date_to'];
		        }
		        $pdf['html_loop'] = $html_loop;
		        $pdf['filename'] = 'RE_' . md5($pdf['current_time']);

		        $this->report_pdf($pdf);
		        echo URL.'/upload/' . $pdf['filename'] . '.pdf';
				die;
			}
		}
		else
			$this->set('arr_data',$arr_data);
	}
	public function customer_summary_report()
	{
		if(!$this->check_permission($this->name.'_@_options_@_customer_summary_report'))
			die;
		$receipts = $this->opm->select_all(array(
						'arr_order'=>array('_id'=>1),
						'arr_field'=>array('_id','company_id','company_name','amount_received')
		));
		if($receipts->count()>0)
		{
			$i = 0;
			$group =array();
			$total_amount = 0;
			$html_loop = '
						<table cellpadding="3" cellspacing="0" class="maintb">
		                  <tr>
		                     <td width="30%" class="first top" align="left">
		                        Customer
		                     </td>
		                     <td width="45%" class="top" align="left">
		                         Invoice #
		                     </td>
		                     <td width="25%" colspan="3" class="end top" align="right">
		                        Total
		                     </td>
		                  </tr>
			';
			$this->selectModel('Company');
			$this->selectModel('Salesinvoice');
			foreach($receipts as $value)
			{
				if(isset($value['company_id'])&&is_object($value['company_id'])){
					$arr_code = array();
					$invoices = $this->Salesinvoice->select_all(array(
					                                            'arr_where'=>array(
					                                                               'company_id'=>$value['company_id']
					                                                               ),
					                                            'arr_field'=>array('code')
					                                            ));
					foreach($invoices as $invoice){
						$arr_code[] = $invoice['code'];
					}
					$id = (string)$value['company_id'];
					$amount_received = (isset($value['amount_received'])&&$value['amount_received']!= '' ? $value['amount_received'] : 0);
					$group[$id]['company_name'] = $value['company_name'];
					$group[$id]['invoice_code'] = !empty($arr_code) ? rtrim(implode(', ', $arr_code),', ') : '';
					if(!isset($group[$id]['total']))
						$group[$id]['total'] = $amount_received;
					else
						$group[$id]['total'] += $amount_received;
					$total_amount += $amount_received;
				}

			}
			if(!empty($group))
			{
				foreach($group as $value)
				{
					$bg = ( $i % 2 == 0? 'background-color:#eeeeee':'background-color:#fdfcfa');
					$html_loop .='
						<tr style="'.$bg.'">
							<td class="first content" align="left">'.$value['company_name'].'</td>
							<td class="content" align="left">'.$value['invoice_code'].'</td>
							<td class="end content" colspan="3" align="right">'.$this->opm->format_currency($value['total']).'</td>
						</tr>
						';
					$i++;
				}
				$bg = ( $i % 2 == 0? 'background-color:#eeeeee':'background-color:#fdfcfa');
				$html_loop .='	<tr style="'.$bg.'">
									<td class="first bottom" align="left">'.$i.' record(s) listed</td>
									<td class="bottom" align="right">
										<span style="font-weight:bold; padding-left:20px">Total:</span>
									</td>
									<td colspan="3" class="end bottom" align="right">'.$this->opm->format_currency($total_amount).'</td>
								</tr>
							</table>';
				//========================================
		        $pdf['current_time'] = date('h:i a m/d/Y');
		        $pdf['title'] = '<span style="color:#b32017">R</span>eceipt <span style="color:#b32017">R</span>eport by <span style="color:#b32017">C</span>ustomer<br/>(Summary)';
		        $this->layout = 'pdf';
		        //set header
		        $pdf['logo_link'] = 'img/logo_anvy.jpg';
		        $pdf['company_address'] = '3145 - 5th Ave NE<br />Calgary  AB  T2A  6A3<br />';
		        $pdf['html_loop'] = $html_loop;
		        $pdf['filename'] = 'RE_' . md5($pdf['current_time']);

		        $this->report_pdf($pdf);
		        echo URL.'/upload/' . $pdf['filename'] . '.pdf';
	    	}
			die;
		}
	}
	public function customer_detailed_report()
	{
		if(!$this->check_permission($this->name.'_@_options_@_customer_detailed_report'))
			die;
		$receipts = $this->opm->select_all(array(
						'arr_order'=>array('_id'=>1),
						'arr_field'=>array('_id','code','receipt_date','paid_by','reference','our_rep','company_id','company_name','notes','amount_received')
		));
		if($receipts->count()>0)
		{
			$group =array();
			$total_amount = 0;
			$html_loop = '';
			foreach($receipts as $key=>$value)
			{
				if(isset($value['company_id'])&&is_object($value['company_id']))
				{
					$id = (string)$value['company_id'];
					$amount_received = (isset($value['amount_received'])&&$value['amount_received']!= '' ? $value['amount_received'] : 0);
					$group[$id]['company_name'] = $value['company_name'];
					$group[$id]['group'][$key]['code'] = $value['code'];
					$group[$id]['group'][$key]['date'] = date('M d, Y',$value['receipt_date']->sec);
					$group[$id]['group'][$key]['paid_by'] = $value['paid_by'];
					$group[$id]['group'][$key]['reference'] = isset($value['reference']) ? $value['reference'] : '';
					$group[$id]['group'][$key]['our_rep'] = isset($value['our_rep']) ? $value['our_rep'] : '';
					$group[$id]['group'][$key]['notes'] = isset($value['notes']) ? $value['notes'] : '';
					$group[$id]['group'][$key]['total'] = $this->opm->format_currency($amount_received);
					if(!isset($group[$id]['total_amount']))
						$group[$id]['total_amount'] = $amount_received;
					else
						$group[$id]['total_amount'] += $amount_received;
					$total_amount += $amount_received;
				}

			}
			if(!empty($group))
			{
				foreach($group as $value)
				{
					$i = 0;
					$html_loop .= '
							<table cellpadding="3" cellspacing="0" class="maintb">
			                  <tr>
			                     <td width="60%" class="first top" align="left">
			                        Customer
			                     </td>
			                     <td width="40%" class="end top" align="right">
			                        Total
			                     </td>
			                  </tr>
			                  <tr style="background-color:#eeeeee">
			                     <td class="first bottom" align="left">
			                        <strong>'.$value['company_name'].'</strong>
			                     </td>
			                     <td width="40%" class="end bottom" align="right">
			                        '.$this->opm->format_currency($value['total_amount']).'
			                     </td>
			                  </tr>
			                </table>
			                <br /><br />
					';
					$html_loop .= '
							<table cellpadding="3" cellspacing="0" class="maintb">
			                  <tr>
			                     <td width="7%" class="first top">
			                        Ref #
			                     </td>
			                     <td width="15%" class="top">
			                        Date
			                     </td>
			                     <td width="10%" class="top" align="left">
			                        Paid by
			                     </td>
			                     <td width="13%" class="top" align="left">
			                        Reference
			                     </td>
			                     <td width="20%" class="top" align="left">
			                        Our rep
			                     </td>
			                     <td width="20%" class="top">
			                        Note
			                     </td>
			                     <td width="15%" colspan="3" class="end top" align="right">
			                        Amount
			                     </td>
			                  </tr>';
					foreach($value['group'] as $val)
					{
						$bg = ( $i % 2 == 0? 'background-color:#eeeeee':'background-color:#fdfcfa');
						$html_loop .='
							<tr style="'.$bg.'">
								<td class="first content" align="left">'.$val['code'].'</td>
								<td class="content">'.$val['date'].'</td>
								<td class="content" align="left">'.$val['paid_by'].'</td>
								<td class="content" align="left">'.$val['reference'].'</td>
								<td class="content" align="left">'.$val['our_rep'].'</td>
								<td class="content" align="left">'.$val['notes'].'</td>
								<td class="end content" colspan="3" align="right">'.$val['total'].'</td>
							</tr>
							';
						$i++;
					}
					$bg = ( $i % 2 == 0? 'background-color:#eeeeee':'background-color:#fdfcfa');
					$html_loop .='	<tr style="'.$bg.'">
										<td colspan="3" class="first bottom" align="left">'.$i.' record(s) listed.</td>
										<td colspan="3" class="bottom" align="right">
											<span style="font-weight:bold; padding-left:20px">Total:</span>
										</td>
										<td colspan="3" class="end bottom" align="right">'.$this->opm->format_currency($value['total_amount']).'</td>
									</tr>
								</table>
								<br />
			                    <div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both"></div>
			                    <br />';
				}
				//========================================
		        $pdf['current_time'] = date('h:i a m/d/Y');
		        $pdf['title'] = '<span style="color:#b32017">R</span>eceipt <span style="color:#b32017">R</span>eport by <span style="color:#b32017">C</span>ustomer<br/>(Detailed)';
		        $this->layout = 'pdf';
		        //set header
		        $pdf['logo_link'] = 'img/logo_anvy.jpg';
		        $pdf['company_address'] = '3145 - 5th Ave NE<br />Calgary  AB  T2A  6A3<br />';
		        $pdf['html_loop'] = $html_loop;
		        $pdf['filename'] = 'RE_' . md5($pdf['current_time']);

		        $this->report_pdf($pdf);
		        echo URL.'/upload/' . $pdf['filename'] . '.pdf';
	    	}
			die;
		}
	}
	public function view_minilist(){
		if(!isset($_GET['print_pdf'])){
			$arr_where = $this->arr_search_where();
			$receipts = $this->opm->select_all(array(
												'arr_where'  => $arr_where,
												'arr_field'  => array('_id','code','receipt_date','paid_by','reference','our_rep','company_id','company_name','notes','amount_received','unallocated','unallocated'),
												'arr_order'  => array('_id'=>1),
												'limit'		 => 2000
												));
			if($receipts->count() > 0){
				$html='';
				$i=0;
				$arr_data = array();
				$sum_amount_received = 0;
				$sum_allocated = 0;
				$sum_unallocated = 0;
				foreach($receipts as $key => $receipt){
					$sum_amount_received += $amount_received = (isset($receipt['amount_received']) ? (float)$receipt['amount_received'] : 0);
					$sum_allocated += $total_allocated = (isset($receipt['total_allocated']) ? (float)$receipt['total_allocated'] : 0);
					$sum_unallocated += $unallocated = (isset($receipt['unallocated']) ? $receipt['unallocated'] : 0);
					$html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">';
					$html .= '<td>'.(isset($receipt['code']) ? $receipt['code'] :'').'</td>';
					$html .= '<td>'.(isset($receipt['company_name']) ? $receipt['company_name'] : '') .'</td>';
					$html .= '<td class="center_text">'.(isset($receipt['receipt_date']) ? date('m/d/Y',$receipt['receipt_date']->sec):'') .'</td>';
					$html .= '<td>'.(isset($receipt['paid_by']) ? $receipt['paid_by'] : '') .'</td>';
					$html .= '<td>'.(isset($receipt['reference']) ? $receipt['reference'] : '') .'</td>';
					$html .= '<td class="right_text">'. $this->opm->format_currency($amount_received) .'</td>';
					$html .= '<td class="right_text">'.$this->opm->format_currency($total_allocated) .'</td>';
					$html .= '<td class="right_text">'.$this->opm->format_currency($unallocated) .'</td>';
					$html .= '</tr>';
	                $i++;
				}
				 $html .='<tr class="last">
	                        <td colspan="4" class="bold_text right_none">'.$i.' record(s) listed.</td>
	                        <td class="right_text bold_text right_none">Total:</td>
	                        <td class="right_text bold_text right_none">'.$this->opm->format_currency($sum_amount_received).'</td>
	                        <td class="right_text bold_text right_none">'.$this->opm->format_currency($sum_allocated).'</td>
	                        <td class="right_text bold_text right_none">'.$this->opm->format_currency($sum_unallocated).'</td>
	                        </tr>';
	            $arr_data['title'] = array('Ref #'=>'text-align: left;width: 7%;','Customer'=>'text-align: left;','Date','Paid by','Reference','Total receipts.'=>'text-align: right;width: 12%;','Allocated'=>'text-align: right;width: 12%;','Unallocated'=>'text-align: right;width: 12%;');
	            $arr_data['content'] = $html;
	            $arr_data['report_name'] = 'Receipt Mini Listing ';
	            $arr_data['report_file_name']='RE_'.md5(time());
	            Cache::write('receipts_minilist', $arr_data);
            }
        } else {
            $arr_data = Cache::read('receipts_minilist');
        	Cache::delete('receipts_minilist');
        }
		$this->render_pdf($arr_data);
	}

	function build_receipts()
	{
		$this->selectModel('Receipt');
		$this->selectModel('Company');
		$this->selectModel('Salesaccount');
		$receipts = $this->Receipt->select_all(array(
		                                       'arr_where' => array(
		                                                            'salesaccount_id' => array('$nin' => array('', null))
		                                                            ),
		                                       'arr_order' => array('_id'=>1),
		                                       'arr_field' => array('salesaccount_id'),
		                                       'limit' => 9999
		                                       ));
		echo $receipts->count().' records found.<br />';
		$i = 0;
		foreach($receipts as $receipt){
			$arr_data = array('_id'=>$receipt['_id']);
			$company = $this->Company->select_one(array('_id'=>$receipt['salesaccount_id']),array('_id'));
			if(!isset($company['_id']))	continue;
			$salesaccount = $this->Salesaccount->select_one(array('company_id'=>$receipt['salesaccount_id']),array('_id'));
			$arr_data['salesaccount_id'] = $salesaccount['_id'];
			$arr_data['company_id'] = $receipt['salesaccount_id'];
			$this->Receipt->rebuild_collection($arr_data);
			$i++;
		}
		echo 'Done - '.$i;
		die;
	}



	public function build_allocation_receipts(){
		$receipts = $this->opm->select_all(array(
		                       'arr_where' => array(
		                                            '$or'=>array(
		                                                        array(
		                                                              'allocation' => array(
		                                                                  '$in' => array('',null)
		                                                                  )
		                                                              ),
		                                                        array(
		                                                              'allocation' => array('$exists' => false)
		                                                              )
		                                                         )
		                                            ),
		                       'arr_field' =>array('_id'),
		                       'limit' => 9999
		                       ));
		echo $receipts->count().' records found.<br />';
		$i = 0;
		foreach($receipts as $receipt){
			$receipt['allocation'] = array();
			$this->Receipt->rebuild_collection($receipt);
			$i++;
		}
		echo $i.' - Done';
		die;
	}

	function save_inactive_outstanding(){
		if(!empty($_POST)){
			$query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())),array('inactive_outstading'));
			if(!isset($query['inactive_outstading']))
				$query['inactive_outstading'] = array();
			$invoice_id = new MongoId($_POST['invoice_id']);
			if($_POST['check']){
				if(!in_array($invoice_id,$query['inactive_outstading']))
					$query['inactive_outstading'][] = $invoice_id;
			} else {
				$key = array_search($invoice_id, $query['inactive_outstading']);
				if(isset($query['inactive_outstading'][$key]))
					unset($query['inactive_outstading'][$key]);
			}
			$this->opm->save($query);
			echo 'ok';
			die;
		}
	}
}