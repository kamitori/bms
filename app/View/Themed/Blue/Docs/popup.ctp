<div class="block_dent2" style="max-width:1000px; margin: 0 auto; height:400px;" id="list_view_company">
	<table class="jt_tb" id="Form_add" style="width:100%; font-size:12px;">
		<thead>
			<tr>
				<th><?php echo translate('Document / file name'); ?></th>
				<th><?php echo translate('Category'); ?></th>
				<th><?php echo translate('Ext'); ?></th>
				<th><?php echo translate('Type'); ?></th>
				<th><?php echo translate('Description'); ?></th>
				<th><?php echo translate('Create from'); ?></th>
				<th><?php echo translate('Module detail'); ?></th>
			</tr>
		</thead>

		<?php
		$i=0;
		foreach ($arr_doc as $value) {
			$i = 1 - $i;
		?>
		<tr class="jt_line_<?php if($i == 1){ ?>black<?php }else{ ?>light<?php } ?>" onclick="after_choose_docs<?php if(substr($key,0,1) == '_')echo $key; ?>('<?php echo $value['_id']; ?>','<?php echo $value['name']; ?>', '<?php echo $key; ?>')" id="Doc_<?php echo $value['_id']; ?>">
			<td align="left"><?php echo $value['name']; ?></td>
			<td align="center"><?php if(isset($value['category']) && isset($arr_docs_category[$value['category']]))echo $arr_docs_category[$value['category']]; ?></td>
			<td align="center"><?php echo $value['ext']; ?></td>
			<td align=""><?php echo substr($value['type'], 0, 24); ?></td>
			<td align=""><?php if(isset($value['description']))echo $value['description']; ?></td>
			<td align=""><?php if(isset($value['create_by_module']))echo $value['create_by_module']; ?></td>
			<td align=""><?php if(isset($value['module_detail']))echo $value['module_detail']; ?></td>
		</tr>
		<?php } ?>
	</table>
</div>