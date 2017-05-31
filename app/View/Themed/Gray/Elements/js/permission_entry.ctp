
<script type="text/javascript">
	$(function() {

		$(".jt_ajax_note").html('');

		<?php echo $this->Common->check_lock_sub_tab($controller, $arr_permission); ?>
        <?php echo $this->Common->unlink_crm_modules($controller,$arr_permission); ?>

		$("li", "#<?php echo $controller; ?>_ul_sub_content").click(function() {
			if( $(this).hasClass("disabled_li") )return false;
			var val = $(this).attr("id");
			$("li", "#<?php echo $controller; ?>_ul_sub_content").removeClass("active");
			$("#" + val).addClass("active");
			$.ajax({
				url: '<?php echo URL; ?>/<?php echo $controller; ?>/sub_tab/' + val + "/<?php echo (isset($this->data[$model]['_id']) ? $this->data[$model]['_id'] : $iditem); ?>",
				success: function(html) {
					$("#<?php echo $controller; ?>_sub_content").html(html);

					$(".container_same_category", "#<?php echo $controller; ?>_sub_content").mCustomScrollbar({
						scrollButtons:{
							enable:false
						},
						advanced:{
					        updateOnContentResize: true,
					        autoScrollOnFocus: false,
					    }
					});
				}
			});

		});

		<?php // co quyen EDIT ?>
		<?php if( $this->Common->check_permission($controller.'_@_entry_@_edit', $arr_permission) ){ ?>
			$("form :input", "#<?php echo $controller; ?>_form_auto_save").change(function() {
				<?php echo $controller; ?>_auto_save_entry(this);
			});
			$("form :input", "#<?php echo $controller; ?>_form_auto_save").focus(function() {
				$(this).removeClass('error_input').removeClass('error_color ');
			});
			<?php echo $controller; ?>_update_entry_header();

		<?php // co quyen VIEW ?>
		<?php }else{ ?>
			$(":input", "#<?php echo $controller; ?>_form_auto_save").attr("disabled", true).css("background","transparent");
			$(".combobox_button, .indent_dw_m, .icon_down_new", "#<?php echo $controller; ?>_form_auto_save").remove();
		<?php } ?>
	})
</script>