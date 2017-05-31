<?php
    $i = 2;
    $delete = $this->Common->check_permission($controller.'_@_entry_@_delete',$arr_permission);
?><br>
<?php foreach ($arr_companies as $company) {
        if ($i == 2)
            $i = $i - 1;
        else
            $i = $i + 1;
        $company = array_merge(array(
                                    'no'    => '',
                                    'name'  => '',
                                    'type'  => [],
                                    'is_customer' => 0,
                                    'is_supplier' => 0,
                                    'phone' => '',
                                    'fax'   => '',
                                    'email' => '',
                                    'our_rep'   => '',
                                    'our_rep_id'=> '',
                                    'our_csr'   => '',
                                    'our_csr_id'=> ''
                                ), $company);
?>
    <ul class="ul_mag clear bg<?php echo $i ?>" id="company_<?php echo $company['_id']; ?>">
        <li class="hg_padd" style="width:.5%">
            <a href="<?php echo URL; ?>/companies/entry/<?php echo $company['_id']; ?>"><span class="icon_emp"></span></a>
        </li>
        <li class="hg_padd " style="width:5%">
            <?php echo $company['no'];  ?>
        </li>
        <li class="hg_padd" style="width:15%">
            <?php echo $company['name'];  ?>
        </li>
        <li class="hg_padd" style="width:10%">
            <?php
                if ($company['is_customer']) {
                    $company['type'][] = 'Customer';
                }
                if ($company['is_supplier']) {
                    $company['type'][] = 'Supplier';
                }
                echo implode(' / ', $company['type']);
            ?>
        </li>
        <li class="hg_padd" style="width:8%">
            <?php echo $company['phone'];  ?>
        </li>
         <li class="hg_padd " style="width:10%">
            <?php echo $company['fax'];  ?>
        </li>
        <li class="hg_padd " style="width:10%">
            <?php echo $company['email'];  ?>
        </li>
        <li class="hg_padd " style="width:10%">
            <?php
                if (is_object($company['our_rep_id'])) {
                    $company['our_rep'] = '<a href="'.URL.'/contacts/entry/'. $company['our_rep_id'] .'">'. $company['our_rep'] .'</a>';
                }
                echo $company['our_rep'];
            ?>
        </li>
        <li class="hg_padd" style="width:10%">
            <?php
                if (is_object($company['our_csr_id'])) {
                    $company['our_csr'] = '<a href="'.URL.'/contacts/entry/'. $company['our_csr_id'] .'">'. $company['our_csr'] .'</a>';
                }
                echo $company['our_csr'];
            ?>
        </li>
        <li class="hg_padd bor_mt" style="width:.5%">
            <?php if($delete){ ?>
            <div class="middle_check">
                <a href="javascript:void(0)" title="Delete link" onclick="delete_record('<?php echo $company['_id']; ?>')">
                    <span class="icon_remove2"></span>
                </a>
            </div>
        <?php } ?>
        </li>
    </ul>
<?php }
echo $this->element('popup/pagination_lists');
?>