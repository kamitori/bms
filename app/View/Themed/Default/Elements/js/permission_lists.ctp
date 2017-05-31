<?php if( $this->Common->check_permission($controller.'_@_entry_@_delete', $arr_permission) ){ ?>
<script type="text/javascript">
	function <?php echo $controller; ?>_lists_delete(id) {
		confirms("Message", "Are you sure you want to delete?",
			function() {
				$.ajax({
					url: '<?php echo URL; ?>/<?php echo $controller; ?>/lists_delete/' + id,
					timeout: 15000,
					success: function(html) {
						if( html == "ok" )
							$("#<?php echo $controller; ?>_" + id).fadeOut();
						else
							alerts("Error: ", html);
					}
				});
			}, function() {
			//else do somthing
		});
	}
</script>
<?php } ?>