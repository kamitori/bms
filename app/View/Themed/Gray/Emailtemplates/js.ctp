<?php echo $this->element('js_entry');?>
<script type="text/javascript">
$(function(){
	$("input","#form_email_template").change(function(){
		$.ajax({
            url: '<?php echo URL; ?>/emailtemplates/auto_save',
            type: "post",
            data: $("input", "#form_email_template").serialize(),
            success: function(html) {
            	ajax_note('Saving...');
            	if (html != "ok") {
                    alerts('Message', html);
                }
                $("#email_template_header").html($("#EmailTemplateName").val());
                ajax_note_set('');
            }
        });
	});
    CKEDITOR.config.contentsCss = '<?php echo URL; ?>/theme/default/css/jt_vunguyen.css';
	CKEDITOR.replace('email_template',
	{
		toolbar : 'EmailTemplate',
        bodyId :'email_template_content',
        resize_enabled : false,
        removePlugins : 'elementspath',
        allowedContent: {
            'table b i u ul ol big small span label': { styles:true },
            'div' : { styles:true},
            'h1 h2 h3 hr p blockquote li': { styles:true },
            'span': {classes:true,attributes:'rel,contenteditable,unselectable'},
            a: { attributes: '!href' },
            img: {
                attributes: '!src,alt',
                styles: true,
                classes: 'left,right'
            }
        },
        filebrowserImageUploadUrl : '<?php echo URL; ?>/js/kcfinder/upload.php?type=images',
        filebrowserImageBrowseUrl : '<?php echo URL; ?>/js/kcfinder/browse.php?type=images',
        on : {
            instanceReady : function ( evt ) {
                var editor = evt.editor,
                body = editor.document.getBody();
                body.setAttribute( 'id', 'email_template_content ');
            }
        },
        height : 310,
        // enterMode:CKEDITOR.ENTER_BR,
	});
    CKEDITOR.instances['email_template'].on('blur', function(e) {
        if (e.editor.checkDirty()) {
            var template = new Object();
            template['template'] = CKEDITOR.instances.email_template.getData();
            $.ajax({
                url: '<?php echo URL; ?>/emailtemplates/auto_save',
                type: "post",
                data: {'data' : { 'EmailTemplate' : template}},
                success: function(html) {
                    ajax_note('Saving...');
                    if (html != "ok") {
                        alerts('Message', html);
                    }
                    ajax_note_set('');
                }
            });
        }
    });
    $.fn.insertAtCaret = function (html) {
        CKEDITOR.instances['email_template'].insertHtml(html);
    };
    spanclick();
    $("#EmailTemplateFolder").change(function(){
        $.ajax({
            url: "<?php echo URL.'/'.$controller; ?>/get_folder",
            type: "POST",
            data: {'folder_name':$("#EmailTemplateFolderId").val()},
            success: function(result){
                $("span.field_button").html(result);
                spanclick();
            }
        });
    });
})
function spanclick(){
    $("span",".field_button").click(function(){
        var tmp_html = $(this);
        tmp_html.addClass("field_span");
        $.fn.insertAtCaret(tmp_html[0].outerHTML);
        return false;
    });
}
</script>