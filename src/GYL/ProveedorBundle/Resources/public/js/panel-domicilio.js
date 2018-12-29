jQuery(document).ready(function () {

    var selected_provincia_legal = $('#form_provincias_legal');
    var selected_provincia_real = $('#form_provincias_real');

    selected_provincia_real.on('change', function () {

        $('#selected-localidad-real').empty();

        var id = $(this).val();

        if (!id) {
            return false;
        }
        cargarLocalidades(id, 'real');

    });

    selected_provincia_legal.on('change', function () {

        $('#selected-localidad-legal').empty();

        var id = $(this).val();

        if (!id) {
            return false;
        }

        cargarLocalidades(id, 'legal');

    });

    $('#domicilioFormReal').validate({
        rules: {
            state: {
                required: true
            },
            'form[codigo_postal]': {
                required: true,
                letrasynumeros:true
            },
            'form[calle]': {
                required: true,
                letrasynumeros:true
            },
            'form[telefono]': {
                required: true,
                telefono:true
            },
            'form[provincias_real]': {
                required: true,
            },
            'form[piso]': {
                required: false,
                letrasynumeros: true
            },
            'form[departamento]': {
                required: false,
                letrasynumeros: true
            }
        },
        messages: {
            'form[codigo_postal]': {
                letrasynumeros: "Ingrese un valor alfanumerico, caracteres especiales no admitidos."
            },
            'form[calle]': {
                letrasynumeros:"Ingrese un valor alfanumerico, caracteres especiales no admitidos."
            },
            'form[telefono]': {
                telefono: "Ingrese un teléfono válido, Ej: +541158764561."
            },
            'form[piso]': {
                letrasynumeros:"Ingrese un valor alfanumerico, caracteres especiales no admitidos."
            },
            'form[departamento]': {
                letrasynumeros:"Ingrese un valor alfanumerico, caracteres especiales no admitidos."
            }
        }
    });

    $('#domicilioFormLegal').validate({
        rules: {
            'state': {
                required: true
            },
            'form[codigo_postal]': {
                required: true,
                letrasynumeros:true
            },
            'form[calle]': {
                required: true,
                letrasynumeros:true
            },
            'form[telefono]': {
                required: true,
                telefono:true
            },
            'form[provincias_legal]': {
                required: true,
            },
            'form[piso]': {
                required: false,
                letrasynumeros: true
            },
            'form[departamento]': {
                required: false,
                letrasynumeros: true
            }
        },
        messages: {
            'form[codigo_postal]': {
                letrasynumeros: "Ingrese un valor alfanumerico, caracteres especiales no admitidos."
            },
            'form[calle]': {
                letrasynumeros: "Ingrese un valor alfanumerico, caracteres especiales no admitidos."
            },
            'form[telefono]': {
                telefono:"Ingrese un teléfono válido, Ej: +541158764561."
            },
            'form[piso]': {
                letrasynumeros:"Ingrese un valor alfanumerico, caracteres especiales no admitidos."
            },
            'form[departamento]': {
                letrasynumeros:"Ingrese un valor alfanumerico, caracteres especiales no admitidos."
            }
        }
    });

    $('#submitDomicilioFormReal').on('click', function () {

        $(document).on("submit", "#domicilioFormReal", function (e) {
            e.preventDefault();
            return false;
        });

        var domicilioFormReal= $('#domicilioFormReal');

        var validacion = domicilioFormReal.valid();

        $('#form_localidad_real').val($('#selected-localidad-real').val());
        var url = __AJAX_PATH__ + "formulariopreinscripcion/agregardomicilio/" + 1;

        //agrego el id-dato-personal (value del combobox proveedores) al form
        let idDatoPersonal = $('#proveedor-selector > option:selected').val();
        domicilioFormReal.append('<input type="hidden" name="form[id_dato_personal]" value='+idDatoPersonal+'>');
        var formData = domicilioFormReal.serialize();

        if (validacion) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData
                }).done(function (response) {
                    $("#msg").text('Datos Guardados en Borrador');
                    $("#ask-pass-alert").modal();
                    changeTimeLineStep('#timeline_domicilio_real', 'bg-verdin', 'bg-tomate');
                }).fail(function (xhr, status, errorThrown) {
                    if (xhr.status === 403) {
                        location.reload();
                    }
                })           
        }
    });

    $('#submitDomicilioFormLegal').on('click', function () {

        $(document).on("submit", "#domicilioFormLegal", function (e) {
            e.preventDefault();
            return false;
        });


        var domicilioFormLegal= $('#domicilioFormLegal');

        var validacion = domicilioFormLegal.valid();

        //agrego el id-dato-personal (value del combobox proveedores) al form
        let idDatoPersonal = $('#proveedor-selector > option:selected').val();
        domicilioFormLegal.append('<input type="hidden" name="form[id_dato_personal]" value='+idDatoPersonal+'>');

        $('#form_localidad_legal').val($('#selected-localidad-legal').val());
        var url = __AJAX_PATH__ + "formulariopreinscripcion/agregardomicilio/" + 2;
        var formData = domicilioFormLegal.serialize();

        if (validacion) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData
                }).done(function (response) {
                    $("#msg").text('Datos Guardados en Borrador');
                    $("#ask-pass-alert").modal();
                    changeTimeLineStep('#timeline_domicilio_legal', 'bg-verdin', 'bg-tomate');
                }).fail(function (xhr, status, errorThrown) {
                    if (xhr.status === 403) {
                        location.reload();
                    }
                })
        }
    });

    if (selected_provincia_legal.val() > 0) {
        cargarLocalidades(selected_provincia_legal.val(), 'legal');
    }
    if (selected_provincia_real.val() > 0) {
        cargarLocalidades(selected_provincia_real.val(), 'real');
    }


});



