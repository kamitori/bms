<?php
$i = 1;
foreach ($arr_job as $key => $value) {
	$i = 3- $i;
?>
<ul class="ul_mag clear bg<?php echo $i; ?>" onclick="dashboard_run('jobs', '<?php echo $value['_id']; ?>')" title="View detail">
	<li class="hg_padd center_txt" style="width:27%">
		<?php if(isset($value['work_end']) && is_object($value['work_end']))
				echo $this->Common->format_date( $value['work_end']->sec, false); ?>
	</li>
	<li class="hg_padd " style="width:41%"><?php if(isset($value['company_name']))echo $value['company_name']; ?></li>
	<li class="hg_padd center_txt" style="width:12%"><?php if(isset($value['no']))echo $value['no']; ?></li>
	<li class="hg_padd center_txt no_border" style="width:14.6%">
		<?php
			if(isset($value['work_end'])){
				if( ($value['work_end']->sec + 86400) < strtotime('now')){
					echo '<span class="Late">X</span>';
				}
			}
		?>
	</li>
</ul>
<?php } ?>