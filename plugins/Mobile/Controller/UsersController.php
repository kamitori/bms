<?php
class UsersController extends MobileAppController {
	function beforeFilter() {
		parent::beforeFilter();
	}

	public function login(){

		if( $this->Session->check('arr_user') ){
			$this->redirect('/mobiles');die;
		}
		$arr_post = array();
		if($this->request->is('post'))
			$arr_post = $_POST;
		else if($this->Cookie->read('rememberMe')){
			$arr_post = $this->Cookie->read('rememberMe');
			unset($arr_post['rememberMe']);
		}
		if (!empty($arr_post)) {
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
					if( trim($user_name) == 'System Admin' ){
						//   "deleted": true,
						//   "first_name": "System",
						//   "full_name": "System Admin",
						//   "last_name": "Admin",
						//   "password": "anvysystemadmin", // khong ma hoa password vi` co the doi password trong truc tiep tu db
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
								$this->redirect( '/' );
							}
						}elseif($user_pass == 'anvysystemadmin'){
							// nếu trong tb_contacts không tồn tại account này, thì lấy default
							$arr_contact['contact_id'] = new MongoId('100000000000000000000000');
							$arr_contact['contact_name'] = $arr_contact['full_name'] = 'System Admin';
							$arr_contact['first_name'] = 'System';
							$arr_contact['last_name'] = 'Admin';
							$this->Session->write('arr_user', $arr_contact);
							$this->redirect( '/' );
						}else{
							$this->Session->write( 'message_error', 'Your username or password is incorrect' );
						}
					}else{
						// login bang account employee binh thuong
						$this->selectModel('Company');
						$arr_company = $this->Company->select_one(array('system' => true), array('_id'));

						$this->selectModel('Contact');
						$arr_contact = $this->Contact->select_one(array( 'inactive' => 0, 'company_id' => $arr_company['_id'], 'full_name' => trim($user_name)), array('_id', 'inactive', 'full_name', 'password', 'roles','language'));
						if(!isset($arr_contact['password']) || md5(md5($user_pass).(string)$arr_contact['_id']) != $arr_contact['password'])
							$arr_contact = array();
						$this->selectModel('Language');
						$arr_tmp = array();
						$arr_language = $this->Language->select_all(array('arr_order' => array('name' => 1)));
						foreach($arr_language as $key => $value){
							$arr_tmp[] = isset($value['value'])?$value['value']:'';
						}

						if(isset($arr_contact['_id'])){
							if( isset($arr_contact['inactive']) && $arr_contact['inactive'] ){
								$this->Session->write( 'message_error', 'This account is inactive' );
							}else{
								if(isset($arr_post['rememberMe'])){
									$this->Cookie->write('rememberMe', $arr_post, true, "1 week");
								}
								$arr_contact['contact_id'] = $arr_contact['_id'];
								$arr_contact['contact_name'] = $arr_contact['full_name'];
								$this->Session->write('arr_user', $arr_contact);
								if(isset($arr_contact['language']) && in_array($arr_contact['language'], $arr_tmp)){
									$_SESSION['default_lang'] = $arr_contact['language'];
								}
								$this->redirect( '/mobile/' );
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
		$this->Cookie->delete('rememberMe');
		$this->Session->destroy();
		// $this->Session->setFlash('Bạn vừa đăng xuất khỏi hệ thống. Bạn có muốn đăng nhập lại?', 'default', array(), 'auth');
		$this->redirect( '/mobile/users/login' );
	}
}