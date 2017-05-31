//////////////////
//Site Preloader//
///////////////////
$(window).load(function() { // makes sure the whole site is loaded
	$("#status").fadeOut(); // will first fade out the loading animation
		$("#preloader").delay(350).fadeOut("slow"); // will fade out the white DIV that covers the website.
})


$(document).ready(function(){
	////////////////////
	//Sidebar Deployer//
	////////////////////
	$('.show-sidebar').click(function(){
		$('.page-content').animate({
			left:'270px'

		}, 500, 'easeInOutExpo', function(){
			$('.page-content').css('position', 'fixed');
		});

		$('.show-sidebar').hide();
		$('.hide-sidebar').show();
		return false
	});

	$('.hide-sidebar').click(function(){
		$('.page-content').css('position', 'absolute');
		$('.page-content').animate({
			left:'0px'
		}, 500, 'easeInOutExpo');
		$('.show-sidebar').show();
		$('.hide-sidebar').hide();
		return false
	});

	$('.hide2-sidebar').click(function(){
		$('.page-content').css('position', 'absolute');
		$('.page-content').animate({
			left:'0px'
		}, 500, 'easeInOutExpo');
		$('.show-sidebar').show();
		$('.hide-sidebar').hide();
		return false
	});

	$('.page-content').click(function(){
		$('.page-content').css('position', 'absolute');
		$('.page-content').animate({
			left:'0px'
		}, 500, 'easeInOutExpo');
		$('.show-sidebar').show();
		$('.hide-sidebar').hide();
	});

	////////////////////
	//Submenu Deployer//
	////////////////////
	$('.deploy-submenu').click(function(){
		$(this).parent().find('.submenu').toggle(500, 'easeInOutExpo');
		return false;
	});

	$('.dropdown-hidden').hide();
	$('.dropdown-item').hide();

	$('.dropdown-deploy').click(function(){
		$(this).parent().parent().find('.dropdown-item').show(200);
		$(this).parent().parent().find('.dropdown-hidden').show();
		$(this).hide();
		return false;
	});

	$('.dropdown-hidden').click(function(){
		$(this).parent().parent().find('.dropdown-item').hide(200);
		$(this).parent().parent().find('.dropdown-deploy').show();
		$(this).parent().parent().find(this).hide();
		return false;
	});

	$('.sliding-door-top').click(function(){
		$(this).animate({
			left:'101%'
		}, 500, 'easeInOutExpo');
		return false;
	});

	$('.sliding-door-bottom a em').click(function(){
		$(this).parent().parent().parent().find('.sliding-door-top').animate({
			left:'0px'
		}, 500, 'easeOutBounce');
		return false
	});
});