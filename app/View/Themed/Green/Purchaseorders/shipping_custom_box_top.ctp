<!-- <div class="float_left hbox_form" style="width:auto; margin-left:5px;">
     <a href="javascript:void(0)" onclick="checkCondition($(this),'track_shipping')" rel="<?php echo URL.'/'.$controller;?>/track_shipping/" >
     	<input class="btn_pur" id="track_shipping" type="button" value="Track shipping" style="width:99%;" />
     </a>
</div> -->
<?php if($this->Common->check_permission('products_@_entry_@_edit',$arr_permission)): ?>
<div class="float_left hbox_form" style="width:auto; margin-left:5px;">
     <a href="javascript:void(0)" onclick="checkCondition($(this),'receive_item')" rel="<?php echo URL.'/'.$controller;?>/receive_item/" >
     	<input class="btn_pur" id="receive_item" type="button" value="Receive" style="width:99%;" />
     </a>
</div>
<?php endif; ?>
<?php if($this->Common->check_permission('shippings_@_entry_@_add',$arr_permission)): ?>
<div class="float_left hbox_form" style="width:auto; margin-left:5px;">
     <a href="javascript:void(0)"  onclick="checkCondition($(this),'return_item')" rel="<?php echo URL.'/'.$controller;?>/return_item/" >
     	<input class="btn_pur" id="return_item" type="button" value="Return" style="width:99%;" />
     </a>
</div>
<?php endif; ?>
