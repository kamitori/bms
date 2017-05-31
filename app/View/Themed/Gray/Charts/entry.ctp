<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Chart</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <meta content="" name="description" />
    <meta content="" name="author" />
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
    <link href="<?php echo URL ?>/css/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo URL ?>/css/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo URL ?>/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo URL ?>/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo URL ?>/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo URL ?>/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo URL ?>/plugins/nouislider/jquery.nouislider.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo URL ?>/plugins/nouislider/jquery.nouislider.pips.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo URL ?>/plugins/jquery-minicolors/jquery.minicolors.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo URL ?>/plugins/select2/select2.css" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN THEME STYLES -->
    <link href="<?php echo URL ?>/css/components-md.css" id="style_components" rel="stylesheet" type="text/css" />
    <link href="<?php echo URL ?>/css/plugins-md.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo URL ?>/css/layout.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo URL ?>/css/darkblue.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo URL ?>/css/custom.css" rel="stylesheet" type="text/css" />
    <style type="text/css" media="screen">
    .select2-container{
        z-index: 100000 !important;
    }
    </style>
    <!-- END THEME STYLES -->
    <link rel="shortcut icon" href="favicon.ico" />
</head>

<body class="page-md">
    <div class="page-content-wrapper">
        <div class="page-content" style="margin-left: 0px;">
            <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
            <div class="modal fade" id="add-chart" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form action="javascript:void(0)" id="add-chart-form" role="form">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                <h4 class="modal-title">Add chart</h4>
                            </div>
                            <div class="modal-body">
                                <div class="tabbable-line">
                                    <ul class="nav nav-tabs ">
                                        <li class="active">
                                            <a href="#main" data-toggle="tab">Main</a>
                                        </li>
                                        <li>
                                            <a href="#objects" data-toggle="tab">Objects</a>
                                        </li>
                                        <li>
                                            <a href="#share" data-toggle="tab">Share with</a>
                                        </li>
                                        <li>
                                            <a href="#other" data-toggle="tab">Other</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="main">
                                            <div class="form-group form-md-line-input form-md-floating-label">
                                                <input type="text" class="form-control" id="name" name="name">
                                                <label for="name">Chart name</label>
                                            </div>
                                            <div class="form-group form-md-line-input form-md-floating-label">
                                                <textarea class="form-control" rows="3" id="description" name="description" style="resize: vertical"></textarea>
                                                <label for="description">Chart description</label>
                                            </div>
                                            <div class="form-group form-md-line-input form-md-floating-label has-info">
                                                <select class="form-control" id="type" name="type">
                                                    <option value="line">Line</option>
                                                    <option value="area">Area</option>
                                                    <option value="column">Column</option>
                                                </select>
                                                <label for="type">Chart type</label>
                                            </div>
                                            <div class="form-group form-md-line-input form-md-floating-label has-info">
                                                <select class="form-control" id="xaxis" name="xaxis">
                                                    <option value="day">Day</option>
                                                    <option value="month">Month</option>
                                                    <option value="year">Year</option>
                                                </select>
                                                <label for="xaxis">xAxis</label>
                                            </div>
                                            <div class="form-group form-md-line-input form-md-floating-label has-info">
                                                <select class="form-control" id="yaxis" name="yaxis">
                                                    <option value="sum_sub_total">Total bf. Tax</option>
                                                    <option value="sum_amount">Total</option>
                                                </select>
                                                <label for="yaxis">yAxis</label>
                                            </div>
                                            <div class="form-group form-md-line-input form-md-floating-label">
                                                <div class="input-group right-addon" id="date-range">
                                                    <input type="text" class="form-control" id="range" name="range" value="<?php echo date('F d, Y', strtotime('-7 days')).' - '.date('F d, Y') ?>">
                                                    <label for="range">Date Ranges</label>
                                                    <span class="input-group-addon">
                                                        <button class="btn default date-range-toggle" type="button">
                                                            <i class="fa fa-calendar"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="objects">
                                            <div class="form-group form-md-line-input">
                                                <label class="col-md-2 control-label" for="form_control_1">Object</label>
                                                <div class="col-md-10">
                                                    <div class="input-group">
                                                        <div class="input-group-control">
                                                            <input type="text" id="object-name" class="form-control" placeholder="object name">
                                                            <div class="form-control-focus">
                                                            </div>
                                                        </div>
                                                        <span class="input-group-btn btn-right">
                                                        <button type="button" class="btn green-haze" onclick="addObject()" >
                                                        Add <i class="fa fa-plus"></i>
                                                        </button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="object-container" class="form-group" style="margin-top: 60px;">
                                                <div id="objects-tab" class="col-md-3 col-sm-3 col-xs-3"></div>
                                                <div id="objects-content" class="col-md-9 col-sm-9 col-xs-9"></div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="share">
                                            <div class="table-scrollable" style="height: 350px; overflow-y: auto;">
                                                <table class="table table-striped table-bordered table-hover dataTable no-footer" id="list-users" role="grid" aria-describedby="sample_3_info">
                                                    <thead>
                                                        <tr role="row">
                                                            <th class="table-checkbox sorting_disabled" rowspan="1" colspan="1" aria-label="" style="width: 24px;">
                                                                <div class="md-checkbox">
                                                                    <input type="checkbox" id="checkbox-all" class="md-check">
                                                                    <label for="checkbox-all">
                                                                        <span class="inc"></span>
                                                                        <span class="check"></span>
                                                                        <span class="box"></span>
                                                                </div>
                                                            </th>
                                                            <th class="sorting_asc" tabindex="0" rowspan="1" colspan="1" style="width: 105px;">
                                                                Name
                                                            </th>
                                                            <th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 178px;">
                                                                Email
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($users as $user):  ?>
                                                            <?php $user = array_merge(array('first_name' => '', 'last_name' => '', 'email' => ''), $user);  ?>
                                                                <tr class="gradeX " role="row">
                                                                    <td>
                                                                        <div class="md-checkbox">
                                                                            <input type="checkbox" name="shares[<?php echo $user['_id'] ?>]" id="checkbox-<?php echo $user['_id'] ?>" <?php if( $currentUser[ '_id']== $user[ '_id'] ) { echo 'checked disabled'; } ?> class="md-check">
                                                                            <label for="checkbox-<?php echo $user['_id'] ?>">
                                                                                <span class="inc"></span>
                                                                                <span class="check"></span>
                                                                                <span class="box"></span>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <?php echo $user['first_name'].' '.$user['last_name'] ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php echo $user['email'] ?>
                                                                    </td>
                                                                </tr>
                                                                <?php endforeach ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="other">
                                            <div class="form-group ">
                                                <label class="col-md-2 control-label">Width (%)</label>
                                                <div class="col-md-10 margin-bottom-40">
                                                    <div id="width-slider" class="margin-bottom-40">
                                                    </div>
                                                    <input type="hidden" id="width-input" name="width" value="25" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn blue">Save changes</button>
                                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <h3 class="page-title">
            Charts
            </h3>
            <div class="page-bar">
                <div class="page-toolbar">
                    <div class="btn-group pull-right">
                        <button data-toggle="modal" href="#add-chart" type="button" class="btn btn-fit-height red">
                            New Chart
                        </button>
                    </div>
                </div>
            </div>
            <!-- END PAGE HEADER-->
            <!-- BEGIN PAGE CONTENT-->
            <div class="row" id="sortable_portlets">
                <?php foreach($charts as $chart): ?>
                <div data-id="<?php echo $chart['id'] ?>" class="<?php echo $chart['width'] ?> column sortable chart-panels">
                    <div class="portlet portlet-sortable light bordered">
                        <div class="portlet-title">
                            <div class="caption" style="color: <?php echo $chart['color'] ?>">
                                <i class="icon-share" style="color: <?php echo $chart['color'] ?>"></i>
                                <span class="caption-subject bold uppercase"><?php echo $chart['name'] ?></span>
                            </div>
                            <div class="actions">
                                <a class="btn edit-chart btn-circle btn-icon-only btn-default" href="javascript:;">
                                    <i class="icon-wrench"></i>
                                </a>
                                <a class="btn remove-chart btn-circle btn-icon-only btn-default" href="javascript:;">
                                    <i class="icon-trash"></i>
                                </a>
                                <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;"></a>
                            </div>
                        </div>
                        <div class="portlet-body">
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <script src="<?php echo URL ?>/js/jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo URL ?>/js/jquery-migrate.min.js" type="text/javascript"></script>
    <!-- IMPORTANT! Load jquery-ui.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
    <script src="<?php echo URL ?>/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    <script src="<?php echo URL ?>/plugins/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js" type="text/javascript"></script>
    <script src="<?php echo URL ?>/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="<?php echo URL ?>/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
    <script src="<?php echo URL ?>/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="<?php echo URL ?>/js/jquery.blockui.min.js" type="text/javascript"></script>
    <script src="<?php echo URL ?>/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
    <script src="<?php echo URL ?>/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
    <script src="<?php echo URL ?>/plugins/bootstrap-daterangepicker/moment.min.js" type="text/javascript"></script>
    <script src="<?php echo URL ?>/plugins/bootstrap-daterangepicker/daterangepicker.js" type="text/javascript"></script>
    <script src="<?php echo URL ?>/plugins/nouislider/jquery.nouislider.all.js" type="text/javascript"></script>
    <script src="<?php echo URL ?>/plugins/jquery-minicolors/jquery.minicolors.min.js" type="text/javascript"></script>
    <script src="<?php echo URL ?>/plugins/select2/select2.min.js" type="text/javascript"></script>
    <script src="<?php echo URL ?>/plugins/highcharts/highcharts.js" type="text/javascript"></script>
    <script src="<?php echo URL ?>/plugins/highcharts/exporting.js" type="text/javascript"></script>
    <script type="text/javascript">
    $('body').on('click', '.portlet > .portlet-title .remove-chart', function(e) {
        e.preventDefault();
        var portlet = $(this).closest(".portlet");

        if ($('body').hasClass('page-portlet-fullscreen')) {
            $('body').removeClass('page-portlet-fullscreen');
        }

        var chartPanel = portlet.parent();
        var chartId = chartPanel.data('id');
        $.ajax({
            url: '<?php echo URL.'/charts/delete_chart' ?>/' + chartId,
            success: function(result) {
                if( result.status == 'ok' ) {
                    portlet.find('.portlet-title .fullscreen').tooltip('destroy');
                    portlet.find('.portlet-title > .tools > .reload').tooltip('destroy');
                    portlet.find('.portlet-title > .tools > .remove').tooltip('destroy');
                    portlet.find('.portlet-title > .tools > .config').tooltip('destroy');
                    portlet.find('.portlet-title > .tools > .collapse, .portlet > .portlet-title > .tools > .expand').tooltip('destroy');

                    chartPanel.remove();
                }
            }
        })

    });

    // handle portlet fullscreen
    $('body').on('click', '.portlet > .portlet-title .fullscreen', function(e) {
        e.preventDefault();
        var portlet = $(this).closest(".portlet");
        if (portlet.hasClass('portlet-fullscreen')) {
            $(this).removeClass('on');
            portlet.removeClass('portlet-fullscreen');
            $('body').removeClass('page-portlet-fullscreen');
            portlet.children('.portlet-body').css('height', 'auto');
        } else {
            var height = getViewPort().height -
                portlet.children('.portlet-title').outerHeight() -
                parseInt(portlet.children('.portlet-body').css('padding-top')) -
                parseInt(portlet.children('.portlet-body').css('padding-bottom'));

            $(this).addClass('on');
            portlet.addClass('portlet-fullscreen');
            $('body').addClass('page-portlet-fullscreen');
            portlet.children('.portlet-body').css('height', height);
        }
        var chartPanel = $(this).parents('.chart-panels');
        var chartId = chartPanel.data('id');
        var chart = chartPanel.data('chart');
        if( chart ) {
            drawChart(chartId, chart);
        }
    });
    $('body').on('click', '.md-checkbox > label, .md-radio > label', function() {
        var the = $(this);
        // find the first span which is our circle/bubble
        var el = $(this).children('span:first-child');

        // add the bubble class (we do this so it doesnt show on page load)
        el.addClass('inc');

        // clone it
        var newone = el.clone(true);

        // add the cloned version before our original
        el.before(newone);

        // remove the original so that it is ready to run on next click
        $("." + el.attr("class") + ":last", the).remove();
    });

    if ($('body').hasClass('page-md')) {
        // Material design click effect
        // credit where credit's due; http://thecodeplayer.com/walkthrough/ripple-click-effect-google-material-design
        var element, circle, d, x, y;
        $('body').on('click', 'a.btn, button.btn, input.btn, label.btn', function(e) {
            element = $(this);

            if (element.find(".md-click-circle").length == 0) {
                element.prepend("<span class='md-click-circle'></span>");
            }

            circle = element.find(".md-click-circle");
            circle.removeClass("md-click-animate");

            if (!circle.height() && !circle.width()) {
                d = Math.max(element.outerWidth(), element.outerHeight());
                circle.css({
                    height: d,
                    width: d
                });
            }

            x = e.pageX - element.offset().left - circle.width() / 2;
            y = e.pageY - element.offset().top - circle.height() / 2;

            circle.css({
                top: y + 'px',
                left: x + 'px'
            }).addClass("md-click-animate");

            setTimeout(function() {
                circle.remove();
            }, 1000);
        });
    }

    // Floating labels
    var handleInput = function(el) {
        if (el.val() != "") {
            el.addClass('edited');
        } else {
            el.removeClass('edited');
        }
    }

    $('body').on('keydown', '.form-md-floating-label .form-control', function(e) {
        handleInput($(this));
    });
    $('body').on('blur', '.form-md-floating-label .form-control', function(e) {
        handleInput($(this));
    });

    $('.form-md-floating-label .form-control').each(function() {
        if ($(this).val().length > 0) {
            $(this).addClass('edited');
        }
    });

    var changePostion = null;


    $('#date-range').daterangepicker({
            format: 'MM/DD/YYYY',
            separator: ' to ',
            startDate: moment().subtract('days', 7),
            endDate: moment(),
            minDate: '01/01/2012',
            maxDate: '12/31/2018',
        },
        function (start, end) {
            $('#date-range input').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY')).trigger('click');
        }
    );

    $('#list-users #checkbox-all').click(function() {
        var checkboxes = $('#list-users tbody input[type=checkbox]:not([disabled])');
        var checked = false;
        if ($(this).is(':checked')) {
            checked = true;
        }
        checkboxes.prop(':checked', checked).attr('checked', checked);
    });

    $('#list-users tbody tr').click(function(event) {
        if (!$(event.target).is(':checkbox') && !$(event.target).hasClass('box') && !$(event.target).hasClass('check')) {
            $(this).find('input[type=checkbox]').trigger('click');
        }
    });

    $('#width-slider').noUiSlider({
        start: 25,
        range: {
            min: 0,
            max: 125
        }
    })
    .noUiSlider_pips({
        mode: 'values',
        values: [25, 50, 75, 100],
        density: 4
    }).on('change', function ( event, value ) {
        if ( value < 25 ) {
            $(this).val(25);
        } else if ( value < 50 ) {
            $(this).val(50);
        } else if ( value < 75 ) {
            $(this).val(75);
        } else if ( value < 100 || value > 100 ) {
            $(this).val(100);
        }
    }).on('set', function( event, value) {
        $('#width-input').val(value);
    });

    $('#add-chart-form').submit(function(){
        var form = $(this);
        $.ajax({
            url: '<?php echo URL.'/charts/add_chart' ?>',
            type: 'post',
            data: $('input, select, textarea', form).serialize(),
            success: function(result) {
                if( result.status == 'ok' ) {
                    $('#add-chart').modal('hide');
                    $('#add-chart-form input').val('');
                    $('#add-chart-form #objects-tab').html('');
                    $('#add-chart-form #objects-content').html('');
                    var html = '<div data-id="'+ result.chartId +'" class="'+ result.chartWidth +' column sortable chart-panels">' +
                                    '<div class="portlet portlet-sortable light bordered">' +
                                        '<div class="portlet-title">' +
                                            '<div class="caption" style="color: '+ result.color +'">' +
                                                '<i class="icon-share" style="color: '+ result.color +'"></i>' +
                                                '<span class="caption-subject bold uppercase">'+ result.chartName +'</span>' +
                                            '</div>' +
                                            '<div class="actions">' +
                                                '<a class="btn edit-chart btn-circle btn-icon-only btn-default" href="javascript:;">' +
                                                    '<i class="icon-wrench"></i>' +
                                                '</a>' +
                                                '<a class="btn remove-chart btn-circle btn-icon-only btn-default" href="javascript:;">' +
                                                    '<i class="icon-trash"></i>' +
                                                '</a>' +
                                                '<a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;"></a>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div class="portlet-body">' +
                                        '</div>' +
                                    '</div>' +
                                '</div>';
                    $('#sortable_portlets').append(html);
                    findChart(result.chartId);
                    sortable();
                    window.scrollTo(0, document.body.scrollHeight);
                }
            }
        });
    });

    $('.chart-panels').each(function(){
        var chartPanel = $(this);
        var chartId = chartPanel.data('id');
        findChart(chartId);
    });

    sortable();

    function sortable()
    {
        $("#sortable_portlets").sortable({
            connectWith: ".portlet",
            items: ".chart-panels",
            opacity: 0.8,
            handle: '.portlet-title',
            coneHelperSize: true,
            placeholder: 'portlet-sortable-placeholder',
            forcePlaceholderSize: true,
            tolerance: "pointer",
            helper: "clone",
            tolerance: "pointer",
            forcePlaceholderSize: !0,
            helper: "clone",
            cancel: ".portlet-sortable-empty, .portlet-fullscreen", // cancel dragging if portlet is in fullscreen mode
            revert: 250, // animation in milliseconds
            update: function(b, c) {
                if( typeof changePostion == 'function' ) {
                    clearTimeout(changePostion);
                }
                changePostion = setTimeout(function() {
                    var position = [];
                    $('.chart-panels').each(function(){
                        var index = $(this).index();
                        var chartId = $(this).data('id');
                        index = parseInt(index) + 1;
                        position.push({'id': chartId, 'index': index});
                    });
                    $.ajax({
                        url: '<?php echo URL.'/charts/reorder' ?>',
                        type: 'post',
                        data: {
                            'position': position
                        },
                        success: function() {

                        }
                    });
                    changePostion = null;
                }, 2000);
            }
        });
    }

    function findChart(chartId)
    {
        $.ajax({
            url: '<?php echo URL ?>/charts/draw_chart/'+ chartId,
            success: function(result) {
                if( result.status == 'ok' ) {
                    var chartPanel = $('.chart-panels[data-id="'+ chartId +'"]');
                    chartPanel.data('chart', result);
                    drawChart(chartPanel.data('id'), result);
                }
            }
        });
    }

    function drawChart(chartId, chart)
    {
        $('.chart-panels[data-id="'+ chartId +'"]').find('.portlet-body')
            .highcharts({
                chart: {
                    type: chart.type
                },
                title: {
                    text: chart.title
                },
                subtitle: {
                    text: chart.description
                },
                xAxis: {
                    categories: chart.xAxis
                },
                yAxis: {
                    title: {
                        text: chart.yAxisText
                    }
                },
                plotOptions: {
                    line: {
                        dataLabels: {
                            enabled: true
                        },
                        enableMouseTracking: false
                    },
                    area: {
                        marker: {
                            enabled: false,
                            symbol: 'circle',
                            radius: 2,
                            states: {
                                hover: {
                                    enabled: true
                                }
                            }
                        }
                    },
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0">$ <b>{point.y:,.2f} </b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                series: chart.objects
            });
    }

    function addObject()
    {
        var objectName = $('#object-name').val().trim();
        if( objectName.length == 0 ) {
            return false;
        }
        $('#object-name').val('');
        var objectNumber = $('#object-container li.object').length;
        var htmlTab = '<li class="object" id="object-'+ objectNumber +'-tab">'
                        + '<a href="#object-'+ objectNumber +'-content" data-toggle="tab" aria-expanded="true" title="'+ objectName +'">'
                            + objectName
                        + '</a>'
                    + '</li>';
        var htmlContent = '<div class="tab-pane" id="object-'+ objectNumber +'-content">'
                           + '<div class="row">'
                           +    '<div class="col-md-6">'
                           +        '<div class="form-group form-md-line-input form-md-floating-label">'
                           +             '<input type="text" class="form-control" name="objects['+ objectNumber +'][name]" onchange="updateObjectName(this)" value="'+ objectName +'" >'
                           +             '<label for="name">Name</label>'
                           +         '</div>'
                           +    '</div>'
                           +    '<div class="col-md-6">'
                           +        '<div class="form-group form-md-line-input">'
                           +            '<select class="form-control" name="objects['+ objectNumber +'][module]">'
                           +            '<option value="quotation">Quote</option>'
                           +            '<option value="salesinvoice">Invoice</option>'
                           +            '<option value="salesorder">Order</option>'
                           +            '</select>'
                           +            '<label for="type">Module</label>'
                           +        '</div>'
                           +    '</div>'
                           + '</div>'
                           + '<div class="row">'
                           +    '<div class="col-md-6">'
                           +        '<a  onclick="addCondition('+ objectNumber +')" class="btn btn-xs green">'
                           +        'Add condition <i class="fa fa-plus"></i>'
                           +        '</a>'
                           +    '</div>'
                           +    '<div class="col-md-6 margin-bottom-40">'
                           +       '<input type="hidden" class="input-color" name="objects['+ objectNumber +'][color]" value="#0088cc" >'
                           +    '</div>'
                           + '</div>'
                           + '<div id="object-' + objectNumber + '-conditions">'
                           + '</div>'
                        + '</div>'
        if( objectNumber == 0 ) {
            $('#object-container #objects-tab').html('<ul class="nav nav-tabs tabs-left">'+ htmlTab +'</ul>');
            $('#object-container #objects-content').html('<div class="tab-content">'+ htmlContent +'</div>');
        } else {
            $('#object-container #objects-tab .nav-tabs').append(htmlTab);
            $('#object-container #objects-content .tab-content').append(htmlContent);
        }
        $('#object-container li.object:last a').trigger('click');
        $('#object-container [name="objects['+ objectNumber +'][name]"]').focus();

        $('#object-container [name="objects['+ objectNumber +'][color]"]').minicolors({
            control: 'hue',
            defaultValue: '',
            inline: false,
            letterCase: 'lowercase',
            opacity: false,
            position: 'bottom left',
            change: function(hex, opacity) {
                if (!hex) return;
                if (opacity) hex += ', ' + opacity;
            },
            theme: 'bootstrap'
        });
    }

    function updateObjectName(object)
    {
        var name = $(object).val();
        var id = $(object).parents('.tab-pane').attr('id').replace('object-', '').replace('-content', '');
        $('#object-container .nav-tabs li#object-'+ id +'-tab a').attr('title', name).text(name);
    }

    function addCondition(id)
    {
        var conditionId = $('#object-' + id + '-conditions .condition').length;
        var html = '<div class="condition row">'
                        + '<div class="col-md-4">'
                        +    '<div class="form-group form-md-line-input">'
                        +        '<select class="form-control" name="objects['+ id +'][conditions]['+ conditionId +'][field]">'
                        +            '<option value="company_id">Company</option>'
                        +            '<option value="our_rep_id">Our Rep</option>'
                        +            '<option value="our_csr_id">Our CSR</option>'
                        +            '<option value="job_id">Job</option>'
                        +        '</select>'
                        +        '<label for="type">Field</label>'
                        +    '</div>'
                        +'</div>'
                        + '<div class="col-md-3">'
                        +    '<div class="form-group form-md-line-input">'
                        +        '<select class="form-control" name="objects['+ id +'][conditions]['+ conditionId +'][operator]">'
                        +            '<option value="in">In</option>'
                        +            '<option value="not_in">Not In</option>'
                        +        '</select>'
                        +        '<label for="type">Operator</label>'
                        +    '</div>'
                        +'</div>'
                        + '<div class="col-md-4">'
                        +    '<div class="form-group form-md-line-input">'
                        +           '<select class="form-control" name="objects['+ id +'][conditions]['+ conditionId +'][values][]" multiple >'
                        +           '</select>'
                        +        '<label for="type">Values</label>'
                        +    '</div>'
                        +'</div>'
                        + '<div class="col-md-1">'
                        +   '<a class="btn red-intense" onclick="deleteCondition(this)" ><i class="fa fa-times"></i></a>'
                        +'</div>'
                    +'</div>';
        $('#object-' + id + '-conditions').append(html);
        var field = $('[name="objects['+ id +'][conditions]['+ conditionId +'][field]"]');
        var values = $('[name="objects['+ id +'][conditions]['+ conditionId +'][values][]"]');
        var setValueSelect = function () {
            values.select2({
                ajax: {
                   url: "<?php echo URL.'/charts/get_id' ?>?type="+ field.val(),
                   dataType: 'json',
                   delay: 250,
                   data: function (params) {
                     return {
                       query: params.term, // search term
                       page: params.page
                     };
                   },
                   processResults: function (result, page) {
                     // parse the results into the format expected by Select2.
                     // since we are using custom formatting functions we do not need to
                     // alter the remote JSON data
                     return {
                       results: result.data
                     };
                   },
                   cache: true
                 },
                escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
                minimumInputLength: 1,
                templateResult: function(result) {
                    return result.text;
                },
                templateSelection: function(result) {
                    return result.text;
                }
            });
        }
        setValueSelect();
        field.change(function() {
            values.val(null).trigger('change')
                    .select2('destroy');
            setValueSelect();
        });

    }

    function deleteCondition(object)
    {
        $(object).parents('.row.condition').fadeOut().remove();
    }

    function getViewPort() {
        var e = window,
            a = 'inner';
        if (!('innerWidth' in window)) {
            a = 'client';
            e = document.documentElement || document.body;
        }

        return {
            width: e[a + 'Width'],
            height: e[a + 'Height']
        };
    }
    </script>
</body>

</html>
