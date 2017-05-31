<?php echo $this->element('../Homes/dashboard_menu', array('noLeftMenu' => true)); ?>
<div id="content" style="height: 100%;">
    <iframe id="chart-iframe" frameborder="0" scrolling="no" style="overflow: hidden; height: 550px; width: 100%" src="<?php echo URL.'/charts' ?>"></iframe>
</div>
<script src="<?php echo URL ?>/js/jquery.browser.js" type="text/javascript"></script>
<script src="<?php echo URL ?>/js/jquery.iframe-auto-height.plugin.1.9.5.min.js" type="text/javascript"></script>
<script type="text/javascript">
var windowResizeFunction = function(resizeFunction, iframe) {
    $(window).resize(function() {
        resizeFunction(iframe);
    });
};

// fire iframe resize when a link is clicked
var clickFunction = function(resizeFunction, iframe) {
    $('iframe#chart-iframe').click(function() {
        resizeFunction(iframe);
    });
};
var iframeResize = function() {
    $('iframe#chart-iframe').trigger('click');
}
$('iframe#chart-iframe').iframeAutoHeight({
    minHeight: 550,
    animate: true,
    triggerFunctions: [
        windowResizeFunction,
        clickFunction
    ]
});
</script>
