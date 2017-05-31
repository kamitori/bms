<style type="text/css">
    h2 .ui-block-a {
        width: 100%;
        color: #d82f2f;
    }
    .filter_box input{
        background-image:none !important;
        position: relative;
        z-index: 10;
        font-size:1em;
        margin:0;
    }
    .filter_item{
        width:100%; /*Edit*/
    }
</style>
    <form action="<?php echo URL; ?>/mobile/<?php echo $controller ?>/popup/<?php echo $key; ?>" id="<?php echo $controller ?>_popup_form<?php echo $key; ?>" method="post" accept-charset="utf-8" data-ajax="false">
        <div class="filter_box_sea" data-role="header">
            <div class="filter_box">
                <div style="width: 22%; float: left">
                    <a id="prev-pagination" data-role="button" class="ui-btn ui-shadow ui-corner-all" title="Back" onclick='$.mobile.changePage($("#main-page").parent());'  href="javascript:void(0)">BACK</a>
                </div>
                <section class="filter_item">
                    <input name="data[Contact][name]" class="window_popup_input_<?php echo $controller ?>_<?php echo $key; ?>" data-type="search" placeholder="Filter by: Name" value="" />
                </section>
                <?php
                echo $this->Form->input('Contact.inactive', array(
                    'type' => 'hidden',
                ));
                ?>
                <?php
                echo $this->Form->input('Contact.is_customer', array(
                    'type' => 'hidden',
                ));
                ?>
                <?php
                echo $this->Form->input('Contact.is_employee', array(
                    'type' => 'hidden',
                ));
                ?>
                <?php
                echo $this->Form->input('Contact.company', array(
                    'type'  => 'hidden'
                ));
                ?>
            </div>
        </div>

        <div data-role="main" class="ui-content">
            <ul data-role="listview" >
                <?php
                $i = 0; $STT = 0;
                foreach ($arr_contact as $value) {
                    $i = 1 - $i; $STT += 1;
                    ?>
                    <li id="list-<?php echo $value['_id'] ?>"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">

                        <h2>
                            <div onclick="after_choose_contacts<?php if (substr($key, 0, 1) == '_') echo $key; ?>('<?php echo $value['_id']; ?>', '<?php echo $value['first_name'] . ' ' . $value['last_name']; ?>', '<?php echo $key; ?>')"  class="ui-block-a"  style="width:40%">
                               <?php echo $value['no']; ?>
                            </div>
                            <div class="ui-block-b" style="width:40%"><?php echo $value['first_name'];echo ' ';echo $value['last_name']; ?></div>
                        </h2>

                        <ul data-role="listview" data-theme="b">
                            <li>
                                <div class="ui-block-a" style="width:40%"><b>Contact</b></div>
                                <div class="ui-block-b" style="width:60%">
                                    <?php if (isset($value['first_name']) ) { ?>
                                    <?php echo $value['first_name'] . ' ' . $value['last_name']; ?>
                                    <?php } ?>
                                </div>
                            </li>
                            <li>
                                <div class="ui-block-a" style="width:40%"><b>Customer</b></div>
                                <div class="ui-block-b" style="width:60%">
                                    <?php if (isset($value['is_customer']) && $value['is_customer']) { ?>
                                    <?php echo 'X'; ?>
                                    <?php } ?>
                                </div>
                            </li>
                            <li>
                                <div class="ui-block-a" style="width:40%"><b>Employee</b></div>
                                <div class="ui-block-b" style="width:60%">
                                    <?php if (isset($value['is_employee']) && $value['is_employee']) { ?>
                                    <?php echo 'X'; ?>
                                    <?php } ?>
                                </div>
                            </li>
                            <li>
                                <div class="ui-block-a" style="width:40%"><b>Default address</b></div>
                                <div class="ui-block-b" style="width:60%">
                                   
                                </div>
                            </li>
                            <li>
                                <div class="ui-block-a" style="width:40%"><b>Linked to company</b></div>
                                <div class="ui-block-b" style="width:60%">
                                    <?php
                                        if (is_object($value['company_id'])) {
                                            if(!isset($arr_company_tmp))$arr_company_tmp = array();
                                            if( !isset($arr_company_tmp[(string)$value['company_id']]) ){
                                                $arr_company = $model_company->select_one(array('_id' => new MongoId($value['company_id'])), array('_id', 'name'));
                                                if(isset($arr_company['name'])){
                                                    $arr_company_tmp[(string)$value['company_id']] = $arr_company['name'];
                                                    echo $arr_company['name'];
                                                }
                                            }else{
                                                echo $arr_company_tmp[(string)$value['company_id']];

                                            }
                                        }
                                    ?>
                                    <input type="hidden" id="after_choose_contacts<?php echo $key; ?><?php echo $value['_id']; ?>" value="<?php echo htmlentities(json_encode($value)); ?>">
                                </div>
                            </li>
                        </ul>

                    </li>
                <?php } ?>
            </ul>

            <?php if( $STT > 0 && $STT < 10 ){ // chỉ khi nào số lượng nhỏ hơn 10 mới add thêm mà thôi
                $loop_for = $limit - $STT;
                for ($j=0; $j < $loop_for; $j++) {
                    $i = 1 - $i;
                  ?>
                <ul><li></li><li></li><li></li></ul>
            <?php
                }
            } ?>

            <?php if( $STT == 0 ){ ?>
            <center style="margin-top:30px">(No data)</center>
            <?php } ?>
        </div>
        <input id="<?php echo $controller; ?>_popup_submit_<?php echo $key; ?>" style="display:none" data-role="none" type="submit" value="Search">
        <?php echo $this->element('pagination'); ?>
    </form>
