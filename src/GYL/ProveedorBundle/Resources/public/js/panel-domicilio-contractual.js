jQuery(document).ready(function () {

    $('#submitDomicilioFormContractual').on('click', function () {

        $(document).on("submit", "#domicilioFormContractual", function (e) {
            e.preventDefault();
            return false;
        });

        var url = __AJAX_PATH__ + "formulariopreinscripcion/agregardomicilio/" + 4;

        $('#domicilioFormContractual').validate({
            submitHandler: function(form) {
                //agrego el id-dato-personal (value del combobox proveedores) al form
                let domicilioContractual = $('#domicilioFormContractual');
                let idDatoPersonal = $('#proveedor-selector > option:selected').val();
                domicilioContractual.append('<input type="hidden" name="form[id_dato_personal]" value='+idDatoPersonal+'>');
                let formData = domicilioContractual.serialize();
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData
                }).done(function (response) {
                    $("#msg").text('Datos Guardados en Borrador');
                    $("#ask-pass-alert").modal();
                    changeTimeLineStep('#timeline_domicilio_contractual', 'bg-verdin', 'bg-tomate');
                }).fail(function (xhr, status, errorThrown) {
                    if (xhr.status === 403) {
                        location.reload();
                    }
                })
            },
            rules: {
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
    });


    $('#inputDocContractual').on('change', function (e) {
        var $label = $('#inputFileNameContractual');
        var fileName = '';

        if (this.files[0]) {
            var form_appended = $('#form-group-for-append-contractual');
            saveDoc('proveedor_domicilio_contraactual', 'inputDocContractual', function (response) {
                if (response.sts === 200) {
                    form_appended.append('<div class="row" id="savedContractualDoc' + response.data.id + '"><input style="width: 20em" type="text" class="form-control adjunto float-l" placeholder="documento.pdf" aria-describedby="archivo-ok"  value="' + response.data['nombreoriginal'] + '" disabled><i class="fa fa-times fa-2 float-l" aria-hidden="true" onclick="quitarDocumentoContractual(' + response.data.id + ')" style="cursor: pointer;"></i></div>');
                    $('#inputDocContractual').val('');
                } else {
                    $("#msg").text('Se ha producido un error al subir el archivo');
                    $("#ask-pass-alert").modal();
                }
            });

        } else if (e.target.value) {
            fileName = e.target.value.split('\\').pop();
        }

        if (fileName) {
            $label.val(fileName)
        }


    });

});

function quitarDocumentoContractual(id) {
    removeDoc(id, function (e) {
        if (e.sts === 200) {
            $("#msg").text('Se ha quitado el documento');
            $("#ask-pass-alert").modal();
            $('#savedContractualDoc' + id).remove();
        }
    })
}

function cargarLocalidades(id, tipo) {
    var url = __AJAX_PATH__ + "formulariopreinscripcion/buscarlocalidad/" + id;
    var form_localidad = $('#form_localidad_' + tipo);

    $.ajax(
        {
            url: url,
            method: 'GET'
        }
    ).done(function (response) {
        if (response.sts === 200) {
            var obj = jQuery.parseJSON(response.data);
            if (obj.length > 0) {
                $('#selected-localidad-' + tipo).append('<option value="" selected="true" disabled="disabled">Elija una Localidad</option>');
                $.each(obj, function (index, localidad) {
                    $('#selected-localidad-' + tipo).append('<option value="' + localidad.id + '">' + localidad.nombre + '</option>')
                });

                if(form_localidad.val() != ""){
                    $('#selected-localidad-' + tipo).val(form_localidad.val());
                }

            }
        }
    }).fail(function (xhr, status, errorThrown) {
        if (xhr.status === 403) {
            location.reload();
        }
    });

}



