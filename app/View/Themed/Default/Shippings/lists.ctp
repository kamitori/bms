<?php echo $this->element('lists');?>
<script type="text/javascript">
$(function(){

	$(".menu_control li:first a").attr('href','javascript:check_add();');
});
function add_new(fieldname,values){
	$.ajax({
		url: '<?php echo URL.'/'.$controller;?>/ajax_save',
		type:"POST",
		data: {field:fieldname,value:values,func:'add'},
		success: function(text_return){
			text_return = text_return.split("||");
			window.location.assign("<?php echo URL.'/'.$controller;?>/entry/"+text_return[0]);
		}
	});
}
function check_add(){
	var arr = new Array();
	arr = ['Outgoing','Incoming',''];
	confirms3('Message',"Create an '<span class=\"bold\">Outgoing</span>' or '<span class=\"bold\">Incoming</span>' shipping/delivery?",arr,function(){
			add_new('shipping_type','Out'); //Outgoing
	},function(){
			add_new('shipping_type','In'); //Incoming
	},function(){
			//
	},function(){
			//
	});
}
</script>