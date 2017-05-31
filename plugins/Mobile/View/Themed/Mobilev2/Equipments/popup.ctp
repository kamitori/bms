<style type="text/css">
    .popup thead tr{
        width: 100%;
        color: #fff;
        background-color: #d82f2f;
    }
    .popup td{
        height: 25px;
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
    .check_cus_item{
        width:20%;
        float:left;
    }
</style>
<div class="ui-content content_popup">
    <form action="<?php echo URL; ?>/mobile/<?php echo $controller ?>/popup/<?php echo $key; ?>" id="<?php echo $controller ?>_popup_form<?php echo $key; ?>" method="post" accept-charset="utf-8" data-ajax="false">
        <div class="filter_box_sea" data-role="header">
            <div class="filter_box custom_head">
                <section class="filter_item link_pop" style="color: white;">
                    <h3>Change to Contact popup</h3>
                    <span id="changeContactPopup" class="show_cp custom_icon" style="margin: 5px;" ></span>
                </section>
                <script type="text/javascript">
                    $(function(){
                        $("#changeContactPopup").click(function(){
                            $.mobile.changePage("#contacts_popup");
                            var value = $("#inputHolder","#<?php echo $key ?>").val();
                            $("#inputHolder","#contacts_popup").val(value);
                        });
                    })
                </script>
            </div>
        </div>
        <div data-role="main" class="ui-content content_fix_M">
            <a href="" class="back_site">Back</a>
            <table data-role="table" data-mode="columntoggle" class="ui_table_customes popup ui-responsive ui-shadow ui-table ui-table-columntoggle table-stroke" id="table_<?php echo $key?>" data-filter="true" data-input=".window_popup_input_<?php echo $controller ?>_<?php echo $key; ?>">
                <thead id="pagination_sort">
                    <tr>
                        <th data-priority="1"><?php echo __('ID'); ?><span id="sort_first_name" rel="first_name" class="desc"></span></th>
                        <th data-priority="1" style="text-align:center;"><?php echo __('Name'); ?><span id="sort_is_customer" rel="is_customer" class="desc"></span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0; $STT = 0;
                    foreach ($arr_equipment as $value) {

                        $i = 1 - $i; $STT += 1;
                        ?>
                        <tr onclick="after_choose_equipments<?php if(substr($key,0,1) == '_')echo $key; ?>('<?php echo $value['_id']; ?>','<?php echo $value['name']; ?>', '<?php echo $key; ?>')">
                            <td align="center"><?php echo $STT; ?></td>
                            <td align="left"><?php echo $value['name']; ?></td>
                        </tr>
                    <?php } ?>

                    <?php if( $STT > 0 && $STT < 10 ){ // chỉ khi nào số lượng nhỏ hơn 10 mới add thêm mà thôi
                        if(!isset($limit))
                            $limit = 10;
                        $loop_for = $limit - $STT;
                        for ($j=0; $j < $loop_for; $j++) {
                            $i = 1 - $i;
                          ?>
                        <tr><td></td><td></td></tr>
                    <?php
                        }
                    } ?>
                </tbody>
            </table>
            <?php if( $STT == 0 ){ ?>
            <center style="margin-top:30px">(No data)</center>
            <?php } ?>
        </div>
        <input id="<?php echo $controller; ?>_popup_submit_<?php echo $key; ?>" style="display:none" data-role="none" type="submit" value="Search">

        <!-- Minh dang test -->
        <?php echo $this->element('popup/pagination'); ?>

    <?php echo $this->Form->end(); ?>
</div>
