<div class="page-sidebar">
    <div class="page-sidebar-scroll">
        <div class="clear"></div>
        <img class="sidebar-logo replace-2x" src="<?php  echo URL; ?>/theme/mobilev2/images/misc/logow.png" alt="img">
        <div class="menu">
            <div class="menu-item">
                <strong class="home-icon"></strong>
                <a class="menu-enabled" rel="external" href="<?php echo M_URL; ?>">Home</a>
            </div>
            <?php foreach($arr_menu as $menu => $sub_menu){ ?>
           	<div class="menu-item">
                <strong class="<?php echo $menu ?>"></strong>
                <a class="menu-disabled deploy-submenu" href="#"><?php echo $sub_menu['name']; ?></a>
                <div class="clear"></div>
                <div class="submenu">
                	<?php unset($sub_menu['name']); ?>
                	<?php foreach($sub_menu as $controller => $value){ ?>
                	<?php if($value['inactive']) continue; ?>
                    <a href="<?php echo M_URL.'/'.$controller.'/entry'; ?>" rel="external"><?php echo $value['name']; ?></a><em class="submenu-decoration"></em>
                    <?php } ?>
                </div>
            </div>
           	<?php }?>
        </div>
        <p class="sidebar-copyright center-text">Copyright © 2013 Anvy Digital All rights reserved.</p>
        <div class="clear"></div>
    </div>
</div>
<script type="text/javascript">
    var currentURL = window.location.href;
    currentURL = currentURL.replace("<?php echo M_URL; ?>/","");
    currentURL = currentURL.replace("<?php echo M_URL; ?>","");
    var replaceChar = ["#","/"];
    for(var i in replaceChar){
        currentURL = currentURL.split(replaceChar[i]);
        currentURL = currentURL[0];
    }
    $(".submenu > a",".menu-item").each(function(){
        if(currentURL == "")
            return false;
        var href = $(this).attr("href");
        if(href.match(currentURL)){
            $(this).css({"background-color": "rgba(255,255,255,0.07)"});
            $(".menu-enabled").removeClass("menu-enabled");
            $(this).parent().show();
            $(".deploy-submenu",$(this).parent().parent()).removeClass("menu-disabled").addClass("menu-enabled");
            return false;
        }
    })
</script>