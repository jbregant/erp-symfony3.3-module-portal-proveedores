jQuery(document).ready(function () {

    $(document).on("submit", "#form-datos-bancarios-extranjero", function (e) {
        e.preventDefault();
        return false;
    });

    $('#form_aba').inputmask({ mask: "999999999", "clearIncomplete":true });

    $('#guardarDatosBancariosExtranjero').on('click', function () {

        var url = __AJAX_PATH__ + "formulariopreinscripcion/agregardatobancarioextranjero";
        var cuenta_local = ($('#cuentaExtranjeroDatoBancario').attr('aria-pressed') === 'true');
        $('#form_cuenta_local').val(cuenta_local);

        let formDatosBancariosExtranjero = $('#form-datos-bancarios-extranjero');
        let idDatoPersonal = $('#proveedor-selector > option:selected').val();
        formDatosBancariosExtranjero.append('<input type="hidden" name="form[id_dato_personal]" value='+idDatoPersonal+'>');

        $('#form-datos-bancarios-extranjero').validate({
            rules: {
                'form[localidad_dato_bancario]': {
                    required: true,
                    estado: true
                },
                'form[swift]':{
                    required: true,
                    swift: true
                },
                'form[swift_banco_corresponsal]':{
                    swift: true
                },
                'form[aba]': {
                    required: true,
                    aba: true
                },
                'form[numero_cuenta_dato_bancario]':{
                    required: true,
                    nrocuenta: true
                },
                'form[beneficiario]':{
                    required: true,
                    letrasynumeros: true
                },
                'form[iban]':{
                    iban: true
                },
                'form[banco_corresponsal]':{
                    letrasynumeros: true
                }
            },
            messages: {
                'form[localidad_dato_bancario]':{
                    estado: "Ingrese un valor alfanumerico, caracteres especiales no admitidos."
                },
                'form[swift]': {
                    swift: "Ingrese un valor alfanumerico, caracteres especiales no admitidos."
                },
                'form[swift_banco_corresponsal]': {
                    swift: "Ingrese un valor alfanumerico, caracteres especiales no admitidos."
                },
                'form[aba]':{
                    aba: "Ingrese un valor numerico, caracteres especiales no admitidos."
                },
                'form[numero_cuenta_dato_bancario]':{
                    nrocuenta: 'Ingrese un numero de cuenta valido, solo numeros, letras y caracteres especiales no admitidos.'
                },
                'form[beneficiario]':{
                    letrasynumeros: "Ingrese un valor alfanumerico, caracteres especiales no admitidos."
                },
                'form[iban]':{
                    iban: "Ingrese un codigo IBAN valido. Ej: AR7620770024003102575766."
                },
                'form[banco_corresponsal]':{
                    letrasynumeros: "Ingrese un valor alfanumerico, caracteres especiales no admitidos."
                }
            }

        });

        if(formDatosBancariosExtranjero.valid()) {
            var formData = formDatosBancariosExtranjero.serialize();

            $.ajax(
                {
                    url: url,
                    method: 'POST',
                    data: formData
                }
            ).done(function (response) {
                switch (response.sts) {
                    case 200:
                        $("#msg").text('Datos Guardados en Borrador');
                        $("#ask-pass-alert").modal();
                        changeTimeLineStep('#timeline_datos_bancarios', 'bg-verdin', 'bg-tomate');
                        break;
                    case 201:
                        $("#msg").text(response.msg);
                        $("#ask-pass-alert").modal();
                        break;
                    default:
                        $("#msg").text('Se ha producido un error al guardar los datos');
                        $("#ask-pass-alert").modal();
                        break;
                }
            }).fail(function (xhr, status, errorThrown) {
                if (xhr.status === 403) {
                    location.reload();
                }
            });
        }

    });


});

