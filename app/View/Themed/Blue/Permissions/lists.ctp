<?php echo $this->element('../'.$name.'/tab_option'); ?>

<div id="content">
	<div class="w_ul2 ul_res2">
        <ul class="ul_mag clear bg top_header_inner2 ul_res2">
        	<li class="hg_padd" style="width:.5%"></li>
        	 <?php foreach ($list_field as $ks => $vls){?>
             	<li class="hg_padd" style=" <?php if(isset($vls['css'])) echo $vls['css']; ?>">
            		 <?php if(isset($name_field[$ks])) echo $name_field[$ks]; ?>
            	</li>
             <?php } ?>
             <li class="hg_padd bor_mt" style="width:.5%"></li>
        </ul>
        <div id="list_view" style="height:100%; overflow:hidden; max-width:100%;" class="indent_ul_top">
		<?php $n=0; foreach ($arr_list as $value) { if($n%2>0) $nclass=" bg1"; else $nclass=" bg2";?>
        	<ul class="ul_mag clear <?php echo $nclass;?>">

                <li class="hg_padd" style="width:.5%" onclick=" window.location.assign('<?php echo URL.'/'.$controller; ?>/entry/<?php if(isset($value['_id'])) echo $value['_id']; ?>');">
                	<span class="icon_emp"></span>
                </li>

				<?php foreach ($list_field as $ks => $vls){?>
                	<li class="hg_padd" style=" <?php if(isset($vls['css'])) echo $vls['css']; ?><?php if(isset($vls['align'])) echo 'text-align:'.$vls['align']; ?>">
                    	<?php
							if($arr_types[$ks]=='select' && isset($value[$ks]) && $value[$ks]!='')
								echo $opt_select[$ks][$value[$ks]];
							else if(($arr_types[$ks]=='price' || $arr_types[$ks]=='price_notchange') && isset($value[$ks]) && $value[$ks]!='')
								echo $this->Common->format_currency((float)$value[$ks]);
							else if($arr_types[$ks]=='percent' && isset($value[$ks]) && $value[$ks]!='')
								echo $this->Common->format_currency((float)$value[$ks]*100).'%';
							else if(isset($value[$ks]))
								echo $value[$ks];
						?>
                    </li>
                <?php } ?>

                <li class="hg_padd bor_mt" style="width:.5%">
                    <div class="middle_check">
                        <a title="Delete link" style="cursor:pointer;" class="delete_link" rel="<?php if(isset($value['_id'])) echo $value['_id']; ?>">
                            <span class="icon_remove2"></span>
                        </a>
                    </div>
                </li>
            </ul>
        <?php $n++; } ?>
        	<input type="hidden" name="sum" id="sum" value="<?php echo $n;?>" />
        </div>

    </div>
</div>

<?php echo $this->element('js_list');?>

<script>
	(function($){
		$(".delete_link").click(function(){
			if (confirm('Are you sure you want to delete this record?')) {
				// Save it!
				//remove line
				var ids = $(this).attr("rel");
				var ix = $('.ul_mag').index($(this).parent().parent().parent());

				$(this).parent().parent().parent().animate({
				  opacity:'0.1',
				  height:'1px'
				},500,function(){$(this).remove();});

				ix = parseInt(ix);
				//changebg(ix);
				$.ajax({
					url: '<?php echo URL.'/'.$controller;?>/delete/'+ids,
					type:"POST",
					success: function(){
						changebg(ix);
					}
				});
			} else {
				// Do nothing!
				//alert('no');
			}
		});
	})(jQuery);
</script>