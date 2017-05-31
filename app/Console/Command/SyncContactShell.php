<?php
App::import('Vendor', 'MailChimp');
class SyncContactShell extends AppShell {

	private $apiKey = 'b92e3c4cc460a588ff1b1587f2428eaa-us6';
	private $listId = '3dc2f2d09c';

	public function main()
	{
		$this->out('API Key: '.$this->apiKey, 1, Shell::QUIET);
		$this->out('List Id: '.$this->listId, 1, Shell::QUIET);
		$chimp = new MailChimp($this->apiKey);
		foreach(array('COMPANY' => 'Company', 'NAME' => 'Name', 'SALESREP' => 'Sales Rep', 'CSR' => 'CSR', 'POSITION' => 'Position', 'MMERGE8' => 'Company Type') as $tag => $name) {
			$chimp->call('lists/merge-var-reset', array(
							'id' => $this->listId,
							'tag' => $tag,
				));
			$chimp->call('lists/merge-var-add', array(
							'id' => $this->listId,
							'tag' => $tag,
							'name' => $name
				));
		}
		$arrBatches = $this->getContact();

		$this->out(count($arrBatches).' contact(s) found.', 1, Shell::QUIET);

		$add = $update = 0;

		$arrBatches = array_chunk($arrBatches, 200);
		foreach($arrBatches as $batch) {
			$result = $chimp->call('lists/batch-subscribe', array(
						'id' => $this->listId,
						'batch' => $batch,
						'double_optin' => false,
						'update_existing' => true,
						'replace_interests' => true,
			)) ;
			$this->out('<info>--'.$result['add_count']."\t added.</info>", 1, Shell::QUIET);
			$this->out('<comment>--'.$result['update_count']."\t updated.</comment>\n", 1, Shell::QUIET);
			$add += $result['add_count'];
			$update += $result['update_count'];
		}
		$this->out($add."\t added.", 1, Shell::QUIET);
		$this->out($update."\t updated.", 1, Shell::QUIET);
	}

	private function getContact()
	{
		$this->selectModel('Company');
		$this->selectModel('Contact');
		$contacts = $this->Contact->select_all(array(
								   'arr_field'  =>  array('is_customer','is_employee','no','first_name','last_name','company','company_id','email','position'),
								   'arr_where' => array('inactive' => array('$ne' => 1), 'email' => array('$ne' => '')),
								   'arr_order'  =>  array('_id'=> 1),
								   'limit'      =>  999999
								   ));
		$arrBatches = $arrCompanies = array();
		foreach($contacts as $contact) {
			if( !isset($contact['email']) || !filter_var($contact['email'], FILTER_VALIDATE_EMAIL) ) continue;
			$contact = array_merge(array('first_name' => '', 'last_name' => '', 'company' => '', 'position' => ''), $contact);
			$type = '';
			if(isset($contact['is_customer'])&&$contact['is_customer']==1)
				$type ='Customer';
			if(isset($contact['is_employee'])&&$contact['is_employee']==1)
				$type .='Employee';
			$companyType = $ourRep = $ourCSR = '';
			if( isset($contact['company_id']) && is_object($contact['company_id']) ) {
				if( !isset($arrCompanies[ (string)$contact['company_id'] ]) ) {
					$company = $this->Company->select_one(array('_id' => $contact['company_id']), array('is_customer', 'is_supplier', 'our_csr', 'our_rep'));
					if(isset($company['is_customer'])&&$company['is_customer']==1)
						$companyType ='Customer';
					if(isset($company['is_supplier'])&&$company['is_supplier']==1)
						$companyType .=' Supllier';
					$ourRep = isset($company['our_rep']) ? $company['our_rep'] : '';
					$ourCSR = isset($company['our_csr']) ? $company['our_csr'] : '';
					$arrCompanies[ (string)$contact['company_id'] ] = array(
																			'company' => $companyType,
																			'our_rep' => $ourRep,
																			'our_csr' => $ourCSR,
																		);
				} else {
					$company =  $arrCompanies[ (string)$contact['company_id'] ];
					$companyType = $company['company'];
					$ourRep = $company['our_rep'];
					$ourCSR = $company['our_csr'];
				}
			}
			if( isset(	$arrBatches[$contact['email']]) ) continue;
			$arrBatches[$contact['email']] = array(
								'email' => array('email' => $contact['email']),
								'merge_vars' => array(
									'EMAIL' 	=> $contact['email'],
									'FNAME' 	=> $contact['first_name'],
									'LNAME' 	=> $contact['last_name'],
									'COMPANY' 	=> $contact['company'],
									'NAME' 		=> trim($contact['first_name'].' '.$contact['last_name']),
									'SALESREP' 	=> $ourRep,
									'CSR' 		=> $ourCSR,
									'POSITION' 	=> $contact['position'],
									'MMERGE8' 	=> $companyType,
								)
							);
		}
		return $arrBatches;
	}
}