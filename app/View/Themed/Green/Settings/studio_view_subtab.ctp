<style type="text/css">
	fieldset{
		border: 2px solid #fff !important;
		padding: 18px;
		margin: 10px;
	}
	legend{
		border: 2px solid #fff !important;
		padding: 3px;
		cursor: pointer;
	}
	.k-window {
	 	padding-bottom: 0 !important;
	}
	.k-tabstrip-items .k-state-active{
		border-color: #949494 !important;
	}
	a:hover{
		text-decoration: none !important;
		-moz-user-select: none;
		-webkit-user-select: none;
		-ms-user-select: none;
	}
	.k-last > .k-link{
		size: 30px !important;
	}
	.k-tabstrip-items .k-state-active{
		border-color: #949494 !important;
	}
	.k-state-active, .k-state-active:hover, .k-active-filter, .k-tabstrip .k-state-active{
		background-color: #fff !important;
		border-color: #949494 !important;
	}
	.subtab, .subtab > .k-content{
		padding-top: 5px;
		float: left;
		-moz-user-select: none;
		-webkit-user-select: none;
		-ms-user-select: none;
	}
	.ui-state-highlight { height: 1.5em; line-height: 1.2em; }
</style>
<button id="return">Return</button>
<div class="subtab">
    <ul>
    	<?php
    		$i =0;
    		foreach($arr_relationship as $key => $value){
    			if(!isset($value['block']) || empty($value['block'])) continue;
    			if(!isset($value['name']) || $value['name'] == '') continue;
				foreach ($value['block'] as $key_subtab => $value_subtab) {
					if(empty($value_subtab))
						continue 2;
				}
    	?>
        <li <?php if($i == 0) {?>class="k-state-active"<?php } ?>>
           <?php echo $value['name']; ?>
           <input type="hidden" class="subtab-key" value="<?php echo $key; ?>" />
        </li>
        <?php $i++; } ?>
        <li>
           +
        </li>
    </ul>
    <?php
    	foreach($arr_relationship as $key => $value){
    		if(!isset($value['block'])) continue;
    		$continue = true;
    		foreach($value['block'] as $key_field => $field){
    			if(!empty($field)){
    				$continue = false;
    				break;
    			}
    		}
    		if($continue) continue;
    ?>
    <div class="each-tab">
		<button class="save-tab-layout">Save layout</button>
    	<div class="field_list" data-key="<?php echo $key?>">
    		<?php
    			foreach($value['block'] as $key_field => $field){
    			if(empty($field)) continue;
    			if(!isset($field['field'])) continue;
    		?>
    		<form class"field-form" id="form-<?php echo $key_field ?>" data-key="<?php echo $key?>" data-key-field="<?php echo $key_field ?>">
    			<fieldset>
    				<legend><?php echo $field['title'] ?></legend>
		    		<?php
		    			foreach($field['field'] as $key_data => $data){
		    				if(!isset($data['name'])) $data['name']  = '';
		    		?>
		            <div class="field_item ui-state-default" style="width: 200px;">
		            	<input type="hidden" name="data[<?php echo $key ?>][<?php echo $key_field; ?>][<?php echo $key_data ; ?>]" value="<?php echo $data['name'] ?>" />
		                <span class="field_item_text drop_and_drap_txt" ><?php echo $data['name']; ?></span>
		            </div>
		    		<?php } ?>
    			</fieldset>
    		</form>
    		<?php } ?>
	    </div>
    </div>
    <?php } ?>
    <div style="width: 96.3%; height: 150px; text-align: center; background-color: #c5c5c5 !important; color: #fff">
    	Create subTab
    </div>
</div>
<script type="text/javascript">
	$("#return").click(function(){
		$("a.active",".nav_setup").click();
	});
    $( ".field_list fieldset" ).sortable({
    	items: "div.ui-state-default"
    }).disableSelection();
    $( ".field_list" ).sortable({
    	connectWith: "form.field-form",
    }).disableSelection();
	$(".subtab").kendoTabStrip({
		select: selectSubtab,
       /* animation:  {
            open: {
                effects: "fadeIn"
            }
        }*/
    });
    $(".save-tab-layout").click(function(){
    	var div = $(this).next();
    	var data = $("input",div).serialize();
    	$.ajax({
    		url : "<?php echo URL.'/settings/studio_save_tab_layout/'.$module_name ?>",
    		type: "POST",
    		data: data,
    		success: function(result){
    			if(result != "ok")
    				alerts("Message",result);
    		}
    	});
    });
    $("legend","form").dblclick(function(){
    	var form = $(this).closest("form");
    	var key = form.attr("data-key");
    	var key_field = form.attr("data-key-field");
    	$.ajax({
    		url: "<?php echo URL.'/settings/studio_get_block_info/'.$module_name ?>",
    		type: "POST",
    		data: {key: key, key_field : key_field},
    		success: function(result){
    			result = $.parseJSON(result);
    			var div_id = "block-info";
    			var div_title = "Block info";
    			ok_callBack = function(div_id){
    				$("#message","#"+div_id).html("");
					var blank = false;
					$("input[type=text]","#"+div_id).each(function(){
						if($(this).val() == ""){
							blank = true;
							return;
						}
					});
					if(blank){
						$("#message","#"+div_id).html("All info must not be empty");
						return false;
					}
					var title = $("#title","#"+div_id).val();
					var data = $("input[type=text]","#"+div_id).serialize();
					data += "&key="+key+"&key_field="+key_field;
					$.ajax({
						url: "<?php echo URL.'/settings/studio_save_block_info/'.$module_name ?>",
						type: "POST",
						data: data,
						success: function(result){
							if(result != "ok")
								alerts("Message",result);
							else
								$("legend",form).text(title);
						}
					});
    			};
    			createPopup(div_id,div_title,result,ok_callBack);
    		}
    	});
    })
	$(".field_item_text ",".subtab").dblclick(function(){
		var span = $(this);
		var data = $("input",$(this).closest("div")).serialize();
		$.ajax({
			url: "<?php echo URL.'/settings/studio_get_field_info/'.$module_name ?>",
			type: "POST",
			data: data,
			success: function(result){
				result = $.parseJSON(result);
    			var div_id = "field-info";
    			var div_title = "Field info";
    			ok_callBack = function(div_id){
    				$("#message","#"+div_id).html("");
					var blank = false;
					$("input[type=text]","#"+div_id).each(function(){
						if($(this).val() == ""){
							blank = true;
							return;
						}
					});
					if(blank){
						$("#message","#"+div_id).html("All info must not be empty");
						return false;
					}
					var name = $("#name","#"+div_id).val();
					var childData = $("input[type=text]","#"+div_id).serialize();
					data = data.replace("data%5B","field%5B");
					childData += "&"+data;
					$.ajax({
						url: "<?php echo URL.'/settings/studio_save_field_info/'.$module_name ?>",
						type: "POST",
						data: childData,
						success: function(result){
							if(result != "ok")
								alerts("Message",result);
							else
								$(span).text(name);
						}
					});
    			};
    			createPopup(div_id,div_title,result,ok_callBack);
			}
		});
	});
    $(".subtab").on("dblclick",".k-link",function(){
    	var thisTab = $(this);
    	var text = thisTab.text().trim();
		if( text == "+")
			return false;
    	var obj_html = {"title":text};
    	var div_id = "change-title-subtab";
    	var div_title = "Change Title";
		var ok_callBack = function(div_id) {
			$("#message","#"+div_id).html("");
			if($("#title","#"+div_id).val().trim() == ""){
				$("#message","#"+div_id).html("Title must not be empty").css("color","red");
				$("#title","#"+div_id).val("").focus();
				return false;
			}
			var subtab_key = $(".subtab-key",$(".k-tab-on-top",".subtab"));
			var title = $("#title","#"+div_id).val();
			$.ajax({
				url : "<?php echo URL.'/settings/studio_change_relationship_name/'.$module_name ?>",
				type: "POST",
				data: {key : subtab_key.val(), name: title},
				success: function(result){
					if(result != "ok")
						alerts("Message",result);
					else
						$(".k-state-active > .k-link",".subtab").html(title+subtab_key[0].outerHTML);
				}
			});
		};
    	createPopup(div_id,div_title,obj_html,ok_callBack);
    });
    function selectSubtab(e)
    {
    	var tabStrip = $(".subtab").kendoTabStrip().data("kendoTabStrip");
    	// var text = $(e.item).find("> .k-link").text();
    	if($(e.item).hasClass("k-last")){
    		var obj_html = {"key":"","title":""};
    		var div_id = "create-subtab";
    		var div_title = "Create Subtab";
    		var ok_callBack = function(div_id){
    			$("#message","#"+div_id).html("");
				var blank = false;
				$("input[type=text]","#"+div_id).each(function(){
					if($(this).val() == ""){
						blank = true;
						return;
					}
				});
				if(blank){
					$("#message","#"+div_id).html("All info must not be empty");
					return false;
				}
		        var subtab_title = $("#title","#"+div_id).val();
		        var subtab_key = $("#key","#"+div_id).val();
				tabStrip.insertBefore({
		            text: subtab_title,
		            content: '<ul class="field_list"></ul>'
		        }, $(e.item));
		        var last_li = $(".k-last",".subtab").prev();
		        var html = subtab_title+'<input type="hidden" class="subtab-key" value="'+subtab_key+'" />';
		        $(".k-link",last_li).html(html);
		        var id = $(last_li).attr("aria-controls");
    			$("#"+id,".subtab").css("width","96.3%");
    		};
    		createPopup(div_id,div_title,obj_html,ok_callBack);
    	} else {
    		$(".k-content",".subtab").hide();
    		var id = $(e.item).attr("aria-controls");
    		$("#"+id,".subtab").hide();
    	}
    }
    function createPopup(div_id,div_title,obj_html,ok_callBack)
    {
    	if( $("#"+div_id ).attr("id") == undefined ){
			var html = '<div id="'+div_id+'" >';
				html +=	   '<div class="jt_box" style=" width:100%;">';
				html +=	      '<div class="jt_box_line">';
				html +=	         '<div class=" jt_box_label " style=" width:25%;height: 25px"></div>';
				html +=	         '<div class="jt_box_field" id="message" style=" width:71%">';
				html += 		 '</div>';
				html +=	      '</div>';
				for(var i in obj_html){
					isObject = true;
					if(isNaN(obj_html[i])){
						try{
							var subObj = obj_html[i];
							subObj = $.parseJSON(subObj);
						} catch(e){
							if($.type(obj_html[i]) == "object")
								var subObj = obj_html[i];
							else
								isObject = false;
						}
					} else {
						isObject = false;
					}
					html +=	      '<div class="jt_box_line">';
					html +=	         '<div class=" jt_box_label " style=" width:25%;height: 25px;">'+ucfirst(i)+'</div>';
					html +=	         '<div class="jt_box_field " style=" width:71%">';
					if(!isObject){
						html +=				'<input id="'+i+'" name="data['+i+']" class="input_1 float_left" type="text" value="'+obj_html[i]+'">';
					}
					html += 		 '</div>';
					html +=	      '</div>';
					if(isObject){
						for(var j in subObj){
							html +=	      '<div class="jt_box_line">';
							html +=	         '<div class=" jt_box_label " style=" width:25%;height: 25px;">'+ucfirst(j)+'</div>';
							html +=	         '<div class="jt_box_field " style=" width:25%">';
							html +=				'<input id="'+j+'" name="data['+i+']['+j+']" class="input_1 float_left" type="text" value="'+subObj[j]+'">';
							html += 		 '</div>';
							html +=	      '</div>';
						}
					}
				}
				html +=	      '<div class="jt_box_line">';
				html +=	         '<div class=" jt_box_label " style=" width:25%;height: 65px"></div>';
				html +=	         '<div class="jt_box_field " style=" width:71%">';
				html += '<input style="margin-top:2%" type="button" class="jt_confirms_window_cancel" id="cancel" value=" Cancel " /><input style="margin-top:2%" type="button" class="jt_confirms_window_ok" value=" Ok " id="ok" />';
				html += 		 '</div>';
				html +=	      '</div>';
				html +=	   '</div>';
				html +=	'</div>';
		}
		$('<div id="'+div_id+'" style="width: 99%; padding: 0px; overflow: auto;">'+html+'</div>').appendTo("body");
		var popup = $("#"+div_id);
		popup.kendoWindow({
			width: "355px",
			title: div_title,
			visible: false,
			modal: true,
		 	activate: function(){
                $(".k-window-actions").remove();
            }
		});
		popup.data("kendoWindow").center();
		popup.data("kendoWindow").open();
		$("#ok").unbind("click");
		$("#ok").click(function(){
			ok_callBack(div_id);
	       	popup.data("kendoWindow").destroy();
		});
		$('#cancel').click(function() {
	       	popup.data("kendoWindow").destroy();
	    });
    }
</script>
