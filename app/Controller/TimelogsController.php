<?php

App::uses('AppController', 'Controller');

class TimelogsController extends AppController {

    var $modelName = 'Timelog';
    var $name = 'Timelogs';
    public function beforeFilter() {
        $this->selectModel('Timelog');
        $this->opm = $this->Timelog;
        parent::beforeFilter();

        $this->set('title_entry', 'Timelogs');
    }

    function entry($id = '0', $num_position = -1) {
        $arr_tmp = $this->entry_init($id, $num_position, 'Timelog', 'Timelogs');
        $arr_tmp['date'] = (is_object($arr_tmp['date'])) ? date('m/d/Y', $arr_tmp['date']->sec) : '';

        $arr_tmp1['Timelog'] = $arr_tmp;
        $this->data = $arr_tmp1;

        $this->selectModel('Setting');
        $this->set('arr_category', $this->Setting->select_option(array('setting_value' => 'timelogs_category'), array('option')));
        $this->set('arr_employee_type', $this->Setting->select_option(array('setting_value' => 'contacts_type'), array('option')));

        $this->selectModel('Contact');
        $contact_list = $this->Contact->select_list(array(
            'arr_field' => array('_id', 'first_name', 'last_name')
        ));
        $this->set('arr_employee', $contact_list);
        $this->expense();
        $this->show_footer_info($arr_tmp);
    }

    function lists() {
        $this->selectModel('Timelog');
        $limit = LIST_LIMIT;
        $skip = 0;
        $sort_field = 'no';
        $sort_type = 1;
        if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
            if( $_POST['sort']['type'] == 'desc' ){
                $sort_type = -1;
            }
            $sort_field = $_POST['sort']['field'];
            $this->Session->write('timelogs_lists_search_sort', array($sort_field, $sort_type));

        }elseif( $this->Session->check('timelogs_lists_search_sort') ){
            $session_sort = $this->Session->read('timelogs_lists_search_sort');
            $sort_field = $session_sort[0];
            $sort_type = $session_sort[1];
        }
        $arr_order = array($sort_field => $sort_type);
        $this->set('sort_field', $sort_field);
        $this->set('sort_type', ($sort_type === 1)?'asc':'desc');
        // dùng cho điều kiện
        $cond = array();
        if( $this->Session->check('timelogs_entry_search_cond') ){
            $cond = $this->Session->read('timelogs_entry_search_cond');
        }
        $cond = array_merge($cond, $this->arr_search_where());
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
        $arr_timelogs = $this->Timelog->select_all(array(
            'arr_where' => $cond,
            'arr_order' => $arr_order,
            'limit' => $limit,
            'skip' => $skip
        ));
        $this->set('arr_timelogs', $arr_timelogs);

        $total_page = $total_record = $total_current = 0;
        if( is_object($arr_timelogs) ){
            $total_current = $arr_timelogs->count(true);
            $total_record = $arr_timelogs->count();
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
        if($this->Session->check('jobs_entry_search_cond')){
            $cond = $this->Session->read('jobs_entry_search_cond');
            if(isset($cond['work_end']['$gte']))
                $this->set('date_from',$this->Timelog->format_date($cond['work_end']['$gte']->sec));
            if(isset($cond['work_end']['$lte']))
                $this->set('date_to',$this->Timelog->format_date($cond['work_end']['$lte']->sec));
        }
        $this->set('sum', $total_record);
    }

    function auto_save() {
        if (!empty($this->data)) {
            $arr_post_data = $this->data['Timelog'];
            $arr_save = $arr_post_data;

            $date = $this->Common->strtotime($arr_save['date'] . ' 00:00:00');
            $arr_save['date'] = new MongoDate($date);
            if(isset($arr_save['job_id']) && strlen($arr_save['job_id'])==24)
                $arr_save['job_id'] = new MongoId($arr_save['job_id']);
            if(isset($arr_save['task_id']) && strlen($arr_save['task_id'])==24)
                $arr_save['task_id'] = new MongoId($arr_save['task_id']);
            $this->selectModel('Timelog');
            if ($this->Timelog->save($arr_save)) {
                echo 'ok';
            } else {
                echo 'Error: ' . $this->Timelog->arr_errors_save[1];
            }
        }
        die;
    }

    function entry_caltime() {
        $data = $this->data['Timelog'];
        $start_time = explode(':', $data['start_time']);
        $finish_time = explode(':', $data['finish_time']);
        $time1 = mktime($finish_time[0], $finish_time[1]);
        $time2 = mktime($start_time[0], $start_time[1]);
        $diff = $time1 - $time2;
        $diff += strtotime(date('Y-m-d'));
        $h = date('H', $diff);
        $m = date('i', $diff);
        $new_time = $h . ':' . $m;
        $total_time = date('H:i', strtotime($new_time));
        echo $total_time;
        die;
    }

    function _add_get_info_save() {
        $this->selectModel('Timelog');
        $arr_tmp = $this->Timelog->select_one(array(), array(), array('no' => -1));

        $arr_save = array(
            // Employee details
            'no' => 1,
            'employee_name' => '',
            'employee_type' => '',
            // Timelog details
            'date' => new MongoDate(),
            'start_time' => '00:00',
            'finish_time' => '00:00',
            'or_entered_time' => '00:00',
            'total_time' => '00:00',
            'category' => '',
            'billable' => '',
            'billed' => '',
            // job/stage/task details
            'job_no' => '',
            'job_name' => '',
            'stage_no' => '',
            'stage_name' => '',
            'task_no' => '',
            'task_name' => '',
            'customer' => '',
            'comment' => ''
        );
        if (isset($arr_tmp['no'])) {
            $arr_save['no'] = $arr_tmp['no'] + 1;
        }

        return $arr_save;
    }

    function add() {
        $arr_save = $this->_add_get_info_save();
        if ($this->Timelog->save($arr_save)) {
            $this->redirect('/timelogs/entry/' . $this->Timelog->mongo_id_after_save);
        } else {
            echo 'Error: ' . $this->Timelog->arr_errors_save[1];
        }
        die;
    }

    function delete($id = 0) {
        $arr_save['_id'] = $id;
        $arr_save['deleted'] = true;

        $this->selectModel('Timelog');
        if ($this->Timelog->save($arr_save)) {
            $this->redirect('/timelogs/entry');
        } else {
            echo 'Error: ' . $this->Timelog->arr_errors_save[1];
        }
    }

    function lists_delete($id = 0) {
        $arr_save['_id'] = $id;
        $arr_save['deleted'] = true;

        $this->selectModel('Timelog');
        if ($this->Timelog->save($arr_save)) {
            echo 'ok';
            die();
        } else {
            echo 'Error: ' . $this->Timelog->arr_errors_save[1];
        }
    }
    function expense(){
        $id = $this->get_id();
        $this->selectModel('Timelog');
        $expense = $this->Timelog->select_one(array('_id'=> new MongoId($id)),array('_id','expenses'));
        if(!isset($expense['expenses']))
            $expense['expenses'] = array();
        $this->set('expenses',$expense['expenses']);
        $this->set('total_expense',$this->cal_sum($expense['expenses']));
    }
    function expense_add(){

        $id = $this->get_id();
        $this->selectModel('Timelog');
        $expenses = $this->Timelog->select_one(array('_id'=> new MongoId($id)),array('_id','expenses'));
        if(isset($_POST['product_id'])){
            $this->selectModel('Product');
            $product = $this->Product->select_one(array('_id'=> new MongoId($_POST['product_id'])),array('cost_price','name','code','category'));
            $arr_save = array(
                              'expense_id'=> $product['_id'],
                              'code'=>$product['code'],
                              'expense_name'=>$product['name'],
                              'expense_date'=> new MongoDate(),
                              'billable'=>0,
                              'category'=>(isset($product['category']) ? $product['category'] : ''),
                              'cost_price'=>(isset($product['cost_price']) ? (float)$product['cost_price'] : ''),
                              'quantity'=>1,
                              'sub_total'=>(isset($product['cost_price']) ? (float)$product['cost_price'] : ''),
                            );
            $expenses['expenses'][$_POST['key']] = $arr_save;
        } else{
            $arr_save = array(
                              'expense_id'=>'',
                              'code'=>'',
                              'expense_name'=>'',
                              'expense_date'=> new MongoDate(),
                              'billable'=>0,
                              'category'=>'',
                              'cost_price'=>0,
                              'quantity'=>1,
                              'sub_total'=>0,
                            );
            $expenses['expenses'][] = $arr_save;
        }

        $this->Timelog->save($expenses);
        $this->set('expenses',$expenses['expenses']);
        $this->render('expense');
    }
    function expense_delete($key){
        $id = $this->get_id();
        $this->selectModel('Timelog');
        $expenses = $this->Timelog->select_one(array('_id'=> new MongoId($id)),array('_id','expenses'));
        if(isset($expenses['expenses'][$key]))
            unset($expenses['expenses'][$key]);
        $expenses['total_expense'] = $this->cal_sum($expenses['expenses']);
        echo json_encode(array('total_expense'=>$expenses['total_expense']));
        $this->Timelog->save($expenses);
        die;
    }
    function cal_sum($expenses){
        $sum = 0;
        foreach($expenses as $expense)
            $sum += $expense['sub_total'];
        return $sum;
    }
    function expense_save(){
        if(isset($_POST)){
            $field = $_POST['name'];
            $value = $_POST['value'];
            $key = $_POST['key'];
            $id = $this->get_id();
            $this->selectModel('Timelog');
            $expenses = $this->Timelog->select_one(array('_id'=> new MongoId($id)),array('_id','expenses'));
            $data = $expenses['expenses'][$key];
            $data = array_merge( $data, array($field=>$value));
            if($field == 'quantity' || $field == 'cost_price'){
                $data[$field] = (float)$value;
                $data['sub_total'] = $data['cost_price'] * $data['quantity'];
            } else if($field == 'expense_date')
                $data[$field] = new MongoDate($this->Common->strtotime($value.' 00:00:00'));
            $expenses['expenses'][$key] = array_merge($expenses['expenses'][$key],$data);
            $expenses['total_expense'] = $this->cal_sum($expenses['expenses']);
            $this->Timelog->save($expenses);
            $data = array_merge($data,array('total_expense'=>$expenses['total_expense']));
            echo json_encode($data);
        }die;
    }
}