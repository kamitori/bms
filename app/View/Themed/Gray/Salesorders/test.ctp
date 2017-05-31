<input id="button" type="button" value="121">
<script type="text/javascript">
	$("#button").click(function(){
		$.ajax({
			url: "http://jobtraq.anvyonline.com/salesorders/create_salesorder_from_worktraq",
			type: "POST",
			data: {"company_name":"Chatters Canada Limited","company_id":"5271dab7222aad6819001055","contact_id":null,"contact_name":"","heading":"","invoice_address":[{"deleted":false,"invoice_address_1":"Unit 210, 1215 Sumas Way","invoice_address_2":"","invoice_address_3":"","invoice_country":"Canada","invoice_country_id":"CA","invoice_default":{"value":"1"},"invoice_name":"Abbotsford Power Centre","invoice_name_id":10708,"invoice_province_state":"British Columbia","invoice_province_state_id":"British Columbia","invoice_town_city":"Abbotsford","invoice_zip_postcode":"V2S 8H2"}],"our_csr":"","our_csr_id":0,"our_rep":"","our_rep_id":0,"products":[{"deleted":false,"products_name":"Illumina - Sexy Hair- Red Head Texture","products_id":320,"sizew":34,"sizew_unit":"in","sizeh":58,"sizeh_unit":"in","sell_by":"Unit","sell_price":0,"oum":"Sq.ft.","custom_unit_price":0,"quantity":33,"sku":"ipM-ill-sh-rht","code":320,"oum_depend":"","design_id":0,"product_image":"http:\/\/chat.local\/resources\/CHT\/products\/320\/320_ipM_ill_sh_rht.jpg","order_id":1,"order_item_id":2,"total_price":2705.67,"upload_key_id":0,"use_inventory":0},{"deleted":false,"products_name":"A Unit Graphic - Body Care","products_id":172,"sizew":8.5,"sizew_unit":"in","sizeh":11,"sizeh_unit":"in","sell_by":"Unit","sell_price":0,"oum":"Sq.ft.","custom_unit_price":0,"quantity":1,"sku":"ageS-sty-c-bc","code":172,"oum_depend":"","design_id":0,"product_image":"http:\/\/chat.local\/resources\/CHT\/products\/172\/body_care.jpg","order_id":1,"order_item_id":1,"total_price":6.99,"upload_key_id":0,"use_inventory":0}]},
			success: function(){

			}
		})
	})
</script>