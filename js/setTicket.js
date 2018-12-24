var $cTicketModal = $('.create_ticket_modal'),
    $ticketInput = $('#ticket'),
    $tokenInput = $('#token'),

    $tModalCancelBtn = $('.t_modal_cancelBtn'),
    $tModalSaveBtn = $('.t_modal_saveBtn');

// bind evt
$ticketInput.add($tokenInput).focus(function() {
    $(this).parent('.form-group').removeClass('has-error');
});

$tModalCancelBtn.click(function() {

    // restore
    $progressBar.css('width', 0).text('');

    $ticketInput.val('').parent('.form-group').removeClass('has-error');
    $tokenInput.val('').parent('.form-group').removeClass('has-error');

    $filePathInput.val('');
    $fileSelector.val('');
    uploadObj = null;

    fileID = null;
    bDownloadUrl = null;

    $cTicketModal.slideUp(800);

    $uploadBtn.removeAttr('disabled');
});

$tModalSaveBtn.click(function() {

    var ok = false;

    $.ajax({
        url: 'app/upload.php',
        method: 'POST',
        data: {

            fileID: fileID,
            url: bDownloadUrl,
            ticket: $ticketInput.val(),
            token: $tokenInput.val(),

            reqFrom: 'sesameOl'
        },
        timeout: 1800,
        beforeSend: function() {
            $tModalSaveBtn.attr('disabled', 'disabled');
        },
        error: function() {

            showAlert('', 'Something went wrong', 'danger', 2200);
        },
        success: function(res) {

            if (res.msgCode === 1101) {

                var errItems = res.data['errItems'];

                if (errItems.indexOf(0) !== -1 || errItems.indexOf(1) !== -1)
                    showAlert('', 'Abnormal operation', 'danger', 2200);

                if (errItems.indexOf(2) !== -1)
                    $ticketInput.parent('.form-group').addClass('has-error');

                if (errItems.indexOf(3) !== -1)
                    $tokenInput.parent('.form-group').addClass('has-error');

            } else {

                var typ = 'danger';
                if (res.msgCode === 1102)
                    typ = 'warning';
                else if (res.msgCode === 1104) {

                    typ = 'success';
                    ok = true;
                }

                showAlert('', res.msg, typ, 3000);
            }

        },
        complete: function() {

            if (ok)
                $tModalCancelBtn.click();

            $tModalSaveBtn.removeAttr('disabled');
        }
    });
});