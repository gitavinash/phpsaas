$(document).ready(function() {

    $('#openNav').click(function(e) {
        e.preventDefault();
        document.getElementById("mobileNav").style.width = "100%";
    });

    $('#closeNav').click(function(e) {
        e.preventDefault();
        document.getElementById("mobileNav").style.width = "0%";
    });

    function searchDomain(domain) {

        // Call Domain search API

        $.ajax({
            type: 'GET',
            url: '/api/domain/search/?domain=' + domain,
			dataType: 'json',
            success: function(data) {
                
                var domainList = data.domain_list;

                if(data.status==200) {

                    /** Hide loader */

                    $('.result-loader').removeClass('show');
                    $('.result-loader').addClass('hide');

                    /** Show result */

                    $('.domain-result').removeClass('hide');
                    $('.domain-result').addClass('show');

                    $.map(domainList, function(key, value){

                        var domainResult = domainList[value];

                        var domainName = domainResult.domain.split('.');
                        var domainExtenstion = '';

                        for(i=1; i<domainName.length; i++) {

                            domainExtenstion = domainExtenstion + '.'+domainName[i];

                        }

                        domainName = domainName[0];

                        if(domainResult.available) {

                            domainPrice = domainResult.price / 10000;
                            domainPriceMRP = domainPrice+200;

                            var domainAvailableHTML = 
                                '<div class="domain-result-box">' +
                                    '<div class="l-w2 l-m-w7">' +
                                        '<p>'+domainName+'<strong>'+ domainExtenstion +'</strong></p>' +
                                        '<p class="domain-price fl mob-show" ><strong>₹ '+ domainPrice +'</strong> <strike>₹ '+ domainPriceMRP +'</strike></p>' +
                                    '</div>' +
                                    '<div class="l-w2 l-m-w3 fr">' +
                                        '<div class="l-w2 l-m-w1 mob-hide">' +
                                            '<p class="domain-price" ><strike>₹'+ domainPriceMRP +'</strike> <strong>₹ '+ domainPrice +'</strong><span class="fr" >Renewal ₹ '+ domainPrice +'</span></p>' +
                                        '</div>' +
                                        '<div class="l-w2 l-m-w1">' +
                                            '<button href="#" class="add-to-cart" id="'+domainResult.domain+'_'+domainPrice+'_1" ><i class="fi-rr-shopping-cart-add"></i> Add to Cart</button>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>';


                           $('.domain-result').append(domainAvailableHTML);

                        } else {

                            var domainNotAvailableHTML = 
                                '<div class="domain-result-box">' +
                                    '<div class="l-w2 l-m-w7">' +
                                        '<p>'+domainName+'<strong>'+ domainExtenstion +'</strong></p>' +
                                    '</div>' +
                                    '<div class="l-w2 l-m-w3 fr">' +
                                        '<button href="#" class="add-to-cart" disabled ><i class="fi-rr-ban"></i> Not Available</button>' +
                                    '</div>' +
                            '</div>';


                           $('.domain-result').append(domainNotAvailableHTML);

                        }

                    });

                } else {



                }

            },
            error: function(data) {

                console.log('Errro');
                console.log(data);
            }

        });

    }

    // Call searchDomain function on search result page by default

    if($('.show-domain-result').length > 0) {

        // If .show-domain-result class is found on page

        var domainName = $('#domain').val();

        searchDomain(domainName);

    }

    // Call searchDomain function when user click on search domain button

    $('#domainSearchBtn').click(function(e) {
        e.preventDefault();

        /** Show loader */

        $('.result-loader').removeClass('hide');
        $('.result-loader').addClass('show');

        /** Hide result */

        $('.domain-result').removeClass('show');
        $('.domain-result').addClass('hide');

        var domainName = $('#domain').val();
        searchDomain(domainName);

    });

});