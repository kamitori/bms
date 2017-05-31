<script type="text/javascript">
    // $(function() {
    //     $('#seach_form input').change(function() {
    //         // goi function ajax_sort
    //         $('#offset').val(0);
    //         ajax_sort('_id', 'desc');
    //         // vo hieu hoa submit from luc bam enter
    //         $('#seach_form').submit(function() {
    //             return false;
    //         });
    //     });
    //     // set gia tri khi no la null
    //     $('.icon_closef').click(function() {
    //         // khi bam clear thi set offser = 0
    //         $('#offset').val(0);
    //         id = $(this).attr('id');
    //         $('#' + id).val('');
    //         // goi function ajax_sort
    //         ajax_sort('_id', 'desc');
    //     });
    //     // goi function
    //     load_more();
    //     sort();
    // });
    // // kiem tra bat su kien sort
    // function sort() {

    //     $("#sort span").click(function() {
    //         // khi bam sort thi set $offset = 0
    //         $('#offset').val(0);
    //         // sort_key la ten cua field trong db
    //         sort_key = $(this).attr('id');
    //         // sort_type la desc hay asc
    //         sort_type = $(this).attr('class');
    //         // kiem tra no la desc hay asc de gan class tuong ung
    //         if (sort_type === 'desc') {
    //             $(this).attr('class', 'asc');
    //             ajax_sort(sort_key, sort_type);
    //         }
    //         if (sort_type === 'asc') {
    //             $(this).attr('class', 'desc');
    //             ajax_sort(sort_key, sort_type);
    //         }
    //         // reset tat ca cac class khac ve desc , ngoai tru class dang chon
    //         $('#sort span').each(function() {
    //             id_reset_class = $(this).attr('id');
    //             if (id_reset_class !== sort_key) {
    //                 $(this).attr('class', 'desc');
    //             }
    //         });
    //     });
    // }
    // // xu ly sort
    // function ajax_sort(sort_key, sort_type) {
    //     $("#sort_key").val(sort_key);
    //     $("#sort_type").val(sort_type);
    //     $.ajax({
    //         type: 'POST',
    //         data: $('#seach_form').serialize(),
    //         success: function(data) {
    //             // hien thi quick_view_ajax
    //             $('#quick_view_content').html(data);
    //             // sort xong thi gan gia tri offset tu config
    //             $('#offset').val(<?php echo LIST_LIMIT ?>);
    //         }
    //     });
    // }
    // // chuc nang load more
    // function load_more() {
    //     $(window).scroll(function() {
    //         offset = $('#offset').val();
    //         if (offset == 0) {
    //             return false;
    //         }
    //         if ($(window).scrollTop() == $(document).height() - $(window).height()) {
    //             $.ajax({
    //                 type: 'post',
    //                 data: $('#seach_form').serialize(),
    //                 success: (function(html) {
    //                     if (html != "") {
    //                         // hien thi quick_view_ajax
    //                         $('#quick_view_content').append(html);
    //                         // gan gia tri offset khi sau khi load
    //                         $('#offset').val(parseInt(offset) + <?php echo LIST_LIMIT ?>);
    //                     }
    //                     else {
    //                         // gan offset = 0 khi khong con du lieu
    //                         $('#offset').val(0);
    //                     }
    //                 })
    //             });
    //         }
    //     });
    // }

</script>
