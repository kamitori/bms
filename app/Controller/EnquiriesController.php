<?php

App::uses('AppController', 'Controller');
class EnquiriesController extends AppController {

	var $modelName = 'Enquiry';
	var $name = 'Enquiries';
	var $sub_tab_default = 'general';

	public function beforeFilter() {
		// goi den before filter cha
		$this->selectModel('Enquiry');
		$this->opm = $this->Enquiry;
		parent::beforeFilter();

		$this->set('title_entry', 'Enquiries');
	}

	public function swith_options($option = '') {
        parent::swith_options($option);
		if ($option == 'hot') {
			$this->Session->write('enquiries_entry_search_cond', array('status_id' => 'Hot'));
			echo URL . DS . $this->params->params['controller'] .DS.'lists';
		}elseif($option == 'cold') {
			$this->Session->write('enquiries_entry_search_cond', array('status_id' => 'Cold'));
			echo URL . DS . $this->params->params['controller'] .DS.'lists';
		}elseif($option == 'in_progress_(hot_&_cold)') {
			$this->Session->write('enquiries_entry_search_cond', array('status_id' => array('$in' => array('Hot', 'Cold'))));
			echo URL . DS . $this->params->params['controller'] .DS.'lists';
		}elseif($option == 'deal') {
			$this->Session->write('enquiries_entry_search_cond', array('status_id' => 'Deal'));
			echo URL . DS . $this->params->params['controller'] .DS.'lists';
		}elseif($option == 'lost') {
			$this->Session->write('enquiries_entry_search_cond', array('status_id' => 'Lost'));
			echo URL . DS . $this->params->params['controller'] .DS.'lists';

		}elseif($option == 'occuring_today') {
			$cond = array(
				'work_start' => array( '$gte' => new MongoDate(strtotime(date('Y-m-d'))) ),
				'work_end' => array( '$lte' => new MongoDate(strtotime(date('Y-m-d')) + 23*3600 + 1800) )
			);
			$cond['status'] = array( '$in' => array('New', 'Confirmed') ) ;
			$this->Session->write('enquiries_entry_search_cond', $cond);
			echo URL . DS . $this->params->params['controller'] .DS.'lists';
		}elseif($option == 'starting_today') {
			$cond = array(
				'work_start' => array( '$gte' => new MongoDate(strtotime(date('Y-m-d'))), '$lte' => new MongoDate(strtotime(date('Y-m-d')) + 23*3600 + 1800) )
			);
			$cond['status'] = array( '$in' => array('New', 'Confirmed') ) ;
			$this->Session->write('enquiries_entry_search_cond', $cond);
			echo URL . DS . $this->params->params['controller'] .DS.'lists';
		}elseif($option == 'finishing_today') {
			$cond = array(
				'work_end' => array( '$gte' => new MongoDate(strtotime(date('Y-m-d'))), '$lte' => new MongoDate(strtotime(date('Y-m-d')) + 23*3600 + 1800) )
			);
			$cond['status'] = array( '$in' => array('New', 'Confirmed') ) ;
			$this->Session->write('enquiries_entry_search_cond', $cond);
			echo URL . DS . $this->params->params['controller'] .DS.'lists';
		}elseif($option == 'find_late_enquiries') {
			$cond = array(
				'work_end' => array( '$lte' => new MongoDate(strtotime('now')) )
			);
			$cond['status'] = array( '$in' => array('New', 'Confirmed') ) ;
			$this->Session->write('enquiries_entry_search_cond', $cond);
			echo URL . DS . $this->params->params['controller'] .DS.'lists';
		}
		else if($option == 'create_customer_record'){
			$this->add_customer('create_from_option',$this->get_id());
		}
		else if($option == 'create_quotation'){
			$this->add_quotation('create_from_option', $this->get_id());
		}
		else if($option =='create_sales_order'){
			$this->add_sale_order('create_from_option',$this->get_id());
		}
		else if($option == 'print_requirements'){
			echo URL . '/enquiries/print_requirements_pdf/',$this->get_id();
		}
		else if($option == 'create_email'){
			echo URL .'/'.$this->params->params['controller']. '/create_email';
		}
		else if($option == 'create_letter'){
			echo URL .'/' .$this->params->params['controller']. '/create_letter';
		}
		else if($option == 'create_fax'){
			echo URL .'/'.$this->params->params['controller'].'/create_fax';
		}
		else if($option == 'print_profile_list'){
			echo URL . '/' . $this->params->params['controller'] . '/print_profile_list_pdf';
		}
		else if($option == 'print_referral_report'){
			echo URL .'/'. $this->params->params['controller'] . '/referral_report_pdf';
		}
		die();
	}

	function get_data(){
		echo $_SESSION['arr_user']['contact_name'];
		echo date('M d, Y',time());
	}
	//opttion trong enquiry
	function get_data_print_requirement(){
		$enquiry_id = $this->get_id();
		$this->selectModel('Enquiry');
		$cond['_id'] = new MongoId($enquiry_id);
		$arr_query = $this->Enquiry->select_all(array(
			'arr_where' => $cond,
		));
		$arr_tmp = array();
		$arr_tmp =$arr_query;
		$tmp = array();
		foreach($arr_tmp as $value){
			$tmp['company'] = isset($value['company'])?$value['company']:'';
			$tmp['no'] = isset($value['no'])?$value['no']:'';
			$tmp['contact_name'] = isset($value['contact_name'])?$value['contact_name']:'';
			$tmp['company_phone'] = isset($value['company_phone'])?$value['company_phone']:'';
			$tmp['date'] = isset($value['date'])?$value['date']:'';
			$tmp['status'] = isset($value['status'])?$value['status']:'';
			$tmp['rating'] = isset($value['rating'])?$value['rating']:'';
			$tmp['default_address_1'] = isset($value['default_address_1'])?$value['default_address_1']:'';
			$tmp['detail'] = isset($value['detail'])?$value['detail']:'';
		}
		//pr($tmp);die;
		return $tmp;
	}

	function print_requirements_pdf(){
		 if(!isset($_GET['print_pdf'])){
			$req = array();
			$req =$this->get_data_print_requirement();
            if(!empty($req)){
                $html='';
                $i=1;
         		$html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">';
                $html .= '<td class="center_text">'.$req['no'].'</td>';
                $html .= '<td class="center_text">'.$req['company'].'</td>';
                $html .= '<td class="right_text">'.$req['contact_name'].'</td>';
                $html .= '<td class="right_text">'.$req['company_phone'].'</td>';
                $html .= '<td class="right_text">'.date('M d, Y',$req['date']->sec).'</td>';
                $html .= '<td class="right_text">'.$req['status'].'</td>';
                $html .= '<td class="right_text">'.$req['rating'].'</td>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td style="font-weight: bold">'.'Yêu cầu: '.'</td>';
                $html .= '<td colspan="6" class="left_text" style="font-size:11">'.str_replace("\n", "<br>", $req['detail']).'</td>';
                $html .= '</tr>';

                $arr_data['title'] = array('No','Công ty', 'Liên hệ', 'Điện thoại', 'Ngày', 'Tình trạng', 'Đánh giá');
                $arr_data['content'] = $html;
                $arr_data['report_name'] = 'Yêu Cầu';
                $arr_data['report_file_name'] = 'Enquiry_'.md5(time());
                Cache::write('enquiry_requirements',$arr_data);
            }
         }else{
              $arr_data = Cache::read('enquiry_requirements');
         }
        $this->render_pdf($arr_data);
	}


	function add_sale_order($type="",$enquiry_id){
		$this->selectModel('Enquiry');
		$data = $this->Enquiry->select_one(array('_id'=>new MongoId($enquiry_id)));
		if(!empty($data)){
			$arr_save['sales_order_type'] = '';
			$arr_save['company_name'] = isset($data['company'])?$data['company']:'';
			$arr_save['company_id'] = new MongoId($data['company_id']);
			$arr_save['company_phone'] = isset($data['company_phone'])?$data['company_phone']:'';
			$arr_save['email'] = isset($data['company_email'])?$data['company_email']:'';
			$arr_save['salesorder_date'] = new MongoDate(strtotime(date('Y-m-d' . ' 08:00:00')));
			$arr_save['payment_due_date'] = new MongoDate(strtotime(date('Y-m-d' . ' 08:00:00')));
			$arr_save['invoice_address'] = '';
			$arr_save['invoice_town_city'] = '';
			$arr_save['invoice_province_state'] = isset($data['default_province_state'])?$data['default_province_state']:'';
			$arr_save['invoice_province_state_id'] = new MongoId($data['default_province_state_id']);
			$arr_save['invoice_zip_postcode'] =  isset($data['default_zip_postcode'])?$data['default_zip_postcode']:'';
			$arr_save['invoice_country'] = isset($data['default_country'])?$data['default_country']:'';
			$arr_save['shipping_address'] = '';
			$arr_save['status'] = '';
			$arr_save['payment_terms'] = '';
			$arr_save['tax']='';
			$arr_save['customer_po_no']='';
			$arr_save['name']='';
			$arr_save['job_number']='';
			$arr_save['job_name']='';
			$arr_save['quotation_number']='';
			$arr_save['quotation_name']='';
			$arr_save['delivery_method'] ='';
			$arr_save['shipper_account'] ='';

			if(isset($data['our_rep_id']) && is_object($data['our_rep_id'])){
				$arr_save['our_rep'] = $data['our_rep'];
				$arr_save['our_rep_id'] = $data['our_rep_id'];
			}
			if(isset($data['our_csr_id']) && is_object($data['our_csr_id'])){
				$arr_save['our_csr'] = $data['our_csr'];
				$arr_save['our_csr_id'] = $data['our_csr_id'];
			}
			$this->selectModel("Salesorder");
			$this->Salesorder->arr_default_before_save = $arr_save;
			// pr($this->Company->arr_default_before_save );die;
			if($id = $this->Salesorder->add()){
				if($type=='create_from_option'){
					echo URL.'/salesorders/entry/' .$id;
					die;
				}else{
					$this->redirect('/salesorders/entry/'.$id);
					die;
				}
			}
		}
	}
	//Create quotation in option
	function add_quotation($type="",$enquiry_id){
		$this->selectModel('Enquiry');
		$data = $this->Enquiry->select_one(array('_id'=>new MongoId($enquiry_id)));
		if(!empty($data)){
			$arr_save['quotation_type'] = '';
			$arr_save['company_name'] = isset($data['company'])?$data['company']:'';
			$arr_save['company_id'] = new MongoId($data['company_id']);
			$arr_save['contact_name'] = isset($data['contact_name'])?$data['contact_name']:'';
			$arr_save['contact_id'] = new MongoId($data['contact_id']);
			$arr_save['phone'] = isset($data['company_phone'])?$data['company_phone']:'';
			$arr_save['email'] = isset($data['company_email'])?$data['company_email']:'';
			$arr_save['date_modified'] =  new MongoDate(strtotime(date('Y-m-d' . ' 08:00:00')));
			$arr_save['invoice_address'] = '';
			$arr_save['invoice_town_city'] = '';
			$arr_save['invoice_province_state'] = isset($data['default_province_state'])?$data['default_province_state']:'';
			$arr_save['invoice_zip_postcode'] = isset($data['default_zip_postcode'])?$data['default_zip_postcode']:'';
			$arr_save['invoice_country'] = isset($data['default_country'])?$data['default_country']:'';
			$arr_save['default_country'] = '';
			$arr_save['payment_due_date'] = '';
			$arr_save['quotation_status']='';
			$arr_save['payment_terms']='';
			$arr_save['tax']='';
			$arr_save['customer_po_no']='';
			$arr_save['job_name']='';
			$arr_save['job_number']='';
			$arr_save['salesorder_name']='';
			$arr_save['salesorder_number']='';

			if(isset($data['our_rep_id']) && is_object($data['our_rep_id'])){
				$arr_save['our_rep'] = $data['our_rep'];
				$arr_save['our_rep_id'] = $data['our_rep_id'];
			}
			if(isset($data['our_csr_id']) && is_object($data['our_csr_id'])){
				$arr_save['our_csr'] = $data['our_csr'];
				$arr_save['our_csr_id'] = $data['our_csr_id'];
			}
			$this->selectModel("Quotation");
			$this->Quotation->arr_default_before_save = $arr_save;
			//pr($this->Quotation->arr_default_before_save);die;
			if($id = $this->Quotation->add()){
				if($type=='create_from_option'){
					echo URL.'/quotations/entry/' .$id;
					die;
				}else{
					$this->redirect('/quotations/entry/'.$id);
					die;
				}
			}
		}
		if($type=='create_from_option')
		{
			echo URL.'/enquiries/entry/'.$this->get_id();
			die;
		}
		$this->redirect('/enquiries/entry/'.$this->get_id());
		die;
	}

	//Create customer record in option
	function add_customer($type='', $enquiry_id){
		$this->selectModel('Enquiry');
		$data = $this->Enquiry->select_one(array('_id'=>new MongoId($enquiry_id)));
		if(!empty($data)){
			$arr_save = array();
			//$arr_save['CompanyNo'] = isset($data['CompanyNo'])?$data['CompanyNo']:'';
			$arr_save['is_supplier'] = 0;
			$arr_save['is_customer'] = 1;
			$arr_save['name'] = isset($data['company'])?$data['company']:'';
			$arr_save['company_id'] = new MongoId($data['company_id']);
			$arr_save['type_name'] = '';
			$arr_save['phone'] = isset($data['company_phone'])?$data['company_phone']:'';
			$arr_save['fax'] = isset($data['company_fax'])?$data['company_fax']:'';
			$arr_save['email'] = isset($data['company_email'])?$data['company_email']:'';
			$arr_save['web'] = isset($data['web'])?$data['web']:'';
			$arr_save['DefaultAddress1'] = isset($data['default_address_1'])?$data['default_address_1']:'';
			$arr_save['DefaultTownCity'] = isset($data['default_town_city'])?$data['default_town_city']:'';
			$arr_save['province_state'] = '';
			$arr_save['DefaultZipPostcode'] = isset($data['default_zip_postcode'])?$data['default_zip_postcode']:'';
			$arr_save['country'] = '';
			$arr_save['business_type'] = '';
			$arr_save['industry'] = '';
			$arr_save['size'] = '';
			$arr_save['inactive'] = '';
			$arr_save['is_shipper'] = '';
			$arr_save['tracking_url'] = '';

			if(isset($data['our_rep_id']) && is_object($data['our_rep_id'])){
				$arr_save['our_rep'] = $data['our_rep'];
				$arr_save['our_rep_id'] = $data['our_rep_id'];
			}
			if(isset($data['our_csr_id']) && is_object($data['our_csr_id'])){
				$arr_save['our_csr'] = $data['our_csr'];
				$arr_save['our_csr_id'] = $data['our_csr_id'];
			}
			$this->selectModel("Company");
			$this->Company->arr_default_before_save = $arr_save;
			// pr($this->Company->arr_default_before_save );die;
			if($id = $this->Company->add()){
				if($type=='create_from_option'){
					echo URL.'/companies/entry/' .$id;
					die;
				}else{
					$this->redirect('/companies/entry/'.$id);
					die;
				}
			}
		}
		if($type=='create_from_option')
		{
			echo URL.'/enquiries/entry/'.$this->get_id();
			die;
		}
		$this->redirect('/enquiries/entry/'.$this->get_id());
		die;
	}


	function auto_save() {
		if (!empty($this->data)) {
			$arr_post_data = $this->data['Enquiry'];
			$arr_save = $arr_post_data;

			$this->selectModel('Enquiry');
			$arr_tmp = $this->Enquiry->select_one(array(
				'_id' => array('$ne' => new MongoId($arr_save['_id'])),
				'no' => (int) $arr_save['no']
					), array('no'));
			if (isset($arr_tmp['no'])) {
				echo 'ref_no';
				die;
			}

			if ( isset($arr_save['date']) && strlen($arr_save['date']) > 0 )
				$arr_save['date'] = new MongoDate($this->Common->strtotime($arr_save['date'] . '00:00:00'));

			if (strlen(trim($arr_save['our_rep_id'])) > 0)
				$arr_save['our_rep_id'] = new MongoId($arr_save['our_rep_id']);

			if (strlen(trim($arr_save['company_id'])) > 0)
				$arr_save['company_id'] = new MongoId($arr_save['company_id']);

			if (strlen(trim($arr_save['contact_id'])) > 0)
				$arr_save['contact_id'] = new MongoId($arr_save['contact_id']);

			$error = 0;
			if (!$error) {
				if ($this->Enquiry->save($arr_save)) {
					echo 'ok';
				} else {
					echo 'Error: ' . $this->Enquiry->arr_errors_save[1];
				}
			}
		}
		die;
	}

	function delete($id = 0) {
		$arr_save['_id'] = $id;
		$arr_save['deleted'] = true;
		$error = 0;
		if (!$error) {
			$this->selectModel('Enquiry');
			if ($this->Enquiry->save($arr_save)) {
				$this->Session->delete('Enquiry_entry_id');
				$this->redirect('/enquiries/entry');
			} else {
				echo 'Error: ' . $this->Enquiry->arr_errors_save[1];
			}
		}
		die;
	}

	public function add() {
		$this->selectModel('Enquiry');
		if ($this->Enquiry->add())
			$this->redirect('/enquiries/entry/' . $this->Enquiry->mongo_id_after_save);
		die;
	}

	public function entry($id = '0', $num_position = -1) {

		$arr_tmp = $this->entry_init($id, $num_position, 'Enquiry', 'enquiries');

		$this->selectModel('Setting');

		$arr_enquiry_title = $this->Setting->select_option(array('setting_value' => 'enquiry_title'), array('option'));
		$this->set('arr_enquiry_title', $arr_enquiry_title);
		if (isset($arr_tmp['title_id']) && isset($arr_enquiry_title[$arr_tmp['title_id']])) {
			$arr_tmp['title'] = $arr_enquiry_title[$arr_tmp['title_id']];
		}

		$arr_enquiry_status = $this->Setting->select_option(array('setting_value' => 'enquiry_status'), array('option'));
		$this->set('arr_enquiry_status', $arr_enquiry_status);
		if (isset($arr_tmp['status_id']) && isset($arr_enquiry_status[$arr_tmp['status_id']])) {
			$arr_tmp['status'] = $arr_enquiry_status[$arr_tmp['status_id']];
		}

		$arr_enquiry_rating = $this->Setting->select_option(array('setting_value' => 'enquiry_rating'), array('option'));
		$this->set('arr_enquiry_rating', $arr_enquiry_rating);
		if (isset($arr_tmp['rating_id']) && isset($arr_enquiry_rating[$arr_tmp['rating_id']])) {
			$arr_tmp['rating'] = $arr_enquiry_rating[$arr_tmp['rating_id']];
		}

		// ----------
		$arr_enquiry_position = $this->Setting->select_option(array('setting_value' => 'contacts_position'), array('option'));
		$this->set('arr_enquiry_position', $arr_enquiry_position);
		if (isset($arr_tmp['position_id']) && isset($arr_enquiry_position[$arr_tmp['position_id']])) {
			$arr_tmp['position'] = $arr_enquiry_position[$arr_tmp['position_id']];
		}

		$arr_enquiry_department = $this->Setting->select_option(array('setting_value' => 'enquiry_department'), array('option'));
		$this->set('arr_enquiry_department', $arr_enquiry_department);
		if (isset($arr_tmp['department_id']) && isset($arr_enquiry_department[$arr_tmp['department_id']])) {
			$arr_tmp['department'] = $arr_enquiry_department[$arr_tmp['department_id']];
		}

		$arr_enquiry_type = $this->Setting->select_option(array('setting_value' => 'contacts_type'), array('option'));
		$this->set('arr_enquiry_type', $arr_enquiry_type);
		if (isset($arr_tmp['type_id']) && isset($arr_enquiry_type[$arr_tmp['type_id']])) {
			$arr_tmp['type'] = $arr_enquiry_type[$arr_tmp['type_id']];
		}

		$arr_enquiry_category = $this->Setting->select_option(array('setting_value' => 'enquiry_category'), array('option'));
		$this->set('arr_enquiry_category', $arr_enquiry_category);
		if (isset($arr_tmp['category_id']) && isset($arr_enquiry_category[$arr_tmp['category_id']])) {
			$arr_tmp['category'] = $arr_enquiry_category[$arr_tmp['category_id']];
		}

		$arr_enquiry_referred = $this->Setting->select_option(array('setting_value' => 'enquiry_referred'), array('option'));
		$this->set('arr_enquiry_referred', $arr_enquiry_referred);
		if (isset($arr_tmp['referred_id']) && isset($arr_enquiry_referred[$arr_tmp['referred_id']])) {
			$arr_tmp['referred'] = $arr_enquiry_referred[$arr_tmp['referred_id']];
		}

		if (isset($arr_tmp['date']))
			$arr_tmp['date'] = (is_object($arr_tmp['date'])) ? date('m/d/Y', $arr_tmp['date']->sec) : '';

		$arr_tmp1['Enquiry'] = $arr_tmp;

		$this->data = $arr_tmp1;

		$arr_contact_id = array();
		// if(isset($arr_tmp['our_rep_id']))
		// 	$arr_contact_id[] = $arr_tmp['our_rep_id'];
		// if(isset($arr_tmp['our_csr_id']))
		// 	$arr_contact_id[] = $arr_tmp['our_csr_id'];
		// hiển thị cho footer
		$this->show_footer_info($arr_tmp, $arr_contact_id);

		$this->set('address_country', $this->country());
		$this->set('address_province', $this->province($this->data['Enquiry']['default_country_id']));

		// Get info for subtask
		$this->set('address_company_id','EnquiryCompanyId');
		$this->sub_tab('', $arr_tmp['_id']);
	}

	public function entry_search_all(){
		$this->Session->delete('enquiries_entry_search_cond');
		$this->redirect('/enquiries/lists');
	}

	public function entry_search() {
		if (!empty($this->data) && $this->request->is('ajax')) {

			$post = $this->data['Enquiry'];
			$cond = array();
			//pr($post);die;
			$post = $this->Common->strip_search($post);
			if( strlen($post['no']) > 0 )$cond['no'] = (int)$post['no'];
			if( strlen($post['company']) > 0 )$cond['company'] = new MongoRegex('/' . trim($post['company']).'/i');
			if( strlen($post['title_id']) > 0 )$cond['title_id'] = $post['title_id'];
			if( strlen($post['contact_name']) > 0 )$cond['contact_name'] = new MongoRegex('/' . trim($post['contact_name']).'/i');
			if( strlen($post['date']) > 0 )$cond['date'] = new MongoDate($this->Common->strtotime($post['date'] .' 00:00:00'));
			if( strlen($post['status_id']) > 0 )$cond['status_id'] = $post['status_id'];
			if( strlen($post['rating_id']) > 0 )$cond['rating_id'] = $post['rating_id'];
			if( strlen($post['web']) > 0 )$cond['web'] = new MongoRegex('/' . trim($post['web']).'/i');

			if( strlen($post['default_address_1']) > 0 )$cond['default_address_1'] = new MongoRegex('/' . trim($post['default_address_1']).'/i');
			if( strlen($post['default_address_2']) > 0 )$cond['default_address_2'] = new MongoRegex('/' . trim($post['default_address_2']).'/i');
			if( strlen($post['default_address_3']) > 0 )$cond['default_address_3'] = new MongoRegex('/' . trim($post['default_address_3']).'/i');
			if( strlen($post['default_town_city']) > 0 )$cond['default_town_city'] = new MongoRegex('/' . trim($post['default_town_city']).'/i');
			if( strlen($post['default_province_state']) > 0 )$cond['default_province_state'] = new MongoRegex('/' . trim($post['default_province_state']).'/i');
			if( strlen($post['default_country']) > 0 )$cond['default_country'] = new MongoRegex('/' . trim($post['default_country']).'/i');

			if( strlen($post['company_phone']) > 0 )$cond['company_phone'] = new MongoRegex('/' . trim($post['company_phone']).'/i');
			if( strlen($post['direct_phone']) > 0 )$cond['direct_phone'] = new MongoRegex('/' . trim($post['direct_phone']).'/i');
			if( strlen($post['home_phone']) > 0 )$cond['home_phone'] = new MongoRegex('/' . trim($post['home_phone']).'/i');
			if( strlen($post['mobile']) > 0 )$cond['mobile'] = new MongoRegex('/' . trim($post['mobile']).'/i');
			if( strlen($post['company_fax']) > 0 )$cond['company_fax'] = new MongoRegex('/' . trim($post['company_fax']).'/i');
			if( strlen($post['direct_fax']) > 0 )$cond['direct_fax'] = new MongoRegex('/' . trim($post['direct_fax']).'/i');
			if( strlen($post['contact_email']) > 0 )$cond['contact_email'] = new MongoRegex('/' . trim($post['contact_email']).'/i');
			if( strlen($post['company_email']) > 0 )$cond['company_email'] = new MongoRegex('/' . trim($post['company_email']).'/i');
			if( strlen($post['position_id']) > 0 )$cond['position_id'] = $post['position_id'];
			if( strlen($post['department_id']) > 0 )$cond['department_id'] = $post['department_id'];
			if( strlen($post['type_id']) > 0 )$cond['type_id'] = $post['type_id'];
			if( strlen($post['category_id']) > 0 )$cond['category_id'] = $post['category_id'];
			if( strlen($post['enquiry_value']) > 0 )$cond['enquiry_value'] = (int)trim($post['enquiry_value']);
			if( strlen($post['no_of_staff']) > 0 )$cond['no_of_staff'] = (int)$post['no_of_staff'];
			if( strlen($post['referred_id']) > 0 )$cond['referred_id'] = $post['referred_id'];
			if( strlen($post['our_rep_id']) > 0 )$cond['our_rep_id'] = new MongoId( $post['our_rep_id'] );

			$this->selectModel('Enquiry');
            $this->identity($cond);
			$tmp = $this->Enquiry->select_one($cond);
			if( $tmp ){
				$this->Session->write('enquiries_entry_search_cond', $cond);

				$cond['_id'] = array('$ne' => $tmp['_id']);
				$tmp1 = $this->Enquiry->select_one($cond);
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
		$this->set('arr_enquiry_title', $this->Setting->select_option(array('setting_value' => 'enquiry_title'), array('option')));
		$this->set('arr_enquiry_status', $this->Setting->select_option(array('setting_value' => 'enquiry_status'), array('option')));
		$this->set('arr_enquiry_rating', $this->Setting->select_option(array('setting_value' => 'enquiry_rating'), array('option')));
		$this->set('arr_enquiry_position', $this->Setting->select_option(array('setting_value' => 'contacts_position'), array('option')));
		$this->set('arr_enquiry_department', $this->Setting->select_option(array('setting_value' => 'enquiry_department'), array('option')));
		$this->set('arr_enquiry_type', $this->Setting->select_option(array('setting_value' => 'contacts_type'), array('option')));
		$this->set('arr_enquiry_category', $this->Setting->select_option(array('setting_value' => 'enquiry_category'), array('option')));
		$this->set('arr_enquiry_referred', $this->Setting->select_option(array('setting_value' => 'enquiry_referred'), array('option')));

		$this->set('set_footer', 'footer_search');
		$this->set('address_country', $this->country());
		$this->set('address_province', $this->province("CA"));

		// Get info for subtask
		// $this->sub_tab('', $arr_tmp['_id']);
	}


	function general($enquiry_id) {
		$this->set('enquiry_id', $enquiry_id);
		$this->selectModel('Enquiry');
		$arr_enquiry = $this->Enquiry->select_one(array('_id' => new MongoId($enquiry_id)), array('detail','keywords'));
		$this->set('arr_enquiry', $arr_enquiry);
		//Commnications/notes --An chinh
		$arr_return = array();
		$module_id = $enquiry_id;
		if(isset($module_id)){
			$arr_return = $this->Enquiry->select_one(array('_id' => new MongoId($module_id)));
		}

		$this->set('model_name', $this->modelName);
		$this->set('arr_return', $arr_return);

		$this->selectModel('Setting');
		$arr_enquiry_keywords = $this->Setting->select_option(array('setting_value' => 'enquiry_keywords'), array('option'));
		$this->set('arr_enquiry_keywords', $arr_enquiry_keywords);

		// BaoNam: gọi view ctp communications dùng chung
		$this->communications($enquiry_id, true);
	}

	function general_auto_save($id) {
		if (!empty($_POST)) {
			$arr_save['_id'] = $id;
			$arr_save['detail'] = $_POST['detail'];
			//$arr_save['kw1'] = $_POST['kw1'];
			$error = 0;
			if (!$error) {
				$this->selectModel('Enquiry');
				if ($this->Enquiry->save($arr_save)) {
					echo 'ok';
				} else {
					echo 'Error: ' . $this->Enquiry->arr_errors_save[1];
				}
			}
		}
		die;
	}
	function general_keywords_auto_save($id){
		if($_POST)
		{
			$this->selectModel('Enquiry');
			$arr_save = $this->Enquiry->select_one(array('_id' => new MongoId($id)));
			$arr_save['keywords'][$_POST['key']]= $_POST['value'];
			if( $this->Enquiry->save($arr_save) ){
				echo 'ok';
			}else{
				echo 'Error: ' . $this->Enquiry->arr_errors_save[1];
			}
		}
		die;
	}

	function quotes($enquiry_id) {
		$this->selectModel('Quotation');
		$arr_quote = $this->Quotation->select_all(array(
			'arr_where' => array('enquiry_id' => new MongoId($enquiry_id)),
		));
		$this->set('arr_quotation', $arr_quote);
		$this->set('enquiry_id', $enquiry_id);
		$this->selectModel('Contact');
		$this->set('model_contact', $this->Contact);
	}

	function quotes_add($enquiry_id) {
		$this->selectModel('Quotation');
		$arr_tmp=array();
		$arr_tmp = $this->Quotation->select_one(array(), array(), array('code' => -1));
		$arr_save = array();
		$arr_save['code'] = 1;
		if (isset($arr_tmp['code'])) {
			$arr_save['code'] = $arr_tmp['code'] + 1;
		}

		$this->selectModel('Enquiry');
		$arr_enquiry = $this->Enquiry->select_one(array('_id' => new MongoId($enquiry_id)));
		$arr_save['enquiry_id'] = $arr_enquiry['_id'];
		$arr_save['company_name'] = isset($arr_enquiry['company'])?$arr_enquiry['company']:'';
		$arr_save['company_id'] = isset($arr_enquiry['company_id'])?$arr_enquiry['company_id']:'';
		$arr_save['contact_name'] = isset($arr_enquiry['contact_name'])?$arr_enquiry['contact_name']:'';
		$arr_save['contact_id'] = isset($arr_enquiry['contact_id'])?$arr_enquiry['contact_id']:'';
		$arr_save['email'] = isset($arr_enquiry['company_email'])?$arr_enquiry['company_email']:'';
		if( isset($arr_enquiry['contact_email']) ){
			$arr_save['email'] = $arr_enquiry['contact_email'];
		}
		$arr_save['phone'] = isset($arr_enquiry['direct_phone'])?$arr_enquiry['direct_phone']:'';
	    $arr_invoice_addrress['invoice_name'] = '';
	    $arr_invoice_addrress['deleted'] = false;
	    $arr_invoice_addrress['invoice_country'] = $arr_enquiry['default_country'];
	    $arr_invoice_addrress['invoice_country_id'] = $arr_enquiry['default_country_id'];
	    $arr_invoice_addrress['invoice_province_state'] = $arr_enquiry['default_province_state'];
	    $arr_invoice_addrress['invoice_province_state_id'] = $arr_enquiry['default_province_state_id'];
	    $arr_invoice_addrress['invoice_address_1'] = $arr_enquiry['default_address_1'];
	    $arr_invoice_addrress['invoice_address_2'] = $arr_enquiry['default_address_2'];
	    $arr_invoice_addrress['invoice_address_3'] = $arr_enquiry['default_address_3'];
	    $arr_invoice_addrress['invoice_town_city'] = $arr_enquiry['default_town_city'];
	    $arr_invoice_addrress['invoice_zip_postcode'] = $arr_enquiry['default_zip_postcode'];

		$arr_save['invoice_address'][0] = (object)$arr_invoice_addrress;
		$arr_save['our_csr'] = $_SESSION['arr_user']['contact_name'];
		$arr_save['our_csr_id'] = new MongoId($_SESSION['arr_user']['contact_id']);

		if(isset($arr_enquiry['our_rep_id'])){
			$arr_save['our_rep'] = isset($arr_enquiry['our_rep'])?$arr_enquiry['our_rep']:'';
			$arr_save['our_rep_id'] = new MongoId($arr_enquiry['our_rep_id']);
		}else
		{
			$arr_save['our_rep'] = $_SESSION['arr_user']['contact_name'];
			$arr_save['our_rep_id'] = new MongoId($_SESSION['arr_user']['contact_id']);
		}
		$arr_save['quotation_date'] = new MongoDate(strtotime(date('Y-m-d')));
		$arr_save['quotation_type'] = 'Quotation';
		$arr_save['tax'] = 'AB';
		$this->Quotation->arr_default_before_save = $arr_save;
		if ($this->Quotation->add()) {
			$this->redirect('/quotations/entry/'. $this->Quotation->mongo_id_after_save);
		}
		$this->redirect('/quotations/entry');
	}

	function quotes_delete($id) {

		$arr_save['_id'] = $id;
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

	function tasks($enquiry_id) {
		$this->selectModel('Task');
		$arr_task = $this->Task->select_all(array(
			'arr_where' => array('enquiry_id' => new MongoId($enquiry_id)),
			'arr_order' => array('_id' => -1)
		));
		$this->set('arr_task', $arr_task);
		$this->set('enquiry_id', $enquiry_id);
	}

	function tasks_add($enquiry_id) {

		//$this->selectModel('Company');
		//$arr_enquiry = $this->Company->select_one(array('_id' => new MongoId($enquiry_id)));

		$this->selectModel('Enquiry');
		$arr_enquiry = $this->Enquiry->select_one(array('_id' => new MongoId($enquiry_id)));

		$this->selectModel('Task');
		$arr_tmp = $this->Task->select_one(array(), array(), array('no' => -1));
		$arr_save = array();
		$arr_save['no'] = 1;
		if (isset($arr_tmp['no'])) {
			$arr_save['no'] = $arr_tmp['no'] + 1;
		}
		$arr_save['status_id'] = 0;
		$arr_save['name'] = '';
		$arr_save['enquiry_name'] = '';
		$arr_save['our_rep'] = $_SESSION['arr_user']['contact_name'];
		$arr_save['our_rep_id'] = new MongoId($_SESSION['arr_user']['contact_id']);
		$arr_save['work_start'] = new MongoDate(strtotime(date('Y-m-d' . ' 08:00:00')));
		$arr_save['work_end'] = new MongoDate(strtotime(date('Y-m-d') . ' 09:00:00'));
		$arr_save['enquiry_no'] = $arr_enquiry['no'];
		$arr_save['enquiry_id'] = $arr_enquiry['_id'];
		if(isset( $arr_enquiry['company']) && isset($arr_enquiry['company_id']) && isset($arr_enquiry['contact_name'])){
			$arr_save['enquiry_name'] = $arr_enquiry['company'];
			$arr_save['company_id'] = $arr_enquiry['company_id'];
			$arr_save['company_name'] = $arr_enquiry['company'];
			$arr_save['contact_id'] = $arr_enquiry['contact_id'];
			$arr_save['contact_name'] = $arr_enquiry['contact_name'];
		}
		if (isset($arr_enquiry['contact_default_id'])) {
			$this->selectModel('Contact');
			$arr_contact = $this->Contact->select_one(array('_id' => $arr_enquiry['contact_default_id']));
			$arr_save['contact_id'] = $arr_contact['_id'];
			$arr_save['contact_name'] = $arr_contact['first_name'] . ' ' . $arr_contact['last_name'];
		}

		$this->Task->arr_default_before_save = $arr_save;
		if ($this->Task->add()) {
			$this->redirect('/tasks/entry/' . $this->Task->mongo_id_after_save);
		}
		$this->redirect('/companies/entry/' . $enquiry_id);
	}

	function tasks_delete($id) {

		$arr_save['_id'] = $id;
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

	function other() {
		$enquiry_id = $this->get_id();
		if( !isset($this->data['Enquiry']) ){ // kiểm tra xem đã có load ở entry chưa
			$this->selectModel('Enquiry');
			$arr_other = $this->Enquiry->select_one(array('_id' => new MongoId($enquiry_id)));
			$arr_tmp['Enquiry'] = $arr_other;
			$this->data = $arr_tmp;
		}
		$this->set('enquiry_id', $enquiry_id);
	}

	function other_result_conmments($enquiry_id) {
		$arr_save = array();
		$arr_save['_id'] = $enquiry_id;
		$arr_save['result_conmments'] = $_POST['result_conmments'];
		$this->selectModel('Enquiry');
		if ($this->Enquiry->save($arr_save)) {
			echo 'ok';
		} else {
			echo 'Error: ' . $this->Enquiry->arr_errors_save[1];
		}
		die;
	}

	function lists() {
		$this->selectModel('Enquiry');

		$limit = LIST_LIMIT; $skip = 0;

		// dùng cho sort
		$sort_field = 'work_start';
		$sort_type = 1;
		if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
			if( $_POST['sort']['type'] == 'desc' ){
				$sort_type = -1;
			}
			$sort_field = $_POST['sort']['field'];
			$this->Session->write('enquiries_lists_search_sort', array($sort_field, $sort_type));

		}elseif( $this->Session->check('enquiries_lists_search_sort') ){
			$session_sort = $this->Session->read('enquiries_lists_search_sort');
			$sort_field = $session_sort[0];
			$sort_type = $session_sort[1];
		}
		$arr_order = array($sort_field => $sort_type);
		$this->set('sort_field', $sort_field);
		$this->set('sort_type', ($sort_type === 1)?'asc':'desc');

		$this->selectModel('Contact');
		$this->set('model_contact', $this->Contact);

		// dùng cho điều kiện
		$cond = array();
		if( $this->Session->check('enquiries_entry_search_cond') ){
			$cond = $this->Session->read('enquiries_entry_search_cond');
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

		// query
		$arr_enquiries = $this->Enquiry->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'limit' => $limit,
			'skip' => $skip
		));
		$this->set('arr_enquiries', $arr_enquiries);


		$total_page = $total_record = $total_current = 0;
		if( is_object($arr_enquiries) ){
			$total_current = $arr_enquiries->count(true);
			$total_record = $arr_enquiries->count();
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

	function lists_delete($id) {
		$arr_save['_id'] = $id;
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

	// Popup form orther module
	public function popup($key = '') {
		$this->set('key', $key);

		$limit = 100; $skip = 0; $cond = array();

        $this->identity($cond);
		// Nếu là search GET
		if (!empty($_GET)) {

			$tmp = $this->data;

			if (isset($_GET['company_id'])) {
				$cond['company_id'] = new MongoId($_GET['company_id']);
				$tmp['Enquiry']['company'] = $_GET['company_name'];
			}

			if (isset($_GET['is_customer'])) {
				$cond['is_customer'] = 1;
				$tmp['Enquiry']['is_customer'] = 1;
			}

			if (isset($_GET['is_employee'])) {
				$cond['is_employee'] = 1;
				$tmp['Enquiry']['is_employee'] = 1;
			}

			$this->data = $tmp;
		}

		// Nếu là search theo phân trang
		$page_num = 1;
		if( isset($_POST['pagination']) && $_POST['pagination']['page-num'] > 0 ){

			// $limit = $_POST['pagination']['page-list'];
			$page_num = $_POST['pagination']['page-num'];
			$limit = $_POST['pagination']['page-list'];
			$skip = $limit*($page_num - 1);
		}
		$this->set('page_num', $page_num);
		$this->set('limit', $limit);

		$arr_order = array('date' => -1);
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

		// search theo submit $_POST kèm điều kiện
		if (!empty($this->data) && !empty($_POST) && isset($this->data['Enquiry']) ) {
			$arr_post = $this->data['Enquiry'];

			if (isset($arr_post['contact_name']) && strlen($arr_post['contact_name']) > 0) {
				$cond['contact_name'] = new MongoRegex('/' . trim($arr_post['contact_name']) . '/i');
			}

			if (strlen($arr_post['company']) > 0) {
				$cond['company'] = new MongoRegex('/' . $arr_post['company'] . '/i');
			}

		}

		$this->selectModel('Enquiry');
		$arr_enquiry = $this->Enquiry->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'limit' => $limit,
			'skip' => $skip
			// 'arr_field' => array('name', 'is_customer', 'is_employee', 'company_id', 'company_name')
		));
		$this->set('arr_enquiry', $arr_enquiry);

		$total_page = $total_record = $total_current = 0;
		if( is_object($arr_enquiry) ){
			$total_current = $arr_enquiry->count(true);
			$total_record = $arr_enquiry->count();
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

	// Lay thong tin tu Enquiry -> Comm de tao email(khong pdf)
	function create_email() {
		$this->selectModel('Enquiry');
		$arr_enquiry = $this->Enquiry->select_one(array('_id' => new MongoId($this->get_id())));

		$arr_save=array();
		$this->selectModel('Communication');
		$arr_save['code']=$this->Communication->get_auto_code('code');

		$arr_save['comms_type']='Email';

		$arr_save['company_id']=isset($arr_enquiry['_id'])?$arr_enquiry['_id']:'';
		$arr_save['company_name']=isset($arr_enquiry['company'])?$arr_enquiry['company']:'';
		$arr_save['module']=isset($this->params->params['controller'])?$this->params->params['controller']:'';


		$arr_save['position']=isset($arr_enquiry['position'])?$arr_enquiry['position']:'';
		$arr_save['position_id']=isset($arr_enquiry['position_id'])?$arr_enquiry['position_id']:'';
		$arr_save['email']=isset($arr_enquiry['contact_email'])?$arr_enquiry['contact_email']:'';
		$arr_save['comms_date'] = new MongoDate();
		$arr_save['sign_off'] = 'Regards';
		$this->selectModel('Contact');
		$arr_contact = $arrtemp = array();

		if (isset($arr_enquiry['contact_id']) && is_object($arr_enquiry['contact_id'])) {
			$arr_contact = $this->Contact->select_one(array('_id' => new MongoId($arr_enquiry['contact_id'])));
		}
		else
		{
			$arr_contact = $this->Contact->select_all(array(
				'arr_where' => array('company_id' => new MongoId($arr_enquiry['_id'])),
				'arr_order' => array('_id' => -1),
			));
			$arrtemp = iterator_to_array($arr_contact);
			if (count($arrtemp) > 0) {
				$arr_contact = current($arrtemp);
			} else
				$arr_contact = array();
		}
		if (isset($arr_contact['_id'])) {
			$arr_save['contact_name']=isset($arr_contact['first_name'])?$arr_contact['first_name']:'';
			$arr_save['last_name']=isset($arr_contact['last_name'])?$arr_contact['last_name']:'';

			$arr_save['contact_id'] = $arr_contact['_id'];
		} else {
			$arr_save['contact_name'] = '';
			$arr_save['contact_id'] = '';
		}

		$arr_save['contact_from_id']=$this->Contact->user_id();
		$arr_save['contact_from']=$this->Contact->user_name();

		if ($this->Communication->save($arr_save)) {
			$this->redirect('/communications/entry/'. $this->Communication->mongo_id_after_save);
		}
		$this->redirect('/communications/entry');
	}

	function create_letter() {
		$this->selectModel('Enquiry');
		$arr_enquiry = $this->Enquiry->select_one(array('_id' => new MongoId($this->get_id())));

		$arr_save=array();
		$this->selectModel('Communication');
		$arr_save['code']=$this->Communication->get_auto_code('code');

		$arr_save['comms_type']='Letter';

		$arr_save['company_id']=isset($arr_enquiry['_id'])?$arr_enquiry['_id']:'';
		$arr_save['company_name']=isset($arr_enquiry['company'])?$arr_enquiry['company']:'';
		//$arr_save['module']=isset($this->params->params['controller'])?$this->params->params['controller']:'';

		$arr_save['sign_off'] = 'Regards';
		$arr_save['email']=isset($arr_enquiry['contact_email'])?$arr_enquiry['contact_email']:'';
		$arr_save['phone']=isset($arr_enquiry['company_phone'])?$arr_enquiry['company_phone']:'';
		$arr_save['fax']=isset($arr_enquiry['company_fax'])?$arr_enquiry['company_fax']:'';
		$arr_save['position']=isset($arr_enquiry['position'])?$arr_enquiry['position']:'';
		$arr_save['comms_date']= new MongoDate();
		$arr_save['contact_address']['0']['contact_address_1'] = isset($arr_enquiry['default_address_1'])?$arr_enquiry['default_address_1']:'';
		$arr_save['contact_address']['0']['contact_address_2'] = isset($arr_enquiry['default_address_2'])?$arr_enquiry['default_address_2']:'';
		$arr_save['contact_address']['0']['contact_address_3'] = isset($arr_enquiry['default_address_3'])?$arr_enquiry['default_address_3']:'';
		$arr_save['contact_address']['0']['contact_town_city'] = isset($arr_enquiry['default_town_city'])?$arr_enquiry['default_town_city']:'';
		$arr_save['contact_address']['0']['contact_province_state'] = isset($arr_enquiry['default_province_state'])?$arr_enquiry['default_province_state']:'';
		$arr_save['contact_address']['0']['contact_zip_postcode'] = isset($arr_enquiry['default_zip_postcode'])?$arr_enquiry['default_zip_postcode']:'';


		$this->selectModel('Contact');
		$arr_contact = $arrtemp = array();

		if (isset($arr_enquiry['contact_id']) && is_object($arr_enquiry['contact_id'])) {
			$arr_contact = $this->Contact->select_one(array('_id' => new MongoId($arr_enquiry['contact_id'])));
		}
		else
		{
			$arr_contact = $this->Contact->select_all(array(
				'arr_where' => array('company_id' => new MongoId($arr_enquiry['_id'])),
				'arr_order' => array('_id' => -1),
			));
			$arrtemp = iterator_to_array($arr_contact);
			if (count($arrtemp) > 0) {
				$arr_contact = current($arrtemp);
			} else
				$arr_contact = array();
		}
		if (isset($arr_contact['_id'])) {
			$arr_save['contact_name']=isset($arr_contact['first_name'])?$arr_contact['first_name']:'';
			$arr_save['last_name']=isset($arr_contact['last_name'])?$arr_contact['last_name']:'';

			$arr_save['contact_id'] = $arr_contact['_id'];
		} else {
			$arr_save['contact_name'] = '';
			$arr_save['contact_id'] = '';
		}

		$arr_save['contact_from_id']=$this->Contact->user_id();
		$arr_save['contact_from']=$this->Contact->user_name();

		if ($this->Communication->save($arr_save)) {
			$this->redirect('/communications/entry/'. $this->Communication->mongo_id_after_save);
		}
		$this->redirect('/communications/entry');
	}

	function create_fax() {
		$this->selectModel('Enquiry');
		$arr_enquiry = $this->Enquiry->select_one(array('_id' => new MongoId($this->get_id())));

		$arr_save=array();
		$this->selectModel('Communication');
		$arr_save['code']=$this->Communication->get_auto_code('code');

		$arr_save['comms_type']='Fax';

		$arr_save['company_id']=isset($arr_enquiry['_id'])?$arr_enquiry['_id']:'';
		$arr_save['company_name']=isset($arr_enquiry['company'])?$arr_enquiry['company']:'';
		$arr_save['created_from']=isset($this->params->params['controller'])?$this->params->params['controller']:'';

		$arr_save['sign_off'] = 'Regards';
		$arr_save['position']=isset($arr_enquiry['position'])?$arr_enquiry['position']:'';
		$arr_save['position_id']=isset($arr_enquiry['position_id'])?$arr_enquiry['position_id']:'';
		$arr_save['email']=isset($arr_enquiry['contact_email'])?$arr_enquiry['contact_email']:'';
		$arr_save['phone']=isset($arr_enquiry['company_phone'])?$arr_enquiry['company_phone']:'';
		$arr_save['fax']=isset($arr_enquiry['company_fax'])?$arr_enquiry['company_fax']:'';
		$arr_save['comms_date']= new MongoDate();
		$arr_save['contact_address']['0']['contact_address_1'] = isset($arr_enquiry['default_address_1'])?$arr_enquiry['default_address_1']:'';
		$arr_save['contact_address']['0']['contact_address_2'] = isset($arr_enquiry['default_address_2'])?$arr_enquiry['default_address_2']:'';
		$arr_save['contact_address']['0']['contact_address_3'] = isset($arr_enquiry['default_address_3'])?$arr_enquiry['default_address_3']:'';
		$arr_save['contact_address']['0']['contact_town_city'] = isset($arr_enquiry['default_town_city'])?$arr_enquiry['default_town_city']:'';
		$arr_save['contact_address']['0']['contact_province_state'] = isset($arr_enquiry['default_province_state'])?$arr_enquiry['default_province_state']:'';
		$arr_save['contact_address']['0']['contact_zip_postcode'] = isset($arr_enquiry['default_zip_postcode'])?$arr_enquiry['default_zip_postcode']:'';
		$this->selectModel('Contact');
		$arr_contact = $arrtemp = array();

		if (isset($arr_enquiry['contact_id']) && is_object($arr_enquiry['contact_id'])) {
			$arr_contact = $this->Contact->select_one(array('_id' => new MongoId($arr_enquiry['contact_id'])));
		}
		else
		{
			$arr_contact = $this->Contact->select_all(array(
				'arr_where' => array('company_id' => new MongoId($arr_enquiry['_id'])),
				'arr_order' => array('_id' => -1),
			));
			$arrtemp = iterator_to_array($arr_contact);
			if (count($arrtemp) > 0) {
				$arr_contact = current($arrtemp);
			} else
				$arr_contact = array();
		}
		if (isset($arr_contact['_id'])) {
			$arr_save['contact_name']=isset($arr_contact['first_name'])?$arr_contact['first_name']:'';
			$arr_save['last_name']=isset($arr_contact['last_name'])?$arr_contact['last_name']:'';

			$arr_save['contact_id'] = $arr_contact['_id'];
		} else {
			$arr_save['contact_name'] = '';
			$arr_save['contact_id'] = '';
		}

		$arr_save['contact_from_id']=$this->Contact->user_id();
		$arr_save['contact_from']=$this->Contact->user_name();

		if ($this->Communication->save($arr_save)) {
			$this->redirect('/communications/entry/'. $this->Communication->mongo_id_after_save);
		}
		$this->redirect('/communications/entry');
	}



	function get_data_print_profile_list(){
		$this->selectModel('Enquiry');
		$arr_query = $this->Enquiry->select_all(array(
			'deleted' => false,
		));
		$tmp = array();
		foreach($arr_query as $key => $value){
			$keys = $value['no'];
			$tmp[$keys]['no'] = isset($value['no'])?$value['no']:'';
			$tmp[$keys]['company'] = isset($value['company'])?$value['company']:'';
			$tmp[$keys]['type'] = isset($value['type'])?$value['type']:'';
			$tmp[$keys]['category'] = isset($value['category'])?$value['category']:'';
			$tmp[$keys]['rating'] = isset($value['rating'])?$value['rating']:'';
			$tmp[$keys]['no_of_staff'] = isset($value['no_of_staff'])?$value['no_of_staff']:'';
			$tmp[$keys]['enquiry_value'] = isset($value['enquiry_value'])?(float)$value['enquiry_value']:0;
			$tmp[$keys]['contact_name'] = isset($value['contact_name'])?$value['contact_name']:'';
			$tmp[$keys]['mobile'] = isset($value['mobile'])?$value['mobile']:'';
			$tmp[$keys]['contact_email'] = isset($value['contact_email'])?$value['contact_email']:'';
		}
		//pr($tmp);die;
		return $tmp;
	}
	function print_profile_list_pdf(){
		$this->layout = 'pdf';
		$date_now = date('Ymd');
		$time=time();
		$filename = 'EPro'.$date_now.$time;
		$html='';
		$i=0;
		$total = 0;
		$tmp = $this->get_data_print_profile_list();
		foreach($tmp as $key=>$value){
			$total += isset($value['enquiry_value'])?(float)$value['enquiry_value']:0;
			if($i%2==0)
				$html .= ' <table cellpadding="4" cellspacing="0" class="tab_nd">';
			else
				$html .= '<table cellpadding="4" cellspacing="0" class="tab_nd2">';

			$html .= ' <tr class="border_2">
				<td width="10%" class="first top border_left border_btom">';

			if(isset($value['no']))
				$html .= $value['no'];

			$html .= '</td>
				<td width="31%" class="top border_btom border_left">';

			if(isset($value['company']))
				$html .= $value['company'];


			$html .='</td>
				<td width="16%" class="top border_btom border_left">';

			if(isset($value['type']))
				$html .= $value['type'];


			$html .='</td>
				<td width="16%" class="top border_btom border_left">';

			if(isset($value['category']))
				$html .= $value['category'];

			$html.='
				</td>
				<td align="center" width="6%" class="top border_btom border_left">';

			if(isset($value['rating']))
				$html .= $value['rating'];


			$html.='
				</td>
				<td align="center" width="8%" class="end top border_btom border_left">';

			if(isset($value['no_of_staff']))
				$html .= $value['no_of_staff'];


			$html.='
				</td>
				<td align="right" width="12.2%" class="end top border_btom border_left">';

			if(isset($value['enquiry_value']))
				$html .= $this->opm->format_currency($value['enquiry_value']);

			$html.='
				</td>
			</tr>
		</table>
	';


			$i+=1;
		}
		$html_new = $html;

		// =================================================== tao file PDF ==============================================//
		include(APP.'Vendor'.DS.'xtcpdf.php');

		$pdf = new XTCPDF();
		date_default_timezone_set('UTC');
		$pdf->today=date("g:i a, j F, Y");
		$textfont = 'freesans'; // looks better, finer, and more condensed than 'dejavusans'

		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Anvy Digital');
		$pdf->SetTitle('Anvy Digital Company');
		$pdf->SetSubject('Company');
		$pdf->SetKeywords('Company, PDF');

	// set default header data
		$pdf->setPrintHeader(true);
		$pdf->setPrintFooter(true);

	// set default monospaced font
		$pdf->SetDefaultMonospacedFont(2);

	// set margins
		$pdf->SetMargins(10, 52, 10);
		$pdf->file3 = 'img'.DS.'bar_662x23.png';

		$pdf->file2_left=115;
		$pdf->file2='img'.DS.'Enquiry_Profile_Listing.png';
		$pdf->file4 = 'img'.DS.'null.png';
		$pdf->file5 = 'img'.DS.'null.png';
		$pdf->file6 = 'img'.DS.'null.png';
		$pdf->file7 = 'img'.DS.'null.png';
		$pdf->print1='';


		$pdf->bar_top_left=136;


		$pdf->hidden_left=153;
		$pdf->hidden_content=' ';

		$pdf->bar_big_content='---------------------------------------------------------------------------------------------------------------------------------------------------------------';

		$pdf->bar_words_content='Ref no        Prospect                                          Type                       Category               Rating   Staff    Enquiry value';
		$pdf->bar_mid_content='         |                                                    |                               |                         |                          |              |';


		$pdf->printedat_left=134;
		$pdf->time_left=152;

		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, 30);

	// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		if (@file_exists(dirname(__FILE__).DS.'lang'.DS.'eng.php')) {
			require_once(dirname(__FILE__).DS.'lang'.DS.'eng.php');
			$pdf->setLanguageArray($l);
		}

	// ---------------------------------------------------------
	// set font
		$pdf->SetFont($textfont, '', 9);

	// add a page
		$pdf->AddPage();
		$pdf->SetMargins(10, 19, 10);

		$pdf->file1 = 'img'.DS.'null.png';
		$pdf->file2 = 'img'.DS.'null.png';
		$pdf->file4 = 'img'.DS.'null.png';
		$pdf->file5 = 'img'.DS.'null.png';
		$pdf->file6 = 'img'.DS.'null.png';
		$pdf->file7 = 'img'.DS.'null.png';
		$pdf->file3_top=10;
		$pdf->bar_words_top=11;
		$pdf->bar_mid_top=10.6;
		$pdf->hidden_content='';
		$pdf->bar_top_content='';
		$pdf->today='';
		$pdf->print='';
		$pdf->print1='';

		$html='
				<style>
					table{
						font-size: 12px;
						font-family: arial;
					}
					td.first{
						border-left:1px solid #e5e4e3;
					}
					td.end{
						border-right:1px solid #e5e4e3;
					}
					td.top{
						color:#fff;
						font-weight:bold;
						background-color:#911b12;
						border-top:1px solid #e5e4e3;
					}
					td.bottom{
						border-bottom:1px solid #e5e4e3;
					}
					.option{
						color: #3d3d3d;
						font-weight:bold;
						font-size:18px;
						text-align: center;
						width:100%;
					}
					.border_left{
						border-left:1px solid #A84C45;
					}
					.border_1{
						border-bottom:1px solid #911b12;
					}

				</style>
				<style>
			            table.tab_nd{
			                font-size: 12px;
			                font-family: arial;
			            }
			            table.tab_nd td.first{
			                border-left:1px solid #e5e4e3;
			            }
			            table.tab_nd td.end{
			                border-right:1px solid #e5e4e3;
			            }
			            table.tab_nd td.top{
			                background-color:#FDFBF9;
			                border-top:1px solid #e5e4e3;
			                font-weight: normal;
			                color: #3E3D3D;
			            }
			            table.tab_nd .border_2{
			                border-bottom:1px solid red;
			            }
			            table.tab_nd .border_left{
			                border-left:1px solid #E5E4E3;
			                border-bottom:1px solid #E5E4E3;
			            }
			            table.tab_nd .border_btom{
			                border-bottom:1px solid #E5E4E3;
			            }

			        </style>
			        <style>
			                table.tab_nd2{
			                    font-size: 12px;
			                    font-family: arial;
			                }
			                table.tab_nd2 td.first{
			                    border-left:1px solid #e5e4e3;
			                }
			                table.tab_nd2 td.end{
			                    border-right:1px solid #e5e4e3;
			                }
			                table.tab_nd2 td.top{
			                    background-color:#EDEDED;
			                    border-top:1px solid #e5e4e3;
			                    font-weight: normal;
			                    color: #3E3D3D;
			                }
			                table.tab_nd2 .border_2{
			                    border-bottom:1px solid red;
			                }
			                table.tab_nd2 .border_left{
			                    border-left:1px solid #E5E4E3;
			                    border-bottom:1px solid #E5E4E3;
			                }
			                table.tab_nd2 .border_btom{
			                    border-bottom:1px solid #E5E4E3;
			                }
			                .size_font{
			                    font-size: 12px !important;
			                }

			            </style>
			';
		$html.=$html_new;
		$html .= '

		<table cellpadding="3" cellspacing="0" class="tab_nd2">
			<tr class="border_2">
				<td width="85%" class="first top border_btom size_font" align="right"><b> Totals: </b>
					&nbsp;';



		$html .='
				</td>
				<td align="right" width="14.4%" class="end top border_btom">';

		$html .= $this->opm->format_currency($total);
		$html .='
					&nbsp;
				</td>
			</tr>
		</table>

		<table cellpadding="3" cellspacing="0" class="tab_nd2">
			<tr class="border_2">
				<td width="80%" class="first top border_btom size_font">
					&nbsp;<b>';

		$html .= $i;

		$html .=' records listed</b>
				</td>
				<td width="19.4%" class="end top border_btom">
					&nbsp;
				</td>
			</tr>
		</table>
		<div style=" clear:both; color: #c9c9c9;"><br />
	---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		</div><br />
		';
		$pdf->writeHTML($html, true, false, true, true, '');
		$pdf->Output(APP. 'webroot'.DS. 'upload'.DS .$filename.'.pdf', 'F');
		$this->redirect('/upload/'. $filename .'.pdf');
		die;
	}

	function get_data_referral_report(){
		$this->selectModel('Enquiry');
		$arr_query = $this->Enquiry->select_all(array(
			'arr_where'=>array('referred' => array('$in'=>array('Customer','Google Adwords','Google Organic','Phone Directory', 'Search engine','Web site','Marketing')),
			'deleted' => false,)
		));
		$tmp = array();
		$sum = 0;
		foreach($arr_query as $key => $value){
			$keys = $value['referred'];
			if(!isset($tmp[$keys]['total_enquiry']))
				$tmp[$keys]['total_enquiry'] = (isset($value['enquiry_value'])?(float)$value['enquiry_value']:0);
			else
				$tmp[$keys]['total_enquiry'] +=  (isset($value['enquiry_value'])?(float)$value['enquiry_value']:0);
			if(!isset($tmp[$keys]['qty']))
				$tmp[$keys]['qty'] = 1;
			else
				$tmp[$keys]['qty']++;
		}
		//pr($tmp);die;
		return $tmp;
	}
	function referral_report_pdf(){
		$this->layout = 'pdf';
		$date_now = date('Ymd');
		$time=time();
		$filename = 'EPro'.$date_now.$time;
		$html='';
		$i=0;
		$total = 0;
		$tmp = $this->get_data_referral_report();
		foreach($tmp as $key=>$value){
			$total += $value['total_enquiry'];
			$color = ($i%2==0? 2 : '');
			$html .= '
				<table cellpadding="4" cellspacing="0" class="tab_nd'.$color.'">
					<tr class="border_2">
						<td width="65%" class="first top border_left border_btom">'.$key.'</td>
						<td width="15%" class="top border_btom border_left">'.$value['qty'].'</td>
						<td align="right" width="19.2%" class="top border_btom border_left">'.$this->opm->format_currency($value['total_enquiry']).'</td>
					</tr>
				</table>

			';

			$i+=1;
		}
		//pr($total);die;
		$html_new = $html;

		// =================================================== tao file PDF ==============================================//
		include(APP.'Vendor'.DS.'xtcpdf.php');

		$pdf = new XTCPDF();
		date_default_timezone_set('UTC');
		$pdf->today=date("g:i a, j F, Y");
		$textfont = 'freesans'; // looks better, finer, and more condensed than 'dejavusans'

		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Anvy Digital');
		$pdf->SetTitle('Anvy Digital Company');
		$pdf->SetSubject('Company');
		$pdf->SetKeywords('Company, PDF');

	// set default header data
		$pdf->setPrintHeader(true);
		$pdf->setPrintFooter(true);

	// set default monospaced font
		$pdf->SetDefaultMonospacedFont(2);

	// set margins
		$pdf->SetMargins(10, 52, 10);
		$pdf->file3 = 'img'.DS.'bar_662x23.png';

		$pdf->file2_left=112;
		$pdf->file2='img'.DS.'Enquiry_Referral_Report.png';
		$pdf->file4 = 'img'.DS.'null.png';
		$pdf->file5 = 'img'.DS.'null.png';
		$pdf->file6 = 'img'.DS.'null.png';
		$pdf->file7 = 'img'.DS.'null.png';
		$pdf->print1='';


		$pdf->bar_top_left=136;


		$pdf->hidden_left=153;
		$pdf->hidden_content=' ';

		$pdf->bar_big_content='---------------------------------------------------------------------------------------------------------------------------------------------------------------';

		$pdf->bar_words_content='Referred by                                                                                                       Qty referrals           Value of enquiries   ';
		$pdf->bar_mid_content='         |                                                    |                               |                         |                          |              |';


		$pdf->printedat_left=134;
		$pdf->time_left=152;

		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, 30);

	// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		if (@file_exists(dirname(__FILE__).DS.'lang'.DS.'eng.php')) {
			require_once(dirname(__FILE__).DS.'lang'.DS.'eng.php');
			$pdf->setLanguageArray($l);
		}

	// ---------------------------------------------------------
	// set font
		$pdf->SetFont($textfont, '', 9);

	// add a page
		$pdf->AddPage();
		$pdf->SetMargins(10, 19, 10);

		$pdf->file1 = 'img'.DS.'null.png';
		$pdf->file2 = 'img'.DS.'null.png';
		$pdf->file4 = 'img'.DS.'null.png';
		$pdf->file5 = 'img'.DS.'null.png';
		$pdf->file6 = 'img'.DS.'null.png';
		$pdf->file7 = 'img'.DS.'null.png';
		$pdf->file3_top=10;
		$pdf->bar_words_top=11;
		$pdf->bar_mid_top=10.6;
		$pdf->hidden_content='';
		$pdf->bar_top_content='';
		$pdf->today='';
		$pdf->print='';
		$pdf->print1='';

		$html='
				<style>
					table{
						font-size: 12px;
						font-family: arial;
					}
					td.first{
						border-left:1px solid #e5e4e3;
					}
					td.end{
						border-right:1px solid #e5e4e3;
					}
					td.top{
						color:#fff;
						font-weight:bold;
						background-color:#911b12;
						border-top:1px solid #e5e4e3;
					}
					td.bottom{
						border-bottom:1px solid #e5e4e3;
					}
					.option{
						color: #3d3d3d;
						font-weight:bold;
						font-size:18px;
						text-align: center;
						width:100%;
					}
					.border_left{
						border-left:1px solid #A84C45;
					}
					.border_1{
						border-bottom:1px solid #911b12;
					}

				</style>
				<style>
			            table.tab_nd{
			                font-size: 12px;
			                font-family: arial;
			            }
			            table.tab_nd td.first{
			                border-left:1px solid #e5e4e3;
			            }
			            table.tab_nd td.end{
			                border-right:1px solid #e5e4e3;
			            }
			            table.tab_nd td.top{
			                background-color:#FDFBF9;
			                border-top:1px solid #e5e4e3;
			                font-weight: normal;
			                color: #3E3D3D;
			            }
			            table.tab_nd .border_2{
			                border-bottom:1px solid red;
			            }
			            table.tab_nd .border_left{
			                border-left:1px solid #E5E4E3;
			                border-bottom:1px solid #E5E4E3;
			            }
			            table.tab_nd .border_btom{
			                border-bottom:1px solid #E5E4E3;
			            }

			        </style>
			        <style>
			                table.tab_nd2{
			                    font-size: 12px;
			                    font-family: arial;
			                }
			                table.tab_nd2 td.first{
			                    border-left:1px solid #e5e4e3;
			                }
			                table.tab_nd2 td.end{
			                    border-right:1px solid #e5e4e3;
			                }
			                table.tab_nd2 td.top{
			                    background-color:#EDEDED;
			                    border-top:1px solid #e5e4e3;
			                    font-weight: normal;
			                    color: #3E3D3D;
			                }
			                table.tab_nd2 .border_2{
			                    border-bottom:1px solid red;
			                }
			                table.tab_nd2 .border_left{
			                    border-left:1px solid #E5E4E3;
			                    border-bottom:1px solid #E5E4E3;
			                }
			                table.tab_nd2 .border_btom{
			                    border-bottom:1px solid #E5E4E3;
			                }
			                .size_font{
			                    font-size: 12px !important;
			                }

			            </style>
			';
		$html.=$html_new;
		$html .= '

		<table cellpadding="3" cellspacing="0" class="tab_nd2">
			<tr class="border_2">
				<td width="85%" class="first top border_btom size_font" align="right"><b> Totals: </b>
					&nbsp;';



		$html .='
				</td>
				<td align="right" width="14.4%" class="end top border_btom">';

		$html .= $this->opm->format_currency($total);
		$html .='
					&nbsp;
				</td>
			</tr>
		</table>

		<table cellpadding="3" cellspacing="0" class="tab_nd2">
			<tr class="border_2">
				<td width="80%" class="first top border_btom size_font">
					&nbsp;<b>';

		$html .= $i;

		$html .=' records listed</b>
				</td>
				<td width="19.4%" class="end top border_btom">
					&nbsp;
				</td>
			</tr>
		</table>
		<div style=" clear:both; color: #c9c9c9;"><br />
	---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		</div><br />
		';
		$pdf->writeHTML($html, true, false, true, true, '');
		$pdf->Output(APP. 'webroot'.DS. 'upload'.DS .$filename.'.pdf', 'F');
		$this->redirect('/upload/'. $filename .'.pdf');
		die;

	}

	public function view_minilist(){
		$arr_where = $this->arr_search_where();
		$this->selectModel('Enquiry');
		$enquiries = $this->Enquiry->select_all(array(
											'arr_where' => $arr_where,
											'arr_field' => array('company','contact_name','referred','company_phone','mobile','contact_email','enquiry_value'),
											'arr_order' => array('_id'=>1),
											'limit'     => 2000
											));
		$arr_data = array();
		if($enquiries->count() > 0){
			$html='';
			$i=0;
			$total = 0;
			foreach($enquiries as $key => $enquiry){
				$total += isset($enquiry['enquiry_value'])?(float)$enquiry['enquiry_value']:0;
				$html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">';
				$html .= '<td>'.(isset($enquiry['company']) ? $enquiry['company'] : '') .'</td>';
				$html .= '<td>'.(isset($enquiry['contact_name']) ? $enquiry['contact_name'] : '') .'</td>';
				$html .= '<td>'.(isset($enquiry['referred']) ? $enquiry['referred'] : '') .'</td>';
				$html .= '<td>'.(isset($enquiry['company_phone']) ? $enquiry['company_phone'] : '') .'</td>';
				$html .= '<td>'.(isset($enquiry['mobile']) ? $enquiry['mobile'] : '') .'</td>';
				$html .= '<td>'.(isset($enquiry['contact_email']) ? $enquiry['contact_email'] : '') .'</td>';
				$html .= '<td class="right_text">'.(isset($enquiry['enquiry_value']) ? $this->opm->format_currency((int)$enquiry['enquiry_value']): '') .'</td>';
				$html .= '</tr>';
                $i++;
			}
			$html .='<tr class="last">
				 <td colspan="2" class="bold_text right_none">'.$i.' record(s) listed.</td>
				 <td colspan="4" class="right_text bold_text right_none">Total:</td>
				 <td class="right_text right_none">'.$this->opm->format_currency($total).'</td>
			  </tr>';
			$arr_data['title'] = array('Company','Contact','Referred by','Phone','Mobile','Email','Enquiry value'=>'text-align: right');
			$arr_data['content'] = $html;
			$arr_data['report_name'] = 'Enquiry Mini  Listing';
			$arr_data['report_file_name'] = 'En_'.md5(time());
			$arr_data['report_orientation'] = 'landscape';
		}
		$this->render_pdf($arr_data);
	}

}