<?php
App::uses('AppController', 'Controller');
class CompaniesController extends AppController {

    var $name = 'Companies';
    var $modelName = 'Company';
    public $helpers = array();
    public $opm; //Option Module
    public function beforeFilter(){
        parent::beforeFilter();
        $this->set_module_before_filter('Company');
        $this->sub_tab_default = 'contacts';
    }

    function ajax_save(){
        if(isset($_POST)){
            $arr_post = $_POST;
            if($arr_post['field'] == 'name'){
                $name = $arr_post['value'];
                $this->selectModel('Salesaccount');
                $salesaccount = $this->Salesaccount->select_one(array('company_id'=> new MongoId($arr_post['ids'])),array('_id'));
                if(isset($salesaccount['_id']) && is_object($salesaccount['_id'])){
                    $this->Salesaccount->save(array('_id'=>$salesaccount['_id'],'company_name'=>$name));
                }
                foreach(array('Quotation','Salesinvoice','Salesorder','Product','Contact','Job','Task') as $model){
                    $this->selectModel($model);
                    $company_name = 'company_name';
                    if($model == 'Contact')
                        $company_name = 'company';
                    $this->$model->collection->update(array('company_id' => new MongoId($arr_post['ids'])), array( '$set' => array($company_name =>  $name) ), array('multiple' => true));
                }
            }else if($arr_post['field'] == 'our_rep' ) {
                if(isset($_POST['value']['password'])){
                    $value = $_POST['value'];
                    $password = $value['password'];
                    $this->selectModel('Stuffs');
                    $change = $this->Stuffs->select_one(array('value' => 'Changing Code'));
                    if(md5($password) != $change['password']){
                        echo 'wrong_pass';
                    } else {
                        echo 'success';
                    }
                    die;
                } else {
                    if(empty($_POST['value'])){
                        $this->opm->save(array('_id' => new MongoId($arr_post['ids']),'our_rep' => '', 'our_rep_id' => ''));
                        die;
                    }
                }
            } else if($arr_post['field'] == 'our_csr' ) {
                if(empty($_POST['value'])){
                    $this->opm->save(array('_id' => new MongoId($arr_post['ids']),'our_csr' => '', 'our_csr_id' => ''));
                    die;
                }
            }
        }
        parent::ajax_save();
    }

    public function rebuild_setting($arr_setting=array()){
        // parent::rebuild_setting($arr_setting);
         $arr_setting = $this->opm->arr_settings;
        if(!$this->check_permission($this->name.'_@_entry_@_edit')){
            $arr_setting = $this->opm->set_lock(array(),'out');
            $this->set('address_lock', '1');
        }
        $this->selectModel('Company');
        $arr_tmp = $this->Company->select_one(array('_id'=>new MongoId($this->get_id())),array('is_supplier','is_customer'));
        if($arr_tmp['is_customer']==1 && $arr_tmp['is_supplier'] != 1){
            unset($arr_setting['relationship']['products']['block'][1]);
            unset($arr_setting['relationship']['products']['block']['keyword']);
            unset($arr_setting['relationship']['orders']['block']['orders_supplier']);
        }else if($arr_tmp['is_supplier'] == 1 && $arr_tmp['is_customer']!=1){
            unset($arr_setting['relationship']['products']['block']['pricing_category']);
            unset($arr_setting['relationship']['products']['block'][4]);
            unset($arr_setting['relationship']['products']['block'][5]);
            unset($arr_setting['relationship']['orders']['block']['orders_cus']);
        }else if($arr_tmp['is_customer'] != 1 && $arr_tmp['is_supplier'] != 1){
            unset($arr_setting['relationship']['products']['block'][1]);
            unset($arr_setting['relationship']['products']['block']['keyword']);
            unset($arr_setting['relationship']['orders']['block']['orders_supplier']);
        }
        if(!$this->check_permission('salesinvoices_@_entry_@_view')){
            unset($arr_setting['relationship']['account']['block']['sales_invoice_this_company'] );
            if(!$this->check_permission('salesaccounts_@_entry_@_view'))
                unset($arr_setting['relationship']['account'] );
        }
        if(!$this->check_permission('salesinvoices_@_entry_@_add')){
            unset($arr_setting['relationship']['account']['block']['sales_invoice_this_company']['add'] );
        }
        if(!$this->check_permission('salesinvoices_@_entry_@_delete')){
            unset($arr_setting['relationship']['account']['block']['sales_invoice_this_company']['field']['delete'] );
        }
        if(!$this->check_permission('jobs_@_entry_@_view')){
            unset($arr_setting['relationship']['jobs'] );
        }
        if(!$this->check_permission('jobs_@_entry_@_add')){
            unset($arr_setting['relationship']['jobs']['block']['jobs']['add'] );
        }
        if(!$this->check_permission('jobs_@_entry_@_delete')){
            unset($arr_setting['relationship']['jobs']['block']['jobs']['field']['delete'] );
        }
        if(!$this->check_permission('tasks_@_entry_@_view')){
            unset($arr_setting['relationship']['tasks'] );
        }
        if(!$this->check_permission('tasks_@_entry_@_add')){
            unset($arr_setting['relationship']['tasks']['block']['tasks']['add'] );
        }
        if(!$this->check_permission('tasks_@_entry_@_delete')){
            unset($arr_setting['relationship']['tasks']['block']['tasks']['field']['delete'] );
        }
        $order = 0;
        if(!$this->check_permission('salesorders_@_entry_@_view')){
            $order++;
            unset($arr_setting['relationship']['orders']['block']['orders_cus'] );
        }
        if(!$this->check_permission('salesorders_@_entry_@_add')){
            unset($arr_setting['relationship']['orders']['block']['orders_cus']['add'] );
        }
        if(!$this->check_permission('salesorders_@_entry_@_delete')){
            unset($arr_setting['relationship']['orders']['block']['orders_cus']['field']['delete'] );
        }
        if(!$this->check_permission('purchaseorders_@_entry_@_view')){
            $order++;
            unset($arr_setting['relationship']['orders']['block']['orders_supplier'] );
        }
        if(!$this->check_permission('purchaseorders_@_entry_@_add')){
            unset($arr_setting['relationship']['orders']['block']['orders_supplier']['add'] );
        }
        if(!$this->check_permission('purchaseorders_@_entry_@_delete')){
            unset($arr_setting['relationship']['orders']['block']['orders_supplier']['field']['delete'] );
        }
        if($order == 2){
            unset($arr_setting['relationship']['orders'] );
        }
        if(!$this->check_permission('shippings_@_entry_@_view')){
            unset($arr_setting['relationship']['shipping'] );
        }
        if(!$this->check_permission('shippings_@_entry_@_add')){
            unset($arr_setting['relationship']['shipping']['block']['shipping']['add'] );
        }
        if(!$this->check_permission('shippings_@_entry_@_delete')){
            unset($arr_setting['relationship']['shipping']['block']['shipping']['field']['delete'] );
        }
        if(!$this->check_permission('quotations_@_entry_@_view')){
            unset($arr_setting['relationship']['quotes'] );
        }
        if(!$this->check_permission('quotations_@_entry_@_add')){
            unset($arr_setting['relationship']['quotes']['block']['quotes']['add'] );
        }
        if(!$this->check_permission('quotations_@_entry_@_delete')){
            unset($arr_setting['relationship']['quotes']['block']['quotes']['field']['delete'] );
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
     public function add($system = '') {
        $this->selectModel('Company');
        if( isset($_SESSION['arr_user']['system_admin']) && $system == 'system' ) {
            $this->opm->arr_default_before_save['system'] = true;
        }
        $ids = $this->opm->add('name', '');
        $newid = explode("||", $ids);
        $this->Session->write($this->name . 'ViewId', $newid[0]);
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
        $arr_tmp['company_address'][0] = array();
        foreach ($address_key as $kss => $vss) {
            //neu ton tai address trong data base
            if (isset($arr_tmp[$vss . '_address'][0])) {
                if(!empty($arr_address_tmp))
                    $arr_tmp[$vss . '_address'][0] = $arr_address_tmp;
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
        $this->set('address_more_line', 3); //set
        $this->set('address_onchange', "save_address_pr('\"+keys+\"');");
        $this->set('address_company_id', 'mongo_id');
        if (isset($arr_tmp['contact_id']) && strlen($arr_tmp['contact_id']) == 24)
            $this->set('address_contact_id', 'contact_id');
        $this->set('address_add', $address_add);
         $this->set('address_class_div_top','tab_1_inner_company');
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

            //BEGIN custom


             if($arr_set['field']['panel_1']['is_supplier']['default'] ==  1)
                $is_supplier = 'checked="checked"';
            else
                $is_supplier = '';

             if($arr_set['field']['panel_1']['is_customer']['default'] ==  1)
                $is_customer = 'checked="checked"';
              else
                $is_customer = '';

            $arr_set['field']['panel_1']['no']['after'] = '
                        <div class="jt_company_checkbox" style="margin-right:2%;">
                                <span class="inactive_fix_entry">Supplier</span>
                                <label class="m_check2">
                                    <input type="checkbox" name="is_supplier" value="1" id="is_supplier" '.$is_supplier.'>                                 <span></span>
                                </label>
                            </div>
                            <div class="jt_company_checkbox" style="width:22%">
                                <span class="inactive_fix_entry">Customer</span>
                                <label class="m_check2">
                                    <input type="checkbox" name="is_customer" value="1" id="is_customer" '.$is_customer.'>                                 <span></span>
                                </label>
                            </div>';

            if (isset($arr_set['field']['panel_1']['no']['default']))
                $item_title['no'] = $arr_set['field']['panel_1']['no']['default'];
            else
                $item_title['no'] = '1';
            $this->set('item_title', $item_title);

            //END custom
            //$this->set('address_lock', '1');
            //END custom
            //show footer info
            $this->show_footer_info($arr_tmp);


            //add, setup field tự tăng
        }else {
            $nextcode = $this->opm->get_auto_code('no');
            $arr_set['field']['panel_1']['no']['default'] = $nextcode;
            $this->set('item_title', array('no' => $nextcode));
        }
        $this->set('arr_settings', $arr_set);
        $this->sub_tab_default = 'contacts';
        $this->sub_tab('', $iditem);
        parent::entry();
        $this->set_entry_address($arr_tmp, $arr_set);
        if (!isset($arr_tmp['addresses_default_key'])) $arr_tmp['addresses_default_key'] = 0;
        $this->set('address_choose',$arr_tmp['addresses_default_key']);
    }

    public function lists() {
        $this->selectModel('Company');
        $limit = LIST_LIMIT;
        $skip = 0;
        $sort_field = '_id';
        $sort_type = 1;
        if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
            if( $_POST['sort']['type'] == 'desc' ){
                $sort_type = -1;
            }
            $sort_field = $_POST['sort']['field'];
            $this->Session->write('Companies_lists_search_sort', array($sort_field, $sort_type));

        }elseif( $this->Session->check('Companies_lists_search_sort') ){
            $session_sort = $this->Session->read('Companies_lists_search_sort');
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
        $arr_companies = $this->Company->select_all(array(
            'arr_where' => $cond,
            'arr_order' => $arr_order,
            'arr_field' => array('no','name','is_customer', 'is_supplier','phone','fax','email','our_rep_id','our_rep','our_csr','our_csr_id'),
            'limit' => $limit,
            'skip' => $skip
        ));
        $this->set('arr_companies', $arr_companies);


        $total_page = $total_record = $total_current = 0;
        if( is_object($arr_companies) ){
            $total_current = $arr_companies->count(true);
            $total_record = $arr_companies->count();
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


    //Associated data function
    public function arr_associated_data($field = '', $value = '', $valueid = '') {
        $arr_return = array();
        $arr_return[$field] = $value;
        // ..........more code
        if($field == 'contact_default_id'){
            $arr_return[$field] = new MongoId($value);
        }
        if($field == 'our_rep'){
            $arr_return['our_rep_id'] = new MongoId($valueid);
        }
        if($field == 'our_csr'){
            $arr_return['our_csr_id'] = new MongoId($valueid);
        } else if($field == 'sell_category') {
            $arr_return['sell_category'] = $arr_return['sell_category_id'] =$value;
        }
        return $arr_return;
    }


    //Search function
    public function entry_search() {

        //parent class
        $arr_set = $this->opm->arr_settings;
        $arr_set['field']['panel_1']['no']['lock'] = '';
        unset($arr_set['field']['panel_1']['is_supplier'], $arr_set['field']['panel_1']['is_customer']);
        $arr_set['field']['panel_1']['no']['after'] = '
                        <div class="jt_company_checkbox" style="margin-right:2%;">
                                <span class="inactive_fix_entry">Supplier</span>
                                <label class="m_check2">
                                    <input type="checkbox" name="is_supplier" value="1" id="is_supplier" >                                 <span></span>
                                </label>
                            </div>
                            <div class="jt_company_checkbox" style="width:22%">
                                <span class="inactive_fix_entry">Customer</span>
                                <label class="m_check2">
                                    <input type="checkbox" name="is_customer" value="1" id="is_customer" >                                 <span></span>
                                </label>
                            </div>';
        $arr_set['field']['panel_1']['our_rep']['not_custom'] = '0';
        $arr_set['field']['panel_1']['our_csr']['not_custom'] = '0';

        $this->set('search_class', 'jt_input_search');
        $this->set('search_class2', 'jt_select_search');
        $this->set('search_flat', 'placeholder="1"');
        $where = array();
        // if ($this->Session->check($this->name . '_where'))
            // $where = $this->Session->read($this->name . '_where');
        if (count($where) > 0) {
            foreach ($arr_set['field'] as $ks => $vls) {
                foreach ($vls as $field => $values) {
                    if (isset($where[$field])) {
                        $arr_set['field'][$ks][$field]['default'] = $where[$field]['values'];
                    }
                }
            }
        }
        //end parent class
        $this->set('arr_settings', $arr_set);
        $this->set('shipping_contact_name','');

        //set address
        $address_label = array('Default address');
        $this->set('address_label', $address_label);
        $address_controller =  $address_key = array('invoice', 'shipping');
        $this->set('address_key', $address_key); //set
        $this->set('address_controller', $address_controller); //set
        $address_conner[0]['top'] = 'hgt fixbor';
        $address_conner[0]['bottom'] = 'fixbor2 jt_ppbot';
        $address_conner[1]['top'] = 'hgt';
        $address_conner[1]['bottom'] = 'fixbor3 jt_ppbot';
        $this->set('address_conner', $address_conner);
        $this->set('address_more_line', 3); //set
        $address_hidden_field = array('invoice_address');
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

    public function find_all(){
        $this->Session->delete('Companies_where');
        $this->redirect('/companies/lists');
    }

    //Swith options function
   public function swith_options($keys){
        parent::swith_options($keys);
        if($keys == 'create_enquiry'){
            echo URL . '/'. $this->params->params['controller'].'/enquiries_add_options/'.$this->get_id();
        }else if($keys == 'create_quotation'){
            echo URL.'/'.$this->params->params['controller'].'/quotes_add/'.$this->get_id();
        }else if($keys == 'create_job'){
            echo URL.'/'.$this->params->params['controller'].'/jobs_add';
        }else if($keys == 'create_task'){
            echo URL.'/'.$this->params->params['controller'].'/tasks_add_options/'.$this->get_id();
        }else if($keys == 'create_sales_order'){
            echo URL.'/'.$this->params->params['controller'].'/orders_add_salesorder/'.$this->get_id();
        }else if($keys == 'create_purchase_order'){
            echo URL.'/'.$this->params->params['controller'].'/orders_add_purchasesorder/'.$this->get_id();
        }else if($keys == 'create_sales_invoice'){
            echo URL.'/'.$this->params->params['controller'].'/salesinvoice_add/'.$this->get_id();
        }else if($keys == 'create_shipping'){
            echo URL.'/'.$this->params->params['controller'].'/shipping_add/'.$this->get_id();
        }else if($keys == 'active_customers'){
            echo URL.'/'.$this->params->params['controller'].'/active_customer';
        }else if($keys=='active_suppliers'){
            $or_where = array(
                'is_customer' => 0,
                'is_supplier' => 1

            );
            $or_where1 = array(
                'is_customer' => 1,
                'is_supplier' => 1

            );
            $cond['$or']=array(
                $or_where
                ,$or_where1
            );

            $this->Session->write('companies_entry_search_cond',$cond);
            echo URL . '/' . $this->params->params['controller'] .'/lists';
        }else if($keys == 'active_customers_that_are_also_suppliers'){
            $or_where = array(
                'is_customer' => 1,
                'is_supplier' => 1

            );
            $this->Session->write('companies_entry_search_cond',$or_where);
            echo URL . '/' . $this->params->params['controller'] .'/lists';
        }else if($keys == 'companies_that_are_not_a_customer_or_supplier'){
            $or_where = array(
                'is_customer' => 0,
                'is_supplier' => 0

            );
            $this->Session->write('companies_entry_search_cond',$or_where);
            echo URL . '/' . $this->params->params['controller'] .'/lists';
        }else if($keys == 'print_mini_list'){
            echo URL.'/'.$this->params->params['controller'].'/view_minilist';
        }else if($keys == 'create_email'){
            echo URL .'/'.$this->params->params['controller']. '/create_email';
        }else if($keys == 'create_fax'){
            echo URL .'/'.$this->params->params['controller']. '/create_fax';
        }else if($keys == 'create_letter'){
            echo URL .'/'.$this->params->params['controller']. '/create_letter';
        }
        die;
   }

    //Subtab function
    public function contacts(){
        $this->selectModel('Contact');
        $arr_contact = $this->Contact->select_all(array(
                                                  'arr_where' => array('company_id'=>new MongoId($this->get_id())),
                                                  'arr_field' => array('title','first_name','last_name','contact_default','position','direct_dial','extension_no','email','inactive','mobile', 'emarketing'),
                                                  'arr_order' => array('last_name' => 1),
                                                  ));

        $this->selectModel('Company');
        $arr_company = $this->Company->select_one(array('_id' => new MongoId($this->get_id())), array('_id', 'contact_default_id', 'name'));

        $arr = array(0=>array());
        foreach($arr_contact as $key => $value){
            if(isset($arr_company['contact_default_id']) && $value['_id']==$arr_company['contact_default_id']){
                $value['contact_default'] = 1;
                $arr[0] = $value;
            }
            else
                $arr[] = $value;
        }
        if(empty($arr[0]))
            unset($arr[0]);
        $subdatas = array();
        $this->set_select_data_list('relationship','contacts');
        $subdatas['contacts'] = $arr;
        $this->set('subdatas', $subdatas);
    }

    function contacts_add($company_id, $company_name = '') {
        $arr_save = array();
        $arr_save['company_id'] = new MongoId($company_id);
        $key = 0;
        if(isset($this->data['Salesaccount']['addresses_default_key']))
        $key = $this->data['Salesaccount']['addresses_default_key'];
        $address = '';
        if(isset($this->data['Salesaccount']['addresses'][$key]['country_id']))

         $address = $this->data['Salesaccount']['addresses'][$key]['country_id'];

        $this->selectModel('Company');
        $key = 0;
        if(isset($this->data['Salesaccount']['addresses_default_key']))
        $key = $this->data['Salesaccount']['addresses_default_key'];
        $address = '';
        if(isset($this->data['Salesaccount']['addresses'][$key]['country_id']))
        $address = $this->data['Salesaccount']['addresses'][$key]['country_id'];
        $arr_company = $this->Company->select_one(array('_id' => $arr_save['company_id']), array('_id', 'name', 'addresses', 'addresses_default_key', 'system', 'fax', 'phone','is_customer', 'identity'));
        if( isset($arr_company['system']) && $arr_company['system'] ){
            $arr_save['is_customer'] = 0;
            $arr_save['is_employee'] = 1;
        } else if(isset($arr_company['is_customer']) && $arr_company['is_customer']){
            $arr_save['is_customer'] = 1;
            $arr_save['is_employee'] = 0;
        }
        $arr_save['identity'] = $arr_company['identity'];
        $arr_save['company'] = $arr_company['name'];
        $arr_save['fax'] = $arr_company['fax'];
        $arr_save['company_phone'] = $arr_company['phone'];
        $arr_save['addresses'] = array(
            array(
                'name' => '',
                'deleted' => false,
                'default' => true,
                'country' => $arr_company['addresses'][$arr_company['addresses_default_key']]['country'],
                'country_id' => $arr_company['addresses'][$arr_company['addresses_default_key']]['country_id'],
                'province_state' => $arr_company['addresses'][$arr_company['addresses_default_key']]['province_state'],
                'province_state_id' => $arr_company['addresses'][$arr_company['addresses_default_key']]['province_state_id'],
                'address_1' => $arr_company['addresses'][$arr_company['addresses_default_key']]['address_1'],
                'address_2' => $arr_company['addresses'][$arr_company['addresses_default_key']]['address_2'],
                'address_3' => $arr_company['addresses'][$arr_company['addresses_default_key']]['address_3'],
                'town_city' => $arr_company['addresses'][$arr_company['addresses_default_key']]['town_city'],
                'zip_postcode' => $arr_company['addresses'][$arr_company['addresses_default_key']]['zip_postcode']
            )
        );
        $arr_save['addresses_default_key'] = 0;

        // Tìm kiếm trước xem company này hiện tại đã có default chưa, nếu chưa thì khi save xong sẽ save vào company này default contact
        $this->selectModel('Contact');
        $arr_contact_default = $this->Contact->select_one(array('company_id' => new MongoId($company_id)), array('_id', 'name'));

        $this->Contact->arr_default_before_save = $arr_save;
        if (!$this->Contact->add()) {
            echo 'Error: ' . $this->Contact->arr_errors_save[1];
            die;
        } else {
            if (!isset($arr_contact_default['_id'])) {
                $arr_save_company = array();
                $arr_save_company['_id'] = new MongoId($company_id);
                $arr_save_company['contact_default_id'] = $this->Contact->mongo_id_after_save;
                $this->selectModel('Company');
                if (!$this->Company->save($arr_save_company)) {
                    echo 'Error: ' . $this->Company->arr_errors_save[1];
                    die;
                }
            }
        }
        die;
    }

    function contacts_auto_save() {
        if (!empty($this->data)) {
            $arr_post_data = $this->data;
            $arr_save = array();
            $arr_save['_id'] = $arr_post_data['contact_id'];
            $arr_save[$arr_post_data['field']] = $arr_post_data['value'];

            if($arr_post_data['field']=='first_name') {
                $arr_save['full_name'] =  $arr_post_data['value'].' '.$arr_post_data['other_name'];
            } else if($arr_post_data['field']=='last_name') {
                $arr_save['full_name'] =  $arr_post_data['other_name'].' '.$arr_post_data['value'];
            } else if ($arr_post_data['field'] == 'mobile') {
                $arr_save['mobile'] = $arr_post_data['value'];
                $arr_save['mobile_login'] = preg_replace( '/[^0-9]/', '', $arr_post_data['value']);
            }

            $this->selectModel('Contact');
            while( $this->Contact->count(array('full_name' => $arr_save['full_name'], '_id' => array('$ne' => $arr_save['_id']))) ){
                $arr_save['full_name'] .= rand(1, 9);
            }
            if ($this->Contact->save($arr_save)) {
                echo 'ok';
            } else {
                echo 'Error: ' . $this->Contact->arr_errors_save[1];
            }
        }
        die;
    }

    function contacts_save_default($company_id, $contact_id) {
        if (!$this->request->is('ajax'))
            exit;
        $arr_save['_id'] = $company_id;
        $arr_save['contact_default_id'] = new MongoId($contact_id);

        $this->selectModel('Company');
        if ($this->Company->save($arr_save)) {
            echo 'ok';
        } else {
            echo 'Error: ' . $this->Company->arr_errors_save[1];
        }
        die;
    }

    function addresses(){
        $this->selectModel('Company');
        $company = $this->Company->select_one(array('_id'=> new MongoId($this->get_id())),array('addresses'));
        $this->selectModel('Province');
        $arr_all_province = array();
        // foreach($arr_company['addresses'] as $key => $value){
        //     $arr_all_province[$key] = $value;
        //     //if($value['deleted'])continue;
        //     //if(isset($arr_all_province[$value['country_id']]))continue;
        //     $province =  $this->Province->select_list(array(
        //                                               'arr_where' => array('country_id'=>$value['country_id']),
        //                                               'arr_field' => array('key','name'),
        //                                               'arr_order' => array('name'=>1)
        //                                               ));
        //     $arr_all_province[$value['country_id']] = $province;
        // }
        $subdatas = array();
        if(!isset($company['addresses']))
            $company['addresses'] = array();
        krsort($company['addresses']);
        $subdatas['addresses'] = $company['addresses'];
        $this->set_select_data_list('relationship','addresses');
        $this->set('subdatas', $subdatas);
        $arr_countries = $this->country();
        $arr_provines = array('' => array('' => ''));
        foreach($arr_countries as $countryKey => $country) {
            $arr_provines[$countryKey] = $this->province($countryKey);
        }
        $this->set('arr_provines', $arr_provines);
        $this->set('arr_countries', $arr_countries);
    }

    function addresses_add($company_id) {
        $this->selectModel('Company');
        $this->Company->collection->update(
                array('_id' => new MongoId($company_id)), array('$push' => array(
                'addresses' => array(
                    'name' => '',
                    'default' => false,
                    'address_1' => '',
                    'address_2' => '',
                    'address_3' => '',
                    'town_city' => '',
                    'zip_postcode' => '',
                    'province_state' => '',
                    'province_state_id' => '',
                    'country' => 'Canada',
                    'country_id' => 'CA',
                    'deleted' => false
                )))
        );
        $this->addresses($company_id);
        $this->render('addresses');
    }

    function enquiries($company_id){
        $this->selectModel('Enquiry');
        $arr_enquiries = $this->Enquiry->select_all(array(
                                                    'arr_where' => array('company_id'=>new MongoId($company_id)),
                                                    'arr_order' => array('no'=> -1),
                                                    'arr_field' => array('no','date_modified','status','contact_name','contact_id','our_rep','our_rep_id','referred','enquiry_value','detail')
                                                    ));
        $subdatas = array();
        $subdatas['enquiries'] = $arr_enquiries;
        $this->set('subdatas', $subdatas);
    }

    function enquiries_add($company_id) {
        $this->selectModel('Company');
        $arr_company = $this->Company->select_one(array('_id' => new MongoId($company_id)));
        $this->selectModel('Enquiry');
        $arr_tmp = $this->Enquiry->select_one(array(), array(), array('no' => -1));
        $arr_save = array();
        $arr_save = $this->arr_associated_data('company_name',$arr_company['name'], $company_id);
        $arr_save['no'] = 1;
        if (isset($arr_tmp['no'])) {
            $arr_save['no'] = $arr_tmp['no'] + 1;
        }

        $key = 0;
        if(isset($this->data['Company']['addresses_default_key']))
            $key = $this->data['Company']['addresses_default_key'];
        $address = '';
        if(isset($this->data['Company']['addresses'][$key]['country_id']))
            $address = $this->data['Company']['addresses'][$key]['country_id'];

        $key = isset($arr_company['addresses_default_key'])?$arr_company['addresses_default_key']:0;
        $arr_save['company_id'] = $arr_company['_id'];
        $arr_save['date']= new MongoDate(time());
        $arr_save['status']='Hot';
        $arr_save['default_country'] = isset($arr_company['addresses'][$key]['country'])?$arr_company['addresses'][$key]['country']:'';
        $arr_save['default_country_id'] = isset($arr_company['addresses'][$key]['country_id'])?$arr_company['addresses'][$key]['country_id']:0;
        $arr_save['default_province_state'] = isset($arr_company['addresses'][$key]['province_state'])?$arr_company['addresses'][$key]['province_state']:'';
        $arr_save['default_province_state_id'] = isset($arr_company['addresses'][$key]['province_state_id'])?$arr_company['addresses'][$key]['province_state_id']:'';
        $arr_save['default_address_1'] = isset($arr_company['addresses'][$key]['address_1'])?$arr_company['addresses'][$key]['address_1']:'';
        $arr_save['default_address_2'] = isset($arr_company['addresses'][$key]['address_2'])?$arr_company['addresses'][$key]['address_2']:'';
        $arr_save['default_address_3'] = isset($arr_company['addresses'][$key]['address_3'])?$arr_company['addresses'][$key]['address_3']:'';
        $arr_save['default_town_city'] = isset($arr_company['addresses'][$key]['town_city'])?$arr_company['addresses'][$key]['town_city']:'';
        $arr_save['default_zip_postcode'] = isset($arr_company['addresses'][$key]['zip_postcode'])?$arr_company['addresses'][$key]['zip_postcode']:'';
        $arr_save['company_phone'] = isset($arr_company['phone'])?$arr_company['phone']:'';
        $arr_save['company_fax'] = isset($arr_company['fax'])?$arr_company['fax']:'';
        $arr_save['company_email'] = isset($arr_company['email'])?$arr_company['email']:'';
        $arr_save['web'] = isset($arr_company['web'])?$arr_company['web']:'';
        $arr_save['company'] = isset($arr_company['name'])?$arr_company['name']:'';

        $this->Enquiry->arr_default_before_save = $arr_save;
        if ($this->Enquiry->add())
           echo URL.'/enquiries/entry/' . $this->Enquiry->mongo_id_after_save;
        else
            echo URL.'/enquiries/entry';
        die;
    }

    function enquiries_add_options($company_id) {
        $this->selectModel('Company');
        $arr_company = $this->Company->select_one(array('_id' => new MongoId($company_id)));
        $this->selectModel('Enquiry');
        $arr_tmp = $this->Enquiry->select_one(array(), array(), array('no' => -1));
        $arr_save = array();
        $arr_save = $this->arr_associated_data('company_name',$arr_company['name'], $company_id);
        $arr_save['no'] = 1;
        if (isset($arr_tmp['no'])) {
            $arr_save['no'] = $arr_tmp['no'] + 1;
        }

        $key = 0;
        if(isset($this->data['Company']['addresses_default_key']))
            $key = $this->data['Company']['addresses_default_key'];
        $address = '';
        if(isset($this->data['Company']['addresses'][$key]['country_id']))
            $address = $this->data['Company']['addresses'][$key]['country_id'];


        //$key = isset($arr_company['addresses_default_key'])?$arr_company['addresses_default_key']:0;
        $key = $arr_company['addresses_default_key'];
        $arr_save['date']= new MongoDate(time());
        $arr_save['status']='Hot';
        $arr_save['default_country'] = isset($arr_company['addresses'][$key]['country'])?$arr_company['addresses'][$key]['country']:'';
        $arr_save['default_country_id'] = isset($arr_company['addresses'][$key]['country_id'])?$arr_company['addresses'][$key]['country_id']:0;
        $arr_save['default_province_state'] = isset($arr_company['addresses'][$key]['province_state'])?$arr_company['addresses'][$key]['province_state']:'';
        $arr_save['default_province_state_id'] = isset($arr_company['addresses'][$key]['province_state_id'])?$arr_company['addresses'][$key]['province_state_id']:'';
        $arr_save['default_address_1'] = isset($arr_company['addresses'][$key]['address_1'])?$arr_company['addresses'][$key]['address_1']:'';
        $arr_save['default_address_2'] = isset($arr_company['addresses'][$key]['address_2'])?$arr_company['addresses'][$key]['address_2']:'';
        $arr_save['default_address_3'] = isset($arr_company['addresses'][$key]['address_3'])?$arr_company['addresses'][$key]['address_3']:'';
        $arr_save['default_town_city'] = isset($arr_company['addresses'][$key]['town_city'])?$arr_company['addresses'][$key]['town_city']:'';
        $arr_save['default_zip_postcode'] = isset($arr_company['addresses'][$key]['zip_postcode'])?$arr_company['addresses'][$key]['zip_postcode']:'';
        $arr_save['company_phone'] = isset($arr_company['phone'])?$arr_company['phone']:'';
        $arr_save['company_fax'] = isset($arr_company['fax'])?$arr_company['fax']:'';
        $arr_save['company_email'] = isset($arr_company['email'])?$arr_company['email']:'';
        $arr_save['web'] = isset($arr_company['web'])?$arr_company['web']:'';
        $arr_save['company'] = isset($arr_company['name'])?$arr_company['name']:'';


        $this->Enquiry->arr_default_before_save = $arr_save;
        if ($this->Enquiry->add())
            $this->redirect('/enquiries/entry/' . $this->Enquiry->mongo_id_after_save);
        $this->redirect('/enquiries/entry');
    }

    function jobs($company_id){
        $this->selectModel('Job');
        $arr_job = $this->Job->select_all(array(
                                          'arr_where'=>array('company_id'=>new MongoId($company_id)),
                                          'arr_field' => array('no','name','type','work_start','work_end','status','contact_name'),
                                          'arr_order' => array('work_start' => -1),
                                          ));
        $arr_tmp = array();
        $this->selectModel('Company');
        $arr_company = $this->Company->select_one(array('_id'=> new MongoId($company_id)),array('markup_rate','rate_per_hour'));
        $subdatas = array();
        $subdatas['jobs'] = $arr_job;
        $subdatas['default'] = $arr_company;
        $this->set('subdatas', $subdatas);
    }

    function jobs_add(){
        $company_id = $this->get_id();
        //if(IS_LOCAL){
            if(!isset($_POST['password']) ){
                $this->selectModel('Receipt');
                $this->selectModel('Salesinvoice');
                $current_time = strtotime(date('Y-m-d'));
                $this->selectModel('Salesaccount');
                $salesaccount =$this->Salesaccount->select_one(array('company_id' => new MongoId($company_id)),array('payment_terms'));
                $payment_terms_salesaccount = isset($salesaccount['payment_terms']) ? $salesaccount['payment_terms'] : 0;
                $obj_salesinvoices = $this->Salesinvoice->select_all(array(
                                    'arr_where'=>array(
                                                        'company_id' => new MongoID($company_id),
                                                        'invoice_status' => 'Invoiced',
                                                        //'invoice_status' => array('$nin' => array('Cancelled','Paid','In Progress')),
                                                        'payment_due_date' => array('$lt' => new MongoDate($current_time -  $payment_terms_salesaccount*DAY))
                                                       ),
                                    'arr_field'=>array('payment_due_date','invoice_date','sum_amount','total_receipt','invoice_status'),
                            ));
                if($obj_salesinvoices->count()){
                    $total_balance = 0;
                    foreach($obj_salesinvoices as $key => $value){
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
                    // if($total_balance)
                    if (($total_balance) && ($company_id != "5271dab4222aad6819000ed0")) { // Khong kiem tra Cong ty Anvy - Le Quan Ha
                        echo 'not_add||'.$payment_terms_salesaccount;
                        die;
                    }
                }
            } else {
                $this->selectModel('Stuffs');
                $change = $this->Stuffs->select_one(array('value' => 'Changing Code'));
                if(md5($_POST['password']) != $change['password']){
                    echo 'Your password is wrong.';
                    die;
                }
            }
        //}

        $this->selectModel('Company');
        $arr_company = $this->Company->select_one(array('_id' => new MongoId($company_id)));
        $this->selectModel('Job');
        $arr_tmp = $this->Job->select_one(
                                array(
                                    'no' => array(
                                        '$nin' => array(new MongoRegex('/-/'))
                                    )
                                ),
                                array('no'),
                                array('no' => -1));
        $arr_save = array();
        $arr_save['no'] = 1;
        if (isset($arr_tmp['no'])) {
            $arr_save['no'] = $arr_tmp['no'] + 1;
        }

        $arr_save['company_id'] = $arr_company['_id'];
        $arr_save['status_id'] = 0;
        $arr_save['name'] = '';
        $arr_save['contacts_default_key'] = 0;
        $arr_save['contacts'][] = array(
            "contact_name" => $_SESSION['arr_user']['contact_name'],
            "contact_id" => $_SESSION['arr_user']['contact_id'],
            "default" => true,
            "deleted" => false
        );
        $arr_save['type'] = '';
        $arr_save['status'] = 'New';


        $arr_save['company_name'] = isset($arr_company['name'])?$arr_company['name']:'';
        $arr_save['company_id'] = $arr_company['_id'];
        $arr_save['company_phone'] = isset($arr_company['phone'])?$arr_company['phone']:'';
        $arr_save['fax'] = isset($arr_company['fax'])?$arr_company['fax']:'';
        $arr_save['email']=isset($arr_company['email'])?$arr_company['email']:'';

        if (isset($arr_company['contact_default_id'])) {
            $this->selectModel('Contact');
            $arr_contact = $this->Contact->select_one(array('_id' => $arr_company['contact_default_id']));
            $arr_save['contact_id'] = $arr_contact['_id'];
            $arr_save['contact_name'] = $arr_contact['first_name'] . ' ' . $arr_contact['last_name'];

            if(isset($arr_contact['email'])&&$arr_contact['email']!='')
                $arr_save['email'] = $arr_contact['email'];

            if(isset($arr_contact['direct_dial'])&&$arr_contact['direct_dial']!='')
                $arr_save['direct_phone'] = $arr_contact['direct_dial'];

            if(isset($arr_contact['mobile'])&&$arr_contact['mobile']!='')
                $arr_save['mobile'] = $arr_contact['mobile'];
        }
        else{
            $this->selectModel('Contact');
            $arr_contact = $this->Contact->select_all(array(
                'arr_where' => array('company_id'=>new MongoId($company_id)),
                'arr_order' => array('_id'=>-1),
            ));
            $arrtemp = iterator_to_array($arr_contact);
            if(count($arrtemp)>0){
                $arr_contact = current($arrtemp);
            }else
                $arr_contact = array();
            if(isset($arr_contact['_id'])){
                $arr_save['contact_id'] = $arr_contact['_id'];
                $arr_save['contact_name'] = $arr_contact['first_name'] . ' ' . $arr_contact['last_name'];
            }

            if(isset($arr_contact['email'])&&$arr_contact['email']!='')
                $arr_save['email'] = $arr_contact['email'];


            if(isset($arr_contact['direct_dial'])&&$arr_contact['direct_dial']!='')
                $arr_save['direct_phone'] = $arr_contact['direct_dial'];

            if(isset($arr_contact['mobile'])&&$arr_contact['mobile']!='')
                $arr_save['mobile'] = $arr_contact['mobile'];
        }
        if (isset($work_start_sec) && $work_start_sec > 0) {
            $arr_save['work_end'] = $arr_save['work_start'] = new MongoDate($work_start_sec);
        } else {
            $arr_save['work_end'] = $arr_save['work_start'] = new MongoDate(strtotime(date('Y-m-d H:00:00')) + 3600);
        }

        if(isset($arr_company['addresses_default_key'])){
            $key = $arr_company['addresses_default_key'];
            foreach($arr_company['addresses'][$key] as $k=>$value)
                $arr_save['invoice_'.$k] = $value;
        }

        $this->Job->arr_default_before_save = $arr_save;
        if($this->request->is('ajax')){
        if ($this->Job->add()) {
             echo URL .'/jobs/entry/'. $this->Job->mongo_id_after_save;
        }else
                echo URL . '/jobs/entry';
        }else {
            if ($this->Job->add())
                $this->redirect('/jobs/entry/'. $this->Job->mongo_id_after_save);
            $this->redirect('/jobs/entry');
        }
        die;
    }

    function jobs_delete($id) {
        $arr_save['_id'] = new MongoId($id);
        $arr_save['deleted'] = true;
        $error = 0;
        if (!$error) {
            $this->selectModel('Job');
            if ($this->Job->save($arr_save)) {
                echo 'ok';
            } else {
                echo 'Error: ' . $this->Job->arr_errors_save[1];
            }
        }
        die;
    }


    function tasks($company_id){
        $this->selectModel('Task');
        $arr_task = $this->Task->select_all(array(
                                            'arr_where' => array('company_id' => new MongoId($company_id)),
                                            'arr_order' => array('work_start' => -1)
                                            ));
        $subdatas = array();
        $subdatas['tasks'] = $arr_task;
        $this->set('subdatas', $subdatas);
    }

    function tasks_delete($id) {
        $arr_save['_id'] = new MongoId($id);
        $arr_save['deleted'] = true;
        $error = 0;
        if (!$error) {
            $this->selectModel('Task');
            if ($this->Task->save($arr_save)) {
                echo 'ok';
            } else {
                echo 'Error: ' . $this->Task->arr_errors_save[1];
            }
        }
        die;
    }

    function enquiries_delete($id) {
        $arr_save['_id'] = new MongoId($id);
        $arr_save['deleted'] = true;
        $error = 0;
        if (!$error) {
            $this->selectModel('Enquiry');
            if ($this->Enquiry->save($arr_save)) {
                echo 'ok';
            } else {
                echo 'Error: ' . $this->Enquiry->arr_errors_save[1];
            }
        }
        die;
    }

    function tasks_add($company_id) {
        $this->selectModel('Company');
        $arr_company = $this->Company->select_one(array('_id' => new MongoId($company_id)));
        $arr_save = array();
        if (isset($arr_company['our_rep_id']) && $arr_company['our_rep_id'] != '' && $arr_company['our_rep_id']!=null) {
            $arr_save['our_rep_id'] = $arr_company['our_rep_id'];
            $arr_save['our_rep'] = $arr_company['our_rep'];
        }
        $arr_save['company_name'] = $arr_company['name'];
        $arr_save['company_id'] = $arr_company['_id'];
        if (isset($arr_company['contact_default_id'])) {
            $this->selectModel('Contact');
            $arr_contact = $this->Contact->select_one(array('_id' => $arr_company['contact_default_id']));
            $arr_save['contact_id'] = $arr_contact['_id'];
            $arr_save['contact_name'] = $arr_contact['first_name'] . ' ' . $arr_contact['last_name'];
        }

        $this->selectModel('Task');
        $this->Task->arr_default_before_save = $arr_save;
        if ($this->Task->add())
            echo URL.'/tasks/entry/' . $this->Task->mongo_id_after_save;
        else
            echo URL .'/tasks/entry';
        die;
    }


    function tasks_add_options($company_id) {
        $this->selectModel('Company');
        $arr_company = $this->Company->select_one(array('_id' => new MongoId($company_id)));
        $arr_save = array();
        if (isset($arr_company['our_rep_id']) && $arr_company['our_rep_id'] != '' && $arr_company['our_rep_id']!=null) {
            $arr_save['our_rep_id'] = $arr_company['our_rep_id'];
            $arr_save['our_rep'] = $arr_company['our_rep'];
        }
        $arr_save['company_name'] = $arr_company['name'];
        $arr_save['company_id'] = $arr_company['_id'];
        if (isset($arr_company['contact_default_id'])) {
            $this->selectModel('Contact');
            $arr_contact = $this->Contact->select_one(array('_id' => $arr_company['contact_default_id']));
            $arr_save['contact_id'] = $arr_contact['_id'];
            $arr_save['contact_name'] = $arr_contact['first_name'] . ' ' . $arr_contact['last_name'];
        }

        $this->selectModel('Task');
        $this->Task->arr_default_before_save = $arr_save;
        if ($this->Task->add())
            $this->redirect('/tasks/entry/' . $this->Task->mongo_id_after_save);
        $this->redirect('/tasks/entry');
    }

    public function comms(){
        $subdatas = array();
        $subdatas['stockcurrent1'] = array();
        $this->set('subdatas', $subdatas);
        //goi ham dung chung cua a.Nam
        $module_id = $this->get_id();
        $this->communications($module_id, true);
    }

    function quotes($company_id){
        $this->selectModel('Quotation');
        $arr_quote = $this->Quotation->select_all(array(
                                                'arr_where' => array('company_id'=>new MongoId($company_id)),
                                                'arr_order' => array('quotation_date' => -1),
                                                'arr_field' => array('code','quotation_type','quotation_date','payment_due_date','quotation_status','our_rep_id','our_rep','our_csr','our_csr_id','sum_sub_total','heading')
                                                ));
        $subdatas = array();
        $subdatas['quotes'] = $arr_quote;
        $this->set('subdatas', $subdatas);
    }

     function quotes_delete($id) {
        $arr_save['_id'] = new MongoId($id);
        $arr_save['deleted'] = true;
        $error = 0;
        if (!$error) {
            $this->selectModel('Quotation');
            if ($this->Quotation->save($arr_save)) {
                echo 'ok';
            } else {
                echo 'Error: ' . $this->Quotation->arr_errors_save[1];
            }
        }
        die;
    }

    function quotes_add() {
        $company_id = $this->get_id();
        $this->selectModel('Company');
        $arr_company = $this->Company->select_one(array('_id' => new MongoId($company_id)),array('name','email','phone','contact_default_id','addresses_default_key','addresses','our_csr_id','our_csr','our_rep_id','our_rep'));
        $arr_save = $this->get_company_info($arr_company);
        $this->selectModel('Quotation');
        $arr_save['code'] = $this->Quotation->get_auto_code('code');
        $arr_save['name'] = $arr_save['code'].' - '.$arr_save['company_name'];
        $arr_save['quotation_status'] = 'In progress';
        $arr_save['quotation_date'] = new MongoDate(strtotime(date('Y-m-d')));
        $arr_save['payment_due_date'] = $arr_save['quotation_date'];
        //tax
        $this->selectModel('Salesaccount');
        $arr_acc = $this->Salesaccount->select_one(array('company_id' => $arr_save['company_id']));
        if(isset($arr_acc['tax_code_id'])&& $arr_acc['tax_code_id']!=''){
            $keytax = $arr_acc['tax_code_id'];
            $this->selectModel('Tax');
            $arr_tax = $this->Tax->tax_list();
            $arr_tax_text = $this->Tax->tax_select_list();
            $arr_save['tax'] = $keytax;
            $arr_save['taxval'] = (float)$arr_tax[$keytax];
            $arr_save['taxtext'] = $arr_tax_text[$keytax];
        }
        //end tax
        $this->Quotation->arr_default_before_save = $arr_save;
        if($this->request->is('ajax')){
            if ($this->Quotation->add()) {
                 echo URL .'/quotations/entry/'. $this->Quotation->mongo_id_after_save;
            }else
                echo URL . '/quotations/entry';
        }else {
            if ($this->Quotation->add())
                $this->redirect('/quotations/entry/'. $this->Quotation->mongo_id_after_save);
            $this->redirect('/quotations/entry');
        }
        die;
    }

    //chua hoan thanh
    function orders(){
        $company_id = $this->get_id();
        $this->selectModel('Company');
        $arr_company = $this->Company->select_one(array('_id'=>new MongoId($company_id)),array('_id','is_customer','is_supplier'));
        $arr_salesorder = $arr_purchaseorder = array();
        // if($arr_company['is_customer']){
            $this->selectModel('Salesorder');
            $arr_salesorder = $this->Salesorder->select_all(array(
                                                            'arr_where'=>array('company_id'=>new MongoId($company_id)),
                                                            'arr_field'=>array('code','sales_order_type','salesorder_date','payment_due_date','status','our_rep','our_csr','sum_sub_total','sum_amount','heading'),
                                                            'arr_order'=>array('salesorder_date'=>-1)
                                                            ));
        // }
            $arr_salesorder = iterator_to_array($arr_salesorder);
            foreach($arr_salesorder as $key => $value){
                if($value['status']=='Cancelled'){
                    $arr_salesorder[$key]['sum_sub_total'] = $arr_salesorder[$key]['sum_amount'] = 0;
                    continue;

                }
            }
            $total_purchaseorder = 0;
        if($arr_company['is_supplier']){
            $this->selectModel('Purchaseorder');
            $arr_purchaseorder = $this->Purchaseorder->select_all(array(
                                                            'arr_where'=> array('company_id'=>new MongoId($company_id)),
                                                            'arr_field'=>array('code','required_date','purchord_date','purchase_orders_status','our_csr','our_rep','sum_sub_total','name','delivery_date','ship_to_contact_name'),
                                                            'arr_order'=>array('purchord_date'=>-1)
                                                            ));
            foreach($arr_purchaseorder as $order) {
                if( $order['purchase_orders_status'] == 'Cancelled' ) continue;
                $total_purchaseorder += $order['sum_sub_total'];
            }
        }

        $subdatas = array();
        $subdatas['orders_cus'] =   $arr_salesorder;
        $subdatas['orders_supplier'] = $arr_purchaseorder;
        $this->set('subdatas', $subdatas);
        $this->set('total_purchaseorder', $total_purchaseorder);
    }

    function orders_delete_saleorder($id) {
        $arr_save['_id'] = new MongoId($id);
        $arr_save['deleted'] = true;
        $error = 0;
        if (!$error) {
            $this->selectModel('Salesorder');
            if ($this->Salesorder->save($arr_save)) {
                echo 'ok';
            } else {
                echo 'Error: ' . $this->Salesorder->arr_errors_save[1];
            }
        }
        die;
    }

    function orders_delete_pur_order($id) {
        $arr_save['_id'] = new MongoId($id);
        $arr_save['deleted'] = true;
        $error = 0;
        if (!$error) {
            $this->selectModel('Purchaseorder');
            if ($this->Purchaseorder->save($arr_save)) {
                echo 'ok';
            } else {
                echo 'Error: ' . $this->Purchaseorder->arr_errors_save[1];
            }
        }
        die;
    }

    function orders_add_salesorder() {
        $company_id = $this->get_id();
        $this->selectModel('Company');
        $arr_company = $this->Company->select_one(array('_id' => new MongoId($company_id)),array('name','email','phone','contact_default_id','addresses_default_key','addresses','our_csr_id','our_csr','our_rep_id','our_rep'));
        $arr_save = $this->get_company_info($arr_company);
        $this->selectModel('Salesorder');
        $arr_save['code'] = $this->Salesorder->get_auto_code('code');
        $arr_save['name'] = $arr_save['code'].' - '.$arr_save['company_name'];
        $arr_save['status'] = 'New';
        $arr_save['salesorder_date'] = new MongoDate(strtotime(date('Y-m-d')));
        $arr_save['payment_due_date'] = $arr_save['salesorder_date'];
        $this->Salesorder->arr_default_before_save = $arr_save;
        if($this->request->is('ajax')){
            if ($this->Salesorder->add()) {
                 echo URL .'/salesorders/entry/'. $this->Salesorder->mongo_id_after_save;
            }else
                echo URL . '/salesorders/entry';
        }else {
            if ($this->Salesorder->add())
                $this->redirect('/salesorders/entry/'. $this->Salesorder->mongo_id_after_save);
            $this->redirect('/salesorders/entry');
        }
        die;
    }

    function orders_add_purchasesorder($company_id){
        $this->selectModel('Company');
        $arr_company = $this->Company->select_one(array('_id' => new MongoId($company_id)));

        $arr_save = array();
        $arr_save = $this->arr_associated_data('company_name',$arr_company['name'], $company_id);
        $this->selectModel('Purchaseorder');
        $arr_save['code'] = $this->Purchaseorder->get_auto_code('code');
        $arr_save['company_id'] = $arr_company['_id'];

        $arr_save['purchase_orders_status'] = 'In progress';
        $arr_save['purchord_date'] = new MongoDate();
        $arr_save['required_date'] = new MongoDate(15 * DAY + (int) time());
        $arr_save['delivery_date'] = '';



        $arr_save['ship_to_contact_id'] = $this->Company->user_id();
        $arr_save['ship_to_contact_name'] = $this->Company->user_name();
        $arr_save = $this->get_company_info($arr_company);


        $collection_company = $this->Company->select_one(array('system' => true));
        if ($collection_company != null) {
            $arr_save['ship_to_company_id'] = $collection_company['_id'];
            $arr_save['ship_to_company_name'] = $collection_company['name'];

            $arr_temp = array();

            foreach ($collection_company['addresses'][$collection_company['addresses_default_key']] as $key => $value) {
                if ($key == 'deleted')
                    $arr_temp[$key] = $value;
                else
                    $arr_temp['shipping_' . $key] = $value;
            }

            $object_child[0] = (object) $arr_temp;
            $object_parent = (object) $object_child;
            $arr_save['shipping_address'] = $object_parent;
        }


        $this->Purchaseorder->arr_default_before_save = $arr_save;

        // BaoNam: sửa ngày 02/12/2013
       if($this->request->is('ajax')){
            if ($this->Purchaseorder->add()) {
                 echo URL .'/purchaseorders/entry/'. $this->Purchaseorder->mongo_id_after_save;
            }else
                echo URL . '/purchaseorders/entry';
        }else {
            if ($this->Purchaseorder->add())
                $this->redirect('/purchaseorders/entry/'. $this->Purchaseorder->mongo_id_after_save);
            $this->redirect('/purchaseorders/entry');
        }
        die;
    }

    function shipping(){
        $company_id = $this->get_id();
        $this->selectModel('Shipping');
        $arr_shipping = $this->Shipping->select_all(array(
                                                    'arr_where' => array('company_id'=>new MongoId($company_id)),
                                                    'arr_field' => array('code','shipping_type','return_status','shipping_date','shipping_status','our_rep','shipper','tracking_no'),
                                                    'arr_order'  => array('shipping_date' => -1)
                                                    ));

        $subdatas = array();
        $subdatas['shipping'] = $arr_shipping;
        $this->set('subdatas', $subdatas);
    }

    function shipping_delete($id) {
        $arr_save['_id'] = new MongoId($id);
        $arr_save['deleted'] = true;
        $error = 0;
        if (!$error) {
            $this->selectModel('Shipping');
            if ($this->Shipping->save($arr_save)) {
                echo 'ok';
            } else {
                echo 'Error: ' . $this->Shipping->arr_errors_save[1];
            }
        }
        die;
    }

    function shipping_add($company_id,$type='') {
        $this->selectModel('Shipping');
        $arr_tmp = $this->Shipping->select_one(array(), array(), array('code' => -1));
        $arr_save = array();
        $arr_save['code'] = 1;
        if (isset($arr_tmp['code'])) {
            $arr_save['code'] = $arr_tmp['code'] + 1;
        }
        $this->selectModel('Company');
        $arr_company = $this->Company->select_one(array('_id' => new MongoId($company_id)));
        $arr_save['company_name'] = isset($arr_company['name'])?$arr_company['name']:'';
        $arr_save['company_id'] = $arr_company['_id'];
        if(isset($arr_company['our_csr_id']) && $arr_company['our_csr_id']!=''){
            $arr_save['our_csr'] = isset($arr_company['our_csr'])?$arr_company['our_csr']:'';
            $arr_save['our_csr_id'] = new MongoId($arr_company['our_csr_id']);
        }

        if(isset($arr_company['our_rep_id']) && $arr_company['our_rep_id']!=''){
            $arr_save['our_rep'] = isset($arr_company['our_rep'])?$arr_company['our_rep']:'';
            $arr_save['our_rep_id'] = new MongoId($arr_company['our_rep_id']);
        }

        if($type=='Incoming')
            $arr_save['shipping_type'] = 'In';
        elseif($type=='Outgoing')
            $arr_save['shipping_type'] = 'Out';

        if($arr_company['is_customer']==0&&$arr_company['is_supplier']==1)
            $arr_save['shipping_type'] = 'In';
        elseif($arr_company['is_customer']==1&&$arr_company['is_supplier']==0)
            $arr_save['shipping_type'] = 'Out';



        $arr_save['email'] = isset($arr_company['email'])?$arr_company['email']:'';
        $arr_save['phone'] = isset($arr_company['phone'])?$arr_company['phone']:'';

        if (isset($arr_company['contact_default_id'])) {
            $this->selectModel('Contact');
            $arr_contact = $this->Contact->select_one(array('_id' => $arr_company['contact_default_id']));
            $arr_save['contact_id'] = $arr_contact['_id'];
            $arr_save['contact_name'] = $arr_contact['first_name'] . ' ' . $arr_contact['last_name'];
            if(isset($arr_contact['email']))
                $arr_save['email'] = $arr_contact['email'];
            if(isset($arr_contact['direct_dial']))
                $arr_save['phone'] = $arr_contact['direct_dial'];
        }
        else
        {
            $this->selectModel('Contact');
            $arr_contact = $this->Contact->select_all(array(
                'arr_where' => array('company_id'=>new MongoId($company_id)),
                'arr_order' => array('_id'=>-1),
            ));
            $arrtemp = iterator_to_array($arr_contact);
            if(count($arrtemp)>0){
                $arr_contact = current($arrtemp);
            }else
                $arr_contact = array();
            if(isset($arr_contact['_id'])){
                $arr_save['contact_id'] = $arr_contact['_id'];
                $arr_save['contact_name'] = $arr_contact['first_name'] . ' ' . $arr_contact['last_name'];
            }

            if(isset($arr_contact['email']))
                $arr_save['email'] = $arr_contact['email'];
            if(isset($arr_contact['direct_dial']))
                $arr_save['phone'] = $arr_contact['direct_dial'];


        }
        $arr_invoice_addrress=array();
        foreach($arr_company['addresses'][$arr_company['addresses_default_key']] as $key=>$value){
            if($key=='deleted')
                $arr_invoice_addrress[$key]=$value;
            else
                $arr_invoice_addrress['invoice_'.$key]=$value;

        }
        $arr_save['invoice_address'][0] = (object)$arr_invoice_addrress;

        $this->Shipping->arr_default_before_save = $arr_save;

        if($this->request->is('ajax')){
            if ($this->Shipping->add()) {
                 echo URL .'/shippings/entry/'. $this->Shipping->mongo_id_after_save;
            }else
                echo URL . '/shippings/entry';
        }else {
            if ($this->Shipping->add())
                $this->redirect('/shippings/entry/'. $this->Shipping->mongo_id_after_save);
            $this->redirect('/shippings/entry');
        }
        die;
    }

    function rfqs($company_id){
        $subdatas = array();
        $subdatas['rfqs'] = array();
        $this->set('subdatas', $subdatas);
    }

    function products($company_id){
        if(empty($this->data) || !isset($this->data['Company'])){
            $this->selectModel('Company');
            $arr_company = $this->Company->select_one(array('_id'=>new MongoId($company_id)));
            $tmp['Company'] = $arr_company;
            $this->data = $tmp;
        }else{
            $arr_company = $this->data['Company'];
        }
        $arr_pricing = array();
        if(!empty($this->data['Company']['pricing']))
        foreach($this->data['Company']['pricing'] as $k => $v){
            if(isset($v['deleted']) && $v['deleted']) continue;
            $arr_pricing[$k] = $v;
            $arr_pricing[$k]['product_id'] = (string)$v['product_id'];

            if(isset($v['price_break']) && isset($v['price_break'])){
                foreach($v['price_break'] as $kk => $vv){
                    if(isset($vv['deleted']) && $vv['deleted']) continue;
                    $arr_pricing[$k]['range'] = $vv['range_from'].'-'.$vv['range_to'];
                    $arr_pricing[$k]['unit_price'] = $vv['unit_price'];
                    break;
                }
            }
        }
        //echo 'arr_pricing:';pr($arr_pricing);
        $arr_tmp = array();
        if($arr_company['is_supplier']==1){
            $this->selectModel('Product');
            $arr_product_supplier=$this->Product->select_all(array(
                                                             'arr_where'=>array("company_id"=>new MongoId($company_id)),
                                                             ));
            foreach($arr_product_supplier as $key => $value){
                $arr_tmp[$key] = $value;
            }
        }

        if($arr_company['is_customer']==1){
            $arr_product_customer=array();
        }

        $subdatas = array();
        $subdatas['1'] = $arr_tmp;
        $subdatas['keyword'] = $this->get_option_data('keyword');
        $subdatas['pricing_category'] = $arr_company;
        $subdatas['4'] = $arr_pricing;
        $subdatas['5'] = array();
        $arr_options_custom =  $this->set_select_data_list('relationship','products');
        $this->set('arr_options_custom', $arr_options_custom);
        $this->set('subdatas', $subdatas);
    }

    function pricing_add(){
        $id = $this->get_id();
        $company = $this->opm->select_one(array('_id' => new MongoId($id)),array('pricing'));
        $this->selectModel('Product');
        $product = $this->Product->select_one(array('_id' => new MongoId($_POST['product_id'])),array('code','sku','name'));
        $company['pricing'][] = array(
                                    'deleted' => false,
                                    'code' => $product['code'],
                                    'sku' => $product['sku'],
                                    'name' => $product['name'],
                                    'product_id' => $product['_id'],
                                    'notes'=>'',
                                    'price_break' => array(),
                                      );
        $this->opm->save($company);
        //move the pointer to the end element of array pricing
        end($company['pricing']);
        echo URL.'/companies/products_pricing/'.(key($company['pricing']));
        die;
    }

    function products_pricing( $key ){
        $company_id = $this->get_id();
        $this->selectModel('Company');
        $company = $this->Company->select_one(array('_id' => new MongoId($company_id)),array('pricing','name'));
        if(!isset($company['pricing'][$key]))
            $this->redirect('/companies/entry');
        $arr_product = $company['pricing'][$key];
        $this->set('company_id', $company_id);
        $this->set('key', $key);

        // hiển thị thông tin pricing cho người dùng chọn
        $this->set('company', $company);
        $this->set('arr_product', $arr_product);
        $this->set('return_mod', 1);
        $this->set('return_link', URL.'/companies/entry/'.$company_id);
        $this->set('return_title', 'Pricing: ');
    }

    function products_price_break_add($key){
        $company_id = $this->get_id();
        $this->set('company_id', $company_id);
        $this->set('key', $key);
        $this->selectModel('Company');
        $arr_save = $this->Company->select_one(array('_id' => new MongoId($company_id)),array('pricing'));
        $arr_save['pricing'][$key]['price_break'][] = array(
            'deleted' => false,
            'range_from' => '',
            'range_to' => '',
            'unit_price' => ''
        );
        if ($this->Company->save($arr_save)) {
            $this->set('arr_product', $arr_save['pricing'][$key]);
            $this->render('products_price_break');
        } else {
            echo 'Error: ' . $this->Company->arr_errors_save[1]; die;
        }
    }
    function product_pricebreak_save( $key, $price_break_key){
        $company_id = $this->get_id();
        if( !empty( $_POST ) ){
            $this->selectModel('Company');
            $arr_save = $this->Company->select_one(array('_id' => new MongoId($company_id)),array('pricing'));

            $field = str_replace(array('data[', ']'), '', $_POST['field']);

            $value = $_POST['value'];

            if( $field == 'unit_price' ){
               $value = (float)$value;
            }else{
               $value = (int)$value;
            }

            $arr_save['pricing'][$key]['price_break'][$price_break_key][$field] = $value;
            $this->selectModel('Company');
            if ($this->Company->save($arr_save)) {
                if( strpos($arr_save['pricing'][$key]['name'], 'Minimum Order') !== false ) {
                    Cache::delete('arr_companies');
                }
                echo 'ok';
            } else {
                echo 'Error: ' . $this->Company->arr_errors_save[1];
            }
        }
        die;
    }

    function products_save_notes($company_id, $key){
        if( !empty( $_POST ) ){
            $this->selectModel('Company');
            $arr_save = $this->Company->select_one(array('_id' => new MongoId($company_id)));
            $arr_save['pricing'][$key]['notes'] = $_POST['notes'];
            $this->selectModel('Company');
            if ($this->Company->save($arr_save)) {
                echo 'ok';
            } else {
                echo 'Error: ' . $this->Company->arr_errors_save[1];
            }
        }
        die;
    }

    function products_pricebreak_delete( $key, $price_break_key){
        $company_id = $this->get_id();
        $this->selectModel('Company');
        $arr_save = $this->Company->select_one(array('_id' => new MongoId($company_id)),array('pricing'));
        unset($arr_save['pricing'][$key]['price_break'][$price_break_key]);
        if ($this->Company->save($arr_save)) {
            echo 'ok';
        } else {
            echo 'Error: ' . $this->Company->arr_errors_save[1];
        }
        die;
    }

    function other(){
        $company_id = $this->get_id();
        $this->selectModel('Company');
        $arr_company = array();
        $arr_company = $this->Company->select_one(array('_id'=>new MongoID($company_id)),array('other','include_in_phone_book','profile_type','category','rating','no_of_staff','speed_dial','generate_serials','phone_sort_by','email_so_completed','postal_mail_only','business_type','industry','size','tracking_url','is_shipper'));
        //if(!empty($arr_company['other'])) continue;
        $arr_tmp = array();
        if(!empty($arr_company['other']))
        foreach($arr_company['other'] as $key => $value){
            $arr_tmp[$key] = $value;
        }
        $arr_options_custom = $this->set_select_data_list('relationship', 'other');
        $this->set('arr_options_custom', $arr_options_custom);
        $subdatas = array();
        $subdatas['other_detail'] = $arr_tmp;
        $subdatas['2'] = array();
        $subdatas['profile'] = $arr_company;
        $this->set('subdatas', $subdatas);
    }

    function other_add($company_id) {
        $this->selectModel('Company');
        $this->Company->collection->update(
                array('_id' => new MongoId($company_id)), array(
                    '$push' => array(
                        'other' => array(
                            'heading' => '',
                            'details' => '',
                            'deleted' => false
                        )
                    )
                )
        );
        $this->other($company_id);
        $this->render('other');
    }
    function salesinvoice_add($company_id){
        $company_id = $this->get_id();
        $this->selectModel('Company');
        $arr_company = $this->Company->select_one(array('_id' => new MongoId($company_id)),array('name','email','phone','contact_default_id','addresses_default_key','addresses','our_csr_id','our_csr','our_rep_id','our_rep'));
        $arr_save = $this->get_company_info($arr_company);
        $this->selectModel('Salesinvoice');
        $arr_save['code'] = $this->Salesinvoice->get_auto_code('code');
        $arr_save['name'] = $arr_save['code'].' - '.$arr_save['company_name'];
        $arr_save['status'] = 'In Progress';
        $arr_save['invoice_date'] = new MongoDate(strtotime(date('Y-m-d')));
        $arr_save['payment_due_date'] = new MongoDate($arr_save['invoice_date']->sec + $arr_save['payment_terms']*DAY);
        $this->Salesinvoice->arr_default_before_save = $arr_save;
        if($this->request->is('ajax')){
            if ($this->Salesinvoice->add()) {
                 echo URL .'/salesinvoices/entry/'. $this->Salesinvoice->mongo_id_after_save;
            }else
                echo URL . '/salesinvoices/entry';
        }else {
            if ($this->Salesinvoice->add())
                $this->redirect('/salesinvoices/entry/'. $this->Salesinvoice->mongo_id_after_save);
            $this->redirect('/salesinvoices/entry');
        }
        die;
    }

    function account($company_id){
        $this->set('company_id', $company_id);
        $this->selectModel('Salesinvoice');
        $this->selectModel('Receipt');
        $obj_salesinvoices = $this->Salesinvoice->select_all(array(
                            'arr_where'=> array(
                                                'company_id' => new MongoID($company_id),
                                                ),
                            'arr_field'=>array('code','customer_po_no','invoice_date','invoice_status','our_rep','other_comment','sum_amount','total_receipt','payment_due_date','payment_terms'),
                            'arr_order'=>array('invoice_date'=>-1)
                    ));
        $arr_salesinvoices = array();
        $total_balance = 0;
        $current_time = (int)strtotime(date('Y-m-d'));
        foreach($obj_salesinvoices as $key => $value){
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
            if( $value['invoice_status'] == 'Cancelled' ) {
                $value['sum_amount'] = 0;
                $total_receipt = 0;
            } else if($value['invoice_status'] == 'Credit' && $value['sum_amount'] > 0)
                $value['sum_amount'] = (float)$value['sum_amount'] * -1;
            if($total_receipt<0&& $value['sum_amount'] > 0)
                $value['sum_amount'] = (float)$value['sum_amount'] * -1;
            $value['total_receipt']= $total_receipt;
            $value['balance'] = $value['sum_amount'] - (float)$value['total_receipt'];

            // Ha added temporarily to set it up, 14-Oct-2014
            // Sometimes, after invoiced, the customers can pay us not by receipts but by online transfer, in that case there will be no receipt,
            // only that accountant will turn it on as Paid, in that case, balanace is still 0
            //
            if (($value['invoice_status'] == 'Paid') && ($value['total_receipt'] == 0)) {
                $value['balance'] = 0;
            }

            $total_balance += $value['balance'];

            //if(IS_LOCAL){
                if($value['balance'] > 0){
                    $payment_due_date =  strtotime(date('Y-m-d',$value['payment_due_date']->sec));
                    if($current_time - (int)$payment_due_date > 90*DAY && in_array($value['invoice_status'], array('Invoiced','In Progress')))
                        $value['xcss'] = 'background-color: rgb(247, 215, 134)';
                }
            //}
            $arr_salesinvoices[$key] = $value;
        }

        $this->set_select_data_list('relationship','account');
        $subdatas = array();
        $subdatas['sales_invoice_this_company'] = $arr_salesinvoices;
        $subdatas['account_related'] = array();
        $this->set('subdatas', $subdatas);
        $this->set('button_account', 'add'); //add,view

        $this->selectModel('Setting');
        $this->set( 'arr_status', $this->Setting->select_option(array('setting_value' => 'salesaccounts_status'), array('option')) );
        $this->set( 'arr_payment_terms', $this->Setting->select_option(array('setting_value' => 'salesaccounts_payment_terms'), array('option')) );

        $this->selectModel('Tax');
        $this->set( 'arr_tax_code', $this->Tax->tax_select_list());
        $this->set( 'arr_nominal_code', $this->Setting->select_option(array('setting_value' => 'salesaccounts_nominal_code'), array('option')) );

        $this->selectModel('Salesaccount');
        $salesaccount = $this->Salesaccount->select_one(array('company_id' => new MongoId($company_id)));

        if( isset($salesaccount['_id']) ){
            $arr_tmp['Salesaccount'] = $salesaccount;
            $arr_tmp['Salesaccount']['difference'] = 0;
            $arr_tmp['Salesaccount']['balance'] = $this->opm->format_currency($total_balance);//(is_numeric($arr_tmp['Salesaccount']['balance']))?$this->opm->format_currency($arr_tmp['Salesaccount']['balance']):'';

            if( strlen($arr_tmp['Salesaccount']['credit_limit']) > 0 ){
                if($arr_tmp['Salesaccount']['balance'] < 0){
                    $tmp_balance = $this->opm->format_currency($arr_tmp['Salesaccount']['balance'])*(-1);
                    $arr_tmp['Salesaccount']['difference'] = $this->opm->format_currency($arr_tmp['Salesaccount']['credit_limit'] - $tmp_balance);
                }else
                    $arr_tmp['Salesaccount']['difference'] = $this->opm->format_currency($arr_tmp['Salesaccount']['credit_limit'] - $arr_tmp['Salesaccount']['balance']);
            }
            if( strlen($arr_tmp['Salesaccount']['quotation_limit']) > 0 ){
                $arr_tmp['Salesaccount']['quotation_limit'] = $this->opm->format_currency($arr_tmp['Salesaccount']['quotation_limit']);
            }

            $arr_tmp['Salesaccount']['credit_limit'] = (is_numeric($arr_tmp['Salesaccount']['credit_limit']))?$this->opm->format_currency($arr_tmp['Salesaccount']['credit_limit']):'';
            $this->data = $arr_tmp;
        }
        $this->selectModel('Contact');
        $this->set('model_contact', $this->Contact);
    }

    function account_create($company_id){
        $this->selectModel('Salesaccount');
        $arr_tmp['company_id'] = new MongoId($company_id);
        $this->Salesaccount->arr_default_before_save = $arr_tmp;
        $salesaccount = $this->Salesaccount->add();
        $salesaccount['balance'] = $this->opm->format_currency($salesaccount['balance']);
        $salesaccount['quotation_limit'] = $this->opm->format_currency($salesaccount['quotation_limit']);
        $tmp['Salesaccount'] = $salesaccount;
        $this->set( 'arr_payment_terms', $this->Setting->select_option(array('setting_value' => 'salesaccounts_payment_terms'), array('option')) );
        $this->set( 'arr_status', $this->Setting->select_option(array('setting_value' => 'salesaccounts_status'), array('option')) );
        $this->set( 'arr_tax_code', $this->Tax->tax_select_list());
        $this->set( 'arr_nominal_code', $this->Setting->select_option(array('setting_value' => 'salesaccounts_nominal_code'), array('option')) );

        $this->data = $tmp;
    }

    function account_auto_save($company_id){
        if (!empty($this->data)) {
            $this->selectModel('Salesaccount');
            $arr_save = $this->Salesaccount->select_one(array('company_id' => new MongoId($company_id)));
            foreach ($this->data['Salesaccount'] as $key => $value) {
                if( $key == 'balance' )continue;
                $arr_save[$key] = $value;
                if( $key == 'credit_limit' || $key == 'quotation_limit' ){
                    $value = (float)str_replace(',', '', $value);
                    $arr_save[$key] = $value;
                }
            }
            if ($this->Salesaccount->save($arr_save)) {
                echo 'ok';
                die;
                // cập nhật lại toàn bộ khung html------------Account Related-----------
                $arr_tmp = $arr_save;
                $arr_tmp['Salesaccount'] = $arr_save;
                if( strlen($arr_tmp['Salesaccount']['credit_limit']) > 0 ){
                    $arr_tmp['Salesaccount']['difference'] = $this->opm->format_currency($arr_tmp['Salesaccount']['credit_limit'] - $arr_tmp['Salesaccount']['balance']);
                }
                $arr_tmp['Salesaccount']['balance'] = (is_numeric($arr_tmp['Salesaccount']['balance']))?$this->opm->format_currency($arr_tmp['Salesaccount']['balance']):'';
                $arr_tmp['Salesaccount']['credit_limit'] = (is_numeric($arr_tmp['Salesaccount']['credit_limit']))?$this->opm->format_currency($arr_tmp['Salesaccount']['credit_limit']):'';
                //An them truong quotation limit
                $arr_tmp['Salesaccount']['quotation_limit'] = (is_numeric($arr_tmp['Salesaccount']['quotation_limit']))?$this->opm->format_currency($arr_tmp['Salesaccount']['quotation_limit']):'';
                $this->data = $arr_tmp;
                $this->selectModel('Setting');
                $this->set( 'arr_status', $this->Setting->select_option(array('setting_value' => 'salesaccounts_status'), array('option')) );
                $this->set( 'arr_payment_terms', $this->Setting->select_option(array('setting_value' => 'salesaccounts_payment_terms'), array('option')) );
                $this->selectModel('Tax');
                $this->set( 'arr_tax_code', $this->Tax->tax_select_list());
                $this->set( 'arr_nominal_code', $this->Setting->select_option(array('setting_value' => 'salesaccounts_nominal_code'), array('option')) );
                echo $this->render('account_related'); die;
                // end cập nhật ---------------------------------------------------

            } else {
                echo 'Error: ' . $this->Salesaccount->arr_errors_save[1];
            }
        }
        die;
    }

    function invoice_delete(){
        if(isset($_POST['id']) && strlen($_POST['id']) == 24){
            $this->selectModel('Salesinvoice');
            $this->Salesinvoice->save(array('_id'=> new MongoId($_POST['id']), 'deleted' => true));
            echo 'ok';
        }
        die;
    }

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

        $arr_order = array('no' => 1);
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
        $cond['inactive'] = 0;
        if (!empty($this->data)) {
            $arr_post = $this->data['Company'];

            if (strlen($arr_post['name']) > 0)
                $cond['name'] = new MongoRegex('/'. (string)$arr_post['name'] .'/i');

            if( isset($arr_post['inactive']) && $arr_post['inactive'] )
                $cond['inactive'] = 1;

            if ( isset($arr_post['is_customer']) &&is_numeric($arr_post['is_customer']) && $arr_post['is_customer']) {
                $cond['is_customer'] = 1;
            }

            if ( isset($arr_post['is_supplier']) &&is_numeric($arr_post['is_supplier']) && $arr_post['is_supplier']) {
                $cond['is_supplier'] = 1;
            }

            if (isset($arr_post['is_shipper'])) {
                $cond['is_shipper'] = 1;
                $this->set( 'is_shipper', 1 );
            }


        }

        if (!empty($_GET)) {

            $tmp = $this->data;

            if (isset($_GET['is_customer'])) {
                $cond['is_customer'] = 1;
                $tmp['Company']['is_customer'] = 1;
            }

            if (isset($_GET['is_supplier'])) {
                $cond['is_supplier'] = 1;
                $tmp['Company']['is_supplier'] = 1;
            }

            if (isset($_GET['name'])) {

                $cond['name'] = new MongoRegex('/'. $_GET['name'] .'/i');
                $tmp['Company']['name'] = $_GET['name'];
            }

            if (isset($_GET['is_shipper']) && is_numeric($_GET['is_shipper']) && $_GET['is_shipper']) {
                $cond['is_shipper'] = 1;
                $this->set( 'is_shipper', 1 );
            }

            $this->data = $tmp;
        }
        unset($_GET['_']);
        $cache_key = md5(serialize($_GET));
        $no_cache = true;
        if(empty($_POST) && !isset($_GET['is_shipper']) ){
            $arr_companies = Cache::read('popup_company_'.$cache_key);
            if($arr_companies)
                $no_cache = false;
        }
        $this->selectModel('Company');
        if($no_cache){
            $arr_companies = $this->Company->select_all(array(
                'arr_where' => $cond,
                'arr_order' => $arr_order,
                'arr_field' => array('name', 'addresses', 'addresses_default_key', 'is_customer', 'is_supplier', 'default_address_1', 'default_address_2', 'default_address_3', 'default_town_city', 'default_country_name', 'default_country', 'default_province_state_name', 'default_province_state', 'default_zip_postcode', 'phone', 'fax', 'email', 'web','our_rep','our_rep_id'),
                'limit' => $limit,
                'skip' => $skip
            ));
            if(empty($_POST))
                Cache::write('popup_company_'.$cache_key,iterator_to_array($arr_companies));
        }
        $this->set('arr_company', $arr_companies);
        $total_page = $total_record = $total_current = 0;
        if( is_object($arr_companies) ){
            $total_current = $arr_companies->count(true);
            $total_record = $arr_companies->count();
            if( $total_record%$limit != 0 ){
                $total_page = floor($total_record/$limit) + 1;
            }else{
                $total_page = $total_record/$limit;
            }
        } else if(is_array($arr_companies)){
            $total_current = count($arr_companies);
            $total_record = $this->Company->count($cond);
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

    public function popup_addresses($key = "") {

        $this->set('key', $key);

        if (!empty($_GET)) {
            if (isset($_GET['company_id']) && $_GET['company_id']!='') {
                $cond = array();
                $cond['_id'] = new MongoId($_GET['company_id']);
                $this->selectModel('Company');
                $this->set('arr_company', $this->Company->select_one($cond, array('_id', 'addresses')));
            }
        }

        if (!empty($_GET)) {
            if (isset($_GET['contact_id']) && $_GET['contact_id']!='') {
                $cond = array();
                $cond['_id'] = new MongoId($_GET['contact_id']);
                $this->selectModel('Contact');
                $this->set('arr_contact', $this->Contact->select_one($cond, array('_id', 'addresses')));
            }
        }

        $this->layout = 'ajax';
    }
    // Popup form orther module

    public function view_minilist(){
        $arr_data = array();
        if(!isset($_GET['print_pdf'])){
            $arr_where = array();
            if($this->Session->check('companies_entry_search_cond'))
                $arr_where = $this->Session->read('companies_entry_search_cond');
            $this->selectModel('Company');
            $companies = $this->Company->select_all(array(
                                       'arr_where'  =>  $arr_where,
                                       'arr_field'  =>  array('is_customer','is_supplier','no','name','contact_default_id','phone','email'),
                                       'arr_order'  =>  array('_id'=>1),
                                       ));
            if($companies->count()>0){
                $group = array();
                $html = '';
                $i = 0;
                $this->selectModel('Contact');
                $arr_companies = array();
                foreach($companies as $company){
                    $type = '';
                    $contact = '';
                    if(isset($company['is_customer'])&&$company['is_customer']==1)
                        $type ='Customer ';
                    if(isset($company['is_supplier'])&&$company['is_supplier']==1)
                        $type .='Supllier';
                    if(isset($company['contact_default_id'])&&is_object($company['contact_default_id'])){
                        $contact = $this->Contact->select_one(array('_id'=>$company['contact_default_id']),array('full_name','phone','mobile','email'));
                    }
                    $arr_companies[] = array(
                                            'type'  => $type,
                                            'no'    => (isset($company['no']) ? $company['no'] : ''),
                                            'name'    => (isset($company['name']) ? $company['name'] : ''),
                                            'full_name'    => (isset($contact['full_name']) ? $contact['full_name'] : ''),
                                            'phone'    => (isset($company['phone']) ? $company['phone'] : ''),
                                            'mobile'    => (isset($company['mobile']) ? $company['mobile'] : ''),
                                            'email'    => (isset($company['email']) ? $company['email'] : ( isset($contact['email']) ? $contact['email'] : '' ) )
                                             );
                }
                foreach($arr_companies as $company){
                    $html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'" style="valign:middle">';
                    $html .= '<td>'.$company['type'].'</td>';
                    $html .= '<td class="center_text">'.$company['no'].'</td>';
                    $html .= '<td>'.$company['name'].'</td>';
                    $html .= '<td>'.$company['full_name'].'</td>';
                    $html .= '<td>'.$company['phone'].'</td>';
                    $html .= '<td>'.$company['mobile'].'</td>';
                    $html .= '<td>'.$company['email'].'</td>';
                    $html .= '</tr>';
                    $i++;
                }
                $html .= '<tr class="last"><td class="bold_text" colspan="7">'.$i.' record(s) listed.</td></tr>';
                $arr_data['title'] = array('Type'=>'width: 10%','Ref no'=>'width: 7%;','Company'=>'width: 40%','Contact'=>'width: 15%','Phone','Mobile','Email');
                $arr_data['content'] = $html;
                $arr_data['report_name'] = 'Company Mini Listing (with main company)';
                $arr_data['report_file_name']='COM_'.md5(time());
                $arr_data['report_orientation'] = 'landscape';
                $arr_data['excel_url'] = URL.'/companies/view_excel';
                Cache::write('company_view_minilist',$arr_data);
                Cache::write('company_view_excel',$arr_companies);
            }
        } else {
            $arr_data = Cache::read('company_view_minilist');
            Cache::delete('company_view_minilist');
        }
        $this->render_pdf($arr_data);
    }

    function view_excel(){
        $arr_companies = Cache::read('company_view_excel');
        Cache::delete('company_view_excel');
        if(!$arr_companies){
            echo 'No data';die;
        }
        App::import('Vendor', 'phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("")
                                     ->setLastModifiedBy("")
                                     ->setTitle("Company - Minilist")
                                     ->setSubject("Company - Minilist")
                                     ->setDescription("Company - Minilist")
                                     ->setKeywords("Company - Minilist")
                                     ->setCategory("Company - Minilist");
        $worksheet = $objPHPExcel->getActiveSheet();
        $worksheet->setCellValue('A1',"Type")
                                        ->setCellValue('B1',"Ref no")
                                        ->setCellValue('C1',"Company")
                                        ->setCellValue('D1',"Contact")
                                        ->setCellValue('E1',"Phone")
                                        ->setCellValue('F1',"Mobile")
                                        ->setCellValue('G1',"Email");
        // $worksheet->freezePane('H2');
        $i = 2;
        foreach($arr_companies as $company){
            $worksheet->setCellValue('A'.$i,$company['type'])
                        ->setCellValue('B'.$i,$company['no'])
                        ->setCellValue('C'.$i,$company['name'])
                        ->setCellValue('D'.$i,$company['full_name'])
                        ->setCellValue('E'.$i,$company['phone'])
                        ->setCellValue('F'.$i,$company['mobile'])
                        ->setCellValue('G'.$i,$company['email']);
            $i ++;
        }
        $worksheet->setCellValue('A'.$i,($i-2).' record(s) listed');
        $worksheet->mergeCells("A$i:C$i");
        $worksheet->getRowDimension(8)->setRowHeight(-1);
        $worksheet->getStyle('A1:G1')->getFont()->setBold(true);
        $worksheet->getStyle('B1:B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
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
        $worksheet->getStyle('A1:G'.($i-1))->applyFromArray($styleArray);
        for($i = 'A'; $i !== 'H'; $i++){
            $worksheet->getColumnDimension($i)
                            ->setAutoSize(true);
        }
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(APP.DS.'webroot'.DS.'upload'.DS.'Company_Minilist.xlsx');
        $this->redirect('/upload/Company_Minilist.xlsx');
        die;
    }

    function create_email(){
        $this->selectModel('Company');
        $arr_company = $this->Company->select_one(array('_id' => new MongoId($this->get_id())));

        $arr_save = array();
        $this->selectModel('Communication');
        $arr_save['code'] = $this->Communication->get_auto_code('code');

        $arr_save['comms_type'] = 'Email';

        $arr_save['company_id'] = isset($arr_company['_id'])?$arr_company['_id']:'';
        $arr_save['company_name'] = isset($arr_company['name'])?$arr_company['name']:'';
        $arr_save['email'] = isset($arr_company['email'])?$arr_company['email']:'';
        $arr_save['module']=isset($this->params->params['controller'])?$this->params->params['controller']:'';

        $this->selectModel('Contact');
        $arr_contact = $arr_temp = array();
        if(isset($arr_company['contact_id']) && is_object($arr_company['contact_id'])){
            $arr_contact = $this->Contact->select_one(array('_id' => new MongoId($arr_company['contact_id'])));
        }
        else{
            $arr_contact = $this->Contact->select_all(array(
                'arr_where' => array('company_id'=> new MongoId($arr_company['_id'])),
                'arr_order' => array('_id'=> -1),
            ));
            $arrtemp = iterator_to_array($arr_contact);
            if(count($arr_temp) > 0){
                $arr_contact = current($arrtemp);
            }else
                $arr_contact = array();
        }
        if(isset($arr_contact['_id'])){
            $arr_save['contact_name'] = isset($arr_contact['first_name'])?$arr_contact['first_name']:'';
            $arr_save['last_name']= isset($arr_contact['last_name'])?$arr_contact['last_name']:'';
            $arr_save['contact_id'] = $arr_contact['_id'];
        }
        else{
            $arr_save['contact_name']='';
            $arr_save['contact_id']='';
        }
        $arr_save['contact_from_id']=$this->Contact->user_id();
        $arr_save['contact_from']=$this->Contact->user_name();

        if ($this->Communication->save($arr_save)) {
            $this->redirect('/communications/entry/'. $this->Communication->mongo_id_after_save);
        }
        $this->redirect('/communications/entry');
    }

    function create_fax(){
        $this->selectModel('Company');
        $arr_company = $this->Company->select_one(array('_id' => new MongoId($this->get_id())));

        $arr_save = array();
        $this->selectModel('Communication');
        $arr_save['code'] = $this->Communication->get_auto_code('code');

        $arr_save['comms_type'] = 'Fax';

        $arr_save['company_id'] = isset($arr_company['_id'])?$arr_company['_id']:'';
        $arr_save['company_name'] = isset($arr_company['name'])?$arr_company['name']:'';
        $arr_save['email'] = isset($arr_company['email'])?$arr_company['email']:'';
        $arr_save['created_from']=isset($this->params->params['controller'])?$this->params->params['controller']:'';
        $arr_save['fax']=isset($arr_company['fax'])?$arr_company['fax']:'';
        $arr_save['phone']=isset($arr_company['phone'])?$arr_company['phone']:'';

        $this->selectModel('Contact');
        $arr_contact = $arr_temp = array();
        if(isset($arr_company['contact_id']) && is_object($arr_company['contact_id'])){
            $arr_contact = $this->Contact->select_one(array('_id' => new MongoId($arr_company['contact_id'])));
        }
        else{
            $arr_contact = $this->Contact->select_all(array(
                'arr_where' => array('company_id'=> new MongoId($arr_company['_id'])),
                'arr_order' => array('_id'=> -1),
            ));
            $arrtemp = iterator_to_array($arr_contact);
            if(count($arr_temp) > 0){
                $arr_contact = current($arrtemp);
            }else
                $arr_contact = array();
        }
        if(isset($arr_contact['_id'])){
            $arr_save['contact_name'] = isset($arr_contact['first_name'])?$arr_contact['first_name']:'';
            $arr_save['last_name']= isset($arr_contact['last_name'])?$arr_contact['last_name']:'';
            $arr_save['contact_id'] = $arr_contact['_id'];
        }
        else{
            $arr_save['contact_name']='';
            $arr_save['contact_id']='';
        }
        $arr_save['contact_from_id']=$this->Contact->user_id();
        $arr_save['contact_from']=$this->Contact->user_name();

        if ($this->Communication->save($arr_save)) {
            $this->redirect('/communications/entry/'. $this->Communication->mongo_id_after_save);
        }
        $this->redirect('/communications/entry');
    }

    function create_letter(){
        $this->selectModel('Company');
        $arr_company = $this->Company->select_one(array('_id' => new MongoId($this->get_id())));

        $arr_save = array();
        $this->selectModel('Communication');
        $arr_save['code'] = $this->Communication->get_auto_code('code');

        $arr_save['comms_type'] = 'Letter';

        $arr_save['company_id'] = isset($arr_company['_id'])?$arr_company['_id']:'';
        $arr_save['company_name'] = isset($arr_company['name'])?$arr_company['name']:'';
        $arr_save['email'] = isset($arr_company['email'])?$arr_company['email']:'';
        $arr_save['created_from']=isset($this->params->params['controller'])?$this->params->params['controller']:'';
        $arr_save['fax']=isset($arr_company['fax'])?$arr_company['fax']:'';
        $arr_save['phone']=isset($arr_company['phone'])?$arr_company['phone']:'';

        $this->selectModel('Contact');
        $arr_contact = $arr_temp = array();
        if(isset($arr_company['contact_id']) && is_object($arr_company['contact_id'])){
            $arr_contact = $this->Contact->select_one(array('_id' => new MongoId($arr_company['contact_id'])));
        }
        else{
            $arr_contact = $this->Contact->select_all(array(
                'arr_where' => array('company_id'=> new MongoId($arr_company['_id'])),
                'arr_order' => array('_id'=> -1),
            ));
            $arrtemp = iterator_to_array($arr_contact);
            if(count($arr_temp) > 0){
                $arr_contact = current($arrtemp);
            }else
                $arr_contact = array();
        }
        if(isset($arr_contact['_id'])){
            $arr_save['contact_name'] = isset($arr_contact['first_name'])?$arr_contact['first_name']:'';
            $arr_save['last_name']= isset($arr_contact['last_name'])?$arr_contact['last_name']:'';
            $arr_save['contact_id'] = $arr_contact['_id'];
        }
        else{
            $arr_save['contact_name']='';
            $arr_save['contact_id']='';
        }
        $arr_save['contact_from_id']=$this->Contact->user_id();
        $arr_save['contact_from']=$this->Contact->user_name();

        if ($this->Communication->save($arr_save)) {
            $this->redirect('/communications/entry/'. $this->Communication->mongo_id_after_save);
        }
        $this->redirect('/communications/entry');
    }

    function contacts_delete($contact_id) {
        $arr_save['_id'] = $contact_id;
        $arr_save['deleted'] = true;
        $this->selectModel('Contact');
        if ($this->Contact->save($arr_save)) {
            echo 'ok';
        } else {
            echo 'Error: ' . $this->Contact->arr_errors_save[1];
        }
        die;
    }

    function get_company_tax($arr_data){
        $arr_return = array();
        $this->selectModel('Tax');
        $key_tax = '';
        if(isset($arr_data['invoice_address'][0]['invoice_province_state_id'])&&$arr_data['invoice_address'][0]['invoice_province_state_id'])
            $key_tax = $arr_data['invoice_address'][0]['invoice_province_state_id'];
        else if(isset($arr_data['shipping_address'][0]['shipping_province_state_id'])&&$arr_data['shipping_address'][0]['shipping_province_state_id'])
            $key_tax = $arr_data['shipping_address'][0]['shipping_province_state_id'];
        $arr_tax = $this->Tax->tax_select_list();
        if(isset($arr_tax[$key_tax])){
            $tax = explode("%",$arr_tax[$key_tax]);
            $arr_return['tax'] = $key_tax;
            $arr_return['taxval'] = (float)$tax[0];
        }
        return $arr_return;
    }

    function get_company_info($arr_company){
        $arr_save = array();
        $arr_save['company_name'] = isset($arr_company['name'])?$arr_company['name']:'';
        $arr_save['company_id'] = $arr_company['_id'];
        $arr_save['email'] = isset($arr_company['email'])?$arr_company['email']:'';
        $arr_save['phone'] = isset($arr_company['phone'])?$arr_company['phone']:'';
        $this->selectModel('Salesaccount');
        $salesaccount = $this->Salesaccount->select_one(array('company_id' => new MongoId($arr_save['company_id'])),array('payment_terms'));
        $arr_save['payment_terms'] = isset($salesaccount['payment_terms']) ? $salesaccount['payment_terms'] : 0;
        if (isset($arr_company['contact_default_id'])) {
            $this->selectModel('Contact');
            $arr_contact = $this->Contact->select_one(array('_id' => $arr_company['contact_default_id']), array('first_name','last_name','email','direct_dial'));
            $arr_save['contact_id'] = $arr_contact['_id'];
            $arr_save['contact_name'] = $arr_contact['first_name'] . ' ' . $arr_contact['last_name'];
            if(isset($arr_contact['email'])&&$arr_contact['email']!='')
                $arr_save['email'] = $arr_contact['email'];
            if(isset($arr_contact['direct_dial'])&&$arr_contact['direct_dial']!='')
                $arr_save['phone'] = $arr_contact['direct_dial'];
        }
        if(isset($arr_company['addresses_default_key'])){
            $key_default = $arr_company['addresses_default_key'];
            $arr_save['invoice_address'][0] = array(
                'deleted' => false,
                'invoice_address_1' => $arr_company['addresses'][$key_default]['address_1'],
                'invoice_address_2' => $arr_company['addresses'][$key_default]['address_2'],
                'invoice_address_3' => $arr_company['addresses'][$key_default]['address_3'],
                'invoice_town_city' => $arr_company['addresses'][$key_default]['town_city'],
                'invoice_province_state' => $arr_company['addresses'][$key_default]['province_state'],
                'invoice_province_state_id' => $arr_company['addresses'][$key_default]['province_state_id'],
                'invoice_zip_postcode' => $arr_company['addresses'][$key_default]['zip_postcode'],
                'invoice_country' => $arr_company['addresses'][$key_default]['country'],
                'invoice_country_id' => $arr_company['addresses'][$key_default]['country_id']
            );
        }elseif(isset($arr_company['addresses'][0])){
            $arr_save['invoice_address'][0] = array(
                'deleted' => false,
                'invoice_address_1' => $arr_company['addresses'][0]['address_1'],
                'invoice_address_2' => $arr_company['addresses'][0]['address_2'],
                'invoice_address_3' => $arr_company['addresses'][0]['address_3'],
                'invoice_town_city' => $arr_company['addresses'][0]['town_city'],
                'invoice_province_state' => $arr_company['addresses'][0]['province_state'],
                'invoice_province_state_id' => $arr_company['addresses'][0]['province_state_id'],
                'invoice_zip_postcode' => $arr_company['addresses'][0]['zip_postcode'],
                'invoice_country' => $arr_company['addresses'][0]['country'],
                'invoice_country_id' => $arr_company['addresses'][0]['country_id']
            );
        }
        if(isset($arr_company['our_csr_id']) && is_object($arr_company['our_csr_id'])){
            $arr_save['our_csr'] = $arr_company['our_csr'];
            $arr_save['our_csr_id'] = $arr_company['our_csr_id'];
        }
        if(isset($arr_company['our_rep_id']) && is_object($arr_company['our_rep_id'])){
            $arr_save['our_rep'] = $arr_company['our_rep'];
            $arr_save['our_rep_id'] = $arr_company['our_rep_id'];
        }
        $arr_save = array_merge($arr_save, $this->get_company_tax($arr_save));
        return $arr_save;
    }

    public function delete_all_associate($ids='',$opname=''){
        $this->selectModel('Company');
        if($opname == 'pricing'){
            $arr_company = $this->Company->select_one(array('_id' => new MongoId($this->get_id())));
            if(isset($arr_company['pricing'][$ids])){
                unset($arr_company['pricing'][$ids]);
                $this->opm->save($arr_company);
            }
        }
        return false;
    }

    public function build_pricing(){
        $this->selectModel('Company');
        $arr_company = $this->Company->select_all(array(
                                                  'arr_where' => array(),
                                                  'arr_field' => array('pricing','our_rep','addresses'),
                                                  'limit' => 3000,
                                                  ));
        foreach($arr_company as $key => $value){

            if(isset($value['pricing']) && $value['pricing'] != ""){
                $arr_tmp = array();
                foreach($value['pricing'] as $k_pricing => $v_pricing){
                    foreach($v_pricing['price_break'] as $v_deleted){
                        if($v_deleted == 'true'){
                            pr($v_deleted);
                            //unset($v_deleted);
                        }
                    }
                   /* if(isset($v_pricing['deleted']) && $v_pricing['deleted'] == true){
                        unset($value['pricing'][$k_pricing]);
                    }*/
                }
                $arr_tmp['pricing'] = $value['pricing'];
                $arr_tmp['_id'] = $value['_id'];
                $this->Company->save($arr_tmp);
            }

        }
        die;
        //echo 'xong';die;
    }

    function test(){
        $arr_companies = $this->opm->select_all(array(
                               'arr_where' => array('$or' => array(
                                                             array('addresses.province_state_id' => array(
                                                                                    '$in' => array(null,'')
                                                                    ),
                                                             array('addresses.province_state_id' => array(
                                                                                    '$exists' => false
                                                                                     ),
                                                                    )

                                                             ),
                                                        ),
                                                    'addresses' => array('$exists' => true),
                                                ),
                                'arr_filed' => array('_id')
                               ));
        echo $arr_companies->count(); die;
    }
    function test_our_rep(){
        $this->selectModel('Company');

        $arr_companies = $this->Company->select_all(array(
                'arr_field' => array('our_rep', 'our_rep_id','no'),
        ));
        $i = 0;$j = 0;
        foreach ($arr_companies as $key => $company) {
            if (isset($company['our_rep_id']) && $company['our_rep_id'] != '') {
                $i++;
                $this->selectModel('Contact');
                $contact = $this->Contact->select_one(
                    array('_id' => $company['our_rep_id']),
                    array('first_name', 'last_name')
                );
                //pr($company);
                //pr($contact);

                // Neu contact ton tai
                $contact_rep = $contact['first_name'].' '.$contact['last_name'];
                if ( $company['our_rep'] != $contact_rep ) {
                    $j++;
                    echo $company['no'].': '; echo $company['our_rep']; echo ' != '; echo $contact_rep; echo "<Br/>";
                }
            }

        }
        echo 'Sai gia tri: '.$j.'/';echo $i;die;
    }
    function restore_records(){
        $this->selectModel('Tmpcollection');
        $tmp_companies = $this->Tmpcollection->select_all(array(
                    'arr_where' => array(
                            "our_rep"=> "Valerie Moss",
                            "deleted" => "no_search"
                        )
            ));
        $tmp_companies = iterator_to_array($tmp_companies);

        $companies = $this->opm->select_all(array(
                    'arr_where' => array(
                            "our_rep"=> "Valerie Moss",
                            "deleted" => "no_search"
                        )
            ));
        echo $companies->count().'<br />';
        $i = 0;
        foreach($companies as $company){
            if(count($company) > 3) continue;
            if(isset($tmp_companies[(string)$company['_id']]))
                $company = $tmp_companies[(string)$company['_id']];
            $company['our_rep_id'] = new MongoId('5271e565222aad8011001634');
            $this->opm->rebuild_collection($company);
            $i++;
        }
        echo $i.' records rebuild.';
        die;
    }

    function active_customer(){
        if(!isset($_GET['print_pdf'])){
            $or_where = array(
                                  'is_customer' => 1,
                                  'is_supplier' => 0
                                  );
            $or_where1 = array(
                               'is_customer' => 1,
                               'is_supplier' => 1,
                               );
            $cond['$or']=array($or_where,$or_where1);

            $this->selectModel('Company');
            $arr_company = $this->Company->select_all(array(
                                             'arr_where' => $cond,
                                             'arr_field' => array('no','name','type_name','phone','fax','email','our_rep','our_csr'),
                                             'arr_order' => array('no'=>1)
                                             ));
            if($arr_company->count() > 0){
                $i=0;
                $html = '';
                foreach($arr_company as $key => $value){
                    $html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">';
                    $html .= '<td class="center_text">'.$value['no'].'</td>';
                    $html .= '<td>'.(isset($value['name'])?$value['name']:'') .'</td>';
                    $html .= '<td>'.(isset($value['type_name'])?$value['type_name']:'' ).'</td>';
                    $html .= '<td>'.(isset($value['phone'])?$value['phone']:'') .'</td>';
                    $html .= '<td>'.(isset($value['fax'])?$value['fax']:'' ).'</td>';
                    $html .= '<td>'.(isset($value['email'])?$value['email']:'' ).'</td>';
                    $html .= '<td>'.(isset($value['our_rep'])?$value['our_rep']:'' ).'</td>';
                    $html .= '<td>'.(isset($value['our_csr'])?$value['our_csr']:'' ).'</td>';
                    $html .= '</tr>';
                    $i++;
                }
                 $html .='<tr class="last">
                                <td colspan="8" class="bold_text right_none">'.$i.' record(s) listed.</td>
                            </tr>';
                $arr_data['title'] = array('Code'=>'text-align: left;width:5%','Company'=>'text-align: left;width: 25%','Type'=>'text-align: left; width: 8%','Phone'=>'text-align: left;width: 7%','Fax'=>'text-align: left; width:8%','Email'=>'text-align: left;width: 8%;','Our Rep'=>'width: 8%;text-align:left','Our Csr'=>'text-align: left;width: 8%;');
                $arr_data['content'] = $html;
                $arr_data['font-size'] = 12;
                $arr_data['report_name'] = 'Active Customer';
                $arr_data['report_orientation'] = '';
                $arr_data['report_file_name'] = 'COM_'.md5(time());
                $arr_data['excel_url'] = URL.'/companies/active_customer_excel';
                Cache::write('active_customer', $arr_data);
                Cache::write('active_customer_excel',iterator_to_array($arr_company) );
            }
        }else {
            $arr_data = Cache::read('active_customer');
            Cache::delete('active_customer');
        }
        $this->render_pdf($arr_data);
    }

    function active_customer_excel(){
        $this->selectModel('Company');
        $arr_data = Cache::read('active_customer_excel');
        if(!$arr_data){
            echo 'No data';die;
        }
        Cache::delete('active_customer_excel');
        App::import('Vendor', 'phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("")
                                     ->setLastModifiedBy("")
                                     ->setTitle("Active Customer")
                                     ->setSubject("Active Customer")
                                     ->setDescription("Active Customer")
                                     ->setKeywords("Active Customer")
                                     ->setCategory("Active Customer");
        $worksheet = $objPHPExcel->getActiveSheet();
        $worksheet->setCellValue('A1',"No")
                                        ->setCellValue('B1',"Company")
                                        ->setCellValue('C1',"Type")
                                        ->setCellValue('D1',"Phone")
                                        ->setCellValue('E1',"Fax")
                                        ->setCellValue('F1',"Email")
                                        ->setCellValue('G1',"Our Rep")
                                        ->setCellValue('H1',"Our Csr");
        $i = 2;
        foreach($arr_data as $key => $value){
            $worksheet->setCellValue('A'.$i,$value['no'])
                        ->setCellValue('B'.$i,(isset($value['name'])?$value['name']:''))
                        ->setCellValue('C'.$i,(isset($value['type_name'])?$value['type_name']:''))
                        ->setCellValue('D'.$i,(isset($value['phone'])?$value['phone']:''))
                        ->setCellValue('E'.$i,(isset($value['fax'])?$value['fax']:''))
                        ->setCellValue('F'.$i,(isset($value['email'])?$value['email']:''))
                        ->setCellValue('G'.$i,(isset($value['our_rep'])?$value['our_rep']:''))
                        ->setCellValue('H'.$i,(isset($value['our_csr'])?$value['our_csr']:''));
            $i++;
        }
        $worksheet->mergeCells("A$i:E$i")
                        ->setCellValue('A'.$i,($i-2).' record(s) listed');

        $worksheet->getRowDimension(8)->setRowHeight(-1);
        $worksheet->getStyle('A1:N1')->getFont()->setBold(true);
        $worksheet->getStyle('A1:A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $worksheet->getStyle('E1:E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
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
        $worksheet->getStyle('A1:H'.($i-1))->applyFromArray($styleArray);
        for($i = 'A'; $i !== 'I'; $i++){
            $worksheet->getColumnDimension($i)
                            ->setAutoSize(true);
        }
        $worksheet->setTitle('Active Customer');

        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(APP.DS.'webroot'.DS.'upload'.DS.'Active_Customer.xlsx');
        $this->redirect('/upload/Active_Customer.xlsx');
        die;
    }

    function rebuild_inactive() {
        $companies = $this->opm->select_all(array(
                        'arr_where' => array('inactive' => array(
                                                '$exists' => true,
                                                '$ne' => 1,
                                                )),
                        'arr_field' => array('inactive'),
                        'arr_order' => array('_id' => 1)
            ));
        echo $companies->count().'<br />';
        $i = 0;
        foreach($companies as $company){
            if($company['inactive']!=1) {
                $company['inactive'] = 0;
                $this->opm->rebuild_collection($company);
                $i++;
            }
        }
        echo $i.' companies repaired.';
        die;
    }

    public function dealer_discount(){
        $company = $this->opm->select_one(array('_id' => new MongoId($this->get_id())),array('dealer_discount', 'dealer_pricing'));
        $subdatas = array();
        $subdatas['dealer_discount']['dealer_discount'] = isset($company['dealer_discount']) ? $company['dealer_discount'] : 0;
        $subdatas['dealer_pricing'] = isset($company['dealer_pricing']) ? $company['dealer_pricing'] : array();
        $this->set('subdatas', $subdatas);
    }
    public function dealer_pricing_add(){
        $id = $this->get_id();
        $company = $this->opm->select_one(array('_id' => new MongoId($id)),array('dealer_pricing'));
        $this->selectModel('Product');
        $product = $this->Product->select_one(array('_id' => new MongoId($_POST['product_id'])),array('code','name','discount'));
        $company['dealer_pricing'][] = array(
                                    'deleted' => false,
                                    'code' => $product['code'],
                                    'name' => $product['name'],
                                    'product_id' => $product['_id'],
                                    'discount'=> isset($product['discount'])?$product['discount']:0,
                                    );
        $this->opm->save($company);
        echo "success";
        die;
    }
    function edit_discount(){
        $company_id = $this->get_id();
        $company = $this->opm->select_one(array('_id' => new MongoId($company_id)),array('dealer_pricing'));
        $dealer_pricing = $company['dealer_pricing'][$_POST['code']];
        $company['dealer_pricing'][$_POST['code']] = array(
            'deleted' => false,
            'code' => $dealer_pricing['code'],
            'name' => $dealer_pricing['name'],
            'product_id' =>  $dealer_pricing['product_id'],
            'discount'=> $_POST['inval'],
        );
        $this->opm->save($company);
        die;
    }
    public function tao_data_demo_company(){
        if($_SERVER['HTTP_HOST']=='jobtraq-demo.anvyonline.com'){
            $arr_company_id = $this->opm->tao_data_demo_company();
            $this->selectModel('Contact');
            $this->Contact->tao_data_demo_contact($arr_company_id);
            $this->selectModel('Communication');
            $this->Communication->tao_data_demo_comms($arr_company_id);
            $this->selectModel('Job');
            $this->Job->tao_data_demo_job($arr_company_id);
            die;
        }
        if($_SERVER['HTTP_HOST']=='jt.banhmisub.com' || $_SERVER['HTTP_HOST']=='demo.jobtraq.vimpact.ca' || $_SERVER['HTTP_HOST']=='bms.com'){
            // $arr_company_id = $this->opm->deleteAll();
            $where = array();
            $where['_id']['$nin'] = array(
                                        new MongoId('541b4eadb0e6df4c080001ac'),
                                        new MongoId('5271dab4222aad6819000ed0'),
                                    );
            $arr_company_id = $this->opm->remove($where);
            $where = array();
            $where['_id']['$nin'] = array(
                                        new MongoId('5271f4fa67b96d7f11000022'),
                                        new MongoId('5271f6d267b96d6013000011'),
                                    );
            $this->selectModel('Contact');
            $this->Contact->remove($where);

            die;
        }

    }

    function generate($test = '')
    {
        $this->selectModel('Salesinvoice');
        $this->selectModel('Company');
        $this->selectModel('Stuffs');

        $openAccount = $this->Stuffs->select_one(array('value'=> 'Open Account'));
        if( !empty($openAccount) ) {
            $openAccount= [
                '_id' => isset($openAccount['open_account_id']) ? $openAccount['open_account_id'] : '',
                'name' => isset($openAccount['open_account']) ? $openAccount['open_account'] : '',
            ];
        } else {
            $openAccount = [];
        }

        $saleManager = $this->Stuffs->select_one(array('value'=> 'Sale Manager'));
        if( !empty($saleManager) ) {
            $saleManager= [
                '_id' => isset($saleManager['sale_manager_id']) ? $saleManager['sale_manager_id'] : '',
                'name' => isset($saleManager['sale_manager']) ? $saleManager['sale_manager'] : '',
            ];
        } else {
            $saleManager = [];
        }
        $arrDate = array(
                    'oneYear' => array(
                        'condition' => array(
                            '$lt' => strtotime("-1 year", time()),
                        ),
                        'our_rep'   => $openAccount,
                        'status'    => ''
                    ),
                    'sixMonths' => array(
                        'condition' => array(
                            '$gt' => strtotime("-1 year", time()),
                            '$lt' => strtotime("-6 months", time())
                        ),
                        'our_rep'   => $saleManager,
                        'status'    => 'Suspended'
                    )
                );

        $companies = $this->Company->select_all(array(
                                   'arr_field'  =>  array('_id'),
                                   'arr_order'  =>  array('_id'=>1),
                                   'limit'      =>  9999999
                                   ));
        $oneYear = $sixMonths = 0;
        foreach($companies as $company){
            $lastInvoice = $this->Salesinvoice->select_one(array(
                                                    'company_id' => $company['_id'],
                                                    'invoice_status' => array('$ne' => 'Cancelled'),
                                                    ), array('invoice_date'), array('invoice_date' => -1));

            if( isset($lastInvoice['invoice_date']) && is_object($lastInvoice['invoice_date'])  ) {
                foreach( $arrDate as $var => $date ) {
                    if( isset($date['condition']['$lt']) && $lastInvoice['invoice_date']->sec > $date['condition']['$lt'] ) {
                        continue;
                    }
                    if( isset($date['condition']['$gt']) && $lastInvoice['invoice_date']->sec < $date['condition']['$gt'] ) {
                        continue;
                    }
                    $ourRep  = isset($date['our_rep']['name']) ? $date['our_rep']['name'] : '';
                    $ourRepId  = isset($date['our_rep']['_id']) ? $date['our_rep']['_id'] : '';
                    $this->Company->collection->update(array(
                            '_id'       => $company['_id'],
                        ), array(
                            '$set' => array(
                                'status'    => $date['status'],
                                'our_rep'   => $ourRep,
                                'our_rep_id'=> $ourRepId
                            )
                        ));
                    $$var++;
                }
            }
        }


        echo json_encode([
            'message' => $oneYear." companies over than 1 year.\n".$sixMonths." companies over than 6 months and less than 1 year.\n"
        ]);
        die;
    }

    public function address_popup()
    {
        $company_id = $company_name = '';
        if (isset($_REQUEST['company_id']) && strlen($_REQUEST['company_id']) == 24) {
            $company_id = $_REQUEST['company_id'];
        }

        if (isset($_REQUEST['company_name']) && !empty($_REQUEST['company_name'])) {
            $company_name = $this->Common->strip_search($_REQUEST['company_name']);
        }

        $arr_where = $arr_addresses = array();

        if (!empty($company_id)) {
            $arr_where['_id'] = new MongoId($company_id);
        }
        if (!empty($company_name)) {
            $arr_where['name'] = new MongoRegex('/'.$company_name.'/i');
        }
        if (!empty($arr_where)) {
            $company = array_merge(array('name' => '', 'addresses' => array()), $this->opm->select_one($arr_where, array('addresses', 'name')));
            $company_name = $company['name'];
            foreach ($company['addresses'] as $address) {
                if (isset($address['deleted']) && $address['deleted']) continue;
                $arr_addresses[] = array_merge(array(
                                                    'deleted'=> false,
                                                    'name' => '',
                                                    'country'=> '',
                                                    'country_id'=> '',
                                                    'province_state'=> '',
                                                    'province_state_id'=> '',
                                                    'address_1'=> '',
                                                    'address_2'=> '',
                                                    'address_3'=> '',
                                                    'town_city'=> '',
                                                    'zip_postcode'=> '',
                                                ), $address);
            }
            $arr_addresses = $company['addresses'];
        }

        $this->set('company_name', $company_name);
        $this->set('addresses', $arr_addresses);
    }

    public function import()
    {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            if (isset($_FILES['file'])) {
                $name = $_FILES['file']['name'];
                $fileDir = WWW_ROOT . DS . 'upload' . DS . $name;
                if (move_uploaded_file($_FILES['file']['tmp_name'], $fileDir)) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $info = finfo_file($finfo, $fileDir);
                    if (in_array($info, ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
                        App::import('Vendor', 'phpexcel/PHPExcel');
                        $objPHPExcel = new PHPExcel();
                        try {
                            $inputFileType = PHPExcel_IOFactory::identify($fileDir);
                            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                            $objPHPExcel = $objReader->load($fileDir);
                        } catch(Exception $e) {
                            echo 'Error loading file "'.$name .'": '.$e->getMessage().'<br />';
                        }
                        $this->selectModel('Company');
                        $this->selectModel('Province');
                        $provinces = $this->Province->get_provinces('CA');
                        $sheet = $objPHPExcel->getSheet(0);
                        $highestRow = $sheet->getHighestRow();
                        $i = 0;
                        for ($row = 2; $row <= $highestRow; $row++) {
                            $data = $sheet->rangeToArray("A{$row}:F{$row}", null, true, false);
                            if (isset($data[0])) {
                                $data = $data[0];
                                $empty = 0;
                                foreach ($data as $value) {
                                    if (empty($value)) {
                                        $empty++;
                                    }
                                }
                                if ($empty == count($data)) {
                                    continue;
                                }
                                $arrSave = array(
                                        'no' => $this->Company->get_auto_code('no'),
                                        'name'  => $data[0],
                                        'is_customer' => 1,
                                        'is_supplier' => 0,
                                        'type_name' => 'Current',
                                        'phone' => '',
                                        'fax' => '',
                                        'email' => '',
                                        'status' => '',
                                        'inactive' => 0,
                                        'addresses_default_key' => 0,
                                        'addresses' => array (
                                                array (
                                                        'deleted'=> false,
                                                        'country'=> 'Canada',
                                                        'country_id'=> 'CA',
                                                        'province_state'=> $data[4],
                                                        'province_state_id'=> array_search($data[4], $provinces),
                                                        'address_1'=>  $data[1],
                                                        'address_2'=> '',
                                                        'address_3'=> '',
                                                        'town_city'=> '',
                                                        'zip_postcode'=> $data[5],
                                                        'default'=> true
                                                    )
                                            ),
                                        'our_rep'=> 'System Admin',
                                        'our_rep_id'=> new MongoId('100000000000000000000000'),
                                        'our_csr'=> 'System Admin',
                                        'our_csr_id'=> new MongoId('100000000000000000000000'),
                                    );
                                $this->Company->save($arrSave);
                                $i++;
                            }
                        }
                        echo $i.' companies has been imported!<br />';
                    } else {
                        echo 'Please upload excel file to import.<br />';
                    }
                } else {
                    echo 'Upload failed. Please try again. <br />';
                }
            }
        }
        echo '<form method="POST" enctype="multipart/form-data">
                <input type="file" name="file" ><br />
                <input type="submit" value="Upload" />
            </form>';
    }
}