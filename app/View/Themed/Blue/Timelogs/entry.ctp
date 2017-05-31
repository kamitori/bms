<?php echo $this->element('entry_tab_option'); ?>
<?php
for ($i = 0; $i < 24; $i++) {
    $j = $i;
    if ($j < 10) {
        $j = '0' . $j;
    }
    $arr_hour[$j . ':00'] = $j . ':00';
    $arr_hour[$j . ':30'] = $j . ':30';
}
?>
<div id="content">
    <div class="clear_percent">
        <div class="block_dent_a">
            <div class="title_1 float_left">
                <h1><?php echo $this->data['Timelog']['date'] ?> | <?php echo $this->data['Timelog']['employee_name'] ?></h1>
            </div>
            <div class="title_1 right_txt float_right">
                <h1>Type | <?php echo $this->data['Timelog']['employee_type'] ?></h1>
            </div>
        </div>
    </div>
    <p class="clear"></p>
    <div class="block_dent_a"></div>
    <div class="clear_percent" id="timelog_form_auto_save">

        <?php echo $this->Form->create('Timelog') ?>
            <?php echo $this->Form->hidden('Timelog._id', array('value' => (string) $this->data['Timelog']['_id'])) ?>
            <div class="clear_percent_21 float_left">
                <div class="tab_1 full_width">
                    <!--Block 1-->
                    <span class="title_block bo_ra1">
                        <span class="float_left">
                            <span class="fl_dent"><h4>Employee details</h4></span>
                        </span>
                    </span>
                    <div class="tab_2_inner">
                        <p class="clear">
                            <span class="label_1 float_left minw_lab2">Employee</span>
                        <div class="width_in3a float_left indent_input_tp">
                            <?php
                            echo $this->Form->hidden('Timelog.employee_id');
                            echo $this->Form->input('Timelog.employee_name', array(
                                'class' => 'input_select'
                            ))
                            ?>
                            <script type="text/javascript">
                                $(function() {
                                    $('#TimelogEmployeeName').combobox(<?php echo json_encode($arr_employee) ?>);
                                });
                            </script>
                        </div>
                        </p>
                        <p class="clear">
                            <span class="label_1 float_left minw_lab2 fixbor3">Employee type</span>
                        <div class="width_in3a float_left indent_input_tp">
                            <?php
                            echo $this->Form->input('Timelog.employee_type', array(
                                'class' => 'input_select'
                            ))
                            ?>
                            <script type="text/javascript">
                                $(function() {
                                    $("#TimelogEmployeeType").combobox(<?php echo json_encode($arr_employee_type); ?>);
                                });
                            </script>

                        </div>
                        </p>
                        <p class="clear"></p>
                    </div>

                    <!--End Block 1-->

                    <!--Block 2-->
                    <div>
                        <span class="title_block">
                            <h4>Timelog details</h4>
                        </span>
                        <div class="tab_2_inner">
                            <p class="clear">
                                <span class="label_1 float_left minw_lab2">Date</span>
                            <div class="width_in3a float_left indent_input_tp">
                                <div class="three_colum top_se">
                                    <?php
                                    echo $this->Form->input('Timelog.date', array(
                                        'class' => 'input_1 float_left indent_input JtSelectDate'
                                    ))
                                    ?>
                                </div>
                                <div class="three_colum top_se">
                                    <input class="input_1 float_left indent_input2 three_text" type="text" readonly>
                                </div>
                                <div class="two_colum no_border">
                                    <span style="float: left;font: 11px arial, verdana, sans-serif;padding-top: 2%;">Ref no:</span>
                                    <?php
                                    echo $this->Form->input('Timelog.no', array(
                                        'class' => 'input_1 float_left indent_input2 three_text',
                                        'readonly' => true,
                                        'style'=>'width: 50%'
                                    ))
                                    ?>
                                </div>
                            </div>
                            </p>
                            <p class="clear"></p>
                            <div class="box_check_label resize_box_width">
                                <div class="float_right">
                                    <span class="label_inner">Start time</span>
                                    <span class="label_inner">Finish time</span>
                                </div>
                                <div class="float_right">
                                    <div class="label_text height_label"></div>
                                    <span class="border_box2"></span>
                                </div>
                            </div>


                            <div id="time_change">
                                <div class="width_in3a float_left margintop">
                                    <div class="three_colum top_se">
                                        <?php
                                        echo $this->Form->input('Timelog.start_time', array(
                                            'class' => 'input_select input_se'
                                        ))
                                        ?>
                                        <script type="text/javascript">
                                            $(function() {
                                                $("#TimelogStartTime").combobox(<?php echo json_encode($arr_hour); ?>);
                                            });
                                        </script>
                                        <span class="icon_emp2 float_right position_icon" style="margin-top: -10px"></span>
                                    </div>
                                    <div class="four_colum top_se">
                                        <input class="input_1 float_left indent_input2 three_text" type="text" readonly>
                                    </div>
                                </div>
                                <div class="width_in3a float_left margintop">
                                    <div class="three_colum top_se">
                                        <?php
                                        echo $this->Form->input('Timelog.finish_time', array(
                                            'class' => 'input_select input_se'
                                        ))
                                        ?>
                                        <script type="text/javascript">
                                            $(function() {
                                                $("#TimelogFinishTime").combobox(<?php echo json_encode($arr_hour); ?>);
                                            });
                                        </script>
                                        <span class="icon_emp2 float_right position_icon" style="margin-top: -10px"></span>
                                    </div>
                                    <div class="four_colum top_se">
                                        <input class="input_1 float_left indent_input2 three_text" type="text" readonly>
                                    </div>
                                </div>
                                <!-- <p class="clear">
                                    <span class="label_1 float_left minw_lab2">or Entered time</span>
                                    <div class="width_in3a float_left indent_input_tp">
                                        <div class="three_colum top_se">
                                            <?php
                                            echo $this->Form->input('Timelog.or_entered_time', array(
                                                'class' => 'input_select input_se'
                                            ))
                                            ?>
                                            <script type="text/javascript">
                                                $(function() {
                                                    $("#TimelogOrEnteredTime").combobox(<?php echo json_encode($arr_hour); ?>);
                                                });
                                            </script>
                                        </div>
                                        <div class="four_colum top_se">
                                            <input class="input_1 float_left indent_input2 three_text" type="text" readonly>
                                        </div>
                                    </div>
                                </p> -->

                                <p class="clear">
                                    <span class="label_1 float_left minw_lab2">Total</span>
                                <div class="width_in3a float_left indent_input_tp">
                                    <div class="three_colum top_se">
                                        <?php
                                        echo $this->Form->input('Timelog.total_time', array(
                                            'class' => 'input_1 float_left indent_input color_hidden_important',
                                            'readonly' => true,
                                        ))
                                        ?>
                                    </div>
                                    <div class="four_colum top_se">
                                        <input class="input_1 float_left indent_input2 three_text color_hidden " type="text" readonly>
                                    </div>
                                </div>
                                </p>
                            </div>

                            <p class="clear">
                                <span class="label_1 float_left minw_lab2">Category</span>
                            <div class="width_in3a float_left indent_input_tp">
                                <div class="three_colum top_se">
                                    <?php
                                    echo $this->Form->input('Timelog.category', array(
                                        'class' => 'input_select input_se'
                                    ))
                                    ?>
                                    <script type="text/javascript">
                                        $(function() {
                                            $("#TimelogCategory").combobox(<?php echo json_encode($arr_category); ?>);
                                        });
                                    </script>
                                </div>
                                <div class="four_colum top_se">
                                    <input class="input_1 float_left indent_input color_hidden_important" type="text" readonly>
                                </div>
                            </div>
                            </p>

                            <p class="clear">
                                <span class="label_1 float_left minw_lab2">Billable</span>
                            <div class="width_in3a float_left indent_input_tp">
                                <div class="three_colum top_se">
                                    <div class="in_active3">
                                        <label class="m_check2">
                                            <?php
                                            echo $this->Form->input('Timelog.billable', array(
                                                'type' => 'checkbox'
                                            ));
                                            ?>
                                            <span class="bx_check dent_chk"></span>
                                        </label>
                                        <span class="inactive dent_check"></span>
                                        <p class="clear"></p>
                                    </div>
                                </div>
                                <div class="four_colum top_se">
                                    <input class="input_1 float_left indent_input3 three_text color_hidden " type="text" readonly>
                                </div>
                            </div>
                            </p>

                            <p class="clear">
                                <span class="label_1 float_left minw_lab2 fixbor3">Billed</span>
                            <div class="width_in3a float_left indent_input_tp">
                                <div class="three_colum top_se">
                                    <div class="in_active3">
                                        <label class="m_check2">
                                            <?php
                                            echo $this->Form->input('Timelog.billed', array(
                                                'type' => 'checkbox'
                                            ));
                                            ?>
                                            <span class="bx_check dent_chk"></span>
                                        </label>
                                        <span class="inactive dent_check"></span>
                                        <p class="clear"></p>
                                    </div>
                                </div>
                                <div class="four_colum top_se">
                                    <input class="input_1 float_left indent_input3 three_text color_hidden " type="text" readonly>
                                </div>
                            </div>
                            </p>
                            <p class="clear"></p>
                        </div>
                    </div>

                    <!--End Block 2-->

                    <!--Block 3-->
                    <div>
                        <span class="title_block">
                            <div class="float_left minw_lab2"><h4>Job / stage / task details</h4></div>
                            <div class="link_top_mod">
                                <div class="block_small">Balance</div>
                                <div class="block_small">Actual</div>
                                <div class="block_small">Budget</div>
                            </div>
                        </span>
                        <div class="tab_2_inner">
                            <p class="clear">
                                <span class="label_1 float_left minw_lab2 link_to_job_name">
                                    <?php echo isset($this->data['Timelog']['job_id'])&&is_object($this->data['Timelog']['job_id']) ? '<a href="'.URL.'/jobs/entry/'.$this->data['Timelog']['job_id'].'">Job</a>' : 'Job'; ?>
                                </span>
                                <div class="width_in3a float_left indent_input_tp">
                                    <div class="three_colum top_se">
                                        <?php
                                        echo $this->Form->hidden('Timelog.job_id');
                                        echo $this->Form->input('Timelog.job_no', array(
                                            'class' => 'input_1 float_left indent_input'
                                        ));
                                        ?>
                                        <span id="click_open_window_jobs" class="icon_down_new float_right fix_down_new"></span>
                                        <script type="text/javascript">
                                            $(function() {
                                                window_popup('jobs', 'Specify Job');
                                            });
                                        </script>

                                    </div>
                                    <div class="three_colum top_se">
                                        <?php
                                        echo $this->Form->input('Timelog.job_name', array(
                                            'class' => 'input_1 float_left indent_input2 three_text color_hidden_important'
                                        ));
                                        ?>

                                    </div>
                                    <div class="two_colum no_border width_colum3">
                                        <div class="input_1 float_left indent_input2 three_text">
                                            <div id="budget" class="block_small block_small_height"></div>
                                            <div class="block_small block_small_height actual"><?php echo isset($this->data['Timelog']['total_time']) ?  $this->data['Timelog']['total_time'] : ''; ?></div>
                                            <div id="balance" class="block_small block_small_height"></div>
                                        </div>
                                    </div>
                                </div>
                            </p>
                            <p class="clear">
                                <span class="label_1 float_left minw_lab2">Stage</span>
                            <div class="width_in3a float_left indent_input_tp">
                                <div class="three_colum top_se">

                                    <?php
                                    echo $this->Form->input('Timelog.stage_name', array(
                                        'class' => 'input_select input_se'
                                    ))
                                    ?>

                                </div>
                                <div class="three_colum top_se">
                                    <div class="input_1 float_left indent_input2 three_text color_hidden_important"></div>
                                </div>
                                <div class="two_colum no_border width_colum3">
                                    <div class="input_1 float_left indent_input2 three_text">
                                        <div id="budget" class="block_small block_small_height"></div>
                                        <div class="block_small block_small_height actual"><?php echo isset($this->data['Timelog']['total_time']) ?  $this->data['Timelog']['total_time'] : ''; ?></div>
                                        <div id="balance" class="block_small block_small_height"></div>
                                    </div>
                                </div>
                            </div>
                            </p>
                            <p class="clear">
                                <span class="label_1 float_left minw_lab2 link_to_task_name">
                                    <?php echo isset($this->data['Timelog']['task_id'])&&is_object($this->data['Timelog']['task_id']) ? '<a href="'.URL.'/tasks/entry/'.$this->data['Timelog']['task_id'].'">Task</a>' : 'Task'; ?>
                                </span>
                            <div class="width_in3a float_left indent_input_tp">
                                <div class="three_colum top_se">
                                     <?php
                                        echo $this->Form->hidden('Timelog.task_id');
                                        echo $this->Form->input('Timelog.task_no', array(
                                            'class' => 'input_1 float_left indent_input'
                                        ));
                                     ?>
                                    <span id="click_open_window_tasks"  class="icon_down_new float_right fix_down_new"></span>
                                    <script type="text/javascript">
                                        $(function() {
                                            window_popup('tasks', 'Specify Task');
                                        });
                                    </script>
                                </div>
                                <div class="three_colum top_se">
                                    <?php
                                    echo $this->Form->input('Timelog.task_name', array(
                                        'class' => 'input_1 float_left indent_input'
                                    ))
                                    ?>
                                </div>
                                <div class="two_colum no_border width_colum3">
                                    <div class="input_1 float_left indent_input2 three_text">
                                        <div id="budget" class="block_small block_small_height"></div>
                                        <div class="block_small block_small_height actual"><?php echo isset($this->data['Timelog']['total_time']) ?  $this->data['Timelog']['total_time'] : ''; ?></div>
                                        <div id="balance" class="block_small block_small_height"></div>
                                    </div>
                                </div>
                            </div>
                            </p>
                            <p class="clear">
                                <span class="label_1 float_left minw_lab2 fixbor3">Customer</span>
                            <div class="width_in3a float_left indent_input_tp">
                                <?php
                                echo $this->Form->input('Timelog.customer', array(
                                    'class' => 'input_1 float_left fix_ip color_hidden_important'
                                ))
                                ?>
                            </div>
                            </p>
                        </div>
                    </div>

                    <!--End Block 3-->
                    <p class="clear"></p>
                    <span class="title_block bo_ra2">
                        <span class="float_right">
                            <span class="check_right_ft2">
                                <div class="middle_check">
                                    <label class="m_check2">
                                        <input type="checkbox">
                                        <span class="bx_check"></span>
                                    </label>
                                </div>
                            </span>
                        </span>
                        <span class="error_text">Error</span>
                    </span>
                </div><!--END Tab1 -->
            </div>
        <?php echo $this->Form->end() ?>
        <div class="clear_percent_22 float_right">
            <div class="tab_1 full_width">
                <span class="title_block bo_ra1">
                    <span class="fl_dent"><h4>Comments</h4></span>
                    <span class="dent_bl_txt4 float_left">Go to timelog screen</span>
                    <form>
                        <div class="float_left">
                            <input class="btn_pur auto_width" type="button" value="Quick View">
                            <input class="btn_pur auto_width" type="button" value="Calendar">
                        </div>
                    </form>
                </span>
                <form>
                    <?php
                    echo $this->Form->input('Timelog.comment', array(
                        'class' => 'area_t4',
                        'type' => 'textarea'
                    ))
                    ?>
                </form>
                <p class="clear"></p>
                <span class="title_block bo_ra2"></span>
            </div><!--END Tab1 -->
            <div class="tab_1 full_width block_dent9 ">
                <span class="title_block bo_ra1">
                    <span class="float_left h_form">
                        <span class="fl_dent"><h4>Expenses for this timelog</h4></span>
                        <a onclick="add_expense()" title="Add expense" href="javascript:void(0)">
                            <span class="icon_down_tl top_f"></span>
                        </a>
                        <form>
                            <div class="float_left hbox_form dent_left_form">
                                <input class="btn_pur auto_width" type="button" value="Print expenses">
                            </div>
                        </form>
                    </span>
                </span>
                <ul class="ul_mag clear bg3">
                    <li class="hg_padd" style="width:10%">Code</li>
                    <li class="hg_padd" style="width:22%">Name / details</li>
                    <li class="hg_padd center_txt" style="width:9%">Date</li>
                    <li class="hg_padd center_txt" style="width:8%">Billable</li>
                    <li class="hg_padd center_txt" style="width:8%">Category</li>
                    <li class="hg_padd right_txt" style="width:10%">Cost price</li>
                    <li class="hg_padd right_txt" style="width:10%">Quantity</li>
                    <li class="hg_padd right_txt" style="width:11%">Ex Tax cost</li>
                    <li class="hg_padd bor_mt" style="width:1.5%"></li>
                </ul>
                <div id="container_expense" style="overflow-y:hidden;height:112px;">
                    <?php echo $this->element('../Timelogs/expense'); ?>

                </div>

                <p class="clear"></p>
                <span class="title_block bo_ra2">
                    <span class="bt_block float_right no_bg">
                        <span class="float_left">Total timelog expenses</span>
                        <input class="input_7 right_txt" id="total_expense" type="text" value="<?php echo $this->Common->format_currency($total_expense) ?>" readonly="readonly" disabled="disabled" />
                    </span>
                </span>
            </div><!--END Tab1 -->
        </div>



    </div>

    <p class="clear"></p>
</div>




<?php echo $this->element('../Timelogs/js'); ?>