<?php

App::uses('AppController', 'Controller');
App::uses('ContactsController', 'Controller');

class ServicesController extends AppController {

    public function get_some_products(){
        $this->autoRender = false;

        $arr_products = array();

        $this->selectModel('Product');

        $products = $this->Product->select_all(array('arr_where' => array('deleted' => 0, 'special_order' => 1),
                                                    'limit' => 10,
                                                    'arr_order' => array('sku' => 1)
                                                    // 'skip' => rand(0, 3320)
                                            ));
        if($products->count()){
            foreach ($products as $key => $value) {
                // pr($value);
                $p = array();
                $p['_id'] = $value['_id'];
                $p['name'] = $value['name'];
                $p['product_description'] = isset($value['product_description'])?$value['product_description']:'';
                
                $products_upload = isset($value['products_upload'])?$value['products_upload']:'';
                if(is_array($products_upload)){
                    foreach ($products_upload as $kk => $vv) {
                        if($vv['deleted']==false && $vv['path']!='' ){
                            $products_upload = $vv['path'];
                            // break;
                        }
                    }
                }
                $p['products_upload'] = URL.$products_upload;

                $p['sell_price'] = $value['sell_price'];

                $arr_products[] = $p;
            }
        }

        $arr_result = ['status'=>'ok', 'arr_products'=>$arr_products];

        $callback = isset($_GET['callback']) ? $_GET['callback'] : null;
        if($callback != null)
        {
            header("Content-Type: application/json; charset=utf-8");
            echo $callback.'('.json_encode($arr_result).')';
            exit;
        }

        echo json_encode($arr_result);
        die;

    }
    
    public function perform_shift($action)
    {        
        $arr_result = ['error'=>0, 'message'=>'Done.'];

        $contact_id = isset($_GET['contact_id']) ? $_GET['contact_id'] : '';
        $contact_pwd = isset($_GET['contact_pwd']) ? $_GET['contact_pwd'] : '';

        $this->selectModel('Contact');
        $arr_where = array();
        $arr_where['_id'] = new MongoId($contact_id);
        $arr_where['password'] = $contact_pwd;
        $concact = $this->Contact->select_one($arr_where);
        if($concact)
        {
            $this->selectModel('WorkingHour');
            $arr_where = array();
            $arr_where['contact_id'] = new MongoId($contact_id);
            $arr_where['year_month'] = (int)date('Ym');
            $wh = $this->WorkingHour->select_one($arr_where);

            //Instantiate
            $contactObj = new ContactsController();
            //Load model, components...
            $contactObj->constructClasses();

            if(!isset($wh['_id'])){
                $contactObj->buildWorkingHourNew($arr_where['contact_id'],$arr_where['year_month']);
                $wh = $this->WorkingHour->select_one($arr_where);
            }

            $this->selectModel('Setting');
            $arr_option = $this->Setting->select_one(array('setting_value' => 'time_zone'), array('option'));
            $default_timezone = 'Canada/Mountain';
            $different_minutes = 0;
            if(isset($arr_option['option']))
            {
                foreach ($arr_option['option'] as $key => $value) {
                    if ($value['deleted'])
                        continue;
                    if($value['name'] == 'default_timezone')
                    {
                        $default_timezone = $value['value'];
                    }
                    elseif($value['name'] == 'different_minutes')
                    {
                        $different_minutes = $value['value'];
                    }
                }                
            }    

            date_default_timezone_set($default_timezone); 
            $exact_time = time() + intval($different_minutes)*60;
            $time = (string)date('H:i', $exact_time);
            $daynum = (int)date('d');

            if( !isset($wh['day_'.$daynum]) ){ //update

                if($action == 'start')
                {
                    $wh['day_'.$daynum]['work_start'] = $time; 
                    $wh['day_'.$daynum]['work_end'] = '';
                }
                else
                {
                    $wh['day_'.$daynum]['work_start'] = $time;
                    $wh['day_'.$daynum]['work_end'] = $time;
                }                
                
                $wh['day_'.$daynum]['lunch'] = '';
                $wh['day_'.$daynum]['off'] = '';
                $this->WorkingHour->save($wh);
            }
            elseif( isset($wh['day_'.$daynum]) && empty($wh['day_'.$daynum]['work_'.$action]) ) {
                if($action == 'end' && empty($wh['day_'.$daynum]['work_start']))
                {
                    $wh['day_'.$daynum]['work_start'] = $time;    
                }
                $wh['day_'.$daynum]['work_'.$action] = $time;
                // pr($wh);
                $this->WorkingHour->save($wh);
            }
            else
            {
                $arr_result = ['error'=>1, 'message'=>'Your '.strtoupper($action).' in your shift today was already performed.'];            
            }            
        }
        else
        {
            $arr_result = ['error'=>1, 'message'=>'Can\'t not operate this action with your credential.'];
        }
        
        $this->autoRender = false;

        $callback = isset($_GET['callback']) ? $_GET['callback'] : null;
        if($callback != null)
        {
	        header("Content-Type: application/json; charset=utf-8");
	        echo $callback.'('.json_encode($arr_result).')';
	        exit;
        }

        echo json_encode($arr_result);
		die;

    }

    //update tb_product, field option_group, Vegige ==> Veggie
    function update_product_option_group($option_group_old, $option_group_new)
    {

        $this->selectModel('Product');

        $query = $this->Product->select_all(array(
                                 'arr_where'=>  ["options"=> array('$elemMatch' => array('option_group'=>'Vegige')), 'deleted'=>array('$ne'=>true)],
                                 'arr_field'=>  ['_id', 'options'],
                                 'limit' => 99999
                                 ));
        echo 'count:'.$query->count();
        if($query->count() > 0)
        {
            foreach ($query as $key => $value) {

                $options = $value['options'];
                foreach ($options as $key1 => $value1) {
                    if($value1['option_group'] == $option_group_old)
                    {
                        $value1['option_group'] = $option_group_new;   
                    }
                    $options[$key1] = $value1;
                }
                $this->Product->collection->update(
                               array('_id'=> new MongoId($value['_id']),
                                    'deleted'=>array('$ne'=>true)
                                ),
                               array('$set'=>array(
                                     'options'=> $options
                                     )
                               ),
                               array('multiple'=>true)
                           );
                
            }

        }
        exit;
    }    

    //update tb_product, field option_group, change group of "Veggie on the Side" to "Veggie" group
    function update_product_option_group_by_id($option_product_id, $option_group_new)
    {

        $this->selectModel('Product');

        $query = $this->Product->select_all(array(
                                 'arr_where'=>  ["options"=> array('$elemMatch' => array('product_id'=>new MongoId($option_product_id))), 'deleted'=>array('$ne'=>true)],
                                 'arr_field'=>  ['_id', 'options'],
                                 'limit' => 99999
                                 ));
        echo 'count:'.$query->count();
        if($query->count() > 0)
        {
            foreach ($query as $key => $value) {

                $options = $value['options'];
                foreach ($options as $key1 => $value1) {
                    if($value1['product_id'] == $option_product_id)
                    {
                        $value1['option_group'] = $option_group_new;       
                    }                    
                    $options[$key1] = $value1;
                }
                $this->Product->collection->update(
                               array('_id'=> new MongoId($value['_id']),
                                    'deleted'=>array('$ne'=>true)
                                ),
                               array('$set'=>array(
                                     'options'=> $options
                                     )
                               ),
                               array('multiple'=>true)
                           );
                
            }

        }
        exit;
    }

    public function add_permission_detail($id) {

        $this->selectModel('Permission');
        $permissions = $this->Permission->select_one(array('_id' => new MongoId($id)));
        if($permissions)
        {
            $col = 1;
            $data = $permissions;

            $group = 'for_the_current_job';

            $line = array();


            if (isset($permissions['option_list'][$col][$group])) {
                $line = $permissions['option_list'][$col][$group];
            }
            $index = count($line);

            $line[$index]['url'] = '';
            $line[$index]['name'] = 'Create Invoice from Combine Sales orders';
            $line[$index]['type'] = 'add';
            $line[$index]['codekey'] = 'create_invoice_from_combine_salesorders';
            $line[$index]['flag'] = '';
            $line[$index]['description'] = '';
            $line[$index]['belong_to'] = ["salesinvoices_@_entry_@_edit"];

            $data['option_list'][$col][$group] = $line;
            // pr($data);exit;
            if ($this->Permission->save($data)) {
                echo 'Success';
            }            
        }
    
        die;
    }          
}