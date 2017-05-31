<?php
App::uses('CakeLog', 'Utility');
class UpdatecostingsShell extends AppShell {
    public function main($product_id = '') {
		$arr_contact['contact_id'] = new MongoId('100000000000000000000000');
		$arr_contact['contact_name'] = $arr_contact['full_name'] = 'System Admin';
		$arr_contact['first_name'] = 'System';
		$arr_contact['last_name'] = 'Admin';
		$_SESSION['arr_user'] =  $arr_contact;
        $queues = $this->getQueue($product_id);
        if( $queues->count() ) {
            foreach($queues as $queue) {
                $this->processQueue($queue);
            }
        }
        $this->mongo_disconnect();
        $this->out($queues->count()." costing queue(s) found", 1, Shell::QUIET);
    }

    function getQueue($product_id) {
        $arr_where = array(
                        'status' => 'New'
                    );
        if( strlen($product_id) == 24 ) {
            $arr_where['product_id'] = new MongoId($product_id);
        }
        $this->selectModel('Costingqueue');
        return $this->Costingqueue->select_all(array(
                                                    $arr_where,
                                                    'arr_field' => array('costings','product_id'),
                                                    'arr_order' => array('_id' => 1)
                                                ));
    }

    function processQueue($queue){
    	$this->selectModel('Product');
    	foreach($queue['costings'] as $costing_id){
    		$arr_data = $this->Product->costings_data($costing_id);
    		$this->Product->save(array('_id' => $costing_id,'cost_price' => $arr_data['pricingsummary']['cost_price']));
    		$queue['cost_price'][] = $arr_data['pricingsummary']['cost_price'];
    		$parent_costings = $this->Product->get_data_costing($costing_id);
    		if(!empty($parent_costings['useon'])) {
    			foreach($parent_costings['useon'] as $parent) {
    				$arr_data = $this->Product->costings_data($parent['_id']);
    				$this->Product->save(array('_id' => $parent['_id'],'cost_price' => $arr_data['pricingsummary']['cost_price']));
    				unset($arr_data);
    			}
    		}
    	}
    	$queue['status'] = 'Done';
    	return $this->Costingqueue->save($queue);
    }
}