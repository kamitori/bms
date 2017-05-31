<?php
App::uses('AppController', 'Controller');
class ApiController extends AppController{
	private $__request;
	private $__modules;
	private $__actions;
	public function beforeFilter()
	{
		$this->autoRender = false;
		$this->response->type('json');
		$this->__request = $_REQUEST;
		if( empty($this->__request) ){
			$__request = json_decode(file_get_contents("php://input"));
			$this->__request = $this->convertToArray($__request);
			$this->__request = $this->convert($this->__request);
		}
		$this->__modules = array('job', 'salesorder', 'salesinvoice', 'quotation', 'company', 'contact', 'task', 'shipping', 'product', 'pricing');
		$this->__actions = array('list', 'detail', 'create', 'update', 'delete', 'report', 'status', 'pricing');
	}

	public function index()
	{
		$this->__setSession();
		if( !$this->checkValid() )
			return $this->sendBack('Missing Argument! - '.$this->__missingArg );
		if( !$this->checkApiKey() )
			return $this->sendBack('API is not valid!');
		// if( !$this->checkSite() )
		// 	return $this->sendBack('Your site do not have right to access.');
		if( !$this->checkModule() )
			return $this->sendBack('Module is not existed!');
		if( !$this->checkAction() )
			return $this->sendBack('Action is not existed!');
		$this->__systemCompany = $this->checkSystemCompany();
		return $this->moduleAction();
	}

	private function __setSession()
	{
		$this->system_admin =  true;
		$arr_contact['contact_id'] = new MongoId('200000000000000000000000');
		$arr_contact['contact_name'] = $arr_contact['full_name'] = $this->__company;
		$arr_contact['first_name'] = $this->__company;
		$arr_contact['last_name'] = '';
		$this->selectModel('Company');
		$company = $this->Company->select_one(array('system' => true,'no' => 1),array('no'),array('_id' => -1));
		$arr_contact['company_id'] = $company['_id'];
		$arr_contact['company_no'] = $company['no'];
		$arr_contact['system_admin'] = true;
		$_SESSION['arr_user'] = $arr_contact;
	}

	private function checkValid()
	{
		if(empty($this->__request))
			return false;
		foreach(array('API','MODULE','ACTION') as $value){
			if(!isset($this->__request[$value])){
				$this->__missingArg = $value;
				return false;
			}
		}
		return true;
	}

	private function checkApiKey()
	{
		$this->selectModel('Api');
		$api = $this->Api->select_one(array(
		                       'api_key' 	=> $this->__request['API'],
		                       ));
		if( empty($api) )
			return false;
		$this->__companyId = $api['company_id'];
		$this->__company = $api['company'].' - API';
		$this->__site = $api['site'];
		return true;
	}

	private function checkSystemCompany()
	{
		$this->selectModel('Company');
		$system = $this->Company->select_one(array(
		                       '_id' 	=> $this->__companyId,
		                       'system' => true
		                       ), array('_id'));
		if( empty($system) )
			return false;
		return true;
	}

	private function checkSite()
	{
		$this->__site = str_replace(array( 'http://', 'https://', '/'), '', $this->__site);
		if( $_SERVER['HTTP_X_FORWARDED_FOR'] == gethostbyname($this->__site) )
			return true;
		return false;
	}



	private function checkModule()
	{
		if( !in_array($this->__request['MODULE'], $this->__modules) )
			return false;
		return true;
	}

	private function checkAction()
	{
		if( !in_array($this->__request['ACTION'], $this->__actions)  )
			return false;
		return true;
	}

	private function moduleAction()
	{

		$action = $this->__request['ACTION'];
		$arr_return = array();
		switch ($action) {
			case 'list':
				$arr_return = $this->listModule();
				break;
			case 'detail':
				$arr_return = $this->detailModule();
				break;
			case 'create':
				$arr_return = $this->createModule();
				break;
			case 'update':
				$arr_return = $this->updateModule();
				break;
			case 'delete':
				$arr_return = $this->deleteModule();
				break;
			case 'report':
				$arr_return = $this->reportModule();
				break;
			case 'status':
				$arr_return = $this->statusModule();
				break;
			case 'pricing':
				$arr_return = $this->pricing();
				break;
			default:
				break;
		}
		return $this->sendBack($arr_return);
	}

	private function getDefaultField()
	{
		$arr_return = array();
		switch ($this->__request['MODULE']) {
			case 'job':
				$arr_return = array('no','company_name','company_id','name','work_start','work_end','status','type','salesorder_total','salesinvoice_total','quotation_total');
				break;
			case 'salesorder':
				$arr_return = array('code','company_name','company_id','contact_name','contact_id','salesorder_date','heading','quotation_total','job_number','job_id','sum_sub_total','status','payment_due_date','payment_terms');
				break;
			case 'salesinvoice':
				$arr_return = array('code','company_name','company_id','contact_name','contact_id','invoice_date','heading','salesorder_total','job_number','job_id','sum_sub_total','invoice_status');
				break;
			case 'quotation':
				$arr_return = array('code','company_name','company_id','contact_name','contact_id','phone','quotation_date','our_rep','quotation_status','salesorder_total','sum_sub_total','quotation_type');
				break;
			case 'company':
				$arr_return = array('no','name','type','phone','our_rep','our_rep_id');
				break;
			case 'contact':
				$arr_return = array('title','first_name','last_name','type','direct_dial','mobile','home_phone');
				break;
			case 'task':
				$arr_return = array('no','name','job_number','our_rep','our_rep_id','type','work_start','work_end','status');
			case 'shipping':
				$arr_return = array('no','name','shipping_type','return_status','company_name','company_id','contact_name','contact_id','phone','our_rep','our_rep_id','shipping_status','shipper','shipper_id');
				break;
		}
		return $arr_return;
	}

	private function listModule()
	{
		$module = ucfirst($this->__request['MODULE']);
		$this->_getDiff = isset($this->__request['GET_DIFF']) ? true : false;
		if($this->_getDiff) {
			$this->selectModel('Stuffs');
			$this->_time = $this->Stuffs->select_one(array('name' => 'Lasted_API_Time_'.$module));
		}
		$arr_where = array('deleted' => false);
		if( !$this->__systemCompany ) {
			if($module == 'Company'){
				if(isset($this->__request['_ID']) && $this->__request['_ID'] == 1)
					$arr_where = array('_id'=>$this->__companyId);
			}
			else if($module != 'Product')
				$arr_where = array('company_id'=>$this->__companyId);
		}
		$this->selectModel($module);
		if( isset($this->__request['WHERE']) && !empty($this->__request['WHERE']) ){
			$arr_where = array_merge($arr_where, $this->__request['WHERE']);
		}
		if(isset($this->__request['JT_ONLY'])){
			$arr_where['api_key'] = array('$exists' => false);
		}
		if($this->_getDiff) {
			if(!empty($this->_time) && isset($this->_time['time']))
				$arr_where['date_modified'] = array('$gt' => $this->_time['time']);
		}
		$arr_field = $this->getDefaultField();
		if( isset($this->__request['FIELD']) && !empty($this->__request['FIELD']) ){
			$arr_field = array_merge($arr_field,(array)$this->__request['FIELD']);
			if(in_array('full',$arr_field))
				$arr_field = array();
		}
		$limit = 10;
		if( isset($this->__request['LIMIT']) && is_numeric($this->__request['LIMIT'])){
			if( $this->__request['LIMIT'] < 1 )
				$this->__request['LIMIT'] = 10;
			if( $this->__request['LIMIT'] > 3000 )
				$this->__request['LIMIT'] = 3000;
			$limit = $this->__request['LIMIT'];
		}
		$skip = 0;
		$page  = 1;
		if( isset($this->__request['PAGE']) && is_numeric($this->__request['PAGE'])){
			if( $this->__request['PAGE'] < 0 )
				$this->__request['PAGE'] = 1;
			$page = $this->__request['PAGE'];
			$skip = $limit*($page - 1);
		}
		$array = array(
                   'arr_where'	=>	$arr_where,
                   'arr_field' 	=> 	$arr_field,
                   'limit'		=>	$limit,
                   'skip'		=>	$skip,
                   'arr_order'	=>	array('_id'=>-1)
                   );
		if(isset($this->__request['GET']) && $this->__request['GET'] == 'file') {
			return $this->__getFileListModule($module, $array);
		}
		$module_object = $this->$module->select_all($array);
		$arr_return = array();
		foreach($module_object as $row){
			$this->dataHandle($row);
			$this->getOtherTotal($row,$module,$arr_field);
			$arr_return[] = $row;
		}
		if($this->_getDiff) {
			$this->_time['time'] = new MongoDate();
			$this->_time['name'] = 'Lasted_API_Time_'.$module;
			$this->Stuffs->save($this->_time);
		}
		return $arr_return;
	}

	private function __getFileListModule($module, $array)
	{
		$collection  = '-c tb_'.strtolower($module);
		$db = strpos(URL, 'jt.anvy.net') !== false ? '-d jobtraq_dev' : '-d jobtraq';
		$field = '';
		if( !empty($array['arr_field']) )
			$field = '-f '.implode(', ', $array['arr_field']);
		$order = '';
		if( !empty($array['arr_order']) ){
			$order = '--sort "{';
			foreach($array['arr_order'] as $key => $value){
				$order .= "'$key' : $value, ";
			}
			$order = rtrim($order, ', ');
			$order .= '}"';
		}
		$limit = '';
		if( !empty($array['limit']) )
			$limit = '--limit '.$array['limit'];
		$skip = '';
		if( !empty($array['skip']) )
			$skip = '--skip  '.$array['skip'];
		$query = '';
		if( !empty($array['arr_where'])) {
			$query = '-q "{';
			$query  .= $this->convertToMongoString($array['arr_where']);
			$query = rtrim($query, ', ');
			$query .=  '}"';
		}
		$name = md5(serialize($array));
		$out = '-o '.WWW_ROOT.DS.'files'.DS.$name.'.json';
		$command = "mongoexport $db $collection $query $limit $skip $order --jsonArray $out";
		/*if(strpos(URL, 'jobtraq.anvyonline.com') !== false){
			$command = "C:\mongodb\bin\mongoexport $db $collection $query $limit $skip $order --jsonArray $out";
		}*/
		$result = exec($command);
		$result = (int)trim(str_replace(['exported', 'records', 'record'],'',$result));
		$zipname = WWW_ROOT.DS.'files'.DS.$name.'.zip';
		$zip = new ZipArchive;
		$zip->open($zipname, ZipArchive::CREATE);
		$zip->addFile( WWW_ROOT.DS.'files'.DS.$name.'.json',$name.'.json');
		$zip->close();
		if($this->_getDiff) {
			$this->_time['time'] = new MongoDate();
			$this->_time['name'] = 'Lasted_API_Time_'.$module;
			$this->Stuffs->save($this->_time);
		}
		return array('file' => URL.'/files/'.$name.'.zip', 'count' => $result);


	}

	private function getOtherTotal(&$row,$requestModule,$arr_field)
	{
		$requestModule = str_replace(' ', '', $requestModule);
		$requestModule = strtolower($requestModule);
		foreach(array('salesinvoice_total'=>'invoice_status','salesorder_total'=>'status','quotation_total'=>'quotation_status') as $total=>$status){
			if( in_array($total, $arr_field) ){
				$row[$total] = 0;
				$module = str_replace('_total','',$total);
				$module = ucfirst($module);
				$this->selectModel($module);
				$arr_totals = $this->$module->select_all(array(
				                           'arr_where'=>array(
				                                              "{$requestModule}_id"	=> new MongoId($row['_id']),
				                                              $status 				=> array(
				                                                             			'$ne'=>'Cancelled'
				                                                             			)
				                                              ),
				                           'arr_field'=>array('sum_sub_total')
				                           ));
				foreach($arr_totals as $value){
					if( !isset($value['sum_sub_total']) )
						$value['sum_sub_total'] = 0;
					$row[$total] += (float)$value['sum_sub_total'];
				}
				$row[$total] = number_format($row[$total],2);
			}
		}
	}

	private function detailModule()
	{
		if( $this->__request['MODULE'] == 'company'){
			$_id = $this->__companyId;
		} else {
			if(!isset($this->__request['_ID']) || strlen($this->__request['_ID']) != 24)
				return 'ID is not valid!';
			$_id = $this->__request['_ID'];
		}
		$module = ucfirst($this->__request['MODULE']);
		$this->selectModel($module);
		$arr_return = $this->$module->select_one(array('_id'=> new MongoId($_id)));
		$this->dataHandle($arr_return);
		return $arr_return;

	}

	private function updateModule()
	{
		if(!isset($this->__request['_ID']) || strlen($this->__request['_ID']) != 24)
			return 'ID is not valid!';
		if(!isset($this->__request['DATA']))
			return 'DATA is requested to update!';
		$_id = $this->__request['_ID'];
		$module = ucfirst($this->__request['MODULE']);
		$this->selectModel($module);
		$arr_save['_id'] = new MongoId($this->__request['_ID']);
		$arr_save['api_key'] = (string)$this->__request['API'];
		$arr_save['modified_by_name'] = $this->__company;
		$arr_save = array_merge($arr_save, $this->__request['DATA']);
		if( $this->$module->save($arr_save) )
			return array('status' => 'This record has been updated successful!');
		return 'There are something wrong. Please try again!';
	}

	private function deleteModule()
	{
		$arr_save = array();
		if(!isset($this->__request['_ID']) || strlen($this->__request['_ID']) != 24){
			if(!isset($this->__request['WHERE']))
				return 'ID is not valid!';
		}
		$module = ucfirst($this->__request['MODULE']);
		$this->selectModel($module);
		$arr_save = array('deleted' => true, 'modified_by_name' => $this->__company);
		if(isset($this->__request['_ID'])){
			$arr_save['_id'] =  new MongoId($this->__request['_ID']);
		}
		if( $this->$module->save($arr_save) )
			return array('status' => 'This record has been deleted successful!');
		return 'There are something wrong. Please try again!';
	}

	private function createModule()
	{
		if(!isset($this->__request['DATA']))
			return 'DATA is requested to create!';
		$is_array = false;
		if(isset($this->__request['BATCH_INSERT'])){
			reset($this->__request['DATA']);
			$firstKey = key($this->__request['DATA']);
			if(!is_numeric($firstKey))
				return 'Wrong DATA format for batch insert!';
			$arr_return = array();
			usort($this->__request['DATA'], function($a, $b){
				$a['code'] = isset($a['code']) ? preg_replace("/[^0-9]/","",$a['code']) : '';
				$b['code'] = isset($b['code']) ? preg_replace("/[^0-9]/","",$b['code'])  : '';
				return $a['code'] > $b['code'];
			});
			$is_array = true;
		}
		switch ($this->__request['MODULE']) {
			case 'salesorder':
				if($is_array){
					$arr_return = array();
					foreach($this->__request['DATA'] as $arrData){
						$arr_return[] = $this->__createSalesorder($arrData);
					}
					return $arr_return;
				}
				return $this->__createSalesorder();
			case 'quotation':
				if($is_array){
					$arr_return = array();
					foreach($this->__request['DATA'] as $arrData){
						$arr_return[] = $this->__createQuotation($arrData);
					}
					return $arr_return;
				}
				return $this->__createQuotation();
			case 'product':
				if($is_array){
					$arr_return = array();
					foreach($this->__request['DATA'] as $arrData){
						$arr_return[] = $this->__createProduct($arrData);
					}
					return $arr_return;
				}
				return $this->__createProduct();
			case 'contact':
				return $this->__createContact();
			default:
				return "{$this->__request['MODULE']} is not yet supported.";
		}
	}

	private function reportModule()
	{
		if(!isset($this->__request['_ID']) || strlen($this->__request['_ID']) != 24)
			return 'ID is not valid!';
		$this->_type 	= isset($this->__request['TYPE'])&&$this->__request['TYPE'] == 'simple' ? 'group': 'detail';
		$this->_pdf		= isset($this->__request['GET']) && $this->__request['GET'] == 'file'  ? true : false;
		switch ($this->__request['MODULE']) {
			case 'salesorder':
				return $this->__reportSalesorder();
			default:
				return "{$this->__request['MODULE']} is not yet supported.";
		}
	}

	private function __getAddress(&$arr_save,$company)
	{
		if(!isset($arr_save['invoice_address'][0])){
			$arr_save['invoice_address'][0] = array(
		                                      'invoice_country'=>'Canada',
		                                      'invoice_country_id'=>'CA',
		                                      );
			$addresses_default_key = 0;
			if(isset($company['addresses_default_key']))
				$addresses_default_key = $company['addresses_default_key'];
			if(isset($company['addresses'][$addresses_default_key])){
				foreach($company['addresses'][$addresses_default_key] as $field => $value){
					if($field == 'name' || $field == 'deleted') continue;
					$arr_save['invoice_address'][0]['invoice_'.$field] = $value;
				}
			}
		}
		if(!isset($arr_save['shipping_address'][0]))
			$arr_save['shipping_address'][0] = array(
		                                      'shipping_country'=>'Canada',
		                                      'shipping_country_id'=>'CA',
		                                      );
		$arr_save['invoice_address'][0]['deleted'] = false;
		$arr_save['shipping_address'][0]['deleted'] = false;
	}

	private function __getCompanyInfo(&$arr_save,$company)
	{
		if(!isset($arr_save['contact_name']) || (isset($arr_save['contact_name'])&& empty($arr_save['contact_name'])) ){
			$arr_save['contact_name'] = isset($company['contact_name']) ? $company['contact_name'] : (isset($arr_save['contact_name']) ? $arr_save['contact_name'] : '');
			$arr_save['contact_id'] = isset($company['contact_id']) ? $company['contact_id'] : (isset($arr_save['contact_id']) ? $arr_save['contact_id'] : '');
		}
		$arr_save['our_rep'] = isset($company['our_rep']) ? $company['our_rep'] : (isset($arr_save['our_rep']) ? $arr_save['our_rep'] : '');
		$arr_save['our_rep_id'] = isset($company['our_rep_id']) ? $company['our_rep_id'] : (isset($arr_save['our_rep_id']) ? $arr_save['our_rep_id'] : '');
		$arr_save['our_csr'] = isset($company['our_csr']) ? $company['our_csr'] : (isset($arr_save['our_csr']) ? $arr_save['our_csr'] : '');
		$arr_save['our_csr_id'] = isset($company['our_csr_id']) ? $company['our_csr_id'] : (isset($arr_save['our_csr_id']) ? $arr_save['our_csr_id'] : '');
		$arr_save['company_name'] = isset($company['name']) ? $company['name'] : '';
		$arr_save['email'] = isset($company['email']) ? $company['email'] : '';
		$arr_save['phone'] = isset($company['phone']) ? $company['phone'] : '';
		$this->selectModel('Salesaccount');
        $arr_salesaccount = $this->Salesaccount->select_one(array('company_id'=> new MongoId($arr_save['company_id'])),array('payment_terms'));
        if(!isset($arr_save['payment_terms']) || empty($arr_save['payment_terms']))
        	$arr_save['payment_terms'] = isset($arr_salesaccount['payment_terms'])?$arr_salesaccount['payment_terms']:0;

		$this->__getAddress($arr_save,$company);
	}

	private function __getTax(&$arr_save)
	{
		$taxper = 5;
		$this->selectModel('Tax');
		$arr_tax = $this->Tax->tax_select_list();
		$key_tax = 'AB';
		if(isset($arr_save['invoice_address'][0]['invoice_province_state_id']) && $arr_save['invoice_address'][0]['invoice_province_state_id']!='')
			$key_tax = $arr_save['invoice_address'][0]['invoice_province_state_id'];
		if(isset($arr_tax[$key_tax])){
			$prov_key = explode("%",$arr_tax[$key_tax]);
			$taxper = $prov_key[0];
		}
		$arr_save['taxval'] = $taxper;
		$arr_save['tax'] = $key_tax;
	}


	private function __createProductFromArray($product, $arr_save)
	{
		if(!isset($this->Product))
			$this->selectModel('Product');
		$this->Product->arrfield();
        $pro = $this->Product->arr_temp;
		$pro['deleted'] = false;
	    $pro['code'] = isset($product['code'])&&!empty($product['code']) ? $product['code'] : $this->Product->get_auto_code('code');
		$pro['name'] = isset($product['products_name']) ? $product['products_name'] : '';
		$pro['sku'] = $product['sku'];
		$pro['group_type'] = 'SELL';
		$pro['product_type'] = 'Product';
		$pro['company_name'] = $this->__company;
		$pro['company_id'] = new MongoId($this->__companyId);
		$pro['sizew'] = isset($product['sizew']) ? $product['sizew'] : '';
		$pro['sizew_unit'] = isset($product['sizew_unit']) ? $product['sizew_unit'] : 'in';
		$pro['sizeh'] = isset($product['sizeh']) ? $product['sizeh'] : '';
		$pro['sizeh_unit'] = isset($product['sizeh_unit']) ? $product['sizeh_unit'] : 'in';
		$pro['sell_by'] = isset($product['sell_by']) ? $product['sell_by'] : 'unit';
		$pro['cost_price'] = isset($product['cost_price']) ? $product['cost_price'] : 0;
		$pro['unit_price'] = isset($product['sell_price']) ? $product['sell_price'] : 0;
		$pro['sell_price'] = isset($product['sell_price']) ? $product['sell_price'] : 0;
		$pro['status'] = 1;
		$pro['category'] = 'api';
		$pro['modified_by_name'] = $this->__company;
		$pro['created_by_name'] = $this->__company;
		$pro['api_key'] = (string)$this->__request['API'];
		$pro['description'] = $this->__productDesciption;
		$this->Product->save($pro);
		return array('_id' => $this->Product->mongo_id_after_save, 'code' => $pro['code']);
	}

	private function __createSalesorder($arr_save = array())
	{
		if(empty($arr_save))
			$arr_save = $this->__request['DATA'];
		$arr_save['company_id'] = $this->__companyId;
		//===================================
		$this->selectModel('Salesorder');
		$arr_save['deleted'] = false;
		$salesorder_code = $arr_save['code'] = isset($arr_save['code'])&&!empty($arr_save['code']) ? $arr_save['code'] : $this->Salesorder->get_auto_code('code');
		$arr_save['wt_code'] = $arr_save['code'];
		$arr_origin = $this->Salesorder->select_one(array('wt_code'=>$arr_save['code']),array('_id','code'));
		if(!empty($arr_origin)){
			if( $arr_origin['code'] != $arr_save['code'] ) {
				return array(
				             'status' 	=> 'Current record has changed at JT. This operation will be bypass.',
				             '_ID'		=> (string)$this->Salesorder->mongo_id_after_save,
				             'code'		=> $salesorder_code,
				             );
			}
			$this->Salesorder->collection->update(array('_id'=>$arr_origin['_id']),array());
			$arr_save['_id'] = $arr_origin['_id'];
		}
		foreach(array('job_id','job_name','job_number','quotation_id','quotation_name','quotation_number','customer_po_no','delivery_method','shipper','shipper_account','shipper_id','our_rep','our_rep_id','our_csr','our_csr_id','contact_name') as $field){
			if(!isset($arr_save[$field]))
				$arr_save[$field] = '';
			else if(strpos($field, '_id') !== false && strlen($arr_save[$field]) == 24)
				$arr_save[$field] = new MongoId($arr_save[$field]);
		}
		//========================================================
		$this->selectModel('Company');
		$company = $this->Company->select_one(array('_id'=> $arr_save['company_id']),array('contact_default_id','contact_id','our_rep','our_rep_id','our_csr','our_csr_id','name','email','phone','addresses','addresses_default_key'));
		//Company info===========================================================================
		$this->__getCompanyInfo($arr_save,$company);

		if(!empty($arr_save['contact_name']) && (!isset($arr_save['contact_id']) || empty($arr_save['contact_id']))) //custom contact name
			unset($company['contact_default_id']);
		//Contact info===========================================================================
		else if(isset($company['contact_default_id']) && is_object($company['contact_default_id'])){
			$arr_save['contact_id'] = $company['contact_default_id'];
			$this->selectModel('Contact');
			$contact = $this->Contact->select_one(array('_id'=>$company['contact_default_id']),array('first_name','last_name'));
			$arr_save['contact_name'] = (isset($contact['first_name']) ? $contact['first_name'].' ' : '');
			$arr_save['contact_name'] .= (isset($contact['last_name']) ? $contact['last_name'] : '');
		}
		//========================================================================================
		$arr_save['sales_order_type'] = 'Sales Order';
		$arr_save['status'] = isset($arr_save['status']) ? $arr_save['status'] : 'New';
		$arr_save['salesorder_date'] = new MongoDate(strtotime(date('Y-m-d')));
		if(!isset($arr_save['payment_due_date']) || empty($arr_save['payment_due_date']))
			$arr_save['payment_due_date'] = new MongoDate($arr_save['salesorder_date']->sec + DAY * $arr_save['payment_terms']);

		$arr_save['heading'] = $arr_save['description'] = "Created from {$this->__site} at ".date('Y-m-d H:i:s');
		//Tax===============================================================
		$this->__getTax($arr_save);
		$taxper = $arr_save['taxval'];
		//Price=============================================================
		if(!isset($arr_save['products']))
			$arr_save['products'] = array();
		$arr_products = array();
		$this->__productDesciption = "Created from {$this->__site} with SO#".$arr_save['code'];
		$this->selectModel('Product');
		foreach($arr_save['products'] as $key=>$product){
			if(!isset($arr_save['products'][$key]['products_id']) || empty($arr_save['products'][$key]['products_id']))
				$arr_save['products'][$key]['products_id'] = '';
			else
				$arr_save['products'][$key]['products_id'] = new MongoId($arr_save['products'][$key]['products_id']);

			if(!isset($arr_save['products'][$key]['code']) || empty($arr_save['products'][$key]['code']))
				$arr_save['products'][$key]['code'] = '';

			$arr_save['products'][$key]['option_group'] = '';
			if(!isset($arr_save['products'][$key]['product_name']) || empty($arr_save['products'][$key]['product_name']))
				$arr_save['products'][$key]['products_name'] = $arr_save['products'][$key]['product_name'] = $arr_save['products'][$key]['products_name'].' - '.$arr_save['code'];
			else
				$arr_save['products'][$key]['products_name'] = $arr_save['products'][$key]['product_name'];
			
			/*if(isset($product['sku'])){
				$pro = $this->Product->select_one(array('sku' => $product['sku']),array('_id','code'));
				if(empty($pro)){
					$pro = $this->__createProductFromArray($product, $arr_save);
					$arr_products[] = $pro['_id'];
				}
				$arr_save['products'][$key]['products_id'] = $pro['_id'];
				$arr_save['products'][$key]['product_id'] = $pro['_id'];
				$arr_save['products'][$key]['code'] = $pro['code'];
			}*/
			$arr_save['products'][$key]['deleted'] = false;
			$arr_save['products'][$key]['sell_price'] = (isset($product['sell_price']) ? (float)$product['sell_price'] : 0);
			$arr_save['products'][$key]['unit_price'] = $arr_save['products'][$key]['custom_unit_price'] = (isset($product['unit_price']) ? (float)$product['unit_price'] : 0);
			$arr_save['products'][$key]['quantity'] = isset($product['quantity']) ? $product['quantity'] : 1;
			// $arr_save['products'][$key]['sub_total'] = round( ($arr_save['products'][$key]['quantity'] * $arr_save['products'][$key]['unit_price']),3);
			$arr_save['products'][$key]['taxper'] = $taxper;
			$arr_save['products'][$key]['tax'] = round( ($arr_save['products'][$key]['sub_total'] * $taxper / 100), 3 );
			$arr_save['products'][$key]['amount'] = round( ($arr_save['products'][$key]['tax'] + $arr_save['products'][$key]['sub_total']), 3 );
			// $arr_save['sum_sub_total']+= $arr_save['products'][$key]['sub_total'];
			// $arr_save['sum_amount']+= $arr_save['products'][$key]['amount'];
		}
		if(!isset($arr_save['sum_sub_total'])) $arr_save['sum_sub_total'] = 0;
		$arr_save['sum_tax'] = round( ($arr_save['sum_sub_total'] * $taxper / 100), 3 );
		$arr_save['sum_amount'] = $arr_save['sum_tax'] + $arr_save['sum_sub_total'];
		//==================================================================
		$arr_save['name'] = $arr_save['code'].' - '.(isset($arr_save['company_name']) ? $arr_save['company_name'] : '');
		$arr_save['modified_by_name'] = $this->__company;
		$arr_save['created_by_name'] = $this->__company;
		$arr_save['created_by'] = new MongoId("200000000000000000000000");
		$arr_save['api_key'] = (string)$this->__request['API'];
		$this->Salesorder->save($arr_save);
		//=============================================================================================================
		$this->selectModel('Stuffs');
		$accountant = $this->Stuffs->select_one(array('value'=>'Accountant'));
		if(isset($accountant['accountant_id'])){
			$this->selectModel('Task');
			$task = $this->Task->select_one(array('salesorder_id' => $this->Salesorder->mongo_id_after_save,'type' => 'Accountant'));
			if(empty($task)){
				$current_date = strtotime(date('Y-m-d H:00:00'));
				$arr_save = array();
				$arr_save['our_rep_type'] = 'contacts';
				$arr_save['salesorder_id'] = new MongoId($this->Salesorder->mongo_id_after_save);
				$arr_save['type_id'] = '';
				$arr_save['type'] = 'Accountant';
				$arr_save['name'] = (isset($so['name']) ? $so['name'] : '');
				$arr_save['our_rep'] = $accountant['accountant'];
				$arr_save['our_rep_id'] = $accountant['accountant_id'];
				$arr_save['work_start'] = new MongoDate($current_date);
				$arr_save['work_end'] = new MongoDate($current_date + HOUR);
				$arr_save['modified_by_name'] = $this->__company;
				$arr_save['created_by_name'] = $this->__company;
				$arr_save['api_key'] = (string)$this->__request['API'];
				$this->Task->arr_default_before_save = $arr_save;
				$this->Task->add();
			}
		}
		return array(
		             'status' 	=> 'This record has been created successful!. Record\'s _ID: '.$this->Salesorder->mongo_id_after_save,
		             '_ID'		=> (string)$this->Salesorder->mongo_id_after_save,
		             'code'		=> $salesorder_code,
		             'products' => $arr_products
		             );
	}

	private function __createContact()
	{
		$arr_save = $this->__request['DATA'];
		foreach($arr_save as $key => $value){
			if(strpos($key, '_id') !== false){
				if(strlen($value) == 24)
					$arr_save[$key] = new MongoId($value);
				else
					$arr_save[$key] = '';
			} else if(strpos($key,'deleted')){
				if($value == 'true')
					$arr_save[$key] = true;
				else
					$arr_save[$key] = false;
			}
		}
		$this->selectModel('Contact');
		$arr_save['no'] = $this->Contact->get_auto_code('no');
		$arr_save['modified_by_name'] = $this->__company;
		$arr_save['created_by_name'] = $this->__company;
		$arr_save['api_key'] = (string)$this->__request['API'];
		$this->Contact->save($arr_save);
		return array(
		             'status' 	=> 'This record has been created successful!. Record\'s _ID: '.$this->Contact->mongo_id_after_save,
		             '_ID'		=> (string)$this->Contact->mongo_id_after_save
		             );
	}

	private function __createQuotation($arr_save = array())
	{
		if(empty($arr_save))
			$arr_save = $this->__request['DATA'];
		$arr_save['company_id'] = $this->__companyId;
		//===================================
		$this->selectModel('Quotation');
		$arr_save['deleted'] = false;
		$quotation_code = $arr_save['code'] = isset($arr_save['code'])&&!empty($arr_save['code']) ? $arr_save['code'] : $this->Quotation->get_auto_code('code');
		$arr_origin = $this->Quotation->select_one(array('code'=>$arr_save['code']),array('_id'));
		if(!empty($arr_origin)){
			$this->Quotation->collection->update(array('_id'=>$arr_origin['_id']),array());
			$arr_save['_id'] = $arr_origin['_id'];
		}
		foreach(array('job_id','job_name','job_number','salesorder_id','salesorder_name','salesorder_number','customer_po_no','our_rep','our_rep_id','our_csr','our_csr_id') as $field){
			if(!isset($arr_save[$field]))
				$arr_save[$field] = '';
			else if(strpos($field, '_id') !== false && strlen($arr_save[$field]) == 24)
				$arr_save[$field] = new MongoId($arr_save[$field]);
		}
		//========================================================
		$this->selectModel('Company');
		$company = $this->Company->select_one(array('_id'=> $arr_save['company_id']),array('contact_default_id','contact_id','our_rep','our_rep_id','our_csr','our_csr_id','name','email','phone','addresses','addresses_default_key'));
		//Company info===========================================================================
		$this->__getCompanyInfo($arr_save,$company);
		//Contact info===========================================================================
		if(isset($arr_save['contact_default_id']) && is_object($arr_save['contact_default_id'])){
			$arr_save['contact_id'] = $arr_save['contact_default_id'];
			$this->selectModel('Contact');
			$contact = $this->Contact->select_one(array('_id'=>$arr_save['contact_default_id']),array('first_name','last_name'));
			$arr_save['contact_name'] = (isset($contact['first_name']) ? $contact['first_name'].' ' : '');
			$arr_save['contact_name'] .= (isset($contact['last_name']) ? $contact['last_name'] : '');
		}
		//========================================================================================
		$arr_save['quotation_status'] = isset($arr_save['quotation_status']) ? $arr_save['quotation_status'] : 'In progress';
		$arr_save['quotation_date'] = new MongoDate(strtotime(date('Y-m-d')));
		$arr_save['quotation_type'] = 'Quotation';
		$arr_save['payment_due_date'] = new MongoDate($arr_save['quotation_date']->sec + DAY * $arr_save['payment_terms']);
		$arr_save['heading'] = $arr_save['description'] = "Created from {$this->__site} at ".date('Y-m-d H:i:s');
		//Tax===============================================================
		$this->__getTax($arr_save);
		$taxper = $arr_save['taxval'];
		//Price=============================================================
		if(!isset($arr_save['products']))
			$arr_save['products'] = array();
		$arr_products = array();
		$this->__productDesciption = "Created from {$this->__site} with QT#".$arr_save['code'];
		$this->selectModel('Product');
		$arr_save['sum_sub_total'] = $arr_save['sum_amount'] = 0;
		foreach($arr_save['products'] as $key=>$product){
			$arr_save['products'][$key]['code'] = isset($product['code']) ? $product['code'] : '';
			$arr_save['products'][$key]['products_id'] = isset($product['products_id']) && is_object($product['products_id']) ? $product['products_id'] : '';
			$arr_save['products'][$key]['option_group'] = isset($product['option_group']) ? $product['option_group'] : '';
			/*if(isset($product['sku'])){
				$pro = $this->Product->select_one(array('sku' => $product['sku']),array('_id','code'));
				if(empty($pro)){
					$pro = $this->__createProductFromArray($product, $arr_save);
					$arr_products[] = $pro['_id'];
				}
				$arr_save['products'][$key]['products_id'] = $pro['_id'];
				$arr_save['products'][$key]['product_id'] = $pro['_id'];
				$arr_save['products'][$key]['code'] = $pro['code'];
			}*/
			$arr_save['products'][$key]['deleted'] = false;
			if(is_object($arr_save['products'][$key]['products_id'])){
				$pro = $this->Product->select_one(array('_id' => $arr_save['products'][$key]['products_id']), array('sell_price'));
			}
			$pro['sell_price'] = isset($pro['sell_price']) ? $pro['sell_price'] : 0;
			$arr_save['products'][$key]['sell_price'] = (isset($product['sell_price']) ? (float)$product['sell_price'] : $pro['sell_price']);
			$arr_save['products'][$key]['unit_price'] = $arr_save['products'][$key]['custom_unit_price'] = (isset($product['unit_price']) ? (float)$product['unit_price'] : 0);
			$arr_save['products'][$key]['quantity'] = isset($product['quantity']) ? $product['quantity'] : 1;
			$arr_save['products'][$key]['taxper'] = $taxper;
			if(!isset($arr_save['products'][$key]['option_for'])){
            	$cal_price = new cal_price;
           		$cal_price->price_break_from_to = $this->change_sell_price_company($arr_save['company_id'],$arr_save['products'][$key]['products_id']);
           		$cal_price->arr_product_items = $arr_save['products'][$key];
           		$cal_price->field_change = '';
           		$arr_save['products'][$key] = array_merge($arr_save['products'][$key],$cal_price->cal_price_items(1));
			}
	        $arr_save['products'][$key]['custom_unit_price'] = $arr_save['products'][$key]['unit_price'];
		}
        $arr_save = array_merge($arr_save,$this->new_cal_sum($arr_save['products']));
		//==================================================================
		$arr_save['name'] = $arr_save['code'].' - '.(isset($arr_save['company_name']) ? $arr_save['company_name'] : '');
		$arr_save['modified_by_name'] = $this->__company;
		$arr_save['created_by_name'] = $this->__company;
		$arr_save['created_by'] = new MongoId("200000000000000000000000");
		$arr_save['api_key'] = (string)$this->__request['API'];
		$this->Quotation->save($arr_save);
		//=============================================================================================================
		return array(
		             'status' 	=> 'This record has been created successful!. Record\'s _ID: '.$this->Quotation->mongo_id_after_save,
		             '_ID'		=> (string)$this->Quotation->mongo_id_after_save,
		             'code'		=> $quotation_code,
		             'products' => $arr_products
		             );
	}

	private function __createProduct($arr_save = array())
	{
		if(empty($arr_save))
			$arr_save = $this->__request['DATA'];
		$this->selectModel('Product');
		$arr_save['deleted'] = false;
		if( isset($arr_save['sku']) ) {
			$arr_origin = $this->Product->select_one(array('sku' => $arr_save['sku'],'api_key' => (string)$this->__request['API']),array('_id','products_upload'));
			if( isset($arr_origin['_id']) ) {
				$arr_save['_id'] = $arr_origin['_id'];
			}
			if( isset($arr_origin['products_upload']) ) {
				$arr_save['products_upload'] = $arr_origin['products_upload'];
			}
		}
		foreach($arr_save as $key => $value){
			if(strpos($key, '_id') !== false){
				if(strlen($value) == 24)
					$arr_save[$key] = new MongoId($value);
				else
					$arr_save[$key] = '';
			} else if(strpos($key,'deleted')){
				if($value == 'true')
					$arr_save[$key] = true;
				else
					$arr_save[$key] = false;
			} else if(strpos($key, '_price') !== false || strpos($key, 'size') !== false ){
				if( is_string($value) ) {
					$arr_save[$key] = $value;
				} else {
					$arr_save[$key] = (float)$value;
				}
			}  else if( is_numeric($value) ) {
				$arr_save[$key] = (int)$value;
			}
		}
		if( !isset($arr_save['_id']) ) {
			$arr_save['code'] = $this->Product->get_auto_code('code');
		}
		$arr_docuses = array();
		if( isset($arr_save['products_upload']) ){
			$thisfolder = 'upload'.DS.date("Y_m");
			$linkfolder = 'upload/'.date("Y_m");
			$folder = ROOT.DS.APP_DIR.DS.WEBROOT_DIR.DS.$thisfolder;
			if (!file_exists($folder)) {
				mkdir($folder, 0777, true);
			}
		   	$this->selectModel('Doc');
		    $this->selectModel('DocUse');
			foreach($arr_save['products_upload'] as $key => $upload) {
				if( isset($upload['doc_id']) && is_object($upload['doc_id']) ) continue;
			 	if( !isset($upload['path']) || empty($upload['path']) || !filter_var($upload['path'], FILTER_VALIDATE_URL) ) continue;
			 	$ext = strtolower(substr(strrchr($upload['path'],'.'),1));
			 	$upload_file = date('Y_m_d_His').'_'.rand(100000,999999).'.'.strtolower($ext);
			 	file_put_contents($folder.DS.$upload_file, file_get_contents($upload['path']));
			 	$file = str_replace(ROOT.DS.APP_DIR.DS.WEBROOT_DIR, '', $folder.DS.$upload_file);
			 	$arr_doc = array();
		        $arr_doc['no'] = $this->Doc->get_auto_code('no');
			 	$arr_doc['name'] = $arr_save['name'];
		        $arr_doc['path'] = $file;
		        $arr_doc['type'] = 'image/'.$ext;
		        $arr_doc['ext'] = $ext;
		        $arr_doc['create_by_module'] = 'Product';
		        $arr_doc['category'] = '';
		        $arr_doc['location'] = 'Inventory-Products';
		        $arr_doc['description'] = 'No.' . $arr_save['code'] . ':' . $arr_save['name'];
		        $this->Doc->save($arr_doc);

		        $arr_use = array();
		        $arr_use['doc_id'] = $this->Doc->mongo_id_after_save;
		        $arr_use['module'] = 'Product';
		        $arr_use['module_controller'] = 'products';
		        $arr_use['create_by_module'] = 'Product';
		        $arr_use['module_detail'] = $arr_save['name'];
		        $arr_use['module_id'] = '';
		        $arr_use['module_no'] = $arr_save['code'];
		        $arr_use['created_by_name'] = $arr_use['modified_by_name'] = $this->__company;
		        $file = str_replace('\\', '/', $file);
		        $arr_save['products_upload'][$key] = array(
		                                            'deleted'=>false,
		                                            'doc_id'=>$arr_use['doc_id'],
		                                            'path'  =>   $file
		                                            );
		        $this->DocUse->save($arr_use);
		        $arr_docuses[] = $this->DocUse->mongo_id_after_save;
			}
		}
		$arr_save['modified_by_name'] = $arr_save['created_by_name'] = $this->__company;
		$arr_save['api_key'] = (string)$this->__request['API'];
		$this->Product->save($arr_save);
		if( !empty($arr_docuses) ){
			$this->DocUse->update_all(array('_id' => array('$in' => $arr_docuses)),array('module_id' => $this->Product->mongo_id_after_save));
		}
		return array(
		             'status' 	=> 'This record has been created successful!. Record\'s _ID: '.$this->Product->mongo_id_after_save,
		             '_ID'		=> (string)$this->Product->mongo_id_after_save
		             );
	}

	private function __reportSalesorder()
	{
		if($this->_pdf) {
			$file = $this->requestAction("/salesorders/view_pdf/1/{$this->_type}/{$this->__request['_ID']}");
			$name = md5($this->_type.$this->__request['_ID']).'.pdf';
			$file = preg_replace('/\x{EF}\x{BB}\x{BF}/','',$file);
			if(file_exists(WWW_ROOT.DS.'files'.DS.$name))
				@unlink(WWW_ROOT.DS.'files'.DS.$name);
			copy(WWW_ROOT.DS.'upload'.DS.$file,WWW_ROOT.DS.'files'.DS.$name);
			return 	array(
			             'file' =>  URL.'/files/'.$name,
			             );
		} else {
			$html = $this->requestAction("/salesorders/view_pdf/1/{$this->_type}/{$this->__request['_ID']}/1");
			$html = str_replace('img/logo_anvy.jpg',URL.'/img/logo_anvy.jpg', $html);
			$html = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $html);
			$name = md5($this->_type.$this->__request['_ID']).'.html';
			if(file_exists(WWW_ROOT.DS.'files'.DS.$name))
				@unlink(WWW_ROOT.DS.'files'.DS.$name);
			file_put_contents(WWW_ROOT.DS.'files'.DS.$name,$html);
			return 	array(
			             'file' =>  URL.'/files/'.$name,
			             );
		}
	}

	private function statusModule()
	{
		$module = $this->__request['MODULE'];
		$arrReturn = array();
		$this->selectModel('Setting');
		switch ($module) {
			case 'salesorder':
				$setting_value = 'salesorders_status';
				break;
			case 'salesinvoice':
				$setting_value = 'salesinvoices_status';
				break;
			case 'quotation':
				$setting_value = 'quotations_status';
				break;
			case 'shipping':
				$setting_value = 'shipping_statuses';
				break;
			case 'job':
				$setting_value = 'jobs_status';
				break;
			case 'task':
				$setting_value = 'tasks_status';
				break;
			default:
				return $arrReturn;
				break;
		}
		$arrData = $this->Setting->select_one(array(
								'setting_value' => $setting_value
							), array('option'));
		if( !isset($arrData['option']) || empty($arrData['option']) )
			return $arrReturn;
		foreach($arrData['option'] as $option){
			if(isset($option['deleted']) && $option['deleted']) continue;
			if(isset($option['inactive']) && $option['inactive']) continue;
			$arrReturn[] = $option['value'];
		}
		return $arrReturn;
	}

	private function pricing()
	{
		if(!isset($this->__request['DATA'])) {
			return 'DATA must not be empty!';
		}
		$data = array_merge( array('product' => array(), 'options' => array() ), $this->__request['DATA'] );
		if( ! isset($data['product']['_id']) || strlen((string)$data['product']['_id']) != 24 ) {
			return 'Product ID must not be empty!';
		}
		$this->selectModel('Product');
		$product = $this->Product->select_one(array('_id' => new MongoId($data['product']['_id'])), array('_id','name', 'sell_by', 'sell_price','options', 'pricebreaks', 'sellprices', 'pricing_method'));
		if( empty($product) ) {
			return 'Product did not exist!';
		}

		$sizeW = (float) ( isset($data['product']['sizew']) ? $data['product']['sizew'] : 0 );
		$sizeH = (float) ( isset($data['product']['sizeh']) ? $data['product']['sizeh'] : 0 );
		$quantity = (float) ( isset($data['product']['quantity']) ? $data['product']['quantity'] : 0 );
		$companyId = isset($data['company_id']) && strlen((string)$data['company_id']) == 24 ? new MongoId($data['company_id']) : '';

		$product = array_merge($product, array('sizew' => $sizeW, 'sizeh' => $sizeH, 'quantity' => $quantity));

		if( is_object($companyId) ) {
			$this->selectModel('Company');
			$company = $this->Company->select_one(array('_id' => $companyId), array('net_discount'));
		} else {
			$company = array();
		}

		$arrOptions = array();
		foreach($data['options'] as $option){
			$optionId = (string)$option['_id'];
			foreach($product['options'] as $opt_k => $opt){
				if( isset($opt['deleted']) && $opt['deleted'] || !is_object($opt['product_id']) ){
					unset($product['options'][$opt_k]); continue;
				}
				if( (string)$opt['product_id'] == $optionId ){
					$tmpOpt = $this->Product->select_one(array('_id' => new MongoId( $opt['product_id'] )), array('name', 'sell_price','sell_by', 'pricebreaks', 'sellprices', 'pricing_method'));
					$opt = array_merge($opt, $option, $tmpOpt);
					if( !isset($opt['same_parent']) || !$opt['same_parent'] ){
						$opt['same_parent'] = 0;
						$opt['quantity'] = isset($opt['quantity']) && $opt['quantity'] ? $opt['quantity'] : 1;
					} else {
						$opt['quantity'] = isset($opt['quantity']) && $opt['quantity'] ? $opt['quantity'] : 1;
					}
					$opt['require'] = isset( $opt['require'] ) ? (int)$opt['require'] : 0;
					$arrOptions[] = $opt;
					unset($product['options'][$opt_k]);
					break;
				}
			}
		}

		$plusSellPrice = $totalOtherLine = 0;
		//=============Cal-Bleed=============
		$cal_price = new cal_price;
    	$lineBleed = $cal_price->cal_bleed($product, true);
    	if( !empty($lineBleed) ){
        	$product['bleed_sizew'] = $lineBleed['bleed_sizew'];
        	$product['bleed_sizeh'] = $lineBleed['bleed_sizeh'];
    	} else {
    		$product['bleed_sizew'] = $product['bleed_sizeh'] = 0;
    	}
    	//=============End Cal-Bleed=========
		//=============Loop option bleed=============
    	foreach($arrOptions as $option) {
    		if( isset($option['same_parent'])&&$option['same_parent'] ){
    				$option['sizew'] = $sizeW;
    				$option['sizeh'] = $sizeH;
    				$option['sizew_unit'] = $option['sizeh_unit'] = 'in';
					$cal_price = new cal_price;
    				$optionBleed = $cal_price->cal_bleed($option, true);
					if( !empty($optionBleed) ) {
	                	$product['bleed_sizew'] += $optionBleed['bleed_sizew'];
	                	$product['bleed_sizeh'] += $optionBleed['bleed_sizeh'];
					}
    		}
    	}
		//=============Check bleed=============
        if( isset($product['bleed_sizew']) && !$product['bleed_sizew'] ) unset($product['bleed_sizew']);
        if( isset($product['bleed_sizeh']) && !$product['bleed_sizeh'] ) unset($product['bleed_sizeh']);
        //=============End Check bleed=========
    	//=============Loop option bleed=========
		foreach($arrOptions as $option) {
			if( isset($option['same_parent']) && $option['same_parent'] ){
				$option['sizew'] = $sizeW;
				$option['sizeh'] = $sizeH;
				$option['sizew_unit'] = $option['sizeh_unit'] = 'in';
				if( isset($product['bleed_sizew']) ) {
					$option['bleed_sizew'] = $product['bleed_sizew'];
				}
				if( isset($product['bleed_sizeh']) ) {
					$option['bleed_sizeh'] = $product['bleed_sizeh'];
				}
				$tmp = $option;

                $cal_price = new cal_price;
                $cal_price->arr_product_items   = $tmp;
                $cal_price->price_break_from_to = $this->change_sell_price_company($companyId, $tmp['product_id']);
                $cal_price->arr_product_items['quantity'] *= $quantity;
                $tmp = $cal_price->cal_price_items();

				$option['sell_price'] = $tmp['sell_price'];
				$cal_price = new cal_price;
                $cal_price->arr_product_items   = $option;
				unset($tmp);
			} else {
				$cal_price = new cal_price;
                $cal_price->arr_product_items   = $option;
                $cal_price->price_break_from_to = $this->change_sell_price_company($companyId, $option['product_id']);
			}
            $option = $cal_price->cal_price_items();

			if( $option['same_parent'] ) {
				$plusSellPrice += $option['sub_total'];//S.P thi cong don de nhan qty line chinh
			}
			else {
				$totalOtherLine += $option['sub_total'];//khong phai SP thi tinh rieng va total lai
			}
		}
		//=============Check bleed=============
        if( isset($product['bleed_sizew']) && !$product['bleed_sizew'] ) unset($product['bleed_sizew']);
        if( isset($product['bleed_sizeh']) && !$product['bleed_sizeh'] ) unset($product['bleed_sizeh']);
        //=============End Check bleed=========
		$product['plus_sell_price'] = $plusSellPrice;
		$cal_price = new cal_price;
        $cal_price->arr_product_items   = $product;
        $cal_price->price_break_from_to = $this->change_sell_price_company($companyId, $product['_id']);
        $product = $cal_price->cal_price_items();
		if( isset($company['net_discount']) ){
			$this->netDiscount($product['sub_total'], $company['net_discount']);
			$this->netDiscount($product['sell_price'], $company['net_discount']);
			$product['unit_price'] = $product['sell_price'];
		}
		$product['sub_total'] += $totalOtherLine;
		if( $quantity ) {
			$product['sell_price'] = $product['sub_total'] /$quantity;
		}
		return array(
					'sell_price' 	=> number_format($product['sell_price'],2),
					'sub_total' 	=> number_format($product['sub_total'],2)
				);
	}

	private function netDiscount(&$sum, $discount)
	{
		$discountPrice = 0;
		if( $discount ) {
			$discount = (float)$discount;
			$discountPrice = round(( $sum * $discount ) / 100, 3);
			$sum -= $discountPrice;
		}
		return $discountPrice;
	}

	private function sendBack($data)
	{
		$arr_return = $data;
		if(is_string($data))
			$arr_return = array('message'=>$data);
		$json = json_encode($arr_return);
		$this->response->body($json);
	}

	private function dataHandle(&$array)
	{
		if(is_array($array)){
			foreach($array as $key=>$value){
				if( is_object($value) ) {
					if( strpos($key, 'date') !== false
					   || strpos($key, 'work_') !== false){
						$array[$key] = date('d-m-Y',$value->sec);
					} else
						$array[$key] = (string)$value;
				} else if( is_array($value) ){
					$this->dataHandle($value);
					$array[$key] = $value;
				} else if( strpos($key, 'sum_')!== false )
					$array[$key] = number_format((float)$value,2);
				else if( is_bool($value) ){
					if($value)
						$array[$key] = 'true';
					else
						$array[$key] = 'false';
				}
			}
		}
	}

	private function convertToArray($arrData)
	{
		if(is_object($arrData)) $arrData = (array) $arrData;
	    if(is_array($arrData)) {
	        $newArray = array();
	        foreach($arrData as $key => $val) {
	            $newArray[$key] = $this->convertToArray($val);
	        }
	    } else
	    	$newArray = $arrData;
	    return $newArray;
	}

	private function convert($arrData)
	{
		foreach(array('WHERE','DATA') as $type){
			if(isset($arrData[$type]) && !empty($arrData[$type]) )
				$arrData[$type] = $this->convertToMongoArray($arrData[$type]);
		}
		return $arrData;
	}

	private function convertToMongoArray(&$arrData)
	{
		foreach($arrData as $key => $value){
			if(is_array($value)) {
				$arrData[$key] = $this->convertToMongoArray($value);
			} else {
				if(is_numeric($key)) {
					return $arrData;
				}if($key == '$id') {
					return new MongoId($value);
				} else if($key == 'sec') {
					return new MongoDate($value);
				} else if($key == 'regex') {
					return new MongoRegex("/$value/".$arrData['flags']);
				} if(is_string($key)) {
					return $arrData;
				}
				return $value;
			}
		}
		return $arrData;
	}

	private function convertToMongoString($arrData, $string = '')
	{
		foreach($arrData as $key => $value) {
			if( strpos($key, '$') !== false ) {
				$key = "\\$key";;
			}
			if(is_object($value)){
				$class = get_class($value);
				if( $class == 'MongoRegex' ){
					$value = (array)$value;
					$option = '';
					if( !empty($value['flags']) )
						$option = ", \$options: '{$value['flags']}'";
					$string .= "{$key} : { \$regex: '{$value['regex']}' {$option} }, ";
				} else if( $class == 'MongoId' ) {
					$value = (string)$value;
					$string .= "$key : ObjectId('$value'), ";
				} else if( $class == 'MongoDate' ) {
					$string .= $key." : new Date('".date('Y-m-d',$value->sec)."'), ";
				}
			} else if(is_array($value)) {
				$isArr = false;
				if(in_array($key, ['$or','$nin','$in']))
					$isArr = true;
				if($isArr) {
					if(is_numeric($key)) {
						$string .= "[".$this->convertToMongoString($value)."], ";
					} else {
						$string .= "$key : [".$this->convertToMongoString($value)."], ";
					}
				} else {
					if(is_numeric($key)) {
						$string .= "{".$this->convertToMongoString($value)."}, ";
					} else {
						$string .= "$key : {".$this->convertToMongoString($value)."}, ";
					}
				}
			} else {
				if(is_numeric($key)) {
					if(!is_bool($value)){
						if( !is_numeric($value) )
							$string .= "'$value', ";
						else
							$string .= "$value, ";
					} else {
						if($value)
							$string .= "true, ";
						else
							$string .= "false, ";
					}
				} else {
					if(!is_bool($value)){
						if( !is_numeric($value) )
							$string .= "$key : '$value', ";
						else
							$string .= "$key : $value, ";
					} else {
						if($value)
							$string .= "$key : true, ";
						else
							$string .= "$key : false, ";
					}
				}
			}
		}
		$string = rtrim($string, ', ');
		return $string;
	}

}