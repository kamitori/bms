<?php
$ModuleField = array();
$ModuleField = array(
	'module_name' 	=> __('Receipt'),
	'module_label' 	=> __('Receipt'),
	'colection' 	=> 'tb_receipt',
	'title_field'	=> array('company_name','contact_name','invoice_status','our_rep','our_bank_account'),
);


//============= *** FIELDS *** =============//

// Panel 1
$ModuleField['field']['panel_1'] = array(
	'setup'	=> array(
			'css'	=> 'width:100%;',
			'lablewith' => '30',
			'blockcss' => 'width:25%;float:left;',
			),
	'code'	=>array(
			'name' 		=> __('Ref no'),
			'type' 		=> 'text',
			'moreclass' => 'fixbor',
			'lock'		=> '1',
			'listview'	=>	array(
								'order'	=>	'1',
								'with'	=>	'100',
								'align'	=>	'center',
								'css'	=>	'width:5%;',
							),
			),
	'amount_received'	=>array(
			'name' 		=>  __('Amount Received'),
			'type' 		=> 'price',
			'css'		=> 'text-align:left;',
			'width'		=> '65%; padding-left:1%;" alt="',
			'listview'	=>	array(
								'order'	=>	'2',
								'with'	=>	'10',
								'align'	=>	'right',
								'css'	=>	'width:10%;',
							),			
			),
	'mongo_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'date_modified'=>array(
			'type' 		=> 'hidden',
			),
	'created_by'=>array(
			'type' 		=> 'hidden',
			),
	'modified_by'=>array(
			'type' 		=> 'hidden',
			),
	'description'=>array(
			'name' 		=>  __('Description'),
			'type' 		=> 'hidden',
			),
	'salesaccount_name'	=>array(
			'name' 		=>  __("Account"),
			'type' 		=> 'relationship',
			'cls'		=> 'salesaccounts',
			'list_syncname' => 'salesaccount_name',
			'id'		=> 'salesaccount_id',
			'css'		=> 'padding-left:2%;',
			'log'		=> '0',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'15',
							'css'	=>	'width:15%;',
						),
			),
	'salesaccount_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'contact_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'receipt_date'	=>array(
			'name' 		=> __('Date'),
			'type' 		=> 'date',
			'css'		=> 'padding-left:2%;',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'5',
							'css'	=>	'width:5%;',
						),
			),

	'paid_by'	=>array(
			'name' 		=>  __('Paid by'),
			'type' 		=> 'select',
			'droplist'	=> 'receipts_paid_by',
			'default'	=> '',
			'not_custom'=> '1',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'5',
							'css'	=>	'width:5%;',
						),
			),
	'our_bank_account'		=>array(
			'name' 		=>  __('Our bank account'),
			'type' 		=> 'select',
			'droplist'	=> 'receipts_our_bank_account',
			'width'		=> '47%',
			'css'		=> 'padding-left:4.5%;',
			'after'		=> '<div class="jt_after float_left" id="md_our_bank_account">&nbsp;</div>',
			'default'	=> '',
			),
	'name'	=>array(
			'name' 		=>  __('Reference'),
			'type' 		=> 'text',
			),
	'our_rep'	=>array(
			'name' 		=>  __('Our rep'),
			'type' 		=> 'relationship',
			'cls'		=> 'contacts',
			'id'		=> 'our_rep_id',
			'list_syncname'	=> 'our_rep',
			'para'		=> ',get_para_employee()',
			'not_custom'=> '1',
			'listview'	=>	array(
							'order'	=>	'1',
							'css'	=>	'width:10%;',
						),
			),
	'our_rep_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'our_csr'	=>array(
			'name' 		=>  __('Our CSR'),
			'type' 		=> 'relationship',
			'cls'		=> 'contacts',
			'id'		=> 'our_csr_id',
			'syncname'	=> 'first_name',
			'para'		=> ',get_para_employee()',
			'not_custom'=> '1',
			),
	'our_csr_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'identity'	=>array(
			'name' 		=> __('Identity'),
			'type' 		=> 'select',
			'droplist'	=> 'comms_position',
			),
	'use_own_letterhead'	=>array(
			'name' 		=> __('Use own letterhead'),
			'type' 		=> 'checkbox',
			'label'		=> '&nbsp;',
			'css'		=> 'width:98%;margin-left:0%;',
			'checkcss'	=> 'margin-left:3%;',
			'default'	=> 0,
			),
	'ext_accounts_sync'	=>array(
			'name' 		=> __('Ext Accounts sync'),
			'type' 		=> 'checkbox',
			'label'		=> ' Approved',
			'css'		=> 'width:98%;margin-left:0%;',
			'checkcss'	=> 'margin-left:3%;',
			'default'	=> 0,
			),
	'none'	=>array(
			'type' 		=> 'not_in_data',
			'moreclass' => 'fixbor2',
			),

	'allocation' => array(
			'type'	=>'fieldsave',
			'rel_name'	=>'allocation',
			),
	'notes' => array(
			'type'	=>'fieldsave',
			),
	'comments' => array(
			'type'	=>'fieldsave',
			),

);


// Panel 2
$ModuleField['field']['panel_2']['boxname'] = __('Receipt allocation');
$ModuleField['field']['panel_2']['block']['allocation'] = array(
	'title'	=>__('Receipt allocation'),
	'type'	=>'listview_box',
	'css'	=>'width:30.5%;margin-top:0;margin-left:1%;',
	'height' => '264',
	'add'	=> __('Add line'),
	'reltb'		=> 'tb_receipt@allocation',//tb@option
	'link'		=> array('w'=>'3', 'cls'=>'salesinvoices','field'=>'salesinvoice_id'),
	// 'delete' => '4',
	'custom_box_bottom'	=> '1',
	//'custom_box_top'	=> '1',
	'field'=> array(
				'salesinvoice_code' => array(
					'name' 	=>  __('&nbsp; Invoice no'),
					'type' 	=> 'select',
					'align' => 'left;overflow:inherit;',
					'width' => '23',
					'edit'	=>'1',
				),
				'salesinvoice_id' => array(
					'name' 	=>  __('&nbsp; Invoice no'),
					'type' 	=> 'hidden',
					'width' => '0',
				),
				'note' => array(
					'name' =>  __('&nbsp; Reference'),
					'type' =>'text',
					'width' => '28',
					'edit'	=>'1',
					'css' => 'padding-left:4%;padding-right:4%;width:92%;',
				),
				'write_off' => array(
					'name' =>  __('&nbsp; Write off'),
					'type' => 'checkbox',
					'width' => '14',
					'edit'	=>'1',
				),
				'amount' => array(
					'name' =>  __('Amount &nbsp;'),
					'type' => 'price',
					'align' => 'right',
					'width' => '20',
					'edit'	=>'1',
					'css' => 'padding-right:7%;width:93%;',
				),
				'mod' => array(
					'name' =>  __('Receipt Modem'),
					'type' 	=> 'hidden',
					'width' => '0',
				),
				'delete' => array(
					'name' =>  __(''),
					'type' => 'delete_icon',
					'node' => 'allocation',
					'rev'  => 'allocation',
					'align' => 'right',
					'width' => '4',
				),
			),
);

//panel 3
$ModuleField['field']['panel_3']['boxname'] = __('Outstanding invoices for this account');
$ModuleField['field']['panel_3']['block']['outstanding'] = array(
	'title'	=>__('Outstanding invoices for this account'),
	'type'	=>'listview_box',
	'custom_box_bottom' => '1',
	'css'	=>'width:42%;margin-top:0;margin-left:1%;',
	'height' => '540',
	'field'=> array(
				'receipt_this' => array(
					'name' 	=>  __('&nbsp;'),
					'type' 	=> 'text',
					'width' => '3',
				),
				'code' => array(
					'name' 	=>  __('&nbsp;Ref no'),
					'type' 	=> 'text',
					'width' => '10',
				),
				'invoice_type' => array(
					'name' =>  __('&nbsp;Type'),
					'type' =>'text',
					'width' => '12',
				),
				'invoice_date' => array(
					'name' =>  __('&nbsp;Date'),
					'type' => 'text',
					'align' => 'center;',
					'width' => '12',
				),
				'due' => array(
					'name' =>  __('<span class="due_red">Due</span>'),
					'type' => 'checkbox',
					'align' => 'center',
					'width' => '6',
				),
				'total' => array(
					'name' =>  __('Total&nbsp;'),
					'type' => 'price',
					'align' => 'right',
					'width' => '16',
				),
				'receipts' => array(
					'name' =>  __('Receipts&nbsp;'),
					'type' => 'price',
					'align' => 'right',
					'width' => '16',
				),
				'balance' => array(
					'name' =>  __('Balance&nbsp;'),
					'type' => 'price',
					'align' => 'right',
					'width' => '14',
				),
			),
);


// Panel 4
$ModuleField['field']['panel_4']['boxname'] = __('Notes');
$ModuleField['field']['panel_4']['block']['notes'] = array(
	'title'	=>__('Notes'),
	'type'	=>'text_box',
	'css'	=>'width:25%;margin-top:-265px;margin-left:0;',
	'height' => '200',
	'insert_timestamp' => __('Insert Timestamp'),
	'textarea_css'	=> 'height:200px; padding: 10px 2%;width:96%;',
);

//panel 5
$ModuleField['field']['panel_5']['boxname'] = __('Comments on receipt');
$ModuleField['field']['panel_5']['block']['comments'] = array(
	'title'	=>__('Comments on receipt'),
	'type'	=>'text_box',
	'css'	=>'width:30.5%;margin-top:-265px;margin-left:1%;',
	'height' => '200',
	'textarea_css'	=> 'height:200px;padding: 10px 1%;',
);



$ReceiptField = $ModuleField;