<?php if($this->Common->check_permission($controller.'_@_documents_tab_@_view',$arr_permission)
			|| $this->Common->check_permission('docs_@_entry_@_view',$arr_permission) ): ?>
<div class="tab_1 full_width block_dent9">
	<span class="title_block bo_ra1">
		<span class="fl_dent"><h4><?php echo translate('Document / file management'); ?></h4></span>
		<?php if($this->Common->check_permission($controller.'_@_documents_tab_@_add',$arr_permission)
			|| $this->Common->check_permission('docs_@_entry_@_add',$arr_permission) ):  ?>
		<a onclick="<?php echo $controller; ?>_docs_select_file()" href="javascript:void(0)" title="Link a document / file"><span class="icon_down_tl top_f"></span></a>
		<?php endif; ?>
		<a id="click_open_window_docs" href="javascript:void(0)" style="float: right">.</a>
	</span>
	<p class="clear"></p>
	<ul class="ul_mag clear bg3">
		<li class="hg_padd" style="width:1.5%"></li>
		<li class="hg_padd" style="width:18%"><?php echo translate('Document / file name'); ?></li>
		<li class="hg_padd" style="width:11%"><?php echo translate('Category'); ?></li>
		<li class="hg_padd center_txt" style="width:3%"><?php echo translate('Ext'); ?></li>
		<li class="hg_padd" style="width:11%"><?php echo translate('Type'); ?></li>
		<li class="hg_padd" style="width:46%"><?php echo translate('Description'); ?></li>
		<li class="hg_padd bor_mt" style="width:1.5%"></li>
	</ul>

	<div id="<?php echo $controller; ?>_docs_data">
		<?php if($this->Common->check_permission($controller.'_@_documents_tab_@_delete',$arr_permission)
					|| $this->Common->check_permission('docs_@_entry_@_delete',$arr_permission) ):  ?>
		<?php
			$i = 1; $count = 0;
			foreach ($arr_doc as $value) {
		?>
		<ul class="ul_mag clear bg<?php echo $i; ?>" id="DocUse_<?php echo $value['_id']; ?>">
			<li class="hg_padd" style="width:1.5%">
				<a href="<?php echo URL; ?>/docs/entry/<?php echo $value['_id']; ?>"><span class="icon_emp"></span></a>
			</li>
			<li class="hg_padd" style="width:18%"><?php echo $value['name']; ?></li>
			<li class="hg_padd" style="width:11%"><?php if(isset($value['category_id']) && isset($arr_docs_category[$value['category_id']]))echo $arr_docs_category[$value['category_id']]; ?></li>
			<li class="hg_padd center_txt" style="width:3%"><?php echo $value['ext']; ?></li>
			<li class="hg_padd" style="width:11%"><?php echo $value['type']; ?></li>
			<li class="hg_padd" style="width:46%"><?php if(isset($value['description']))echo $value['description']; ?></li>
			<li class="hg_padd bor_mt" style="width:1.5%">
				<div class="middle_check">
					<a title="Delete link" href="javascript:void(0)" onclick="<?php echo $controller; ?>_docs_delete('<?php echo $value['_id']; ?>')">
						<span class="icon_remove2"></span>
					</a>
				</div>
			</li>
		</ul>

		<?php $i = 3 - $i; $count += 1;
			}
		?>
		<?php else: ?>
		<?php
			$i = 1; $count = 0;
			foreach ($arr_doc as $value) {
		?>
		<ul class="ul_mag clear bg<?php echo $i; ?>" id="DocUse_<?php echo $value['_id']; ?>">
			<li class="hg_padd" style="width:1.5%">
				<a href="<?php echo URL; ?>/docs/entry/<?php echo $value['_id']; ?>"><span class="icon_emp"></span></a>
			</li>
			<li class="hg_padd" style="width:18%"><?php echo $value['name']; ?></li>
			<li class="hg_padd" style="width:11%"><?php if(isset($value['category_id']) && isset($arr_docs_category[$value['category_id']]))echo $arr_docs_category[$value['category_id']]; ?></li>
			<li class="hg_padd center_txt" style="width:3%"><?php echo $value['ext']; ?></li>
			<li class="hg_padd" style="width:11%"><?php echo $value['type']; ?></li>
			<li class="hg_padd" style="width:46%"><?php if(isset($value['description']))echo $value['description']; ?></li>
			<li class="hg_padd bor_mt" style="width:1.5%"></li>
		</ul>

		<?php $i = 3 - $i; $count += 1;
			}
		?>
		<?php endif; ?>
		<?php
		$count = 8 - $count;
		if( $count > 0 ){
			for ($j=0; $j < $count; $j++) { ?>
				<ul class="ul_mag clear bg<?php echo $i; ?>">
				</ul>
	  <?php $i = 3 - $i;
			}
		}
	?>

	</div>

	<span class="hit"></span>
	<span class="title_block bo_ra2">
		<span class="float_left bt_block"><?php echo translate('Click to view document record'); ?></span>
	</span>

</div><!--END Tab1 -->

<script type="text/javascript">

	$(function(){
		window_popup('docs', 'Specify documents');
	});

	function after_choose_docs(doc_id, doc_name){
		var data = $("#Doc_" + doc_id).html();
		var html = $.parseHTML( data );
		$.ajax({
			url: '<?php echo URL; ?>/<?php echo $controller; ?>/documents_save/<?php echo $module_id; ?>/' + doc_id + '/' + $("#<?php echo $model; ?>No").val() + '/' + $("#<?php echo $model; ?>Name").val(),
			timeout: 15000,
			success: function(html){
				if(html != "ok"){
					alerts("Error: ", html);
				}else{
					$("#window_popup_docs").data("kendoWindow").close();
					$("#documents").click();
				}
				console.log(html);
			}
		});

		return false;
	}
	<?php if($this->Common->check_permission($controller.'_@_documents_tab_@_delete',$arr_permission)
					|| $this->Common->check_permission('docs_@_entry_@_delete',$arr_permission) ):  ?>
	function <?php echo $controller; ?>_docs_delete(doc_id){

		confirms( "Message", "Are you sure you want to delete?",
			function(){
				$.ajax({
					url: '<?php echo URL; ?>/<?php echo $controller; ?>/documents_delete/<?php echo $module_id; ?>/' + doc_id,
					timeout: 15000,
					success: function(html){
						if(html == "ok"){
							$("#DocUse_" + doc_id).fadeOut();
						}else{
							alerts("Error: ", html);
						}
						console.log(html);
					}
				});
			},function(){
				//else do somthing
		});
		return false;
	}
	<?php endif; ?>
	function <?php echo $controller; ?>_docs_select_file(){
		confirms3('Message','<?php echo translate('Would you like to add a new document or link to an exist document from the Documents module'); ?>',['Existing','New','']
			,function(){//Existing
				$("#click_open_window_docs").click();
			},function(){//New
				location.href="<?php echo URL; ?>/docs/add/<?php echo $model; ?>/<?php echo $module_id; ?>/" + $("#<?php echo $model; ?>Name").val();
			},function(){
				return false;
			});
	}
</script>
<?php endif; ?>