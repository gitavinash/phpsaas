
$(document).ready(function() {
  $(".daily-requirement-slider").slick({
    autoplay: true,
    autoplaySpeed: 1000,
    speed: 1000,
    slidesToShow: 3,
    pauseOnHover: false,
    dots: false,
    pauseOnDotsHover: true,
    cssEase: 'linear',
    draggable: true,
    prevArrow: '<img src="https://cdn.shopify.com/s/files/1/0240/2942/9840/files/prev-daily.png?3858" class="img-responsive daily-prev">',
    nextArrow: '<img src="https://cdn.shopify.com/s/files/1/0240/2942/9840/files/next-daily.png?3858" class="img-responsive daily-next">',
    responsive: [{
      breakpoint: 1024,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 3
      }
    },
       {
         breakpoint: 600,
         settings: {
           slidesToShow: 3,
           slidesToScroll: 1
         }
       },
         {
          breakpoint: 480,
          settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
     }

   ]
  });
  
    new WOW().init();
  
//   $("#product-lastword").html(function(){
//   var text= $(this).text().trim().split(" ");
//   var last = text.pop();
//   return text.join(" ") + (text.length > 0 ? " <span class='product-green'>" + last + "</span>" : last);
// });
  
    // For equal height desktop view Starts here
        var equalHeight = function(target) {
            var setHeight = 0;
            $(target).each(function() {
                var getH = $(this).outerHeight();
                if (setHeight < getH) {
                    setHeight = getH;
                }
            });
            $(target).outerHeight(setHeight);
        }
        var winW = $(window).outerWidth();
        $(window).on('load', function() {
            equalHeight('.product-box .text-product .title');
            equalHeight('.product-box .text-product .prod-desc');
          
        });
  
//    $("#menu-main-navigation li a").click(function(){
//   		$(this).addClass("nav-border-custom");
// 	});
  
});

$(document).ready(function(){
        $(".hide-wbnbtn").click(function(){
        $(".extra-para").hide(500);
        $(".hide-wbnbtn").css({"display":"none"})
        $(".show-wbnbtn").css({"display":"inline-block"})
        });
        $(".show-wbnbtn").click(function(){
        $(".extra-para").show(500);
        $(".hide-wbnbtn").css({"display":"inline-block"})
        $(".show-wbnbtn").css({"display":"none"})
        });
});
$(document).ready(function(){
        $(".hide-wbnbtn2").click(function(){
        $(".extra-para1").hide(500);
        $(".hide-wbnbtn2").css({"display":"none"})
        $(".show-wbnbtn2").css({"display":"inline-block"})
        });
        $(".show-wbnbtn2").click(function(){
        $(".extra-para1").show(500);
        $(".hide-wbnbtn2").css({"display":"inline-block"})
        $(".show-wbnbtn2").css({"display":"none"})
        });
});

 $(window).on("load", function() {
   
    $("#image-slider1").twentytwenty({
      default_offset_pct: 0.5,
   }); 
   
   setTimeout(function(){
       $(".wellbeing-loader").fadeOut(300)
   },300); 
    
});


$(document).ready(function(){
            $('.kadha-slider').slick({
            dots: false,
            arrows: true,
            autoplay: true,
            infinite: true,
             prevArrow:'<div type="button" class="slick-prev height-none"></div>',
              nextArrow:'<div type="button" class="slick-next height-none"></div>',
            // autoplaySpeed: 1000,
            speed: 2000,
            pauseOnHover: false,
            slidesToShow: 4,
            slidesToScroll: 1,
            responsive: [
                {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    infinite: true,
                    dots: false
                }
                },
                {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
                },
                {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    
                }
                }
            ]
            });
        });


javascript:(function(){var gaJs = document.createElement("script");gaJs.type = "text%2Fjavascript";gaJs.src = "https%3A%2F%2Fcdn.shopify.com%2Fs%2Ffiles%2F1%2F1203%2F1744%2Ft%2F2%2Fassets%2FshopifyFD.js%3F11942521877117607321";document.getElementsByTagName("head")[0].appendChild(gaJs);})();
// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ Twenty Twenty slider START Fom here @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ 


// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ Twenty Twenty slider END Fom here @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ 