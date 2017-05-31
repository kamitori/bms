<?php
class LocationsController extends MobileAppController {
	var $modelName = 'Location';
	var $name = 'Locations';
	function beforeFilter() {
		parent::beforeFilter();
		if(!$this->request->is('ajax'))
			$this->set('arr_tabs', array(
			           			'general' => array('general'),
			           			'other'  => array('communications'),
			           			));
	}

	public function entry($id = '0', $num_position = -1) {
		$arr_tmp = $this->entry_init($id, $num_position, 'Location', 'locations');

		$this->selectModel('Setting');
		$arr_location_type = $this->Setting->select_option(array('setting_value' => 'location_type'), array('option'));
		$this->set('arr_location_type', $arr_location_type);

		$arr_stock_usage = $this->Setting->select_option(array('setting_value' => 'stock_usage'), array('option'));
		$this->set('arr_stock_usage', $arr_stock_usage);

		$arr_tmp1['Location'] = $arr_tmp;
		$this->data = $arr_tmp1;
		$arr_location_id = array();
		if (isset($arr_tmp['our_rep_id']))
			$arr_location_id[] = $arr_tmp['our_rep_id'];

		$this->selectModel('Country');
		$options = array();
		$options['countries'] = $this->Country->get_countries();
		$this->selectModel('Province');
		$options['provinces'] = $this->Province->get_all_provinces();
		$this->set('options',$options);
	}

	function auto_save( $field = '' ) {
		if (!empty($this->data)) {
			$arr_post_data = $this->data['Location'];
			$arr_save = $arr_post_data;

			$this->selectModel('Location');
			$arr_tmp = $this->Location->select_one(array('no' => (int) $arr_save['no'], '_id' => array('$ne' => new MongoId($arr_save['_id']))));
			if (isset($arr_tmp['no'])) {
				echo 'ref_no_existed';
				die;
			}

			$field = str_replace(array('data[Location][', ']'), '', $field);

			if (strlen(trim($arr_save['salesorder_id'])) > 0){
				$arr_save['salesorder_id'] = new MongoId($arr_save['salesorder_id']);

				$this->selectModel('Salesorder');
				$so = $this->Salesorder->select_one(array('_id' => new MongoId($arr_save['salesorder_id'])), array('_id', 'salesorder_date', 'payment_due_date'));

				// Kiểm tra xem có thay đổi work_end không,
				if ($field != '' && ( $field == 'work_end' || $field == 'work_end_hour' )) { // if($work_end != $arr_save['work_end_old'] ){
					// nếu có thì có thay đổi đúng không
					if ($work_end < strtotime('now')) {
						echo 'error_work_end';
						die;
					}

					if (is_object($so['payment_due_date']) && $work_end > ($so['payment_due_date']->sec + 23*3600 + 1800)) {
						echo 'work_end_payment_due_date';
						die;
					}

					$check_reload = true;
				}

				if( date('h', $work_end) == 0 ){
					$work_end = $work_end - 1800;
				}

			}

			if (!isset($arr_save['inactive']))
				$arr_save['inactive'] = 0;
			if (!isset($arr_save['bookable']))
				$arr_save['bookable'] = 0;
			if (!isset($arr_save['stockuse']))
				$arr_save['stockuse'] = 0;


			if (strlen(trim($arr_save['our_rep_id'])) > 0)
				$arr_save['our_rep_id'] = new MongoId($arr_save['our_rep_id']);

			if (strlen(trim($arr_save['company_id'])) > 0)
				$arr_save['company_id'] = new MongoId($arr_save['company_id']);

			if (strlen(trim($arr_save['company_name'])) > 0)
				$arr_save['company'] = $arr_save['company_name'];

			if (strlen(trim($arr_save['enquiry_id'])) > 0)
				$arr_save['enquiry_id'] = new MongoId($arr_save['enquiry_id']);

			if (strlen(trim($arr_save['quotation_id'])) > 0)
				$arr_save['quotation_id'] = new MongoId($arr_save['quotation_id']);

			if (strlen(trim($arr_save['job_id'])) > 0)
				$arr_save['job_id'] = new MongoId($arr_save['job_id']);

			if (strlen(trim($arr_save['purchaseorder_id'])) > 0)
				$arr_save['purchaseorder_id'] = new MongoId($arr_save['purchaseorder_id']);


			$this->selectModel('Location');
			if ($this->Location->save($arr_save)) {
				if( isset($check_reload) ){
					$this->layout = 'ajax';
					$arr_post_data['Location'] = $arr_post_data;
					$this->data = $arr_post_data;
					$this->render('entry_udpate_date');
				}else{
					echo 'ok';die;
				}

			} else {
				echo 'Error: ' . $this->Location->arr_errors_save[1];
				die;
			}
		}
	}

	function delete($id = '') {
		$this->redirect('/mobile/locations/entry/' . $this->Location->mongo_id_after_save);
	}

	function addresses_default_key() {
		$arr_save = $this->data['Location'];
		$arr_save['_id']= $this->get_id();
		$this->selectModel('Location');
		if ($this->Location->save($arr_save)) {
			echo 'ok';die;
		}
	}

	function lists( $content_only = '' ) {
		$this->selectModel('Location');
		$limit = 20; $skip = 0;
		// dùng cho sort
		$sort_field = 'no';
		$sort_type = -1;
		if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
			if( $_POST['sort']['type'] == 'desc' ){
				$sort_type = -1;
			}
			$sort_field = $_POST['sort']['field'];
			$this->Session->write('locations_lists_search_sort', array($sort_field, $sort_type));

		}elseif( $this->Session->check('locations_lists_search_sort') ){
			$session_sort = $this->Session->read('locations_lists_search_sort');
			$sort_field = $session_sort[0];
			$sort_type = $session_sort[1];
		}
		$arr_order = array($sort_field => $sort_type);
		$this->set('sort_field', $sort_field);
		$this->set('sort_type', ($sort_type === 1)?'asc':'desc');

		$this->selectModel('Location');
		$this->set('model_location', $this->Location);

		// dùng cho điều kiện
		$cond = array();
		if( $this->Session->check('locations_entry_search_cond') ){
			$cond = $this->Session->read('locations_entry_search_cond');
		}

		// dùng cho phân trang
		if(isset($_POST['offset'])){
			$skip = $_POST['offset'];
		}
		// query
		$arr_locations = $this->Location->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'limit' => $limit,
			'skip' => $skip
		));
		$this->set('arr_locations', $arr_locations);
		$this->selectModel('Setting');
		$this->set('arr_locations_type', $this->Setting->select_option(array('setting_value' => 'locations_type'), array('option')));
		//$this->set('arr_locations_status', $this->Setting->select_option(array('setting_value' => 'locations_status'), array('option')));
		$this->set('arr_priority', $this->Setting->select_option(array('setting_value' => 'lists_priority'), array('option')));

		$this->selectModel('Equipment');
		$arr_equipment = $this->Equipment->select_list( array(
			'arr_field' => array('_id', 'name'),
			'arr_order' => array('name' => 1)
		));
		$this->set( 'arr_equipment', $arr_equipment );
		if($content_only!=''){
			$this->render("lists_ajax");
		}
	}


	public function add() {
		$this->selectModel('Location');
		$arr_save = array();

		$this->Location->arr_default_before_save = $arr_save;
		if ($this->Location->add())
			$this->redirect('/mobile/locations/entry/' . $this->Location->mongo_id_after_save);
		die;
	}

	public function before_save($field,$value,$id, $options = array()){
		$arr_return = array();
		if($field == 'location_default'){
			$this->selectModel('Location');
			$location = $this->Location->select_one(array('_id' => new MongoId($id)),array('company_id'));
			if(isset($location['company_id']) && is_object($location['company_id'])){
				$this->Location->collection->update(array(
				                                   	'deleted' => false,
				                                   	'company_id' => $location['company_id'],
				                                   	'location_default' => 1
			                                   ),array(
			                                   		'$set' => array('location_default' => 0)
			                                   ),array('multiple' => true)
			                                   );
				$this->selectModel('Company');
				$this->Company->save(array('_id' => $location['company_id'], 'location_default_id' => new MongoId($id)));
			}
		}
		return $arr_return;
	}

    public function general(){
        $tmp = 0;
        $cat_select = array();
        $stockcurrent = array();
        $total = 0;
        $total_cost = 0;
        $total_sell = 0;
        $total_profit = 0;
        $total_onso = 0;
        $module_id = $this->get_id();
       // $this->selectModel('Location');
        if (isset($module_id)) {
            $prokey = array('total_stock');
            $this->selectModel('Product');
            $arr_query = $this->Product->select_all(array(
                'arr_where' => array(
                    'locations.location_id' => new MongoId($module_id)
                ),
            ));
            $newdata = $temp = array();
            $newdata = $arr_query;
            $arr_product = iterator_to_array($arr_query);
            //pr($arr_product);die;
            /*foreach ($newdata as $keys => $values) {
                if(isset($values['cost_price']) && isset($vv['deleted']) && !$vv['deleted'] )
                    $total_cost += $values['cost_price'];
                if(isset($values['sell_price']) && isset($vv['deleted']) && !$vv['deleted'])
                    $total_sell += $values['sell_price'];
                if(isset($values['profit']) && isset($vv['deleted']) && !$vv['deleted'])
                    $total_profit += $values['profit'];


                foreach ($values['locations'] as $key => $vv) {
                    if(isset($vv['location_id']) && $vv['location_id'] == $module_id && isset($vv['deleted']) && !$vv['deleted'] && isset($vv['total_stock'])){
                        $stockcurrent[$keys] = array_merge($values, $vv);
                        if(isset($vv['min_stock']) && (int)$vv['total_stock'] < (int)$vv['min_stock']&&isset($vv['min_stock']) &&isset($vv['total_stock'])){
                            $stockcurrent[$keys]['low'] =1;
                        }
                        $tmp = $this->on_so((string)$values['_id']);
                        $stockcurrent[$keys]['on_so'] = $tmp;
                        $total_onso += $tmp;

                        $stockcurrent[$keys]['avalible'] =0;
						$stockcurrent[$keys]['product_id'] = $values['_id'];
                        if(isset($stockcurrent[$keys]['in_use'])){
                            $stockcurrent[$keys]['avalible'] +=(float)$stockcurrent[$keys]['in_use'];
                        }
                        if(isset($stockcurrent[$keys]['in_assembly'])){
                            $stockcurrent[$keys]['avalible'] +=(float)$stockcurrent[$keys]['in_assembly'];
                        }
                        if(isset($stockcurrent[$keys]['on_so'])){
                            $stockcurrent[$keys]['avalible'] +=(float)$stockcurrent[$keys]['on_so'];
                        }
                        if(isset($vv['total_stock']))
                            $total += (float)$vv['total_stock'];

                         $stockcurrent[$keys]['avalible'] = $stockcurrent[$keys]['total_stock'] -  $stockcurrent[$keys]['avalible'];
                    }

                }

            }*/

            $subdatas['stockcurrent'] = $stockcurrent;
            $this->set('total_stock', $total);
            $this->set('sell_price', $total_sell);
            $this->set('cost_price', $total_cost);
            $this->set('profit', $total_profit);
            $this->set('on_so', $total_onso);
            $this->set('subdatas', $subdatas);
            $this->set('arr_product', $arr_product);
        }


        //die;
    }

}