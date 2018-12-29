jQuery(document).ready(function () {

    $('#persona-juridicaForm').on('submit', function (e) {
        e.preventDefault();
        return false;
    });

    $('#miembroPersonaJuridicaForm').on('submit', function (e) {
        e.preventDefault();
        return false;
    });

    $('#form_fecha_inicio_actividad_persona_juridica').inputmask('9999-99-99',{placeholder:"yyyy-mm-dd"});

    $('#persona-juridica-FormularioSubmit').on('click', function (e) {
        var url = __AJAX_PATH__ + "formulariopreinscripcion/agregarpersonajuridica";

        $('#persona-juridicaForm').validate({
            submitHandler: function (form) {

                //agrego el id-dato-personal (value del combobox proveedores) al form
                let formPersonaJuridica = $('#persona-juridicaForm');
                let idDatoPersonal = $('#proveedor-selector > option:selected').val();
                formPersonaJuridica.append('<input type="hidden" name="form[id_dato_personal]" value='+idDatoPersonal+'>');
                let formData = formPersonaJuridica.serialize();
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
                        changeTimeLineStep('#timeline_persona_juridica', 'bg-verdin', 'bg-tomate');
                    } else {
                        $("#msg").text('Ha ocurrido un error al guardar el borrador, verifique los datos e intente nuevamente');
                        $("#ask-pass-alert").modal();
                    }
                }).fail(function (xhr, status, errorThrown) {
                    if (xhr.status === 403) {
                        location.reload();
                    }
                })
            },
            rules: {
                'form[direccion_web_persona_juridica]': {
                    required: true,
                    url: true,
                    normalizer: function (value) {
                        var url = value;
                        // Check if it doesn't start with http:// or https:// or ftp://
                        if (url && url.substr(0, 7) !== "http://"
                                && url.substr(0, 8) !== "https://"
                                && url.substr(0, 6) !== "ftp://") {
                            // then prefix with http://
                            url = "http://" + url;
                        }
                        // Return the new url
                        return url;
                    }
                },
                'form[razon_social_persona_juridica]': {
                    required: true,
                    razonsocial:true
                },
                'form[fecha_inicio_actividad_persona_juridica]': {
                    fechaValida: true
                }
            },
            messages: {
                'form[razon_social_persona_juridica]': {
                    razonsocial:"Ingrese un valor alfabético, caracteres especiales no admitidos salvo el punto y la coma. Debe comenzar con letra."
                },
            }
        });
    });

    $('#form_cuit_persona_juridica').inputmask({
        mask: "99-99999999-9",
        placeholder: "_"
    });

    $('#miembroPersonaJuridicaFormularioSubmit').on('click', function () {

        var flagEdit = $(this).attr("editar");

        // Flag para editar.
        if (flagEdit === 'false') {
            var url = __AJAX_PATH__ + "formulariopreinscripcion/agregarmiembroextranjero";
        } else {
            var url = __AJAX_PATH__ + "formulariopreinscripcion/editarmiembroextranjero/" + flagEdit;
        }

        //validate setup
        $('#miembroPersonaJuridicaForm').validate({

            rules: {
                'form[nombre_persona_juridica]': {
                    required: true,
                    nyp: true
                },
                'form[apellido_persona_juridica]': {
                    required: true,
                    nyp: true
                },
                'form[cuit_persona_juridica]': {
                    required: true,
                    cuit: true
                },
                'form[participacion_persona_juridica]': {
                    required: true
                }
            },
            messages: {
                'form[nombre_persona_juridica]': {
                    nyp:"Ingrese un valor alfanumerico, caracteres especiales no admitidos."
                },
                'form[apellido_persona_juridica]': {
                    nyp:"Ingrese un valor alfanumerico, caracteres especiales no admitidos."
                }
            }
        });

        if ($('#miembroPersonaJuridicaForm').valid()) {

            //agrego el id-dato-personal (value del combobox proveedores) al form
            let formMiembroPersonaJuridica = $('#miembroPersonaJuridicaForm');
            let idDatoPersonal = $('#proveedor-selector > option:selected').val();
            formMiembroPersonaJuridica.append('<input type="hidden" name="form[id_dato_personal]" value='+idDatoPersonal+'>');
            let formData = formMiembroPersonaJuridica.serialize();
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
                        if (flagEdit === 'false') {
                            $('#tabla-datos-persona-juridica').append('<tr id="miembro-persona-juridica-row-' + response.data.form['id'] + '"><td id="id" hidden>' + response.data.form['id'] + '</td><td id="cuit">' + $('#form_cuit_persona_juridica').val() + '</td><td id="nombre">' + $('#form_nombre_persona_juridica').val() + '</td><td id="apellido">' + $('#form_apellido_persona_juridica').val() + '</td><td id="participacion">' + $('#form_participacion_persona_juridica').val() + '</td><td><a onclick="quitarMiembroPersonaJuridica(' + response.data.form['id'] + ')" class="glyphicon glyphicon-remove"></a><a data-target="#agrega-miembro-persona-juridica" data-toggle="modal" id="editarMiembro" class="editar-miembro glyphicon glyphicon-pencil" ></a></td></tr>');
                        } else {
                            $('#tabla-datos-persona-juridica').find('#miembro-persona-juridica-row-' + response.data.form['id']).html('<td id="id" hidden>' + response.data.form['id'] + '</td><td id="cuit">' + $('#form_cuit_persona_juridica').val() + '</td><td id="nombre">' + $('#form_nombre_persona_juridica').val() + '</td><td id="apellido">' + $('#form_apellido_persona_juridica').val() + '</td><td id="participacion">' + $('#form_participacion_persona_juridica').val() + '</td><td><a onclick="quitarMiembroPersonaJuridica(' + response.data.form['id'] + ')" class="glyphicon glyphicon-remove"></a><a data-target="#agrega-miembro-persona-juridica" data-toggle="modal" id="editarMiembro" class="editar-miembro glyphicon glyphicon-pencil" ></a></td></tr>');
                        }
                        break;
                    case 204:
                        modal.modal('toggle');
                        msg.text('Ese cuit ya fue ingresado');
                        alert.modal();
                        break;
                    case 206:
                        modal.modal('toggle');
                        msg.text('Valor de participacion incorrecto');
                        alert.modal();
                        break;
                    default:
                        modal.modal('toggle');
                        msg.text('Ha ocurrido un error al guardar el borrador, verifique los datos e intente nuevamente');
                        alert.modal();
                        break;
                }

            }).fail(function (xhr, status, errorThrown) {
                if (xhr.status === 403) {
                    location.reload();
                }
            });
        }

    });

    $('#form_fecha_inicio_actividad_persona_juridica').datetimepicker({
        format: 'yyyy-mm-dd',
        minView: '2',
        language: 'es',
        autoclose: true
    });

    $("#agregarMiembroPersonaJuridica").on('click', function () {

        $('#agrega-miembro-persona-juridica h4').html('Agregar Miembro');
        $('#miembroPersonaJuridicaFormularioSubmit').text('Agregar');

        $('#miembroPersonaJuridicaFormularioSubmit').attr("editar", false);
        $('#miembroPersonaJuridicaForm').trigger("reset");
    });

    // Edit para contactos.
    $(document).on("click", ".editar-miembro", function () {

        // Modifico los textos del modal.
        $('#agrega-miembro-persona-juridica h4').html('Editar Miembro');
        $('#miembroPersonaJuridicaFormularioSubmit').text('Editar');

        // Obtengo los datos de la fila.
        var id = $(this).closest("tr").find("#id").text().trim();
        var nombre = $(this).closest("tr").find("#nombre").text().trim();
        var apellido = $(this).closest("tr").find("#apellido").text().trim();
        var cuit = $(this).closest("tr").find("#cuit").text().trim();
        var participacion = $(this).closest("tr").find("#participacion").text().trim();

        // Cargo los campos del modal.
        $("#form_nombre_persona_juridica").val(nombre);
        $("#form_apellido_persona_juridica").val(apellido);
        $("#form_cuit_persona_juridica").val(cuit);
        $("#form_participacion_persona_juridica").val(participacion);
        $("#miembroPersonaJuridicaFormularioSubmit").attr("editar", id);

    });

    $('#inputDocPersonaJuridica').on('change', function (e) {
        var $label = $('#inputFileNamePersonaJuridica');
        var fileName = '';
        if (this.files[0]) {
            var form_appended = $('#form-group-for-append-persona-juridica');
            saveDoc('proveedor_persona_juridica', 'inputDocPersonaJuridica', function (response) {
                if (response.sts === 200) {
                    form_appended.append('<div class="row" id="savedPersonaJuridicaDoc' + response.data.id + '">\<input style="width: 20em" type="text" class="form-control adjunto" placeholder="documento.pdf" aria-describedby="archivo-ok"  value="' + response.data.nombreoriginal + '" disabled><i class="fa fa-times fa-2" aria-hidden="true" onclick="quitarDocumentoPersonaJuridica(' + response.data.id + ')" style="cursor: pointer;"></i></div>');
                    $('#inputDocPersonaJuridica').val('');
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


function quitarMiembroPersonaJuridica(id) {
    var url = __AJAX_PATH__ + "formulariopreinscripcion/eliminarmiembropersonajuridica";
    $.ajax(
            {
                url: url,
                method: 'POST',
                data: {id: id}
            }
    ).done(function (response) {
        if (response.sts === 200) {
            $('#miembro-persona-juridica-row-' + id).remove();
            var rowCount = $('#tabla-datos-persona-juridica tr').length;
            if (rowCount < 2) {
                changeTimeLineStep('#timeline_persona_juridica', 'bg-tomate', 'bg-verdin');
            }
        } else {
            $("#msg").text('Ha ocurrido un error al eliminar el integrante');
            $("#ask-pass-alert").modal();
        }
    }).fail(function (xhr, status, errorThrown) {
        if (xhr.status === 403) {
            location.reload();
        }
    })
}

function quitarDocumentoPersonaJuridica(id) {
    removeDoc(id, function (e) {
        if (e.sts === 200) {
            $("#msg").text('Se ha quitado el documento');
            $("#ask-pass-alert").modal();
            $('#savedPersonaJuridicaDoc' + id).remove();
        }
    })

}