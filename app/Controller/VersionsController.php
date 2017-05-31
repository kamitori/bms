<?php
App::uses('AppController', 'Controller');
class VersionsController extends AppController {
	public function beforeFilter() {
		if($this->params->params['action']=='create_version')
			parent::beforeFilter();
	}

	function create_version(){
		$arr_info = array();
		$version_path = APP.'version.ini';
		$controller_path = APP.'Controller'.DS;
		$model_path = APP.'Model'.DS;
		$view_path = APP.'View'.DS.'Themed'.DS.'Default'.DS;
		if( file_exists($version_path))
			$arr_info = parse_ini_file($version_path);
		$arr_controler = $this->getChangedFile($controller_path);
		$arr_model = $this->getChangedFile($model_path);
		$arr_view = $this->getChangedFile($view_path,true);

		$arr_info['path_change'] = array(
                         'Controller' 	=> $arr_controler,
                         'Model' 		=> $arr_model,
                         // 'View' 		=> $arr_view, //Khong can update view
                         );
		$this->selectModel('Version');
		$main_path = $this->Version->select_one(array('name'=>'Main'));
		$this->checkChanged($arr_info,$main_path);
		if(!isset($arr_info['path_change']) || empty($arr_info['path_change'])){
			echo 'Not thing has changed. This cannot be an upgrade.';
			die;
		}
		if(!isset($main_path['path_change']))
			$main_path['path_change'] = array();

		$main_path['path_change'] = array_replace_recursive($main_path['path_change'],$arr_info['path_change']);
		$main_path['name'] = 'Main';
		$main_path['deleted'] = false;
		if(!isset($main_path['_id']))
			$this->Version->collection->insert($main_path);
		else
			$this->Version->collection->update(array('_id'=>new MongoId($main_path['_id']) ),$main_path);
		$arr_info['version'] = '1.'.date('d.m.hi');
		$version = $arr_info;
		$this->Version->save($version);
		file_put_contents(@$version_path,'version = "'.$version['version'].'"');
		echo 'ok';
		die;
	}

	function get_update($version){
		$this->selectModel('Version');
		$version = $this->Version->select_one(array('version'=>$version),array('_id'),array('_id'=>1));
		if(!isset($version['_id'])){
			echo 'Your version is not valid';
			die;
		}
		$arr_files = $this->Version->select_all(array(
		                           'arr_order' => array('_id'=>1),
		                           'arr_where' => array('_id'=>array('$gt'=>$version['_id']))
		                           ));
		if($arr_files->count() == 0){
			echo 'Your version is up to date.';
			die;
		}
		$arr_return = array();
		$lastest_version = '';
		foreach($arr_files as $file){
			unset($file['path_change']['View']);
			$arr_return = array_replace_recursive($arr_return, $file['path_change']);
			$lastest_version = $file['version'];
		}
		if(empty($arr_return)){
			echo 'Your version is up to date.';
			die;
		}
		$last_version = $this->Version->select_one(array(),array('version'),array('_id'=>-1));
		$zipname = 'update-JobTraq-v'.$last_version['version'].'-'.date('h.i.s').'.zip';
		$zippath = APP.'webroot'.DS.'upload'.DS.$zipname;

		$iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($arr_return));
		$arr_files = array();
		foreach ($iterator as $key => $value) {
		    for ($i = $iterator->getDepth() - 1; $i >= 0; $i--) {
		        $key = $iterator->getSubIterator($i)->key() . DS . $key;
		    }
		    $arr_files[] = $key;
		}
		$zip = new ZipArchive;
		$zip->open($zippath, ZipArchive::CREATE);
		foreach ($arr_files as  $file) {
			$zip->addFile( APP.$file, $file);
		}
		$zip->close();
		header('Set-Cookie: fileDownload=true; path=/');
		header('Content-Type: application/zip');
		header('Content-disposition: attachment; filename='.$zipname);
		header('Content-Length: ' . filesize($zippath));//
		readfile($zippath);
		unlink($zippath);die;
	}
	function check_update($version)	{
		$this->selectModel('Version');
		$lastest_version = $this->Version->select_one(array(),array('version'),array('_id'=>-1));
		if($version == $lastest_version['version']){
			echo json_encode(array(
			                 'Message'=>'Your version is up to date',
			                 'Status' => 'Up to date',
			                 ));
		} else {
			echo json_encode(array(
			                 'Message'=>'Your version is out of date. The lastest version is '.$lastest_version['version'],
			                 'Status' => 'Need update',
			                 ));
		}
		die;
	}

	function get_latest_version(){
		$this->selectModel('Version');
		$lastest_version = $this->Version->select_one(array(),array('version'),array('_id'=>-1));
		echo $lastest_version['version'];die;
	}

	function checkChanged(&$array, $compareArray){
		foreach($array as $key => $value){
			if(!isset($compareArray[$key])) continue;
			$tmp = $compareArray[$key];
			if( !is_array($value)){
				if($value == $tmp)
					unset($array[$key]);
			} else if( is_array($value) ){
				$value = $this->checkChanged($value, $tmp);
				$array[$key] = $value;
				if(empty($value))
					unset($array[$key]);
			}
		}
		return $array;
	}

	function getChangedFile($dir, $goToSubDir = false, $arr_return = array() ){
		$arr_files = scandir($dir);
		foreach($arr_files as $file){
			if($file == '.' || $file == '..' || $file == 'empty') continue;
			if(is_file($dir.$file)){
				$key =$file;
				$arr_return[$key] = md5_file($dir.$file);
			} else if(  $goToSubDir && is_dir($dir.$file)) {
				$dirName = $file;
				$arr_return[$dirName] = $this->getChangedFile($dir.$dirName.DS, $goToSubDir);
			}
		}
		return $arr_return;
	}

}
