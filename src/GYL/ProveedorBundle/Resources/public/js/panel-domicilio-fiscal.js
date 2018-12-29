jQuery(document).ready(function () {

    var form_localidad_fiscal = $('#form_localidad_fiscal');
    var selected_provincia_fiscal = $('#form_provincias_fiscal');

    $("#form_provincias_fiscal").val($("#form_provincias_fiscal").val());

    actualizarLocalidad($('#form_provincias_fiscal').val());

    selected_provincia_fiscal.on('change', function () 
    {
        actualizarLocalidad($(this).val());
    });

    $('.panel-heading').on('click', function (e) {
        if(e.currentTarget.innerHTML.indexOf("Fiscal")!=-1)
            $("#selected-localidad-fiscal").val($("#form_localidad_fiscal").val());
    });

    $('#domicilioFormFiscal').validate({
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
                    letrasynumeros: "Ingrese un valor alfanumerico, caracteres especiales no admitidos."
                },
                'form[departamento]': {
                    letrasynumeros: "Ingrese un valor alfanumerico, caracteres especiales no admitidos."
                }
            }
        });

    $('#submitDomicilioFormFiscal').on('click', function () {

        $(document).on("submit", "#domicilioFormFiscal", function (e) {
            e.preventDefault();
            return false;
        });

        $('#form_localidad_fiscal').val($('#selected-localidad-fiscal').val());
        var url = __AJAX_PATH__ + "formulariopreinscripcion/agregardomicilio/" + 3;

        let domicilioFiscal = $('#domicilioFormFiscal');

        //agrego el id-dato-personal (value del combobox proveedores) al form
        let idDatoPersonal = $('#proveedor-selector > option:selected').val();
        domicilioFiscal.append('<input type="hidden" name="form[id_dato_personal]" value='+idDatoPersonal+'>');

        var formData = domicilioFiscal.serialize();
        var validacion = domicilioFiscal.valid();

        if(validacion)
        {
            $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData
                }).done(function (response) {
                    $("#msg").text('Datos Guardados en Borrador');
                    $("#ask-pass-alert").modal();
                    changeTimeLineStep('#timeline_domicilio_fiscal', 'bg-verdin', 'bg-tomate');
                }).fail(function (xhr, status, errorThrown) {
                    if (xhr.status === 403) {
                        location.reload();
                    }
                })
        }
        if (selected_provincia_fiscal.val() > 0) {
            cargarLocalidades(selected_provincia_fiscal.val(), 'fiscal');
        }
    });

});

function actualizarLocalidad(id)
{
    $('#selected-localidad-fiscal').empty();

        var id = id;
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
                    $('#selected-localidad-fiscal').append('<option value="" selected="true" disabled="disabled">Elija una Localidad</option>');
                    $.each(obj, function (index, localidad) {
                        $('#selected-localidad-fiscal').append('<option value="' + localidad.id + '">' + localidad.nombre + '</option>');
                    });
                }
            }
        }).fail(function (xhr, status, errorThrown) {

            if (xhr.status === 403) {
                location.reload();
            }
    });
}



