(function($){
function bt_portfolioHoverEffects() { 

	if(!$('body').hasClass('mobile') && !navigator.userAgent.match(/(iPad|IEMobile)/)) {
		
		//style 1 & 2
		$('.portfolio-items:not([data-ps="7"]) .col .work-item:not(.style-3-alt):not(.style-3):not([data-custom-content="on"])').hover(function(){
			$(this).find('.work-info .vert-center').css({'margin-top' : 0});
			$(this).find('.work-info, .work-info .vert-center > *, .work-info > i').css({'opacity' : 1});
			$(this).find('.work-info-bg').css({ 'opacity' : 0.9 });
		},function(){
			$(this).find('.work-info .vert-center').css({ 'margin-top' : -20 });
			$(this).find('.work-info, .work-info .vert-center > *:not(.mfp-figure), .work-info > i').css({ 'opacity' : 0 });
			$(this).find('.work-info-bg').css({ 'opacity' : 0 });
		});	
		//style 3
		$('.portfolio-items .col .work-item.style-3').hover(function(){
			$(this).find('.work-info-bg').css({ 'opacity' : 0 });
		},function(){
			$(this).find('.work-info-bg').css({ 'opacity' : 0.45 });
		});
		
		//style 4
		$('.portfolio-items .col .work-item.style-4').hover(function(){
			$(this).find('img').stop().animate({
				'top' : '-'+$(this).find('.work-info .bottom-meta').outerHeight()/2+'px'
			},250,'easeOutCubic');
			$(this).find('.work-info .bottom-meta').addClass('shown').stop().animate({
				'bottom' : '0px'
			},320,'easeOutCubic');
		},function(){
			$(this).find('img').stop().animate({
				'top' : '0px'
			},250,'easeOutCubic');
			$(this).find('.work-info .bottom-meta').removeClass('shown').stop().animate({
				'bottom' : '-'+$(this).find('.work-info .bottom-meta').outerHeight()-2+'px'
			},320,'easeOutCubic');
		
		});
	
	} 
	////mobile event
	else {
		if($('body').hasClass('mobile') || navigator.userAgent.match(/(iPad|IEMobile)/) ){
			
			//if using more details
			if($('.portfolio-items .col .work-item').find('a:not(".pp")').length > 0){
				$('.portfolio-items .col .work-item').find('a.pp').css('display','none');
			} 
			
			//if only using pp
			else {
				$('.portfolio-items .col .work-item').find('a:not(".pp")').css('display','none');
			}
		
		} else {
			$('.portfolio-items .col .work-item').find('a').css('display','inline');
		}
	}
}

if ( btpnx ) {
	if ( btqci ) {
		var ap_n = btpnx+1;
		(function($){
			$( "#bt-load-more-archive-posts" ).click(function() {
				let j = ( bttag ) ? 'tags':'categories';
				let jqxhr = $.getJSON( "https://www.biddytarot.com/wp-json/wp/v2/posts?_embed&per_page=6&"+j+"="+btqci+"&page=" + ap_n, 
					function(d) {
					//	console.log(d);
						for(i = 0; i < d.length; i++) { 
						  let
						    p = d[i],
						    m = (p._embedded['wp:featuredmedia'])?p._embedded['wp:featuredmedia'][0].source_url:'h',
						    f = (p)?'<img src="'+m+'" />':'';
						  $('<div class="col span_4 element" data-project-cat="" data-default-color="true" data-title-color="" data-subtitle-color="" style="" data-delay-amount="0"><div class="inner-wrap" data-animation="none"><div class="work-item style-2">'+f+'<div class="work-info-bg"></div><div class="work-info"><a href="'+p.link+'"></a><div class="vert-center"><h3>'+p.title.rendered+'</h3></div></div></div></div></div>' ).appendTo('#bt-recent-archive-posts');
							console.log(p.id);
						}
						ap_n = ap_n + 1;
				})
					.done(function(d) {
						bt_portfolioHoverEffects();
					})
					.fail(function() {
						$('#bt-load-more-archive-posts').remove();
					});
			});
		})(jQuery);
	}

//	var bp_n = btpnx+1;
	var 
	hold = btpnx*6,
	bp_n = hold - stick;
	(function($){
		$( "#bt-load-more-blog-posts" ).click(function() {
			let jqxhr = $.getJSON( "https://www.biddytarot.com/wp-json/wp/v2/posts?_embed&per_page=6&categories_exclude=140&offset=" + bp_n, function(d) {
				if (!d.length) {
					$('#bt-load-more-blog-posts').remove();
				}
				for(i = 0; i < d.length; i++) { 
				  let
						p = d[i],
						m = (p._embedded['wp:featuredmedia'])?p._embedded['wp:featuredmedia'][0].source_url:'h',
						f = (p)?'<img src="'+m+'" />':'';
				  $('<div class="col span_4 element" data-project-cat="" data-default-color="true" data-title-color="" data-subtitle-color="" style="" data-delay-amount="0"><div class="inner-wrap" data-animation="none"><div class="work-item style-2">'+f+'<div class="work-info-bg"></div><div class="work-info"><a href="'+p.link+'"></a><div class="vert-center"><h3>'+p.title.rendered+'</h3></div></div></div></div></div>' ).appendTo('#bt-recent-blog-posts');
				}
			//	bp_n = bp_n + 1;
				bp_n = bp_n + 6;
			}) 
			.done(function(d) {
				bt_portfolioHoverEffects();
			})
			.fail(function() {
				$('#bt-load-more-blog-posts').remove();
			});
		});
	})(jQuery);
	var pp_n = 2;
	(function($){
		$( "#bt-load-more-podcast-posts" ).click(function() {
			let jqxhr = $.getJSON( "https://www.biddytarot.com/wp-json/wp/v2/posts?_embed&per_page=6&categories=140&page=" + pp_n, function(d) {
					for(i = 0; i < d.length; i++) { 
					  let
					    p = d[i],
					    m = (p._embedded['wp:featuredmedia'])?p._embedded['wp:featuredmedia'][0].source_url:'h',
					    f = (p)?'<img src="'+m+'" />':'';
					  $('<div class="col span_4 element" data-project-cat="" data-default-color="true" data-title-color="" data-subtitle-color="" style="" data-delay-amount="0"><div class="inner-wrap" data-animation="none"><div class="work-item style-2">'+f+'<div class="work-info-bg"></div><div class="work-info"><a href="'+p.link+'"></a><div class="vert-center"><h3>'+p.title.rendered+'</h3></div></div></div></div></div>' ).appendTo('#bt-recent-podcast-posts');
					}
					pp_n = pp_n + 1;
				}) 
				.done(function(d) {
					bt_portfolioHoverEffects();
				})
				.fail(function() {
					$('#bt-load-more-podcast-posts').remove();
				})
		});
	})(jQuery);
}
})(jQuery);
