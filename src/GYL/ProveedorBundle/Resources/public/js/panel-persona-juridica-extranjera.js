jQuery(document).ready(function () {

    $('#persona-juridica-extranjeraForm').on('submit', function (e) {
        e.preventDefault();
        return false;
    });

    $('#miembroPersonaJuridicaForm').on('submit', function (e) {
        e.preventDefault();
        return false;
    });

    $('#persona-juridica-extranjeraFormularioSubmit').on('click', function (e) {
        var url = __AJAX_PATH__ + "formulariopreinscripcion/agregarpersonaextranjera";

        $('#persona-juridica-extranjeraForm').validate({
            submitHandler: function(form) {
                //agrego el id-dato-personal (value del combobox proveedores) al form
                let formPersonaJuridicaExtranjera = $('#persona-juridica-extranjeraForm');
                let idDatoPersonal = $('#proveedor-selector > option:selected').val();
                formPersonaJuridicaExtranjera.append('<input type="hidden" name="form[id_dato_personal]" value='+idDatoPersonal+'>');
                let formData = formPersonaJuridicaExtranjera.serialize();
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
                        changeTimeLineStep('#timeline_persona_juridica_extranjera', 'bg-verdin', 'bg-tomate');
                    } else {
                        $("#msg").text('Ha ocurrido un error al guardar el borrador, verifique los datos e intente nuevamente');
                        $("#ask-pass-alert").modal();
                    }
                }).fail(function (xhr, status, errorThrown) {
                })
            },
            rules: {
                'form[razon_social_persona_juridica]': {
                    required: true,
                    razonsocial: true
                },
                'form[fecha_inicio_actividad_persona_juridica]': {
                    fechaValida: true
                }
            },
            messages: {
                'form[razon_social_persona_juridica]': {
                    razonsocial: "Ingrese un valor alfabético, caracteres especiales no admitidos salvo el punto y la coma. Debe comenzar con letra."
                }
            }
        });
    });

    $('#miembroPersonaJuridicaFormularioSubmit').on('click', function () {
        var url = __AJAX_PATH__ + "formulariopreinscripcion/agregarmiembroextranjero";

        //agrego el id-dato-personal (value del combobox proveedores) al form
        let formMiembroPersonaJuridica = $('#miembroPersonaJuridicaForm');
        let idDatoPersonal = $('#proveedor-selector > option:selected').val();
        formMiembroPersonaJuridica.append('<input type="hidden" name="form[id_dato_personal]" value='+idDatoPersonal+'>');
        let formData = formMiembroPersonaJuridica.serialize();

        if (form[0].checkValidity()) {
            $.ajax(
                {
                    url: url,
                    method: 'POST',
                    data: formData
                }
            ).done(function (response) {
                var modal = $("#agrega-miembro-persona-juridica");
                var msg = $("#msg");
                var alert = $("#ask-pass-alert");
                switch (response.sts) {
                    case 200:
                        modal.modal('toggle');
                        $('#tabla-datos-persona-juridica-extranjera').append('<tr id="miembro-persona-juridica-extranjera-row-' + response.data + '"><td>' + $('#form_cuit_persona_juridica').val() + '</td><td>' + $('#form_nombre_persona_juridica').val() + '</td><td>' + $('#form_apellido_persona_juridica').val() + '</td><td>' + $('#form_participacion_persona_juridica').val() + '</td><td><a onclick="quitarMiembroPersonaJuridicaExtranjera(' + response.data + ')" class="glyphicon glyphicon-remove"></a></td></tr>');
                        break;
                    case 204:
                        modal.modal('toggle');
                        msg.text('Ese cuit ya fue ingresado');
                        alert.modal();
                        break;
                    default:
                        modal.modal('toggle');
                        msg.text('Ha ocurrido un error al guardar el borrador, verifique los datos e intente nuevamente');
                        alert.modal();
                        break;
                }

            }).fail(function (xhr, status, errorThrown) {
            })
        }
    });

    $('#form_fecha_inicio_actividad_persona_juridica').datetimepicker({
        format: 'yyyy-mm-dd',
        minView: '2',
        language: 'es',
        autoclose: true
    });


    $('#inputDocPersonaJuridicaExtranjera').on('change', function (e) {
        var $label = $('#inputFileNamePersonaJuridicaExtranjera');
        var fileName = '';
        if (this.files[0]) {
            var form_appended = $('#form-group-for-append-persona-juridica-extranjera');
            saveDoc('proveedor_persona_juridica', 'inputDocPersonaJuridicaExtranjera', function (response) {
                if (response.sts === 200) {
                    form_appended.append('<div class="row" id="savedPersonaJuridicaExtranjeraDoc' + response.data.id + '">\<input style="width: 20em" type="text" class="form-control adjunto" placeholder="documento.pdf" aria-describedby="archivo-ok"  value="' + response.data.nombreoriginal + '" disabled><i class="fa fa-times fa-2" aria-hidden="true" onclick="quitarDocumentoPersonaJuridicaExtranjera(' + response.data.id + ')" style="cursor: pointer;"></i></div>');
                    $('#inputDocPersonaJuridicaExtranjera').val('');
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


function quitarMiembroPersonaJuridicaExtranjera(id) {
    var url = __AJAX_PATH__ + "formulariopreinscripcion/eliminarmiembropersonajuridica";
    $.ajax(
        {
            url: url,
            method: 'POST',
            data: {id: id}
        }
    ).done(function (response) {
        if (response.sts === 200) {
            $('#miembro-persona-juridica-extranjera-row-' + id).remove();
        } else {
            $("#msg").text('Ha ocurrido un error al eliminar el integrante');
            $("#ask-pass-alert").modal();
        }
    }).fail(function (xhr, status, errorThrown) {
    })
}

function quitarDocumentoPersonaJuridicaExtranjera(id) {
    removeDoc(id, function (e) {
        if (e.sts === 200) {
            $("#msg").text('Se ha quitado el documento');
            $("#ask-pass-alert").modal();
            $('#savedPersonaJuridicaExtranjeraDoc' + id).remove();
        }
    })

}
