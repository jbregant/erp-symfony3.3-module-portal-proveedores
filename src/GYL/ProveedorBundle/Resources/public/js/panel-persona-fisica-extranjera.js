jQuery(document).ready(function () {

    $('#persona-fisica-extranjeraForm').on('submit', function (e) {
        e.preventDefault();
        return false;
    });

    $('#miembroPersonaFisicaForm').on('submit', function (e) {
        e.preventDefault();
        return false;
    });

    $('#form_tipo_documento_persona_fisica_extranjera').on('change', function () {
        let inputNroDoc = $('#form_numero_documento_persona_fisica_extranjera');
        switch ($(this).text()) {
            case "DNI": //DNI NACIONAL
                inputNroDoc.inputmask('99.999.999');
                break;
            case "EXTRANJEROS": //EXTRANJEROS
                inputNroDoc.inputmask('');
                break;
            default:
                break;
        }
    });


    $('#persona-fisica-extranjeraFormularioSubmit').on('click', function (e) {

        let url = __AJAX_PATH__ + "formulariopreinscripcion/agregarpersonafisicaextranjera";
        //agrego el id-dato-personal (value del combobox proveedores) al form
        let formPersonaFisicaExtranjera = $('#persona-fisica-extranjeraForm');


        formPersonaFisicaExtranjera.validate({
            rules: {
                'form[nombre_persona_fisica_extranjera]': {
                    required: true,
                    nyp: true
                },
                'form[apellido_persona_fisica_extranjera]':{
                    required: true,
                    nyp: true
                },
                'form[numero_documento_persona_fisica_extranjera]':{
                    required: true,
                    letrasynumeros: true
                }
            },
            messages: {
                'form[nombre_persona_fisica_extranjera]':{
                    nyp: "Ingrese un valor alfanumerico, caracteres especiales no admitidos."
                },
                'form[apellido_persona_fisica_extranjera]': {
                    nyp: "Ingrese un valor alfanumerico, caracteres especiales no admitidos."
                },
                'form[numero_documento_persona_fisica_extranjera]': {
                    letrasynumeros: "Ingrese un valor alfanumerico, caracteres especiales no admitidos."
                }
            }
        });

        if (formPersonaFisicaExtranjera.valid()){
            let idDatoPersonal = $('#proveedor-selector > option:selected').val();
            formPersonaFisicaExtranjera.append('<input type="hidden" name="form[id_dato_personal]" value='+idDatoPersonal+'>');
            let formData = formPersonaFisicaExtranjera.serialize();
            $.ajax(
                {
                    url: url,
                    method: 'POST',
                    data: formData
                }
            ).done(function (response) {
                if (response.sts === 200) {
                    $("#msg").text('Datos guardados en el borrador');
                    $("#ask-pass-alert").modal();
                    changeTimeLineStep('#timeline_persona_fisica_extranjera', 'bg-verdin', 'bg-tomate');
                } else {
                    $("#msg").text('Ha ocurrido un error al guardar el borrador, verifique los datos e intente nuevamente');
                    $("#ask-pass-alert").modal();
                }
            }).fail(function (xhr, status, errorThrown) {
                if (xhr.status === 403) {
                    location.reload();
                }
            })
        }
    });
});
