<div class="tab_1 full_width" id="block_full_noteactive">
  <!-- Header-->
  <span class="title_block bo_ra1">
      <span class="fl_dent">
          <h4><?php echo translate('Note & Activities'); ?></h4>
      </span>
      <?php echo $this->Js->link( '<span class="icon_down_tl top_f"></span>', '/'.$controller.'/noteactivity_add/'.$module_id,
          array(
              'update' => '#noteactivity',
              'title' => 'Add line',
              'escape' => false
          ) );
      ?>
  </span>
  <!--CONTENTS-->
  <div class="jt_subtab_box_cont" style="height:299px;overflow:auto">
      <ul class="ul_mag clear bg3">
          <li class="hg_padd" style="text-align:left;width:7%;"><?php echo translate('Type'); ?></li>
          <li class="hg_padd" style="text-align:left;width:16%;"><?php echo translate('Date'); ?></li>
          <li class="hg_padd" style="text-align:left;width:12%;"><?php echo translate('By'); ?></li>
          <li class="hg_padd" style="text-align:left;width:57%;"><?php echo translate('Details'); ?></li>
          <li class="hg_padd bor_mt" style="width:1%;"></li>
      </ul>
      <div class="clear">
          <?php
          $i = 1; $count = 0;
          foreach ($arr_noteactivity as $key => $value) { ?>
          <ul class="ul_mag clear bg<?php echo $i; ?>" id="noteactivity_<?php echo $value['_id']; ?>">
               <li class="hg_padd" style="text-align:left;width:7%;"><?php if(isset($value['type']))echo $value['type']; ?></li>
               <li class="hg_padd" style="text-align:left;width:16%;"><?php echo $this->Common->format_date($value['_id']->getTimestamp()); ?></li>
               <li class="hg_padd" style="text-align:left;width:12%;">
                    <?php if (isset($value['created_by'])){
                          if (is_object($value['created_by'])) {
                              if(!isset($arr_contact_tmp))$arr_contact_tmp = array();
                              if( !isset($arr_contact_tmp[(string)$value['created_by']]) ){
                                  $arr_contact = $model_contact->select_one(array('_id' => $value['created_by']), array('_id', 'first_name', 'last_name'));
                                  if(isset($arr_contact['first_name'])){
                                      $arr_contact_tmp[(string)$value['created_by']] = $arr_contact['first_name'].' '.$arr_contact['last_name'];
                                      echo $arr_contact['first_name'].' '.$arr_contact['last_name'];
                                  }
                              }else{
                                  echo $arr_contact_tmp[(string)$value['created_by']];
                              }
                          }
                      }
                      ?>
               </li>
               <li class="hg_padd" style="text-align:left;width:57%;">
                    <?php echo $this->Form->input('Noteactivity.content', array(
                         'class' => 'input_inner input_inner_w bg'.$i,
                         'value' => $value['content'],
                         'rel' => $value['_id'],
                         'onchange' => 'noteactivity_update(this)'
                    )); ?>
               </li>
               <li class="hg_padd bor_mt" style="width:1%;">
                    <div class="middle_check">
                         <a title="Delete link" href="javascript:void(0)" onclick="noteactivity_delete('<?php echo $value['_id']; ?>')">
                              <span class="icon_remove2"></span>
                         </a>
                    </div>
               </li>
           </ul>

          <?php $i = 3 - $i; $count += 1;
               }

               $count = 12 - $count;
               if( $count > 0 ){
                    for ($j=0; $j < $count; $j++) { ?>
                         <ul class="ul_mag clear bg<?php echo $i; ?>"></ul>
            <?php $i = 3 - $i;
                    }
               }
          ?>
      </div>
  </div>
  <!--Footer-->
  <span class="title_block bo_ra2"></span>
</div>