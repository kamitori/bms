<?php echo $this->element('menu_calendar'); ?>

<style type="text/css">
    div#salesorders_calendar_month ul.list_day22 li {
        width: 13.1% !important;
    }

    .myscheduler a{
        color: #00f;
    }

    .dhx_after .dhx_month_body, .dhx_before .dhx_month_body {
        background-color: #ECECEC;
    }
    .dhx_after .dhx_month_head, .dhx_before .dhx_month_head {
        background-color: #E2E3E6;
        color: #94A6BB;
    }
    .clear_tophead2_nomargin {
        padding-bottom: 26px;
    }
    .fix_heili_old2 {
        background-color:#fff !important;
    }
</style>

<div id="content">

        <ul class="ul_mag clear bg top_header_inner2 ul_res2">
            <li class="hg_padd" style="width:1%"></li>
            <li class="hg_padd" style="width:3%">Ref no</li>
            <li class="hg_padd center_txt" style="width:18%">Heading</li>
            <li class="hg_padd" style="width:10%">Company</li>
            <li class="hg_padd" style="width:5%">Contact</li>
            <li class="hg_padd" style="width:15%">Our Rep</li>
            <li class="hg_padd" style="width:6%">Order date</li>
            <li class="hg_padd" style="width:6%">Due date</li>
            <li class="hg_padd" style="width:5%">Status</li>
        </ul>
    <div id="salesorders_calendar_day" class="w_ul2 ul_res2">
        <br>
        <?php
        $i = 1;
        foreach ($arr_salesorderdds as $key => $value) {
            $i = 3 - $i;
        ?>
        <ul class="ul_mag clear bg<?php echo $i; ?> <?php echo str_replace(' ', '_', $value['status_id']); ?>">
            <li class="hg_padd" style="width:1%">
                <a href="<?php echo URL; ?>/salesorders/entry/<?php echo $value['_id']; ?>">
                    <span class="icon_emp"></span>
                </a>
            </li>
            <li class="hg_padd center_txt" style="width:3%"><?php echo $value['code']; ?></li>
            <li class="hg_padd" style="width:18%"><?php echo $value['name']; ?></li>
            <li class="hg_padd" style="width:10%"><?php echo $value['company_name']; ?></li>
            <li class="hg_padd" style="width:5%"><?php echo $value['contact_name']; ?></li>
            <li class="hg_padd" style="width:15%"><?php echo $value['our_rep']; ?></li>
            <li class="hg_padd" style="width:6%"><?php echo $this->Common->format_date( $value['salesorder_date']->sec, false); ?></li>
            <li class="hg_padd" style="width:6%"><?php if(isset($value['payment_due_date']) && is_object($value['payment_due_date']))echo $this->Common->format_date( $value['payment_due_date']->sec, false); ?></li>
            <li class="hg_padd" style="width:5%"><?php echo $value['status']; ?></li>
            <li class="hg_padd" style="width:10%"></li>
        </ul>
        <?php } ?>

    </div>
</div>

<script type="text/javascript">

    function salesorderdds_calendar_onchange_status(){

        var contain = $("#salesorders_calendar_day");

        var status_id = $("#SalesorderStatusFilter").val();
        status_id = status_id.replace(" ","_");

        if( $.trim( status_id ).length < 1 ){
            $("ul", contain).show();
        }else{
            $("ul", contain).hide();
            $("ul." + status_id, contain).show();
        }
    }

    // function salesorderdds_calendar_onchange_type(object, item_id) {

    //     if($(object).hasClass("active")){
    //         $("li", "#calendar-left").removeClass("active");
    //         $("div", "#content_of_calendar").show();

    //     }else{
    //         $("li", "#calendar-left").removeClass("active");
    //         $(object).addClass("active");

    //         $("div", "#content_of_calendar").hide();
    //         $("." + item_id).show();
    //     }
    // }

    // $(function(){
    //     $("#<?php echo $_SESSION['arr_user']['contact_id'];?>").click();
    // });
</script>