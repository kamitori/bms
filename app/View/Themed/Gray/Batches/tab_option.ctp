<?php 
	$option_entry = array(
						'add' =>'New',
						'delete'=>'Delete',
						'entry_search' =>'Find',
						'find_all'=>'Find all',
						//'omit' =>'Omit',
						//'sorts'=>'Sort',
						//'prints'=>'Print',
					);
	  $option_search_entry = array(
						'add' =>'New',
						//'delete'=>'Delete',
						'entry_search' =>'Find',
						'continues' =>'Continue',
						'cancel' =>'Cancel',
						//'omit'=>'Omit',
					);
					
					
      $actionlist_entry = array(
						'entry' =>'Entry',
						'lists'=>'List',
						'options' =>'Options',
					);
      $actionlist_search_entry = array(
						'entry' =>'Entry',
						'lists'=>'List',
					);
					
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
				$link = "onclick=\"if(!confirm('Are you sure?')) return false;\" href=\"".URL."/".$controller."/".$ks."/".$iditem."\"";
			else if($ks=='continues')
				$link = 'onclick="search_entry();" style=" cursor:pointer;"';
			else
				$link = 'href="'.URL.'/'.$controller.'/'.$ks.'"';
		?>
        	<li>
            	<a <?php echo $link; ?> class="entry_menu_<?php echo $ks;?> <?php if($ks==$action) echo 'active';?>">
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