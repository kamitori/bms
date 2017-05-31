<?php
$ModuleField = array();
$ModuleField = array(
	'module_name' 	=> __('Company'),
	'module_label' 	=> __('Company'),
	'colection' 	=> 'tb_company',
	'title_field'	=> array('no','name','type_name','phone'),
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
			'name' 		=> __('Company no'),
			'type' 		=> 'text',
			'moreclass' => 'fixbor',
			'width'		=> '20%',
			'after'		=> '',
			'lock'		=> '1',
			'listview'	=>	array(
								'order'	=>	'1',
								'with'	=>	'100',
								'align'	=>	'center',
								'css'	=>	'width:5%;',
								'sort'=> '1',
							),
			),
	'is_customer'	=>array(
			'name' 		=>  __('Customer'),
			'type' 		=> 'hidden',
			'default'	=> 1
			),
	'is_supplier'	=>array(
			'label' 	=>  __('Supplier'),
			'type' 		=> 'hidden',
			'default'	=> 0
			),
	'mongo_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'name'	=>array(
			'name' 		=>  __('Company  name'),
			'type' 		=> 'text',
			'css'		=> 'padding-left:2%;width:95%;',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'15',
							'css'	=>	'width:15%;',
							'sort'=> '1',
						),
			),
	'type_name'	=>array(
			'name' 		=>  __('Type'),
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
			'listview'	=>	array(
							'order'	=>	'1',
							'css'	=>	'width:10%;',
							'sort'=> '1',
						),
			),
	'email'	=>array(
			'name' 		=> __('Email'),
			'type' 		=> 'email',
			'css'		=> 'padding-left:2%;',
			'listview'	=>	array(
							'order'	=>	'1',
							'css'	=>	'width:10%;',
							'sort'=> '1',
						),
			),
	/*'web'	=>array(
			'name' 		=> __('Web'),
			'type' 		=> 'email',
			'css'		=> 'padding-left:2%;',
			),*/
	'our_rep'	=>array(
			'name' 		=>  __('Our Rep'),
			'type' 		=> 'relationship',
			'cls'		=> 'contacts',
			'id'		=> 'our_rep_id',
			'para'		=> ',get_para_employee()',
			'not_custom'=> '1',
			'syncname'	=> 'first_name',
			'list_syncname' => 'our_rep',
			'listview'	=>	array(
							'order'	=>	'1',
							'css'	=>	'width:10%;',
							'sort'=> '1',
						),
			'null' 		=> 1
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
			'para'		=> ',get_para_employee()',
			'not_custom'=> '1',
			'list_syncname'	=> 'our_csr',
			'syncname'	=> 'first_name',
			'listview'	=>	array(
							'order'	=>	'1',
							'css'	=>	'width:10%;',
							'sort'=> '1',
						),
			'null' 		=> 1
	),
	'our_csr_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'inactive'	=>array(
			'name' 		=> __('Inactive'),
			'type' 		=> 'checkbox',
			'css'		=> 'padding-left:2%; border-bottom:none',
			'default'	=> 0,
			),
	'status'	=>array(
			'name' 		=> __('Status'),
			'type' 		=> 'select',
        	'not_custom' => '1',
			'droplist'	=> 'company_status',
			'default'	=> '',
			'css'		=> 'padding-left:2%;',
			'moreclass' => 'fixbor2',
			),
);



// Panel 1
$ModuleField['field']['panel_2'] = array(
	'setup'	=> array(
			'css'	=> 'width:100%;',
			'lablewith' => '35',//%
			'blockcss' => 'width:20%;float:left; margin-left:1%',
			'blocktype'=> 'address',
			),
	'address' =>array(
			'name' 		=> __('Default address'),
			'type' 		=> 'text',
			),
);


$ModuleField['field']['panel_3'] = array(
	'setup'	=> array(
			'css'	=> 'width:100%;',
			'lablewith' => '35',//%
			'blockcss' => 'width:48.2%;float:right;border:none;" id="entry_communication',
			),
);



//============ *** RELATIONSHIP *** =============//
$ModuleField['relationship']['contacts']['name'] =  __('Contacts');
$ModuleField['relationship']['contacts']['block']['contacts'] = array(
	'title'	=>__('Contacts for this company'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '300',
	'add'	=> __('Add option'),
	'reltb'		=> 'tb_contacts@contacts',//tb@option
	'link' => array('w' => '1', 'cls' => 'contacts','field'=>'_id'),
	'field'=> array(
				'title' => array(
					'name' 		=>  __('Title'),
					'width'		=> '4',
					'align' => 'center',
					'edit'		=> '1',
					'type'	    => 'select',
					'droplist'  => 'contacts_title',
					'not_custom'=>'1',
				),
				'first_name' => array(
					'name' 		=>  __('First Name'),
					'align' => 'center',
					'width'		=> '8',
					'edit'		=> '1',
				),
				'last_name' => array(
					'name' 		=>  __('Last Name'),
					'align' => 'center',
					'width'		=> '8',
					'edit'		=> '1',
				),
				'contact_default' => array(
					'name' 		=>  __('Default'),
					'width'		=> '3',
					'edit'		=> '1',
					'type'=>'checkbox',
					'align'     => 'center',
				),
				'position' => array(
					'name' 		=>  __('Position'),
					'width'		=> '8',
					'edit'		=> '1',
					'type'      => 'select',
					'droplist'  => 'contacts_position',
					'combobox_blank'=>'1',
				),
				'direct_dial' => array(
					'name' 		=>  __('Direct dial'),
					'width'		=> '10',
					'edit'		=> '1',
				),
				'extension_no' => array(
					'name' 		=>  __('Ext no'),
					'width'		=> '3',
					'edit'		=> '1',
				),
				'mobile' => array(
					'name' 		=>  __('Mobile'),
					'width'		=> '10',
					'edit'		=> '1',
				),
				'email' => array(
					'name' 		=>  __('Email address'),
					'width'		=> '15',
					'edit'		=> '1',
				),
				'emarketing' => array(
					'name' 		=>  __('eMarketing'),
					'title' 	=> 'Mail / eMarketing',
					'align'     => 'center',
					'width'		=> '6',
					'edit'		=> '1',
					'type'      => 'checkbox',
				),
				'inactive' => array(
					'name' 		=>  __('Inactive'),
					'width'		=> '4',
					'edit'		=> '1',
					'type'      => 'checkbox',
					'default'	=> 0,
					'align'		=> 'center',
				),
				'delete' => array(
					'name' 		=>  __(''),
					'width'		=> '1',
					'edit'		=> '1',
					'node'      => 'contacts',
					'type'      => 'delete_another',
					'rev'		=> '1',
					'align'		=> 'center',
				),
			),
);


$ModuleField['relationship']['addresses']['name'] =  __('Addresses');
$ModuleField['relationship']['addresses']['block']['addresses'] = array(
	'title'	=>__('Addresses for this company'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '250',
	'add'	=> __('Add new line'),
	'reltb'		=> 'tb_company@addresses',
	'delete' => '1',
	'field'=> array(
		'name' => array(
			'name' 		=>  __('Name'),
			'width'		=> '6',
			'align' => 'center',
			'edit'		=> '1',
			'type'	    => 'select',
			'droplist'  => 'contacts_addresses_name',
			'not_custom'=>'1',
		),
		'default' => array(
			'name' 		=>  __('Default'),
			'type'      => 'checkbox',
			'align' => 'center',
			'width'		=> '4',
			'edit'		=> '1',
		),
		'address_1' => array(
			'name' 		=>  __('Address 1'),
			'align' => 'center',
			'type' => 'text',
			'width'		=> '15',
			'edit'		=> '1',
		),
		'address_2' => array(
			'name' 		=>  __('Address 2'),
			'width'		=> '15',
			'type'		=> 'text',
			'edit'		=> '1',
			'type'=>'text',
		),
		'address_3' => array(
			'name' 		=>  __('Address 3'),
			'width'		=> '15',
			'edit'		=> '1',
			'type'      => 'text',
		),
		'town_city' => array(
			'name' 		=>  __('Town / City'),
			'width'		=> '8',
			'edit'		=> '1',
			'type'		=> 'text',
		),
		'province_state' => array(
			'name' 		=>  __('Province / State'),
			'width'		=> '8',
			'edit'		=> '1',
			'type'      => 'text',
		),
		'zip_postcode' => array(
			'name' 		=>  __('Zip / Post code'),
			'width'		=> '8',
			'edit'		=> '1',
			'type'	   => 'text',
		),
		'country' => array(
			'name' 		=>  __('Country'),
			'width'		=> '8',
			'edit'		=> '1',
			'type'      => 'text',
		),
	),
);


$ModuleField['relationship']['enquiries']['name'] =  __('Enquiries');
$ModuleField['relationship']['enquiries']['block']['enquiries'] = array(
	'title'	=>__('Enquiries from this company'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '250',
	'add'	=> __('Add new line'),
	'reltb'		=> 'tb_company@enquiries',
	'link' => array('w' => '1', 'cls' => 'enquiries','field'=>'_id'),
	'field'=> array(
		'no' => array(
			'name' 		=>  __('Ref no'),
			'width'		=> '6',
			'align' => 'center',
			'type' => 'text',
			'edit'		=> '1',
			'not_custom'=>'1',
		),
		'date_modified' => array(
			'name' 		=>  __('Date'),
			'type'      => 'date',
			'align' => 'left',
			'lock' => '1',
			'width'		=> '10',
		),
		'status' => array(
			'name' 		=>  __('Status'),
			'align' => 'center',
			'type' => 'text',
			'width'		=> '10',
			'edit'		=> '1',
		),
		'contact_name' => array(
			'name' 		=>  __('Contact'),
			'width'		=> '10',
			'type'		=> 'text',
			'edit'		=> '1',
			'type'=>'text',
		),
		'our_rep' => array(
			'name' 		=>  __('Our rep'),
			'width'		=> '10',
			'edit'		=> '1',
			'type'      => 'text',
		),
		'referred' => array(
			'name' 		=>  __('Referred'),
			'width'		=> '10',
			'type'		=> 'text',
		),
		'enquiry_value' => array(
			'name' 		=>  __('Enquiry value'),
			'width'		=> '10',
			'type'      => 'text',
		),
		'detail' => array(
			'name' 		=>  __('Requirements'),
			'width'		=> '20',
			'type'	   => 'text',
		),
		'delete' => array(
			'name' 		=>  __(''),
			'width'		=> '1',
			'edit'		=> '1',
			'node'      => 'enquiries',
			'type'      => 'delete_another',
			'rev'		=> '1',
			'align'		=> 'center',
		),
	),
);

$ModuleField['relationship']['jobs']['name'] =  __('Jobs');
$ModuleField['relationship']['jobs']['block']['jobs'] = array(
	'title'	=>__('Jobs for this company'),
	'type'	=>'listview_box',
	'css'	=>'width:70%;margin-top:0;',
	'height' => '240',
	'add'	=> __('Add new line'),
	'reltb'		=> 'tb_company@jobs',
	'link' => array('w' => '1', 'cls' => 'jobs','field'=>'_id'),
	'field'=> array(
		'no' => array(
			'name' 		=>  __('Job no'),
			'width'		=> '6',
			'align' => 'center',
			'type' => 'text',
			'not_custom'=>'1',
		),
		'name' => array(
			'name' 		=>  __('Job heading'),
			'type'      => 'text',
			'align' => 'left',
			'width'		=> '27',
		),
		'type' => array(
			'name' 		=>  __('Job type'),
			'type' => 'text',
			'width'		=> '10',
		),
		'work_start' => array(
			'name' 		=>  __('Start'),
			'width'		=> '12',
			'type'		=> 'date',
		),
		'work_end' => array(
			'name' 		=>  __('Finish'),
			'width'		=> '12',
			'type'	    => 'date',
		),
		'status' => array(
			'name' 		=>  __('Status'),
			'width'		=> '10',
			'type'		=> 'text',
		),
		'contact_name' => array(
			'name' 		=>  __('Job manager'),
			'width'		=> '10',
			'type'      => 'text',
		),
		'delete' => array(
			'name' 		=>  __(''),
			'width'		=> '1',
			'edit'		=> '1',
			'node'      => 'jobs',
			'type'      => 'delete_another',
			'rev'		=> '1',
			'align'		=> 'center',
		),
	),
);
$ModuleField['relationship']['jobs']['block']['default'] = array(
    'title' => __('Defaults for new jobs'),
    'css' => 'width:29%;float:right',
    'height' => '240',
    'moreclass' => '',
    'type' => 'editview_box',
    'field' => array(
        'markup_rate' => array(
            'name' => __('Markup rate'),
            'type' => 'text',
            'edit' => '1',
        ),
        'rate_per_hour' => array(
            'name' => __('Change rate per hour'),
            'type' => 'text',
            'edit' => '1',
        ),
    ),
);

$ModuleField['relationship']['tasks']['name'] =  __('Tasks');
$ModuleField['relationship']['tasks']['block']['tasks'] = array(
	'title'	=>__('Tasks relating to this company'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '250',
	'add'	=> __('Add new line'),
	'reltb'		=> 'tb_company@tasks',
	'link' => array('w' => '1', 'cls' => 'tasks','field'=>'_id'),
	'field'=> array(
		'no' => array(
			'name' 		=>  __('No'),
			'width'		=> '6',
			'align' => 'center',
			'type' => 'text',
			'not_custom'=>'1',
		),
		'name' => array(
			'name' 		=>  __('Task'),
			'type'      => 'text',
			//'align' => 'center',
			'width'		=> '30',
		),
		'type' => array(
			'name' 		=>  __('Type'),
			'align' => 'center',
			'type' => 'text',
			'width'		=> '10',
		),
		'our_rep' => array(
			'name' 		=>  __('Responsible'),
			'width'		=> '10',
			'type'		=> 'text',
			'type'=>'text',
		),
		'work_start' => array(
			'name' 		=>  __('Work start'),
			'width'		=> '10',
			'edit'		=> '1',
			'type'      => 'date',
		),
		'work_end' => array(
			'name' 		=>  __('Work end'),
			'width'		=> '10',
			'edit'		=> '1',
			'type'		=> 'date',
		),
		'status' => array(
			'name' 		=>  __('Status'),
			'width'		=> '10',
			'type'      => 'text',
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

$ModuleField['relationship']['documents']['name'] =  __('Documents');

/*$ModuleField['relationship']['comms']['name'] = __('Communications');
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
);*/

$ModuleField['relationship']['quotes']['name'] =  __('Quotes');
$ModuleField['relationship']['quotes']['block']['quotes'] = array(
	'title'	=>__('Quotations for this company'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '250',
	'add'	=> __('Add new line'),
	'reltb'		=> 'tb_company@quotes',
	'link' => array('w' => '1', 'cls' => 'quotations','field'=>'_id'),
	'field'=> array(
		'code' => array(
			'name' 		=>  __('Ref no'),
			'width'		=> '6',
			'align' => 'center',
			'type' => 'text',
			'not_custom'=>'1',
		),
		'quotation_type' => array(
			'name' 		=>  __('Type'),
			'type'      => 'text',
			'align' => 'center',
			'width'		=> '10',
		),
		'quotation_date' => array(
			'name' 		=>  __('Date'),
			'align' => 'center',
			'type' => 'date',
			'width'		=> '10',
		),
		'payment_due_date' => array(
			'name' 		=>  __('Due date'),
			'width'		=> '10',
			'type'		=> 'date',
		),
		'quotation_status' => array(
			'name' 		=>  __('Status'),
			'width'		=> '10',
			'type'		=> 'text',
			'type'=>'text',
		),
		'our_rep' => array(
			'name' 		=>  __('Our rep'),
			'width'		=> '10',
			'type'      => 'text',
		),
		'our_csr' => array(
			'name' 		=>  __('Our CRS'),
			'width'		=> '10',
			'type'		=> 'text',
		),
		'sum_sub_total' => array(
			'name' 		=>  __('Total (bf.Tax)'),
			'width'		=> '10',
			'type'      => 'price',
		),
		'heading' => array(
			'name' 		=>  __('Heading'),
			'width'		=> '10',
			'type'      => 'text',
		),
		'delete' => array(
			'name' 		=>  __(''),
			'width'		=> '1',
			'edit'		=> '1',
			'node'      => 'quotes',
			'type'      => 'delete_another',
			'rev'		=> '1',
			'align'		=> 'center',
		),
	),
);

$ModuleField['relationship']['orders']['name'] =  __('Orders');
$ModuleField['relationship']['orders']['block']['orders_cus'] = array(
	'title'	=>__('Sales orders for this company'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '200',
	'add'	=> __('Add new line'),
	'custom_box_bottom' => '1',
	'reltb'		=> 'tb_company@orders',
	'link' => array('w' => '1', 'cls' => 'salesorders','field'=>'_id'),
	'field'=> array(
		'code' => array(
			'name' 		=>  __('Ref no'),
			'width'		=> '6',
			'align' => 'center',
			'type' => 'text',
			'not_custom'=>'1',
		),
		'sales_order_type' => array(
			'name' 		=>  __('Type'),
			'type'      => 'text',
			'align' => 'center',
			'width'		=> '10',
		),
		'salesorder_date' => array(
			'name' 		=>  __('Date in'),
			'align' => 'center',
			'type' => 'date',
			'width'		=> '10',
		),
		'payment_due_date' => array(
			'name' 		=>  __('Due date'),
			'width'		=> '10',
			'type'		=> 'date',
		),
		'status' => array(
			'name' 		=>  __('Status'),
			'width'		=> '10',
			'type'		=> 'text',
		),
		'our_rep' => array(
			'name' 		=>  __('Our rep'),
			'width'		=> '10',
			'type'      => 'text',
		),
		'our_csr' => array(
			'name' 		=>  __('Our CRS'),
			'width'		=> '10',
			'type'		=> 'text',
		),
		'sum_sub_total' => array(
			'name' 		=>  __('Total (bf.Tax)'),
			'width'		=> '10',
			'type'      => 'price',
		),
		'sum_amount' => array(
			'name' 		=>  '',
			'type'      => 'hidden',
		),
		'heading' => array(
			'name' 		=>  __('Heading'),
			'width'		=> '10',
			'type'      => 'text',
		),
		'delete' => array(
			'name' 		=>  __(''),
			'width'		=> '1',
			'edit'		=> '1',
			'node'      => 'orders',
			'type'      => 'delete_another',
			'rev'		=> '1',
			'align'		=> 'center',
		),
	),
);

$ModuleField['relationship']['orders']['block']['orders_supplier'] = array(
	'title'	=>__('Purchase orders for this company'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0.8%;',
	'height' => '200',
	'add'	=> __('Add new line'),
	'reltb'		=> 'tb_company@orders',
	'link' => array('w' => '1', 'cls' => 'purchaseorders','field'=>'_id'),
	'custom_box_bottom' => '1',
	'field'=> array(
		'code' => array(
			'name' 		=>  __('Ref no'),
			'width'		=> '6',
			'align' => 'center',
			'type' => 'text',
			'not_custom'=>'1',
		),
		'required_date' => array(
			'name' 		=>  __('Required date'),
			'type'      => 'date',
			'align' => 'center',
			'width'		=> '10',
		),
		'delivery_date' => array(
			'name' 		=>  __('Delivery date'),
			'align' => 'center',
			'type' => 'date',
			'width'		=> '10',
		),
		'purchase_orders_status' => array(
			'name' 		=>  __('Status'),
			'width'		=> '10',
			'type'		=> 'text',
		),
		'ship_to_contact_name' => array(
			'name' 		=>  __('Supplier contact'),
			'width'		=> '10',
			'type'		=> 'text',
			'type'=>'text',
		),
		'our_rep' => array(
			'name' 		=>  __('Our rep'),
			'width'		=> '10',
			'type'      => 'text',
		),
		'sum_sub_total' => array(
			'name' 		=>  __('Total (bf.Tax)'),
			'width'		=> '10',
			'type'		=> 'price',
		),
		'name' => array(
			'name' 		=>  __('Heading'),
			'width'		=> '10',
			'type'      => 'text',
		),
		'delete' => array(
			'name' 		=>  __(''),
			'width'		=> '1',
			'edit'		=> '1',
			'node'      => 'purchase',
			'type'      => 'delete_another',
			'rev'		=> '1',
			'align'		=> 'center',
		),
	),
);

$ModuleField['relationship']['shipping']['name'] =  __('Shipping');
$ModuleField['relationship']['shipping']['block']['shipping'] = array(
	'title'	=>__('Shipping related to this company'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '250',
	'add'	=> __('Add new line'),
	'reltb'		=> 'tb_company@shipping',
	'link' => array('w' => '1', 'cls' => 'shippings','field'=>'_id'),
	'field'=> array(
		'code' => array(
			'name' 		=>  __('Ref no'),
			'width'		=> '6',
			'align' => 'center',
			'type' => 'text',
			'not_custom'=>'1',
		),
		'shipping_type' => array(
			'name' 		=>  __('Type'),
			'type'      => 'text',
			'align' => 'center',
			'width'		=> '10',
		),
		'return_status' => array(
			'name' 		=>  __('Return'),
			'type' => 'checkbox',
			'width'		=> '5',
		),
		'shipping_date' => array(
			'name' 		=>  __('Date Shipped'),
			'width'		=> '10',
			'type'		=> 'date',
		),
		'shipping_status' => array(
			'name' 		=>  __('Status'),
			'width'		=> '10',
			'type'		=> 'text',
			'type'=>'text',
		),
		'our_rep' => array(
			'name' 		=>  __('Our rep'),
			'width'		=> '10',
			'type'      => 'text',
		),
		'shipper' => array(
			'name' 		=>  __('Carrier'),
			'width'		=> '15',
			'type'		=> 'text',
		),
		'tracking_no' => array(
			'name' 		=>  __('Tracking no'),
			'width'		=> '15',
			'type'      => 'price',
		),
		'delete' => array(
			'name' 		=>  __(''),
			'width'		=> '1',
			'edit'		=> '1',
			'node'      => 'shipping',
			'type'      => 'delete_another',
			'rev'		=> '1',
			'align'		=> 'center',
		),
	),
);

$ModuleField['relationship']['rfqs']['name'] =  __('RFQ');
$ModuleField['relationship']['rfqs']['block']['rfqs'] = array(
	'title'	=>__('Supplier RFQs (Requtest for quotes)'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '250',
	'add'	=> __('Add new line'),
	'reltb'		=> 'tb_company@rfqs',
	'link' => array('w' => '1', 'cls' => 'rfqs','field'=>'_id'),
	'delete' => '1',
	'field'=> array(
		'code' => array(
			'name' 		=>  __('Ref no'),
			'width'		=> '6',
			'align' => 'center',
			'type' => 'text',
			'not_custom'=>'1',
		),
		'shipping_type' => array(
			'name' 		=>  __('Date'),
			'type'      => 'text',
			'align' => 'center',
			'width'		=> '10',
		),
		'return_status' => array(
			'name' 		=>  __('Deadline'),
			'align' => 'center',
			'type' => 'date',
			'width'		=> '5',
		),
		'received_date' => array(
			'name' 		=>  __('Status'),
			'width'		=> '10',
			'type'		=> 'date',
		),
		'shipping_status' => array(
			'name' 		=>  __('Late'),
			'width'		=> '5',
			'type'		=> 'text',
		),
		'our_rep' => array(
			'name' 		=>  __('Supplier contact'),
			'width'		=> '10',
			'type'      => 'text',
		),
		'shipper' => array(
			'name' 		=>  __('Supplier quote ref'),
			'width'		=> '10',
			'type'		=> 'text',
		),
		'4' => array(
			'name' 		=>  __('Price quoted'),
			'width'		=> '5',
			'edit'		=> '1',
			'type'      => 'price',
		),
		'tracking_no' => array(
			'name' 		=>  __('Employee'),
			'width'		=> '5',
			'edit'		=> '1',
			'type'      => 'text',
		),
		'3' => array(
			'name' 		=>  __('Code'),
			'width'		=> '5',
			'edit'		=> '1',
			'type'      => 'text',
		),
		'2' => array(
			'name' 		=>  __('Name / details'),
			'width'		=> '5',
			'edit'		=> '1',
			'type'      => 'text',
		),
		'1' => array(
			'name' 		=>  __('Quote ref'),
			'width'		=> '5',
			'edit'		=> '1',
			'type'      => 'text',
		),
	),
);

$ModuleField['relationship']['products']['name'] =  __('Products');
$ModuleField['relationship']['products']['block']['1'] = array(
	'title'	=>__('Products / services from this supplier'),
	'type'	=>'listview_box',
	'css'	=>'width:70%;margin-top:0;',
	'link' => array('w' => '1', 'cls' => 'products','field'=>'_id'),
	'height' => '150',
	'field'=> array(
		'code' => array(
			'name' 		=>  __('Code'),
			'width'		=> '6',
			'align' => 'center',
			'type' => 'text',
			'not_custom'=>'1',
		),
		'name' => array(
			'name' 		=>  __('Name / details'),
			'type'      => 'text',
			'align' => 'left',
			'width'		=> '25',
		),
		'sku' => array(
			'name' 		=>  __('SKU'),
			'align' => 'center',
			'type' => 'text',
			'width'		=> '8',
		),
		'product_type' => array(
			'name' 		=>  __('Type'),
			'width'		=> '8',
			'type'		=> 'text',
			'type'=>'text',
		),
		'sizew' => array(
			'name' 		=>  __('Size-W'),
			'width'		=> '5',
			'type'      => 'text',
		),
		'sizew_unit' => array(
			'name' 		=>  __(''),
			'width'		=> '5',
			'type'      => 'text',
		),
		'sizeh' => array(
			'name' 		=>  __('Size-H'),
			'width'		=> '5',
			'type'		=> 'text',
		),
		'sizeh_unit' => array(
			'name' 		=>  __(''),
			'width'		=> '5',
			'type'		=> 'text',
		),
		'sell_by' => array(
			'name' 		=>  __('Sold by'),
			'width'		=> '5',
			'type'      => 'text',
		),
		'sell_price' => array(
			'name' 		=>  __('Cost price'),
			'width'		=> '5',
			'type'      => 'price',
		),
		'unit_price' => array(
			'name' 		=>  __('Unit price'),
			'width'		=> '5',
			'type'      => 'price',
		),
	),
);

/*$ModuleField['relationship']['products']['block']['supplies_also_supplier'] = array(
	'title'	=>__('Supplier also supplies'),
	'type'	=>'listview_box',
	'add'	=> __('Add new line'),
	'css'	=>'width:29%;margin-top:0px;margin-left:1%; float: left',
	'height' => '150',
	'full_height' => '1',
	'reltb'		=> 'tb_company@2',//tb@option
	'field'=> array(
		'field'	=> array(
			'type'	=> 'text','width' => '5',
			),
		'keyword' => array(
					'name' 		=>  __(''),
					'width' => '80',
					'type' 		=> 'select',
					'droplist'	=> 'keyword_droplist',
					'edit'	=> '1',
					'not_custom'=>'1',
				),
	),
);*/

$ModuleField['relationship']['products']['block']['keyword'] = array(
	'title'	=>__('Supplier also supplies'),
	'type'	=>'listview_box',
	'css'	=>'width:29%;margin-top:0px;margin-left:1%; float: left',
	'height' => '150',
	'full_height' => '1',
	'add'	=> __('Add new line'),
	'reltb'		=> 'tb_company@keyword',//tb@option
	'delete' => '10',
	'field'=> array(
		'field'	=> array(
			'type'	=> 'text',
			'width' => '5',
			),
		'keyword' => array(
					'name' 		=>  __(''),
					'width' => '80',
					'type' 		=> 'select',
					'droplist'	=> 'supplier_also_supplies',
					'edit'	=> '1',
					'not_custom'=>'1',
				),
	),
);

$ModuleField['relationship']['products']['block']['pricing_category'] = array(
    'title' => 'Pricing category',
    'css' => 'width:20%;margin-top:0.9%;float:left;',
    'height' => '150',
    'reltb' => 'tb_company@products',
    'type' => 'editview_box',
	'field' => array(
	      'sell_category' => array(
			'name'  => __('Use '),
			'type' 		=> 'select',
			'droplist'	=> 'products_sell_category',
			'default'	=> '',
			'not_custom'=>'1',
			'edit'		=>'1',
			),
         'discount' => array(
			'name' => __('Discount %'),
			'type' => 'price',
			'css'=>'text-align:left;',
		),
         'net_discount' => array(
			'name' => __('Net Discount %'),
			'type' => 'price',
			'css'=>'text-align:left;',
		),
    ),
);
$ModuleField['relationship']['products']['block']['4'] = array(
	'title'	=>__('Specific pricing for this customer'),
	'type'	=>'listview_box',
	'css'	=>'width:49%;margin-top:0.9%;float: left; margin-left: 1.1%',
	'add'	=> __('Add new line'),
	'reltb'		=> 'tb_company@pricing',//tb@option
	'delete' => '2',
	'height' => '167',
	'field'=> array(
		/*'code' => array(
			'name' 		=>  __('Code'),
			'width'		=> '6',
			'align' => 'center',
			'type' => 'link_code',
			'url'=>'javascript:void(0) "class="link_to_pricing',
			'not_custom'=>'1',
		),*/
		'sku' => array(
			'name' 		=>  __('SKU'),
			'align' => 'center',
			'width'		=> '8',
			'type' => 'link_code',
			'url'=>'javascript:void(0) "class="link_to_pricing',
			'not_custom'=>'1',
		),		
		'name' => array(
			'name' 		=>  __('Name'),
			'type'      => 'text',
			'align' => 'left',
			'width'		=> '60',
		),
		'product_id' => array(
			'name' 		=>  __('Name'),
			'type'      => 'hidden',
		),
		'range' => array(
			'name' 		=>  __('Range'),
			'align' => 'center',
			'type' => 'text',
			'width'		=> '10',
		),
		'unit_price' => array(
			'name' 		=>  __('Unit Price'),
			'width'		=> '10',
			'type'		=> 'price',
		)
	),
);
$ModuleField['relationship']['products']['block']['5'] = array(
	'title'	=>__('Units / assets for this customer'),
	'type'	=>'listview_box',
	'css'	=>'width:28.8%;margin-top:0.9%;float: left; margin-left: 1.1%',
	'height' => '168',
	'field'=> array(
	           ),
);

$ModuleField['relationship']['other']['name'] =  __('Other');
$ModuleField['relationship']['other']['block']['other_detail'] = array(
	'title'	=>__('Other details'),
	'type'	=>'listview_box',
	'css'	=>'width:48%;margin-top:0;',
	'height' => '406',
	'add'	=> __('Add new line'),
	'reltb'		=> 'tb_company@other',
	'delete' => '2',
	'field'=> array(
		'heading' => array(
			'name' 		=>  __('Heading'),
			'width'		=> '30',
			'align' => 'left',
			'type' => 'text',
			'edit'		=> '1',
		),
		'details' => array(
			'name' 		=>  __('Detail'),
			'type'      => 'text',
			'align' => 'center',
			'width'		=> '60',
			'edit'		=> '1',
		),
	),
);

$ModuleField['relationship']['other']['block']['2'] = array(
	'title'	=>__('Group linked to this copany'),
	'type'	=>'listview_box',
	'css'	=>'width:30%;margin-left:1%;',
	'height' => '406',
	'reltb'		=> 'tb_company@other',
	'field'=> array(
		'code' => array(
			'name' 		=>  __('Group'),
			'width'		=> '100',
			'align' => 'left',
			'type' => 'text',
			'edit'		=> '1',
		),
	),
);

$ModuleField['relationship']['other']['block']['profile'] = array(
    'title' => 'Profile',
    'css' => 'width:20%;margin-left:1%;float:left;',
    'height' => '150',
    'reltb' => 'tb_company@profile',
    'type' => 'editview_box',
    'field' => array(
		'profile_type' => array(
			'name' => __('Type'),
			'type' => 'select',
			'droplist' => 'company_type',
			'default' => '',
			'not_custom'=>'1',
			'edit'		=>'1',
		),
		'category' => array(
			'name' => __('Category'),
			'type' => 'select',
			'css'=>'text-align:left;',
			'droplist' => 'company_category',
			'default' => '',
			'edit'		=>'1',
		),
		'rating' => array(
			'name' => __('Rating'),
			'type' => 'select',
			'css'=>'text-align:left;',
			'droplist' => 'company_rating',
			'default' => '',
		),
		'no_of_staff' => array(
			'name' => __('No of staff'),
			'type' => 'text',
			'css'=>'text-align:left;',
			'edit'		=>'1',
		),
		'none' => array(
			'name' => __('Phone Related'),
			'type' => 'header',
		),
		'speed_dial' => array(
			'name' => __('Speed Dial'),
			'type' => 'text',
			'label'=> ' using serial numbers &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
		),
		'generate_serials' => array(
			'name'  => __('For'),
			'type' 		=> 'select',
			'droplist'	=> 'generate_serials_droplist',
			'default'	=> '',
			'not_custom'=>'1',
			'edit'		=>'1',
		),
		'include_in_phone_book' => array(
			'name'  => __('Include in phonebook'),
			'type' => 'checkbox',
			'css'		=> 'padding-left:2%; border-bottom:none',
			'default'   => '0',
		),
		'phone_sort_by' => array(
			'name'  => __('Sort by'),
			'type' => 'text',
			'element_input'=>'maxlength="20"',
		),
		'none2' => array(
			'name' => __('Custom field'),
			'type' => 'header',
		),
	  	'email_so_completed' => array(
			'name'  => __('Email SO completed'),
			'type' => 'checkbox',
			'css'		=> 'padding-left:2%; border-bottom:none',
			'default'   => '0',
		),
		'business_type'	=>array(
			'name' 		=>  __('Business Type'),
			'type' 		=> 'select',
			'droplist'  => 	'company_business_type',
			'default'	=> '',
		),
		'industry'		=>array(
			'name' 		=>  __('Industry'),
			'type' 		=> 'select',
			'droplist'  => 	'company_industry',
			'default'	=> '',
		),
		/*'size'		=>array(
			'name' 		=>  __('Size'),
			'type' 		=> 'select',
			'droplist'  => 	'company_size',
			'default'	=> '',
		),*/
		'postal_mail_only'		=>array(
			'name' 		=>  __('Postal Mail only'),
			'type' 		=> 'checkbox',
			'css'		=> 'padding-left:2%; border-bottom:none',
			'default'   => '0',
		),
		'is_shipper'	=>array(
			'name' 		=>  __('Is shipper'),
			'type' 		=> 'checkbox',
			'css'		=> 'padding-left:2%; border-bottom:none',
			'default'   => '0',
		),
		'tracking_url'		=>array(
			'name' 		=>  __('Tracking URL'),
			'type' 		=> 'text',
		),
    ),
);

$ModuleField['relationship']['account']['name'] =  __('Account');
$ModuleField['relationship']['account']['block']['sales_invoice_this_company'] = array(
	'title'	=>__('Sales invoices for this company'),
	'type'	=>'listview_box',
	'moreclass' => 'clear_percent_11 float_left right_pc',
	'css'	=>'width:69%;margin-top:0;height: 350px;',
	'height' => '228',
	'add'	=> __('Add new line'),
	'reltb'		=> 'tb_company@account',
	'link' => array('w' => '1', 'cls' => 'salesinvoices','field'=>'_id'),
	// 'delete' => '1',
	'custom_box_bottom'=>1,
	'field'=> array(
		'code' => array(
			'name' 		=>  __('Ref no'),
			'width'		=> '6',
			'align' => 'center',
			'type' => 'text',
			'not_custom'=>'1',
		),
		'customer_po_no' => array(
			'name' 		=>  __('PO#'),
			'type'      => 'text',
			'align' => 'center',
			'width'		=> '10',
		),
		'invoice_date' => array(
			'name' 		=>  __('Date'),
			'align' => 'center',
			'type' => 'date',
			'width'		=> '10',
		),
		'invoice_status' => array(
			'name' 		=>  __('Status'),
			'width'		=> '10',
			'type'		=> 'text',
		),
		'our_rep' => array(
			'name' 		=>  __('Our rep'),
			'width'		=> '7',
			'type'		=> 'text',
		),
		'other_comment' => array(
			'name' 		=>  __('Comments'),
			'width'		=> '15',
			'type'      => 'text',
		),
		'sum_amount' => array(
			'name' 		=>  __('Total'),
			'width'		=> '7',
			'type'		=> 'price',
			'align' => 'right',
		),
		'total_receipt' => array(
			'name' 		=>  __('Receipts'),
			'width'		=> '7',
			'type'      => 'price',
			'align' => 'right',
		),
		'balance' => array(
			'name' 		=>  __('Balance'),
			'width'		=> '7',
			'type'      => 'price',
			'align' => 'right',
		),
		'delete' => array(
			'name' 		=>  __(''),
			'width'		=> '3',
			'edit'		=> '1',
			'node'      => 'invoices',
			'type'      => 'delete_another',
			'rev'		=> '1',
			'align'		=> 'center',
		),
	),
);

$ModuleField['relationship']['dealer_discount']['name'] =  __('Dealer Discount');
$ModuleField['relationship']['dealer_discount']['block']['dealer_discount'] = array(
    'title' => 'Dealer discount',
    'css' => 'width:20%;margin-top:0.9%;float:left;margin-bottom:4%',
    'height' => '160',
	'reltb'		=> 'tb_company@dealer_discount',
    'type' => 'editview_box',
	'field' => array(
         'dealer_discount' => array(
			'name' => __('Dealer Discount %'),
			'type' => 'price',
			'css'=>'text-align:left;',
		),
    ),
);
$ModuleField['relationship']['dealer_discount']['block']['dealer_pricing'] = array(
	'title'	=>__('Specific discount for this dealer'),
	'type'	=>'listview_box',
	'css'	=>'width:78%;margin-top:0.9%;float: left; margin-left: 1.1%',
	'add'	=> __('Add new line'),
	'reltb'		=> 'tb_company@dealer_pricing',//tb@option
	'delete' => '2',
	'height' => '167',
	'field'=> array(
		'code' => array(
			'name' 		=>  __('Code'),
			'width'		=> '6',
			'align' => 'center',
			'type' => 'text',
		),
		'name' => array(
			'name' 		=>  __('Name'),
			'type'      => 'text',
			'align' => 'left',
			'width'		=> '60',
		),
		'product_id' => array(
			'name' 		=>  __('Name'),
			'type'      => 'hidden',
		),
		'discount' => array(
			'name' 		=>  __('Discount %'),
			'width'		=> '10',
			'type'		=> 'price',
			'edit' 		=> 1
		)
	),
);

/*$ModuleField['relationship']['account']['block']['account_related'] = array(
    'title' => __('Account related'),
    'css' => 'width:29%;float:right',
    'height' => '210',
    'moreclass' => '',
    'type' => 'editview_box',
    'custom_box_top' => '1',
    'field' => array(
        'status' => array(
            'name' => __('Account'),
            'type' => 'select',
            'droplist' => 'salesaccounts_status',
            'default' => '',
            'edit' => '1',
        ),
        'balance' => array(
            'name' => __('Account balance'),
            'type' => 'text',
            'edit' => '1',
        ),
        'credit_limit' => array(
            'name' => __('Credit limit'),
            'type' => 'price',
            'edit' => '1',
        ),
        'difference' => array(
            'name' => __('Difference'),
            'type' => 'price',
            'edit' => '1',
        ),
        'payment_terms' => array(
            'name' => __('Payment terms'),
            'type' => 'select',
            'droplist' => 'salesaccounts_payment_terms',
            'default' => '',
            'edit' => '1',
        ),
        'tax_code' => array(
            'name' => __('Default Tax code'),
            'type' => 'text',
            'edit' => '1',
        ),
        'nominal_code' => array(
            'name' => __('Default nominal code'),
            'type' => 'select',
            'droplist' => 'salesaccounts_nominal_code',
            'default' => '',
            'edit' => '1',
        ),
        'tax_no' => array(
            'name' => __('Tax no'),
            'type' => 'text',
            'edit' => '1',
        ),
        'quotation_limit' => array(
            'name' => __('Quotation limit'),
            'type' => 'text',
            'edit' => '1',
        ),
    ),
);*/

$CompanyField = $ModuleField;