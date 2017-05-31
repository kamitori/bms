<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!--Favicon shortcut link-->
    <link href="<?php echo URL; ?>/favicon.ico" type="image/x-icon" rel="icon">
    <link href="<?php echo URL; ?>/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <title>JobTraq</title>
    <!--Declare page as mobile friendly -->
    <meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0"/>
    <!-- Declare page as iDevice WebApp friendly -->
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <!-- Stylesheet Load -->
    <?php echo $this->Minify->css(array('style','framework.style','framework','jquery.ui.min','jquery.mobile.1.4.3.min')); ?>
    <!--Page Scripts Load -->
    <?php echo $this->Minify->script(array('jquery.min','jquery-ui.min','jquery.mobile.1.4.3.min','custom')); ?>
    <script type="text/javascript">
        $(document).on("mobileinit", function(){
            $.mobile.changePage.defaults.changeHash = true;
        });
    </script>
</head>
<body>
    <div id="preloader">
        <div id="status">
            <p class="center-text">
                Loading the content...
                <em>Loading depends on your connection speed!</em>
            </p>
        </div>
    </div>
    <div class="page-content" id="main-page">
        <div class="header">
            <a href="#" class="show-sidebar"></a>
            <a href="#" class="hide-sidebar"></a>
            <p>
                <?php echo $controller; ?>
                <?php if(isset($arr_tabs) && !empty($arr_tabs)){ ?>
                 | <a id="subtab-popup" href="#popupNested" data-rel="popup" class="" data-transition="pop"><?php if(in_array($action,array('entry','lists'))) echo 'Choose sub tabs...'; else echo  ucfirst(str_replace('_', ' ', $action)); ?></a>
                <?php } ?>
            </p>

            <a href="<?php echo M_URL.'/users/logout'; ?>" rel="external" class="header-arrow"></a>

            <?php echo $this->element('entry_tab_option'); ?>
        </div>
        <div class="header-decoration"></div>
        <div class="content">
            <div class="container no-bottom">
                <?php echo $this->fetch('content'); ?>
            </div>
        </div>
    </div>
    <?php if(isset($arr_tabs) && !empty($arr_tabs)){ ?>
    <div data-role="popup" id="popupNested" data-theme="none">
        <div data-role="collapsible-set" data-theme="b" data-content-theme="a" data-collapsed-icon="arrow-r" data-expanded-icon="arrow-d" style="margin:0; width:250px;">
            <ul data-role="listview">
            <?php foreach($arr_tabs as $key =>  $tab){ ?>
                <?php if(!is_array($tab)){ ?>
                        <li><a href="<?php echo M_URL.'/'.$controller.'/'.$tab; ?>" rel="external" data-rel="dialog"><?php echo ucfirst(str_replace('_', ' ', $tab)); ?></a></li>
                <?php } else{ ?>
            <?php if(!isset($ul)){ ?>
            </ul>
            <?php $ul = 1; } ?>
                    <div data-role="collapsible">
                        <h2><?php echo ucfirst(str_replace('_', ' ', $key)); ?></h2>
                        <ul data-role="listview">
                    <?php foreach($tab as $value){ ?>
                            <li data-theme="a"><a href="<?php echo M_URL.'/'.$controller.'/'.$value; ?>" rel="external" data-rel="dialog"><?php echo ucfirst(str_replace('_', ' ', $value)); ?></a></li>
                    <?php } ?>
                        </ul>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
    <?php
        echo $this->element('header');
    ?>
    <script type="text/javascript">
        $(document).ready(function () {
            $(".content, .header").on("swipeleft",function(){
                $(location).attr('href', "<?php echo M_URL.'/'.$controller.'/nexts'; ?>");
            });
            $(".content,.header").on("swiperight",function(){
                $(location).attr('href', "<?php echo M_URL.'/'.$controller.'/prevs'; ?>");
            });
        });
    </script>
</body>
</html>