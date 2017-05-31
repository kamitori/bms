<?php
class UpdateController extends Controller{

	private $server = 'http://jt.anvy.net';
	// private $server = 'http://jt.com';
	function __construct()
	{
		$this->autoRender = false;
		parent::__construct();
		$version = 1.0;
		$version_path = APP.'version.ini';
		if( file_exists($version_path)){
			$arr_info = parse_ini_file($version_path);
			$version = $arr_info['version'];
		}
		$this->version = $version;
	}

	function check_update()
	{
		$curl = curl_init($this->server.'/versions/check_update/'.$this->version);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		$response  = curl_exec($curl);
		echo $response;
	}

	function get_lastest_version()
	{
		$curl = curl_init($this->server.'/versions/get_latest_version/');
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		$response  = curl_exec($curl);
		return $response;
	}

	function do_update()
	{
		$curl = curl_init($this->server.'/versions/get_update/'.$this->version);
		$f = fopen(APP.'tmp'.DS.'update-JobTraq.zip','wb');
		curl_setopt($curl, CURLOPT_FAILONERROR, true);
		curl_setopt($curl, CURLOPT_HEADER,0);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_AUTOREFERER, true);
		curl_setopt($curl, CURLOPT_BINARYTRANSFER,true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 300);
		curl_setopt($curl, CURLOPT_FILE, $f);
		if(curl_exec($curl) === false)
		    echo 'Error: ' . curl_error($curl);
		else{
			curl_close($curl);
			fclose($f);
			$zip = new ZipArchive;
			$response = $zip->open(APP.'tmp'.DS.'update-JobTraq.zip');
			if ( $response === TRUE) {
			    $zip->extractTo(APP);
			    $zip->close();
			    $lastest_version = $this->get_lastest_version();
			    file_put_contents(APP.'version.ini', 'version = "'.$lastest_version.'"');
			    echo 'Done. Your version is '.$lastest_version;
			} else
				echo 'Failed. Please try again.<br />Error code: '.$response;
		}
	}
}