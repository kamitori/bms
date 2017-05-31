<div class="bg_menu"></div>
<div class="tab_1 half_width" style="width:40%;margin:0% auto;">
        <span class="title_block bo_ra1">
            <span class="float_left">
                <span class="fl_dent">
                    <h4>Print customer price list</h4>
                </span>
            </span>
        </span>
        <form id="customer_price_list" method="POST">
        <div class="tab_2_inner">
           <p class="clear">
                           <span class="label_1 float_left minw_lab2" style="height: 50%">
                               Base price list using price category</span>
                           </p>
                           <div class="width_in float_left indent_input_tp" style="width:61.5%">
                               <?php echo $this->Form->input('product_category', array(
                                       'class' => 'input_select input_3 validate',
                                       'readonly' => true,
                                       'name' => 'product_category',
                                       'style'=>'margin: 0px 16px 0px 0px;padding: 0 0px 0 2%;'
                               )); ?>
                               <span class="combobox_button" style="cursor:pointer;position:absolute; height:16px; width:16px; top:0; right: -12px;">
                                   <div class="combobox_arrow" style="margin-left:35%"></div>
                               </span>
                               <script type="text/javascript">
                                   $(function () {
                                       $("#product_category").combobox(<?php echo json_encode($arr_data['product_category']); ?>);
                                   });
                               </script>
                           </div>
            <p></p>


            <p class="clear">
                <span class="label_1 float_left minw_lab2" style="height: 50%">Group price list by product category</span>
                </p>
                <div class="width_in3a float_left indent_input_tp" >
                    <div class="in_active2">
                        <label class="m_check2">
                        <input id="group_by_category" name="group_by_category" value="0" type="checkbox"  >
                        <span class="bx_check dent_chk"></span>
                        </label>
                        <span class="inactive dent_check"></span>

                        <p class="clear"></p>
                     </div>
                </div>
            <p></p>

              <p class="clear">
                 <span class="label_1 float_left minw_lab2" style="height: 50%">Include Tax on price list</span>
                 </p>
                 <div class="width_in3a float_left indent_input_tp" >
                     <div class="in_active2">
                         <label class="m_check2">
                         <input id="include_tax" name="include_tax" value="0" type="checkbox"  >
                         <span class="bx_check dent_chk"></span>
                         </label>
                         <span class="inactive dent_check"></span>

                         <p class="clear"></p>
                      </div>
                 </div>
             <p></p>

            <p class="clear"><span class="label_1 float_left minw_lab2"></span></p>
            <p></p>

            <p class="clear"><span class="label_1 float_left minw_lab2"></span></p>
            <p></p>

            <p class="clear"><span class="label_1 float_left minw_lab2"></span></p>
            <p></p>

            <p class="clear"></p>
        </div>
        <div>
            <span class="title_block bo_ra2">
            <span class="icon_vwie indent_down_vwie2">
                <a >
                   Specify options for price list
                </a>
            </span>
            <ul class="menu_control float_right" style="margin:-1% -5%;width:35%">
                <li><a href="javascript:void()" id="CancelButton" style="margin-top: 6%;font-size: 10px;line-height: 4px;border-radius: 3px;box-shadow: 0px 1px 2px">Cancel</a></li>
                <li style="margin-left:10%"><a style="margin-top: 6%;font-size: 10px;line-height: 4px;border-radius: 3px;box-shadow: 0px 1px 2px" id="ContinueButton"
                href="javascript:void()">Continue</a></li>
            </ul>
            <p class="clear"></p>
        </span>
        </div>
    </form>
</div>

<script type="text/javascript">

    var productcategory = $("#product_category").val();


        $('#group_by_category').change(function() {
            if($(this).is(':checked'))
                $(this).val(1);
            else
                $(this).val(0);
        });

         $('#include_tax').change(function() {
                    if($(this).is(':checked'))
                        $(this).val(1);
                    else
                        $(this).val(0);
        });

    $('#ContinueButton').click(function(){



                if($("#product_category").val()=='')
                {

                     alerts('Message','Please enter a Category first.');

                }
                else
                {
                mywindow = window.open("about:blank", "Download PDF");
                    $.ajax({
                        url: "<?php echo URL; ?>/<?php echo $controller;?>/check_exist_price_list",
                        timeout: 15000,
                        type: "POST",
                        data: {data : $('#customer_price_list').serialize()},
                        async: false,
                        success: function(result){
                        console.log(result);
                             if(result=='empty')
                             {
                                 mywindow.close();
                                 alerts('Message','No record!');
                             }
                             else if(result != 'empty')
                                  mywindow.location = result;


                        }
                    });

                    return false;
			    }


    });


   $('#CancelButton').click(function(){
         window.location.assign("<?php echo URL; ?>/products/options/");
   });
</script>