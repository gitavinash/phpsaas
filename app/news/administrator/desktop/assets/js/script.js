$(document).ready(function() {

	/** SweetAlert Pop Up Box */

	function simplePopUp(status, title, body) {
		Swal.fire(title,body,status)
	}

	function positionPopUp(status, title, position, btn, time) {
		Swal.fire({
			position: "'" + position + "'",
			type: status,
			title: title,
			showConfirmButton: btn,
			timer: time
		})
	}

	/** Image Preview Section */

	// Image Preview Default Function

	function imagePreview(input, imageName) {
		if (input.files && input.files[0]) {
		  	var reader = new FileReader();
		  	reader.onload = function(e) {
				$('.' + imageName + 'PreviewBox').show();
				$('#' + imageName + 'Path').attr('src', e.target.result);
				$('.' + imageName + 'UploadBox').hide();

				/** Only for feature image */

				if(imageName=='featureImage') {

					if($('#facebookPath').attr('src')=='') {
						$('.facebookPreviewBox').show();
						$('#facebookPath').attr('src', e.target.result);
						$('.facebookUploadBox').hide();
					}
	
					if($('#twitterPath').attr('src')=='') {
						$('.twitterPreviewBox').show();
						$('#twitterPath').attr('src', e.target.result);
						$('.twitterUploadBox').hide();
					}

				}

		  	}
		  	reader.readAsDataURL(input.files[0]); // convert to base64 string
		}
	}

	// Image Replace Default Function

	function imageReplace(idName) {
		console.log(idName);
		$('.' + idName + 'PreviewBox').hide();
		$('#' + idName + 'Path').attr('src', '');
		$('.' + idName + 'UploadBox').show();
	}

	// change image action

	$('#featureImage').change(function() {
		imagePreview(this, 'featureImage');
	});

	$('#facebookImage').change(function() {
		imagePreview(this, 'facebook');
	});

	$('#twitterImage').change(function() {
		imagePreview(this, 'twitter');
	});

	// replace image actions

	$('#featureChangeImage').click(function() {
		console.log('i m here');
		imageReplace('featureImage');
	})

	$('#facebookChangeImage').click(function() {
		imageReplace('facebook');
	})

	$('#twitterChangeImage').click(function() {
		imageReplace('twitter');
	})

	/* Calling Media API */

	var fullpath = window.location.pathname;
	var basepath = fullpath.replace('/dashboard/', '');

	switch (basepath){

		case 'media/' :


			if(window.location.search.indexOf('?type=list')!=-1) {

				/* call media jquery function */

				if(window.location.search.indexOf('&media=')) {

					var mediaType = window.location.search.substring(17);
				}

				$.ajax({
					url: window.location.origin + '/api/media.php',
					type: 'GET',
					dataType: 'JSON',
					data: {
						media : mediaType
					},
					success: function(data) {
						$.each(data.images, function(index, value) {
							
							var showImage = $('<li class="imageBox"><img src="'+ value +'" /></li>');
							$(".gallery").append(showImage);

                        });
                        console.log(data.msg);
					},
					error: function(data) {
						console.log('path or data type error');
					}
				});

			}

			break;

	}

	/* Tags */

	var tagList = [];		// use to define tags value for form submission
	var tagValue = [];		// use to display tags to user in UI

	$('.tags').on('keyup keypress', function(e) {
		var keyCode = e.keyCode || e.which;
		var tagName = $('.tags').val().replace(',', '');
		if(keyCode===188 || keyCode==13) {
			showTags(tagName)
			e.preventDefault();
			return false;
		}
		suggestTags(tagName);
	});

	/* edit page tags */

	var tagValues = $('#tagValue').val();

	if(tagValues=='' || tagValues!=undefined) {
		var tags = tagValues.split(',');
		$.each(tags, function(index, value) {
			showTags(value);
		});
		$('#tagValue').val('');
	}

	function suggestTags(tagName) {
		if(tagName!='')	{
			$.ajax({
				url: window.location.origin + '/api/tag.php',
				type: 'GET',
				dataType: 'JSON',
				data: {
					tag: tagName
				},
				success: function(data) {
					$('.selectTag').empty();
					$.each(data.msg, function(index, value) {
						var tags = $('<li>'+value+'</li>').on('click', function() {
							showTags(value);
						});
						$('.selectTag').append(tags);
					});
				},
				error: function(data) {
					console.log('Error : ' + data);
				}
			});
		}
	}

	function showTags(tagName) {
		$('.tags').val('');
		$('.selectTag').empty();
		if(tagName!='') {
			$('.tagList').show();

			var tagName=tagName.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#39;");
			
			/* remove dublicate value from tags */

			var position = $.inArray(tagName, tagValue);

			if(position==-1) {

				var tag = $('<span >'+tagName+'</span>').on('click', function() {
					/* Remove tag on click */
					$(this).remove();
					$('#tags').val(tagList);
					var remoVePosition = $.inArray(tagName, tagList);
					tagList.splice(remoVePosition, 1);
					$('#tags').val(tagList);
				});

				/* Show inserted value to user */

				tagValue.push(tagName);
				$('.tagList').append(tag);

				/* Define value to tags */

				tagList.push(tagName);
				$('#tags').val(tagList);

			}

		}
	}

	/* Tabs */

	$('#seo').click(function() {
		$('.seo').show();
		$('#seo').addClass('active');
		$('.fb').hide();
		$('#fb').removeClass('active');
		$('.tw').hide();
		$('#tw').removeClass('active');
	});

	$('#fb').click(function() {
		$('.fb').show();
		$('#fb').addClass('active');
		$('.seo').hide();
		$('#seo').removeClass('active');
		$('.tw').hide();
		$('#tw').removeClass('active');
	});

	$('#tw').click(function() {
		$('.tw').show();
		$('#tw').addClass('active');
		$('.seo').hide();
		$('#seo').removeClass('active');
		$('.fb').hide();
		$('#fb').removeClass('active');
	});

	/* align title */

	$('#title').on('keyup keypress', function(e) {
		var title = $('#title').val();
		$('#meta_title').val(title);
		$('#fb_title').val(title);
		$('#tw_title').val(title);
		$('#url').val(title.replace(/\ /g, '-').replace(/\,/g, '').replace(/\?/g, ''));
	});
	
	/* Submit Category */

	$('#submitCategory').click(function(e) {
		var data = new FormData(document.getElementById('categoryData'));
		console.log('i m here');
		if(categoryID=$('#category_id').val()!=0) {
			data.append('action', 'update');
			console.log('update');
		} else {
			data.append('action', 'add');
			console.log('add');
		}
		$.ajax({
			type: 'POST',
			data: data,
			processData: false,
			contentType: false,
			dataType: 'JSON',
			success: function(data) {
				if(data.status==200) {
					var success = 'success';
					$('#categoryData')[0].reset();
				} else {
					var success = 'error';
				}
				positionPopUp('success', data.msg, 'top-end', false, 1500);
				if(data.status!=206) {
					setTimeout(function(){
						location.reload();
					}, 1500)
				}
			},
			error: function(data) {
				console.log(data);
			}
		})
		e.preventDefault();
	});

	/** Check username is available or not */

	$('#checkUsername').on('keyup keypress', function(e) {
		var username = $('#checkUsername').val();
		$.ajax({
			url: window.location.origin + '/api/username.php',
			type: 'GET',
			dataType: 'JSON',
			data: {
				username: username
			},
			success: function(data) {
				if(data.status==200) {
					$('#updateProfile').prop("disabled", false);
					$('#updateProfile').css('display', 'block');
					$('#checkUsername').css({'background': '#28a745', 'color': '#fff'});
				} else {
					$('#updateProfile').css('display', 'none');
					$('#checkUsername').css({'background': '#D01F2A', 'color': '#fff'});
					$('#updateProfile').prop("disabled", true);
					event.preventDefault();
				}
			},
			error: function(data) {
				console.log(data);
			}
		})
	});

	/** Update profile */

	$('#updateProfile').click(function(e) {
		var data = new FormData(document.getElementById('profileData'));
		var username=$('#checkUsername').val();
		$.ajax({
			type: 'POST',
			data: data,
			processData: false,
			contentType: false,
			dataType: 'JSON',
			success: function(data) {
				if(data.status==200) {
					error='success';
				} else {
					error='error';
				}

				simplePopUp(error, 'Setting' ,data.msg)

			},
			error: function (data) {
				//console.log('Error');
				console.log(data);
			}
		})
		event.preventDefault();
	});

	/**  */

	$('#importCategory').change(function(e) {

		if($('#importCategory').val()!='') {

			$('.import-file-box').show();
			$('.hide-info').show();

		} else {

			$('.import-file-box').hide();
			$('.hide-info').hide();

		}
	});

	/** Import Data */

	$('#importData').change(function(e) {
		var divId = $('#importData').val();
		if(divId!='') {
			/** Show current div as per user choice */
			$('#cms').hide();
			$('#url').hide();
			$('#file').hide();
			$('#'+divId).show();
		} else {
			/** Hide all div if select value is default */
			$('#cms').hide();
			$('#url').hide();
			$('#file').hide();
		}
	});

	/** Import File */

	$('#importFile').change(function(event) {

		event.preventDefault();

		var fileName = $('#importFile').val().split('.');
		var fileExtension= fileName[fileName.length-1];
		
		if(fileExtension=='csv' || fileExtension=='json' || fileExtension=='xml') {

			$('#formData').hide();
			$('.hide-info').hide();
			$('.please-wait').show();

			/** Import file */

			var data = new FormData(document.getElementById('formData'));
			data.append('action', fileExtension);

			$.ajax({
				type: 'POST',
		        data: data,
				processData: false,
		        contentType: false,
		        dataType: 'JSON',
			    success: function(data) {
			        if(data.status==200) {
						$('.please-wait').hide();
						$('#formData').show();
						$('#importData').val('');
						$('#importFile').val('');
						var status = 'success';
			        } else {
						var status = 'error';
						$('.please-wait').hide();
						$('#formData').show();
						$('#importData').val('');
						$('#importFile').val('');
					}

					simplePopUp(status, 'Alert', data.msg)

		        },
		        error: function(data) {
					
					$('.please-wait').hide();
					$('#formData').show();
					$('#importData').val('');
					$('#importFile').val('');

					simplePopUp('error', 'Alert', 'Unable to connect with server')

					console.log(data);
		        }
			})
			
		} else {

			simplePopUp('error', 'Alert', 'Please select valid file format')

			$('#importFile').val('');

		}

	});

	/** Schedule Section */

	$('#scheduleBtn').click(function(){
		$('.publishGroup').hide();
		$('.scheduleGroup').show();
	});

	/** Website Configuration */

	$('#pos1').click(function() {

		$('#pos1').addClass('active');
		$('#pos1Box').css('display', 'block');

		$('#pos2').removeClass('active');
		$('#pos2Box').hide();

		$('#pos3').removeClass('active');
		$('#pos3Box').hide();

		$('#pos4').removeClass('active');
		$('#pos4Box').hide();

	});

	$('#pos2').click(function() {

		$('#pos2').addClass('active');
		$('#pos2Box').css('display', 'block');

		$('#pos1').removeClass('active');
		$('#pos1Box').hide();

		$('#pos3').removeClass('active');
		$('#pos3Box').hide();

		$('#pos4').removeClass('active');
		$('#pos4Box').hide();

	});

	$('#pos3').click(function() {

		$('#pos3').addClass('active');
		$('#pos3Box').css('display', 'block');

		$('#pos1').removeClass('active');
		$('#pos1Box').hide();

		$('#pos2').removeClass('active');
		$('#pos2Box').hide();

		$('#pos4').removeClass('active');
		$('#pos4Box').hide();

	});

	$('#pos4').click(function() {

		$('#pos4').addClass('active');
		$('#pos4Box').css('display', 'block');

		$('#pos1').removeClass('active');
		$('#pos1Box').hide();

		$('#pos2').removeClass('active');
		$('#pos2Box').hide();

		$('#pos3').removeClass('active');
		$('#pos3Box').hide();

	});

	$('#adsTXT').click(function() {
		
		$.ajax({
			type: 'POST',
			data: {
				type : 'api',
				action : 'setting',
				key : 'ads',
				data : $('#ads_txt').val()
			},
			success: function(data) {
				simplePopUp('success', 'success', data.msg)
			}, error : function(data) {
				simplePopUp('error', 'Error', data.msg)
			}
		})

	});

	$('#robotsTXT').click(function() {
		
		$.ajax({
			type: 'POST',
			data: {
				type : 'api',
				action : 'setting',
				key : 'robots',
				data : $('#robots_txt').val()
			},
			success: function(data) {
				simplePopUp('success', 'success', data.msg)
			}, error : function(data) {
				simplePopUp('error', 'Error', data.msg)
			}
		})

	});

	$('#mainfestJSON').click(function() {
		
		$.ajax({
			type: 'POST',
			data: {
				type : 'api',
				action : 'setting',
				key : 'mainfest',
				data : $('#mainfest_json').val()
			},
			success: function(data) {
				simplePopUp('success', 'success', data.msg)
			}, error : function(data) {
				simplePopUp('error', 'Error', data.msg)
			}
		})

	});

	$('#categoryButton').click(function() {
		$('#categoryBox').show();
		$('#pageBox').hide();
	});

	$('#pageButton').click(function() {
		$('#pageBox').show();
		$('#categoryBox').hide();
	});

	$("#siteLogo").change(function() {
		readURL(this, '#siteLogoPreview');
	});

});