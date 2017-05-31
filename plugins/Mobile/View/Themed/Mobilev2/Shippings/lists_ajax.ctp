<?php foreach ($arr_shippings as $value): ?>
    <li id="list-<?php echo $value['_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:30%">
                <a class="link-to-entry" href="<?php echo URL; ?>/mobile/shippings/entry/<?php echo $value['_id']; ?>"><?php echo $value['code']; ?></a>
            </div>
            <div class="ui-block-b" style="width:60%"><?php echo isset($value['company_name'])?$value['company_name']:'' ;?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
            <li>
                <div class="ui-block-a" style="width: 30%"><b>Shipping code</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['code']; ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>Type</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php if (isset($value['shipping_type']) && isset($arr_shippings_type[$value['shipping_type']])) echo $arr_shippings_type[$value['shipping_type']]; ?>
                </div>
            </li>


            <li>
                <div class="ui-block-a" style="width: 30%"><b>Company</b></div>
                <div class="ui-block-b" style="width:70%">
                        <?php if (isset($value['company_id']) && is_object($value['company_id']) ) { ?>
                        <a data-ajax="false" href="<?php echo URL; ?>/mobile/companies/entry/<?php echo $value['company_id']; ?>">
                        <?php echo $value['company_name']; ?>
                        </a>
                    <?php } ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width: 30%"><b>Contact</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo isset($value['contact_name'])?$value['contact_name']:'';?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width: 30%"><b>Phone</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo isset($value['phone'])?$value['phone']:'';?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width: 30%"><b>Email</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo isset($value['email'])?$value['email']:'';?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>Date</b></div>
                <div class="ui-block-b" style="width:70%"><?php echo $this->Common->format_date($value['shipping_date']->sec); ?></div>
            </li>

            <li>
                <div class="ui-block-a" style="width: 30%"><b>Our rep</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['our_rep'];?>
                </div>
            </li>

             <li>
                <div class="ui-block-a" style="width: 30%"><b>Our CSR</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['our_csr'];?>
                </div>
            </li>


            <li>
                <div class="ui-block-a" style="width:30%"><b>Due Date</b></div>
                <div class="ui-block-b" style="width:70%"><?php echo $this->Common->format_date($value['received_date']->sec); ?></div>
            </li>

            <li>
                <div class="ui-block-a" style="width: 30%"><b>Status</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo isset($value['shipping_status'])?$value['shipping_status']:'';?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width: 30%"><b>Payment terms</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo isset($value['payment_terms'])?$value['payment_terms']:'';?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width: 30%"><b>Tax</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo isset($value['taxval'])?$value['taxval']:'';?>
                </div>
            </li>

           

            <li>
                <a href="#popupDialog" class="callDelete" data-id="<?php echo $value['_id']; ?>" data-rel="popup" data-position-to="window" data-transition="pop" class="ui-btn ui-shadow ui-btn-inline ui-icon-delete ui-btn-icon-left ui-btn-b">Delete</a>
            </li>
        </ul>
    </li>
<?php endforeach ?>
<script type="text/javascript">
    $(".link-to-entry").click(function(){
        window.location.assign($(this).attr("href"));
        event.preventDefault();
    });
</script>