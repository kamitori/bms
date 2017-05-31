<div class="block_dent2" style="max-width:1000px; margin: 0 auto; height:400px;" id="list_view_company">

	<table class="jt_tb" id="Form_add" style="width:100%; font-size:12px;">
		<thead>
			<tr>
				<th style="width: 64px;"><?php echo translate('Name'); ?></th>
				<th><?php echo translate('Address line 1'); ?></th>
				<th><?php echo translate('Address line 2'); ?></th>
				<th><?php echo translate('Address line 3'); ?></th>
				<th><?php echo translate('Town / City'); ?></th>
				<th><?php echo translate('Province / State'); ?></th>
				<th style="width: 80px;"><?php echo translate('Zip Postcode'); ?></th>
				<th style="width: 44px;"><?php echo translate('Country'); ?></th>
			</tr>
		</thead>

		<?php
		if( isset($arr_company) ){
			$i=0;
			foreach ($arr_company['addresses'] as $k_addr => $value) {

				if( $value['deleted'] )continue;
				$i = 1 - $i;
			?>
			<tr class="jt_line_<?php if($i == 1){ ?>black<?php }else{ ?>light<?php } ?>" onclick="after_choose_addresses<?php if(substr($key,0,1) == '_')echo $key; ?>('<?php echo $k_addr; ?>','company', '<?php echo $key; ?>')">
				<td><?php echo $value['name']; ?></td>
				<td><?php echo $value['address_1']; ?></td>
				<td><?php echo $value['address_2']; ?></td>
				<td><?php echo $value['address_3']; ?></td>
				<td><?php echo $value['town_city']; ?></td>
				<td><?php echo $value['province_state']; ?></td>
				<td><?php echo $value['zip_postcode']; ?></td>
				<td><?php echo $value['country']; ?>
					<input type="hidden" id="window_popup_addresses_company_name_<?php echo $k_addr.$key; ?>" value="<?php echo $value['name']; ?>">
					<input type="hidden" id="window_popup_addresses_company_address_1_<?php echo $k_addr.$key; ?>" value="<?php echo $value['address_1']; ?>">
					<input type="hidden" id="window_popup_addresses_company_address_2_<?php echo $k_addr.$key; ?>" value="<?php echo $value['address_2']; ?>">
					<input type="hidden" id="window_popup_addresses_company_address_3_<?php echo $k_addr.$key; ?>" value="<?php echo $value['address_3']; ?>">
					<input type="hidden" id="window_popup_addresses_company_town_city_<?php echo $k_addr.$key; ?>" value="<?php echo $value['town_city']; ?>">
					<input type="hidden" id="window_popup_addresses_company_province_state_<?php echo $k_addr.$key; ?>" value="<?php echo $value['province_state']; ?>">
					<input type="hidden" id="window_popup_addresses_company_province_state_id_<?php echo $k_addr.$key; ?>" value="<?php echo $value['province_state_id']; ?>">
					<input type="hidden" id="window_popup_addresses_company_zip_postcode_<?php echo $k_addr.$key; ?>" value="<?php echo $value['zip_postcode']; ?>">
					<input type="hidden" id="window_popup_addresses_company_country_<?php echo $k_addr.$key; ?>" value="<?php echo $value['country']; ?>">
					<input type="hidden" id="window_popup_addresses_company_country_id_<?php echo $k_addr.$key; ?>" value="<?php echo $value['country_id']; ?>">
				</td>
			</tr>
			<?php } ?>
		<?php } ?>
	<!--</table>

	<div class="k-window-titlebar k-header" style="margin-top: -29px;">
		&nbsp;
		<span class="k-window-title" id="window_popup_addresses_wnd_title" style="right: 30px;">Specify contact address</span>
	</div>
	<table class="jt_tb" id="Form_add" style="width:100%; font-size:12px;">
		<thead>
			<tr>
				<th style="width: 64px;"><?php echo translate('Name'); ?></th>
				<th><?php echo translate('Address line 1'); ?></th>
				<th><?php echo translate('Address line 2'); ?></th>
				<th><?php echo translate('Address line 3'); ?></th>
				<th><?php echo translate('Town / City'); ?></th>
				<th><?php echo translate('Province / State'); ?></th>
				<th style="width: 80px;"><?php echo translate('Zip Postcode'); ?></th>
				<th style="width: 44px;"><?php echo translate('Country'); ?></th>
			</tr>
		</thead>-->

		<?php
		if( isset($arr_contact) ){
			$i=0;
			foreach ($arr_contact['addresses'] as $k_addr => $value) {

				if( $value['deleted'] )continue;
				$i = 1 - $i;
			?>
			<tr class="jt_line_<?php if($i == 1){ ?>black<?php }else{ ?>light<?php } ?>" onclick="after_choose_addresses<?php if(substr($key,0,1) == '_')echo $key; ?>('<?php echo $k_addr; ?>','contact', '<?php echo $key; ?>')">
				<td><?php echo $value['name']; ?></td>
				<td><?php echo $value['address_1']; ?></td>
				<td><?php echo $value['address_2']; ?></td>
				<td><?php echo $value['address_3']; ?></td>
				<td><?php echo $value['town_city']; ?></td>
				<td><?php echo $value['province_state']; ?></td>
				<td><?php echo $value['zip_postcode']; ?></td>
				<td><?php echo $value['country']; ?>
					<input type="hidden" id="window_popup_addresses_contact_name_<?php echo $k_addr.$key; ?>" value="<?php echo $value['name']; ?>">
					<input type="hidden" id="window_popup_addresses_contact_address_1_<?php echo $k_addr.$key; ?>" value="<?php echo $value['address_1']; ?>">
					<input type="hidden" id="window_popup_addresses_contact_address_2_<?php echo $k_addr.$key; ?>" value="<?php echo $value['address_2']; ?>">
					<input type="hidden" id="window_popup_addresses_contact_address_3_<?php echo $k_addr.$key; ?>" value="<?php echo $value['address_3']; ?>">
					<input type="hidden" id="window_popup_addresses_contact_town_city_<?php echo $k_addr.$key; ?>" value="<?php echo $value['town_city']; ?>">
					<input type="hidden" id="window_popup_addresses_contact_province_state_<?php echo $k_addr.$key; ?>" value="<?php echo $value['province_state']; ?>">
					<input type="hidden" id="window_popup_addresses_contact_province_state_id_<?php echo $k_addr.$key; ?>" value="<?php echo $value['province_state_id']; ?>">
					<input type="hidden" id="window_popup_addresses_contact_zip_postcode_<?php echo $k_addr.$key; ?>" value="<?php echo $value['zip_postcode']; ?>">
					<input type="hidden" id="window_popup_addresses_contact_country_<?php echo $k_addr.$key; ?>" value="<?php echo $value['country']; ?>">
					<input type="hidden" id="window_popup_addresses_contact_country_id_<?php echo $k_addr.$key; ?>" value="<?php echo $value['country_id']; ?>">
				</td>
			</tr>
			<?php } ?>
		<?php } ?>
	</table>

</div>