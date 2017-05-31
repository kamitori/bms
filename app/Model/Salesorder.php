<?php
require_once APP.'Model'.DS.'AppModel.php';
class Salesorder extends AppModel {
	public $arr_settings = array(
								'module_name' => 'Salesorder'
							);
	public $arr_temp = array();
	public $temp = '';
	public $arr_key_nosave = array('setup','none','none2','mongo_id');
	public $arr_type_nosave = array('autocomplete','id');

	public $Task = '';

	function __construct($db){
		if(is_object($db)){
			$this->_setting();
			$this->collection = $db->selectCollection($this->arr_settings['colection']);
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->collection->ensureIndex(array('company_id'=>1), array('name'=>'company_id'));
			$this->collection->ensureIndex(array('job_id'=>1), array('name'=>'job_id'));
			$this->collection->ensureIndex(array('quotation_id'=>1), array('name'=>'quotation_id'));
			$this->Task = $db->selectCollection('tb_task'); //khai bao task 1 lan de dung
			$this->db = $db;
			$this->change_language();
		}

	}


	// Thêm record, gán các giá trị mặc định và gán field $field = $values,
	public $arr_default_before_save = array();
	public function add($field='',$values=''){
		$this->arr_temp = array();
		if($this->arrfield()){

			if( $field != '' )
				$this->arr_temp[$field] = $values;
			//BEGIN custom
			$this->arr_temp['code'] = $this->arr_temp['name'] = $this->get_auto_code('code');
			$this->arr_temp['our_rep_id'] = $this->arr_temp['our_rep'] = '';
			$this->arr_temp['our_csr_id'] = $this->user_id();
			$this->arr_temp['our_csr'] = $this->user_name();
			$this->arr_temp['status_id'] = $this->arr_temp['status'] = 'New';
			$this->arr_temp['salesorder_date'] = new MongoDate(strtotime(date('Y-m-d'))); // chỉ lấy ngày, không lấy giờ bì sẽ bị lỗi chỗ khác
			// $this->arr_temp['payment_due_date'] = '';
			$this->arr_temp['invoice_address'][0]['invoice_country'] = 'CA';
			$this->arr_temp['invoice_address'][0]['deleted'] = false;
			$this->arr_temp['shipping_address'][0]['shipping_country'] = 'CA';
			$this->arr_temp['shipping_address'][0]['deleted'] = false;
			$this->arr_temp['job_id'] = $this->arr_temp['quotation_id'] = $this->arr_temp['company_id'] = '';
			// BaoNam: thêm các default khi thêm mới 1 SO
			$this->arr_temp['payment_due_date'] = $this->arr_temp['salesorder_date'];

			$this->arr_temp['sum_sub_total'] = $this->arr_temp['sum_tax'] = $this->arr_temp['sum_amount'] = 0;
			// BaoNam: arr_default_before_save dùng để gán trước các giá trị mặc định từ controller, ví dụ trong các tạo từ các options
			// mục đích là dùng chung 1 hàm add thôi để sau này chỉ sửa 1 chỗ
			$this->arr_temp = array_merge($this->arr_temp, $this->arr_default_before_save);
			//  --- end ---
			if(trim($this->arr_temp['name']) == '')
				$this->arr_temp['name'] = $this->arr_temp['code'].' - '.$this->arr_temp['company_name'];

			//END custom
			$this->save($this->arr_temp);
			$this->arr_temp = array();

			return $this->mongo_id_after_save.'||'.$values; //xuất ra chuỗi để js hiển thị ra html
		}else
			return '';
	}
	public function save(array $arr = array()){
		try{
			foreach ($arr as $key => $value) {
				if(is_string($value))$arr[$key] = trim($arr[$key]);
				if(is_string($value) && substr($value, 0, 1) != "+" && is_numeric($value) && !is_float($value) && ( substr($value, 0, 1) != "0" || $value == "0" ) )$arr[$key] = (int)$value;

				if( $key == 'name' && is_string($arr[$key]) && strlen($arr[$key]) > 0 )
					$arr[$key]{0} = strtoupper($arr[$key]{0});

				// if(!is_array($value))
					// if(trim($value) == "")$arr[$key] = " "; // blank to save to db, we have fields, if not mongo will remove field
			}
			$this->identity($arr);
			if(!isset($arr['_id'])){// add new
				$arr['_id'] = new MongoId();
				$arr['created_by'] = $arr['modified_by'] = $_SESSION['arr_user']['contact_id'];
				$arr['date_modified'] = new MongoDate(strtotime('now'));
				if($this->has_field_deleted)
					$arr = array_merge(array('deleted' => false), $arr);
				$this->collection->insert($arr, array('safe'=>true));
				require_once APP.'Model'.DS.'Task.php';
				$TaskModel = new Task($this->db);
				$current_date = strtotime(date('Y-m-d H:00:00'));
				if(isset($arr['payment_terms']) &&$arr['payment_terms']==0){
					require_once APP.'Model'.DS.'Stuffs.php';
					$StuffsModel = new Stuffs($this->db);
					$accountant = $StuffsModel->select_one(array('value'=>'Accountant'));
					if(isset($accountant['accountant_id'])){
						$arr_save = array();
						$arr_save['our_rep_type'] = 'contacts';
						$arr_save['salesorder_id'] = new MongoId($arr['_id']);
						$arr_save['type_id'] = '';
						$arr_save['type'] = 'Accountant';
						$arr_save['name'] = (isset($arr['name']) ? $arr['name'] : '');
						$arr_save['our_rep'] = $accountant['accountant'];
						$arr_save['our_rep_id'] = $accountant['accountant_id'];
						$arr_save['work_start'] = new MongoDate($current_date);
						$arr_save['work_end'] = new MongoDate($current_date + HOUR);
						$TaskModel->arr_default_before_save = $arr_save;
						$TaskModel->add();
					}
				}
				$this->createDefaultTask($TaskModel, $arr);
				$this->createAssetTask($TaskModel, $arr);
				// Save log
				// $this->save_log($arr);

			}else{ //is update
				if(!is_object($arr['_id']))
					$arr['_id'] = new MongoId($arr['_id']);
				if (isset($arr['status']) ) {
					if (in_array($arr['status'], array('Cancelled', 'Completed'))) {
						$arr['asset_status'] = $arr['status'];
					}
				}
				$this->old_data = $this->select_one(array( '_id' => new MongoId($arr['_id'])));//old data

				$arr['modified_by'] = $_SESSION['arr_user']['contact_id'];
				$arr['date_modified'] = new MongoDate(strtotime('now'));
				if($this->has_field_deleted)
					$arr = array_merge(array('deleted' => false), $arr);
				$arr_tmp_save = $arr;
				if(isset($arr_tmp_save['payment_terms']) && $arr_tmp_save['payment_terms']==0){
					require_once APP.'Model'.DS.'Task.php';
					$TaskModel = new Task($this->db);
					$accountant_task = $TaskModel->select_one(array('salesorder_id'=>new MongoId($arr_tmp_save['_id']),'type'=>'Accountant'),array('_id'));
					if(!isset($accountant_task['_id'])){
						require_once APP.'Model'.DS.'Stuffs.php';
						$StuffsModel = new Stuffs($this->db);
						$accountant = $StuffsModel->select_one(array('value'=>'Accountant'));
						if(isset($accountant['accountant_id'])){
							$current_date = strtotime(date('Y-m-d H:00:00'));
							$arr_save = array();
							$arr_save['our_rep_type'] = 'contacts';
							$arr_save['salesorder_id'] = new MongoId($arr['_id']);
							$arr_save['type_id'] = '';
							$arr_save['type'] = 'Accountant';
							$arr_save['name'] = (isset($arr_tmp_save['name']) ? $arr_tmp_save['name'] : '');
							$arr_save['our_rep'] = $accountant['accountant'];
							$arr_save['our_rep_id'] = $accountant['accountant_id'];
							$arr_save['work_start'] = new MongoDate($current_date);
							$arr_save['work_end'] = new MongoDate($current_date + HOUR);
							$TaskModel->arr_default_before_save = $arr_save;
							$TaskModel->add();
						}
					}
				}
				unset($arr_tmp_save['_id']);
				$this->collection->update(array( '_id' => new MongoId($arr['_id'])), array( '$set' => $arr_tmp_save ));

				// Save log
				$this->save_log($arr);
			}
			$this->mongo_id_after_save = $arr['_id'];

			return true;
		}catch(MongoCursorException $e){
			$this->arr_errors_save = array( $e->getCode(), $e->getMessage() );
			return false;
		}
	}
	//update field $field  = $values của record có _id là $ids
	public function update($ids='',$field='',$values=''){
		if(!in_array($field,$this->arr_key_nosave) && !in_array($field,$this->arr_rel_set())){
			$arr_temp = $cal_price = array();
			$arr_temp['_id'] = $ids;

			//tim cac field name cho phep custom va set id = ''
			$arr_temp = array_merge((array)$arr_temp,(array)$this->set_null_id($field));

			//END special
			if(preg_match("/_date$/",$field))
				$arr_temp[$field] = new MongoDate($values);
			else if(preg_match("/_id$/",$field) && !is_object($values))
				$arr_temp[$field] = new MongoId($values);
			else
				$arr_temp[$field] = $values;

			if($field=='status'){
				$arr_temp['status_id'] = $values;
			}

			if($this->save($arr_temp))
				return $ids.'||'.$arr_temp[$field];
		}else if($ids!='')
			return $ids;
	}



	//Sắp xếp lại thứ tự arr_setting và bỏ bớt các giá trị mảng ko dùng
	public function re_array_fields($arr_new=array(),$arr_fields=array()){
		$ret = array();
		foreach($arr_new as $vss){
			$ret[$vss] = $arr_fields[$vss];
		}
		return $ret;
	}

	public function createDefaultTask($task, $arr)
	{
		require_once APP.'Model'.DS.'Contact.php';
		$contactModel = new Contact($this->db);
		$contact = $contactModel->select_one(array('_id'=>	$arr['created_by']),array('full_name'));
		$arr_save = array(
			'system_default'=> true,
			'our_rep' 		=> isset($contact['full_name']) ? $contact['full_name'] : '',
			'our_rep_id' 	=> $arr['created_by'],
			'our_rep_type' 	=> 'contacts',
			'type_id'		=> 'SO',
			'type'			=> 'SO',
			'salesorder_id' => new MongoId($arr['_id']),
			'name'			=> (isset($arr['name']) ? $arr['name'] : '')
		);
		if( date('i') <= 30 ){
			$arr_save['work_end'] = new MongoDate( $arr['payment_due_date']->sec + date('H')*3600 );
			$arr_save['work_start'] = new MongoDate( $arr_save['work_end']->sec - 3600 );
		}else{
			$arr_save['work_end'] = new MongoDate( $arr['payment_due_date']->sec + date('H')*3600 + 3600 );
			$arr_save['work_start'] = new MongoDate( $arr_save['work_end']->sec - 3600 );
		}
		$task->arr_default_before_save = $arr_save;
		return $task->add();
	}

	public function createAssetTask($task, $arr)
	{
		$current_date = strtotime(date('Y-m-d H:00:00'));
		$arr_save = array(
			'custom_by_user'=> true,
			'our_rep' 		=> '06. Fulfillment',
			'our_rep_id' 	=> new MongoId('52b73d7ee6f2b2c7440944e5'),
			'our_rep_type' 	=> 'assets',
			'type_id'		=> 'Production',
			'type'			=> 'Production',
			'salesorder_id' => new MongoId($arr['_id']),
			'work_start'	=> new MongoDate($current_date),
			'work_end'		=> new MongoDate($current_date + HOUR),
			'name'			=> (isset($arr['name']) ? $arr['name'] : '')
		);
		$task->arr_default_before_save = $arr_save;
		return $task->add();
	}



	public function updateAssetStatus($orderId, $taskId, $our_rep, $taskStatus)
	{
		if (!is_object($orderId) || empty($taskStatus)) {
			return false;
		}
		$order = $this->select_one(array('_id' => $orderId), array('status'));
		if (in_array($order['status'], array('Completed', 'Cancelled'))) {
			return false;
		}
		$status = $order['status'];
		if (in_array($taskStatus, array('Cancelled', 'New'))) {
			require_once APP.'Model'.DS.'Task.php';
			$taskModel = new Task($this->db);
			$task = $taskModel->select_one(array(
										'salesorder_id' => $orderId,
										'our_rep_type' => 'assets',
										'status' => array(
											'$nin' => array(
													'New',
													'Cancelled'
												)
											),
										'_id' => array('$ne' => $taskId)
									), array(
										'our_rep', 'status'
									), array(
										'date_modified' => -1
									));
			if (!empty($task)) {
				$task = array_merge(array('our_rep' => '', 'status' => ''), $task);
				$status = $task['our_rep'].' - '.$task['status'];
			}
		} else {
			$status = $our_rep .' - '. $taskStatus;
		}
		return $this->collection->update(array(
											'_id' 	 => $orderId,
										),
										array(
											'$set' => array(
												'asset_status' => $status
											)
										));
	}




}