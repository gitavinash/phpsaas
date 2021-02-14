$(document).ready(function() {
    $(".filtr1").click(function() {
			  
        var lable = $(".filtr1").text().trim();

        if(lable == "Hide") {
            $(".white-overly").hide();
        } else {
            $(".white-overly").show();
        }
     
    });

    $(".filtr2").click(function() {
   
        var lable = $(".filtr2").text().trim();

        if(lable == "Hide") {
        
            $(".white-overly").hide();
      
        } else {

            $(".white-overly").show();

        }
     
    });


    $('#close-overly').click(function() {

        $('.white-overly').toggle("slow");

    });

    $('.icon-wishlist').on('click', function(){

        $(this).toggleClass('in-wishlist');

    });

    window.onscroll = function() {
        myFunction()
    };

	var header = document.getElementById("header");
	var sticky = header.offsetTop;

	function myFunction() {

		if(window.pageYOffset > sticky) {
			header.classList.add("sticky");
		} else {
		    header.classList.remove("sticky");
		}
		
    }

});