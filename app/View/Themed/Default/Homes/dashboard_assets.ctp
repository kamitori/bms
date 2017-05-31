<?php
$i = 1; $data = eval('return $arr_asset_'. $asset_key . ';');
foreach ($data as $key => $value) {
	$i = 3- $i;
?>
<ul class="ul_mag clear bg<?php echo $i; ?>" onclick="dashboard_run('tasks', '<?php echo $value['_id']; ?>')" title="View detail">
	<li class="hg_padd center_txt" style="width:25%">
		<?php if(isset($value['work_end']) && is_object($value['work_end']))
				echo $this->Common->format_date( $value['work_end']->sec, false); ?>
	</li>
	<li class="hg_padd center_txt" style="width:19%"><?php if(isset($value['work_end']))echo date('H:i', $value['work_end']->sec); ?></li>
	<li class="hg_padd" style="width:39%">
		<?php $color = '';
			if( isset($value['our_rep_type']) ){
				if( $value['our_rep_type'] == 'contacts' ){
					if( isset($color_status[$value['status_id']]) )
						$color = $color_status[$value['status_id']];
				}else if( $value['our_rep_type'] == 'assets' ){
					if( isset($color_status[$value['our_rep_id'].'_'.$value['status_id']]) )
						$color = $color_status[$value['our_rep_id'].'_'.$value['status_id']];
				}

			}
		?>
		<span style="color:<?php echo $color; ?>"><?php echo isset($value['salesorder_no']) ? $value['salesorder_no'] : 'x' ?>-<?php echo $value['name']; ?>-<?php
			if($value['company_name'] != ""){
				echo $value['company_name'];
			}else{
				echo 'Empty Company';
			}
		?></span>
	</li>
	<li class="hg_padd center_txt no_border" style="width:11%">
		<?php
			if(isset($value['work_end'])){
				if( $value['work_end']->sec < strtotime('now')){
					echo '<span class="Late">X</span>';
				}
			}
		?>
	</li>
</ul>
<?php } ?>

