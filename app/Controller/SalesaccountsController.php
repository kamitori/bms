<?php
App::uses('AppController', 'Controller');
class SalesaccountsController extends AppController {
    var $modelName = 'Salesaccount';
    var $name = 'Salesaccounts';
    public $helpers = array();
    public $opm; //Option Module
    public function beforeFilter(){
        parent::beforeFilter();
        $this->set_module_before_filter('Salesaccount');
        $this->sub_tab_default = 'general';
    }


    public function rebuild_setting($arr_setting=array()){
        // parent::rebuild_setting($arr_setting);
         $arr_setting = $this->opm->arr_settings;
        if(!$this->check_permission($this->name.'_@_entry_@_edit')){
            $arr_setting = $this->opm->set_lock(array(),'out');
            $this->set('address_lock', '1');
        }
        $params = isset($this->params->params['pass'][0]) ? $this->params->params['pass'][0] : null;
        foreach(array('invoices','receipts','tasks') as $value){
            if($params == $value){
                if($value == 'invoices')
                    $value = 'salesinvoices';
                if(!$this->check_permission($value.'_@_entry_@_view'))
                    unset($arr_setting['relationship'][$params]);
                if(!$this->check_permission($value.'_@_entry_@_add'))
                    unset($arr_setting['relationship'][$params]['block'][$params]['add']);
                if(!$this->check_permission($value.'_@_entry_@_delete'))
                    unset($arr_setting['relationship'][$params]['block'][$params]['delete']);
            }
        }
        $this->opm->arr_settings = $arr_setting;
        $arr_tmp = $this->opm->arr_field_key('cls');
        $arr_link = array();
        if(!empty($arr_tmp))
            foreach($arr_tmp as $key=>$value)
                $arr_link[$value][] = $key;
        $this->set('arr_link',$arr_link);
    }

    // Add action
    public function add() {
        $this->selectModel('Company');
        $arr_contact = $this->Session->read('arr_user');
        $query = $this->Company->select_one(array('system' => true, '_id' => $arr_contact['company_id']));
        $arr_more = $query;
        if(isset($arr_more) && is_array($arr_more) && count($arr_more)>0){
            //lưu các giá trị của company system vào loation
            if(isset($arr_more['name']))
                $arr_tmp['company_name'] = $arr_more['name'];
            if(isset($arr_more['_id']))
                $arr_tmp['company_id'] = $arr_more['_id'];
            if(isset($arr_more['phone']))
                $arr_tmp['phone'] = $arr_more['phone'];
            if(isset($arr_more['fax']))
                $arr_tmp['fax'] = $arr_more['fax'];
            if(isset($arr_more['email']))
                $arr_tmp['email'] = $arr_more['email'];
            if(isset($arr_more['addresses'][0])){
                foreach($arr_more['addresses'][0] as $kk=>$vv){
                    $arr_tmp['shipping_address'][0]['shipping_'.$kk] = $vv;
                }
            }
            //contact
            if(isset($arr_more['contact_default_id'])){
                $this->selectModel('Contact');
                $query = $this->Contact->select_one(array('_id' => new MongoId($arr_more['contact_default_id'])));
                $contact = $query;
                if(isset($contact['first_name']))
                    $arr_tmp['contact_name'] = $contact['first_name'].' ';
                if(isset($contact['last_name']))
                    $arr_tmp['contact_name'] .= $contact['last_name'];
                if(isset($contact['_id']))
                    $arr_tmp['contact_id'] = $contact['_id'];
            }
        }
        $this->opm->arr_default_before_save = $arr_tmp;
        $this->opm->add();
        $ids = $this->opm->mongo_id_after_save;
        $this->Session->write($this->name . 'ViewId', $ids);
        $this->redirect('/' . $this->params->params['controller'] . '/entry');
        die;
    }

    public function set_entry_address($arr_tmp, $arr_set) {

        $address_fset = array('address_1', 'address_2', 'address_3', 'town_city', 'country', 'province_state', 'zip_postcode');
        $address_value = $address_province_id = $address_country_id = $address_province = $address_country = array();
        $address_controller = array('company');
        $address_value['company'] = array('', '', '', '', "CA", '', '');
        $this->set('address_controller', $address_controller); //set
        $address_key = array('company');
        $this->set('address_key', $address_key); //set
        $address_country = $this->country();
        $arr_address_tmp = array();
        if(!isset($arr_tmp['addresses_default_key']))
            $arr_tmp['addresses_default_key'] = 0;
        if(isset($arr_tmp['addresses'][$arr_tmp['addresses_default_key']])){
            foreach($arr_tmp['addresses'][$arr_tmp['addresses_default_key']] as $key=>$value){
                if($key=='deleted') continue;
                $arr_address_tmp['company_'.$key] = $value;
            }
        }

        if(!empty($arr_address_tmp))
            $arr_tmp['company_address'][0] = $arr_address_tmp;
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
        $this->set('address_value', $address_value);
        $address_hidden_field = array('invoice_address');
        $this->set('address_hidden_field', $address_hidden_field); //set

        $address_label[0] = $arr_set['field']['panel_2']['address']['name'];
        $this->set('address_label', $address_label); //set

        $address_conner[0]['top'] = 'hgt fixbor';
        $address_conner[0]['bottom'] = 'fixbor2 jt_ppbot';
        $this->set('address_conner', $address_conner); //set

        $keys = 'company';
        $address_field_name[$keys][0]['name'] = 'address_1';
        $address_field_name[$keys][0]['id']  = ucfirst($keys).'Address1';
        $address_field_name[$keys][1]['name'] = 'address_2';
        $address_field_name[$keys][1]['id']     = ucfirst($keys).'Address2';
        $address_field_name[$keys][2]['name'] = 'address_3';
        $address_field_name[$keys][2]['id']     = ucfirst($keys).'Address3';
        $address_field_name[$keys][3]['name'] = 'town_city';
        $address_field_name[$keys][3]['id']     = ucfirst($keys).'TownCity';
        $address_field_name[$keys][4]['name'] = 'country';
        $address_field_name[$keys][4]['id']     = ucfirst($keys).'Country';
        $address_field_name[$keys][5]['name'] = 'province_state';
        $address_field_name[$keys][5]['id']     = ucfirst($keys).'ProvinceState';
        $address_field_name[$keys][6]['name'] = 'zip_postcode';
        $address_field_name[$keys][6]['id']     = ucfirst($keys).'ZipPostcode';
         $this->set('address_field_name', $address_field_name); //set
        //pr($address_field_name);die;

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

    //Entry - trang chi tiet
    public function entry() {
        $arr_set = $this->opm->arr_settings;
        $arr_tmp = array();
        // Get value id
        $iditem = $this->get_id();
        if ($iditem == '')
            $iditem = $this->get_last_id();

        $this->set('iditem', $iditem);
        $this->selectModel('Company');
        //Load record by id
        if ($iditem != '') {
            $arr_tmp = $this->opm->select_one(array('_id' => new MongoId($iditem)));
            if( isset($arr_tmp['credit_limit']) && strlen($arr_tmp['credit_limit']) > 0 ){
                if($arr_tmp['balance'] < 0){
                    $tmp_balance = $this->opm->format_currency($arr_tmp['balance'])*(-1);
                    $arr_tmp['difference'] = $this->opm->format_currency($arr_tmp['credit_limit'] - $tmp_balance);
                }else
                    $arr_tmp['difference'] = $this->opm->format_currency($arr_tmp['credit_limit'] - $arr_tmp['balance']);
            }
            if( isset($arr_tmp['receipts']) && strlen($arr_tmp['receipts']) > 0 ){
                $arr_tmp['receipts'] = $this->opm->format_currency($arr_tmp['receipts']);
            }

            if( isset($arr_tmp['invoices_credits']) && strlen($arr_tmp['invoices_credits']) > 0 ){
                $arr_tmp['invoices_credits'] = $this->opm->format_currency($arr_tmp['invoices_credits']);
            }

            $arr_tmp['balance'] = (is_numeric($arr_tmp['balance']))?$this->opm->format_currency($arr_tmp['balance']):'';
            $arr_tmp['credit_limit'] = (is_numeric($arr_tmp['credit_limit']))?$this->opm->format_currency($arr_tmp['credit_limit']):'';
            foreach ($arr_set['field'] as $ks => $vls) {
                foreach ($vls as $field => $values) {
                    if (isset($arr_tmp[$field])) {
                        $arr_set['field'][$ks][$field]['default'] = $arr_tmp[$field];
                        if (preg_match("/_date$/", $field) && is_object($arr_tmp[$field]))
                            $arr_set['field'][$ks][$field]['default'] = date('m/d/Y', $arr_tmp[$field]->sec);
                        else if (in_array($field, $arr_set['title_field']))
                            $item_title[$field] = $arr_tmp[$field];
                        else if ($field == 'contact_name' && isset($arr_tmp['contact_last_name'])) {
                            $arr_set['field'][$ks][$field]['default'] = $arr_tmp[$field] . ' ' . $arr_tmp['contact_last_name'];
                            $item_title['contact_name'] = $arr_tmp[$field] . ' ' . $arr_tmp['contact_last_name'];
                        }
                    } /*else if($this->opm->check_field_link($ks,$field)){
                            $field_id = $arr_set['field'][$ks][$field]['id'];
                            if(!isset($arr_set['field'][$ks][$field]['syncname']))
                                $arr_set['field'][$ks][$field]['syncname'] = 'name';
                            $arr_set['field'][$ks][$field]['default'] = $this->get_name($this->ModuleName($arr_set['field'][$ks][$field]['cls']),$arr_tmp[$field_id],$arr_set['field'][$ks][$field]['syncname']);

                    }*/ else if($field == 'name' && is_object($arr_tmp['company_id'])){
                        $company = $this->Company->select_one(array('_id'=>$arr_tmp['company_id']),array('name','email','phone','fax','our_rep','addresses_default_key','addresses'));
                        $this->set_entry_address($company, $arr_set);
                        foreach($company as $company_field=>$company_value){
                            $arr_set['field'][$ks][$company_field]['default'] = $company_value;
                        }
                    }
                }
            }

            $arr_set['field']['panel_1']['mongo_id']['default'] = $iditem;
            $this->Session->write($this->name . 'ViewId', $iditem);

            //BEGIN custom
            if (isset($arr_set['field']['panel_1']['no']['default']))
                $item_title['no'] = $arr_set['field']['panel_1']['no']['default'];
            else
                $item_title['no'] = '1';
            $this->set('item_title', $item_title);

            //END custom
            $this->set('address_lock', '1');
            //END custom
            //show footer info
            $this->show_footer_info($arr_tmp);


            //add, setup field tự tăng
        }else {
            $this->redirect(URL.'/salesaccounts/add');
        }
        $this->set('arr_settings', $arr_set);
        $this->sub_tab_default = 'invoices';
        $this->sub_tab('', $iditem);
        parent::entry();
    }
    //Associated data function
    public function arr_associated_data($field = '', $value = '', $valueid = '') {
        $arr_return = array();
        $arr_return[$field] = $value;
        // ..........more code
        return $arr_return;
    }

    public function entry_search_all(){
        $this->Session->delete('salesaccounts_entry_search_cond');
        $this->redirect('/salesaccounts/lists');
    }
    //Search function
    public function entry_search(){

        if ($this->request->is('ajax')) {

            $post = $this->data['Salesaccount'];
            $cond = array();

            if( strlen($post['no']) > 0 )$cond['no'] = (int)$post['no'];
            if( strlen($post['_id']) > 0 )$cond['company_id'] = new MongoId($post['_id']);
            if( strlen($post['name']) > 0 )$cond['company_name'] = new MongoRegex('/' . trim($post['name']).'/i');
            if( strlen($post['status_id']) > 0 )$cond['account.status_id'] = trim($post['status_id']);
            if( strlen($post['phone']) > 0 )$cond['phone'] = new MongoRegex('/' . trim($post['phone']).'/i');
            if( strlen($post['fax']) > 0 )$cond['fax'] = new MongoRegex('/' . trim($post['fax']).'/i');
            if( strlen($post['email']) > 0 )$cond['email'] = new MongoRegex('/' . trim($post['email']).'/i');
            if( strlen($post['contact_id']) > 0 )$cond['contact_id'] = new Mongoid(trim($post['contact_id']));
            if( strlen($post['direct_dial']) > 0 )$cond['direct_dial'] = new MongoRegex('/' . trim($post['direct_dial']).'/i');

            if( strlen($post['default_address_1']) > 0 )$cond['addresses'] = array('$elemMatch' => array('address_1' => new MongoRegex('/' . trim($post['default_address_1']).'/i'), 'default' => true) );
            if( strlen($post['default_address_2']) > 0 )$cond['addresses'] = array('$elemMatch' => array('address_2' => new MongoRegex('/' . trim($post['default_address_2']).'/i'), 'default' => true) );
            if( strlen($post['default_address_3']) > 0 )$cond['addresses'] = array('$elemMatch' => array('address_3' => new MongoRegex('/' . trim($post['default_address_3']).'/i'), 'default' => true) );
            if( strlen($post['default_town_city']) > 0 )$cond['addresses'] = array('$elemMatch' => array('town_city' => new MongoRegex('/' . trim($post['default_town_city']).'/i'), 'default' => true) );
            if( strlen($post['default_province_state']) > 0 )$cond['addresses'] = array('$elemMatch' => array('province_state' => new MongoRegex('/' . trim($post['default_province_state']).'/i'), 'default' => true) );
            if( strlen($post['default_country']) > 0 )$cond['addresses'] = array('$elemMatch' => array('country' => new MongoRegex('/' . trim($post['default_country']).'/i'), 'default' => true) );

            if( strlen($post['invoices_credits']) > 0 )$cond['invoices_credits'] = new MongoRegex('/' . trim($post['invoices_credits']).'/i');
            if( strlen($post['receipts']) > 0 )$cond['receipts'] = new MongoRegex('/' . trim($post['receipts']).'/i');
            if( strlen($post['balance']) > 0 )$cond['balance'] = new MongoRegex('/' . trim($post['balance']).'/i');
            if( strlen($post['credit_limit']) > 0 )$cond['credit_limit'] = new MongoRegex('/' . trim($post['credit_limit']).'/i');
            if( strlen($post['difference']) > 0 )$cond['difference'] = new MongoRegex('/' . trim($post['difference']).'/i');
            if( strlen($post['payment_terms']) > 0 )$cond['payment_terms'] = new MongoRegex('/' . trim($post['payment_terms']).'/i');
            if( strlen($post['tax_code']) > 0 )$cond['tax_code'] = new MongoRegex('/' . trim($post['tax_code']).'/i');
            if( strlen($post['nominal_code']) > 0 )$cond['nominal_code'] = new MongoRegex('/' . trim($post['nominal_code']).'/i');
            if( strlen($post['usually_pay_by']) > 0 )$cond['usually_pay_by'] = new MongoRegex('/' . trim($post['usually_pay_by']).'/i');
            if( strlen($post['card_type']) > 0 )$cond['card_type'] = new MongoRegex('/' . trim($post['card_type']).'/i');
            if( strlen($post['card_number']) > 0 )$cond['card_number'] = new MongoRegex('/' . trim($post['card_number']).'/i');
            if( strlen($post['expires_month']) > 0 )$cond['expires_month'] = new MongoRegex('/' . trim($post['expires_month']).'/i');
            if( strlen($post['security_id']) > 0 )$cond['security_id'] = trim($post['security_id']);
            if( strlen($post['card_holder_id']) > 0 )$cond['card_holder_id'] = trim($post['card_holder_id']);
            if( strlen($post['address']) > 0 )$cond['address'] = new MongoRegex('/' . trim($post['address']).'/i');
            if( strlen($post['ext_accounts_id']) > 0 )$cond['ext_accounts_id'] = trim($post['ext_accounts_id']);
            $this->selectModel('Salesaccount');
            $this->identity($cond);
            $tmp = $this->Salesaccount->select_one($cond);
            if( $tmp ){
                $this->Session->write('salesaccounts_entry_search_cond', $cond);

                $cond['_id'] = array('$ne' => $tmp['_id']);
                $tmp1 = $this->Salesaccount->select_one($cond);
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

        $this->selectModel('Setting');
        $arr_status = $this->Setting->select_option(array('setting_value' => 'salesaccounts_status'), array('option'));
        $this->set( 'arr_status', $arr_status );
        $arr_usually_pay_by = $this->Setting->select_option(array('setting_value' => 'salesaccounts_usually_pay_by'), array('option'));
        $this->set( 'arr_usually_pay_by', $arr_usually_pay_by );
        $arr_card_type = $this->Setting->select_option(array('setting_value' => 'salesaccounts_card_type'), array('option'));
        $this->set( 'arr_card_type', $arr_card_type );
        $arr_card_holder = $this->Setting->select_option(array('setting_value' => 'salesaccounts_card_holder'), array('option'));
        $this->set( 'arr_card_holder', $arr_card_holder );
        $arr_payment_terms = $this->Setting->select_option(array('setting_value' => 'salesaccounts_payment_terms'), array('option'));
        $this->set( 'arr_payment_terms', $arr_payment_terms );
        $arr_tax_code = $this->Setting->select_option(array('setting_value' => 'salesaccounts_tax_code'), array('option'));
        $this->set( 'arr_tax_code', $arr_tax_code );
        $arr_nominal_code = $this->Setting->select_option(array('setting_value' => 'salesaccounts_nominal_code'), array('option'));
        $this->set( 'arr_nominal_code', $arr_nominal_code );

        $this->set('set_footer', 'footer_search');
        $this->set('address_country', $this->country());
        $this->set('address_province', $this->province("CA"));
    }

    //Swith options function
  public function swith_options($keys='')
    {
        parent::swith_options($keys);
        $id = $this->get_id();
        if($keys=='create_sales_invoice')
        {
            $this->invoices_add('create_from_option',$this->get_id());
        }
        else if($keys=='create_receipt')
        {
            $this->receipt_add('create_from_option',$this->get_id());
        }
        else if($keys=='create_task')
        {
            $this->tasks_add($id,'create_from_option');
        }
        else
         if($keys == 'customers_with_balances'){
            $tmp = $this->get_balance();
            $this->Session->write($this->name . '_where', $tmp);
            echo URL . '/' . $this->params->params['controller'] . '/lists';
        }
        else if($keys == 'customer_with_credit'){
            $tmp = $this->get_credit();
            $this->Session->write($this->name . '_where', $tmp);
            echo URL. '/'.$this->params->params['controller']. '/lists';
        }
        else if($keys == 'customer_credit_limit'){
            $tmp = $this->get_over_limit();
            $this->Session->write($this->name . '_where', $tmp);
            echo URL. '/'.$this->params->params['controller']. '/lists';
        }
        else if($keys == 'customer_overdue_invoices'){
            $current_time = strtotime(date('Y-m-d'));
            $arr_where['payment_due_date'] = array(
                    'operator'  => 'other',
                    'values'        =>  array(
                                'invoice_status'    => array('$in' => array('In progress','Invoiced')),
                                'payment_due_date'  => array('$lt' => new MongoDate($current_time))
                        )

                );
            //$tmp = $this->get_overdue_invoices();
            $this->Session->write($this->name . '_where', $arr_where);
            echo URL. '/'.$this->params->params['controller']. '/lists';
        }
        else if($keys == 'sales_account_totals'){
            echo URL . '/' . $this->params->params['controller'] . '/report_account_totals';
        }
        else if($keys == 'create_statement'){
            echo URL . '/salesaccounts/print_statement_pdf/'.$id;
        }
        else if($keys == 'create_email'){
            echo URL . '/' . $this->params->params['controller'] . '/create_email';
        }
        else if($keys == 'create_letter'){
            echo URL . '/' . $this->params->params['controller'] . '/create_letter';
        }
        else if($keys == 'create_fax'){
            echo URL . '/' . $this->params->params['controller'] . '/create_fax';
        }
        else if($keys == 'print_credit_account_application'){
            echo URL . '/salesaccounts/print_application_pdf/'.$id;
        }
        die;
    }

/*    public function get_overdue_invoices(){
        $this->selectModel('Salesaccount');
        $this->selectModel('Salesinvoice');
        $arr_salesaccount = $this->Salesaccount->select_all(array(
                                                            'arr_where' => array('deleted' => false),
                                                            'arr_field' => array('company_id')
                                                            ));
        $arr_data = array();
        foreach($arr_salesaccount as $arr_sales){
            $arr_invoices = $this->Salesinvoice->select_all(array(
                                                            'arr_where' => array('company_id' => new MongoId($arr_sales['company_id'])),
                                                            'arr_field' => array('payment_terms','payment_due_date')
                                                            ));
            $arr_invoices = iterator_to_array($arr_invoices);
            $current_date = strtotime("m/d/Y");
        }
    }*/

    //Subtab function
    public function invoices($salesaccount_id){
        $subdatas = array();
        $this->selectModel('Salesaccount');
        $this->selectModel('Receipt');
        $arr_acc= $this->Salesaccount->select_one(array('_id'=>new MongoId($salesaccount_id)));
        $this->set('company_id',$arr_acc['company_id']);
            $this->selectModel('Salesinvoice');
            $arr_invoices = $this->Salesinvoice->select_all(array(
                                                           'arr_where'=>array('company_id'=>new MongoId($arr_acc['company_id'])),
                                                           'arr_field'=>array('code','date_modified','sum_sub_total','sum_tax','sum_amount','paid_date','total_receipt','taxval','invoice_status','payment_terms','payment_due_date','our_rep','our_csr')
                                                           ));
        $arr_invoices = iterator_to_array($arr_invoices);
        $total['sum_amount'] = $total['total_receipt'] = $total['balance_invoiced'] = $total['total_tax']  = $total['total_30'] = $total['total_60'] = $total['total_90'] = $total['total_90+'] =  0;
        $current_date = strtotime(date("m/d/Y"));
        foreach($arr_invoices as $key=>$invoice){
            if($invoice['invoice_status'] == 'Cancelled'){
                $arr_invoices[$key]['balance_invoiced'] = $arr_invoices[$key]['sum_amount'] =  $arr_invoices[$key]['total_receipt'] = $arr_invoices[$key]['tax']  = 0;
                $arr_invoices[$key]['paid_date']  = '';
                continue;
            } else if($invoice['invoice_status'] == 'Credit'){
                $arr_invoices[$key]['sum_amount'] *= -1;
                $arr_invoices[$key]['sum_sub_total'] *= -1;
            } else {
                $minimum = $this->get_minimum_order('Salesinvoice',$invoice['_id']);
                if($invoice['sum_sub_total']<$minimum){
                    $more_sub_total = $minimum - (float)$invoice['sum_sub_total'];
                    $sub_total = $more_sub_total;
                    $tax = $sub_total*(float)$invoice['taxval']/100;
                    $amount = $sub_total+$tax;
                    $arr_invoices[$key]['sum_sub_total'] += $sub_total;
                    $arr_invoices[$key]['sum_amount'] += $amount;
                }
            }
            $receipts = $this->Receipt->collection->aggregate(
                        array(
                            '$match'=>array(
                                            'company_id' => new MongoID($arr_acc['company_id']),
                                            'deleted'=> false
                                            ),
                        ),
                        array(
                            '$unwind'=>'$allocation',
                        ),
                         array(
                            '$match'=>array(
                                            'deleted'=> false,
                                            'allocation.salesinvoice_id' => $invoice['_id']
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
            $arr_invoices[$key]['total_receipt'] = $total_receipt;
            $total['total_tax'] += $arr_invoices[$key]['tax'] = $arr_invoices[$key]['sum_amount']-$arr_invoices[$key]['sum_sub_total'];
            $total['sum_amount'] += $arr_invoices[$key]['sum_amount'];
            $total['total_receipt'] += $arr_invoices[$key]['total_receipt'];
            $arr_invoices[$key]['paid_date'] = isset($invoice['paid_date'])&&is_object($invoice['paid_date']) ? $invoice['paid_date'] : '';
            if($invoice['invoice_status'] == 'Credit')
                $arr_invoices[$key]['balance_invoiced'] = $arr_invoices[$key]['sum_amount'] +  $arr_invoices[$key]['total_receipt'];
            else
                $arr_invoices[$key]['balance_invoiced'] = $arr_invoices[$key]['sum_amount'] -  $arr_invoices[$key]['total_receipt'];
            if(!isset($arr_invoices[$key]['balance_invoiced']))
                $arr_invoices[$key]['balance_invoiced'] = 0;
            if(($current_date - strtotime(date("m/d/Y",$invoice['payment_due_date']->sec)))>=0 && ($current_date - strtotime(date("m/d/Y",$invoice['payment_due_date']->sec))) <= 259200){
                $total['total_30'] += $arr_invoices[$key]['balance_invoiced'];
            }else if($current_date - strtotime((date("m/d/Y", $invoice['payment_due_date']->sec))) > 259200 && ($current_date - strtotime(date("m/d/Y", $invoice['payment_due_date']->sec))) < 5184000){
                $total['total_60'] += $arr_invoices[$key]['balance_invoiced'];
            }else if(($current_date - strtotime(date("m/d/Y", $invoice['payment_due_date']->sec))) > 5184000 && ($current_date - strtotime(date("m/d/Y", $invoice['payment_due_date']->sec))) < 7776000){
                $total['total_90'] += $arr_invoices[$key]['balance_invoiced'];
            }else if(($current_date - strtotime(date("m/d/Y", $invoice['payment_due_date']->sec))) > 7776000){
                $total['total_90+'] += $arr_invoices[$key]['balance_invoiced'];
            }else if(($current_date - strtotime(date("m/d/Y", $invoice['payment_due_date']->sec))) < 0){
                $total['total_30'] += $arr_invoices[$key]['balance_invoiced'];
            }
        }

        $total['balance_invoiced']= $total['sum_amount']-  $total['total_receipt'];
        $this->set('total',$total);
        $this->set('arr_invoices',$arr_invoices);
        $subdatas['invoices'] =  $arr_invoices;
        $this->set('subdatas', $subdatas);
    }

    function invoices_add($type=''){
        $this->selectModel('Salesaccount');
        $arr_sa = $this->Salesaccount->select_one(array('_id' => new MongoId($this->get_id())));
        if(isset($arr_sa['company_id'])){
            $this->selectModel('Company');
            $arr_tmp = $this->Company->select_one(array('_id' => $arr_sa['company_id']));
            $arr_save['company_name'] = (isset($arr_tmp['name']) ? $arr_tmp['name'] : '');
            $arr_save['phone'] = isset($arr_tmp['phone'])?$arr_tmp['phone']:'';
            $arr_save['email'] = isset($arr_tmp['email'])?$arr_tmp['email']:'';
            $arr_save['invoices_date'] = new MongoDate(strtotime(date('Y-m-d')));
            $arr_save['invoice_address']['0']['invoice_address_1']= isset($arr_tmp['addresses']['0']['address_1'])?$arr_tmp['addresses']['0']['address_1']:'';
            $arr_save['invoice_address']['0']['invoice_town_city']= isset($arr_tmp['addresses']['0']['town_city'])?$arr_tmp['addresses']['0']['town_city']:'';
            $arr_save['invoice_address']['0']['invoice_zip_postcode']= isset($arr_tmp['addresses']['0']['zip_postcode'])?$arr_tmp['addresses']['0']['zip_postcode']:'';
        }else if($arr_sa['contact_id']){
            $this->selectModel('Contact');
            $arr_tmp = $this->Contact->select_one(array('_id' => $arr_sa['contact_id']));
            $arr_save['contact_name'] = $arr_tmp['first_name'].' '.$arr_tmp['last_name'];
            $arr_save['contact_id'] = $arr_tmp['_id'];
            $arr_save['invoice_address']['0']['invoice_address_1']= isset($arr_tmp['addresses']['0']['address_1'])?$arr_tmp['addresses']['0']['address_1']:'';
            $arr_save['invoice_address']['0']['invoice_town_city']= isset($arr_tmp['addresses']['0']['town_city'])?$arr_tmp['addresses']['0']['town_city']:'';
            $arr_save['invoice_address']['0']['invoice_zip_postcode']= isset($arr_tmp['addresses']['0']['zip_postcode'])?$arr_tmp['addresses']['0']['zip_postcode']:'';
        }
        $arr_save['shipping_address'] = '';
        $arr_save['shipping_town_city'] = '';
        $arr_save['shipping_province_state']='';
        $arr_save['shipping_zip_postcode']='';
        $arr_save['shipping_country']='';
        $arr_save['payment_terms'] = isset($arr_sa['payment_terms'])?$arr_sa['payment_terms']:'';
        $arr_save['tax'] = isset($arr_sa['tax_code'])?$arr_sa['tax_code']:'';
        $arr_save['paid_date']='';
        $arr_save['payment_due_date']='';
        $arr_save['job_name']='';
        $arr_save['name']='';
        $arr_save['salesorder_name']='';
        $arr_save['customer_po_no']='';

        if( isset($arr_tmp['our_rep_id']) && is_object($arr_tmp['our_rep_id']) ){
            $arr_save['our_rep'] = $arr_tmp['our_rep'];
            $arr_save['our_rep_id'] = $arr_tmp['our_rep_id'];
        }
        if( isset($arr_tmp['our_csr_id']) && is_object($arr_tmp['our_csr_id']) ){
            $arr_save['our_csr'] = $arr_tmp['our_csr'];
            $arr_save['our_csr_id'] = $arr_tmp['our_csr_id'];
        }

        $this->selectModel("Salesinvoice");
        $this->Salesinvoice->arr_default_before_save = $arr_save;
        if ( $this->Salesinvoice->add() ){
            $id = $this->Salesinvoice->mongo_id_after_save;
            if($type=='create_from_option')
            {
                echo URL.'/salesinvoices/entry/'.$id;
                die;
            }else
            {
                $this->redirect('/salesinvoices/entry/'.$id);
                die;
            }
        }

        if($type=='create_from_option')
        {
            echo URL.'/salesaccounts/entry/'.$this->get_id();
            die;
        }
        $this->redirect('/salesaccounts/entry/'.$this->get_id());
        die;
    }

    public function receipts($salesaccount_id){
        $this->selectModel('Salesaccount');
        $arr_acc = $this->Salesaccount->select_one(array('_id'=>new MongoId($salesaccount_id)),array('company_id'));
        $this->selectModel('Receipt');
        $receipt = $this->Receipt->select_all(array(
                                              'arr_where' => array('company_id'=>$arr_acc['company_id']),
                                              'arr_order' => array('_id'=>-1),
                                              'arr_field' => array('_id','code','allocation','amount_received','deleted','name','paid_by','notes','receipt_date','unallocated','total_allocated'),
                                              ));
        $sales_account = array();
        if($receipt->count() > 0){
            foreach($receipt as $key => $value){
               $sales_account['receipt'][$key]['id'] = $value['_id'];
                $sales_account['receipt'][$key]['code'] = $value['code'];
                $sales_account['receipt'][$key]['date'] = (isset($value['receipt_date'])?$value['receipt_date']:'');
                $sales_account['receipt'][$key]['amount_received'] = (isset($value['amount_received'])&&$value['amount_received']!='' ? (float)$value['amount_received'] : 0);
                $sales_account['receipt'][$key]['unallocated'] = (isset($value['unallocated'])&&$value['unallocated']!='' ? (float)$value['unallocated'] : 0);
                $sales_account['receipt'][$key]['total_allocated'] = (isset($value['total_allocated'])&&$value['total_allocated']!='' ? (float)$value['total_allocated'] : 0);
                $sales_account['receipt'][$key]['paid_by'] = (isset($value['paid_by']) ? $value['paid_by'] : '');
                $sales_account['receipt'][$key]['reference'] = (isset($value['name'])&&$value['name']!='' ? $value['name'] : '');
                $sales_account['receipt'][$key]['notes'] = (isset($value['notes'])&&$value['notes']!='' ? $value['notes'] : '');
                if(isset($value['allocation']) && !empty($value['allocation'])){
                    foreach($value['allocation'] as $k => $v){
                        if($v['deleted'] == true)continue;
                            $sales_account['receipt_allocation'][$key][$k]['receipt_id'] = $value['_id'];
                            $sales_account['receipt_allocation'][$key][$k]['receipt_code'] = $value['code'];
                            $sales_account['receipt_allocation'][$key][$k]['salesinvoice_id'] = (isset($v['salesinvoice_id'])&&is_object($v['salesinvoice_id']) ? $v['salesinvoice_id'] : '');
                            $sales_account['receipt_allocation'][$key][$k]['salesinvoice_code'] = (isset($v['salesinvoice_code']) ? $v['salesinvoice_code'] : '');
                            $sales_account['receipt_allocation'][$key][$k]['date'] = $sales_account['receipt'][$key]['date'];
                            $sales_account['receipt_allocation'][$key][$k]['note'] = (isset($v['note']) ? $v['note'] : '');
                            $sales_account['receipt_allocation'][$key][$k]['write_off'] = (isset($v['write_off']) ? $v['write_off'] : 0);
                            $sales_account['receipt_allocation'][$key][$k]['amount'] = (isset($v['amount'])&&$v['amount']!='' ? (float)$v['amount'] : 0);
                    }
                }
            }
        }
        $arr_receipt = array();
        if(isset($sales_account['receipt'])){
            foreach($sales_account['receipt'] as $key => $value){
                $arr_receipt[$key] = $value;
            }
        }
        $this->set('arr_receipt',$arr_receipt);
        $arr_allocation = array();
        if(isset($sales_account['receipt_allocation'])){
            foreach($sales_account['receipt_allocation'] as $k_receipt_allocation => $v_receipt_allocation){
                foreach($v_receipt_allocation as $k => $v){
                    $arr_allocation[$k] = $v;
                }
            }
        }
        $this->set('arr_allocation',$arr_allocation);
        $subdatas = array();
        $subdatas['receipts'] = $arr_receipt;
        $subdatas['receipts_allocation'] = $arr_allocation;
        $this->set('subdatas', $subdatas);
        $this->set('salesaccount_id',$salesaccount_id);
    }

    function receipt_add($type='' )
    {
        $this->selectModel('Salesaccount');
        $arr_acc = $this->Salesaccount->select_one(array('_id' => new MongoId($this->get_id())),array('company_id','contact_id'));

        $arr_save = array();
        if( isset($arr_acc['company_id']) ){
            $this->selectModel('Company');
            $arr_tmp = $this->Company->select_one(array('_id' => $arr_acc['company_id']));

            $arr_save['salesaccount_name'] = $arr_save['company_name'] = (isset($arr_tmp['name']) ? $arr_tmp['name'] : '');
            $arr_save['salesaccount_id'] = new MongoId($this->get_id());
            $arr_save['company_id'] = $arr_tmp['_id'];
            if( isset($arr_tmp['our_rep_id']) && is_object($arr_tmp['our_rep_id']) ){
                $arr_save['our_rep'] = $arr_tmp['our_rep'];
                $arr_save['our_rep_id'] = $arr_tmp['our_rep_id'];
            }
            if( isset($arr_tmp['our_csr_id']) && is_object($arr_tmp['our_csr_id']) ){
                $arr_save['our_csr'] = $arr_tmp['our_csr'];
                $arr_save['our_csr_id'] = $arr_tmp['our_csr_id'];
            }
            if( isset($arr_tmp['identity']) ){
                $arr_save['identity'] = $arr_tmp['identity'];
            }

        }elseif(isset($arr_acc['contact_id'])){
            $this->selectModel('Contact');
            $arr_tmp = $this->Contact->select_one(array('_id' => $arr_acc['contact_id']));

            $arr_save['contact_name'] = $arr_tmp['first_name'].' '.$arr_tmp['last_name'];
            $arr_save['contact_id'] = $arr_tmp['_id'];
        }

        $arr_save['amount_received'] = 0;
        $arr_save['description'] = '';

        $arr_save['receipt_date'] = new MongoDate(strtotime(date('Y-m-d')));
        $arr_save['paid_by'] = '';
        $arr_save['our_bank_account'] = '';

        $arr_save['use_own_letterhead'] = 0;
        $arr_save['ext_accounts_sync']  = 0 ;
        $this->selectModel("Receipt");
        $this->Receipt->arr_default_before_save = $arr_save;
        if ( $this->Receipt->add() ){
            $id = $this->Receipt->mongo_id_after_save;
            if($type=='create_from_option')
                echo URL.'/receipts/entry/'.$id;
            else
                 echo URL.'/receipts/entry/'.$id;
            die;
        }
        if($type=='create_from_option')
            echo URL.'/salesaccounts/entry/'.$this->get_id();
        else
            echo URL.'/salesaccounts/entry/'.$this->get_id();
        die;
    }

    public function comms(){
        $subdatas = array();
        $subdatas['stockcurrent1'] = array();
        $this->set('subdatas', $subdatas);
        //goi ham dung chung cua a.Nam
        $module_id = $this->get_id();
        $this->communications($module_id, true);
    }

    function task($salesaccount_id){
        $this->selectModel('Salesaccount');
        $arr_acc = $this->Salesaccount->select_one(array('_id' => new MongoId($salesaccount_id)),array('company_id'));
        $this->set('company_id', $arr_acc['company_id']);
        $this->selectModel('Task');
        $arr_task = $this->Task->select_all(array(
                                            'arr_where' => array('company_id' => $arr_acc['company_id']),
                                            'arr_field' => array('no','name','type','our_rep','work_start','work_end','status'),
                                            'arr_order' => array('work_start' => 1),
                                            ));
        $arr = array();
        foreach($arr_task as $key => $value){
            $arr[$key] = $value;
        }
        $subdatas['task'] = array();
        $subdatas['task'] =  $arr;
        $this->set('subdatas', $subdatas);
    }



    function other(){
        $arr_tmp = array();
        $arr_tmp = $this->Salesaccount->select_one(array('_id' => new MongoId($this->get_id())),array('account_details','trade1','account_app'));

        $subdatas = array();
        $subdatas['detail'] = isset($arr_tmp['account_details'][0])?$arr_tmp['account_details'][0]:'';
        $subdatas['trade1'] = isset($arr_tmp['trade1'][0])?$arr_tmp['trade1'][0]:'';
        $subdatas['trade2'] = isset($arr_tmp['trade1'][1])?$arr_tmp['trade1'][1]:'';
        $subdatas['account_app'] = isset($arr_tmp['account_app'][0])?$arr_tmp['account_app'][0]:'';
        $subdatas['account_app']['approved_date'] = isset($subdatas['account_app']['approved_date']) && is_object($subdatas['account_app']['approved_date']) ? $this->Salesaccount->format_date($subdatas['account_app']['approved_date']->sec):'';
        $this->selectModel('Salesaccount');
        $this->set('subdatas', $subdatas);
    }
    // Popup form orther module
    public function popup($key = "") {
        $this->set('key', $key);

        $limit = 100; $skip = 0;
        $page_num = 1;
        if( isset($_POST['pagination']) && $_POST['pagination']['page-num'] > 0){

            // $limit = $_POST['pagination']['page-list'];
            $page_num = $_POST['pagination']['page-num'];
            $limit = $_POST['pagination']['page-list'];
            $skip = $limit*($page_num - 1);
        }
        $this->set('page_num', $page_num);
        $this->set('limit', $limit);

        $arr_order = array('company_name' => 1);
        if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
            $sort_type = 1;
            if( $_POST['sort']['type'] == 'desc' ){
                $sort_type = -1;
            }
            $arr_order = array($_POST['sort']['field'] => $sort_type);

            $this->set('sort_field', $_POST['sort']['field']);
            $this->set('sort_type', ($sort_type === 1)?'asc':'desc');
            $this->set('sort_type_change', ($sort_type === 1)?'desc':'asc');
        }

        $cond = array();
        $this->identity($cond);
        if (!empty($this->data) && isset($this->data['Salesaccount'])) {
            $arr_post = $this->data['Salesaccount'];

            if (strlen($arr_post['name']) > 0){
                $arr_where['name'] = new MongoRegex('/'. (string)$arr_post['name'] .'/i');
                $this->selectModel('Company');
                $arr_companies = $this->Company->select_all(array(
                                                            'arr_where'=>$arr_where,
                                                            'arr_field'=>array('_id')
                                                            ));
                if($arr_companies->count()){
                    $arr_in = array();
                    foreach($arr_companies as $company)
                        $arr_in[] =  $company['_id'];
                    $cond['company_id']['$in'] = $arr_in;
                }
            }
        }
        $this->selectModel('Salesaccount');
        $arr_salesaccount = $this->Salesaccount->select_all(array(
            'arr_where' => $cond,
            'arr_order' => $arr_order,
            'limit' => $limit,
            'skip' => $skip
        ));
        $this->set('arr_salesaccount', $arr_salesaccount);
        $this->selectModel('Contact');
        $this->set('model_contact', $this->Contact);
        $this->selectModel('Company');
        $this->set('model_company', $this->Company);

        $total_page = $total_record = $total_current = 0;
        if( is_object($arr_salesaccount) ){
            $total_current = $arr_salesaccount->count(true);
            $total_record = $arr_salesaccount->count();
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

     function lists() {

        $this->selectModel('Salesaccount');

        $limit = LIST_LIMIT; $skip = 0;

        // dùng cho sort
        $sort_field = '_id';
        $sort_type = 1;
        if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
            if( $_POST['sort']['type'] == 'desc' ){
                $sort_type = -1;
            }
            $sort_field = $_POST['sort']['field'];
            $this->Session->write('salesaccount_lists_search_sort', array($sort_field, $sort_type));

        }elseif( $this->Session->check('salesaccount_lists_search_sort') ){
            $session_sort = $this->Session->read('salesaccount_lists_search_sort');
            $sort_field = $session_sort[0];
            $sort_type = $session_sort[1];
        }
        $arr_order = array($sort_field => $sort_type);
        $this->set('sort_field', $sort_field);
        $this->set('sort_type', ($sort_type === 1)?'asc':'desc');

        $this->selectModel('Contact');
        $this->set('model_contact', $this->Contact);
        $this->selectModel('Company');
        $this->set('model_company', $this->Company);
        $this->selectModel('Salesinvoice');
        $this->set('model_salesinvoice', $this->Salesinvoice);

        // dùng cho điều kiện
        $cond = array();
        $this->Session->delete('salesaccounts_entry_search_cond');
        $this->Session->delete($this->name . '_where');
        $this->Session->delete($this->name . '_where_popup');

        if( $this->Session->check('salesaccounts_entry_search_cond') ){
            $cond = $this->Session->read('salesaccounts_entry_search_cond');
        }

        // dùng cho phân trang
        $page_num = 1;
        if( isset($_POST['pagination']) && $_POST['pagination']['page-num'] > 0){
            $page_num = $_POST['pagination']['page-num'];
            $limit = $_POST['pagination']['page-list'];
            $skip = $limit*($page_num - 1);
        }

        $this->set('page_num', $page_num);
        $this->set('limit', $limit);

        $cond = array_merge($cond, $this->arr_search_where());
        $arr_salesaccount = $this->Salesaccount->select_all(array(
            'arr_where' => $cond,
            'arr_order' => $arr_order,
            'limit' => $limit,
            'skip' => $skip
        ));
        $this->set('arr_salesaccount', $arr_salesaccount);

        // dùng cho phân trang
        $total_page = $total_record = $total_current = 0;
        if( is_object($arr_salesaccount) ){
            $total_current = $arr_salesaccount->count(true);
            $total_record = $arr_salesaccount->count();
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


    function build_company_name(){
        $this->selectModel('Salesaccount');
        $this->selectModel('Company');
        $arr_salesaccounts = $this->Salesaccount->select_all(array(
                                                             'arr_where' => array(
                                                                                  'company_id' => array('$nin'=>array(null,'')),
                                                                                  ),
                                                             'arr_field' => array('company_id'),
                                                             'limit'    =>9999
                                                             ));
        $i = 0;
        echo 'Total records: '.$arr_salesaccounts->count();
        foreach($arr_salesaccounts as $salesaccount){
            $arr_data = array('_id'=>$salesaccount['_id']);
            if(!isset($salesaccount['company_id']) || !is_object($salesaccount['company_id'])){
                $arr_data['company_name'] = '';
            } else {
                $company = $this->Company->select_one(array('_id'=>$salesaccount['company_id']),array('name'));
                $arr_data['company_name'] = isset($company['name']) ? $company['name'] : '';
            }
            $this->Salesaccount->rebuild_collection($arr_data);
            $i++;
        }
        echo "<br /> $i records rebuild.";
        die;
    }

    function auto_create_receipt(){
        $this->selectModel('Receipt');
        $arr_receipts = $this->Receipt->select_all(array(
                                'arr_where' => array(
                                                     'salesaccount_id' =>array('$nin' => array( '',null )),
                                                     ),
                                'arr_field' => array('salesaccount_id')
                                ));
        $arr_salesaccount_ids = array();
        foreach($arr_receipts as $receipt){
            if(!isset($receipt['salesaccount_id']) || !is_object($receipt['salesaccount_id'])) continue;
            $arr_salesaccount_ids[] = $receipt['salesaccount_id'];
        }
        $this->selectModel('Salesaccount');
        $arr_salesaccounts = $this->Salesaccount->select_all(array(
                                'arr_where' => array(
                                                     '_id' =>array('$nin' => $arr_salesaccount_ids),
                                                     ),
                                'arr_field' => array('company_id','company_name')
                                ));
        $this->selectModel('Company');
        echo $arr_salesaccounts->count().' records found.<br />';
        $i = 0;
        foreach($arr_salesaccounts as $salesaccount){
            $this->Receipt->arr_default_before_save['our_rep_id'] = new MongoId('5276d62d67b96d913d000014');
            $this->Receipt->arr_default_before_save['our_rep'] = 'Hanh Nguyen';
            $company = $this->Company->select_one(array('_id' => $salesaccount['company_id']),array('name'));
            $this->Receipt->arr_default_before_save['salesaccount_name'] = isset($company['name']) ? $company['name'] : '';
            $this->Receipt->arr_default_before_save['company_id'] = $company['_id'];
            $this->Receipt->add('salesaccount_id',$salesaccount['_id']);
            $i++;
        }
        echo $i.' - Done.';
        die;
    }
}