<?php
class CommunicationsController extends MobileAppController{
	var $modelName = 'Communication';
	var $name = 'Communications';

	function beforeFilter(){
		parent::beforeFilter();
	}

	function entry($id = '0', $num_position = -1) {
		//$id = $this->get_id();
		//$this->selectModel('Communication');
		//$comm = $this->Communication->select_one(array('_id' => new MongoId($id)));
		$comm = $this->entry_init($id, $num_position, 'Communication', 'communications');
		//pr($comm);die;
		$comm['comms_date'] = (is_object($comm['comms_date'])) ? date('m/d/Y', $comm['comms_date']->sec) : '';

		$arr_tmp1['Communication'] = $comm;
		$this->data = $arr_tmp1;

		$this->selectModel('Country');
		$options = array();
		$options['countries'] = $this->Country->get_countries();
		$this->selectModel('Province');
		$options['provinces'] = $this->Province->get_all_provinces();
		$this->set('options',$options);

		switch ($comm['comms_type']) {
			case 'Message':
				$this->message();
				$this->render('../Communications/message');
				break;
			case 'Fax':
				$this->fax();
				$this->render('../Communications/fax');
				break;
			case 'Letter':
				$this->letter();
				$this->render('../Communications/letter');
				break;
			case 'Note':
				$this->note();
				$this->render('../Communications/note');
				break;
			case 'Email':
				$this->email();
				$this->render('../Communications/email');
				break;
			default:
				$this->note();
				$this->render('../Communications/note');
				break;
		}
	}

	function add_com($type){
		$this->selectModel('Communication');
		$arr_save = array();

		/*
		if ($type == 'letter') {
			die('letter');
		}
		if ($type == 'fax') {
			die('fax');
		}
		if ($type == 'email') {
			die('email');
		}*/
		if ($type == 'note') {
			$this->create_note();
			die('note');
		}
		if ($type == 'message') {
			$this->create_message();
			die('message');;
		}
		

		$arr_save['comms_type'] = ucfirst($type);
		$this->Communication->arr_default_before_save = $arr_save;
		if ($this->Communication->add())
			$this->redirect('/mobile/communications/entry/' . $this->Communication->mongo_id_after_save);
		die;
	}

    function create_message() {
        $this->selectModel('Communication');
        $arr_save['code'] = $this->Communication->get_auto_code('code');
        $arr_save['contact_from_id'] = $this->Communication->user_id();
        $arr_save['contact_from'] = $this->Communication->user_name();
        //$arr_save['contact_to'] = $_POST['contact'];
        //$arr_save['contact_to_id'] = new MongoId($_POST['contact_id']);
        $arr_save['contact_to'] = '';
        $arr_save['contact_to_id'] = '';
        $arr_save['comms_type'] = 'Message';
        //$arr_save['content'] = (isset($_POST['content']) ? trim((string)$_POST['content']) : '');
        $arr_save['content'] = '';
        $arr_save['comms_date'] = new MongoDate();
        $arr_save['viewed'] = false;

        // BaoNam: ghi nhớ là được tạo từ module nào
        if( $this->Session->check( $this->modelName.'_module_'.$arr_save['contact_to_id']) ){
            $arr_tmp = $this->Session->read( $this->modelName.'_module_'.$arr_save['contact_to_id'] );
            $arr_save['module'] = $arr_tmp[0];
            $arr_save['module_id'] = new MongoId($arr_tmp[1]);
            $this->Session->delete( $this->modelName.'_module_'.$arr_save['contact_to_id']);
        }
        // end

        /*if(isset($_POST['reply'])&&isset($_POST['message_id'])&&$_POST['reply'])
        {
            $parent_message = $this->Communication->select_one(array('_id'=>new MongoId($_POST['message_id'])));
            $arr_save['message_group'] = $parent_message['message_group'];
        }*/
        else
            $arr_save['message_group'] = $this->Communication->get_auto_code('message_group');

        if($this->Communication->save($arr_save))
        {
            $id = $this->Communication->mongo_id_after_save;
            //echo json_encode(array('status'=>'ok','url'=>URL.'/communications/entry/'.$id));
            $this->redirect('/mobile/communications/entry/' . $id);
        }
    }
    function create_note() {
        $this->selectModel('Communication');
        $arr_save['code'] = $this->Communication->get_auto_code('code');
        $arr_save['comms_type'] = 'Note';
        $arr_save['comms_date'] = new MongoDate();
        $arr_save['note'] = '';
        $arr_save['contact_from_id'] = $this->Communication->user_id();
        $arr_save['contact_from'] = $this->Communication->user_name();
        $arr_save['company_name'] = '';
        $arr_save['company_id'] = '';
        $arr_save['contact_name'] = '';
        $arr_save['contact_id'] = '';
        if($this->Communication->save($arr_save))
        {
            $id = $this->Communication->mongo_id_after_save;
            $this->redirect('/mobile/communications/entry/' . $id);
        }
        die;
    }

	function fax(){
		$this->selectModel('Setting');

		$this->set('com_sign_off', $this->Setting->select_option(array('setting_value' => 'com_sign_off'), array('option')));
		$this->set('contacts_position', $this->Setting->select_option(array('setting_value' => 'contacts_position'), array('option')));
	}

	function letter(){
		$this->selectModel('Setting');

		$this->set('com_sign_off', $this->Setting->select_option(array('setting_value' => 'com_sign_off'), array('option')));
		$this->set('contacts_position', $this->Setting->select_option(array('setting_value' => 'contacts_position'), array('option')));
	}

	function note(){

	}

	function message(){

	}

	function email(){

		$id = $this->get_id();
		$this->selectModel('Communication');
		$comm = $this->Communication->select_one(array('_id' => new MongoId($id)));

		$this->selectModel('Setting');
		$this->set('com_sign_off', $this->Setting->select_option(array('setting_value' => 'com_sign_off'), array('option')));
		$this->set('contacts_position', $this->Setting->select_option(array('setting_value' => 'contacts_position'), array('option')));
	}

	function lists( $content_only = '' ) {
		$this->selectModel('Communication');
		$limit = 20; $skip = 0;
		// dùng cho sort
		$sort_field = 'code';
		$sort_type = -1;
		if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
			if( $_POST['sort']['type'] == 'desc' ){
				$sort_type = -1;
			}
			$sort_field = $_POST['sort']['field'];
			$this->Session->write('communications_lists_search_sort', array($sort_field, $sort_type));

		}elseif( $this->Session->check('communications_lists_search_sort') ){
			$session_sort = $this->Session->read('communications_lists_search_sort');
			$sort_field = $session_sort[0];
			$sort_type = $session_sort[1];
		}
		$this->set('sort_field', $sort_field);
		$this->set('sort_type', ($sort_type === 1)?'asc':'desc');


		// dùng cho điều kiện
		$cond = array();
		if( $this->Session->check('communications_entry_search_cond') ){
			$cond = $this->Session->read('communications_entry_search_cond');
		}

		// dùng cho phân trang
		if(isset($_POST['offset'])){
			$skip = $_POST['offset'];
		}
		// query
		$arr_communications = $this->Communication->select_all(array(
			'arr_where' => $cond,
			'arr_order' => array('_id'=>-1),
			'limit' => $limit,
			'skip' => $skip
		));
		$this->set('arr_communications', $arr_communications);
		$this->selectModel('Setting');
		$this->set('arr_communications_type', $this->Setting->select_option(array('setting_value' => 'communications_type'), array('option')));

		if($content_only!=''){
			$this->render("lists_ajax");
		}
	}

	function auto_save() {
		if (!empty($this->data)) {
			$id = $this->get_id();
			$arr_return = array();
			$arr_post_data = $this->data['Communication'];
			$arr_save = $arr_post_data;
			$arr_save['_id'] = new MongoId($id);
			$this->selectModel('Communication');

			if(isset($arr_save['code'])){
				if(!is_numeric($arr_save['code'])){
					echo json_encode(array('status'=>'error','message'=>'must_be_numberic'));
					die;
				}
				$arr_tmp = $this->Communication->select_one(array('code' => (int) $arr_save['code'], '_id' => array('$ne' => new MongoId($id))));
				if (isset($arr_tmp['code'])) {
					echo json_encode(array('status'=>'error','message'=>'ref_code_existed'));
					die;
				}
			}

			if (!isset($arr_save['include_signature']))
				$arr_save['include_signature'] = 0;

			if( strlen($arr_save['name']) > 0 )
				$arr_save['name']{0} = strtoupper($arr_save['name']{0});

			if (isset($arr_save['contact_address']['0']['contact_country_id'])) {
				$this->selectModel('Country');
				$options = array();
				$options = $this->Country->get_countries();
				$arr_save['contact_address']['0']['contact_country'] = $options[$arr_save['contact_address']['0']['contact_country_id']] ;
			}

			if (isset($arr_save['contact_address']['0']['contact_province_state_id']) && isset($arr_save['contact_address']['0']['contact_country_id']) ) {
				$this->selectModel('Country');
				$options = array();
				$options = $this->Country->get_countries();

				$this->selectModel('Province');
				$optionProvince = $this->Province->get_all_provinces();
				$arr_save['contact_address']['0']['contact_province_state'] = $optionProvince[$arr_save['contact_address']['0']['contact_country_id']][$arr_save['contact_address']['0']['contact_province_state_id']] ;
			}

			if(isset($arr_save['comms_date'])) {
				$comms_date = $this->Common->strtotime($arr_save['comms_date'].' 00:00:00');
				$arr_save['comms_date'] =  new MongoDate($comms_date);
			}


			$this->selectModel('Communication');
			if ($this->Communication->save($arr_save))
				$arr_return['status'] = 'ok';
			else
				$arr_return = array('status'=>'error','message'=>'Error: ' . $this->Communication->arr_errors_save[1]);
			echo json_encode($arr_return);
		}
		die;
	}

	public function send_email($use_default_email='') {
        App::uses('CakeEmail', 'Network/Email');
        $email = new CakeEmail();
        //Nếu user tự dùng mail của mình
        if($use_default_email==''){
            $arr_email_setting = $this->get_user_email_setting();
            $config_set = array();
            $username = '';
            $password = '';
            //Truong hop tat ca thong tin da save vao contact
            if (!empty($arr_email_setting)&&isset($arr_email_setting['password']) && isset($arr_email_setting['email']) && $arr_email_setting['email'] != '')
            {
                $username = $arr_email_setting['email'];
                $data = str_replace(array('-','_'),array('+','/'),$arr_email_setting['password']);
                $mod4 = strlen($data) % 4;
                if ($mod4) {
                    $data .= substr('====', $mod4);
                }
                $data = base64_decode($data);
                $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
                $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
                $password = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->opm->user_id(), $data, MCRYPT_MODE_ECB, $iv));

            }//Tùng, Nói nôm na là user chỉ nhập mail và pass, mà ko chịu save, nên đẩy qua $_POST luôn, không biết đặt tên thế nào cho hợp lý, thôi kệ
            else if(isset($_POST['email_one_use'])&&isset($_POST['password_one_use'])
                &&$_POST['email_one_use']!=''&&$_POST['password_one_use']!='')
            {
                $username = $_POST['email_one_use'];
                $password = $_POST['password_one_use'];
            }
            if(strlen($username)>1&&strlen($password)>1)
            {
                $config_set = array(
                    'from'      => array($username=>$this->opm->user_name().' - JobTraq'),
                    'username'  => $username,
                    'password'  => $password,
                );

                //Tim @ cuoi cung
                $pos1 = strrpos($username, '@', -1);
                //Tim dau . gan nhat bat dau tu @
                $pos2 = strpos ($username, '.' ,$pos1 );
                //Cat chuoi tu @ toi .
                $email_type = strtoupper(substr($username, $pos1+1,$pos2-$pos1-1));
                //Khong phai gmail thi tim port va host dc dinh san o setting, gmail da co default (ko can tim)
                if($email_type!='GMAIL')
                {
                    $config_set['config_type'] = 'smtp';
                    $this->selectModel('Setting');
                    $email_setting = $this->Setting->select_one(array('setting_value' => 'email_setting'), array('option'));
                    foreach($email_setting['option'] as $value)
                    {
                        if($value['name']==$email_type){
                            if($value['value']['host']!='')
                                $config_set['host'] = $value['value']['host'];
                            if($value['value']['port']!='')
                                $config_set['port'] = (int)$value['value']['port'];
                            break;
                        }
                    }
                    if(!isset($config_set['host'])&&!isset($config_set['port'])){
                        echo 'contact_admin';
                        die;
                    }
                }
                else
                    $config_set['config_type'] = 'gmail';
            }
            else{
                echo 'not_valid_info';
                die;
            }
        }
        if(isset($config_set['config_type'])){//Co dung email cua minh
            $config_type = $config_set['config_type'];
            unset($config_set['config_type']);
            $email->config($config_type,$config_set);
        }
        else { //Dùng mail mặc định gửi
            $this->selectModel('Stuffs');
            $system_email = $this->Stuffs->select_one(array('value'=>"system_email"));
            if(!empty($system_email)){
                $config_set = array(
                    'from'      => array($system_email['username']=>(trim($system_email['email_name'])!='' ? $system_email['email_name'] : 'Anvy Digital - JobTraq') ),
                    'username'  => $system_email['username'],
                    'password'  => $system_email['password'],
                    'host'      => $system_email['host'],
                    'port'      => $system_email['port'],
                );
                $email->config('smtp',$config_set);
            }
            else
                $email->config('gmail',array('jobtraq.mail@gmail.com'=>$this->opm->user_name()));
        }
        if (isset($_POST['email']))
            $email->to($_POST['email']);
        else
            $email->to('hth.tung90@gmail.com');
        if (isset($_POST['name'])&&trim($_POST['name'])!='')
            $email->subject($_POST['name']);
        else
            $email->subject($this->opm->user_name().' - JobTraq');
        if (isset($_POST['email_cc']) && strlen($_POST['email_cc']) > 0 && filter_var($_POST['email_cc'], FILTER_VALIDATE_EMAIL) )
            $email->cc($_POST['email_cc']);
        if (isset($_POST['email_bcc']) && strlen($_POST['email_bcc']) > 0 && filter_var($_POST['email_bcc'], FILTER_VALIDATE_EMAIL) )
            $email->bcc($_POST['email_bcc']);
        $arr_email = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())));
        if (isset($_POST['content']))
            $arr_email['content'] = $_POST['content'];
        else{
            $arr_email['content'] = (isset($arr_email['content']) ? $arr_email['content'] : '');
        }
        //Kiem tra attachment, va dua vao mail se gui
        $files = array();
        $attachments = $this->get_attachment($this->get_id());
        if(is_object($attachments)&&$attachments->count())
        {
            foreach($attachments as $value)
                $files[] = getcwd().$value['path'];
        }
        if(!empty($files))
            $email->attachments($files);
        $email->emailFormat('both');
        $email->template('default');
        //$email->template('ten_file ctp');
        try{
            if ($email->send($arr_email['content'])) {
                $this->set_status_sent();
                echo 'sent';
                die;
            }
        } catch(Exception $e){
            echo $e;
            die;
        }
        die;
    }

    public function get_user_email_setting(){
        $this->selectModel('Contact');
        $arr_user = $this->Contact->select_one(array('_id' => new MongoId($this->Contact->user_id())), array('email_setting'));
        if (isset($arr_user['email_setting']) && !empty($arr_user['email_setting']))
        {
            return $arr_user['email_setting'];
        }
        else
            return array();
        die;
    }

    function get_attachment($id=''){
        if($id!=''&&strlen($id)==24)
        {
            $arr_where = array();
            $this->selectModel('DocUse');
            $doc_id = $this->DocUse->select_all(array(
                'arr_where'=>array('module_id'=> new MongoId($id)),
                'arr_order'=>array('_id'=>1),
                'arr_field'=>array('doc_id')
                ));
            foreach($doc_id as $value)
                $arr_where['_id']['$in'][] = $value['doc_id'];
            if(!empty($arr_where))
            {
                $this->selectModel('Doc');
                $docs = $this->Doc->select_all(array('arr_where'=>$arr_where,'arr_field'=>array('_id','name','location','path')));
                return $docs;
            }
            return '';
        }
        die;
    }
}