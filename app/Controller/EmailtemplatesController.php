<?php

App::uses('AppController', 'Controller');

class EmailtemplatesController extends AppController {

	var $modelName = 'Emailtemplate';
	var $name = 'Emailtemplates';
	var $field_button = '';
    public $opm; //Option Module

	public function beforeFilter() {
		// goi den before filter cha
		parent::beforeFilter();
		$this->set('title_entry', 'Email Templates');
		$this->set('base_root',basename(ROOT));
	}
	public function entry( $id = 0,$num_position= -1){
		$iditem = $this->get_id();
		if(isset($_POST['data'])){
			$this->selectModel('Emailtemplate');
			$arr_save = $_POST['data']['EmailTemplate'];
			// }
			$arr_save['_id'] = new MongoId($iditem);
			$this->Emailtemplate->save($arr_save);
		}
		$this->selectModel('Setting');
		$template_type = $this->Setting->select_option(array('setting_value'=>'email_template_type'));
		$template_folder = $this->get_folder();
		$arr_tmp = $this->entry_init( $id, $num_position, 'Emailtemplate', 'Emailtemplates');
		$this->selectModel('Emailtemplate');
		$arr_template = $this->Emailtemplate->select_one(array('_id'=> new MongoId($iditem)));
		$default_field = $this->Setting->select_one(array('setting_value'=>'email_template_default'));
		if(isset($default_field['option'])){
			$this->field_button = '';
			foreach($default_field['option'] as $value){
				$this->field_button .= '<span rel="'.$value['value'].'">'.$value['name'].'</span>';
		}
		if($arr_template['folder_id']!='')
			$this->set('field_button',$this->get_folder($arr_template['folder_id']));
		}
		$this->set('template_type',$template_type);
		$this->set('template_folder',$template_folder);
		$this->set('arr_template',$arr_template);
		$this->set('iditem',$iditem);
	}
	function test(){
		$email['salesorder_date'] = '20/12/2014';
		$template = '<span class="field_span" contenteditable="false" rel="{{SALESORDER_DATE}}" unselectable="on">Date</span> <span class="field_span" contenteditable="false" rel="{{CONTACT_FROM}}" unselectable="on">From</span> <span class="field_span" contenteditable="false" rel="{{CONTACT_TO}}" unselectable="on">To</span>{{content}}<span class="field_span" contenteditable="false" rel="{{CONTENT}}" unselectable="on">Content</span>';
		preg_match_all("!<span[^>]+>(.*?)</span>!", $template, $matches);
		$all_span = $matches[0];
		foreach($all_span as $span){
			//Lay noi dung trong rel
			preg_match_all("/<span [^>]+ rel=\"{{(.+?)}}\" [^>]+>[^>]+<\/span>/",$span,$content_matches);
			foreach($content_matches[1] as $val){
				$val = strtolower($val);
				$template = str_replace($span, (isset($email[$val]) ? $email[$val] : ''), $template);
			}
		}
		pr($template);
		die;
	}
	function get_folder($name=''){
		$this->selectModel('Setting');
		if(isset($_POST['folder_name']) || $name!=''){
			if(isset($_POST['folder_name']))
				$name = $_POST['folder_name'];
			$email_folder = $this->Setting->select_one(array('setting_value'=>$name));
			if(isset($email_folder['option'])){
				$html = '';
				if($name!='email_template_default'){
					$default_field = $this->Setting->select_one(array('setting_value'=>'email_template_default'));
					if(isset($default_field['option'])){
						foreach($default_field['option'] as $value)
							$html .= '<span class="field_span" contenteditable="false" unselectable="on" rel="'.$value['value'].'">'.$value['name'].'</span>';
					}
				}
				foreach($email_folder['option'] as $value){
					$html .= '<span class="field_span" contenteditable="false" unselectable="on" rel="'.$value['value'].'">'.$value['name'].'</span>';
				}
				if(!$this->request->is('ajax'))
					return $html;
				echo $html;
			}
			die;
		}
		$email_folder = $this->Setting->select_all(array(
		                           'arr_where'=>array(
		                                              'setting_value'=>new MongoRegex('/email_template/i'),
		                                              )
		                           ));
		$folder = array();
		if($email_folder->count()){
			foreach($email_folder as $value){
				if($value['setting_value']=='email_template_type') continue;
				$name = str_replace('Email Template: ', '', $value['setting_name']);
				$folder[$value['setting_value']] = $name;
			}
			ksort($folder);
		}
		return $folder;
	}
	public function entry_search_all(){
		$this->Session->delete('emailtemplates_entry_search_cond');
		$this->redirect('/emailtemplates/lists');
	}
	public function entry_search() {
		if (!empty($this->data) && $this->request->is('ajax')) {
			$post = $this->data['EmailTemplate'];
			$cond = array();
			$post = $this->Common->strip_search($post);

			if( strlen($post['no']) > 0 )$cond['no'] = (int)$post['no'];
			if( strlen($post['name']) > 0 )$cond['name'] = new MongoRegex('/' . trim($post['name']) . '/i');
			$this->selectModel('Emailtemplate');
			$tmp = $this->Emailtemplate->select_one($cond);
			if( $tmp ){
				$this->Session->write('tasks_entry_search_cond', $cond);

				$cond['_id'] = array('$ne' => $tmp['_id']);
				$tmp1 = $this->Emailtemplate->select_one($cond);
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
	}
	function add(){
		$this->selectModel('Emailtemplate');
		if ($this->Emailtemplate->add()){
			$this->redirect('/emailtemplates/entry/' . $this->Emailtemplate->mongo_id_after_save);
		}
		die;
	}
	function delete($id = 0) {
		$arr_save['_id'] = $id;
		$this->selectModel('Emailtemplate');
		$template = $this->Emailtemplate->select_one(array('_id'=>new MongoId($id)));
		if($template['no']==1){
			echo 'You can not delete this record. Click <a style="color: #000;" href="'.URL.'/emailtemplates/entry">here</a> to return.';
			die;
		}
		$arr_save['deleted'] = true;
		if ($this->Emailtemplate->save($arr_save)) {
			if(!$this->request->is('ajax')){
				$this->Session->delete('EmailtemplatesViewId');
				$this->redirect('/emailtemplates/entry');
			} else
				echo 'ok';
		} else {
			echo 'Error: ' . $this->Emailtemplate->arr_errors_save[1];
		}
		die;
	}
	function auto_save(){
		if (!empty($_POST)) {
			$this->selectModel('Emailtemplate');
			$arr_save = $_POST['data']['EmailTemplate'];
			// if(isset($arr_save['template'])){
			// 	$find_string = '{{content}}';
			// 	if(strpos($arr_save['template'], $find_string)===false)
			// 		$arr_save['template'] .= $find_string;
			// 	else if(substr_count($arr_save['template'],$find_string)>1){
			// 		$position = strpos($arr_save['template'], $find_string);
			// 		$position += strlen($find_string);
			// 		$start_string = substr($arr_save['template'], 0, $position);
			// 		$end_string = substr($arr_save['template'], $position);
			// 		$end_string = str_replace($find_string, '', $end_string);
			// 		$arr_save['template'] = $start_string.$end_string;
			// 	}
			// }
			$arr_save['_id'] = new MongoId($this->get_id());
			if($this->Emailtemplate->save($arr_save)){
				echo 'ok';
			} else {
				echo 'Error: ' . $this->Emailtemplate->arr_errors_save[1];
			}
		}
		die;
	}
	function lists() {

		$this->selectModel('Emailtemplate');

		$limit = LIST_LIMIT; $skip = 0;

		// dùng cho sort
		$sort_field = '_id';
		$sort_type = 1;
		if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
			if( $_POST['sort']['type'] == 'desc' ){
				$sort_type = -1;
			}
			$sort_field = $_POST['sort']['field'];
			$this->Session->write('emailtemplates_lists_search_sort', array($sort_field, $sort_type));

		}elseif( $this->Session->check('emailtemplates_lists_search_sort') ){
			$session_sort = $this->Session->read('emailtemplates_lists_search_sort');
			$sort_field = $session_sort[0];
			$sort_type = $session_sort[1];
		}
		$arr_order = array($sort_field => $sort_type);
		$this->set('sort_field', $sort_field);
		$this->set('sort_type', ($sort_type === 1)?'asc':'desc');
		// dùng cho điều kiện
		$cond = array();
		if( $this->Session->check('emailtemplates_entry_search_cond') ){
			$cond = $this->Session->read('emailtemplates_entry_search_cond');
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

		$arr_emailtemplates = $this->Emailtemplate->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'limit' => $limit,
			'skip' => $skip
		));
		$this->set('arr_emailtemplates', $arr_emailtemplates);
		// dùng cho phân trang
		$total_page = $total_record = $total_current = 0;
		if( is_object($arr_emailtemplates) ){
			$total_current = $arr_emailtemplates->count(true);
			$total_record = $arr_emailtemplates->count();
			if( $total_record%$limit != 0 ){
				$total_page = floor($total_record/$limit) + 1;
			}else{
				$total_page = $total_record/$limit;
			}
		}
		$this->set('total_current', $total_current);
		$this->set('total_page', $total_page);
		$this->set('total_record', $total_record);

		$this->selectModel('Contact');
		$model_contact = $this->Contact;
		$this->set('model_contact',$model_contact);
		if ($this->request->is('ajax')) {
			$this->render('lists_ajax');
		}

		$this->set('sum', $total_record);
	}
}