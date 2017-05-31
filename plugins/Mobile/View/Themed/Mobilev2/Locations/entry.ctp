<script type="text/javascript">
/*$(function(){
	input_show_select_calendar(".JtSelectDate", "#form_location_<?php echo $this->data['Location']['_id']; ?>");
})*/
</script>
<form class="<?php echo $controller; ?>_form_auto_save" id="form_location_<?php echo $this->data['Location']['_id']; ?>">
	<input type="hidden" id="mongoid" value="<?php echo $this->data['Location']['_id']; ?>" />
    <?php echo $this->Form->hidden('Location._id', array('value' => (string)$this->data['Location']['_id'])); ?>
	<div class="ui-field-contain">
        <label class="field-title" for="LocationNo"><?php echo __('Ref no'); ?></label>
        <?php echo $this->Form->input('Location.code', array(
				'readonly' 	=> 'true',
				'class' 	=> 'locationField'
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="Firstname"><?php echo __('Location name'); ?></label>
        <?php echo $this->Form->input('Location.name', array(
				'class' 	=> 'locationField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="LocationLocationType"><?php echo __('Type'); ?></label>
        <?php echo $this->Form->input('Location.location_type', array(
            	'type'=>'select',
				'options' => $arr_location_type,
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="LocationStockUsage"><?php echo __('Stock usage'); ?></label>
        <?php echo $this->Form->input('Location.stock_usage', array(
                'type'=>'select',
                'options' => $arr_stock_usage,
        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="LocationInactive"><?php echo __('active'); ?></label>
        <label class="field-title" for="LocationInactive"><?php echo __('Inactive'); ?></label>
        <?php
            echo $this->Form->input('Location.inactive', array(
                        'type'      => 'checkbox',
            ));
        ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="LocationBookable"><?php echo __('active'); ?></label>
        <label class="field-title" for="LocationBookable"><?php echo __('Bookable'); ?></label>
        <?php
            echo $this->Form->input('Location.bookable', array(
                    'type'      => 'checkbox',
            ));
        ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="LocationStockuse"><?php echo __('active'); ?></label>
        <label class="field-title" for="LocationStockuse"><?php echo __('Stockuse'); ?></label>
        <?php
            echo $this->Form->input('Location.stockuse', array(
                    'type'      => 'checkbox',
            ));
        ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="LocationCompanyName"><?php echo __('Company'); ?></label>
        <?php
                echo $this->Form->input('Location.company_name', array(
                        'readonly' => true,
                        'class' => 'popup-input',
                        'data-popup-controller' => 'companies',
                        'data-popup-key' => 'company_name'
                ));
                echo $this->Form->input('Location.company_id', array(
                    'type'=>'hidden'
                ));
        ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="LocationContactName"><?php echo __('Contact'); ?></label>
        <?php
                echo $this->Form->input('Location.contact_name', array(
                        'readonly' => true,
                        'class' => 'popup-input',
                        'data-popup-controller' => 'contacts',
                        'data-popup-key' => 'contact_name'
                ));
                echo $this->Form->input('Location.contact_id', array(
                    'type'=>'hidden'
                ));
        ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="LocationPhone"><?php echo __('Phone'); ?></label>
        <?php echo $this->Form->input('Location.phone', array(
				'class' 	=> 'locationField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="fax"><?php echo __('Fax'); ?></label>
        <?php echo $this->Form->input('Location.fax', array(
                'class'     => 'locationField',
        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="Email"><?php echo __('Email'); ?></label>
        <?php echo $this->Form->input('Location.email', array(
				'class' 	=> 'locationField',
		)); ?>
    </div>



    <div class="ui-field-contain">
        <label class="field-title" for="LocationTownCity"><?php echo __('Address'); ?></label>
        <div data-role="collapsible">
            <h1>Address Detail</h1>
            <?php
                $default_address_key = isset($this->data['Location']['addresses_default_key']) ? $this->data['Location']['addresses_default_key'] : 0;

            ?>
            <ul data-role="listview" data-theme="a">
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Address 1</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Location.address_1', array(
                                'value' => $this->data['Location']['shipping_address']["0"]['shipping_address_1'],
                                'readonly'  => 'true',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Address 2</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Location.address_1', array(
                                'value' => $this->data['Location']['shipping_address']["0"]['shipping_address_2'],
                                'readonly'  => 'true',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Address 3</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Location.address_3', array(
                                'value' => $this->data['Location']['shipping_address']["0"]['shipping_address_3'],
                                'readonly'  => 'true',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Town / City</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Location.town_city', array(
                                'value' => $this->data['Location']['shipping_address']["0"]['shipping_town_city'],
                                'readonly'  => 'true',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Province / State</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php
                            echo $this->Form->input('Location.province_state_id', array(
                                    'value' => $this->data['Location']['shipping_address']["0"]['shipping_province_state'],
                                    'options' => $options['provinces'][$this->data['Location']['shipping_address']["0"]['shipping_country_id']],
                                    'empty' => '',
                                    'readonly'  => 'true',
                                ));
                        ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Zip / Post code</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Location.zip_postcode', array(
                                'value' => $this->data['Location']['shipping_address']["0"]['shipping_zip_postcode'],
                                'readonly'  => 'true',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Country</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Location.country_id', array(
                                'options' => $options['countries'],
                                'value' => $this->data['Location']['shipping_address']["0"]['shipping_country_id'],
                                'readonly'  => 'true',
                        )); ?>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    

    <div class="ui-field-contain">
        <label class="field-title" for="LocationDescription"><?php echo __('Description'); ?></label>
		<?php
				echo $this->Form->input('Location.description', array(
					'readonly' => true,
				));
		?>
    </div>


</form>
<script type="text/javascript">
	/*$(function() {
        locations_update_entry_header("<?php echo $this->data['Location']['_id']; ?>");
    });*/
</script>
<?php echo $this->element('../Locations/js'); ?>