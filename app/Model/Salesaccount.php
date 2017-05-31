<?php
require_once APP.'Model'.DS.'AppModel.php';
class Salesaccount extends AppModel {
	public $arr_settings = array(
								'module_name' => 'Salesaccount'
							);
	public $arr_temp = array();
	public $temp = '';
	public $arr_key_nosave = array('setup','none','none2','mongo_id');
	public $arr_type_nosave = array('autocomplete','id');
	public $arr_default_before_save = array();

	function __construct($db) {
		$this->_setting();
		if(is_object($db)){
			$this->collection = $db->selectCollection($this->arr_settings['colection']);
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->db = $db;
		}
	}


	// Thêm record, gán các giá trị mặc định và gán field $field = $values,
/*	public function add($field='',$values=''){
		$this->arr_temp = array();
		if($this->arrfield()){
			$this->arr_temp[$field] = $values;
			$this->save($this->arr_temp);
			$this->arr_temp = array();
			return $this->mongo_id_after_save.'||'.$values; //xuất ra chuỗi để js hiển thị ra html
		}else
			return '';
	}*/

	public function add() {
		$arr_save = array();
		$arr_save['no'] = $this->get_auto_code('no');
		$arr_save['status'] = 'Current';
		$arr_save['status_id'] = 'Current';
		$arr_save['balance'] = 0;
		$arr_save['invoices_credits'] = 0;
		$arr_save['receipts'] = 0;
		$arr_save['credit_limit'] = 0;
		$arr_save['invoices_credits'] = 0;
		$arr_save['quotation_limit'] = 0;
		$arr_save['difference'] = 0;
		$arr_save['payment_terms'] = $arr_save['payment_terms_id'] = '';
		$arr_save['tax_code'] = $arr_save['tax_code_id'] = '';
		$arr_save['nominal_code'] = $arr_save['nominal_code_id'] = '';
		$arr_save = array_merge($arr_save, $this->arr_default_before_save);

		if ($this->save($arr_save)) {
			$arr_save['_id'] = $this->mongo_id_after_save;
			require_once APP.'Model'.DS.'Company.php';
			$CompanyModel = new Company($this->db);

			require_once APP.'Model'.DS.'Receipt.php';
			$ReceiptModel = new Receipt($this->db);

			$company = $CompanyModel->select_one(array('_id'=>$arr_save['company_id']),array('name'));
			$ReceiptModel->arr_default_before_save['salesaccount_name'] = isset($company['name']) ? $company['name'] : '';
			$ReceiptModel->arr_default_before_save['salesaccount_id'] = $this->mongo_id_after_save;
			$ReceiptModel->arr_default_before_save['company_id'] = $arr_save['company_id'];
			$ReceiptModel->add();
			return $arr_save;
		} else {
			echo 'Error: ' . $this->arr_errors_save[1];die;
		}
	}

	public function update_account($id, $option = array()){
		if( !is_object($id) )$id = new MongoId($id);
		// kiểm tra là Company hay Contact
		$arr_save = array();
		$arr_save['invoices_credits'] =0;
		if( !isset($option['model']) ){
			$arr_save = $this->select_one(array('_id' => $id));
		}else{
			if( $option['model'] == 'Company' ){
				$arr_save = $this->select_one(array('company_id' => $id));
			}elseif( $option['model'] == 'Contact' ){
				$arr_save = $this->select_one(array('contact_id' => $id));
			}
		}

		// tự động thêm mới SA nếu chưa tồn tại
		if( !isset($arr_save['_id']) ){
			if( $option['model'] == 'Company' ){
				$this->arr_default_before_save = array('company_id' => $id);
			}elseif( $option['model'] == 'Contact' ){
				$this->arr_default_before_save = array('contact_id' => $id);
			}
			$arr_save = $this->add();
		}

		// update balance, invoices_credits, receipts
		if( isset($option['balance']) ){
			$arr_save['balance'] = (float)$arr_save['balance'] + $option['balance'];
		}
		if( isset($option['invoices_credits']) ){
			$arr_save['invoices_credits'] = (float)$arr_save['invoices_credits'] + $option['invoices_credits'];
		}
		if( isset($option['receipts']) ){
			if(!isset($arr_save['receipts']))
				$arr_save['receipts'] = 0;
			$arr_save['receipts'] = (float)$arr_save['receipts'] + $option['receipts'];
		}

		if (!$this->save($arr_save)) {
			echo 'Error: ' . $this->arr_errors_save[1]; die;
		}
		return $arr_save;
	}

	//update field $field  = $values của record có _id là $ids
	public function update($ids='',$field='',$values=''){
			$arr_temp = $cal_price = array();
			$arr_temp['_id'] = $ids;
			if(preg_match("/_date$/",$field))
				$arr_temp[$field] = new MongoDate($values);
			else
				$arr_temp[$field] = $values;
			if($this->save($arr_temp))
				return $ids.'||'.$arr_temp[$field];
	}

}