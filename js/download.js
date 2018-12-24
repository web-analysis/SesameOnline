var $getFileBtn = $('.get_file_btn');

$getFileBtn.click(function() {

    var $usrTicketInput = $('.ticket_usrInput'),
        $usrTokenInput = $('.token_usrInput');

    $.ajax({
        url: 'app/download.php',
        method: 'POST',
        data: {
            ticket: $usrTicketInput.val(),
            token: $usrTokenInput.val(),
            reqFrom: 'sesameOl'
        },
        timeout: 1800,
        beforeSend: function() {
            $getFileBtn.attr('disabled', 'disabled');
        },
        error: function() {
            showAlert('', 'Something went wrong', 'danger', 2200);
        },
        success: function(res) {

            if (res.msgCode !== 1204)
                showAlert('', res.msg, 'danger', 3000);
            else {

                $usrTicketInput.val('');
                $usrTokenInput.val('');

                var url = res.data['info'][2]? res.data['info'][2] : res.data['info'][1];
                window.open(url, '_blank');
            }
        },
        complete: function() {
            $getFileBtn.removeAttr('disabled');
        }
    });
});