<?php echo $this->Form->create('User'); ?>

<?php if(isset($message)){ echo $message; } ?>
<table class="Data">
	<tr>
		<td>Username:</td>
		<td><?php echo $this->Form->input('User.username'); ?></td>
	</tr>
	<tr>
		<td>Password:</td>
		<td><?php echo $this->Form->input('User.password', array('type' => 'text')); ?></td>
	</tr>
	<tr>
		<td>Fullname:</td>
		<td><?php echo $this->Form->input('User.fullname'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><button type="submit">Save</button></td>
	</tr>
</table>
<?php echo $this->Form->end(); ?>