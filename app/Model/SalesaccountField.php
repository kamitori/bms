<?php
$ModuleField = array();
$ModuleField = array(
	'module_name' 	=> __('Salesaccount'),
	'module_label' 	=> __('Sales Accounts'),
	'colection' 	=> 'tb_salesaccount',
	'title_field'	=> array('','name','',''),
);
//============= *** FIELDS *** =============//
// Panel 1
$ModuleField['field']['panel_1'] = array(
	'setup'	=> array(
			'css'	=> 'width:100%;',
			'lablewith' => '25',
			'blockcss' => 'width:30%;float:left;',
			),
	'no'	=>array(
			'name' 		=> __('Account no'),
			'type' 		=> 'text',
			'moreclass' => 'fixbor',
			'width'		=> '30%;text-align:right;',
			'css'		=> 'width:65%; padding-left:6.5%;',
			'after_field'=>'invoice_type',
			'lock'		=> '1',
			'moreinline'=> 'Type',
			'listview'	=>	array(
								'order'	=>	'1',
								'with'	=>	'100',
								'align'	=>	'center',
								'css'	=>	'width:5%;',
								'sort'=> '1',
							),
			),
	'invoice_type'	=>array(
			'name' 		=>  __('Type'),
			'type' 		=> 'text',
			'other_type' => 'after_other',
			'classselect' =>'jt_after_field',
			'width'		=> '41%;" id="field_after_quotetype" alt="',
			'css'		=> ' width:91%;',
			'not_custom'=> '1',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'5',
							'css'	=>	'width:5%;',
							'sort'=> '1',
						),
			),
	'mongo_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'name'	=>array(
			'name' 		=>  __('Account name'),
			'type' 		=> 'relationship',
			'cls'		=> 'companies',
			'id'		=> 'company_id',
			'list_syncname' => 'name',
			'css'		=> 'padding-left:2%;width:95%;readonly:disable',
			'hide_button' => 1,
			//'not_custom' => '0',
			//'lock'		=> '1',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'15',
							'css'	=>	'width:15%;',
							'sort'=> '1',
						),
			),
	'company_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'status'	=>array(
			'name' 		=>  __('Account status'),
			'type'		=> 'select',
			'droplist'	=> 'salesaccounts_status',
			'default'	=> '',
			'css'		=> 'padding-left:2%;',
			'para'		=> ',get_para_contact()',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'10',
							'css'	=>	'width:10%;',
							'sort'=> '1',
						),
			),
	'phone'	=>array(
			'name' 		=> __('Phone'),
			'type' 		=> 'phone',
			'css'		=> 'padding-left:2%;',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'8',
							'css'	=>	'width:8%;',
							'sort'=> '1',
						),
			),
	'fax'	=>array(
			'name' 		=> __('Fax'),
			'type' 		=> 'email',
			'css'		=> 'padding-left:2%;',
			),
	'email'	=>array(
			'name' 		=> __('Email'),
			'type' 		=> 'email',
			'css'		=> 'padding-left:2%;',
			),
	'our_rep'	=>array(
			'name' 		=>  __('Account contact'),
			'type' 		=> 'relationship',
			'cls'		=> 'contacts',
			'id'		=> 'our_rep_id',
			'css'		=> 'padding-left:2%;',
			'lock'		=> '0',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'15',
							'css'	=>	'width:15%;',
							'sort'=> '1',
						),
			),
	'our_rep_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'direct_dial'	=>array(
			'name' 		=> __('Direct detail'),
			'type' 		=> 'text',
			'css'		=> 'padding-left:2%;',
			),
	'none'	=>array(
			'type' 		=> 'not_in_data',
			'moreclass' => 'fixbor2',
			),
);
//panel 2
$ModuleField['field']['panel_2'] = array(
	'setup'	=> array(
			'css'	=> 'width:70%;',
			'lablewith' => '35',//%
			'blockcss' => 'width:69%;float:right;',
			'blocktype'=> 'address',
			),
	'address' =>array(
			'name' 		=> __('Address'),
			'type' 		=> 'text',
			),
);
//panel 3
$ModuleField['field']['panel_3'] = array(
	'setup'	=> array(
			'css'	=> 'width:33%;',
			'lablewith' => '35',
			),
	'invoices_credits'	=>array(
			'name' 		=>  __('Invoices / credits'),
			'type' 		=> 'price',
			'not_custom'=> '1',
			'lock'	    => '1',
			),
	'receipts'		=>array(
			'name' 		=>  __('Receipts'),
			'type' 		=> 'price',
			'not_custom'=> '1',
			'lock'	    => '1',
			),
	'balance'		=>array(
			'name' 		=>  __('Account balance'),
			'type' 		=> 'price',
			'not_custom'=> '1',
			'lock'	    => '1',
			),
	'credit_limit'		=>array(
			'name' 		=>  __('Credit limit'),
			'type' 		=> 'price',
			'not_custom'=> '1',
			),
	'difference'		=>array(
			'name' 		=>  __('Difference'),
			'type' 		=> 'price',
			'not_custom'=> '1',
			),
	'payment_terms'	=>array(
			'name' 		=>  __('Payment terms'),
			'type'		=> 'select',
			'droplist'	=> 'salesinvoices_payment_terms',
			'default'	=> '',
			'css'		=> 'padding-left:2%;',
			'para'		=> ',get_para_contact()',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'10',
							'css'	=>	'width:10%;',
							'sort'=> '1',
						),
			),
	'tax_code'	=>array(
			'name' 		=>  __('Tax code'),
			'type'		=> 'select',
			'droplist'	=> 'salesaccounts_tax_code',
			'default'	=> '',
			'css'		=> 'padding-left:2%;',
			'para'		=> ',get_para_contact()',
			),
	'nominal_code'	=>array(
			'name' 		=>  __('Nominal code'),
			'type'		=> 'select',
			'droplist'	=> 'salesaccounts_nominal_code',
			'default'	=> '',
			'css'		=> 'padding-left:2%;',
			'para'		=> ',get_para_contact()',
			),
	'none'	=>array(
			'type' 		=> 'not_in_data',
			'moreclass' => 'fixbor3',
			),
);
//panel 4
$ModuleField['field']['panel_4'] = array(
	'setup'	=> array(
			'css'	=> 'width:33%;',
			'lablewith' => '35',
			),
	'usually_pay_by'	=>array(
			'name' 		=>  __('Usually Pay By'),
			'type'		=> 'select',
			'droplist'	=> 'salesaccounts_usually_pay_by',
			'default'	=> '',
			'css'		=> 'padding-left:2%;',
			'para'		=> ',get_para_contact()',
			),
	'card_type'	=>array(
			'name' 		=>  __('Card type'),
			'type'		=> 'select',
			'droplist'	=> 'salesaccounts_card_type',
			'default'	=> '',
			'css'		=> 'padding-left:2%;',
			'para'		=> ',get_para_contact()',
			),
	'card_number'		=>array(
			'name' 		=>  __('Card number'),
			'type' 		=> 'text',
			'not_custom'=> '1',
			),
	'expires_month'		=>array(
			'name' 		=>  __('Expires: Month'),
			'type' 		=> 'text',
			'not_custom'=> '1',
			),
	'security_id'		=>array(
			'name' 		=>  __('Security ID'),
			'type' 		=> 'text',
			'not_custom'=> '1',
			),
	'card_holder'	=>array(
			'name' 		=>  __('Card holde'),
			'type'		=> 'select',
			'droplist'	=> 'salesaccounts_card_holder',
			'default'	=> '',
			'css'		=> 'padding-left:2%;',
			'para'		=> ',get_para_contact()',
			),
	'address'		=>array(
			'name' 		=>  __('Address'),
			'type' 		=> 'text',
			'not_custom'=> '1',
			),
	'ext_accounts_id'		=>array(
			'name' 		=>  __('Ext accounts ID'),
			'type' 		=> 'text',
			'not_custom'=> '1',
			),
	'none'	=>array(
			'type' 		=> 'not_in_data',
			'moreclass' => 'fixbor3',
			),
);
//============ *** RELATIONSHIP *** =============//
$ModuleField['relationship']['invoices']['name'] =  __('Invoices');

//Option list data
$ModuleField['relationship']['invoices']['block']['invoices'] = array(
	'title'	=>__('Invoices relating to this Account'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '200',
	'add'	=> __('Add option'),
	'reltb'		=> 'tb_salesaccount@invoices',//tb@option
	'link' => array('w' => '1', 'cls' => 'salesinvoices','field'=>'_id'),
	'custom_box_bottom' => '1',
	'delete' => '1',
	'field'=> array(
				'code' => array(
					'name' 		=>  __('Ref no'),
					'width'		=> '6',
					'align' => 'center',
				),
				'date_modified' => array(
					'name' 		=>  __('Date'),
					'align' => 'center',
					'type'  => 'date',
					'width'		=> '6',
				),
				'invoice_status' => array(
					'name' 		=>  __('Status'),
					'align' => 'center',
					'width'		=> '4',
				),
				'payment_terms' => array(
					'name' 		=>  __('Term'),
					'width'		=> '3',
				),
				'payment_due_date' => array(
					'name' 		=>  __('Due'),
					'width'		=> '6',
					'type'      => 'date',
				),
				'paid_date' => array(
					'name' 		=>  __('Paid'),
					'width'		=> '6',
					'type'		=> 'date',
				),
				'our_rep' => array(
					'name' 		=>  __('Our Rep'),
					'width'		=> '10',
				),
				'our_csr' => array(
					'name' 		=>  __('Our CSR'),
					'width'		=> '10',
				),
				'tax' => array(
					'name' 		=>  __('Tax'),
					'width'		=> '10',
					'type'      => 'price',
				),
				'sum_amount' => array(
					'name' 		=>  __('Total'),
					'width'		=> '6',
					'type'      => 'price',
				),
				'total_receipt' => array(
					'name' 		=>  __('Receipts'),
					'width'		=> '6',
					'type'      => 'price',
				),
				'balance_invoiced' => array(
					'name' 		=>  __('Balance'),
					'width'		=> '6',
					'type'      => 'price',
				),
			),
);
//Receipts
$ModuleField['relationship']['receipts']['name'] =  __('Receipts');
$ModuleField['relationship']['receipts']['block']['receipts'] = array(
	'title'	=>__('Receipts for this account'),
	'type'	=>'listview_box',
	'css' => 'width:60%;margin-top:0;',
	'height' => '200',
	'add'	=> __('Add option'),
	'reltb'		=> 'tb_salesaccount@receipts',//tb@option
	'link' => array('w' => '1', 'cls' => 'receipts','field'=>'_id'),
	'custom_box_bottom' => '1',
	'delete' => '5',
	'field'=> array(
				'code' => array(
					'name' 		=>  __('Ref no'),
					'width'		=> '6',
					'align' => 'center',
					'edit'		=> '1',
				),
				'date' => array(
					'name' 		=>  __('Date'),
					'align' => 'center',
					'type'  => 'date',
					'width'		=> '10',
					'edit'		=> '1',
				),
				'paid_by' => array(
					'name' 		=>  __('Paid by'),
					'align' => 'center',
					'width'		=> '10',
					'edit'		=> '1',
				),
				'reference' => array(
					'name' 		=>  __('Reference'),
					'width'		=> '10',
					'edit'		=> '1',
				),
				'notes' => array(
					'name' 		=>  __('Notes'),
					'width'		=> '16',
					'edit'		=> '1',
				),
				'amount_received' => array(
					'name' 		=>  __('Total receipt'),
					'width'		=> '10',
					'edit'		=> '1',
					'type'      => 'price',
				),
				'total_allocated' => array(
					'name' 		=>  __('Allocated'),
					'width'		=> '10',
					'edit'		=> '1',
					'type'      => 'price',
				),
				'unallocated' => array(
					'name' 		=>  __('Unallocated'),
					'width'		=> '10',
					'edit'		=> '1',
					'type'      => 'price',
				),
			),
);
$ModuleField['relationship']['receipts']['block']['receipts_allocation'] = array(
	'title' => __('Receipt allocations'),
	'type' => 'listview_box',
	'link' => array('w' => '1', 'cls' => 'receipts','field'=>'receipt_id'),
	'css' => 'width:39%;margin-left:1%;',
	'height' => '200',
	'custom_box_bottom' => '1',
	'reltb'		=> 'tb_salesaccount@receipts',
	'field' => array(
		'receipt_code' => array(
			'name' => __('Ref no'),
			'width' => '8',
			'align'=>'center',
			'type' => 'text',
		),
		'date' => array(
			'name' => __('Date'),
			'width' => '15',
			'align'=>'center',
			'type' => 'date',
		),
		'salesinvoice_code' => array(
			'name' => __('Invoice'),
			'width' => '10',
			'align'=>'center',
			'default' => 'Click for edit',
		),
		'note' => array(
			'name' => __('Notes for customers'),
			'width' => '25',
			'align'=>'center',
			'type'=>'date',
		),
		'write_off' => array(
			'name' => __('Write off'),
			'width' => '12',
			'align'=>'center',
			'type' => 'checkbox',
			'default' => '0'
		),
		'amount' => array(
			'name' => __('Amount'),
			'width' => '19',
			'type' => 'price',
		),
	),
);

//
$ModuleField['relationship']['comms']['name'] = __('Comms');
$ModuleField['relationship']['comms']['block']['stockcurrent1'] = array(
    'title' => 'History / movements for this item',
    'css' => 'width:49.5%;margin-bottom:1%; float: left',
    'height' => '200',
    //'custom_box_bottom' => '1',
	//'custom_box_top' => '1',
    'link' => array('w' => '1', 'cls' => 'units'),
    'type' => 'listview_box',
    'field' => array(
        'history_date' => array(
            'name' => __('Date'),
            'width' => '10',
            'align' => 'center',
            'type' => 'text',
        ),
        'type' => array(
            'name' => __('Type'),
            'width' => '20',
            'type' => 'text',
        ),
        'from' => array(
			'name' => __('From'),
			'type' => 'text',
			'width'=> '25',
		),
		'to' => array(
			'name' => __('To'),
			'type' => 'text',
			'width'=> '25',
		),
		'quantity' => array(
			'name' => __('Quantity'),
			'type' => 'text',
			'width'=> '10',
		),
    ),
);

//Task
$ModuleField['relationship']['task']['name'] =  __('Task');
$ModuleField['relationship']['task']['block']['task'] = array(
	'title'	=>__('Tasks relating to this enquiry'),
	'type'	=>'listview_box',
	'link' => array('w' => '1', 'cls' => 'tasks','field'=>'_id'),
	'css'	=>'width:100%;margin-top:0;',
	'height' => '200',
	'add'	=> __('Add new Task'),
	'reltb'		=> 'tb_salesaccount@task',//tb@option
	'field'=> array(
				'_id' => array(
		            'name' => __('ID'),
					'type'=>'id',
				),
				'no' => array(
					'name' 		=>  __('No'),
					'width'		=> '5',
				),
				'name' => array(
					'name' 		=>  __('Task'),
					'width'		=> '20',
				),
				'type' => array(
					'name' 		=>  __('Type'),
					'width'		=> '10',
				),
				'our_rep' => array(
					'name' 		=>  __('Responsible'),
					'width'		=> '10',
				),
				'work_start' => array(
					'name' 		=>  __('Work start'),
					'width'		=> '10',
					'type' 		=> 'date',
					'fulltime'  => '1',
				),
				'work_end' => array(
					'name' 		=>  __('Work end'),
					'width'		=> '10',
					'type' 		=> 'date',
					'fulltime'  => '1',
				),
				'status' => array(
					'name' 		=>  __('Status'),
					'width'		=> '10',
				),
				'delete' => array(
					'name' 		=>  __(''),
					'width'		=> '1',
					'edit'		=> '1',
					'node'      => 'tasks',
					'type'      => 'delete_another',
					'rev'		=> '1',
					'align'		=> 'center',
				),
			),
);

//other
$ModuleField['relationship']['other']['name'] = __('Other');
$ModuleField['relationship']['other']['block']['detail'] = array(
    'title' => 'Additional account details',
    'css' => 'width:25%;margin-top:0;float:left;',
    'height' => '150',
    'reltb' => 'tb_product@stocktracking',
    'type' => 'editview_box',
	'field' => array(
	                 'company' => array(
							'name' => __('Company reg no'),
							'type' => 'text',
							'css'=>'text-align:left;',
						),
	                 'tax_no' => array(
							'name' => __('Tax no'),
							'type' => 'text',
							'css'=>'text-align:left;',
						),
	                 'year_established' => array(
							'name' => __('Year established'),
							'type' => 'text',
							'css'=>'text-align:left;',
						),
	                 'bank_name' => array(
							'name' => __('Bank name'),
							'type' => 'text',
							'css'=>'text-align:left;',
						),
	                 'bank_address' => array(
							'name' => __('Bank address'),
							'type' => 'text',
							'css'=>'text-align:left;',
						),
	                 'bank_no' => array(
							'name' => __('Bank no'),
							'type' => 'text',
							'css'=>'text-align:left;',
						),
	                 'bank_transit_no' => array(
							'name' => __('Bank transit no'),
							'type' => 'text',
							'css'=>'text-align:left;',
						),
	                 'bank_account' => array(
							'name' => __('Bank account no'),
							'type' => 'text',
							'css'=>'text-align:left;',
						),
	                 'bank_sort_code' => array(
							'name' => __('Bank sort code'),
							'type' => 'text',
							'css'=>'text-align:left;',
						),
	                 'bank_iban' => array(
							'name' => __('Bank IBAN no'),
							'type' => 'text',
							'css'=>'text-align:left;',
						),
	                ),
);
$ModuleField['relationship']['other']['block']['trade1'] = array(
    'title' => 'Trade reference 1',
    'css' => 'width:20%;margin-top:0;float:left;margin-left: 1.2%',
    'height' => '240',
    'reltb' => 'tb_salesaccount@other',
    'type' => 'editview_box',
	'field' => array(
	                 'company' => array(
							'name' => __('Name'),
							'type' => 'text',
							'css'=>'text-align:left;',
						),
	                 'tax_no' => array(
							'name' => __('Contact'),
							'type' => 'text',
							'css'=>'text-align:left;',
						),
	                 'year_established' => array(
							'name' => __('Phone'),
							'type' => 'text',
							'css'=>'text-align:left;',
						),
	                 'bank_name' => array(
							'name' => __('Fax'),
							'type' => 'text',
							'css'=>'text-align:left;',
						),
	                 'bank_address' => array(
							'name' => __('Address'),
							'type' => 'text',
							'css'=>'text-align:left;',
						),
	                ),
);
$ModuleField['relationship']['other']['block']['trade2'] = array(
    'title' => 'Trade reference 2',
    'css' => 'width:20%;margin-top:0;float:left;margin-left: 1.2%',
    'height' => '240',
    'reltb' => 'tb_salesaccount@other',
    'type' => 'editview_box',
	'field' => array(
	                 'company' => array(
							'name' => __('Name'),
							'type' => 'text',
							'css'=>'text-align:left;',
						),
	                 'tax_no' => array(
							'name' => __('Contact'),
							'type' => 'text',
							'css'=>'text-align:left;',
						),
	                 'year_established' => array(
							'name' => __('Phone'),
							'type' => 'text',
							'css'=>'text-align:left;',
						),
	                 'bank_name' => array(
							'name' => __('Fax'),
							'type' => 'text',
							'css'=>'text-align:left;',
						),
	                 'bank_address' => array(
							'name' => __('Address'),
							'type' => 'text',
							'css'=>'text-align:left;',
						),
	                ),
);
$ModuleField['relationship']['other']['block']['account_app'] = array(
    'title' => 'Account application',
    'css' => 'width:31%;margin-top:0;float:left;margin-left: 1.2%',
    'height' => '240',
    'reltb' => 'tb_salesaccount@other',
    'type' => 'editview_box',
	'field' => array(
	                 'application_name' => array(
							'name' => __('Applicant name'),
							'type' => 'text',
							'css'=>'text-align:left;',
						),
	                 'position' => array(
							'name' => __('Position'),
							'type' => 'text',
							'css'=>'text-align:left;',
						),
	                 'application_date' => array(
							'name' => __('Application date'),
							'type' => 'text',
							'css'=>'text-align:left;',
						),
	                  'approval' => array(
							'name' => __(''),
							'type' => 'text',
							'css'=>'text-align:left;',
							'default' => 'Account approval',
							'lock' => '1',
						),
	                 'approved' => array(
							'name' => __('Approved'),
							'type' => 'checkbox',
							'default' => '0',
							'css'=>'text-align:left;margin-left: 12px; border-bottom: none',
						),
	                 'approved_by' => array(
							'name' => __('Approved by'),
							'type' => 'text',
							'css'=>'text-align:left;',
							'lock' => 1,
						),
	                  'approved_date' => array(
							'name' => __('Approved date'),
							'type' => 'text',
							'css'=>'text-align:left;',
							'lock' => 1,
						),
	                ),
);

$SalesaccountField = $ModuleField;