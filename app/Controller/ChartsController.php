<?php
App::uses('AppController', 'Controller');
class ChartsController extends AppController {

	var $name = 'Charts';
    var $arrWidth = array('25' => 'col-md-3', '50' => 'col-md-6', '75' => 'col-md-9', '100' => 'col-md-12');

	public function beforeFilter()
    {
        $this->layout = 'ajax';
        $this->autoRender = false;
        $this->response->type('json');
        parent::beforeFilter();
	}

    public function entry()
    {
        $this->autoRender = true;
        $this->response->type('html');
        $currentUser = $this->Session->read('arr_user');

        $this->selectModel('Contact');
        $users = $this->Contact->select_all(array(
                'arr_where' => array(
                    'is_employee' => 1
                ),
                'arr_order' => array(
                    'first_name' => 1
                ),
                'arr_field' => array(
                    'first_name',
                    'last_name',
                    'email'
                )
        ));

        $this->selectModel('Chart');
        $charts = $this->Chart->select_all(array(
            'arr_where' => array(
                'shares' => array(
                    '$elemMatch' => array( 'employee_id' => new MongoId($currentUser['_id']))
                )
            ),
            'arr_field' => array('_id', 'name', 'width', 'shares')
        ));

        $arrCharts = array();
        foreach($charts as $chart) {
            foreach($chart['shares'] as $share) {
                if( $share['employee_id'] != $currentUser['_id'] ) continue;
                $orderNo = $share['order_no'];
                break;
            }
            if( isset($arrCharts[$orderNo]) ) {
                $orderNo .= '.'.$orderNo;
            }
            $arrCharts[$orderNo] = array(
                    'name'  => $chart['name'],
                    'width' => $this->arrWidth[$chart['width']],
                    'id'    => $chart['_id'],
                    'color' => '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT)
                );
        }
        ksort($arrCharts);

        $this->set('currentUser', $currentUser);
        $this->set('users', $users);
        $this->set('charts', $arrCharts);
    }

    public function draw_chart($chartId)
    {
        if( !$this->request->is('ajax')  ){
            $this->redirect('/');
        }
        $arrReturn = array('status' => 'error');
        if( strlen($chartId) == 24 ) {
            $currentUser = $this->Session->read('arr_user');
            $this->selectModel('Chart');
            $chart = $this->Chart->select_one(array(
                '_id' => new MongoId($chartId),
                'shares' => array(
                    '$elemMatch' => array( 'employee_id' => new MongoId($currentUser['_id']))
                )
            ));
            if( !empty($chart) ) {
                $this->selectModel('Company');
                $dateFields = array(
                    'Quotation' => 'quotation_date',
                    'Salesorder' => 'order_date',
                    'Salesinvoice' => 'invoice_date'
                );
                $orgirinMinimum = Cache::read('minimum');
                $product = Cache::read('minimum_product');
                if(!$orgirinMinimum){
                    $this->selectModel('Product');
                    $product = $this->Stuffs->select_one(array('value'=>"Minimum Order Adjustment"),array('product_id'));
                    $p = $this->Product->select_one(array('_id'=> new MongoId($product['product_id'])),array('sell_price'));
                    $orgirinMinimum = $p['sell_price'];
                    Cache::write('minimum',$orgirinMinimum);
                    Cache::write('minimum_product',$product);
                }
                $productId = $product['product_id'];
                $arrCompanies = Cache::read('arr_companies');
                if(!$arrCompanies) {
                    $arrCompanies = array();
                }

                $fromDate = date('Y-m-d', $chart['from_date']->sec).' 00:00:00';
                $toDate = date('Y-m-d', $chart['to_date']->sec).' 00:00:00';

                $arrYAxis = array('sum_amount' => 'Total', 'sum_sub_total' => 'Total ex. tax');

                $arrReturn = array(
                                    'status'        => 'ok',
                                    'name'          => $chart['name'],
                                    'description'   => $chart['description'],
                                    'title'         => date('d M, Y', $chart['from_date']->sec).' - '.date('d M, Y', $chart['to_date']->sec),
                                    'type'          => $chart['type'],
                                    'xAxis'         => [],
                                    'yAxisText'     => $arrYAxis[ $chart['yaxis'] ],
                                );

                $data = array();
                if( $chart['xaxis'] == 'day' ) {
                    $dateRange  = new DatePeriod(
                         new DateTime($fromDate),
                         new DateInterval('P1D'),
                         new DateTime($toDate)
                    );
                    foreach($dateRange as $date) {
                        $data[$date->format('Y-m-d')] = 0;
                        $arrReturn['xAxis'][] = $date->format('Y-m-d');
                    }

                } else if ( $chart['xaxis'] == 'month' ) {
                    $dateRange  = new DatePeriod(
                         new DateTime($fromDate),
                         new DateInterval('P1M'),
                         new DateTime($toDate)
                    );
                    foreach($dateRange as $date) {
                        $data[$date->format('Y-m')] = 0;
                        $arrReturn['xAxis'][] = $date->format('Y-m');
                    }
                } else {
                    $dateRange  = new DatePeriod(
                         new DateTime($fromDate),
                         new DateInterval('P1Y'),
                         new DateTime($toDate)
                    );
                    foreach($dateRange as $date) {
                        $data[$date->format('Y')] = 0;
                        $arrReturn['xAxis'][] = $date->format('Y');
                    }
                }

                foreach($chart['objects'] as $object) {
                    $module = ucfirst($object['module']);
                    $this->selectModel($module);

                    $dateField = $dateFields[ $module ];
                    $arrWhere = array(
                                    $dateField => array(
                                            '$gte' => $chart['from_date'],
                                            '$lte' => $chart['to_date'],
                                        )
                                );
                    foreach($object['conditions'] as $condition) {
                        $arrWhere[ $condition['field'] ] = array(
                                        $condition['operator'] => $condition['values']
                                    );
                    }
                    $results = $this->$module->select_all(array(
                        'arr_where' => $arrWhere,
                        'arr_field' => array('sum_amount', 'sum_sub_total', 'company_id', 'taxval', 'invoice_status', $dateField)
                    ));
                    $obj = $data;
                    foreach($results as $result) {
                        $companyId = $result['company_id'];
                        $minimum = $orgirinMinimum;
                        if(isset($arrCompanies[(string)$companyId])){
                            $minimum = $arrCompanies[(string)$companyId];
                        } else if(is_object($companyId)){
                            $company = $this->Company->select_one(array('_id'=>$companyId),array('pricing'));
                            if(isset($company['pricing'])){
                                foreach($company['pricing'] as $pricing){
                                    if(isset($pricing['deleted'])&&$pricing['deleted']) continue;
                                    if((string)$pricing['product_id']!=(string)$productId) continue;
                                    if(!isset($pricing['price_break']) || empty($pricing['price_break'])) continue;
                                    $price_break = reset($pricing['price_break']);
                                    $minimum =  (float)$price_break['unit_price'];
                                    $arrCompanies[(string)$companyId] = $minimum;
                                    break;
                                }
                            }
                        }

                        list($year, $m, $day) = explode('-', date('Y-m-d', $result[$dateField]->sec));
                        $month = date('M', $result[$dateField]->sec);
                        if( $chart['xaxis'] == 'day' ) {
                            $originKey = $m.'-'.$day;
                            $key = $month.' '.$day;
                        } else if( $chart['xaxis'] == 'month' ) {
                            $originKey = $year.'-'.$m;
                            $key = $month.' '.$year;
                        } else {
                            $originKey = $year;
                            $key = $year;
                        }

                        if( $module == 'Salesinvoice' && $result['invoice_status'] == 'Credit' && $result['sum_sub_total'] > 0) {
                            $result['sum_sub_total'] *= -1;
                            $result['sum_amount'] *= -1;
                        } else if( $result['sum_sub_total'] < $minimum ) {
                            $result['sum_sub_total'] = $minimum;
                            $result['sum_amount'] = $result['sum_sub_total'] * (1 + $result['taxval'] / 100);
                        }

                        $amount = $result[ $chart['yaxis'] ];
                        if( !isset($obj[$originKey]) ) {
                            $obj[$originKey] = $amount;
                        } else {
                            $obj[$originKey] += $amount;
                        }
                    }
                    ksort($obj);
                    $arrReturn['objects'][] = array(
                                        'name'  => $object['name'],
                                        'color' => $object['color'],
                                        'data'  => array_values($obj)
                                    );
                }
            }
        }

        $json = json_encode($arrReturn);
        $this->response->body($json);
    }

    public function add_chart()
    {
        if( !$this->request->is('ajax')  ){
            $this->redirect('/');
        }
        $currentUser = $this->Session->read('arr_user');
        $this->selectModel('Chart');
        $arrPost = $_POST;
        $chart = array_merge(array(
            'name' => '',
            'description' => '',
            'type' => 'line',
            'xaxis' => 'day',
            'yaxis' => 'sum_sub_total',
            'range' => date('G d, Y', strtotime('- 7 days')).' - '.date('G d, Y'),
            'width' => 25,
            'objects' => array(

            ),
            'shares' => array(
            )
        ), $arrPost);
        $lastChart = $this->Chart->collection->aggregate(array(
                array('$unwind' => '$shares'),
                array('$match' => array(
                        'shares.employee_id' => new MongoId($currentUser['_id'])
                    )),
                array('$sort' => array('shares.order_no' => -1)),
                array(
                    '$project' =>array('shares'=> '$shares')
                ),
                array('$limit' => 1)
        ));
        $orderNo = 0;
        if( isset($lastChart['result'][0]) ) {
            $orderNo = $lastChart['result'][0]['_id']['shares']['order_no'];
        }
        $shares = array(
                    array(
                        'employee_id' => new MongoId($currentUser['_id']),
                        'order_no' => ++$orderNo
                    )
                );
        foreach($chart['shares'] as $id => $check) {
            if( strlen($id) != 24 ) {
                continue;
            }
            $id =  new MongoId($id);
            $lastChart = $this->Chart->collection->aggregate(array(
                    array('$unwind' => '$shares'),
                    array('$match' => array(
                            'shares.employee_id' => $id
                        )),
                    array('$sort' => array('shares.order_no' => -1)),
                    array(
                        '$project' =>array('shares'=> '$shares')
                    ),
                    array('$limit' => 1)
            ));
            $orderNo = 0;
            if( isset($lastChart['result'][0]) ) {
                $orderNo = $lastChart['result'][0]['_id']['shares']['order_no'];
            }
            $shares[] = array('employee_id' => $id, 'order_no' => ++$orderNo);
        }
        $chart['shares'] = $shares;

        list($fromDate, $toDate) = explode(' - ', $chart['range']);
        $chart['from_date'] = new MongoDate(strtotime($fromDate));
        $chart['to_date'] = new MongoDate(strtotime($toDate));

        $objects = [];
        foreach($chart['objects'] as $object) {
            $object = array_merge(array(
                'name' => '',
                'color' => '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT),
                'module' => 'quotation',
                'conditions' => array()
            ), $object);
            if( !in_array($object['module'], array('quotation', 'salesinvoice', 'salesorder')) ) {
                continue;
            }
            $conditions = array();
            foreach($object['conditions'] as $condition) {
                $condition = array_merge(array('field' => '', 'operator' => '', 'values' => ''), $condition);
                if( !in_array($condition['field'], array('company_id', 'job_id', 'our_rep_id', 'our_csr_id')) ) {
                    continue;
                }
                if( $condition['operator']  == 'in') {
                    $condition['operator'] = '$in';
                } else if( $condition['operator'] == 'not_in' ) {
                    $condition['operator'] = '$nin';
                } else {
                    continue;
                }
                $values = array();
                foreach($condition['values'] as $key => $value) {
                    $value = trim($value);
                    if( strlen($value) != 24 ) {
                        continue;
                    }
                    $values[] = new MongoId($value);
                }

                if( empty($values) ){
                    continue;
                }
                $conditions[] = array(
                    'field' => $condition['field'],
                    'operator' => $condition['operator'],
                    'values' => $values,
                );
            }
            $objects[] = array(
                'name' => $object['name'],
                'color' => $object['color'],
                'module' => $object['module'],
                'conditions' => $conditions
            );
        }

        $chart['objects'] = $objects;


        $this->selectModel('Chart');
        $this->Chart->save(array(
            'name' => $chart['name'],
            'description' => $chart['description'],
            'width' => $chart['width'],
            'from_date' => $chart['from_date'],
            'to_date' => $chart['to_date'],
            'type' => $chart['type'],
            'xaxis' => $chart['xaxis'],
            'yaxis' => $chart['yaxis'],
            'shares' => $chart['shares'],
            'objects' => $chart['objects'],
        ));

        $json = json_encode([
                            'status'    => 'ok',
                            'chartId'   => (string)$this->Chart->mongo_id_after_save,
                            'chartName' => $chart['name'],
                            'chartWidth' => $this->arrWidth[(int)$chart['width']],
                            'color'     => '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT)
                        ]);
        $this->response->body($json);


    }

    public function delete_chart($chartId)
    {
        if( !$this->request->is('ajax')  ){
            $this->redirect('/');
        }
        $arrReturn = array('status' => 'error');
        if( strlen($chartId) == 24 ) {
            $currentUser = $this->Session->read('arr_user');
            $this->selectModel('Chart');
            $chart = $this->Chart->select_one(array(
                '_id' => new MongoId($chartId),
                'shares' => array(
                    '$elemMatch' => array( 'employee_id' => new MongoId($currentUser['_id']))
                )
            ), array('shares'));
            if( !empty($chart) ) {
                foreach($chart['shares'] as $key => $share) {
                    if( $share['employee_id'] == new MongoId($currentUser['_id']) ) {
                        unset($chart['shares'][$key]); break;
                    }
                }
                $chart['shares'] = array_values($chart['shares']);
                $this->Chart->save($chart);
                $arrReturn = array('status' => 'ok');
            }
        }
        $json = json_encode($arrReturn);
        $this->response->body($json);
    }

    public function reorder()
    {
        if( !$this->request->is('ajax')  ){
            $this->redirect('/');
        }
        $arrReturn = array('status' => 'error');
        if( isset($_POST['position']) ) {
            $position = $_POST['position'];
            $currentUser = $this->Session->read('arr_user');
            $this->selectModel('Chart');
            foreach($position as $value) {
                if( !isset($value['id']) || !isset($value['index']) ) continue;
                if( strlen($value['id']) != 24 ) continue;
                $chartId = $value['id'];
                $chart = $this->Chart->select_one(array(
                    '_id' => new MongoId($chartId),
                    'shares' => array(
                        '$elemMatch' => array( 'employee_id' => new MongoId($currentUser['_id']))
                    )
                ), array('shares'));
                if( !empty($chart) ) {
                    foreach($chart['shares'] as $key => $share) {
                        if( $share['employee_id'] == new MongoId($currentUser['_id']) ) {
                            $chart['shares'][$key]['order_no'] = (int)$value['index'];
                            break;
                        }
                    }
                    $chart['shares'] = array_values($chart['shares']);
                    $this->Chart->save($chart);
                }
            }
            $arrReturn = array('status' => 'ok');
        }
        $json = json_encode($arrReturn);
        $this->response->body($json);
    }

    public function get_id()
    {
        if( !$this->request->is('ajax')  ){
            $this->redirect('/');
        }
        $type = $_GET['type'];
        $query = $_GET['query'];
        $arrReturn = array();
        if( $type == 'company_id' ) {
            $this->selectModel('Company');
            $arrWhere = array('name' => new MongoRegex('/'. $query .'/i'));
            $companies = $this->Company->select_all(array(
                'arr_where' => $arrWhere,
                'arr_field' => array('name', 'no'),
                'arr_order' => array('no' => 1)
            ));
            foreach($companies as $company) {
                $company = array_merge(array('no' => '', 'name' => ''), $company);
                $arrReturn[] = array('text' => $company['name'], 'id' => (string)$company['_id']);
            }
        } else if( $type == 'job_id' ) {
            $this->selectModel('Job');
            $arrWhere = array('name' => new MongoRegex('/'. $query .'/i'));
            $jobs = $this->Job->select_all(array(
                'arr_where' => $arrWhere,
                'arr_field' => array('name', 'no'),
                'arr_order' => array('no' => 1)
            ));
            foreach($jobs as $job) {
                $job = array_merge(array('no' => '', 'name' => ''), $job);
                $arrReturn[] = array('text' => $job['name'], 'id' => (string)$job['_id']);
            }
        } else if( in_array($type, ['our_rep_id', 'our_csr_id']) ) {
            $this->selectModel('Contact');
            $arrWhere = array(
                            '$or' => array(
                                array('first_name' => new MongoRegex('/'. $query .'/i')),
                                array('last_name' => new MongoRegex('/'. $query .'/i')),
                            ),
                            'is_employee' => 1
                        );
            $contacts = $this->Contact->select_all(array(
                'arr_where' => $arrWhere,
                'arr_field' => array('first_name', 'last_name'),
                'arr_order' => array('no' => 1)
            ));
            foreach($contacts as $contact) {
                $contact = array_merge(array('first_name' => '', 'last_name' => ''), $contact);
                $contact['name'] = $contact['first_name'].' '.$contact['last_name'];
                $arrReturn[] = array('text' => $contact['name'], 'id' => (string)$contact['_id']);
            }
        }
        if( !empty($arrReturn) ) {
            $arrReturn = array('status' => 'found', 'data' => $arrReturn);
        } else {
            $arrReturn = array('status' => 'notFound');
        }
        $json = json_encode($arrReturn);
        $this->response->body($json);

    }
}