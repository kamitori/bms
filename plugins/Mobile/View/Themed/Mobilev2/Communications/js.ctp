<div data-role="popup" id="popupNested" data-theme="none">
    <div data-role="collapsible-set" data-theme="b" data-content-theme="a" data-collapsed-icon="arrow-r" data-expanded-icon="arrow-d" style="margin:0; width:250px;">
        <ul data-role="listview">
            <li><a href="<?php echo M_URL.'/communications/add_com/message'; ?>" rel="external" data-rel="dialog">Message</a></li>
            <li><a href="<?php echo M_URL.'/communications/add_com/letter'; ?>" rel="external" data-rel="dialog">Letter</a></li>
            <li><a href="<?php echo M_URL.'/communications/add_com/fax'; ?>" rel="external" data-rel="dialog">Fax</a></li>
            <li><a href="<?php echo M_URL.'/communications/add_com/note'; ?>" rel="external" data-rel="dialog">Note</a></li>
            <li><a href="<?php echo M_URL.'/communications/add_com/email'; ?>" rel="external" data-rel="dialog">Email</a></li>
        </ul>
    </div>
</div>

<div data-role="popup" id="popupDialog" data-overlay-theme="b" data-theme="b" data-dismissible="false" style="max-width:300px;">
    <div role="main" class="ui-content">
        <h2 class="ui-title">Are you sure you want to delete this record?</h2>
    <p>This action cannot be undone.</p>
        <input type="hidden" id="hiddenId" value="" />
        <input  type="text" name="email_system" id="email_system" />
        <a href="#" id="cancel_button" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b" data-rel="back">Cancel</a>
        <a href="#" id="delete_button" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b" data-rel="back" data-transition="flow">Delete</a>
    </div>
</div>
<script type="text/javascript">
    $('#delete_button').click(function(){
        window.location.assign("<?php echo M_URL.'/'.$controller.'/delete/'.(isset($mongoid) ? $mongoid : ''); ?>");return false;
    });
</script>

<script type="text/javascript">
$(function(){
	$("a:first",".ui-navbar").replaceWith('<a href="#popupNested" class="ui-link ui-btn" data-rel="popup" class="" data-transition="pop">New</a>');

    /*$("#content").change(function() {
        alert();
    });*/
    $(".container").delegate("input,select,textarea","change",function(){
            communication_auto_save();
        });
    $("#find-record").text("Send Email").attr("href","<?php echo M_URL.'/'.$controller.'/send_email'; ?>");

    })
    function after_choose_contacts(id, name, key){
        $("#CommunicationContactTo").val(name);
        $("#CommunicationContactToId").val(id);
        backToMain();
        communication_auto_save();
    }
    function communication_auto_save(){
        loading("Saving...");
        $.ajax({
            url:"<?php echo M_URL.'/'.$controller; ?>/auto_save",
            type:"POST",
            data:$(".<?php echo $controller; ?>_form_auto_save","#main-page").serialize()+"&data[Communication][content]=" + $("#content").val() +"&data[Communication][internal_notes]=" + $("#internal_notes").val(),
            success: function(result){
                $.mobile.loading( 'hide' );
                if(result!='ok')
                    alerts("Error",result);
            }
        });
    }
</script>
<?php echo $this->element('js'); ?>
