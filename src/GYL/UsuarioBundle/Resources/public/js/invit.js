$(document).ready(function() {
    $('form').validate({
        lang: 'es',
        rules: {
            username: {
                required: true,
                email: true
            },
            password: {
                required: true
            }
        }
    });
    $('#username').focus();
    $('#invitLink').on('click', function(event) {
        event.preventDefault();
        validarInvit($('form'));
    });
    $("#ask-pass-alert").on('hidden.bs.modal', function() {
        $('#username').focus();
    });
});

function validarInvit(obj) {
    if ($(obj).valid()) {
        if (grecaptcha.getResponse() !== null && grecaptcha.getResponse() !== '') {
            var url = __AJAX_PATH__ + "invitacion/crear";
            var pMail = $('#username').val();
            var pReCaptcha = grecaptcha.getResponse();
            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    email: pMail,
                    reCaptcha: pReCaptcha
                },
                beforeSend: function() {
                    $('.loading').show();
                }
            }).done(function(response) {
                console.log(response);
                $("#msg").html(response.msg);
                $("#ask-pass-alert").modal();
                grecaptcha.reset();
            }).fail(function(xhr, status, errorThrown) {
                $("#msg").html('Se ha producido un error en el sistema, por favor reporte el problema detallando la situación en el siguiente <a href="mailto:soporte.portal@adifse.com.ar">link</a>');
                $("#ask-pass-alert").modal();
                console.log("Error: " + errorThrown);
                console.log("Status: " + status);
                console.dir(xhr);
            }).always(function(xhr, status, errorThrown) {
                $('.loading').hide();
            });
        } else {
            $("#msg").text('Debe completar el código captcha.');
            $("#ask-pass-alert").modal();
        }
    }
}