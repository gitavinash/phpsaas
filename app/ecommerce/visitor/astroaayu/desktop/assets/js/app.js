const nav = document.querySelector('.nav');
window.addEventListener('scroll',function(){
    if(scrollY > 100){
        nav.classList.add('nav_active');
    }else{
        nav.classList.remove('nav_active')
    }
})
const toggle = document.querySelector('.toggler');
const nav_res = document.querySelector('.nav_res');
const close = document.querySelector('.close_icon_res');
toggle.addEventListener('click',function(){
  nav_res.classList.toggle('nav_res_active');
})
close.addEventListener('click',function(){
    nav_res.classList.remove('nav_res_active');
})
window.addEventListener('click',function(e){
    if(e.target == nav_res){
        nav_res.classList.remove('nav_res_active');
    }
});

/** Add to cart */

$('#addToCart').click(function(e) {
    e.preventDefault();
    $('#addToCart').hide();
    var productInfo = $("#productInfo").attr('value');
    var qty = productInfo.split('_');
    var id = qty[0];
    var price = qty[1];
    var qty = qty[2];

    $.ajax({
        type: 'POST',
        dataType: 'json',
        data: {
            'id' : id,
            'quantity' : 1,
            'price' : price,
            'type' : 'cart',
            'action' : 'add'
        },
        success: function(data) {
                        
            $('#addToCart').show();
            
            if(data.status==200) {
                $('#cartNumber').html(1)
                $('#cartNumber').css({'background': '#219518', 'color': '#fff'});
                $('#addToCardPopUp').show();

                $('#selectedName').text('');
                $('#selectedName').append(data.productName);
                $('#selectedImg').text('');
                $('#selectedImg').attr('src', data.productImage);
                $('#selectedQty').text('');
                $('#selectedQty').append(data.productQty);

                window.location.href='/checkout/';

            } else {
                console.log(data.msg);
            }

        },
        error: function(data) {
            console.log(data);
            $('#addToCart').show();
        }
    })

});


  /** Remove from cart */

  $('.removeFromCart').click(function(e) {
    e.preventDefault();
    var id = $(this).attr('id');
    
    /** Call remove from cart api */

    $.ajax({
        type: 'POST',
        dataType: 'json',
        data = {
            type: 'cart',
            action: 'remove',
            id: id
        },
        success: function(data) {

            if(data.status==200) {

                location.reload();

            } else {

                popUpMessage('center', data.msg, 'error');

            }
            $('#product_'+id).hide();

        },
        error: function(data) {
            console.log(data);
        }
    })

});
