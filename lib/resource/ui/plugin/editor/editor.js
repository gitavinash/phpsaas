function popUpMessage(msgPostion, msgTxt, msgType) {

    if(msgType==200) {

        msgType='success';

    }

    Swal.fire({
        position: msgPostion,
        type: msgType,
        title: msgTxt,
        showConfirmButton: false,
        timer: 1500
    })

}

(function ($) {

    if (typeof $.fn.rte === "undefined") {

        var defaults = {
            max_height: 350
        };

        $.fn.rte = function (options) {

            $.fn.rte.html = function (iframe) {
                return iframe.contentWindow.document.getElementsByTagName("body")[0].innerHTML;
            };

            // build main options before element iteration
            var opts = $.extend(defaults, options);

            // iterate and construct the RTEs
            return this.each(function () {
                var textarea = $(this);
                var iframe;
                var element_id = textarea.attr("id");

                // enable design mode
                function enableDesignMode() {

                    var content = textarea.val();

                    // Mozilla needs this to display caret
                    if ($.trim(content) === '') {
                        content = '';
                    }

                    // already created? show/hide
                    if (iframe) {
                        //console.log("already created");
                        textarea.hide();
                        $(iframe).contents().find("body").html(content);
                        $(iframe).show();
                        $("#toolbar-" + element_id).remove();
                        textarea.before(toolbar());
                        return true;
                    }

                    // for compatibility reasons, need to be created this way
                    iframe = document.createElement("iframe");
                    iframe.frameBorder = 0;
                    iframe.frameMargin = 0;
                    iframe.framePadding = 0;
                    iframe.height = 200;
                    if (textarea.attr('class')) iframe.className = textarea.attr('class');
                    if (textarea.attr('id')) iframe.id = element_id;
                    if (textarea.attr('name')) iframe.title = textarea.attr('name');

                    textarea.after(iframe);

                    var doc = content;
                    tryEnableDesignMode(doc, function () {
                        $("#toolbar-" + element_id).remove();
                        textarea.before(toolbar());
                        // hide textarea
                        textarea.hide();

                    });

                }

                function tryEnableDesignMode(doc, callback) {
                    if (!iframe) {
                        return false;
                    }

                    try {
                        iframe.contentWindow.document.open();
                        iframe.contentWindow.document.write(doc);
                        iframe.contentWindow.document.close();
                    } catch (error) {
                        console.log(error);
                    }
                    if (document.contentEditable) {
                        iframe.contentWindow.document.designMode = "On";
                        callback();
                        return true;
                    } else if (document.designMode !== null) {
                        try {
                            iframe.contentWindow.document.designMode = "on";
                            callback();
                            return true;
                        } catch (error) {
                            console.log(error);
                        }
                    }
                    setTimeout(function () {
                        tryEnableDesignMode(doc, callback);
                    }, 500);
                    return false;
                }

                /** Disable Design Mode and show only html code */

                function disableDesignMode(submit) {

                    var content = $(iframe).contents().find("body").html();

                    if ($(iframe).is(":visible")) {
                        textarea.val(content);
                    }

                    if (submit !== true) {
                        textarea.show();
                        $(iframe).hide();
                    }

                }

                // create toolbar and bind events to it's elements
                function toolbar() {
                    var tb = $("\
                        <div class='editor-menu' id='toolbar-" + element_id + "'>\
                            <select class='form-control heading'>\
                                <option value=''>Heading</option>\
                                <option value='p'>P</option>\
                                <option value='h1'>H1</option>\
                                <option value='h2'>H2</option>\
                                <option value='h3'>H3</option>\
                                <option value='h4'>H4</option>\
                                <option value='h5'>H5</option>\
                                <option value='h6'>H6</option>\
                            </select>\
                            <a class='editor-btn bold'><i class='fas fa-bold'></i></a>\
                            <a class='editor-btn italic'><i class='fas fa-italic'></i></a>\
                            <a class='editor-btn underline'><i class='fas fa-underline'></i></a>\
                            <a class='editor-btn subscript'><i class='fas fa-subscript'></i></a>\
                            <a class='editor-btn superscript'><i class='fas fa-superscript'></i></a>\
                            <a class='editor-btn unorderedlist'><i class='fas fa-list'></i></a>\
                            <a class='editor-btn orderedlist'><i class='fas fa-list-ol'></i></a>\
                            <a class='editor-btn indent'><i class='fas fa-indent'></i></a>\
                            <a class='editor-btn outdent'><i class='fas fa-outdent'></i></a>\
                            <a class='editor-btn justifyLeft'><i class='fas fa-align-left'></i></a>\
                            <a class='editor-btn justifyRight'><i class='fas fa-align-right'></i></a>\
                            <a class='editor-btn justfyCenter'><i class='fas fa-align-center'></i></a>\
                            <a class='editor-btn justifyFull'><i class='fas fa-align-justify'></i></a>\
                            <a class='editor-btn link'><i class='fas fa-link'></i></a>\
                            <a class='editor-btn unlink'><i class='fas fa-unlink'></i></a>\
                            <a class='editor-btn image'><i class='fas fa-image'></i></a>\
                            <a class='editor-btn fullScreen'><i class='fas fa-expand-arrows-alt'></i></a>\
                            <a class='editor-btn minScreen' style='display: none'><i class='fas fa-compress-arrows-alt'></i></a>\
                            <a class='editor-btn sourceCode'><i class='fas fa-code'></i></a>\
                            <a class='editor-btn htmlEnable' style='display: none' ><i class='fab fa-html5'></i></a>\
                        </div>\
                        <div class='imgBrowser' style='display: none;'>\
                            <div class='inner-imgBrowser'>\
                                <div class='box-side-menu'>\
                                    <ul>\
                                        <li class='library'>Media Library</li>\
                                        <li class='active upload'>Upload File</li>\
                                    </ul>\
                                </div>\
                                <div class='box-main-view'>\
                                    <div class='libraryBox' style='display: none'>\
                                        <div class='search-box'>\
                                            <input type='text' id='searchBar' name='imageSearch' placeholder='Search...'>\
                                        </div>\
                                        <div class='gallery-box'><ul class='gallery'></ul></div>\
                                    </div>\
                                    <div class='uploadBox' style='display: block'>\
                                        <form id='uploadFile' class='uploader-box'>\
                                            <h3>Drag &amp; drop files here</h3>\
                                            <div class='form-group'>\
                                                <span id='uploadText' >Open the file Browser</span>\
                                                <input type='file' name='upload' accept='image/x-png,image/gif,image/jpeg' />\
                                                <input type='hidden' name='type' value='singleUpload' />\
                                                <input type='hidden' name='action' value='upload' />\
                                            </div>\
                                        </form>\
                                        <div class='notice'><strong>only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif, .doc, .docx, .xls, .xlsx, .pdf, .txt, .csv <strong>Maximum upload limit is 300 MB</strong></div>\
                                    </div>\
                                </div>\
                                <div class='close-window'><i class='fa fa-times'></i></div>\
                            </div>\
                        </div>\
                        ");

                    /* Disable all pop up box */

                    $(document).on('keyup',function(evt) {
                        if(evt.keyCode==27) {
                            $('.imgBrowser').hide();
                            $('.linkBox').hide();
                            $('.fullscreen-editor').removeClass('setFullScreen');
                        }
                    });

                    $('.close-window', tb).click(function() {
                        $('.imgBrowser').hide();
                        $('.linkBox').hide();
                    });

                    $('.heading', tb).change(function () {
                        var heading = $(this).val();
                        formatText('formatBlock', heading);
                    });
                    $('.bold', tb).click(function () {
                        formatText('bold');
                        return false;
                    });
                    $('.italic', tb).click(function () {
                        formatText('italic');
                        return false;
                    });
                    $('.underline', tb).click(function () {
                        formatText('underline');
                        return false;
                    });
                    $('.subscript', tb).click(function () {
                        formatText('subscript');
                        return false;
                    });
                    $('.superscript', tb).click(function () {
                        formatText('superscript');
                        return false;
                    });
                    $('.unorderedlist', tb).click(function () {
                        formatText('insertunorderedlist');
                        return false;
                    });
                    $('.orderedlist', tb).click(function () {
                        formatText('insertorderedlist');
                        return false;
                    });
                    $('.indent', tb).click(function () {
                        formatText('indent');
                        return false;
                    });
                    $('.outdent', tb).click(function () {
                        formatText('outdent');
                        return false;
                    });
                    $('.justifyLeft', tb).click(function () {
                        formatText('justifyLeft');
                        return false;
                    });
                    $('.justifyRight', tb).click(function () {
                        formatText('justifyRight');
                        return false;
                    });
                    $('.justfyCenter', tb).click(function () {
                        formatText('justifyCenter');
                        return false;
                    });
                    $('.justifyFull', tb).click(function () {
                        formatText('justifyFull');
                        return false;
                    });
                    $('.link', tb).click(function () {
                        var link = prompt('Enter URL', '');
                        if(link!='') {
                            formatText('createlink', link);
                        }
                    });
                    $('.unlink', tb).click(function () {
                        formatText('unlink');
                        return false;
                    });
                    $('.fullScreen', tb).click(function() {
                        $('.fullScreen').hide();
                        $('.minScreen').show();
                        $('.fullscreen-editor').addClass('setFullScreen');
                    });
                    $('.minScreen', tb).click(function() {
                        $('.minScreen').hide();
                        $('.fullScreen').show();
                        $('.fullscreen-editor').removeClass('setFullScreen');
                    });

                    /* Disable or enable text editor */

                    $('.sourceCode', tb).click(function() {
                        disableDesignMode();
                        $('.heading').hide();
                        $('.bold').hide();
                        $('.italic').hide();
                        $('.underline').hide();
                        $('.subscript').hide();
                        $('.superscript').hide();
                        $('.unorderedlist').hide();
                        $('.orderedlist').hide();
                        $('.indent').hide();
                        $('.outdent').hide();
                        $('.justifyLeft').hide();
                        $('.justifyRight').hide();
                        $('.justfyCenter').hide();
                        $('.justifyFull').hide();
                        $('.link').hide();
                        $('.unlink').hide();
                        $('.image').hide();
                        $('.sourceCode').hide();
                        $('.htmlEnable').show();
                    });

                    $('.htmlEnable', tb).click(function() {
                        $('.heading').hide();
                        $('.bold').hide();
                        $('.italic').hide();
                        $('.underline').hide();
                        $('.subscript').hide();
                        $('.superscript').hide();
                        $('.unorderedlist').hide();
                        $('.orderedlist').hide();
                        $('.indent').hide();
                        $('.outdent').hide();
                        $('.justifyLeft').hide();
                        $('.justifyRight').hide();
                        $('.justfyCenter').hide();
                        $('.justifyFull').hide();
                        $('.link').hide();
                        $('.unlink').hide();
                        $('.image').hide();
                        $('.sourceCode').hide();
                        $('.htmlEnable').show();
                        enableDesignMode();
                    });

                    // call media browser

                    $('.image', tb).click(function () {
                        $('.imgBrowser').show();
                    });

                    // img browser pop up box
                    
                    $('.library', tb).click(function() {
                        imageList('image');
                        $('.library').addClass('active');
                        $('.upload').removeClass('active');
                        $('.libraryBox').css(
                            'display', 'block'
                        );
                        $('.uploadBox').css(
                            'display', 'none'
                        );
                    });
                    
                    $('.upload', tb).click(function() {
                        $('.upload').addClass('active');
                        $('.library').removeClass('active');
                        $('.uploadBox').css(
                            'display', 'block'
                        );
                        $('.libraryBox').css(
                            'display', 'none'
                        );
                    });

                    // search bar
                    
                    $('#searchBar', tb).on('keyup keypress', function(e) {
                        var keyCode = e.keyCode || e.which;
                        if (keyCode === 13) { 
                            e.preventDefault();
                            return false;
                        }
                        var search = $('#searchBar').val();
                        if(search.length>=3) {
                            $('.gallery').empty();
                            imageList('image', search);
                        }
                        if(search.length==0) {
                            $('.gallery').empty();
                            imageList('image');
                        }
                    });

                    // image uploader

                    $('#uploadFile', tb).change(function(e) {

                        var file = e.target.files[0];
                        var imageType = /image.*/;

                        if (!file.type.match(imageType)) return;

                        var form_data = new FormData();
                        form_data.append('file', file);

                        $.ajax({
                            type: 'POST',
                            data : new FormData(this),
                            processData: false,
                            contentType: false,
                            dataType: 'JSON',
                            encrypt: 'multipart/form-data',
                            success: function(data){
                                if(data.status!=200) {
                                    alert(data.msg);
                                } else {
                                    insertImage(data.msg);
                                    $('.imgBrowser').hide();
                                }
                            },
                            error: function(data) {
                                console.log('i m here');
                                console.log(data);
                            }
                        });
                    });


                    // Detect Paste to link

                    $(iframe).parents('form').submit(function () {

                        disableDesignMode();


                    });

                    return tb;
                }

                function imageList(imageType, name='') {
                    $.ajax({
                        type: 'GET',
                        url: window.location.origin + '/api/media.php',
                        dataType: 'JSON',
                        data: {
                            name    :   name,
                            media   :   imageType
                        },
                        success: function(data) {
                            $('.gallery').empty();
                            var i = 1;
                            $.each(data.images, function(index, value) {

                                var showImage = $('<li class="imageBox"><img src="'+ value +'" /></li>').on('click', function() {
                                    insertImage(value);
                                    $('.imgBrowser').hide();
                                });
                                $(".gallery").append(showImage);

                                i++;
                            });
                            if(i==1) {
                                $('.gallery').empty();
                                $(".gallery").append('\
                                   <p>Image Not Found</p>\
                                ');
                            }
                        },
                        error: function(data) {
                            console.log('Error while fetching images');
                        }
                    });
                }

                function insertImage(src) {
                    formatText('insertImage', src);
                    return false;
                }

                function formatText(command, option) {
                    iframe.contentWindow.focus();
                    try {
                        iframe.contentWindow.document.execCommand(command, false, option);
                    } catch (e) {
                        console.log(e);
                    }
                    iframe.contentWindow.focus();
                }

                function getSelectionElement() {
                    if (iframe.contentWindow.document.selection) {
                        // IE selections
                        selection = iframe.contentWindow.document.selection;
                        range = selection.createRange();
                        try {
                            node = range.parentElement();
                        } catch (e) {
                            return false;
                        }
                    } else {
                        // Mozilla selections
                        try {
                            selection = iframe.contentWindow.getSelection();
                            range = selection.getRangeAt(0);
                        } catch (e) {
                            return false;
                        }
                        node = range.commonAncestorContainer;
                    }

                    return node;
                }

                // enable design mode now
                enableDesignMode();

                // publish all post where editor is enable

                $('#publishPost').click(function() {
                    console.log(`i m click`);
                    disableDesignMode();
                    $('#publishPost').hide();
                    $('#draftPost').hide();
		            var postTitle = $('#title').val();
		            var data = new FormData(document.getElementById('formData'));
                    data.append('action', 'publish');

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
                            }
                            $('#publishPost').show();
                            $('#draftPost').show();
			            },
			            error: function(data) {
                            console.log(data);
                            $('#publishPost').show();
                            $('#draftPost').show();
			            }
                    });
                    
                });

                // Update all post where editor is enable

                $('#updatePost').click(function() {
                    disableDesignMode();
                    $('#updatePost').hide();
                    $('#draftPost').hide();
                    var postTitle = $('#title').val();
                    var data = new FormData(document.getElementById('formData'));
                    data.append('action', 'update');

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
                                $('#updatePost').show();
                                $('#draftPost').show();
                            }
                        },
                        error: function(data) {
                            console.log(data);
                            $('#updatePost').show();
                            $('#draftPost').show();
                        }
                    });

                });

                // Schedule Post

                $('#schedulePost').click(function() {
                    disableDesignMode();
                    $('#schedulePost').hide();
                    $('#draftPost').hide();
                    var postTitle = $('#title').val();
                    var data = new FormData(document.getElementById('formData'));
                    data.append('type', 'schedule');
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
                            }
                            $('#schedulePost').show();
                            $('#draftPost').show();
                        },
                        error: function(data) {
                            $('#schedulePost').show();
                            $('#draftPost').show();
                        }
                    });
                });

            }); //return this.each

        }; // rte

    } // if

    $(".editor").rte({});

})(jQuery);