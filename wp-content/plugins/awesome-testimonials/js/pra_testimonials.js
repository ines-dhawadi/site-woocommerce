(function($){
$(window).ready(function(){
	
	/*======================== Slider ========================*/	
	pra_rotators = $('.pra_rotator, .pra_slider2');
		
});


$(window).load(function(){
	pra_rotators.each(function(){
		pra_rotator_play($(this));
	});
});


function pra_rotator_play(pra_rotator) 
{
	
	pra_rotator.carouFredSel({
		responsive: true,
		width:'variable',
		height:'variable',
		prev: {
			button: function() {
				return $(this).parents().children(".pra_navigation").children(".pra_prev");
			}
		},
		next: {
			button: function() {
				return $(this).parents().children(".pra_navigation").children(".pra_next");
			}
		},
		pagination: {
			container: function() {
				return $(this).parents().next().children('.pra_paginationContainer');
			},
			anchorBuilder	: function(nr) {
				return "<div class='pra_image_container "+$(this).attr('data-imageradius')+"'><div class='pra_image' style='"+$(this).attr('data-bgimg')+"'></div><div class='pra_image_overlay' style='background-color:"+$(this).parent().attr('data-slider2unselectedoverlaybgcolor')+"'></div></div>";
			}
		},
		scroll: {
			items:1,          
			duration: pra_rotator.data('scrollduration'),
			fx: pra_rotator.data('transitioneffect')
		},
		auto: {
			play: pra_rotator.data('autoplay'),
			timeoutDuration:pra_rotator.data('pauseduration'),
			pauseOnHover:pra_rotator.data('pauseonhover')
		},
		items: {
			width:700
		},
		swipe: {
			onMouse: false,
			onTouch: true
		}
				
	});
			
}

}) (jQuery);