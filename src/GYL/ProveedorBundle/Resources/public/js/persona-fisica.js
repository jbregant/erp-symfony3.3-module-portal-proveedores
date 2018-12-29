jQuery(document).ready(function () {

    $('#form_numero_documento').inputmask('99.999.999');

    $(document).on("submit", "#personaFisicaForm", function (e) {
        e.preventDefault();
        return false;
    });

    // $.validator.methods.dni = function( value, element ) {
    //     var arrayDigitos = value.toString();
    //     var flag = true;
    //     if(arrayDigitos[0] == '0' && arrayDigitos[1] == '0'){
    //         flag = false;
    //     } else if(arrayDigitos[0] == '0' && arrayDigitos[1] < '4'){
    //         flag = false;
    //     }
    //     return this.optional( element ) || flag;
    // };

    // se deja comentado en caso de que se quieran agregar mas datos al combo
    // $('#form_tipo_documento').on('change', function () {
    //     let inputNroDoc = $('#form_numero_documento');
    //     switch ($(this).text()) {
    //         case "DNI": //DNI NACIONAL
    //             console.log('DNI');
    //             inputNroDoc.inputmask('99.999.999');
    //             break;
    //         case "EXTRANJEROS": //EXTRANJEROS
    //             inputNroDoc.inputmask('');
    //             break;
    //         default:
    //             break;
    //     }
    // });

    $('#guardarDatosPersonaFisica').on('click', function (e) {
        var url = __AJAX_PATH__ + "formulariopreinscripcion/modificardatopersonal";

        let formPersonaFisica = $('#personaFisicaForm');

        formPersonaFisica.validate({
            rules: {
                'form[nombre]': {
                    required: true,
                    nyp:true
                },
                'form[apellido]': {
                    required: true,
                    nyp:true
                },
                'form[tipo_documento]': {
                    required: true
                },
                'form[numero_documento]': {
                    required: true,
                    dni: true
                },
            },
            messages: {
                'form[nombre]': {
                    nyp:"Ingrese un nombre válido, numeros y caracteres especiales no admitidos."
                },
                'form[apellido]': {
                    nyp:"Ingrese un apellido válido, numeros y caracteres especiales no admitidos."
                },
                'form[numero_documento]': {
                    dni:"Ingrese un DNI válido."
                }
            }
        });

        if (formPersonaFisica.valid()){
            //agrego el id-dato-personal (value del combobox proveedores) al form
            let idDatoPersonal = $('#proveedor-selector > option:selected').val();
            $('#personaFisicaForm').append('<input type="hidden" name="form[id_dato_personal]" value='+idDatoPersonal+'>');
            var formData = $('#personaFisicaForm').serialize();
            $.ajax({
                url: url,
                method: 'POST',
                data: formData
            }).done(function (response) {
                if (response.sts === 200) {
                    $("#msg").text('Datos del panel guardados en borrador');
                    $("#ask-pass-alert").modal();
                    changeTimeLineStep('#timeline_persona_fisica', 'bg-verdin', 'bg-tomate');
                } else if (response.sts === 304) {
                    $("#msg").text('Error no se ha modificado ningun dato');
                    $("#ask-pass-alert").modal();
                }
            }).fail(function (xhr, status, errorThrown) {

                if (xhr.status === 403) {
                    location.reload();
                }
            })
        }

        // $('#personaFisicaForm').validate({
        //     submitHandler: function(form) {
        //         //agrego el id-dato-personal (value del combobox proveedores) al form
        //         let idDatoPersonal = $('#proveedor-selector > option:selected').val();
        //         $('#personaFisicaForm').append('<input type="hidden" name="form[id_dato_personal]" value='+idDatoPersonal+'>');
        //         var formData = $('#personaFisicaForm').serialize();
        //         $.ajax({
        //             url: url,
        //             method: 'POST',
        //             data: formData
        //         }).done(function (response) {
        //             if (response.sts === 200) {
        //                 $("#msg").text('Datos del panel guardados en borrador');
        //                 $("#ask-pass-alert").modal();
        //                 changeTimeLineStep('#timeline_persona_fisica', 'bg-verdin', 'bg-tomate');
        //             } else if (response.sts === 304) {
        //                 $("#msg").text('Error no se ha modificado ningun dato');
        //                 $("#ask-pass-alert").modal();
        //             }
        //         }).fail(function (xhr, status, errorThrown) {
        //
        //             if (xhr.status === 403) {
        //                 location.reload();
        //             }
        //         })
        //     },
        //     rules: {
        //         'form[nombre]': {
        //             required: true,
        //             nyp:true
        //         },
        //         'form[apellido]': {
        //             required: true,
        //             nyp:true
        //         },
        //         'form[numero_documento]': {
        //             required: true,
        //             dni: true
        //         }
        //     },
        //     messages: {
        //         'form[nombre]': {
        //             nyp:"Ingrese un nombre válido, numeros y caracteres especiales no admitidos."
        //         },
        //         'form[apellido]': {
        //             nyp:"Ingrese un apellido válido, numeros y caracteres especiales no admitidos."
        //         },
        //         'form[numero_documento]': {
        //             dni:"Ingrese un documento válido."
        //         }
        //     }
        // });
    });
});