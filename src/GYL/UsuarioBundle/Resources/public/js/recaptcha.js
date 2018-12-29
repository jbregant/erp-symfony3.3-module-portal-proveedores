$(document).ready(function() {
    $('#username').focus();
    $('form').on('submit', function() {
        if ($(this).valid()) {
            if (grecaptcha.getResponse() !== null && grecaptcha.getResponse() !== '') {
                return true;
            } else {
                $("#msg").text('Debe completar el código captcha.');
                $("#ask-pass-alert").modal();
                return false;
            }
        }
    });
    $("#ask-pass-alert").on('hidden.bs.modal', function() {
        $('#username').focus();
    });
});