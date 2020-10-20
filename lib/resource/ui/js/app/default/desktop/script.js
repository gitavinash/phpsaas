$(document).ready(function() {

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
	
	/** Image Uplaod Function */

	function imageUpload(formName, returnMsg, btnName) {
		$('#'+btnName).hide();
		var data = new FormData(document.getElementById(formName));
		$.ajax({
			type: 'POST',
			data: data,
			processData: false,
			contentType: false,
			dataType: 'JSON',
			encrypt: 'multipart/form-data',
			success: function(data) {
				popUpMessage('center', data.msg, data.status);
				$('#'+btnName).show();
			},
			error: function(data) {
				console.log(data);
				popUpMessage('center', data.msg, data.status);
				$('#'+btnName).show();
			}
		});
	}

	/** Start Image Preview Section */

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
		  	reader.readAsDataURL(input.files[0]);
		}
	}

	// Image Replace Default Function

	function imageReplace(idName) {
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

	$('#profileImage').change(function() {
		$('#updateProfileImage').show();
		imagePreview(this, 'profile');
	});

	$('#siteIconImage').change(function() {
		imagePreview(this, 'siteIcon');
	});

	$('#siteLogoImage').change(function() {
		imagePreview(this, 'siteLogo');
	});

	// replace image actions
	
	$('#featureChangeImage').click(function() {
		imageReplace('featureImage');
	})

	$('#facebookChangeImage').click(function() {
		imageReplace('facebook');
	})

	$('#twitterChangeImage').click(function() {
		imageReplace('twitter');
	})

	$('#profileChangeImage').click(function() {
		imageReplace('profile');
	})

	$('#siteIconChangeImage').click(function() {
		imageReplace('siteIcon');
		$('#updateSiteIcon').show();
	})

	$('#siteLogoChangeImage').click(function() {
		imageReplace('siteLogo');
		$('#updateSiteLogo').show();
	})

	/** End Image Preview Section */


	/* align title */

	/** Start Title to URL code */

	$('#title').on('keyup keypress', function(e) {
		var title = $('#title').val();
		$('#meta_title').val(title);
		$('#fb_title').val(title);
		$('#tw_title').val(title);
		$('#url').val(title.replace(/\ /g, '-').replace(/\,/g, '').replace(/\?/g, ''));
	});

	/** End Title to URL code */

	/** Start Website and Social Media Meta Tabs */

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

	/** End of Code Website and Social Media Tabs */

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

	$("#gallery").on("change", function(e) {
		var files = e.target.files,
		  filesLength = files.length;
		for (var i = 0; i < filesLength; i++) {
		  var f = files[i]
		  var fileReader = new FileReader();
		  fileReader.onload = (function(e) {
			var file = e.target;
			$("<span class=\"pip\">" +
			  "<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
			  "<br/><span class=\"remove\">Remove image</span>" +
			  "</span>").insertAfter("#gallery");
			$(".remove").click(function(){
			  $(this).parent(".pip").remove();
			});
			
			// Old code here
			/*$("<img></img>", {
			  class: "imageThumb",
			  src: e.target.result,
			  title: file.name + " | Click to remove"
			}).insertAfter("#gallery").click(function(){$(this).remove();});*/
			
		  });
		  fileReader.readAsDataURL(f);
		}
	});

	/** Start Code of Website Configuration Accordion Tabs */

	//  Website Configuration tab

	$('#configurationBtn').click(function() {

		$('#configurationBtn').addClass('active');
		$('#configurationTabBox').css('display', 'block');

		$('#adsTab').removeClass('active');
		$('#adsTabBox').hide();

		$('#robotsTab').removeClass('active');
		$('#robotsTabBox').hide();

		$('#mainfestTab').removeClass('active');
		$('#mainfestTabBox').hide();

	});

	$('#adsTab').click(function() {

		$('#adsTab').addClass('active');
		$('#adsTabBox').css('display', 'block');

		$('#configurationBtn').removeClass('active');
		$('#configurationTabBox').hide();

		$('#robotsTab').removeClass('active');
		$('#robotsTabBox').hide();

		$('#mainfestTab').removeClass('active');
		$('#mainfestTabBox').hide();

	});

	$('#robotsTab').click(function() {

		$('#robotsTab').addClass('active');
		$('#robotsTabBox').css('display', 'block');

		$('#configurationBtn').removeClass('active');
		$('#configurationTabBox').hide();

		$('#adsTab').removeClass('active');
		$('#adsTabBox').hide();

		$('#mainfestTab').removeClass('active');
		$('#mainfestTabBox').hide();

	});

	$('#mainfestTab').click(function() {

		$('#mainfestTab').addClass('active');
		$('#mainfestTabBox').css('display', 'block');

		$('#configurationBtn').removeClass('active');
		$('#configurationTabBox').hide();

		$('#adsTab').removeClass('active');
		$('#adsTabBox').hide();

		$('#robotsTab').removeClass('active');
		$('#robotsTabBox').hide();

	});

	/** Website Configuration */

	$('#updateWebsite').click(function(e) {
		e.preventDefault();

		$('#updateWebsite').hide();

		var websiteName = $('#websiteName').val();
		var websiteDescription = $('#websiteDescription').val();
		var websiteKeyword = $('#websiteKeyword').val();

		if(websiteName=='') {
			popUpMessage('center', 'Please enter website name', 'warning');
			$('#updateWebsite').show();
		} else if(websiteDescription=='') {
			popUpMessage('center', 'Please enter website description', 'warning');
			$('#updateWebsite').show();
		} else if(websiteKeyword=='') {
			popUpMessage('center', 'Please enter website keywords', 'warning');
			$('#updateWebsite').show();
		} else {
			
			$.ajax({
				type: 'POST',
				dataType: 'json',
				data: $('#websiteData').serialize(),
				success: function(data) {
					popUpMessage('center', data.msg, data.status);
					$('#updateWebsite').show();
				},
				error: function(data) {
					console.log(data);
					$('#updateWebsite').show();
				}
			})

		}

	});

	/** Ads.txt */

	$('#updateAdsTxt').click(function(e) {
		e.preventDefault();
		$('#updateAdsTxt').hide();
		$.ajax({
			type: 'POST',
			data: {
				type : 'setting',
				action : 'ads',
				adsData : $('#ads_txt').val()
			},
			success: function(data) {
				popUpMessage('center', data.msg, data.status);
				$('#updateAdsTxt').show();
			}, error : function(data) {
				console.log(data)
				popUpMessage('center', data.msg, data.status);
				$('#updateAdsTxt').show();
			}
		})

	});

	/** Robots.txt Website Configuration tab */

	$('#updateRobotTxt').click(function(e) {
		e.preventDefault();
		$('#updateRobotTxt').hide();
		
		$.ajax({
			type: 'POST',
			data: {
				type : 'setting',
				action : 'robots',
				robotsData : $('#robots_txt').val()
			},
			success: function(data) {
				popUpMessage('center', data.msg, data.status);
				$('#updateRobotTxt').show();
			}, error : function(data) {
				console.log(data);
				popUpMessage('center', data.msg, data.status);
				$('#updateRobotTxt').show();
			}
		})

	});

	/** Mainfest.json Website Configuration tab */

	$('#updateMainfestJson').click(function(e) {
		e.preventDefault();
		$('#updateMainfestJson').hide();
		
		$.ajax({
			type: 'POST',
			data: {
				type : 'setting',
				action : 'mainfest',
				mainfestData : $('#mainfest_json').val()
			},
			success: function(data) {
				popUpMessage('center', data.msg, data.status);
				$('#updateMainfestJson').show();
			}, error : function(data) {
				console.log(data);
				popUpMessage('center', data.msg, data.status);
				$('#updateMainfestJson').show();
			}
		})

	});

	/** End Code of Website Configuration Accordion Tabs */

	/** Update Password */

	$('#updatePasswordBtn').click(function(e) {
		e.preventDefault();
		$('#updatePasswordBtn').hide();

		var password = $('#password').val();
		var confirmPassword = $('#confirmPassword').val();

		var isPasswordSame = password.localeCompare(confirmPassword);

		if(password=='') {
			popUpMessage('center', 'Please enter your password', 'warning');
			$('#updatePasswordBtn').show();
		} else if(confirmPassword=='') {
			popUpMessage('center', 'Please confirm your password', 'warning');
			$('#updatePasswordBtn').show();
		} else if(isPasswordSame===0) {	
			
			$.ajax({
				type: 'POST',
				dataType: 'json',
				data: $('#updatePassword').serialize(),
				success: function(data) {

					popUpMessage('center', data.msg, data.status);

					$('#password').val('');
					$('#confirmPassword').val('');

					$('#updatePasswordBtn').show();
				},
				error: function(data) {
					console.log(data);
					$('#updatePasswordBtn').show();
				}
			})
			
		} else {
			popUpMessage('center', 'Your password not matched', 'warning');
			$('#updatePasswordBtn').show();
		}

	})

	/** Change Site Logo */

	$('#updateSiteLogo').click(function(e) {
		imageUpload('updateSiteLogoForm', true, 'updateSiteLogo');
	});

	/** Change Website Icon */

	$('#updateSiteIcon').click(function(e) {
		imageUpload('updateSiteIconForm', true, 'updateSiteIcon');
	});

	/** Change Profile Picture */

	$('#updateProfileImage').click(function() {
		imageUpload('updateProfileImageForm', true, 'updateProfileImage');
	});

	/** User profile update */

	$('#updateProfile').click(function(e) {
		e.preventDefault();
		$('#updateProfile').hide();
		var firstName = $('#firstName').val();
		var lastName = $('#lastName').val();
		var fullName = $('#fullName').val();
		var mobile = $('#mobile').val();

		if(firstName=='') {
			popUpMessage('center', 'Please enter your first name', 'warning');
			$('#updateProfile').show();
		} else if(lastName=='') {
			popUpMessage('center', 'Please enter your last name', 'warning');
			$('#updateProfile').show();
		} else if(fullName=='') {
			popUpMessage('center', 'Please enter your full name', 'warning');
			$('#updateProfile').show();
		} else if(mobile=='') {
			popUpMessage('center', 'Please enter your mobile number', 'warning');
			$('#updateProfile').show();
		} else {

			$.ajax({
				type: 'POST',
				dataType: 'json',
				data: $('#profileData').serialize(),
				success: function(data) {
					popUpMessage('center', data.msg, data.status);
					$('#updateProfile').show();
				},
				error: function(data) {
					console.log(data);
					$('#updateProfile').show();
				}
			})			

		}

	});

	// Start Call to Actions codes

	/** Form Publish Actions for non editor */

	$('#publishBtn').click(function(e) {
		e.preventDefault();
		
		$('#publishBtn').hide();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: $('#publishForm').serialize(),
            success: function(data) {
                if(data.status=='success') {
                    $('#publishForm').trigger("reset");
                }
                popUpMessage('center', data.msg, data.status);
                $('#publishBtn').show();
            },
            error: function(data) {
                console.log(data);
                $('#publishBtn').show();
            }
        })

	})

	/** Form Update Actions for non editor */

	$('#updateBtn').click(function(e) {
		e.preventDefault();
		
		$('#updateBtn').hide();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: $('#updateForm').serialize(),
            success: function(data) {
                popUpMessage('center', data.msg, data.status);
                $('#updateBtn').show();
            },
            error: function(data) {
                console.log(data);
                $('#updateBtn').show();
            }
        })

	})

	/** Form Draft Actions for non editor */

	$('#draftBtn').click(function(e) {
		e.preventDefault();
		console.log('i m draft');
	})

	/** Form Delete Actions */

	$('#deleteBtn').click(function(e) {
		e.preventDefault();
		console.log('i m delete');
	})

	// Category from

	function submitCategory(e) {
		e.preventDefault();
		var status;
		var categoryName = $('#title').val();
		var type = $('#type').val();

		if(categoryName!='') {
			
			$('#submitCategory').hide();
			var categoryID = $('#category_id').val();

			var formData = $('#categoryData').serializeArray();

			if(categoryID==0) {

				formData.push({name:'action', value:'publish'})

			} else {

				formData.push({name:'action', value:'update'})

			}

			$.ajax({
				type: 'POST',
				dataType: 'json',
				data: formData,
				success: function(data) {
					if(data.status==200) {
						popUpMessage('center', data.msg, data.status);
						window.setTimeout(function() {
							window.location.href='/dashboard/'+ type +'/?type=category&id=' + data.categoryID;
						}, 1500);
					} else {
						status = data.status;
						popUpMessage('center', data.msg, status);
						$('#submitCategory').show();
					}
				},
				error: function(data) {
					console.log(data);
					$('#submitCategory').show();
				}
			})
		} else {

			popUpMessage('center', 'Please enter category name.', 'warning');

		}

	}

	$('#submitCategory').click(function(e) {

		submitCategory(e);

	})

	// End Call to Action code

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
		if(tagName.length>2) {
			suggestTags(tagName);
		}
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
					// console.log(data);
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

	// Delete btn

	$('.deleteBtn').click(function(e) {
		e.preventDefault();

		var clickClassvalue=this.id;

		var deleteValue = clickClassvalue.split('_');

		var id = deleteValue[0];
		var action = deleteValue[1];

		$.ajax({
			type: 'POST',
			dataType: 'json',
			data: {
				id: id,
				type: 'delete',
				action: action
			},
			success: function(data) {
				if(data.status==200) {
					popUpMessage('center', data.msg, data.status);
					window.setTimeout(function() {
						window.location.reload();
					}, 1500);
				} else {
					status = data.status;
					popUpMessage('center', data.msg, status);
					$('#submitCategory').show();
				}
			},
			error: function(data) {
				console.log(data);
				$('#submitCategory').show();
			}
		})

	});

	// Import file

	$('#importDataBtn').click(function(e){
		e.preventDefault();
		$('#importData').hide();
		$('.please-wait').show();
		var data = new FormData(document.getElementById('importData'));

		$.ajax({
			type: 'POST',
			data: data,
			processData: false,
			contentType: false,
			dataType: 'JSON',
			encrypt: 'multipart/form-data',
			success: function(data) {
				if(data.status==200) {
					popUpMessage('center', data.msg, data.status);
					window.setTimeout(function() {
						window.location.href=data.redirect;
					}, 1600)
				} else {
					popUpMessage('center', data.msg, data.status);
					console.log(data);
				}
				$('#importData').show();
				$('.please-wait').hide();
			},
			error: function(data) {
				console.log(data);
				$('#importData').show();
				$('.please-wait').hide();
			}
		});
		
	});

});