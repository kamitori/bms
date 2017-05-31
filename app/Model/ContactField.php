<?php
$ModuleField = array();
$ModuleField = array(
	'module_name' 	=> __('Contact'),
	'module_label' 	=> __('Contacts'),
	'colection' 	=> 'tb_contact',
	'title_field'	=> array('no','company','type','our_rep'),
);


//============= *** FIELDS *** =============//

// Panel 1
$ModuleField['field']['panel_1'] = array(
	'setup'	=> array(
			'css'	=> 'width:100%;',
			'lablewith' => '35',
			'blockcss' => 'width:30%;float:left;',
			),
	'mongo_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'no' => array(
		'name' 		=> __(' Ref no'),
		'type' 		=> 'text',
		'moreclass' => 'fixbor',
		'width'		=> '38%;float:left',
		'css'		=> 'width:95%; padding-left:5%;float:left;border-right:1px solid #ddd',
		'after_field'=>'is_customer',
		'lock'		=> '1',
	),
	'is_customer'	=>array(
			'label' 	=>  __('Customer'),
			'type' 		=> 'checkbox',
			'other_type'=> 'after_other',
			'default'	=> '0',
			'classselect' =>'jt_after_field',
			'width'		=> '22%;margin:0px;padding-left:1%" id="field_after_contacttype" alt="',
			'checkcss' => 'float:right;margin-bottom:-2px;margin-top:0px;',
			'css'=> 'margin-top:4px;margin-left:0px !important;',
			'not_custom'=> '1',
			),
	'is_employee'	=>array(
			'label' 	=>  __('Employee'),
			'type' 		=> 'hidden',
			'other_type'=> 'after_other',
			'default'	=> '0',
			'classselect' =>'jt_after_field',
			'width'		=> '22%;margin:0px;padding-left:1%" id="field_after_contacttype" alt="',
			'checkcss' => 'float:right;margin-bottom:-2px;margin-top:0px;',
			'css'=> 'margin-top:4px;margin-left:0px !important;',
			'not_custom'=> '1',
			),
	'title' => array(
			'name' 		=> __('Title'),
			'type' 		=> 'select',
			'droplist' => 'contacts_title',
        	'default' => 'Dr',
        	'not_custom'=> '1',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'4',
							'css'	=>	'width:4%;',
						),
			),
	'first_name' => array(
			'name' 		=> __('First name'),
			'type' 		=> 'text',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'8',
							'css'	=>	'width:8%;',
						),
			),
	'last_name' => array(
			'name' 		=> __('Last name'),
			'type' 		=> 'text',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'8',
							'css'	=>	'width:8%;',
						),
			),
	'type'	=>array(
			'name' 		=> __('Type'),
			'type' 		=> 'select',
			'droplist' => 'contacts_type',
			/*'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'8',
							'css'	=>	'width:8%;',
						),*/
			),
	'direct_dial'	=>array(
			'name' 		=> __('Direct Dial'),
			'type' 		=> 'phone',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'8',
							'css'	=>	'width:8%;',
						),
			),
	'mobile'	=>array(
			'name' 		=> __('Mobile'),
			'type' 		=> 'phone',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'8',
							'css'	=>	'width:8%;',
						),
			),
	'email'=>array(
			'name' 		=>  __('Email'),
			'type' 		=> 'email',
			),
	'none'	=>array(
		'type' 		=> 'not_in_data',
		'moreclass' => 'fixbor2',
		),

	'field03' => array(
			'type'	=>'fieldsave',
			),
);





$ModuleField['field']['panel_2'] = array(
	'setup'	=> array(
			'css'	=> 'width:33%;',
			'lablewith' => '44',
			'blockcss' => 'width:32%;float:left;',
			),
	'fax'	=>array(
			'name' 		=> __('Fax'),
			'type' 		=> 'email',
			'default' => '',
			'moreclass' => 'fixbor',
			),
	'home_phone'	=>array(
			'name' 		=> __('Home phone'),
			'type' 		=> 'phone',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'8',
							'css'	=>	'width:8%;',
						),
			),
	'company'	=>array(
			'name' 		=>  __('Company'),
			'type' 		=> 'relationship',
			'cls'		=> 'companies',
			'id'		=> 'company_id',
        	'list_syncname' => 'company',
			'css'		=> 'padding-left:2%;',
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
	'company_phone'	=>array(
			'name' 		=> __('Company phone'),
			'type' 		=> 'phone',
			'default' => '',
			),
	'position'	=>array(
			'name' 		=> __('Position'),
			'type' 		=> 'select',
			'droplist' => 'contacts_position',
			'default'	=> '',
			),
	'department'	=>array(
			'name' 		=> __('Department'),
			'type' 		=> 'select',
			'droplist' => 'contacts_department',
			'default' => '',
			),
	'extension_no'	=>array(
			'name' 		=> __('Extension no'),
			'type' 		=> 'phone',
			),
	'none'	=>array(
		'type' 		=> 'not_in_data',
		),
	'none2'	=>array(
		'type' 		=> 'not_in_data',
		'moreclass' => 'fixbor2',
		),

	'field06' => array(
			'type'	=>'fieldsave',
			),
);


$ModuleField['field']['panel_3'] = array(
	'setup'	=> array(
			'css'	=> 'width:70%;',
			'lablewith' => '35',//%
			'blockcss' => 'width:69%;float:right;',
			'blocktype'=> 'address',
			),
	'invoice_address' =>array(
			'name' 		=> __('Default address'),
			'type' 		=> 'text',
			),
);



// Panel 4
$ModuleField['field']['panel_4'] = array(
	'setup'	=> array(
			'css'	=> 'width:33%;',
			'lablewith' => '44',
			'blockcss' => 'width:49%;float:left;',
			),
	'inactive' => array(
			'name' 		=> __('Inactive'),
			//'type' 		=> 'text',
			'type' 		=> 'checkbox',
			'default' => '0',
			'css'		=> 'padding-left:2%; border-bottom:none',
			),
    'addresses_default_key' => array(
			'type' 		=> 'hidden',
			),
	'none'	=>array(
		'type' 		=> 'not_in_data',
		),
	'none2'	=>array(
		'type' 		=> 'not_in_data',
		),
	'none3'	=>array(
		'type' 		=> 'not_in_data',
		),
	'none4'	=>array(
		'type' 		=> 'not_in_data',
		),
	'none5'	=>array(
		'type' 		=> 'not_in_data',
		),
	'none6'	=>array(
		'type' 		=> 'not_in_data',
		),
	'none7'	=>array(
		'type' 		=> 'not_in_data',
		),
	'none8'	=>array(
		'type' 		=> 'not_in_data',
		),
	'field06' => array(
			'type'	=>'fieldsave',
			),
);



//============ *** RELATIONSHIP *** =============//



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
			'width'		=> '10',
			'edit'		=> '1',
		),
		'address_2' => array(
			'name' 		=>  __('Address 2'),
			'width'		=> '10',
			'type'		=> 'text',
			'edit'		=> '1',
			'type'=>'text',
		),
		'address_3' => array(
			'name' 		=>  __('Address 3'),
			'width'		=> '10',
			'edit'		=> '1',
			'type'      => 'text',
		),
		'town_city' => array(
			'name' 		=>  __('Town / City'),
			'width'		=> '10',
			'edit'		=> '1',
			'type'		=> 'text',
		),
		'province_state_id' => array(
			'name' 		=>  __('Province / State'),
			'width'		=> '10',
			'edit'		=> '1',
			'type'      => 'select_dynamic',
			'droplist'  => 'list_provine'
		),
		'zip_postcode' => array(
			'name' 		=>  __('Zip / Post code'),
			'width'		=> '10',
			'edit'		=> '1',
			'type'	   => 'text',
		),
		'country_id' => array(
			'name' 		=>  __('Country'),
			'width'		=> '10',
			'edit'		=> '1',
			'type'      => 'select_dynamic',
			'droplist'  => 'list_country'
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
	'delete' => '1',
	'field'=> array(
		'no' => array(
			'name' 		=>  __('Ref no'),
			'width'		=> '6',
			'align' => 'center',
			'type' => 'text',
			'not_custom'=>'1',
		),
		'date_modified' => array(
			'name' 		=>  __('Date'),
			'type'      => 'date',
			'align' => 'center',
			'width'		=> '10',
		),
		'status' => array(
			'name' 		=>  __('Status'),
			'align' => 'center',
			'type' => 'text',
			'width'		=> '10',
		),
		'contact_name' => array(
			'name' 		=>  __('Contact'),
			'width'		=> '10',
			'type'		=> 'text',
			'type'=>'text',
		),
		'our_rep' => array(
			'name' 		=>  __('Our rep'),
			'width'		=> '10',
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
	),
);

$ModuleField['relationship']['jobs']['name'] =  __('Jobs');
$ModuleField['relationship']['jobs']['block']['jobs'] = array(
	'title'	=>__('Jobs for this company'),
	'type'	=>'listview_box',
	'css'	=>'width:70%;margin-top:0;',
	'height' => '265',
	'add'	=> __('Add new line'),
	'reltb'		=> 'tb_company@jobs',
	'link' => array('w' => '1', 'cls' => 'jobs','field'=>'_id'),
	'delete' => '1',
	'field'=> array(
		'no' => array(
			'name' 		=>  __('Job no'),
			'width'		=> '6',
			'align' => 'center',
			'type' => 'text',
			'not_custom'=>'1',
		),
		'name' => array(
			'name' 		=>  __('Job name'),
			'type'      => 'text',
			'align' => 'center',
			'width'		=> '10',
		),
		'type' => array(
			'name' 		=>  __('Job type'),
			'align' => 'center',
			'type' => 'text',
			'width'		=> '10',
		),
		'work_start' => array(
			'name' 		=>  __('Start'),
			'width'		=> '10',
			'type'		=> 'date',
		),
		'work_end' => array(
			'name' 		=>  __('Finish'),
			'width'		=> '10',
			'type'      => 'date',
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
	),
);
$ModuleField['relationship']['jobs']['block']['default'] = array(
    'title' => __('Defaults for new jobs'),
    'css' => 'width:29%;float:right',
    'height' => '242',
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




//====== Task =======//
$ModuleField['relationship']['task']['name'] =  __('Task');
//Premission list data
$ModuleField['relationship']['task']['block']['task'] = array(
	'title'	=>__('Tasks relating to this contact'),
	'type'	=>'listview_box',
	'link' => array('w' => '1', 'cls' => 'tasks','field'=>'_id'),
	'css'	=>'width:100%;margin-top:0;',
	'height' => '250',
	'add'	=> __('Add new Task'),
	//'reltb'		=> 'tb_enquiry@task',//tb@option
	//'delete' => '6',
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
			),
);



//====== personal =======//
$ModuleField['relationship']['personal']['name'] = __('Personal');
$ModuleField['relationship']['personal']['block']['profile'] = array(
    'title' => 'Employee details',
    'css' => 'width:39%;float:left;',
    'height' => '200',
    'reltb' => 'tb_task@profile',
    'type' => 'editview_box',
    'field' => array(
		'status' => array(
			'name' => __('Marital status'),
			'type' => 'select',
			'droplist' => 'contact_employee_marital_status',
			'default' => '',
			'not_custom'=>'1',
			'edit'		=>'1',
		),
		'date_birth'=>array(
			'name' 		=>  __('Date of birth'),
			'type' 		=> 'date',
			),
		'ssn_no' => array(
			'name' => __('SSN/PPS no'),
			'type' => 'text',
			'css'=>'text-align:left;',
			'edit'		=>'1',
		),
		'none' => array(
			'name' => __('Employment details'),
			'type' => 'header',
		),
		'start_date' => array(
			'name' => __('Start date'),
			'type' => 'text',
			'label'=> ' using serial numbers &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
		),
		'finish_date' => array(
			'name'  => __('Finish date'),
			'type' 		=> 'text',
			'default'	=> '',
			'not_custom'=>'1',
			'edit'		=>'1',
		),
		'weeks_worked' => array(
			'name'  => __('Weeks worked'),
			'type' => 'text',
			'element_input'=>'maxlength="20"',
		),
    ),
);
$ModuleField['relationship']['personal']['block']['emergency'] = array(
	'title'	=>__('Emergency / other contacts'),
	'type'	=>'listview_box',
	'css'	=>'width:60%;margin-top:0;float:right;',
	'height' => '225',
	'add'	=> __('Add new line'),
	'reltb'		=> 'tb_basic@emergency',//tb@option
	'delete' => '3',
	'field'=> array(
				'name' => array(
					'name' 		=>  __('Name'),
					'width' => '35',
					'type'	=> 'text',
					'edit'	=> '1',
				),
				'phone' => array(
					'name' 		=>  __('Phone'),
					'width' => '10',
					'type'	=> 'text',
					'edit'	=> '1',
				),
				'relationship' => array(
					'name' 		=>  __('Relationship / notes'),
					'width' => '47',
					'type'	=> 'text',
					'edit'	=> '1',
				),
			),
);



//====== rate =======//
$ModuleField['relationship']['rate']['name'] = __('Rates/Wages');
$ModuleField['relationship']['rate']['block']['profile'] = array(
    'title' => 'Employee details',
    'css' => 'width:31%;float:left;',
    'height' => '200',
    'reltb' => 'tb_task@profile',
    'type' => 'editview_box',
    'field' => array(
		'employee_type' => array(
			'name' => __('Employee type'),
			'type' => 'select',
			'droplist' => 'contacts_employment_type',
			'default' => '',
			'not_custom'=>'1',
			'edit'		=>'1',
		),
		'employment_type' => array(
			'name' => __('Employment type'),
			'type' => 'select',
			'droplist' => 'company_type',
			'css'=>'text-align:left;',
			'edit'		=>'1',
		),
		'work_type' => array(
			'name' => __('Work type'),
			'type' => 'select',
			'droplist' => 'contacts_work_type',
			'label'=> ' using serial numbers &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
		),
		'paid_by' => array(
			'name'  => __('Paid by'),
			'type' => 'select',
			'droplist' => 'employee_paid_by',
			'default'	=> '',
			'not_custom'=>'1',
			'edit'		=>'1',
		),
		'overtime_starts_at' => array(
			'name'  => __('Overtime starts at'),
			'type' => 'text',
			'element_input'=>'maxlength="20"',
		),
		'overtime_ends_at' => array(
			'name'  => __('Overtime ends at'),
			'type' => 'text',
			'element_input'=>'maxlength="20"',
		),
		'commission' => array(
			'name'  => __('Commission(%)'),
			'type' => 'text',
			'element_input'=>'maxlength="20" onkeypress="return isNumbers(event);"',
		),
    ),
);
$ModuleField['relationship']['rate']['block']['review'] = array(
	'title'	=>__('Employment reviews and rates'),
	'type'	=>'listview_box',
	'css'	=>'width:68%;margin-top:0;float:right;',
	'height' => '215',
	'add'	=> __('Add new line'),
	'reltb'		=> 'tb_basic@review',//tb@option
	'delete' => '1',
	'field'=> array(
				'reviewed_date' => array(
					'name' 		=>  __('Reviewed'),
					'width' => '7',
					'type'	=> 'date',
					'edit'	=> '1',
				),
				'start_date' => array(
					'name' 		=>  __('Start'),
					'width' => '7',
					'type'	=> 'date',
					'edit'	=> '1',
				),
				'finish_date' => array(
					'name' 		=>  __('Finish'),
					'width' => '7',
					'type'	=> 'date',
					'edit'	=> '1',
				),
				'weeks' => array(
					'name' 		=>  __('Weeks'),
					'width' => '5',
					'type'	=> 'text',
					'edit'	=> '0',
				),
				'wage' => array(
					'name' 		=>  __('Wage'),
					'width' => '4',
					'type'	=> 'text',
				),
				'per' => array(
					'name' 		=>  __('Per'),
					'width' => '4',
					'type'	=> 'text',
					'edit'	=> '1',
				),
				'hour_cost_rate' => array(
					'name' 		=>  __('Cost rate'),
					'width' => '4',
					'type'	=> 'text',
					'edit'	=> '1',
				),
				'hour_overtime' => array(
					'name' 		=>  __('Overtime'),
					'width' => '7',
					'type'	=> 'text',
					'edit'	=> '1',
				),
				'hour_bill_rate' => array(
					'name' 		=>  __('Bill rate'),
					'width' => '6',
					'type'	=> 'text',
					'edit'	=> '1',
				),
				'day_cost_rate' => array(
					'name' 		=>  __('Cost rate'),
					'width' => '6',
					'type'	=> 'text',
					'edit'	=> '1',
				),
				'day_bill_rate' => array(
					'name' 		=>  __('Bill rate'),
					'width' => '6',
					'type'	=> 'text',
					'edit'	=> '1',
				),
				'commission' => array(
					'name' 		=>  __('Commission'),
					'width' => '8',
					'type'	=> 'text',
					'edit'	=> '1',
				),
				'notes' => array(
					'name' 		=>  __('Notes'),
					'width' => '10',
					'type'	=> 'text',
					'edit'	=> '1',
				),
			),
);


//====== expenses =======//
$ModuleField['relationship']['expense']['name'] = __('Expenses');
$ModuleField['relationship']['expense']['block']['expenses'] = array(
	'title'	=>__('Expenses for this employee'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;float:right;',
	'height' => '200',
	'add'	=> __('Add new line'),
	'reltb'		=> 'tb_basic@expenses',//tb@option
	'delete' => '1',
	'field'=> array(
				'type' => array(
					'name' 		=>  __('Type'),
					'width' => '6',
					'type' => 'select',
					'droplist' => 'contact_expense_type',
					'edit'	=> '1',
				),
				'expenses_date' => array(
					'name' 		=>  __('Date'),
					'width' => '10',
					'type'	=> 'date',
					'edit'	=> '1',
				),
				/*'code' => array(
					'name' 		=>  __('Code'),
					'width' => '8',
					'type'	=> 'text',
					'edit'	=> '1',
				),*/
				'name' => array(
					'name' 		=>  __('Name / details'),
					'width' => '40',
					'type'	=> 'text',
					'edit'	=> '1',
				),
				'reference' => array(
					'name' 		=>  __('Reference'),
					'width' => '15',
					'type'	=> 'text',
					'edit'	=> '1',
				),
				'cost_price' => array(
					'name' 		=>  __('Net'),
					'width' => '10',
					'type'	=> 'price',
					'edit'	=> '1',
				),
				/*'quantity' => array(
					'name' 		=>  __('Quantity'),
					'width' => '8',
					'type'	=> 'price',
					'edit'	=> '1',
				),*/
				'total' => array(
					'name' 		=>  __('Total'),
					'width' => '10',
					'type'	=> 'price',
				),
				/*'debit' => array(
					'name' 		=>  __('Debit'),
					'width' => '8',
					'type'	=> 'price',
					'edit'	=> '1',
				),
				'credit' => array(
					'name' 		=>  __('Credit'),
					'width' => '8',
					'type'	=> 'price',
					'edit'	=> '1',
				),*/
			),
);


//====== leave =======//
$ModuleField['relationship']['leave']['name'] = __('Leave');
$ModuleField['relationship']['leave']['block']['leave'] = array(
);
/*$ModuleField['relationship']['leave']['name'] = __('Leave');
$ModuleField['relationship']['leave']['block']['leave'] = array(
	'title'	=>__('Leave / holidays for this employee'),
	'type'	=>'listview_box',
	'css'	=>'width:70%;margin-top:0;float:left;',
	'height' => '200',
	'add'	=> __('Add new line'),
	'reltb'		=> 'tb_contact@leave',//tb@option
	'delete' => '1',
	'field'=> array(
				'purpose' => array(
					'name' 		=>  __('Purpose'),
					'width' => '6',
					'type' => 'select',
					'droplist' => 'company_type',
					'edit'	=> '1',
				),
				'used' => array(
					'name' 		=>  __('Used'),
					'width' => '8',
					'type'	=> 'text',
					'edit'	=> '1',
				),
				'from_date' => array(
					'name' 		=>  __('Date form'),
					'width' => '8',
					'type'	=> 'date',
					'edit'	=> '1',
				),
				'to_date' => array(
					'name' 		=>  __('Date to'),
					'width' => '15',
					'type'	=> 'date',
					'edit'	=> '0',
				),
				'clash' => array(
					'name' 		=>  __('Clash'),
					'width' => '7',
					'type'	=> 'text',
				),
				'status' => array(
					'name' 		=>  __('Status'),
					'width' => '8',
					'type'	=> 'text',
					'edit'	=> '1',
				),
				'details' => array(
					'name' 		=>  __('Details'),
					'width' => '8',
					'type'	=> 'text',
					'edit'	=> '1',
				),
				'dontdeduct' => array(
					'name' 		=>  __('Don\'t deduct'),
					'width' => '8',
					'type'	=> 'checkbox',
					'edit'	=> '1',
				),
			),
);
$ModuleField['relationship']['leave']['block']['accummulated'] = array(
	'title'	=>__('Days accummulated'),
	'type'	=>'listview_box',
	'css'	=>'width:29%;margin-top:0;float:right;',
	'height' => '200',
	'add'	=> __('Add new line'),
	'reltb'		=> 'tb_basic@review',//tb@option
	'delete' => '1',
	'field'=> array(
				'per_month' => array(
					'name' 		=>  __('Per month'),
					'width' => '20',
					'type' => 'text',
					'droplist' => 'company_type',
					'edit'	=> '1',
				),
				'start' => array(
					'name' 		=>  __('Start'),
					'width' => '25',
					'type'	=> 'date',
					'edit'	=> '1',
				),
				'end' => array(
					'name' 		=>  __('End'),
					'width' => '25',
					'type'	=> 'date',
					'edit'	=> '1',
				),
				'total' => array(
					'name' 		=>  __('Total'),
					'width' => '20',
					'type'	=> 'text',
					'edit'	=> '0',
				),
			),
);*/


//====== working =======//
/*$ModuleField['relationship']['working']['name'] = __('Working & Holidays');
$ModuleField['relationship']['working']['block']['working_hour'] = array(
	'title'	=>__('Expenses for this employee'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;float:left;',
	'height' => '200',
	'reltb'		=> 'tb_basic@review',//tb@option
	'field'=> array(
				'day' => array(
					'name' 		=>  __('DAY'),
					'width' => '20',
					'type' => 'text',
				),
				'time1' => array(
					'name' 		=>  __('Time 1'),
					'width' => '22',
					'type'	=> 'time',
					'edit'	=> '1',
				),
				'time2' => array(
					'name' 		=>  __('Time 2'),
					'width' => '22',
					'type'	=> 'time34',
					'edit'	=> '1',
				),
				'time3' => array(
					'name' 		=>  __('Time 3'),
					'width' => '22',
					'type'	=> 'time56',
					'edit'	=> '0',
				),
			),
);*/

$ModuleField['relationship']['workings_holidays']['name'] = __('Working & Holidays');
$ModuleField['relationship']['workings_holidays']['block']['profile'] = array(
);

//====== personal =======//
$ModuleField['relationship']['user_refs']['name'] = __('User Prefs');
$ModuleField['relationship']['user_refs']['block']['profile'] = array(
);

//====== Product =======//
$ModuleField['relationship']['product']['name'] =  __('Product');
//Premission list data
$ModuleField['relationship']['product']['block']['pricing_category'] = array(
    'title' => 'Pricing category',
    'css' => 'width:16%;float:left;',
    'height' => '200',
    'reltb' => 'tb_company@3',
    'type' => 'editview_box',
    'field' => array(
		'category' => array(
			'name' => __('Use category'),
			'type' => 'select',
			'width'		=> '5',
			'droplist' => 'custom_field',
			'not_custom'=>'1',
		),
		'discount' => array(
			'name' => __('Discount %'),
			'type' => 'text',
			'css'=>'text-align:left;',
			'lock'=>'1',
			'droplist' => 'custom_field',
			'default' => '',
		),
		'non' => array(
			'name' => __(''),
			'type' => 'text',
			'css'=>'text-align:left;',
			'droplist' => 'enquirynote_type',
			'default' => 'Note: Discount is applied to
			all products and services from the Products module. Specific pricing overrides category pricing Discount is not applied to specific pricing',
		),
    ),
);
$ModuleField['relationship']['product']['block']['product'] = array(
	'title'	=>__('Specific pricing for this contact'),
	'type'	=>'listview_box',
	'link' => array('w' => '1', 'cls' => 'products','field'=>'_id'),
	'css'	=>'width:51%;margin-left:1%',
	'height' => '215',
	'add'	=> __('Add new Product'),
	'field'=> array(
				'_id' => array(
		            'name' => __('ID'),
					'type'=>'id',
				),
				'code' => array(
					'name' 		=>  __('No'),
					'width'		=> '5',
				),
				'name' => array(
					'name' 		=>  __('Name'),
					'width'		=> '20',
				),
				'range' => array(
					'name' 		=>  __('Range'),
					'width'		=> '10',
				),
				'unit_price' => array(
					'name' 		=>  __('Unit price'),
					'width'		=> '10',
				),
			),
);
$ModuleField['relationship']['product']['block']['units'] = array(
	'title'	=>__('Units / assets for this contact'),
	'type'	=>'listview_box',
	'css'	=>'width:30%;margin-left:1%;',
	'height' => '215',
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



//====== Quotes =======//
$ModuleField['relationship']['quote']['name'] =  __('Quotes');
//Option list data
$ModuleField['relationship']['quote']['block']['quote'] = array(
	'title'	=>__('Quotations for this enquiry'),
	'type'	=>'listview_box',
	'link' => array('w' => '1', 'cls' => 'quotations','field'=>'_id'),
	'css'	=>'width:100%;margin-top:0;',
	'height' => '220',
	'add'	=> __('Add option'),
	'custom_box_bottom' => '1',
	'footlink' => array(
        'label' => __('Click to view full details')
    ),

	'field'=> array(
				'_id' => array(
		            'name' => __('ID'),
					'type'=>'id',
				),
				'code' => array(
					'name' 		=>  __('Ref no'),
					'width'		=> '5',
				),
				'quotation_type' => array(
					'name' 		=>  __('Type'),
					'width'		=> '10',
				),
				'quotation_date' => array(
					'name' 		=>  __('Date'),
					'type' 		=> 'date',
					'width'		=> '10',
				),
				'payment_due_date' => array(
					'name' 		=>  __('Due date'),
					'type' 		=> 'date',
					'width'		=> '10',
				),
				'quotation_status' => array(
					'name' 		=>  __('Status'),
					'width'		=> '10',
				),
				'our_rep' => array(
					'name' 		=>  __('Our Rep'),
					'width'		=> '10',
				),
				'sum_tax' => array(
					'name' 		=>  __('Tax'),
					'width'		=> '10',
				),
				'heading' => array(
					'name' 		=>  __('Heading'),
					'width'		=> '20',
				),

			),
);



//====== ORDERS =======//
$ModuleField['relationship']['order']['name'] = __('Orders');
//Sales orders
$ModuleField['relationship']['order']['block']['order'] = array(
    'title' => 'Sales orders for this item',
    'css' => 'width:100%;',
    'height' => '220',
    'custom_box_bottom' => '1',
    'footlink' => array(
        'label' => __('Click to view full details')
    ),
    'link' => array('w' => '1', 'cls' => 'salesorders','field'=>'_id'),
    'reltb' => 'tb_salesorder@products', //tb@option
   /* 'total' => 'Total (not inc. cancelled)',
    'total_css' => 'margin-right:3%;',*/
    'type' => 'listview_box',
    'add'	=> __('Add option'),
    'field' => array(
    		'_id' => array(
		            'name' => __('ID'),
					'type'=>'id',
				),
	        'code' => array(
	            'name' => __('Ref no'),
	            'width' => '6',
	        ),
	        'salesorder_date' => array(
	            'name' => __('Date in'),
	            'width' => '10',
	            'type' => 'date',
	            'align' => 'center',
	        ),
	        'payment_due_date' => array(
	            'name' => __('Due date'),
	            'width' => '10',
	            'type' => 'date',
	            'align' => 'center',
	        ),
	        'status' => array(
	            'name' => __('Status'),
	            'width' => '8',
	            'type' => 'idlink',
	            'relid' => 'contact_id',
	            'cls' => 'contacts',
	        ),
	        'our_rep' => array(
	            'name' => __('Assign to'),
	            'width' => '8',
	            'type' => 'idlink',
	            'relid' => 'contact_id',
	            'cls' => 'contacts',
	        ),
	        'our_rep_id' => array(
	            'name' => __('Assign to ID'),
	            'type' => 'id',
	        ),
	        'heading' => array(
	            'name' => __('Heading'),
	            'width' => '10',
	        ),
	        'comments' => array(
	            'name' => __('Comments'),
	            'width' => '30',
	        ),
    ),
);



//====== SHIPPING =======//
$ModuleField['relationship']['shipping']['name'] = __('Shipping');
$ModuleField['relationship']['shipping']['block']['ship'] = array(
    'title' => 'Shipping / dispatches for this item',
    'css'   => 'width: 100%; margin-bottom:1%;',
    'height' => '220',
    'footlink' => array(
        'label' => __('Click to view full details'),
        ),
    'link' => array('w' => '1', 'cls' => 'shippings','field'=>'_id'),
    'reltb' => 'tb_product@ship',
    'type' => 'listview_box',
    'add'	=> __('Add option'),
    'field' => array(
	        'code' => array(
	            'name' => __('Ref no'),
	            'width' => '3',
	            'align' => 'left',
	            'type' => 'text',
	        ),
	        'shipping_type' => array(
	            'name' => __('Type'),
	            'width' => '10',
	            'align' => 'left',
	            'align' => 'center',
	            'type' => 'text',
	        ),
	        'return_status' => array(
	            'name' => __('Return'),
	            'width' => '10',
	            'align' => 'center',
	            'type' => 'checkbox',
	        ),
	        'shipping_date' => array(
	            'name' => __('Date received'),
	            'width' => '10',
	            'align' => 'left',
	            'type' => 'date',
	        ),
	        'shipping_status' => array(
	            'name' => __('Status'),
	            'width' => '8',
	            'align' => 'left',
	            'type' => 'text',
	        ),
	        'our_rep' => array(
	            'name' => __('Our rep'),
	            'width' => '15',
	            'align' => 'left',
	            'type' => 'text',
	        ),
	        'shipper' => array(
	            'name' => __('Shipper'),
	            'width' => '14',
	            'align' => 'left',
	            'type' => 'text',
	        ),
	        'tracking_no' => array(
	            'name' => __('Tracking no'),
	            'width' => '17',
	            'align' => 'left',
	            'type' => 'text',
	        ),
    ),
);



//====== INVOICE =======//
$ModuleField['relationship']['invoice']['name'] = __('Account');
//Sales orders
$ModuleField['relationship']['invoice']['block']['sale_invoice'] = array(
    'title' => 'Sales invoices for this item',
    'css' => 'width:68%;',
    'height' => '220',
    'footlink' => array(
        'label' => __('Click to view full details'),
    ),
    'link' => array('w' => '1', 'cls' => 'salesinvoices','field'=>'_id'),
    'reltb' => 'tb_salesinvoice@products', //tb@option
    'type' => 'listview_box',
    'add'	=> __('Add option'),
    //'custom_box_bottom' => '1',
    'field' => array(
	        'code' => array(
	            'name' => __('Ref no'),
	            'width' => '3',
	            'align' => 'center',
	            'type' => 'text',
	        ),
	        'invoice_type' => array(
	            'name' => __('Type'),
	            'width' => '5',
	            'type' => 'text',
	        ),
	        'invoice_date' => array(
	            'name' => __('Date'),
	            'width' => '10',
	            'type' => 'date',
	        ),
	         'invoice_status' => array(
	            'name' => __('Status'),
	            'width' => '8',
	            'type' => 'select',
	            'droplist' => 'salesinvoices_status',
	        ),
	        'our_rep' => array(
	            'name' => __('Our rep'),
	            'width' => '12',
	            'relid' => 'our_rep_id',
	            'cls' => 'contacts',
	        ),
	        'sum_sub_total' => array(
	            'name' => __('Ex. Tax total'),
	            'width' => '12',
	        ),
	        'other_comment' => array(
	            'name' => __('Comments'),
	            'type' => 'text',
	            'width' => '28',
	        ),
    ),
);
$ModuleField['relationship']['invoice']['block']['overall'] = array(
	'title' => 'Overall job totals',
    'css' => 'width:31%;float:right',
    'height' => '220',
    'reltb' => 'tb_salesinvoice@products', //tb@option
    'type' => 'editview_box',
    'add'	=> __('Add option'),
    'field' => array(
    	   'purchase' => array(
		            'name' => __('Purchase order'),
		            'width' => '2',
		            'type' => 'text',
		            'default'	=> '0',
		            'edit' => '1'
	        ),
    	    'sales' => array(
		            'name' => __('Sales order costs'),
		            'width' => '2',
		            'type' => 'text',
		            'edit' => '1',
		            'default'	=> '0',
	        ),
	        'expenses' => array(
					'name' 		=>  __('Expenses'),
					'type' => 'text',
					'width'		=> '10',
					'default'	=> '0',
					'edit'		=> '1',
			),
			'time_costs' => array(
					'name' 		=>  __('Employee time costs'),
					'width'		=> '10',
					'type' => 'text',
					'default'	=> '0',
					'edit'		=> '1',

			),
			'commission' => array(
					'name' 		=>  __('Commission'),
					'width'		=> '10',
					'type' => 'text',
					'default'	=> '0',
					'edit'		=> '1',

			),
			'total_costs' => array(
					'name' 		=>  __('Total costs'),
					'width'		=> '10',
					'type' => 'text',
					'default'	=> '0',
					'edit'		=> '1',

			),
			'sales_invoices' => array(
					'name' 		=>  __('Sales invoices'),
					'width'		=> '10',
					'type' => 'text',
					'default'	=> '0',
					'edit'		=> '1',

			),
			'Profit' => array(
					'name' 		=>  __('Profit'),
					'width'		=> '10',
					'type' => 'text',
					'default'	=> '0',
					'edit'		=> '1',

			),
			'Margin' => array(
					'name' 		=>  __('Margin'),
					'width'		=> '10',
					'type' => 'text',
					'default'	=> '0',
					'edit'		=> '1',

			),
    ),
);


//====== DOCUMENTS =======//
// Dung chung ham document cua anh nam
$ModuleField['relationship']['documents']['name'] =  __('Documents');
// end document


//====== Premission =======//
$ModuleField['relationship']['other']['name'] =  __('Other');
$ModuleField['relationship']['other']['block']['other_detail'] = array(
	'title'	=>__('Other details'),
	'type'	=>'listview_box',
	'css'	=>'width:48%;margin-top:0;',
	'height' => '260',
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
	'title'	=>__('Group linked to this company'),
	'type'	=>'listview_box',
	'css'	=>'width:30%;margin-left:1%;',
	'height' => '260',
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

$ModuleField['relationship']['other']['block']['3'] = array(
    'title' => 'Profile',
    'css' => 'width:20%;margin-left:1%;float:left;',
    'height' => '250',
    'reltb' => 'tb_company@3',
    'type' => 'editview_box',
    'field' => array(
		'profile_type' => array(
			'name' => __('Custom 1'),
			'type' => 'select',
			'droplist' => 'custom_field',
			'default' => '',
			'not_custom'=>'1',
			'edit'		=>'1',
		),
		'category' => array(
			'name' => __('Custom 2'),
			'type' => 'select',
			'css'=>'text-align:left;',
			'lock'=>'1',
			'droplist' => 'custom_field',
			'default' => '',
		),
		'rating' => array(
			'name' => __('Custom 3'),
			'type' => 'select',
			'css'=>'text-align:left;',
			'droplist' => 'enquirynote_type',
			'default' => '',
		),
		'depart'	=>array(
			'name' 		=> __('Department'),
			'type' 		=> 'select',
			'css'=>'text-align:left;',
			'droplist' => 'enquirynote_type',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'8',
							'css'	=>	'width:8%;',
						),
			),
		'off_report_working_hour'	=>array(
			'name' 		=> __("Working Hour "),
			'type' 		=> 'checkbox',
			'css'		=> 'text-align:left;',
			'default'	=> '0',
			'label' => "&nbsp;Don't report in Working Hour",
			),
		'anvy_support'	=>array(
			'name' 		=> __("Support "),
			'type' 		=> 'checkbox',
			'css'		=> 'text-align:left;',
			'default'	=> '0',
			'label' => "&nbsp;Support in anvyonline.com",
			),
    ),
);




$ContactField = $ModuleField;




//============ *** RELATIONSHIP *** =============//

