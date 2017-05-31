<?php echo $this->Form->create('Equipment'); ?>

<?php if(isset($message)){ echo $message; } ?>
<table class="Data">
	<tr>
		<td>Equipmentname:</td>
		<td><?php echo $this->Form->input('Equipment.name'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><button type="submit">Save</button></td>
	</tr>
</table>
<?php echo $this->Form->end(); ?>