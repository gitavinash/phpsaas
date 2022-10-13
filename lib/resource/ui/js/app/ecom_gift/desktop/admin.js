$(document).ready(function(e) {

    /** SweetAlert Pop Up Box */

	function popUpMessage(msgPostion, msgTxt, msgType) {

		if(msgType==200) {

			msgType='success'

		}

		Swal.fire({
			position: msgPostion,
			type: msgType,
			title: msgTxt,
			showConfirmButton: false,
			timer: 1500
		})

    }
    

	/** Start product details accordion tabs */

	$('#productGeneral').click(function() {
		$('.productGeneral').show();
		$('#productGeneral').addClass('active');
		$('.productInventory').hide();
		$('#productInventory').removeClass('active');
		$('.productShipping').hide();
		$('#productShipping').removeClass('active');
		$('.productGallery').hide();
		$('#productGallery').removeClass('active');
		$('.productAttributesDetails').hide();
		$('#productAttributesDetails').removeClass('active');
		$('.productAdditionalDetails').hide();
		$('#productAdditionalDetails').removeClass('active');
	});

	$('#productInventory').click(function() {
		$('.productInventory').show();
		$('#productInventory').addClass('active');
		$('.productGeneral').hide();
		$('#productGeneral').removeClass('active');
		$('.productShipping').hide();
		$('#productShipping').removeClass('active');
		$('.productGallery').hide();
		$('#productGallery').removeClass('active');
		$('.productAttributesDetails').hide();
		$('#productAttributesDetails').removeClass('active');
		$('.productAdditionalDetails').hide();
		$('#productAdditionalDetails').removeClass('active');
	});

	$('#productShipping').click(function() {
		$('.productShipping').show();
		$('#productShipping').addClass('active');
		$('.productGeneral').hide();
		$('#productGeneral').removeClass('active');
		$('.productInventory').hide();
		$('#productInventory').removeClass('active');
		$('.productGallery').hide();
		$('#productGallery').removeClass('active');
		$('.productAttributesDetails').hide();
		$('#productAttributesDetails').removeClass('active');
		$('.productAdditionalDetails').hide();
		$('#productAdditionalDetails').removeClass('active');
	});

	$('#productGallery').click(function() {
		$('.productGallery').show();
		$('#productGallery').addClass('active');
		$('.productGeneral').hide();
		$('#productGeneral').removeClass('active');
		$('.productInventory').hide();
		$('#productInventory').removeClass('active');
		$('.productShipping').hide();
		$('#productShipping').removeClass('active');
		$('.productAttributesDetails').hide();
		$('#productAttributesDetails').removeClass('active');
		$('.productAdditionalDetails').hide();
		$('#productAdditionalDetails').removeClass('active');
	});

	$('#productAttributesDetails').click(function() {
		$('.productAttributesDetails').show();
		$('#productAttributesDetails').addClass('active');
		$('.productGeneral').hide();
		$('#productGeneral').removeClass('active');
		$('.productInventory').hide();
		$('#productInventory').removeClass('active');
		$('.productShipping').hide();
		$('#productShipping').removeClass('active');
		$('.productGallery').hide();
		$('#productGallery').removeClass('active');
		$('.productAdditionalDetails').hide();
		$('#productAdditionalDetails').removeClass('active');
	});

	$('#productAdditionalDetails').click(function() {
		$('.productAdditionalDetails').show();
		$('#productAdditionalDetails').addClass('active');
		$('.productGeneral').hide();
		$('#productGeneral').removeClass('active');
		$('.productInventory').hide();
		$('#productInventory').removeClass('active');
		$('.productShipping').hide();
		$('#productShipping').removeClass('active');
		$('.productGallery').hide();
		$('#productGallery').removeClass('active');
		$('.productAttributesDetails').hide();
		$('#productAttributesDetails').removeClass('active');
	});
});