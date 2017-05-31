<div id="content" class="fix_magr">
	<?php echo $this->element('../Settings/index', array('active' => 2)); ?>
	<div class="clear" style="margin-top: 10px;" id="salesorders_form_auto_save">
		<div class="block_dent2" style="width:800px; margin: 0 auto; height:400px;" id="list_view_company">
            <div style="margin:50px auto auto auto; width:400px; height:50px; text-align:center;">
            		<p style="color:#03C;">Double module for custom</p><br />
                    <?php echo $this->Form->create('Setting'); ?>
                        Module Name: <?php echo $this->Form->input('Setting.name', array('class' => 'k-input')); ?><?php echo $this->Form->input('Setting.many', array('class' => 'k-input','style'=>'width:30px','value'=>'s')); ?>
                        <?php
                            echo $this->Js->submit( "Add new", array(
                                "class" => "k-button",
                                'success' => '$("#content").html(data);',
                                'div' => false
                            ));
                        ?>
                    <?php echo $this->Form->end(); ?>
            </div>
		</div>
		
	</div>
	<div class="clear"></div>
</div>

<script type="text/javascript">
</script>