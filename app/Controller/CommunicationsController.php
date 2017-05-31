<?php

App::uses('AppController', 'Controller');

class CommunicationsController extends AppController {

    var $name = 'Communications';
    var $modelName = 'Communication';
    public $helpers = array();
    public $opm; //Option Module

    public function beforeFilter() {
        parent::beforeFilter();
        //$this->set: name, arr_settings, arr_options, iditem, entry_menu
        $this->set_module_before_filter('Communication');
    }
    public function rebuild_setting($arr_setting=array()){
        if(!$this->check_permission($this->name.'_@_entry_@_edit')){
            $arr_setting = $this->opm->set_lock(array(),'out');
            $this->set('address_lock', '1');
        }
        $arr_setting = $this->opm->arr_settings;
        $_id = $this->get_id();
        $comms = array();
        if( !empty($_id) ) {
            $comms = $this->opm->select_one(array('_id'=>new MongoId($_id)));
        }
        if(isset($comms['comms_type'])&&$comms['comms_type']=='Email'&&isset($comms['comms_status'])&&$comms['comms_status']=='Sent')
            $arr_setting = $this->opm->set_lock(array(),'out');
        $this->opm->arr_settings = $arr_setting;
    }
    public function entry($id = '') {
        if($id=='')
            $id = $this->get_id();
        $comms_type = '';
        if(!empty($id))
            $comms_type = $this->get_name('Communication',$id,'comms_type');
        if( isset($comms_type) && $comms_type == 'Message' ){
            $this->_entry_message();
        }else if(isset($comms_type) && $comms_type == 'Note'){
            $this->_entry_note();
        }else{
            $this->_entry_other();
        }
        $address_add = array( 'contact' => 0);
        $this->set('address_add', $address_add);
        $this->set('current_email', isset($_SESSION['arr_user']['email']) ? $_SESSION['arr_user']['email'] : '' );
    }
    function _entry_note()
    {
        $arr_set = $this->opm->arr_settings;
        $iditem = $this->get_id();
        if ($iditem == '')
            $iditem = $this->get_last_id();
        $this->set('iditem', $iditem);
        //Load record by id
        if ($iditem != '') {
            $arr_tmp = $this->opm->select_one(array('_id' => new MongoId($iditem)));
            //echo strtolower($arr_tmp['comms_type']);die;
            if (isset($arr_tmp['comms_type'])) {
                $arr_set['field'] = $arr_set['field_custom'] = $this->opm->custom_entry_layout(strtolower($arr_tmp['comms_type']));
                $arr_set['module_label'] = $arr_tmp['comms_type'];
            }
            if(isset($arr_tmp['content']))
               $this->set('content',$arr_tmp['content']);
            foreach ($arr_set['field'] as $ks => $vls) {
                foreach ($vls as $field => $values) {
                    if (isset($arr_tmp[$field])) {
                        $arr_set['field'][$ks][$field]['default'] = $arr_tmp[$field];
                        if(is_object($arr_tmp[$field])&&strpos($field,'_date'))
                        {
                            $arr_set['field'][$ks][$field]['default'] = date('M d, Y',$arr_tmp[$field]->sec);
                            $arr_set['field'][$ks]['comms_time']['default'] = date('H:m',$arr_tmp[$field]->sec);
                        }
                        if (in_array($field, $arr_set['title_field']))
                        {
                            $item_title[$field] = $arr_tmp[$field];
                            if(is_object($arr_tmp[$field])&&strpos($field,'_date'))
                                $item_title[$field] = date('D, M d, Y',$arr_tmp[$field]->sec);
                        }
                    }
                }
            }
            $arr_set['field']['panel_1']['mongo_id']['default'] = $iditem;
            $this->Session->write($this->name . 'ViewId', $iditem);

            //BEGIN custom
            $this->set('contact',$arr_tmp['contact_from']);
            $this->set('content',isset($arr_tmp['content'])?$arr_tmp['content']:'');
            $this->set('id',$iditem);
            $this->set('item_title', $item_title);

            $datas = $this->general_select($arr_set['field']);

            $this->set("arr_options", $datas);
            //END custom
            //show footer info
            $this->show_footer_info($arr_tmp);


            //add, setup field tự tăng
        }
        $this->set('arr_settings', $arr_set);
    }
    function _entry_message($iditem='')
    {
        $arr_set = $this->opm->arr_settings;
        $iditem = $this->get_id();
        if ($iditem == '')
            $iditem = $this->get_last_id();
        $this->set('iditem', $iditem);
        //Load record by id
        if ($iditem != '') {
            $arr_tmp = $this->opm->select_one(array('_id' => new MongoId($iditem)));
            if($this->opm->user_id() == $arr_tmp['contact_to_id'])
            {
                $arr_tmp['viewed'] = true;
                $this->opm->save($arr_tmp);
            }
            //echo strtolower($arr_tmp['comms_type']);die;
            if (isset($arr_tmp['comms_type'])) {
                $arr_set['field'] = $arr_set['field_custom'] = $this->opm->custom_entry_layout(strtolower($arr_tmp['comms_type']));
                $arr_set['module_label'] = $arr_tmp['comms_type'];
            }
            foreach ($arr_set['field'] as $ks => $vls) {
                foreach ($vls as $field => $values) {
                    if (isset($arr_tmp[$field])) {
                        $arr_set['field'][$ks][$field]['default'] = $arr_tmp[$field];
                        if(is_object($arr_tmp[$field])&&strpos($field,'_date'))
                        {
                            $arr_set['field'][$ks][$field]['default'] = date('M d, Y',$arr_tmp[$field]->sec);
                            $arr_set['field'][$ks]['message_time']['default'] = date('H:m',$arr_tmp[$field]->sec);
                        }
                        if (in_array($field, $arr_set['title_field']))
                        {
                            $item_title[$field] = $arr_tmp[$field];
                            if(is_object($arr_tmp[$field])&&strpos($field,'_date'))
                                $item_title[$field] = date('D, M d, Y',$arr_tmp[$field]->sec);
                        }
                    }
                }
            }
            $arr_set['field']['panel_1']['mongo_id']['default'] = $iditem;
            $this->Session->write($this->name . 'ViewId', $iditem);

            //BEGIN custom
            $this->set('message_id',$iditem);
            $this->set('contact',$arr_tmp['contact_from']);
            $this->set('contact_id',$arr_tmp['contact_from_id']);
            $this->set('content',(isset($arr_tmp['content'])?$arr_tmp['content']:''));
            $this->set('item_title', $item_title);

            $datas = $this->general_select($arr_set['field']);

            $this->set("arr_options", $datas);
            //END custom
            //show footer info
            $this->show_footer_info($arr_tmp);


            //add, setup field tự tăng
        }
        $this->set('arr_settings', $arr_set);
        $this->related_messages($iditem,true);
    }

    public function entry_message($contact_id, $message_id='')
    {
        // BaoNam: sửa lại điều kiện vì $contact_id luôn luôn phải có
        if($message_id=='')
        {
            $contact_name = $this->get_name('Contact',new MongoId($contact_id),'full_name');
            $this->set('contact_name',$contact_name);
            $this->set('contact_id',$contact_id);
        }

        // Nếu có $message_id tức là reply
        else if($message_id!='')
        {
            $message = $this->opm->select_one(array('_id'=> new MongoId($message_id)));
            $this->related_messages($message_id);
            $this->set('contact_name',isset($message['contact_from']) ? $message['contact_from'] : '');
            $this->set('contact_id',isset($message['contact_from_id']) ? $message['contact_from_id'] : '');
            $this->set('reply',true);
        }

        $this->set('message_id',$message_id);
        $this->set('return_mod',1);
        $this->set('return_title','Message');
        $this->set('return_id','cancel_message');

        // BaoNam: ghi nhớ là được tạo từ module nào
        if( isset($_GET['module']) && isset($_GET['module_id']) ){
            $this->Session->write( $this->modelName.'_module_'.$contact_id, array( $_GET['module'], $_GET['module_id'] ) );
        }

    }
    function related_messages($message_id,$option='')
    {
        if($message_id)
        {
            $this_message = $this->opm->select_one(array('_id'=> new MongoId($message_id)));
            $messages = '';
            $messages = $this->opm->select_all(array(
                'arr_where'=>array(
                        'message_group'     =>  $this_message['message_group'],
                        'comms_type'        =>  'Message',
                        '_id'               =>  array('$ne'=> new MongoId($message_id))
                    ),
                'arr_field'=>array(
                    'contact_from','contact_to','message_time','comms_date','code','_id','content'
                    ),
                'arr_order'=>array('_id'=>-1)
                ));
            $this->set('data',$messages);
            $this->set('option',$option);
        }
    }
    function create_message()
    {
        if(isset($_POST))
        {
            $arr_save['code'] = $this->opm->get_auto_code('code');
            $arr_save['contact_from_id'] = $this->opm->user_id();
            $arr_save['contact_from'] = $this->opm->user_name();
            $arr_save['contact_to'] = $_POST['contact'];
            $arr_save['contact_to_id'] = new MongoId($_POST['contact_id']);
            $arr_save['comms_type'] = 'Message';
            $arr_save['content'] = (isset($_POST['content']) ? trim((string)$_POST['content']) : '');
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

            if(isset($_POST['reply'])&&isset($_POST['message_id'])&&$_POST['reply'])
            {
                $parent_message = $this->opm->select_one(array('_id'=>new MongoId($_POST['message_id'])));
                $arr_save['message_group'] = $parent_message['message_group'];
            }
            else
                $arr_save['message_group'] = $this->opm->get_auto_code('message_group');

            if($this->opm->save($arr_save))
            {
                $id = $this->opm->mongo_id_after_save;
                echo json_encode(array('status'=>'ok','url'=>URL.'/communications/entry/'.$id));
            }
        }
        die;
    }
    function _entry_other(){
        $arr_set = $this->opm->arr_settings;
        // Get value id
        $iditem = $this->get_id();
        if ($iditem == '')
            $iditem = $this->get_last_id();
        $this->set('iditem', $iditem);
        //Load record by id
        if ($iditem != '') {
            $arr_tmp = $this->opm->select_one(array('_id' => new MongoId($iditem)));
            //echo strtolower($arr_tmp['comms_type']);die;
            if (isset($arr_tmp['comms_type'])) {
                $arr_set['field'] = $this->opm->custom_entry_layout(strtolower($arr_tmp['comms_type']));
                $arr_set['module_label'] = $arr_tmp['comms_type'];
            }
            foreach ($arr_set['field'] as $ks => $vls) {
                foreach ($vls as $field => $values) {
                    if (isset($arr_tmp[$field])) {
                        if(isset($arr_tmp['comms_status'])&&$arr_tmp['comms_status']=='Sent')
                            $arr_set['field'][$ks][$field]['lock'] = 1;
                        $arr_set['field'][$ks][$field]['default'] = $arr_tmp[$field];
                        if(is_object($arr_tmp[$field])&&strpos($field,'_date'))
                        {
                            $arr_set['field'][$ks][$field]['default'] = date('M d, Y',$arr_tmp[$field]->sec);
                        }
                        if (in_array($field, $arr_set['title_field']))
                            $item_title[$field] = $arr_tmp[$field];
                        if(is_object($arr_tmp[$field])&&strpos($field,'_date'))
                                $item_title[$field] = date('D, M d, Y',$arr_tmp[$field]->sec);
                    }
                }
            }
            $this->set('quotation_submit', isset($arr_tmp['quotation_submit']) ? 1 : 0 );
            $arr_set['field']['panel_1']['mongo_id']['default'] = $iditem;
            if(isset($arr_tmp['content']))
               $this->set('content',$arr_tmp['content']);
            $this->selectModel('Contact');
            $contact = $this->Contact->select_one(array('_id'=>new MongoId($this->opm->user_id())),array('email'));
            $this->set('current_email',(isset($contact['email']) ? $contact['email'] : ''));
            $this->Session->write($this->name . 'ViewId', $iditem);

            //BEGIN custom
            $this->set('item_title', isset($item_title) ? $item_title : '');

            $datas = $this->general_select($arr_set['field']);
            if (isset($arr_tmp['company_id']) && $arr_tmp['company_id'] != '')
                $datas['email_cc'] = $datas['email_bcc'] = $this->reload_cc($arr_tmp['company_id']);

            $this->set('status',(isset($arr_tmp['comms_status']) ? $arr_tmp['comms_status'] : ''));
            $this->set("arr_options", $datas);
            //END custom
            //show footer info
            $this->show_footer_info($arr_tmp);


            //add, setup field tự tăng
        }else {
            $this->redirect('/communications/add');
        }
        $this->set('arr_settings', $arr_set);
        $this->set('attachment',$this->get_attachment($iditem));
        //pr($arr_set);
        //address

        if (isset($arr_tmp) && isset($arr_tmp['comms_type']) && ($arr_tmp['comms_type'] == 'Letter' || $arr_tmp['comms_type'] == 'Fax')) {
            $address_fset = array('address_1', 'address_2', 'address_3', 'town_city', 'country', 'province_state', 'zip_postcode');
            $address_value = $address_province_name = $address_country_name = $address_province = array();
            $address_value['contact'] = array('', '', '', '', "CA", '', '');
            $address_controller = array('contact');
            $this->set('address_controller', $address_controller); //set
            $address_key = array('contact');
            $this->set('address_key', $address_key); //set
            foreach ($address_key as $kss => $vss) {
                if (isset($arr_tmp[$vss . '_address'][0])) {
                    $arr_temp_op = $arr_tmp[$vss . '_address'][0];
                    for ($i = 0; $i < count($address_fset); $i++) {
                        if (isset($arr_temp_op[$vss . '_' . $address_fset[$i]])) {
                            $address_value[$vss][$i] = $arr_temp_op[$vss . '_' . $address_fset[$i]];
                        } else {
                            $address_value[$vss][$i] = '';
                        }
                    }
                    $arr_country = $this->country();
                    $arr_province = $this->province();
                    if (isset($arr_temp_op[$vss . '_province_state']) && isset($arr_province[$arr_temp_op[$vss . '_province_state']]))
                        $address_province_name[$kss] = $arr_province[$arr_temp_op[$vss . '_province_state']];
                    else
                        $address_province_name[$kss] = '';

                    if (isset($arr_temp_op[$vss . '_country_id'])) {
                        $v_country = $arr_temp_op[$vss . '_country_id'];
                        //pr($v_country);die; // v_country = 'US', arr_country = array ( [CA] => Canada, [CN] => China, [CN] => China)
                        $address_country_name[$kss] = $arr_country[$v_country];
                        $address_province[$vss] = $this->province($v_country);
                    } else {
                        $v_country = "CA";
                        $address_country_name[$kss] = $arr_country[$v_country];
                        $arr_temp = $this->province('', $v_country);
                        $address_province[$vss][''] = '';
                        $address_province[$vss] = array_merge($address_province[$vss], $arr_temp);
                    }
                } else {
                    $v_country = "CA";
                    $arr_temp = $this->province('', $v_country);
                    $address_province[$vss][''] = '';
                    $address_province[$vss] = array_merge($address_province[$vss], $arr_temp);
                }
            }
            $address_country_id = array( 0 => isset($arr_tmp['contact_address'][0]['contact_country_id']) ? $arr_tmp['contact_address'][0]['contact_country_id'] : '' );
            //pr($address_country_id);die;
            $this->set('address_country_id',$address_country_id);
            $this->set('address_value', $address_value);
            $address_hidden_field = array('contact_address');
            $this->set('address_hidden_field', $address_hidden_field); //set
            $address_label[0] = $arr_set['field']['panel_3']['contact_address']['name'];
            $this->set('address_label', $address_label); //set
            $address_conner[0]['top'] = 'hgt';
            $address_conner[0]['bottom'] = 'fixbor3 jt_ppbot';
            $this->set('address_conner', $address_conner); //set
            $address_country = $this->country();
            $this->set('address_country', $address_country); //set
            $this->set('address_country_name', $address_country_name); //set
            $this->set('address_province', $address_province); //set
            $this->set('address_province_name', $address_province_name); //set
            $this->set('address_more_line', 0); //set
            $this->set('address_onchange', "save_address_pr('\"+keys+\"');");
            $this->set('address_botclass', '');
        }
    }
    //Kiểm tra xem còn đc attach file vào email ko
    function check_attachment($id='')
    {
        $limit_file = 3;
        if($id!=''&&strlen($id)==24)
        {
            $this->selectModel('DocUse');
            $docs = $this->DocUse->select_all(array('arr_where'=>array('module_id'=> new MongoId($id)),'arr_field'=>array('module_id')));
            if($docs->count()>=$limit_file)
            {
                echo 'false';
                die;
            }
            echo 'true';
            die;
        }
    }
    function get_attachment($id='')
    {
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
    function ajax_attachment($id='')
    {
        $i = 0;
        $html ='';
        if($id!=''&&strlen($id)==24)
        {
            $attachment = $this->get_attachment($id);
            if($attachment!=''&&$attachment->count()>0)
            {
                foreach($attachment as $value)
                {
                    $bg=($i%2==0? 'bg1':'bg2');
                    $html .= '
                        <ul class="ul_mag clear '.$bg.'" id="DocUse_'.$value['_id'].'">
                            <li class="hg_padd" style="width:1.5%"><a href="'.URL.'/docs/entry/'.$value['_id'].'" title="View attachment"><span class="icon_emp"></span></a></li>
                            <li class="hg_padd" style="width:40%">'.$value['name'].'</li>
                            <li class="hg_padd" style="width:35%">'.(isset($value['location'])?$value['location']:'').'</li>
                            <li class="hg_padd center_txt" style="width:13%"><input type="checkbox" disabled readonly="readonly" /></li>
                            <li class="hg_padd center_txt" style="width:4%">
                                <div class="middle_check">
                                    <a title="Delete link" href="javascript:void(0)" onclick="comms_docs_delete(\''.$value['_id'].'\')">
                                        <span class="icon_remove2"></span>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    ';
                    $i++;
                }
            }
        }
        if($i<3)
        {
            for($j=$i;$j<3;$j++)
            {
                $bg=($j%2==0? 'bg1':'bg2');
                $html .='<ul class="ul_mag clear '.$bg.'"></ul>';
            }
        }
        echo $html;
        die;
    }
    //Khi $field thay đổi thì các field này cũng thay đổi theo
    public function arr_associated_data($field = '', $value = '', $ids = '') {
        $arr_return[$field] = $value;
        //company
        if ($field == 'contact_name') {
            $this->selectModel('Contact');
            $contact = $this->Contact->select_one(array('_id' => new MongoId($ids)),array('first_name', 'last_name', 'email'));
            $arr_return['contact_name'] = (isset($contact['first_name']) ? $contact['first_name'] : '').' '.(isset($contact['last_name']) ? $contact['last_name'] : '');
            $arr_return['contact_id'] = $contact['_id'];
            $arr_return['email']  = isset($contact['email']) ? $contact['email'] : '';
        }
        if ($field == 'company_name') {
            $from = 'Company';
            $setup_ass = array(
                '_id' => 'contact_id',
                'first_name' => 'contact_name',
                'last_name' => 'last_name',
                'title' => 'contact_title',
                'email' => 'email',
                'id' => 'company_id',
                'company' => 'company_name',
            );
        }
        return $arr_return;
    }

    public function entry_search() {
        if (!empty($this->data) && $this->request->is('ajax')) {

            $post = $this->data['Communication'];
            $cond = array();

            $post = $this->Common->strip_search($post);

            if( strlen($post['code']) > 0 )$cond['code'] = (int)$post['code'];
            if( strlen($post['comms_type']) > 0 )$cond['comms_type'] = $post['comms_type'];
            if( strlen($post['contact_title']) > 0 )$cond[''] = $post['contact_title'];
            if( strlen($post['contact_name']) > 0 )$cond['contact_name'] = new MongoRegex('/' . trim($post['contact_name']).'/i');
            if( strlen($post['last_name']) > 0 )$cond['last_name'] = new MongoRegex('/' . trim($post['last_name']).'/i');
            if( strlen($post['email']) > 0 )$cond['email'] = new MongoRegex('/' . trim($post['email']).'/i');
            if( strlen($post['comms_date']) > 0 )$cond['comms_date'] = $post['comms_date'];

            if( strlen($post['sign_off']) > 0 )$cond['sign_off'] = $post['sign_off'];
            if( strlen($post['contact_from']) > 0 )$cond['contact_from'] = $post['contact_from'];
            if( strlen($post['position']) > 0 )$cond['position'] = $post['position'];
            if( strlen($post['salutation']) > 0 )$cond['salutation'] = $post['salutation'];
            if( strlen($post['name']) > 0 )$cond['name'] = $post['name'];

            $this->selectModel('Communication');
            $this->identity($cond);
            $tmp = $this->Communication->select_one($cond);
            if( $tmp ){
                $this->Session->write('communications_entry_search_cond', $cond);

                $cond['_id'] = array('$ne' => $tmp['_id']);
                $tmp1 = $this->Communication->select_one($cond);
                if( $tmp1 ){
                    echo 'yes'; die;
                }
                echo 'yes_1_'.$tmp['_id']; die; // chỉ có 1 kết quả thì chuyển qua trang entry luôn
            }else{
                echo 'no'; die;
            }

            echo 'ok';
            die;
        }
        $array_communication_type = array('Email','Note','Message','Letter','Fax');
        $this->set('arr_communication_type', $array_communication_type);
        $this->set('arr_business_type', $this->get_setting_option('communication_business_type'));
        $this->set('arr_industry', $this->get_setting_option('communication_industry'));
        $this->set('arr_size', $this->get_setting_option('communication_size'));

        $this->set('set_footer', 'footer_search');
        $this->set('address_country', $this->country());
        $this->set('address_province', $this->province("CA"));
    }
    function save_email_setting()
    {
        if(!empty($_POST))
        {
            $config_email = array();
            if(isset($_POST['config_email']) && !empty($_POST['config_email']))
                $config_email['email'] = $_POST['config_email'];
            if(isset($_POST['pass']) && !empty($_POST['pass']) ){
                $config_email['password'] = $_POST['pass'];
            }
            if(empty($config_email))
            {
                echo 'Enter valid info.';
                die;
            }
            $this->selectModel('Contact');
            $contact['_id'] = new MongoId($this->opm->user_id());
            $contact['email_setting'] = $config_email;
            if($this->Contact->save($contact)) {
                $arrContact = $this->Session->read('arr_user');
                $arrContact['email_setting'] = $config_email;
                $this->Session->write('arr_user', $arrContact);
                echo 'ok';
                die;
            }
        }
        die;
    }
    public function send_email($use_default_email='') {
        App::uses('CakeEmail', 'Network/Email');
        $email = new CakeEmail();
        //Nếu user tự dùng mail của mình
        if($use_default_email==''){
            $config_set = array();
            $username = '';
            $password = '';
            //Truong hop tat ca thong tin da save vao contact
            if(isset($_POST['email_one_use'])&&isset($_POST['password_one_use'])
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
                if($email_type!='GMAIL'){
                    $config_set['config_type'] = 'smtp';
                    $this->selectModel('Setting');
                    $email_setting = $this->Setting->select_one(array('setting_value' => 'email_setting'), array('option'));
                    foreach($email_setting['option'] as $value){
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
        if(is_object($attachments)&&$attachments->count()){
            foreach($attachments as $value)
                $files[] = getcwd().$value['path'];
        }
        $this->email_image_filter($arr_email['content'],$files);
        if(!empty($files))
            $email->attachments($files);
        $email->emailFormat('both');
        $email->template('default');
        try{
            if ($email->send($arr_email['content'])) {
                $this->set_status_sent();
                if(isset($arr_email['rfq_email'])){
                    $this->selectModel('Quotation');
                    $quotation = $this->Quotation->select_one(array('_id' => $arr_email['module_id']),array('rfqs'));
                    $quotation['rfqs'][$arr_email['rfq_id']]['rfq_status'] = 'Sent';
                    $this->Quotation->save( $quotation);
                } else if( $arr_email['module'] == 'Salesinvoice' ) {
                    $this->selectModel('Salesinvoice');
                    $invoice = $this->Salesinvoice->select_one(array('_id' => $arr_email['module_id']),array('job_id'));
                    if( is_object($invoice['job_id']) ) {
                        $this->selectModel('Job');
                        $this->Job->save(array('_id' => $invoice['job_id'], 'status' => 'Completed'));
                    }
                    $this->Salesinvoice->save(array('_id' => $arr_email['module_id'],'invoice_status' => 'Invoiced'));
                }
                echo 'sent';
                die;
            }
        } catch(SocketException  $e){
            $message = $e->getMessage();
            if( strpos($message, 'authentication failed') !== false ) {
                $message .= '<br />Wrong username or password.';
            }
            echo $message;
            die;
        }
        die;
    }

    //set trạng thái đã gửi email
    public function set_status_sent()
    {
        if ($this->get_id() != '') {
            $this->opm->update($this->get_id(), 'comms_status', 'Sent');
            $this->opm->update($this->get_id(), 'comms_date', new MongoDate());

        }
    }

    public function check_user_email_setting() {
        $arr_email_setting = $this->get_user_email_setting();
        if (isset($arr_email_setting['password']) && $arr_email_setting['password'] != '') {
            echo '1';
        }
        else
            echo '0';
        die;
    }

    public function get_user_email_setting()
    {
        $this->selectModel('Contact');
        $arr_user = $this->Contact->select_one(array('_id' => new MongoId($this->opm->user_id())), array('email_setting'));
        if (isset($arr_user['email_setting']) && !empty($arr_user['email_setting']))
        {
            return $arr_user['email_setting'];
        }
        else
            return array();
        die;
    }

    public function quick_view() {
        $this->selectModel('Communication');
        // set default sort
        $order = array('_id' => -1);
        $cond = array();
        // seach or sort
        if ($this->request->is('ajax')) {
            if ($_REQUEST['contact_name'] != '') {
                $cond['first_name'] = new MongoRegex('/' . $_REQUEST['contact_name'] . '/i');
            }
            // end seach
            // sort
            $sort_key = $_REQUEST['sort_key'];
            $sort_type = $_REQUEST['sort_type'];
            // kiem tran sort type roi gan gia tri "asc = 1;  desc = -1 "
            if ($sort_type == 'desc') {
                $sort = -1;
            }
            if ($sort_type == 'asc') {
                $sort = 1;
            }
            $order = array($sort_key => $sort);
            // end sort
        }
        // query
        $this->set('arr_comms', $this->Communication->select_all(array(
                    'arr_where' => $cond,
                    'arr_order' => $order
        )));
        // render ajax view
        if ($this->request->is('ajax')) {
            $this->render('quick_view_ajax');
        }
    }

	function comm_delete($comm_id) {
		$arr_save['_id'] = $comm_id;
		$arr_save['deleted'] = true;
		$error = 0;
		if (!$error) {
			$this->selectModel('Communication');
			if ($this->Communication->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->Communication->arr_errors_save[1];
			}
		}
		die;
	}

	public function add_comm_from_module_from_email($comms_type='',$id_mongo='',$model='',$controller='',$v_doc_id=''){
		$arr_save=array();
		$this->selectModel('Communication');
		$arr_save['code']=$this->Communication->get_auto_code('code');
		$arr_save['comms_type']=$comms_type!=''?$comms_type:'Note';
		$arr_total= array();
		$this->selectModel($model);
		if(isset($id_mongo)&&$id_mongo!=''){
			$arr_total=$this->$model->select_one(array('_id'=>new MongoId($id_mongo)));
		}
		$arr_save['company_id']=isset($arr_total['company_id'])?$arr_total['company_id']:'';
		$arr_save['company_name']=isset($arr_total['company_name'])?$arr_total['company_name']:'';
		$arr_save['created_from']=isset($controller)?$controller:'';
		$arr_save['contact_to_id']='';
		$arr_save['message_content']='';
		$arr_contact=array();
		$arr_contact1=array();
		$this->selectModel('Contact');
		$company_id=isset($arr_total['company_id'])?$arr_total['company_id']:'';
		if(isset($company_id)&&$company_id!='')
			$arr_contact=$this->Contact->select_one(array('company_id'=>new MongoId($company_id)));
		$arr_save['contact_from_id']=$this->opm->user_id();
		$arr_save['contact_from']=$this->opm->user_name();
		$arr_save['contact_id']=isset($arr_total['contact_id'])?$arr_total['contact_id']:'';
		$v_contact_id=isset($arr_save['contact_id'])?$arr_save['contact_id']:'';
		if(isset($v_contact_id)&&$v_contact_id!='')
			$arr_contact1=$this->Contact->select_one(array('_id'=>new MongoId($v_contact_id)));
		$arr_save['contact_name']=isset($arr_contact1['first_name'])?$arr_contact1['first_name']:'';
		$arr_save['last_name']=isset($arr_contact1['last_name'])?$arr_contact1['last_name']:'';
		$arr_save['email']=isset($arr_total['email'])?$arr_total['email']:'';
		$arr_save['phone']=isset($arr_total['phone'])?$arr_total['phone']:'';
		$arr_save['fax']=isset($arr_total['fax'])?$arr_total['fax']:'';
		$arr_save['job_number']=isset($arr_total['job_number'])?$arr_total['job_number']:'';
		$arr_save['job_name']=isset($arr_total['job_name'])?$arr_total['job_name']:'';
		if ($this->Communication->save($arr_save)) {
			$this->selectModel('DocUse');
			$arr_save_doc = array();
			$arr_save_doc['module'] = 'Communications';
			if($v_doc_id!='')
				$arr_save_doc['doc_id'] =new MongoId($v_doc_id);
			$arr_save_doc['module_id'] = new MongoId($this->Communication->mongo_id_after_save);
			if (!$this->DocUse->save($arr_save_doc)) {
				echo 'Error add new. Please contact IT developer. Error: ' . $this->DocUse->arr_errors_save[1];
			}
			$this->redirect('/communications/entry/'. $this->Communication->mongo_id_after_save);
		}
		$this->redirect('/communications/entry');
	}

    function create_note()
    {
        $arr_save['code'] = $this->opm->get_auto_code('code');
        $arr_save['comms_type'] = 'Note';
        $arr_save['comms_date'] = new MongoDate();
        $arr_save['note'] = '';
        $arr_save['contact_from_id'] = $this->opm->user_id();
        $arr_save['contact_from'] = $this->opm->user_name();
        $arr_save['company_name'] = '';
        $arr_save['company_id'] = '';
        $arr_save['contact_name'] = '';
        $arr_save['contact_id'] = '';
        if($this->opm->save($arr_save))
        {
            $id = $this->opm->mongo_id_after_save;
            echo json_encode(array('status'=>'ok','id'=>$id));
        }
        die;
    }

    public function swith_options($keys = '')
    {
        parent::swith_options($keys);
        if ($keys == 'emails') {
            $arr_where = array();
            $arr_where['comms_type'] = array('values' => 'Email', 'operator' => '=');
            $this->Session->write($this->name . '_where', $arr_where);
            echo URL . '/communications/lists';
        } else if ($keys == 'letters') {
            $arr_where = array();
            $arr_where['comms_type'] = array('values' => 'Letter', 'operator' => '=');
            $this->Session->write($this->name . '_where', $arr_where);
            echo URL . '/communications/lists';
        } else if ($keys == 'faxes') {
            $arr_where = array();
            $arr_where['comms_type'] = array('values' => 'Fax', 'operator' => '=');
            $this->Session->write($this->name . '_where', $arr_where);
            echo URL . '/communications/lists';
        } else if ($keys == 'notes') {
            $arr_where = array();
            $arr_where['comms_type'] = array('values' => 'Note', 'operator' => '=');
            $this->Session->write($this->name . '_where', $arr_where);
            echo URL . '/communications/lists';
        } else if ($keys == 'messages') {
            $arr_where = array();
            $arr_where['comms_type'] = array('values' => 'Message', 'operator' => '=');
            $this->Session->write($this->name . '_where', $arr_where);
            echo URL . '/communications/lists';
        } else if($keys == 'emails_created_today' || $keys == 'letter_created_today'  || $keys == 'faxed_created_today'
             || $keys == 'notes_created_today'  || $keys == 'messages_created_today'){
            $arr_where = array();
            $current_date = strtotime(date("Y-m-d"));
            $current_date_end = $current_date + DAY - 1;
            $arr_where['comms_date']['>='] = array('values' => new MongoDate($current_date), 'operator' => 'day>=');
            $arr_where['comms_date']['<='] = array('values' => new MongoDate($current_date_end), 'operator' => 'day<=');
            if($keys == 'emails_created_today')
                $arr_where['comms_type'] = array('values' => 'Email', 'operator' => '=');
            if($keys == 'letter_created_today')
                $arr_where['comms_type'] = array('values' => 'Letter', 'operator' => '=');
            if($keys == 'faxed_created_today')
                $arr_where['comms_type'] = array('values' => 'Fax', 'operator' => '=');
            if($keys == 'notes_created_today')
            $arr_where['comms_type'] = array('values' => 'Note', 'operator' => '=');
            if($keys == 'messages_created_today')
                $arr_where['comms_type'] = array('values' => 'Message', 'operator' => '=');
            $this->Session->write($this->name . '_where', $arr_where);
            echo URL . '/' . $this->params->params['controller'] . '/lists';
        }
        else
            echo URL.'/communications/lists';
        die;
    }
     public function ajax_save() {
        if (isset($_POST['field']) && isset($_POST['value']) && isset($_POST['func']) && !in_array((string) $_POST['field'], $this->opm->arr_autocomplete())) {

            if ($_POST['func'] == 'add') {
                $ids = $this->opm->add($_POST['field'], $_POST['value']);
                $newid = explode("||", $ids);
                $this->Session->write($this->name . 'ViewId', $newid[0]);
            } else if ($_POST['func'] == 'update' && isset($_POST['ids'])) {
                $ids = $this->opm->update($_POST['ids'], $_POST['field'], $_POST['value']);
                $this->Session->write($this->name . 'ViewId', $_POST['ids']);
            }
            echo $ids;
        } else
            echo 'error';
        die;
    }

    public function view_minilist(){
        if(!isset($_GET['print_pdf'])){
        $arr_where = array();
        $communications = $this->opm->select_all(array(
                                                'arr_where' => $arr_where,
                                                'arr_field' => array('comms_type','comms_date','contact_from','contact_name','company_name','descriptation'),
                                                'arr_order' => array('_id'=>1),
                                                'limit'     => 2000
                                                ));
            if($communications->count() > 0){
                $html = '';
                $arr_data = array();
                $i=0;
                foreach($communications as $key => $communication){
                    $html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">';
                    $html .= '<td>'.(isset($communication['comms_type']) ? $communication['comms_type'] : '') .'</td>';
                    $html .= '<td>'.(isset($communication['comms_date']) && is_object($communication['comms_date']) ? date('m/d/Y',$communication['comms_date']->sec):'') .'</td>';
                    $html .= '<td>'.(isset($communication['contact_from']) ? $communication['contact_from'] : '') .'</td>';
                    $html .= '<td>'.(isset($communication['contact_name']) ? $communication['contact_name'] : '') .'</td>';
                    $html .= '<td>'.(isset($communication['company_name']) ? $communication['company_name'] : '') .'</td>';
                    $html .= '<td>'.(isset($communication['descriptation']) ? $communication['descriptation'] : '') .'</td>';
                    $html .= '</tr>';
                    $i++;
                }
                 $html .='<tr class="last">
                            <td colspan="6" class="bold_text right_none">'.$i.' record(s) listed.</td>
                            </tr>';
                $arr_data['title'] = array('Type','Date','From / By','To','Company','Details');
                $arr_data['content'] = $html;
                $arr_data['report_name'] = 'Communication Mini  Listing';
                $arr_data['report_file_name']='Co_'.md5(time());
                $arr_data['report_orientation'] = 'landscape';
            }
            Cache::write('communication_minilist', $arr_data);
        }else
            $arr_data = Cache::read('communication_minilist');
            $this->render_pdf($arr_data);
    }

    public function entry_search_all(){
        $this->Session->delete('Communications_lists_search_sort');
        $this->redirect('/communications/lists');
    }
}