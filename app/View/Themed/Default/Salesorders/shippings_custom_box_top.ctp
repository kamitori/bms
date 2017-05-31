<?php if($this->Common->check_permission('shippings_@_entry_@_add',$arr_permission)): ?>
<div class="float_left hbox_form" style="width:auto; margin-left:5px;">
     <a id="create_full_shipment" href="javascript:void(0)" onclick="create_full_shipping()">
     	<input class="btn_pur" id="printexport_products" type="button" value="Create full shipment" style="width:99%;" />
     </a>
</div>

<div class="float_left hbox_form" style="width:auto; margin-left:5px;">
     <a id="create_part_shipment" href="javascript:void(0)">
     	<input class="btn_pur" id="printexport_products" type="button" value="Create part shipment" style="width:99%;" />
     </a>
</div>
<?php endif; ?>