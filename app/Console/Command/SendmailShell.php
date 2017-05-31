<?php
class SendmailShell extends AppShell {
    public function main($status = 'New') {
		$arr_contact['contact_id'] = new MongoId('100000000000000000000000');
		$arr_contact['contact_name'] = $arr_contact['full_name'] = 'System Admin';
		$arr_contact['first_name'] = 'System';
		$arr_contact['last_name'] = 'Admin';
		$_SESSION['arr_user'] =  $arr_contact;
    	$emails = $this->getMails($status);
    	if( $emails->count() ) {
    		foreach($emails as $email) {
    			$this->sendMail($email);
    		}
    	}
    	$this->mongo_disconnect();
        $this->out($emails->count()." {$status} email(s) found", 1, Shell::QUIET);
    }

    function sendPendingMail(){
    	$this->main('Pending');
    }

    function getMails($status = 'New'){
    	$this->selectModel('Mailqueue');
    	return $this->Mailqueue->select_all(array(
    								'arr_where' => array(
    										'status' => $status,
    									),
    								'arr_order' => array(
    										'_id' 	=> 1,
		    								'prior' => 1,
		    								'try'	=> 1
    									)
    							));
    }

    function sendMail($data){
    	if(!isset($data['try']))
				$data['try'] = 0;
		$data['try']++;
		if($data['try'] <= 3)
			$status = 'Pending';
		else
			$status = 'Failed';
		$this->updateMail($data,$status);
		if($status == 'Failed')
			return false;
    	App::uses('CakeEmail', 'Network/Email');
		$email = new CakeEmail();
		$this->selectModel('Stuffs');
		$system_email = $this->Stuffs->select_one(array('value'=>"system_email"));
		if(!empty($system_email)){
			$config_set = array(
				'from'      => array($system_email['username']=>(trim($system_email['email_name'])!='' ? $system_email['email_name'] : 'BanhMiSub - JobTraq')) ,
				'username'  => $system_email['username'],
				'password'  => $system_email['password'],
				'host'		=> $system_email['host'],
				'port'		=> $system_email['port'],
			);
			$email->config('smtp',$config_set);
		}
		else{
			$email->config('gmail',array('jobtraq.mail@gmail.com'=>$this->opm->user_name()));
			$system_email['username'] = 'jobtraq.mail@gmail.com';
		}
		$email->to($data['to']);
		if(isset($data['cc'])&&!empty($data['cc']))
			$email->cc($data['cc']);
		if(isset($data['bcc'])&&!empty($data['bcc']))
			$email->bcc($data['bcc']);
		$email->subject($data['subject']);
		$this->email_image_filter($data['template'], $data['attachments']);
		//Kiem tra attachment, va dua vao mail se gui
		if(!empty($data['attachments']))
			$email->attachments($data['attachments']);
		$email->emailFormat('both');
		try{
			$email->send($data['template']);
			$this->updateMail($data,'Sent');
			return true;
		}
		catch(Exception $e){
			if(!isset($data['failed_reason']))
				$data['failed_reason'] = array();
			$data['failed_reason'][$data['try']] = array(
															'code'		=> $e->getCode(),
															'message' 	=> $e->getMessage(),
															'time'		=> date('d-m-Y h:i:s')
														);
			$this->updateMail($data,$status);
			return false;
		}
    }

    function updateMail($data , $status = 'Sent'){
    	if(isset($data['_id'])){
    		$data['status'] = $status;
    		$this->Mailqueue->save($data);
    	}
    }

	function email_image_filter(&$content, &$files){
    	$arr_downloaded_files = array();
        preg_match_all("!<img [^>]+ />!", $content, $matches);
        if(!empty($matches)) {
        	foreach($matches as $match) {
				foreach($match as $urls){
					preg_match('!http://[^?#]+\.(?:jpe?g|png|gif)!Ui' , $urls , $url_match);
					if(empty($url_match)) continue;
					$url_match = reset($url_match);
					$original_url = $url_match;
					$url_match = urldecode($url_match);
					$file_name = pathinfo($url_match, PATHINFO_BASENAME );
					if(strpos($url_match, URL) === false) {
						$path = WWW_ROOT.'upload'.DS.$file_name;
						file_put_contents($path, file_get_contents($url_match));
					} else {
						$path = str_replace(URL.'/', WWW_ROOT, $url_match);
						$path = str_replace('/', DS, $path);
					}
					$contentId = md5($file_name);
	        		$files[$contentId] = array(
	                            'file' => $path,
	                            'mimetype' => image_type_to_mime_type (exif_imagetype($path)) ,
	                            'contentId' => $contentId
	                        );
	        		$content = str_replace($original_url, 'cid:'.$contentId, $content);
				}
        	}
        }
    }
}