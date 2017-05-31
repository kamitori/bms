<?php echo $this->element('../' . $name . '/tab_option'); ?>

<div class="tab_1 half_width" style="width:45%;margin:5% auto;">
    <span class="title_block bo_ra1">
        <span class="float_left">
            <span class="fl_dent">
                <h4>Find option for report</h4>
            </span>
        </span>
    </span>
    <form method="post" id="find_option_supplier">
        <div class="tab_2_inner">
            <p class="clear">
                <span class="label_1 float_left minw_lab2">Heading</span>
            </p>
            <div class="width_in float_left indent_input_tp" style="width:61.5%">                                   
                <?php
                echo $this->Form->input('heading', array(
                    'class' => 'input_4 float_left validate'
                ));
                ?>                
            </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2">Status</span>
            </p>
            <div class="width_in float_left indent_input_tp" style="width:61.5%">                                   
                <?php
                echo $this->Form->input('status', array(
                    'class' => 'input_select input_3 validate',
                    'readonly' => true,
                    'style' => 'margin: 0px 16px 0px 0px;padding: 0 0px 0 2%;'
                ));
                ?>
                <script type="text/javascript">
                    $(function() {
                        $("#status").combobox(<?php echo json_encode($status); ?>);
                    });
                </script>                 
            </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2">Exclude cancelled transactions</span>
            </p>
            <div class="width_in3a float_left indent_input_tp" style="margin-top:1.7%">
                <label class="m_check2">
                    <?php
                    echo $this->Form->input('cancel', array(
                        'class' => 'custoer-employee',
                        'type' => 'checkbox',
                        'checked' => 'checked',
                        'value' => 1
                    ));
                    ?> 
                    <span style="margin-left:1%"></span>
                    <p style= "margin-left:37px;width:280px">Also excludes Amended and Rejected transactions  </p>
                </label>                     
            </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2">Supplier Company</span>
            </p>
            <div class="indent_new width_in float_left" style="width:61%">
                <?php
                echo $this->Form->input('company', array(
                    'class' => 'input_4 float_left validate',
                    'disable' => true
                ));
                ?>
                <input type="hidden" value="" id="company_id" name="company_id" >
                <span class="icon_down_new float_right" id="open_popup_company" style=""></span>
                <script type="text/javascript">
                    $(function() {
                        var parameter = '?is_supplier=1';
                        window_popup('companies', 'Specify Company', 'company', 'open_popup_company', parameter);
                    });
                </script>
            </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2">Supplier Contact</span>
            </p>
            <div class="indent_new width_in float_left" style="width:61%">
                <?php
                echo $this->Form->input('contact', array(
                    'class' => 'input_4 float_left validate',
                ));
                ?>
                <span class="icon_down_new float_right" id="open_popup_contact" style=""></span>
                <input type="hidden" value="" id="contact_id" name="contact_id" />
                <script type="text/javascript">
                    $(function() {
                        var parameter = '?is_employee=1';
                        window_popup('contacts', 'Specify Contact', 'contact', 'open_popup_contact', parameter);
                    });
                </script>
            </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2">Job no</span>
            </p>
            <div class="indent_new width_in float_left" style="width:61%">
                <?php
                echo $this->Form->input('job_no', array(
                    'class' => 'input_4 float_left validate'
                ));
                ?>
                <span class="icon_down_new float_right" id="open_popup_job" style=""></span>
                <input type="hidden" value="" id="job_no_id" name="job_no_id"/>
                <script type="text/javascript">
                    $(function() {
                        window_popup('jobs', 'Specify Job', 'job_no', 'open_popup_job');
                    });
                </script>
            </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2">Date equal</span>
            </p>
            <div class="indent_new width_in float_left" style="width:61%">
                <?php
                echo $this->Form->input('date_equals', array(
                    'class' => 'input_4 float_left JtSelectDate validate',
                    'readonly' => true
                ));
                ?>                             
            </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2" style="height:50%">Date between from</span>
            </p>
            <div class="indent_new width_in float_left" style="width:25%">
                <?php
                echo $this->Form->input('date_from', array(
                    'class' => 'input_4 float_left JtSelectDate validate',
                    'readonly' => true,
                    'style' => 'padding:0 3%;'
                ));
                ?>
            </div>
            <span class="float_left" style="margin-top:2%">To</span>
            <?php
            echo $this->Form->input('date_to', array(
                'class' => 'input_4 float_left jt_input_search JtSelectDate validate',
                'readonly' => true,
                'name' => 'date_to',
                'style' => 'padding: .3% 1%;width: 30%;'
            ));
            ?>
            <p class="clear"></p>
            <p></p>            
            <p class="clear">
                <span class="label_1 float_left minw_lab2" style="height:50%">Our Rep</span>
            </p>
            <div class="indent_new width_in float_left" style="width:61%">
                <?php
                echo $this->Form->input('our_rep', array(
                    'class' => 'input_4 float_left validate',
                ));
                ?>
                <span class="icon_down_new float_right" id="open_popup_rep" style=""></span>
                <input type="hidden" value="" id="our_rep_id" name="our_rep_id">
                <script type="text/javascript">
                    $(function() {
                        var parameter = '?is_employee=1';
                        window_popup('contacts', 'Specify Our Rep', 'our_rep', 'open_popup_rep', parameter);
                    });
                </script>
            </div>
            <input type="hidden" name="report_type" value="summary" />
            <p></p>
            <p class="clear"></p>
        </div>
    </form>
    <div>           
        <span class="title_block bo_ra2">
            <span class="icon_vwie indent_down_vwie2">
                <a href="">Enter find criteria and click Continue</a>
            </span>
            <ul class="menu_control float_right" style="margin:-1% -5%;width:35%">
                <li><a href="javascript:void()" id="CancelButton" style="margin-top: 6%;font-size: 10px;line-height: 4px;border-radius: 3px;box-shadow: 0px 1px 2px">Cancel</a></li>
                <li style="margin-left:10%"><a style="margin-top: 6%;font-size: 10px;line-height: 4px;border-radius: 3px;box-shadow: 0px 1px 2px" id="ContinueButton" href="javascript:void()">Continue</a></li>
            </ul>
            <p class="clear"></p>
        </span>
    </div>
    <?php echo $this->Form->end(); ?>

    <script type="text/javascript">
        $(function() {
            $('#ContinueButton').click(function() {
                if (validate() == false) {
                    find_option_supplier();
                } else {
                    confirms('Warning', 'The provided criteria is not valid. Enter a valid request before proceeding.');
                    return false;
                }
            });
        });

        function find_option_supplier() {
            $.ajax({
                url: '<?php echo URL . '/' . $controller; ?>/find_option_supplier',
                type: 'post',
                data: $('#find_option_supplier').serialize(),
                success: function(link) {
                    console.log(link);
                }
            });
        }
        
        function validate() {
            var status;
            $('.validate').each(function() {
                if ($(this).val() != '') {
                    status = false;
                    return false;
                }
            });
            if (status == false)
                return false;
            else
                return true;
        }

        function after_choose_companies(_id, company_name, keys){
            $('#' + keys + '').val(company_name);
            $('#' + keys + '_id').val(_id);
            $("#window_popup_companies" + keys).data("kendoWindow").close();
            return false;

        }
        function after_choose_contacts(_id, contact_name, keys){
            $('#' + keys + '').val(contact_name);
            $('#' + keys + '_id').val(_id);
            $("#window_popup_contacts" + keys).data("kendoWindow").close();
            return false;
        }
        
        function after_choose_jobs(_id, our_rep, keys){
            $('#' + keys + '').val(our_rep);
            $('#' + keys + '_id').val(_id);
            $("#window_popup_jobs" + keys).data("kendoWindow").close();
            return false;
        }
        
        
    </script>