<?php echo $this->element('../'.$controller.'/tab_option', array('no_show_delete' => true)); ?>
<?php echo $this->element('js/lists_view'); ?>
<div id="content" class="fix_magr">
	<form method="POST" id="sort_form">
		<div class="w_ul2 ul_res2">
			<ul class="ul_mag clear bg top_header_inner2 ul_res2" id="sort">
				<li class="hg_padd" style="width:1%"></li>
				<li class="hg_padd" style="width:5%">
					<label>Ref no</label>
					<span id="no" class="desc"></span>
				</li>
				<li class="hg_padd" style="width:31%">
					<label>Name</label>
					<span id="name" class="desc"></span>
				</li>
				<li class="hg_padd" style="width:15%">
					<label>Type</label>
					<span id="type_id" class="desc"></span>
				</li>
				<li class="hg_padd" style="width:15%">
					<label>Folder</label>
					<span id="folder_id" class="desc"></span>
				</li>
				<li class="hg_padd" style="width:10%">
					<label>Create by</label>
					<span id="created_by" class="desc"></span>
				</li>
				<li class="hg_padd" style="width:10%">
					<label>Modified by</label>
					<span id="modified_by" class="desc"></span>
				</li>
				<li class="hg_padd center_txt" style="width:4%">
					<label>Inactive</label>
					<span id="inactive" class="desc"></span>
				</li>
			</ul>
			<div id="lists_view_content">
				<!-- goi lists ajax -->
				<?php echo $this->element('../Emailtemplates/lists_ajax') ?>
			</div>
		</form>

	</div>
</div>
<script type="text/javascript">
    function emailtemplates_lists_delete(id) {
        confirms("Message", "Are you sure you want to delete?",
                function() {
                    $.ajax({
                        url: '<?php echo URL; ?>/emailtemplates/delete/' + id,
                        timeout: 15000,
                        success: function(html) {
                            if (html == "ok") {
                                $("#emailtemplates_" + id).fadeOut();
                            }
                            else
                            	alerts('Message',html);
                            console.log(html);
                        }
                    });
                }, function() {
            //else do somthing
        });

        return false;
    }
</script>