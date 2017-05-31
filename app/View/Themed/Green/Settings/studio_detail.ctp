<style>
  .field_header{
		width: 97%;
		padding-left: 3%;
		height: 25px;
		float: left;
		display: table;
		background: #797979;
		color: white;
		line-height: 25px;
		font-size: 14px;
	}
	.field_item_delete{
		background:#fff;
		border:1px solid #ddd;
		float:right;
		cursor:pointer;
		padding:2px 6px 2px 6px;
		margin:-3px 0 0 10%;
		line-height:16px;
		font-size:14px;
		font-family: Verdana, Geneva, sans-serif;
		color:#ddd;
		position: absolute;
		z-index:2;
	}
	.field_item_delete:hover{
		color:#444;
	}
	.group_div{
		margin: 1%;
		padding: 1%;
		float: left;
		background: #fff;
		min-height: 250px;
		float: left;
		display: table;
		list-style:none;
	}
	.panel_div{
		margin: 0% 1% ;
		padding: 0.5%;
		background: #fff;
		height:100px;
		float: left;
		display: table;
		list-style:none;
	}
	.layout_highlight{
		background:#FFC;
	}
	.drop_and_drap_txt{
		display: inline-block;
		width: 100%;
		padding: 0px 10px;
	}
	.label{
	    font-weight: bold;
	    font-size: 12px;
	    cursor: default;
	}
	.k-window {
	 	padding-bottom: 0 !important;
	}
	#extra_field_list .delete{
		float: right;
		z-index: 100;
		color: red;
		font-weight: 900;
		padding-bottom: 5px;
		margin-top: -19px;
		margin-right: -16px;
		width: 40px;
		text-align: center;
	}
	#extra_field_list .delete:hover{
		text-decoration: none;
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
</style>
<button id="add-new-field">Add</button>
<button id="view-subtab">View Sub Tab</button>
<div id="field_list">
	<div class="field_header">Field list</div>
	<div>
	    <ul class="field_list" id="extra_field_list">
	    	<?php
	    		if( ! empty($arr_layout['group_extra'])  ) {
	    			foreach($arr_layout['group_extra']['panel_extra'] as $field => $data){
	    	?>
            <li class="field_item ui-state-default" style="width: 200px;">
            	<input type="hidden" name="data[panel_extra][<?php echo $field;?>][value]" value="<?php echo $data['name'] ?>" />
            	<input type="hidden" name="data[panel_extra][<?php echo $field;?>][belong_to]" value="panel_extra" />
                <span class="field_item_text drop_and_drap_txt" ><?php echo $data['name']; ?></span>
            </li>
            <?php
            		}
            	}
            	unset($arr_layout['group_extra']);
            ?>
	    </ul>
	</div>
</div>
<div id="notifyTop"></div>
<form id="save_layout_form">
    <div class="field_header" style="margin-top:20px;">Layout</div>
    <div>
        <ul class="field_list" id="layout_struct">
            <?php foreach($arr_layout as $group => $panel){ ?>
                <li class="group_div" id="<?php echo $group;?>" style=" width:<?php if($group=='group_1') echo '20';else echo '70';?>%;">
                    <ul >
                    	<?php $i = 0; ?>
                        <?php foreach($panel as $panel_name => $arr_field){ ?>
                            <li>
                                <ul  id="<?php echo $panel_name;?>"
                                	<?php
                                		$is_address = 0;
                                		if(isset($arr_field['setup']['config']['blocktype']) && $arr_field['setup']['config']['blocktype']=='address'){
                                			$is_address = 1;
                                		   	if(count($arr_field) > 3)
                                				echo 'style="width: '.((count($arr_field)-1)*150).'px"';
                                		}
                                		else if($group!='group_1') echo 'style="width: 25%"';
                                	?>
                                		<?php if(!$is_address && $panel_name != 'panel_5'){ ?>class="panel_div"<?php } ?>
                                		<?php if($panel_name == 'panel_5'){ ?>class="hidden"<?php } ?>
                                	>
                            		 <?php foreach($arr_field as $field => $data){ ?>
	                            		 <?php
											if(in_array($field, array('products'))
												|| strpos($field, 'none') !== false
											   	|| strpos($field, 'field') !== false
											   	|| strpos($field, 'is_') !== false
											   	|| strpos($field, 'sum_') !== false
											   	|| strpos($field, '_amount') !== false
											   	|| strpos($field, '_number') !== false
											   	|| strpos($field, 'rfq') !== false
												|| (isset($data['config']['type']) && $data['config']['type'] =='hidden')
												|| (isset($data['config']['element_input']) && strpos($data['config']['element_input'], 'jthidden')!== false)
												|| $field == 'setup'
												){
	                            		 ?>
                            		 	<input type="hidden" name="data[<?php echo $panel_name; ?>][<?php echo $field;?>]" value="<?php echo $data['name'] ?>" />
                            		 	<?php  continue; } ?>
                            		 	<li class="field_item ui-state-default" style="width: 200px;<?php if($is_address) echo 'height: 200px;' ?>">
							                <input type="hidden" name="data[<?php echo $panel_name; ?>][<?php echo $field;?>][value]" value="<?php echo $data['name'];?>" />
                            		 		<input type="hidden" name="data[<?php echo $panel_name; ?>][<?php echo $field;?>][belong_to]" value="<?php echo $panel_name; ?>" />
							                <span class="field_item_text drop_and_drap_txt" ><?php echo $data['name']; ?></span>
							            </li>
                            		 <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
        </ul>
    </div>
    <button id="save_layout" style="margin-top: 5px;">Save layout</button>
</form>
<script type="text/javascript">
	$(function(){
		// $(".field_item","#extra_field_list").hover(
		//     function(){
		// 		var html = '<a class="delete" href="javascript:void(0)"><span>X</span></a>';
		// 		$(this).append(html);
		// 	},function(){
		// 		$("a.delete",this).remove();
		// });
		$("#view-subtab").click(function(){
			$.ajax({
				url:'<?php echo URL; ?>/settings/studio_view_subtab/<?php echo $module_name; ?>',
				success: function(html){
					$("div#list_detail").html(html);
				}
			});
		});
		$("#extra_field_list").on("click",".delete",function(){
			$.ajax({
				url:  '<?php echo URL ?>/settings/delete_new_field/<?php echo $module_name; ?>',
				type: 'POST',
				data: $("input",$(this).parent()).serialize(),
				success: function(result){
					if(result == "ok")
						$("a.active",".nav_setup").click();
					else
						alerts("Message",result);
				}
			});
		});
		$("#save_layout").click(function(){
			$("notifyTop").html('Processing...');
			var empty = false;
			if(!empty){
				$.ajax({
					url:  '<?php echo URL ?>/settings/save_layout/<?php echo $module_name; ?>',
					type: 'POST',
					data: $("#save_layout_form").serialize(),
					success: function(result){
						if(result!='ok')
							alerts('Message',result);
						else{
							notifyTop('Saved!');
							$("a.active",".nav_setup").click();
						}
					}
				});
			}
		});
		var extra_field_list = $("#extra_field_list");
		var panel_div = $(".panel_div");
		$( panel_div ).sortable({
		    connectWith: panel_div,
		    start: function(event,ui){

		    },
		    stop: function(event,ui){
		    	var old_parent_id = $(event.target).attr("id");
		    	var parent_id = $(ui.item).parent().attr("id");
		    	$("input[type=hidden]",$(ui.item)[0]).each(function(){
			    	var name = $(this).attr("name");
			    	name = name.replace(old_parent_id,parent_id);
		    		$(this).attr("name",name);
		    	});
		    }
	    }).disableSelection();
	    $( panel_div ).sortable({
		    connectWith: extra_field_list,
		    start: function(event,ui){

		    },
		    stop: function(event,ui){
		    	var old_parent_id = $(event.target).attr("id");
		    	var parent_id = 'panel_extra';
		    	$("input[type=hidden]",$(ui.item)[0]).each(function(){
			    	var name = $(this).attr("name");
			    	name = name.replace(old_parent_id,parent_id);
		    		$(this).attr("name",name);
		    	});
		    	$.ajax({
		    		url : "<?php echo URL.'/settings/apend_extra_field/'.$module_name ?>",
		    		type: "POST",
		    		data: $("input",ui.item).serialize(),
		    		success: function(result){
		    			if(result == "ok")
							$("a.active",".nav_setup").click();
						else
							alerts("Message",result);
		    		}
		    	})
		    }
	    }).disableSelection();
	    $( extra_field_list ).sortable({
		    connectWith: panel_div,
		    stop: function(event,ui){
		   		$("a.delete",panel_div).remove();
	   			var old_parent_id = 'panel_extra';
		    	var parent_id = $(ui.item).parent().attr("id");
		    	$("input[type=hidden]",$(ui.item)[0]).each(function(){
			    	var name = $(this).attr("name");
			    	name = name.replace(old_parent_id,parent_id);
		    		$(this).attr("name",name);
		    	});
		    }
	    }).disableSelection();
		$("#add-new-field").click(function(){
			openFieldInfo();
		});
		$(".field_item_text",".panel_div").hover(function(){
			$("a.delete",this).remove();
		});
		var open = false;
		$(".field_item_text").dblclick(function(){
			if(open)
				return false;
			open = true;
			$.ajax({
				url: "<?php echo URL.'/settings/get_field_info/'.$module_name; ?>",
				type: "POST",
				data: $("input",$(this).parent()).serialize(),
				success: function(result){
					var isJSON = true;
			       	try{
			            var result = $.parseJSON(result);
			        } catch(err){
			            isJSON = false;
			        }
			        if(isJSON){
			        	openFieldInfo(result)
			        } else
						alerts("Message",result);
					open = false;
				}
			})
		});
	});
	function notifyTop(html){
	    if($.trim(html) != "" )
	        $("#notifyTop").html(html).fadeIn(600).append('<div id="notifyTopsub"><a href="javascript:void(0)" onclick="$(\'#notifyTop\').fadeOut()">Hide</a></div>');
	    	setTimeout(function(){
	    		$("#notifyTop").fadeOut(600);
	    	},2000);
	}
	function ucfirst(strs){
		return strs.charAt(0).toUpperCase() + strs.slice(1);
	}
	function openFieldInfo(object){
		if( $("#new-field-content" ).attr("id") == undefined ){
			var field_type = <?php echo $field_type ?>;
			var html = '<div id="new-field-content" >';
					html +=	   '<div class="jt_box" style=" width:100%;">';
					html +=	      '<div class="jt_box_line">';
					html +=	         '<div class=" jt_box_label " style=" width:25%;height: 25px"></div>';
					html +=	         '<div class="jt_box_field" id="message" style=" width:71%">';
					html += 		 '</div>';
					html +=	      '</div>';
			if(object == undefined){

					html +=	      '<div class="jt_box_line">';
					html +=	         '<div class=" jt_box_label " style=" width:25%;height: 25px;">Type</div>';
					html +=	         '<div class="jt_box_field " style=" width:71%">';
					html +=				'<input name="type" id="type" class="input_1 float_left" type="text" value="">';
					html += 		 '</div>';
					html +=	      '</div>';
					html +=	      '<div id="field-type-content">';
					var obj = field_type["text"];
					for(var i in obj){
						html +=	      '<div class="jt_box_line">';
						html +=	         '<div class=" jt_box_label " style=" width:25%;height: 25px;">'+ucfirst(obj[i])+'</div>';
						html +=	         '<div class="jt_box_field " style=" width:71%">';
						html +=				'<input name="'+obj[i]+'" id="'+obj[i]+'" class="input_1 float_left" type="text" value="">';
						html += 		 '</div>';
						html +=	      '</div>';
					}
					html +=	      '</div>';
				} else {
					object['old_key'] = object['key'];
					html +=	      '<div class="jt_box_line">';
					html +=	         '<div class=" jt_box_label " style=" width:25%;height: 25px;">Type</div>';
					html +=	         '<div class="jt_box_field " style=" width:71%">';
					html +=				'<input name="type" id="type" class="input_1 float_left" type="text" value="'+object["type"]+'">';
					html += 		 '</div>';
					html +=	      '</div>';
					html +=	      '<div id="field-type-content">';
					delete(object["type"]);
					for(var i in object){
						isJSON = true;
						try{
							var subObj = object[i];
							subObj = $.parseJSON(subObj);
						} catch(e){
							isJSON = false;
						}
						html +=	      '<div class="jt_box_line">';
						html +=	         '<div class=" jt_box_label " style=" width:25%;height: 25px;">'+( i == "old_key" ? '' : ucfirst(i))+'</div>';
						html +=	         '<div class="jt_box_field " style=" width:71%">';
						if(!isJSON)
							html +=				'<input name="'+i+'" id="'+i+'" class="input_1 float_left" type="'+( i == "old_key" ? "hidden" : "text")+'" value="'+object[i]+'">';
						html += 		 '</div>';
						html +=	      '</div>';
						if(isJSON){
							for(var j in subObj){
								html +=	      '<div class="jt_box_line">';
								html +=	         '<div class=" jt_box_label " style=" width:25%;height: 25px;">'+ucfirst(j)+'</div>';
								html +=	         '<div class="jt_box_field " style=" width:25%">';
								html +=				'<input name="'+j+'" id="'+j+'" class="input_1 float_left" type="text" value="'+subObj[j]+'">';
								html += 		 '</div>';
								html +=	      '</div>';
							}
						}
					}
					html +=	      '</div>';
				}
				html +=	      '<div class="jt_box_line">';
					html +=	         '<div class=" jt_box_label " style=" width:25%;height: 65px"></div>';
					html +=	         '<div class="jt_box_field " style=" width:71%">';
					html += '<input style="margin-top:2%" type="button" class="jt_confirms_window_cancel" id="cancel" value=" Cancel " /><input style="margin-top:2%" type="button" class="jt_confirms_window_ok" value=" Ok " id="ok" />';
					html += 		 '</div>';
					html +=	      '</div>';
					html +=	   '</div>';
					html +=	'</div>';
			$('<div id="new-field-content" style="width: 99%; padding: 0px; overflow: auto;">'+html+'</div>').appendTo("body");
		}
		$("#type").combobox({'text':'Text','select':'Select','checkbox':'Checkbox','relationship':'Relationship'});
		$("#type").change(function(){
		 	var html = "";
		 	var value = $(this).val();
		 	value = value.toLowerCase();
		 	var obj = field_type[value];
		 	for(var i in obj){
				html +=	      '<div class="jt_box_line">';
				html +=	         '<div class=" jt_box_label " style=" width:25%;height: 25px;">'+ucfirst(obj[i])+'</div>';
				html +=	         '<div class="jt_box_field " style=" width:71%">';
				html +=				'<input name="'+obj[i]+'" id="'+obj[i]+'" class="input_1 float_left" type="text" value="">';
				html += 		 '</div>';
				html +=	      '</div>';
		 	}
		 	$("#field-type-content").html(html);
		});
		$("#key").keypress(function(){
			var value = $(this).val();
			value = value.replace(/[^a-zA-Z0-9_ ]/g, "");
			value = value.replace(" ", "_");
			value = value.toLowerCase();
			$(this).val(value);
		}).change(function(){
			var value = $(this).val();
			value = value.replace(/[^a-zA-Z0-9_ ]/g, "");
			value = value.replace(" ", "_");
			value = value.toLowerCase();
			$(this).val(value);
		});
		var field_content = $("#new-field-content");
		field_content.kendoWindow({
			width: "355px",
			title: "New Field",
			visible: false,
			modal: true,
			activate: function(){

			}
		});
		field_content.data("kendoWindow").center();
		field_content.data("kendoWindow").open();
		$("#ok").unbind("click");
		$("#ok").click(function() {
			$("#message","#new-field-content").html("");
			var blank = false;
			$("input[type=text]","#new-field-content").each(function(){
				if($(this).val() == ""){
					blank = true;
					return;
				}
			});
			if(blank){
				$("#message","#new-field-content").html("All info must not be empty");
				return false;
			}
			$.ajax({
				url : "<?php echo URL.'/settings/add_new_field/'.$module_name;?>",
				type: "POST",
				data: $("input","#new-field-content").serialize(),
				success: function(result){
					if(result == "ok")
						$("a.active",".nav_setup").click();
					else
						alerts("Message",result);
				}
			});
	       	field_content.data("kendoWindow").destroy();
		});
		$('#cancel').click(function() {
	       	field_content.data("kendoWindow").destroy();
	    });
	    $("input[type=text]","#new-field-content").focus(function(){
	    	$("#message","#new-field-content").html("");
	    });
	}
</script>