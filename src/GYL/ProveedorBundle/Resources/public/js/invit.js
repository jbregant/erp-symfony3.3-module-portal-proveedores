var proveedorNacionalRules = {
    email: {
        required: true,
        email: true
    },
    cuit: {
        cuit: true,
        required: true
    }
};
var proveedorExtanjeroRules = {
    email: {
        required: true,
        email: true
    },
    identificacion_tributaria: {
        required: true
    },
    website: {
        required: true
    }
};
$(document).ready(function() {
    $('form').validate({
        lang: 'es'
    });
    $('#cuit').inputmask({
        mask: "99-99999999-9",
        placeholder: "_"
    });
    addRules(proveedorNacionalRules);
    $('#email').focus();
    $('form').on('submit', function(event) {
        event.preventDefault();
        validarInvit(this);
    });
    $("#ask-pass-alert").on('hidden.bs.modal', function() {
        $('#email').focus();
    });
});

function addRules(rulesObj) {
    for (var item in rulesObj) {
        $('#' + item).rules('add', rulesObj[item]);
    }
}

function removeRules(rulesObj) {
    for (var item in rulesObj) {
        $('#' + item).rules('remove');
    }
}

function proveedorExtranjero(obj) {

    let form = $('#loginForm');

    if ($('.fg-website').is(":visible")) { //proveedor nacional
        form.trigger('reset');
        $('.fg-website').hide();
        removeRules(proveedorExtanjeroRules);
        addRules(proveedorNacionalRules);

        $('#cuit').inputmask({
            mask: "99-99999999-9",
            placeholder: "_"
        });
        $('.fg-identificacion-tributaria').hide();
        $('.fg-cuit').show();
        $(obj).addClass('active');
    } else { //proveedor extranjero
        form.trigger('reset');
        $('.fg-website').show();
        removeRules(proveedorNacionalRules);
        addRules(proveedorExtanjeroRules);
        $('cuit').inputmask('remove');
        $('.fg-identificacion-tributaria').show();
        $('.fg-cuit').hide();
        $(obj).removeClass('active');
    }
}

function validarInvit(obj) {
    if ($(obj).valid()) {
        if (grecaptcha.getResponse() !== null && grecaptcha.getResponse() !== '') {
            var url = $(obj).attr('action');
            var data = $('form').serializeArray();
            var extranjero = $("#extranjero").hasClass("active") ? true : false;
            data.push({
                name: 'extranjero',
                value: extranjero
            });
            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                beforeSend: function() {
                    $('.loading').show();
                }
            }).done(function(response) {
                $("#msg").html(response.msg);
                $("#ask-pass-alert").modal();
                grecaptcha.reset();
                switch (response.sts) {
                    case 5:
                        $("#btnAceptar").on('click', function() {
                            window.location.href = response.url;
                        });
                        break;
                    default:
                }
            }).fail(function(xhr, status, errorThrown) {
                $("#msg").html('<strong>ERROR:</strong> Consulte al administrador');
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