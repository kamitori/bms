<?php

App::uses('AppController', 'Controller');

class DocsController extends AppController {

    var $modelName = 'Doc';

    public function beforeFilter() {
        // goi den before filter cha
        parent::beforeFilter();

        $this->set('title_entry', 'Docs');
    }

    // public function index(){
    // 	echo $this->Session->read('calendar_last_visit');
    // 	die;
    // }
    public function swith_options($keys){
        if($keys == 'documents_added_today'){
            $start_current_time = strtotime(date('Y-m-d 00:00:00'));
            $end_current_time = strtotime(date('Y-m-d 23:59:59'));
            $start_id = new MongoId(sprintf("%08x%016x", $start_current_time, 0));
            $end_id = new MongoId(sprintf("%08x%016x", $end_current_time, 0));
            $arr_where = array(
                               '_id'=>array('$gte'=>$start_id, '$lte'=>$end_id),
                               );
            $this->Session->write('docs_entry_search_cond', $arr_where);
            echo URL . '/'. $this->params->params['controller'].'/lists';
        } else if($keys == 'export_document')
            echo URL . '/'. $this->params->params['controller'].'/entry_export/'.$this->get_id();
        else if($keys == 'create_new_version_duplicate')
            echo URL . '/'. $this->params->params['controller'].'/create_new_version_duplicate/'.$this->get_id();
        else if($keys == 'email_document')
            echo URL . '/communications/add_comm_from_module_from_email/Email/'.$this->get_id().'/Doc/Docs/'.$this->get_id();
        else
            echo URL . '/'. $this->params->params['controller'].'/lists';
        die;

    }
    function auto_save() {
        if (!empty($this->data)) {
            $arr_post_data = $this->data['Doc'];
            $arr_save = $arr_post_data;

            $this->selectModel('Doc');
            if ($this->Doc->save($arr_save)) {
                echo 'ok';
            } else {
                echo 'Error: ' . $this->Doc->arr_errors_save[1];
            }
        }
        die;
    }

    function delete($id = 0) {
        $arr_save['_id'] = $id;
        $arr_save['deleted'] = true;
        $error = 0;
        if (!$error) {
            $this->selectModel('Doc');
            if ($this->Doc->save($arr_save)) {

                $this->Session->delete('Doc_entry_id');
                $this->redirect('/docs/entry');
            } else {
                echo 'Error: ' . $this->Doc->arr_errors_save[1];
            }
        }
        die;
    }

    function _add_get_info_save() {
        $this->selectModel('Doc');
        $arr_tmp = $this->Doc->select_one(array(), array('no'), array('no' => -1));
        $arr_save = array();
        $arr_save['no'] = 1;
        $arr_save['path'] = $arr_save['description'] = $arr_save['type'] = $arr_save['ext'] = $arr_save['category'] = $arr_save['name'] = '';
        $arr_save['create_by_module'] = 'Document';

        if (isset($arr_tmp['no'])) {
            $arr_save['no'] = $arr_tmp['no'] + 1;
        }
        return $arr_save;
    }

    public function add($module = '', $module_id = '', $module_detail = '') {
        if($module!=''){
            if(!$this->check_permission($module.'_@_documents_tab_@_add')
                &&!$this->check_permission('docs_@_entry_@_add')){
                $this->error_auth();
            }
        }elseif(!$this->check_permission('docs_@_entry_@_add')){
             $this->error_auth();
        }
        $arr_save = $this->_add_get_info_save();
        if ($module != '') {
            $arr_save['create_by_module'] = $module;
        }
        $this->selectModel('Doc');
        if ($this->Doc->save($arr_save)) {

            // Save to DocUse db
            if ($module != '') {
                $arr_save = array();
                $arr_save['module'] = $module;
                $arr_save['doc_id'] = $this->Doc->mongo_id_after_save;
                $arr_save['create_by_module'] = $module;
                if ($module_detail != '') {
                    $arr_save['module_detail'] = $module_detail;
                }
                if (strlen($module_id) > 0) {
                    $arr_save['module_id'] = new MongoId($module_id);
                }
                $this->selectModel('DocUse');
                if (!$this->DocUse->save($arr_save)) {
                    echo 'Error add new. Please contact IT developer. Error: ' . $this->DocUse->arr_errors_save[1];
                }
            }

            $this->redirect('/docs/entry/' . $this->Doc->mongo_id_after_save);
        } else {
            echo 'Error: ' . $this->Doc->arr_errors_save[1];
        }
        die;
    }
	public function add_from_option($module = '', $module_id = '', $file='' , $filename='',$controller='') {
		$arr_save = array();
		$this->selectModel('Doc');
		$arr_save['no']=$this->Doc->get_auto_code('no');
		if ($module != '') {
			$arr_save['create_by_module'] = $module;
		}

		$arr_save['path']=str_replace(',','\\',$file);
		$arr_save['name']=$filename;
		$arr_save['ext']='pdf';
		$arr_save['location']=$controller;
		$arr_save['type']='application/pdf';
		date_default_timezone_set('UTC');
		$arr_save['description']='Created at : '.date("g:i a, j F, Y");

		if ($this->Doc->save($arr_save)) {
			$v_doc_id='';
			// Save to DocUse db
			if ($module != '') {
				$arr_save = array();
				$arr_save['module'] = $module;

				$v_doc_id=$this->Doc->mongo_id_after_save;

				$arr_save['doc_id'] =$v_doc_id;
				$arr_save['create_by_module'] = $module;
				if ($module != '') {
					$arr_save['module_detail'] = $module;
				}
				if (strlen($module_id) > 0) {
					$arr_save['module_id'] = new MongoId($module_id);
				}
//				$this->selectModel('DocUse');
//				if (!$this->DocUse->save($arr_save)) {
//					echo 'Error add new. Please contact IT developer. Error: ' . $this->DocUse->arr_errors_save[1];
//				}
			}


			$this->redirect('/communications/add_comm_from_module_from_email/Email/'.$module_id.'/'.$module.'/'.$controller.'/'.$v_doc_id.'');



		} else {
			echo 'Error: ' . $this->Doc->arr_errors_save[1];
		}
		die;
	}
    public function entry($id = '0', $num_position = -1) {

        $arr_tmp = $this->entry_init($id, $num_position, 'Doc', 'docs');

        $arr_tmp1['Doc'] = $arr_tmp;
        $this->data = $arr_tmp1;

        // lấy danh sách tất cả các module dùng Doc này
        $this->selectModel('DocUse');
        $arr_docuse_tmp = $this->DocUse->select_all(array(
            'arr_where' => array(
                'doc_id' => $arr_tmp1['Doc']['_id']
            )
        ));
        $arr_docuse = $arr_contact_id = array();
        foreach ($arr_docuse_tmp as $key => $value) {
            $arr_docuse[] = $value;
            $arr_contact_id[] = (object) $value['created_by'];
        }
        $this->set('arr_docuse', $arr_docuse);

        // lấy ra category
        $this->selectModel('Setting');
        $this->set('arr_docs_category', $this->Setting->select_option(array('setting_value' => 'docs_category'), array('option')));

        $this->show_footer_info($arr_tmp);
    }

    public function entry_upload_file() {
        if (!empty($this->data)) {

            $post_file = $this->data['Doc']['file'];

            $file = $this->Common->move_file($post_file);
            $arr_save = array();
            $arr_save['name'] = $post_file['name'];
            $arr_save['path'] = $file;
            $arr_save['_id'] = $this->data['Doc']['_id'];
            $arr_save['type'] = $post_file['type'];
            if ($arr_save['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                $arr_save['type'] = 'excel';
            }
            $arr_save['ext'] = strtolower(substr(strrchr($post_file['name'], '.'), 1));

            $this->selectModel('Doc');

            if ($this->Doc->save($arr_save)) {
                $this->redirect('/docs/entry/' . $arr_save['_id']);
            } else {
                echo 'Error: ' . $this->Doc->arr_errors_save[1];
                die;
            }
        }
        echo 'Error empty this->data';
        die;
    }

    function entry_export($id = null) {
        $this->selectModel('Doc');
        $arr_tmp = $this->Doc->select_one(array('_id' => new MongoId($id)), array('_id', 'name', 'path', 'ext'));

        if (!isset($arr_tmp['name'])) {
            echo 'This file does not exist, please contact administrator';
            die;
        }

        $filen_path = APP . 'webroot' . $arr_tmp['path'];
        if (!file_exists($filen_path)) {
            echo 'This file does not exist, please contact administrator and re-upload this file. Thank you.';
            exit;
        }

        $mimeType = array(
            'ai' => 'application/postscript', 'bcpio' => 'application/x-bcpio', 'bin' => 'application/octet-stream',
            'ccad' => 'application/clariscad', 'cdf' => 'application/x-netcdf', 'class' => 'application/octet-stream',
            'cpio' => 'application/x-cpio', 'cpt' => 'application/mac-compactpro', 'csh' => 'application/x-csh',
            'csv' => 'application/csv', 'dcr' => 'application/x-director', 'dir' => 'application/x-director',
            'dms' => 'application/octet-stream', 'doc' => 'application/msword', 'drw' => 'application/drafting',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'one' => 'application/onenote',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'dvi' => 'application/x-dvi', 'dwg' => 'application/acad', 'dxf' => 'application/dxf',
            'dxr' => 'application/x-director', 'eot' => 'application/vnd.ms-fontobject', 'eps' => 'application/postscript',
            'exe' => 'application/octet-stream', 'ez' => 'application/andrew-inset',
            'flv' => 'video/x-flv', 'gtar' => 'application/x-gtar', 'gz' => 'application/x-gzip',
            'bz2' => 'application/x-bzip', '7z' => 'application/x-7z-compressed', 'hdf' => 'application/x-hdf',
            'hqx' => 'application/mac-binhex40', 'ico' => 'image/vnd.microsoft.icon', 'ips' => 'application/x-ipscript',
            'ipx' => 'application/x-ipix', 'js' => 'application/x-javascript', 'latex' => 'application/x-latex',
            'lha' => 'application/octet-stream', 'lsp' => 'application/x-lisp', 'lzh' => 'application/octet-stream',
            'man' => 'application/x-troff-man', 'me' => 'application/x-troff-me', 'mif' => 'application/vnd.mif',
            'ms' => 'application/x-troff-ms', 'nc' => 'application/x-netcdf', 'oda' => 'application/oda',
            'otf' => 'font/otf', 'pdf' => 'application/pdf',
            'pgn' => 'application/x-chess-pgn', 'pot' => 'application/mspowerpoint', 'pps' => 'application/mspowerpoint',
            'ppt' => 'application/mspowerpoint', 'ppz' => 'application/mspowerpoint', 'pre' => 'application/x-freelance',
            'prt' => 'application/pro_eng', 'ps' => 'application/postscript', 'roff' => 'application/x-troff',
            'scm' => 'application/x-lotusscreencam', 'set' => 'application/set', 'sh' => 'application/x-sh',
            'shar' => 'application/x-shar', 'sit' => 'application/x-stuffit', 'skd' => 'application/x-koan',
            'skm' => 'application/x-koan', 'skp' => 'application/x-koan', 'skt' => 'application/x-koan',
            'smi' => 'application/smil', 'smil' => 'application/smil', 'sol' => 'application/solids',
            'spl' => 'application/x-futuresplash', 'src' => 'application/x-wais-source', 'step' => 'application/STEP',
            'stl' => 'application/SLA', 'stp' => 'application/STEP', 'sv4cpio' => 'application/x-sv4cpio',
            'sv4crc' => 'application/x-sv4crc', 'svg' => 'image/svg+xml', 'svgz' => 'image/svg+xml',
            'swf' => 'application/x-shockwave-flash', 't' => 'application/x-troff',
            'tar' => 'application/x-tar', 'tcl' => 'application/x-tcl', 'tex' => 'application/x-tex',
            'texi' => 'application/x-texinfo', 'texinfo' => 'application/x-texinfo', 'tr' => 'application/x-troff',
            'tsp' => 'application/dsptype', 'ttf' => 'font/ttf',
            'unv' => 'application/i-deas', 'ustar' => 'application/x-ustar',
            'vcd' => 'application/x-cdlink', 'vda' => 'application/vda', 'xlc' => 'application/vnd.ms-excel',
            'xll' => 'application/vnd.ms-excel', 'xlm' => 'application/vnd.ms-excel', 'xls' => 'application/vnd.ms-excel',
            'xlw' => 'application/vnd.ms-excel', 'zip' => 'application/zip', 'aif' => 'audio/x-aiff', 'aifc' => 'audio/x-aiff',
            'aiff' => 'audio/x-aiff', 'au' => 'audio/basic', 'kar' => 'audio/midi', 'mid' => 'audio/midi',
            'midi' => 'audio/midi', 'mp2' => 'audio/mpeg', 'mp3' => 'audio/mpeg', 'mpga' => 'audio/mpeg',
            'ra' => 'audio/x-realaudio', 'ram' => 'audio/x-pn-realaudio', 'rm' => 'audio/x-pn-realaudio',
            'rpm' => 'audio/x-pn-realaudio-plugin', 'snd' => 'audio/basic', 'tsi' => 'audio/TSP-audio', 'wav' => 'audio/x-wav',
            'asc' => 'text/plain', 'c' => 'text/plain', 'cc' => 'text/plain', 'css' => 'text/css', 'etx' => 'text/x-setext',
            'f' => 'text/plain', 'f90' => 'text/plain', 'h' => 'text/plain', 'hh' => 'text/plain', 'htm' => 'text/html',
            'html' => 'text/html', 'm' => 'text/plain', 'rtf' => 'text/rtf', 'rtx' => 'text/richtext', 'sgm' => 'text/sgml',
            'sgml' => 'text/sgml', 'tsv' => 'text/tab-separated-values', 'tpl' => 'text/template', 'txt' => 'text/plain',
            'xml' => 'text/xml', 'avi' => 'video/x-msvideo', 'fli' => 'video/x-fli', 'mov' => 'video/quicktime',
            'movie' => 'video/x-sgi-movie', 'mpe' => 'video/mpeg', 'mpeg' => 'video/mpeg', 'mpg' => 'video/mpeg',
            'qt' => 'video/quicktime', 'viv' => 'video/vnd.vivo', 'vivo' => 'video/vnd.vivo', 'gif' => 'image/gif',
            'ief' => 'image/ief', 'jpe' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'jpg' => 'image/jpeg',
            'pbm' => 'image/x-portable-bitmap', 'pgm' => 'image/x-portable-graymap', 'png' => 'image/png',
            'pnm' => 'image/x-portable-anymap', 'ppm' => 'image/x-portable-pixmap', 'ras' => 'image/cmu-raster',
            'rgb' => 'image/x-rgb', 'tif' => 'image/tiff', 'tiff' => 'image/tiff', 'xbm' => 'image/x-xbitmap',
            'xpm' => 'image/x-xpixmap', 'xwd' => 'image/x-xwindowdump', 'ice' => 'x-conference/x-cooltalk',
            'iges' => 'model/iges', 'igs' => 'model/iges', 'mesh' => 'model/mesh', 'msh' => 'model/mesh',
            'silo' => 'model/mesh', 'vrml' => 'model/vrml', 'wrl' => 'model/vrml',
            'mime' => 'www/mime', 'pdb' => 'chemical/x-pdb', 'xyz' => 'chemical/x-pdb');

        if (isset($mimeType[$arr_tmp['ext']])) {
            //$this->set('cache', '3 days');
            $this->set('download', true);
            $this->set('name', str_replace('.' . $arr_tmp['ext'], '', $arr_tmp['name']));
            $this->set('id', basename($filen_path));
            $this->set('path', 'webroot' . dirname($arr_tmp['path']) . DS);
            //echo mime_content_type(DIR_UPLOAD.$this->data['Uploader']['file']);
            $this->set('extension', $arr_tmp['ext']);

            $this->viewClass = 'Media';
            $this->autoLayout = false;
        } else {
            $this->redirect($arr_tmp['path']);
            die;
        }
    }

    public function entry_docuse_delete($id) {
        $arr_save = array();
        $arr_save['_id'] = $id;
        $arr_save['deleted'] = true;
        $this->selectModel('DocUse');
        if ($this->DocUse->save($arr_save)) {
            echo 'ok';
        } else {
            echo 'Error: ' . $this->DocUse->arr_errors_save[1];
        }
        die;
    }

    public function lists() {
        $arr_where = array();
        if($this->Session->check('docs_entry_search_cond'))
            $arr_where = $this->Session->read('docs_entry_search_cond');
        $this->selectModel('Doc');
        /*if (isset($_SESSION['sort'])) {
            $order = $_SESSION['sort'];
        } else {
            $order = array('_id' => -1);
        }*/
        // load dong thu may trong csdl
        $skip = 0;
        $sort_field = '_id';
        $limit = LIST_LIMIT;
        $sort_type = -1;

        if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
            if( $_POST['sort']['type'] == 'desc' ){
                $sort_type = 1;
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

        //print_r($_REQUEST);die;
        /*if ($this->request->is('ajax')) {
            // set offset load_more
            if (isset($_REQUEST['offset'])) {
                $skip = (int) $_REQUEST['offset'];
            }
            // end seach
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
            $order = array($sort_key => $sort);
            $_SESSION['sort'] = $order;
        }*/

        $page_num = 1;
        if( isset($_POST['pagination']) && $_POST['pagination']['page-num'] > 0){
            $page_num = $_POST['pagination']['page-num'];
            $limit = $_POST['pagination']['page-list'];
            $skip = $limit*($page_num - 1);
        }
        $this->set('page_num', $page_num);
        $this->set('limit', $limit);

        $arr_docs = $this->Doc->select_all(array(
            'arr_where' => $arr_where,
            'arr_order' => $arr_order,
            'limit' => $limit,
            'skip' => $skip
        ));
        $this->set('arr_docs', $arr_docs);

        // lấy ra category
        $this->selectModel('Setting');
        $this->set('arr_docs_category', $this->Setting->select_option(array('setting_value' => 'docs_category'), array('option')));
        // render ajax

        $total_page = $total_record = $total_current = 0;
        if( is_object($arr_docs) ){
            $total_current = $arr_docs->count(true);
            $total_record = $arr_docs->count();
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
    }

    public function lists_delete($id = 0) {
        $arr_save['_id'] = $id;
        $arr_save['deleted'] = true;
        $error = 0;
        if (!$error) {
            $this->selectModel('Doc');
            if ($this->Doc->save($arr_save)) {
                echo 'ok';
            } else {
                echo 'Error: ' . $this->Doc->arr_errors_save[1];
            }
        }
        die;
    }

    // Popup form orther module
    public function popup($key = '') {

        $this->set('key', $key);

        $this->selectModel('Doc');
        $cond = array();
        $this->identity($cond);
        $arr_doc = $this->Doc->select_all(array(
                'arr_where' => $cond,
                // 'arr_field' => array('name', 'is_customer', 'is_employee', 'company_id', 'company_name'),
                'arr_order'=>array('no'=>1),
        ));
        $this->set('arr_doc', $arr_doc);

        $this->selectModel('Setting');
        $this->set('arr_docs_category', $this->Setting->select_option(array('setting_value' => 'docs_category'), array('option')));

        $this->layout = 'ajax';
    }
    function create_new_version_duplicate($doc_id){
        $this->selectModel('Doc');
        $this->selectModel('DocUse');
        $arr_save = $this->Doc->select_one(array('_id'=>new MongoId($doc_id)));
        $arr_save['no'] = $this->Doc->get_auto_code('no');
        unset($arr_save['_id'] );
        $this->Doc->save($arr_save);
        $new_doc_id = $this->Doc->mongo_id_after_save;
        $docuses = $this->DocUse->select_all(array(
                                             'arr_where'=>array('doc_id'=>new MongoId($doc_id))
                                             ));
        if($docuses->count()){
            foreach($docuses as $docuse){
                $docuse['no'] = $this->DocUse->get_auto_code('no');
                $docuse['doc_id'] = new MongoId($new_doc_id);
                unset($docuse['_id']);
                $this->DocUse->save($docuse);
            }
        }
        $this->redirect('/docs/entry/'.$new_doc_id);
        die;
    }

    public function entry_search() {
        if (!empty($this->data) && $this->request->is('ajax')) {

            $post = $this->data['Doc'];
            $cond = array();

            $post = $this->Common->strip_search($post);

            if( strlen($post['no']) > 0 )$cond['no'] = (int)$post['no'];
            if( strlen($post['type']) > 0 )$cond['type'] = $post['type'];
            if( strlen($post['name']) > 0 )$cond['name'] = $post['name'];
            if( strlen($post['location']) > 0 )$cond['location'] = $post['location'];
            if( strlen($post['description']) > 0 )$cond['description'] = new MongoRegex('/' . trim($post['description']).'/i');
            if( strlen($post['category']) > 0 )$cond['category'] = $post['category'];
            if( strlen($post['ext']) > 0 )$cond['ext'] = $post['ext'];

            $this->selectModel('Doc');
            $this->identity($cond);
            $tmp = $this->Doc->select_one($cond);
            if( $tmp ){
                $this->Session->write('docs_entry_search_cond', $cond);

                $cond['_id'] = array('$ne' => $tmp['_id']);
                $tmp1 = $this->Doc->select_one($cond);
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




        $this->set('arr_docs_category', $this->Setting->select_option(array('setting_value' => 'docs_category'), array('option')));
        $this->set('set_footer', 'footer_search');
    }
    public function entry_search_all(){
        $this->Session->delete('docs_entry_search_cond');
        $this->redirect('/docs/lists');
    }
}