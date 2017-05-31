<style type="text/css">
.bleed .viewprice_sizew, .bleed .viewprice_sizeh{
	color: blue !important;
}
</style>
<?php
		foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){

			echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));

		}
?>
<p class="clear"></p>
<input type="hidden" id="is_add" value="0" />
<?php
	echo $this->element('../Quotations/new_line_entry');
?>