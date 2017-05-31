<?php
class SalesaccountsController extends MobileAppController {
	var $modelName = 'Salesaccount';
	var $name = 'Salesaccounts';
	function beforeFilter() {
		parent::beforeFilter();
		if(!$this->request->is('ajax'))
			$this->set('arr_tabs', array(
			           			'invoices',
			           			'receipts' => array('receipts'),
			           			'comms' => array('communications'),
			           			'task'  => array('tasks'),
			           			//'other'  => array('#'),
			           			));
	}

	public function entry($id = '0', $num_position = -1) {
		$arr_tmp = $this->entry_init($id, $num_position, 'Salesaccount', 'salesaccounts');
		//$arr_tmp['date'] = (is_object($arr_tmp['date'])) ? date('m/d/Y', $arr_tmp['date']->sec) : '';
		//pr($arr_tmp);die;

		$this->selectModel('Setting');

		$arr_salesaccounts_status = $this->Setting->select_option(array('setting_value' => 'salesaccounts_status'), array('option'));
		$this->set('arr_salesaccounts_status', $arr_salesaccounts_status);

		$arr_salesaccounts_card_holder = $this->Setting->select_option(array('setting_value' => 'salesaccounts_card_holder'), array('option'));
		$this->set('arr_salesaccounts_card_holder', $arr_salesaccounts_card_holder);

		$arr_salesaccounts_card_type = $this->Setting->select_option(array('setting_value' => 'salesaccounts_card_type'), array('option'));
		$this->set('arr_salesaccounts_card_type', $arr_salesaccounts_card_type);

		$arr_usually_pay_by = $this->Setting->select_option(array('setting_value' => 'salesaccounts_usually_pay_by'), array('option'));
		$this->set('arr_usually_pay_by', $arr_usually_pay_by);

		$arr_salesaccounts_nominal_code = $this->Setting->select_option(array('setting_value' => 'salesaccounts_nominal_code'), array('option'));
		$this->set('arr_salesaccounts_nominal_code', $arr_salesaccounts_nominal_code);

		$arr_salesaccounts_tax_code = $this->Setting->select_option(array('setting_value' => 'salesaccounts_tax_code'), array('option'));
		$this->set('arr_salesaccounts_tax_code', $arr_salesaccounts_tax_code);

		$arr_salesaccounts_payment_terms = $this->Setting->select_option(array('setting_value' => 'salesinvoices_payment_terms'), array('option'));
		$this->set('arr_salesaccounts_payment_terms', $arr_salesaccounts_payment_terms);

		if (isset($arr_tmp['type_id'])) {
			$arr_tmp['type'] = $arr_tmp['type_id'];
		}

		$this->selectModel('Company');
		$company = $this->Company->select_one(array('_id'=>$arr_tmp['company_id']),array('name','email','phone','fax','our_rep','addresses_default_key','addresses'));
		//pr($company['addresses']['0']);die;

		$this->selectModel('Country');
		$options = array();
		$options['countries'] = $this->Country->get_countries();
		$this->selectModel('Province');
		$options['provinces'] = $this->Province->get_all_provinces();
		$this->set('options',$options);


		$arr_tmp1['Salesaccount'] = $arr_tmp;
		$arr_tmp1['Company'] = $company['addresses']['0'];

		$this->data = $arr_tmp1;
		$arr_salesaccount_id = array();
		if (isset($arr_tmp['our_rep_id']))
			$arr_salesaccount_id[] = $arr_tmp['our_rep_id'];
	}

	function auto_save() {
		if (!empty($this->data)) {
			$id = $this->get_id();
			$arr_return = array();
			$arr_post_data = $this->data['Salesaccount'];
			$arr_save = $arr_post_data;
			$arr_save['_id'] = new MongoId($id);
			$this->selectModel('Salesaccount');

			if(isset($arr_save['no'])){
				if(!is_numeric($arr_save['no'])){
					echo json_encode(array('status'=>'error','message'=>'must_be_numberic'));
					die;
				}
				$arr_tmp = $this->Salesaccount->select_one(array('no' => (int) $arr_save['no'], '_id' => array('$ne' => new MongoId($id))));
				if (isset($arr_tmp['no'])) {
					echo json_encode(array('status'=>'error','message'=>'ref_no_existed'));
					die;
				}
			}

			$arr_tmp = $this->Salesaccount->select_one(array('_id' =>  new MongoId($id)),array('status','company_id','custom_po_no'));

			if(isset($arr_save['custom_po_no']) && isset($arr_tmp['custom_po_no']) && $arr_save['custom_po_no']!= $arr_tmp['custom_po_no']){
				foreach(array('Salesinvoice','Salesorder','Quotation') as $model){
					$this->selectModel($model);
					$this->$model->collection->update(
		                                           array('salesaccount_id'=>new MongoId($arr_save['_id'])),
		                                           array('$set'=>array(
		                                                 'customer_po_no'=>$arr_save['custom_po_no']
		                                                 )
		                                           ),
		                                           array('multiple'=>true)
	                                           );
				}
			}

			if (isset($arr_save['status']))
				$arr_save['status_id'] = $arr_save['status'];
			if (isset($arr_save['type']))
				$arr_save['type_id'] = $arr_save['type'];
			
			/*if(strlen($arr_save['our_rep_id'])==24)
				$arr_save['our_rep_id'] = new MongoId($arr_save['our_rep_id']);
			else
				$arr_save['our_rep_id'] = '';*/

			if( strlen($arr_save['name']) > 0 )
				$arr_save['name']{0} = strtoupper($arr_save['name']{0});

			$this->selectModel('Salesaccount');
			if ($this->Salesaccount->save($arr_save))
				$arr_return['status'] = 'ok';
			else
				$arr_return = array('status'=>'error','message'=>'Error: ' . $this->Salesaccount->arr_errors_save[1]);
			echo json_encode($arr_return);
		}
		die;
	}

	function lists( $content_only = '' ) {
		$this->selectModel('Salesaccount');
		$limit = 20; $skip = 0;
		// dùng cho sort
		$sort_field = 'no';
		$sort_type = -1;
		if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
			if( $_POST['sort']['type'] == 'desc' ){
				$sort_type = -1;
			}
			$sort_field = $_POST['sort']['field'];
			$this->Session->write('salesaccounts_lists_search_sort', array($sort_field, $sort_type));

		}elseif( $this->Session->check('salesaccounts_lists_search_sort') ){
			$session_sort = $this->Session->read('salesaccounts_lists_search_sort');
			$sort_field = $session_sort[0];
			$sort_type = $session_sort[1];
		}
		$arr_order = array($sort_field => $sort_type);
		$this->set('sort_field', $sort_field);
		$this->set('sort_type', ($sort_type === 1)?'asc':'desc');

		$this->selectModel('Contact');
		$this->set('model_contact', $this->Contact);

		// dùng cho điều kiện
		$cond = array();
		if( $this->Session->check('salesaccounts_entry_search_cond') ){
			$cond = $this->Session->read('salesaccounts_entry_search_cond');
		}

		// dùng cho phân trang
		if(isset($_POST['offset'])){
			$skip = $_POST['offset'];
		}
		// query
		$arr_salesaccounts = $this->Salesaccount->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'limit' => $limit,
			'skip' => $skip
		));
		$this->set('arr_salesaccounts', $arr_salesaccounts);
		$this->selectModel('Setting');
		$this->set('arr_salesaccounts_type', $this->Setting->select_option(array('setting_value' => 'salesaccounts_type'), array('option')));

		$this->selectModel('Equipment');
		$arr_equipment = $this->Equipment->select_list( array(
			'arr_field' => array('_id', 'name'),
			'arr_order' => array('name' => 1)
		));
		$this->set( 'arr_equipment', $arr_equipment );
		if($content_only!=''){
			$this->render("lists_ajax");
		}
	}

	function delete($id = 0) {
		$arr_save['_id'] = $id;
		$arr_save['deleted'] = true;
		$error = 0;
		if (!$error) {
			$this->selectModel('Salesaccount');
			if ($this->Salesaccount->save($arr_save)) {
				$this->Session->delete($this->name.'ViewId');
				if($this->request->is('ajax'))
					echo 'ok';
				else
					$this->redirect('/mobile/salesaccounts/entry');
			} else {
				echo 'Error: ' . $this->Salesaccount->arr_errors_save[1];
			}
		}
		die;
	}

	public function add() {
		$this->selectModel('Salesaccount');
		$arr_save = array();

		$this->Salesaccount->arr_default_before_save = $arr_save;
		if ($this->Salesaccount->add())
			$this->redirect('/mobile/salesaccounts/entry/' . $this->Salesaccount->mongo_id_after_save);
		die;
	}

    public function tasks(){
    	if(isset($_POST['add']))
    		return $this->tasks_add();
    	$offset = 0;
    	if(isset($_POST['offset']))
    		$offset = $_POST['offset'];
    	$salesaccount_id = $this->get_id();

        $this->selectModel('Salesaccount');
        $arr_acc = $this->Salesaccount->select_one(array('_id' => new MongoId($salesaccount_id)),array('company_id'));
        $this->set('company_id', $arr_acc['company_id']);
        $this->selectModel('Task');
        $arr_tasks = $this->Task->select_all(array(
                                            'arr_where' => array('company_id' => $arr_acc['company_id']),
                                            'arr_field' => array('no','name','type','our_rep','work_start','work_end','status'),
                                            'arr_order' => array('work_start' => 1),
                                            ));



    	$arr_data = array('data'=>array());
		foreach($arr_tasks as $task){
			$task['_id'] = (string)$task['_id'];
			$task['name'] = isset($task['name']) ? $task['name'] : '';
			$task['type'] = isset($task['type']) ? $task['type'] : '';
			$task['our_rep'] = isset($task['our_rep']) ? $task['our_rep'] : '';
			$task['work_start'] = date('d M, Y', $task['work_start']->sec);
			$task['work_end'] = date('d M, Y', $task['work_end']->sec);
			$arr_data['data'][] = $task;
		}
        if($this->request->is('ajax')){
        	if($arr_tasks->count(true) == 0){
        		echo json_encode(array('empty'=>true));
        		die;
        	}
			echo json_encode($arr_data);
			die;
        } else {
	        $this->set('arr_tasks',$arr_data['data']);
        	$arr_data['field'] = array(
	                           'no'=>array(
	                                       'label' => 'Job no',
	                                       'type' => '',),
	                           'name'=>array(
	                                       'label' => 'Task',
	                                       'type' => '',
	                                       ),
	                           'type'=>array(
	                                       'label' => 'Type',
	                                       'type' => ''),
	                           'our_rep'=>array(
	                                       'label' => 'Responsible',
	                                       'type' => ''),
	                           'work_start'=>array(
	                                       'label' => 'Work start',
	                                       'type' => ''),
	                           'work_end'=>array(
	                                       'label' => 'Work end',
	                                       'type' => '',
	                                       ),
	                           'status'=>array(
	                                       'label' => 'Status',
	                                       'type' => '',),
	                           );
			$arr_data['header'] = array(
			                        'link_to_entry' => M_URL.'/tasks/entry/',
			                        'link_to_entry_value' => 'no',
			                        'info' => 'name'
			                            );
			$this->set('arr_data',$arr_data);
        }
    }
    function tasks_delete($task_id){
    	$this->selectModel('Task');
    	$this->Task->save(array('_id' => new MongoId($task_id),'deleted' => true));
    	echo 'ok';
    	die;
    }


    function get_salesaccount_info($arr_salesaccount){
        $arr_save = array();
        $arr_save['salesaccount_id'] = $arr_salesaccount['_id'];

        if(isset($arr_salesaccount['our_rep_id']) && is_object($arr_salesaccount['our_rep_id'])){
            $arr_save['our_rep'] = $arr_salesaccount['our_rep'];
            $arr_save['our_rep_id'] = $arr_salesaccount['our_rep_id'];
        }
        return $arr_save;
    }


    public function popup($key = '') {
		$this->set('key', $key);

		$limit = 100; $skip = 0; $cond = array();

		// Nếu là search GET
		if (!empty($_GET)) {

			$tmp = $this->data;

			if (isset($_GET['company_id'])) {
				$cond['company_id'] = new MongoId($_GET['company_id']);
				$tmp['Salesaccount']['company'] = $_GET['company_name'];
			}

			if (isset($_GET['is_customer'])) {
				$cond['is_customer'] = 1;
				$tmp['Salesaccount']['is_customer'] = 1;
			}

			if (isset($_GET['is_employee'])) {
				$cond['is_employee'] = 1;
				$tmp['Salesaccount']['is_employee'] = 1;
			}

			$this->data = $tmp;
		}

		// Nếu là search theo phân trang
		$page_num = 1;
		$limit = 10; $skip = 0;
		if( isset($_POST['pagination']) && $_POST['pagination']['page-num'] > 0 ){

			// $limit = $_POST['pagination']['page-list'];
			$page_num = $_POST['pagination']['page-num'];
			//$limit = $_POST['pagination']['page-list'];
			$skip = $limit*($page_num - 1);
		}
		$this->set('page_num', $page_num);
		$this->set('limit', $limit);

		//$arr_order = array('date' => -1);
		$arr_order = array('company_name' => 1);
		if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
			$sort_type = 1;
			if( $_POST['sort']['type'] == 'desc' ){
				$sort_type = -1;
			}
			$arr_order = array($_POST['sort']['field'] => $sort_type);

			$this->set('sort_field', $_POST['sort']['field']);
			$this->set('sort_type', ($sort_type === 1)?'asc':'desc');
			$this->set('sort_type_change', ($sort_type === 1)?'desc':'asc');
		}

		// search theo submit $_POST kèm điều kiện
		if (!empty($this->data) && !empty($_POST) && isset($this->data['Salesaccount']) ) {
			$arr_post = $this->data['Salesaccount'];

			if (isset($arr_post['contact_name']) && strlen($arr_post['contact_name']) > 0) {
				$cond['contact_name'] = new MongoRegex('/' . trim($arr_post['contact_name']) . '/i');
			}

		}

		$this->selectModel('Salesaccount');
		$arr_salesaccount = $this->Salesaccount->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'limit' => $limit,
			'skip' => $skip,
			'arr_field' => array('no', 'company_id')
		));
		$this->set('arr_salesaccount', $arr_salesaccount);
		$this->selectModel('Company');
        $this->set('model_company', $this->Company);

		$total_page = $total_record = $total_current = 0;
		if( is_object($arr_salesaccount) ){
			$total_current = $arr_salesaccount->count(true);
			$total_record = $arr_salesaccount->count();
			if( $total_record%$limit != 0 ){
				$total_page = floor($total_record/$limit) + 1;
			}else{
				$total_page = $total_record/$limit;
			}
		}
		$this->set('total_current', $total_current);
		$this->set('total_page', $total_page);
		$this->set('total_record', $total_record);

		$this->layout = 'ajax';
	}

	public function requirements($id = '0', $num_position = -1) {
		$arr_tmp = $this->entry_init($id, $num_position, 'Salesaccount', 'salesaccounts');
		if (!isset($arr_tmp['detail'])) $arr_tmp['detail'] = $arr_tmp['detail'] = '';
		$this->set('detail',$arr_tmp['detail']);
	
		$arr_tmp1['Salesaccount'] = $arr_tmp;
		$this->data = $arr_tmp1;
		$arr_salesaccount_id = array();
		//pr($arr_tmp);die;
	}
	public function keywords($id = '0', $num_position = -1) {
		$arr_tmp = $this->entry_init($id, $num_position, 'Salesaccount', 'salesaccounts');
		$this->selectModel('Setting');
		
		$arr_salesaccount_keywords = array(""=>"");
		$arr_salesaccount_keywords = $arr_salesaccount_keywords + $this->Setting->select_option(array('setting_value' => 'salesaccount_keywords'), array('option'));

		if (!isset($arr_tmp['keywords'])) $arr_tmp['keywords'] = array();
		$this->set('keywords',$arr_tmp['keywords']);
		$this->set('arr_salesaccount_keywords',$arr_salesaccount_keywords);

		$arr_tmp1['Salesaccount'] = $arr_tmp;
		$this->data = $arr_tmp1;
		$arr_salesaccount_id = array();
		
	}

    public function invoices(){
    	if(isset($_POST['add']))
    	   return $this->invoices_add();
    	$offset = 0;
    	$this->selectModel('Salesaccount');
    	$salesaccount_id = $this->get_id();
    	$arr_acc= $this->Salesaccount->select_one(array('_id'=>new MongoId($salesaccount_id)));
    	$this->set('company_id',$arr_acc['company_id']);

    	if(isset($_POST['offset']))
    		$offset = $_POST['offset'];
    	$id = $this->get_id();
        $this->selectModel('Salesinvoice');
        $this->selectModel('Receipt');
        /*$arr_invoices = $this->Salesinvoice->select_all(array(
                                                'arr_where' => array('salesaccount_id'=>new MongoId($id)),
                                                'arr_order' => array('invoice_date' => -1),
                                                'arr_field' => array('code','invoice_type','invoice_date','invoice_status','our_rep','sum_sub_total','other_comment'),
                                                'skip' => $offset,
                                                'limit'  => 10
                                                ));*/
        $arr_invoices = $this->Salesinvoice->select_all(array(
                                               'arr_where'=>array('company_id'=>new MongoId($arr_acc['company_id'])),
                                               'arr_field'=>array('code','date_modified','sum_sub_total','sum_tax','sum_amount','paid_date','total_receipt','taxval','invoice_status','payment_terms','payment_due_date','our_rep','our_csr','invoice_date')
                                               ));
        /*foreach ($arr_invoices as $key => $value) {
        	# code... 
        	pr($value);
        }die;*/
       

        $arr_data = array('data'=>array());
		foreach($arr_invoices as $invoice){
            if($invoice['invoice_status'] == 'Credit' && $invoice['sum_sub_total'] > 0)
                $invoice['sum_sub_total'] = (float)$invoice['sum_sub_total'] * -1;
			$invoice['_id'] = (string)$invoice['_id'];
			$invoice['invoice_type'] = isset($invoice['invoice_type']) ? $invoice['invoice_type'] : '';
			$invoice['invoice_date'] = date('d M, Y', $invoice['invoice_date']->sec);
			$invoice['date_modified'] = date('d M, Y', $invoice['date_modified']->sec);
			$invoice['our_rep'] = isset($invoice['our_rep']) ? $invoice['our_rep'] : '';
			$invoice['other_comment'] = isset($invoice['other_comment']) ? $invoice['other_comment'] : '';
			$invoice['sum_sub_total'] = number_format((float)$invoice['sum_sub_total'],2);
			$arr_data['data'][] = $invoice;
		}
        if($this->request->is('ajax')){
        	if($arr_invoices->count(true) == 0){
        		echo json_encode(array('empty'=>true));
        		die;
        	}
			echo json_encode($arr_data);
			die;
        } else {
	        $this->set('arr_invoices',$arr_data['data']);
        	$arr_data['field'] = array(
	                           'code'=>array(
	                                       'label' => 'Ref no',
	                                       'type' => '',),
	                           'invoice_type'=>array(
	                                       'label' => 'Type',
	                                       'type' => '',
	                                       ),
	                           'invoice_date'=>array(
	                                       'label' => 'Date',
	                                       'type' => ''),
	                           'invoice_status'=>array(
	                                       'label' => 'Status',
	                                       'type' => ''),
	                           'our_rep'=>array(
	                                       'label' => 'Our rep',
	                                       'type' => ''),
	                           'sum_sub_total'=>array(
	                                       'label' => 'Ex. Tax total',
	                                       'type' => '',
	                                       ),
	                           'other_comment'=>array(
	                                       'label' => 'Comments',
	                                       'type' => '',
	                                       ),
	                           );
			$arr_data['header'] = array(
			                        'link_to_entry' => M_URL.'/salesinvoices/entry/',
			                        'link_to_entry_value' => 'code',
			                        'info' => 'other_comment'
			                            );
			$this->set('arr_data',$arr_data);
			$this->set('id',$id);
			$sum_amount = $this->Salesinvoice->sum('sum_amount', 'tb_salesinvoice' , array(
			                'salesaccount_id' => new MongoId($id),
			                'invoice_status' => array('$ne' => 'Cancelled'),
			                'deleted' => false
			                ));
			$sum_tax = $this->Salesinvoice->sum('sum_tax', 'tb_salesinvoice' , array(
			                'salesaccount_id' => new MongoId($id),
			                'invoice_status' => array('$ne' => 'Cancelled'),
			                'deleted' => false
			                ));
			$sum_sub_total = $this->Salesinvoice->sum('_', 'tb_salesinvoice' , array(
			                'salesaccount_id' => new MongoId($id),
			                'invoice_status' => array('$ne' => 'Cancelled'),
			                'deleted' => false
			                ));
			$this->set('sum_amount',$sum_amount);
			$this->set('sum_tax',$sum_tax);
			$this->set('sum_sub_total',$sum_sub_total);
        }
    }

    function invoices_add(){
    	$id = $this->get_id();
    	$this->selectModel('Salesaccount');
    	$arr_salesaccount = $this->Salesaccount->select_one(array('_id' => new MongoId($id)));
        if(isset($arr_salesaccount['company_id']) && is_object($arr_salesaccount['company_id'])){
	        $this->selectModel('Company');
	        //$arr_tmp = $this->Company->select_one(array('_id' => new MongoId($arr_salesaccount['company_id'])),array('name','email','phone','contact_default_id','addresses_default_key','addresses','our_csr_id','our_csr','our_rep_id','our_rep','is_customer','is_supplier'));
	    	$arr_tmp = $this->Company->select_one(array('_id' => $arr_salesaccount['company_id']));
	    	//$arr_save = $this->get_company_info($arr_tmp);
	    	$arr_save['company_id'] = $arr_tmp['_id'];
	    	$arr_save['company_name'] = (isset($arr_tmp['name']) ? $arr_tmp['name'] : '');
            $arr_save['phone'] = isset($arr_tmp['phone'])?$arr_tmp['phone']:'';
            $arr_save['email'] = isset($arr_tmp['email'])?$arr_tmp['email']:'';
            $arr_save['invoices_date'] = new MongoDate(strtotime(date('Y-m-d')));
            $arr_save['invoice_address']['0']['invoice_address_1']= isset($arr_tmp['addresses']['0']['address_1'])?$arr_tmp['addresses']['0']['address_1']:'';
            $arr_save['invoice_address']['0']['invoice_town_city']= isset($arr_tmp['addresses']['0']['town_city'])?$arr_tmp['addresses']['0']['town_city']:'';
            $arr_save['invoice_address']['0']['invoice_zip_postcode']= isset($arr_tmp['addresses']['0']['zip_postcode'])?$arr_tmp['addresses']['0']['zip_postcode']:'';
        }else if($arr_salesaccount['contact_id']  && is_object($arr_salesaccount['contact_id']) ){
            $this->selectModel('Contact');
            $arr_tmp = $this->Contact->select_one(array('_id' => $arr_salesaccount['contact_id']));
            $arr_save['contact_name'] = $arr_tmp['first_name'].' '.$arr_tmp['last_name'];
            $arr_save['contact_id'] = $arr_tmp['_id'];
            $arr_save['invoice_address']['0']['invoice_address_1']= isset($arr_tmp['addresses']['0']['address_1'])?$arr_tmp['addresses']['0']['address_1']:'';
            $arr_save['invoice_address']['0']['invoice_town_city']= isset($arr_tmp['addresses']['0']['town_city'])?$arr_tmp['addresses']['0']['town_city']:'';
            $arr_save['invoice_address']['0']['invoice_zip_postcode']= isset($arr_tmp['addresses']['0']['zip_postcode'])?$arr_tmp['addresses']['0']['zip_postcode']:'';
        }
    	$this->selectModel('Salesinvoice');
        $arr_save['code'] = $this->Salesinvoice->get_auto_code('code');
        $arr_save['name'] = $arr_save['code'].' - '.(isset($arr_save['company_name']) ? $arr_save['company_name'] : '');
        $arr_save['salesaccount_id'] = new MongoId($id);
        $arr_save['salesaccount_number'] = $arr_salesaccount['no'];
        $arr_save['salesaccount_name'] = $arr_salesaccount['name'];
        $arr_save['status'] = 'In Progress';
        $arr_save['invoice_date'] = new MongoDate(strtotime(date('Y-m-d')));
        //$arr_save['payment_due_date'] = new MongoDate($arr_save['invoice_date']->sec + $arr_save['payment_terms']*DAY);

        $arr_save['shipping_address'] = '';
        $arr_save['shipping_town_city'] = '';
        $arr_save['shipping_province_state']='';
        $arr_save['shipping_zip_postcode']='';
        $arr_save['shipping_country']='';
        $arr_save['payment_terms'] = isset($arr_sa['payment_terms'])?$arr_sa['payment_terms']:'';
        $arr_save['tax'] = isset($arr_sa['tax_code'])?$arr_sa['tax_code']:'';
        $arr_save['paid_date']='';
        $arr_save['payment_due_date']='';
        $arr_save['job_name']='';
        //$arr_save['name']='';
        $arr_save['salesorder_name']='';
        $arr_save['customer_po_no']='';
        if( isset($arr_tmp['our_rep_id']) && is_object($arr_tmp['our_rep_id']) ){
            $arr_save['our_rep'] = $arr_tmp['our_rep'];
            $arr_save['our_rep_id'] = $arr_tmp['our_rep_id'];
        }
        if( isset($arr_tmp['our_csr_id']) && is_object($arr_tmp['our_csr_id']) ){
            $arr_save['our_csr'] = $arr_tmp['our_csr'];
            $arr_save['our_csr_id'] = $arr_tmp['our_csr_id'];
        }

        $this->Salesinvoice->arr_default_before_save = $arr_save;
        if ($this->Salesinvoice->add()) {
            echo M_URL .'/salesinvoices/entry/'. $this->Salesinvoice->mongo_id_after_save;
        }else
            echo M_URL . '/salesinvoices/entry';
        die;
    }

    function invoices_delete($id) {
        $this->selectModel('Salesinvoice');
    	$this->Salesinvoice->save(array('_id' => new MongoId($id),'deleted' => true));
    	echo 'ok';
    	die;
    }

    public function other(){
    	die;
    }

    public function receipts(){
    	$salesaccount_id = $this->get_id();
    	$this->selectModel('Salesaccount');
        $arr_acc = $this->Salesaccount->select_one(array('_id'=>new MongoId($salesaccount_id)),array('company_id'));
        $this->selectModel('Receipt');
        $receipt = $this->Receipt->select_all(array(
                                              'arr_where' => array('company_id'=>$arr_acc['company_id']),
                                              'arr_order' => array('_id'=>-1),
                                              'arr_field' => array('_id','code','allocation','amount_received','deleted','name','paid_by','notes','receipt_date','unallocated','total_allocated'),
                                              ));
        $sales_account = array();
        if($receipt->count() > 0){
            foreach($receipt as $key => $value){
               $sales_account['receipt'][$key]['id'] = $value['_id'];
                $sales_account['receipt'][$key]['code'] = $value['code'];
                $sales_account['receipt'][$key]['date'] = (isset($value['receipt_date'])?date('d M, Y', $value['receipt_date']->sec):'');
                $sales_account['receipt'][$key]['amount_received'] = (isset($value['amount_received'])&&$value['amount_received']!='' ? (float)$value['amount_received'] : 0);
                $sales_account['receipt'][$key]['unallocated'] = (isset($value['unallocated'])&&$value['unallocated']!='' ? (float)$value['unallocated'] : 0);
                $sales_account['receipt'][$key]['total_allocated'] = (isset($value['total_allocated'])&&$value['total_allocated']!='' ? (float)$value['total_allocated'] : 0);
                $sales_account['receipt'][$key]['paid_by'] = (isset($value['paid_by']) ? $value['paid_by'] : '');
                $sales_account['receipt'][$key]['reference'] = (isset($value['name'])&&$value['name']!='' ? $value['name'] : '');
                $sales_account['receipt'][$key]['notes'] = (isset($value['notes'])&&$value['notes']!='' ? $value['notes'] : '');
                if(isset($value['allocation']) && !empty($value['allocation'])){
                    foreach($value['allocation'] as $k => $v){
                        if($v['deleted'] == true)continue;
                            $sales_account['receipt_allocation'][$key][$k]['receipt_id'] = $value['_id'];
                            $sales_account['receipt_allocation'][$key][$k]['receipt_code'] = $value['code'];
                            $sales_account['receipt_allocation'][$key][$k]['salesinvoice_id'] = (isset($v['salesinvoice_id'])&&is_object($v['salesinvoice_id']) ? $v['salesinvoice_id'] : '');
                            $sales_account['receipt_allocation'][$key][$k]['salesinvoice_code'] = (isset($v['salesinvoice_code']) ? $v['salesinvoice_code'] : '');
                            $sales_account['receipt_allocation'][$key][$k]['date'] = $sales_account['receipt'][$key]['date'];
                            $sales_account['receipt_allocation'][$key][$k]['note'] = (isset($v['note']) ? $v['note'] : '');
                            $sales_account['receipt_allocation'][$key][$k]['write_off'] = (isset($v['write_off']) ? $v['write_off'] : 0);
                            $sales_account['receipt_allocation'][$key][$k]['amount'] = (isset($v['amount'])&&$v['amount']!='' ? (float)$v['amount'] : 0);
                    }
                }
            }
        }
        $arr_receipt = array();
        if(isset($sales_account['receipt'])){
            foreach($sales_account['receipt'] as $key => $value){
                $arr_receipt[$key] = $value;
            }
        }
        $total_receipt = 0;
        $total_allocated = 0;
        $unallocated = 0;
        $this->set('arr_receipt',$arr_receipt);
        foreach ($arr_receipt as $key => $receipt) {
        	$total_receipt =  $total_receipt + $receipt['amount_received'];
        	$total_allocated = $total_allocated + $receipt['total_allocated'];
        	$unallocated = $unallocated + $receipt['unallocated'];

        }
        $this->set('total_receipt',$total_receipt);
        $this->set('total_allocated',$total_allocated);
        $this->set('unallocated',$unallocated);


        //echo $total_receipt; echo '-'.$total_allocated; echo '-'.$unallocated;die;
        $arr_allocation = array();
        if(isset($sales_account['receipt_allocation'])){
            foreach($sales_account['receipt_allocation'] as $k_receipt_allocation => $v_receipt_allocation){
                foreach($v_receipt_allocation as $k => $v){
                    $arr_allocation[$k] = $v;
                }
            }
        }
        $this->set('arr_allocation',$arr_allocation);
        $this->set('salesaccount_id',$salesaccount_id);
    	//die('receipt');
    }
}