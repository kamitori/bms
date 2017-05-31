<style type="text/css">
.label_1 {

width: 7.5%;
}
.minw_lab2 {
min-width: 21%;
}
.width_in3a {
width: 75%;
}
</style>

<div style="padding:0px 0 10px 15px; margin-top:10px;">
    <h2><?php echo translate('Leave'); ?>: <?php echo isset($leave['purpose'])?$leave['purpose']:''; ?></h2>
</div>

<?php echo $this->element('entry_tab_option');?>
<div class="clear_percent">
    <div class="float_left " style=" width:27%; float: left;">
        <div class="tab_1 full_width">
           <span class="title_block bo_ra1">
                <span class="float_left">
                    <span class="fl_dent"><h4><?php echo translate('Booking details'); ?></h4></span>
                     <input class="btn_pur" id="send_email" type="button" value="Send Email" style="width: 39%;float: right;" />
                </span>
            </span>
            <div class="container_same_category tab_2_inner" style="height:484px;overflow-y:hidden" id="company_product_left" >

            <form method="POST" id="bookings_detail_id">
                <p class="clear">
                    <span class="label_1 float_left minw_lab2"><?php echo translate('Name'); ?></span>
                </p>
                <div class="width_in3a float_left indent_input_tp">
                    <?php echo $this->Form->input('name', array(
                            'class' => 'input_1 float_left',
                            'readonly' => true,
                            'value' => isset($full_name)?$full_name:'',
                    )); ?>
                </div>
                <p></p>

                <p class="clear">
                <p class="clear">
                   <span class="label_1 float_left minw_lab2">Purpose</span>
                </p>
                <div class="width_in3 float_left indent_input_tp" id="purpose_div" style="width:75%">
                   <input name="purpose" value="<?php echo isset($leave['purpose'])?$leave['purpose']:'';?>" class="input_select" type="text" id="purpose">
                   <input name="purpose_id" type="hidden" id="purposeId" value="<?php echo isset($leave['purpose_id'])?$leave['purpose_id']:''?>">
                   <script type="text/javascript">
                    $(function(){
                        $("#purpose").combobox(<?php echo json_encode($arr_leave_purpose); ?>);
                    });
                   </script>
                </div>

                <p class="clear">
                   <span class="label_1 float_left minw_lab2">Day used</span>
                </p>
                <div class="width_in3 float_left indent_input_tp" style=" width: 75%%; ">
                   <input readonly="true" class="input_1 float_left" id="used" name="used" type="text" value="<?php echo isset($leave['used'])?$leave['used']:''?>">
                </div>
                <p></p>

                <p class="clear">
                   <span class="label_1 float_left minw_lab2">From</span>
                </p>
                <div class="width_in3 float_left indent_input_tp" style=" width: 75%%; ">
                   <input readonly="true" class="input_1 float_left" id="date_from" name="date_from" type="text" value="<?php echo date('m/d/Y', $leave['date_from']->sec);?>">
                </div>
                <p></p>

                <p class="clear">
                   <span class="label_1 float_left minw_lab2">To</span>
                </p>
                <div class="width_in3 float_left indent_input_tp" style=" width: 75%%; ">
                   <input readonly="true" class="input_1 float_left" id="date_to" name="date_to" type="text" value="<?php echo date('m/d/Y', $leave['date_to']->sec);?>">
                </div>
                <p></p>

                <p class="clear">
                   <span class="label_1 float_left minw_lab2">Status</span>
                </p>
                <div class="width_in3 float_left indent_input_tp" style=" width: 75%%; ">
                   <input class="input_1 float_left input_select" id="status" name="status" type="text" value="<?php echo isset($leave['status'])?$leave['status']:'';?>" readonly="readonly" />
                   <script type="text/javascript">
                    // $(function(){
                    //     $("#status").combobox(<?php echo json_encode($arr_leave_status); ?>);
                    // });
                   </script>
                </div>
                <p></p>

                <p class="clear">
                   <span class="label_1 float_left minw_lab2" style="height:70px">Don't deduct</span>
                </p>
                <div class="in_active2">
                    <label class="m_check2">
                        <input type="checkbox" id="dontdeduct" name="dontdeduct" value="<?php if(isset($leave['dontdeduct']))echo $leave['dontdeduct'];?>"  <?php if(isset($leave['dontdeduct'])&&$leave['dontdeduct']==1){?> checked <?php }?>>
                        <span class="bx_check"></span>
                    </label>
                    <p class="clear"></p>
                </div>

            <!-- <form method="POST" id="approve_id"> -->
            <?php
                $check = $this->Common->check_permission('contacts_@_workings_holidays_approve_@_edit',$arr_permission);
             ?>
                <div>
                    <span class="title_block" style="margin-top:0%">
                        <span class="float_left">
                            <span class="fl_dent"><h4><?php echo translate('Approve'); ?></h4></span>
                            <!-- <input class="btn_pur" id="submit_approved" type="button" value="Submit" style="width: 43%%;float: right;" /> -->
                        </span>
                    </span>

                    <p class="clear">
                       <span class="label_1 float_left minw_lab2">Approved</span>
                    </p>
                    <div class="in_active2">
                        <label class="m_check2">
                            <input type="checkbox" id="approved" <?php if($check){ ?>name="approved"<?php }else{ ?> disabled <?php } ?> value="<?php if(isset($leave['approved']))echo $leave['approved'];?>"  <?php if(isset($leave['approved'])&&$leave['approved']==1){?> checked <?php }?>>
                            <span class="bx_check"></span>
                        </label>
                        <p class="clear"></p>

                        <p class="clear">
                           <span class="label_1 float_left minw_lab2" style="height:218px">Comment</span>
                           <textarea style="width:77%; height:216px;" <?php if($check){ ?>name="comment"<?php }else{ ?> disabled <?php } ?> id="comment"><?php echo isset($leave['comment'])?$leave['comment']:'' ?></textarea>
                        </p>
                    </div>
                </div>
                </form>
            </div>
            <span class="title_block bo_ra2"></span>
        </div>
    </div>

    <div class="float_left " style=" width:72%;margin-left:1%; float: left;">

            <div class="tab_1 full_width">
                <span class="title_block bo_ra1">
                    <span class="float_left">
                        <span class="fl_dent"><h4><?php echo translate('Details'); ?></h4></span>
                    </span>
                </span>
                <p class="clear"></p>
                <div id="company_products_pricing_note" style="height: 483px;">
                    <textarea class="area_t3" onchange="leave_save_details(this)" style="height:98%;" value><?php echo isset($leave['details'])?$leave['details']:'';?></textarea>
                </div>
                <span class="title_block bo_ra2">
                    <span class="float_left bt_block">
                    </span>
                </span>
            </div>
    </div>
    <p class="clear"></p>
</div>
<p class="clear"></p><p class="clear"></p>

<script type="text/javascript">

    $(function(){
        $("form#bookings_detail_id textarea,select,input").change(function(){
            var fieldname = $(this).attr("name");
            var values = $(this).val();
            var fieldtype = $(this).attr("type");

            if(fieldtype == 'checkbox'){
                if($(this).is(':checked'))
                    values = 1;
                else
                    values = 0;
            }
            $.ajax({
                url:"<?php echo URL?>/contacts/leave_save_approved/<?php echo $contact_id?>/<?php echo $key;?>",
                timeout:15000,
                type:"POST",
                data:{fieldname: fieldname, values:values},
                success:function(html){
                    console.log(html);
                }
            });
            return false;
        });
    })

    $("#send_email").click(function(){
       window.location.replace("<?php echo URL?>/contacts/send_email_leave/<?php echo $contact_id;?>/<?php echo $key;?>");
    })

    function leave_save_details(object){
        $.ajax({
            url: "<?php echo URL; ?>/contacts/leave_save_details/<?php echo $contact_id; ?>/<?php echo $key; ?>",
            timeout: 15000,
            type: "POST",
            data: { details: $(object).val() },
            success: function(html){
                if(html != "ok"){
                    alerts("Error: ", html);
                }
            }
        });
        return false;
    }


</script>