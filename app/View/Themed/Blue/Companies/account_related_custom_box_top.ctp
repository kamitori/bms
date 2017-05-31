<?php if(isset($button_account) && $button_account =='view'){?>
	<span class="title_block_inner3 center_txt">
		<input class="btn_pur" type="button" value="View account" onclick=" ">
	</span>
<?php }else if($this->Common->check_permission('salesaccounts_@_entry_@_edit',$arr_permission)){?>
	<span class="title_block_inner3 center_txt">
		<input class="btn_pur" type="button" value="Create account" onclick="company_sc_create()">
	</span>
<?php }?>
