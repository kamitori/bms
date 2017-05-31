<?php if($this->Common->check_permission($controller.'_@_entry_@_delete',$arr_permission)): ?>
<br><?php $n=0; foreach ($arr_list as $value) { if($n%2>0) $nclass=" bg1"; else $nclass=" bg2";?>

	<ul class="ul_mag clear <?php echo $nclass;?>">

		<li class="hg_padd" style="width:.5%" onclick=" window.location.assign('<?php echo URL.'/'.$controller; ?>/entry/<?php if(isset($value['_id'])) echo $value['_id']; ?>');">
			<span class="icon_emp"></span>
		</li>

		<?php foreach ($list_field as $ks => $vls){?>
			<li class="hg_padd" style=" <?php if(isset($vls['css'])) echo $vls['css']; ?><?php if(isset($vls['align'])) echo 'text-align:'.$vls['align']; ?>">
				<?php
					// hien thi noi dung theo loai field
					if(isset($value[$ks]) && $value[$ks]!=''){
						if($arr_set['type'][$ks]=='select' && isset($opt_select[$ks][$value[$ks]]))
							echo $opt_select[$ks][$value[$ks]];

						else if(($arr_set['type'][$ks]=='price' || $arr_set['type'][$ks]=='price_notchange'))
							echo number_format((float)$value[$ks],2);

						else if($arr_set['type'][$ks]=='percent')
							echo number_format((float)$value[$ks]*100,2).'%';

						else if($arr_set['type'][$ks]=='date' && is_object($value[$ks]))
							echo date('d M, Y',$value[$ks]->sec);

						else if($arr_set['type'][$ks]=='checkbox')
							echo '<div class="middle_check"><input type="checkbox" disabled="disabled" checked="checked"></div>';


						else if($arr_set['type'][$ks]=='relationship'){
							$ids_key = $arr_set['id'][$ks];
							$ids_value = $value[$ids_key];
							$coclass = $arr_set['cls'][$ks].'_class';
							if(!is_array($value[$ks]) && $ids_value!='')
								echo '<a style="text-decoration:none;" href="'.URL.'/'.$arr_set['cls'][$ks].'/entry/'.$ids_value.'">'.$$coclass->find_name($ids_value,$syncname[$ks]).'</a>';
							else if(!is_array($value[$ks]))
								echo $value[$ks];

						}

						else if(isset($value[$ks]) && is_array($value[$ks]))
							echo 'Data array';

						else if(isset($value[$ks]))
							echo $value[$ks];
					}
				?>
			</li>
		<?php } ?>

		<li class="hg_padd bor_mt" style="width:.5%">
			<div class="middle_check">
				<a title="Delete link" style="cursor:pointer;" class="delete_link" onclick="delete_record('del_<?php if(isset($value['_id'])) echo $value['_id']; ?>');" id="del_<?php if(isset($value['_id'])) echo $value['_id']; ?>">
					<span class="icon_remove2"></span>
				</a>
			</div>
		</li>

	</ul>
<?php $n++; } ?>
<?php else: ?>
<br><?php $n=0; foreach ($arr_list as $value) { if($n%2>0) $nclass=" bg1"; else $nclass=" bg2";?>

	<ul class="ul_mag clear <?php echo $nclass;?>">

		<li class="hg_padd" style="width:.5%" onclick=" window.location.assign('<?php echo URL.'/'.$controller; ?>/entry/<?php if(isset($value['_id'])) echo $value['_id']; ?>');">
			<span class="icon_emp"></span>
		</li>

		<?php foreach ($list_field as $ks => $vls){?>
			<li class="hg_padd" style=" <?php if(isset($vls['css'])) echo $vls['css']; ?><?php if(isset($vls['align'])) echo 'text-align:'.$vls['align']; ?>">
				<?php
					// hien thi noi dung theo loai field
					if(isset($value[$ks]) && $value[$ks]!=''){
						if($arr_set['type'][$ks]=='select' && isset($opt_select[$ks][$value[$ks]]))
							echo $opt_select[$ks][$value[$ks]];

						else if(($arr_set['type'][$ks]=='price' || $arr_set['type'][$ks]=='price_notchange'))
							echo number_format((float)$value[$ks],2);

						else if($arr_set['type'][$ks]=='percent')
							echo number_format((float)$value[$ks]*100,2).'%';

						else if($arr_set['type'][$ks]=='date' && is_object($value[$ks]))
							echo date('d M, Y',$value[$ks]->sec);

						else if($arr_set['type'][$ks]=='checkbox')
							echo '<div class="middle_check"><input type="checkbox" disabled="disabled" checked="checked"></div>';


						else if($arr_set['type'][$ks]=='relationship'){
							$ids_key = $arr_set['id'][$ks];
							$ids_value = $value[$ids_key];
							$coclass = $arr_set['cls'][$ks].'_class';
							if(!is_array($value[$ks]) && $ids_value!='')
								echo '<a style="text-decoration:none;" href="'.URL.'/'.$arr_set['cls'][$ks].'/entry/'.$ids_value.'">'.$$coclass->find_name($ids_value,$syncname[$ks]).'</a>';
							else if(!is_array($value[$ks]))
								echo $value[$ks];

						}

						else if(isset($value[$ks]) && is_array($value[$ks]))
							echo 'Data array';

						else if(isset($value[$ks]))
							echo $value[$ks];
					}
				?>
			</li>
		<?php } ?>
	</ul>
<?php $n++; } ?>

<?php endif; ?>
<?php echo $this->element('popup/pagination_lists'); ?>