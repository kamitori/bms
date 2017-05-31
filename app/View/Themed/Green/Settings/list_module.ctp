<style type="text/css">
.label{
    font-weight: bold;
    font-size: 12px;
    cursor: default;
}
#notifyTop {
    display: none;
    position: fixed;
    top: 101px;
    left: 30%;
    z-index: 9999;
    background-color: #852020;
    color: #FFF;
    border-radius: 3px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    padding: 6px 28px 6px 10px;
    font-weight: bold;
    width: 40%;
    text-align: center;
    overflow: hidden;
    line-height: 1.3;
}
#notifyTop .flash_message{
    margin-right: 40px;
}
#notifyTop #notifyTopsub{
    position: absolute;
    right: 4px;
    top: 6px;
    text-decoration: underline;
}#notifyTop #notifyTopsub a:hover{
    color: #D1B9B9;
}
#list li a{
	color:#333;
}
</style>
<div id="notifyTop"></div>
<div class="tab_1 full_width">
    <span class="title_block bo_ra1">
        <span class="fl_dent">
            <h4>Module studio</h4>
        </span>
        <a id="add_module" title="Add new list" href="javascript:void(0)">
				<span class="icon_down_tl top_f"></span>
		</a>


    </span>

    <form action="<?php echo URL ?>/settings/auto_create_module" id="auto_create_module_form" method="post">

	    <div id="system_module_list" style="height: 473px;overflow-y: auto;display:block;">
			<ul id="list">
				<?php foreach($list_module as $value){?>
					<li><a href="<?php echo URL?>/settings/studio/<?php echo $value;?>"><?php echo $value; ?></a></li>
				<?php }?>
			</ul>
	    </div>


	     <div id="system_email_content" style="height: 473px;overflow-y: auto;display:none">
	        <div class="jt_box" style=" width:100%;">
	            <div class="jt_box_line">
	                <div class=" jt_box_label " style=" width:25%;"><span class="label">Module name</span></div>
	                <div class="jt_box_field " style=" width:71%"><input name="module_name"   class="input_1 float_left  " type="text" value="<?php //echo $email['email_name']; ?>"></div>
	            </div>
	            <div class="jt_box_line">
	                <div class=" jt_box_label " style=" width:25%;"><span class="label" >Model name</span></div>
	                <div class="jt_box_field " style=" width:71%"><input name="model_name" id="model_name" class="input_1 float_left" type="text" value="<?php //echo $email['password']; ?>"></div>
	            </div>
	            <div class="jt_box_line">
	                <div class=" jt_box_label " style=" width:25%;"><span class="label">Controller name</span></div>
	                <div class="jt_box_field " style=" width:71%"><input name="controller_name" id="controller_name" class="input_1 float_left  " type="text" value="<?php //echo $email['username']; ?>"></div>
	            </div>
	            <input type="hidden" name="ok" value="ok" />
	            <div class="jt_box_line">
	                <div class=" jt_box_label " style=" width:25%;height: 401px;"><span class="label" ></span></div>
	                <div class="jt_box_field " style=" width:71%">
	                	<input id="module_submit" class="jt_confirms_window_ok" style="float: left;margin-top: 20px;" type='button' name='ok' value='OK' />
	                	<input id="module_cancel" class="jt_confirms_window_ok" style="float: left;margin-top: 20px;" type='button' name='ok' value='Cancel' />
	                </div>
	            </div>
	        </div>
	    </div>

    </form>

    <span class="title_block bo_ra2">
    <span class="float_left bt_block"></span>
    </span>
</div>

<script type="text/javascript">
$(function(){
	$("#add_module").click(function(){
    	$("#system_module_list").css("display","none");
    	$("#system_email_content").css("display","block");
 	});
	$("#module_cancel").click(function(){
		$("#system_module_list").css("display","block");
		$("#system_email_content").css("display","none");
	});

    $("#module_submit").click(function(){
        $("#notifyTop").html('Processing...');



        var empty = false;
        $("input[type=text]","#system_email_content").each(function(){
            if($(this).val().trim()==''){
                alerts('Message','Please enter all valid infomation.');
                empty = true;
                return false;
            }
        });
        if(!empty){
            $.ajax({
                url: '<?php echo URL ?>/settings/auto_create_module',
                type: 'POST',
                data: $("#auto_create_module_form").serialize(),
                success: function(result){
                    if(result!='ok')
                        alerts('Message',result);
                    else{
                        notifyTop('Saved!');
                        $("input[type=text]","#auto_create_module_form").each(function(){
                            $(this).val('');
                        });
                    }
                }
            });
        }
    });
    $("#model_name").keyup(function(){
        var value = $(this).val().trim();
        value = value.replace(' ','').replace(/[^\w\s]/gi, '').toLowerCase();
        value = ucfirst(value);
        $(this).val(value);
        $("#controller_name").val(value);
    });
    $("#model_name").change(function(){
        var value = $(this).val();
        var tmp_value = value.toUpperCase();
        var last_char = tmp_value.substring(tmp_value.length - 1);
        if(last_char=='S' || last_char=='X')
            value += 'es';
        else if(last_char=='Y')
            value = value.replaceAt(value.length - 1, 'ies');
        else
            value += 's';
        $("#controller_name").val(value);
    });
})
String.prototype.replaceAt=function(index, character) {
    return this.substr(0, index) + character + this.substr(index+character.length);
}
function ucfirst(string){
    return string.charAt(0).toUpperCase() + string.slice(1);
}
function notifyTop(html){
    if($.trim(html) != "" )
        $("#notifyTop").html(html).fadeIn(600).append('<div id="notifyTopsub"><a href="javascript:void(0)" onclick="$(\'#notifyTop\').fadeOut()">Hide</a></div>');
}

</script>