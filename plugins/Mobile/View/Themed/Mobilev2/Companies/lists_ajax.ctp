<style type="text/css">
	.ui-popup-screen.in {
	    position: fixed;
	}
</style>
<ul data-role="listview" >
<?php foreach ($arr_companies as $value): ?>
    <li id="list-<?php echo $value['_id'] ?>"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:40%">
                <a class="link-to-entry" data-ajax="false" href="<?php echo URL; ?>/mobile/companies/entry/<?php echo $value['_id']; ?>"><?php echo $value['no']; ?></a>
            </div>
            <div class="ui-block-b" style="width:40%"><?php echo $value['name']; ?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
            <li>
                <div class="ui-block-a" style="width:40%"><b>Company name</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php if (isset($value['name']) ) { ?>
                    <?php echo $value['name']; ?>
                    <?php } ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:40%"><b>Type</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php if (isset($value['type_name']) ) { ?>
                    <?php echo $value['type_name']; ?>
                    <?php } ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:40%"><b>Phone</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php if (isset($value['phone']) ) { ?>
                    <?php echo $value['phone']; ?>
                    <?php } ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:40%"><b>Fax</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php if (isset($value['fax'])) echo $value['fax']; ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:40%"><b>Email</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php if (isset($value['email'])) echo $value['email']; ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:40%"><b>Our Rep</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php
                            if (isset($value['our_rep_id'])){
                                if (is_object($value['our_rep_id'])) {
                                    if(!isset($arr_contact_tmp)) $arr_contact_tmp = array();
                                    if( !isset($arr_contact_tmp[(string)$value['our_rep_id']]) ){
                                        $arr_contact = $model_contact->select_one(array('_id' => $value['our_rep_id']), array('_id', 'first_name', 'last_name'));
                                        if(isset($arr_contact['first_name'])){
                                            $arr_contact_tmp[(string)$value['our_rep_id']] = $arr_contact['first_name'].' '.$arr_contact['last_name'];
                                        ?>
                    <?php echo $arr_contact['first_name'].' '.$arr_contact['last_name']; ?>
                    <?php
                        }
                        }else{
                        ?>
                    <?php echo $arr_contact_tmp[(string)$value['our_rep_id']]; ?>
                    <?php
                        }
                        }
                        }
                     ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:40%"><b>Our CSR</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php
                            if (isset($value['our_csr_id'])){
                                if (is_object($value['our_csr_id'])) {
                                    if(!isset($arr_contact_tmp)) $arr_contact_tmp = array();
                                    if( !isset($arr_contact_tmp[(string)$value['our_csr_id']]) ){
                                        $arr_contact = $model_contact->select_one(array('_id' => $value['our_csr_id']), array('_id', 'first_name', 'last_name'));
                                        if(isset($arr_contact['first_name'])){
                                            $arr_contact_tmp[(string)$value['our_csr_id']] = $arr_contact['first_name'].' '.$arr_contact['last_name'];
                                        ?>
                    <?php echo $arr_contact['first_name'].' '.$arr_contact['last_name']; ?>
                    <?php
                        }
                        }else{
                        ?>
                    <?php echo $arr_contact_tmp[(string)$value['our_csr_id']]; ?>
                    <?php
                        }
                        }
                        }
                     ?>
                </div>
            </li>


            <li>
            	<a href="#popupDialog" class="callDelete" data-id="<?php echo $value['_id']; ?>" data-rel="popup" data-position-to="window" data-transition="pop" class="ui-btn ui-shadow ui-btn-inline ui-icon-delete ui-btn-icon-left ui-btn-b">Delete</a>
            </li>
        </ul>
    </li>
    <a data-icon="delete" href="#"></a>
<?php endforeach ?>
</ul>

<script type="text/javascript">
    $(".link-to-entry").click(function(){
    	window.location.assign($(this).attr("href"));
    });
    $(".callDelete").click(function(){
    	var value = $(this).attr("data-id");
    	$("#hiddenId").val(value);
    });

</script>