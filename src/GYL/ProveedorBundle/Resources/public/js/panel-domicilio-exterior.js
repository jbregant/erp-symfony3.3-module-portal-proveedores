jQuery(document).ready(function () {

    var form_localidad_exterior = $('#form_localidad_exterior');
    var selected_provincia_exterior = $('#form_provincias_exterior');


    selected_provincia_exterior.on('change', function () {

        $('#selected-localidad-exterior').empty();

        var id = $(this).val();
        if (!id) {
            return false;
        }

        var url = __AJAX_PATH__ + "formulariopreinscripcion/buscarlocalidad/" + id;

        $.ajax(
            {
                url: url,
                method: 'GET'
            }
        ).done(function (response) {
            if (response.sts === 200) {
                var obj = jQuery.parseJSON(response.data);
                if (obj.length > 0) {
                    $('#selected-localidad-exterior').append('<option value="" selected="true" disabled="disabled">Elija una Localidad</option>');
                    $.each(obj, function (index, localidad) {
                        $('#selected-localidad-exterior').append('<option value="' + localidad.id + '">' + localidad.nombre + '</option>')
                    });
                }
            }
        }).fail(function (xhr, status, errorThrown) {

            if (xhr.status === 403) {
                location.reload();
            }
        });


    });

    $('#domicilioFormExterior').validate({
            rules: {
                'form[telefono]': {
                    required: true,
                    telefono:true
                },
                'form[calle]':{
                    required: true,
                    letrasynumeros: true
                },
                'form[codigo_postal]':{
                    required: true,
                    letrasynumeros: true
                },
                'form[provincia_exterior]':{
                    required: true,
                    estado: true
                },
                'form[piso]':{
                    letrasynumeros: true
                },
                'form[departamento]':{
                    letrasynumeros: true
                }
            },
            messages: {
                'form[telefono]': {
                    telefono:"Ingrese un teléfono válido, Ej: +541158764561."
                },
                'form[calle]': {
                    letrasynumeros:"Ingrese un valor alfanumerico, caracteres especiales no admitidos."
                },
                'form[codigo_postal]':{
                    letrasynumeros: "Ingrese un valor alfanumerico, caracteres especiales no admitidos."
                },
                'form[provincia_exterior]':{
                    estado: "Ingrese un valor alfanumerico, caracteres especiales no admitidos."
                },
                'form[piso]':{
                    letrasynumeros: "Ingrese un valor alfanumerico, caracteres especiales no admitidos."
                },
                'form[departamento]':{
                    letrasynumeros: "Ingrese un valor alfanumerico, caracteres especiales no admitidos."
                }
            }
        });

    $('#submitDomicilioFormExterior').on('click', function () {


        $(document).on("submit", "#domicilioFormExterior", function (e) {
            e.preventDefault();
            return false;
        });

        var domicilioFormExterior = $('#domicilioFormExterior');
        var validacion = domicilioFormExterior.valid();


        $('#form_localidad_exterior').val($('#selected-localidad-exterior').val());
        var url = __AJAX_PATH__ + "formulariopreinscripcion/agregardomicilio/" + 5;

        //agrego el id-dato-personal (value del combobox proveedores) al form
        let idDatoPersonal = $('#proveedor-selector > option:selected').val();
        domicilioFormExterior.append('<input type="hidden" name="form[id_dato_personal]" value='+idDatoPersonal+'>');

        var formData = domicilioFormExterior.serialize();
        if (validacion) {
            $.ajax(
                {
                    url: url,
                    method: 'POST',
                    data: formData
                }
            ).done(function (response) {
                if (response.sts === 200) {
                    $("#msg").text('Datos Guardados en Borrador');
                    $("#ask-pass-alert").modal();
                    changeTimeLineStep('#timeline_domicilio_exterior', 'bg-verdin', 'bg-tomate');
                } else {
                }
            }).fail(function (xhr, status, errorThrown) {

                if (xhr.status === 403) {
                    location.reload();
                }
            })
        }
    });

    if (selected_provincia_exterior.val() > 0) {
        cargarLocalidades(selected_provincia_exterior.val(), 'exterior');
    }

});




