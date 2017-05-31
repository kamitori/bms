<?php if(!isset($arr_vls['xempty'][$viewkeys])){ ?>
<?php
	if(isset($arr_view_st['label']))
		$label = $arr_view_st['label'];
	else
		$label = 'View costing';

	if(isset($arr_view_st['url']))
		$url = URL.'/'.$arr_view_st['url'];
	else
		$url = URL.'/'.$controller;

	if(isset($arr_view_st['id']))
		$id = $arr_view_st['id'];

	if(isset($arr_vls[$id]) && $arr_vls[$id]!='')
		$ids = $arr_vls[$id];
	else if(isset($icon_link_id))
		$ids = $icon_link_id;
	else
		$ids = 'custom';

	$lock = 0;
	if(!isset($arr_view_st['edit']) || $arr_view_st['edit']!='1' )
	   $lock = 1;
?>


    <a title="<?php echo $label;?>" href="javascript:void(0)" <?php if(!$lock){ ?> rel="<?php echo $url.'/'.$ids.'/'.$arr_vls['_id'];?>" class="icon_print_list costings_popup" style="cursor:pointer;" <?php } ?> >
        <span class="icon_emp" <?php if($lock) echo 'style="cursor:default;" '; ?>></span>
    </a>
<?php } ?>