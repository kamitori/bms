<?php
	if(isset($arr_settings['field'][$css]['setup']['blockcss']))
		$blockcss = $arr_settings['field'][$css]['setup']['blockcss'];
	else
		$blockcss = '';
?>

<div class="jt_panel" style=" <?php if(isset($blockcss)) echo $blockcss;?>">
 <?php foreach($datas as $keys){
 		$arr_field = $arr_settings['field'][$keys];

		if(isset($arr_field['setup']['blocktype']) && $arr_field['setup']['blocktype']=='address'){
		//nếu là panel loại address
	?>
		<?php echo $this->element('box_type/'.$arr_field['setup']['blocktype']); ?>

	<?php
		}else{

		//tinh toan lai width
 		$lb = $fiedw = '';
		if(isset($arr_field['setup']['lablewith'])){
			$lb = (int)$arr_field['setup']['lablewith'];
			$fiedw = 'width:'.(96-$lb).'%';
			$lb = 'width:'.$lb.'%';
		}
		if(isset($arr_field['setup']['fieldwith']))
			$fiedw ='width:'.$arr_field['setup']['fieldwith'].'%';

 	 ?>

     <div class="jt_box" style=" <?php if(isset($arr_field['setup']['css'])) echo $arr_field['setup']['css'];?>" >

        <?php foreach($arr_field as $k =>$arr_s){
				// neu loai field khac fieldsave, hidden, id
				if($k!='setup' && isset($arr_s['type']) && $arr_s['type']!='fieldsave' && !isset($arr_s['other_type'])&& $arr_s['type']!='hidden' && $arr_s['type']!='id'){ ?>
                 <div class="jt_box_line">

					<?php // LABEL ?>
                    <div class=" jt_box_label <?php if(isset($arr_s['moreclass'])) echo $arr_s['moreclass'];?>" style=" <?php echo $lb.';';?><?php if(isset($arr_s['morecss'])) echo $arr_s['morecss'];?>">
                        <?php if($arr_s['type']=='relationship' || $arr_s['type']=='autocomplete'){			$linkclass = '';
							if(isset($arr_field[$arr_s['id']]['default']) && $arr_field[$arr_s['id']]['default']!='')
								$linkclass = 'jt_link_on';
						?>
                        	<span class="jt_box_line_span link_to_<?php echo $k.' '.$linkclass;?>">
						<?php }?>
						<?php if(isset($arr_s['name'])) echo $arr_s['name']; else echo'&nbsp;';?>
						<?php if($arr_s['type']=='relationship' || $arr_s['type']=='autocomplete'){?>
                        	 </span>
						<?php }?>

                    </div>

                    <?php // INPUT ?>
                    <?php if(isset($arr_s['before'])) echo $arr_s['before'];?>
                    <?php if(isset($arr_s['before_field'])) echo $this->element('basic_field/before_field',array('before_field'=>$arr_s['before_field'],'panel'=>$keys));?>
                    <div class="jt_box_field <?php if(isset($arr_s['field_class'])) echo $arr_s['field_class'];?>" style=" <?php if(isset($arr_s['width']))echo 'width:'.$arr_s['width'].';';else echo $fiedw;?>">
                        <?php echo $this->element('basic_field/'.$arr_s['type'],array('keys'=>$k,'arr_field'=>$arr_s)); ?>
                    </div>
                    <?php if(isset($arr_s['after'])) echo $arr_s['after'];?>
                    <?php if(isset($arr_s['after_field'])) echo $this->element('basic_field/after_field',array('after_field'=>$arr_s['after_field'],'panel'=>$keys));?>


                </div>

                <?php // neu la loai field hidden, id
				}else if(isset($arr_s['type']) && ($arr_s['type']=='hidden' || $arr_s['type']=='id')){
					echo $this->element('basic_field/'.$arr_s['type'],array('keys'=>$k,'arr_field'=>$arr_s));
				?>
        <?php } } ?>

     </div>


 <?php } } ?>
</div>