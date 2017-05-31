<?php
class ReceiptsController extends MobileAppController {
	var $modelName = 'Receipt';
	var $name = 'Receipts';
	function beforeFilter() {
		parent::beforeFilter();
		if(!$this->request->is('ajax'))
			$this->set('arr_tabs', array(
			           			'receipt allocation' => array('allocation'),
			           			));
	}

	public function entry($id = '0', $num_position = -1) {
		$arr_tmp = $this->entry_init($id, $num_position, 'Receipt', 'receipts');

		$this->selectModel('Setting');
		$arr_receipts_paid_by = $this->Setting->select_option(array('setting_value' => 'receipts_paid_by'), array('option'));
		$this->set('arr_receipts_paid_by', $arr_receipts_paid_by);
		
		$arr_receipts_our_bank_account = $this->Setting->select_option(array('setting_value' => 'receipts_our_bank_account'), array('option'));
		$this->set('arr_receipts_our_bank_account', $arr_receipts_our_bank_account);

		if (isset($arr_tmp['status_id'])) {
			$arr_tmp['status'] = $arr_tmp['status_id'];
		}
		$arr_tmp['receipt_date'] = (is_object($arr_tmp['receipt_date'])) ? date('m/d/Y', $arr_tmp['receipt_date']->sec) : '';

		$this->set('arr_country', $this->Setting->select_option(array('setting_value' => 'lists_country'), array('option')));

		$arr_tmp1['Receipt'] = $arr_tmp;
		$this->data = $arr_tmp1;
		$arr_receipt_id = array();
		if (isset($arr_tmp['our_rep_id']))
			$arr_receipt_id[] = $arr_tmp['our_rep_id'];
	}

	function auto_save() {
		if (!empty($this->data)) {
			$id = $this->get_id();
			$arr_return = array();
			$arr_post_data = $this->data['Receipt'];
			$arr_save = $arr_post_data;
			$arr_save['_id'] = new MongoId($id);
			$this->selectModel('Receipt');
		
			if(isset($arr_save['code'])){
				if(!is_numeric($arr_save['code'])){
					echo json_encode(array('status'=>'error','message'=>'must_be_numberic'));
					die;
				}
				if(!is_numeric($arr_save['amount_received'])){
					echo json_encode(array('status'=>'error','message'=>'must_be_numberic'));
					die;
				}
				$arr_tmp = $this->Receipt->select_one(array('code' => (int) $arr_save['code'], '_id' => array('$ne' => new MongoId($id))));
				if (isset($arr_tmp['code'])) {
					echo json_encode(array('status'=>'error','message'=>'ref_no_existed'));
					die;
				}
			}

			$receipt_date = $this->Common->strtotime($arr_save['receipt_date'] . ' 00:00:00');
			$arr_save['receipt_date'] = new MongoDate($receipt_date);

			if( strlen($arr_save['name']) > 0 )
				$arr_save['name']{0} = strtoupper($arr_save['name']{0});

			$this->selectModel('Receipt');
			if ($this->Receipt->save($arr_save))
				$arr_return['status'] = 'ok';
			else
				$arr_return = array('status'=>'error','message'=>'Error: ' . $this->Receipt->arr_errors_save[1]);
			echo json_encode($arr_return);
		}
		die;
	}

	function lists( $content_only = '' ) {
		$this->selectModel('Receipt');
		$limit = 20; $skip = 0;
		// dùng cho sort
		$sort_field = 'code';
		$sort_type = -1;
		if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
			if( $_POST['sort']['type'] == 'desc' ){
				$sort_type = -1;
			}
			$sort_field = $_POST['sort']['field'];
			$this->Session->write('receipts_lists_search_sort', array($sort_field, $sort_type));

		}elseif( $this->Session->check('receipts_lists_search_sort') ){
			$session_sort = $this->Session->read('receipts_lists_search_sort');
			$sort_field = $session_sort[0];
			$sort_type = $session_sort[1];
		}
		$arr_order = array($sort_field => $sort_type);
		$this->set('sort_field', $sort_field);
		$this->set('sort_type', ($sort_type === 1)?'asc':'desc');


		// dùng cho điều kiện
		$cond = array();
		if( $this->Session->check('receipts_entry_search_cond') ){
			$cond = $this->Session->read('receipts_entry_search_cond');
		}

		// dùng cho phân trang
		if(isset($_POST['offset'])){
			$skip = $_POST['offset'];
		}
		// query
		$arr_receipts = $this->Receipt->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'limit' => $limit,
			'skip' => $skip
		));
		$this->set('arr_receipts', $arr_receipts);
		$this->selectModel('Setting');
		$this->set('arr_receipts_type', $this->Setting->select_option(array('setting_value' => 'receipts_type'), array('option')));

		if($content_only!=''){
			$this->render("lists_ajax");
		}
	}

	function delete($id = 0) {
		$arr_save['_id'] = $id;
		$arr_save['deleted'] = true;
		$error = 0;
		if (!$error) {
			$this->selectModel('Receipt');
			if ($this->Receipt->save($arr_save)) {
				$this->Session->delete($this->name.'ViewId');
				if($this->request->is('ajax'))
					echo 'ok';
				else
					$this->redirect('/mobile/receipts/entry');
			} else {
				echo 'Error: ' . $this->Receipt->arr_errors_save[1];
			}
		}
		die;
	}

	public function add() {
		$this->selectModel('Receipt');
		$arr_save = array();

		$this->Receipt->arr_default_before_save = $arr_save;
		if ($this->Receipt->add())
			$this->redirect('/mobile/receipts/entry/' . $this->Receipt->mongo_id_after_save);
		die;
	}
	function save_data(){
		if(!empty($_POST)){
			$controller = $this->params->params['controller'];
			$model = ucfirst(Inflector::singularize($controller));
			$this->selectModel($model);
			if ($_POST['field']=='salesaccount_name') {
				$arr_save = array(
				                    '_id' => new MongoId($_POST['ids']),
				                    'salesaccount_name' => $_POST['value'],
				                    'salesaccount_id' => new MongoId($_POST['valueid'])
				                );
			} else {
				$arr_save = array(
				                    '_id' => new MongoId($_POST['ids']),
				                    $_POST['field'] => $_POST['value']
				                );
			}
			$arr_save = array_merge($arr_save,$this->before_save($_POST['field'],$arr_save,$_POST['ids']));
			$this->$model->save($arr_save);
			echo 'ok';
		}
		die;
	}
	function allocation(){
		$ret = array();
		$total_allocated = 0;
		$option_select=array();
		if($this->get_id()!=''){
			$this->selectModel('Receipt');
			$arr_tmp = $this->Receipt->select_one(array('_id' => new MongoId($this->get_id())));
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
		//pr($ret);die;
		$this->set('total_allocated',$total_allocated);
		$this->set('arr_allocated',$ret);
		return $ret;
		die('123');
	}
}