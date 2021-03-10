$(document).ready(function(e) {

	// find GET parameter keys

	var queryDict = {}
	location.search.substr(1).split("&").forEach(function(item) {queryDict[item.split("=")[0]] = item.split("=")[1]})

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
	});

	/** Product gallery */

	if(queryDict.type=='add') {

		$(window).bind('beforeunload', function(){
			return "Leaving this page will reset the wizard";
		});

		$('.input-images').imageUploader({
			imagesInputName: 'gallery',
			maxFiles: 10
		});

	}

	if(queryDict.type=='edit') {

		$(window).bind('beforeunload', function(){
			return "Leaving this page will reset the wizard";
		});

		console.log(oldGalleryImages);
		
		$('.input-images').imageUploader({
			preloaded: oldGalleryImages,
			imagesInputName: 'gallery',
			preloadedInputName: 'oldGallery',
			maxSize: 2 * 1024 * 1024,
			maxFiles: 10
		});

	}

});