<?php
class PurchaseordersController extends MobileAppController {
	var $modelName = 'Purchaseorder';
	var $name = 'Purchaseorders';
	function beforeFilter() {
		parent::beforeFilter();
		if(!$this->request->is('ajax'))
			$this->set('arr_tabs', array(
			           			'line entry' => array('line_entry'),
			           			'text entry' => array('text_entry'),
			           			'shipping/received' => array('shipping'),
			           			'supplier Invoice' => array('invoice'),
			           			'tasks' => array('tasks'),
			           			'document' => array('docs'),
			           			'other'  => array('communications'),
			           			));
	}

	public function entry($id = '0', $num_position = -1) {
		$arr_tmp = $this->entry_init($id, $num_position, 'Purchaseorder', 'purchaseorders');
		$arr_tmp['purchord_date'] = (is_object($arr_tmp['purchord_date'])) ? date('m/d/Y', $arr_tmp['purchord_date']->sec) : '';
		$arr_tmp['required_date'] = (is_object($arr_tmp['required_date'])) ? date('m/d/Y', $arr_tmp['required_date']->sec) : '';
		//pr($arr_tmp);die;

		$this->selectModel('Setting');
		$arr_purchaseorders_type = $this->Setting->select_option(array('setting_value' => 'purchaseorders_type'), array('option'));
		$this->set('arr_purchaseorders_type', $arr_purchaseorders_type);

		$arr_purchaseorders_payment_terms = $this->Setting->select_option(array('setting_value' => 'salesinvoices_payment_terms'), array('option'));
		$this->set('arr_purchaseorders_payment_terms', $arr_purchaseorders_payment_terms);

		$arr_purchaseorders_status = $this->Setting->select_option(array('setting_value' => 'purchase_orders_status'), array('option'));
		$this->set('arr_purchaseorders_status', $arr_purchaseorders_status);

		$this->selectModel('Tax');
		$arr_purchaseorders_tax = $this->Tax->tax_select_list();
		$this->set('arr_purchaseorders_tax', $arr_purchaseorders_tax);	
		

		$this->set('arr_priority', $this->Setting->select_option(array('setting_value' => 'lists_priority'), array('option')));
		$this->set('arr_country', $this->Setting->select_option(array('setting_value' => 'lists_country'), array('option')));


		$arr_tmp1['Purchaseorder'] = $arr_tmp;
		$this->data = $arr_tmp1;
		$arr_purchaseorder_id = array();
		if (isset($arr_tmp['our_rep_id']))
			$arr_purchaseorder_id[] = $arr_tmp['our_rep_id'];

		$this->selectModel('Country');
		$options = array();
		$options['countries'] = $this->Country->get_countries();
		$this->selectModel('Province');
		$options['provinces'] = $this->Province->get_all_provinces();
		$this->set('options',$options);
	}

	function auto_save() {
		if (!empty($this->data)) {
			$id = $this->get_id();
			$arr_return = array();
			$arr_post_data = $this->data['Purchaseorder'];
			$arr_save = $arr_post_data;
			$arr_save['_id'] = new MongoId($id);
			$this->selectModel('Purchaseorder');
		
			if(isset($arr_save['code'])){
				if(!is_numeric($arr_save['code'])){
					echo json_encode(array('status'=>'error','message'=>'must_be_numberic'));
					die;
				}
				$arr_tmp = $this->Purchaseorder->select_one(array('code' => (int) $arr_save['code'], '_id' => array('$ne' => new MongoId($id))));
				if (isset($arr_tmp['code'])) {
					echo json_encode(array('status'=>'error','message'=>'ref_no_existed'));
					die;
				}
			}

			$arr_tmp = $this->Purchaseorder->select_one(array('_id' =>  new MongoId($id)),array('purchaseorder_status','company_id','customer_po_no'));

			$purchord_date = $this->Common->strtotime($arr_save['purchord_date'] . ' 00:00:00');
			$arr_save['purchord_date'] = new MongoDate($purchord_date);

			$required_date = $this->Common->strtotime($arr_save['required_date'] . ' 00:00:00');
			$arr_save['required_date'] = new MongoDate($required_date);

			if (strlen(trim($arr_save['contact_id'])) == 24)
				$arr_save['contact_id'] = new MongoId($arr_save['contact_id']);
			else
				$arr_save['contact_id'] = '';

			if(strlen($arr_save['company_id'])==24){
				$arr_save['company_id'] = new MongoId($arr_save['company_id']);
				if(isset($arr_tmp['company_id'])
				   		&& (string)$arr_tmp['company_id']!= (string)$arr_save['company_id']){
					$this->selectModel('Company');
					$company = $this->Company->select_one(array('_id'=>$arr_save['company_id']),array('contact_default_id'));
					if(isset($company['contact_default_id'])&&is_object($company['contact_default_id'])){
						$this->selectModel('Contact');
						$contact = $this->Contact->select_one(array('_id'=>new MongoId($company['contact_default_id'])),array('full_name'));
						$arr_save['contact_name'] = '';
						$arr_save['contact_id'] = $company['contact_default_id'];
						if(isset($contact['full_name']))
							$arr_save['contact_name'] = $contact['full_name'];
						$arr_return['contact_id'] = (string)$arr_save['contact_id'];
						$arr_return['contact_name'] = $arr_save['contact_name'];
					}
				}
			}
			else
				$arr_save['company_id'] = '';

			if(strlen($arr_save['our_rep_id'])==24)
				$arr_save['our_rep_id'] = new MongoId($arr_save['our_rep_id']);
			else
				$arr_save['our_rep_id'] = '';
			
			if( strlen($arr_save['name']) > 0 )
				$arr_save['name']{0} = strtoupper($arr_save['name']{0});

			$this->selectModel('Purchaseorder');
			if ($this->Purchaseorder->save($arr_save))
				$arr_return['status'] = 'ok';
			else
				$arr_return = array('status'=>'error','message'=>'Error: ' . $this->Purchaseorder->arr_errors_save[1]);
			echo json_encode($arr_return);
		}
		die;
	}

	function lists( $content_only = '' ) {
		$this->selectModel('Purchaseorder');
		$limit = 20; $skip = 0;
		// dùng cho sort
		$sort_field = 'code';
		$sort_type = -1;
		if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
			if( $_POST['sort']['type'] == 'desc' ){
				$sort_type = -1;
			}
			$sort_field = $_POST['sort']['field'];
			$this->Session->write('purchaseorders_lists_search_sort', array($sort_field, $sort_type));

		}elseif( $this->Session->check('purchaseorders_lists_search_sort') ){
			$session_sort = $this->Session->read('purchaseorders_lists_search_sort');
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
		if( $this->Session->check('purchaseorders_entry_search_cond') ){
			$cond = $this->Session->read('purchaseorders_entry_search_cond');
		}

		// dùng cho phân trang
		if(isset($_POST['offset'])){
			$skip = $_POST['offset'];
		}
		// query
		$arr_purchaseorders = $this->Purchaseorder->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'limit' => $limit,
			'skip' => $skip,
			'arr_field' => array('code','company_name', 'name', 'company_id', 'contact_name','purchord_date','our_rep','purchase_orders_status','salesorder_name','sum_amount'),
		));
		$this->set('arr_purchaseorders', $arr_purchaseorders);
		$this->selectModel('Setting');
		$this->set('arr_purchaseorders_type', $this->Setting->select_option(array('setting_value' => 'purchaseorders_type'), array('option')));

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
			$this->selectModel('Purchaseorder');
			if ($this->Purchaseorder->save($arr_save)) {
				$this->Session->delete($this->name.'ViewId');
				if($this->request->is('ajax'))
					echo 'ok';
				else
					$this->redirect('/mobile/purchaseorders/entry');
			} else {
				echo 'Error: ' . $this->Purchaseorder->arr_errors_save[1];
			}
		}
		die;
	}

	public function add() {
		$this->selectModel('Purchaseorder');
		$arr_save = array();

		$this->Purchaseorder->arr_default_before_save = $arr_save;
		if ($this->Purchaseorder->add())
			$this->redirect('/mobile/purchaseorders/entry/' . $this->Purchaseorder->mongo_id_after_save);
		die;
	}

    public function popup($key = '') {
        $this->set('key', $key);
        $limit = 100;
        $skip = 0;
        $cond = array();
        // Nếu là search GET
        if (!empty($_GET)) {

            $tmp = $this->data;

            if (isset($_GET['company_id'])) {
                $cond['company_id'] = new MongoId($_GET['company_id']);
                $tmp['Purchaseorder']['company'] = $_GET['company_name'];
            }

            $this->data = $tmp;
        }

        // Nếu là search theo phân trang
        $page_num = 1;
        $limit = 10; $skip = 0;
        if (isset($_POST['pagination']) && $_POST['pagination']['page-num'] > 0) {
            // $limit = $_POST['pagination']['page-list'];
            $page_num = $_POST['pagination']['page-num'];
            //$limit = $_POST['pagination']['page-list'];
            $skip = $limit * ($page_num - 1);
        }
        $this->set('page_num', $page_num);
        $this->set('limit', $limit);
        $arr_order = array('first_name' => 1);
        if (isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0) {
            $sort_type = 1;
            if ($_POST['sort']['type'] == 'desc') {
                $sort_type = -1;
            }
            $arr_order = array($_POST['sort']['field'] => $sort_type);

            $this->set('sort_field', $_POST['sort']['field']);
            $this->set('sort_type', ($sort_type === 1) ? 'asc' : 'desc');
            $this->set('sort_type_change', ($sort_type === 1) ? 'desc' : 'asc');
        }

        // search theo submit $_POST kèm điều kiện
        if (!empty($this->data) && !empty($_POST) && isset($this->data['Purchaseorder'])) {
            $arr_post = $this->Common->strip_search($this->data['Purchaseorder']);
            if (isset($arr_post['contact_name']) && strlen($arr_post['contact_name']) > 0) {
                $cond['contact_name'] = new MongoRegex('/' . trim($arr_post['contact_name']) . '/i');
            }

            if (isset($arr_post['company']) && strlen($arr_post['company']) > 0) {
                $cond['company_name'] = new MongoRegex('/' . trim($arr_post['company']) . '/i');
            }
        }
      
        $this->selectModel('Purchaseorder');

        $arr_purchaseorder = $this->Purchaseorder->select_all(array(
            'arr_where' => $cond,
            'arr_order' => $arr_order,
            'arr_field' => array('company_id', 'company_name', 'contact_name','contact_id','purchord_date','name','code'),
            'limit' => $limit,
            'skip' => $skip
        ));
         
        $this->set('arr_purchaseorder', $arr_purchaseorder);

        $total_page = $total_record = $total_current = 0;
        if (is_object($arr_purchaseorder)) {
            $total_current = $arr_purchaseorder->count(true);
            $total_record = $arr_purchaseorder->count();
            if ($total_record % $limit != 0) {
                $total_page = floor($total_record / $limit) + 1;
            } else {
                $total_page = $total_record / $limit;
            }
        } else if(is_array($arr_purchaseorder)){
            $total_current = count($arr_purchaseorder);
            $total_record = $this->Purchaseorder->count($cond);
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
    public function tasks(){
    	if(isset($_POST['add']))
    		return $this->tasks_add();
    	$offset = 0;
    	if(isset($_POST['offset']))
    		$offset = $_POST['offset'];
    	$id = $this->get_id();
    	$this->selectModel('Task');
    	$arr_tasks = $this->Task->select_all(array(
    	                        'arr_where' => array('purchaseorder_id' => new MongoId($id),),
    	                        'arr_field' => array('no','name','type','our_rep','work_start','work_end','status'),
    	                        'arr_order' => array('work_end' => -1),
    	                        'skip' => $offset,
    	                        'limit' => 10
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
			$this->set('id',$id);
        }
    }

    function tasks_add() {
    	$id = $this->get_id();
        $this->selectModel('Purchaseorder');
        $arr_purchaseorder = $this->Purchaseorder->select_one(array('_id' => new MongoId($id)),array('our_rep_id', 'name', 'contact_default_id'));
        $arr_save = array();
        $arr_save['purchaseorder_name'] = $arr_purchaseorder['name'];
        $arr_save['purchaseorder_id'] = $arr_purchaseorder['_id'];
        $arr_save['our_rep_id'] = $arr_save['our_rep'] = '';
        if (isset($arr_purchaseorder['our_rep_id']) && is_object($arr_purchaseorder['our_rep_id']) ){
            $arr_save['our_rep_id'] = $arr_purchaseorder['our_rep_id'];
            $arr_save['our_rep'] = isset($arr_purchaseorder['our_rep']) ? $arr_purchaseorder['our_rep'] : '';
        }
        $arr_save['contact_id'] = $arr_save['contact_name'] = '';
        if (isset($arr_purchaseorder['contact_default_id']) && is_object($arr_purchaseorder['contact_default_id'])) {
            $this->selectModel('Contact');
            $arr_contact = $this->Contact->select_one(array('_id' => $arr_purchaseorder['contact_default_id']),array('first_name','first_name'));
            $arr_save['contact_id'] = $arr_contact['_id'];
            $arr_save['contact_name'] = $arr_contact['first_name'] . ' ' . $arr_contact['first_name'];
        }
        $this->selectModel('Task');
        $this->Task->arr_default_before_save = $arr_save;
        if ($this->Task->add())
            echo M_URL.'/tasks/entry/' . $this->Task->mongo_id_after_save;
        else
            echo M_URL .'/tasks/entry';
        die;
    }

    function tasks_delete($task_id){
    	$this->selectModel('Task');
    	$this->Task->save(array('_id' => new MongoId($task_id),'deleted' => true));
    	echo 'ok';
    	die;
    }

    public function line_entry_data($opname = 'products', $is_text = 0,$mod = '') {
        $arr_ret = array(); $option_for = '';
        $this->selectModel('Setting');
        $this->selectModel('Purchaseorder');
        if ($this->get_id() != '') {
            $newdata = $option_select_dynamic = array();
            $query = $this->Purchaseorder->select_one(array('_id' => new MongoId($this->get_id())),array('options','products','sum_sub_total','sum_amount','sum_tax','rfqs','purchord_date'));
            if(!isset($query['options']))
                $query['options'] = array();
            //set sum
            $arr_ret['sum_sub_total'] = $arr_ret['sum_amount'] = $arr_ret['sum_tax'] = '0.00';
            if (isset($query['sum_sub_total']) && $query['sum_sub_total'] != '')
                $arr_ret['sum_sub_total'] = $query['sum_sub_total'];
            if (isset($query['sum_amount']) && $query['sum_amount'] != '')
                $arr_ret['sum_amount'] = $query['sum_amount'];
            if (isset($query['sum_tax']) && $query['sum_tax'] != '')
                $arr_ret['sum_tax'] = $query['sum_tax'];
            $this->selectModel('Product');
            $arr_product_approved = Cache::read('arr_product_approved');
            if(!$arr_product_approved){
                $arr_product_approved = $this->Product->select_all(array(
                                           'arr_where'=>array('approved'=>1),
                                           'arr_field'=>array('_id')
                                           ));
                $arr_product_approved = iterator_to_array($arr_product_approved);
                Cache::write('arr_product_approved',$arr_product_approved);
            }
            $option_for_sort = array();
            if (isset($query[$opname]) && is_array($query[$opname])) {
                $options = array();
                if(isset($query['options']) && !empty($query['options']) )
                    $options = $query['options'];
                foreach ($query[$opname] as $key => $arr) {
                    if (!$arr['deleted']) {
                        $newdata[$key] = $arr;
						$newdata[$key]['is_printer'] = 0;
						//set default Unit price
						if(!isset($arr['custom_unit_price']) && isset($arr['unit_price']))
							$newdata[$key]['custom_unit_price'] = $arr['unit_price'];

                        if(!isset($arr['option_for'])){
                            $option = $this->new_option_data(array('key'=>$key,'products_id'=>$arr['products_id'],'options'=>$query['options'],'products'=>$query['products'],'date'=>$query['purchord_date']),$query['products']);
                            //Khoa sell_by,oum neu nhu line nay co option
                            //Khoa tiep sell_price neu line nay co option same_parent
                            if(isset($option['option'])&&!empty($option['option'])){
                                foreach($option['option'] as $value){
                                    if($value['deleted']) continue;
                                    if(isset($value['choice'])&&$value['choice']==0&&isset($value['require']) && $value['require']!=1) continue;
                                    if($value['oum']!=$arr['oum'])
                                        $newdata[$key]['oum'] = 'Mixed';
                                    $newdata[$key]['xlock']['sell_by'] = '1';
                                    $newdata[$key]['xlock']['oum'] = '1';
                                    break;
                                    // if(!isset($value['same_parent']) || $value['same_parent']==0) continue;
                                    // $newdata[$key]['xlock']['sell_price'] = '1';
                                }
                            }
                            if(isset($arr['products_id'])&&!isset($arr_product_approved[(string)$arr['products_id']]))
                                $newdata[$key]['xcss_element']['sku']= 'approved_product';
                        } else {
                            $newdata[$key]['xlock']['sell_by'] = '1';
                            $newdata[$key]['xlock']['oum'] = '1';
                        }
						$newdata[$key]['option'] = 1;
                        $newdata[$key]['option_group'] = '';
						if (isset($newdata[$key]['products_id']) && $newdata[$key]['products_id']!='')
							$newdata[$key]['xlock']['unit_price']   = '1';
						else
							$newdata[$key]['xlock']['unit_price']   = '0';
                        if (isset($newdata[$key]['products_name'])) {
                            if($is_text != 1){
                                $arrtmp = explode("\n", $newdata[$key]['products_name']);
                                $newdata[$key]['products_name'] = $arrtmp[0];
                                if(isset($arr['same_parent']) && $arr['same_parent']==1)
                                    $get_name_only = true;
                            }
                            if(!empty($option)){
                                foreach($options as $k=>$val){
                                    if(isset($val['deleted']) && $val['deleted']) continue;
                                    if(!isset($val['line_no']) || $val['line_no']!=$key) continue;
                                    $newdata[$key]['option_group'] = (isset($val['option_group']) ? $val['option_group'] : '');
                                    if(isset($get_name_only)){
                                        if(!isset($val['quantity'])) continue;
                                        if($val['quantity']==1) continue;
                                        $newdata[$key]['products_name'] .= ' ('.$val['quantity'].')';
                                        unset($options[$k]);
                                    }
                                }
                            }
                        }
                        if(isset($newdata[$key]['products_name']) && $is_text == 1){
                            $newdata[$key]['products_costing_name'] = '';
                            if(isset($arr['details']))
                                $newdata[$key]['products_costing_name'] .= htmlentities('<p style="margin-left:15px;font-style:italic;">'.nl2br($arr['details']).'</p>');
                            if(isset($option['option'])&&!empty($option['option'])){
                                foreach($option['option'] as $value){
                                    if(isset($value['deleted']) && $value['deleted']) continue;
                                    if(!isset($value['product_name']) || $value['product_name']=='') continue;
                                    if(!isset($value['view_in_detail'])|| $value['view_in_detail']==0)continue;
                                    $newdata[$key]['products_costing_name'] .= htmlentities($value['product_name']).' ('.(isset($value['quantity']) ? $value['quantity'] : 0).')<br />';
                                }
                            }
                            $newdata[$key]['xlock']['products_name']= '1';
                            $newdata[$key]['xlock']['sell_by']  = '1';
                            $newdata[$key]['xlock']['sell_price']   = '1';
                            $newdata[$key]['xlock']['oum']      = '1';
                            $newdata[$key]['xlock']['quantity']     = '1';
                            $newdata[$key]['xlock']['sizew']        = '1';
                            $newdata[$key]['xlock']['sizew_unit']   = '1';
                            $newdata[$key]['xlock']['sizeh']        = '1';
                            $newdata[$key]['xlock']['sizeh_unit']   = '1';
                            $newdata[$key]['xlock']['unit_price']   = '1';
                            $newdata[$key]['xlock']['adj_qty']  = '1';
                            $newdata[$key]['xlock']['sub_total']    = '1';
                            $newdata[$key]['xlock']['tax']      = '1';
                            $newdata[$key]['xlock']['amount']       = '1';
                            $newdata[$key]['xlock']['option']       = '1';
                            $newdata[$key]['xlock']['receipts']     = '1';
                        }
                        //set all price in display
                        if (isset($arr['area']))
                            $newdata[$key]['area'] = (float) $arr['area'];
                        $newdata[$key]['custom_unit_price'] = number_format((isset($arr['custom_unit_price']) ? (float)$arr['custom_unit_price'] : 0), 3);
                        if (isset($arr['unit_price'])){
                            $newdata[$key]['unit_price'] = number_format((float) $arr['unit_price'], 3);
                            if(!isset($arr['custom_unit_price']))
                                $newdata[$key]['custom_unit_price'] = $newdata[$key]['unit_price'];
                        }
                        else
                            $newdata[$key]['unit_price'] = '0.000';
                        if (isset($arr['sub_total']))
                            $newdata[$key]['sub_total'] = number_format((float) $arr['sub_total']);
                        else
                            $newdata[$key]['sub_total'] = '0.00';
                        if (isset($arr['tax']))
                            $newdata[$key]['tax'] = number_format((float) $arr['tax'], 3);
                        else
                            $newdata[$key]['tax'] = '0.000';
                        if (isset($arr['amount']))
                            $newdata[$key]['amount'] = number_format((float) $arr['amount']);
                        else
                            $newdata[$key]['amount'] = '0.00';
						unset($newdata[$key]['id']);
						$newdata[$key]['_id'] = $key;
						$newdata[$key]['sort_key'] = $this->Purchaseorder->num_to_string($key).'-'.'0';

						$option_for = '';
						if(isset($arr['option_for']) && $arr['option_for']!=''){
                            $newdata[$key]['xempty']['option']      = '1';
                            $newdata[$key]['xempty']['view_costing']      = '1';
                            if(isset($arr['same_parent'])&&$arr['same_parent']==1)
                                $newdata[$key]['xempty']['custom_unit_price']   = '1';
                            $newdata[$key]['_id'] = $key;
							$newdata[$key]['sku_disable'] = '1';
							$newdata[$key]['sku'] = '';
							$newdata[$key]['remove_deleted'] = '1';
							$newdata[$key]['icon']['products_name'] = (is_object($arr['products_id']) ? URL.'/products/entry/'.$arr['products_id'] : '#');
							$newdata[$key]['sort_key'] = $this->Purchaseorder->num_to_string($arr['option_for']).'-'.$key;
							if($mod!='options_list')
							     unset($newdata[$key]['products_id']);
						}


                        //data RFQ's
                        $receipts = 0;
                        if (isset($query['rfqs']) && is_array($query['rfqs']) && count($query['rfqs']) > 0) {
                            foreach ($query['rfqs'] as $rk => $rv) {
                                if (!$rv['deleted'] && isset($rv['rfq_code']) && (int) $rv['rfq_code'] == $key) {
                                    $receipts = 1;
                                }
                            }
                            $newdata[$key]['receipts'] = $receipts;
                        } else
                            $newdata[$key]['receipts'] = 0;

                        //chặn không cho custom size nếu is_custom_size = 1
                        if(is_object($arr['products_id'])){
                            $product_custom_size = Cache::read('product_custom_size');
                            if($product_custom_size && isset($product_custom_size[(string)$arr['products_id']]))
                                $is_custom_size = $product_custom_size[(string)$arr['products_id']];
                            else{
                                $product = $this->Product->select_one(array('_id'=>new MongoId($arr['products_id'])),array('is_custom_size'));
                                $is_custom_size = isset($product['is_custom_size'])&&$product['is_custom_size'] == 1 ? 1 : 0;
                                if(!is_array($product_custom_size))
                                    $product_custom_size = array();
                                $product_custom_size[(string)$arr['products_id']] = $is_custom_size;
                                Cache::write('product_custom_size',$product_custom_size);
                            }
                            if($is_custom_size==1){
                                $newdata[$key]['xlock']['sizeh'] = '1';
                                $newdata[$key]['xlock']['sizew'] = '1';
                                $newdata[$key]['xlock']['sizeh_unit'] = '1';
                                $newdata[$key]['xlock']['sizew_unit'] = '1';
                                $newdata[$key]['xlock']['sell_by'] = '1';
                            }

							//is_printer
							$product_is_printer = Cache::read('product_is_printer');
							if($product_is_printer && isset($product_is_printer[(string)$arr['products_id']]))
                                $is_printer = $product_is_printer[(string)$arr['products_id']];
                            else{
                                $product = $this->Product->select_one(array('_id'=>new MongoId($arr['products_id'])),array('is_printer'));
                                $is_printer = isset($product['is_printer'])&&$product['is_printer'] == 1 ? 1 : 0;
                                if(!is_array($product_is_printer))
                                    $product_is_printer = array();
                                $product_is_printer[(string)$arr['products_id']] = $is_printer;
                                Cache::write('product_is_printer',$product_is_printer);
                            }
							$newdata[$key]['is_printer'] = $is_printer;
							//end is_printer
                        }


						//empty neu same_parent = 1
						if(isset($arr['same_parent']) && $arr['same_parent']==1){
							$newdata[$key]['xlock']['products_name']= '1';
							$newdata[$key]['xempty']['sell_by'] 	= '1';
							$newdata[$key]['xempty']['sell_price'] 	= '1';
							$newdata[$key]['xempty']['oum'] 		= '1';
							$newdata[$key]['xempty']['quantity'] 	= '1';
							$newdata[$key]['xempty']['sizew'] 		= '1';
							$newdata[$key]['xempty']['sizew_unit'] 	= '1';
							$newdata[$key]['xempty']['sizeh'] 		= '1';
							$newdata[$key]['xempty']['sizeh_unit'] 	= '1';
							$newdata[$key]['xempty']['unit_price'] 	= '1';
							$newdata[$key]['xempty']['adj_qty'] 	= '1';
							$newdata[$key]['xempty']['sub_total'] 	= '1';
							$newdata[$key]['xempty']['tax'] 		= '1';
							$newdata[$key]['xempty']['amount'] 		= '1';
							$newdata[$key]['xempty']['option'] 		= '1';
							$newdata[$key]['xempty']['receipts'] 	= '1';
						}


						//empty neu sell_by parent = combination
						if($option_for!='' && isset($query[$opname][$option_for]['sell_by']) && $query[$opname][$option_for]['sell_by']=='combination'){
							//$newdata[$key]['xempty']['sub_total'] = '1';
							$newdata[$key]['xempty']['tax'] 		= '1';
							$newdata[$key]['xempty']['amount'] 		= '1';
						}

						//khoa Sold by neu la combination
						if (isset($newdata[$key]['sell_by']) && $newdata[$key]['sell_by']=='combination') {
							$newdata[$key]['xlock']['sell_by']= '1';
							$newdata[$key]['xlock']['sell_price']= '1';
							$newdata[$key]['xlock']['oum']= '1';
						}



                        //set lại select dựa vào loại sell_by
                        if (isset($newdata[$key]['sell_by'])) {
                            $option_select_dynamic['oum_' . $key] = $this->Setting->select_option_vl(array('setting_value' => 'product_oum_' . strtolower($arr['sell_by'])));
                        }
                        if(isset($arr['option_for']))
                            $option_for_sort['option'][$arr['option_for']][$key] = $newdata[$key];
                        else
                            $option_for_sort['parent'][$key] = $newdata[$key];

                    } //end if

                }
            }
            $arr_ret[$opname] =array();
            $this->selectModel('Product');
            if(isset($option_for_sort['parent'])){
                foreach($option_for_sort['parent'] as $p_key=>$parent){
                    $arr_ret[$opname][] = $parent;
                    if(!isset($option_for_sort['option'][$p_key])) continue;
                    if(is_object($parent['products_id'])){
                        $p_product = $this->Product->select_one(array('_id'=> new MongoId($parent['products_id'])),array('options'));
                        if(isset($p_product['options'])&&!empty($p_product['options'])){
                            foreach($option_for_sort['option'][$p_key] as $k_opt=>$opt){
                                if(!isset($opt['proids'])) continue;
                                $opt_key = str_replace((string)$parent['products_id'].'_', '', $opt['proids']);
                                $option_for_sort['option'][$p_key][$k_opt]['option_group'] = (isset($p_product['options'][$opt_key]['option_group']) ? $p_product['options'][$opt_key]['option_group'] : '');
                                $option_for_sort['option'][$p_key][$k_opt]['option_group_for_sort'] = $option_for_sort['option'][$p_key][$k_opt]['option_group'].'_'.$option_for_sort['option'][$p_key][$k_opt]['products_name'];
                            }
                        }
                    }
                    if(isset($option_for_sort['option'])){
                        if(isset($query['options'])&&!empty($query['options'])){
                            foreach($option_for_sort['option'][$p_key] as $k_opt=>$opt){
                                $line_no = $opt['_id'];
                                foreach($query['options'] as  $custom_opt_k=>$custom_opt_v){
                                    if(isset($custom_opt_v['deleted'])&&$custom_opt_v['deleted']){
                                        unset($query['options'][$custom_opt_k]);
                                        continue;
                                    }
                                    if(!isset($custom_opt_v['line_no']) || $custom_opt_v['line_no']!=$line_no) continue;
                                    if(!isset($custom_opt_v['option_group']))
                                        $custom_opt_v['option_group'] = '';
                                    $option_for_sort['option'][$p_key][$k_opt]['option_group'] = $custom_opt_v['option_group'];
                                    $option_for_sort['option'][$p_key][$k_opt]['option_group_for_sort'] = $option_for_sort['option'][$p_key][$k_opt]['option_group'].'_'.$option_for_sort['option'][$p_key][$k_opt]['products_name'];
                                    unset($query['options'][$custom_opt_k]); break;
                                }
                            }
                        }
                        $option_for_sort['option'][$p_key] = $this->Purchaseorder->aasort($option_for_sort['option'][$p_key],'option_group_for_sort');
                        foreach($option_for_sort['option'][$p_key] as $value)
                            array_push($arr_ret[$opname], $value);
                    }
                }
            }
        }
        $this->set('option_select_dynamic', $option_select_dynamic);
        return $arr_ret;
    }

    var $is_text = 0;
    function line_entry(){
    	if(isset($_POST['add']))
    		return $this->line_entry_add();
    	$is_text = $this->is_text;
    	$opname = 'products';
    	$this->selectModel('Purchaseorder');
            //get entry data
    	$ids = $this->get_id();
        $date_modified = $this->Purchaseorder->select_one(array('_id'=>new MongoId($ids)),array('date_modified','purchase_orders_status'));
        $prefix_cache_name = 'line_purchaseorder_'.$ids.'_';
        $cache_name = $prefix_cache_name.$date_modified['date_modified']->sec;
        $arr_ret = Cache::read($cache_name);
        if(!$arr_ret){
            $arr_ret = $this->line_entry_data($opname, $is_text);
            Cache::write($cache_name,$arr_ret);
            $old_cache = $this->get_cache_keys_diff($cache_name,$prefix_cache_name);
            foreach($old_cache as $cache){
                Cache::delete($cache);
            }
        }
        if(isset($arr_ret[$opname])){
            $minimum = 0; // $this->get_minimum_order();
            if($arr_ret['sum_sub_total']<$minimum){
                $arr_ret = $this->get_minimum_order_adjustment($arr_ret,$minimum);
            }
        }
        if(isset($date_modified['purchase_orders_status']) && $date_modified['purchase_orders_status'] == 'Cancelled' )
            $arr_ret['sum_sub_total'] = $arr_ret['sum_tax'] = $arr_ret['sum_amount'] = 0;
    	$this->set('arr_ret',$arr_ret);

    	$this->selectModel('Setting');
		$options['product_oum_size'] = $this->Setting->select_option(array('setting_value' => 'product_oum_size'), array('option'));
		$options['product_sell_by'] = $this->Setting->select_option(array('setting_value' => 'product_sell_by'), array('option'));
		$options['product_oum_area'] = $this->Setting->select_option(array('setting_value' => 'product_oum_area'), array('option'));
		$this->set('options', $options);
		if(!$this->request->is('ajax')){
			$arr_data['field'] = array(
	                           'sku'=>array(
	                                       'label' => 'SKU',
	                                       ),
	                           'products_name'=>array(
	                           				'type' => 'text',
	                                       'label' => 'Name / details',
	                                       ),
	                           'options'=>array(
	                                       'label' => '',
	                                       'type' => 'button'),
	                           'costings'=>array(
	                                       'label' => '',
	                                       'type' => 'button'),
	                           'sizew'=>array(
	                                       'label' => 'Size-W',
	                                       'type' => 'text'),
	                           'sizew_unit'=>array(
	                                       'label' => '',
	                                       'type' => 'select',
	                                       'options'=>'product_oum_size'
	                                       ),
	                           'sizeh'=>array(
	                                       'label' => 'Size-H',
	                                       'type' => 'text'),
	                           'sizeh_unit'=>array(
	                                       'label' => '',
	                                       'type' => 'select',
	                                       'options'=>'product_oum_size'
	                                       ),
	                           'sell_by'=>array(
	                                       'label' => 'Sold by',
	                                       'type' => 'select',
	                                       'options'=>'product_sell_by'
	                                       ),
	                           'oum'=>array(
	                                       'label' => 'OUM',
	                                       'type' => 'select',
	                                       'options' => 'product_oum_area',
	                                       ),
	                           'custom_unit_price'=>array(
	                                       'label' => 'Est. Unit price',
	                                       'type' => 'text'),
	                           'unit_price'=>array(
	                                       'label' => 'Unit price',
	                                       'type' => 'text'),
	                           'adj_qty'=>array(
	                                       'label' => 'Adj Qty'),
	                           'sub_total'=>array(
	                                       'label' => 'Sub total'),
	                           'tax'=>array(
	                                       'label' => 'Tax'),
	                           'amount'=>array(
	                                       'label' => 'Amount'),
	                           );
			$this->set('arr_data',$arr_data);
		}
    }
    public function line_entry_add(){
    	$this->selectModel('Purchaseorder');
    	$purch = $this->Purchaseorder->select_one(array('_id' => new MongoId($this->get_id())),array('products'));
    	if(!isset($purch['products']) || !is_array($purch['products']) )
    		$purch['products'] = array();
    	$key = count($purch['products']);
    	$new_line = array(
    				'_id'			=> $key,
    				'deleted'		=> false,
    				'sku'			=>'&nbsp;&nbsp;&nbsp;',
    				'products_id' 	=> '',
    				'products_name' => 'This is new record. Click for edit',
    				'quantity'		=> 1,
    				'options'		=> 'Option',
    				'costings'		=> 'Costing',
					'sizew'		=> 0,
					'sizew_unit' 	=> 'in',
					'sizeh'		=> 0,
					'sizeh_unit' 	=> 'in',
					'sell_by' 		=> 'area',
					'oum' 			=> 'Sq.ft.',
					'custom_unit_price' => 0,
					'unit_price' 	=> 0,
					'adj_qty' 		=> 0,
					'sub_total'		=> 0,
					'tax'			=> 0,
					'amount'		=> 0,
    		);
    	echo json_encode(array($new_line));
    	$new_line['sku'] = '';
    	unset($new_line['_id'],$new_line['options'],$new_line['costings']);
    	$purch['products'][$key] = $new_line;
    	$this->Purchaseorder->save($purch);
    	die;
    }

    public function before_save($field,$value,$id, $options = array()){
    	if($field == 'products'){
    		if( isset($options['key']) && !empty($value['products'][$options['key']]) ){
    			if(isset($value['products'][$options['key']]['deleted']) && $value['products'][$options['key']]['deleted']){
    				$value['products'][$options['key']] = array('deleted'=>true);
    				foreach($value['products'] as $key=>$val){
						if(isset($val['option_for']) && $val['option_for']==$options['key']){
                            $value['products'][$key] = array('deleted'=>true);
						}
					}
					$value = array_merge($value,$this->new_cal_sum($value['products']));
    			} else if(isset($value['products'][$options['key']]['choose'])){
    				unset($value['products'][$options['key']]['choose']);
    				//Product moi choose chi co 3 gia tri, deleted, products_id, products_name
	                //remove cac option cu cua $value['products'][$options['key']]
	                foreach($value['products'] as $key=>$product){
	                    if(isset($product['option_for']) && $product['option_for']==$options['key'])
	                       $value['products'][$key] = array('deleted' => true);
	                }
	                if(isset($value['options'])){
	                	foreach($value['options'] as $opt_key=>$opt_value){
	                        if(isset($opt_value['parent_line_no']) && $opt_value['parent_line_no']==$options['key'])
	                            $value['options'][$opt_key] = array('deleted' => true);
	                    }
	                }

	                //tim data option cua product
	                $this->selectModel('Product');
	                $parent = $this->Product->select_one(array('_id'=>$value['products'][$options['key']]['products_id']),array('sku','product_type','code','sell_by','oum','sell_price','unit_price','sizeh','sizeh_unit','sizew','sizew_unit','is_custom_size'));
	                if(isset($parent['sku']))
	                    $value['products'][$options['key']]['sku'] = $parent['sku'];
	                else
	                	$value['products'][$options['key']]['sku'] = '';
	                
	                $value['products'][$options['key']]['code'] = $parent['code'];
	                $value['products'][$options['key']]['sell_by'] = $parent['sell_by'];
	                $value['products'][$options['key']]['oum'] = $parent['oum'];
	                $value['products'][$options['key']]['sell_price'] = $parent['sell_price'];
	                $value['products'][$options['key']]['unit_price'] = isset($parent['unit_price'])?$parent['unit_price']:'';
	                $value['products'][$options['key']]['sizeh'] = $parent['sizeh'];
	                $value['products'][$options['key']]['sizew'] = $parent['sizew'];
	                $value['products'][$options['key']]['sizeh_unit'] = $parent['sizeh_unit'];
	                $value['products'][$options['key']]['sizew_unit'] = $parent['sizew_unit'];
	                $value['products'][$options['key']]['is_custom_size'] = isset($parent['is_custom_size'])?$parent['is_custom_size']:'';
	                $value['products'][$options['key']]['tax'] = 5;
	                $value['products'][$options['key']]['taxper'] = "5";
	                $value['products'][$options['key']]['balance_received'] = 0;
	                $value['products'][$options['key']]['quantity'] = 0;
	                $value['products'][$options['key']]['sub_total'] = '';
	                $value['products'][$options['key']]['amount'] = '';
	                $value['products'][$options['key']]['oum_depend'] = '';

	                //lay danh sach option va luu lai
	                $products = $this->Product->options_data((string)$value['products'][$options['key']]['products_id']);
	                if(isset($products['productoptions']) && is_array($products['productoptions']) && count($products['productoptions'])>0){
	                    $total_sub_total = 0;
	                    if(!isset($value['options']))
	                        $value['options']= array();
	                    $options_num = count($value['options']);
	                    $line_num = count($value['products']);
	                    foreach($products['productoptions'] as $pro_key=>$pro_value){
	                        //loop va tao moi items
	                        $new_array = array();
	                        $new_array['code']          = $pro_value['code'];
	                        $new_array['sku']           = $pro_value['sku'];
	                        $new_array['products_name'] = $pro_value['product_name'];
	                        $new_array['product_name'] 	= $pro_value['product_name'];
	                        $new_array['products_id']   = $pro_value['product_id'];
	                        $new_array['product_id']    = $pro_value['product_id'];
	                        $new_array['quantity']      = $pro_value['quantity'];
	                        $new_array['sub_total']     = $pro_value['sub_total'];
	                        $new_array['option_group']  = (isset($pro_value['option_group']) ? $pro_value['option_group'] : '');
	                       if(isset($value['products'][$options['key']]['sizew']))
	                            $new_array['sizew']     = $value['products'][$options['key']]['sizew'];
	                        else
	                            $new_array['sizew']     = $pro_value['sizew'];

	                        if(isset($value['products'][$options['key']]['sizew_unit']))
	                            $new_array['sizew_unit']= $value['products'][$options['key']]['sizew_unit'];
	                        else
	                            $new_array['sizew_unit']= $pro_value['sizew_unit'];

	                        if(isset($value['products'][$options['key']]['sizeh']))
	                            $new_array['sizeh']     = $value['products'][$options['key']]['sizeh'];
	                        else
	                            $new_array['sizeh']     = $pro_value['sizeh'];

	                        if(isset($value['products'][$options['key']]['sizeh_unit']))
	                            $new_array['sizeh_unit']= $value['products'][$options['key']]['sizeh_unit'];
	                        else
	                            $new_array['sizeh_unit']= $pro_value['sizeh_unit'];
	                        $new_array['sell_by']       = $pro_value['sell_by'];
	                        $new_array['oum']       	= $pro_value['oum'];
	                        if(isset($pro_value['same_parent']))
	                            $new_array['same_parent']= (int)$pro_value['same_parent'];
	                        else
	                            $new_array['same_parent']= 0;
	                        $more_discount              = (float)$pro_value['unit_price']*((float)$pro_value['discount']/100);
	                        $new_array['sell_price']    = (float)$pro_value['unit_price'] - $more_discount;
	                        $new_array['taxper']        = (isset($value['products'][$options['key']]['taxper']) ? (float)$value['products'][$options['key']]['taxper'] : 0);
	                        $new_array['tax']           = $value['products'][$options['key']]['tax'];
	                        $new_array['option_for']    = $options['key'];
	                        $new_array['deleted']       = false;
	                        $new_array['proids']        = $value['products'][$options['key']]['products_id'].'_'.$options_num;
	                        $this->cal_price = new cal_price;
	                        //truyen data vao cal_price de tinh gia
	                        $this->cal_price->arr_product_items = $new_array;
	                        //lay thong tin khach hang de tinh chiec khau/giam gia
	                        $result = array();
	                        if(!isset($value['company_id']))
	                            $value['company_id'] = '';
	                        if(isset($new_array['products_id']))
	                            $result = $this->change_sell_price_company($value['company_id'],$new_array['products_id']);
	                        //truyen bang chiec khau va gia giam vao
	                        $this->cal_price->price_break_from_to = $result;
	                        //kiem tra field nao dang thay doi
	                        $this->cal_price->field_change = '';
	                        //chay tinh gia
	                        $arr_ret = $this->cal_price->cal_price_items();
	                        //
	                        if(isset($pro_value['line_no']))
	                            unset($pro_value['line_no']);
	                        $value['options'][$options_num] = $pro_value;
	                        $value['options'][$options_num]['this_line_no'] = $options_num;
	                        $value['options'][$options_num]['parent_line_no'] = $options['key'];
	                        $value['options'][$options_num]['choice'] = 0;
	                        if(isset($pro_value['require']) && (int)$pro_value['require']==1){
	                            $value['products'][$line_num] = array_merge((array)$new_array,(array)$arr_ret);
	                            $value['options'][$options_num]['line_no'] = $line_num;
	                            $value['options'][$options_num]['choice'] = 1;
	                            $line_num++;
	                        }
	                        $options_num++;
	                    }
	                    //=============================================
	                    $this->Purchaseorder->save($value);
	                    $arr_data = array( 'data' => array('id' => $options['key']));
	                    if(isset($parent['product_type']) && $parent['product_type'] == 'Custom Product')
	                    	$arr_data['fieldchange'] = 'custom';
	                    else
	                    	$arr_data['fieldchange'] = 'products_name';
	                    unset($_POST);
	                    $arr_data = $this->cal_price_line($arr_data);
	                    die;
	                }
    			}
    		}
    	}
    	return $value;
    }

    public function option_list($idsub) {
    	if(isset($_POST['submit'])){
            $this->option_cal_price($_POST);
        }
    	$this->selectModel('Purchaseorder');
        if(isset($_POST['submit'])){
            $this->option_cal_price($_POST);
        }
        $option_list_data = array();
        //neu idopt khac rong
        //DATA: salesorder line details
        $query = $this->Purchaseorder->select_one(array('_id'=> new MongoId($this->get_id())),array('products','options','purchord_date'));
        if(!isset($query['options']))
            $query['options'] = array();
        $products_id = $products_name = '';
        if(!empty($query['products'])){
            if(isset($query['products'][$idsub])&&!$query['products'][$idsub]['deleted']){
                $products_id = $query['products'][$idsub]['products_id'];
                $products_name = $query['products'][$idsub]['products_name'];
            }
        }
        //DATA: option list
        $option_list_data = $this->new_option_data(array('key'=>$idsub,'products_id'=>$products_id,'options'=>$query['options'],'date'=>$query['purchord_date']),$query['products']);
        $this->set('options',$option_list_data['option']);
        $this->set('products_name',$products_name);
        $this->set('products_key',$idsub);
    }

	public function create_pur_from_product($product_id = '') {
		if(strlen($product_id)!=24){
			$this->redirect('/mobile/products/entry');
		}

		//lay data dua vao product
		$this->selectModel('Product');
		$arr_product = $this->Product->select_one(array('_id' => new MongoId($product_id)));

		//Tạo data PO để lưu
		$new_arr = array();
		if(isset($arr_product['company_name']) && isset($arr_product['company_id'])){
			$new_arr['company_name'] = $arr_product['company_name'];
			$new_arr['company_id'] = $arr_product['company_id'];
			//lấy thông tin liên quan với company
			$new_arr = $this->arr_associated_data('company_name', $arr_product['company_name'], $arr_product['company_id']);
		}

		// thông tin lien quan den product - line entry
		$arr_fields = array('code','sku','sizew','sizew_unit','sizeh','sizeh_unit','oum','sell_price','sell_by','oum_depend','unit_price');
		$arr_float = array('sizew','sizeh','sell_price','unit_price');

		if(isset($arr_product['_id']))
			$new_arr['products'][0]['products_id'] = $arr_product['_id'];
		if(isset($arr_product['name']))
			$new_arr['products'][0]['products_name'] = $arr_product['name'];

		$new_arr['products'][0]['deleted'] = false;
		$new_arr['products'][0]['quantity'] = 0;

		foreach($arr_fields as $kk){
			if(isset($arr_product[$kk])){
				if(in_array($kk,$arr_float))
					$new_arr['products'][0][$kk] = (float)$arr_product[$kk];
				else if($kk=='code')
					$new_arr['products'][0]['code'] = (int)$arr_product[$kk];
				else
					$new_arr['products'][0][$kk] = $arr_product[$kk];
			}
		}

		$new_arr['products'][0]['taxper'] = 5;

		//tính gia cho line
		$this->set_cal_price();
		$this->cal_price->arr_product_items = $new_arr['products'][0];
		$new_arr['products'][0] = $this->cal_price->cal_price_items();
		$this->selectModel('Purchaseorder');
		$this->Purchaseorder->arr_default_before_save = $new_arr;
		$this->Purchaseorder->add('name','');
		$this->redirect('/mobile/purchaseorders/entry/'.$this->Purchaseorder->mongo_id_after_save);
	}
	public function set_cal_price() {
		$this->cal_price = new cal_price; //Option cal_price
		//set arr_price_break default
		$this->cal_price->arr_price_break = array();
		//set arr_product default
		$this->cal_price->arr_product = array();
		//set arr_product item default
		$this->cal_price->arr_product_items = array();
	}
	public function arr_associated_data($field = '', $value = '', $valueid = '') {
		if(isset($_POST['arr']) && is_string($_POST['arr']) && $_POST['arr']!='')
			$tmp_data = (array)json_decode($_POST['arr']);
        if(isset($tmp_data['keys'])){
            if( ($tmp_data['keys']=='update' || $tmp_data['keys']=='add')
                &&!$this->check_permission($this->name.'_@_entry_@_edit')){
                echo 'You do not have permission on this action.';
                die;
            }
        }
		$arr_return = array();
		$arr_return[$field] = $value;

		/*
		* Xử lý chọn combobox company
		*/
		if ($field == 'company_name' && $valueid != '') {
			$arr_return = array(
				'company_name' => '',
				'company_id' => '',
				'contact_name' => '',
				'contact_id' => '',
				'our_rep_id' => '',
				'our_rep' => '',
				'phone' => '',
				'email' => '',
				'fax' => '',
			);
			//change company
			$arr_return['company_name'] = $value;
			$arr_return['company_id'] = new MongoId($valueid);

			//find contact and more from Company
			$this->selectModel('Company');
			$arr_company = $this->Company->select_one(array('_id' => new MongoId($valueid)));
			$this->selectModel('Contact');
			$arr_contact = $arrtemp = array();

			$this->selectModel('Salesaccount');
			$salesaccount = $this->Salesaccount->select_one(array('company_id' => $arr_return['company_id']));
			$arr_return['payment_terms'] = (isset($salesaccount['payment_terms']) ? $salesaccount['payment_terms'] : 0);
			$arr_return['payment_terms_id'] = (isset($salesaccount['payment_terms_id']) ? $salesaccount['payment_terms_id'] : 0);

			// is set contact_default_id
			if (isset($arr_company['contact_default_id']) && is_object($arr_company['contact_default_id'])) {
				$arr_contact = $this->Contact->select_one(array('_id' => $arr_company['contact_default_id']));
			} else {
				$arr_contact = $this->Contact->select_all(array(
					'arr_where' => array('company_id' => new MongoId($valueid)),
					'arr_order' => array('_id' => -1),
				));
				$arrtemp = iterator_to_array($arr_contact);
				if (count($arrtemp) > 0) {
					$arr_contact = current($arrtemp);
				} else
					$arr_contact = array();
			}

			//change contact
			if (isset($arr_contact['_id'])) {
				$arr_return['contact_name'] = $arr_contact['first_name'] . ' ' . $arr_contact['last_name'];
				$arr_return['contact_id'] = $arr_contact['_id'];
			} else {
				$arr_return['contact_name'] = '';
				$arr_return['contact_id'] = '';
			}


			//change our_rep
			if (isset($arr_company['our_rep']) && isset($arr_company['our_rep_id']) && $arr_company['our_rep_id'] != '') {
				$arr_return['our_rep_id'] = $arr_company['our_rep_id'];
				$arr_return['our_rep'] = $arr_company['our_rep'];
			}else{
				$arr_return['our_rep_id'] = $this->opm->user_id();
				$arr_return['our_rep'] = $this->opm->user_name();
			}


			//change phone
			if (isset($arr_company['phone']))
				$arr_return['phone'] = $arr_company['phone'];
			else {  // neu khong co phone thi lay phone cua contact mac dinh
				if (isset($arr_contact['direct_dial']))
					$arr_return['phone'] = $arr_contact['direct_dial'];
				elseif (!isset($arr_contact['direct_dial']) && isset($arr_contact['mobile']))
					$arr_return['phone'] = $arr_contact['mobile'];
				elseif (!isset($arr_contact['direct_dial']) && !isset($arr_contact['mobile']))
					$arr_return['phone'] = '';  //bat buoc phai co dong nay khong thi no se lay du lieu cua cty truoc
			}

			//change email
			if (isset($arr_company['email']) && $arr_company['email']!='')
				$arr_return['email'] = $arr_company['email'];
			elseif (isset($arr_contact['email']))
				$arr_return['email'] = $arr_contact['email'];
			elseif (!isset($arr_contact['email']))
				$arr_return['email'] = '';

			//change fax
			if (isset($arr_company['fax']))
				$arr_return['fax'] = $arr_company['fax'];
			elseif (isset($arr_contact['fax']))
				$arr_return['fax'] = $arr_contact['fax'];
			elseif (!isset($arr_contact['fax']))
				$arr_return['fax'] = '';

			//change address
			if (isset($arr_company['addresses_default_key']))
				$add_default = $arr_company['addresses_default_key'];
			if (isset($add_default) && isset($arr_company['addresses'][$add_default])) {
				foreach ($arr_company['addresses'][$add_default] as $ka => $va) {
					if ($ka != 'deleted')
						$arr_return['invoice_address'][0]['invoice_' . $ka] = $va;
					else
						$arr_return['invoice_address'][0][$ka] = $va;
				}
			}

		}
		/*
		* Xử lý chọn ship_to_company_name
		*/
		if ($field == 'ship_to_company_name' && $valueid != ''){

			$arr_return = array(
				'ship_to_company_name' => '',
				'ship_to_company_id' => '',
				'ship_to_contact_name' => '',
				'ship_to_contact_id' => '',
			);

			$arr_return['ship_to_company_name'] = $value;
			$arr_return['ship_to_company_id'] = new MongoId($valueid);

			//find contact and more from Company
			$this->selectModel('Company');
			$arr_company = $this->Company->select_one(array('_id' => new MongoId($valueid)));
			$this->selectModel('Contact');
			$arr_contact = $arrtemp = array();

			// is set contact_default_id
			if (isset($arr_company['contact_default_id']) && is_object($arr_company['contact_default_id'])) {
				$arr_contact = $this->Contact->select_one(array('_id' => $arr_company['contact_default_id']));
			} else {
				$arr_contact = $this->Contact->select_all(array(
					'arr_where' => array('company_id' => new MongoId($valueid)),
					'arr_order' => array('_id' => -1),
				));
				$arrtemp = iterator_to_array($arr_contact);
				if (count($arrtemp) > 0) {
					$arr_contact = current($arrtemp);
				} else
					$arr_contact = array();
			}

			//change contact
			if (isset($arr_contact['_id'])) {
				$arr_return['ship_to_contact_name'] = $arr_contact['first_name'] . ' ' . $arr_contact['last_name'];
				$arr_return['ship_to_contact_id'] = $arr_contact['_id'];
			}else {
				$arr_return['ship_to_contact_name'] = '';
				$arr_return['ship_to_contact_id'] = '';
			}
			//change address
			if(isset($arr_company['addresses_default_key']))
				$add_default = $arr_company['addresses_default_key'];
			if(isset($add_default) && isset($arr_company['addresses'][$add_default])) {
				foreach ($arr_company['addresses'][$add_default] as $ka => $va) {
					if ($ka != 'deleted')
						$arr_return['shipping_address'][0]['shipping_'.$ka] = $va;
					else
						$arr_return['shipping_address'][0][$ka] = $va;
				}

				//set lai province_state_id neu Company ko luu
				if(isset($arr_return['shipping_address'][0]['shipping_province_state_id']) && $arr_return['shipping_address'][0]['shipping_province_state_id']==''){
					$reprovice = $this->province_reverse('CA');
					if(isset($reprovice[$arr_return['shipping_address'][0]['shipping_province_state']]))
						$arr_return['shipping_address'][0]['shipping_province_state_id'] = $reprovice[$arr_return['shipping_address'][0]['shipping_province_state']];
				}

				//change tax
				if(isset($arr_return['shipping_address'][0]['shipping_province_state_id']) && $arr_return['shipping_address'][0]['shipping_province_state_id']!=''){
					$arr_return['tax_key'] = $arr_return['shipping_address'][0]['shipping_province_state_id'];
					$this->selectModel('Tax');
					$arr_tax = $this->Tax->tax_select_list();
					if(isset($arr_tax[$arr_return['tax_key']]))
						$arr_return['tax'] = $arr_tax[$arr_return['tax_key']];
				// neu company ko lưu shipping_province_state_id
				}
			}

		}
		/*
		* Xử lý chọn shipper_company_name
		*/
		if ($field == 'shipper_company_name' && $valueid != ''){
			$arr_return['shipper_company_id'] = new MongoId($valueid);
		}
		/*
		* Xử lý chọn combobox contact
		*/
		if ($field == 'contact_name' && $valueid != '') {
			$arr_return = array(
				'contact_name' => '',
				'contact_id' => '',
				'phone' => '',
				'email' => '',
			);
			//change contacts
			$arr_return['contact_name'] = $value;
			$arr_return['contact_id'] = new MongoId($valueid);
			//find more from contact
			$this->selectModel('Contact');
			$arr_contact = $this->Contact->select_one(array('_id' => new MongoId($valueid)));

			//change phone
			if (isset($arr_contact['direct_dial']) && $arr_contact['direct_dial'] != '')
				$arr_return['phone'] = $arr_contact['direct_dial'];
			//change email
			if (isset($arr_contact['email']))
				$arr_return['email'] = $arr_contact['email'];
			//nếu company khác hiện có
			if (isset($arr_contact['company_id'])) {
				echo '';
			}
		}
		/*
		* Xử lý chọn our_rep
		*/
		if ($field == 'our_rep' && $valueid != ''){
			$arr_return['our_rep_id'] = new MongoId($valueid);
		}

		/*
		* Xử lý chọn ship_to_contact_name
		*/
		if ($field == 'ship_to_contact_name' && $valueid != ''){
			$arr_return['ship_to_contact_id'] = new MongoId($valueid);
		}
		/*
		* Xử lý chọn combobox received_by_contact_name
		*/
		if ($field == 'received_by_contact_name' && $valueid != ''){
			$arr_return['received_by_contact_id'] = new MongoId($valueid);
		}
		/*
		* Xử lý thay đổi TAX
		*/
		if ($field == 'shipping_address'){
			$value = end($value);
			if( isset($value['shipping_province_state_id']) )
				$arr_return['tax_key'] = $value['shipping_province_state_id'];
			$this->selectModel('Tax');
			$arr_tax = $this->Tax->tax_select_list();
			if(isset($arr_tax[$arr_return['tax_key']]))
				$arr_return['tax'] = $arr_tax[$arr_return['tax_key']];
			$arr_return['shipping_address'] = array();
			$arr_return['shipping_address'][0] = $value; //ep address ve array vi address chep tu company qua co the khong phai la array thu 0

			//tinh lai thue cho line entry
			if(isset($arr_return['tax'])){
				$taxper = explode("%",$arr_return['tax']);
				$taxper = (float)trim($taxper[0]);
				$this->update_all_option('products',array('taxper'=>$taxper));
			}
			return $arr_return;
		}

		/*
		* Xử lý status
		*/
		if($field=='purchase_orders_status' && $value=='Received'){
			$arr_return['delivery_date'] = new MongoDate(time());
			$arr_return['received_by_contact_id']= $this->opm->user_id();
			$arr_return['received_by_contact_name']= $this->opm->user_name();
		}else if($field=='purchase_orders_status'){
			$arr_return['delivery_date'] = '';
			$arr_return['received_by_contact_id']= '';
			$arr_return['received_by_contact_name']= '';
		}
		if($field=='salesorder_name'){
			$arr_value = explode('_@_', $value);
			$arr_return['salesorder_name'] =$arr_value[0];
			$arr_return['salesorder_number'] =$arr_value[1];
			$arr_return['salesorder_id'] = new MongoId($valueid);
		}

		return $arr_return;
	}
}