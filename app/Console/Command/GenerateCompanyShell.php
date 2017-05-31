<?php
class GenerateCompanyShell extends AppShell {

	private $apiKey = 'b92e3c4cc460a588ff1b1587f2428eaa-us6';
	private $listId = '3dc2f2d09c';

	public function main()
	{
		$this->selectModel('Salesinvoice');
        $this->selectModel('Company');
        $this->selectModel('Stuffs');

        $openAccount = $this->Stuffs->select_one(array('value'=> 'Open Account'));
        if( !empty($openAccount) ) {
            $openAccount= [
                '_id' => isset($openAccount['open_account_id']) ? $openAccount['open_account_id'] : '',
                'name' => isset($openAccount['open_account']) ? $openAccount['open_account'] : '',
            ];
        } else {
            $openAccount = [];
        }

        $saleManager = $this->Stuffs->select_one(array('value'=> 'Sale Manager'));
        if( !empty($saleManager) ) {
            $saleManager= [
                '_id' => isset($saleManager['sale_manager_id']) ? $saleManager['sale_manager_id'] : '',
                'name' => isset($saleManager['sale_manager']) ? $saleManager['sale_manager'] : '',
            ];
        } else {
            $saleManager = [];
        }
        $arrDate = array(
                    'oneYear' => array(
                        'condition' => array(
                            '$lt' => strtotime("-1 year", time()),
                        ),
                        'our_rep'   => $openAccount,
                        'status'    => ''
                    ),
                    'sixMonths' => array(
                        'condition' => array(
                            '$gt' => strtotime("-1 year", time()),
                            '$lt' => strtotime("-6 months", time())
                        ),
                        'our_rep'   => $saleManager,
                        'status'    => 'Suspended'
                    )
                );

        $companies = $this->Company->select_all(array(
                                   'arr_field'  =>  array('_id'),
                                   'arr_order'  =>  array('_id'=>1),
                                   'limit'      =>  9999999
                                   ));
        $oneYear = $sixMonths = 0;
        foreach($companies as $company){
            $lastInvoice = $this->Salesinvoice->select_one(array(
                                                    'company_id' => $company['_id'],
                                                    'invoice_status' => array('$ne' => 'Cancelled'),
                                                    ), array('invoice_date'), array('invoice_date' => -1));

            if( isset($lastInvoice['invoice_date']) && is_object($lastInvoice['invoice_date'])  ) {
                foreach( $arrDate as $var => $date ) {
                    if( isset($date['condition']['$lt']) && $lastInvoice['invoice_date']->sec > $date['condition']['$lt'] ) {
                        continue;
                    }
                    if( isset($date['condition']['$gt']) && $lastInvoice['invoice_date']->sec < $date['condition']['$gt'] ) {
                        continue;
                    }
                    $ourRep  = isset($date['our_rep']['name']) ? $date['our_rep']['name'] : '';
                    $ourRepId  = isset($date['our_rep']['_id']) ? $date['our_rep']['_id'] : '';
                    $this->Company->collection->update(array(
                            '_id'       => $company['_id'],
                        ), array(
                            '$set' => array(
                                'status'    => $date['status'],
                                'our_rep'   => $ourRep,
                                'our_rep_id'=> $ourRepId
                            )
                        ));
                    $$var++;
                }
            }
        }
		$this->out($oneYear."\t companies over than 1 year.", 1, Shell::QUIET);
		$this->out($sixMonths."\t companies over than 6 months and less than 1 year.", 1, Shell::QUIET);
	}

}