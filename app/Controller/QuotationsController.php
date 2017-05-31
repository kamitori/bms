<?php

// Attach lib cal_price
App::import('Vendor', 'cal_price/cal_price');

App::uses('AppController', 'Controller');

class QuotationsController extends AppController{

    var $name = 'Quotations';
    public $helpers = array();
    public $opm; //Option Module
    public $cal_price; //Option cal_price
    public $modelName = 'Quotation';

    public function beforeFilter() {
        parent::beforeFilter();
        $this->set_module_before_filter('Quotation');
    }

    public function ajax_save(){
        $continue = true;
        if ($_POST['field'] == 'quotation_status' ) {
            if( $_POST['value'] == 'In progress' ){
                $quotation = $this->opm->select_one(array('_id'=> new MongoId($this->get_id())),array('quotation_status','payment_terms'));
                if($quotation['quotation_status'] == 'Approved'){
                    $quotation['quotation_date'] = new MongoDate();
                    $quotation['payment_due_date'] = new MongoDate($quotation['quotation_date']->sec + DAY * $quotation['payment_terms']);
                    $quotation['quotation_status'] = $_POST['value'];
                    $this->opm->save($quotation);
                    $continue = false;
                    echo (string)$quotation['_id'].'||'.$_POST['value'];
                    die;
                }
            } else if(in_array($_POST['value'], array('Submitted','Approved', 'Manager Approved', 'Amended', 'Cancelled', 'On Hold', 'Rejected'))){
                $id = $this->get_id();
                $quotation = $this->opm->select_one(array('_id'=> new MongoId($id)),array('company_id','api_key','products','sum_sub_total','sum_tax','sum_amount', 'shipping_cost'));
                if( !$this->check_permission('quotations_@_submit_quotation_@_edit') ) {
                    if(isset($quotation['company_id']) && is_object($quotation['company_id'])){
                        $this->selectModel('Salesaccount');
                        $company = $this->Salesaccount->select_one(array('company_id' => $quotation['company_id']), array('quotation_limit'));
                        if( isset($company['quotation_limit']) && $quotation['sum_amount'] > (float)$company['quotation_limit'] ){
                            echo 'need_mail|This quotation is over the limit, please select one of the manager to approval.';
                            die;
                        }
                    }
                    if( $_POST['value'] != 'Submitted' ) {
                        echo 'need_mail|You do not have permission to change this status. Please select one of the manager to change this.';
                        die;
                    }
                }
                if( in_array($_POST['value'], array('Submitted','Approved', 'Manager Approved'))
                        && isset($quotation['api_key']) &&  strlen($quotation['api_key']) == 24){
                    App::import('Vendor', 'wtapis/wtapis');
                    $wtapis = new wtapis;
                    $minimum = $this->get_minimum_order();
                    if($quotation['sum_sub_total']<$minimum){
                        $quotation = $this->get_minimum_order_adjustment($quotation,$minimum);
                    }
                    $wtapis->functionality_method($id,'update', 'rfq',$quotation);
                }
            }
        }
        if($continue)
            parent::ajax_save();
    }

    function send_mail_manager(){
        if(isset($_POST['manager_id'])){
            $id = $this->get_id();
            $data = $this->opm->select_one(array('_id' => new MongoId($id)), array('company_id','company_name','contact_id','contact_name','job_number','job_name','job_id','code'));
            $this->selectModel('Contact');
            $contact = $this->Contact->select_one(array('_id' => new MongoId($_POST['manager_id'])),array('email','first_name','last_name'));
            $this->selectModel('Communication');
            $message = 'There is an outstanding quotation need your approval';
            if( isset($_POST['wish_status']) && !empty($_POST['wish_status'])
                && !in_array($_POST['wish_status'], array('Submitted', 'Approved', 'Manager Approved')) ){
                $message = 'There is an outstanding quotation need you change to '.$_POST['wish_status'];
            }
            $arr_save = array(
                'deleted'           => false,
                'code'              => $this->Communication->get_auto_code('code'),
                'comms_type'        => 'Email',
                'comms_date'        => new MongoDate(),
                'comms_status'      => 'Draft',
                'sign_off'          => 0,
                'include_signature' => 0,
                'email_cc'          => 'jobtraq@anvydigital.com',
                'email_bcc'         => '',
                'identity'          => '',
                'internal_notes'    => '',
                'company_id'        => (isset($data['company_id'])&&$data['company_id']!='' ? new MongoId($data['company_id']) : ''),
                'company_name'      => (isset($data['company_name']) ? $data['company_name'] : ''),
                'module'            => 'Quotation',
                'module_id'         => new MongoId($data['_id']),
                'content'           => '',
                'contact_id'        => (isset($contact['_id'])? $contact['_id'] : ''),
                'contact_name'      => (isset($contact['first_name']) ?$contact['first_name'].' ' : '').(isset($contact['last_name']) ?$contact['last_name'] : ''),
                'last_name'         => '',
                'position'          => '',
                'salutation'        => '',
                'toother'           => '',
                'quotation_submit'  => 1,
                'email'             => (isset($contact['email']) ? $contact['email'] : ''),
                'phone'             =>  '',
                'fax'               =>  '',
                'job_number'        => (isset($data['job_number']) ? $data['job_number'] : ''),
                'job_name'          => (isset($data['job_name']) ? $data['job_name'] : ''),
                'job_id'            => (isset($data['job_id'])&&$data['job_id']!='' ? new MongoId($data['job_id']) : ''),
                'name'              => "JobTraq - Quotation #{$data['code']}",
                'content'           => "{$message}. Enter this <a href='".URL."/quotations/entry/{$id}'>link</a> to check.<br />"
                );
            $this->Communication->save($arr_save);
            echo URL.'/communications/entry/'.$this->Communication->mongo_id_after_save.'#send';
        }
        die;
    }


    public function rebuild_setting($arr_setting=array()){
        parent::rebuild_setting();
        $params = isset($this->params->params['pass'][0]) ? $this->params->params['pass'][0] : null;
        $valid = false;
        if($this->params->params['action'] =='rfqs'){
            if(!$this->check_permission($this->name.'_@_rfqs_tab_@_delete')){
                $arr_settings = $this->opm->arr_settings;
                unset($arr_settings['relationship']['rfqs']['block']['rfqs']['delete']);
                $this->opm->arr_settings = $arr_settings;
            }
            if(!$this->check_permission($this->name.'_@_rfqs_tab_@_add')){
                $arr_settings = $this->opm->arr_settings;
                foreach($arr_settings['relationship']['rfqs']['block']['rfqs']['field'] as $key=>$value)
                    $arr_settings['relationship']['rfqs']['block']['rfqs']['field'][$key]['lock'] = 1;
                $arr_settings['relationship']['rfqs']['block']['rfqs']['field']['internal_notes']['block']['internal_notes']['lock'] = 1;
                $arr_settings['relationship']['rfqs']['block']['rfqs']['field']['details_for_request']['block']['details_for_request']['lock'] = 1;
                $this->opm->arr_settings = $arr_settings;
            }
        } else if($this->params->params['action'] == 'entry' || $valid = in_array($params,array('line_entry','text_entry'))){
            if(!$this->check_permission('quotations_@_entry_@_edit')){
                if($valid){
                    $this->opm->set_lock_option('line_entry', 'products');
                    $this->opm->set_lock_option('text_entry', 'products');
                    $this->set('lock_product', true);
                } else {
                    $this->opm->set_lock(array('name'), 'out');
                    $this->set('address_lock', '1');
                }
                return;
            }
            $id = $this->get_id();
            if($id != ''){
                $query = $this->opm->select_one(array('_id'=> new MongoId($id)),array('job_id','quotation_status'));
                $continue = true;
                if($query['quotation_status'] != 'In progress'){
                    unset($this->opm->arr_settings['relationship']['line_entry']['block']['products']['add']);
                    unset($this->opm->arr_settings['relationship']['text_entry']['block']['products']['add']);
                } else {
                    unset($this->opm->arr_settings['relationship']['line_entry']['block']['products']['custom_box_top']);
                    unset($this->opm->arr_settings['relationship']['text_entry']['block']['products']['custom_box_top']);
                }
                if(isset($query['job_id'])&&is_object($query['job_id'])){
                    $this->selectModel('Job');
                    $job = $this->Job->select_one(array('_id'=> new MongoId($query['job_id'])),array('status','work_end'));
                    if($continue && isset($job['status']) && $job['status'] == 'Completed'){
                        if($valid){
                            $this->opm->set_lock_option('line_entry', 'products');
                            $this->opm->set_lock_option('text_entry', 'products');
                            $this->set('lock_product', true);
                        } else {
                            $this->opm->set_lock(array('name'), 'out');
                            $this->set('address_lock', '1');
                        }
                        $continue = false;
                    }
                }
            }
        }
    }

    function check_password(){
        $this->selectModel('Stuffs');
        $change = $this->Stuffs->select_one(array('value' => 'Changing Code'));
        $wrong = true;
        if(md5($_POST['password']) == $change['password']){
            $wrong = false;
        }
        if(!$wrong){
            echo 'success';
            if(isset($_POST['open'])){
                $_id = $this->checkClosingMonth();
                if(!$_id) {
                    $this->Session->setFlash('The closing month has not been setup yet.','default',array('class'=>'flash_message'));
                    die;
                }
                $this->Session->write('JobsOpen_'.$_id,1);
                $this->Session->setFlash('This '.$this->name.' has been opened.','default',array('class'=>'flash_message'));
            }
            $arr_save = array();
            foreach($_POST as $key => $post){
                if(strpos($key, '_save') !== false){
                    $arr_save[str_replace('_save', '', $key)] = $post;
                }
            }
            if(!empty($arr_save)){
                $arr_save['_id'] = new MongoId($this->get_id());
                $this->opm->save($arr_save);
            }
        } else {
            echo 'wrong_pass';
        }
        die;
    }


    function get_all_permission(){
        $arr_permission = parent::get_all_permission();
        $id = $this->get_id();
        if( !empty($id) ) {
            $this->selectModel('Quotation');
            $query = $this->Quotation->select_one(array('_id'=> new MongoId($id)),array('quotation_status'));
            if($query['quotation_status'] == 'In progress'){
                foreach(array('print_quotation','print_quotation_exclude_quantity_and_price_columms','print_quotation_include_category_headings', 'print_quotation_include_category_headings_only','email_quotation','email_quotation_exclude_quantity_and_price_columms','email_quotation_include_category_headings','email_quotation_include_catgory_headings_only','create_email') as $value){
                    if(isset($arr_permission['quotations_@_options_@_'.$value]))
                        unset($arr_permission['quotations_@_options_@_'.$value]);
                }
            }
        }
        return $arr_permission;
    }

    //Các điều kiện mở/khóa field trong entry
    public function check_lock() {
        if ($this->get_id() != '') {
            $arr_tmp = $this->opm->select_one(array('_id' => new MongoId($this->get_id())),array('quotation_status'));
            if (isset($arr_tmp['quotation_status'])&& !in_array($arr_tmp['quotation_status'], array('In progress','Amended')))
                return true;
        } else
            return false;
    }


    public function check_limit(){
        $quotation = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())),array('company_id','sum_amount'));
        $this->selectModel('Salesaccount');
        $salesaccount = array();
        if(isset($quotation['company_id'])&&is_object($quotation['company_id']))
            $salesaccount = $this->Salesaccount->select_one(array('company_id'=>$quotation['company_id']));
        if(isset($salesaccount['quotation_limit'])
           && (float)$quotation['sum_amount']>=(float)$salesaccount['quotation_limit'])
            return true;
        return false;
    }


    public function entry() {
        $mod_lock = '0';
        if ($this->check_lock()) {
            if($this->check_permission($this->name.'_@_revise_status_@_edit'))
                $this->opm->set_lock(array('quotation_status'), 'out');
            else
                $this->opm->set_lock(array(), 'out');
            $mod_lock = '1';
            $this->set('address_lock', '1');
            $this->opm->set_lock_option('line_entry', 'products');
            $this->opm->set_lock_option('text_entry', 'products');
        }

        $arr_set = $this->opm->arr_settings;
        // Get value id
        $iditem = $this->get_id();
        if ($iditem == '')
            $iditem = $this->get_last_id();
        $this->set('iditem', $iditem);
        //Load record by id
        $arr_tmp = array();
        if ($iditem != '') {
            $arr_tmp = $this->opm->select_one(array('_id' => new MongoId($iditem)),array('quotation_status','code','quotation_type','date_modified','created_by','modified_by','description','company_name','company_id','contact_name','contact_id','phone','email','quotation_date','our_rep','our_rep_id','our_csr','our_csr_id','invoice_address','shipping_address','payment_due_date','quotation_status','payment_terms','tax','taxval','customer_po_no','heading','name','job_name','job_number','job_id','salesorder_name','salesorder_number','salesorder_id','sum_amount','modified_by_name','created_by_name'));
            if(!isset($arr_tmp['_id'])){
                $query = $this->opm->select_one(array('deleted' => 'no_search', '_id' => new MongoId($iditem)),array('deleted','date_modified','modified_by','code'));
                if( $query['deleted'] ){
                    $this->selectModel('Contact');
                    $contact = $this->Contact->select_one(array('_id'=> $query['modified_by']),array('full_name'));
                    if(!isset($contact['full_name']))
                        $contact['full_name'] = 'System Admin';
                    echo "This Quotation #{$query['code']} has been deleted by {$contact['full_name']} at ".date('d M, Y h:i:s',$query['date_modified']->sec).'. Click this <a href="'.URL.'/quotations/lasts'.'" style="color: blue;">link</a> to go to the lastest Quote.';
                    $this->autoRender = false;
                    return;
                }
            }
            foreach ($arr_set['field'] as $ks => $vls) {
                foreach ($vls as $field => $values) {
                    if (isset($arr_tmp[$field])) {
                        $arr_set['field'][$ks][$field]['default'] = $arr_tmp[$field];
                        if (in_array($field, $arr_set['title_field']))
                            $item_title[$field] = $arr_tmp[$field];

                        if (preg_match("/_date$/", $field) && is_object($arr_tmp[$field]))
                            $arr_set['field'][$ks][$field]['default'] = date('m/d/Y', $arr_tmp[$field]->sec);
                        //chế độ lock, hiện name của các relationship custom
                        else if (($field == 'company_name' || $field == 'contact_name') && $mod_lock == '1')
                            $arr_set['field'][$ks][$field]['default'] = $arr_tmp[$field];
                    }
                }
            }
            $arr_set['field']['panel_1']['mongo_id']['default'] = $iditem;
            $this->Session->write($this->name . 'ViewId', $iditem);

            //BEGIN custom
            if (isset($arr_set['field']['panel_1']['code']['default']))
                $item_title['code'] = $arr_set['field']['panel_1']['code']['default'];
            else
                $item_title['code'] = '1';
            $this->set('item_title', $item_title);

            //custom list tax
            $arr_options_custom['tax'] = '';
            $this->selectModel('Tax');
            $arr_options_custom['tax'] = $this->Tax->tax_select_list();
            $this->set('arr_options_custom', $arr_options_custom);
            //END custom
            //show footer info
            $this->show_footer_info($arr_tmp);


            //add, setup field tự tăng
        }else {
            $this->redirect(URL.'/quotations/add');
        }
        $this->set('query', $arr_tmp);
        $this->set('arr_settings', $arr_set);
        $this->sub_tab_default = 'line_entry';
        $this->sub_tab('', $iditem);
        $this->set_entry_address($arr_tmp, $arr_set);
        parent::entry();
    }

    public function entry_test() {
        $iditem = $this->get_id();
        if(empty($iditem))
            $iditem = $this->get_last_id();
        $this->set('iditem', $iditem);
        $this->Session->write($this->name . 'ViewId', $iditem);
        $this->selectModel('Quotation');
        $arr_field = array('code','company_name','company_id','contact_id','contact_name','phone','email','quotation_date','our_rep','our_rep_id','our_csr','our_csr_id','invoice_address','shipping_address','payment_due_date','quotation_status','payment_terms','taxval','tax','taxtext','customer_po_no','heading','name','job_name','job_number','job_id','salesorder_name','salesorder_number','salesorder_id');
        $query = $this->Quotation->select_one(array('_id' => new MongoId($iditem)),$arr_field);
        foreach($arr_field as $field){
            if(!isset($query[$field]))
                $query[$field] = '';
        }
        $this->set('query',$query);
        $lock = array();
        if (isset($query['quotation_status'])&&$query['quotation_status'] != 'In progress') {
            if($this->check_permission($this->name.'_@_revise_status_@_edit'))
                $lock['exc'][] = 'quotation_status';
            $this->opm->set_lock_option('line_entry', 'products');
            $this->opm->set_lock_option('text_entry', 'products');
        }
        $this->set('lock',$lock);
        $arr_set = $this->opm->arr_settings;
        // Get value id
        $this->set('arr_settings', $arr_set);
        $this->sub_tab_default = 'line_entry';
        $this->sub_tab('', $iditem);

        //Combobox
        $this->selectModel('Tax');
        $this->selectModel('Setting');
        $arr_combobox['tax'] = $this->Tax->tax_select_list();
        $arr_combobox['country'] = $this->country();
        if (isset($query['invoice_address'][0]['invoice_country_id']))
            $arr_combobox['invoice_province_state'] = $this->province($query['invoice_address'][0]['invoice_country_id']);
        else
            $arr_combobox['invoice_province_state'] = $this->province();

        if (isset($query['shipping_address'][0]['shipping_country_id']))
            $arr_combobox['shipping_province_state'] = $this->province($query['shipping_address'][0]['shipping_country_id']);
        else
            $arr_combobox['shipping_province_state'] = $this->province();
        foreach(array('quotations_type','quotations_status','salesinvoices_payment_terms') as $combobox){
            $arr_combobox[$combobox] = $this->Setting->select_option_vl(array('setting_value' => $combobox));
        }
        $this->set('arr_combobox', $arr_combobox);
        parent::entry();
    }

    public function lists(){
        $this->set('_controller',$this);
        $this->selectModel('Quotation');
        $limit = LIST_LIMIT;
        $skip = 0;
        $sort_field = '_id';
        $sort_type = 1;
        if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
            if( $_POST['sort']['type'] == 'desc' ){
                $sort_type = -1;
            }
            $sort_field = $_POST['sort']['field'];
            $this->Session->write('Quotations_lists_search_sort', array($sort_field, $sort_type));

        }elseif( $this->Session->check('Quotations_lists_search_sort') ){
            $session_sort = $this->Session->read('Quotations_lists_search_sort');
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
        $arr_quotes = $this->Quotation->select_all(array(
            'arr_where' => $cond,
            'arr_order' => $arr_order,
            'arr_field' => array('code','quotation_type','company_name','company_id','contact_name','contact_id','phone','quotation_date','our_rep','our_rep_id','quotation_status','salesorder_id','salesorder_name','sum_sub_total', 'shipping_cost'),
            'limit' => $limit,
            'skip' => $skip
        ));
        $this->set('arr_quotes', $arr_quotes);


        $total_page = $total_record = $total_current = 0;
        if( is_object($arr_quotes) ){
            $total_current = $arr_quotes->count(true);
            $total_record = $arr_quotes->count();
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

    public function arr_associated_data($field = '', $value = '', $valueid = '',$fieldopt='') {
        $arr_return[$field] = $value;
        $tmp_data = array();
		if(isset($_POST['arr']) && is_string($_POST['arr']) && $_POST['arr']!='')
			$tmp_data = (array)json_decode($_POST['arr']);
        if(isset($tmp_data['keys'])){
            if( ($tmp_data['keys']=='update' || $tmp_data['keys']=='add')
                &&!$this->check_permission($this->name.'_@_entry_@_edit')){
                echo 'You do not have permission on this action.';
                die;
            }
        }
        /**
         * Chọn Company
         */
        if ($field == 'company_name' && $valueid != '') {
            $arr_return = array(
                'company_name' => '',
                'company_id' => '',
                'contact_name' => '',
                'contact_id' => '',
                'our_csr' => '',
                'our_csr_id' => '',
                'our_rep' => '',
                'our_rep_id' => '',
                'phone' => '',
                'email' => '',
                'invoice_address' => array(),
                'shipping_address' => array(),
            );
            //change company
            $arr_return['company_name'] = $value;
            $arr_return['company_id'] = new MongoId($valueid);
            $query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())));
            $arr_return['name'] = $query['code'].'-'.$value;
            $this->selectModel('Salesaccount');
            $salesaccount = $this->Salesaccount->select_one(array('company_id' => $arr_return['company_id']));
            $arr_return['payment_terms'] = (isset($salesaccount['payment_terms']) ? $salesaccount['payment_terms'] : 0);
            $arr_return['payment_terms_id'] = (isset($salesaccount['payment_terms_id']) ? $salesaccount['payment_terms_id'] : 0);
            //find contact and more from Company
            $this->selectModel('Company');
            $arr_company = $this->Company->select_one(array('_id' => new MongoId($valueid)));

            $this->selectModel('Contact');
            $arr_contact = $arrtemp = array();
            // is set contact_default_id
            if (isset($arr_company['contact_default_id']) && is_object($arr_company['contact_default_id'])) {
                $arr_contact = $this->Contact->select_one(array('_id' => $arr_company['contact_default_id']));

                // not set contact_default_id
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




            //change our_csr
            if (isset($arr_company['our_csr']) && isset($arr_company['our_csr_id']) && $arr_company['our_csr_id'] != '') {
                $arr_return['our_csr_id'] = $arr_company['our_csr_id'];
                $arr_return['our_csr'] = $arr_company['our_csr'];
            }else{
				$arr_return['our_csr_id'] = $this->opm->user_id();
				$arr_return['our_csr'] = $this->opm->user_name();
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


            if (isset($arr_company['email']) && $arr_company['email']!='')
                $arr_return['email'] = $arr_company['email'];
            elseif (isset($arr_contact['email']))
                $arr_return['email'] = $arr_contact['email'];
            elseif (!isset($arr_contact['email']))
                $arr_return['email'] = '';

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

			//change tax
			if((!isset($arr_return['invoice_address'][0]['invoice_province_state_id']) || $arr_return['invoice_address'][0]['invoice_province_state_id']=='') && isset($arr_return['invoice_address'][0]['invoice_province_state'])){
				$province_name = $arr_return['invoice_address'][0]['invoice_province_state'];
				$arr_province = $this->province_reverse('CA');
				if(isset($arr_province[$province_name]))
					$arr_return['invoice_address'][0]['invoice_province_state_id'] = $arr_province[$province_name];
			}

            $this->selectModel('Salesaccount');
            if(isset($salesaccount['tax_code_id'])&& $salesaccount['tax_code_id']!=''){
                $keytax = $salesaccount['tax_code_id'];
                $this->selectModel('Tax');
                $arr_tax = $this->Tax->tax_list();
                $arr_tax_text = $this->Tax->tax_select_list();
                $arr_return['tax'] = isset($arr_tax_text[$keytax]) ? $keytax : 'Not used';
                $arr_return['taxval'] = (float)(isset($arr_tax[$keytax]) ? $arr_tax[$keytax] : 0);
                $arr_return['taxtext'] = isset($arr_tax_text[$keytax]) ? $arr_tax_text[$keytax] : '';

            }else if(isset($arr_return['invoice_address'][0]['invoice_province_state_id'])){
				$keytax = $arr_return['invoice_address'][0]['invoice_province_state_id'];
				$this->selectModel('Tax');
				$arr_tax = $this->Tax->tax_list();
				$arr_tax_text = $this->Tax->tax_select_list();
				$arr_return['tax'] = $keytax;
				$arr_return['taxval'] = (float)$arr_tax[$keytax];
				$arr_return['taxtext'] = $arr_tax_text[$keytax];
			}
            $arr_return['products'] = $this->update_all_option('products',array('taxper'=>$arr_return['taxval']),true);

            $query = array_merge($query, $arr_return);
            $products = isset($query['products']) ? $query['products'] : array();
            foreach($query['products'] as $key => $product) {
                if( isset($product['deleted']) && $product['deleted'] ) continue;
                if( isset($product['same_parent']) && $product['same_parent'] ) continue;
                $data = $this->cal_price_line(array('data'=>array('id'=>$key),'fieldchange'=>'quantity'), true, true, $query);
                $query = array_merge($query, $data);
            }
            $arr_return['products'] = $query['products'];
            $arr_return['sum_sub_total'] = $query['sum_sub_total'];
            $arr_return['sum_tax'] = $query['sum_tax'];
            $arr_return['sum_amount'] = $query['sum_amount'];
        /**
         * Chọn Contact
         */
        }else if ($field == 'contact_name' && $valueid != '') {
            $arr_return = array(
                'contact_name' => '',
                'contact_id' => '',
                'phone' => '',
                'email' => '',
            );
            //change company
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
        else if ($field == 'our_rep' && $valueid != '') {
            $arr_return['our_rep_id'] = new MongoId($valueid);
        }
        else if ($field == 'our_csr' && $valueid != '') {
            $arr_return['our_csr_id'] = new MongoId($valueid);
        }
		/**
         * Save Line entry
        */
		if($field == 'products'){

			if(isset($value[$valueid]) && isset($value[$valueid]['products_id']) && is_object($value[$valueid]['products_id']) && $fieldopt!='code' && $fieldopt!='deleted'){
				//change size other


			//giam gia cho product parrent neu la xoa item option
			}else if(isset($value[$valueid]) && isset($value[$valueid]['products_id']) && is_object($value[$valueid]['products_id']) && $fieldopt=='deleted'){
				$vv = $value[$valueid];

				if(isset($vv['option_for']) && $vv['option_for']!='' && isset($vv['same_parent']) && $vv['same_parent']==1 && isset($value[$vv['option_for']])){
					$query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())),array('company_id'));
					$option_for = $vv['option_for'];
					if(!isset($query['company_id']))
						$query['company_id'] = '';

					$result = array();
					$arr_plus_temp = $value[$option_for];
					//tinh gia theo price list
					$arr_plus_temp['plus_sell_price'] = 0;
					$cal_price = new cal_price;
					$cal_price->arr_product_items = $arr_plus_temp;
					$result = $this->change_sell_price_company($query['company_id'],$vv['products_id']);
					$cal_price->price_break_from_to = $result;
					$cal_price->field_change = '';
					$arr_plus_temp = $cal_price->cal_price_items();

					//loai bo gia option
					$value[$option_for]['sell_price'] -= (float)$arr_plus_temp['sell_price'];
					$value[$option_for]['plus_sell_price'] -= (float)$arr_plus_temp['sell_price'];
					//tinh lai unit price
					$cal_price2 = new cal_price;
					$cal_price2->arr_product_items = $value[$option_for];
					$cal_price2->field_change = 'sell_price';
					$value[$option_for] = $cal_price2->cal_price_items();

				}
				$value[$valueid]['deleted'] = true;
				//pr($value);die;



			//truong hop thay Code
			}else if(isset($value[$valueid]) && isset($value[$valueid]['products_id']) && is_object($value[$valueid]['products_id']) && $fieldopt=='code'){
                $save = false;
                $query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())),array('products','options','company_id', 'shipping_cost'));
                if( isset($query['products'][$valueid]['sku']) && preg_match('/^SHP/', $query['products'][$valueid]['sku']) ) {
                    $query['shipping_cost'] -= $query['products'][$valueid]['sub_total'];
                    $save = true;
                }
                //remove cac option cu cua $valueid
                foreach($value as $kks=>$vvs){
                    if(isset($vvs['option_for']) && $vvs['option_for']==$valueid)
                       $value[$kks] = array('deleted' => true);
                }
                if(isset($query['options']))
                    foreach($query['options'] as $options_key=>$options){
                        if(isset($options['parent_line_no']) && $options['parent_line_no']==$valueid)
                            $query['options'][$options_key] = array('deleted' => true);
                    }

                //tim data option cua product
                $this->selectModel('Product');
                $parent = $this->Product->select_one(array('_id'=>$value[$valueid]['products_id']));
                if(isset($parent['sku']))
                    $value[$valueid]['sku'] = $parent['sku'];
                else
                    $value[$valueid]['sku'] = '';
                $value[$valueid]['origin_options'] = array();

                //lay danh sach option va luu lai
                $products = $this->Product->options_data((string)$value[$valueid]['products_id']);
                if(isset($products['productoptions']) && is_array($products['productoptions']) && count($products['productoptions'])>0){
                    $total_sub_total = 0;
                    if(!isset($query['options']))
                        $query['options']= array();
                    $arr_return['options'] = $query['options'];
                    $options_num = count($arr_return['options']);
                    $line_num = count($value);
                    foreach($products['productoptions'] as $kk=>$vv){
                        //loop va tao moi items
                        $new_array = array();
                        $new_array['code']          = $vv['code'];
                        $new_array['sku']           = $vv['sku'];
                        $new_array['products_name'] = $vv['product_name'];
                        $new_array['product_name'] = $vv['product_name'];
                        $new_array['products_id']   = $vv['product_id'];
                        $new_array['product_id']    = $vv['product_id'];
                        $new_array['quantity']      = $vv['quantity'];
                        $new_array['sub_total']     = $vv['sub_total'];
                        $new_array['option_group']  = (isset($vv['option_group']) ? $vv['option_group'] : '');
                        if(isset($value[$valueid]['sizew']))
                            $new_array['sizew']         = $value[$valueid]['sizew'];
                        else
                            $new_array['sizew']         = $vv['sizew'];

                        if(isset($value[$valueid]['sizew_unit']))
                            $new_array['sizew_unit']        = $value[$valueid]['sizew_unit'];
                        else
                            $new_array['sizew_unit']        = $vv['sizew_unit'];

                        if(isset($value[$valueid]['sizeh']))
                            $new_array['sizeh']         = $value[$valueid]['sizeh'];
                        else
                            $new_array['sizeh']         = $vv['sizeh'];

                        if(isset($value[$valueid]['sizeh_unit']))
                            $new_array['sizeh_unit']        = $value[$valueid]['sizeh_unit'];
                        else
                            $new_array['sizeh_unit']        = $vv['sizeh_unit'];


                        $new_array['sell_by']       = $vv['sell_by'];
                        $new_array['oum']       = $vv['oum'];

                        if(isset($vv['same_parent']))
                            $new_array['same_parent']   = (int)$vv['same_parent'];
                        else
                            $new_array['same_parent']   = 0;
                        $more_discount              = (float)$vv['unit_price']*((float)$vv['discount']/100);
                        $new_array['sell_price']    = (float)$vv['unit_price'] - $more_discount;

                        $new_array['taxper']        = (isset($value[$valueid]['taxper']) ? (float)$value[$valueid]['taxper'] : 0);
                        $new_array['tax']           = $value[$valueid]['tax'];
                        $new_array['option_for']    = $valueid;
                        $new_array['deleted']       = false;
                        $new_array['proids']        = $value[$valueid]['products_id'].'_'.$options_num;
                        $this->cal_price = new cal_price;
                        //truyen data vao cal_price de tinh gia
                        $this->cal_price->arr_product_items = $new_array;
                        //lay thong tin khach hang de tinh chiec khau/giam gia
                        $result = array();
                        if(!isset($query['company_id']))
                            $query['company_id'] = '';
                        if(isset($new_array['products_id']))
                            $result = $this->change_sell_price_company($query['company_id'],$new_array['products_id']);
                        $tmp_sell_price = 0;
                        if(isset($result['sell_price'])) {
                            $tmp_sell_price = $result['sell_price'];
                        }
                        //truyen bang chiec khau va gia giam vao
                        $this->cal_price->price_break_from_to = $result;
                        //kiem tra field nao dang thay doi
                        $this->cal_price->field_change = '';
                        //chay tinh gia
                        $arr_ret = $this->cal_price->cal_price_items();
                        //
                        if(isset($vv['line_no']))
                            unset($vv['line_no']);
                        if(isset($arr_ret['same_parent']) && $arr_ret['same_parent'] == 0 && (isset($arr_ret['company_price_break']) && $arr_ret['company_price_break'] ) ) {
                            $arr_ret['custom_unit_price'] = $arr_ret['sell_price'];
                            $arr_ret['sell_price'] = $arr_ret['unit_price'] = $tmp_sell_price;
                            $vv['sell_price'] = $vv['unit_price'] = $tmp_sell_price;
                        }
                        $arr_return['options'][$options_num] = $vv;
                        $arr_return['options'][$options_num]['hidden'] = isset($vv['hidden'])&&$vv['hidden'] ? 1 : 0;
                        $arr_return['options'][$options_num]['this_line_no'] = $options_num;
                        $arr_return['options'][$options_num]['parent_line_no'] = $valueid;
                        $arr_return['options'][$options_num]['choice'] = 0;
                        $arr_return['options'][$options_num]['user_custom'] = 0;
                        if(isset($vv['require']) && (int)$vv['require']==1){
                            $value[$valueid]['origin_options'][] = array(
                                '_id'   => $vv['product_id'],
                                'name'  => $vv['product_name']
                            );
                            $value[$line_num] = array_merge((array)$new_array,(array)$arr_ret);
                            $value[$line_num]['user_custom'] = 0;
                            $value[$line_num]['hidden'] = $arr_return['options'][$options_num]['hidden'];
                            $arr_return['options'][$options_num]['line_no'] = $line_num;
                            $arr_return['options'][$options_num]['choice'] = 1;
                            $line_num++;
                        }
                        $options_num++;
                    }
                    //=============================================
                    $query['options'] = $arr_return['options'];
                    $save = true;
                }
                $query['products'] = $value;
                if( $save ) {
                    $this->opm->save($query);
                    echo $valueid;
                    die;
                }
            }
			$arr_return[$field] = $value;
		}



		/**
         * Save Costing Line entry
        */

		//ASSET TAGS
		else if($field=='asset_tags'){
            ksort($value);
            $arr_return[$field] = $value;
		}
        return $arr_return;
    }




	public function delete_all_associate($idopt='',$key=''){
		if($key=='products'){ // update cac line entry option cua products
            $this->Session->write($this->name.'ViewAssetTag','');
			$ids = $this->get_id();
			if($ids!=''){
				$arr_insert = $line_entry = array();
				//lay note products hien co
				$query = $this->opm->select_one(array('_id'=>new MongoId($ids)),array('products'));
				if(isset($query['products']) && is_array($query['products']) && count($query['products'])>0){
					$line_entry = $query['products'];
                    $line_entry[$idopt] = array('deleted'=>true);
					foreach($query['products'] as $keys=>$values){
						if(isset($values['option_for']) && $values['option_for']==$idopt)
                            $line_entry[$keys] = array('deleted'=>true);
					}
				}
				$arr_insert['products'] = $line_entry;//pr($line_entry);die;
				$arr_insert['_id'] 		= new MongoId($ids);
				$arr_insert = array_merge($arr_insert,$this->new_cal_sum($line_entry));
                $this->opm->save($arr_insert);
			}
		}

		if($key=='costing'){
			$ids = $this->get_id(); $items = ''; $total_area = $total_unit = 0;
			if($ids!=''){
				$quotation = $this->opm->select_one(array('_id'=>new MongoId($ids)),array('costing','products'));
				//lấy id opt của line entry
				if(isset($quotation['costing']) &&  isset($quotation['costing'][$idopt]) &&  isset($quotation['costing'][$idopt]['for_line'])){
					$items = $quotation['costing'][$idopt]['for_line'];
					$quotation['costing'][$idopt]['deleted'] = true;
				}
				// cap nhat lai gia cho line entry
				if(isset($quotation['products'][$items]) && $items!=''){
					$arr_return['products'] = $quotation['products'];
					$update = $quotation['products'][$items];
					//lap vong de tinh total
					foreach($quotation['costing'] as $kks=>$vvs){
						if(isset($vvs['deleted']) && !$vvs['deleted'] && isset($vvs['for_line']) && $vvs['for_line']==$items){
							if(isset($vvs['sell_by']) && $vvs['sell_by']=='area'){
								$total_area += (float)$vvs['sub_total'];
							}elseif(isset($vvs['sell_by']) && $vvs['sell_by']=='unit'){
								$total_unit += (float)$vvs['sub_total'];
							}
						}

					}
					//tinh lai sell_price
					if(isset($update['sell_by']) && $update['sell_by'] =='area'){
						$update['sell_price'] = $total_area;
						$update['plus_unit_price'] = $total_unit;
						$cal_price = new cal_price;
						$cal_price->arr_product_items = $update;
						$cal_price->field_change = 'plus_unit_price';
						$cal_price->cal_price_items();
						$update = $cal_price->arr_product_items;

					}elseif(isset($update['sell_by']) && $update['sell_by'] =='unit'){
						$update['sell_price'] = $total_area;
						$update['plus_unit_price'] = $total_unit;
						$cal_price = new cal_price;
						$cal_price->arr_product_items = $update;
						$cal_price->field_change = 'plus_unit_price';
						$cal_price->cal_price_items();
						$update = $cal_price->arr_product_items;
					}

					$arr_return['products'][$items] = $update;
					$arr_return['_id'] = new MongoId($ids);
					$this->opm->save($arr_return);

				}
			}
		}
        if($key=='options'){
            $id = $this->get_id();
            $query = $this->opm->select_one(array('_id'=>new MongoId($id)),array('products','options'));
            if(isset($query['options'][$idopt]['line_no'])&&$query['options'][$idopt]['line_no']!=''){
                $line_no = $query['options'][$idopt]['line_no'];
                $parent_no = $query['options'][$idopt]['parent_line_no'];
                $query['options'][$idopt] = array('deleted'=> true);
                if(isset($query['products'][$line_no])){
                    $query['products'][$line_no] = array('deleted'=> true);
                    if(isset($query['products'][$line_no]['same_parent'])&&$query['products'][$line_no]['same_parent']==1){
                        $this->opm->save($query);
                        $this->cal_price_line(array('data'=>array('id'=>$parent_no),'fieldchange'=>''));
                    } else {
                        $query = array_merge($query,$this->new_cal_sum($query['products']));
                        $this->opm->save($query);
                    }
                }
            }
        }
	}



    public function entry_search() {
        //parent class
        $arr_set = $this->opm->arr_settings;
        $arr_set['field']['panel_1']['code']['lock'] = '';
        $arr_set['field']['panel_1']['quotation_type']['element_input'] = '';
        $arr_set['field']['panel_1']['our_rep']['not_custom'] = '0';
        $arr_set['field']['panel_1']['our_csr']['not_custom'] = '0';
        $arr_set['field']['panel_4']['job_name']['not_custom'] = '0';
        $arr_set['field']['panel_4']['job_number']['lock'] = '0';
        $arr_set['field']['panel_4']['salesorder_name']['not_custom'] = '0';
        $arr_set['field']['panel_4']['salesorder_number']['lock'] = '0';
        $arr_set['field']['panel_1']['quotation_type']['default'] = '';
        $arr_set['field']['panel_1']['quotation_date']['default'] = '';
        $arr_set['field']['panel_4']['quotation_status']['default'] = '';
        $arr_set['field']['panel_4']['payment_due_date']['default'] = '';
        $arr_set['field']['panel_4']['payment_terms']['default'] = '';
        $arr_set['field']['panel_4']['tax']['default'] = '';
		$arr_set['field']['panel_4']['taxval']['default'] = '';
        unset( $arr_set['field']['panel_1']['none'] );
        $arr_set['field']['panel_1']['products_name'] = array(
                                                        'name' => 'Description',
                                                        'type' => 'text',
                                                        'moreclass' => 'fixbor2',
                                                        );
        unset($arr_set['field']['panel_1']['company_name']['readonly'], $arr_set['field']['panel_1']['contact_name']['readonly']);
        $this->set('search_class', 'jt_input_search');
        $this->set('search_class2', 'jt_select_search');
        $this->set('search_flat', 'placeholder="1"');
        $where = array();
        if ($this->Session->check($this->name . '_where'))
            $where = $this->Session->read($this->name . '_where');
        if (count($where) > 0) {
            foreach ($arr_set['field'] as $ks => $vls) {
                foreach ($vls as $field => $values) {
                    if (isset($where[$field])) {
                        $arr_set['field'][$ks][$field]['default'] = ''; //$where[$field]['values'];
                    }
                }
            }
        }
        unset($arr_set['relationship']['line_entry']['block']['products']['add'],
                $arr_set['relationship']['line_entry']['block']['products']['custom_box_top'],
                $arr_set['relationship']['line_entry']['block']['products']['custom_box_bottom'],
                $arr_set['relationship']['line_entry']['block']['products']['delete'],
                $arr_set['relationship']['line_entry']['block']['products']['field']['sizew_unit'],
                $arr_set['relationship']['line_entry']['block']['products']['field']['sizeh_unit'],
                $arr_set['relationship']['line_entry']['block']['products']['field']['details'],
                $arr_set['relationship']['line_entry']['block']['products']['field']['option'],
                $arr_set['relationship']['line_entry']['block']['products']['field']['vip'],
                $arr_set['relationship']['line_entry']['block']['products']['field']['adj_qty'],
                $arr_set['relationship']['line_entry']['block']['products']['field']['docket_check'],
                $arr_set['relationship']['line_entry']['block']['products']['field']['oum'],
                $arr_set['relationship']['line_entry']['block']['products']['field']['receipts']
            );
        $arr_set['relationship']['line_entry']['block']['products']['field']['sku']['type'] = 'text';
        $arr_set['relationship']['line_entry']['block']['products']['field']['sizew']['type'] = 'text';
        $arr_set['relationship']['line_entry']['block']['products']['field']['sizeh']['type'] = 'text';
        $arr_set['relationship']['line_entry']['block']['products']['field']['sell_price']['type'] = 'text';
        $arr_set['relationship']['line_entry']['block']['products']['field']['sell_by']['type'] = 'text';
        $arr_set['relationship']['line_entry']['block']['products']['field']['quantity']['type'] = 'text';
        $arr_set['relationship']['line_entry']['block']['products']['field']['unit_price']['type'] = 'text';
        $arr_set['relationship']['line_entry']['block']['products']['field']['custom_unit_price']['type'] = 'text';
        $this->set('subdatas', array(
            'products' => array(
                array(
                    'deleted' => false,
                    'code' => '',
                    'sku' => '',
                    'products_name' => '',
                    'sizew' => '',
                    'sizeh' => '',
                    'quantity' => '',
                )
            )
        ));
        //end parent class
        $this->set('arr_settings', $arr_set);
        $this->set('shipping_contact_name','');
        //set address
        $address_label = array('Invoice Address', 'Shipping address');
        $this->set('address_label', $address_label);
        $address_conner[0]['top'] = 'hgt fixbor';
        $address_conner[0]['bottom'] = 'fixbor2 jt_ppbot';
        $address_conner[1]['top'] = 'hgt';
        $address_conner[1]['bottom'] = 'fixbor3 jt_ppbot';
        $this->set('address_conner', $address_conner);
        $this->set('address_more_line', 2); //set
        $address_hidden_field = array('invoice_address', 'shipping_address');
        $this->set('address_hidden_field', $address_hidden_field); //set
        $address_country = $this->country();
        $this->set('address_country', $address_country); //set
        $this->set('address_country_id', ''); //set
        $address_province['invoice'] = $address_province['shipping'] = $this->province("CA");
        $this->set('address_province', ""); //set
        $this->set('address_province_id', ""); //set
        $this->set('address_onchange', "save_address_pr('\"+keys+\"');");
        $address_hidden_value = array('', '');
        $this->set('address_hidden_value', $address_hidden_value);
        $this->set('address_mode', 'search');
    }

    // Options list
    public function swith_options($keys) {
        parent::swith_options($keys);
        if ($keys == 'quotations') {
            $arr_where = array();
            $arr_where['quotation_type'] = array('values' => 'Quotation', 'operator' => '=');
            $this->Session->write($this->name . '_where', $arr_where);
            echo URL . '/' . $this->params->params['controller'] . '/lists';
        } else if ($keys == 'estimates') {
            $arr_where = array();
            $arr_where['quotation_type'] = array('values' => 'Estimate', 'operator' => '=');
            $this->Session->write($this->name . '_where', $arr_where);
            echo URL . '/' . $this->params->params['controller'] . '/lists';
        } else if ($keys == 'proposals') {
            $arr_where = array();
            $arr_where['quotation_type'] = array('values' => 'Proposal', 'operator' => '=');
            $this->Session->write($this->name . '_where', $arr_where);
            echo URL . '/' . $this->params->params['controller'] . '/lists';
        } else if ($keys == 'pro_forma_invoices') {
            $arr_where = array();
            $arr_where['quotation_type'] = array('values' => 'Pro Forma', 'operator' => '=');
            $this->Session->write($this->name . '_where', $arr_where);
            echo URL . '/' . $this->params->params['controller'] . '/lists';
        } else if ($keys == 'outstanding') {
            $or_where = array(
                array("quotation_status" => 'In progress'),
                array("quotation_status" => 'Submitted'),
                array("quotation_status" => 'Amended')
            );
            $arr_where = array();
            $arr_where[] = array('values' => $or_where, 'operator' => 'or');
            $this->Session->write($this->name . '_where', $arr_where);
            echo URL . '/' . $this->params->params['controller'] . '/lists';
        } else if ($keys == 'print_quotation') {
            echo URL . '/' . $this->params->params['controller'] . '/view_pdf/';
        } else if ($keys == 'print_quotation_exclude_quantity_and_price_columms')
            echo URL . '/' . $this->params->params['controller'] . '/view_pdf/0/exclude_qty_price';
        else if ($keys == 'print_quotation_include_category_headings')
            echo URL . '/' . $this->params->params['controller'] . '/view_pdf/0/category';
        else if ($keys == 'print_quotation_include_category_headings_only')
            echo URL . '/' . $this->params->params['controller'] . '/view_pdf/0/category_heading_only';
        else if ($keys == 'finished_products')
            echo URL . '/' . $this->params->params['controller'] . '/entry';
        else if ($keys == 'find_out_sync')
            echo URL . '/' . $this->params->params['controller'] . '/entry';
        else if ($keys == 'find_history')
            echo URL . '/' . $this->params->params['controller'] . '/history';
        else if ($keys == 'sync_stock_current')
            echo URL . '/' . $this->params->params['controller'] . '/entry';
        else if ($keys == 'sync_stock_found')
            echo URL . '/' . $this->params->params['controller'] . '/entry';
        else if ($keys == 'create_sales_order')
            echo URL . '/salesorders/create_sale_order/' . $this->get_id();
        else if ($keys == 'print_price_list')
            echo URL . '/' . $this->params->params['controller'] . '/entry';
        else if ($keys == 'print_mini_list')
            echo URL . '/' . $this->params->params['controller'] . '/entry';
        else if ($keys == 'report_by_customer_detailed')
            echo URL . '/' . $this->params->params['controller'] . '/option_detailed_customer_find';
        else if ($keys == 'report_by_customer_summary')
            echo URL . '/' . $this->params->params['controller'] . '/option_summary_customer_find';
        else if ($keys == 'report_by_area_detailed')
            echo URL . '/' . $this->params->params['controller'] . '/option_detailed_customer_find/area';
        else if ($keys == 'report_by_area_summary')
            echo URL . '/' . $this->params->params['controller'] . '/option_summary_customer_find/area';
        else if ($keys == 'report_by_product_detailed')
            echo URL . '/' . $this->params->params['controller'] . '/option_detailed_product_find';
        else if ($keys == 'report_by_products_summary')
            echo URL . '/' . $this->params->params['controller'] . '/option_summary_product_find';
        else if ($keys == 'report_by_category_detailed')
            echo URL . '/' . $this->params->params['controller'] . '/option_detailed_product_find/category';
        else if ($keys == 'report_by_category_summary')
            echo URL . '/' . $this->params->params['controller'] . '/option_summary_product_find/category';
        else if ($keys == 'duplicate_current_quotation_create_new_revision')
        {
            echo URL . '/' . $this->params->params['controller'] . '/duplicate_revise_quotation';
        }
        else if ($keys == 'create_job')
            echo URL . '/' . $this->params->params['controller'] . '/create_job';
        else if ($keys == 'email_quotation')
            echo URL . '/' . $this->params->params['controller'] . '/create_email_pdf/0/group';
        else if ($keys == 'email_quotation_exclude_quantity_and_price_columms')
            echo URL . '/' . $this->params->params['controller'] . '/create_email_pdf/0/exclude_qty_price';
        else if ($keys == 'email_quotation_include_category_headings')
            echo URL . '/' . $this->params->params['controller'] . '/create_email_pdf/0/category';
        else if ($keys == 'email_quotation_include_catgory_headings_only')
            echo URL . '/' . $this->params->params['controller'] . '/create_email_pdf/0/category_heading_only';
        else if ($keys == 'create_email')
            echo URL . '/' . $this->params->params['controller'] . '/add_from_module/'.$this->get_id().'/Email';
        else if ($keys == 'create_letter')
            echo URL . '/' . $this->params->params['controller'] . '/add_from_module/'.$this->get_id().'/Letter';
        else if ($keys == 'create_fax')
            echo URL . '/' . $this->params->params['controller'] . '/add_from_module/'.$this->get_id().'/Fax';
        else if ($keys == 'summary_commission_report_by_name_found_set_of_records')
            echo URL . '/' . $this->params->params['controller'] . '/commission_summary/';
        else if ($keys == 'detailed_commission_report_by_name_found_set_of_records')
            echo URL . '/' . $this->params->params['controller'] . '/commission_detailed/';
        else if ($keys == 'inventory_report')
            echo URL . '/' . $this->params->params['controller'] . '/inventory_report';

        else
            echo '';
        die;
    }


    public function set_cal_price() {
        $this->cal_price = new cal_price; //Option cal_price
        //set arr_product item default
        $this->cal_price->arr_product_items = array();
    }


	//Sử dụng thư viện cal_price để tính
    public function ajax_cal_line($arr_data = array()) {
        $arr_ret = $arr_product_items = array();
        if(!isset($arr_data['arr'])&&isset($_POST['arr'])){
            $arr_data['arr'] = $_POST['arr'];
        }
        if(!isset($arr_data['field'])&&isset($_POST['field']))
            $arr_data['field'] = $_POST['field'];
        if (isset($arr_data['arr'])) {
            $getdata = $arr_data['arr'];
            $getdata = (array) $getdata;
            if(isset($getdata['custom_unit_price'])){
                $getdata['custom_unit_price'] = (float)$getdata['custom_unit_price'];
            }
            //truong hop co id

            if (isset($getdata['id'])) {
                $get_id = $getdata['id'];
                $query  = $this->opm->select_one(array('_id' => new MongoId($this->get_id())));
                if(isset($getdata['custom_unit_price'])){
                    $getdata['custom_unit_price'] = (float)$getdata['custom_unit_price'];
                    if($query['products'][$get_id]['unit_price'] > $getdata['custom_unit_price']
                       &&!$this->check_permission($this->name.'_@_custom_unit_price_@_add')){
                        echo 'You do not have permission to change this value.';
                        die;
                    }
                }
                if(isset($arr_data['company_id'])&&is_object($arr_data['company_id']))
                        $query['company_id'] = $arr_data['company_id'];
                if (isset($query['products']))
                    $arr_pro = $arr_insert['products'] = (array) $query['products'];
                if (is_array($arr_pro) && count($arr_pro) > 0 && isset($arr_pro[$get_id]) && !$arr_pro[$get_id]['deleted']) {
                    $arr_pro = array_merge((array) $arr_pro[$get_id], (array) $getdata);

					if(isset($query['tax']) && $query['tax']!='')
						$arr_pro['tax'] = $this->get_tax($query['tax']);
					//tim va luu them cac thay doi phu thuoc

					if(isset($arr_data['field']))
						$fieldchage = $arr_data['field'];
					else
						$fieldchage = '';
					if($fieldchage=='sell_price' || $fieldchage=='custom')
						$arr_pro['plus_unit_price'] = 0;
					if(!isset($query['company_id']))
						$query['company_id'] = '';
                    $is_special = false;

					//tinh lai plus sell price neu thay doi lien quan den gia
                    $total_sub_total = 0;
                    $product_data = $query['products'][$get_id];
					if($fieldchage!='sell_price'){
                        $parent_no = $get_id;
                        $parent_id = $query['products'][$parent_no]['products_id'];
                        if($fieldchage=='options'&&isset($getdata['data'])){
                            $options_change = true;
                            $parent_no = $getdata['data']['parent_line_no'];
                            if(isset($getdata['data']['line_no']))
                                $this_line_no = $getdata['data']['line_no'];
                            $parent_id = $query['products'][$parent_no]['products_id'];
                            $get_id = $parent_no;
                            $arr_pro = $query['products'][$parent_no];
                        }
						$arr_pro['plus_sell_price'] = 0;
						if(!isset($arr_pro['sell_price']))
							$arr_pro['sell_price'] = 0;
                        if(strpos($fieldchage, 'size')!==false){
                            $size_tmp = $fieldchage;
                        }

						//tinh lai gia option
						$option = $this->option_list_data($parent_id,$parent_no);
						foreach($option['option'] as $value){
							if(isset($value['choice'])&&$value['choice']==1){
                                if(isset($value['same_parent'])&&$value['same_parent']==1){
                                    $is_special = true;
									 if(isset($value['is_custom']) && $value['is_custom']==1)
									 	$value['sell_price'] = $value['unit_price'];
                                    $value['sizew'] = $arr_pro['sizew'];
                                    $value['sizew_unit'] = $arr_pro['sizew_unit'];
                                    $value['sizeh'] = $arr_pro['sizeh'];
                                    $value['sizeh_unit'] = $arr_pro['sizeh_unit'];
                                    $value['quantity'] = (isset($value['quantity']) ? (float)$value['quantity'] : 1);
                                    if(isset($size_tmp))
                                        $value[$size_tmp] = $getdata[$size_tmp];
                                    $value['plus_sell_price'] = 0;
                                    $cal_price = new cal_price;


                                    $cal_price->arr_product_items = $value;
									$cal_price->price_break_from_to = $this->change_sell_price_company($query['company_id'],$value['product_id']);
									if(isset($value['is_custom']) && $value['is_custom']==1){
										$cal_price->field_change = 'sell_price';
									}else{
										$cal_price->field_change = $arr_data['field'];
										$cal_price->arr_product_items['quantity'] *= $arr_pro['quantity'];
										$cal_price->cal_price_items();
										$value['sell_price'] = $cal_price->arr_product_items['sell_price'];
										$cal_price->arr_product_items = $value;
										$cal_price->field_change = 'sell_price';
									}

                                    $value = $cal_price->cal_price_items();
                                    $arr_pro['plus_sell_price'] += $value['sub_total'];
                                    $total_sub_total += (isset($value['sub_total']) ? (float)$value['sub_total'] : 0);
                                    $fieldchage = '';
								//neu khong phai la S.P thi xet loai combination
                                } else if($product_data['sell_by']=='combination'
                                          && (!isset($value['same_parent']) || $value['same_parent'] == 0)
										  && isset($value['choice'])&&$value['choice']==1){
                                    $total_sub_total += (isset($value['sub_total']) ? (float)$value['sub_total'] : 0);
                                }
                            }
						}

						//dau check cuoi cung cua
						if(!$is_special
							&&isset($getdata['data']['same_parent'])
							&&$getdata['data']['same_parent']==0){
							$is_special = true;
						}


					}
					$cal_price = new cal_price;
					$cal_price->arr_product_items = $arr_pro;
					$result = array();
                    //Kiem tra neu nhu product cha la custom(ko co id) thi line nay ko can phai tinh bang pricebreak
                    if(isset($arr_pro['option_for'])&&$arr_pro['option_for']!=''){
                        $option_for = $arr_pro['option_for'];
                        if(!isset($arr_pro['same_parent']) || $arr_pro['same_parent']==0){
                            if(isset($query['products'][$option_for])&&!is_object($query['products'][$option_for]['products_id'])){
                                $is_custom = true;
                            }
                        }
                    }
					if(isset($arr_pro['products_id'])&&!isset($is_custom)){
                    	$result = $this->change_sell_price_company($query['company_id'],$arr_pro['products_id']);
					}

					//truyen bang chiec khau va gia giam vao
					$cal_price->price_break_from_to = $result;
					$cal_price->field_change = $fieldchage;

					//chay tinh gia, neu la combination
                    if($product_data['sell_by']=='combination'){
                        $arr_ret = $cal_price->combination_cal_price();
                        $arr_ret['unit_price'] += $total_sub_total;

                        $arr_ret['sell_price'] = $arr_ret['unit_price'];
                        if($total_sub_total>0){
                            $arr_ret['sub_total'] = round((float)$arr_ret['unit_price']*(float)$arr_ret['quantity'],2);
                            $arr_ret['tax'] = round(((float)(isset($arr_ret['taxper']) ? $arr_ret['taxper'] : 0)/100)*(float)$arr_ret['sub_total'],3);
                            $arr_ret['amount'] = round((float)$arr_ret['sub_total']+(float)$arr_ret['tax'],2);
                        }


					//nếu như có sp thì là loại special, thì tính diện tích rồi nhân vào line chính mới cộng cho plus_sell_price không thì ngược lại
					}else{
					   $arr_ret = $cal_price->cal_price_items($is_special);
                       //Kiem tra neu nhu product cha la custom(ko co id) update thong tin thay doi qua option
                       if(isset($is_custom)){
                            $orginal_query = $query;
                            if(isset($query['options'])&&!empty($query['options'])){
                                foreach($query['options'] as $option_key=>$option_value){
                                    if(isset($option_value['deleted'])&&$option_value['deleted']) continue;
                                    if(!isset($option_value['parent_line_no']) || $option_value['parent_line_no']!=$option_for) continue;
                                    if(!isset($option_value['product_id']) || (string)$option_value['product_id']!=(string)$arr_ret['products_id']) continue;
                                    $query['options'][$option_key]['unit_price'] = $arr_ret['sell_price'];
                                    $query['options'][$option_key]['quantity'] = $arr_ret['quantity'];
                                    $query['options'][$option_key]['sub_total'] = $arr_ret['sub_total'];
                                    break;
                                }
                            }
                            if($query['options']!=$orginal_query['options']){
                                $this->opm->save($query);
                            }
                        }
                       if(isset($arr_ret['option_for']) && isset($arr_ret['same_parent']) && $arr_ret['same_parent']==1){
                            $ids = $arr_ret['option_for'];
                            if(is_array($query['products'][$ids])&&!$query['products'][$ids]['deleted']){
                                $sub_total = (float)$query['products'][$get_id]['sub_total'];
                                $new_sub_total = (float)$arr_ret['sub_total'];
                                $query['products'][$ids]['sell_price'] = $query['products'][$ids]['unit_price'] = (float)$query['products'][$ids]['unit_price'] - $sub_total + $new_sub_total;
                                $query['products'][$ids]['sub_total'] = round((float)$query['products'][$ids]['unit_price']*(float)$query['products'][$ids]['quantity'],2);
                                $query['products'][$ids]['tax'] = round(((float)(isset($query['products'][$ids]['taxper']) ? $query['products'][$ids]['taxper'] : 0)/100)*(float)$query['products'][$ids]['sub_total'],3);
                                $query['products'][$ids]['amount'] = round((float)$query['products'][$ids]['sub_total']+(float)$query['products'][$ids]['tax'],2);
                                $arr_insert['products'][$ids] = $query['products'][$ids];
                                $query['products'][$ids]['ids'] = $ids;
                            }
                       }

                    }
					//Save all data
                    $arr_insert['products'][$get_id] = array_merge((array) $arr_pro, (array) $arr_ret);
                     //custom unit price
                    if(isset($arr_insert['products'][$get_id]['custom_unit_price'])){
                        $product = $arr_insert['products'][$get_id];
                        $is_reverse_update = false;
                        if(!is_object($product['products_id']))
                            $is_reverse_update = true;
                        $is_admin_edit = false;
                        if($this->check_permission($this->name.'_@_custom_unit_price_@_add'))
                            $is_admin_edit = true;
                        if((float)$product['custom_unit_price']<(float)$product['unit_price']){
                            if($arr_data['field']=='custom_unit_price'
                               &&!$this->check_permission($this->name.'_@_custom_unit_price_@_add'))
                               $product['custom_unit_price'] = $product['unit_price'];
                            else if($arr_data['field']!='custom_unit_price')
                                $product['custom_unit_price'] = $product['unit_price'];
                        }
                        if($is_reverse_update)
                            $product['sell_price'] = $product['unit_price'] = $product['custom_unit_price'];
                        $unit_price = $product['custom_unit_price'];
                        $product['sub_total'] = round((float)$unit_price*(float)$product['quantity'],2);
                        $product['tax'] = round(((float)(isset($product['taxper']) ? $product['taxper'] : 0)/100)*(float)$product['sub_total'],3);
                        $product['amount'] = round((float)$product['sub_total']+(float)$product['tax'],2);
                        $arr_insert['products'][$get_id] = $product;
                        $arr_ret = $product;
                    }
                    //end custom
					$arr_insert = $this->arr_associated_data('products',$arr_insert['products'],$get_id,$fieldchage);
					$arr_insert['_id'] = new MongoId($this->get_id());
                    $this->opm->save($arr_insert);
					//update sum
                    $keyfield = array(
                        "sub_total" 	=> "sub_total",
                        "tax" 			=> "tax",
                        "amount" 		=> "amount",
                        "sum_sub_total" => "sum_sub_total",
                        "sum_tax" 		=> "sum_tax",
                        "sum_amount" 	=> "sum_amount"
                    );
                    $arr_sum = $this->update_sum('products', $keyfield);
                    $arr_ret = array_merge((array) $arr_ret, (array) $arr_sum);
                    if(isset($ids)){
                        $arr_ret = array('self'=>$arr_ret,'parent'=>$query['products'][$ids]);
                    }
                    else if(isset($options_change)&&isset($this_line_no)&&$this_line_no!=''){
                        foreach($query['options'] as $option){
                            if($option['deleted']) continue;
                            if(!isset($option['line_no']) || $option['line_no']!=$this_line_no) continue;
                            $query['products'][$this_line_no] = array_merge($query['products'][$this_line_no],$option);
                            break;
                        }
                        $arr_ret = array('parent'=>$arr_ret,'self'=>$query['products'][$this_line_no]);
                    }
                    //Return data for display
                    if(!isset($arr_data['company_id']))
                        echo json_encode($arr_ret);
                }

                //truong hop khong chon id nao
            } else {
                if(!isset($arr_data['company_id']))
                    echo '';
            }
        }
        if(!isset($arr_data['company_id']))
            die;
    }

    var $is_text = 0;

    //subtab line entry
    public function line_entry() {
        $is_text = $this->is_text;
        if ($this->check_lock()) {
            $this->opm->set_lock_option('line_entry', 'products');
            $this->opm->set_lock_option('text_entry', 'products');
        }
        $subdatas = $arr_ret = array();
        $codeauto = 0;
        $opname = 'products';
        $sum_sub_total = $sum_tax = 0;
        $subdatas[$opname] = array();
        $ids = $this->get_id();
        if ($ids != '') {
            //get entry data
            $quote = $this->opm->select_one(array('_id'=>new MongoId($ids)),array('date_modified', 'quotation_status'));
            $prefix_cache_name = 'line_quotation_'.$ids.'_';
            $cache_name = $prefix_cache_name.$quote['date_modified']->sec;
            $arr_ret = Cache::read($cache_name);
            if(!$arr_ret){
                $arr_ret = $this->line_entry_data($opname, $is_text);
                Cache::write($cache_name,$arr_ret);
                $old_cache = $this->get_cache_keys_diff($cache_name,$prefix_cache_name);
                foreach($old_cache as $cache){
                    Cache::delete($cache);
                }
            }
            // pr($arr_ret['sum_tax']);die;
            if(isset($arr_ret[$opname])){
                $minimum = $this->get_minimum_order();
                if ($quote['quotation_status'] != 'Cancelled') {
                    if($arr_ret['sum_sub_total']<$minimum){
                        $arr_ret = $this->get_minimum_order_adjustment($arr_ret,$minimum);
                    }
                }
                $subdatas[$opname] = $arr_ret[$opname];
            }
            //pr($arr_ret['sum_tax']);die;
            if(isset($quote['quotation_status']) && $quote['quotation_status'] == 'Cancelled' )
                $arr_ret['sum_sub_total'] = $arr_ret['sum_tax'] = $arr_ret['sum_amount'] = 0;
        }
        $this->set('subdatas', $subdatas);
        $codeauto = $this->opm->get_auto_code('code');
        $this->set('nextcode', $codeauto);
        $this->set('file_name', 'quotation_' . $this->get_id());
        $this->set('sum_sub_total', $arr_ret['sum_sub_total']);
        $this->set('sum_amount', $arr_ret['sum_amount']);
        $this->set('sum_tax', $arr_ret['sum_tax']);
        // /pr($arr_ret['sum_tax']);die;
		$link_add_atction['option'] = 'option_list';
		$link_add_atction['receipts'] = 'rfqs_list';
        $this->set('link_add_atction', $link_add_atction);
        $this->set_select_data_list('relationship', 'line_entry');
		$this->set('icon_link_id', $this->get_id());
        $this->selectModel('Stuffs');
        if( $this->Stuffs->count(array('value' => 'Default Prefer Customer Search', 'default_search' => true)) ) {
            $this->set('default_prefer_customer', true);
        }
    }

    //check and cal for Line Entry
    public function line_entry_data($opname = 'products', $is_text = 0,$mod = '') {
        $arr_ret = array(); $option_for = '';
        $this->selectModel('Setting');
        if(isset($this->module_id))
            $ids = $this->module_id;
        else
            $ids = $this->get_id();
        if ($ids != '') {
            $newdata = $option_select_dynamic = array();
            $query = $this->opm->select_one(array('_id' => new MongoId($ids)),array('options','products','sum_sub_total','sum_amount','sum_tax','rfqs','quotation_date'));
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
            $arr_product_special = Cache::read('arr_product_special');
            if(!$arr_product_special){
                $arr_product_special = $this->Product->select_all(array(
                                           'arr_where'=>array('special_order'=>1),
                                           'arr_field'=>array('_id')
                                           ));
                $arr_product_special = iterator_to_array($arr_product_special);
                Cache::write('arr_product_special',$arr_product_special);
            }
            $arr_product_approved = Cache::read('arr_product_approved');
            if(!$arr_product_approved){
                $arr_product_approved = $this->Product->select_all(array(
                                           'arr_where'=>array('approved'=>1),
                                           'arr_field'=>array('_id')
                                           ));
                $arr_product_approved = iterator_to_array($arr_product_approved);
                Cache::write('arr_product_approved',$arr_product_approved);
            }
            $arr_product_rfq = Cache::read('arr_product_rfq');
            if(!$arr_product_rfq){
                $arr_product_rfq = $this->Product->select_all(array(
                                           'arr_where'=>array('is_rfq'=>1),
                                           'arr_field'=>array('_id')
                                           ));
                $arr_product_rfq = iterator_to_array($arr_product_rfq);
                Cache::write('arr_product_rfq',$arr_product_rfq);
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
                        if( isset($arr['bleed']) && $arr['bleed'] ) {
                            $newdata[$key]['xclass'] = 'bleed';
                        }
						//set default Unit price
						if(!isset($arr['custom_unit_price']) && isset($arr['unit_price']))
							$newdata[$key]['custom_unit_price'] = $arr['unit_price'];
                        if(!isset($arr['option_for'])){
                            $origin_options = array();
                            if (isset($arr['origin_options'])) {
                                $origin_options = $arr['origin_options'];
                            }
                            $difference_option = false;
                            $option_qty = 0;
                            $option = $this->new_option_data(array('key'=>$key,'products_id'=>$arr['products_id'],'options'=>$query['options'],'products'=>$query['products'],'date'=>$query['quotation_date']),$query['products']);
                            //Khoa sell_by,oum neu nhu line nay co option
                            //Khoa tiep sell_price neu line nay co option same_parent
                            if(isset($option['option'])&&!empty($option['option'])){
                                foreach($option['option'] as $value){
                                    if($value['deleted']) continue;
                                    if(isset($value['choice'])&&$value['choice']==0&&isset($value['require']) && $value['require']!=1) continue;
                                    if(isset($value['oum']) && $value['oum']!=$arr['oum'])
                                        $newdata[$key]['oum'] = 'Mixed';
                                    $newdata[$key]['xlock']['sell_by'] = '1';
                                    $newdata[$key]['xlock']['oum'] = '1';
                                    $option_qty++;
                                    if (!$difference_option && array_search($value['product_id'], array_column($origin_options, '_id')) === false ) {
                                        $difference_option = true;
                                    }
                                    // if(!isset($value['same_parent']) || $value['same_parent']==0) continue;
                                    // $newdata[$key]['xlock']['sell_price'] = '1';
                                }
                            }
                            if ($option_qty != count($origin_options)) {
                                $difference_option = true;
                            }
                            if ($difference_option) {
                                $newdata[$key]['xstyle_element']['details']= 'background-color: red !important;width: 69%;margin-left: 5px;';
                            }
                            if(isset($arr['products_id']) ) {
                                if(isset($arr_product_special[(string)$arr['products_id']]))
                                    $newdata[$key]['xstyle_element']['sku']= 'color: blue !important;';
                                else if(!isset($arr_product_approved[(string)$arr['products_id']]))
                                    $newdata[$key]['xcss_element']['sku']= 'approved_product';
                                if(isset($arr['rfq_approve']) && $arr['rfq_approve'])
                                    $newdata[$key]['xstyle_element']['receipts']= 'background-color: #949494 !important;';
                                else if(isset($arr_product_rfq[(string)$arr['products_id']]))
                                    $newdata[$key]['xstyle_element']['receipts']= 'background-color: #852020 !important;';
                            }
                            if(!isset($arr['company_price_break']) || !$arr['company_price_break'])
                                $newdata[$key]['xhidden']['vip'] = '1';
                        } else {
                            $newdata[$key]['xlock']['sell_by'] = '1';
                            $newdata[$key]['xlock']['oum'] = '1';
                            $newdata[$key]['xempty']['vip'] = '1';
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
                        if( /*(isset($newdata[$key]['option_group'])
                            && (strpos(strtolower($newdata[$key]['option_group']), 'cutting') !== false
                                 ||strpos(strtolower($newdata[$key]['option_group']), 'packing') !== false
                                 ||strpos(strtolower($newdata[$key]['option_group']), 'cut') !== false) )
                            ||*/ (isset($this->export_pdf)&&isset($arr['hidden'])&&$arr['hidden']) ){
                            if(isset($this->export_pdf) && (!isset($arr['same_parent']) || !$arr['same_parent']) && isset($arr['option_for'])){
                                $newdata[$arr['option_for']]['sub_total'] = str_replace(',', '', $newdata[$arr['option_for']]['sub_total']);
                                $newdata[$arr['option_for']]['sub_total'] += $arr['sub_total'];
                                $newdata[$arr['option_for']]['tax'] = str_replace(',', '', $newdata[$arr['option_for']]['tax']);
                                $newdata[$arr['option_for']]['tax'] += $arr['tax'];
                                $newdata[$arr['option_for']]['amount'] = str_replace(',', '', $newdata[$arr['option_for']]['amount']);
                                $newdata[$arr['option_for']]['amount'] += $arr['amount'];
                                $newdata[$arr['option_for']]['custom_unit_price'] =  round($newdata[$arr['option_for']]['sub_total'] /  $newdata[$arr['option_for']]['quantity'],3);
                                $option_for_sort['parent'][$arr['option_for']] = array_merge($option_for_sort['parent'][$arr['option_for']],$newdata[$arr['option_for']]);
                            }
                            unset($newdata[$key]);
                            continue;
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
                        $newdata[$key]['custom_unit_price'] = $this->opm->format_currency((isset($arr['custom_unit_price']) ? $arr['custom_unit_price'] : 0), 3);
                        if (isset($arr['unit_price'])){
                            $newdata[$key]['unit_price'] = $this->opm->format_currency( $arr['unit_price'], 3);
                            if(!isset($arr['custom_unit_price']))
                                $newdata[$key]['custom_unit_price'] = $newdata[$key]['unit_price'];
                        }
                        else
                            $newdata[$key]['unit_price'] = '0.000';
                        if (isset($arr['sub_total']))
                            $newdata[$key]['sub_total'] = $this->opm->format_currency( $arr['sub_total']);
                        else
                            $newdata[$key]['sub_total'] = '0.00';
                        if (isset($arr['tax']))
                            $newdata[$key]['tax'] = $this->opm->format_currency( $arr['tax'], 3);
                        else
                            $newdata[$key]['tax'] = '0.000';
                        if (isset($arr['amount']))
                            $newdata[$key]['amount'] = $this->opm->format_currency( $arr['amount']);
                        else
                            $newdata[$key]['amount'] = '0.00';
						unset($newdata[$key]['id']);
						$newdata[$key]['_id'] = $key;
						$newdata[$key]['sort_key'] = $this->opm->num_to_string($key).'-'.'0';

						$option_for = '';
						if(isset($arr['option_for']) && is_numeric($arr['option_for'])){
                            $newdata[$key]['xempty']['option']      = '1';
                            $newdata[$key]['xempty']['view_costing']      = '1';
                            if(isset($arr['same_parent'])&&$arr['same_parent']==1)
                                $newdata[$key]['xempty']['custom_unit_price']   = '1';
                            $newdata[$key]['_id'] = $key;
							$newdata[$key]['sku_disable'] = '1';
							$newdata[$key]['sku'] = '';
							$newdata[$key]['remove_deleted'] = '1';
							$newdata[$key]['icon']['products_name'] = (is_object($arr['products_id']) ? URL.'/products/entry/'.$arr['products_id'] : '#');
							$newdata[$key]['sort_key'] = $this->opm->num_to_string($arr['option_for']).'-'.$key;
							if($mod!='options_list')
							     unset($newdata[$key]['products_id']);
						}


                        //data RFQ's
                        $receipts = 0;
                        if (isset($query['rfqs']) && is_array($query['rfqs']) && count($query['rfqs']) > 0) {
                            foreach ($query['rfqs'] as $rk => $rv) {
                                if (!$rv['deleted'] && isset($rv['rfq_code']) && (int) $rv['rfq_code'] == $key) {
                                    $receipts = 1;
                                    break;
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
                        else{
                            $option_for_sort['parent'][$key] = $newdata[$key];
                        }

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
                        $option_for_sort['option'][$p_key] = $this->opm->aasort($option_for_sort['option'][$p_key],'option_group_for_sort');
                        foreach($option_for_sort['option'][$p_key] as $value)
                            array_push($arr_ret[$opname], $value);
                    }
                }
            }
        }
        $this->set('option_select_dynamic', $option_select_dynamic);
        return $arr_ret;
    }


	//subtab Text entry
    public function text_entry() {
        $this->is_text = 1;
        $this->line_entry();
    }


	public function test_pdf2() {
		$this->layout = 'pdf';
		$this->set('filename', 'test_pdf2');
	}



    //Export pdf

    public function view_pdf($getfile=false,$type='', $ids = '') {
        if($type=='group')
            $this->export_pdf = true;
        $arrData = array('pdf_name' => 'Quotation');
        $this->layout = 'pdf';
        if( empty($ids) )
            $ids = $this->get_id();
        else
            $this->module_id = $ids;

        if ($ids != '') {
            $query = $this->opm->select_one(array('_id' => new MongoId($ids)));
            if(IS_LOCAL && $query['sum_amount'] > 20000000 && (!isset($query['quotation_accepted']) ||  !$query['quotation_accepted'])){
                $this->selectModel('Company');
                $company = $this->Company->select_one(array('system'=>true),array('contact_default_id','name'));
                $this->selectModel('Contact');
                $contact = $this->Contact->select_one(array('_id'=>$company['contact_default_id']),array('first_name','last_name'));
                echo "This quotation is greater than 2,000. It needs to be accepted by {$contact['first_name']} {$contact['last_name']} - {$company['name']}.";
                die;
            }
            if( !isset($_GET['print_pdf']) ) {
                $filename = 'QT-'.$query['code'].(empty($type) ? '-detailed' : ($type == 'group' ? '' : '-'.$type));
                if( $this->print_pdf(array('report_file_name' =>  $filename, 'report_url' => URL.'/quotations/view_pdf/'.$getfile.'/'.$type.'/'.$ids)) ) {
                    if($getfile){
                        return $filename.'.pdf';
                    }
                    $this->redirect(URL.'/upload/'.$filename.'.pdf');
                } else {
                    if($getfile){
                        return false;
                    }
                    echo 'Please contact IT for this issue.';
                    die;
                }
            }
            $arrtemp = $query;
			//sort product by parent
			if(isset($arrtemp['products'])){
				foreach($arrtemp['products'] as $keys=>$values){
					if(isset($values['option_for']) && $values['option_for']!=''){
						if(!isset($arrtemp['products'][$keys]['products_name']))
							$arrtemp['products'][$keys]['products_name'] = '';
						$arrtemp['products'][$keys]['products_name'] = '&bull; '.$arrtemp['products'][$keys]['products_name'];
						$arrtemp['products'][$keys]['sku'] = '';
						$arrtemp['products'][$keys]['sort_key'] = $values['option_for'].'-'.$keys;
					}else
						$arrtemp['products'][$keys]['sort_key'] = $keys.'-'.'0';
				}
				$arrtemp['products'] = $this->opm->aasort($arrtemp['products'],'sort_key');
			}

            //customer address
            $customer = '';
            if (isset($arrtemp['company_id']) && strlen($arrtemp['company_id']) == 24)
                $customer .= '<b>' . $this->get_name('Company', $arrtemp['company_id']) . '</b><br />';
            else if (isset($arrtemp['company_name']))
                $customer .= '<b>' . $arrtemp['company_name'] . '</b><br />';
            if (isset($arrtemp['contact_id']) && strlen($arrtemp['contact_id']) == 24)
                $customer .= '<p>'.$this->get_name('Contact', $arrtemp['contact_id']) . '</p>';
            else if (isset($arrtemp['contact_name']))
                $customer .= '<p>'.$arrtemp['contact_name'] . '</p>';

            //loop 2 address
            $arradd = array('invoice', 'shipping');
            foreach ($arradd as $vvs) {
                $kk = $vvs;
                $customer_address = '';
                if (isset($arrtemp[$kk . '_address']) && isset($arrtemp[$kk . '_address'][0]) && count($arrtemp[$kk . '_address']) > 0) {
                    $temp = $arrtemp[$kk . '_address'][0];
                    if (isset($temp[$kk . '_address_1']) && $temp[$kk . '_address_1'] != '')
                        $customer_address .= $temp[$kk . '_address_1'] . '<br />';
                    if (isset($temp[$kk . '_address_2']) && $temp[$kk . '_address_2'] != '')
                        $customer_address .= $temp[$kk . '_address_2'] . '<br />';
                    if (isset($temp[$kk . '_address_3']) && $temp[$kk . '_address_3'] != '')
                        $customer_address .= $temp[$kk . '_address_3'] . '<br />';
                    else
                        $customer_address .= '<br />';
                    if (isset($temp[$kk . '_town_city']) && $temp[$kk . '_town_city'] != '')
                        $customer_address .= $temp[$kk . '_town_city'].', ';

                    if (isset($temp[$kk . '_province_state']))
                        $customer_address .= ' ' . $temp[$kk . '_province_state'] . ', ';
                    else if (isset($temp[$kk . '_province_state_id']) && isset($temp[$kk . '_country_id'])) {
                        $keytemp = $temp[$kk . '_province_state_id'];
                        $provkey = $this->province($temp[$kk . '_country_id']);
                        if (isset($provkey[$temp]))
                            $customer_address .= ' ' . $provkey[$temp] . '<br/>';
                    }


                    if (isset($temp[$kk . '_zip_postcode']) && $temp[$kk . '_zip_postcode'] != '')
                        $customer_address .= $temp[$kk . '_zip_postcode'].'<br/>';

                    if (isset($temp[$kk . '_country']) && isset($temp[$kk . '_country_id']) && $temp[$kk . '_country_id'] != "CA")
                        $customer_address .= ' ' . $temp[$kk . '_country'] . '<br />';
                    else
                        $customer_address .= '<br />';
                    $arr_address[$kk] = $customer_address;
                }
            }

            if (isset($arrtemp['heading']) && $arrtemp['heading'] != '')
                $arrData['heading'] = $arrtemp['heading'];
            if (!isset($arr_address['invoice']))
                $arr_address['invoice'] = '';
            if( !empty($customer) )
                $customer = "<p>{$customer}</p>";
            $arrData['customer_address'] = $customer.$arr_address['invoice'];
            if (!isset($arr_address['shipping']))
                $arr_address['shipping'] = '';
			if($arr_address['shipping']=='')
				$arr_address['shipping'] = $arr_address['invoice'];
            $ship_to = '';
            if(isset($arrtemp['shipping_address'][0]['shipping_contact_name']))
                $ship_to = $arrtemp['shipping_address'][0]['shipping_contact_name'];
            if($ship_to == '')
                $ship_to = $this->get_name('Contact', $arrtemp['contact_id']);
            if( !empty($ship_to) )
                $ship_to = "<p>{$ship_to}</p>";
            $arrData['shipping_address'] = $ship_to.$arr_address['shipping'];
            $arrData['right_info'] = array(
                        'Quotation no'  => $arrtemp['code'],
                        'Date'          => $this->opm->format_date($arrtemp['quotation_date'])
                );

            $arrData['extra_note'] = 'Quotes are based on infomation provided and valid for 30 days. Subsequent information or insufficient print ready files to additional charges. A minimum charge of $50.00 applies to all orders.';

            /** Nội dung bảng giá */

			//comments note
			if(isset($arrtemp['other_comment']))
                $arrData['note'] = nl2br($arrtemp['other_comment']);

            $html_cont = '';
            $line_entry_data = $this->line_entry_data();
            $minimum = $this->get_minimum_order('Quotation', $ids);
            if($arrtemp['sum_sub_total']<$minimum){
                $line_entry_data = $this->get_minimum_order_adjustment($line_entry_data,$minimum, $ids);
            }
            if (isset($line_entry_data['products']) && !empty($line_entry_data['products'])) {
                $arrData['content'] = '';
                if(isset($arrtemp['options']) && !empty($arrtemp['options']) )
                    $options = $arrtemp['options'];
                if($type=='' || $type=='exclude_qty_price'){
                    if($type == '' ) {
                        $arrData['title'] = array(
                                'SKU', 'Description', 'Width' => 'text-align: right;', 'Height' => 'text-align: right;', 'Unit price' => 'text-align: right;', 'Qty' => 'text-align: right;', 'Line total' => 'text-align: right;' );
                    } else {
                        $arrData['title'] = array(
                                'SKU', 'Description', 'Width' => 'text-align: right;', 'Height' => 'text-align: right;', 'Line total' => 'text-align: right;' );
                    }
                    foreach ($line_entry_data['products'] as $keys => $values) {
                        if ( !isset($values['deleted']) || !$values['deleted']) {
                            if (isset($values['products_name']))
                                $values['products_name'] = nl2br($values['products_name']);
                            else
                                $values['products_name'] = 'Empty';
                            if (isset($values['sku']))
                                $values['sku'] = '  ' . $values['sku'];
                            else
                                $values['sku'] = '  #' . $keys;
                            if (isset($values['sizew']) && $values['sizew'] != '' && isset($values['sizew_unit']) && $values['sizew_unit'] != '')
                                $values['sizew'] = $values['sizew'] . ' (' . $values['sizew_unit'] . ')';
                            else if (isset($values['sizew']) && $values['sizew'] != '')
                                $values['sizew'] = $values['sizew'] . ' (in.)';
                            else
                                $values['sizew'] = '';
                            if (isset($values['sizeh']) && $values['sizeh'] != '' && isset($values['sizeh_unit']) && $values['sizeh_unit'] != '')
                                $values['sizeh'] = $values['sizeh'] . ' (' . $values['sizeh_unit'] . ')';
                            else if (isset($values['sizeh']) && $values['sizeh'] != '')
                                $values['sizeh'] = $values['sizeh'] . ' (in.)';
                            else
                                $values['sizeh'] = '';

                            if(!isset($values['company_price_break']) || !$values['company_price_break']) {
                                if (!isset($values['custom_unit_price']))
                                    $values['custom_unit_price'] = (isset($values['unit_price']) ? $values['unit_price'] : 0);
                                $values['unit_price'] = $this->opm->format_currency(isset($values['custom_unit_price']) ? $values['custom_unit_price'] : 0, 3);
                            } else {
                                $values['unit_price'] = isset($values['custom_unit_price']) ? $this->opm->format_currency($values['custom_unit_price'], 3) : $this->opm->format_currency($values['unit_price'], 3);
                            }

                             if (isset($values['sub_total']))
                                $values['sub_total'] = $this->opm->format_currency($values['sub_total']);
                            else
                                $values['sub_total'] = '';
                            $arrData['content'] .= '<tr>';
                            if ( $values['_id'] === 'Extra_Row' ) {
                                $arrData['content'].= '
                                                    <td></td>
                                                    <td>' . $values['products_name'] . '</td>
                                                    <td class="right_text">' . $values['sizew'] . '</td>
                                                    <td class="right_text">' . $values['sizeh'] . '</td>
                                                    <td></td>
                                                    <td class="right_text">' . $values['quantity'] . '</td>
                                                    <td class="right_text">' . $values['sub_total'] . '</td>
                                        ';
                            } else if(isset($values['option_for'])&&is_numeric($values['option_for'])){
                                if(isset($values['same_parent']) && $values['same_parent']) {
                                    $arrData['content'] .= '
                                                <td></td>
                                                <td class="option_product"><span class="bullet"></span>'.$values['products_name'].'</td>
                                                <td colspan="'.( $type == '' ? 5 : 4 ).'"></td>';
                                } else {
                                    $arrData['content'] .= '
                                                <td></td>
                                                <td class="option_product"><span class="bullet"></span>'.$values['products_name'].'</td>
                                                <td class="right_text">'.$values['sizew'].'</td>
                                                <td class="right_text">'.$values['sizeh'].'</td>';
                                    if( $type == '') {
                                        $arrData['content'] .= '<td class="right_text">'.$values['unit_price'].'</td>
                                                <td class="right_text">'.$values['quantity'].'</td>';
                                    }
                                    $arrData['content'] .= '<td class="right_text">'.$values['sub_total'].'</td>';
                                }
                            } else {
                                $arrData['content'] .= '
                                            <td>'.$values['sku'].'</td>
                                            <td>'.$values['products_name'].'</td>
                                            <td class="right_text">'.$values['sizew'].'</td>
                                            <td class="right_text">'.$values['sizeh'].'</td>';
                                if( $type == '') {
                                    $arrData['content'] .= '<td class="right_text">'.$values['unit_price'].'</td>
                                            <td class="right_text">'.$values['quantity'].'</td>';
                                }
                                $arrData['content'] .= '<td class="right_text">'.$values['sub_total'].'</td>';
                            }
                            $arrData['content'] .= '</tr>';
                        }//end if deleted
                    }//end for
				}else if($type=='category'||$type=='category_heading_only'){
                    if($type == 'category' ) {
                        $arrData['title'] = array(
                                'SKU', 'Description', 'Width' => 'text-align: right;', 'Height' => 'text-align: right;', 'Unit price' => 'text-align: right;', 'Qty' => 'text-align: right;', 'Line total' => 'text-align: right;' );
                    } else {
                        $arrData['title'] = array(
                                'SKU', 'Description', 'Line total' => 'text-align: right;' );
                    }
                    $this->selectModel('Product');
                    $cate = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
                    $group = array();
                    $arr_products = array();
                    foreach($line_entry_data['products'] as $keys => $values){
                         if(!isset($values['company_price_break']) || !$values['company_price_break']) {
                            if (!isset($values['custom_unit_price']))
                                $values['custom_unit_price'] = (isset($values['unit_price']) ? $values['unit_price'] : 0);
                            $unit_price = $this->opm->format_currency($values['custom_unit_price']);
                        } else {
                            $unit_price = $this->opm->format_currency($values['unit_price']);
                        }
                        if(isset($values['deleted'])&&$values['deleted']) continue;
                        $product['category'] = '';
                        if(isset($values['products_id']) && is_object($values['products_id']))
                            $product = $this->Product->select_one(array('_id'=> new MongoId($values['products_id'])),array('category'));
                        $product_cate = 'No category entered';
                        if(isset($cate[$product['category']]))
                            $product_cate = $cate[$product['category']];
                        if(isset($values['option_for']) && isset($arr_products[$values['option_for']])){
                            $product_cate = $arr_products[$values['option_for']]['category'];
                            $product['category'] = $arr_products[$values['option_for']]['category_id'];
                        }
                        else{
                            $arr_products[$values['_id']]['category'] = $product_cate;
                            $arr_products[$values['_id']]['category_id'] = (isset($product['category']) ? $product['category'] : '');
                        }
                        $group[$product_cate]['category_id'] = (isset($product['category']) ? $product['category'] : '');
                        $group[$product_cate]['category'] = (isset($product_cate) ? $product_cate : '' );
                        $group[$product_cate]['group'][$keys]['same_parent'] = (isset($values['same_parent']) ? $values['same_parent'] : 0);
                        if($type=='category'){
                            $group[$product_cate]['group'][$keys]['code'] = (isset($values['sku']) ? $values['sku'] : '');
                            $group[$product_cate]['group'][$keys]['products_name'] = (isset($values['products_name']) ? $values['products_name'] : '');
                            $group[$product_cate]['group'][$keys]['quantity'] = (isset($values['quantity']) ? $values['quantity'] : 0);
                            $group[$product_cate]['group'][$keys]['sub_total'] = (isset($values['sub_total'])&&$values['sub_total']!='' ? $values['sub_total'] : '0.00');
                            $group[$product_cate]['group'][$keys]['custom_unit_price'] = $unit_price;
                            $group[$product_cate]['group'][$keys]['sizew'] = (isset($values['sizew']) && $values['sizew'] != '' && isset($values['sizew_unit']) && $values['sizew_unit'] != '' ? $values['sizew'] . ' (' . $values['sizew_unit'] . ')' : (isset($values['sizew']) && $values['sizew'] != '') ?  $values['sizew'] . ' (in.)' : '');
                            $group[$product_cate]['group'][$keys]['sizeh'] = (isset($values['sizeh']) && $values['sizeh'] != '' && isset($values['sizeh_unit']) && $values['sizeh_unit'] != '' ? $values['sizeh'] . ' (' . $values['sizeh_unit'] . ')' : (isset($values['sizeh']) && $values['sizeh'] != '') ?  $values['sizeh'] . ' (in.)' : '');

                        }
                        else if($type=='category_heading_only'){
                            if(!isset($group[$product_cate]['sub_total']))
                                $group[$product_cate]['sub_total'] = $values['sub_total'];
                            else{
                                if($group[$product_cate]['group'][$keys]['same_parent'] == 0)
                                    $group[$product_cate]['sub_total'] += (isset($values['sub_total'])&&$values['sub_total']!='' ? $values['sub_total'] : 0);
                            }
                        }
                    }
                    if(!empty($group)){
                        ksort($group);
                        foreach($group as $value){
                            if($type=='category'){
                                $total_sub_total = 0;
                                $arrData['content'] .= '<tr style="color: #fff;font-weight: bold">';
                                $arrData['content'] .= '<td style="background-color:#757575;" colspan="2">'.$value['category_id'].'</td>';
                                $arrData['content'] .= '<td style="background-color:#757575;"colspan="5">'.$value['category'].'</td>';
                                $arrData['content'] .= '</tr>';
                                foreach($value['group'] as $val){
                                    if($val['same_parent'] == 0)
                                        $total_sub_total += (float)$val['sub_total'];
                                    $arrData['content'] .= '<tr">';
                                    $arrData['content'] .= '<td class="first">'.$val['code'].'</td>';
                                    $arrData['content'] .= '<td>'.$val['products_name'].'</td>';
                                    if($val['same_parent'] != 0){
                                        $arrData['content'] .= '<td colspan="5"></td></tr>';
                                        continue;
                                    }
                                    $unit_price = $this->opm->format_currency($val['custom_unit_price']);
                                    $arrData['content'] .= '<td class="right_text">'.$val['sizew'].'</td>';
                                    $arrData['content'] .= '<td class="right_text">'.$val['sizeh'].'</td>';
                                    $arrData['content'] .= '<td class="right_text">'.$unit_price.'</td>';
                                    $arrData['content'] .= '<td class="right_text">'.$val['quantity'].'</td>';
                                    $arrData['content'] .= '<td class="right_text">'.$this->opm->format_currency($val['sub_total']).'</td>';
                                    $arrData['content'] .= '</tr>';
                                }
                                $arrData['content'] .= '<tr>
                                                    <td class="first" colspan="6" style="text-align:right; border-bottom: 1px dash #000;"><strong>Total by category:</strong></td>
                                                    <td style="text-align:right; border-bottom: 1px dash #000;">'.$this->opm->format_currency($total_sub_total).'</td>
                                                </tr>';
                            }
                            else if($type=='category_heading_only'){
                                $arrData['content'] .= '<tr>';
                                $arrData['content'] .= '<td style="width:15%;">'.$value['category_id'].'</td>';
                                $arrData['content'] .= '<td style="width:70%;">'.$value['category'].'</td>';
                                $arrData['content'] .= '<td style="width: 15%; text-align: right;">'.$this->opm->format_currency($value['sub_total']).'</td>';
                                $arrData['content'] .= '</tr>';
                            }
                        }
                    }

                }else if($type=='group'){
                    $arrData['title'] = array(
                            'SKU', 'Description', 'Width' => 'text-align: right;', 'Height' => 'text-align: right;', 'Unit price' => 'text-align: right;', 'Qty' => 'text-align: right;', 'Line total' => 'text-align: right;'
                        );
                    $arr_price = array();
                    foreach($line_entry_data['products'] as $product){
                        if(!isset($product['option_for'])) continue;
                        if(!isset($product['same_parent']) || $product['same_parent'] == 1) continue;
                        if (!isset($values['custom_unit_price']))
                            $product['custom_unit_price'] = (isset($product['unit_price']) ? $product['unit_price'] : 0);
                        if(!isset($arr_price[$product['option_for']]))
                            $arr_price[$product['option_for']]['unit_price'] = $arr_price[$product['option_for']]['sub_total'] = 0;
                        $product['custom_unit_price'] = (float)str_replace(',', '', $product['custom_unit_price']);
                        $product['sub_total'] = (float)str_replace(',', '', $product['sub_total']);
                        $arr_price[$product['option_for']]['unit_price'] += $product['custom_unit_price'];
                        $arr_price[$product['option_for']]['sub_total'] += $product['sub_total'];
                    }
                    foreach ($line_entry_data['products'] as $keys => $values) {
                        if (!isset($values['deleted']) || !$values['deleted']) {
                            if( isset($values['option_for']) && isset($query['products'][$values['option_for']] ) ){
                                $pro = $query['products'][$values['option_for']];
                                if(isset($pro['sku']) && strpos(str_replace(' ', '', $pro['sku']), 'DCP-') !== false)
                                    continue;
                            }
                            if (isset($values['products_name']))
                                $values['products_name'] = nl2br($values['products_name']);
                            else
                                $values['products_name'] = 'Empty';
                            if (isset($values['sku']))
                                $values['sku'] = '  ' . $values['sku'];
                            else
                                $values['sku'] = '  #' . $keys;
                            if (isset($values['sizew']) && $values['sizew'] != '' && isset($values['sizew_unit']) && $values['sizew_unit'] != '')
                                $values['sizew'] = $values['sizew'] . ' (' . $values['sizew_unit'] . ')';
                            else if (isset($values['sizew']) && $values['sizew'] != '')
                                $values['sizew'] = $values['sizew'] . ' (in.)';
                            else
                                $values['sizew'] = '';
                            if (isset($values['sizeh']) && $values['sizeh'] != '' && isset($values['sizeh_unit']) && $values['sizeh_unit'] != '')
                                $values['sizeh'] = $values['sizeh'] . ' (' . $values['sizeh_unit'] . ')';
                            else if (isset($values['sizeh']) && $values['sizeh'] != '')
                                $values['sizeh'] = $values['sizeh'] . ' (in.)';
                            else
                                $values['sizeh'] = '';

                            if(isset($arr_price[$values['_id']])){
                                $values['custom_unit_price'] += $arr_price[$values['_id']]['unit_price'];
                                $values['sub_total'] = str_replace(',', '', (string)$values['sub_total']);
                                $values['sub_total'] += $arr_price[$values['_id']]['sub_total'];
                            }
                            if(isset($arr_price[$values['_id']]))
                                $values['unit_price'] = $this->opm->format_currency($values['sub_total'] / $values['quantity'], 3);
                            else
                                $values['unit_price'] = $this->opm->format_currency(isset($values['custom_unit_price']) ? $values['custom_unit_price'] : 0, 3);
                             if (isset($values['sub_total']))
                                $values['sub_total'] = $this->opm->format_currency($values['sub_total']);
                            else
                                $values['sub_total'] = '';
                            $arrData['content'] .= '<tr>';
                            if ( $values['_id'] === 'Extra_Row' ) {
                                $arrData['content'].= '
                                                    <td></td>
                                                    <td>' . $values['products_name'] . '</td>
                                                    <td class="right_text">' . $values['sizew'] . '</td>
                                                    <td class="right_text">' . $values['sizeh'] . '</td>
                                                    <td></td>
                                                    <td class="right_text">' . $values['quantity'] . '</td>
                                                    <td class="right_text">' . $values['sub_total'] . '</td>
                                        ';
                            } else if(isset($values['option_for'])&&is_numeric($values['option_for'])){
                                if (isset($values['same_parent']) && $values['same_parent']) {
                                        $arrData['content'].= '
                                                        <td></td>
                                                        <td class="option_product"><span class="bullet"></span>' . $values['products_name'] . '</td>
                                                        <td colspan="5"></td>
                                            ';
                                    }
                                    else {
                                        $arrData['content'].= '
                                                        <td></td>
                                                        <td class="option_product"><span class="bullet"></span>' . $values['products_name'] . '</td>
                                                        <td class="right_text"></td>
                                                        <td class="right_text"></td>
                                                        <td class="right_text"></td>
                                                        <td class="right_text">' . $values['quantity'] . '</td>
                                                        <td class="right_text"></td>
                                            ';
                                    }
                            } else {
                                $arrData['content'] .= '
                                            <td>'.$values['sku'].'</td>
                                            <td>'.$values['products_name'].'</td>
                                            <td class="right_text">'.$values['sizew'].'</td>
                                            <td class="right_text">'.$values['sizeh'].'</td>
                                            <td class="right_text">'.$values['unit_price'].'</td>
                                            <td class="right_text">'.$values['quantity'].'</td>
                                            <td class="right_text">'.$values['sub_total'].'</td>
                                ';
                            }
                            $arrData['content'] .= '</tr>';
                        }//end if deleted
                    }
                }
                $sub_total = $total = $taxtotal = 0.00;
                if (isset($line_entry_data['sum_sub_total']))
                    $sub_total = $line_entry_data['sum_sub_total'];
                if (isset($line_entry_data['sum_tax']))
                    $taxtotal = $line_entry_data['sum_tax'];
                if (isset($line_entry_data['sum_amount']))
                    $total = $line_entry_data['sum_amount'];
                //Sub Total
                $arrData['sum_sub_total'] = $this->opm->format_currency($sub_total);
                $arrData['sum_tax'] = $this->opm->format_currency($taxtotal);
                $arrData['sum_amount'] = $this->opm->format_currency($total);
            }//end if
            $arrData['qr_image'] = 'https://chart.googleapis.com/chart?chs=100x100&cht=qr&chl='.URL.'/quotations/entry/'.$ids.'&choe=UTF-8';
            //set footer
            $arrData['render_path'] = '../Elements/view_pdf';
            $this->render_pdf($arrData);
            // $this->redirect('/upload/' . $filename . '.pdf');
        }
    }







    /* ================ RFQ's ================== */
    public function rfqs() {
        $subdatas = array();
        if (isset($_REQUEST['sort'])) {
            $sort = $_REQUEST['sort'];
            echo $sort;
            die;
        }

        $subdatas['rfqs'] = $arr_temp = array();
        if ($this->get_id() != '') {
            $links = "onclick=\" window.location.assign('" . URL . "/quotations/rfqs_entry/" . $this->get_id() . "/";
            $query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())));

            if (isset($query['rfqs']) && is_array($query['rfqs']) && count($query['rfqs']) > 0) {

                foreach ($query['rfqs'] as $kss => $vss) {
                    if (!$vss['deleted']) {
                        $arr_temp[$kss] = $vss;
                        $arr_temp[$kss]['_id'] = $arr_temp[$kss]['rfqs_id'] = $kss;
                        if (isset($vss['rfq_date']) && is_object($vss['rfq_date']))
                            $arr_temp[$kss]['rfq_date'] = $vss['rfq_date']->sec;
                        if (isset($vss['deadline_date']) && is_object($vss['deadline_date']))
                            $arr_temp[$kss]['deadline_date'] = $vss['deadline_date']->sec;
                        else
                            $arr_temp[$kss]['deadline_date'] = '';
                        $arr_temp[$kss]['set_link'] = $links . $arr_temp[$kss]['_id'] . "');\"";
                        if (isset($arr_temp[$kss]['rfq_code'])) {
                            $temp = $arr_temp[$kss]['rfq_code'];
                            $arr_temp[$kss]['rfq_code'] = (isset($query['products'][$temp]['code']) ? $query['products'][$temp]['code'] : '');
                            $arr_temp[$kss]['name_details'] = (isset($query['products'][$temp]['products_name']) ? $query['products'][$temp]['products_name'] : '');
                        }
                    }
                }
                $subdatas['rfqs'] = $arr_temp;
            }
        }
        $this->set('subdatas', $subdatas);
    }



	 public function find_sub_line_entry($keysearch = 'proids',$keys='') {
		 $arr = array();
		 if ($this->get_id() != '') {
		 	$query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())));
			if(isset($query['products']) && is_array($query['products']) && count($query['products'])>0){
				foreach($query['products'] as $kk=>$vv){
					if(isset($vv['deleted']) && $vv['deleted']==false && isset($vv[$keysearch]) && $vv[$keysearch]!=''){
						if($keys=='')
							$arr[] = $vv[$keysearch];
						else if($keys=='swap')
							$arr[$vv[$keysearch]] = $kk;
						else
							$arr[$kk] = $vv[$keysearch];
					}
				}
			}
		 }
		 return $arr;
	 }



	 public function find_sub_line_entry_for_line($option_for='',$keysearch = 'proids',$keys='') {
		 $arr = array();
		 if ($this->get_id() != '' && $option_for!='') {
		 	$query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())));
			if(isset($query['products']) && is_array($query['products']) && count($query['products'])>0){
				foreach($query['products'] as $kk=>$vv){
					if(isset($vv['deleted']) && $vv['deleted']==false && isset($vv[$keysearch]) && $vv[$keysearch]!='' && $vv['option_for'] == $option_for){
						if($keys=='')
							$arr[] = $vv[$keysearch];
						else if($keys=='swap')
							$arr[$vv[$keysearch]] = $kk;
						else
							$arr[$kk] = $vv[$keysearch];
					}
				}
			}
		 }
		 return $arr;
	 }




	public function option_list_data($products_id='',$idsub=-1) {
        $data = $option_group = array(); $groupstr = '';

        if($idsub<0)
            return $data;

        if(is_object($products_id))
            $products_id = (string)$products_id;

        if($products_id!=''){
            $this->selectModel('Product');
            $products = $this->Product->options_data($products_id);
        }
        $custom_option = $this->quotation_options_data($idsub);
        if(isset($products['productoptions']) && count($products['productoptions'])>0){
            $data = $products['productoptions'];
            foreach($data as $kk=>$vv){
                if(isset($custom_option[$kk])){
                    $option = $custom_option[$kk];
                    if(isset($option['quantity'])&&$option['quantity']!=$vv['quantity']
                       || isset($option['unit_price'])&&$option['unit_price']!=$vv['unit_price']
                       || isset($option['discount'])&&$option['discount']!=$vv['discount']){
                       $data[$kk] = array_merge($vv,array_merge($custom_option[$kk],array('is_custom'=>true)));
                    }
                }
            }
        }else{
            foreach($custom_option as $key=>$value)
                $custom_option[$key]['is_custom'] = true;
            $data = $custom_option;
        }
        if(!empty($data)){
            foreach($data as $k=>$v)
                $data[$k]['_id'] = $k;
            $this->opm->aasort($data,'option_group');
        }
        //pr($products['productoptions']);pr($custom_option); pr($data);die;
        //tim danh sach field proids trong cac option cua line dang xu ly
        $arr_lineid = $this->find_sub_line_entry_for_line($idsub,'proids','swap');
        //tim danh sach cac product id
        $arr_proid = $this->find_sub_line_entry_for_line($idsub,'proids');

        //pr($arr_lineid);pr($arr_proid);die;echo $proids."</br>";

        foreach($data as $kks=>$vvs){
            if(!isset($vvs['product_id']))
                continue;

            $proids = $products_id.'_'.$kks;

            if(in_array($proids,$arr_proid))
                $data[$kks]['choice'] = 1;
            else
                $data[$kks]['choice'] = 0;

            if(isset($arr_lineid[$products_id.'_'.$kks]))
                $data[$kks]['line_no'] = $arr_lineid[$products_id.'_'.$kks];
            else
                $data[$kks]['line_no'] = '';

            if(isset($vvs['require']) && (int)$vvs['require']==1){
                if(isset($vvs['group_type']) && $vvs['group_type']=='Exc')
                    $m=0;
                //else
                    //$data[$kks]['xlock']['choice'] = 1;
            }

            if(!isset($vvs['proline_no']))
                $data[$kks]['proline_no'] = $kks;

            if(!isset($vvs['parent_line_no']))
                $data[$kks]['parent_line_no'] = $idsub;

            if(isset($vvs['option_group'])){
                $option_group[$vvs['option_group']] = (string)$vvs['option_group'];
                $groupstr.= (string)$vvs['option_group'].',';
            }
        }
        $arr_return = array();
        $arr_return['option'] = $data;
        $arr_return['groupstr'] = $groupstr;
        $arr_return['option_group'] = $option_group;
        return $arr_return;

    }



	public function option_list() {
        if(isset($_POST['submit'])){
            $this->option_cal_price($_POST);
        }
        $opname = 'products';
        $arr_set = $this->opm->arr_settings;
        $subdatas = $arr_subsetting = $custom_option_group = array();
        $quote_code = $sumrfq = 0; $products_id = $idsub = $groupstr = '';
        //neu idopt khac rong
        if ($this->params->params['pass'][1] != '') {
            //DATA: salesorder line details
            $idsub = $this->params->params['pass'][1];
            $arr_ret = $this->opm->select_one(array('_id'=> new MongoId($this->get_id())),array('products','options','quotation_date'));
            if(!isset($arr_ret['options']))
                $arr_ret['options'] = array();
            $subdatas['quote_line_details'] = array();
            $products_id = '';
            if(!empty($arr_ret[$opname])){
                if(isset($arr_ret[$opname][$idsub])&&!$arr_ret[$opname][$idsub]['deleted']){
                    $products_note = '';
                    $subdatas['quote_line_details'] = $arr_ret[$opname][$idsub];
                    $products_id = $arr_ret[$opname][$idsub]['products_id'];
                    if(is_object($products_id)){
                        $this->selectModel('Product');
                        $notes = $this->Product->select_one(array('_id'=>$products_id),array('otherdetails'));
                        if(isset($notes['otherdetails'])){
                            foreach($notes['otherdetails'] as $note){
                                if(isset($note['deleted']) && $note['deleted']) continue;
                                $products_note = '<b>'.$note['heading'].'</b> '.$note['details'];
                            }
                        }
                    }
                    $this->set('products_name',$arr_ret[$opname][$idsub]['products_name']);
                    $this->set('products_note',$products_note);
                }
            }
            //DATA: option list
            $arr_ret[$opname][$idsub]['products_id'] = $products_id;
            $option_list_data = $this->new_option_data(array('key'=>$idsub,'products_id'=>$products_id,'options'=>$arr_ret['options'],'date'=>$arr_ret['_id']->getTimestamp()),$arr_ret['products']);
            $subdatas['option'] = $option_list_data['option'];

        }

        //VIEW: option list
        $arr_field_options['option']['option'] = array(
                'title'     => "Options for this item",
                'type'      => 'listview_box',
                'link'      => array('w' => '1', 'cls' => 'products','field'=>'product_id'),
                'css'       => 'width:100%;',
                'height'    => '420',
                'reltb' => 'tb_product@options',
                'footlink'  => array('label' => 'Click to view and edit in this product', 'link' => ''.URL.'/products/entry/'.$products_id),
                'field'     => array(
                        'choice' => array(
                            'name' => __('Choice'),
                            'width' => '5',
                            'type'=>'checkbox',
                            'edit'=>'1',
                            'default'=>0,
                        ),
                        'code' => array(
                            'name' => __('Code'),
                            'type' => 'text',
                            'width' => 3,
                            'align' => 'center',
                        ),
                        'product_name' => array(
                            'name' => __('Name'),
                            'width' => 22,
                        ),
                        'product_id' => array(
                            'name' => __('ID'),
                            'type'=>'hidden',
                        ),
                        'require' => array(
                            'name' => __('Req'),
                            'width' => 3,
                            'align'=>'center',
                            'type'=>'checkbox',
                        ),
                        'same_parent' => array(
                            'name' => __('S.P.'),
                            'width' => 3,
                            'align'=>'center',
                            'type'=>'checkbox',
                        ),
                        'user_custom' => array(
                            'name' => __('Custom Price'),
                            'width' => 3,
                            'align'=>'center',
                            'type'=>'checkbox',
                            'edit'  => '1',
                        ),
                        'unit_price' => array(
                            'name' => __('Unit cost'),
                            'width' => '7',
                            'type' => 'price',
                            'edit'  => '1',
                            'align' => 'right',
                            'numformat'=>3,
                        ),
                        'oum' => array(
                            'name' => __('UOM'),
                            'width' => '5',
                            'type'      => 'text',
                            'droplist'  => 'product_oum_area',
                            'align' => 'center',
                        ),
                        'sell_by' => array(
                            'type'      => 'hidden',
                        ),
                        'discount' => array(
                            'name' => __('%Discount'),
                            'width' => '8',
                            'edit'  => '1',
                            'type' => 'hidden',
                            'align' => 'right',
                        ),
                        'quantity' => array(
                            'name' => __('Quantity'),
                            'width' => '5',
                            'edit'  => '1',
                            'type' => 'price',
                            'align' => 'right',
                            'numformat'=>0,
                        ),
                        'sub_total' => array(
                            'name' => __('Sub total'),
                            'width' => '7',
                            'align' => 'right',
                            'type' => 'price',
                        ),
                        'group_type' => array(
                            'name' => __('Type'),
                            'width' => '7',
                            'type'=>'text',
                            'droplist' => 'product_group',
                        ),
                        'option_group' => array(
                            'name' => __('Group'),
                            'width' => '7',
                            'type'=>'select',
                            'droplist' => 'product_group',
                        ),
                        //so thu tu line entry cua option nay
                        'line_no' => array(
                            'width' => '0',
                            'type'=>'hidden',
                        ),
                        //so thu tu option ben product
                        'proline_no' => array(
                            'width' => '0',
                            'type'=>'hidden',
                        ),
                        //so thu tu trong option cua quota
                        'this_line_no' => array(
                            'type'=>'hidden',
                        ),
                        'thisline_no' => array(
                            'type'=>'hidden',
                        ),
                        //so thu tu line entry cha
                        'parent_line_no' => array(
                            'width' => '0',
                            'type'=>'hidden',
                        ),
                        'hidden' => array(
                            'name' => __('Hidden'),
                            'title' => 'Hidden from PDFs',
                            'edit'  => 1,
                            'width' => 3,
                            'align'=>'center',
                            'type'=>'checkbox',
                        ),
                        'delete' => array(
                            'type'  => 'delete_icon',
                            'rev'   => 'option',
                            'node'   => 'options',
                            'width' => 2
                        )
                ),
        );
        if(!isset($arr_ret[$opname][$idsub]['products_id']) || !is_object($arr_ret[$opname][$idsub]['products_id'])){
             $arr_field_options['option']['option']['add'] = 'Add more option';
             $arr_field_options['option']['option']['delete'] = '1';
             $arr_field_options['option']['option']['field']['require']['edit'] = '1';
             $arr_field_options['option']['option']['field']['same_parent']['edit'] = '1';
             $arr_field_options['option']['option']['field']['group_type']['type'] = 'select';
             $arr_field_options['option']['option']['field']['group_type']['edit'] = '1';
             $arr_field_options['option']['option']['field']['option_group']['edit'] = '1';
             $option_select_custom = array();
             $option_select_custom['option_group'] = $option_list_data['option_group'];
             $option_select_custom['group_type'] = array('Inc'=>'Inc','Exc'=>'Exc');
             $this->set('option_select_custom', $option_select_custom);
             $this->set('groupstr', $option_list_data['groupstr']);
        }
        $this->set('subdatas', $subdatas);
        $this->set('arr_subsetting', $arr_subsetting);
        $this->set('arr_field_options', $arr_field_options);
        $this->set('line_sum', 18);
        $this->set('quoteline', 'quotation_line_details');
        $this->set('quote_code', $quote_code);
        $this->set('sumrfq', $sumrfq);
        $this->set('products_id', (string)$arr_ret[$opname][$idsub]['products_id']);
        $this->set('subitems', $idsub);
        $this->set('employee_id', $this->opm->user_id());
        $this->set('employee_name', $this->opm->user_name());
        if(!isset($arr_ret[$opname][$idsub]['is_saved']))
          $this->set('custom_product2', '1');

    }


	public function quotation_options_data($parent_line_no=0){
		$arr_op = array();
        $ids = $this->get_id();
        if($ids!=''){
            $query = $this->opm->select_one(array('_id'=>new MongoId($ids)),array('products','options'));
            if(isset($query['options']) && is_array($query['options']) && count($query['options'])>0){
                foreach($query['options'] as $k=>$vs){
                    //lay thong tin cac option custom cua line entry cha $parent_line_no
                    if(isset($vs['deleted']) && !$vs['deleted'] &&  isset($vs['parent_line_no']) && $vs['parent_line_no']==$parent_line_no ){
                        if(!isset($vs['proline_no']) || $vs['proline_no']=='')
                            $vs['proline_no'] = $k;
                        $arr_op[$vs['proline_no']] = $vs;
                        $arr_op[$vs['proline_no']]['thisline_no'] = $k;
                        $arr_op[$vs['proline_no']]['proline_no'] = $k;
                    }
                }
            }
        }
        return $arr_op;
	}

	// Luu line entry dang option dua vao product id va ids note option
	public function save_new_line_entry_option($product_id='',$option_id='',$option_for=''){
		if(isset($_POST['product_id']))
			$product_id = $_POST['product_id'];
		if(isset($_POST['option_id']))
			$option_id = $_POST['option_id'];
		if(isset($_POST['option_for']))
			$option_for = $_POST['option_for'];

		$ids = $this->get_id();
		if($ids!=''){
			$arr_insert = $line_entry = $parent_line = array();
			//lay note products hien co
			$query = $this->opm->select_one(array('_id'=>new MongoId($ids)),array('products','company_id'));
			if(isset($query['products']) && is_array($query['products']) && count($query['products'])>0){
				$line_entry = $query['products'];
                $key = count($line_entry);
			}

			//khởi tạo line entry mới
			$option_line_data = $this->option_list_data($product_id,$option_for);
			$options_data = $option_line_data['option'];
			if(isset($options_data[$option_id])){
				$vv = $options_data[$option_id];

				if(isset($line_entry[$option_for]))
					$parent_line = $line_entry[$option_for];
                $vv['unit_price'] = (isset($vv['unit_price']) ? (float)$vv['unit_price'] : 0);
				$new_line = array();
				$new_line['code'] 			= $vv['code'];
				$new_line['sku'] 			= (isset($vv['sku']) ? $vv['sku'] : '');
				$new_line['products_name'] 	= $vv['product_name'];
				$new_line['products_id'] 	= $vv['product_id'];
				$new_line['quantity'] 		= $vv['quantity'];
				$new_line['sub_total'] 		= $vv['sub_total'];
				$new_line['sizew'] 			= isset($parent_line['sizew']) ? $parent_line['sizew'] : $vv['sizew'];
				$new_line['sizew_unit'] 	= isset($parent_line['sizew_unit'])?$parent_line['sizew_unit']:$vv['sizew_unit'];
				$new_line['sizeh'] 			= isset($parent_line['sizeh']) ? $parent_line['sizeh'] : $vv['sizeh'];
				$new_line['sizeh_unit'] 	= isset($parent_line['sizeh_unit'])?$parent_line['sizeh_unit']:$vv['sizeh_unit'];
				$new_line['sell_by'] 		= (isset($vv['sell_by']) ? $vv['sell_by'] : 'unit');
				$new_line['oum'] 			= $vv['oum'];
				$new_line['same_parent'] 	= isset($vv['same_parent']) ? (int)$vv['same_parent'] : 0;
				$new_line['sell_price'] 	= (float)$vv['unit_price'] - (float)$vv['unit_price']*((float)$vv['discount']/100);

				if(isset($query['products'][$option_for]['taxper']))
					$new_line['taxper'] 	= $query['products'][$option_for]['taxper'];
				if(isset($query['products'][$option_for]['tax']))
					$new_line['tax'] 		= $query['products'][$option_for]['tax'];
				$new_line['option_for'] 	= $option_for;
				$new_line['deleted'] 		= false;
				$new_line['proids'] 		= $product_id.'_'.$option_id;

				if(!isset($query['company_id']))
					$query['company_id']='';

				$cal_price = new cal_price;
				$cal_price->arr_product_items = $new_line;
				$cal_price->price_break_from_to = $this->change_sell_price_company($query['company_id'],$new_line['products_id']);
				$cal_price->field_change = '';
				$cal_price->cal_price_items();
				$new_line = array_merge((array)$new_line,(array)$cal_price->arr_product_items);

				//neu la same_parent thi thay gia cua parent va tinh lai gia
				if($new_line['same_parent']==1){
					$cal_price = new cal_price;
					$cal_price->arr_product_items = $new_line;
					$cal_price->price_break_from_to = $this->change_sell_price_company($query['company_id'],$new_line['products_id']);
					$cal_price->field_change = '';
					$cal_price->cal_price_items();
					$new_line['sell_price'] = $cal_price->arr_product_items['sell_price'];
					if(!isset($line_entry[$option_for]['plus_sell_price']))
						$line_entry[$option_for]['plus_sell_price'] = 0;

					$line_entry[$option_for]['sell_price'] += (float)$new_line['sell_price'];
					$line_entry[$option_for]['plus_sell_price'] += (float)$new_line['sell_price'];
					$cal_price2 = new cal_price;
					$cal_price2->arr_product_items = $line_entry[$option_for];
					$cal_price2->field_change = 'sell_price';
					$cal_price2->cal_price_items();
					$line_entry[$option_for] = $cal_price2->arr_product_items;
					$new_line['sell_price'] = '';
				}

				$line_entry[] = $new_line;


				//neu la nhom Exc thi xoa cac item khac cung nhom
				if(isset($vv['option_group']) && isset($vv['group_type']) &&  $vv['group_type']=='Exc'){
					foreach ($line_entry as $k=>$vs){
						if(isset($vs['deleted']) && !$vs['deleted'] && isset($vs['proids']) && $vs['proids'] !=$product_id.'_'.$option_id){
							$proids = explode("_",$vs['proids']);
							$proids = $proids[1];
							//neu cung nhom
							if(isset($options_data[$proids]['option_group']) && $options_data[$proids]['option_group']==$vv['option_group'] && isset($vs['option_for']) && $vs['option_for']==$option_for){
								//xoa item
								$line_entry[$k]['deleted'] = true;

								//tru ra neu la loai SP
								if($vs['same_parent']==1){
									$cal_price = new cal_price;
									$cal_price->arr_product_items = $line_entry[$option_for];
									$cal_price->price_break_from_to = $this->change_sell_price_company($query['company_id'],$vs['products_id']);
									$cal_price->field_change = '';
									$cal_price->cal_price_items();
									$sellprice = $cal_price->arr_product_items['sell_price'];

									if(!isset($line_entry[$option_for]['plus_sell_price']))
										$line_entry[$option_for]['plus_sell_price'] = 0;

									$line_entry[$option_for]['sell_price'] -= $sellprice;;
									$line_entry[$option_for]['plus_sell_price'] -= $sellprice;

									$cal_price2 = new cal_price;
									$cal_price2->arr_product_items = $line_entry[$option_for];
									$cal_price2->field_change = 'sell_price';
									$cal_price2->cal_price_items();
									$line_entry[$option_for] = $cal_price2->arr_product_items;
								}



							}

						}
					}
				}
                $keyfield = array(
                        "sub_total"     => "sub_total",
                        "tax"           => "tax",
                        "amount"        => "amount",
                        "sum_sub_total" => "sum_sub_total",
                        "sum_tax"       => "sum_tax",
                        "sum_amount"    => "sum_amount"
                    );
                $this->update_sum('products', $keyfield);
				//save lai
				$arr_insert['products'] = $line_entry;
				$arr_insert['_id'] = new MongoId($ids);

				if($this->opm->save($arr_insert)){
					//output
					if(isset($_POST['product_id'])){
                        $new_line['key'] = $key;
						echo json_encode($new_line);die;
					}else
						return $new_array;
				}else{
					if(isset($_POST['product_id'])){
						echo 'error'; die;
					}else
						return false;
				}
			}
		}die;
	}



	public function reload_box($boxname=''){
			if(isset($_POST['boxname']))
				$boxname = $_POST['boxname'];

			if($this->get_id()!=''){
				$arr_tmp = $this->opm->select_one(array('_id' => new MongoId($this->get_id())));
				$subdatas = array();
				if($boxname=='costing'){


				}

				if(isset($arr_tmp['amount_received']))
				$this->set('amount_allocated',(float)$arr_tmp['amount_received']);

				$this->set('blockname', $boxname);
				$this->set('arr_subsetting',$arr_settings['field'][$panel]['block']);
				$this->set('subdatas', $subdatas);
			}else
				die;
	}


	public function costing_for_line($idopt='') {
		$costing_list = array(); $merge = 1;
		if($idopt!=''){
			$ids = $this->get_id();
			if($ids!=''){
				$query = $this->opm->select_one(array('_id'=>new MongoId($ids)),array('costing'));
				if(isset($query['costing']) && is_array($query['costing']) && count($query['costing'])>0){
					foreach($query['costing'] as $keys=>$values){
						if(isset($values['deleted']) && !$values['deleted'] && isset($values['for_line']) && $values['for_line']==$idopt){
							$costing_list[$keys] = $values;
							$costing_list[$keys]['costing_id'] = $keys;
							if(is_object($values['product_id']))
								$costing_list[$keys]['xlock']['oum'] = '1';
							if(isset($values['markup']) && $merge==1){
								$merge = 0;
							}
						}

					}
				}
			}
		}

		$return['costing_list'] = $costing_list;
		$return['merge'] = $merge;
		//pr($return);die;
		return $return;
	}


    public function costing_list(){
        if(isset($_POST['submit'])){
            $this->option_cal_price($_POST);
        }
        $opname = 'products';
        $arr_set = $this->opm->arr_settings;
        $subdatas = $arr_subsetting = $custom_option_group = array();
        $quote_code = $sumrfq = 0; $products_id = $idsub = $groupstr = '';
        //neu idopt khac rong
        if ($this->params->params['pass'][1] != '') {
            //DATA: salesorder line details
            $idsub = $this->params->params['pass'][1];
            $arr_ret = $this->opm->select_one(array('_id'=> new MongoId($this->get_id())),array('products','options','quotation_date'));
            if(!isset($arr_ret['options']))
                $arr_ret['options'] = array();
            $subdatas['quotation_line_details'] = array();
            $products_id = '';
            if(!empty($arr_ret[$opname])){
                foreach($arr_ret[$opname] as $key => $value){
                    if($key!=$idsub) continue;
                    if(isset($value['deleted'])&&$value['deleted']) continue;
                    $subdatas['quotation_line_details'] = $value;
                    $subdatas['quotation_line_details']['key'] = $key;
                    $products_id = $value['products_id'];
                    break;
                }
            }
            //DATA: option list
            $arr_ret[$opname][$idsub]['products_id'] = $products_id;
            $option_list_data = $this->new_option_data(array('key'=>$idsub,'products_id'=>$products_id,'options'=>$arr_ret['options'],'date'=>$arr_ret['quotation_date']),$arr_ret['products']);
            $subdatas['option'] = $option_list_data['option'];

        }
        //VIEW: option list
        $arr_field_options['option']['option'] = array(
                'title'     => "Making for this item",
                'type'      => 'listview_box',
                'link'      => array('w' => '1', 'cls' => 'products','field'=>'product_id'),
                'css'       => 'width:100%;',
                'height'    => '420',
                'reltb'     => 'tb_product@options',
                'footlink'  => array('label' => 'Click to view and edit in this product', 'link' => ''.URL.'/products/entry/'.$products_id),
                'field'     => array(
                        'code' => array(
                            'name' => __('Code'),
                            'type' => 'text',
                            'width' => '5',
                            'align' => 'center',
                        ),
                        'product_name' => array(
                            'name' => __('Name'),
                            'width' => '28',
                            'edit'  => 1,
                        ),
                        'product_id' => array(
                            'name' => __('ID'),
                            'type'=>'hidden',
                        ),
                        'require' => array(
                            'width' => '5',
                            'align'=>'center',
                            'type'=>'hidden',
                        ),
                        'choice' => array(
                            'width' => '5',
                            'align'=>'center',
                            'type'=>'hidden',
                        ),
                        'same_parent' => array(
                            'name' => __('<span title="Same info as parent product">S.P.</span>'),
                            'width' => '3',
                            'align'=>'center',
                            'type'=>'checkbox',
                        ),
                        'unit_price' => array(
                            'name' => __('Unit cost'),
                            'width' => '7',
                            'type' => 'price',
                            'edit'  => '1',
                            'align' => 'right',
                            'numformat'=>3,
                        ),
                        'oum' => array(
                            'name' => __('UOM'),
                            'width' => '5',
                            'type'      => 'select',
                            'droplist'  => 'product_oum_area',
                            'edit'  => 1
                        ),
                        'discount' => array(
                            'name' => __('%Discount'),
                            'width' => '8',
                            'edit'  => '1',
                            'type' => 'hidden',
                            'align' => 'right',
                        ),
                        'quantity' => array(
                            'name' => __('Quantity'),
                            'width' => '5',
                            'edit'  => '1',
                            'type' => 'text',
                            'align' => 'right',
                        ),
                        'sub_total' => array(
                            'name' => __('Sub total'),
                            'width' => '7',
                            'align' => 'right',
                            'type' => 'price',
                        ),
                        'group_type' => array(
                            'name' => __('Type'),
                            'width' => '5',
                            'type'=>'text',
                            'droplist' => 'product_group',
                        ),
                        'option_group' => array(
                            'name' => __('Group'),
                            'width' => '7',
                            'type'=>'select',
                            'droplist' => 'product_group',
                        ),
                        //so thu tu line entry cua option nay
                        'line_no' => array(
                            'width' => '0',
                            'type'=>'hidden',
                        ),
                        //so thu tu option ben product
                        'proline_no' => array(
                            'width' => '0',
                            'type'=>'hidden',
                        ),
                        //so thu tu trong option cua quota
                        'thisline_no' => array(
                            'type'=>'hidden',
                        ),
                        'this_line_no' => array(
                            'type'=>'hidden',
                        ),
                        //so thu tu line entry cha
                        'parent_line_no' => array(
                            'width' => '0',
                            'type'=>'hidden',
                        ),
                        'delete' => array(
                            'type'  => 'delete_icon',
                            'rev'   => 'option',
                            'node'   => 'options',
                            'width' => 2
                        )
                ),
        );
        if(!is_object($arr_ret[$opname][$idsub]['products_id']) || $this->Product->count(array('_id'=>$arr_ret[$opname][$idsub]['products_id'],'options.deleted'=>false))==0){
                $arr_field_options['option']['option']['add'] = 'Add more option';
                $arr_field_options['option']['option']['field']['require']['edit'] = '1';
                $arr_field_options['option']['option']['field']['same_parent']['edit'] = '1';
                $arr_field_options['option']['option']['field']['group_type']['type'] = 'select';
                $arr_field_options['option']['option']['field']['group_type']['edit'] = '1';
                $arr_field_options['option']['option']['field']['option_group']['edit'] = '1';
                $option_select_custom = array();
                $option_select_custom['option_group'] = $option_list_data['option_group'];
                $option_select_custom['group_type'] = array('Inc'=>'Inc','Exc'=>'Exc');
                $this->selectModel('Setting');
                $option_select_custom['oum'] = $this->Setting->uom_option_list(true);
                $this->set('option_select_custom', $option_select_custom);
                $this->set('groupstr', $option_list_data['groupstr']);
        }
        $this->set('subdatas', $subdatas);
        $this->set('arr_subsetting', $arr_subsetting);
        $this->set('arr_field_options', $arr_field_options);
        $this->set('line_sum', 18);
        $this->set('quoteline', 'quotation_line_details');
        $this->set('quote_code', $quote_code);
        $this->set('sumrfq', $sumrfq);
        $this->set('products_id', (string)$arr_ret[$opname][$idsub]['products_id']);
        $this->set('subitems', $idsub);
        $this->set('employee_id', $this->opm->user_id());
        $this->set('employee_name', $this->opm->user_name());
        if(!isset($arr_ret[$opname][$idsub]['is_saved']))
          $this->set('custom_product2', '1');

    }


    public function save_costing_list($key){
        $quotation = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())));
        $this->selectModel('Product');
        $this->selectModel('Setting');
        //Lay default
        $this->Product->arrfield();
        $default_field = $this->Product->arr_temp;
        $arr_line_entry = $default_field;
        $product_line_entry = $quotation['products'][$key];
        //Truong hop Product line entry chua co ID
        if(!isset($product_line_entry['products_id']) || !is_object($product_line_entry['products_id'])){
            //Add line entry vao Product truoc
            $arr_line_entry['code']     =   $this->Product->get_auto_code('code');
            $arr_line_entry['name'] =   $product_line_entry['products_name'];
            $arr_line_entry['product_type'] =   'Custom Product';
            $arr_line_entry['company_name'] = (isset($quotation['company_name']) ? $quotation['company_name'] : '' );
            $arr_line_entry['company_id']   = (isset($quotation['company_id'])&&is_object(($quotation['company_id'])) ? new MongoId($quotation['company_id']) : '' );
            $arr_line_entry['sizew']    = (isset($product_line_entry['sizew']) ? $product_line_entry['sizew'] : '');
            $arr_line_entry['sizew_unit']   = (isset($product_line_entry['sizew_unit']) ? $product_line_entry['sizew_unit'] : 'in');
            $arr_line_entry['sizeh']    = (isset($product_line_entry['sizeh']) ? $product_line_entry['sizeh'] : '');
            $arr_line_entry['sizeh_unit']   = (isset($product_line_entry['sizeh_unit']) ? $product_line_entry['sizeh_unit'] : 'in');
            $arr_line_entry['sell_by']  = (isset($product_line_entry['sell_by']) ? $product_line_entry['sell_by'] : 'unit');
            $arr_line_entry['sell_price']   = (isset($product_line_entry['sell_price']) ? $product_line_entry['sell_price'] : 0);
            $arr_line_entry['oum']  = (isset($product_line_entry['oum']) ? $product_line_entry['oum'] : 'unit');
            $arr_line_entry['oum_depend']   = '';
            $arr_line_entry['unit_price']   = '';
            $arr_line_entry['markup']   = (isset($product_line_entry['markup']) ? $product_line_entry['markup'] : 0);
            $arr_line_entry['margin']  = (isset($product_line_entry['margin']) ? $product_line_entry['margin'] : 0);
            $this->Product->save($arr_line_entry);
            $line_entry_id = new MongoId($this->Product->mongo_id_after_save);
            $arr_line_entry['_id'] = $line_entry_id;
            //End add line entry
        } else {
            //Truong hop Product line entry da co ID
            $line_entry_id = new MongoId($product_line_entry['products_id']);
            $arr_line_entry = $this->Product->select_one(array('_id'=>$line_entry_id));
        }
        //Add costing to Product
        if(isset($quotation['costing'])){
            $i = 0;
            if(isset($arr_line_entry['madeup']))
                $i = count($arr_line_entry['madeup']);
            $line_entry_madeup = array();
            foreach($quotation['costing'] as $costing_key=>$costing){
                if($costing['for_line']!=$key) continue;
                if(!isset($costing['product_id']) || $costing['product_id'] == ''){ //Truong hop custom product thi save vao
                    $arr_save = $default_field;
                    $arr_save['code']   =   $this->Product->get_auto_code('code');
                    $arr_save['sku'] = '';
                    $arr_save['name']   =   $costing['product_name'];
                    $arr_save['product_type'] = (isset($costing['product_type']) ? $costing['product_type'] : 'Product');
                    $arr_save['company_name']   = (isset($quotation['company_name']) ? $quotation['company_name'] : '' );
                    $arr_save['company_id']     = (isset($quotation['company_id'])&&is_object($quotation['company_id']) ? new MongoId($quotation['company_id']) : '' );
                    $arr_save['sizew']  = 12;
                    $arr_save['sizew_unit'] = 'in';
                    $arr_save['sizeh']  = 12;
                    $arr_save['sizeh_unit'] = 'in';
                    $arr_save['oum']    = (isset($costing['oum']) ? $costing['oum'] : 'unit');
                    $arr_save['sell_by']    = (isset($costing['sell_by']) ? $costing['sell_by'] : 'unit');
                    $arr_save['sell_price'] = (isset($costing['unit_price']) ? $costing['unit_price'] : 0);
                    $arr_save['oum_depend']    = (isset($costing['oum']) ? $costing['oum'] : 0);
                    $arr_save['unit_price'] = (isset($costing['unit_price']) ? $costing['unit_price'] : 0);
                    $arr_save['cost_price'] = (isset($costing['unit_price']) ? $costing['unit_price'] : 0);
                    $arr_save['markup'] = (isset($costing['markup']) ? $costing['markup'] : 0);
                    $arr_save['margin'] = (isset($costing['margin']) ? $costing['margin'] : 0);
                    $arr_save['quantity'] = (isset($costing['quantity']) ? $costing['quantity'] : 1);
                    $arr_save['view_in_detail'] = (isset($costing['view_in_detail'])&&$costing['view_in_detail']==1 ? 1 : 0);
                    $this->Product->save($arr_save);
                    $id = new MongoId($this->Product->mongo_id_after_save);
                    $quotation['costing'][$costing_key]['product_id'] = $id;
                    $quotation['costing'][$costing_key]['code'] = $arr_save['code'];
                } else { //Truong hop product co ID
                    $id = new MongoId($costing['product_id']);
                    $arr_save['sku'] = (isset($costing['sku']) ? $costing['sku'] : '');
                    $arr_save['name'] = (isset($costing['product_name']) ? $costing['product_name'] : '');
                    $arr_save['code'] = (isset($costing['code']) ? $costing['code'] : '');
                    $arr_save['product_type'] = (isset($costing['product_type']) ? $costing['product_type'] :'Product');
                    $arr_save['category'] = (isset($costing['category']) ? $costing['category'] : '');
                    $arr_save['company_id'] = (isset($costing['company_id'])&&is_object($costing['company_id']) ? $costing['company_id'] : '');
                    $arr_save['unit_price'] = (isset($costing['unit_price']) ?(float)$costing['unit_price'] : 0);
                    $arr_save['oum'] = (isset($costing['oum']) ? $costing['oum'] : 'unit');
                    $arr_save['markup'] = (isset($costing['markup']) ? $costing['markup'] : 0);
                    $arr_save['margin'] = (isset($costing['margin']) ? $costing['margin'] : 0);
                    $arr_save['quantity'] = (isset($costing['quantity']) ? $costing['quantity'] : 1);
                }
                $line_entry_madeup[$i] = array(
                                            'deleted'       => false,
                                            'sku'           => $arr_save['sku'],
                                            'product_name'  => $arr_save['name'],
                                            'product_id'    => $id,
                                            'product_type'  => $arr_save['product_type'],
                                            'product_code'  => $arr_save['code'],
                                            'category'      => $arr_save['category'],
                                            'company_id'    => $arr_save['company_id'],
                                            'unit_price'    => $arr_save['unit_price'],
                                            'oum'           => $arr_save['oum'],
                                            'markup'        => $arr_save['markup'],
                                            'margin'        => $arr_save['margin'],
                                            'quantity'      => $arr_save['quantity']
                                            );
                unset($quotation['costing'][$costing_key]['unit_price']);
                unset($quotation['costing'][$costing_key]['oum']);
                unset($quotation['costing'][$costing_key]['oum']);
                unset($quotation['costing'][$costing_key]['markup']);
                unset($quotation['costing'][$costing_key]['margin']);
                unset($quotation['costing'][$costing_key]['sub_total']);
                unset($quotation['costing'][$costing_key]['sell_by']);
                $i++;

            }
        }
        //End add costing to Product
        if(!empty($line_entry_madeup))
            $arr_line_entry['madeup'] = $line_entry_madeup;
        $this->Product->save($arr_line_entry);
        $quotation['products'][$key]['code'] = $arr_line_entry['code'];
        $quotation['products'][$key]['products_id'] = $line_entry_id;
        $this->opm->save($quotation);
    }


    function send_rfq_email(){
        if(isset($_POST['rfq_id'])){
            $id = $this->get_id();
            $data = $this->opm->select_one(array('_id' => new MongoId($id)), array('company_id','company_name','job_number','job_name','job_id','code','products','rfqs'));
            $rfq = isset($data['rfqs'][$_POST['rfq_id']]) ? $data['rfqs'][$_POST['rfq_id']] : array();
            $content = '';
            if(isset($rfq['include_name_details']) && $rfq['include_name_details']){
                $product = $data['products'][$rfq['rfq_code']];
                $content .= 'Product name: '.(isset($product['products_name']) ? $product['products_name'] : '');
                $content .= '<br />SKU: '.(isset($product['sku']) ? $product['sku'] : '');
            }
            $content .= '<br />Unit price quoted: '.(isset($rfq['unit_price_quoted']) ? $this->opm->format_currency((float)$rfq['unit_price_quoted']) : '');
            $content .= '<br />Supplier quote ref: '.(isset($rfq['supplier_quote_ref']) ? $rfq['supplier_quote_ref'] : '');
            $this->selectModel('Communication');
            $arr_save = array(
                'deleted'           => false,
                'code'              => $this->Communication->get_auto_code('code'),
                'comms_type'        => 'Email',
                'comms_date'        => new MongoDate(),
                'comms_status'      => 'Draft',
                'sign_off'          => 0,
                'include_signature' => 0,
                'email_cc'          => 'jobtraq@anvydigital.com',
                'email_bcc'         => '',
                'identity'          => '',
                'internal_notes'    => '',
                'company_id'        => (isset($rfq['company_id']) ? $rfq['company_id'] : ''),
                'company_name'      => (isset($rfq['company_name']) ? $rfq['company_name'] : ''),
                'module'            => 'Quotation',
                'module_id'         => new MongoId($data['_id']),
                'content'           => '',
                'contact_id'        => (isset($rfq['first_name_id']) ? $rfq['first_name_id'] : ''),
                'contact_name'      => (isset($rfq['first_name']) ? $rfq['first_name'] : ''),
                'last_name'         => '',
                'position'          => '',
                'salutation'        => '',
                'toother'           => '',
                'email'             => (isset($rfq['supplier_email']) ? $rfq['supplier_email'] : ''),
                'phone'             =>  '',
                'fax'               =>  '',
                'job_number'        => (isset($data['job_number']) ? $data['job_number'] : ''),
                'job_name'          => (isset($data['job_name']) ? $data['job_name'] : ''),
                'job_id'            => (isset($data['job_id'])&&$data['job_id']!='' ? new MongoId($data['job_id']) : ''),
                'name'              => "JobTraq - Quotation #{$data['code']} - RFQ".(isset($rfq['rfq_no']) ? ' '.$rfq['rfq_no'] :  ''),
                'content'           => $content,
                'rfq_email'         => true,
                'rfq_id'            => $_POST['rfq_id']
                );
            if(isset($rfq['include_signature']) && !$rfq['include_signature'])
                $arr_save['use_no_signature'] = true;
            $this->Communication->save($arr_save);
            echo URL.'/communications/entry/'.$this->Communication->mongo_id_after_save;
        }
        die;
    }

    public function rfqs_list() {
        if($this->check_permission($this->name.'_@_rfqs_tab_@_view')){
            $this->set('return_mod', true);
            $this->set('return_title', 'Request for Quotes');
            $this->set('return_link', URL . '/quotations/entry');
            $opname = 'products';
            $arr_set = $this->opm->arr_settings;
            $subdatas = $arr_subsetting = array();
            $quote_code = $sumrfq = 0;
            if ($this->params->params['pass'][1] != '') {
                //DATA: quotation line details
                $idsub = $this->params->params['pass'][1];
                $arr_ret = $this->line_entry_data($opname);
                $subdatas['quotation_line_details'] = array_filter($arr_ret['products'], function($arr) use($idsub){
                    return isset($arr['deleted']) && !$arr['deleted']
                            && isset($arr['_id']) && $arr['_id'] == $idsub;
                });
                $subdatas['quotation_line_details'] = reset($subdatas['quotation_line_details']);
                //DATA: rfqs list
                $query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())));
                if (isset($query['rfqs']) && is_array($query['rfqs']) && count($query['rfqs']) > 0) {
                    $arr_temp = array();
                    $id = $this->get_id();
                    $links = "onclick=\" window.location.assign('" . URL . "/quotations/rfqs_entry/" . $id . "/";
                    foreach ($query['rfqs'] as $kss => $vss) {
                        if (!$vss['deleted'] && $vss['rfq_code'] == $idsub) {
                            $arr_temp[$kss] = $vss;
                            $arr_temp[$kss]['_id'] = $arr_temp[$kss]['rfqs_id'] = $kss;
                            $arr_temp[$kss]['rfq_date'] = $vss['rfq_date']->sec;
                            if (isset($vss['deadline_date']) && is_object($vss['deadline_date']))
                                $arr_temp[$kss]['deadline_date'] = $vss['deadline_date']->sec;
                            else
                                $arr_temp[$kss]['deadline_date'] = '';
                            $arr_temp[$kss]['set_link'] = $links . $arr_temp[$kss]['_id'] . "');\"";
                        }
                    }
                    $subdatas['rfqs'] = $arr_temp;
                    $sumrfq = count($query['rfqs']);
                } else
                    $subdatas['rfqs'] = array();

                if (isset($query['code']))
                    $quote_code = $query['code'];
            }
            //VIEW: quotation line details
            $strs = $arr_set['relationship']['line_entry']['block']['products']['field'];
            $arr_subsetting['quotation_line_details'] = array(
                'code' => $strs['code']['name'],
                'products_name' => 'Description',
                'sell_price' => $strs['sell_price']['name'],
                'oum' => $strs['oum']['name'],
                'quantity' => $strs['quantity']['name'],
                'sub_total' => $strs['sub_total']['name'],
                'taxper' => $strs['taxper']['name'],
                'tax' => $strs['tax']['name'],
                'amount' => $strs['amount']['name'],
                'rfq_approve' => 'Approve'
            );

            //VIEW: rfqs list
            $arr_subsetting['rfqs'] = $arr_set['relationship']['rfqs']['block'];
            $arr_subsetting['rfqs']['rfqs']['title'] = "RFQ's for this item";
            $arr_subsetting['rfqs']['rfqs']['css'] = 'width:66%;float:right;';
            $arr_subsetting['rfqs']['rfqs']['height'] = '520';
            $arr_subsetting['rfqs']['rfqs']['delete'] = '2';
            if($this->check_permission($this->name.'_@_rfqs_tab_add'))
                $arr_subsetting['rfqs']['rfqs']['add'] = 'Create record';
            $arr_subsetting['rfqs']['rfqs']['footlink'] = array('label' => 'Click an arrow to view an RFQ', 'link' => '');
            $arr_subsetting['rfqs']['rfqs']['field']['rfq_status']['width'] = 7;
            $arr_subsetting['rfqs']['rfqs']['field']['rfq_no']['width'] = 5;
            $arr_subsetting['rfqs']['rfqs']['field']['company_name']['width'] = 20;
            $arr_subsetting['rfqs']['rfqs']['field']['company_name']['type'] = 'text';
            $arr_subsetting['rfqs']['rfqs']['field']['unit_price_quoted']['width'] = 12;
            $arr_subsetting['rfqs']['rfqs']['field']['rfq_date']['width'] = 9;
            $arr_subsetting['rfqs']['rfqs']['field']['deadline_date']['width'] = 9;
            $arr_subsetting['rfqs']['rfqs']['field']['late']['width'] = 4;
            $arr_subsetting['rfqs']['rfqs']['field']['internal_notes']['width'] = 18;
            unset($arr_subsetting['rfqs']['rfqs']['custom_box_bottom']);
            unset($arr_subsetting['rfqs']['rfqs']['custom_box_top']);
            unset($arr_subsetting['rfqs']['rfqs']['field']['print']);
            unset($arr_subsetting['rfqs']['rfqs']['field']['code']);
            unset($arr_subsetting['rfqs']['rfqs']['field']['name_details']);
            unset($arr_subsetting['rfqs']['rfqs']['field']['name_details']);
            unset($arr_subsetting['rfqs']['rfqs']['field']['employee_name']);
            unset($arr_subsetting['rfqs']['rfqs']['field']['rfq_code']);

            $this->set('subdatas', $subdatas);
            $this->set('arr_subsetting', $arr_subsetting);
            $this->set('line_sum', 18);
            $this->set('quoteline', 'quotation_line_details');
            $this->set('quote_code', $quote_code);
            $this->set('sumrfq', $sumrfq);

            $this->set('subitems', $idsub);
            $this->set('employee_id', $this->opm->user_id());
            $this->set('employee_name', $this->opm->user_name());
        } else
            $this->error_auth();
    }

    //Detail RFQ's
    public function rfqs_entry() {
        if($this->check_permission($this->name.'_@_rfqs_tab_@_view')){
            $this->set('return_mod', true);
            $this->set('return_title', 'Request for Quotes');
            $return_link = URL . '/quotations/entry/';
            $arr_set = $this->opm->arr_settings;
            $subdatas = $datas = $arr_subsetting = array();
            $quote_pro_name = $quote_pro_code = '';

            if ($this->params->params['pass'][1] != '') {
                //data
                $query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())));
                $idrfq = $this->params->params['pass'][1];
                $this->set('idrfq', $idrfq);
                if (isset($query['rfqs']) && is_array($query['rfqs']) && count($query['rfqs']) > 0 && isset($query['rfqs'][$idrfq])) {
                    $datas = $query['rfqs'][$idrfq];
                    $idpro = $query['rfqs'][$idrfq]['rfq_code'];
                }

                if (isset($query['products'][$idpro]['products_name']))
                    $quote_pro_name = $query['products'][$idpro]['products_name'];
                if (isset($query['products'][$idpro]['code']))
                    $quote_pro_code = $query['products'][$idpro]['code'];
                if (isset($idpro))
                    $return_link = URL . '/quotations/rfqs_list/' . $this->get_id() . '/' . $idpro;
                if (isset($query['rfqs'][$idrfq]['internal_notes']))
                    $subdatas['internal_notes']['internal_notes'] = $query['rfqs'][$idrfq]['internal_notes'];
                if (isset($query['rfqs'][$idrfq]['details_for_request']))
                    $subdatas['details_for_request']['details_for_request'] = $query['rfqs'][$idrfq]['details_for_request'];
            }
            $this->set('return_link', $return_link);
            $this->set('quote_pro_name', $quote_pro_name);
            $this->set('quote_pro_code', $quote_pro_code);

            $arr_subsetting['rfqs'] = $arr_set['relationship']['rfqs']['block']['rfqs'];
            $edit_box = array(
                'title' => 'RFQ details',
                'line_sum' => 14,
                'more_html' => '',
            );
            $this->set('box_note', $arr_subsetting['rfqs']['field']['internal_notes']['block']);
            $this->set('box_detail', $arr_subsetting['rfqs']['field']['details_for_request']['block']);

            $arr_new_setting = array(
                'rfq_no',
                'company_name',
                'company_id',
                'first_name',
                'first_name_id',
                'last_name',
                'supplier_email',
                'employee_name',
                'employee_id',
                'rfq_date',
                'rfq_status',
                'include_name_details',
                'include_signature',
                'deadline_date',
                'supplier_quote_ref',
                'unit_price_quoted',
            );

            $arr_subsetting['rfqs']['field'] = $this->opm->re_array_fields($arr_new_setting, $arr_subsetting['rfqs']['field']);
            $arr_subsetting['rfqs']['field']['first_name']['type'] = 'relationship';
            $arr_subsetting['rfqs']['field']['company_name']['type'] = 'relationship';
            $arr_subsetting['rfqs']['field']['last_name']['type'] = 'text';
            $arr_subsetting['rfqs']['field']['supplier_email']['type'] = 'text';
            $arr_subsetting['rfqs']['field']['employee_name']['type'] = 'relationship';
            $arr_subsetting['rfqs']['field']['include_name_details']['type'] = 'checkbox';
            $arr_subsetting['rfqs']['field']['include_name_details']['label'] = $quote_pro_name;
            $arr_subsetting['rfqs']['field']['include_signature']['type'] = 'checkbox';
            $arr_subsetting['rfqs']['field']['deadline_date']['name'] = __('Tender deadline');
            $arr_subsetting['rfqs']['field']['supplier_quote_ref']['type'] = 'text';
            $arr_subsetting['rfqs']['field']['unit_price_quoted']['type'] = 'price';

            //set data
            foreach ($arr_new_setting as $kss) {
                if (isset($datas[$kss])) {
                    if (preg_match("/_date$/", $kss) && is_object($datas[$kss]))
                        $arr_subsetting['rfqs']['field'][$kss]['default'] = date("d M, Y", $datas[$kss]->sec);
                    else if (preg_match("/_date$/", $kss) && !is_object($datas[$kss]))
                        $arr_subsetting['rfqs']['field'][$kss]['default'] = 'non date';
                    else
                        $arr_subsetting['rfqs']['field'][$kss]['default'] = $datas[$kss];
                }
            }
            if(!$this->check_permission($this->name.'_@_rfqs_tab_@_delete'))
                unset($arr_subsetting['rfqs']['delete']);
            if(!$this->check_permission($this->name.'_@_rfqs_tab_@_delete'))
                foreach($arr_subsetting['rfqs']['field'] as $key=>$value)
                    $arr_subsetting['rfqs']['field'][$key]['lock'] = 1;
            $this->set('arr_subsetting', $arr_subsetting);
            $this->set('blockname', 'rfqs');
            $this->set('subdatas', $subdatas);
            $this->set('edit_box', $edit_box);
            $this->selectModel('Setting');
            $arr_options['rfq_status'] = $this->Setting->select_option_vl(array('setting_value' => $arr_subsetting['rfqs']['field']['rfq_status']['droplist']));
            $this->set('arr_options', $arr_options);
        } else
            $this->error_auth();
    }


    //address
    public function set_entry_address($arr_tmp, $arr_set) {
        $address_fset = array('address_1', 'address_2', 'address_3', 'town_city', 'country', 'province_state', 'zip_postcode');
        $address_value = $address_province_id = $address_country_id = $address_province = $address_country = array();
        $address_controller = array('invoice', 'shipping');
        $address_value['invoice'] = $address_value['shipping'] = array('', '', '', '', "CA", '', '');
        $this->set('address_controller', $address_controller); //set
        $address_key = array('invoice', 'shipping');
        $this->set('shipping_contact_name', (isset($arr_tmp['shipping_address'][0]['shipping_contact_name']) ? $arr_tmp['shipping_address'][0]['shipping_contact_name'] : ''));
        $this->set('address_key', $address_key); //set
        $address_country = $this->country();
        foreach ($address_key as $kss => $vss) {
            //neu ton tai address trong data base
            if (isset($arr_tmp[$vss . '_address'][0])) {
                $arr_temp_op = $arr_tmp[$vss . '_address'][0];
                for ($i = 0; $i < count($address_fset); $i++) { //loop field and set value for display
                    if (isset($arr_temp_op[$vss . '_' . $address_fset[$i]])) {
                        $address_value[$vss][$i] = $arr_temp_op[$vss . '_' . $address_fset[$i]];
                    } else {
                        $address_value[$vss][$i] = '';
                    }
                }//pr($arr_temp_op);die;
                //get province list and country list

                if (isset($arr_temp_op[$vss . '_country_id']))
                    $address_province[$vss] = $this->province($arr_temp_op[$vss . '_country_id']);
                else
                    $address_province[$vss] = $this->province();
                //set province
                if (isset($arr_temp_op[$vss . '_province_state_id']) && $arr_temp_op[$vss . '_province_state_id'] != '' && isset($address_province[$vss][$arr_temp_op[$vss . '_province_state_id']]))
                    $address_province_id[$kss] = $arr_temp_op[$vss . '_province_state_id'];
                else if (isset($arr_temp_op[$vss . '_province_state']))
                    $address_province_id[$kss] = $arr_temp_op[$vss . '_province_state'];
                else
                    $address_province_id[$kss] = '';

                //set country
                if (isset($arr_temp_op[$vss . '_country_id'])) {
                    $address_country_id[$kss] = $arr_temp_op[$vss . '_country_id'];
                    $address_province[$vss] = $this->province($arr_temp_op[$vss . '_country_id']);
                } else {
                    $address_country_id[$kss] = "CA";
                    $address_province[$vss] = $this->province("CA");
                }

                $address_add[$vss] = '0';
                //chua co address trong data
            } else {
                $address_country_id[$kss] = "CA";
                $address_province[$vss] = $this->province("CA");
                $address_add[$vss] = '1';
            }
        }
        //pr($address_province);
        $this->set('address_value', $address_value);
        $address_hidden_field = array('invoice_address', 'shipping_address');
        $this->set('address_hidden_field', $address_hidden_field); //set
        $address_label[0] = $arr_set['field']['panel_2']['invoice_address']['name'];
        $address_label[1] = $arr_set['field']['panel_2']['shipping_address']['name'];
        $this->set('address_label', $address_label); //set
        $address_conner[0]['top'] = 'hgt fixbor';
        $address_conner[0]['bottom'] = 'fixbor2 jt_ppbot';
        $address_conner[1]['top'] = 'hgt';
        $address_conner[1]['bottom'] = 'fixbor3 jt_ppbot';
        $this->set('address_conner', $address_conner); //set
        $this->set('address_country', $address_country); //set
        $this->set('address_country_id', $address_country_id); //set
        $this->set('address_province', $address_province); //set
        $this->set('address_province_id', $address_province_id); //set
        $this->set('address_more_line', 2); //set
        $this->set('address_onchange', "save_address_pr('\"+keys+\"');");
        if (isset($arr_tmp['company_id']) && strlen($arr_tmp['company_id']) == 24)
            $this->set('address_company_id', 'company_id');
        if (isset($arr_tmp['contact_id']) && strlen($arr_tmp['contact_id']) == 24)
            $this->set('address_contact_id', 'contact_id');
        $this->set('address_add', $address_add);
    }

    // Popup form orther module
    public function popup($key = '') {
        $this->set('key', $key);
        $limit = 100;
        $skip = 0;
        $cond = array();
        $this->identity($cond);
        // Nếu là search GET
        if (!empty($_GET)) {

            $tmp = $this->data;

            if (isset($_GET['company_id'])) {
                $cond['company_id'] = new MongoId($_GET['company_id']);
                $tmp['Quotation']['company'] = $_GET['company_name'];
            }

            $this->data = $tmp;
        }

        // Nếu là search theo phân trang
        $page_num = 1;
        if (isset($_POST['pagination']) && $_POST['pagination']['page-num'] > 0) {
            // $limit = $_POST['pagination']['page-list'];
            $page_num = $_POST['pagination']['page-num'];
            $limit = $_POST['pagination']['page-list'];
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
        if (!empty($this->data) && !empty($_POST) && isset($this->data['Quotation'])) {
            $arr_post = $this->Common->strip_search($this->data['Quotation']);
            if (isset($arr_post['contact_name']) && strlen($arr_post['contact_name']) > 0) {
                $cond['contact_name'] = new MongoRegex('/' . trim($arr_post['contact_name']) . '/i');
            }

            if (strlen($arr_post['company']) > 0) {
                $cond['company_name'] = new MongoRegex('/' . trim($arr_post['company']) . '/i');
            }
        }
        unset($_GET['_']);
        $cache_key = md5(serialize($_GET));
        $no_cache = true;
        if(empty($_POST) ){
            $arr_quotation = Cache::read('popup_quotation_'.$cache_key);
            if($arr_quotation)
                $no_cache = false;
        }
        $this->selectModel('Quotation');
        if($no_cache){
            $arr_quotation = $this->Quotation->select_all(array(
                'arr_where' => $cond,
                'arr_order' => $arr_order,
                'arr_field' => array('company_id', 'company_name', 'our_rep', 'our_rep_id', 'contact_name','contact_id','quotation_date','quotation_status','quotation_type','name','heading','code','payment_terms','payment_due_date'),
                'limit' => $limit,
                'skip' => $skip
            ));
            if(empty($_POST))
                Cache::write('popup_quotation_'.$cache_key,iterator_to_array($arr_quotation));
        }

        $this->set('arr_quotation', $arr_quotation);

        $total_page = $total_record = $total_current = 0;
        if (is_object($arr_quotation)) {
            $total_current = $arr_quotation->count(true);
            $total_record = $arr_quotation->count();
            if ($total_record % $limit != 0) {
                $total_page = floor($total_record / $limit) + 1;
            } else {
                $total_page = $total_record / $limit;
            }
        } else if(is_array($arr_quotation)){
            $total_current = count($arr_quotation);
            $total_record = $this->Quotation->count($cond);
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
    public function duplicate_revise_quotation($type = 'duplicate') {
        if(!$this->check_permission($this->name.'_@_options_@_duplicate_current_quotation'))
            $this->error_auth();
        $id = $this->get_id();
        $data = $this->opm->select_one(array('_id' => new MongoId($id),'products'=>array('$elemMatch'=>array('deleted'=>false))));
        if(empty($data) || !isset($data['products']) || isset($data['products'])&&empty($data['products']))
        {
            echo json_encode(array('status'=>'error','message'=>'No item has been entered in this transaction yet.'));
            die;
        }
        if(isset($_POST['type'])){
            if ($_POST['type'] == 'new_rev') {
                $old_data = $data;
                $old_data['quotation_status'] = 'Amended';
                $data['quotation_status'] = 'In progress';
                $data['revise_id'] = $data['_id'];
                $data['code'] = $this->opm->get_auto_code('code');
                $data['revision_id'] = new MongoId($id);
                unset($data['_id']);
                unset($data['created_by']);
                unset($data['date_modified']);
                unset($data['modified_by']);
                $this->opm->save($old_data);
                if ($this->opm->save($data)) {
                    $or_where = array(
                        array("_id" => new MongoId($id)),
                        array("revision_id" => new MongoId($id)),
                    );
                    $arr_where= array(array('values' => $or_where, 'operator' => 'or'));
                    $this->Session->write($this->name . '_where', $arr_where);
                    $new_id = $this->opm->mongo_id_after_save;
                    echo json_encode(array('status'=>'ok','url'=>URL.'/quotations/entry/'.$new_id));
                }
            }
            else if ($_POST['type'] == 'duplicate') {
                $data['duplicate_id'] = $data['_id'];
                unset($data['_id']);
                unset($data['created_by']);
                unset($data['date_modified']);
                unset($data['modified_by']);

                $data['code'] = $this->opm->get_auto_code('code');
                $data['quotation_status'] = 'In progress';
                $data['duplicate_id'] = new MongoId($id);
                if ($this->opm->save($data)) {
                    $or_where = array(
                        array("_id" => new MongoId($id)),
                        array("duplicate_id" => new MongoId($id)),
                    );
                    $arr_where= array(array('values' => $or_where, 'operator' => 'or'));
                    $this->Session->write($this->name . '_where', $arr_where);
                    $new_id = $this->opm->mongo_id_after_save;
                    echo json_encode(array('status'=>'ok','url'=>URL.'/quotations/entry/'.$new_id));
                }
            }
        }
        die();
    }



	/*
      Tung
      Option report

     */
    public function report_pdf($data) {

        App::import('Vendor', 'xtcpdf');
        $pdf = new XTCPDF();
        $textfont = 'freesans'; // looks better, finer, and more condensed than 'dejavusans'
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Anvy Digital');
        $pdf->SetTitle('Anvy Digital Quotation');
        $pdf->SetSubject('Quotation');
        $pdf->SetKeywords('Quotation, PDF');

        // set default header data
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(true);

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(2);

        // set margins
        $pdf->SetMargins(10, 3, 10);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // ---------------------------------------------------------
        // set font
        $pdf->SetFont($textfont, '', 9);

        // add a page
        $pdf->AddPage();


        // writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
        // writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
        // create some HTML content


        $html = '
        <table cellpadding="2" cellspacing="0" style="width:100%; margin: 0px auto">
           <tbody>
              <tr>
                 <td width="32%" valign="top" style="color:#1f1f1f;">
                    <img src="img/logo_anvy.jpg" alt="" margin-bottom:0px>
                    <p style="margin-bottom:5px; margin-top:0px;">3145 - 5th Ave NE<br/ >Calgary  AB  T2A  6A3</p>
                 </td>
                 <td width="68%" valign="top" align="right">
                    <table>
                       <tbody>
                          <tr>
                             <td width="25%">&nbsp;</td>
                             <td width="75%">
                                <span style="text-align:right; font-size:21px; font-weight:bold; color: #919295;">
                                    ' . $data['title'] . '<br />';
        if (isset($data['date_equals']))
            $date = '<span style="font-size:12px; font-weight:normal">' . $data['date_equals'] . '</span>';
        else {
            if (isset($data['date_from']) && isset($data['date_to']))
                $date = '<span style="font-size:12px; font-weight:normal">( ' . $data['date_from'] . ' - ' . $data['date_to'] . ' )</span>';
            else if (isset($data['date_from']))
                $date = '<span style="font-size:12px; font-weight:normal">From ' . $data['date_from'] . '</span>';
            else if (isset($data['date_to']))
                $date = '<span style="font-size:12px; font-weight:normal">To ' . $data['date_to'] . '</span>';
            else
                $date = '';
        }
        $html .= $date;
        $html .= '
                                </span>
                                <div style=" border-bottom: 1px solid #cbcbcb;height:5px;width:50%">&nbsp;</div>
                             </td>
                          </tr>
                          <tr>
                             <td colspan="2">
                                    <span style="font-weight:bold;">Printed at: </span>' . $data['current_time'] . '
                             </td>
                          </tr>
                       </tbody>
                    </table>
                 </td>
              </tr>
           </tbody>
        </table>
        <div class="option">' . @$data['heading'] . '</div>
        <br />
        <br />
        <div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both"></div>
        <br />
        <style>
           td{
           line-height:2px;
           }
           td.first{
            text-align: center;
           border-left:1px solid #e5e4e3;
           }
           td.end{
           border-right:1px solid #e5e4e3;
           }
           td.top{
           color:#fff;
           text-align: center;
           font-weight:bold;
           background-color:#911b12;
           border-top:1px solid #e5e4e3;
           }
           td.bottom{
           border-bottom:1px solid #e5e4e3;
           }
           td.content{
            border-right: 1px solid #E5E4E3;
            text-align: center;
           }
           .option{
           color: #3d3d3d;
           font-weight:bold;
           font-size:20px;
           text-align: center;
           width:100%;
           }
           table.maintb{
           }
        </style>
        <br />
        ';
        $html .= $data['html_loop'];

        $pdf->writeHTML($html, true, false, true, false, '');



        // reset pointer to the last page
        $pdf->lastPage();



        // ---------------------------------------------------------
        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        //$pdf->Output('example_001.pdf', 'I');




        $pdf->Output('upload/' . $data['filename'] . '.pdf', 'F');
    }


    public function option_summary_customer_find($type = '') {
        if(!$this->check_permission($this->name.'_@_options_@_report_by_customer_summary'))
            $this->error_auth();
        $arr_data['quotations_status'] = $this->Setting->select_option_vl(array('setting_value' => 'quotations_status'));
        $arr_data['quotations_type'] = $this->Setting->select_option_vl(array('setting_value' => 'quotations_type'));
        $this->set('arr_data', $arr_data);
        $report_type = 'summary';
        if($type=='area')
            $report_type = 'area_summary';
        $this->set('report_type',$report_type);
    }
    public function customer_report($type='') {
        $arr_data = array();
        if(isset($_GET['print_pdf'])){
            $arr_data = Cache::read('quotations_customer_report_'.$type);
            Cache::delete('quotations_customer_report_'.$type);
        } else {
            if (isset($_POST) && !empty($_POST)) {
                $arr_post = $_POST;
                $arr_post = $this->Common->strip_search($arr_post);
                $arr_where = array('company_id'=>array('$exists' => true,'$ne'=>''));
                $arr_where['deleted'] = false;
                if($arr_post['status'] && $arr_post['status'] != '')
                    $arr_where['quotation_status'] = $arr_post['status'];
                //Check loại trừ cancel thì bỏ các status bên dưới
                if(isset($arr_post['is_not_cancel']) && $arr_post['is_not_cancel']==1){
                    $arr_where['quotation_status'] = array('$nin'=>array('Rejected','Cancelled','Amended'));
                    //Tuy nhiên nếu ở ngoài combobox nếu có chọn, thì ưu tiên nó, set status lại
                    if(isset($arr_post['status'])&&$arr_post['status']!='')
                        $arr_where['quotation_status'] = $arr_post['status'];
                }
                if ($arr_post['type'] && $arr_post['type'] != '')
                    $arr_where['quotation_type'] = $arr_post['type'];
                if (isset($arr_post['company']) &&$arr_post['company'] != '')
                    $arr_where['company_name'] = new MongoRegex('/' . trim($arr_post['company']) . '/i');
                if (isset($arr_post['contact'])&&$arr_post['contact'] != '')
                    $arr_where['contact_name'] = new MongoRegex('/' . trim($arr_post['contact']) . '/i');
                if (isset($arr_post['job_no'])&&$arr_post['job_no'] != '')
                    $arr_where['job_number'] = new MongoRegex('/' . trim($arr_post['job_no']) . '/i');
                //tim chinh xac ngay
                if (isset($arr_post['date_equals'])&&$arr_post['date_equals'] != '') {
                    $arr_where['quotation_date']['$gte'] = new MongoDate($this->Common->strtotime($arr_post['date_equals'] . '00:00:00'));
                    $arr_where['quotation_date']['$lt'] = new MongoDate($this->Common->strtotime($arr_post['date_equals'] . '23:59:59'));
                } else { //ngay nam trong khoang
                    //neu chi nhap date from
                    if (isset($arr_post['date_from'])&&$arr_post['date_from']!='') {
                        $arr_where['quotation_date']['$gte'] = new MongoDate($this->Common->strtotime($arr_post['date_from'] . '00:00:00'));
                    }
                    //neu chi nhap date to
                    if (isset($arr_post['date_to'])&&$arr_post['date_to']!='') {
                        $arr_where['quotation_date']['$lte'] = new MongoDate($this->Common->strtotime($arr_post['date_to'] . '23:59:59'));
                    }
                }
                if (isset($arr_post['our_rep'])&&$arr_post['our_rep'] != '')
                    $arr_where['our_rep'] = new MongoRegex('/' . $arr_post['our_rep'] . '/i');
                if (isset($arr_post['our_csr'])&&$arr_post['our_csr'] != '')
                    $arr_where['our_csr'] = new MongoRegex('/' . $arr_post['our_csr'] . '/i');
                $this->selectModel('Quotation');
                //lay het quotation, voi where nhu tren va lay sum_amount giam dan
               /* $quotation = $this->Quotation->select_all(array(
                    'arr_where' => $arr_where,
                    'arr_order' => array(
                        'sum_sub_total' => -1
                    ),
                    'arr_field' => array('quotation_type','code','quotation_date','heading','quotation_status','our_rep','sum_sub_total','company_id','company_name'),
                    'limit' => 9999999
                ));*/
                $count = $this->Quotation->count($arr_where,array('limit' => 9999999));
                if ($count == 0) {
                    echo 'empty';
                } else if(!$this->request->is('ajax')) {
                    $minimum = 50;
                    $this->selectModel('Stuffs');
                    $product = $this->Stuffs->select_one(array('value'=>"Minimum Order Adjustment"),array('product_id'));
                    if(isset($product['product_id'])&&is_object($product['product_id'])){
                        $this->selectModel('Product');
                        $product = $this->Product->select_one(array('_id'=> new MongoId($product['product_id'])),array('sell_price'));
                        $minimum = $product['sell_price'];
                    }
                    if ($arr_post['report_type'] == 'summary')
                        $arr_data = $this->summary_customer_report($arr_post, $arr_where,$minimum,$product['_id']);
                    else if ($arr_post['report_type'] == 'detailed')
                        $arr_data = $this->detailed_customer_report($arr_post, $arr_where,$minimum,$product['_id']);
                    else if ($arr_post['report_type'] == 'area_summary')
                        $arr_data = $this->summary_area_customer_report($arr_post, $arr_where,$minimum,$product['_id']);
                    else if ($arr_post['report_type'] == 'area_detailed')
                        $arr_data = $this->detailed_area_customer_report($arr_post, $arr_where,$minimum,$product['_id']);
                    Cache::write('quotations_customer_report_'.$type, $arr_data);
                }
            }
        }
        if($this->request->is('ajax'))
            die;
        else
            $this->render_pdf($arr_data);
    }
    function get_sum_sub_total($product_id,$origin_minimum, $arr_where,$is_area = false){
        $arr_where['deleted'] = false;
        $arr_companies = $this->opm->collection->group(array('company_id' => true), array('companies' => array()), 'function (obj, prev) { prev.companies.push({_id : obj.company_id}); }',array('condition' => $arr_where));
        if( $arr_companies['ok'] && !empty($arr_companies['retval']) ) {
            $arr_companies = $arr_companies['retval'];
        }
        $arr_data = array();
        foreach($arr_companies as $company){
            $_id = $company['company_id'];
            $sum = 0;
            $minimum = $origin_minimum;
            $company = $this->Company->select_one(array('_id'=>$_id),array('name','our_rep','addresses','addresses_default_key', 'pricing'));
            if(isset($company['pricing'])){
                foreach($company['pricing'] as $pricing){
                    if(isset($pricing['deleted'])&&$pricing['deleted']) continue;
                    if((string)$pricing['product_id']!=(string)$product_id) continue;
                    if(!isset($pricing['price_break']) || empty($pricing['price_break'])) continue;
                    $price_break = reset($pricing['price_break']);
                    $minimum =  (float)$price_break['unit_price']; break;
                }
            }
            $data = $this->db->command(array(
                'mapreduce'     => 'tb_quotation',
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
                                       'company_id' => new MongoId($_id),
                                       'deleted'    => false,
                                       'quotation_status'  => array('$ne' => 'Cancelled'),
                                    ),
                'out'           => array('merge' => 'tb_result')
            ));
            if( isset($data['ok']) ) {
                $result = $this->db->selectCollection('tb_result')->findOne();
                $this->db->tb_sum_result->remove(array('_id' => 'total'));
                $sum = isset($result['value']) ? $result['value'] : 0;

            }
            $_id = (string)$_id;
            $arr_data[$_id]['sum_sub_total'] = $sum;
            $arr_data[$_id]['company_name'] = (isset($company['name']) ? $company['name'] : '');
            $arr_data[$_id]['our_rep'] = (isset($company['our_rep']) ? $company['our_rep'] : '');
            $arr_data[$_id]['addresses'] = (isset($company['addresses']) ? $company['addresses'] : '');
            $arr_data[$_id]['addresses_default_key'] = (isset($company['addresses_default_key']) ? $company['addresses_default_key'] : 0);
            $arr_data[$_id]['minimum'] = $minimum;
            $arr_data[$_id]['number_of_quotations'] = $this->opm->count(array_merge($arr_where,array('company_id'=>new MongoId($_id))));
        }
        return $arr_data;
    }
    public function summary_customer_report($data, $arr_where, $minimum, $product_id) {
        //--------------------------------------
        $html = '';
        $i = $sum = 0;
        $arr_company = array();
        $this->selectModel('Company');
        $arr_company = $this->get_sum_sub_total($product_id,$minimum, $arr_where);
        foreach ($arr_company as $value) {
            $html .= '
                <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa'). ';">
                     <td class="first content" align="left">' . $value['company_name'] . '</td>
                     <td class="content" align="left">' . $value['our_rep'] . '</td>
                     <td class="content">' . $value['number_of_quotations'] . '</td>
                     <td colspan="3" class="content"  align="right" class="end">' . $this->opm->format_currency($value['sum_sub_total']) . '</td>
                </tr>
            ';
            $sum += ($value['sum_sub_total'] ? $value['sum_sub_total'] : 0);
            $i++;
        }
        $html .= '
                    <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa') . ';">
                         <td colspan="2" class="bold_text right_none">' . $i . ' record(s) listed</td>
                         <td class="bold_text right_none right_text" >Total</td>
                         <td class="bold_text right_text">' . $this->opm->format_currency($sum) . '</td>
                    </tr>
                </table>
                ';
        //========================================
        //set header
        if($data['heading']!='')
            $arr_data['report_heading'] = $data['heading'];
        $arr_data['date_from_to'] = '';
        if(isset($data['date_from'])&&$data['date_from']!='')
            $arr_data['date_from_to'] .= '<span class="color_red bold_text">From</span> '.$data['date_from'].' ';
        if(isset($data['date_to'])&&$data['date_to']!='')
            $arr_data['date_from_to'] .= ' <span class="color_red bold_text">To</span> '.$data['date_to'];
        if(isset($data['date_equals'])&&$data['date_equals']!='')
            $arr_data['date_from_to'] .= $data['date_equals'];
        $arr_data['title'] = array('Company'=>'text-align: left','Our Rep'=>'text-align: left','No. of QT'=>'text-align: left','Ex. Tax total'=>'text-align: right');
        $arr_data['content'] = $html;
        $arr_data['report_name'] = 'Quotation Report By Customer (Summary)';
        $arr_data['report_file_name'] = 'QT_'.md5(time());
        return $arr_data;
    }
    public function summary_area_customer_report($data, $arr_where, $minimum, $product_id) {
        //--------------------------------------
        $html = '';
        $i = $sum = 0;
        $arr_company = array();
        $this->selectModel('Company');
        $arr_company = $this->get_sum_sub_total($product_id,$minimum, $arr_where, true);
        $arr_province = $this->province('CA');
        $arr_area = array();
        foreach ($arr_company as $company_id=>$value) {
            $value['company_id'] = new MongoId($company_id);
            $province_state = '';
            $addresses_default_key = 0;
            if(isset($value['addresses_default_key']))
                $addresses_default_key = $value['addresses_default_key'];
            $province = '(empty)';
            if( isset($value['addresses'][$addresses_default_key]['province_state_id']) )
                $province = (isset($arr_province[$value['addresses'][$addresses_default_key]['province_state_id']]) ? $arr_province[$value['addresses'][$addresses_default_key]['province_state_id']] : '(empty)');
            // unset($value['addresses']);
            if(!isset($arr_area[$province]['sum_sub_total']))
                $arr_area[$province]['sum_sub_total'] = 0;
            $arr_area[$province]['sum_sub_total'] += $value['sum_sub_total'];
            if(!isset($arr_area[$province]['number_of_quotations']))
                $arr_area[$province]['number_of_quotations'] = 0;
            $arr_area[$province]['number_of_quotations'] += $value['number_of_quotations'];
            if(!isset($arr_area[$province]['number_of_companies']))
                $arr_area[$province]['number_of_companies'] = 0;
            $arr_area[$province]['number_of_companies']++;
        }
        ksort($arr_area);
        foreach ($arr_area as $province_name=>$value) {
            $html .= '
                <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa'). ';">
                     <td class="first content" align="left">' . $province_name . '</td>
                     <td class="content" align="left">' . $value['number_of_companies'] . '</td>
                     <td class="content">' . $value['number_of_quotations'] . '</td>
                     <td colspan="3" class="content"  align="right" class="end">' . $this->opm->format_currency($value['sum_sub_total']) . '</td>
                </tr>
            ';
            $sum += ($value['sum_sub_total'] ? $value['sum_sub_total'] : 0);
            $i++;
        }
        $html .= '
                    <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa') . ';">
                         <td colspan="2" class="bold_text right_none">' . $i . ' record(s) listed</td>
                         <td class="bold_text right_none right_text" >Total</td>
                         <td class="bold_text right_text">' . $this->opm->format_currency($sum) . '</td>
                    </tr>
                </table>
                ';
        //========================================
        //set header
        if($data['heading']!='')
            $arr_data['report_heading'] = $data['heading'];
        $arr_data['date_from_to'] = '';
        if(isset($data['date_from'])&&$data['date_from']!='')
            $arr_data['date_from_to'] .= '<span class="color_red bold_text">From</span> '.$data['date_from'].' ';
        if(isset($data['date_to'])&&$data['date_to']!='')
            $arr_data['date_from_to'] .= ' <span class="color_red bold_text">To</span> '.$data['date_to'];
        if(isset($data['date_equals'])&&$data['date_equals']!='')
            $arr_data['date_from_to'] .= $data['date_equals'];
        $arr_data['title'] = array('Province'=>'text-align: left','No. of QT'=>'text-align: left','No. of Companies'=>'text-align: left','Ex. Tax total'=>'text-align: right');
        $arr_data['content'] = $html;
        $arr_data['report_name'] = 'Quotation Report By Area Customer (Summary)';
        $arr_data['report_file_name'] = 'QT_'.md5(time());
        return $arr_data;
    }
    public function option_detailed_customer_find($type = '') {
        if(!$this->check_permission($this->name.'_@_options_@_report_by_customer_detailed'))
            $this->error_auth();
        $arr_data['quotations_status'] = $this->Setting->select_option_vl(array('setting_value' => 'quotations_status'));
        $arr_data['quotations_type'] = $this->Setting->select_option_vl(array('setting_value' => 'quotations_type'));

        $this->set('arr_data', $arr_data);
        $report_type = 'detailed';
        if($type=='area')
            $report_type = 'area_detailed';
        $this->set('report_type',$report_type);
    }
    public function detailed_customer_report($data, $arr_where, $minimum, $product_id) {
        $i = $sum = 0;
        $html = '';
        //Loc ra nhung quotation group theo company id
        $arr_result = array();
        $this->selectModel('Company');
        $arr_company = $this->get_sum_sub_total($product_id,$minimum, $arr_where);
        $arr_quotations = $this->opm->collection->group(array('company_id' => true), array('quotations' => array()), 'function (obj, prev) { prev.quotations.push({_id : obj._id, code: obj.code, quotation_type : obj.quotation_type, quotation_date: obj.quotation_date, heading: obj.heading, quotation_status : obj.quotation_status, our_rep : obj.our_rep, sum_sub_total : obj.sum_sub_total, shipping_cost : obj.shipping_cost,}); }',array('condition' => $arr_where));
        if( $arr_quotations['ok'] && !empty($arr_quotations['retval']) ) {
            $arr_quotations = $arr_quotations['retval'];
        }
        foreach($arr_company as $company_id => $value) {
            foreach($arr_quotations as $k => $v) {
                if( (string)$v['company_id'] != $company_id ) continue;
                if( !empty($v['quotations']) ) {
                    usort($v['quotations'], function($a, $b){
                        return $a['code'] < $b['code'];
                    });
                    foreach($v['quotations'] as $quotation) {
                        $quotation['sum_sub_total'] = $quotation['sum_sub_total'] < $value['minimum'] ? $value['minimum']  : $quotation['sum_sub_total'];
                        $arr_company[$company_id]['quotations'][$quotation['code']] = array(
                                                                                'quotation_code'    =>$quotation['code'],
                                                                                'quotation_type'    =>$quotation['quotation_type'],
                                                                                'quotation_date'    =>$this->opm->format_date($quotation['quotation_date']->sec),
                                                                                'quotation_heading'    =>(isset($quotation['heading']) ? $quotation['heading'] : ''),
                                                                                'quotation_status'    =>$quotation['quotation_status'],
                                                                                'quotation_our_rep'    =>$quotation['our_rep'],
                                                                                'sum_sub_total'     => $quotation['sum_sub_total']
                                                                               );
                    }
                }
                unset($arr_quotations[$k]);
                break;
            }
        }
        foreach ($arr_company as $key => $company) {
            $html .= '
            <table class="table_content">
               <tbody>
                  <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                     <td width="35%">
                        Company
                     </td>
                     <td width="25%">
                        Our Rep
                     </td>
                     <td class="right_text" width="15%">
                        No. of QT
                     </td>
                     <td class="right_text" colspan="3">
                        Group total (ex. tax)
                     </td>
                  </tr>
                  <tr class="bg_2">
                     <td>' . $company['company_name'] . '</td>
                     <td>' . $company['our_rep'] . '</td>
                     <td class="right_text">' . $company['number_of_quotations'] . '</td>
                     <td colspan="3" class="right_text">' . $this->opm->format_currency($company['sum_sub_total']) . '</td>
                  </tr>
               </tbody>
            </table>
            <br />
            <br />
            <div class="line" style="margin-bottom: 10px;"></div>';
            $html .= '<table class="table_content" >
                        <tbody>
                          <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                             <td width="7%">
                                QT#
                             </td>
                             <td width="15%">
                                Type
                             </td>
                             <td width="15%">
                                Date
                             </td>
                             <td width="15%">
                                Status
                             </td>
                             <td width="15%">
                                Heading
                             </td>
                             <td width="15%">
                                Our Rep
                             </td>
                             <td class="right_text" colspan="3" width="18%">
                                Ex. Tax total
                             </td>
                          </tr>';
            $i = 0;
            $sum = 0;
            foreach ($company['quotations'] as $key => $quotation) {
                if( $quotation['quotation_status'] == 'Cancelled' ) {
                    $quotation['sum_sub_total'] = 0;
                }
                $sum += $quotation['sum_sub_total'];
                $html .= '
                      <tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                         <td>' . $quotation['quotation_code'] . '</td>
                         <td>' . $quotation['quotation_type'] . '</td>
                         <td>' . $quotation['quotation_date'] . '</td>
                         <td>' . $quotation['quotation_status'] . '</td>
                         <td class="left_text">' . $quotation['quotation_heading'] . '</td>
                         <td class="left_text">' . $quotation['quotation_our_rep'] . '</td>
                         <td colspan="3" class="right_text">' . $this->opm->format_currency((float)$quotation['sum_sub_total']) . '</td>
                      </tr>';
                $i++;
            }
            $html .= '
                            <tr class="bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                             <td colspan="5" class="left_text bold_text right_none">' . $i . ' record(s) listed</td>
                             <td class="bold_text right_text right_none">Total</td>
                             <td colspan="3" class="bold_text right_text">' . $this->opm->format_currency($sum) . '</td>
                          </tr>
                        </tbody>
                    </table>
                    <br />
                    <br />';
        }
        //========================================
        //set header
        if($data['heading']!='')
            $arr_data['report_heading'] = $data['heading'];
        $arr_data['date_from_to'] = '';
        if(isset($data['date_from'])&&$data['date_from']!='')
            $arr_data['date_from_to'] .= '<span class="color_red bold_text">From</span> '.$data['date_from'].' ';
        if(isset($data['date_to'])&&$data['date_to']!='')
            $arr_data['date_from_to'] .= ' <span class="color_red bold_text">To</span> '.$data['date_to'];
        if(isset($data['date_equals'])&&$data['date_equals']!='')
            $arr_data['date_from_to'] .= $data['date_equals'];
        $arr_data['content'][]['html'] = $html;
        $arr_data['is_custom'] = true;
        $arr_data['image_logo'] = true;
        $arr_data['report_name'] = 'Quotation Report By Customer (Detailed)';
        $arr_data['report_file_name'] = 'QT_'.md5(time());
        return $arr_data;
    }
    public function detailed_area_customer_report($data, $arr_where, $minimum, $product_id) {
        $i = $sum = 0;
        $html = '';
        //Loc ra nhung quotation group theo company id
        $group_company = array();
        $this->selectModel('Company');
        $arr_company = $this->get_sum_sub_total($product_id,$minimum, $arr_where);
        foreach ($arr_company as $company_id=>$value) {
            $value['company_id'] = new MongoId($company_id);
            $province_state = '';
            $addresses_default_key = 0;
            if(isset($value['addresses_default_key']))
                $addresses_default_key = $value['addresses_default_key'];
            $province = '(empty)';
            if( isset($value['addresses'][$addresses_default_key]['province_state_id']) )
                $province = (isset($arr_province[$value['addresses'][$addresses_default_key]['province_state_id']]) ? $arr_province[$value['addresses'][$addresses_default_key]['province_state_id']] : '(empty)');
            // unset($value['addresses']);
            if(!isset($arr_area[$province]['sum_sub_total']))
                $arr_area[$province]['sum_sub_total'] = 0;
            $arr_area[$province]['sum_sub_total'] += $value['sum_sub_total'];
            if(!isset($arr_area[$province]['number_of_quotations']))
                $arr_area[$province]['number_of_quotations'] = 0;
            $arr_area[$province]['number_of_quotations'] += $value['number_of_quotations'];
            $arr_area[$province]['companies'][] = $value;
        }
        $arr_quotations = $this->opm->collection->group(array('company_id' => true), array('quotations' => array()), 'function (obj, prev) { prev.quotations.push({_id : obj._id, code: obj.code, company_name : obj.company_name, quotation_type : obj.quotation_type, quotation_date: obj.quotation_date, heading: obj.heading, quotation_status : obj.quotation_status, our_rep : obj.our_rep, sum_sub_total : obj.sum_sub_total, shipping_cost : obj.shipping_cost}); }',array('condition' => $arr_where));
        if( $arr_quotations['ok'] && !empty($arr_quotations['retval']) ) {
            $arr_quotations = $arr_quotations['retval'];
        }
        foreach($arr_area as $province=>$value){
            foreach($value['companies'] as $company){
                $company_id = $company['company_id'];
                foreach($arr_quotations as $k => $v) {
                    if( (string)$v['company_id'] != $company_id ) continue;
                    if( !empty($v['quotations']) ) {
                        foreach($v['quotations'] as $quotation) {
                            $quotation['sum_sub_total'] = $quotation['sum_sub_total'] < $company['minimum'] ? $company['minimum'] : $quotation['sum_sub_total'];
                            $arr_area[$province]['quotations'][$quotation['code']] = array(
                                                                            'quotation_code'    =>$quotation['code'],
                                                                            'company_name'      =>(isset($quotation['company_name']) ? $quotation['company_name'] : ''),
                                                                            'quotation_type'    =>$quotation['quotation_type'],
                                                                            'quotation_date'    =>$this->opm->format_date($quotation['quotation_date']->sec),
                                                                            'quotation_heading'    =>(isset($quotation['heading']) ? $quotation['heading'] : ''),
                                                                            'quotation_status'    =>$quotation['quotation_status'],
                                                                            'quotation_our_rep'    =>$quotation['our_rep'],
                                                                            'sum_sub_total'     => $quotation['sum_sub_total']
                                                                           );
                        }
                    }
                    unset($arr_quotations[$k]);
                    break;
                }
            }
        }
        foreach ($arr_area as $province => $value) {
            $html .= '
            <table class="table_content">
               <tbody>
                  <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                     <td width="35%">
                        Province
                     </td>
                     <td class="right_text" width="15%">
                        No. of QT
                     </td>
                     <td class="right_text" colspan="3">
                        Group total (ex. tax)
                     </td>
                  </tr>
                  <tr class="bg_2">
                     <td>' . $province . '</td>
                     <td class="right_text">' . $value['number_of_quotations'] . '</td>
                     <td colspan="3" class="right_text">' . $this->opm->format_currency($value['sum_sub_total']) . '</td>
                  </tr>
               </tbody>
            </table>';
            $html .= '<table class="table_content" >
                        <tbody>
                          <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                             <td width="7%">
                                QT#
                             </td>
                             <td width="10%">
                                Type
                             </td>
                             <td width="15%">
                                Company
                             </td>
                             <td width="15%">
                                Date
                             </td>
                             <td width="15%">
                                Status
                             </td>
                             <td width="15%">
                                Heading
                             </td>
                             <td width="15%">
                                Our Rep
                             </td>
                             <td class="right_text" colspan="3" width="18%">
                                Ex. Tax total
                             </td>
                          </tr>';
            $i = 0;
            $sum = 0;
            usort($value['quotations'], function($a, $b){
                return $a['quotation_code'] < $b['quotation_code'];
            });
            foreach ($value['quotations'] as $key => $quotation) {
                if( $quotation['quotation_status'] == 'Cancelled' )
                    $quotation['sum_sub_total'] = 0;
                $sum += $quotation['sum_sub_total'];
                $html .= '
                      <tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                         <td>' . $quotation['quotation_code'] . '</td>
                         <td>' . $quotation['quotation_type'] . '</td>
                         <td>' . $quotation['company_name'] . '</td>
                         <td>' . $quotation['quotation_date'] . '</td>
                         <td>' . $quotation['quotation_status'] . '</td>
                         <td class="left_text">' . $quotation['quotation_heading'] . '</td>
                         <td class="left_text">' . $quotation['quotation_our_rep'] . '</td>
                         <td colspan="3" class="right_text">' . $this->opm->format_currency((float)$quotation['sum_sub_total']) . '</td>
                      </tr>';
                $i++;
            }
            $html .= '
                            <tr class="bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                             <td colspan="5" class="left_text bold_text right_none">' . $i . ' record(s) listed</td>
                             <td class="bold_text right_text right_none">Total</td>
                             <td colspan="3" class="bold_text right_text">' . $this->opm->format_currency($sum) . '</td>
                          </tr>
                        </tbody>
                    </table>
                    <br />
                    <br />
                    <div class="line" style="margin-bottom: 10px;"></div>';
        }
        //========================================
        //set header
        if($data['heading']!='')
            $arr_data['report_heading'] = $data['heading'];
        $arr_data['date_from_to'] = '';
        if(isset($data['date_from'])&&$data['date_from']!='')
            $arr_data['date_from_to'] .= '<span class="color_red bold_text">From</span> '.$data['date_from'].' ';
        if(isset($data['date_to'])&&$data['date_to']!='')
            $arr_data['date_from_to'] .= ' <span class="color_red bold_text">To</span> '.$data['date_to'];
        if(isset($data['date_equals'])&&$data['date_equals']!='')
            $arr_data['date_from_to'] .= $data['date_equals'];
        $arr_data['content'][]['html'] = $html;
        $arr_data['is_custom'] = true;
        $arr_data['image_logo'] = true;
        $arr_data['report_name'] = 'Quotation Report By Customer (Detailed)';
        $arr_data['report_file_name'] = 'QT_'.md5(time());
        return $arr_data;
    }
    public function option_summary_product_find($type=''){
        if(!$this->check_permission($this->name.'_@_options_@_report_by_products_summary'))
            $this->error_auth();
        $arr_data['quotations_status'] = $this->Setting->select_option_vl(array('setting_value' => 'quotations_status'));
        $arr_data['quotations_type'] = $this->Setting->select_option_vl(array('setting_value' => 'quotations_type'));
        $arr_data['product_category'] = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
        $this->set('arr_data', $arr_data);
        if($type=='category')
            $this->render('../Quotations/option_summary_category_find');
    }


    public function get_cate_product($value) {
        $cate = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
        if(isset($cate[$value]))
            echo $cate[$value];
        else
            echo '';
        die();
    }


    public function product_report($type = ''){
        $arr_data = array();
        if(isset($_GET['print_pdf'])){
            $arr_data = Cache::read('quotations_product_report_'.$type);
            Cache::delete('quotations_product_report_'.$type);
        } else {
            if(isset($_POST)){
                $data['product_category'] = $this->Setting->select_option_vl(array('setting_value'=>'product_category'));
                $arr_post = $_POST;
                $arr_post = $this->Common->strip_search($arr_post);
                $arr_where = array();
                $arr_where['products']['$ne'] = '';
                if(isset($arr_post['status']) && $arr_post['status'] != '')
                    $arr_where['quotation_status'] = $arr_post['status'];
                //Check loại trừ cancel thì bỏ các status bên dưới
                if(isset($arr_post['is_not_cancel'])&&$arr_post['is_not_cancel']==1){
                    $arr_where['quotation_status'] = array('$nin'=>array('Rejected','Cancelled','Amended'));
                    //Tuy nhiên nếu ở ngoài combobox nếu có chọn, thì ưu tiên nó, set status lại
                    if(isset($arr_post['status'])&&$arr_post['status']!='')
                        $arr_where['quotation_status'] = $arr_post['status'];
                }
                if(isset($arr_post['type']) && $arr_post['type']!= '')
                    $arr_where['quotation_type'] = $arr_post['type'];
                if(isset($arr_post['company']) && $arr_post['company']!='')
                    $arr_where['company_name'] = new MongoRegex('/'.trim($arr_post['company']).'/i');
                if(isset($arr_post['contact']) &&$arr_post['contact']!='')
                    $arr_where['contact_name'] = new MongoRegex('/'.trim($arr_post['contact']).'/i');
                if(isset($arr_post['job_no']) && $arr_post['job_no']!='')
                    $arr_where['job_number'] = trim($arr_post['job_no']);
                if(isset($arr_post['employee']) && trim($arr_post['employee'])!=''){
                    $arr_where['$or'][]['our_rep'] = new MongoRegex('/'.trim($arr_post['employee']).'/i');
                    $arr_where['$or'][]['our_csr'] = new MongoRegex('/'.trim($arr_post['employee']).'/i');
                }
                //Tìm chính xác ngày
                //Vì để = chỉ tìm đc 01/01/1969 00:00:00 nên phải cộng cho 23:59:59 rồi tìm trong khoảng đó
                if(isset($arr_post['date_equals'])&&$arr_post['date_equals']!=''){
                    $arr_where['quotation_date']['$gte'] = new MongoDate($this->Common->strtotime($arr_post['date_equals'] . '00:00:00'));
                    $arr_where['quotation_date']['$lt'] = new MongoDate($this->Common->strtotime($arr_post['date_equals'] . '23:59:59'));
                } else{  //Ngày nằm trong khoảng
                    //neu chi nhap date from
                    if(isset($arr_post['date_from']) && $arr_post['date_from'] != ''){
                        $arr_where['quotation_date']['$gte'] =  new MongoDate($this->Common->strtotime($arr_post['date_from'] . '00:00:00'));
                    }
                    //neu chi nhap date to
                    if(isset($arr_post['date_to']) && $arr_post['date_to'] != ''){
                        $arr_where['quotation_date']['$lte'] = new MongoDate($this->Common->strtotime($arr_post['date_to'] . '23:59:59'));
                    }
                }
                //Kiểm tra nếu có thông tin liên quan đến product tồn tại
                $pro_where = array();
                if(isset($arr_post['product'])&&$arr_post['product']!='')
                    $pro_where['code'] = trim($arr_post['product']);
                if(isset($arr_post['name'])&&$arr_post['name']!='')
                    $pro_where['name'] = new MongoRegex('/' . trim($arr_post['name']) . '/i');
                if(isset($arr_post['category_id'])&&$arr_post['category_id']!='')
                    $pro_where['category'] = new MongoRegex('/'.$arr_post['category_id'].'/i');
                $pro_list = array();
                $arr_products_where = array();
                $arr_products_where['products.deleted'] = $arr_where['deleted'] = false;
                if(isset($arr_post['sell_price_from'])&&$arr_post['sell_price_from']!=''){
                    $arr_where['products']['$elemMatch']['sell_price']['$gte'] = (float)$arr_post['sell_price_from'];
                    $arr_products_where['products.unit_price']['$gte'] = (float)$arr_post['sell_price_from'];
                }
                if(isset($arr_post['sell_price_to'])&&$arr_post['sell_price_to']!=''){
                    $arr_where['products']['$elemMatch']['sell_price']['$lte'] = (float)$arr_post['sell_price_to'];
                    $arr_products_where['products.unit_price']['$lte'] = (float)$arr_post['sell_price_to'];
                }
                if(!empty($pro_where)){
                    //Lấy ra _id của Product phù hợp với điều kiện trên
                    $this->selectModel('Product');
                    $pro_list = $this->Product->select_all(array(
                                            'arr_where'=>$pro_where,
                                            'arr_field'=>array('_id')
                        ));
                    foreach($pro_list as $p_id){
                       $arr_where['products']['$elemMatch']['products_id']['$in'][] = new MongoId($p_id['_id']);
                       $arr_products_where['products.products_id']['$in'][] = new MongoId($p_id['_id']);
                    }
                }
                $arr_where['products']['$elemMatch']['deleted'] = false;
                $quotation = $this->opm->collection->aggregate(
                        array(
                            '$match'=>$arr_where,
                        ),
                        array(
                            '$unwind'=>'$products',
                        ),
                         array(
                            '$match'=>$arr_products_where
                        ),
                        array(
                            '$project'=>array('quotation_status'=>'$quotation_status','code'=>'$code','company_name'=>'$company_name','company_id'=>'$company_id','quotation_date'=>'$quotation_date','products'=>'$products')
                        ),
                        array(
                            '$group'=>array(
                                          '_id'=>array('_id'=>'$_id','quotation_status'=>'$quotation_status','code'=>'$code','company_name'=>'$company_name','company_id'=>'$company_id','quotation_date'=>'$quotation_date'),
                                          'products'=>array('$push'=>'$products')
                                        )
                        )
                    );
                if(empty($quotation['result'])) {
                    echo 'empty';
                    die;
                } else {
                    $quotation = $quotation['result'];
                    if ($arr_post['report_type'] == 'summary'){
                        $arr_data = $this->summary_product_report($quotation,$arr_post);
                        Cache::write('quotations_product_report_'.$type, $arr_data);
                    }
                    else if ($arr_post['report_type'] == 'detailed'){
                        $arr_data = $this->detailed_product_report($quotation,$arr_post);
                        Cache::write('quotations_product_report_'.$type, $arr_data);
                    }
                    else if($arr_post['report_type'] == 'category_summary'){
                        $arr_data = $this->summary_category_product_report($quotation,$arr_post);
                        Cache::write('quotations_product_report_'.$type, $arr_data);
                    }
                    else if($arr_post['report_type'] == 'category_detailed'){
                        $arr_data = $this->detailed_category_product_report($quotation,$arr_post);
                        Cache::write('quotations_product_report_'.$type, $arr_data);
                    }

                }

            }
        }
        if($this->request->is('ajax'))
            die;
        else
            $this->render_pdf($arr_data);
    }


    public function summary_product_report($arr_quotation,$data){
        $html = '';
        $i = $sum = 0;
        $this->selectModel('Product');
        $category = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
        $arr_data = array();
        foreach($arr_quotation as $quotation){
            foreach($quotation['products'] as $product){
                $product['code'] = (isset($product['code']) ? $product['code'] : 'empty');
                $arr_data[$product['code']]['products_name'] = $product['products_name'];
                $arr_data[$product['code']]['code'] = $product['code'];
                $arr_data[$product['code']]['products_id'] = $product['products_id'];
                if(!isset($arr_data[$product['code']]['quantity']))
                    $arr_data[$product['code']]['quantity'] = 0;
                $arr_data[$product['code']]['quantity'] += $product['quantity'];
                if(!isset($arr_data[$product['code']]['sub_total']))
                    $arr_data[$product['code']]['sub_total'] = 0;
                $arr_data[$product['code']]['sub_total'] += (isset($product['sub_total']) ? (float)$product['sub_total'] : 0);
            }
        }
        foreach ($arr_data as $value) {
            if(is_object($value['products_id']))
                $product = $this->Product->select_one(array('_id'=>new MongoId($value['products_id'])),array('category'));
            if (!isset($product['category']))
                $product['category'] = '';
            $html .= '
                <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa'). ';">
                     <td>' . $value['code'] . '</td>
                     <td>' . $value['products_name'] . '</td>
                     <td>' . (isset($category[$product['category']]) ? $category[$product['category']] : '') . '</td>
                     <td class="right_text">' . $value['quantity'] . '</td>
                     <td colspan="3" class="right_text">' . $this->opm->format_currency($value['sub_total']) . '</td>
                </tr>
            ';
            $sum += ($value['sub_total'] ? $value['sub_total'] : 0);
            $i++;
        }
        $html .= '
                    <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa') . ';">
                         <td colspan="3" class="bold_text right_none">' . $i . ' record(s) listed</td>
                         <td class="bold_text right_none right_text" >Total</td>
                         <td class="bold_text right_text">' . $this->opm->format_currency($sum) . '</td>
                    </tr>
                </table>
                ';
        //========================================
        //set header
        if($data['heading']!='')
            $arr_pdf['report_heading'] = $data['heading'];
        $arr_pdf['date_from_to'] = '';
        if(isset($data['date_from'])&&$data['date_from']!='')
            $arr_pdf['date_from_to'] .= '<span class="color_red bold_text">From</span> '.$data['date_from'].' ';
        if(isset($data['date_to'])&&$data['date_to']!='')
            $arr_pdf['date_from_to'] .= ' <span class="color_red bold_text">To</span> '.$data['date_to'];
        if(isset($data['date_equals'])&&$data['date_equals']!='')
            $arr_pdf['date_from_to'] .= $data['date_equals'];
        $arr_pdf['title'] = array('P. Code'=>'text-align: left','Product Name'=>'text-align: left','Category'=>'text-align: left','Qty'=>'text-align: right;','Ex. Tax total'=>'text-align: right');
        $arr_pdf['content'] = $html;
        $arr_pdf['report_name'] = 'Quotation Report By Product (Summary)';
        $arr_pdf['report_file_name'] = 'QT_'.md5(time());
        return $arr_pdf;
    }

    public function summary_category_product_report($arr_quotation,$data){
        $html = '';
        $i = $sum = 0;
        $this->selectModel('Product');
        $category = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
        $arr_data = array();
        foreach($arr_quotation as $quotation){
            foreach($quotation['products'] as $product){
                $product_category = '(empty)';
                if(is_object($product['products_id'])){
                    $_product = $this->Product->select_one(array('_id'=> new MongoId($product['products_id'])),array('category'));
                    if (isset($_product['category']))
                        $product_category = (isset($category[$_product['category']]) ? $category[$_product['category']] : '(empty)');
                }
                $arr_data[$product_category]['category_name'] = $product_category;
                if(!isset($arr_data[$product_category]['quantity']))
                    $arr_data[$product_category]['quantity'] = 0;
                $arr_data[$product_category]['quantity'] += $product['quantity'];
                if(!isset($arr_data[$product_category]['sub_total']))
                    $arr_data[$product_category]['sub_total'] = 0;
                $arr_data[$product_category]['sub_total'] += (isset($product['sub_total']) ? (float)$product['sub_total'] : 0);
            }
        }
        foreach ($arr_data as $value) {
            $html .= '
                <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa'). ';">
                     <td>' . $value['category_name'] . '</td>
                     <td class="right_text">' . $value['quantity'] . '</td>
                     <td colspan="2" class="right_text">' . $this->opm->format_currency($value['sub_total']) . '</td>
                </tr>
            ';
            $sum += ($value['sub_total'] ? $value['sub_total'] : 0);
            $i++;
        }
        $html .= '
                    <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa') . ';">
                         <td class="bold_text right_none">' . $i . ' record(s) listed</td>
                         <td class="bold_text right_none right_text" >Total</td>
                         <td class="bold_text right_text">' . $this->opm->format_currency($sum) . '</td>
                    </tr>
                </table>
                ';
        //========================================
        //set header
        if($data['heading']!='')
            $arr_pdf['report_heading'] = $data['heading'];
        $arr_pdf['date_from_to'] = '';
        if(isset($data['date_from'])&&$data['date_from']!='')
            $arr_pdf['date_from_to'] .= '<span class="color_red bold_text">From</span> '.$data['date_from'].' ';
        if(isset($data['date_to'])&&$data['date_to']!='')
            $arr_pdf['date_from_to'] .= ' <span class="color_red bold_text">To</span> '.$data['date_to'];
        if(isset($data['date_equals'])&&$data['date_equals']!='')
            $arr_pdf['date_from_to'] .= $data['date_equals'];
        $arr_pdf['title'] = array('Category Name'=>'text-align: left','Qty'=>'text-align: right;','Ex. Tax total'=>'text-align: right');
        $arr_pdf['content'] = $html;
        $arr_pdf['report_name'] = 'Quotation Report By Category Product (Summary)';
        $arr_pdf['report_file_name'] = 'QT_'.md5(time());
        return $arr_pdf;
    }

	public function option_detailed_product_find($type='') {
        if(!$this->check_permission($this->name.'_@_options_@_report_by_product_detailed'))
            $this->error_auth();
        $arr_data['quotations_status'] = $this->Setting->select_option_vl(array('setting_value' => 'quotations_status'));
        $arr_data['quotations_type'] = $this->Setting->select_option_vl(array('setting_value' => 'quotations_type'));
        $arr_data['product_category'] = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
        $this->set('arr_data', $arr_data);
        if($type=='category')
            $this->render('../Quotations/option_detailed_category_find');
    }


	public function detailed_product_report($arr_quotation,$data){
        $i = $sum = 0;
        $html = '';
        $this->selectModel('Product');
        $category = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
        $total_num_of_quotations = $total_sum_sub_total = 0;
        $arr_data = $arr_pdf = array();
        foreach($arr_quotation as $quotation){
            foreach($quotation['products'] as $product){
                $product['code'] = (isset($product['code']) ? $product['code'] : '(empty)');
                $arr_data[$product['code']]['products_name'] = $product['products_name'];
                $arr_data[$product['code']]['code'] = $product['code'];
                $arr_data[$product['code']]['products_id'] = $product['products_id'];
                if(!isset($arr_data[$product['code']]['quantity']))
                    $arr_data[$product['code']]['quantity'] = 0;
                $arr_data[$product['code']]['quantity'] += $product['quantity'];
                if(!isset($arr_data[$product['code']]['sub_total']))
                    $arr_data[$product['code']]['sub_total'] = 0;
                $arr_data[$product['code']]['sub_total'] += (isset($product['sub_total']) ? (float)$product['sub_total'] : 0);
                if(!isset($arr_data[$product['code']]['quantity']))
                    $arr_data[$product['code']]['quantity'] = 0;
                $arr_data[$product['code']]['quantity'] += (isset($product['quantity']) ? (float)$product['quantity'] : 0);
                $arr_data[$product['code']]['quotations'][] = array_merge($quotation['_id'], array('unit_price'=>$product['unit_price'],'quantity'=>$product['quantity'],'sub_total'=>$product['sub_total']));
                if(!isset( $arr_data[$product['code']]['no_of_qt']))
                    $arr_data[$product['code']]['no_of_qt'] = array();
                $arr_data[$product['code']]['no_of_qt'][$quotation['_id']['code']] = 1;
            }
        }

        foreach ($arr_data as $value) {
            $total_num_of_quotations += count($value['quotations']);
            if(is_object($value['products_id']))
                $product = $this->Product->select_one(array('_id'=>new MongoId($value['products_id'])),array('category'));
            if (!isset($product['category']))
                $product['category'] = '';
            $html .= '
            <table class="table_content">
               <tbody>
                  <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                     <td width="10%">
                        P. Code
                     </td>
                     <td>
                        Product Name
                     </td>
                     <td width="15%">
                        Category
                     </td>
                     <td class="right_text" width="15%">
                        No. of QT
                     </td>
                     <td class="right_text" colspan="3" width="20%">
                        Group total (ex. tax)
                     </td>
                  </tr>
                  <tr class="bg_2">
                     <td>' . $value['code'] . '</td>
                     <td>' . $value['products_name'] . '</td>
                     <td>' . (isset($category[$product['category']]) ? $category[$product['category']] : '') . '</td>
                     <td class="right_text">' . count($value['no_of_qt']) . '</td>
                     <td colspan="3" class="right_text">' . $this->opm->format_currency($value['sub_total']) . '</td>
                  </tr>
               </tbody>
            </table>';
            $html .= '<table class="table_content" >
                        <tbody>
                          <tr class="tr_right_none" style="background: #979797;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                             <td width="10%">
                                QT#
                             </td>
                             <td width="30%">
                                Company
                             </td>
                             <td width="15%" class="center_text">
                                Date
                             </td>
                             <td width="15%" class="right_text">
                                Unit Price
                             </td>
                             <td width="15%" class="right_text">
                                Quantity
                             </td>
                             <td class="right_text" colspan="3" width="18%">
                                Ex. Tax total
                             </td>
                          </tr>';
            $i = 0;
            $sum = $total_quantity = 0;
            foreach ($value['quotations'] as $quotation) {
                $sum += $quotation['sub_total'];
                $total_quantity += $quotation['quantity'];
                $html .= '
                      <tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                         <td>' . $quotation['code'] . '</td>
                         <td>' . $quotation['company_name'] . '</td>
                         <td class="center_text">' . $this->opm->format_date($quotation['quotation_date']->sec) . '</td>
                         <td class="right_text">' . $this->opm->format_currency((float)$quotation['unit_price'],2) . '</td>
                         <td class="right_text">' . $quotation['quantity'] . '</td>
                         <td colspan="3" class="right_text">' . $this->opm->format_currency((float)$quotation['sub_total']) . '</td>
                      </tr>';
                $i++;
            }
            $html .= '
                            <tr class="bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                             <td colspan="3" class="left_text bold_text right_none">' . $i . ' record(s) listed</td>
                             <td class="bold_text right_text right_none">Totals</td>
                             <td class="bold_text right_text right_none">' . $total_quantity . '</td>
                             <td colspan="4" class="bold_text right_text">' . $this->opm->format_currency($sum) . '</td>
                          </tr>
                        </tbody>
                    </table>
                    <br />
                    <br />';
            $total_sum_sub_total += $sum;
        }
        $html .= '
                <div class="line" style="margin-bottom: 5px;"></div>
                <table class="table_content">
                    <tr style="background-color: #333; color: white">
                        <td class="bold_text right_none" width="70%">'.$total_num_of_quotations.' record(s) listed</td>
                        <td class="right_text bold_text right_none" >Totals</td>
                        <td class="right_text bold_text" width="15%">'.$this->opm->format_currency($total_sum_sub_total).'</td>
                    </tr>
                </table>';

        //========================================
        //set header
        if($data['heading']!='')
            $arr_pdf['report_heading'] = $data['heading'];
        $arr_pdf['date_from_to'] = '';
        if(isset($data['date_from'])&&$data['date_from']!='')
            $arr_pdf['date_from_to'] .= '<span class="color_red bold_text">From</span> '.$data['date_from'].' ';
        if(isset($data['date_to'])&&$data['date_to']!='')
            $arr_pdf['date_from_to'] .= ' <span class="color_red bold_text">To</span> '.$data['date_to'];
        if(isset($data['date_equals'])&&$data['date_equals']!='')
            $arr_pdf['date_from_to'] .= $data['date_equals'];
        $arr_pdf['content'][]['html'] = $html;
        $arr_pdf['is_custom'] = true;
        $arr_pdf['image_logo'] = true;
        $arr_pdf['report_name'] = 'QT Report By Product (Detailed)';
        $arr_pdf['report_file_name'] = 'QT_'.md5(time());
        return $arr_pdf;
    }

    public function detailed_category_product_report($arr_quotation,$data){
        $i = $sum = 0;
        $html = '';
        $this->selectModel('Product');
        $category = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
        $total_num_of_quotations = $total_sum_sub_total = 0;
        $arr_data = $arr_pdf = array();
        foreach($arr_quotation as $quotation){
            foreach($quotation['products'] as $product){
                $product_category = '(empty)';
                if(is_object($product['products_id'])){
                    $_product = $this->Product->select_one(array('_id'=> new MongoId($product['products_id'])),array('category'));
                    if (isset($_product['category']))
                        $product_category = (isset($category[$_product['category']]) ? $category[$_product['category']] : '(empty)');
                }
                 $arr_data[$product_category]['category_name'] = $product_category;
                if(!isset($arr_data[$product_category]['quantity']))
                    $arr_data[$product_category]['quantity'] = 0;
                $arr_data[$product_category]['quantity'] += $product['quantity'];
                if(!isset($arr_data[$product_category]['sub_total']))
                    $arr_data[$product_category]['sub_total'] = 0;
                $arr_data[$product_category]['sub_total'] += (isset($product['sub_total']) ? (float)$product['sub_total'] : 0);
                $arr_data[$product_category]['quantity'] += (isset($product['quantity']) ? (float)$product['quantity'] : 0);
                $arr_data[$product_category]['quotations'][] = array_merge($quotation['_id'], array('unit_price'=>$product['unit_price'],'quantity'=>$product['quantity'],'sub_total'=>$product['sub_total']));
                if(!isset( $arr_data[$product_category]['no_of_qt']))
                    $arr_data[$product_category]['no_of_qt'] = array();
                $arr_data[$product_category]['no_of_qt'][$quotation['_id']['code']] = 1;
            }
        }

        foreach ($arr_data as $value) {
            $total_num_of_quotations += count($value['quotations']);
            $html .= '
            <table class="table_content">
               <tbody>
                  <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                     <td width="25%">
                        Category Name
                     </td>
                     <td class="right_text" width="15%">
                        No. of QT
                     </td>
                     <td class="right_text" colspan="3" width="20%">
                        Group total (ex. tax)
                     </td>
                  </tr>
                  <tr class="bg_2">
                     <td>' . $value['category_name'] . '</td>
                     <td class="right_text">' . count($value['no_of_qt']) . '</td>
                     <td colspan="3" class="right_text">' . $this->opm->format_currency($value['sub_total']) . '</td>
                  </tr>
               </tbody>
            </table>';
            $html .= '<table class="table_content" >
                        <tbody>
                          <tr class="tr_right_none" style="background: #979797;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                             <td width="10%">
                                QT#
                             </td>
                             <td width="30%">
                                Company
                             </td>
                             <td width="15%" class="center_text">
                                Date
                             </td>
                             <td width="15%" class="right_text">
                                Unit Price
                             </td>
                             <td width="15%" class="right_text">
                                Quantity
                             </td>
                             <td class="right_text" colspan="3" width="18%">
                                Ex. Tax total
                             </td>
                          </tr>';
            $i = 0;
            $sum = $total_quantity = 0;
            foreach ($value['quotations'] as $quotation) {
                $sum += $quotation['sub_total'];
                $total_quantity += $quotation['quantity'];
                $html .= '
                      <tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                         <td>' . $quotation['code'] . '</td>
                         <td>' . $quotation['company_name'] . '</td>
                         <td class="center_text">' . $this->opm->format_date($quotation['quotation_date']->sec) . '</td>
                         <td class="right_text">' . $this->opm->format_currency((float)$quotation['unit_price']) . '</td>
                         <td class="right_text">' . $quotation['quantity'] . '</td>
                         <td colspan="3" class="right_text">' . $this->opm->format_currency((float)$quotation['sub_total'], 2) . '</td>
                      </tr>';
                $i++;
            }
            $html .= '
                            <tr class="bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                             <td colspan="3" class="left_text bold_text right_none">' . $i . ' record(s) listed</td>
                             <td class="bold_text right_text right_none">Totals</td>
                             <td class="bold_text right_text right_none">' . $total_quantity . '</td>
                             <td colspan="4" class="bold_text right_text">' . $this->opm->format_currency($sum, 2) . '</td>
                          </tr>
                        </tbody>
                    </table>
                    <br />
                    <br />';
            $total_sum_sub_total += $sum;
        }
        $html .= '
                <div class="line" style="margin-bottom: 5px;"></div>
                <table class="table_content">
                    <tr style="background-color: #333; color: white">
                        <td class="bold_text right_none" width="70%">'.$total_num_of_quotations.' record(s) listed</td>
                        <td class="right_text bold_text right_none" >Totals</td>
                        <td class="right_text bold_text" width="15%">'.$this->opm->format_currency($total_sum_sub_total).'</td>
                    </tr>
                </table>';

        //========================================
        //set header
        if($data['heading']!='')
            $arr_pdf['report_heading'] = $data['heading'];
        $arr_pdf['date_from_to'] = '';
        if(isset($data['date_from'])&&$data['date_from']!='')
            $arr_pdf['date_from_to'] .= '<span class="color_red bold_text">From</span> '.$data['date_from'].' ';
        if(isset($data['date_to'])&&$data['date_to']!='')
            $arr_pdf['date_from_to'] .= ' <span class="color_red bold_text">To</span> '.$data['date_to'];
        if(isset($data['date_equals'])&&$data['date_equals']!='')
            $arr_pdf['date_from_to'] .= $data['date_equals'];
        $arr_pdf['content'][]['html'] = $html;
        $arr_pdf['is_custom'] = true;
        $arr_pdf['image_logo'] = true;
        $arr_pdf['report_name'] = 'QT Report By Category Product (Detailed)';
        $arr_pdf['report_file_name'] = 'QT_'.md5(time());
        return $arr_pdf;
    }
	 /*
      End option report
     */
	 public function check_condition_create_job()
     {
        $this->selectModel('Job');
        $id = $this->get_id();
        $quo = $this->opm->select_one(array('_id' => new MongoId($id),'deleted'=>false,'company_id'=>array('$ne'=>'')),array('job_id','quotation_status','company_id'));
        $company_id = $quo['company_id'];
        $str = '';
        if(!isset($quo['job_id']) || !is_object($quo['job_id'])){
            if(!isset($_POST['password'])  ){
                if(!$this->Session->check('create_job_ok') || !$this->Session->read('create_job_ok') ) {
                    $this->selectModel('Receipt');
                    $this->selectModel('Salesinvoice');
                    $current_time = strtotime(date('Y-m-d'));
                    $obj_salesinvoices = $this->Salesinvoice->select_all(array(
                                        'arr_where'=>array(
                                                            'company_id' => new MongoID($company_id),
                                                            'invoice_status' => 'Invoiced',
                                                            'payment_due_date' => array('$lt' => new MongoDate($current_time - 90*DAY))
                                                           ),
                                        'arr_field'=>array('_id'),
                                ));
                    if($obj_salesinvoices->count()){
                        $obj_salesinvoices = $this->Salesinvoice->select_all(array(
                                    'arr_where'=>array(
                                                        'company_id' => new MongoID($company_id),
                                                        'invoice_status' => 'Invoiced',
                                                       ),
                                    'arr_field'=>array('payment_due_date','invoice_date','sum_amount','total_receipt','invoice_status'),
                                    'arr_order'=>array('invoice_date'=>-1)
                            ));
                        $total_balance = 0;
                        foreach($obj_salesinvoices as $key => $value){
                            $payment_due_date = strtotime(date('Y-m-d',$value['payment_due_date']->sec));

                            $receipts = $this->Receipt->collection->aggregate(
                                array(
                                    '$match'=>array(
                                                    'company_id' => new MongoID($company_id),
                                                    'deleted'=> false
                                                    ),
                                ),
                                array(
                                    '$unwind'=>'$allocation',
                                ),
                                 array(
                                    '$match'=>array(
                                                    'allocation.deleted'=> false,
                                                    'allocation.salesinvoice_id' => $value['_id']
                                                    )
                                ),
                                array(
                                    '$project'=>array('allocation'=>'$allocation')
                                ),
                                array(
                                    '$group'=>array(
                                                  '_id'=>array('_id'=>'$_id'),
                                                  'allocation'=>array('$push'=>'$allocation')
                                                )
                                )
                            );
                            $total_receipt = 0;
                            if(isset($receipts['ok']) && $receipts['ok']){
                                foreach($receipts['result'] as $receipt){
                                    foreach($receipt['allocation'] as $allocation)
                                        $total_receipt += isset($allocation['amount']) ? (float)str_replace(',', '', $allocation['amount']) : 0;
                                }
                            }
                            if($value['invoice_status'] == 'Credit' && $value['sum_amount'] > 0)
                                $value['sum_amount'] = (float)$value['sum_amount'] * -1;
                            $value['total_receipt']= $total_receipt;
                            $total_balance += $value['sum_amount'] - (float)$value['total_receipt'];
                        }
                        if($total_balance){
                            $str = 'not_add';
                            $this->Session->write('create_job_ok',0);
                            //echo json_encode(array('str' => 'not_add'));
                            //$str = 'not_add';
                            //die;
                        }
                    }
                }
            } else {
                $this->selectModel('Stuffs');
                $change = $this->Stuffs->select_one(array('value' => 'Changing Code'));
                if(md5($_POST['password']) != $change['password']){
                    echo 'Your password is wrong.';
                    die;
                }
                echo 'ok';
                $this->Session->write('create_job_ok',1);
                die;
            }
        } else {
            echo json_encode(array('status'=>'error','message'=>'Job had been created.','confirm'=>'no'));
            die;
        }
        if(empty($quo) || !isset($quo['company_id']) || isset($quo['company_id'])&&$quo['company_id']=='')
        {
            echo json_encode(array('status'=>'error','message'=>'This record has to be linked to a record from Companies or Contacts first.','confirm'=>'no'));
            die;
        }
        $confirm = '';
        if($quo['quotation_status']!='Approved')
            $confirm = 'yes';
        echo json_encode(array('status'=>'ok','confirm'=>$confirm,'str'=>$str));
        die;
     }


	 public function create_job() {
        if(!$this->check_permission($this->name.'_@_options_@_create_job'))
            $this->error_auth();
        $this->selectModel('Job');
        $this->Session->delete('create_job_ok');
        $id = $this->get_id();
        $quo = $this->opm->select_one(array('_id' => new MongoId($id),'company_id'=>array('$ne'=>'')));

        if(isset($_POST['type'])&&!empty($quo)){
            $arr_save = array();
            $arr_save['no'] = $this->Job->get_auto_code('no');
            $arr_save['deleted'] = false;
            $arr_save['company_id'] = $quo['company_id'];
            $arr_save['company_name'] = $quo['company_name'];
            $arr_save['company_phone'] = $quo['phone'];
            $arr_save['contact_id'] = $quo['contact_id'];
            $arr_save['contact_name'] = $quo['contact_name'];

            $arr_save['contacts'][0]['contact_name'] = $_SESSION['arr_user']['contact_name'];
            $arr_save['contacts'][0]['contact_id'] = $_SESSION['arr_user']['contact_id'];
            $arr_save['contacts'][0]['default'] = true;
            $arr_save['contacts'][0]['deleted'] = false;

            $arr_save['contacts_default_key'] = 0;
            $arr_save['custom_po_no'] = $quo['customer_po_no'];
            $arr_save['direct_phone'] = $quo['phone'];
            $arr_save['email'] = $quo['email'];
            $arr_save['fax'] = (isset($quo['fax'])?$quo['fax']:'');
            $arr_save['mobile'] = '';
            $arr_save['name'] = $quo['name'];
            $arr_save['status'] = 'New';
            $arr_save['status_id'] = 'New';
            $arr_save['type'] = '';
            $arr_save['type_id'] = '';
            $arr_save['work_end'] = new MongoDate(strtotime(date('Y-m-d H:00:00')) + 3600);
            $arr_save['work_start'] = new MongoDate(strtotime(date('Y-m-d H:00:00')) + 3600);
            if ($this->Job->save($arr_save)) {
                $id = $this->Job->mongo_id_after_save;
                if(!isset($quo['job_id']) || !is_object($quo['job_id'])){
                    $quo['job_id'] = new MongoId($id);
                    $quo['job_number'] = $arr_save['no'];
                    $quo['job_name'] = $arr_save['name'];
                }
                if($_POST['type']=='change_status'){
                    $quo['quotation_status'] = 'Approved';
                }
                $this->opm->save($quo);
                echo json_encode(array('status'=>'ok','url'=> URL.'/jobs/entry/'.$id));
            }
        }
        echo '';
        die;
    }


	public function check_condition_create_salesinvoice()
    {
        $check = false;
        $id = $this->get_id();
        $quo = $this->opm->select_one(array('_id' => new MongoId($id),'deleted'=>false,'company_id'=>array('$ne'=>'')));

        if(empty($quo) || !isset($quo['company_id']) || isset($quo['company_id'])&&$quo['company_id']=='')
        {
            echo json_encode(array('status'=>'error','message'=>'This record has to be linked to a record from Companies or Contacts first.','confirm'=>'no'));
            die;
        }
        else if(isset($quo['products'])&&!empty($quo['products']))
        {
            foreach($quo['products'] as $products)
            {
                if(isset($products['deleted'])&&$products['deleted']==false || !isset($products['deleted']) )
                {
                    $check = true;
                    break;
                }
            }
        }
        else
            $check = false;
        if($check == false)
        {
            echo json_encode(array('status'=>'error','message'=>'No items have been entered on this transaction yet.','confirm'=>'no'));
            die;
        }
        $confirm = '';
        if($quo['quotation_status']!='Approved')
            $confirm = 'yes';
        echo json_encode(array('status'=>'ok','confirm'=>$confirm));
        die;
    }


    public function create_salesinvoice(){
        if(!$this->check_permission($this->name.'_@_options_@_create_sales_invoice'))
            $this->error_auth();
        $id = $this->get_id();
        $quo = $this->opm->select_one(array('_id' => new MongoId($id),'deleted'=>false,'company_id'=>array('$ne'=>''),'products'=>array('$elemMatch'=>array('deleted'=>false))));
        if(isset($_POST['type'])&&!empty($quo)){
            $this->selectModel('Salesinvoice');
            $si = $quo;
            $si['code'] = $this->Salesinvoice->get_auto_code('code');
            $si['invoice_date'] = new MongoDate();
            $si['invoice_status'] = 'Invoiced';
            $si['invoice_type'] = 'Invoice';
            $si['quotation_id'] = new MongoId($id);
            $si['quotation_code'] = $quo['code'];
            $si['quotation_name'] = $quo['name'];
            $si['salesorder_id'] = $si['salesorder_name'] = $si['salesorder_code'] = '';
            unset($si['_id']);
            unset($si['created_by']);
            unset($si['date_modified']);
            unset($si['modified_by']);
            unset($si['quotation_date']);
            unset($si['quotation_status']);
            unset($si['quotation_type']);
            if($this->Salesinvoice->save($si)){
                $id = $this->Salesinvoice->mongo_id_after_save;
                $quo['salesinvoice_id'] = new MongoId($id);
                $quo['salesinvoice_code'] = $si['code'];
                if($_POST['type']=='change_status')
                {
                    $quo['quotation_status'] = 'Approved';
                }
                $this->opm->save($quo);
                echo json_encode(array('status'=>'ok','url'=> URL.'/salesinvoices/entry/'.$id));
                die;
            }
        }
        echo '';
        die;
    }


    public function create_salesorder(){
        if(!$this->check_permission($this->name.'_@_options_@_create_sales_order'))
            $this->error_auth();
        $id = $this->get_id();
        $quo = $this->opm->select_one(array('_id' => new MongoId($id),'deleted'=>false,'company_id'=>array('$ne'=>''),'products'=>array('$elemMatch'=>array('deleted'=>false))));
        if(!empty($quo)){
            $this->selectModel('Salesorder');
            $so = $quo;
            $so['code'] = $this->Salesorder->get_auto_code('code');
            $so['salesorder_date'] = new MongoDate();
            // $so['payment_due_date'] = new MongoDate((isset($so['payment_terms']) ? (int)$so['payment_terms']*DAY : 0 ) + $so['salesorder_date']->sec);
           /* $this->selectModel('Salesaccount');
            $salesaccount = $this->Salesaccount->select_one(array('company_id' => new MongoId($company_id)));
            $so['payment_terms']=isset($salesaccount['payment_terms'])?$salesaccount['payment_terms']:0;
            $so['payment_due_date'] = new MongoDate((int)$so['payment_terms']*DAY + (int)time());*/
            $so['payment_due_date'] = $so['salesorder_date'];
            $so['status'] = 'New';
            $so['sales_order_type'] = 'Sales Order';
            $so['quotation_id'] = new MongoId($id);
            $so['quotation_number'] = $so['quotation_code'] = $quo['code'];
            $so['quotation_name'] = $quo['name'];
            $so['salesinvoice_id'] = $so['salesinvoice_name'] = $so['salesinvoice_code'] = '';
            unset($so['_id']);
            unset($so['created_by']);
            unset($so['date_modified']);
            unset($so['modified_by']);
            unset($so['quotation_date']);
            unset($so['quotation_status']);
            unset($so['quotation_type']);
            if($this->Salesorder->save($so)){
                $id = $this->Salesorder->mongo_id_after_save;
                $quo['salesorder_id'] = new MongoId($id);
                $quo['salesorder_code'] = $so['code'];
                $this->opm->save($quo);
                echo json_encode(array('status'=>'ok','url'=> URL.'/salesorders/entry/'.$id));
                die;
            }
        } else
            echo json_encode(array('status'=>'error','message'=> 'This record has to be linked to a record from Companies or Contacts first. Or No items have been entered on this transaction yet.'));
        die;
    }


	public function asset_tags($ids = ''){
        if($ids=='')
            $ids = $this->get_id();
        $subdatas['asset_tags'] = array();
        if($ids!=''){
            $key = '';
            if(isset($_POST['data'])){
                $key = $_POST['data'];
                $this->Session->write($this->name.'ViewAssetTag',$key);
            } else if(isset($_SESSION[$this->name.'ViewAssetTag'])){
                $key = $_SESSION[$this->name.'ViewAssetTag'];
            }
            if($key!='all'&&$key!=''){
                $key = explode('_', $key);
                (string)$key = $key[1];
            } else
                $key = '';

            $subdatas['asset_tags'] = $this->asset_tags_data($ids,$key);
        }
        $this->set('subdatas', $subdatas);
        $this->set_select_data_list('relationship', 'asset_tags');

        $list_line_entry = array('all'=>'All');
        $quotation = $this->opm->select_one(array('_id'=>new MongoId($ids)),array('products'));
        if(!empty($quotation['products'])){
            foreach($quotation['products'] as $kk => $product){
                if( isset($product['deleted']) && $product['deleted'] || !is_object($product['products_id']) ) continue;
                if( isset($product['option_for'])&&$product['option_for']!='') continue;
                $list_line_entry['xm_'.$kk] = (isset($product['sku'])&&$product['sku']!='' ? $product['sku'] : 'CODE - '.$product['code'])."  (";
                $list_line_entry['xm_'.$kk] .= $product['sizew']." ".$product['sizew_unit'];
                $list_line_entry['xm_'.$kk] .= " x ".$product['sizeh']." ".$product['sizeh_unit'];
                $list_line_entry['xm_'.$kk] .= ")";

            }
        }
        $line_entry_value = 'All';
        $line_entry_id = 'all';
        if(isset($_POST['data'])){
            $line_entry_value = $list_line_entry[$_POST['data']];
            $line_entry_id = $_POST['data'];
        } else if(isset($_SESSION[$this->name.'ViewAssetTag'])){
            $line_entry_value = 'All';
            $line_entry_id = 'all';
            if(isset($list_line_entry[$_SESSION[$this->name.'ViewAssetTag']])){
                $line_entry_value = $list_line_entry[$_SESSION[$this->name.'ViewAssetTag']];
                $line_entry_id = $_SESSION[$this->name.'ViewAssetTag'];
            }
        }
        //pr($list_line_entry);die;
        $this->set('list_line_entry', json_encode($list_line_entry));
        $this->set('line_entry_value', $line_entry_value);


        $option_select_custom['oum'] = array_merge(
             $this->Setting->select_option_vl(array('setting_value'=>'product_oum_area'))
             ,$this->Setting->select_option_vl(array('setting_value'=>'product_oum_lengths'))
             ,$this->Setting->select_option_vl(array('setting_value'=>'product_oum_unit'))
        );
        $this->selectModel('Equipment');
        $option_select_custom['tag'] = $this->Equipment->select_combobox_asset_old();
        $this->set('option_select_custom',$option_select_custom);
    }


    public function asset_tags_data($ids='',$key=''){
        $group = array();
        if($ids!=''){
            $this->selectModel('Task');
            $quotation = $this->opm->select_one(array('_id'=>new MongoId($ids)),array('asset_tags','costing','products'));
            $original_products = $quotation['products'];
            $tmp = $this->line_entry_data('products',0,'options_list');
            $quotation['products'] = $tmp['products'];
            if(!empty($quotation['products'])){
                //$cal_price = new cal_price;
                $this->selectModel('Product');$total_time = $i = $j = 0;
                $num_field = array('quantity','sizew','sizeh');
                $asset_tags = array();
                if(!empty($quotation['asset_tags'])){
                    $asset_tags = $quotation['asset_tags'];
                }
                //loop note products in tb_quotation
                foreach($quotation['products'] as $product){
                    $product_key = $product['_id']; //Vì product của line entry đã đc sort, thứ tự phân biệt bằng _id (để hiển thị đúng [stt ẩn]])
                    if( isset($product['deleted']) && $product['deleted'] ) continue;
                    $cond = '';
                    if(is_object($product['products_id']))
                        $cond = $product['products_id'];
                    else if(isset($quotation['options']) && !empty($quotation['options']))
                        $cond = array($quotation['options'],$product_key);
                    $extra_info = array('line_no'=>$product_key);
                    if(isset($product['option_for']))
                        $extra_info['for_line'] = $product['option_for'];
                    $production = $this->Product->get_product_asset($cond,$extra_info);
                    //Tạo li đỏ nếu chọn all và ko có  option for, hoặc chọn $key
                    if( $key == '' &&(!isset($product['option_for']) || $product['option_for']=='')
                       || $key == $product_key){
                        $group[$i] = array(
                                            '_id'           => '-1',
                                            'asset_key'     =>  '',
                                            'product_key'   =>  $product_key,
                                            'products_name' =>  $product['products_name'],
                                            'product_id'    =>  (isset($product['products_id']) ? $product['products_id'] : ''),
                                            'key'           =>  '',
                                            'product_type'  =>  '',
                                            'code'          =>  (isset($product['code']) ? $product['code'] : ''),
                                            'oum'           =>  $product['oum'],
                                            'tag_key'       =>  '',
                                            'tag'           =>  '',
                                            'min_of_uom'    =>  '',
                                           );
                        $group[$i]['sizew'] = (isset($product['sizew']) ? $product['sizew'] : '').' '.(isset($product['sizew_unit'])&&$product['sizew_unit']!=''? $product['sizew_unit'] : 'in');
                        $group[$i]['sizeh'] = (isset($product['sizeh']) ? $product['sizeh'] : '').' '.(isset($product['sizeh_unit'])&&$product['sizeh_unit']!=''? $product['sizeh_unit'] : 'in');
                        $group[$i]['quantity'] = $product['quantity'];
                        $group[$i]['xempty']['factor'] = '1';
                        $group[$i]['xempty']['min_of_uom'] = '1';

                        $group[$i]['xcss'] = 'background-color:#816060;color:white;font-weight:bold';
                        $i++;
                    }
                    //loop list asset tag of a product
                    foreach($production as $production_key=>$value){
                        if(!isset($value['tag']) || isset($value['deleted'])&&$value['deleted']) continue;
                        if( ($key!='' && ((int)$product_key==(int)$key)) || (isset($product['option_for'])&&(int)$product['option_for']==(int)$key)  || $key=='' ){
                            //Tim code
                            preg_match("/<a ?.*>(.*)<\/a>/", $value['from'], $matches);
                            if(isset($matches[1]))
                                $code = $matches[1];
                            else
                                $code = $value['from'];
                            $group[$i] = array(
                                            '_id'           =>  $j,
                                            'asset_key'     =>  $j,
                                            'products_name' =>  $value['product_name'],
                                            'product_id'    =>  $product['products_id'],
                                            'key'           =>  (isset($product['products_id']) ? $product['products_id'] : '').'_@_'.$product_key.'_@_'.$code.'_@_'.(string)$value['tag_key'],
                                            'product_type'  =>  $value['product_type'],
                                            'code'          =>  $value['from'],
                                            'sell_by'       =>  $value['sell_by'],
                                            'oum'           =>  $value['oum'],
                                            'tag_key'       =>  $value['tag_key'],
                                            'tag'           =>  $value['tag'],
                                            'min_of_uom'    =>  $value['min_of_uom'],
                                            );
                            if(isset($value['for_line_no']))
                                $group[$i]['for_line_no'] = $value['for_line_no'];
                            if(isset($value['line_no'])){
                                $group[$i]['line_no'] = $value['line_no'];
                                if(isset($original_products[$value['line_no']]['products_name'])&&$original_products[$value['line_no']]!=$group[$i]['products_name'])
                                    $group[$i]['products_name'] = $original_products[$value['line_no']]['products_name'];
                            }
                            if(isset($value['for_line'])){
                                $group[$i]['for_line'] = $value['for_line'];
                            }
                            if(!empty($asset_tags)){
                                foreach($asset_tags as $asset_key=>$assettag){
                                    if(!isset($assettag['key'])) continue;
                                    if($assettag['key']==$group[$i]['key']){
                                        if(isset($assettag['factor']))
                                            $group[$i]['factor'] = (float)$assettag['factor'];
                                        if(isset($assettag['min_of_uom']))
                                            $group[$i]['min_of_uom'] = (float)$assettag['min_of_uom'];
                                        break;
                                    }
                                }
                            }
                            //custom factor
                            if(!isset($group[$i]['factor'])&&isset($value['factor']))
                                $group[$i]['factor'] = (float)$value['factor'];
                            else if(!isset($group[$i]['factor']))
                                $group[$i]['factor'] = 0;

                            //custom min_of_uom
                            if(!isset($group[$i]['min_of_uom'])&&isset($value['min_of_uom']))
                                $group[$i]['min_of_uom'] = (float)$value['min_of_uom'];
                            else if(!isset($group[$i]['factor']))
                                $group[$i]['min_of_uom'] = 0;


                            foreach($num_field as $keys){
                                if(isset($product[$keys]))
                                    $group[$i][$keys] = (float)$product[$keys];
                                else
                                    $group[$i][$keys] = 0;
                            }
                            //sizew
                            $group[$i]['sizew'] = (isset($value['sizew']) ? (float)$value['sizew'] : '0' );
                            $group[$i]['sizeh'] = (isset($value['sizeh']) ? (float)$value['sizeh'] : '0' );
                            $group[$i]['sizew_unit'] = (isset($value['sizew_unit']) ? $value['sizew_unit'] : 'in' );
                            $group[$i]['sizeh_unit'] = (isset($value['sizeh_unit']) ? $value['sizeh_unit'] : 'in' );
                            if(isset($product['same_parent'])&&$product['same_parent']==1
                                        &&isset($product['option_for'])&&$product['option_for']!=''){
                                if( $value['oum']=='area'|| $value['sell_by']=='lengths' || (string)$value['product_id']==(string)$product['products_id']){
                                    $parent_product = array();
                                    $option_for = $product['option_for'];
                                    foreach($quotation['products'] as $pro){
                                        if($pro['_id']!=$option_for) continue;
                                        $parent_product = $pro;
                                    }
                                    $group[$i]['sizew'] = (isset($parent_product['sizew']) ? $parent_product['sizew'] : '0' );
                                    $group[$i]['sizew_unit'] = (isset($parent_product['sizew_unit']) ? $parent_product['sizew_unit'] : 'in' );
                                    $group[$i]['sizeh'] = (isset($parent_product['sizeh']) ? $parent_product['sizeh'] : '0' );
                                    $group[$i]['sizeh_unit'] = (isset($parent_product['sizeh_unit']) ? $parent_product['sizeh_unit'] : 'in' );
                                }
                            }else if( $value['sell_by']=='area' || $value['sell_by']=='lengths' || (string)$value['product_id']==(string)$product['products_id'] ){
                                $group[$i]['sizew'] = (isset($product['sizew']) ? $product['sizew'] : '0' );
                                $group[$i]['sizew_unit'] = (isset($product['sizew_unit']) ? $product['sizew_unit'] : 'in' );
                                $group[$i]['sizeh'] = (isset($product['sizeh']) ? $product['sizeh'] : '0' );
                                $group[$i]['sizeh_unit'] = (isset($product['sizeh_unit']) ? $product['sizeh_unit'] : 'in' );
                            }
                            $arr_data = $group[$i];
                            $group[$i]['production_time'] = $this->cal_production_time($arr_data);
                            $group[$i]['sizew'] .= ' '.$group[$i]['sizew_unit'];
                            $group[$i]['sizeh'] .= ' '.$group[$i]['sizeh_unit'];
                            if(strtolower($value['sell_by'])=='unit'){
                                $group[$i]['xempty']['sizew'] = '1';
                                $group[$i]['xempty']['sizeh'] = '1';
                            }
                            $total_time += (float)$group[$i]['production_time'];
                        }
                        $i++;
                        $j++;
                    }//end for
                }//end for
                $this->set('total_time',$total_time);

            }

        }
        // pr($group);die;
        return $group;
    }


    public function production($id = '')
    {
        if($id=='')
            $id = $this->get_id();
        $group = array();
        $quotation = $this->opm->select_one(array('_id'=>new MongoId($id)),array('products'));
        if(!empty($quotation['products']))
        {
			$cal_price = new cal_price;
            $this->selectModel('Product');
            $i = 0;
            foreach($quotation['products'] as $product)
            {
                if( isset($product['deleted'])&&$product['deleted'] || !is_object($product['products_id']) ) continue;
                $production = $this->Product->get_product_asset($product['products_id']);
                if(empty($production)) continue;
                foreach($production as $value){
                    if(!isset($value['tag']) || isset($value['deleted'])&&$value['deleted']) continue;
                    $tag = '(empty)';
                    if($value['tag']!='')
                        $tag = $value['tag'];
                    $quantity = isset($product['quantity'])&&$product['quantity']!='' ? $product['quantity'] : 0;

					$sizew = isset($product['sizew'])&&$product['sizew']!='' ? $product['sizew'] : 0;
					$sizeh = isset($product['sizeh'])&&$product['sizeh']!='' ? $product['sizeh'] : 0;
					$sizew_unit = isset($product['sizew_unit'])&&$product['sizew_unit']!='' ? $product['sizew_unit'] : 'unit';
					$sizeh_unit = isset($product['sizeh_unit'])&&$product['sizeh_unit']!='' ? $product['sizeh_unit'] : 'unit';

					$factor = isset($value['factor'])&&$value['factor']!='' ? $value['factor'] : 0;
                    $total_factor = $quantity*$factor;
					$value['name'] = '';
                    $group[$tag]['product'][$value['from']] = array(
                                                    'products_name' =>  $value['product_name'],
                                                    'name'          =>  $value['name'],
													'product_type'  =>  $value['product_type'],
													'sell_by'       =>  $value['sell_by'],
													'oum'          	=>  $value['oum'],
													'sizew'         =>  $sizew,
													'sizeh'         =>  $sizeh,
													'sizew_unit'    =>  $sizew_unit,
													'sizeh_unit'    =>  $sizeh_unit,
                                                    'quantity'      =>  $quantity,
													'tag_key'     	=>  $value['tag_key'],
                                                    'factor'        =>  $factor,
													'min_of_uom'	=>  $value['min_of_uom'],
                                                    'total_factor'  =>  $total_factor,
                                                );
					$arr_data = $group[$tag]['product'][$value['from']];
					$group[$tag]['product'][$value['from']]['production_time'] = $this->cal_production_time($arr_data);

					if(!isset($group[$tag]['total_factor']))
                        $group[$tag]['total_factor'] = $total_factor;
                    else
                        $group[$tag]['total_factor'] += $total_factor;
                }
            }
        }
        $this->set('group',$group);
        return $group; // BaoNam them de dung $this->requestAction('/quotations/production/'.$arr_quotation['quotation_id']) ben quotations -> tasks_auto_save_default
    }


    public function costings(){
        $query = $this->line_entry_data('products',0,'options_list');
        $query = array_merge($query, $this->opm->select_one(array('_id' => new MongoId($this->get_id())), array('rfqs')));
        if(!isset($query['rfqs']))
            $query['rfqs'] = array();
        $total_amount = 0;
        if(isset($query['products'])&&!empty($query['products'])){
            foreach($query['products'] as $key=>$value){
                if(!isset($value['products_id']) || !is_object($value['products_id'])){
                    unset($query['products'][$key]);
                    continue;
                }
                $this_product = $query['products'][$key];
                if(isset($this_product['xempty']))
                    unset($this_product['xempty']);
                if(isset($this_product['xlock']))
                    unset($this_product['xlock']);
                $this_product['oum'] = ucfirst($value['oum']);
                $this_product['sell_by'] = ucfirst($value['sell_by']);
                if(isset($value['same_parent']) && $value['same_parent']) {
                    foreach($query['products'] as $k => $v) {
                        if($v['_id'] != $value['option_for']) continue;
                        $this_product['sizew'] = $v['sizew'];
                        $this_product['sizeh'] = $v['sizeh'];
                        $this_product['sizew_unit'] = $v['sizew_unit'];
                        $this_product['sizeh_unit'] = $v['sizeh_unit'];
                        $this_product['quantity'] *= $v['quantity'];
                        break;
                    }
                }
                /*if(strtolower($value['sell_by'])=='unit')
                    $this_product['sizew'] = $this_product['sizew_unit'] = $this_product['sizeh'] = $this_product['sizeh_unit'] = '';*/
                if(isset($value['icon']['products_name']))
                    $this_product['icon']['products_name'] = URL.'/products/entry/'.$value['products_id'];
                $currentKey = $value['_id'];
                $rfq = array_filter($query['rfqs'], function($arr) use($currentKey){
                    return  isset($arr['deleted']) && isset($arr['deleted'])
                                && isset($arr['rfq_code']) && $arr['rfq_code'] == $currentKey
                                && $arr['rfq_status'] == 'Accepted'
                                && $arr['unit_price_quoted'] > 0;
                });
                if(!empty($rfq)){
                    $rfq = reset($rfq);
                    $this_product['cost_price'] = isset($rfq['unit_price_quoted']) ? $rfq['unit_price_quoted'] : 0;
                } else {
                    $product = $this->requestAction('/products/costings_data/'.$value['products_id']);
                    $this_product['cost_price'] = $this->opm->format_currency($product['pricingsummary']['cost_price']);
                }
                if($value['sell_by']=='unit') {
                    $this_product['amount'] = $this_product['cost_price'] * $this_product['quantity'];
                    $this_product['adj_qty'] = $this_product['quantity'];
                }
                else if($value['sell_by']=='area') {
                    // if(!isset($this_product['area'])) {
                        $cal_price = new cal_price;
                        $cal_price->arr_product_items = $this_product;
                        $cal_price->cal_area();
                        $cal_price->cal_adj_qty();
                        $this_product['area'] = $cal_price->arr_product_items['area'];
                        $this_product['adj_qty'] = $cal_price->arr_product_items['adj_qty'];
                    // }
                    $this_product['amount'] = $this_product['cost_price'] * $this_product['quantity'] * $this_product['area'];
                }
                else if($value['sell_by']=='lengths') {
                    // if(!isset($this_product['perimeter'])) {
                        $cal_price = new cal_price;
                        $cal_price->arr_product_items = $this_product;
                        $cal_price->cal_perimeter();
                        $cal_price->cal_adj_qty();
                        $this_product['perimeter'] = $cal_price->arr_product_items['perimeter'];
                        $this_product['adj_qty'] = $cal_price->arr_product_items['adj_qty'];
                    // }
                    $this_product['amount'] = $this_product['cost_price'] * $this_product['quantity'] * $this_product['perimeter'];
                }
                $total_amount += $this_product['amount'];
                $query['products'][$key] = $this_product;
            }
        }
        $subdatas['costings']= $query['products'];
        $this->set('subdatas', $subdatas);
        $total_profit = $query['sum_sub_total'] - $total_amount;
        $this->set('total_profit', $this->opm->format_currency($total_profit));
        $this->set('total_costs', $this->opm->format_currency($total_amount));
        $this->set('total_sales', $this->opm->format_currency($query['sum_sub_total']));
        $this->set('margin_total',$this->opm->format_currency(100*($total_profit/($total_amount==0?1:$total_amount)),2).'%');
    }


    public function view_minilist(){
        if(!isset($_GET['print_pdf'])){
            $arr_where = $this->arr_search_where();
            $quotations = $this->opm->select_all(array(
                                                'arr_where' => $arr_where,
                                                'arr_field' => array('code','quotation_type','company_name','phone','quotation_date','our_rep','job_number','quotation_status','sum_amount','sum_sub_total','sum_tax'),
                                                'arr_order' => array('_id'=>1),
                                                ));
            if($quotations->count() > 0){
                $html='';
                $i=0;
                $arr_data = array();
                $total_amount = 0;
                $total_tax = 0;
                $total_sub = 0;
                foreach($quotations as $key => $quotation){
                    $total_amount += $sum_amount = (isset($quotation['sum_amount']) ? (float)$quotation['sum_amount'] : 0);
                    $total_tax += $sum_tax = (isset($quotation['sum_tax']) ? (float)$quotation['sum_tax'] : 0);
                    $total_sub += $sum_sub_total = (isset($quotation['sum_sub_total']) ? (float)$quotation['sum_sub_total'] : 0);
                    $html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">';
                    $html .= '<td class="center_text">'.$quotation['code'].'</td>';
                    $html .= '<td>'.(isset($quotation['quotation_type']) ? $quotation['quotation_type'] : '') .'</td>';
                    $html .= '<td>'.(isset($quotation['company_name']) ? $quotation['company_name'] : '') .'</td>';
                    $html .= '<td class="center_text">'.(isset($quotation['phone']) ? $quotation['phone'] : '') .'</td>';
                    $html .= '<td class="center_text">'.(isset($quotation['quotation_date']) ? date('m/d/Y',$quotation['quotation_date']->sec):'') .'</td>';
                    $html .= '<td class="center_text">'.(isset($quotation['our_rep']) ? $quotation['our_rep'] : '') .'</td>';
                    $html .= '<td>'.(isset($quotation['job_number']) ? $quotation['job_number'] : '') .'</td>';
                    $html .= '<td class="center_text">'.(isset($quotation['quotation_status']) ? $quotation['quotation_status'] : '') .'</td>';
                    $html .= '<td class="right_text">'.$this->opm->format_currency($sum_sub_total) .'</td>';
                    $html .= '<td class="right_text">'.$this->opm->format_currency($sum_tax) .'</td>';
                    $html .= '<td class="right_text">'.$this->opm->format_currency($sum_amount) .'</td>';
                    $html .= '</tr>';
                    $i++;
                }
                $html .='<tr class="last">
                            <td colspan="7" class="bold_text right_none">'.$i.' record(s) listed.</td>
                            <td class="bold_text right_none">Totals:</td>
                            <td class="bold_text right_none right_text">'.$this->opm->format_currency($total_sub).'</td>
                            <td class="bold_text right_none right_text">'.$this->opm->format_currency($total_tax).'</td>
                            <td class="bold_text right_none right_text">'.$this->opm->format_currency($total_sub).'</td>
                        </tr>';
                $arr_data['title'] = array('Ref no','Type','Company / Contact', 'Phone', 'Date', 'Our rep', 'Job no', 'Status','Total bf. Tax'=>'text-align: right','Tax'=>'text-align: right','Total Amount'=>'text-align: right');
                $arr_data['content'] = $html;
                $arr_data['report_name'] = 'Quotation Mini  Listing';
                $arr_data['report_file_name']  = 'Qo_'.md5(time());
                $arr_data['report_orientation'] = 'landscape';
                Cache::write('quotations_minilist', $arr_data);
            }
        }else {
            $arr_data = Cache::read('quotations_minilist');
            Cache::delete('quotations_minilist');
        }
        $this->render_pdf($arr_data);
    }


    public function create_new_custom_product(){
        if(isset($_POST['product_line'])){
            $product_line = $_POST['product_line'];
            $query = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())));
            if(!isset($query['products']) || empty($query['products'])
                || !isset($query['products'][$product_line]) //Neu khong ton tai product nay
                || (isset($query['products'][$product_line]['deleted'])&&$query['products'][$product_line]['deleted']) /*product nay da bi xoa*/ ){
                echo 'This record is deleted or does not exist!';
                die;
            }
            $this_product = $query['products'][$product_line];
            $product_id = '';
            $product = array();
            $this->selectModel('Product');
            //Neu product co ID
            if( isset($query['products'][$product_line]['products_id'])&&is_object($query['products'][$product_line]['products_id']) ){
                $product_id = $query['products'][$product_line]['products_id'];
                $product = $this->Product->select_one(array('_id'=>new MongoId($product_id)));
            } else { //Custom product
                //Lay default
                $this->Product->arrfield();
                $default_field = $this->Product->arr_temp;
                $product = $default_field;
                $product['sell_price'] = $product['unit_price'] = $product['cost_price'] =  0;
                $product['product_type'] = 'Custom Product';
            }
            //$option = $this->option_list_data($product_id,$product_line);
            $costing_for_line = $this->costing_for_line($product_line);
            foreach($costing_for_line['costing_list'] as $key=>$value){
                if(isset($value['xlock']))
                    unset($costing_for_line['costing_list'][$key]['xlock']);
                $costing_for_line['costing_list'][$key]['require'] = 1;
                $costing_for_line['costing_list'][$key]['same_parent'] = 1;
            }
            $product['code'] = $this->Product->get_auto_code('code');
            $product['name'] = $this_product['products_name'];
            $product['sizeh'] = $this_product['sizeh'];
            $product['sizeh_unit'] = $this_product['sizeh_unit'];
            $product['sizew'] = $this_product['sizew'];
            $product['sizew_unit'] = $this_product['sizew_unit'];
            $product['sku'] = $this_product['sku'];
            $product['sell_by'] = $this_product['sell_by'];
            $product['oum'] = $this_product['oum'];
            $product['options'] = $costing_for_line['costing_list'];
            $product['created_by'] = new MongoId($this->opm->user_id());
            unset($product['_id']);
            unset($product['modified_by']);
            $this->Product->save($product);
            $new_product_id = $this->Product->mongo_id_after_save;
            $query['products'][$product_line]['products_id'] = $new_product_id;
            $query['products'][$product_line]['code'] = $product['code'];
            $query['products'][$product_line]['is_saved'] = true;
            if($this->opm->save($query)){
                echo 'ok';
                die;
            } else {
                echo $this->opm->arr_errors_save[1];
                die;
            }
        }
        die;
    }


    public function save_over_older_custom_product(){
        if(isset($_POST['product_line'])){
            if(!isset($_POST['replace_id']) || strlen($_POST['replace_id'])!=24){
                echo 'There is something wrong. Please refresh and try again!';
                die;
            }
            $product_line = $_POST['product_line'];
            $query = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())));
            if(!isset($query['products']) || empty($query['products'])
                || !isset($query['products'][$product_line]) //Neu khong ton tai product nay
                || (isset($query['products'][$product_line]['deleted'])&&$query['products'][$product_line]['deleted']) /*product nay da bi xoa*/ ){
                echo 'This record is deleted or does not exist!';
                die;
            }
            $this_product = $query['products'][$product_line];
            $product_id = '';
            $product = array();
            $this->selectModel('Product');
            //Lay default
            $this->Product->arrfield();
            $default_fields = $this->Product->arr_temp;
            //Neu product co ID
            if( isset($query['products'][$product_line]['products_id'])&&is_object($query['products'][$product_line]['products_id']) ){
                $product_id = $query['products'][$product_line]['products_id'];
                $product = $this->Product->select_one(array('_id'=>new MongoId($product_id)));
                $arr_tmp['sellprices'] = (isset($product['sellprices']) ? $product['sellprices'] : array());
                $arr_tmp['pricebreaks'] = (isset($product['pricebreaks']) ? $product['pricebreaks'] : array());
                $arr_tmp['price_note'] = (isset($product['price_note']) ? $product['price_note'] : '');
                if(isset($product['pricing_method'])&&$product['pricing_method']!='')
                    $arr_tmp['pricing_method'] = $product['pricing_method'];
                $arr_tmp['sell_price'] = (isset($product['sell_price']) ? (float)$product['sell_price'] : 0);
                $arr_tmp['code'] = $product['code'];
                $product = $default_fields;
                $product = array_merge($product,$arr_tmp);
            } else { //Custom product
                $product_id = $_POST['replace_id'];
                $product_replace = $this->Product->select_one(array('_id'=>new MongoId($product_id)));
                $product_id = '';
                $product = $default_fields;
                $product['sell_price'] = $product['unit_price'] = $product['cost_price'] =  0;
                $product['product_type'] = 'Custom Product';
                $product['code'] = $product_replace['code'];
            }
            //$option = $this->option_list_data($product_id,$product_line);
            $option = $this->costing_for_line($product_line);
            foreach($option['costing_list'] as $key=>$value){
                if(isset($value['xlock']))
                    unset($option['costing_list'][$key]['xlock']);
                $option['costing_list'][$key]['require'] = 1;
                $option['costing_list'][$key]['same_parent'] = 1;
            }
            $product['name'] = $this_product['products_name'];
            $product['sizeh'] = $this_product['sizeh'];
            $product['sizeh_unit'] = $this_product['sizeh_unit'];
            $product['sizew'] = $this_product['sizew'];
            $product['sizew_unit'] = $this_product['sizew_unit'];
            $product['sku'] = $this_product['sku'];
            $product['sell_by'] = $this_product['sell_by'];
            $product['oum'] = $this_product['oum'];
            $product['options'] = (isset($option['costing_list'])&&!empty($option['costing_list']) ? $option['costing_list'] : array());
            $product['created_by'] = new MongoId($this->opm->user_id());
            $product['_id'] = new MongoId($_POST['replace_id']);
            unset($product['modified_by']);
            $this->Product->save($product);
            $new_product_id = $product['_id'];
            $query['products'][$product_line]['products_id'] = $new_product_id;
            $query['products'][$product_line]['code'] = $product['code'];
            $query['products'][$product_line]['is_saved'] = true;
            if($this->opm->save($query)){
                echo 'ok';
                die;
            } else {
                echo $this->opm->arr_errors_save[1];
                die;
            }
        }
        die;

    }
    function rebuild_quotation_code(){
        $query = $this->opm->select_all(array('arr_field'=>array('code'),'arr_order'=>array('_id'=>1),'limit'=>9999));
        echo $query->count().'<br />';
        $i = $m = 0;
        foreach($query as $value){
            $i++;
            $arr_data = array(
                              '_id'=>new MongoId($value['_id']),
                              'code'=>$i,
                              );

            $this->opm->rebuild_collection($arr_data);
        }
        echo 'Xong - '.$i;
        die;
    }

    function create_email_pdf($ajax=false,$type = ''){
        $this->selectModel('Quotation');
        $quotation = $this->Quotation->select_one(array('_id' => new MongoId($this->get_id())),array('sum_amount','quotation_accepted'));
        if(IS_LOCAL && $quotation['sum_amount'] > 20000000 && (!isset($quotation['quotation_accepted']) ||  !$quotation['quotation_accepted'])){
            $this->selectModel('Company');
            $company = $this->Company->select_one(array('system'=>true),array('contact_default_id','name'));
            $this->selectModel('Contact');
            $contact = $this->Contact->select_one(array('_id'=>$company['contact_default_id']),array('first_name','last_name'));
            echo "This quotation is greater than 2,000. It needs to be accepted by {$contact['first_name']} {$contact['last_name']} - {$company['name']}.";
            die;
        }
        parent::create_email_pdf($ajax,$type);
    }
    function check_quotation(){
        $id = $this->get_id();
        $this->selectModel('Quotation');
        $quotation = $this->Quotation->select_one(array('_id' => new MongoId($id)),array('sum_amount','quotation_accepted','code'));
        if(IS_LOCAL && $quotation['sum_amount'] > 20000000 && (!isset($quotation['quotation_accepted']) ||  !$quotation['quotation_accepted'])){
            $this->selectModel('Company');
            $company = $this->Company->select_one(array('system'=>true),array('contact_default_id','name'));
            $this->selectModel('Contact');
            $contact = $this->Contact->select_one(array('_id'=>$company['contact_default_id']),array('first_name','last_name','email'));
            $extra_contact = $this->Contact->select_one(array('company_id'=>$company['_id'],'position' => 'Managing Director'),array('first_name','last_name','email'));
            ini_set('output_buffering', 'off');
            ini_set('zlib.output_compression', false);
            while (@ob_end_flush());
            ini_set('implicit_flush', true);
            ob_implicit_flush(true);
            echo "This quotation is greater than 2,000. It needs to be accepted by {$contact['first_name']} {$contact['last_name']} - {$company['name']}.";
            @ob_flush();
            @flush();
            $arr_data = array(
                            'subject' =>'Please accept Quotation '.$quotation['code'],
                            'template' => 'Dear Anvy Digital Administrators.<br/>We would like to inform you that there is just a new Quotation ref no. '.$quotation['code'].' of $'.$this->opm->format_currency($quotation['sum_amount']).'.<br />In order to preview this Quotation, please click <a href="'.URL.'/quotations/entry/'.$id.'">here</a>.<br />If you agree to proceed this Quotation, please make necessary adjustment to it, then please do not forget to click your acceptance <a href="'.URL.'/quotations/accept_quotation/'.$id.'">here</a> so that Anvy Digital staff can email PDF file for customer.<br />Otherwise, if you refuse this Quotation, please click here <a href="'.URL.'/quotations/cancel_quotation/'.$id.'">here</a>  to cancel this Quotation.<br />Sincerely,<br />Any Digital Jobtraq.',
                            'to' => array($contact['email'],$extra_contact['email']),
                            'cc' => 'jobtraq@anvydigital.com',
                            'no_save_comms' => true,
                            );
            $this->auto_send_email($arr_data);
            die;
        }
        echo 'ok';
        die;
    }

    function accept_quotation($id){
        if(is_null($id) || strlen($id) != 24){
            echo 'Quotation was not existed.';
            die;
        }
        $this->selectModel('Company');
        $company = $this->Company->select_one(array('system'=>true),array('contact_default_id'));
        $this->selectModel('Contact');
        $contact = $this->Contact->select_one(array('_id'=>$company['contact_default_id']),array('first_name','last_name'));
        $extra_contact = $this->Contact->select_one(array('company_id'=>$company['_id'],'position' => 'Managing Director'),array('first_name','last_name','email'));
        $message = "";
        $arr_id = array('100000000000000000000000',(string)$contact['_id'],(string)$extra_contact['_id']);
        if( !in_array((string)$_SESSION['arr_user']['contact_id'], $arr_id ) ){
            $message =  "You have no permission to accept this Quotation. Send this link to {$contact['first_name']} {$contact['last_name']} or {$extra_contact['first_name']} {$extra_contact['last_name']}.";
            $this->selectModel('Quotation');
        } else{
            $this->Quotation->save(array('_id' => new MongoId($id),'quotation_accepted' => true));
            $message =  "This Quotation has been accepted.";
        }
        $this->Session->setFlash($message,'default',array('class'=>'flash_message'));
        $this->redirect('/quotations/entry/'.$id);
    }

    function cancel_quotation($id){
        if(is_null($id) || strlen($id) != 24){
            echo 'Quotation was not existed.';
            die;
        }
        $this->selectModel('Company');
        $company = $this->Company->select_one(array('system'=>true),array('contact_default_id'));
        $this->selectModel('Contact');
        $contact = $this->Contact->select_one(array('_id'=>$company['contact_default_id']),array('first_name','last_name'));
        $extra_contact = $this->Contact->select_one(array('company_id'=>$company['_id'],'position' => 'Managing Director'),array('first_name','last_name','email'));
        $message = "";
        $arr_id = array('100000000000000000000000',(string)$contact['_id'],(string)$extra_contact['_id']);
        if( !in_array((string)$_SESSION['arr_user']['contact_id'], $arr_id ) ){
            $message =  "You have no permission to cancel this Quotation. Send this link to {$contact['first_name']} {$contact['last_name']} or {$extra_contact['first_name']} {$extra_contact['last_name']}.";
            $this->selectModel('Quotation');
        } else{
            $this->Quotation->save(array('_id' => new MongoId($id),'quotation_status' => 'Cancelled'));
            $message =  "This Quotation has been cancel.";
        }
        $this->Session->setFlash($message,'default',array('class'=>'flash_message'));
        $this->redirect('/quotations/entry/'.$id);
    }

    public function build_product_quotations(){
        $quotations = $this->opm->select_all(array(
                               'arr_where' => array(
                                                    '$or'=>array(
                                                                array(
                                                                      'products' => array(
                                                                          '$in' => array('',null)
                                                                          )
                                                                      ),
                                                                array(
                                                                      'products' => array('$exists' => false)
                                                                      )
                                                                 )
                                                    ),
                               'arr_field' =>array('_id'),
                               ));
        echo $quotations->count().' records found.<br />';
        $i = 0;
        foreach($quotations as $quote){
            $quote['products'] = array();
            $this->opm->rebuild_collection($quote);
            $i++;
        }
        echo $i.' - Done';
        die;
    }


	public function cal_price_printing($arr_post=array()){
		$line_no = 0;
		if(isset($_POST['data'])){
            $arr_post = $_POST;
            $arr_post['data'] = (array)json_decode($_POST['data']);
			if(isset($arr_post['data']['id']))
			$line_no = $arr_post['data']['id'];
        }

		//normal call price
		$arr_result_normal = $this->cal_price_line($arr_post,1);

		//get info
		$query = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())),array('products','options','company_id','tax',));
		$date = $query['_id']->getTimestamp();
		$line_data = $query['products'][$line_no];

		$options = $this->new_option_data(array('key'=>$line_no,'products_id'=>$line_data['products_id'],'options'=>$query['options'],'products'=>$query['products'],'date'=>$date),$query['products']);
		if(isset($options['option'])&&!empty($options['option'])){
			foreach($options['option'] as $key=>$value){
				if(!$value['deleted']){
					if(isset($value['group_type']) && $value['group_type']=='Exc'){
						$price_option[$value['option_group'].'_arr'][(string)$value['sub_total']] = $value['product_name'].'('.$value['sub_total'].')';
						if(isset($value['choice']) && $value['choice'] == 1)
							$price_option[$value['option_group']] = $value['sub_total'];
					}else if(isset($value['choice']) && $value['choice'] == 1)
						$price_option[$value['option_group']] = $value['sub_total'];
					else
						$price_option[$value['option_group']] = '';
				}
			}
		}


		$w = $h = $wr = $hr = 1;
		$wr = $line_data['sizew'];
		$hr = $line_data['sizeh'];
		$cal_price = new cal_price();
		$wr = $cal_price->unit_convertor->unit_convertor($line_data['sizew'],$line_data['sizew_unit'],'in',5);
		$hr = $cal_price->unit_convertor->unit_convertor($line_data['sizeh'],$line_data['sizeh_unit'],'in',5);
		$this->selectModel('Product');
		if(isset($line_data['products_id']))
			$product = $this->Product->select_one(array('_id' => new MongoId($line_data['products_id'])),array('paper_size'));
		if(isset($product['paper_size'])){
			$size = explode("x",$product['paper_size']);
			$w = max((float)$size[0],(float)$size[1]);
			$h = min((float)$size[0],(float)$size[1]);
		}
		$yield = $this->sheet_yield_calculator($w,$h,$wr,$hr,1);
		$paper_amount = (float)$line_data['quantity']/(float)$yield['total_yield'];


		$cut_rate = 1.5;
		$printer_pricing = (float)$line_data['quantity']*(float)$price_option['inkcolor'] + (float)$price_option['rip'] + (float)$price_option['packing'] + $paper_amount*$cut_rate*(float)$price_option['cutting'];


		//change line entry
		$arr_post['data'] = array();
		$arr_post['data']['custom_unit_price'] = $printer_pricing/(float)$line_data['quantity'];
		$arr_post['data']['id'] = $line_no;
		$arr_post['fieldchange'] = 'custom_unit_price';

		$arr_result = $this->cal_price_line($arr_post,1);
		//pr($options);pr($price_option);pr($yield); pr($printer_pricing);pr($arr_result);die;


		if(isset($_POST['data'])){
			echo $printer_pricing;die;
		}else
			return $printer_pricing;
	}

    function inventory_report(){
        if(!isset($_GET['print_pdf'])){
            $ids = $this->get_id();
            $quote = $this->opm->select_one(array('_id'=>new MongoId($ids)),array('date_modified','salesorder_id'));
            $prefix_cache_name = 'line_quotation_'.$ids.'_';
            $cache_name = $prefix_cache_name.$quote['date_modified']->sec;
            $arr_ret = Cache::read($cache_name);
            if(!$arr_ret){
                $arr_ret = $this->line_entry_data('products', 0);
                Cache::write($cache_name,$arr_ret);
                $old_cache = $this->get_cache_keys_diff($cache_name,$prefix_cache_name);
                foreach($old_cache as $cache){
                    Cache::delete($cache);
                }
            }
            $arr_products = array();
            $this->selectModel('Purchaseorder');
            $this->selectModel('Product');
            $this->selectModel('Salesorder');
            $sales_product = array();
            if(isset($quote['salesorder_id']) && is_object($quote['salesorder_id'])){
                $sales_product = $this->Salesorder->select_one(array('_id'=>$quote['salesorder_id']),array('products'));
                if(isset($sales_product['products']))
                    $sales_product = $sales_product['products'];
                else
                    $sales_product = array();
            }
            foreach($arr_ret['products'] as $product){
                if(isset($product['product_id']) && is_object($product['product_id']) )
                    $product['products_id'] = $product['product_id'];
                if( isset($product['products_id']) && is_object($product['products_id'])){

                    $pro = $this->Product->select_one(array('_id' => new MongoId($product['products_id'])),array('company_name','in_stock','product_type'));
                    $product['company_name'] = isset($pro['company_name']) ? $pro['company_name'] : '';
                    $product['in_stock'] = isset($pro['in_stock']) ? (float)$pro['in_stock'] : 0;
                    $product['type'] = isset($pro['product_type']) ? $pro['product_type'] : '';
                    $tmp_pro = $this->Purchaseorder->collection->aggregate(
                        array(
                            '$match'=>array(
                                            'deleted'=> false,
                                            'purchase_orders_status' => array('$nin' => array('Cancelled')),
                                            'products' => array(
                                                                '$exists' => true,
                                                                '$nin' => array('',null)
                                                                ),
                                            ),
                        ),
                        array(
                            '$unwind'=>'$products',
                        ),
                         array(
                            '$match'=>array(
                                            'products.deleted'=> false,
                                            'products.products_id' => $pro['_id']
                                            )
                        ),
                        array(
                            '$project'=>array('products'=>'$products')
                        ),
                        array(
                            '$group'=>array(
                                          '_id'=>array('_id'=>'$_id'),
                                          'products'=>array('$push'=>'$products')
                                        )
                        )
                    );
                    $product['order'] = 0;
                    if(isset($tmp_pro['ok']) && $tmp_pro['ok']){
                        foreach($tmp_pro['result'] as $tmp){
                            foreach($tmp['products'] as $p)
                                $product['order'] += $p['quantity'];
                        }
                    }

                    $product['sold'] = 0;
                    if(!empty($sales_product)){
                        foreach($sales_product as $tmp){
                            if(isset($tmp['deleted']) && $tmp['deleted']) continue;
                            if($tmp['products_id'] != $pro['_id']) continue;
                            $product['sold']+= $tmp['quantity'];
                        }
                    }
                    $product['return'] = 0;
                } else {
                    $product['company_name'] = '';
                    $product['in_stock'] = 0;
                    $product['order'] = 0;
                    $product['return'] = 0;
                    $product['sold'] = 0;
                    $product['type'] = '';
                }
                $arr_products[] = array(
                                        'code' => isset($product['code']) ? $product['code'] : '',
                                        'name' => isset($product['products_name']) ? $product['products_name'] : '',
                                        'type' => $product['type'],
                                        'company_name' => $product['company_name'],
                                        'order' => $product['order'],
                                        'in_stock' => $product['in_stock'],
                                        'return' => $product['return'],
                                        'sold' => $product['sold'],
                                        );
            }

            $i = 0;
            $html = '';
            foreach($arr_products as $product){
                $html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">';
                $html .= '<td>'.$product['code'] .'</td>';
                $html .= '<td>'. $product['name'] .'</td>';
                $html .= '<td class="center_text">'.$product['type'] .'</td>';
                $html .= '<td>'. $product['company_name'] .'</td>';
                $html .= '<td class="center_text">'. $product['order'] .'</td>';
                $html .= '<td class="center_text">'. $product['in_stock'] .'</td>';
                $html .= '<td class="center_text">'. $product['return'] .'</td>';
                $html .= '<td class="center_text">'. $product['sold'] .'</td>';
                $html .= '<td class="center_text">'. ($product['in_stock'] - $product['return'] - $product['sold']) .'</td>';
                $html .= '</tr>';
                $i++;
            }
            $html .='<tr class="last">
                    <td colspan="9" class="bold_text right_none">'.$i.' record(s) listed.</td>
                </tr>';
            $arr_data['title'] = array('Code#','Name/ Description'=>'text-align: left',  'Type',  'Supplier'=>'text-align: left', 'Order'=>'text-align: center','In-Stock'=>'text-align: center', 'Return'=>'text-align: center','SOLD'=>'text-align: center','Balance'=>'text-align: center',);
            $arr_data['content'] = $html;
            $arr_data['report_name'] = 'Inventory Report';
            $arr_data['report_file_name'] = 'SI_'.md5(time());
            $arr_data['report_orientation'] = 'landscape';
            $arr_data['excel_url'] = URL.'/quotations/inventory_report_excel';
            Cache::write('inventory_report', $arr_data);
            Cache::write('inventory_report_excel', $arr_products);
        } else {
            $arr_data = Cache::read('inventory_report');
            Cache::delete('inventory_report');
        }
        $this->render_pdf($arr_data);
    }

    function inventory_report_excel(){
        $arr_products = Cache::read('inventory_report_excel');
        if(!$arr_products){
            echo 'No data';die;
        }
        App::import('Vendor', 'phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("")
                                     ->setLastModifiedBy("")
                                     ->setTitle("Inventory Report")
                                     ->setSubject("Inventory Report")
                                     ->setDescription("Inventory Report")
                                     ->setKeywords("Inventory Report")
                                     ->setCategory("Inventory Report");
        $worksheet = $objPHPExcel->getActiveSheet();
        $objPHPExcel->getActiveSheet()->setCellValue('A1',"Code#")
                                        ->setCellValue('B1',"Name/ Description")
                                        ->setCellValue('C1',"Type")
                                        ->setCellValue('D1',"Supplier")
                                        ->setCellValue('E1',"Order")
                                        ->setCellValue('F1',"In-Stock")
                                        ->setCellValue('G1',"Return")
                                        ->setCellValue('H1',"SOLD")
                                        ->setCellValue('I1',"Balance");
        $objPHPExcel->getActiveSheet()->freezePane('J2');
        $i = 2;
        foreach($arr_products as $product){
            $worksheet->setCellValue('A'.$i,$product['code'])
                        ->setCellValue('B'.$i,$product['name'])
                        ->setCellValue('C'.$i,$product['type'])
                        ->setCellValue('D'.$i,$product['company_name'])
                        ->setCellValue('E'.$i,$product['order'])
                        ->setCellValue('F'.$i,$product['in_stock'])
                        ->setCellValue('G'.$i,$product['return'])
                        ->setCellValue('H'.$i,$product['sold'])
                        ->setCellValue('I'.$i,"=F$i-G$i-H$i");
            $i ++;
        }
        $worksheet->setCellValue('A'.$i,($i-2).' record(s) listed');
        $worksheet->mergeCells("A$i:C$i");
        $worksheet->getRowDimension(8)->setRowHeight(-1);
        $worksheet->getStyle('A1:I1')->getFont()->setBold(true);
        $worksheet->getStyle('A1:A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $worksheet->getStyle('B1:B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $worksheet->getStyle('C1:C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $worksheet->getStyle('E1:I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $styleArray = array(
                'borders' => array(
                   'allborders' => array(
                       'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'font'  => array(
                    'size'  => 12,
                    'name'  => 'Century Gothic'
                )
        );
        $worksheet->getStyle('A1:I'.($i-1))->applyFromArray($styleArray);
        for($i = 'A'; $i !== 'I'; $i++){
            $worksheet->getColumnDimension($i)
                            ->setAutoSize(true);
        }
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(APP.DS.'webroot'.DS.'upload'.DS.'Inventory_Report.xlsx');
        Cache::delete('inventory_report_excel');
        $this->redirect('/upload/Inventory_Report.xlsx');
        die;
    }

    function create_purchaseorder_from_rfq(){
        if(isset($_POST['rfq_id'])){
            $id = $this->get_id();
            $data = $this->opm->select_one(array('_id' => new MongoId($id)), array('code','products','rfqs'));
            $create_blank = false;
            $rfq = isset($data['rfqs'][$_POST['rfq_id']]) ? $data['rfqs'][$_POST['rfq_id']] : array();
            // if( !empty($rfq) && isset($rfq['rfq_code']) && isset($data['products'][$rfq['rfq_code']]) ) {
            //     $product = $data['products'][$rfq['rfq_code']];
            //     if( !isset($product['products_id']) || !is_object($product['products_id']) ){
            //         $create_blank = true;
            //         echo json_encode(array('status' => 'error', 'message' => 'Cannot found product to create Purchaseorder'));
            //         die;
            //     }
            // }
            $this->selectModel('Product');
            $po_product = array();
            $blank_po = false;
            if( !empty($rfq) && isset($rfq['rfq_code']) && isset($data['products'][$rfq['rfq_code']]) ) {
                $product = $data['products'][$rfq['rfq_code']];
                if( !isset($product['products_id']) || !is_object($product['products_id']) ){
                    $po_product = array();
                    $blank_po = true;
                }
            } else {
                $po_product = $this->Product->select_one(array('_id' => $product['products_id'], 'product_type' => 'VENDOR STOCK'), array('name','code','sku','sell_price', 'oum'));
            }
            $this->selectModel('Purchaseorder');
            $this->selectModel('Company');
            $this->selectModel('Contact');
            $arr_costing = array();
            if( empty($po_product) && !$blank_po ) {
                $arr_costing = $this->Product->findVendorCosting($product['products_id']);
                $blank_po = true;
            }
            if( !empty($arr_costing) ){
                $arr_purchaseorder = array();
                $i = 0;
                foreach($arr_costing as $company_id => $products){
                    $this->Purchaseorder->arr_default_before_save = array();
                    $po_products = array();
                    if( !empty($company_id) ){
                        $company = $this->Company->select_one(array('_id' => new MongoId($company_id)),array('name','phone','fax','email','contact_default_id'));
                        if( !empty($company) ){
                            $this->Purchaseorder->arr_default_before_save['company_id'] = $company['_id'];
                            $this->Purchaseorder->arr_default_before_save['company_name'] = isset($company['name']) ? $company['name'] : '';
                            $this->Purchaseorder->arr_default_before_save['phone'] = isset($company['phone']) ? $company['phone'] : '';
                            $this->Purchaseorder->arr_default_before_save['fax'] = isset($company['fax']) ? $company['fax'] : '';
                            $this->Purchaseorder->arr_default_before_save['email'] = isset($email['name']) ? $company['email'] : '';
                            if( isset($company['contact_default_id']) && is_object($company['contact_default_id']) ) {
                                $contact = $this->Contact->select_one(array('_id' => $company['contact_default_id']),array('first_name','last_name'));
                            } else {
                                $contact = $this->Contact->select_one(array('company_id' => $company['_id']),array('first_name','last_name'));
                            }
                            if( !empty($contact) ) {
                                $this->Purchaseorder->arr_default_before_save['contact_id'] = $contact['_id'];
                                $this->Purchaseorder->arr_default_before_save['contact_name'] = (isset($contact['first_name']) ? $contact['first_name'].' ' : '').(isset($contact['last_name']) ? $contact['last_name'] : '');
                            }
                        }
                    }
                    foreach($products as $product){
                        $po_products[] = array(
                                    'products_id'   => $product['_id'],
                                    'code'          => isset($product['code']) ? $product['code'] : '',
                                    'sku'           => isset($product['sku']) ? $product['sku'] : '',
                                    'products_name' => isset($product['name']) ? $product['name'] : '',
                                    'sizew'         => 0,
                                    'sizeh'         => 0,
                                    'sizew_unit'    => 'unit',
                                    'sizeh_unit'    => 'unit',
                                    'sell_price'    => isset($product['sell_price']) ? $product['sell_price'] : '',
                                    'sell_by'       => isset($product['sell_by']) ? $product['sell_by'] : 'area',
                                    'oum'           => isset($product['oum']) ? $product['oum'] : 'Sq.ft.',
                                    'unit_price'    => 0,
                                    'quantity'      => 1,
                        );
                    }
                    $this->Purchaseorder->arr_default_before_save['quotation_id'] = $data['_id'];
                    $this->Purchaseorder->arr_default_before_save['quotation_number'] = $data['code'];
                    $this->Purchaseorder->arr_default_before_save['products'] = $po_products;
                    $this->Purchaseorder->arr_default_before_save['code'] = $this->Purchaseorder->get_auto_code('code');
                    $this->Purchaseorder->add();
                    $i++;
                    $arr_purchaseorder[] = $this->Purchaseorder->arr_default_before_save['code'];
                }
                if( !empty($arr_purchaseorder) ){
                    $message = 'There '.( count($arr_purchaseorder) > 1 ? 'are' : 'is' ).' '.count($arr_purchaseorder).' Purchase Order'.( count($arr_purchaseorder) > 1 ? 's' : '' ).' created: '.(implode($arr_purchaseorder, ',')).'.';
                    echo json_encode(array('status' => 'error', 'message' => $message, 'url' => URL.'/purchaseorders/entry/'.$this->Purchaseorder->mongo_id_after_save));
                    die;
                }
            } else {
                $products = array();
                if( !$blank_po ) {
                    $products[] = array(
                                    'products_id'   => isset($po_product['_id']) ? $po_product['_id'] : '',
                                    'code'          => isset($po_product['code']) ? $po_product['code'] : '',
                                    'sku'           => isset($po_product['sku']) ? $po_product['sku'] : '',
                                    'products_name' => isset($po_product['name']) ? $po_product['name'] : '',
                                    'sizew'         => 0,
                                    'sizeh'         => 0,
                                    'sizew_unit'    => 'unit',
                                    'sizeh_unit'    => 'unit',
                                    'sell_price'    => isset($po_product['sell_price']) ? $po_product['sell_price'] : '',
                                    'sell_by'       => isset($po_product['sell_by']) ? $po_product['sell_by'] : 'area',
                                    'oum'           => isset($po_product['oum']) ? $po_product['oum'] : 'Sq.ft.',
                                    'unit_price'    => 0,
                                    'quantity'      => 1,
                        );
                }
                $this->Purchaseorder->arr_default_before_save['quotation_id'] = $data['_id'];
                $this->Purchaseorder->arr_default_before_save['quotation_number'] = $data['code'];
                $this->Purchaseorder->arr_default_before_save['products'] = $products;
            }
            $this->Purchaseorder->add();
            echo json_encode(array('status' => 'ok', 'url' => URL.'/purchaseorders/entry/'.$this->Purchaseorder->mongo_id_after_save));
            die;
        }
        echo json_encode(array('status' => 'error', 'message' => 'Cannot found product to create Purchaseorder'));
        die;
    }

    function test(){
        $app = rtrim(APP,DS);
        $arr_folder = $this->getFolder($app);
        pr($arr_folder);die;
    }

    function getFolder($folder_path, &$arr_folder = array()){
        $folders = array_filter(glob($folder_path.DS.'*'), 'is_dir');
        if(empty($folders)){
            $arr_folder[] = $folder_path;
            return $arr_folder;
        }
        foreach($folders as $folder){
            $this->getFolder($folder,$arr_folder);
        }
        return $arr_folder;
    }

    function missing_tax()
    {
        $quotations = $this->opm->select_all(array(
                        'arr_where' => array(
                                '_id' => array( '$gt' => new MongoId( str_pad(dechex(strtotime('2015-04-01 00:00:00')), 8, '0', STR_PAD_LEFT).'0000000000000000'  ) ),
                                'taxval' => array( '$gt' => 0 ),
                                'products' => array('$elemMatch' => array(
                                                                        'deleted' => false,
                                                                        'taxper'  => 0
                                    ))
                            ),
                        'arr_field' => array('code'),
                        'arr_order' => array('_id' => 1)
            ));
        $html = '';
        foreach($quotations as $quotation) {
            $html .= 'Quote <a href="'.URL.'/quotations/entry/'.$quotation['_id'].'" target="_blank">#'.$quotation['code'].'</a><br/>';
        }
        echo $html;
        die;
    }
    function fix_order_no(){
        $quotation = $this->opm->select_all(array(
                                           'arr_where'  =>  array (
                                                                '$or'=>array(
                                                                    array('code' => new MongoRegex("/^15-/")),
                                                                ),
                                                                'deleted'=>true
                                                            ),
                                           'arr_field'  => array ('_id','code'),
                                           'arr_order'  => array ('_id' => 1)
                                       ));
        // $begin = $this->opm->get_auto_code('code');
        // echo $begin;die;
        $begin = 0;
        foreach ($quotation as $key => $value){
            echo $begin.'<br>';
            // pr($value);
            $this->opm->collection->update(array('_id' => $value['_id']), array('$set'=> array('code' => 0)), array("multiple" => true));
            $begin++;
        }
        die;
    }
}