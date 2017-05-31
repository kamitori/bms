<?php
	$option_entry = array(
						'add' =>translate('New'),
						'delete'=>translate('Delete'),
						'entry_search' =>translate('Find'),
						'find_all'=>translate('Find all'),
						//'omit' =>'Omit',
						//'sorts'=>'Sort',
						//'prints'=>'Print',
					);
	if( $controller == 'products' ) {
		$option_entry['export_list'] = translate('Export List');
	}
	$option_entry['support'] = translate('Support');
	$option_entry['history'] = translate('History');
	  $option_search_entry = array(
						'add' =>translate('New'),
						//'delete'=>'Delete',
						'entry_search' =>translate('Find'),
						'continues' =>translate('Continue'),
						'cancel' =>translate('Cancel'),
						//'omit'=>'Omit',
					);


      $actionlist_entry = array(
						'entry' =>translate('Entry'),
						'lists'=>translate('List'),
						'options' =>translate('Options'),
					);
      $actionlist_search_entry = array(
						'entry' =>'Entry',
						'lists'=>'List',
					);
      if(!$this->Common->check_permission($controller.'_@_entry_@_add',$arr_permission))
      	unset($option_entry['add'],$option_search_entry['add']);
      if(!$this->Common->check_permission($controller.'_@_entry_@_delete',$arr_permission))
      	unset($option_entry['delete']);
      if(!$this->Common->check_permission($controller.'_@_options_@_',$arr_permission,true))
      	unset($actionlist_entry['options']);
	  if($action=='entry_search'){
	  		$option	= $option_search_entry;
			$actionlist = $actionlist_search_entry;
	  }else{
	  		$option	= $option_entry;
			$actionlist = $actionlist_entry;
	  }

?>

<div class="bg_menu">
    <ul class="menu_control float_left">
    	<?php foreach($option as $ks=>$vls){
			if($ks=='delete')
				$link = "onclick=\"confirm_delete('".URL."/".$controller."/".$ks."/".$iditem."');\" style=\"cursor:pointer;\" ";
			else if($ks=='continues')
				$link = 'onclick="search_entry();" style=" cursor:pointer;"';
			else if($ks=='add' && !in_array($controller, array('shippings','communications')))
				$link = 'onclick="confirms_add();" style=" cursor:pointer;"';
			else
				$link = 'href="'.URL.'/'.$controller.'/'.$ks.'"';
		?>
        	<li>
            	<a <?php echo $link;?>  class="<?php if($ks!='support'&&$ks!='history') {?>entry_menu_<?php }else{ ?>icon <?php } ?><?php echo $ks;?> <?php if($ks==$action) echo 'active';?>">
            		<?php echo $vls;?>
                </a>
            </li>
        <?php }?>
    </ul>
    <ul class="menu_control2 float_right">
    	<?php foreach($actionlist as $ks=>$vls){?>
        	<li>
            	<a href="<?php echo URL.'/'.$controller.'/'.$ks; ?>" class="<?php if($action == $ks) echo 'active';?>">
					<?php echo $vls;?>
                 </a>
            </li>
         <?php }?>
    </ul>
</div>
<script type="text/javascript">
	function confirms_add(){
		<?php if(isset($_SESSION['arr_user']['system_admin']) && $controller == 'companies' ){ ?>
		confirms3("Message","Do you want to create a new System Company or Normal Company?", ["System", "Normal", ""],
		function(){//Yes
 			window.location.replace("<?php echo URL.'/'.$controller.'/add/system'; ?>");
 		},function(){//Yes
 			window.location.replace("<?php echo URL.'/'.$controller.'/add'; ?>");
 		}
 		,function(){
 			return false;
 		});
		<?php } else { ?>
		confirms("Message","Are you sure you want to create a new <?php echo ucfirst($model); ?>?"
	 		,function(){//Yes
	 			window.location.replace("<?php echo URL.'/'.$controller.'/add'; ?>");
	 		}
	 		,function(){
	 			return false;
	 		});
		<?php } ?>
	}
</script>