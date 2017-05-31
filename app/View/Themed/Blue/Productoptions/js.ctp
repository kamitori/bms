<script type="text/javascript">
$(function(){
	//default focus
	$("#name").focus();

	$(".link_to_parent").click(function(){
		var ids = $('#parent_id').val();
		window.location.assign("<?php echo URL;?>/productoptions/entry/"+ids);
	});

	//Link Sub Tab
	$(".ul_tab li").click(function() {

		var val = $(this).attr("id");
		$(".ul_tab li").removeClass("active");
		$("#"+val).addClass("active");
		ajax_note_set("Loading...");
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/sub_tab/'+val+'/<?php echo $iditem;?>',
			success: function(html){
				$("#load_subtab").stop().html(html);
				ajax_note("");
			}
		});

	});

	// Xu ly save, update
	$("form input,form select").change(function() {
		var fieldname = $(this).attr("name");
		var fieldid = $(this).attr("id");
		var fieldtype = $(this).attr("type");
			modulename = 'mongo_id';
		var ids = $("#"+modulename).val();
		var values = $(this).val();
		var func = ''; var titles = new Array();

		if(ids!='')
			func = 'update'; //add,update
		else
			func = 'add';
		if(fieldtype=='checkbox'){
			if($(this).is(':checked'))
				values = 1;
			else
				values = 0;
		}

	if(fieldname=='chanfield')
		$(".jt_ajax_note").html("");
	else{
		$(".jt_ajax_note").html("Saving...       ");

		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/ajax_save',
			type:"POST",
			data: {field:fieldname,value:values,func:func,ids:ids},
			success: function(text_return){ //alert(text_return);
				text_return = text_return.split("||");
				$("#"+modulename).val(text_return[0]);

				// change tittle, thay đổi tiêu đề của items
				<?php foreach($arr_settings['title_field'] as $ks=>$vls){?>
					titles[<?php echo $ks;?>] = '<?php echo $vls;?>';
				<?php }?>
				if(titles.indexOf(fieldname)!=-1){
					$("#md_"+fieldname).html(values);
				}
				ajax_note("Saving...Saved !");
			}
		});
	}


	});

	$(".jt_ajax_note").html('');

});



//Hien thong bao truoc khi ajax
function ajax_note_set(txt){
	$(".jt_ajax_note").stop().fadeIn(1);
	$(".jt_ajax_note").css("color","red");
	$(".jt_ajax_note").html(txt);
}
//Hien thong bao sau khi ajax thanh cong
function ajax_note(txt){
	$(".jt_ajax_note").stop().html(txt);
	$(".jt_ajax_note").fadeOut(1500, function() {
		$(".jt_ajax_note").html("");
		$(".jt_ajax_note").fadeIn(100);
	});
}


function after_choose_productoptions(ids,names,keys){
	if(keys=='parent'){
		$("#parent_id").val(ids);
		$("#parent").val(names);
		save_field('parent_id',ids,'');
		save_field('parent',names,'');
	}
}


//save main fields
function save_field(field,value,idmongo){
	if(field!='' && field!=undefined && value!=undefined){
		var func,ids;
		if(idmongo=='')
			ids = $("#mongo_id").val();
		else
			ids = idmongo;
		if(ids!='')
			func = 'update';
		else
			func = 'add';
		//alert(field+'='+value+'='+func+'='+ids);
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/ajax_save',
			type:"POST",
			data: {field:field,value:value,func:func,ids:ids},
			success: function(text_return){
				text_return = text_return.split("||");
				$("#mongo_id").val(text_return[0]);
				ajax_note("Saving...Saved !");
				$(".k-window").fadeOut('slow', function() {
					return text_return[1];
				 });
			}
		});
	}
}


function ajax_delete(ports,iditem){
	if (confirm('Are you sure you want to delete this record?')) {
		//remove line
		var boxname = $("#"+iditem).attr("rev");
		var ids = $("#"+iditem).attr("rel");
		var ix = $("#container_"+boxname+" .ul_mag").index($("#"+iditem).parent().parent().parent());
		$("#"+iditem).parent().parent().parent().animate({
		  opacity:'0.1',
		  height:'1px'
		},500,function(){$(this).remove();});

		ix = parseInt(ix);
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/'+ports+'/'+ids,
			type:"POST",
			success: function(txt){
				change_bg(ix,boxname);
				reload_subtab(subtabname);
				ajax_note(" Deleted !");
			}
		});
	}
}

function change_bg(index,boxname){
	var sum = $("#container_"+boxname+" .ul_mag").length;
	sum = parseInt(sum);
	var strs='';var lengs = 0; var testing='';
	var i=0; index = parseInt(index);
	for(i=index;i<=sum+1;i++){
		strs =$("#container_"+boxname+" .ul_mag:eq("+i+")").attr('class');
		if(strs){
			strs = strs.split(" ");
			lengs = parseInt(strs.length);
			strs = strs[lengs-1];
			testing+= i+strs+"\n";
			if(strs=='bg1'){
				$("#container_"+boxname+" .ul_mag:eq("+i+")").addClass('bg2');
				$("#container_"+boxname+" .ul_mag:eq("+i+")").removeClass('bg1');
			}else{
				$("#container_"+boxname+" .ul_mag:eq("+i+")").addClass('bg1');
				$("#container_"+boxname+" .ul_mag:eq("+i+")").removeClass('bg2');
			}
		}

	}
}


function reload_subtab(subtabname){
	$(".ul_tab li").removeClass("active");
	$("#"+subtabname).addClass("active");
	$.ajax({
		url: '<?php echo URL.'/'.$controller;?>/sub_tab/'+subtabname+'/<?php echo $iditem;?>',
		success: function(html){
			$("#load_subtab").stop().html(html);
			ajax_note("");
		}
	});
}


function reload_box(boxname){
	$.ajax({
		url: '<?php echo URL.'/'.$controller;?>/reload_box',
		type:"POST",
		data: {boxname:boxname},
		success: function(text_return){
			$("#container_"+boxname).parent().html(text_return);
		}
	});
}


function save_option(opname,arr_value,opid,isreload,subtab,keys){
	if(opname != undefined ){
		if(keys == undefined )
			keys  = 'update';
		var arr = {
				'keys' : keys,
				'opname' : opname,
				'value_object' : arr_value,
				'opid' : opid
			};
		var jsonString = JSON.stringify(arr);
		//ajax_note_set(keys+"=\n"+opname+"=\n"+value_str+"=\n"+opid);
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/save_option',
			type:"POST",
			data: {arr:jsonString},
			success: function(rtu){
				$(".k-window").fadeOut('slow', function() {
					if( isreload != undefined && isreload==1 )
						reload_subtab(subtab);
					else if( isreload != undefined && isreload==2)
						reload_box(opname);
				 });
			}
		});

	}else
		return '';
}

// Scrollbar
function Scrollbar(divname_scroll){
	$("#" + divname_scroll).mCustomScrollbar({
		scrollButtons:{
			enable:false
		}
	});

}

</script>