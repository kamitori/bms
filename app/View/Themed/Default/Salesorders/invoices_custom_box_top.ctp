<?php if($this->Common->check_permission('salesinvoices_@_entry_@_add',$arr_permission)): ?>
<div class="float_left hbox_form" style="width:auto; margin-left:5px;">
     <a id="create_full_invoice" href="javascript:void(0)" onclick="create_full_invoice()">
     	<input class="btn_pur" id="printexport_products" type="button" value="Create full invoice" style="width:99%;" />
     </a>
</div>

<div class="float_left hbox_form" style="width:auto; margin-left:5px;">
     <a id="create_part_invoice" href="javascript:void(0)">
     	<input class="btn_pur" id="printexport_products" type="button" value="Create part invoice" style="width:99%;" />
     </a>
</div>
<?php endif; ?>