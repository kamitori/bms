<?php
App::uses('AppController', 'Controller');
class UsersController extends AppController {

	var $name = 'Users';

	var $modelName = 'User';
    public $helpers = array();
    public $opm; //Option Module

	function beforeFilter(){
		parent::beforeFilter();
		$this->set_module_before_filter('User');
		// Allow anyone access specific functions
		// $this->Auth->allow( 'login', 'logout' );
	}

	public function entry() {

        $arr_set = $this->opm->arr_settings;
        $arr_tmp = array();
        // Get value id
        $iditem = $this->get_id();
        if ($iditem == '')
            $iditem = $this->get_last_id();

        $this->set('iditem', $iditem);
        //Load record by id
        if ($iditem != '') {
            $arr_tmp = $this->opm->select_one(array('_id' => new MongoId($iditem)));
            if( isset($arr_tmp['system']) && $arr_tmp['system'] && $arr_tmp['no'] != 1 ) {
                $this->set('extra_system', true);
            }
            foreach ($arr_set['field'] as $ks => $vls) {
                foreach ($vls as $field => $values) {
                    if (isset($arr_tmp[$field])) {
                        $arr_set['field'][$ks][$field]['default'] = $arr_tmp[$field];
                        if (preg_match("/_date$/", $field) && is_object($arr_tmp[$field]))
                            $arr_set['field'][$ks][$field]['default'] = date('m/d/Y', $arr_tmp[$field]->sec);
                        if (in_array($field, $arr_set['title_field']))
                            $item_title[$field] = $arr_tmp[$field];
                        if ($field == 'contact_name' && isset($arr_tmp['contact_last_name'])) {
                            $arr_set['field'][$ks][$field]['default'] = $arr_tmp[$field] . ' ' . $arr_tmp['contact_last_name'];
                            $item_title['contact_name'] = $arr_tmp[$field] . ' ' . $arr_tmp['contact_last_name'];
                        }
                    }
                }
            }

            $arr_set['field']['panel_1']['mongo_id']['default'] = $iditem;
            $this->Session->write($this->name . 'ViewId', $iditem);

        }else {
            $nextcode = $this->opm->get_auto_code('no');
            $arr_set['field']['panel_1']['no']['default'] = $nextcode;
            $this->set('item_title', array('no' => $nextcode));
        }
        $this->set('arr_settings', $arr_set);
        parent::entry();
	}

    public function lists() {
        $this->selectModel('User');
        $limit = LIST_LIMIT;
        $skip = 0;
        $sort_field = '_id';
        $sort_type = 1;
        if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
            if( $_POST['sort']['type'] == 'desc' ){
                $sort_type = -1;
            }
            $sort_field = $_POST['sort']['field'];
            $this->Session->write('Users_lists_search_sort', array($sort_field, $sort_type));

        }elseif( $this->Session->check('Users_lists_search_sort') ){
            $session_sort = $this->Session->read('Users_lists_search_sort');
            $sort_field = $session_sort[0];
            $sort_type = $session_sort[1];
        }
        $arr_order = array($sort_field => $sort_type);
        $this->set('sort_field', $sort_field);
        $this->set('sort_type', ($sort_type === 1)?'asc':'desc');

        // dùng cho điều kiện
        $cond = $where_query = $this->arr_search_where();
        // dùng cho phân trang
        $page_num = 1;
        if( isset($_POST['pagination']) && $_POST['pagination']['page-num'] > 0){
            $page_num = $_POST['pagination']['page-num'];
            $limit = $_POST['pagination']['page-list'];
            $skip = $limit*($page_num - 1);
        }
        $this->set('page_num', $page_num);
        $this->set('limit', $limit);

        // query
        $arr_users = $this->User->select_all(array(
            'arr_where' => $cond,
            'arr_order' => $arr_order,
            'arr_field' => array('no','user_name'),
            'limit' => $limit,
            'skip' => $skip
        ));
        $this->set('arr_users', $arr_users);


        $total_page = $total_record = $total_current = 0;
        if( is_object($arr_users) ){
            $total_current = $arr_users->count(true);
            $total_record = $arr_users->count();
            if( $total_record%$limit != 0 ){
                $total_page = floor($total_record/$limit) + 1;
            }else{
                $total_page = $total_record/$limit;
            }
        }
        $this->set('total_current', $total_current);
        $this->set('total_page', $total_page);
        $this->set('total_record', $total_record);

        if ($this->request->is('ajax')) {
            $this->render('lists_ajax');
        }
        $this->set('sum', $total_record);
    }	

	function reload_db(){
		$this->selectModel('User');

		$this->selectModel('Contact');
		$arr_contact = $this->Contact->select_all(array('arr_where' => array('is_employee' => 1)));
		foreach ($arr_contact as $key => $value) {
			$arr_save = $value;
            $arr_save['first_name'] = trim($arr_save['first_name']);
            $arr_save['last_name'] = trim($arr_save['last_name']);
			$arr_save['full_name'] = $arr_save['first_name'] . ' '. $arr_save['last_name'];

			// lay mat khau cua bang User bo qua Contact
			$user = $this->User->select_one(array('user_name' => $arr_save['full_name']));
			if( isset($user['_id']) && strlen($user['user_password']) > 0 ){
				$arr_save['password'] = $user['user_password'];
			}
			if( !$this->Contact->save($arr_save) ){
				echo 'bi loi save';die;
			}
		}
		echo 'xong';
		die;
	}

	public function login(){
		//echo md5(md5('anvy123').'anvysercurity');
		if($_SERVER['HTTP_HOST']=='demo.jobtraq.vimpact.ca' || $_SERVER['HTTP_HOST']=='jt.banhmisub.com' || $_SERVER['HTTP_HOST']=='bms.com' || $_SERVER['HTTP_HOST']=='bms.local' || $_SERVER['HTTP_HOST']=='bmsjt.vimpact.ca' || $_SERVER['HTTP_HOST']=='bmsdemo_jt.com'){ //is demo
			$hash_key = '9a0d10368cb4fb5b3d40f649bd8c0e22';
			$user_admin_key = 'admin';
		}else{
			$hash_key = '9556dff9d688661f7aa1338e144b6491';
			$user_admin_key = 'System Admin';
		}
		if( $this->Session->check('arr_user') ){
			$this->redirect('/');die;
		}

		if ($this->request->is('post')) {
			$arr_post = $_POST;
			if( isset( $arr_post['txt_user_name'] ) && isset( $arr_post['txt_user_pass'] ) ){
				$user_name = $arr_post['txt_user_name'];
				$user_pass = $arr_post['txt_user_pass'];
				$error = 0;
				if( trim($user_name) == '' ){
					$error = 1;
					$this->Session->write( 'message_error', 'You must specify a user_name to login' );
				}
				if( $error == 0 && $user_pass == '' ){
					$error = 1;
					$this->Session->write( 'message_error', 'You must specify password to login' );
				}
				if( $error == 0 ){
					// login bang account system để login vào hệ thống (tb_contact)
					if( trim($user_name) == $user_admin_key ){
					//   "deleted": true,
					//   "first_name": "System",
					//   "full_name": "System Admin",
					//   "last_name": "Admin",
					//   "password": "3016", // khong ma hoa password vi` co the doi password trong truc tiep tu db
					//   "roles": "all"
						$this->selectModel('Contact');
						$this->Contact->has_field_deleted = false;
						$arr_contact = $this->Contact->select_one(array( 'full_name' => trim($user_name)));
						if(isset($arr_contact['_id']) && isset($arr_contact['password'])){
							// nếu tb_contacts co thi kiem tra password
							if($arr_contact['password'] == $user_pass){
								$arr_contact['contact_id'] = $arr_contact['_id'];
								$arr_contact['contact_name'] = $arr_contact['full_name'];
								$this->Session->write('arr_user', $arr_contact);
								$this->Contact->save_working_begin_time($arr_contact['_id']);
								$this->redirect( '/' );
							}
						}elseif(md5(md5($user_pass).'anvysercurity') == $hash_key){
							// nếu trong tb_contacts không tồn tại account này, thì lấy default
							$arr_contact['contact_id'] = new MongoId('100000000000000000000000');
							$arr_contact['contact_name'] = $arr_contact['full_name'] = 'System Admin';
							$arr_contact['first_name'] = 'System';
							$arr_contact['last_name'] = 'Admin';
							$this->selectModel('Company');
							$company = $this->Company->select_one(array('system' => true,'no' => 1),array('no'),array('_id' => -1));
							$arr_contact['company_id'] = $company['_id'];
							$arr_contact['company_no'] = $company['no'];
							if( IS_LOCAL ) {
								$arr_contact['system_admin'] = true;
							}
							$this->Session->write('arr_user', $arr_contact);
							$this->redirect( '/' );
						}else{
							$this->Session->write( 'message_error', 'Your username or password is incorrect' );
						}
					}else{
					// login bang account employee binh thuong
						/*$this->selectModel('Company');
						$arr_company = $this->Company->select_one(array('system' => true), array('_id'));*/

						$this->selectModel('Contact');
						// $arr_contact = $this->Contact->select_one(array( 'inactive' => 0, 'company_id' => $arr_company['_id'], 'full_name' => trim($user_name)), array('_id', 'inactive', 'full_name', 'password', 'roles','language','theme','email', 'email_setting'));
						$arr_contact = $this->Contact->select_one(array( 'inactive' => 0, 'is_employee' => 1, 'full_name' => trim($user_name)), array('_id', 'inactive', 'full_name', 'password', 'roles','language','theme','email', 'email_setting', 'company_id', 'our_rep', 'our_rep_id'));
						if(!isset($arr_contact['password']) || md5(md5($user_pass).(string)$arr_contact['_id']) != $arr_contact['password'])
							$arr_contact = array();
						if(isset($arr_contact['_id'])){
							if( isset($arr_contact['inactive']) && $arr_contact['inactive'] ){
								$this->Session->write( 'message_error', 'This account is inactive' );
							}else{
								$arr_contact['company_id'] = $arr_contact['company_id'];
								$this->selectModel('Company');
								$company = $this->Company->select_one(array('_id' => $arr_contact['company_id']),array('no'));
								$arr_contact['company_no'] = $company['no'];
								$arr_contact['contact_id'] = $arr_contact['_id'];
								$arr_contact['contact_name'] = $arr_contact['full_name'];
								$this->Session->write('arr_user', $arr_contact);
								//=======================================================
								$this->requestAction('/contacts/working_hours_start/call-from-login');
								//=======================================================
								$this->selectModel('Language');
								$arr_tmp = array();
								$arr_language = $this->Language->select_all(array('arr_order' => array('name' => 1)));
								foreach($arr_language as $key => $value){
									$arr_tmp[] = isset($value['value'])?$value['value']:'';
								}
								//=======================================================
								if(isset($arr_contact['language']) && in_array($arr_contact['language'], $arr_tmp)){
									$_SESSION['default_lang'] = $arr_contact['language'];
								}
								if(isset($arr_contact['theme']))
									$_SESSION['theme'] = strtolower($arr_contact['theme']);
								else
									$_SESSION['theme'] = 'default';
								if(isset($_SESSION['REFERRER_LOGIN'])){
									$referrer_login = $_SESSION['REFERRER_LOGIN'];
									unset($_SESSION['REFERRER_LOGIN']);
									//language

									if( $referrer_login == URL.'/homes/iframe_reload_session' || $referrer_login == '/homes/iframe_reload_session' )
										$referrer_login = '/';
									$this->redirect( $referrer_login );
								}else{
									$this->redirect( '/' );
								}
							}
						}else
							$this->Session->write( 'message_error', 'Your username or password is incorrect' );
					}
				}
			}
		}
		$this->layout = 'login';
	}
	public function logout() {
		// $this->Cookie->delete('Auth.User');
		$this->Session->destroy();
		// $this->Session->setFlash('Bạn vừa đăng xuất khỏi hệ thống. Bạn có muốn đăng nhập lại?', 'default', array(), 'auth');
		$this->redirect( '/users/login' ); die;
	}

	function test() {
		$string = 'WT-FGL-559';
		echo preg_replace("/[^0-9]/","",$string);
		die;
	}

	public function get_chatters()
	{
		$this->selectModel('Contact');
		$chatters = $this->Contact->select_all(array(
							'arr_where' => array(
											'is_employee' =>1,
											// '_id' => array('$ne' => $_SESSION['arr_user']['contact_id'])
											),
							'arr_field' => array('first_name', 'last_name', 'color'),
							'arr_order'	=> array('first_name' => 1)
							));
		$arrChatters = array();
		$chat 	= $this->db->selectCollection('tb_chat');
		$newMessage = 0;
		foreach($chatters as $chatter) {
			$color = isset($chatter['color']) ? $chatter['color'] : '#000';
			$name = trim((isset($chatter['first_name']) ? $chatter['first_name'] : '').' '.(isset($chatter['last_name']) ? $chatter['last_name'] : ''));
			$arrName = explode(' ', $name);
			$shortName = '';
			foreach($arrName as $n) {
				$shortName .= strtoupper(substr($n, 0, 1));
			}
			$count =  $chat->count(array('from'=> $chatter['_id'], 'to' => $_SESSION['arr_user']['contact_id'], 'read' => false));
			$arrChatters[] = array('_id' => (string)$chatter['_id'], 'name' => $name, 'short_name' => $shortName, 'color' => $color, 'count' => $count);
			if( $chatter['_id'] ==  $_SESSION['arr_user']['contact_id']) continue;
			$newMessage += $count;
		}
		echo json_encode(array('chatters' => $arrChatters, 'new_message' => $newMessage));
		die;
	}

	public function get_chat_content() {
		$from 	= new MongoId($_POST['from']);
		$to 	= new MongoId($_POST['to']);
		$chat 	= $this->db->selectCollection('tb_chat');
		$chatContent = $chat->find(array(
										'$or' => array(
													array(
														'from' => $from,
														'to' => $to,
														),
													array(
														'from' => $to,
														'to' => $from,
														),
												),
										'date_modified' => array('$gte' => new MongoDate(time() - 3*DAY))
										))->sort(array('date_modified' => 1));
		$arrContent = array();
		foreach($chatContent as $content) {
			$arrContent[] = array(
					'from' => (string)$content['from'],
					'to' 	=> (string)$content['to'],
					'message' => $content['message'],
					'read' => (int)$content['read'],
					'time' => date('H:i:s M d, Y', $content['date_modified']->sec),
				);
		}
		echo json_encode($arrContent);
		die;
	}

	public function update_chat_read() {
		$from 	= new MongoId($_POST['from']);
		$to 	= new MongoId($_POST['to']);
		$chat 	= $this->db->selectCollection('tb_chat');
		$chat->update(array('from'=> $from, 'to' => $to, 'read' => false), array('$set' =>array('read' => true)),array('multiple' => true));
		echo 'ok';
		die;
	}
}