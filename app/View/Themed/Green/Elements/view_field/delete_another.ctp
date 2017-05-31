<?php if(!isset($arr_vls['xempty'][$viewkeys]) ){  ?>
<div class="jt_right_check">
    <a title="Delete" rel="<?php echo $arr_vls['_id']; ?>@<?php echo $arr_view_st['node']; ?>" rev="<?php echo $arr_view_st['rev']; ?>" id="del_<?php echo $arr_view_st['node']; ?>_<?php echo $arr_vls['_id']; ?>" class="deleteopt_link del_<?php echo $arr_view_st['node']; ?>" href="javascript:void(0);">
        <span class="icon_remove2"></span>
    </a>
</div>
<?php } ?>