<span class="float_left jtchecktype" style=" <?php if(isset($arr_field['css'])) echo $arr_field['css'];?>">
    <label class="m_check2">
        <input name="<?php echo $keys;?>" id="<?php echo $keys;?>" type="checkbox"  <?php if($arr_field['default']=='1'){?>checked="checked"<?php }?> <?php if(isset($arr_field['lock']) && $arr_field['lock']=='1'){?>disabled <?php }?> />
        <span style=" <?php if(isset($arr_field['checkcss'])) echo $arr_field['checkcss'];?>"></span>
    </label>
    <span class="fl_dent" for="<?php echo $keys;?>">&nbsp;<?php if(isset($arr_field['label'])) echo $arr_field['label'];?></span>
</span>
<?php if(isset($arr_field['moreinline'])) echo $arr_field['moreinline'];?>