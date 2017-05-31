<style type="text/css">
.input_switch_popup{
	width:99%;padding: 3px 10px;border-radius: 9px;
}
.input_switch_popup:hover{
	color:#942525;
}
</style>

<?php if( $key == '_tab_tasks' ){ ?>
<div style="width: 99%;">
	<div class="float_right">
		<input title="Switch to choose contact" onclick="$('#window_popup_contacts_tab_tasks').data('kendoWindow').open().center();" class="input_switch_popup btn_pur" type="button" value="Contacts">
	</div>
</div>
<?php } ?>

<div style="clear:both"></div>

<div class="block_dent2 container_same_category" style="width:100%; margin: 0 auto; height:400px;" id="list_view_company">
	<table class="jt_tb" id="Form_add" style="width:100%; font-size:12px;">
		<thead style="position: fixed;">
			<tr>
				<th style="width: 404px"><?php echo translate('STT'); ?></th>
				<th style="width: 404px"><?php echo translate('Name'); ?></th>
			</tr>
		</thead>

		<tbody>
			<tr>
				<th style="width: 181px">&nspb;</th>
				<th style="width: 181px">&nspb;</th>
			</tr>
			<?php
			$i=0; $STT = 1;
			foreach ($arr_equipment as $value) {
				$i = 1 - $i;
			?>
			<tr class="jt_line_<?php if($i == 1){ ?>black<?php }else{ ?>light<?php } ?>" onclick="after_choose_equipments<?php if(substr($key,0,1) == '_')echo $key; ?>('<?php echo $value['_id']; ?>','<?php echo $value['name']; ?>', '<?php echo $key; ?>')">
				<td align="center"><?php echo $STT++; ?></td>
				<td align="left"><?php echo $value['name']; ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>

<script type="text/javascript">
// function popup_contact_switch( type ){
// 	if( type == "contacts" ){
// 		$("#popup_contacts<?php echo $key; ?>").show();
// 		$("#popup_assets<?php echo $key; ?>").hide();
// 	}else{
// 		$("#popup_contacts<?php echo $key; ?>").hide();
// 		$("#popup_assets<?php echo $key; ?>").show();
// 	}
// 	$('#window_popup_contacts_assets').data('kendoWindow').center();
// }
</script>