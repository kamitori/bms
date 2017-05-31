<?php echo $this->element('js/js');?>
<script>
	(function($){

	})(jQuery);

	function delete_record(iditem){
		var str = 'Are you sure you want to delete this record?';
		confirms('Confirm delete', str,
				function(){
					var ids = iditem.replace("del_","");
					$("#"+iditem).parent().parent().parent().animate({
					  opacity:'0.1',
					  height:'1px'
					},500,function(){$(this).remove();});

					var ix = $('.ul_mag').index($("#"+iditem).parent().parent().parent());
					ix = parseInt(ix); //alert(ix);
					//changebg(ix);
					$.ajax({
						url: '<?php echo URL.'/'.$controller;?>/delete/'+ids,
						type:"POST",
						success: function(){
							changebg(ix);
						}
					});
				},function(){
					//else
					console.log('Cancel');
				});
	}

	function changebg(index){
		var sum = $("#lists_view_content ul").length;
		sum = parseInt(sum);console.log('tong'+sum);
		var strs='';var lengs = 0;
		for(var i=index+1;i<=sum+1;i++){
			strs =$(".ul_mag:nth-child("+i+")").attr('class');console.log(strs);
			if(strs.split("clear")!=undefined){
				strs = strs.split("clear");
				lengs = parseInt(strs.length);
				strs = $.trim(strs[lengs-1]);
			}
			if(strs=='bg1'){
				$(".ul_mag:nth-child("+i+")").addClass('bg2');
				$(".ul_mag:nth-child("+i+")").removeClass('bg1');
			}else{
				$(".ul_mag:nth-child("+i+")").addClass('bg1');
				$(".ul_mag:nth-child("+i+")").removeClass('bg2');
			}
		}
	}
</script>