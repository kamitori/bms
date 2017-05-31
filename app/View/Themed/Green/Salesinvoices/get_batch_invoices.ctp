<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<style type="text/css">
.cke_dialog{
	z-index: 10050 !important;
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
</style>
<ul class="menu_control float_right">
    <li >
        <a href="javascript:void(0)" id="submit_batch_invoice" style="background: #852020;color: #fff;height: 19px;width: 60px;text-align: center;font-weight: bold;">Submit</a>
    </li>
</ul>
<div class="invoice-tabs">
	<ul>
		<?php foreach($arr_data as $value){ ?>
		<li <?php if(!isset($k_active)) { echo 'class="k-state-active"'; $k_active = true; } ?>><?php echo $value['name']; ?></li>
		<?php } ?>
	</ul>
	<?php foreach($arr_data as $value){ ?>
	<div style=" width:97%; min-height: 400px" class="container_same_category">
		<div class="jt_box_line">
			<div class=" jt_box_label " style=" width:10%; height: 250px;">Content</div>
			<div class="jt_box_field " style=" width:85%">
				<textarea id="content_<?php echo $value['_id'] ?>" name="batch_salesinvoices[<?php echo $value['_id']; ?>][content]"><?php echo $value['content'] ?></textarea>
				<script type="text/javascript">
					var id = "content_<?php echo $value['_id'] ?>";
					CKEDITOR.replace(id,
					{
						toolbar : 'Email',
				        resize_enabled : false,
				        removePlugins : 'elementspath',
				        height : 150,
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
				        filebrowserImageUploadUrl : '<?php echo URL; ?>/js/kcfinder/upload.php?type=images',
				        filebrowserImageBrowseUrl : '<?php echo URL; ?>/js/kcfinder/browse.php?type=images',
				        enterMode:CKEDITOR.ENTER_BR,
					});
				</script>
			</div>
		</div>
		<?php foreach($value['invoices'] as $invoice){ ?>
		<div class="jt_box_line">
			<div class=" jt_box_label " style=" width:10%; min-height: 80px;">INV #<?php echo $invoice['code'] ?></div>
			<div class="jt_box_field " style=" width:85%">
				<table style="width: 80%;">
					<tr>
						<td style="width: 30%;">Invoice Status</td>
						<td><?php echo $invoice['invoice_status'] ?>
							<input class="resend" name="batch_salesinvoices[<?php echo $value['_id'] ?>][invoices][<?php echo $invoice['_id'] ?>][invoice_status]" value="<?php echo $invoice['invoice_status'] ?>" type="hidden">
							<input class="resend" name="batch_salesinvoices[<?php echo $value['_id'] ?>][invoices][<?php echo $invoice['_id'] ?>][_id]" value="<?php echo $invoice['_id'] ?>" type="hidden">
						</td>
					</tr>
					<?php if(isset($invoice['sent'])){ ?>
					<tr>
						<td>
							<a style="color: blue;" href="<?php echo URL.'/communications/entry/'.$invoice['sent_mail']; ?>/" target="_blank" >View Email</a>
						</td>
						<td>This email has been invoice at <?php echo $invoice['sent_date']; ?></td>
					</tr>
					<tr>
						<td>Re-send</td>
						<td>
							<span class="float_left jtchecktype" style=" width:101%;margin-left:0%;">
								<label class="m_check2"><input class="resend" name="batch_salesinvoices[<?php echo $value['_id'] ?>][invoices][<?php echo $invoice['_id'] ?>][resend]" id="<?php echo $invoice['_id'] ?>" type="checkbox">
									<span style=" margin-left:3%;"></span>
								</label>
								<span class="fl_dent" for="batch_salesinvoices[<?php echo $value['_id'] ?>][invoices][<?php echo $invoice['_id'] ?>][resend]" style="margin-left:5px;color: #ddd" >Tick to re-send email.</span>
							</span>
						</td>
					</tr>
					<?php } ?>
				</table>
			</div>
		</div>
		<?php } ?>
	</div>
	<?php } ?>
<div>
<script>
	var callAjax = function(data) {
        notifyTop("Sending Email. Please wait for a moment...");
        var url = "<?php echo URL.'/salesinvoices/batch_invoices' ?>";
        $.ajax({
            url: url,
            data: data,
            type: "POST",
            success: function(result){
                if( result.indexOf("$id")!=-1){
                    $("#notifyTop").fadeOut(600);
                    result = JSON.parse(result);
                    var html = "";
                    for(i in result)
                        html += "<a style=\"color:blue\" target=\"_blank\" href=\"<?php echo URL.'/communications/entry/' ?>"+result[i].$id+"\">Email "+(parseInt(i)+1)+"</a><br />";
                    localStorage.removeItem('batch_salesinvoice');
                    $("input[type=checkbox]","#batch_salesinvoices").prop("checked",false);
                    alerts("Email Lists",html);
                } else
                    alerts("Message",result);
            }
        });
    }
    $(document).ready(function(){
        $(".invoice-tabs").kendoTabStrip();
        $(".container_same_category", ".invoice-tabs").mCustomScrollbar({
            scrollButtons:{
                enable:false
            },
            advanced:{
                updateOnContentResize: true,
                autoScrollOnFocus: false,
            }
        });
        $("#submit_batch_invoice").click(function(){
        	for(var instanceName in CKEDITOR.instances)
    			CKEDITOR.instances[instanceName].updateElement();
        	var data = $("input,textarea",".invoice-tabs").serialize();
        	$("#new_batch_invoice").data("kendoWindow").destroy();

        	if(data == ""){
        		alerts("Message", "No email will be sent");
        		return false;
        	}
        	confirms3("Message","Do you want to CC email to Order Contact?",["Yes","No",""]
                 ,function(){ //Yes
                    data += "&cc_to_contact=true";
                    callAjax(data);
                 }, function(){ //No
                    callAjax(data);
                 }, function(){
                    return false;
                 });
        });
    });
</script>