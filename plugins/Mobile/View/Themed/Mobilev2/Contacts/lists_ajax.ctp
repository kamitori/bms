<style type="text/css">
	.ui-popup-screen.in {
	    position: fixed;
	}
</style>
<ul data-role="listview" >
<?php foreach ($arr_contacts as $value): ?>
    <li id="list-<?php echo $value['_id'] ?>"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:40%">
                <a class="link-to-entry" data-ajax="false" href="<?php echo URL; ?>/mobile/contacts/entry/<?php echo $value['_id']; ?>"><?php echo $value['no']; ?></a>
            </div>
            <div class="ui-block-b" style="width:40%"><?php echo $value['first_name'];echo ' ';echo $value['last_name']; ?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
            <li>
                <div class="ui-block-a" style="width:40%"><b>Title</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php if (isset($value['title']) ) { ?>
                    <?php echo $value['title']; ?>
                    <?php } ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:40%"><b>Direct Dial</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php if (isset($value['direct_dial']) ) { ?>
                    <?php echo $value['direct_dial']; ?>
                    <?php } ?>
                </div>
            </li>


            <li>
                <div class="ui-block-a" style="width:40%"><b>Mobile</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php if (isset($value['mobile']) ) { ?>
                    <?php echo $value['mobile']; ?>
                    <?php } ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:40%"><b>Email</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php if (isset($value['email']) ) { ?>
                    <?php echo $value['email']; ?>
                    <?php } ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:40%"><b>Company</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php if (isset($value['company_id']) && is_object($value['company_id']) ) { ?>
                    <a data-ajax="false" href="<?php echo URL; ?>/mobile/companies/entry/<?php echo $value['company_id']; ?>">
                    <?php echo $value['company']; ?>
                    </a>
                    <?php } ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:40%"><b>Responsible</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php
                        	if (isset($value['our_rep_id'])){
                        		if (is_object($value['our_rep_id'])) {
                        			if(!isset($arr_contact_tmp))$arr_contact_tmp = array();
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
                <div class="ui-block-a" style="width:40%"><b>Type</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php if (isset($value['type']) && isset($arr_contacts_type[$value['type']])) echo $arr_contacts_type[$value['type']]; ?>
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