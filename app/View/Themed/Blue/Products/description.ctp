<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<textarea class="ckeditor" id="product_desciption" name="data[product_desciption]">
	<?php echo $product_desciption; ?>
</textarea>
<script type="text/javascript">
$(function(){
	CKEDITOR.replace('product_desciption',
	{
		toolbar : 'ProductDescription',
        resize_enabled : false,
        allowedContent: {
            'table b i u ul ol big small span label': { styles:true },
            'div' : { styles:true},
            'h1 h2 h3 hr p blockquote li': { styles:true },
            a: { attributes: '!href' },
            img: {
                attributes: '!src,alt',
                styles: true,
                classes: 'left,right'
            }
        },
        on : {
            instanceReady : function ( evt ) {
                var editor = evt.editor,
                body = editor.document.getBody();
                body.setAttribute( 'id', 'product_desciption_content ');
            }
        },
        height : 300,
        enterMode:CKEDITOR.ENTER_BR,
	});
    CKEDITOR.instances['product_desciption'].on('blur', function(e) {
        if (e.editor.checkDirty()) {
            var product_desciption = CKEDITOR.instances.product_desciption.getData();
            save_data('product_desciption',product_desciption,$("#mongo_id").val());
        }
    });
})
</script>