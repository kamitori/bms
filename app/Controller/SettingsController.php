<?php

App::uses('AppController', 'Controller');
class SettingsController extends AppController {

	public $helpers = array();
	var $modelName = 'Setting';
	public $layout = 'setup';

	public function beforeFilter() {
		parent::beforeFilter();
		if(!$this->check_permission('setup_@_entry_@_view'))
			$this->redirect('/');
	}

	// ========== Admin page ============================================================================
	public function get_province_from_country($country_id) {
		$this->selectModel('Province');
		$this->set('arr_province', $this->Province->select_all(array(
			'arr_where' => array('country_id' => "CA"),
			'arr_field' => array('_id', 'key', 'name')
		)));
	}

	public function index() {
		if( !$this->Session->check('setup_remember_page') ){
			$this->general();
		}
	}

	public function list_message() {
		$this->Session->write('setup_remember_page', 'list_message');
	}

	// public function convert() {
	// 	$this->selectModel('Permission');
	// 	$arr_setting = $this->Permission->select_all();
	// 	foreach ($arr_setting as $key => $value) {
	// 		$value['permission'] = array(
	// 			array('entry' => array(
	// 				array('name' => 'Add', 'codekey' => 'add', 'description' => '', 'deleted' => false),
	// 				array('name' => 'Delete', 'codekey' => 'delete', 'description' => '', 'deleted' => false),
	// 				array('name' => 'View', 'codekey' => 'view', 'description' => '', 'deleted' => false),
	// 				array('name' => 'Edit', 'codekey' => 'edit', 'description' => '', 'deleted' => false)
	// 			))
	// 		);
	// 		$this->Permission->save($value);
	// 	}
	// 	echo 'xong';
	// 	die;
	// }

	public function list_message_detail($message_type) {
		$this->selectModel('SettingMessage');
		$arr_setting = $this->SettingMessage->select_all(array(
			'arr_where' => array(
				'message_type' => $message_type,
			),
			'arr_order' => array('name' => 1)
		));
		$this->set('message_type', $message_type);
		$this->set('arr_setting', $arr_setting);
	}


	public function list_message_detail_auto_save() {

		if (!empty($this->data)) {

			$post = $this->data['Setting'];
			$arr_tmp = $post;
			$arr_save = array();

			$arr_save['_id'] = $post['_id'];
			$arr_save['content'] = $post['content'];
			$arr_save['key'] = $post['key'];

			$this->selectModel('SettingMessage');

			if ($this->SettingMessage->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->SettingMessage->arr_errors_save[1];
			}
		}
		die;
	}

	public function update_system_admin($id, $value = "false") {

		$arr_save['_id'] = $id;
		$arr_save['system_admin'] = ($value == "true")?true:false;
		$this->selectModel('Setting');
		if ($this->Setting->save($arr_save)) {
			echo 'ok';
		} else {
			echo 'Error: ' . $this->Setting->arr_errors_save[1];
		}
		die;
	}

	public function list_message_detail_add($message_type) {
		$this->selectModel('SettingMessage');

		$arr_new_message = array('_id' => new MongoId()
			, 'content' => ''
			, 'key' => '(undefined)'
			, 'deleted' => false
			, 'message_type' => $message_type
		);
		$this->SettingMessage->collection->insert($arr_new_message);

		$this->list_message_detail($message_type);
		$this->render('list_message_detail');
	}

	public function delete_message_detail_null($message_type) {


		$this->selectModel('SettingMessage');
		$this->SettingMessage->collection->update(
				array(
			'content' => '',
			'key' => '(undefined)'
				), array('$set' => array(
				'deleted' => true
			)), array('multiple' => true));

		$this->list_message_detail($message_type);
		$this->render('list_message_detail');
	}

	/*	 * ********************************************Nguyen end code**************************************************** */
	public function list_and_menu_detail_add($id='', $all_field_of_option='') {
		$arr_field = explode('_._', $all_field_of_option);
		foreach ($arr_field as $value) {
			if ($value == 'name') {
				$option[$value] = '(undefined)';
			} elseif (in_array($value, array('deleted'))) { // nếu có option nào bằng false thì b�? vào array này
				$option[$value] = false;
			} elseif (in_array($value, array('cal_enabled_move'))) { // nếu có option nào bằng true thì b�? vào array này
				$option[$value] = true;
			} else {
				$option[$value] = '';
			}
		}
		$this->selectModel('Setting');
		$this->Setting->collection->update(
				array('_id' => new MongoId($id)),
				array('$push' => array(
						'option' => $option
					)
				)
		);
		$lang = 'en';
		$this->list_and_menu_detail($id,$lang);
		$this->render('list_and_menu_detail');   //dùng một view khác view mặc định trùng tên với action
	}

	public function list_and_menu_detail_auto_save() {
		if (!empty($this->data)) {
			$post = $this->data['Setting'];
			$this->selectModel('Setting');
			$arr_save = $this->Setting->select_one(array('_id'=>new MongoId($post['_id'])));
			foreach($post as $key => $value){
				if($key == 'option_key' ||  $key == '_id')
					continue;
				$arr_save['option'][$post['option_key']][$key] = $post[$key];
			}
			$arr_save['option'][$post['option_key']]['name'] = $post['name'];
			$arr_save['option'][$post['option_key']]['value'] = $post['value'];
			$arr_save['option'][$post['option_key']]['deleted'] = ($post['deleted'])?true:false;
			if(isset($post['color'])){// dùng cho những option có thêm color như tasks_status
				$arr_save['option'][$post['option_key']]['color'] = $post['color'];
			}
			if ($this->Setting->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->Setting->arr_errors_save[1];
			}
		}
		die;
	}

	public function list_and_menu() {
		$this->selectModel('Setting');
		$arr_settings = $this->Setting->select_all(array(
												   'arr_where'=>array(
																	  'setting_value'=>array('$nin'=>array('email_setting')),
																	  'name'=>array('$ne'=>'Pricing Rules')
																	  ),
												   'arr_order' => array('setting_name' => 1)
												   ));
		$this->set('arr_settings', $arr_settings);

		$this->Session->write('setup_remember_page', 'list_and_menu');
	}

	public function list_add()
	{
		$this->selectModel('Setting');
		$arrSave = array(
				'setting_name' 	=> $_POST['name'],
				'setting_value' => $_POST['value'],
				'system_admin'	=> false,
				'option'		=> array()
			);
		$this->Setting->save($arrSave);
		die;
	}

	public function list_and_menu_detail($id,$lang) {
		$this->selectModel('Setting');
		$arr_setting = $this->Setting->select_one(array('_id' => new MongoId($id)));
		$this->set('arr_setting', $arr_setting);
		$this->selectModel('Language');
		$arr_tmp = array();
		$arr_language = $this->Language->select_all(array('arr_order' => array('name' => 1)));
		foreach($arr_language as $key => $value){
			$arr_tmp[$value['value']] = isset($value['lang'])?$value['lang']:'';
		}
		$this->set('arr_language', $arr_tmp);
		$this->set('arr_language_value', $lang);
	}

	public function privileges(){
		$this->selectModel('Permission');
		$arr_permissions = $this->Permission->select_all(array('arr_order' => array('controller' => 1)));
		$this->set('arr_permissions', $arr_permissions);

		$this->Session->write('setup_remember_page', 'privileges');
	}

	public function privileges_detail($id) {
		$this->selectModel('Permission');
		$arr_privilege = $this->Permission->select_one(array('_id' => new MongoId($id)));
		$this->set('arr_privilege', $arr_privilege);

		if( $this->system_admin ){
			$this->render('privileges_detail_system');
		}
	}

	public function privilege_detail_belong_to_module($id, $key) {
		$this->selectModel('Permission');
		$permission = $this->Permission->select_one(array('_id' => new MongoId($id)),array('option_list'));
		$arr_key = explode('_@_' , $key);
		if(isset($_POST['check'])){
			if($_POST['check']){
				if(!in_array($_POST['permission'], $permission['option_list'][$arr_key[0]][$arr_key[1]][$arr_key[2]]['belong_to'])){
					$permission['option_list'][$arr_key[0]][$arr_key[1]][$arr_key[2]]['belong_to'][] = $_POST['permission'];
				}
			} else {
				$find_key = array_search($_POST['permission'], $permission['option_list'][$arr_key[0]][$arr_key[1]][$arr_key[2]]['belong_to']);
				if($find_key !== false){
					unset($permission['option_list'][$arr_key[0]][$arr_key[1]][$arr_key[2]]['belong_to'][$find_key]);
				}
			}
			$this->Permission->save($permission);
			echo 'ok';
			die;
		}
		$belong_to = $permission['option_list'][$arr_key[0]][$arr_key[1]][$arr_key[2]]['belong_to'];
		$all_permissions = $this->Permission->select_all(array(
								'arr_where' => array(
									'permission' => array('$exists' => true),
									),
								'arr_order' => array('name'=>1),
								'arr_field' => array('controller','name','permission')
			));
		$this->set('current_key', $key);
		$this->set('current_id', $id);
		$this->set('belong_to', $belong_to);
		$this->set('all_permissions', $all_permissions);
	}

	public function privileges_detail_auto_save() {
		if (!empty($this->data)) {
			if( isset($this->data['Privilege']) ){
				$post = $this->data['Privilege'];
				$this->selectModel('Permission');
				$arr_permission = $this->Permission->select_one(array('_id' => new MongoId($post['_id'])));
				if( $post['page'] != $post['page_old'] ){
					if( isset($arr_permission['permission'][0][$post['page_old']][$post['option_key']]) )
						unset($arr_permission['permission'][0][$post['page_old']][$post['option_key']]);
					$arr_permission['permission'][0][$post['page_old']] = array_values($arr_permission['permission'][0][$post['page_old']]);
					if( isset($arr_permission['permission'][0][$post['page_old']]) && empty($arr_permission['permission'][0][$post['page_old']]) )
						unset($arr_permission['permission'][0][$post['page_old']]);
					// Thêm vào mảng cũ
					$arr_permission['permission'][0][$post['page']][] = array(
						'name' => $post['name'],
						'codekey' => $post['codekey'],
						'description' => $post['description'],
						'deleted' => (isset($post['deleted']) && $post['deleted'])?true:false,
						'ownership' => array('all', 'owner', 'group')
					);
				}else{
					$arr_permission['permission'][0][$post['page']][$post['option_key']]['name'] = $post['name'];
					$arr_permission['permission'][0][$post['page']][$post['option_key']]['codekey'] = $post['codekey'];
					$arr_permission['permission'][0][$post['page']][$post['option_key']]['description'] = $post['description'];
					$arr_permission['permission'][0][$post['page']][$post['option_key']]['deleted'] = (isset($post['deleted']) && $post['deleted'])?true:false;
					$arr_permission['permission'][0][$post['page']][$post['option_key']]['ownership'] = array('all', 'owner', 'group');
				}
				if(isset($post['deleted']) && $post['deleted']){
					if(!isset($arr_permission['inactive_permission']))
						$arr_permission['inactive_permission'] = array();
					$arr_permission['inactive_permission'][$arr_permission['controller'].'_@_'.$post['page'].'_@_'.$post['codekey']] = 'all';
				}elseif( isset($arr_permission['inactive_permission'][$arr_permission['controller'].'_@_'.$post['page'].'_@_'.$post['codekey']] ) ){
					unset( $arr_permission['inactive_permission'][$arr_permission['controller'].'_@_'.$post['page'].'_@_'.$post['codekey']] );
				}
			}else{
				$post = $this->data['Optionlist'];
				$this->selectModel('Permission');
				$arr_permission = $this->Permission->select_one(array('_id' => new MongoId($post['_id'])));
				$arr_permission['option_list'][$post['key_column']][$post['key_page']][$post['option_key']]['name'] = $post['name'];
				$arr_permission['option_list'][$post['key_column']][$post['key_page']][$post['option_key']]['deleted'] = ($post['deleted'])?true:false;

				if(isset($post['description']))
					$arr_permission['option_list'][$post['key_column']][$post['key_page']][$post['option_key']]['description'] = $post['description'];
				if(isset($post['codekey']))
					$arr_permission['option_list'][$post['key_column']][$post['key_page']][$post['option_key']]['codekey'] = $post['codekey'];
				if(isset($post['finish']))
					$arr_permission['option_list'][$post['key_column']][$post['key_page']][$post['option_key']]['finish'] = $post['finish'];
				if(isset($post['permission']))
					$arr_permission['option_list'][$post['key_column']][$post['key_page']][$post['option_key']]['permission'] = $post['permission'];
			}

			if ($this->Permission->save($arr_permission)) {
				if( isset($post['page']) && $post['page'] != $post['page_old'] ){
					echo 'ok_change';
				}else{
					echo 'ok';
				}
			} else {
				echo 'Error: ' . $this->Permission->arr_errors_save[1];
			}
		}
		die;
	}

	public function privileges_detail_add($id) {
		$this->selectModel('Permission');
		$arr_permission = $this->Permission->select_one(array('_id' => new MongoId($id)));
		$arr_permission['permission'][0]['undefined'] = array(
			array(
				'name' => '',
				'codekey' => '',
				'ownership' => array('all', 'owner', 'group'),
				'deleted' => '',
			)
		);
		if ($this->Permission->save($arr_permission)) {
			echo 'ok';

		}else{
			echo 'Error: ' . $this->Permission->arr_errors_save[1];
		}
		die;
	}

	// ============================================= ROLE =====================================
	public function roles(){
		$this->selectModel('Role');
		$arr_roles = $this->Role->select_all(array('arr_order' => array('name' => 1)));
		$this->set('arr_roles', $arr_roles);

		$this->Session->write('setup_remember_page', 'roles');
	}

	public function roles_module($role_id) {
		$this->selectModel('Permission');
		$arr_permissions = $this->Permission->select_all(array('arr_order' => array('controller' => 1)));
		$this->set('arr_permissions', $arr_permissions);

		$this->set('role_id', $role_id);
		$this->selectModel('Role');
		$arr_roles = $this->Role->select_one(array('_id' => new MongoId($role_id)));
		$this->set('roles', $arr_roles);

		$this->selectModel('Contact');
		$contacts = $this->Contact->select_all(array(
											'arr_where' => array(
															'is_employee' => 1,
															'roles.roles' =>array('$in' => array(new MongoId($role_id)))
															),
											'arr_order' => array('first_name' => 1),
											'arr_field' => array('first_name','last_name')
											));
		$this->set('contacts', $contacts);
		$this->set('arr_roles', $arr_roles['value']);
	}

	public function roles_module_detail($role_id, $id) {
		$this->selectModel('Permission');
		$arr_permission = $this->Permission->select_one(array('_id' => new MongoId($id)));
		$this->set('arr_permission', $arr_permission);

		// .......................
		// $role_id, lấy ra tất cả permission của role này
		$this->selectModel('Role');
		$arr_roles = $this->Role->select_one(array('_id' => new MongoId($role_id)));
		$this->set('arr_roles', $arr_roles['value']);

		$this->set('role_id', $role_id);
	}

	public function roles_add() {
		$this->selectModel('Role');
		$arr_save['name'] = 'undefined';
		$arr_save['value'] = array();
		if ($this->Role->save($arr_save)) {
			echo 'ok';
		}else{
			echo 'Error: ' . $this->Role->arr_errors_save[1];
		}
		die;
	}

	public function roles_auto_save() {
		$this->selectModel('Role');
		$post = $_POST;
		$arr_save['_id'] = $post['_id'];
		$arr_save['name'] = $post['name'];
		if ($this->Role->save($arr_save))
			echo 'ok';
		else
			echo 'Error: ' . $this->Role->arr_errors_save[1];
		die;
	}

	public function roles_module_detail_option_list(){
		if(!empty($_POST)){
			$this->selectModel('Permission');
			$arr_permission = $this->Permission->select_one(array('controller'=>$_POST['controller']));
			$this->selectModel('Role');
			$arr_save = $this->Role->select_one(array('_id' => new MongoId($_POST['role_id'])));
			//Tick all
			$controller = $_POST['controller'];
			if(isset($_POST['options'])){
				foreach($arr_permission['option_list'] as $option_list){
					foreach($option_list as $value){
						foreach($value as $val){
							$permission_path = $controller.'_@_options_@_'.$val['codekey'];
							$arr_save['value'][$permission_path] = 'all';
						}
					}
				}
			} else { //Untick all
				foreach($arr_permission['option_list'] as $option_list){
					foreach($option_list as $value){
						foreach($value as $val){
							$permission_path = $controller.'_@_options_@_'.$val['codekey'];
							if(isset($arr_save['value'][$permission_path]))
								unset($arr_save['value'][$permission_path]);
						}
					}
				}
			}
			if ($this->Role->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->Role->arr_errors_save[1];
			}
		}
		die;
	}

	public function roles_modules_detail_choose_all($checkbox){
		if (!empty($this->data)) {
			$post = $_POST;
			$this->selectModel('Role');
			$arr_save = $this->Role->select_one(array('_id' => new MongoId($post['role_id'])));
			$controller = $_POST['controller'];
			if($checkbox == 'true') {
				$this->selectModel('Permission');
				$arr_permission = $this->Permission->select_one(array('controller'=>$controller));
				foreach($arr_permission['option_list'] as $option_list){
					foreach($option_list as $value){
						foreach($value as $val){
							$permission_path = $controller.'_@_options_@_'.$val['codekey'];
							$arr_save['value'][$permission_path] = array('all');
						}
					}
				}
				foreach($arr_permission['permission'] as $permission){
					if(!is_array($permission) || empty($permission)) continue;
					foreach($permission as $key => $value){
						if(!is_array($value) || empty($value)) continue;
						foreach($value as $v){
							if(isset($v['deleted']) && $v['deleted']) continue;
							$permission_path = $controller.'_@_'.$key.'_@_'.$v['codekey'];
							$arr_save['value'][$permission_path] = array('all');
						}
					}
				}
			} else {
				$this->selectModel('Permission');
				$arr_permission = $this->Permission->select_one(array('controller'=>$controller));
				foreach($arr_permission['option_list'] as $option_list){
					foreach($option_list as $value){
						foreach($value as $val){
							$permission_path = $controller.'_@_options_@_'.$val['codekey'];
							if(isset($arr_save['value'][$permission_path]))
								unset($arr_save['value'][$permission_path]);
						}
					}
				}
				foreach($arr_permission['permission'] as $permission){
					if(!is_array($permission) || empty($permission)) continue;
					foreach($permission as $key => $value){
						if(!is_array($value) || empty($value)) continue;
						foreach($value as $v){
							if(isset($v['deleted']) && $v['deleted']) continue;
							$permission_path = $controller.'_@_'.$key.'_@_'.$v['codekey'];
							$arr_save['value'][$permission_path] = array('');
						}
					}
				}
			}
			if ($this->Role->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->Role->arr_errors_save[1];
			}
		}
		die;
	}

	public function roles_modules_detail_option_save($checkbox){
		if (!empty($this->data)) {
			$post = $_POST;
			$this->selectModel('Role');
			$arr_save = $this->Role->select_one(array('_id' => new MongoId($post['role_id'])));
			if($checkbox == 'true') {
				$arr_save['value'][$_POST['permission_path']] = array('all');
			} else {
				unset($arr_save['value'][$_POST['permission_path']]);
			}
			if ($this->Role->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->Role->arr_errors_save[1];
			}
		}
		die;
	}

	public function roles_modules_detail_auto_save(){
		if (!empty($this->data)) {
			$post = $_POST;
			$this->selectModel('Role');
			$arr_save = $this->Role->select_one(array('_id' => new MongoId($post['role_id'])));
			if(empty($post['ownership']) && isset($arr_save['value'][$_POST['permission_path']]) )
				unset($arr_save['value'][$_POST['permission_path']]);
			else
				$arr_save['value'][$_POST['permission_path']] = array($post['ownership']);
			if ($this->Role->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->Role->arr_errors_save[1];
			}
		}
		die;
	}

	// public function roles_module_detail_auto_save( $checkbox ) {

	// 	if (!empty($this->data)) {
	// 		$post = $_POST;

	// 		// kiểm tra và save vào db
	// 		$this->selectModel('Role');
	// 		$arr_save = $this->Role->select_one(array('_id' => new MongoId($post['role_id'])));

	// 		// nếu đang toàn quyền tất cả
	// 		if( isset($arr_save['value']['all']) ){
	// 			unset( $arr_save['value']['all'] );
	// 			$this->selectModel('Permission');

	// 			// cập nhật lại tất cả quyền của tất cả controller
	// 			$arr_permission = $this->Permission->select_all();
	// 			foreach ($arr_permission as $permission) {
	// 				foreach ($permission['permission'][0] as $key_page => $page) {
	// 					foreach ($page as $key => $value) {

	// 						// lưu lại toàn bộ quyền của module này, ngoại trừ quyền vừa loại bỏ
	// 						$permission_path = $permission['controller'].'_@_'.$key_page.'_@_'.$value['codekey'];
	// 						if( $permission_path != $post['permission_path'] ){
	// 							$arr_save['value'][$permission_path] = array('all');
	// 						}
	// 					}
	// 				}
	// 			}

	// 		}else{
	// 			if( $checkbox == 'true' ){
	// 				if(isset($post['ownership'])){
	// 					foreach($post['ownership'] as $key => $value){
	// 						if( !isset($key,$arr_save['value'][$post['permission_path']]) || !in_array($key,$arr_save['value'][$post['permission_path']]) )
	// 							$arr_save['value'][$post['permission_path']][] = $key;
	// 					}
	// 					if(isset($post['ownership']['all']) && in_array('all',$arr_save['value'][$post['permission_path']]) ){
	// 						if(!in_array('owner',$arr_save['value'][$post['permission_path']]))
	// 							$arr_save['value'][$post['permission_path']][] = 'owner';
	// 						if(!in_array('group',$arr_save['value'][$post['permission_path']]))
	// 							$arr_save['value'][$post['permission_path']][] = 'group';
	// 					}
	// 				} else if(isset($_POST['permission_path'])){
	// 					$arr_save['value'][$_POST['permission_path']] = 'all';
	// 				}
	// 			}
	// 			elseif( isset($arr_save['value'][$post['permission_path']]) ){
	// 				$arr_ownership = array('all','owner','group');
	// 				foreach($arr_ownership as $key => $value){
	// 					if(!isset($post['ownership'])) break;
	// 					foreach($post['ownership'] as $k => $v){
	// 						if($k == $value)
	// 							unset($arr_ownership[$key]);
	// 					}
	// 				}
	// 				if(is_array($arr_save['value'][$post['permission_path']])){
	// 					foreach($arr_save['value'][$post['permission_path']] as $k => $v){
	// 						if(in_array($v,$arr_ownership))
	// 							unset($arr_save['value'][$post['permission_path']][$k]);
	// 					}
	// 					if(empty($arr_save['value'][$post['permission_path']]))
	// 						unset($arr_save['value'][$post['permission_path']]);
	// 				} else {
	// 					unset($arr_save['value'][$post['permission_path']]);
	// 				}
	// 				$arr_save['value'][$post['permission_path']] = array_values($arr_save['value'][$post['permission_path']]);
	// 			}
	// 		}
	// 		if ($this->Role->save($arr_save)) {
	// 			echo 'ok';
	// 		} else {
	// 			echo 'Error: ' . $this->Role->arr_errors_save[1];
	// 		}
	// 	}
	// 	die;
	// }

	public function roles_set_all_permission($id, $type = 'true') {
		$this->selectModel('Role');
		$arr_save = $this->Role->select_one(array('_id' => new MongoId($id)));
		if( $type == 'true' )
			$arr_save['value'] = array('all' => '');
		else
			$arr_save['value'] = array();
		if ($this->Role->save($arr_save))
			echo 'ok';
		else
			echo 'Error: ' . $this->Role->arr_errors_save[1];
		die;
	}
	// ========================= ROLE END ===================================================================


	// ============================================= USER ROLE =====================================
	// hiển thị cột 1
	public function user_roles(){
		$this->selectModel('Contact');
		$arr_employees = $this->Contact->select_all(array(
			'arr_where' => array('is_employee' => 1),
			'arr_order' => array('first_name' => 1),
			'arr_field'	=> array('full_name')
		));
		$this->set('arr_employees', $arr_employees);

		$this->Session->write('setup_remember_page', 'user_roles');
	}

	// hiển thị cột 2
	public function user_roles_module($contact_id) {

		// Lấy danh sách tất cả module có trong bảng permission
		$this->selectModel('Permission');
		$arr_permissions = $this->Permission->select_all(array('arr_order' => array('controller' => 1)));
		$this->set('arr_permissions', $arr_permissions);

		// Lấy danh sách tất cả ROLE
		$this->selectModel('Role');
		$arr_roles = $this->Role->select_all(array('arr_field' => array('_id', 'name'), 'arr_order' => array('name' => 1)));
		$this->set('arr_roles', $arr_roles);

		// danh sách quyền của user này
		$this->set('contact_id', $contact_id);
		$this->selectModel('Contact');
		$arr_user_roles = $this->Contact->select_one(array('_id' => new MongoId($contact_id)), array('_id', 'roles'));
		if( isset($arr_user_roles['roles']) )$arr_user_roles = $arr_user_roles['roles'];
		$this->set('arr_user_roles', $arr_user_roles);
	}

	// hiển thị cột 3
	public function user_roles_module_detail($contact_id, $permission_id) {
		$this->selectModel('Permission');
		$arr_permission = $this->Permission->select_one(array('_id' => new MongoId($permission_id)));
		$this->set('arr_persmiss', $arr_permission);

		// .......................
		// danh sách quyền của user này
		$this->set('contact_id', $contact_id);
		$this->selectModel('Contact');
		$arr_user_roles = $this->Contact->select_one(array('_id' => new MongoId($contact_id)), array('_id', 'roles','inactive_permission'));
		if(!isset($arr_user_roles['inactive_permission']))
			$arr_user_roles['inactive_permission'] = array();
		$this->set('inactive_permission', $arr_user_roles['inactive_permission']);
		if( isset($arr_user_roles['roles']) )$arr_user_roles = $arr_user_roles['roles'];

		if( isset($arr_user_roles['roles']) && !empty($arr_user_roles['roles']) ){ // kiểm tra xem có group nào không, nếu có thì kiểm tra đã có những quyền gì
			$this->selectModel('Role');
			$arr_all_roles = array();
			foreach ($arr_user_roles['roles'] as $role) {
				$arr_tmp = $this->Role->select_one(array('_id' => $role), array('_id', 'value'));
				if( is_array($arr_tmp['value']) )
					$arr_all_roles = array_merge($arr_all_roles, $arr_tmp['value']);
			}
			$arr_user_roles['roles'] = $arr_all_roles;
		}

		$this->set('arr_user_roles', $arr_user_roles);

		$this->set('permission_id', $permission_id);
	}

	// click vào checkbox cột số 2
	public function user_roles_set_all_permission($contat_id, $role_id, $type = 'true') {

		$this->selectModel('Contact');
		$arr_save = $this->Contact->select_one(array('_id' => new MongoId($contat_id)),array('roles'));
		if( !isset($arr_save['roles']) ){
			$arr_save['roles'] = array();
			$arr_save['roles']['roles'] = array();
		}

		if( $type == 'true' )
			$arr_save['roles']['roles'][] = new MongoId( $role_id );

		else // == false tức là loại bỏ quyền
			foreach ($arr_save['roles']['roles'] as $key => $value) {
				if( (string)$value == $role_id ){
					unset( $arr_save['roles']['roles'][$key] );
				}
			}
		$arr_save['roles']['roles'] = array_values($arr_save['roles']['roles']);
		if ($this->Contact->save($arr_save))
			echo 'ok';
		else
			echo 'Error: ' . $this->Contact->arr_errors_save[1];
		die;
	}

	// function user_roles_module_detail_option_list(){
	// 	if(!empty($_POST)){
	// 		$this->selectModel('Permission');
	// 		$arr_permission = $this->Permission->select_one(array('controller'=>$_POST['controller']));
	// 		$this->selectModel('Contact');
	// 		$arr_save = $this->Contact->select_one(array('_id' => new MongoId($_POST['contact_id'])));
	// 		//Tick all
	// 		$controller = $_POST['controller'];
	// 			if( !isset($arr_save['roles']) ){ // khai báo default
	// 			$arr_save['roles'] = array();
	// 			$arr_save['roles']['roles'] = array();
	// 		}
	// 		if(isset($_POST['options'])){
	// 			foreach($arr_permission['option_list'] as $option_list){
	// 				foreach($option_list as $value){
	// 					foreach($value as $val){
	// 						$permission_path = $controller.'_@_options_@_'.$val['codekey'];
	// 						$arr_save['roles'][$permission_path] = 'all';
	// 					}
	// 				}
	// 			}
	// 		} else { //Untick all
	// 			foreach($arr_permission['option_list'] as $option_list){
	// 				foreach($option_list as $value){
	// 					foreach($value as $val){
	// 						$permission_path = $controller.'_@_options_@_'.$val['codekey'];
	// 						if(isset($arr_save['roles'][$permission_path]))
	// 							unset($arr_save['roles'][$permission_path]);
	// 					}
	// 				}
	// 			}
	// 		}
	// 		if ($this->Contact->save($arr_save))
	// 			echo 'ok';
	// 		else
	// 			echo 'Error: ' . $this->Contact->arr_errors_save[1];
	// 		}
	// 	die;
	// }

	public function user_roles_module_detail_option_save($checkbox){
		if (!empty($this->data)) {
			$post = $_POST;
			$this->selectModel('Contact');
			$arr_save = $this->Contact->select_one(array('_id' => new MongoId($post['contact_id'])),array('roles'));
			if($checkbox == 'true') {
				$arr_save['roles'][$_POST['permission_path']] = array('all');
			} else {
				unset($arr_save['roles'][$_POST['permission_path']]);
			}
			if ($this->Contact->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->Contact->arr_errors_save[1];
			}
		}
		die;
	}

	public function user_roles_module_detail_auto_save(){
		if (!empty($this->data)) {
			$post = $_POST;
			$this->selectModel('Contact');
			$arr_save = $this->Contact->select_one(array('_id' => new MongoId($post['contact_id'])),array('roles'));
			if(empty($post['ownership']) && isset($arr_save['roles'][$_POST['permission_path']]) )
				unset($arr_save['roles'][$_POST['permission_path']]);
			else
				$arr_save['roles'][$_POST['permission_path']] = array($post['ownership']);
			if ($this->Contact->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->Contact->arr_errors_save[1];
			}
		}
		die;
	}

	public function user_roles_module_detail_custom_permission($custom_permission) {
		$this->selectModel('Contact');
		$id = $this->Contact->user_id();
		$arr_employees = $this->Contact->select_all(array(
				'arr_where' => array(
						'is_employee' => 1,
						'_id' => array('$ne' => $id)
					),
				'arr_order' => array('first_name' => 1),
				'arr_field' => array('no','first_name','last_name','custom_permission')
			));
		$contact = $this->Contact->select_one(array('_id' => $id),array('custom_permission'));
		if(!isset($contact['custom_permission']))
			$contact['custom_permission'] = array();
		$this->set('current_permission',$contact['custom_permission']);
		$this->set('arr_employees', $arr_employees);
		$this->set('custom_permission', $custom_permission);
	}

	public function custom_permission($checkbox){
		if (!empty($this->data)) {
			$post = $_POST;
			$post['id'] = new MongoId($post['id']);
			$this->selectModel('Contact');
			$arr_save = $this->Contact->select_one(array('_id' => $this->Contact->user_id()),array('custom_permission'));
			if(!isset($arr_save['custom_permission']))
				$arr_save['custom_permission'] = array();
			if($checkbox == 'true'){
				if(!isset($arr_save['custom_permission']) || !in_array($post['id'], $arr_save['custom_permission']))
					$arr_save['custom_permission'][$post['custom_permission']][] = $post['id'];
			} else if( ($key = array_search($post['id'], $arr_save['custom_permission'][$post['custom_permission']]))!==false ) {
				unset($arr_save['custom_permission'][$post['custom_permission']][$key]);
				if(empty($arr_save['custom_permission'][$post['custom_permission']]))
					unset($arr_save['custom_permission'][$post['custom_permission']]);
			}
			if ($this->Contact->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->Contact->arr_errors_save[1];
			}
		}
		die;
	}
	/*// click vào checkbox cột số 3
	public function user_roles_module_detail_set_permission($checkbox = 'false') {
		$post = $_POST;
		$this->selectModel('Contact');
		$arr_save = $this->Contact->select_one(array('_id' => new MongoId($post['contact_id'])));
		if( !isset($arr_save['roles']) ){ // khai báo default
			$arr_save['roles'] = array();
			$arr_save['roles']['roles'] = array();
		}
		if(!isset($arr_save['inactive_permission']))
			$arr_save['inactive_permission'] = array();

		$arr_ownership = array('all','owner','group');
		if( $checkbox == 'false' ) {
			if(isset($post['ownership'])) {
				foreach($post['ownership'] as $key => $value) {
					if(($k = array_search($key, $arr_ownership)) !== false)
						unset($arr_ownership[$k]);
				}
			}
			$arr_save['inactive_permission'][$post['permission_path']] = $arr_ownership;
			if( isset($arr_save['roles'][$post['permission_path']]) ){
				foreach($arr_save['roles'][$post['permission_path']] as $k => $v){
					if(in_array($v,$arr_ownership)) {
						unset($arr_save['roles'][$post['permission_path']][$k]);
					}
				}
				if(empty($arr_save['roles'][$post['permission_path']]))
					unset($arr_save['roles'][$post['permission_path']]);
			}
		}
		else {
			if(isset($arr_save['inactive_permission'][$post['permission_path']])) {
				foreach($post['ownership'] as $key => $value) {
					if(($k = array_search($key, $arr_save['inactive_permission'][$post['permission_path']])) !== false ) {
						unset($arr_save['inactive_permission'][$post['permission_path']][$k]);
					}
				}
				if(empty($arr_save['inactive_permission'][$post['permission_path']]))
					unset($arr_save['inactive_permission'][$post['permission_path']]);
			}
			foreach($post['ownership'] as $key => $value){
				if( !isset($key,$arr_save['roles'][$post['permission_path']])
					|| !in_array($key,$arr_save['roles'][$post['permission_path']]) )
					$arr_save['roles'][$post['permission_path']][] = $key;
			}
			if(isset($post['ownership']['all']) && in_array('all',$arr_save['roles'][$post['permission_path']]) ){
				if(!in_array('owner',$arr_save['roles'][$post['permission_path']]))
					$arr_save['roles'][$post['permission_path']][] = 'owner';
			}
		}
		if(isset($arr_save['roles'][$post['permission_path']]))
			$arr_save['roles'][$post['permission_path']] = array_values($arr_save['roles'][$post['permission_path']]);
		if(isset($arr_save['inactive_permission'][$post['permission_path']]))
			$arr_save['inactive_permission'][$post['permission_path']] = array_values($arr_save['inactive_permission'][$post['permission_path']]);

		if ($this->Contact->save($arr_save))
			echo 'ok';
		else
			echo 'Error: ' . $this->Contact->arr_errors_save[1];
		die;
	}*/

	// ========================= USER ROLE END ===================================================================

	public function equipments() {
		$this->selectModel('Equipment');
		$this->Equipment->has_field_deleted = false;
		$arr_equipments = $this->Equipment->select_all(array('arr_order' => array('name' => 1)));
		$this->selectModel('Setting');
		$arr_uom = array_merge(
		   array('hour'=>'Hour','Linch'=>'Lin. ft','Sq.ft.'=>'Sq.ft.','inch'=>'Length (inch)')
		   // ,$this->Setting->select_option_vl(array('setting_value'=>'product_oum_Sq. ft.'))
		   // ,$this->Setting->select_option_vl(array('setting_value'=>'product_oum_area'))
		   // ,$this->Setting->select_option_vl(array('setting_value'=>'product_oum_lengths'))
		   // ,$this->Setting->select_option_vl(array('setting_value'=>'product_oum_size'))
		   // ,$this->Setting->select_option_vl(array('setting_value'=>'product_oum_weight'))
		   // ,$this->Setting->select_option_vl(array('setting_value'=>'product_oum_volume'))
		   // ,$this->Setting->select_option_vl(array('setting_value'=>'product_oum_unit'))
		);
		$this->set('arr_uom',$arr_uom);
		$this->set('arr_equipments', $arr_equipments);

		$this->set('arr_tasks_status', $this->Setting->select_option(array('setting_value' => 'tasks_status'), array('option')));

		$this->Session->write('setup_remember_page', 'equipments');
	}

	public function equipments_auto_save() {
		if (!empty($this->data) || isset($_POST['equipment_id'])) {
			$this->selectModel('Equipment');
			$arr_save = array();
			if( isset($_POST['equipment_id']) ){
				$arr_save = $this->Equipment->select_one(array('_id' => new MongoId($_POST['equipment_id'])));
				if(!isset($arr_save['color']))
					$arr_save['color'] = array();
				$arr_save['color'][$_POST['status']] = $_POST['color'];
			}else{
				$post = $this->data['Equipment'];
				$arr_save['_id'] = $post['_id'];
				$arr_save['name'] = $post['name'];
				$arr_save['uom'] = $post['uom'];
				$arr_save['uom_key'] = $post['uom_id'];
				$arr_save['speed_per_hour'] = $post['speed_per_hour'];
				$arr_save['description'] = $post['description'];
				$arr_save['deleted'] = (isset($post['deleted']) ? true : false);
			}

			if ($this->Equipment->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->Equipment->arr_errors_save[1];
			}
		}
		die;
	}

	public function equipments_add() {
		$arr_save = array();
		$arr_save['name'] = 'undefined';
		$this->selectModel('Equipment');
		if ($this->Equipment->save($arr_save)) {
			$this->equipments();
			$this->render('equipments');
		} else {
			echo 'Error: ' . $this->Equipment->arr_errors_save[1];
		}
	}

	public function salesorders_status_color() {
		$this->selectModel('Setting');
		$this->set('arr_salesorders_status_color', $this->Setting->select_all(array('setting_value' => 'salesorders_status'), array('option')));
	}

	public function salesorders_status_pickcolor($mongo_id, $idx, $color) {
		if (is_numeric($idx)) {
			$error = 0;
			if (!$error) {
				$this->selectModel('Setting');
				if ($this->Setting->update_field_option($mongo_id, $idx, 'color', '#' . $color)) {
					echo 'ok';
				} else {
					echo 'Error: ' . $this->Setting->arr_errors_save[1];
				}
			}
		}
		die;
	}

	function list_country() {
		$this->selectModel('Country');
		$arr_countrys = $this->Country->select_all(array('arr_order' => array('name' => 1)));
		$this->set('arr_countrys', $arr_countrys);

		$this->Session->write('setup_remember_page', 'list_country');
	}

	public function list_province_add_country(){
		$this->selectModel('Country');
		$arr_save['value'] = $this->data['Setting']['country_code'];
		$arr_save['name'] = $this->data['Setting']['country_name'];
		if ($this->Country->save($arr_save)) {
			echo 'ok';
		} else {
			echo 'Error: ' . $this->Company->arr_errors_save[1];
		}
		die;
	}

	public function list_province_detail($country) {
		$this->selectModel('Province');
		$this->selectModel('Country');
		$arr_country = $this->Country->select_one(array('value' => $country));
		$arr_provinces = $this->Province->select_all(array(
			'arr_where' => array('country_id' => $country),
			'arr_order' => array('name' => 1)
		));
		$this->set('arr_country', $arr_country);
		$this->set('arr_provinces', $arr_provinces);
	}

	public function list_province_detail_auto_save() {
		if (!empty($this->data)) {
			$arr_save = $_REQUEST['Setting'];
			$arr_save['deleted'] = $arr_save['deleted'] ? true : false;
			$this->selectModel('Province');
			if ($this->Province->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->Province->arr_errors_save[1];
			}
		}
		die;
	}

	public function list_province_add($id) {
		$this->selectModel('Province');
		$save_data = array(
			'country_id' => $id,
			'name' => 'undefine',
			'key' => '',
			'deleted' => false,
		);
		$this->Province->save($save_data);
		$this->list_province_detail($id);
		$this->render('list_province_detail');
	}

	// Permission

	public function permission_list() {
		$this->selectModel('Permission');
		$permissions = $this->Permission->select_all(array(
			'arr_order' => array('_id' => 1)
		));
		$this->set('permissions', $permissions);
	}

	public function permission_detail($id = 0) {
		$this->selectModel('Permission');
		$permissions = $this->Permission->select_one(array('_id' => new MongoId($id)));
		$this->set('permissions', $permissions);
	}

	public function permission_detail_add($id) {
		$this->selectModel('Permission');
		$permissions = $this->Permission->select_one(array('_id' => new MongoId($id)));

		$col = $_REQUEST['col'];
		$data = $permissions;
		//$data = array('option_lists' => array(array(), array(), array()));
		if (isset($_REQUEST['group']) && $_REQUEST['group'] != '') {
			$group = str_replace(' ', '_', $_REQUEST['group']);
			$group = strtolower($group);
			$data['option_list'][$col][$group] = array();
		}

		$line = array();

		if (isset($permissions['option_list'][$col][$group])) {
			$line = $permissions['option_list'][$col][$group];
		}
		$index = count($line);

		$type = str_replace(' ', '_', $_REQUEST['name']);

		$line[$index]['url'] = $_REQUEST['url'];
		$line[$index]['name'] = $_REQUEST['name'];
		$line[$index]['type'] = $_REQUEST['type'];
		$line[$index]['codekey'] = strtolower($type);
		$line[$index]['flag'] = $_REQUEST['flag'];
		$line[$index]['description'] = $_REQUEST['description'];

		$data['option_list'][$col][$group] = $line;
		//pr($data);
		if ($this->Permission->save($data)) {
			echo 'Success';
		}
		die;
	}
	//Tung doi mau status
	public function sys_list()
	{
		$this->selectModel('Setting');
		$arr_system[] = $this->Setting->select_one(array('setting_value' => 'tasks_status'));

		$this->set('arr_system', $arr_system);
	}
	public function system_detail($id)
	{
		$this->selectModel('Setting');
		$arr_system = $this->Setting->select_one(array('_id'=>new MongoId($id)));
		$this->set('arr_system',$arr_system);

	}
	public function system_detail_auto_save()
	{
		if (!empty($this->data)) {
			$arr_save = $_REQUEST['Setting'];
			$this->selectModel('Setting');
			if ($this->Setting->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->Setting->arr_errors_save[1];
			}
		}
		die;
	}

	public function system_detail_add($id)
	{
		$this->selectModel('Setting');
		$system = $this->Setting->select_one(array('_id'=>new MongoId($id)));
		$option = array(
			'value' => 'undefine',
			'color' => '#fff',
			'deleted' => '0',
			'cal_enabled_move' => '0',
			'show' => '0'

		);
		$system['option'][] = $option;
		$this->Setting->save($system);
		$this->system_detail($id);
		$this->render('system_detail');
	}

	public function add_action(){
		$this->selectModel('Permission');
		$data = array(
			'controller' => 'shippings',
			'option_list' => array()
		);
		if($this->Permission->save($data)){
			echo 'success';
		}
		die();
	}

	// end Permission
	//module_buider v1.0
	function module_buider() {
		// g�?i class cần dùng
		// tạo file Module.php
		// tạo file ModuleField.php
		// tạo file ModulesController.php
		// tạo thư mục Modules trong View/Themed/Default
	}
	public function auto_process(){
		$this->selectModel("AutoProcess");
		$this->AutoProcess->has_field_deleted = false;
		$arr_process = $this->AutoProcess->select_all(array('arr_order'=>array('controller'=>1)));
		$this->selectModel("Emailtemplate");
		$template = $this->Emailtemplate->select_all(array('arr_order'=>array('name'=>1)));
		$arr_template = array();
		if($template->count()>0){
			foreach($template as $value){
				$arr_template[(string)$value['_id']] = $value['name'];
			}
		}
		$arr_module = array('salesorders'=>'Sales Order','quotations'=>'Quotation');
		$arr_name = array(
						  'When status is changed to Completed - email Customer'=>'Email Customer (Completed)',
						  'When status is changed to Completed - email CSR, Rep'=>'Email CSR, Rep (Completed)',
						  'When status is changed to Completed - email Accounting'=>'Email Accounting (Completed)',
						  'When status is changed to Submitted - check Quotation limit & email CSR'=>'Email CSR (Submitted)',
						  'When status is changed to Approved - email Rep'=>'Email Rep (Approved)',
						  'When status is changed to Completed by whom is not our CSR, and payment term equals to zero, restores status to last state and asks if send email to our CSR'=>'Email CSR (payment term)',
						  );
		$this->set('arr_name',$arr_name);
		$this->set('arr_module',$arr_module);
		$this->set('arr_template',$arr_template);
		$this->set('arr_process',$arr_process);

		$this->Session->write('setup_remember_page', 'auto_process');
	}
	function auto_process_add(){
		$this->selectModel("AutoProcess");
		if($this->AutoProcess->add()){
			$this->auto_process();
			$this->render('auto_process');
		}
		else
			echo $this->AutoProcess->arr_errors_save[1];
	}
	function auto_process_remove(){
		if(isset($_POST['id'])&&strlen($_POST['id'])==24){
			$this->selectModel("AutoProcess");
			if($this->AutoProcess->remove(new MongoId($_POST['id'])))
				echo 'ok';
			else
				echo $this->AutoProcess->arr_errors;
		}
		die;
	}
	function auto_process_auto_save(){
		if (!empty($this->data) || isset($this->data['Process']['_id'])) {
			$this->selectModel('AutoProcess');
			$arr_save = array();
			$post = $this->data['Process'];
			$arr_save['_id'] = new MongoId($post['_id']);

			if($_SESSION['arr_user']['contact_name']=='System Admin'){
				$arr_save['name'] = $post['name'];
				$arr_save['module_name'] = $post['module_name'];
				$arr_save['controller'] = $post['controller'];
				$arr_save['email_template'] = $post['email_template'];
				if(isset($post['email_template_id'])&&strlen($post['email_template_id'])==24)
					$arr_save['email_template_id'] = new MongoId($post['email_template_id']);
				$arr_save['description'] = $post['description'];
			}
			if(isset($post['extra_email'])&&$post['extra_email']!=''){
				if(!filter_var($post['extra_email'], FILTER_VALIDATE_EMAIL)){
					echo 'Please enter valid email.';
					die;
				} else
					$arr_save['extra_email'] = $post['extra_email'];
			}
			$arr_save['deleted'] = (isset($post['deleted']) ? true : false);

			if ($this->AutoProcess->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->AutoProcess->arr_errors_save[1];
			}
		}
		die;
	}
	function pricing_rules(){
		$this->selectModel('Setting');
		$arr_pricing_rule = $this->Setting->select_one(array('setting_value' => 'pricing_rules'));
		$this->set('arr_pricing_rule', $arr_pricing_rule);
	}
	function system_email(){
		$this->selectModel('Stuffs');
		if(isset($_POST['data'])){
			$arr_save = $_POST['data'];
			$arr_save['host'] = trim($arr_save['host']);
			if(!filter_var($arr_save['username'], FILTER_VALIDATE_EMAIL)){
				echo 'Email must be valid.';
				die;
			}
			if(trim($arr_save['password'])==''){
				echo 'Password must not be empty';
				die;
			}
			if(!is_numeric($arr_save['port'])){
				echo 'Port must be a numeric value.';
				die;
			}
			// if(!filter_var(gethostbyname($arr_save['host']), FILTER_VALIDATE_IP)){
			// 	echo 'Host must be valid.';
			// 	die;
			// }
			if($this->Stuffs->save($arr_save)){
				echo 'ok';
				die;
			} else {
				echo $this->Stuffs->arr_errors_save[1];
				die;
			}
		}
		$email = $this->Stuffs->select_one(array('value'=>"system_email"));
		$this->set('email',$email);
		$this->Session->write('setup_remember_page', 'system_email');

	}
	function hook_setting(){
		$this->Session->write('setup_remember_page', 'hook_setting');
		$this->selectModel('Hooksetting');
		$hooks = $this->Hooksetting->select_all(array(
		                                 'arr_field' => array('name'),
		                                 ));
		$this->set('hooks',$hooks);
	}
	function hook_detail($hook_id){
		$this->selectModel('Hooksetting');
		$hook = $this->Hooksetting->select_one(array('_id' => new MongoId($hook_id)));
		$this->set('hook',$hook);
	}

	function hook_option_add(){
		if(!empty($_POST)){
			$this->selectModel('Hooksetting');
			$hook = $this->Hooksetting->select_one(array('_id' => new MongoId($_POST['hook_id'])));
			$hook['options'][] = array('area_limit' => 0, 'up_price' => 0);
			$this->Hooksetting->save($hook);
			echo 'ok';
		}
		die;
	}

	function hook_option_save(){
		if(!empty($_POST)){
			$key = $_POST['key'];
			$key = explode('_', $key);
			$key = $key[count($key) - 1];
			$name = str_replace('_'.$key,'',$_POST['key']);
			$this->selectModel('Hooksetting');
			$hook = $this->Hooksetting->select_one(array('_id' => new MongoId($_POST['hook_id'])));
			$hook['options'][$key][$name] = $_POST['value'];
			$this->Hooksetting->save($hook);
			echo 'ok';
		}
		die;
	}

	function hook_option_delete(){
		if(!empty($_POST)){
			$this->selectModel('Hooksetting');
			$hook = $this->Hooksetting->select_one(array('_id' => new MongoId($_POST['hook_id'])));
			if(isset($hook['options'][$_POST['key']]))
				unset($hook['options'][$_POST['key']]);
			$this->Hooksetting->save($hook);
			echo 'ok';
		}
		die;
	}

	function get_name_php_file($folder_name='Controller',$last_part_name='Controller.php'){
		$result = array();
		$dir = scandir(APP.$folder_name);
		foreach($dir as $keys=>$values){
			if(strpos($values,".php")===false)
				continue;
			if(strpos($values,$last_part_name)===false)
				continue;
			$result[] = str_replace($last_part_name, '', $values);
		}
		return $result;
	}


	function get_list_module(){
		$list_controller = glob(APP.'Controller'.DS.'*Controller.php');
		$controller_name = array();
		foreach($list_controller as $value){
			$controller_name[] = str_replace('Controller.php','',str_replace(APP.'Controller'.DS, '', $value));
		}
		$this->selectModel('Studio');
		foreach($controller_name as $v){
			if($v == 'App' || $v == 'Basics') continue;
			$save['module_name'] = $this->ModuleName(strtolower($v));
			$save['deleted'] = false;
			$tmp = $this->Studio->select_one(array('module_name' => $save['module_name']),array('_id'));
			if(!isset($tmp['_id'])){
				echo $save['module_name']."<br/>";
				if(!$this->Studio->save($save)){
					echo 'error save: '.$save['module_name'];
				}
			}
		}
		echo '=====xong====';die;
	}

	function list_module(){
		$this->selectModel('Studio');
		$list_module = $this->Studio->select_all();
		foreach($list_module as $key =>  $value){
			if($value['module_name'] == 'Calendar' || $value['module_name'] == 'Communication' || $value['module_name'] == 'Doc' || $value['module_name'] == 'Emailtemplate' || $value['module_name'] == 'Enquiry' || $value['module_name'] == 'Equipment' || $value['module_name'] == 'Home' || $value['module_name'] == 'Job' || $value['module_name'] == 'Salesaccount' || $value['module_name'] == 'User' || $value['module_name'] == 'Test' || $value['module_name'] == 'Task') continue;
			$arr_tmp[] = isset($value['module_name'])?$value['module_name']:'';
		}
		$this->set('list_module',$arr_tmp);
	}

	function studio($module = ''){
		//List module
		$this->selectModel('Studio');
		$list_module = $this->Studio->select_all();
		$arr_not_show = array('Calendar','Communication','Doc','Emailtemplate','Enquiry','Equipment','Home','Job','Salesaccount','User','Task','Setup','Setting','Tax','Permission','Batche','Log','Stage','Resource','Receipt','Timelog');
		$arr_tmp = array();
		foreach($list_module as $key =>  $value){
			if(in_array($value['module_name'],$arr_not_show)) continue;
			$arr_tmp[] = isset($value['module_name'])?$value['module_name']:'';
		}
		$this->set('list_module',$arr_tmp);
	}

	function studio_detail($module_name){
		$this->selectModel('Studio');
		$module_name = $this->ModuleName(strtolower($module_name));
		$module_field = $module_name. 'Field';
		$custom_model = APP.'Model'.DS.'Custom'.DS.$module_field.'.php';
		$origin_model = APP.'Model'.DS.$module_field.'.php';
		if(file_exists($custom_model))
			require $custom_model;
		else
			require $origin_model;
		$arr_tmp = $$module_field;
		$arr_fields = array();
		$arr_layout = array('group_extra'=> array());
		foreach($arr_tmp['field'] as $panel => $value){
			foreach( $value as $field => $arr_config){
				if(isset($arr_config['name']))
					$name = $arr_config['name'];
				else
					$name = $field;
				$name = preg_replace('/<span[^>]+\>/i','',$name);
				$arr_fields[$field]['name'] = $name;
				$group = 'group_2';
				if($panel == 'panel_1')
					$group = 'group_1';
				else if($panel == 'panel_extra')
					$group = 'group_extra';
				$arr_layout[$group][$panel][$field] = $arr_fields[$field];
				$arr_layout[$group][$panel][$field]['config'] = $arr_config;
			}
		}
		if(isset($arr_layout['group_extra']['panel_extra'] )){
			foreach($arr_layout['group_extra']['panel_extra'] as $extra_field => $extra_value ){
				foreach($arr_tmp['field'][$extra_value['config']['belong_to']] as $field => $value){
					// if(!isset($arr_config['belong_to']) || $arr_config['belong_to'] != 'panel_extra') continue;
					if($field != $extra_field) continue;
					unset($arr_layout['group_extra']['panel_extra'][$extra_field]);
				}
			}
		}
		$this->set('module_name',$module_name);
		$this->set('arr_fields',$arr_fields);
		$this->set('arr_layout',$arr_layout);
		$field_type['text'] = array('key','name','width','css');
		$field_type['select'] = array('key','droplist','name','width','css');
		$field_type['checkbox'] = array('key','name','default','css');
		$field_type['relationship'] = array('key','name','cls','id');
		$this->set('field_type',json_encode($field_type));
	}

	function getCustomModel($module_name){
		$module_field = $module_name. 'Field';
		$custom_model = APP.'Model'.DS.'Custom'.DS.$module_field.'.php';
		$origin_model = APP.'Model'.DS.$module_field.'.php';
		if( !file_exists($custom_model) ){
			copy($origin_model, $custom_model);
		}
		require $custom_model;
		return $$module_field;
	}

	function studio_view_subtab($module_name){
		$this->selectModel('Studio');
		$module_name = $this->ModuleName(strtolower($module_name));
		$module_field = $module_name. 'Field';
		$custom_model = APP.'Model'.DS.'Custom'.DS.$module_field.'.php';
		$origin_model = APP.'Model'.DS.$module_field.'.php';
		if(file_exists($custom_model))
			require $custom_model;
		else
			require $origin_model;
		$arr_tmp = $$module_field;
		if(!isset($arr_tmp['relationship']))
			$arr_tmp['relationship'] = array();
		$arr_relationship = $arr_tmp['relationship'];
		$this->set('arr_relationship',$arr_relationship);
		$this->set('module_name',$module_name);
	}

	function studio_change_relationship_name($module_name){
		$custom_model = APP.'Model'.DS.'Custom'.DS.$module_name.'Field.php';
		$arr_module = $this->getCustomModel($module_name);
		$key = $_POST['key'];
		$arr_module['relationship'][$key]['name'] = $_POST['name'];
		$fopen = fopen($custom_model,"w");
		$string = "<?php\r\n\t\$ModuleField = array();\r\n";
		$string .= $this->array_to_PHP_string($arr_module);
		$string .= "\t\${$arr_module['module_name']}Field = \$ModuleField;\r\n?>";
		fwrite($fopen, $string);
		fclose($fopen);
		echo 'ok';die;
	}


	function studio_get_block_info($module_name){
		$custom_model = APP.'Model'.DS.'Custom'.DS.$module_name.'Field.php';
		$arr_module = $this->getCustomModel($module_name);
		$key = $_POST['key'];
		$key_field = $_POST['key_field'];
		$arr_return = $arr_module['relationship'][$key]['block'][$key_field];
		unset($arr_return['field']);
		echo json_encode($arr_return);
		die;
	}

	function studio_save_block_info($module_name){
		$custom_model = APP.'Model'.DS.'Custom'.DS.$module_name.'Field.php';
		$arr_module = $this->getCustomModel($module_name);
		$key = $_POST['key'];
		$key_field = $_POST['key_field'];
		$arr_post = $_POST['data'];
		foreach($arr_post as $k => $value){
			$arr_module['relationship'][$key]['block'][$key_field][$k] = $value;
		}
		$fopen = fopen($custom_model,"w");
		$string = "<?php\r\n\t\$ModuleField = array();\r\n";
		$string .= $this->array_to_PHP_string($arr_module);
		$string .= "\t\${$arr_module['module_name']}Field = \$ModuleField;\r\n?>";
		fwrite($fopen, $string);
		fclose($fopen);
		echo 'ok';die;
	}

	function studio_get_field_info($module_name){
		$custom_model = APP.'Model'.DS.'Custom'.DS.$module_name.'Field.php';
		$arr_module = $this->getCustomModel($module_name);
		$arr_return = array();
		foreach($_POST['data'] as $key => $value){
			foreach($value as $key_field => $v){
				$arr_return = $arr_module['relationship'][$key]['block'][$key_field]['field'][key($v)];
			}
		}
		echo json_encode($arr_return);
		die;
	}

	function studio_save_field_info($module_name){
		$custom_model = APP.'Model'.DS.'Custom'.DS.$module_name.'Field.php';
		$arr_module = $this->getCustomModel($module_name);
		$arr_data = array();
		foreach($_POST['field'] as $key => $value){
			foreach($value as $key_field => $v){
				$arr_data = $arr_module['relationship'][$key]['block'][$key_field]['field'][key($v)];
				foreach($_POST['data'] as $k => $value){
					$arr_data[$k] = $value;
				}
				$arr_module['relationship'][$key]['block'][$key_field]['field'][key($v)] = $arr_data;
				$fopen = fopen($custom_model,"w");
				$string = "<?php\r\n\t\$ModuleField = array();\r\n";
				$string .= $this->array_to_PHP_string($arr_module);
				$string .= "\t\${$arr_module['module_name']}Field = \$ModuleField;\r\n?>";
				fwrite($fopen, $string);
				fclose($fopen);
				break;
			}
		}
		echo 'ok';die;
	}

	function studio_save_tab_layout($module_name){
		$custom_model = APP.'Model'.DS.'Custom'.DS.$module_name.'Field.php';
		$arr_module = $this->getCustomModel($module_name);
		$arr_data = array();
		foreach($_POST['data'] as $key_data => $data){
			$arr_data['relationship'][$key_data] = $arr_module['relationship'][$key_data];
			unset($arr_data['relationship'][$key_data]['block']);
			foreach($data as $key_field => $value_field){
				$arr_data['relationship'][$key_data]['block'][$key_field] = $arr_module['relationship'][$key_data]['block'][$key_field];
				unset($arr_data['relationship'][$key_data]['block'][$key_field]['field']);
				foreach($value_field as $key => $value){
					$arr_data['relationship'][$key_data]['block'][$key_field]['field'][$key] = $arr_module['relationship'][$key_data]['block'][$key_field]['field'][$key];
				}
			}
		}
		$arr_module['relationship'][$key_data] = $arr_data['relationship'][$key_data];
		$fopen = fopen($custom_model,"w");
		$string = "<?php\r\n\t\$ModuleField = array();\r\n";
		$string .= $this->array_to_PHP_string($arr_module);
		$string .= "\t\${$arr_module['module_name']}Field = \$ModuleField;\r\n?>";
		fwrite($fopen, $string);
		fclose($fopen);
		echo 'ok';die;
	}


	function save_layout($module_name){
		$this->selectModel('Studio');
		$module_name = $this->ModuleName(strtolower($module_name));
		$module_field = $module_name. 'Field';
		$custom_model = APP.'Model'.DS.'Custom'.DS.$module_field.'.php';
		$origin_model = APP.'Model'.DS.$module_field.'.php';
		require $origin_model;
		$arr_origin = $$module_field;
		$arr_origin_field = $arr_origin['field'];
		//
		require $custom_model;
		$arr_custom = $$module_field;
		$arr_custom_field = $arr_custom['field'];
		$arr_fields = array();
		$arr_post = $_POST['data'];
		foreach($arr_origin_field as $panel=>$arr_data){
			if($panel == 'panel_5') continue;
			foreach($arr_data as $key=>$value){
				if($key == 'setup')
					$arr_fields[$panel][$key] = $value;
				else
					$arr_fields[$key] = $value;
			}
		}
		$arr_new_fields = array();
		foreach($arr_post as $panel_name => $arr_data){
			foreach($arr_data as $field=>$value){
				if($field == 'setup'){
					if($panel_name == 'panel_5')
						$arr_new_fields[$panel_name][$field] = $arr_origin_field[$panel_name][$field];
					else
						$arr_new_fields[$panel_name][$field] = $arr_fields[$panel_name][$field];
					continue;
				}
				if(isset($arr_fields[$field]['moreclass']))
					unset($arr_fields[$field]['moreclass']);
				if($panel_name == 'panel_5')
					$arr_new_fields['panel_5'][$field] = $arr_origin_field['panel_5'][$field];
				else if( isset($value['belong_to']) ){
					$arr_new_fields[$panel_name][$field] = $arr_custom_field[$value['belong_to']][$field];
					$arr_new_fields[$panel_name][$field]['belong_to'] = $value['belong_to'];
				}
				else
					$arr_new_fields[$panel_name][$field] = $arr_fields[$field];
			}
		}
		foreach($arr_new_fields as $panel_name=>$panel){
			$i = 0;
			if($panel_name == 'panel_5') continue;
			if(isset($panel['setup']['blocktype']) && $panel['setup']['blocktype'] == 'address') continue;
			$last_field = '';
			foreach($panel as $field=>$value){
				if($field=='setup') continue;
				if($i && !in_array($value['type'],array('id'))){
					$last_field = $field;
					continue;
				} else if($i)
					continue;
				if(!isset($value['type']) || in_array($value['type'],array('id','hidden'))) continue;
				$arr_new_fields[$panel_name][$field]['moreclass'] = 'fixbor';
				$i++;
			}
			if($panel == 'panel_4')
				$arr_new_fields[$panel_name][$last_field]['moreclass'] = 'fixbor3';
			else if( count($arr_new_fields[$panel_name]) > 1)
				$arr_new_fields[$panel_name][$last_field]['moreclass'] = 'fixbor2';
		}
		$arr_new_fields['panel_extra'] = isset($arr_custom_field['panel_extra']) ? $arr_custom_field['panel_extra'] : array();
		$arr_origin['field'] = $arr_new_fields;
		$fopen = fopen($custom_model,"w");
		$string = "<?php\r\n\t\$ModuleField = array();\r\n";
		$string .= $this->array_to_PHP_string($arr_origin);
		$string .= "\t\${$arr_origin['module_name']}Field = \$ModuleField;\r\n?>";
		fwrite($fopen, $string);
		fclose($fopen);
		echo 'ok';die;
	}


	function array_to_PHP_string($array,$string = '',$level = 1){
		$tab = '';
		for($i = 0; $i < $level; $i++)
			$tab .= "\t";
		foreach($array as $key => $value){
			if(is_array($value)){
				if($level == 1)
					$string .= $tab."\$ModuleField['{$key}']\t\t= array(\r\n";
				else
					$string .= $tab."'{$key}'\t\t=> array(\r\n";
				$string .= $this->array_to_PHP_string($value,'',$level+1);
				if($level == 1)
					$string .= $tab.");\r\n";
				else
					$string .= $tab."),\r\n";
			} else {
				$value = str_replace("'","\'",$value);
				if($key != 'title_field'&& in_array($key, array('name','title','add')))
					$value = "__('{$value}')";
				else{
					if(!is_numeric($value))
						$value = "'{$value}'";
					else
						$value = $value;
				}
				if($level == 1)
					$string .= $tab."\$ModuleField['{$key}']\t\t= {$value};\r\n";
				else{

					if(!is_numeric($key))
						$string .= $tab."'{$key}'\t\t=> {$value},\r\n";
					else
						$string .= $tab."{$value},\r\n";
				}
			}
		}
		return $string;
	}

	function get_field_info($module_name){
		$module_field = $module_name. 'Field';
		$custom_model = APP.'Model'.DS.'Custom'.DS.$module_field.'.php';
		$origin_model = APP.'Model'.DS.$module_field.'.php';
		if(file_exists($custom_model))
			require $custom_model;
		else
			require $origin_model;
		$module = $$module_field;
		$arr_module_field = $module['field'];
		foreach($_POST['data'] as $field => $data){
			foreach ($data as $key => $value) {
				$type = $arr_module_field[$field][$key]['type'];
				if( !in_array($type, array('text','select','checkbox','relationship'))
				   /*|| isset($arr_module_field[$field][$key]['listview'])*/ ){
					echo 'You can not edit this type.';
					die;
				}
				$arr_module_field[$field][$key]['key'] = $key;
				$arr_return = $arr_module_field[$field][$key];
				foreach($arr_return as $key => $value){
					if(is_array($value))
						$arr_return[$key] = json_encode($value);
				}
				echo json_encode($arr_return);die;
			}
		}
	}

	function add_new_field($module_name){
		if(empty($_POST))
			die;
		$custom_model = APP.'Model'.DS.'Custom'.DS.$module_name.'Field.php';
		$arr_module = $this->getCustomModel($module_name);
		$arr_module_field = $arr_module['field'];
		$key = strtolower($_POST['key']);
		if(isset($_POST['old_key'])){
			$old_key = strtolower($_POST['old_key']);
			if( isset($arr_module_field['panel_extra'][$old_key]) )
				unset($arr_module_field['panel_extra'][$old_key]);
		}
		$arr_module_field['panel_extra'][$key] = array(
		                                                    'name' => $_POST['name'],
		                                                    'type' => strtolower($_POST['type']),
		                                                    'width' => (float)$_POST['width'].'%',
		                                                    'css' 	=> $_POST['css'],
		                                                    'belong_to'=> 'panel_extra',
		                                                    );
		if( $_POST['type'] == 'select' )
			$arr_module_field['panel_extra'][$key]['droplist'] = $_POST['droplist'];
		$arr_module['field'] = $arr_module_field;
		$fopen = fopen($custom_model,"w");
		$string = "<?php\r\n\t\$ModuleField = array();\r\n";
		$string .= $this->array_to_PHP_string($arr_module);
		$string .= "\t\${$arr_module['module_name']}Field = \$ModuleField;\r\n?>";
		fwrite($fopen, $string);
		fclose($fopen);
		echo 'ok';die;
	}

	function delete_new_field($module_name){
		if(empty($_POST))
			die;
		$module_field = $module_name. 'Field';
		$custom_model = APP.'Model'.DS.'Custom'.DS.$module_field.'.php';
		require $custom_model;
		$arr_module = $$module_field;
		$arr_module_field = $arr_module['field'];
		foreach ($_POST['data']['panel_extra'] as $key => $value) {
			foreach ($arr_module_field as $field => $value) {
				if(isset($arr_module_field[$field][$key])){
					if(isset( $arr_module_field[$field][$key]['extra_field'] )
					   || $field == 'panel_extra')
						unset($arr_module_field[$field][$key]);
				}
			}
		}
		$arr_module['field'] = $arr_module_field;
		$fopen = fopen($custom_model,"w");
		$string = "<?php\r\n\t\$ModuleField = array();\r\n";
		$string .= $this->array_to_PHP_string($arr_module);
		$string .= "\t\${$arr_module['module_name']}Field = \$ModuleField;\r\n?>";
		fwrite($fopen, $string);
		fclose($fopen);
		echo 'ok';die;
	}

	function apend_extra_field($module_name){
		$custom_model = APP.'Model'.DS.'Custom'.DS.$module_name.'Field.php';
		$arr_module = $this->getCustomModel($module_name);
		$arr_module_field = $arr_module['field'];
		foreach($_POST['data']['panel_extra'] as $field => $value){
			$arr_module_field['panel_extra'][$field] = $arr_module_field[$value['belong_to']][$field];
			$arr_module_field['panel_extra'][$field]['belong_to'] = $value['belong_to'];
			unset($arr_module_field[$value['belong_to']][$field]);
			$arr_module['field'] = $arr_module_field;
			$fopen = fopen($custom_model,"w");
			$string = "<?php\r\n\t\$ModuleField = array();\r\n";
			$string .= $this->array_to_PHP_string($arr_module);
			$string .= "\t\${$arr_module['module_name']}Field = \$ModuleField;\r\n?>";
			fwrite($fopen, $string);
			fclose($fopen);
			echo 'ok';die;
		}
		die;
	}

	function auto_create_module(){
		if(isset($_POST['ok'])){
			if($_POST['module_name'] == NULL || $_POST['controller_name'] == NULL || $_POST['model_name'] == NULL){
				echo 'Please enter all valid infomation.';die;
		}else{
			$tmp = $this->get_name_php_file();
			if(in_array($_POST['controller_name'],$tmp)){echo 'Name controller exited!';die;}else{
				$controller_dir = APP.'Controller';
				$model_dir = APP.'Model';
				$view_dir = APP.'View'.DS.'Themed'.DS.'Default'.DS. ucfirst($_POST['controller_name']);

				//BasicField
				$file = $model_dir . DS.ucfirst($_POST['model_name']).'Field'. '.php';
				$source = $model_dir . DS . 'BasicField.php';
				copy($source, $file);
				$t = file_get_contents($file);
				$fopen = fopen($file,"w+");
				$t = str_replace("__('Basic'),", "__('".$_POST['model_name']."'),",$t);
				$t = str_replace("'module_label' 	=> __('Basics'),", "'module_label' 	=> __('".$_POST['controller_name']."'),",$t);
				$t = str_replace("'colection' 	=> 'tb_basic',", "'colection' 	=> 'tb_".strtolower($_POST['model_name'])."',",$t);
				$t = str_replace("BasicField", $_POST['model_name'].'Field',$t);
				fwrite($fopen, $t);
				fclose($fopen);

				//Basic
				$file = $model_dir . DS .ucfirst($_POST['model_name']).'.php';
				$source_basic = $model_dir . DS . 'Basic.php';
				copy($source_basic, $file);
				$t = file_get_contents($file);
				$fopen = fopen($file,"w+");
				$t = str_replace("class Basic", "class ".$_POST['model_name'],$t);
				$t = str_replace("'module_name' => 'Basic'", "'module_name' => '".$_POST['model_name']."'",$t);
				fwrite($fopen, $t);
				fclose($fopen);

				//BasicsController
				$file = $controller_dir . DIRECTORY_SEPARATOR . ucfirst($_POST['controller_name']). 'Controller.php';
				$source_controller = $controller_dir . DS . 'BasicsController.php';
				copy($source_controller,$file);
				$t = file_get_contents($file);
				$fopen = fopen($file,"w+");
				$t = str_replace("class Basics", "class ".$_POST['controller_name'],$t);
				$t = str_replace("var \$name = 'Basics';", "var \$name = '".$_POST['controller_name']."';",$t);
				$t = str_replace("\$this->set_module_before_filter('Basic');", "\$this->set_module_before_filter('".$_POST['model_name']."');",$t);
				fwrite($fopen, $t);
				fclose($fopen);

				//View
				mkdir($view_dir);
				$old_folder_path = APP.'View'.DS.'Themed'.DS.'Default'.DS.'Basics';
				$new_folder_path = $view_dir;
				$files = scandir($old_folder_path);

				foreach($files as $file)
					if ($file != "." && $file != "..")
						copy($old_folder_path.DS.$file,$new_folder_path.DS.$file);
				}
				if($this->request->is('ajax')){
					echo 'ok';
					die;
				}
			}
		}
		$this->Session->write('setup_remember_page', 'auto_create_module');
	}

	function language(){
		$this->selectModel('Language');
		$arr_language = $this->Language->select_all(array('arr_order' => array('name' => 1)));
		$this->set('arr_language', $arr_language);
	}

	public function list_laguage(){
		$this->selectModel('Language');
		$arr_tmp = array();
		$arr_language = $this->Language->select_all(array('arr_order' => array('name' => 1)));
		foreach($arr_language as $value){
			$arr_tmp[] = $value['value'];
		}
		if($this->data['Setting']['language_code'] == '' || $this->data['Setting']['language_name'] == ''){
			$this->set('error', '121');
			echo "All input must is not empty";
			die;
		}
		if(in_array($this->data['Setting']['language_code'], $arr_tmp)){
			echo 'This language is exited';die;
		}
		$arr_save['value'] =strtolower($this->data['Setting']['language_code']);
		$arr_save['lang'] = $this->data['Setting']['language_name'];
		if ($this->Language->save($arr_save)) {
			echo 'ok';
		} else {
			echo 'Error: ' . $this->Language->arr_errors_save[1];
		}
		die;
	}

	public function language_detail($language, $strsearch=''){
		$this->selectModel('Language');
		$this->selectModel('Languagedetail');
		$arr_language = $this->Language->select_one(array('value' => $language));
		$arr_where = array();
		//set dk
		if($strsearch != '')
		$arr_where['$or'] = array(
				array('content.'.$language =>  new MongoRegex('/'. trim($strsearch).'/i')),
				array('key'=>new MongoRegex('/'. strtoupper(trim($strsearch)).'/i'))
			);

		$arr_language_detail = $this->Languagedetail->select_all(array(
			'arr_where' => $arr_where
			/*'arr_order' => array('created_by' => 1),*/
		));
		$this->set('arr_language', $arr_language);
		$this->set('arr_language_detail',$arr_language_detail);
	}

	public function language_add($id){
		$this->selectModel('Languagedetail');
		$save_data = array(
			'key' => '',
			'content' => array('en' => 'empty'),
			'deleted' => False,
		);
		$save_data['content'][$id] = '';
		$this->Languagedetail->save($save_data);
		$this->language_detail($id);
		$this->render('language_detail');
	}
	public function language_auto_save() {
		if (!empty($this->data)) {
			$arr_save = $_REQUEST['Setting'];
			$arr_save['_id'] = new MongoId($arr_save['_id']);
			$this->selectModel('Languagedetail');
			//kiem tra trung key

			if(isset($arr_save['key']) && $arr_save['key'] != '' ){
				$arr_save['key'] = trim($arr_save['key']);
				$arr_language = $this->Languagedetail->count(array('key' => $arr_save['key']));
				if($arr_language > 0 && isset($_REQUEST['name_change'])){
					echo 'This key is existed ';die;
				}
			}

			if ($this->Languagedetail->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->Languagedetail->arr_errors_save[1];
			}
		}
		die;
	}


	function create_tab_option(){
		$filename = array('entry_tab_option','tab_option');
		$list_tab_option = glob(APP.'View'.DS.'Themed'.DS.'Default'.DS.'Elements'.DS.'*tab_option.ctp');
		$list_menu = glob(APP.'View'.DS.'Themed'.DS.'Default'.DS.'Elements'.DS.'entry_menu.ctp');
		$address = glob(APP.'View'.DS.'Themed'.DS.'Default'.DS.'Elements'.DS.'box_type'.DS.'address.ctp');
		$tmp = array_merge($list_tab_option,$list_menu);
		$t = array_merge($address,$tmp);
		foreach($t as $file){
			$handle = fopen($file, "r");
			$content = fread($handle, filesize($file));
			fclose($handle);

			preg_match_all('/translate\(\'(.*?)\'\)/', $content, $result);
			//pr($result);die;
			if(!empty($result[1])){
				$this->selectModel('Languagedetail');
				foreach($result[1] as $value){
					$save['key'] = 'JT_MENU_'.str_replace('__', '_', str_replace(' ','_',strtoupper(preg_replace("/[^A-Za-z0-9\-]/", ' ', $value))));
					$save['content'][DEFAULT_LANG] = str_replace('\'', ' ', $value);
					$tmp = $this->Languagedetail->select_one(array('key' => $save['key']),array('_id'));
					if(!isset($tmp['_id'])){
						echo $save['key']."<br />";
						if(!$this->Languagedetail->save($save))
							echo 'error save: '.$save['key'];
					}
				}
			}
		}

			echo 'xong';die;
	}

	// BaoNam: 20 02 2014
	function create_auto_key($module = 'Company'){
		foreach (glob(APP.'View'.DS.'Themed'.DS.'Default'.DS.Inflector::pluralize($module).DS.'*.ctp') as $filename) {
			// $content = file_get_contents($filename);
			$handle = fopen($filename, "r");
			$content = fread($handle, filesize($filename));
			fclose($handle);

			if(preg_match_all('/translate\((.*?)\)/', $content, $result)) {
				// ------------ SAVE ----------------
				//An, 26/2/2014 Sua theo dung dinh dang
				$tmp = explode("\\",$filename);
				$t =  str_replace(".ctp","",end($tmp));
				$this->selectModel('Languagedetail');
				foreach ($result[1] as $value) {
					$save['key'] = 'JT_'.strtoupper($module).'_'.strtoupper($t).'_'.str_replace('__', '_', str_replace(' ', '_', strtoupper(preg_replace("/[^A-Za-z0-9\-]/", '', $value))));
					$save['content'][DEFAULT_LANG] = str_replace('\'', '', $value);
					$tmp = $this->Languagedetail->select_one(array('key' => $save['key']), array('_id'));
					if(!isset($tmp['_id'])){
						echo $save['key']."<br/>";
						if(!$this->Languagedetail->save($save))
							echo 'error save: '.$save['key'];
					}

				}
				 //pr($result);
			}
		}
		echo 'xong';
		die;
	}

	public function create_auto_key_lang($module_name){
		$this->selectModel('Languagedetail');
		$arr_language = $this->Languagedetail->select_all(array(
				'arr_where' => array('key' => array('$ne'=>'')),
				));
		$arr_key =array();
		if(!empty($arr_language)){
			foreach($arr_language as $keys => $value){
				$arr_key[] = $value['key'];
			}
		}


		$module_field = $module_name.'Field';
		require_once APP.'Model'.DS.$module_field.'.php';
		$arr_tmp =  $$module_field;
		$key_lang =  KEY_LANG.strtoupper($module_name);

		if(!in_array($key_lang,$arr_key) && isset($arr_tmp['module_label'])){
			$new_data['key'] = $key_lang;
			$new_data['content'][DEFAULT_LANG] = $arr_tmp['module_label'];
			$this->Languagedetail->save($new_data);
		}

		// tu dong tao name field cho modules
		foreach($arr_tmp['field'] as $keys => $value){
			foreach($value as $k => $v){
				if(isset($v['name'])){
					$key_name = $key_lang.'_'.strtoupper($k);
					if(!in_array($key_name,$arr_key)){
						echo $key_name."<br/>";
						//tao language moi
						$new_data['key'] = $key_name;
						$new_data['content'][DEFAULT_LANG] = $v['name'];
						$this->Languagedetail->save($new_data);
					}

				}
			}
		}

		//tu dong tao name file cho relationship
		$rel = $arr_tmp['relationship'];
		$key_lang.='_REL';
		foreach($rel as $k => $v){
			$key_rel = $key_lang.'_'.strtoupper($k);
			if(!in_array($key_rel, $arr_key)){
				echo $key_rel."<br/>";
				//tao language moi
				$new_data['key'] = $key_rel;
				$new_data['content'][DEFAULT_LANG] = $v['name'];
				$this->Languagedetail->save($new_data);
			}
			if(isset($rel[$k]['block'])){
				foreach($rel[$k]['block'] as $k_block => $v_block){
					$key_block = $key_lang.'_'.strtoupper($k).'_'.strtoupper($k_block);
					if(!in_array($key_block, $arr_key)){
						echo $key_block."<br/>";
						//tao language moi
						$new_data['key'] = $key_block;
						$new_data['content'][DEFAULT_LANG] = isset($v_block['title'])?$v_block['title']:'';
						$this->Languagedetail->save($new_data);
					}
					if(isset($v_block['field'])){
						foreach($v_block['field'] as $k_field => $v_field){
							if(isset($v_field['name'])){
								$key_name = $key_block.'_'.strtoupper($k_field);
								if(!in_array($key_name,$arr_key)){
									echo $key_name."<br/>";
									//tao language moi
									$new_data['key'] = $key_name;
									$new_data['content'][DEFAULT_LANG] = $v_field['name'];
									$this->Languagedetail->save($new_data);
								}
							}
						}
					}
					else{echo '';}
				}
			}else{echo '';}

		}
		echo 'xong';
		die;
	}

/*	public function create_auto_key_lang_settings(){
		//Lay thong tin tu bang setting
		$this->selectModel('Languagedetail');
		$arr_language = $this->Languagedetail->select_all(array(
				'arr_where' => array('key' => array('$ne'=>'')),
				));
		$arr_key =array();
		if(!empty($arr_language)){
			foreach($arr_language as $keys => $value){
				$arr_key[] = $value['key'];
			}
		}
		$this->selectModel('Setting');
		$key_lang =  KEY_LANG.'SETTING';
		$arr_setting = $this->Setting->select_all();
		foreach($arr_setting as $key => $value){
			foreach($value['option'] as $k => $v){
				if(isset($v['name']) && is_string($v['name']) && isset($value['setting_value'])){
					$key_name = $key_lang.'_'.strtoupper($value['setting_value']).'_'.strtoupper($v['value']);
					if(!in_array($key_name,$arr_key)){
						echo $key_name."<br/>";
						//tao language moi
						$new_data['key'] = $key_name;
						$new_data['content'][DEFAULT_LANG] = $v['name'];
						$this->Languagedetail->save($new_data);
					}
				}
			}
		}
		die;
	}*/

	public function create_auto_key_lang_settings_message(){

	}
	public function support(){
		$this->selectModel('Permission');
		$arr_modules = $this->Permission->select_all(array('arr_order' => array('controller' => 1)));
		$this->set('arr_modules', $arr_modules);

		$this->Session->write('setup_remember_page', 'support');
	}
	public function support_detail($name){
		$name = ucfirst(strtolower(str_replace(' ', '', $name)));
		$this->selectModel('Support');
		$this->Support->has_field_deleted = false;
		$supports = $this->Support->select_all(array(
		                                       'arr_where'=>array('module'=>$name),
		                                       'arr_order'=>array('name'=>1),
		                                       'arr_field'=>array('name','deleted')
		                                       ));
		$this->set('name',$name);
		$this->set('supports',$supports);
	}
	public function get_content_support(){
		if(isset($_POST['_id'])){
			$this->selectModel('Support');
			$support = $this->Support->select_one(array('_id'=>new MongoId($_POST['_id'])));
			echo $support['content'];
		}
		die;
	}
	public function support_add($name){
		$name = ucfirst(strtolower(str_replace(' ', '', $name)));
		$this->selectModel('Support');
		$arr_save['module'] = $name;
		$this->Support->add($arr_save);
		$this->support_detail($name);
		$this->render('support_detail');
	}
	public function support_auto_save(){
		if(isset($_POST)){
			$arr_save = $_POST;
			if(isset($arr_save['deleted']))
				$arr_save['deleted'] = ($arr_save['deleted']==1 ? true: false);
			$arr_save['_id'] = new MongoId($arr_save['_id']);
			$this->selectModel('Support');
			if($this->Support->save($arr_save))
				echo 'ok';
			else
				echo $this->Support->arr_errors_save[1];
		}
		die;
	}
	public function general(){
		$this->selectModel('Stuffs');
		$accountant = $this->Stuffs->select_one(array('value'=>"Accountant"));
		$product = $this->Stuffs->select_one(array('value'=>"Minimum Order Adjustment"));
		$current_default= $this->Stuffs->select_one(array('key'=>"currency_list"));
		$changing_code = $this->Stuffs->select_one(array('value'=>"Changing Code"));
		$logo = $this->Stuffs->select_one(array('value'=>"Logo"));
		$format_currency = $this->Stuffs->select_one(array('value'=>"Format Currency"));
		$format_date = $this->Stuffs->select_one(array('value'=>"Format Date"));
		$default_search = $this->Stuffs->select_one(array('value'=>"Default Prefer Customer Search"));
		$open_account = $this->Stuffs->select_one(array('value'=>"Open Account"));
		$sale_manager = $this->Stuffs->select_one(array('value'=>"Sale Manager"));
		$production_time = $this->Stuffs->select_one(array('value'=> 'Production Time'));
		$this->set('accountant', $accountant);
		$this->set('product', $product);
		$this->set('changing_code', $changing_code);
		$this->set('logo', $logo);
		$this->set('format_currency', $format_currency);
		$this->set('format_date', $format_date);
		$this->set('default_search', $default_search);
		$format_date_list = array(
		                          'd M, Y' => date('d M, Y'),
		                          'd/m/Y' => date('d/m/y'),
		                          'd-m-Y' => date('d-m-y'),
		                          );
		$this->selectModel('Setting');
        $current_list = $this->Setting->select_option_vl(array('setting_value'=> 'currency_type'));
		$this->set('current_list',$current_list);
		$this->set('current_default',$current_default);
		$this->set('format_date_list',json_encode($format_date_list));
		$this->set('open_account', $open_account);
		$this->set('sale_manager', $sale_manager);
		$this->set('production_time', $production_time);
		$this->Session->write('setup_remember_page', 'general');
	}
	function save_accountant(){
		if(isset($_POST)&&isset($_POST['_id'])){
			$this->selectModel('Stuffs');
			$arr_save = $_POST;
			$arr_save['value'] = 'Accountant';
			$arr_save['accountant_id'] = new MongoId($arr_save['accountant_id']);
			if(strlen($arr_save['_id'])==24 )
				$arr_save['_id'] = new MongoId($arr_save['_id']);
			else
				unset($arr_save['_id']);
			if($this->Stuffs->save($arr_save))
				echo 'ok';
			else
				echo $this->Stuffs->arr_errors_save[1];
		}
		die;
	}
	function save_production_time(){
		if(isset($_POST)&&isset($_POST['_id'])){
			$this->selectModel('Stuffs');
			$arr_save = $_POST;
			$arr_save['value'] = 'Production Time';
			$arr_save['day'] = (int)$arr_save['day'];
			if(strlen($arr_save['_id'])==24 )
				$arr_save['_id'] = new MongoId($arr_save['_id']);
			else
				unset($arr_save['_id']);
			if($this->Stuffs->save($arr_save))
				echo 'ok';
			else
				echo $this->Stuffs->arr_errors_save[1];
		}
		die;
	}
	function save_open_account(){
		if(isset($_POST)&&isset($_POST['_id'])){
			$this->selectModel('Stuffs');
			$arr_save = $_POST;
			$arr_save['value'] = 'Open Account';
			$arr_save['open_account_id'] = new MongoId($arr_save['open_account_id']);
			if(strlen($arr_save['_id'])==24 )
				$arr_save['_id'] = new MongoId($arr_save['_id']);
			else
				unset($arr_save['_id']);
			if($this->Stuffs->save($arr_save))
				echo 'ok';
			else
				echo $this->Stuffs->arr_errors_save[1];
		}
		die;
	}
	function save_sale_manager(){
		if(isset($_POST)&&isset($_POST['_id'])){
			$this->selectModel('Stuffs');
			$arr_save = $_POST;
			$arr_save['value'] = 'Sale Manager';
			$arr_save['sale_manager_id'] = new MongoId($arr_save['sale_manager_id']);
			if(strlen($arr_save['_id'])==24 )
				$arr_save['_id'] = new MongoId($arr_save['_id']);
			else
				unset($arr_save['_id']);
			if($this->Stuffs->save($arr_save))
				echo 'ok';
			else
				echo $this->Stuffs->arr_errors_save[1];
		}
		die;
	}
	function save_product(){
		if(isset($_POST)){
			$this->selectModel('Stuffs');
			$arr_save = $_POST;
			$arr_save['value'] = 'Minimum Order Adjustment';
			$arr_save['product_id'] = new MongoId($arr_save['product_id']);
			if(strlen($arr_save['_id'])==24 )
				$arr_save['_id'] = new MongoId($arr_save['_id']);
			else
				unset($arr_save['_id']);
			if($this->Stuffs->save($arr_save))
				echo 'ok';
			else
				echo $this->Stuffs->arr_errors_save[1];
		}
		die;
	}
	function save_changing_code(){
		if(isset($_POST)){
			$this->selectModel('Stuffs');
			$arr_save = $_POST;
			$arr_save['value'] = 'Changing Code';
			$arr_save['password'] = md5($arr_save['password']);
			if(strlen($arr_save['_id'])==24 )
				$arr_save['_id'] = new MongoId($arr_save['_id']);
			else
				unset($arr_save['_id']);
			if($this->Stuffs->save($arr_save))
				echo 'ok';
			else
				echo $this->Stuffs->arr_errors_save[1];
		}
		die;
	}

	function save_logo(){
		if(isset($_POST)){
			$this->selectModel('Stuffs');
			$arr_save = $_POST;
			$arr_save['value'] = 'Logo';
			$arr_save['image_path'] = $arr_save['logo'];
			unset($arr_save['logo']);
			if(strlen($arr_save['_id'])==24 )
				$arr_save['_id'] = new MongoId($arr_save['_id']);
			else
				unset($arr_save['_id']);
			if($this->Stuffs->save($arr_save))
				echo 'ok';
			else
				echo $this->Stuffs->arr_errors_save[1];
		}
		die;
	}

	function save_format_currency(){
		if(isset($_POST)){
			$this->selectModel('Stuffs');
			$arr_save = $_POST;
			$arr_save['value'] = 'Format Currency';
			$arr_save['format_currency'] = (int)$arr_save['format_currency'];
			if(strlen($arr_save['_id'])==24 )
				$arr_save['_id'] = new MongoId($arr_save['_id']);
			else
				unset($arr_save['_id']);
			if($this->Stuffs->save($arr_save))
				echo 'ok';
			else
				echo $this->Stuffs->arr_errors_save[1];
		}
		die;
	}

	function save_format_date(){
		if(isset($_POST)){
			$this->selectModel('Stuffs');
			$arr_save = $_POST;
			$arr_save['value'] = 'Format Date';
			if($arr_save['format_date'] == '')
			$arr_save['format_date'] = 'd M, Y';
			if(strlen($arr_save['_id'])==24 )
				$arr_save['_id'] = new MongoId($arr_save['_id']);
			else
				unset($arr_save['_id']);
			if($this->Stuffs->save($arr_save))
				echo 'ok';
			else
				echo $this->Stuffs->arr_errors_save[1];
		}
		die;
	}

	function save_default_search(){
		if(isset($_POST)){
			$this->selectModel('Stuffs');
			$arr_save = $_POST;
			$arr_save['value'] = 'Default Prefer Customer Search';
			$arr_save['default_search'] = $arr_save['default_search'] == 'Yes' ? true : false;
			if(strlen($arr_save['_id'])==24 )
				$arr_save['_id'] = new MongoId($arr_save['_id']);
			else
				unset($arr_save['_id']);
			if($this->Stuffs->save($arr_save))
				echo 'ok';
			else
				echo $this->Stuffs->arr_errors_save[1];
		}
		die;
	}




// ========================= Code của Trí =============================
	function administrator(){
			$this->Session->write('setup_remember_page','administrator');

	}
	/*function zip(){
		if(isset($_POST)){
			ini_set("max_execution_time", 666300);
			$source=$_POST
			$namezip=$source."compress.zip";
			$zip = new ZipArchive();
			$zip->open($namezip,ZipArchive::CREATE);
			$listfile = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source));
			foreach ($listfile as $key=>$value) {
				$pathinfo=pathinfo($value);
				echo realpath($value)."<br/>";
				if(is_file(realpath($value))!=true){
					$zip->addFolder($pathinfo['dirname']);
				}
				else{
					$zip->addFile(realpath($value),$pathinfo['dirname'].'/'.$pathinfo['basename']);
				}
			}

			$zip->close();
			echo "Complete....!";
			echo "<a href='$namezip'>Download File Zip</a><br/>";
		}
	}*/

	function administrator_backup_restore(){
		if (isset($_POST)) {
			// Phần chạy thao tác backup
			if (isset($_POST['task'])&&$_POST['task']=='backup') {
				ini_set("max_execution_time", 666300);
				App::uses('CakeTime', 'Utility');
				$source=$_POST['folder'];
				$folder_backup_name=@end(explode("/",$source));
				$namezip="backup-".$folder_backup_name.'-'.CakeTime::fromString(time()).".zip";
				$root=str_replace('\\', '/', ROOT);
				$zip = new ZipArchive();
				$zip->open($namezip,ZipArchive::CREATE);
				$listfile = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source));
				foreach ($listfile as $key=>$value) {
					$pathinfo=pathinfo($value);
					$path_file= str_replace($root.'/'.APP_DIR.'/'.WEBROOT_DIR.'/', '', $pathinfo['dirname']);
					if(is_file(realpath($value))!=true){
						$zip->addFolder($pathinfo['dirname']);
					}
					else{
						$zip->addFile(realpath($value),$path_file.'/'.$pathinfo['basename']);
					}
				}
				$zip->close();
				$link=URL.'/'.'app/webroot/'.$namezip;
				echo '<a id="link_backup_file" href="'.$link.'" style="color:#ff0000; text-decoration:none;">Download File Backup</a>';
				die;
			}

			// Phần chạy thao tác upload
			if (isset($_POST['task'])&&$_POST['task']=='upload') {
				if(!isset($_FILES['file'])){
					echo 'please choose file';
					die;
				}
				else{

					$filename = $_FILES["file"]["name"];
					$source = $_FILES["file"]["tmp_name"];
					$type = $_FILES["file"]["type"];

					if(($type=="application/zip" )|| ($type=="application/x-zip-compressed") || ($type== "multipart/x-zip")|| ($type=="application/x-compressed")||($type=="application/octet-stream")){
						 if ($_FILES["file"]["error"] > 0) {
						    echo "File Error: " . $_FILES["file"]["error"] . "<br/>";die;
						 }
						 else {
						 	if (file_exists(TMP.'/'.$filename)){
						 		echo 'File already exists';
						 		die;
						 	}
						 	else{
						 		$root=str_replace('\\', '/', ROOT);
						 		if(move_uploaded_file($source,$root.'/'.APP_DIR.'/'.WEBROOT_DIR.'/upload/'.$filename)){
									echo 'File has been uploaded';
						 			die;
						 		}
						 		else{
						 		echo 'Error upload file';
						 			die;
						 		}
							}
						 }

					}
					else{
						echo "Please choose a file zip";
						die;
					}
				}
			}

			// Phần chạy thao tác xóa file backup
			if (isset($_POST['task'])&&$_POST['task']=='clear_backup') {
				$root=str_replace('\\', '/', ROOT);
				$path= $root.'/'.APP_DIR.'/'.WEBROOT_DIR.'/';
				array_map('unlink', glob( "$path*.zip"));
				echo "Backup file has clean";
				die;
			}

			// Phần chạy thao tác xóa restore
			if (isset($_POST['task'])&&$_POST['task']=='restore') {
				$folder=$_POST['folder'];
				echo $folder;
				die;
			}
		}
	}
	public function export_company($file_type = 'excel'){
		set_time_limit(100);
		ini_set('memory_limit', -1);
		if( DS == '\\') {
			ini_set('mongo.long_as_object', 1);
		}
		if(!in_array($file_type, array('csv','excel')))
        	$file_type = 'excel';
		$this->selectModel('Company');
		$this->selectModel('Contact');
        $companies = $this->Company->select_all(array(
                                   'arr_field'  =>  array('is_customer','is_supplier','no','name','type','phone','fax','email','web','business_type','industry','size','our_rep','our_rep_id','our_csr','our_csr_id','contact_default_id','inactive','is_shipper','tracking_url','addresses','addresses_default_key', 'pricing'),
                                   'arr_order'  =>  array('_id'=>1),
                                   'limit'      =>  9999999
                                   ));
        $arr_addresses = array();
        if($file_type == 'excel'){
        	App::import('Vendor', 'phpexcel/PHPExcel');
			$objPHPExcel = new PHPExcel();

			// Set document properties
			$objPHPExcel->getProperties()->setCreator("")
										 ->setLastModifiedBy("")
										 ->setTitle("Company List")
										 ->setSubject("Company List")
										 ->setDescription("Company List")
										 ->setKeywords("Company List")
										 ->setCategory("Company List");

    		$worksheet = $objPHPExcel->getActiveSheet();

			$worksheet->setCellValue('A1',"Ref no")
											->setCellValue('B1',"Company Type")
											->setCellValue('C1',"Type")
											->setCellValue('D1',"Company")
											->setCellValue('E1',"Contact")
											->setCellValue('F1',"Phone")
											->setCellValue('G1',"Fax")
											->setCellValue('H1',"Email")
											->setCellValue('I1',"Web")
											->setCellValue('J1',"Business Type")
											->setCellValue('K1',"Industry")
											->setCellValue('L1',"Size")
											->setCellValue('M1',"Our Rep")
											->setCellValue('N1',"Our CSR")
											->setCellValue('O1',"Date Last Invoice")
											->setCellValue('P1',"Total 2014")
											->setCellValue('Q1',"Total 2015")
											->setCellValue('R1',"Total to date");
			$i = 2;
    		$arr_companies = Cache::read('arr_companies');
			$orgirin_minimum = Cache::read('minimum');
	    	$product = Cache::read('minimum_product');
	    	if(!$orgirin_minimum){
	    		$this->selectModel('Stuffs');
	    		$this->selectModel('Product');
	    		$product = $this->Stuffs->select_one(array('value'=>"Minimum Order Adjustment"),array('product_id'));
				$p = $this->Product->select_one(array('_id'=> new MongoId($product['product_id'])),array('sell_price'));
				$orgirin_minimum = $p['sell_price'];
				Cache::write('minimum',$orgirin_minimum);
	        	Cache::write('minimum_product',$product);
	    	}
			$product_id = $product['product_id'];
	    	$this->selectModel('Salesinvoice');
	    	$array = array(
	            			'sum_2014' => array(
                        		'$gte' => new MongoDate(strtotime('2014-01-01 00:00:00')),
                        		'$lte' => new MongoDate(strtotime('2014-12-31 23:59:59')),
                        	),
            				'sum_2015' => array(
                        		'$gte' => new MongoDate(strtotime('2015-01-01 00:00:00')),
                        		'$lte' => new MongoDate(strtotime('2015-12-31 23:59:59')),
                        	)
	            		);
	        foreach($companies as $company){
	        	$company_id = $company['_id'];
				$minimum = $orgirin_minimum;
				if(isset($arr_companies[(string)$company_id])){
					$minimum = $arr_companies[(string)$company_id];
				}else if(is_object($company_id)){
					if(isset($company['pricing'])){
						foreach($company['pricing'] as $pricing){
							if(isset($pricing['deleted'])&&$pricing['deleted']) continue;
							if((string)$pricing['product_id']!=(string)$product_id) continue;
							if(!isset($pricing['price_break']) || empty($pricing['price_break'])) continue;
							$price_break = reset($pricing['price_break']);
							$minimum =  (float)$price_break['unit_price'];
							break;
						}
					}
            		$arr_companies[(string)$company_id] = $minimum;
				}
	        	$arr_addresses[(string)$company['_id']] = array(
	        	                                                'addresses'=>(isset($company['addresses']) ? $company['addresses'] : array()),
	        	                                                'addresses_default_key'=>(isset($company['addresses_default_key']) ? $company['addresses_default_key'] : 0),
	        	                                                );
	            $type = '';
	            $contact = '';
	            if(isset($company['is_customer']) && $company['is_customer'])
	                $type ='Customer';
	            if(isset($company['is_supplier']) && $company['is_supplier'])
	                $type .='Supllier';
	            if(isset($company['contact_default_id'])&&is_object($company['contact_default_id'])){
	                $contact = $this->Contact->select_one(array('_id'=>$company['contact_default_id']),array('full_name','mobile','email'));
	            }
	            foreach($array as $year => $condition) {
	        		$data = $this->db->command(array(
	        		    'mapreduce'     => 'tb_salesinvoice',
	        		    'map'           => new MongoCode('
	        		                                    function() {
	        		                                        if( this.sum_sub_total < '.$minimum.') {
	        		                                            this.sum_sub_total = '.$minimum.';
	        		                                        }
	        		                                        emit("total", this.sum_sub_total)
	        		                                    }
	        		                                    '),
	        		    'reduce'        => new MongoCode('
	        		                                    function(k, v) {
	        		                                        var i, sum = 0;
	        		                                        for(i in v) {
	        		                                            sum += v[i];
	        		                                        }
	        		                                        return sum;
	        		                                    }
	        		                                '),
	        		    'query'         => array(
	                   							'company_id'	=> $company['_id'],
	        		                            'deleted'    	=> false,
	        		                            'invoice_date' 	=> $condition,
	        		                            'status' 		=> array('$ne' => 'Cancelled')
	        		                        ),
	        		    'out'           => array('merge' => 'tb_result')
	        		));
					if( isset($data['ok']) && isset($data['counts']['output']) && $data['counts']['output'] ) {
					    $result = $this->db->selectCollection('tb_result')->findOne();
					    $$year = isset($result['value']) ? $result['value'] : 0;

					} else {
						$$year = 0;
					}
					$this->db->selectCollection('tb_result')->remove(array('_id' => 'total'));
	            }
	            $invoice = $this->Salesinvoice->select_one(array('company_id' => $company['_id'], 'invoice_status' => array('$ne' => 'Cancelled')), array('invoice_date'), array('invoice_date' => -1));
	            if( isset($invoice['invoice_date']) && is_object($invoice['invoice_date']) ) {
	            	$invoice_date = date('m/d/Y', $invoice['invoice_date']->sec);
	            } else {
	            	$invoice_date = '';
	            }
	            if( empty($invoice_date) ) {
	            	$sum_2014 = $sum_2015 = 0;
	            }
	            $worksheet->setCellValue('A'.$i,(isset($company['no']) ? (string)$company['no'] : ''))
												->setCellValue('B'.$i,$type)
												->setCellValue('C'.$i,(isset($company['type']) ? $company['type'] : ''))
												->setCellValue('D'.$i,$company['name'])
												->setCellValue('E'.$i,(isset($contact['full_name']) ? $contact['full_name'] : ''))
												->setCellValue('F'.$i,(isset($company['phone']) ? $company['phone'] : ''))
												->setCellValue('G'.$i,(isset($contact['fax']) ? $contact['fax'] : ''))
												->setCellValue('H'.$i,(isset($company['email']) ? $company['email'] : '' ))
												->setCellValue('I'.$i,(isset($company['web']) ? $company['web'] : '' ))
												->setCellValue('J'.$i,(isset($company['business_type']) ? $company['business_type'] : '' ))
												->setCellValue('K'.$i,(isset($company['industry']) ? $company['industry'] : '' ))
												->setCellValue('L'.$i,(isset($company['size']) ? $company['size'] : '' ))
												->setCellValue('M'.$i,(isset($company['our_rep']) ? $company['our_rep'] : '' ))
												->setCellValue('N'.$i,(isset($company['our_csr']) ? $company['our_csr'] : '' ))
												->setCellValue('O'.$i, $invoice_date)
												->setCellValue('P'.$i, $sum_2014)
												->setCellValue('Q'.$i, $sum_2015)
												->setCellValue('R'.$i, "=SUM(T$i:U$i)");
	            $i++;
	        }
	        $worksheet->getRowDimension(8)->setRowHeight(-1);
			$worksheet->setTitle('Company List');
			$worksheet->getStyle('P2:R'.$i)->getNumberFormat()->setFormatCode("#,##0.00");
			$worksheet->getStyle('O2:O'.$i)->getNumberFormat()->setFormatCode("[$-409]mmmm d, yyyy;@");
			$worksheet->getStyle('A1:R1')->getFont()->setBold(true);
			$worksheet->freezePane('E1');
			$styleArray = array(
			        'borders' => array(
			           'allborders' => array(
			               'style' => PHPExcel_Style_Border::BORDER_THIN
			            )
			        ),
			        'font'  => array(
			            'size'  => 11,
			            'name'  => 'Century Gothic'
			        )
			);
			$worksheet->getStyle('A1:R'.($i-1))->applyFromArray($styleArray);
			for($i = 'A'; $i !== 'S'; $i++){
			    $worksheet->getColumnDimension($i)
			                    ->setAutoSize(true);
			}

			$objPHPExcel->setActiveSheetIndex(0);
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save(APP.'webroot'.DS.'upload'.DS.'Company_List.xlsx');
			//Address
			$objPHPExcel = new PHPExcel();

			// Set document properties
			$objPHPExcel->getProperties()->setCreator("")
										 ->setLastModifiedBy("")
										 ->setTitle("Company Address List")
										 ->setSubject("Company Address List")
										 ->setDescription("Company Address List")
										 ->setKeywords("Company Address List")
										 ->setCategory("Company Address List");

			$objPHPExcel->setActiveSheetIndex(0)
						->mergeCells('A1:C1')
			            ->setCellValue('A1', "Company Address List\n");
    		$worksheet = $objPHPExcel->getActiveSheet();

			$worksheet->setCellValue('A8',"Company ID")
											->setCellValue('B8',"Name")
											->setCellValue('C8',"Default")
											->setCellValue('D8',"Address 1")
											->setCellValue('E8',"Address 2")
											->setCellValue('F8',"Address 3")
											->setCellValue('G8',"Town / City")
											->setCellValue('H8',"Province / State")
											->setCellValue('I8',"Zip / Post code")
											->setCellValue('J8',"Country");
			$i = 9;
			foreach($arr_addresses as $company_id => $address){
				$addresses_default_key = $address['addresses_default_key'];
				foreach($address['addresses'] as $key=>$value){
					if(isset($value['deleted']) && $value['deleted']) continue;
					$worksheet->setCellValue('A'.$i,$company_id)
											->setCellValue('B'.$i,(isset($value['name']) ? $value['name'] : ''))
											->setCellValue('C'.$i,($key == $addresses_default_key ? 1 : ''))
											->setCellValue('D'.$i,(isset($value['address_1']) ? $value['address_1'] : ''))
											->setCellValue('E'.$i,(isset($value['address_2']) ? $value['address_2'] : ''))
											->setCellValue('F'.$i,(isset($value['address_3']) ? $value['address_3'] : ''))
											->setCellValue('G'.$i,(isset($value['town_city']) ? $value['town_city'] : ''))
											->setCellValue('H'.$i,(isset($value['province_state']) ? $value['province_state'] : ''))
											->setCellValue('I'.$i,(isset($value['zip_postcode']) ? $value['zip_postcode'] : ''))
											->setCellValue('J'.$i,(isset($value['country']) ? $value['country'] : ''));
				}
				$i++;
			}
			$worksheet->getRowDimension(8)->setRowHeight(-1);
			$worksheet->setTitle('Company Address List');

			$objPHPExcel->setActiveSheetIndex(0);
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save(APP.'webroot'.DS.'upload'.DS.'Company_Address_List.xlsx');
			//
			$zipname = APP.'webroot'.DS.'upload'.DS.'companies_list.zip';
			$zip = new ZipArchive;
			$zip->open($zipname, ZipArchive::CREATE);
			$zip->addFile( APP.'webroot'.DS.'upload'.DS.'Company_Address_List.xlsx','Company_Address_List.xlsx');
			$zip->addFile( APP.'webroot'.DS.'upload'.DS.'Company_List.xlsx','Company_List.xlsx');
			$zip->close();
			header('Set-Cookie: fileDownload=true; path=/');
			// header('Content-Type: application/zip');
			header('Content-disposition: attachment; filename=companies_list.zip');
			header('Content-Length: ' . filesize($zipname));
			readfile($zipname);
        } else if($file_type == 'csv'){
        	ob_start();
        	$csv = fopen(APP.DS.'webroot'.DS.'upload'.DS."Company_List.csv",'w');
        	fputcsv($csv, array("","Type","Company","Company ID","Contact","Contact ID","Phone","Fax","Email","Web","Business Type","Industry","Size","Our Rep","Our Rep ID","Our CSR","Our CSR ID","Inactive","Is Shipper","Tracking URL"));
        	foreach($companies as $company){
	        	$arr_addresses[(string)$company['_id']] = array(
	        	                                                'addresses'=>(isset($company['addresses']) ? $company['addresses'] : array()),
	        	                                                'addresses_default_key'=>(isset($company['addresses_default_key']) ? $company['addresses_default_key'] : 0),
	        	                                                );
	            $type = '';
	            $contact = '';
	            if(isset($company['is_customer'])&&$company['is_customer']==1)
	                $type ='Customer';
	            if(isset($company['is_supplier'])&&$company['is_supplier']==1)
	                $type .='Supllier';
	            if(isset($company['contact_default_id'])&&is_object($company['contact_default_id'])){
	                $contact = $this->Contact->select_one(array('_id'=>$company['contact_default_id']),array('full_name','mobile','email'));
	            }
	            $array = array();
	            $array[] = (isset($company['no']) ? $company['no'] : '');
				$array[] = $type;
				$array[] = (isset($company['type']) ? $company['type'] : '');
				$array[] = $company['name'];
				$array[] = (string)$company['_id'];
				$array[] = (isset($contact['full_name']) ? $contact['full_name'] : '');
				$array[] = (isset($contact['_id']) ? (string)$contact['_id'] : '');
				$array[] = (isset($company['phone']) ? $company['phone'] : '');
				$array[] = (isset($contact['fax']) ? $contact['fax'] : '');
				$array[] = (isset($company['email']) ? $company['email'] : '' );
				$array[] = (isset($company['web']) ? $company['web'] : '' );
				$array[] = (isset($company['business_type']) ? $company['business_type'] : '' );
				$array[] = (isset($company['industry']) ? $company['industry'] : '' );
				$array[] = (isset($company['size']) ? $company['size'] : '' );
				$array[] = (isset($company['our_rep']) ? $company['our_rep'] : '' );
				$array[] = (isset($company['our_rep_id']) ? (string)$company['our_rep_id'] : '' );
				$array[] = (isset($company['our_csr']) ? $company['our_csr'] : '' );
				$array[] = (isset($company['our_csr_id']) ? (string)$company['our_csr_id'] : '' );
				$array[] = (isset($company['inactive']) ? 1 : '' );
				$array[] = (isset($company['is_shipper']) ? 1 : '' );
				$array[] = (isset($company['tracking_url']) ? $company['tracking_url'] : '' );
				fputcsv($csv,$array);
	        }
        	fclose($csv);
        	$csv = fopen(APP.DS.'webroot'.DS.'upload'.DS."Company_Address_List.csv",'w');
        	fputcsv($csv, array("Company ID","Name","Default","Address 1","Address 2","Address 3","Town / City","Province / State","Zip / Post code","Country"));
        	foreach($arr_addresses as $company_id => $address){
				$addresses_default_key = $address['addresses_default_key'];
				foreach($address['addresses'] as $key=>$value){
					if(isset($value['deleted']) && $value['deleted']) continue;
					$array = array();
					$array[] = $company_id;
					$array[] = (isset($value['name']) ? $value['name'] : '');
					$array[] = ($key == $addresses_default_key ? 1 : '');
					$array[] = (isset($value['address_1']) ? $value['address_1'] : '');
					$array[] = (isset($value['address_2']) ? $value['address_2'] : '');
					$array[] = (isset($value['address_3']) ? $value['address_3'] : '');
					$array[] = (isset($value['town_city']) ? $value['town_city'] : '');
					$array[] = (isset($value['province_state']) ? $value['province_state'] : '');
					$array[] = (isset($value['zip_postcode']) ? $value['zip_postcode'] : '');
					$array[] = (isset($value['country']) ? $value['country'] : '');
					fputcsv($csv,$array);
				}
			}
			fclose($csv);
			$zipname = APP.'webroot'.DS.'upload'.DS.'companies_list.zip';
			$zip = new ZipArchive;
			$zip->open($zipname, ZipArchive::CREATE);
			$zip->addFile( APP.'webroot'.DS.'upload'.DS.'Company_Address_List.csv','Company_Address_List.csv');
			$zip->addFile( APP.'webroot'.DS.'upload'.DS.'Company_List.csv','Company_List.csv');
			$zip->close();
			header('Set-Cookie: fileDownload=true; path=/');
			// header('Content-Type: application/zip');
			header('Content-disposition: attachment; filename=companies_list.zip');
			header('Content-Length: ' . filesize($zipname));
			readfile($zipname);
        }

		die;
	}
	public function export_contact($file_type = 'excel'){
		ini_set('memory_limit', '-1');
		$this->selectModel('Company');
		$this->selectModel('Contact');
        $contacts = $this->Contact->select_all(array(
                                   'arr_field'  =>  array('is_customer','is_employee','no','first_name','last_name','company','company_id','phone','email','fax','position','company_phone','department','extension_no', 'anvy_login'),
                                   'arr_where' => array('inactive' => array('$ne' => 1)),
                                   'arr_order'  =>  array('company_id'=>1),
                                   'limit'      =>  9999
                                   ));
        if(!in_array($file_type, array('csv','excel')))
        	$file_type = 'excel';
		$arrCompanies = [];
        if($file_type == 'excel'){
        	App::import('Vendor', 'phpexcel/PHPExcel');
			$objPHPExcel = new PHPExcel();

			// Set document properties
			$objPHPExcel->getProperties()->setCreator("")
										 ->setLastModifiedBy("")
										 ->setTitle("Contact List")
										 ->setSubject("Contact List")
										 ->setDescription("Contact List")
										 ->setKeywords("Contact List")
										 ->setCategory("Contact List");

			$objPHPExcel->setActiveSheetIndex(0)
						->mergeCells('A1:C1')
			            ->setCellValue('A1', "Contact List\n");

			$objPHPExcel->getActiveSheet()->setCellValue('A8',"Ref no")
											->setCellValue('B8',"Type")
											->setCellValue('C8',"Title")
											->setCellValue('D8',"First name")
											->setCellValue('E8',"Last Name")
											->setCellValue('F8',"Company")
											->setCellValue('G8',"Company ID")
											->setCellValue('H8',"Company Type")
											->setCellValue('I8',"Our Rep")
											->setCellValue('J8',"Our CSR")
											->setCellValue('K8',"Position")
											->setCellValue('L8',"Department")
											->setCellValue('M8',"Company Phone")
											->setCellValue('N8',"Extension no")
											->setCellValue('O8',"Phone")
											->setCellValue('P8',"Fax")
											->setCellValue('Q8',"Mobile")
											->setCellValue('R8',"Email")
											->setCellValue('S8',"Anvy Login")
											;
			$i = 9;
	        foreach($contacts as $contact){
	            $type = '';
	            if(isset($contact['is_customer'])&&$contact['is_customer']==1)
	                $type ='Customer';
	            if(isset($contact['is_employee'])&&$contact['is_employee']==1)
	                $type .='Employee';
	            $companyType = $ourRep = $ourCSR = '';
	           	if( isset($contact['company_id']) && is_object($contact['company_id']) ) {
	           		if( !isset($arrCompanies[ (string)$contact['company_id'] ]) ) {
	           			$company = $this->Company->select_one(array('_id' => $contact['company_id']), array('is_customer', 'is_supplier', 'our_csr', 'our_rep'));
	           			if(isset($company['is_customer'])&&$company['is_customer']==1)
			                $companyType ='Customer';
			            if(isset($company['is_supplier'])&&$company['is_supplier']==1)
			                $companyType .=' Supllier';
			           	$ourRep = isset($company['our_rep']) ? $company['our_rep'] : '';
			           	$ourCSR = isset($company['our_csr']) ? $company['our_csr'] : '';
	           			$arrCompanies[ (string)$contact['company_id'] ] = array(
	           																	'company' => $companyType,
	           																	'our_rep' => $ourRep,
	           																	'our_csr' => $ourCSR,
	           																);
	           		} else {
	           			$company =  $arrCompanies[ (string)$contact['company_id'] ];
	           			$companyType = $company['company'];
	           			$ourRep = $company['our_rep'];
	           			$ourCSR = $company['our_csr'];
	           		}
	           	}
	            $objPHPExcel->getActiveSheet()->setCellValue('A'.$i,(isset($contact['no']) ? $contact['no'] : ''))
												->setCellValue('B'.$i,$type)
												->setCellValue('C'.$i,(isset($contact['title']) ? $contact['title'] : ''))
												->setCellValue('D'.$i,$contact['first_name'])
												->setCellValue('E'.$i,$contact['last_name'])
												->setCellValue('F'.$i,(isset($contact['company']) ? $contact['company'] : ''))
												->setCellValue('G'.$i,(isset($contact['company_id']) ? (string)$contact['company_id'] : ''))
												->setCellValue('H'.$i,$companyType)
												->setCellValue('I'.$i,$ourRep)
												->setCellValue('J'.$i,$ourCSR)
												->setCellValue('K'.$i,(isset($contact['position']) ? $contact['position'] : ''))
												->setCellValue('L'.$i,(isset($contact['department']) ? $contact['department'] : ''))
												->setCellValue('M'.$i,(isset($contact['company_phone']) ? $contact['company_phone'] : ''))
												->setCellValue('N'.$i,(isset($contact['extension_no']) ? $contact['extension_no'] : ''))
												->setCellValue('O'.$i,(isset($contact['phone']) ? $contact['phone'] : ''))
												->setCellValue('P'.$i,(isset($contact['fax']) ? $contact['fax'] : ''))
												->setCellValue('Q'.$i,(isset($contact['mobile']) ? $contact['mobile'] : ''))
												->setCellValue('R'.$i,(isset($contact['email']) ? $contact['email'] :  '' ))
												->setCellValue('S'.$i,(!isset($contact['anvy_login']) || $contact['anvy_login']  ? 1 :  0 ));
	            $i++;
	        }
	        $objPHPExcel->getActiveSheet()->getRowDimension(8)->setRowHeight(-1);
			$objPHPExcel->getActiveSheet()->setTitle('Contact List');

			$objPHPExcel->setActiveSheetIndex(0);
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save(APP.DS.'webroot'.DS.'upload'.DS.'Contact_List.xlsx');
			header('Set-Cookie: fileDownload=true; path=/');
			header('Content-Disposition: attachment; filename="Contact_List.xlsx"');
			readfile(APP.DS.'webroot'.DS.'upload'.DS.'Contact_List.xlsx');
        } else if($file_type == 'csv'){
        	ob_start();
        	$csv = fopen(APP.DS.'webroot'.DS.'upload'.DS."Contact_List.csv",'w');
        	fputcsv($csv, array('Ref no','Type','Title','First name','Last Name','Company','Company ID','Position','Department','Company Phone','Extension no','Phone','Fax','Mobile','Email'));
        	foreach($contacts as $contact){
	            $companyType = $ourRep = $ourCSR = '';
	           	if( isset($contact['company_id']) && is_object($contact['company_id']) ) {
	           		if( !isset($arrCompanies[ (string)$contact['company_id'] ]) ) {
	           			$company = $this->Company->select_one(array('_id' => $contact['company_id']), array('is_customer', 'is_supplier', 'our_csr', 'our_rep'));
	           			if(isset($company['is_customer'])&&$company['is_customer']==1)
			                $companyType ='Customer';
			            if(isset($company['is_supplier'])&&$company['is_supplier']==1)
			                $companyType .=' Supllier';
			           	$ourRep = isset($company['our_rep']) ? $company['our_rep'] : '';
			           	$ourCSR = isset($company['our_csr']) ? $company['our_csr'] : '';
	           			$arrCompanies[ (string)$contact['company_id'] ] = array(
	           																	'company' => $companyType,
	           																	'our_rep' => $ourRep,
	           																	'our_csr' => $ourCSR,
	           																);
	           		} else {
	           			$company =  $arrCompanies[ (string)$contact['company_id'] ];
	           			$companyType = $company['company'];
	           			$ourRep = $company['our_rep'];
	           			$ourCSR = $company['our_csr'];
	           		}
	           	}
        		$type = '';
	            if(isset($contact['is_customer'])&&$contact['is_customer']==1)
	                $type ='Customer';
	            if(isset($contact['is_employee'])&&$contact['is_employee']==1)
	                $type .='Employee';
	            $array = array();
	            $array[] = (isset($contact['no']) ? $contact['no'] : '');
	            $array[] = $type;
	            $array[] = (isset($contact['title']) ? $contact['title'] : '');
	            $array[] = (isset($contact['first_name']) ? $contact['first_name'] : '');
	            $array[] = (isset($contact['last_name']) ? $contact['last_name'] : '');
	            $array[] = (isset($contact['company']) ? $contact['company'] : '');
	            $array[] = (isset($contact['company_id']) ? (string)$contact['company_id'] : '');
	            $array[] = $companyType;
	            $array[] = $ourRep;
	            $array[] = $ourCSR;
	            $array[] = (isset($contact['position']) ? $contact['position'] : '');
	            $array[] = (isset($contact['department']) ? $contact['department'] : '');
	            $array[] = (isset($contact['company_phone']) ? $contact['company_phone'] : '');
	            $array[] = (isset($contact['extension_no']) ? $contact['extension_no'] : '');
	            $array[] = (isset($contact['phone']) ? $contact['phone'] : '');
	            $array[] = (isset($contact['fax']) ? $contact['fax'] : '');
	            $array[] = (isset($contact['mobile']) ? $contact['mobile'] : '');
	            $array[] = (isset($contact['email']) ? $contact['email'] : '');
	            fputcsv($csv,$array);
	            $i++;
        	}
			header('Set-Cookie: fileDownload=true; path=/');
			header("Content-Disposition: attachment;filename=Contact_List.csv");
			readfile(APP.DS.'webroot'.DS.'upload'.DS."Contact_List.csv");
        }
		die;
	}

	function list_menu_delete($id,$key) {
        $arr_save['_id'] = new MongoId($id);
        $this->selectModel('Setting');
        $arr_save = $this->Setting->select_one(array('_id'=> new MongoId($id)));
        unset($arr_save['option'][$key]);
        $arr_save['option'] = array_values($arr_save['option']);
        $error = 0;
        if (!$error) {
            if ($this->Setting->save($arr_save)) {
                echo 'ok';
            } else {
                echo 'Error: ' . $this->Setting->arr_errors_save[1];
            }
        }
        die;
    }

    function cache(){
		$this->Session->write('setup_remember_page', 'cache');
    	$settings = Cache::settings();
		$arr_cache = array();
    	if(class_exists('Memcache') && $settings['engine'] == 'Memcache'){
    		$memcache = new Memcache;
		    $memcache->connect('127.0.0.1', 11211)
		       or die ("Could not connect to memcache server");
		    $allSlabs = $memcache->getExtendedStats('slabs');
		    $items = $memcache->getExtendedStats('items');
		    foreach($allSlabs as $server => $slabs) {
		        foreach($slabs AS $slabId => $slabMeta) {
		        	if (!is_int($slabId)) continue;
		            $cdump = $memcache->getExtendedStats('cachedump',(int)$slabId,  100000000);
		            foreach($cdump AS $keys => $arrVal) {
		                if (!is_array($arrVal)) continue;
		                foreach($arrVal AS $k => $v){
		                	$k = str_replace('app_', '', $k);
		                	$arr_cache[] = $k;
		                }
		            }
		        }
		    }
    	} else {
    		$cache_dir = APP.'tmp'.DS.'cache'.DS;
            $caches = scandir($cache_dir);
            foreach($caches as $cache){
            	if(is_dir($cache_dir.$cache)) continue;
            	$cache = str_replace(array('cake_','.ctp'), '', $cache);
            	if(in_array($cache, array('.','..'))) continue;
                $arr_cache[] = $cache;
            }
    	}
    	asort($arr_cache);
	    $this->set('arr_cache',$arr_cache);
    }

    function cache_detail($name){
    	$cache_detail = Cache::read($name);
    	$this->set('cache_detail',$cache_detail);
    	$this->set('name',$name);
    }
    function cache_delete($name){
    	if($name == 'all')
    		Cache::clear();
    	else if(strpos($name, '@') !== false){
    		$name = ltrim($name, '@');
    		$name = explode($name, '|');
    		foreach($name as $value){
	    		$arr_caches = $this->get_cache_keys_diff('',$value);
	    		foreach($arr_caches as $key => $cache){
	    			Cache::delete($cache);
	    		}
    		}
    	} else
    		Cache::delete($name);
    	echo 'ok';
    	die;
    }
    function build_roles(){
    	$this->selectModel('Role');
    	$arr_roles = $this->Role->select_all(array(
    						'arr_field' => array('value')
    		));
    	echo $arr_roles->count().'<br />';
    	$i = 0;
    	foreach($arr_roles as $role){
    		if(!isset($role['value'])) continue;
    		foreach($role['value'] as $k => $v){
    			$role['value'][$k] = array('all');
    		}
    		$this->Role->rebuild_collection($role);
    		$i++;
    	}
    	echo $i.' records build';
    	die;
    }
    function build_employee_roles(){
    	$this->selectModel('Contact');
    	$contacts = $this->Contact->select_all(array(
                'arr_where' => array(
                        'is_employee' => 1
                    ),
                'arr_field' => array('roles')
            ));
        $i = 0;
        foreach($contacts as $contact){
            if(!isset($contact['roles']['roles']) )
            	$contact['roles']['roles'] = array();
            $contact['roles']['roles'] = array_values($contact['roles']['roles']);
            $this->Contact->rebuild_collection($contact);
            $i++;
        }
        echo $i.' employees done.';
        die;
    }
    function update_stuffs($ids='',$value=''){
    	if(isset($_POST['_id']))
    		$ids = $_POST['_id'];
    	if(isset($_POST['value']))
    		$value = $_POST['value'];

    	$this->selectModel('Stuffs');
    	$arr_data = array(
    		'_id'=>new MongoId($ids),
    		'value'=>$value,
    	);
    	if($this->Stuffs->save($arr_data)){
    		if(isset($_POST['_id'])){
    			echo 'ok';die;
    		}else
    			return true;
    	}
    }

    function sale_ruler(){
		$this->Session->write('setup_remember_page', 'sale_ruler');
		$this->selectModel('Setting');
		$arr_tmp = $this->Setting->select_one(array('setting_value'=>'product_base'));
		$this->set('product_base_id',(string)$arr_tmp['_id']);
		$arr_tmp = $this->Setting->select_one(array('setting_value'=>'product_category'));
		$this->set('product_category_id',(string)$arr_tmp['_id']);
	}

	function sale_price_list_type($pb_id=''){
		$this->selectModel('Stuffs');
    	$sale_price_list = $this->Stuffs->select_all(array(
            'arr_where' => array(
                    'type' => 'price_break'
                ),
            'arr_field' => array('option','table_ranges'),
            'arr_order' => array('date_modified'=>1)
        ));
        $cf_option = $cf_price_select = $cf_option_tmp = array();
        $cf_table_ranges = $table_ranges = $cf_ranges_tmp = array();
        $cf_price_id = '0';
        $cf_price_select['0'] = 'Empty';
        if($sale_price_list->count(true)>0)
    	foreach ($sale_price_list as $key => $value) {
    		$cf_price_select[$key] = $value['option']['name']['value'];
    		$cf_option_tmp = $value['option'];
    		$cf_key_tmp = $key;
    		if(isset($value['table_ranges']))
    			$cf_ranges_tmp = $value['table_ranges'];
    		else
    			$cf_ranges_tmp = array();
    		if($key==$pb_id){
    			$cf_option = $value['option'];
    			if(isset($value['table_ranges']))
    				$cf_table_ranges = $value['table_ranges'];
    			$cf_price_id = $key;
    		}
    	}
    	if(empty($cf_option)){
    		$cf_option = $cf_option_tmp;
    		$cf_price_id = $cf_key_tmp;
    		$cf_table_ranges = $cf_ranges_tmp;
    	}
    	$this->set('cf_price_select',$cf_price_select);
    	$this->set('cf_price_id',$cf_price_id);
		$this->set('cf_option',$cf_option);
		$this->set('cf_table_ranges',$cf_table_ranges);


	}

	function sale_price_save($id='',$field='',$value='',$type=''){
		$this->selectModel('Stuffs');
		if(isset($_POST['id']))
			$id=$_POST['id'];
		if(isset($_POST['field']))
			$field=$_POST['field'];
		if(isset($_POST['value']))
			$value=$_POST['value'];
		if(isset($_POST['type']))
			$type=$_POST['type'];

		if($type=='add')
	    	$arr_data = array(
	    		'type'=>'price_break',
	    		'option'=>array(
	    			'name' =>array(
	    				'title'=>'Name',
	    				'value'=> 'Price Brakes A',
	    			),
					'price_step' =>array(
						'title'=>'Price step',
						'value'=> 'Not use',
					),
					'qt_step' =>array(
						'title'=>'Qty step',
						'value'=> 'Not use',
					),
					'price_rate' =>array(
						'title'=>'Sell price = cost x',
						'value'=> 3,
					),
	    		),
	    	);
	    else if($id!=''){
	    	$arr_data = $this->Stuffs->select_one(array('_id'=> new MongoId($id)));
	    	if(isset($arr_data['option'][$field]))
	    		$arr_data['option'][$field]['value'] = $value;
	    }


    	if($this->Stuffs->save($arr_data)){
    		if(isset($_POST['type']) || isset($_POST['id'])){
    			echo 'ok';die;
    		}else
    			return true;
    	}
	}
	function sale_pb_price_ranges(){
		$arr_save = array();
		$this->selectModel('Stuffs');
		$table_ranges = $this->data;
		if(!isset($table_ranges['id']))
			die;
		$stuff_id = $table_ranges['id'];
		$arr_save['_id'] = new MongoId($stuff_id);
		//add
		if(isset($table_ranges['type']) && $table_ranges['type']=='add'){
			$arr_save['table_ranges'] = array();
			$arr_tmp = $this->Stuffs->select_one(array('_id'=> new MongoId($stuff_id)),array('table_ranges'));
			if(isset($arr_tmp['table_ranges']))
				$arr_save['table_ranges'] = $arr_tmp['table_ranges'];
			$arr_save['table_ranges'][] = array(
				'deleted' =>false,
				'unit_price' =>0,
				'sell_category' =>'Retail',
				'range_from' =>0,
				'range_to' =>0,
			);
			if($this->Stuffs->save($arr_save))
	        	echo 'ok';
	        pr($arr_save);
	        die;
		}
		//delete
		if(isset($table_ranges['type']) && $table_ranges['type']=='delete'){
			$arr_save['table_ranges'] = array();
			$arr_tmp = $this->Stuffs->select_one(array('_id'=> new MongoId($stuff_id)),array('table_ranges'));
			if(isset($arr_tmp['table_ranges']))
				$arr_save['table_ranges'] = $arr_tmp['table_ranges'];
			if(isset($table_ranges['ids']))
				unset($arr_save['table_ranges'][$table_ranges['ids']]);
			if($this->Stuffs->save($arr_save))
	        	echo 'ok';
	        pr($arr_save);
	        die;
		}
		//deleted config
		if(isset($table_ranges['type']) && $table_ranges['type']=='delete_config'){
			$arr_save['deleted'] = true;
			if($this->Stuffs->save($arr_save))
        		echo 'ok';
			die;
		}
		//update
		unset($table_ranges['id']);
        foreach ($table_ranges as $key => $value) {
        	$table_ranges[$key]['deleted'] = false;
        	$table_ranges[$key]['unit_price'] = 0;
        	$table_ranges[$key]['sell_category'] = 'Retail';
        	$table_ranges[$key]['range_from'] = (int)$value['range_from'];
        	$table_ranges[$key]['range_to'] = (int)$value['range_to'];
        }
        $arr_save['table_ranges'] = $table_ranges;
        if($this->Stuffs->save($arr_save))
        	echo 'ok';
        die;
    }



    function sales_ruler_basic_product($pb_id=''){
		$this->selectModel('Stuffs');
    	$sale_price_list = $this->Stuffs->select_all(array(
            'arr_where' => array(
                    'type' => 'price_break'
                ),
            'arr_field' => array('option','table_ranges'),
            'arr_order' => array('date_modified'=>1)
        ));
        $cf_option = $cf_price_select = $cf_option_tmp = array();
        $cf_table_ranges = $table_ranges = $cf_ranges_tmp = array();
        $cf_price_id = '0';
        $cf_price_select['0'] = 'Empty';
        if($sale_price_list->count(true)>0)
    	foreach ($sale_price_list as $key => $value) {
    		$cf_price_select[$key] = $value['option']['name']['value'];
    		$cf_option_tmp = $value['option'];
    		$cf_key_tmp = $key;
    		if(isset($value['table_ranges']))
    			$cf_ranges_tmp = $value['table_ranges'];
    		else
    			$cf_ranges_tmp = array();
    		if($key==$pb_id){
    			$cf_option = $value['option'];
    			if(isset($value['table_ranges']))
    				$cf_table_ranges = $value['table_ranges'];
    			$cf_price_id = $key;
    		}
    	}
    	if(empty($cf_option)){
    		$cf_option = $cf_option_tmp;
    		$cf_price_id = $cf_key_tmp;
    		$cf_table_ranges = $cf_ranges_tmp;
    	}
    	$this->set('cf_price_select',$cf_price_select);
    	$this->set('cf_price_id',$cf_price_id);
		$this->set('cf_option',$cf_option);
		$this->set('cf_table_ranges',$cf_table_ranges);
	}


	function bleed_calculation($stuff_id=''){
		$this->selectModel('Stuffs');
    	$cf_bleed = $this->Stuffs->select_one(array('value' => 'bleed_type'));
    	$this->set('cf_bleed',$cf_bleed);
    	$cf_uom = array('in'=>'Inch','ft'=>'Feet');
    	$this->set('cf_uom',$cf_uom);
	}
	function sale_bleed($stuff_id='',$type='',$ids=0){
		$this->selectModel('Stuffs');
		if(isset($_POST['id']))
			$stuff_id = $_POST['id'];
		if(isset($_POST['type']))
			$type = $_POST['type'];
		if(isset($_POST['ids']))
			$ids = $_POST['ids'];


		$cf_bleed = $this->Stuffs->select_one(array('_id' => new MongoId($stuff_id)));

		$arr_save = array();
		$arr_save['_id'] = new MongoId($stuff_id);
		if($type=='add'){
			$arr_save['option'] = $cf_bleed['option'];
			$arr_tmp = $this->Stuffs->aasort($cf_bleed['option'],'key',-1,true);
			$arr_new = array();
			if(isset($arr_tmp[0]['key']))
				$arr_new['key'] = (int)$arr_tmp[0]['key']+1;
			else
				$arr_new['key'] = 0;
			$arr_new['name'] = 'New Bleed '.$arr_new['key'];
			$arr_new['sizew'] = $arr_new['sizeh'] = 0;
			$arr_new['sizew_unit'] = $arr_new['sizeh_unit'] = 'in';
			$arr_save['option'][] = $arr_new;
		}else if($type=='delete' && isset($cf_bleed['option'][$ids])){
			unset($cf_bleed['option'][$ids]);
			$arr_save['option'] = $cf_bleed['option'];

		}else if($type=='update' && isset($cf_bleed['option'][$ids]) && isset($_POST['field']) && isset($_POST['value'])){
			$field = $_POST['field'];
			$cf_bleed['option'][$ids][$field] = $_POST['value'];
			if($field=='sizew'){
				$cf_bleed['option'][$ids]['sizeh'] = $cf_bleed['option'][$ids][$field] = (float)$_POST['value'];
			}
			if($field=='sizew_unit')
				$cf_bleed['option'][$ids]['sizeh_unit'] = $_POST['value'];
			$arr_save['option'] = $cf_bleed['option'];
		}
		// pr($arr_save);die;
		$this->Stuffs->save($arr_save);
		echo 'ok';die;
	}


}
