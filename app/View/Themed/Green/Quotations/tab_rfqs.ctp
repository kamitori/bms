<?php
	if($action=='rfqs_list')
	  $option = array(
						'add_rfqs' =>'&nbsp; &nbsp; New RFQ &nbsp; &nbsp;',
						'print_rfqs'=>'&nbsp; &nbsp; Print RFQ list &nbsp; &nbsp;',
					);
	else if($action=='rfqs_entry')
		$option = array(
						'create_po' =>'&nbsp; &nbsp; Create PO &nbsp; &nbsp;',
						'email_rfqs'=>'&nbsp; &nbsp; Email RFQ &nbsp; &nbsp;',
						'print_rfqs'=>'&nbsp; &nbsp; Print RFQ &nbsp; &nbsp;',
					);
	if(!$this->Common->check_permission($controller.'_@_entry_@_edit',$arr_permission))
		unset($option['add_rfqs']);

?>

<div class="bg_menu">
    <ul class="menu_control float_left">
    	<?php foreach($option as $ks=>$vls){
				$link = 'style=" cursor:pointer;"';
		?>
        	<li>
            	<a <?php echo $link; ?> class="entry_menu_<?php echo $ks;?> <?php if($ks==$action) echo 'active';?>">
            		<?php echo $vls;?>
                </a>
            </li>
        <?php }?>
    </ul>
</div>