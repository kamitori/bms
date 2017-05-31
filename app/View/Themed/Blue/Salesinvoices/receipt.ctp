<?php if($this->Common->check_permission('receipts_@_entry_@_view',$arr_permission)): ?>
<div class="clear_percent" id="load_subtab" style="width:60%; margin-left: 0;float:left">
   <div class="float_left " style=" width:100%;margin-top:0;">
      <div class="tab_1 full_width" id="block_full_receipt">
         <!-- Header-->
         <span class="title_block bo_ra1">
            <span class="fl_dent">
               <h4><?php echo translate('Receipts'); ?></h4>
            </span>
            <?php if(isset($invoice_status)&&$invoice_status!='Paid'
               &&$this->Common->check_permission('salesinvoices_@_entry_@_edit',$arr_permission)): ?>
            <a title="Add line" id="bt_add_receipt" href="javascript:void()">
            <span class="icon_down_tl top_f"></span>
            </a>
            <?php endif; ?>
            <div class="float_left hbox_form" style="width:auto; margin-left:5px;">
               <!--<a href="#" target="_blank">
               <input class="btn_pur" id="printexport_receipt" type="button" value="Export PDF" style="width:99%;">
               </a>-->
            </div>
         </span>
         <!--CONTENTS-->
         <div class="jt_subtab_box_cont" style=" height:282px;">
            <ul class="ul_mag clear bg3">
               <li class="hg_padd" style="width:1%;"></li>
               <li class="hg_padd" style="text-align:center;width:7%;">
                  <?php echo translate('Receipts'); ?>
               </li>
               <li class="hg_padd" style="text-align:center;width:10%;">
                  <?php echo translate('Date'); ?>
               </li>
               <li class="hg_padd" style="text-align:left;width:12%;">
                  <?php echo translate('Paid by'); ?>
               </li>
               <li class="hg_padd" style="text-align:left;width:20%;">
                  <?php echo translate('Reference'); ?>
               </li>
               <li class="hg_padd" style="text-align:left;width:22%;">
                  <?php echo translate('Notes'); ?>
               </li>
               <li class="hg_padd" style="text-align:center;width:8%;">
                  <?php echo translate('Write off'); ?>
               </li>
               <li class="hg_padd" style="text-align:right;width:11%;border-right: none">
                  <?php echo translate('Amount'); ?>
               </li>
            </ul>
            <div class="container_receipt" style=" overflow-y:auto;height:261px;">
               <div id="receipt_content">
                  Loading ...
                  <!--- Recipt se hien o day, load,update,add -->
               </div>
            </div>
         </div>
         <!--<span class="hit"></span>-->
         <!--Footer-->
         <span class="title_block bo_ra2">
            <span class="float_left bt_block"><?php echo translate('Click to view document record'); ?></span>
            <input class="input_w2 float_right" id="totals" type="text" style="width:8%; margin:0 1% 0 0;color:#444;" value="" readonly="readonly">
            <span class="float_right" style="width:75px;font: 11px arial,verdana,sans-serif;"><?php echo translate('Total receipts'); ?></span>
            <div class="clear"></div>
         </span>
      </div>
   </div>
   <p class="clear"></p>
</div>
<div class="float_left" id="outstading_invoice" style="width:23%; float:left">
   <div class="float_left " style=" width:100%;margin-top:0;">
      <div class="tab_1 full_width" id="block_full_outstading_invoice">
         <!-- Header-->
         <span class="title_block bo_ra1">
            <span class="fl_dent">
               <h4><?php echo translate('Outstading Invoice'); ?></h4>
            </span>
         </span>
         <!--CONTENTS-->
         <div class="jt_subtab_box_cont" style=" height:282px;">
            <ul class="ul_mag clear bg3">
               <li class="hg_padd" style="width:3%;">&nbsp;</li>
               <li class="hg_padd" style="text-align:left;width:20%;">
                  <?php echo translate('Ref no'); ?>
               </li>
               <li class="hg_padd" style="text-align:center;width:22%;">
                  <?php echo translate('Date'); ?>
               </li>
               <li class="hg_padd" style="text-align:right;width:23%;">
                  <?php echo translate('Total'); ?>
               </li>
               <li class="hg_padd" style="text-align:right;width:23%;">
                  <?php echo translate('Receipts'); ?>
               </li>
            </ul>
            <div class="container_receipt" style=" overflow-y:auto;height:261px;">
               <div id="outstading_invoice_content">
               <?php
                  $i = 0;
                  $html = '';
                  if(!empty($outstanding_invoice)){
                     foreach($outstanding_invoice as $invoice_id => $invoice){
                        $bg = ($i%2 ==0 ? 'bg2' : 'bg1');
                        $html .= '<ul class="ul_mag clear '.$bg.'  line_box">
                                    <li class="hg_padd " style="width:3%;" title="View detail" onclick=" window.location.assign(\''.URL.'/salesinvoices/entry/'.$invoice_id.'\');">
                                      <span class="icon_emp"></span>
                                    </li>
                                    <li class="hg_padd" style="text-align:left; width:20%;">
                                        <span>'.$invoice['code'].'</span>
                                    </li>
                                    <li class="hg_padd" style="text-align:center; width:22%;">
                                        <span>'.$invoice['invoice_date'].'</span>
                                    </li>
                                    <li class="hg_padd" style="text-align:right; width:23%;">
                                        <span>'.$this->Common->format_currency($invoice['total']).'</span>
                                    </li>
                                     <li class="hg_padd" style="text-align:right; width:23%;">
                                        <span>'.$this->Common->format_currency($invoice['receipts']).'</span>
                                    </li>
                                 </ul>';
                        $i++;
                     }
                  }
                  for($i ; $i < 11; $i++){
                     $bg = ($i%2 ==0 ? 'bg2' : 'bg1');
                     $html .= '<ul class="ul_mag clear '.$bg.'  line_box"></ul>';
                  }
                  echo $html;
               ?>
               </div>
            </div>
         </div>
         <!--Footer-->
         <span class="title_block bo_ra2">
            <span class="float_left bt_block"><?php echo translate('Click to view'); ?></span>
            <div class="clear"></div>
         </span>
      </div>
   </div>
   <p class="clear"></p>
</div>
<div class="float_left " style=" width:15%;float:right">
    <div class="tab_1 full_width" id="block_full_pricingsummary">

        <!-- Header-->
         <span class="title_block bo_ra1">
            <span class="fl_dent">
                <h4><?php echo translate('Overall Totals'); ?></h4>
            </span>
         </span>

         <!--CONTENTS-->
         <div class="jt_subtab_box_cont">
            <div class="tab_2_inner" id="editview_box_overall_totals">
               <p class="clear">
                  <span class="label_1 float_left minw_lab2"><?php echo translate('Total Invoice'); ?></span>
               </p>
               <div class="width_in3a float_left indent_input_tp">
                   <span class="input_1 " style="text-align:right;" id="overall_totals_invoice"></span>
               </div>
               <p></p>
               <p class="clear">
                  <span class="label_1 float_left minw_lab2"><?php echo translate('Total Receipt'); ?></span>
               </p>
               <div class="width_in3a float_left indent_input_tp">
                  <span class="input_1 " style="text-align:right;" id="overall_totals_receipt"></span>
               </div>
               <p></p>
               <p class="clear">
                  <span class="label_1 float_left minw_lab2"><?php echo translate('Balance'); ?></span>
               </p>
               <div class="width_in3a float_left indent_input_tp">
                  <span class="input_1 " style="text-align:right;" id="overall_totals_balance"></span>
               </div>
               <p></p>
               <p class="clear">
                  <span class="label_1 float_left minw_lab2"></span>
               </p>
               <div class="width_in3a float_left indent_input_tp"></div>
               <p></p>
               <div class="block_warning" style="display:block;">
                  <span class="label_bg float_left minw_lab2 fixbor3"></span>
                  <div class="width_in3a float_left indent_input_tp">
                     <div class="warning"></div>
                  </div>
               </div>
               <p class="clear">
                  <span class="label_1 float_left minw_lab2"></span>
               </p>
               <div class="width_in3a float_left indent_input_tp"></div>
               <p></p>
               <p class="clear">
                  <span class="label_1 float_left minw_lab2"></span>
               </p>
               <div class="width_in3a float_left indent_input_tp"></div>
               <p></p>
               <p class="clear">
                  <span class="label_1 float_left minw_lab2"></span>
               </p>
               <div class="width_in3a float_left indent_input_tp"></div>
               <p></p>
               <p class="clear">
                  <span class="label_1 float_left minw_lab2"></span>
               </p>
               <div class="width_in3a float_left indent_input_tp"></div>
               <p></p>
               <p class="clear">
                  <span class="label_1 float_left minw_lab2" style="height: 26px;"></span>
               </p>
               <div class="width_in3a float_left indent_input_tp"></div>
               <p></p>
               <p class="clear"></p>
            </div>
         </div>
         <!--Footer-->
         <span class="title_block bo_ra2"></span>
    </div>
</div>
<div class="clear"></div>
<script type="text/javascript">
   $(function(){
      $(".container_receipt").mCustomScrollbar({
            scrollButtons:{
               enable:false
            },
            advanced:{
               updateOnContentResize: true,
               autoScrollOnFocus: false,
            }
      });
      $('#receipt_content').load('<?php echo URL; ?>/salesinvoices/receipt_content',function(){
         update_total();
         overall_totals($('#totals').val());
      });
      <?php if($this->Common->check_permission('receipts_@_entry_@_add',$arr_permission)): ?>
      $('#bt_add_receipt').click(function(){
         add_receipt();
         return false;
      });
      <?php endif; ?>
      update_total();
   })
   function update_total()
   {
      $('#totals').val($('#totals_receipt').val());
   }
   function load_receipt()
   {
      $.ajax({
         url: '<?php echo URL; ?>/salesinvoices/receipt_content',
         type: 'GET',
         success: function(html)
         {
            //console.log(html);
            $('#receipt_content').html(html);
            update_total();
            overall_totals($('#totals').val());
         }
      });
   }
   <?php if($this->Common->check_permission('receipts_@_entry_@_add',$arr_permission)): ?>
   function add_receipt()
   {
      if($('#company_id').val()==''&&$('#contact_id').val()==''){
         alerts('Message','This function cannot be performed as there is no company or contact linked to this record.');
         return false;
      }
      else if($('#invoice_statusId').val()!='Paid'){
         var arrmsg = ['Yes','No',''];
         confirms3('Message',"Create a receipt for full invoice balance?",arrmsg,
            function(){//YES
               $.ajax({
                  url: '<?php echo URL; ?>/salesinvoices/create_receipt/Fully',
                  type: 'GET',
                  success: function(){
                     location.reload();
                  }
               });

            },
            function(){//NO
               $.ajax({
                  url: '<?php echo URL; ?>/salesinvoices/create_receipt/Part',
                  type: 'GET',
                  success: function(url){
                     window.location.replace(url);
                  }
               });
            });
      }
      else{
         $.ajax({
            url: '<?php echo URL; ?>/salesinvoices/create_receipt/Part',
            type: 'GET',
            success: function(){
               load_receipt();
            }
         });
      }
   }
   <?php endif; ?>
   <?php if($this->Common->check_permission('receipts_@_entry_@_edit',$arr_permission)): ?>
   function update_receipt(data)
   {
      $.ajax({
         url: '<?php echo URL; ?>/salesinvoices/update_receipt/',
         type: 'POST',
         data: data,
         success: function(result){
            if(result=='ok')
               load_receipt();
            else
               alerts('Message',result);
         }
      });
   }
   <?php endif; ?>
   function overall_totals(total_receipt)
   {
      $.ajax({
         url: '<?php echo URL; ?>/salesinvoices/receipt_overall_total/'+total_receipt,
         type: 'GET',
         success: function(result){
            result = jQuery.parseJSON(result);
            $('#overall_totals_invoice').html(result.total_invoice);
            $('#overall_totals_receipt').html(result.total_receipt);
            if(parseFloat(result.balance)<0)
               result.balance = '<span style="color:red">'+result.balance+'</span>';
            $('#overall_totals_balance').html(result.balance);
         }
      });
      return false;

   }
</script>
<?php endif; ?>