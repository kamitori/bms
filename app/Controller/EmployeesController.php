<?php
App::uses('AppController', 'Controller');
class EmployeesController  extends AppController {

	var $name = 'Employees';
	public $helpers = array();
	public $opm; //Option Module
    var $modelName = 'Employee';
	public function beforeFilter(){
		parent::beforeFilter();
		$this->set_module_before_filter('Employee');
	}


	public function rebuild_setting($arr_setting=array()){
		// parent::rebuild_setting($arr_setting);
         $arr_setting = $this->opm->arr_settings;
        if(!$this->check_permission($this->name.'_@_entry_@_edit')){
            $arr_setting = $this->opm->set_lock(array(),'out');
            $this->set('address_lock', '1');
        }
        $arr_tmp = $this->opm->arr_field_key('cls');
        $arr_link = array();
        if(!empty($arr_tmp))
            foreach($arr_tmp as $key=>$value)
                $arr_link[$value][] = $key;
        $this->set('arr_link',$arr_link);
	}

	// Add action



	//Entry - trang chi tiet
 	  public function entry(){

        $arr_set = $this->opm->arr_settings;
        // Get value id
        $iditem = $this->get_id();
        if($iditem=='')
            $iditem = $this->get_last_id();

        $this->set('iditem',$iditem);
        //Load record by id
        if($iditem!=''){
            $arr_tmp = $this->opm->select_one(array('_id' => new MongoId($iditem)));
            foreach($arr_set['field'] as $ks => $vls){
                foreach($vls as $field => $values){
                    if(isset($arr_tmp[$field])){
                        $arr_set['field'][$ks][$field]['default'] = $arr_tmp[$field];
                        if(in_array($field,$arr_set['title_field']))
                            $item_title[$field] = $arr_tmp[$field];

                        if(preg_match("/_date$/",$field) && is_object($arr_tmp[$field]))
                            $arr_set['field'][$ks][$field]['default'] = date('m/d/Y',$arr_tmp[$field]->sec);
                        else if($this->opm->check_field_link($ks,$field)){
                            $field_id = $arr_set['field'][$ks][$field]['id'];
                            if(!isset($arr_set['field'][$ks][$field]['syncname']))
                                $arr_set['field'][$ks][$field]['syncname'] = 'name';
                            $arr_set['field'][$ks][$field]['default'] = $this->get_name($this->ModuleName($arr_set['field'][$ks][$field]['cls']),$arr_tmp[$field_id],$arr_set['field'][$ks][$field]['syncname']);

                        }
                    }
                }
            }
            $arr_set['field']['panel_1']['mongo_id']['default'] = $iditem;
            $this->Session->write($this->name.'ViewId',$iditem);

            //BEGIN custom
            if(isset($arr_set['field']['panel_1']['code']['default']))
                $item_title['code'] = $arr_set['field']['panel_1']['code']['default'];
            else
                $item_title['code'] = '1';
            $this->set('item_title',$item_title);

            //show footer info
            $this->show_footer_info($arr_tmp);


        //add, setup field tự tăng
        }else{
            $nextcode = $this->opm->get_auto_code('code');
            $arr_set['field']['panel_1']['code']['default'] = $nextcode;
            $this->set('item_title',array('code'=>$nextcode));
        }
        $this->set('arr_settings',$arr_set);
        $this->sub_tab_default = 'leave';
        $this->sub_tab('',$iditem);
        $this->set_entry_address($arr_tmp,$arr_set);

        parent::entry();
    }


    public function reload_address($address_key = '') {
        if (isset($_POST['address_key']))
            $address_key = $_POST['address_key'];
        $ids = $this->get_id();
        $arr_tmp = $this->opm->select_one(
            array('_id' => new MongoId($ids)),
            array('addresses')
        );

        $arr_temp = array();
        if (isset($arr_tmp['addresses'][0]))
            foreach ($arr_tmp['addresses'][0] as $kk => $vv) {
                $arr_temp[$kk] = $vv;
                if ($kk == 'province_state') {
                    $arr_province = $this->province();
                    if (isset($arr_province[$vv]))
                        $arr_temp[$kk] = $arr_province[$vv];
                }else if ($kk == 'country') {
                    $arr_country = $this->country();
                    if (isset($arr_country[$vv]))
                        $arr_temp[$kk] = $arr_country[$vv];
                }
            }
        echo json_encode($arr_temp);
        die;
    }

    public function set_entry_address($arr_tmp, $arr_set) {
        $address_fset = array('address_1', 'address_2', 'address_3', 'town_city', 'country', 'province_state', 'zip_postcode');
        $address_value = $address_province_id = $address_country_id = $address_province = $address_country = array();
        $address_controller = array('invoice');
        $address_value['invoice'] = array('', '', '', '', "VN", '', '');
        $this->set('address_controller', $address_controller); //set
        $address_key = array(''); // ******chu y ********

        $this->set('address_key', $address_key); //set
        $address_country = $this->country();

        // $arr_set luu nguyen mang db toan bo trang rat lon
        // $arr_tmp la 1 mang lon gom nhiu [addresses], [invoice_address] , đổ dữ liệu từ db ra là nhờ biến nay   ****************
        foreach ($address_key as $kss => $vss) {  // vss = invoice
            //neu ton tai address trong data base
            if (isset($arr_tmp['addresses'][0])) {  // Neu ton tai $arr_tmp['invoice_address'][0] sua lai $arr_tmp['addresses'][0]
                $arr_temp_op = $arr_tmp['addresses'][0];  //  $arr_temp_op là mãng quan trọng nhát : array( country=>Viet Nam, address_1=>'1 oki',....)
                for ($i = 0; $i < count($address_fset); $i++) { //loop field and set value for display
                    if (isset($arr_temp_op[$address_fset[$i]])) {
                        $address_value[$vss][$i] = $arr_temp_op[$address_fset[$i]];  // $address_value chính là giá trị trực tiếp show ra giao diện
                    } else {
                        $address_value[$vss][$i] = '';
                    }
                }

                if (isset($arr_temp_op[$vss . 'country_id']))
                    $address_province[$vss] = $this->province($arr_temp_op['country_id']); // array(California=>'California',New York =>'New York',.)
                else
                    $address_province[$vss] = $this->province();

                //set province
                if (isset($arr_temp_op[$vss . 'province_state_id']) && $arr_temp_op[$vss . 'province_state_id'] != '' && isset($address_province[$vss][$arr_temp_op[$vss . 'province_state_id']]))
                    $address_province_id[$kss] = $arr_temp_op[$vss . 'province_state_id'];
                else if (isset($arr_temp_op['province_state']))
                    $address_province_id[$kss] = $arr_temp_op['province_state'];
                else
                    $address_province_id[$kss] = '';

                //set country
                if (isset($arr_temp_op['country_id'])) {
                    $address_country_id[$kss] = $arr_temp_op['country_id'];
                    $address_province[$vss] = $this->province($arr_temp_op['country_id']);
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
        $this->set('address_value', $address_value);  // $address_value chính là giá trị trực tiếp show ra giao diện
        $address_hidden_field = array('invoice_address');
        $this->set('address_hidden_field', $address_hidden_field); //set
        $address_label[0] = $arr_set['field']['panel_3']['invoice_address']['name'];
        $this->set('address_label', $address_label); //set
        $address_conner[0]['top'] = 'hgt';// fixbor';
        $address_conner[0]['bottom'] = 'jt_ppbot'; //fixbor2
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


    public function find_all(){
        $this->Session->delete('employees_entry_search_cond');
        $this->redirect('/employees/lists');
    }

    public function get_id() {
        if (isset($this->params->params['pass'][0]) && strlen($this->params->params['pass'][0]) == 24 && strpos($this->params->params['pass'][0], '_')===false)
            $iditem = $this->params->params['pass'][0];
        else if ($this->Session->check($this->name . 'ViewId'))
            $iditem = $this->Session->read($this->name . 'ViewId');
        else {
            //find last id
            if ($this->opm)
                $arr_tmp = $this->opm->select_one(array(), array(), array('_id' => -1));
            else {
                $module = $this->modelName;
                $this->selectModel($module);
                $arr_tmp = $this->$module->select_one(array(), array(), array('_id' => -1));
            }
            if (isset($arr_tmp['_id']) && is_object($arr_tmp['_id'])) {
                $iditem = (array) $arr_tmp['_id'];
                $iditem = $iditem['$id'];
            } else if (isset($arr_tmp['_id']) && strlen($arr_tmp['_id']) == 24) {
                $iditem = $arr_tmp['_id'];
            }
            else
                $iditem = '';
        }
        return $iditem;
    }

    public function arr_associated_data($field = '', $value = '', $valueid = '',$fieldopt='') {
        if($field == 'employee_name'){

            $arr_return['employee_id'] = new MongoId($valueid);
            $this->selectModel('Contact');
            $contact = $this->Contact->select_one(array('_id'=> $arr_return['employee_id']));
            $arr = array('direct_dial','mobile','email','fax','home_phone','extension_no','position','addresses_default_key','addresses','status','date_birth');
            foreach($arr as $key){
                if(!isset($contact[$key]))
                    $contact[$key] = '';
                $key_add = $key;
                if($key == 'status')
                    $key_add = 'marital_status';
                $arr_return[$key_add] = $contact[$key];
            }
        }
        return $arr_return;
    }

    public function employee_image($delete = false){
        $id = new MongoId($this->get_id());
        $employee = $this->opm->select_one(array('_id'=> $id),array('employee_image'));
        if(!isset($employee['employee_image']))
            $employee['employee_image'] = '';
        if($delete){
            if( $employee['employee_image'] && file_exists(WWW_ROOT. $employee['employee_image']) )
                unlink(WWW_ROOT. $employee['employee_image']);
            die;
        }
        if(!empty($_FILES)){
            $filename = $_FILES['employee_image_upload']['name'];
            move_uploaded_file($_FILES['employee_image_upload']['tmp_name'], WWW_ROOT.'upload'.DS.$filename);
            echo URL.'/upload/'.$filename;
            $this->opm->save(array('_id'=>$id,'employee_image'=>'upload/'.$filename));
            if( $employee['employee_image'] && file_exists(WWW_ROOT. $employee['employee_image']) )
                unlink(WWW_ROOT. $employee['employee_image']);
            die;
        }
        $this->set('employee_image',$employee['employee_image']);
    }

    function leave(){
        $id = new MongoId($this->get_id());
        $employee = $this->opm->select_one(array('_id'=>new MongoId($id)),array('employee_id'));
        $employee['employee_id'] = '528c36f667b96d314e000018';
        if(!isset($employee['employee_id']))
            $employee['employee_id'] = '';
        $this->set('contact_id',  $employee['employee_id']);
        $arr_return = $this->requestAction('/contacts/leave/'.$employee['employee_id'].'/return');
        foreach($arr_return as $key => $value)
            $this->set($key,$value);
        $this->set('id',$id);
    }

    function workings_holidays(){
        $id = new MongoId('528c36f667b96d314e000018');
        $employee = $this->opm->select_one(array('_id'=>new MongoId($id)),array('employee_id','workings_holidays'));
        if(!isset($employee['workings_holidays'])){
            $current_month = date('m_Y');
            $employee['workings_holidays'][$current_month] = $this->workings_holidays_add($employee,$current_month);
        }
        if(!isset($employee['employee_id']))
            $employee['employee_id'] = '';
        $this->set('contact_id',  $employee['employee_id']);
        $this->set('workings_months' , array_keys($employee['workings_holidays']));
    }

    function workings_holidays_ajax($month = ''){
        $id = new MongoId($this->get_id());
        $employee = $this->opm->select_one(array('_id'=>new MongoId($id)),array('workings_holidays'));
        if(isset($_POST['key'])){
            $key = str_replace('][', '_@_', $_POST['key']);
            $key = str_replace(array('data[WorkHoliday_@_',']'), '', $key);
            $key = explode('_@_', $key);
            $employee['workings_holidays'][$_POST['workings_month']][$key[0]][$key[1]][$key[2]] = $_POST['value'];
            $this->opm->save($employee);
            $total = $this->workings_holidays_total($employee['workings_holidays'][$_POST['workings_month']]);
            echo json_encode($total);
            die;
        }
        if(isset($_POST['month'])){
            $month = $_POST['month'];
        }
        if(!$month){
            $month = key( array_slice( $employee['workings_holidays'], -1, 1, TRUE ) );
        }
        $this->set('workings_month',$month);
        $this->set('workings_holidays',$employee['workings_holidays'][$month]);
        $total = $this->workings_holidays_total($employee['workings_holidays'][$month]);
        $this->set('total',$total);
    }

    function workings_holidays_total($employee){
        $total['month'] = 0;
        foreach($employee as $week => $week_days){
            foreach($week_days as $day){
                if(!isset($total[$week]))
                    $total[$week] = 0;
                $total[$week] += (float)str_replace(':','.',$day['to_time']) - (float)str_replace(':','.',$day['from_time']) + (float)str_replace(':','.',$day['lunch_time']);
                $total['month'] += $total[$week];
            }
        }
        return $total;
    }

    function workings_holidays_add($employee = array(), $month = ''){
        if($this->request->is('ajax')){
            $id = new MongoId($this->get_id());
            $employee = $this->opm->select_one(array('_id'=>new MongoId($id)),array('employee_id','workings_holidays'));
            $month = str_replace('/','_',$_POST['month']);
        }
        $arr_day = array( 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday' );
            foreach($arr_day as $day){
                $employee['workings_holidays'][$month]['week_1'][$day] = array(
                                                                                       'from_time' => '08:00',
                                                                                       'to_time' => '18:00',
                                                                                       'lunch_time' => '',
                                                                                       );
            }
         $employee['workings_holidays'][$month]['week_1']['sunday'] = array(
                                                                                       'from_time' => '',
                                                                                       'to_time' => '',
                                                                                       'lunch_time' => '',
                                                                                       );
        $employee['workings_holidays'][$month]['week_4'] = $employee['workings_holidays'][$month]['week_3'] = $employee['workings_holidays'][$month]['week_2'] = $employee['workings_holidays'][$month]['week_1'];
        $this->opm->save($employee);
        if($this->request->is('ajax'))
            die;
        return $employee['workings_holidays'][$month];
    }

    function workings_holidays_pdf(){
        if(!isset($_GET['print_pdf'])){
            $weeks = array();
            $month = date('m_Y');
            if(empty($_GET)){
                $weeks['week_1'] = $weeks['week_2'] = 1;
            } else{
                $weeks = $_GET;
                unset($weeks['month']);
            }
            if(isset($_GET['month']))
                $month = $_GET['month'];
            $employee = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())),array('employee_id','employee_name','mobile','workings_holidays'));
            $workings_weeks = $employee['workings_holidays'][$month];
            $html = '';
            $i = 0;
            $total = 0;
            foreach($weeks as $week => $value){
                $total_hours = 0;
                if(!isset($workings_weeks[$week])) continue;
                foreach($workings_weeks[$week] as $day_name => $day){
                    $hour_worked = (float)str_replace(':','.',$day['to_time']) - (float)str_replace(':','.',$day['from_time']) + (float)str_replace(':','.',$day['lunch_time']);
                    $total_hours += $hour_worked;
                    $total += $hour_worked;
                    $html .= '<tr class="'.($i%2==0 ? 'bg_1' : 'bg_2').'">';
                    if($i % 7 == 0)
                        $html .= '<td rowspan="8" class="center_text" style="background-color:#eeeeee">'.ucfirst(str_replace('_', ' ', $week)).'</td>';
                    $html .= '<td class="center_text">'.ucfirst($day_name).'</td>';
                    $html .= '<td class="center_text">'.$day['from_time'].'</td>';
                    $html .= '<td class="center_text">'.$day['to_time'].'</td>';
                    $html .= '<td class="center_text">'.$day['lunch_time'].'</td>';
                    $html .= '<td class="right_text">'.number_format($hour_worked,2).'</td>';
                    $html .= '</tr>';
                    $i++;
                    if($i % 7 == 0 && $i != 0){
                        $html .= '<tr class="'.($i%2==0 ? 'bg_1' : 'bg_2').'"><td colspan="4" style="font-size: 11px;" class="right_text">Sub Total:</td><td class="right_text">'.number_format($total_hours,2).'</td></tr>';
                        $html .= '<tr class="'.(($i+1)%2==0 ? 'bg_1' : 'bg_2').'" '.($i % 14 == 0 ? 'style="page-break-after:always;"' : '').'  ><td colspan="6" class="center_text">&nbsp;</td></tr>';
                    }
                }
            }
            $html .= '<tr class="'.($i%2==0 ? 'bg_1' : 'bg_2').'"><td colspan="5" style="font-weight:bold;" class="right_text">Total (hrs):</td><td class="right_text" style="color:red; font-size:20;font-weight:bold">'.number_format($total,2).'</td></tr>';
            $arr_data['left_info'] = array(
                                       'name'=>'<span class="bold_text">Name</span>:     '.(isset($employee['employee_name']) ? $employee['employee_name'] : '').'<br /><span class="bold_text">Mobile</span>:       '.(isset($employee['mobile']) ? $employee['mobile'] : '').'<br />',
                                       'address' => '&nbsp;',
                                       );
            $arr_data['title'] = array('Week','', 'In', 'Out', 'Lunch', 'Hrs. Worked');
            $arr_data['content'] = $html;
            $arr_data['report_name'] = 'Hrs.Worked Employee';
            $arr_data['report_file_name'] = 'Working_Hours_Report_'.md5(time());
            Cache::write('hrs_worked_employee',$arr_data);
        } else
            $arr_data = Cache::read('hrs_worked_employee');
        $this->render_pdf($arr_data);
    }
}