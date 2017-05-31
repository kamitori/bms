<?php

App::uses('AppController', 'Controller');

class SalesorderddsController extends AppController {

    var $modelName = 'Salesorder';

    public function beforeFilter() {
        // goi den before filter cha
        parent::beforeFilter();

        $this->set('title_entry', 'Salesorders');
    }

    // ================================== CALENDAR ====================================
    public function calendar( $date_from_sec = '', $date_to_sec = '' ) {
        $this->set('set_footer', '../Salesorderdds/calendar_footer');
        $this->Session->write('calendar_last_visit', '/' . $this->request->url);

        $this->selectModel('Setting');

        // get all salesorderdd
        if ($date_from_sec == '') {
            if (date('N') == 1) {
                $date_from_sec = strtotime(date('Y-m-d'));
                $date_to_sec = strtotime('next Sunday');
            } elseif (date('N') == 7) {
                $date_from_sec = strtotime('last Monday');
                $date_to_sec = strtotime(date('Y-m-d'));
            } else {
                $date_from_sec = strtotime('last Monday');
                $date_to_sec = strtotime('next Sunday');
            }
        }
        $this->set('date_from_sec', $date_from_sec);
        $this->set('date_to_sec', $date_to_sec);

        $arr_time_sec['prev'] = array(strtotime('last Monday', $date_from_sec), strtotime('last Sunday', $date_from_sec));
        $arr_time_sec['next'] = array(strtotime('next Monday', $date_to_sec), strtotime('next Sunday', $date_to_sec));
        $this->set('arr_time_sec', $arr_time_sec);

        $arr_where['$or'] = array(
            array(
                'salesorder_date' => array('$lte' => new MongoDate($date_from_sec)),
                'payment_due_date' => array('$gte' => new MongoDate($date_from_sec))
            ),
            array(
                'salesorder_date' => array('$lte' => new MongoDate($date_to_sec)),
                'payment_due_date' => array('$gte' => new MongoDate($date_to_sec))
            ),
            array(
                'salesorder_date' => array('$gte' => new MongoDate($date_from_sec)),
                'payment_due_date' => array('$lte' => new MongoDate($date_to_sec))
            ),
            array(
                'salesorder_date' => array('$lte' => new MongoDate($date_to_sec)),
                'payment_due_date' => ''
            )
        );

        // var $cond_status = array(
        //     'status_id' => array( '$in' => array('New', 'Submitted', 'In production', 'Partly shipped'))
        // );
        // $arr_where = array_merge($arr_where, $this->cond_status);
        // $arr_where = $this->cond_status;

        $this->selectModel('Salesorder');
        $arr_salesorderdds = $this->Salesorder->select_all(array(
            'arr_where' => $arr_where,
            'limit' => 1000,
            'maxLimit' => 1000
        ));

        // pr($arr_where);
        // pr($arr_salesorderdds->count());die;

        $this->set('arr_salesorderdds', $arr_salesorderdds);

        $this->selectModel('Contact');
        $arr_contact = $this->Contact->select_list(array(
            'arr_where' => array('is_employee' => 1),
            'arr_field' => array('_id', 'first_name', 'last_name'),
            'arr_order' => array('first_name' => 1),
        ));
        $this->set('arr_contact', $arr_contact);

        $arr_option = $this->get_option_status_color('salesorders_status');
        $arr_status = $arr_option[0];
        $arr_status_color = $arr_option[1];
        $this->set('arr_status', $arr_status);
        $this->set('arr_status_color', $arr_status_color);

        $this->layout = 'calendar';
    }

    public function _get_beginning_month_datetime($current_view_date) {
        $time_sec = strtotime(date($current_view_date));

        // tìm ngày d?u tiên c?a tu?n ch?a ngày 01
        if (date('N', $time_sec) == 1) {
            $date_from_sec = strtotime(date('Y-m-d', $time_sec));
        } else {
            $date_from_sec = strtotime('last Monday', $time_sec);
        }
        return $date_from_sec;
    }

    public function calendar_month($current_view_date = 'Y-m-01') {

        $this->set('set_footer', '../Salesorderdds/calendar_footer');
        $this->Session->write('calendar_last_visit', '/' . $this->request->url);

        $arr_option = $this->get_option_status_color('salesorders_status');
        $arr_status = $arr_option[0];
        $arr_status_color = $arr_option[1];
        $this->set('arr_status', $arr_status);
        $this->set('arr_status_color', $arr_status_color);

        $date_from_sec = $this->_get_beginning_month_datetime($current_view_date);
        $date_to_sec = $date_from_sec + 41 * DAY;
        $this->set('date_from_sec', $date_from_sec);
        $this->set('date_to_sec', $date_to_sec);

        $this->set('current_view_date', $current_view_date);

        $arr_time_sec['prev'] = array(date('Y-m-01', strtotime('first day of previous month', strtotime(date($current_view_date)))), '');
        $arr_time_sec['next'] = array(date('Y-m-01', strtotime('first day of next month', strtotime(date($current_view_date)))), '');
        $this->set('arr_time_sec', $arr_time_sec);

        $arr_where['$or'] = array(
            array(
                'salesorder_date' => array('$lte' => new MongoDate($date_from_sec)),
                'payment_due_date' => array('$gte' => new MongoDate($date_from_sec))
            ),
            array(
                'salesorder_date' => array('$lte' => new MongoDate($date_to_sec)),
                'payment_due_date' => array('$gte' => new MongoDate($date_to_sec))
            ),
            array(
                'salesorder_date' => array('$gte' => new MongoDate($date_from_sec)),
                'payment_due_date' => array('$lte' => new MongoDate($date_to_sec))
            ),
            array(
                'salesorder_date' => array('$lte' => new MongoDate($date_to_sec)),
                'payment_due_date' => ''
            )
        );

        $this->selectModel('Salesorder');
        $arr_salesorderdds = $this->Salesorder->select_all(array(
            'arr_where' => $arr_where,
            'limit' => 1000,
            'maxLimit' => 1000
        ));

        $this->set('arr_salesorderdds', $arr_salesorderdds);

        $this->layout = 'calendar';
    }

    public function calendar_day($date_from_sec = '') {

        $this->set('set_footer', '../Salesorderdds/calendar_footer');
        $this->Session->write('calendar_last_visit', '/' . $this->request->url);

        $arr_option = $this->get_option_status_color('salesorders_status');
        $arr_status = $arr_option[0];
        $arr_status_color = $arr_option[1];
        $this->set('arr_status', $arr_status);
        $this->set('arr_status_color', $arr_status_color);

        // get all salesorderdd
        if ($date_from_sec == '') {
            $date_from_sec = strtotime(date('Y-m-d'));
        }else{
            $date_from_sec = strtotime(date('Y-m-d', $date_from_sec)); // convert b? gi? di thì so sánh m?i dúng
        }

        $date_to_sec = $date_from_sec;

        $arr_time_sec['prev'] = array($date_from_sec - DAY, '');
        $arr_time_sec['next'] = array($date_from_sec + DAY, '');
        $this->set('arr_time_sec', $arr_time_sec);

        $this->set('date_from_sec', $date_from_sec);
        $this->set('date_to_sec', $date_to_sec);

        $arr_where['$or'] = array(
            array(
                'salesorder_date' => array('$lte' => new MongoDate($date_from_sec)),
                'payment_due_date' => array('$gte' => new MongoDate($date_from_sec))
            ),
            array(
                'salesorder_date' => array('$lte' => new MongoDate($date_to_sec)),
                'payment_due_date' => array('$gte' => new MongoDate($date_to_sec))
            ),
            array(
                'salesorder_date' => array('$gte' => new MongoDate($date_from_sec)),
                'payment_due_date' => array('$lte' => new MongoDate($date_to_sec))
            ),
            array(
                'salesorder_date' => array('$lte' => new MongoDate($date_to_sec)),
                'payment_due_date' => ''
            )
        );

        $this->selectModel('Salesorder');
        $arr_salesorderdds = $this->Salesorder->select_all(array(
            'arr_where' => $arr_where,
            'limit' => 1000,
            'maxLimit' => 1000
        ));

        $this->set('arr_salesorderdds', $arr_salesorderdds);

        $this->layout = 'calendar';
    }

    // Popup form orther module
    public function popup($key = "") {

        $this->set('key', $key);

        $cond = array();
        // if(!empty($this->data)){
        // 	$arr_post = $this->data['Salesorder'];
        // 	$cond['name'] = new MongoRegex('/'.$arr_post['name'].'/i');
        // 	$cond['inactive'] = $arr_post['inactive'];
        // 	if( is_numeric($arr_post['is_customer']) )
        // 		$cond['is_customer'] = $arr_post['is_customer'];
        // }

        $this->selectModel('Salesorder');
        $arr_salesorderdds = $this->Salesorder->select_all(array(
            'arr_where' => $cond,
            'arr_order' => array('_id' => -1),
                // 'arr_field' => array('name', 'is_customer', 'is_employee', 'default_address_1', 'default_address_2', 'default_address_3', 'default_town_city', 'default_country_name', 'default_province_state_name', 'default_zip_postcode', 'phone')
        ));
        $this->set('arr_salesorderdds', $arr_salesorderdds);

        $this->layout = 'ajax';
    }

    public function quick_view() {

        $this->selectModel('Salesorder');
        $this->selectModel('Contact');
        $this->selectModel('Setting');
        // set default
        $cond = array();
        $order = array('_id' => -1);
        // load dong thu may trong csdl
        $skip = 0;
        $limit = LIST_LIMIT;
        // check ajax
        if ($this->request->is('ajax')) {
            // seach
            if ($_REQUEST['identity']) {
                $cond['type'] = new MongoRegex('/' . $_REQUEST['identity'] . '/i');
            }
            if ($_REQUEST['filter_list_below']) {
                $cond['contact_name'] = new MongoRegex('/' . $_REQUEST['filter_list_below'] . '/i');
            }
            if ($_REQUEST['our_rep']) {
                $cond['contacts'] = array(
                    '$elemMatch' => array(
                        'contact_name' => new MongoRegex('/' . $_REQUEST['our_rep'] . '/i'),
                        'default' => true
                    )
                );
            }
            // end seach
            // set offset load_more
            if (isset($_REQUEST['offset'])) {
                $skip = $_REQUEST['offset'];
            }
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
            // order sub
            if ($sort_key == 'contact_name') {
                $order = array('contacts.contact_name' => $sort);
            } else {
                $order = array($sort_key => $sort);
            }

            $this->Session->write('salesorderdds_quick_view_search', array($cond, $order));
        } elseif ($this->Session->check('salesorderdds_quick_view_search')) {
            $seach_tmp = $this->Session->read('salesorderdds_quick_view_search');
            $cond = $seach_tmp[0];
            $order = $seach_tmp[1];
        }
        // end ajax
        // salesorderdds list
        $this->set('arr_salesorderdds', $this->Salesorder->select_all(array(
                    'arr_where' => $cond,
                    'arr_order' => $order,
                    'limit' => $limit,
                    'skip' => $skip
        )));
        if ($this->request->is('ajax')) {
            $this->render('quick_view_ajax');
        }
        // salesorderdds type list
        $this->set('arr_salesorderdds_type', $this->Setting->select_option(array('setting_value' => 'salesorderdds_type'), array('option')));
        // contact list
        $contact_list = $this->Contact->select_all(array(
            'arr_where' => array(
                'is_employee' => 1
            ),
            'arr_field' => array('first_name', '_id')
        ));
        foreach ($contact_list as $value) {
            $contact_list_tmp[] = (string) $value['first_name'];
        }
        $this->set('arr_contact_list', $contact_list_tmp);
    }

}