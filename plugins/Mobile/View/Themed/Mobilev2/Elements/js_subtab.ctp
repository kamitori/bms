<?php if(!isset($isOptions)){ ?>
<a id="loadmore" data-role="button">Load more</a>
<input type="hidden" id="offsetRecord" value="0" />
<?php } ?>
<script type="text/javascript">
	var length = $("li[data-role='collapsible']","#list-view").length;
	if( !length ||  length < 10){
		$("#loadmore").remove();
		$("#offsetRecord").remove();
	}
	var arrData = <?php echo json_encode($arr_data); ?>;
	<?php if(isset($options)&&is_array($options)){ ?>
	var options = <?php echo json_encode($options); ?>;
	<?php } ?>
	var header = arrData.header;
	var field = arrData.field;
	var headerString = $.trim(header.info);
	var headerArr = headerString.split(" ");
	var headerValueString = $.trim(header.link_to_entry_value);
	var headerValueArr = headerValueString.split(" ");
	$("#loadmore").click(function(){
		var offset = $("#offsetRecord").val();
		offset = eval(offset)+10;
		$.ajax({
			type : "POST",
			data : {offset: offset},
			success : function(result){
				var result = $.parseJSON(result);
				if(result.empty != undefined){
					$("#loadmore").attr("disbled",true).hide();
					$("#offsetRecord").attr("disbled",true).hide();
				} else {
					var data = result.data;
					$("#list-view").append(getHtml(data));
					$("#list-view").listview().trigger("create");
					$("#offsetRecord").val(offset);
				}
			}
		});
	});
	$("#add-new-record").click(function(){
		$.ajax({
			type : "POST",
			data : {add : true},
			success : function(result){
				if(result.indexOf("<?php echo M_URL; ?>/") != -1){	// mobile
					window.location.assign(result);
					return false;
				}
				var result = $.parseJSON(result);
				if( !length ){
					$("#no-data").remove();
				}
				$("#list-view").prepend(getHtml(result));
				$("#list-view").listview().trigger("create");
				$("#list-view").stop().animate({ scrollTop : 0 }, 500);
			}
		})
	});
	function getHtml(data)
	{
		var html = "";
		for(var i in data){
			var subData = data[i];
			if($("#list-"+subData._id,"#list-view").attr("id")!=undefined) continue;
			var curHeader = headerString;
			for(var k in headerArr){
				if(subData[headerArr[k]] == undefined)
					subData[headerArr[k]] = headerArr[k];
				curHeader = curHeader.replace(headerArr[k],subData[headerArr[k]]);
			}
			html += '<li id="list-'+subData._id+'" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">';
			if(header.link_to_entry != undefined){
				var href = "";
				if(header.link_to_entry != "")
					href = 'href="'+header.link_to_entry+subData._id+'"';
				var curHeaderValue = headerValueString;
				for(var k in headerValueArr){
					if(subData[headerValueArr[k]] == undefined)
						subData[headerValueArr[k]] = headerValueArr[k];
					curHeaderValue = curHeaderValue.replace(headerValueArr[k],subData[headerValueArr[k]]);
				}
				html += '<h2><div class="ui-block-a" style="width:30%"><a class="link-to-entry" '+href+' >'+curHeaderValue+'</a></div>';
			} else {
				html += '<h2><div class="ui-block-a" style="width:30%">&nbsp;</div>';
			}
			html += '<div class="ui-block-b" style="width:60%">'+curHeader+'</div></h2>';
			html += '<ul data-role="listview" data-theme="b">';
			for(var j in field){
				html += getField(field[j], j, subData[j], subData._id);
			}
			html += ' <li><a href="#popupDialog" class="callDelete" data-id="'+subData._id+'" data-rel="popup" data-position-to="window" data-transition="pop" class="ui-btn ui-shadow ui-btn-inline ui-icon-delete ui-btn-icon-left ui-btn-b">Delete</a></li>';
			html += '</ul>';
			html += '</li>';
		}
		return html;
	}
	function getField(fieldData, key, value, id)
	{
		if(value == undefined)
			value = "";
		var html = '<li>';
		html += '<div class="ui-block-a" style="width: 30%"><b>'+fieldData.label+'</b></div>';
		switch(fieldData.type){
			case '':
				html += '<div class="ui-block-b" style="width:70%">'+value+'</div>';
				break;
			case 'text':
				html +=  '<div class="ui-block-b" style="width:70%"><input type="text" name="'+key+'" id="'+key+'_'+id+'" data-theme="a" value="'+value+'"  /></div>';
				break;
			case 'checkbox':
				var check = "";
				if(value == 1)
					check = "checked";
				html += '<div class="ui-block-b ui-checkbox" style="width:70%"><label for="checkbox-enhanced" class="ui-btn ui-corner-all ui-btn-inherit ui-btn-icon-left" >Check</label><input name="'+key+'" id="'+key+'_'+id+'" data-enhanced="true" type="checkbox" '+check+'></div>';
				break;
			case 'select':
				var optionsString = fieldData.options;
				if(optionsString.indexOf("@") != -1){
					var tmpOptions = optionsString.split("@");
					var extraKey = tmpOptions[1];
					tmpOptions = tmpOptions[0];
					var curOption = options[tmpOptions];
					curOption = curOption[extraKey];
				}
				else
					var curOption = options[fieldData.options];
				html += '<div class="ui-block-b" style="width:70%"><select name="'+key+'" id="'+key+'_'+id+'">';
				html += '<option value=""></option>';
				for(var i in curOption){
					isSelected = '';
					if( $.trim(i) == $.trim(value) )
						isSelected = 'selected="selected"';
					html += '<option value="'+i+'" '+isSelected+'>'+curOption[i]+'</option>';
				}
				html += '</select></div>';
				break;
			default:
				html += '<div class="ui-block-b" style="width:70%">'+value+'</div>';
				break;
		}
		html += '</li>';
		return html;
	}
	function saveData(object){
		var field = object.field;
		var value = object.value;
		var ids = object.ids;
		var controller = object.controller;
		var callBack = object.callBack;
	    if(controller ==undefined || controller =='')
	        controller = '<?php echo $controller;?>';
	    $.ajax({
	        url: '<?php echo M_URL;?>/'+controller+'/save_data',
	        type:"POST",
	        data: {field:field,value:value,ids:ids},
	        success: function(result){
	            if(typeof callBack == "function")
	                callBack(result);
	        }
	    });
	}
	function saveOption(object){
		var opname = object.opname;
	    var key = object.key;
	    var data = object.data;
	    var controller = object.controller;
	    var callBack = object.callBack;
	    if(controller ==undefined || controller =='')
	        controller = '<?php echo $controller;?>';
	    var dataSend = {controller:controller,opname:opname,key:key,data:data};
	    if(object.extraField != undefined)
	        dataSend["extra_field"] = object.extraField;
	    $.ajax({
	        url: '<?php echo M_URL;?>/'+controller+'/save_option',
	        type:"POST",
	        data: dataSend,
	        dataType: "json",
	        success: function(result){
	            if(typeof callBack == "function")
	                callBack(result);
	        }
	    });
	}

</script>