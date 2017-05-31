<?php if(isset($doc_id)){ ?>
<div class="middle_check" style="margin-left: 300px;">
	<a title="Delete image" style=" text-decoration: none" href="javascript:void(0)" onclick="product_image_delete('<?php echo $doc_id; ?>')">
		<span style="width: 14px;height: 14px;border: 1px solid #fff; text-align: center; line-height: 14px;font-weight: 900;">X</span>
	</a>
</div>
<script type="text/javascript">
	function product_image_delete(img_id){
		confirms( "Message", "Are you sure you want to delete?",
			function(){
				save_option('products_upload',{'deleted':true},'<?php echo $key; ?>',0,'','update',function(){
					$.ajax({
						url: 'http://localhost/jobtraq/products/documents_delete/'+$("#mongo_id").val()+'/' + img_id + '/image',
						timeout: 15000,
						success: function(html){
							if(html != "ok"){
								alerts("Error: ", html);
							}else
								location.reload();
						}
					});
				});
			},function(){
				//else do somthing
		});
		return false;
	}
</script>
<?php } ?>