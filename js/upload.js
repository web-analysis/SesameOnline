// report error msg
function showAlert(tit, msg, typ, d) {

    $.notify({

        title: tit,
        message: msg,
    },{
        type: typ,
        animate: {
            enter: 'animated bounceInDown',
            exit: 'animated bounceOutUp'
        },
        delay: d
    });
}



// get upload file object
var $filePathInput = $('.filePath_input'),
    $fileSelector = $('.file_selector'),
    uploadObj = null,

    fileID = null,
    bDownloadUrl = null;


    // drag way
$filePathInput.on({
    dragenter: function(e) {e.preventDefault();},
    dragover: function(e) {e.preventDefault();},
    dragleave: function(e) {e.preventDefault();},
    drop: function(e) {
        e.preventDefault();

        var fileList = e.originalEvent.dataTransfer.files;

        if (fileList.length === 0) {

            showAlert('', 'Failed to get upload file', 'danger', 3000);
            return;
        }

        uploadObj = fileList[0];
        this.value = uploadObj.name;
    }
});

    // click way
$('.browse_file_btn').click(function() {
    $fileSelector.click();
});

$fileSelector.change(function(e) {

    if (e.target.files && e.target.files.length > 0) {

        uploadObj = e.target.files[0];
        $filePathInput.val(uploadObj.name);
    }
});


// process upload
var $uploadBtn = $('.upload_file_btn'),
    $progressBar = $('.progress-bar');

$uploadBtn.click(function() {

    if (!uploadObj) {

        showAlert('', 'Please select a file to upload', 'danger', 3000);
        return;
    }

    $uploadBtn.attr('disabled', 'disabled');

    var url = getUploadUrl();
    if (!url) {

        exitProcess('Failed to get remote upload url');
        return;
    }

    ajaxUploadFile(url, false);
});

function exitProcess(msg) {

    showAlert('', msg, 'danger', 3000);
    $uploadBtn.removeAttr('disabled');
}

function getUploadUrl() {

    var url = false;

    $.ajaxSettings.async = false;
    for (var i = 0; i < 2; i++) {

        $.get('https://api.openload.co/1/file/ul', function(data, status) {

            if (status === 'success' && data.status === 200)
                url = data.result['url'];
        });

        if (i === 0) {

            if (url)
                break;
            else {

                // Automatically try again
            }

        }
    }

    $.ajaxSettings.async = true;
    return url;
}

function ajaxUploadFile(url, secondTime) {

    var fID = null,

        fData = new FormData();
    fData.append('file1', uploadObj);

    $.ajax({
        url: url,
        type: 'POST',
        data: fData,
        cache: false,
        contentType: false,
        processData: false,
        enctype: 'multipart/form-data',
        xhr: function() {

            var mXhr = $.ajaxSettings.xhr();

            if (mXhr.upload)
                mXhr.upload.addEventListener('progress', function(e) {

                    if (e.lengthComputable) {

                        var pg = Math.floor(e.loaded / e.total * 100) + '%';
                        $progressBar.css('width', pg).text(pg);
                    }

                }, false);

            return mXhr;
        },
        success: function(data) {

            if (data.status === 200) {

                fileID = fID = data.result['id'];
                bDownloadUrl = data.result['url'];
            }
        },
        complete: function() {

            if (!secondTime && !fID) {

                showAlert('', 'Something went wrong! Retrying now', 'warning',1200);

                // Automatically try again
                $progressBar.css('width', 0).text('');
                setTimeout(function() { ajaxUploadFile(url, true); }, 800);
            } else if (!fID)
                exitProcess('Failed to upload the file');
            else
                $cTicketModal.fadeIn(800);
        }
    });
}