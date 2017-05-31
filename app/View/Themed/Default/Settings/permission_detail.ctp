<style>
	table input[type="text"]{
		width: 400px;
	}

</style>
<div class="tab_1 full_width" style="margin-bottom: 2%;">
    <span class="title_block bo_ra1">
        <span class="fl_dent">
            <h4 id="setting_name"><?php
				if (isset($permissions['controller']))
					echo $permissions['controller'];
				?>
				- option
            </h4>
        </span>
        <a title="Add option" href="javascript:void(0)" onclick="permission_option_add('<?php echo $permissions['controller']; ?>')">
            <span class="icon_down_tl top_f"></span>
        </a>
    </span>

	<div class="container_same_category" style="height: 250px;" id="permission_add">
		<form method="post" id="f_option_add" style="width: 100%;">
			<table border="1" style="margin-top: 5px">
				<input type="hidden" name="op_ctr" value="'<?php echo $permissions['controller']; ?>" />
				<tr>
					<td>Col</td>

					<td>
						<select name="col">
							<option value="0">0</option>
							<option value="1">1</option>
							<option value="2">2</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Group</td>
					<td><input type="text" name="group" value="" /></td>
				</tr>
				<tr>
					<td>Url</td>
					<td><input type="text" name="url" value="" /></td>
				</tr>
				<tr>
					<td>Name</td>
					<td><input type="text" name="name" value="" /></td>
				</tr>
				<tr>
					<td>Type</td>
					<td>
						<select name="type">
							<option value="">Empty</option>
							<option value="add">Add</option>
							<option value="search">Search</option>
							<option value="printer">Printer</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Flag</td>
					<td>
						<select name="flag">
							<option value="">Empty</option>
							<option value="cana">Canada</option>
							<option value="en">English</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Decription</td>
					<td><textarea name="description"></textarea></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" name="submit" value="Save"/></td>
				</tr>
			</table>
		</form>
    </div>
	<span class="title_block bo_ra2">
        <span class="float_left bt_block"><?php echo translate('Edit or create values for list'); ?>.</span>
    </span>
</div>

<script>
	$(document).ready(function() {
		$('#f_option_add').submit(function() {
			var id = '<?php echo $permissions['_id']; ?>';
			$.ajax({
				url: '<?php echo URL ?>/settings/permission_detail_add/' + id,
				type: 'post',
				data: $(this).serialize(),
				success: function(html) {
					alert(html);
				}
			});
			return false;
		});
	})
</script>









