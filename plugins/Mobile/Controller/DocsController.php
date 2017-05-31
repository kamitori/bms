<?php
class DocsController extends MobileAppController {
	var $modelName = 'Doc';
	var $name = 'Docs';
	function beforeFilter() {
		parent::beforeFilter();
	}

	public function entry($id = '0', $num_position = -1) {
		$arr_tmp = $this->entry_init($id, $num_position, 'Doc', 'docs');

		$this->selectModel('Setting');
		$arr_docs_type = $this->Setting->select_option(array('setting_value' => 'docs_type'), array('option'));
		$this->set('arr_docs_type', $arr_docs_type);
		
		$arr_docs_category = $this->Setting->select_option(array('setting_value' => 'product_category'), array('option'));
		$this->set('arr_docs_category', $arr_docs_category);

		if (isset($arr_tmp['status_id'])) {
			$arr_tmp['status'] = $arr_tmp['status_id'];
		}

		$this->set('arr_country', $this->Setting->select_option(array('setting_value' => 'lists_country'), array('option')));


		$arr_tmp1['Doc'] = $arr_tmp;
		$this->data = $arr_tmp1;
		$arr_doc_id = array();
		if (isset($arr_tmp['our_rep_id']))
			$arr_doc_id[] = $arr_tmp['our_rep_id'];
	}

	function auto_save() {
		if (!empty($this->data)) {
			$id = $this->get_id();
			$arr_return = array();
			$arr_post_data = $this->data['Doc'];
			$arr_save = $arr_post_data;
			$arr_save['_id'] = new MongoId($id);
			$this->selectModel('Doc');
		
			if(isset($arr_save['no'])){
				if(!is_numeric($arr_save['no'])){
					echo json_encode(array('status'=>'error','message'=>'must_be_numberic'));
					die;
				}
				$arr_tmp = $this->Doc->select_one(array('no' => (int) $arr_save['no'], '_id' => array('$ne' => new MongoId($id))));
				if (isset($arr_tmp['no'])) {
					echo json_encode(array('status'=>'error','message'=>'ref_no_existed'));
					die;
				}
			}

			if( strlen($arr_save['name']) > 0 )
				$arr_save['name']{0} = strtoupper($arr_save['name']{0});

			$this->selectModel('Doc');
			if ($this->Doc->save($arr_save))
				$arr_return['status'] = 'ok';
			else
				$arr_return = array('status'=>'error','message'=>'Error: ' . $this->Doc->arr_errors_save[1]);
			echo json_encode($arr_return);
		}
		die;
	}

	function lists( $content_only = '' ) {
		$this->selectModel('Doc');
		$limit = 20; $skip = 0;
		// dùng cho sort
		$sort_field = 'no';
		$sort_type = -1;
		if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
			if( $_POST['sort']['type'] == 'desc' ){
				$sort_type = -1;
			}
			$sort_field = $_POST['sort']['field'];
			$this->Session->write('docs_lists_search_sort', array($sort_field, $sort_type));

		}elseif( $this->Session->check('docs_lists_search_sort') ){
			$session_sort = $this->Session->read('docs_lists_search_sort');
			$sort_field = $session_sort[0];
			$sort_type = $session_sort[1];
		}
		$arr_order = array($sort_field => $sort_type);
		$this->set('sort_field', $sort_field);
		$this->set('sort_type', ($sort_type === 1)?'asc':'desc');


		// dùng cho điều kiện
		$cond = array();
		if( $this->Session->check('docs_entry_search_cond') ){
			$cond = $this->Session->read('docs_entry_search_cond');
		}

		// dùng cho phân trang
		if(isset($_POST['offset'])){
			$skip = $_POST['offset'];
		}
		// query
		$arr_docs = $this->Doc->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'limit' => $limit,
			'skip' => $skip
		));
		$this->set('arr_docs', $arr_docs);
		$this->selectModel('Setting');
		$this->set('arr_docs_type', $this->Setting->select_option(array('setting_value' => 'docs_type'), array('option')));

		if($content_only!=''){
			$this->render("lists_ajax");
		}
	}

	function delete($id = 0) {
		$arr_save['_id'] = $id;
		$arr_save['deleted'] = true;
		$error = 0;
		if (!$error) {
			$this->selectModel('Doc');
			if ($this->Doc->save($arr_save)) {
				$this->Session->delete($this->name.'ViewId');
				if($this->request->is('ajax'))
					echo 'ok';
				else
					$this->redirect('/mobile/docs/entry');
			} else {
				echo 'Error: ' . $this->Doc->arr_errors_save[1];
			}
		}
		die;
	}

	public function add() {
		$this->selectModel('Doc');
		$arr_save = array();

		$this->Doc->arr_default_before_save = $arr_save;
		if ($this->Doc->add())
			$this->redirect('/mobile/docs/entry/' . $this->Doc->mongo_id_after_save);
		die;
	}
}