<div class="bg_menu"></div>
<div class="tab_1 half_width" style="width:45%;margin:5% auto;">
        <span class="title_block bo_ra1">
            <span class="float_left">
                <span class="fl_dent">
                    <h4>Find option for report</h4>
                </span>
            </span>
        </span>
        <form id="form_employee" method="POST">
        <div class="tab_2_inner">
            <p class="clear">
                <span class="label_1 float_left minw_lab2" style="height:50px">
                    Report Heading</span>
                </p>
                <div class="width_in float_left indent_input_tp" style="width:61.5%">
                    <?php echo $this->Form->input('heading', array(
                            'class' => 'input_4 float_left',
                            'name'=>'heading'
                    )); ?>
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2">Employee</span>
                </p>
                <div class="indent_new width_in float_left" style="width:61%">
                    <?php
                        echo $this->Form->input('contact',array(
                                'class'=>'input_4 float_left validate',
                                'name'=>'contact'
                            ));
                    ?>
                    <span class="icon_down_new float_right" id="click_open_window_contactscontact_name" style=""></span>
                    <input type="hidden" value="" id="contact_id" name="contact_id" />
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2">Date equal</span>
                </p>
                <div class="indent_new width_in float_left" style="width:61%">
                    <?php
                        echo $this->Form->input('date_equals',array(
                            'class'=>'input_4 float_left JtSelectDate validate',
                            'name'=>'date_equals',
                            'readonly' => true,
                            ));
                    ?>
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2" style="height:50%">Date between from</span>
                </p>
                <div class="indent_new width_in float_left" style="width:25%">
                    <?php echo $this->Form->input('date_from', array(
                            'class' => 'input_4 float_left JtSelectDate validate',
                            'readonly' => true,
                            'name'=>'date_from',
                            'style' =>'padding:0 3%;'
                    )); ?>
                </div>
                <span class="float_left" style="margin-top:2%">To</span>
                    <?php echo $this->Form->input('date_to', array(
                            'class' => 'input_4 float_left jt_input_search JtSelectDate validate',
                            'readonly' => true,
                            'name'=>'date_to',
                            'style' =>'padding: .3% 1%;width: 30%;'
                    )); ?>
                <p class="clear"></p>
            <p></p>
            <p class="clear"></p>
        </div>
        <div>
        <span class="title_block bo_ra2">
            <span class="icon_vwie indent_down_vwie2">
                <a href="">
                    Enter find criteria and click Continue
                </a>
            </span>
            <ul class="menu_control float_right" style="margin:-1% -5%;width:35%">
                <li><a href="javascript:void()" id="CancelButton" style="margin-top: 6%;font-size: 10px;line-height: 4px;border-radius: 3px;box-shadow: 0px 1px 2px">Cancel</a></li>
                <li style="margin-left:10%"><a style="margin-top: 6%;font-size: 10px;line-height: 4px;border-radius: 3px;box-shadow: 0px 1px 2px" id="ContinueButton" href="javascript:void(0)">Continue</a></li>
            </ul>
            <p class="clear"></p>
        </span>
    </div>
</form>
<script type="text/javascript">
    $(function(){
        $('#is_not_cancel').change(function() {
            $(this).val(0);
            if($(this).is(':checked'))
                $(this).val(1);
        });
        $('#date_equals').change(function() {
            $('#date_from').val('');
            $('#date_to').val('');
        });
        $('#date_from,#date_to').change(function(){
            $('#date_equals').val('');
        });
        $('#CancelButton').click(function(){
            window.location.replace('<?php echo URL; ?>/contacts/options');
        });
        $('#ContinueButton').click(function(){
            $.ajax({
                type: 'POST',
                data:  $('input','#form_employee').serialize(),
                success : function(result) {
                    if(result=='empty')
                        alerts('Message','No record!');
                    else
                       $("#form_employee").submit();
                }
            });
        });
        window_popup('contacts', 'Specify Contact','contact','click_open_window_contactscontact_name', '?true_employee=1');
    });
    function after_choose_contacts(_id,contact_name,keys)
    {
        $('#'+keys+'').val(contact_name);
        $('#'+keys+'_id').val(_id);
        $("#window_popup_contacts"  + keys).data("kendoWindow").close();
        return false;
    }
</script>