<div class="block_dent2" style="width:100%; margin: 0 auto; height:400px;" id="list_view_<?php echo $controller;?>">

    <table class="jt_tb" id="Form_add" style="width:100%; font-size:12px;">
        <caption>&nbsp;<!--List view - <?php echo $name;?>--></caption>
        <thead>
            <tr>
            	<th></th>
                <?php foreach ($list_field as $ks => $vls){?>
                    <th>
                        <?php if(isset($name_field[$ks])) echo $name_field[$ks]; ?>
                    </th>
                <?php } ?>
                <th></th>
            </tr>
        </thead>
        
        <tbody>
            <?php $n=0; foreach ($arr_list as $value) { if($n%2>0) $nclass="jt_line_light"; else $nclass="jt_line_black";?>
                <tr class="<?php echo $nclass;?>" onclick="after_choose_<?php echo $controller;?>('<?php if(isset($keys)) echo $keys; ?>','<?php if(isset($value['name'])) echo $value['name']; ?>','<?php if(isset($value['_id'])) echo $value['_id']; ?>')">
                    <td style="width:60px;" align="center">
                    	<input type="checkbox" name="checkbox" disabled="disabled" />
                    </td>
					<?php foreach ($list_field as $ks => $vls){?>
                        <td align="<?php if(isset($vls['align'])) echo $vls['align']; ?>" >
                            <?php if(isset($value[$ks])) echo $value[$ks]; ?>
                        </td>
                    <?php }?>
                    <td style="width:60px;" align="center">
                        <a class="jt_edit" onclick="after_choose_<?php echo $controller;?>('<?php if(isset($keys)) echo $keys; ?>','<?php if(isset($value['name'])) echo $value['name']; ?>','<?php if(isset($value['_id'])) echo $value['_id']; ?>')">
                            Select
                        </a>
                    </td>
                </tr>
            <?php $n++; } ?>
        </tbody>
    </table>
    <input type="hidden" name="key_return" id="key_return" value="<?php if(isset($keys)) echo $keys; ?>" />
</div>
<script>
	(function($){
		$(window).load(function(){
			$("#list_view_<?php echo $controller;?>").mCustomScrollbar({
				scrollButtons:{
					enable:false
				}
			});
		});
				
	})(jQuery);
</script>