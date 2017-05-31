<?php echo $this->element('entry_tab_option'); ?>
<div id="content">
	<div class="clear_percent">
		<div class="block_dent_a">
			<div class="title_1 float_left">
				<h1><?php if(isset($this->data['Doc']['name']))echo $this->data['Doc']['name']; ?></h1>
			</div>
			<div class="title_1 right_txt float_right">
				<h1><?php //echo $this->data['Doc']['type']; ?></h1>
			</div>
			<p class="clear"></p>
		</div>
	</div>

	<div class="clear_percent" id="docs_form_auto_save">
		<div class="block_top">
			<div class="clear_percent_3a float_left">
				<?php echo $this->Form->create('Doc'); ?>
				<?php echo $this->Form->hidden('Doc._id', array('value' => (string)$this->data['Doc']['_id'])); ?>
				<div class="tab_1 full_width">
					<span class="title_block bo_ra1">
						<span class="float_left">
							<span class="fl_dent">
								<h4><?php echo translate('Document info'); ?></h4>
							</span>
						</span>
					</span>
					<div class="tab_2_inner">
						<p class="clear">
							<span class="label_1 float_left minw_lab2">
								<?php echo translate('Ref no'); ?></span>
							<div class="width_in3a float_left indent_input_tp">
								<?php echo $this->Form->input('Doc.no', array(
									'class' => 'input_1 float_left bold',
								)); ?>
							</div>
						</p>
						<p class="clear">
							<span class="label_1 float_left minw_lab2"><?php echo translate('Document name'); ?></span>
							<div class="width_in3a float_left indent_input_tp">
								<?php echo $this->Form->input('Doc.name', array(
									'class' => 'input_1 float_left bold',
								)); ?>
							</div>
						</p>
						<p class="clear">
							<span class="label_1 float_left minw_lab2">
								<?php echo translate('Location'); ?>
							</span>
							<div class="width_in3a float_left indent_input_tp">
								<?php echo $this->Form->input('Doc.location', array(
									'class' => 'input_1 float_left bold',
								)); ?>
							</div>
						</p>
						<p class="clear">
							<span class="label_1 float_left minw_lab2"><?php echo translate('Description'); ?></span>
							<div class="width_in3a float_left indent_input_tp">
								<?php echo $this->Form->input('Doc.description', array(
									'class' => 'input_1 float_left bold',
								)); ?>
							</div>
						</p>
						<p class="clear">
							<span class="label_1 float_left minw_lab2"><?php echo translate('Category'); ?></span>
							<div class="width_in3a float_left indent_input_tp">
								<?php echo $this->Form->input('Doc.category', array(
										'class' => 'input_select',
										'readonly' => true
								)); ?>
								<?php echo $this->Form->hidden('Doc.category_id'); ?>
								<script type="text/javascript">
							        $(function () {
							            $("#DocCategory").combobox(<?php echo json_encode($arr_docs_category); ?>);
							        });
							    </script>
							</div>
						</p>
						<p class="clear">
							<span class="label_1 float_left minw_lab2"><?php echo translate('Type'); ?></span>
							<div class="width_in3a float_left indent_input_tp">
								<?php echo $this->Form->input('Doc.type', array(
									'class' => 'input_1 float_left bold',
									'readonly' => true
								)); ?>
							</div>
						</p>
						<p class="clear">
							<span class="label_1 float_left minw_lab2"><?php echo translate('Extension'); ?></span>
							<div class="width_in3a float_left indent_input_tp">
								<?php echo $this->Form->input('Doc.ext', array(
									'class' => 'input_1 float_left bold',
									'readonly' => true
								)); ?>
							</div>
						</p>
						<p class="clear">
							<span class="label_1 float_left minw_lab2"><?php echo translate('Created by module'); ?></span>
							<div class="width_in3a float_left indent_input_tp">
								<?php echo $this->Form->input('Doc.create_by_module', array(
									'class' => 'input_1 float_left bold',
									'readonly' => true
								)); ?>
							</div>
						</p>
						<p class="clear">
							<span class="label_1 float_left minw_lab2 fixbor3 color_hidden2"><?php echo translate('Module details'); ?></span>
							<div class="width_in3a float_left indent_input_tp">
								<?php echo $this->Form->input('Doc.module_detail', array(
									'class' => 'input_1 float_left bold',
									'readonly' => true
								)); ?>
							</div>
						</p>
						<p class="clear"></p>
					</div>
					<div>
						<span class="title_block">
							<span class="fl_dent">
								<h4><?php echo translate('Notes'); ?></h4>
							</span>
							<a href="" title="Link a contact">
								<span class="icon_notes top_f"></span>
							</a>
						</span>
						<div class="container_same_category" style="height: 278px">
							<div class="tab_2_inner">
								<div class="block_txt">
									<p>
										<?php echo $this->Form->input('Doc.note', array(
											'rows' => 17,
											'cols' => 49,
											'style' => 'border: none;resize:none'
										)); ?>
									</p>
								</div>
							</div>
						</div>
					</div>
					<span class="title_block bo_ra2"></span>
				</div>
				<?php echo $this->Form->end(); ?>
				<!--END Tab1 -->
				</div>
				<div class="clear_percent_4a float_left">
				<div class="tab_1 full_width">
					<span class="title_block bo_ra1">
						<span class="docs_title">
							<h4><?php echo translate('Document'); ?></h4>
						</span>
						<div class="docs_icons_right">
							<input class="btn_pur" type="button" value="Export" onclick="location.href='<?php echo URL; ?>/docs/entry_export/<?php echo $this->data['Doc']['_id']; ?>'">

							<div class="icon_docs_1">
								<span class="version_text"><?php echo translate('Insert'); ?></span>
								<div class="box_file_icons" style="margin-top: -3px;">

									<?php echo $this->Form->create('Docfile', array('url' => '/docs/entry_upload_file', 'type' => 'file')); ?>
									<?php echo $this->Form->hidden('Doc._id', array('value' => (string)$this->data['Doc']['_id'])); ?>
									<?php echo $this->Form->input('Doc.file', array(
											'class' => 'input_docs',
											'type' => 'file'
									)); ?>
									<?php echo $this->Form->end(); ?>

									<span class="icosp_sea indent_sea2" style=" margin: 2px 0 0 -19px;" onclick="$('#DocFile').click()"></span>
								</div>
							</div>
						</div>
					</span>
					<div class="container_same_category scroll_3" style="overflow: inherit;">
						<div class="tab_2_inner">
							<div class="block_txt">
								<div class="table">
									<div class="table_cell">
										<div class="table_position">
											<?php if(strlen($this->data['Doc']['path']) > 0){
													if(substr($this->data['Doc']['type'], 0, 5) == 'image'){
											?>
												<img style="max-width: 449px;" src="<?php echo URL . str_replace('\\', '/', $this->data['Doc']['path']); ?>" alt="" />
											<?php }else if($this->data['Doc']['type'] == "application/pdf" ){ ?>
												<?php echo $this->data['Doc']['name']; ?><br />
												<?php if(file_exists(WWW_ROOT.$this->data['Doc']['path'])){ ?>
												<iframe id="fred"  src="<?php echo URL . str_replace('\\', '/', $this->data['Doc']['path']); ?>" style="height: 508px; width: 100%;"></iframe>
												<?php } else { ?>
												This file does not exist.
												<?php } ?>
											<?php }else{
													echo $this->data['Doc']['name'];
												}
											} ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<span class="title_block bo_ra2">
						<span class="check_right_ft">
							<div class="middle_check">
								<label class="m_check2">
									<input type="checkbox">
									<span class="bx_check"></span>
								</label>
							</div>
						</span>
					</span>
				</div>
				<!--END Tab1 -->
			</div>
			<div class="clear_percent_5 float_right">
				<div class="full_width">
					<div class="tab_1 full_width">
						<span class="title_block bo_ra1">
							<span class="fl_dent">
								<h4><?php echo translate('Document usage'); ?></h4>
							</span>
						</span>
						<ul class="ul_mag clear bg3">
							<li class="hg_padd" style="width: 3%"></li>
							<li class="hg_padd" style="width: 18%"><?php echo translate('Module'); ?></li>
							<li class="hg_padd" style="width: 10%"><?php echo translate('Ref no'); ?></li>
							<li class="hg_padd" style="width: 17%"><?php echo translate('Details'); ?></li>
							<li class="hg_padd center_txt" style="width: 15%"><?php echo translate('Date'); ?></li>
							<li class="hg_padd" style="width: 22%"><?php echo translate('Linked by'); ?></li>
							<li class="hg_padd bor_mt" style="width: 3%"></li>
						</ul>
						<div class="container_same_category scroll_4" style="height: 506px">

							<?php

							$i = 1;
							foreach ($arr_docuse as $value) {

								$i = 3 - $i;
							?>

							<ul class="ul_mag clear bg<?php echo $i; ?>" id="DocUse_<?php echo $value['_id']; ?>">
								<li class="hg_padd" style="width: 3%">
									<?php if(isset($value['controller']) || isset($value['module_controller']) ){ ?>
									<?php if(isset($value['module_controller']))  $value['controller'] = $value['module_controller']; ?>
									<a href="<?php echo URL.'/'.$value['controller'].'/entry/'.$value['module_id']; ?>">
										<span class="icon_emp"></span>
									</a>
									<?php } ?>
								</li>
								<li class="hg_padd" style="width: 18%"><?php echo $value['module']; ?></li>
								<li class="hg_padd" style="width: 10%"><?php if(isset($value['module_no']))echo $value['module_no']; ?></li>
								<li class="hg_padd" style="width: 17%"><?php if(isset($value['module_detail']))echo $value['module_detail']; ?></li>
								<li class="hg_padd center_txt" style="width: 15%"><?php echo $this->Common->format_date($value['_id']->getTimestamp(), false); ?></li>
								<li class="hg_padd" style="width: 22%"><?php if(isset($arr_contact[(string)$value['modified_by']]))echo $arr_contact[(string)$value['modified_by']]; ?></li>
								<li class="hg_padd bor_mt" style="width: 3%">
									<div class="middle_check">
										<a title="Delete link" href="javascript:void(0)" onclick="docs_entry_delete('<?php echo $value['_id']; ?>')">
											<span class="icon_remove2"></span>
										</a>
									</div>
								</li>
							</ul>

							<?php } ?>

							<?php $count = 23 - count($arr_docuse);
								if( $count > 0 ){
									for ($j=0; $j < $count; $j++) {
										$i = 3 - $i;
							?>
							<ul class="ul_mag clear bg<?php echo $i; ?>">
								<li class="hg_padd" style="width: 3%"></li>
								<li class="hg_padd" style="width: 18%"></li>
								<li class="hg_padd" style="width: 10%"></li>
								<li class="hg_padd" style="width: 17%"></li>
								<li class="hg_padd" style="width: 15%"></li>
								<li class="hg_padd" style="width: 22%"></li>
								<li class="hg_padd bor_mt" style="width: 3%"></li>
							</ul>
							<?php
									}
								}
							?>

						</div>
						<span class="title_block bo_ra2">
							<span class="icon_vwie indent_down_vwie2">
								<a href="">
									Click to view module record
								</a>
							</span>
						</span>
					</div>
					<!--END Tab1 -->
				</div>

			</div>
		</div>
	</div>
	<p class="clear"></p>
</div>
<!--END Content -->

<?php echo $this->element('../Docs/js'); ?>