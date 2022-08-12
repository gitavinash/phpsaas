let allimg = document.querySelectorAll('.sm-image-group img');
let productimg = document.querySelector('.product_image img')
let popup = document.querySelector('.popup');
console.log(allimg);

allimg.forEach(el=>{
    el.addEventListener('click',()=>{
        productimg.src = el.src
       console.log(productimg);

    })
})

// popup.addEventListener('click',()=>{
//     Swal.fire({
//       title: 'Input something',
//       input: 'textarea',
//       title: 'Input something',
//       input: 'textarea',
//       title: 'Input something',
//       input: 'textarea',
//         title: 'Input something',
//         input: 'textarea'
//       }).then(function(result) {
//         if (result.value) {
//           Swal.fire(result.value)
//         }
//       })
    
// })





// navbar

let navBtn = document.querySelector('.navbar-global .mobile-tpggle');
let mobliemenu = document.querySelector('.side-nav')
console.log(mobliemenu);
navBtn.addEventListener('click',()=>{
  mobliemenu.classList.toggle('active')
})