<?php
class CommonComponent extends Component{

	function strip_search($arr_post=array()){

		$patern_replace = array('(', ')');

		if( is_array($arr_post) && count($arr_post)>0 ){
			foreach ($arr_post as $key => $value) {
				if( !is_array($value) && !is_object($value) && strlen(trim($value)) > 0 ){
					$arr_post[$key] = str_replace($patern_replace, '.*', trim($value));
				}
			}
		}else{
			return str_replace($patern_replace, '.*', $arr_post);
		}
		return $arr_post;
	}


	function strtotime( $datetime ){
		$datetime = trim($datetime);

		$date = substr($datetime, 0, -8);

		// chuyen dinh dang ngay
		$tmp = explode(' ', $date);
		$arr_month = array(
			'Jan' => 'January',
			'Feb' => 'February',
			'Mar' => 'March',
			'Apr' => 'April',
			'May' => 'May',
			'Jun' => 'June',
			'Jul' => 'July',
			'Aug' => 'August',
			'Sep' => 'September',
			'Oct' => 'October',
			'Nov' => 'November',
			'Dec' => 'December'
		);
		$tmp[1] = $arr_month[str_replace(',', '', $tmp[1])];
		$date = implode(' ', $tmp);

		$date_sec = strtotime($date);

		$hour = substr($datetime, -8);
		$hour_sec = strtotime('2013-09-11 '.$hour) - strtotime('2013-09-11');

		return ($date_sec + $hour_sec);
	}

	function move_file( $file ){
		$thisfolder = 'upload'.DS.date("Y_m");
		$linkfolder = 'upload/'.date("Y_m");
		$folder = ROOT.DS.APP_DIR.DS.WEBROOT_DIR.DS.$thisfolder;
		$upload_file = "";
		$type='gif|jpg|jpeg|png|swf|doc|xls|GIF|JPG|JPEG|PNG|SWF|DOC|XLS|PPT';

		if ( $file["size"] > 8000000 ) {
			echo 'File size must be < 8000000'; die;
		}

		$ext = strtolower(substr(strrchr($file['name'],'.'),1));

		if ( 1==2 && strpos($type, $ext) === false ) {
			echo 'This file is not allow to upload. It must be '.$type; die;
		}

		$upload_file = date('Y_m_d_His').'_'.rand(100000,999999).'.'.strtolower($ext);

		if (!file_exists($folder)) {
			mkdir($folder, 0777, true);
		}

		if( move_uploaded_file($file["tmp_name"],$folder.DS.$upload_file) ){
			return str_replace(ROOT.DS.APP_DIR.DS.WEBROOT_DIR, '', $folder.DS.$upload_file);
		}
		return false;
	}


	function change_time( $datetime ){
		$datetime = trim($datetime);
		$date = explode(",",$datetime);
		$date = explode(" ",$date[0]);
		$tmp = $date[1];
		$arr_month = array(
			'Jan' => 'January',
			'Feb' => 'February',
			'Mar' => 'March',
			'Apr' => 'April',
			'May' => 'May',
			'Jun' => 'June',
			'Jul' => 'July',
			'Aug' => 'August',
			'Sep' => 'September',
			'Oct' => 'October',
			'Nov' => 'November',
			'Dec' => 'December'
		);
		$datetime = str_replace($tmp,$arr_month[$tmp],$datetime);
		return strtotime($datetime);
	}



}