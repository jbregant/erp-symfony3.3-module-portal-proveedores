jQuery(document).ready(function () {

    $('#form_participacion_remunerativa').prop('readonly', true);

    $(document).on("submit", "#uteForm", function (e) {
        e.preventDefault();
        return false;
    });

    $(document).on("submit", "#miembroForm", function (e) {
        e.preventDefault();
        return false;
    });

    $('#saveBorradorUTE').on('click', function (e) {

        if($("#tabla-datos-ute tbody tr").length < 1) {
            $("#msg").text('Debe ingresar al menos un miembro');
            $("#ask-pass-alert").modal();
            return false;
        }

        $("#msg").text('Datos guardados en el borrador');
        $("#ask-pass-alert").modal();
    });

    $('.cuil-mask').inputmask('99-99999999-9');

    $('#uteFormularioSubmit').on('click', function (e) {

        var url = __AJAX_PATH__ + "formulariopreinscripcion/agregarute";

        if (new Date($('#form_fecha_constitucion').val()).getTime() > new Date($('#form_fecha_finalizacion').val())) {
            $("#msg").text('Fecha de constitución no puede ser mayor Fecha de finalización');
            $("#ask-pass-alert").modal();
            return
        }
        $('#uteForm').validate({
            submitHandler: function (form) {
                $('.cuil-mask').inputmask();

                //agrego el id-dato-personal (value del combobox proveedores) al form
                let formUte = $('#uteForm');
                let idDatoPersonal = $('#proveedor-selector > option:selected').val();
                formUte.append('<input type="hidden" name="form[id_dato_personal]" value='+idDatoPersonal+'>');

                var formData = formUte.serialize();
                var miembros = false;

                if ($("#tabla-datos-ute > tbody > tr").length > 0) {
                    miembros = true;
                }

                $('.cuil-mask').inputmask('99-99999999-9');
                $.ajax(
                        {
                            url: url,
                            method: 'POST',
                            data: formData + '&form%5Bmiembros%5D=' + miembros,
                        }
                ).done(function (response) {
                    switch (response.sts) {
                        case 200:
                            $("#msg").text('Datos guardados en el borrador');
                            $("#ask-pass-alert").modal();
                            changeTimeLineStep('#timeline_contratos_ute', 'bg-verdin', 'bg-tomate');
                            break;
                        case 202:
                            $("#msg").text('Datos guardados en el borrador');
                            $("#ask-pass-alert").modal();
                            break;
                        case 204:
                            $("#msg").text('La denominacion ingresada ya existe en nuestros registros');
                            $("#ask-pass-alert").modal();
                            break;
                        default:
                            $("#msg").text('Ha ocurrido un error al guardar el borrador, verifique los datos e intente nuevamente');
                            $("#ask-pass-alert").modal();
                            break;
                    }
                }).fail(function (xhr, status, errorThrown) {
                    if (xhr.status === 403) {
                        location.reload();
                    }
                })
            },
            rules: {
                'form[url]': {
                    required: false,
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
                'form[denominacion]':{
                  required: true,
                  letrasynumeros: true
                },
                'form[nombre_fantasia]': {
                    required: false,
                    letrasynumeros: true
                },
                'form[razon_social]': {
                    required: true,
                    razonsocial: true
                },
                'form[fecha_constitucion]': {
                    fechaValida: true
                },
                'form[fecha_finalizacion]': {
                    fechaValida: true
                },
                'form[numero_inscripcion]':{
                    solonumeros: true
                }
            },
            messages: {
                'form[denominacion]':{
                    letrasynumeros: "Ingrese un valor alfanumerico, caracteres especiales no admitidos."
                },
                'form[nombre_fantasia]': {
                    letrasynumeros: "Ingrese un valor alfanumerico, caracteres especiales no admitidos."
                },
                'form[razon_social]': {
                    razonsocial: "Ingrese un valor alfabético, caracteres especiales no admitidos salvo el punto y la coma. Debe comenzar con letra."
                },
                'form[numero_inscripcion]':{
                    solonumeros: "Ingrese un valor numerico, caracteres especiales no admitidos."
                }
            }
        });
    });

    $('#empleador_Btn').on('click', function (e) {
        if (!($('#empleador_Btn').attr('aria-pressed') === 'true')) {
            $('#form_participacion_remunerativa').val('0');
            $('#form_participacion_remunerativa').prop('readonly', true);
        } else {
            var filas = $('td.porcentaje-remuneracion');
            var total = 0;
            filas.each(function (i) {
                var value = parseFloat($(this).html());
                if (value != 0) {
                    total += value;
                }
            });
            total = 100 - total;
            $('#form_participacion_remunerativa').val(total.toFixed(2));
            $('#form_participacion_remunerativa').prop('readonly', false);
        }
    });

    $('#miembroFormularioSubmit').on('click', function (e) {

        var flagEdit = $(this).attr("editar");

        // Flag para editar.
        if (flagEdit === 'false') {
            var url = __AJAX_PATH__ + "formulariopreinscripcion/agregarmiembrosute";
        } else {
            var url = __AJAX_PATH__ + "formulariopreinscripcion/editarmiembroute/" + flagEdit;
        }


        if (!($('#empleador_Btn').attr('aria-pressed') === 'true')) {
            $('#form_empleador').val('0');
        } else {
            $('#form_empleador').val('1');
        }

        $('#miembroForm').validate({
            submitHandler: function (form) {

            },
            rules: {
                'form[cuit]': {
                    required: true,
                    cuit: true
                },
                'form[numero_inscripcion_miembro]': {
                    required: true,
                    solonumeros: true
                },
                'form[razon_social_miembro]': {
                    required: true,
                    razonsocial: true
                },
                'form[participacion_ganancias]': {
                    required: true,
                    porcentajes: true
                },
                'form[participacion_remunerativa]': {
                    required: true,
                    porcentajes: true
                },

            },
            messages: {
                'form[numero_inscripcion_miembro]': {
                    solonumeros: "Ingrese un valor alfanumerico, caracteres especiales no admitidos."
                },
                'form[razon_social_miembro]': {
                    razonsocial: "Ingrese un valor alfabético, caracteres especiales no admitidos salvo el punto y la coma. Debe comenzar con letra."
                },
                'form[participacion_ganancias]': {
                    porcentajes: "Ingrese un porcentaje valido."
                },
                'form[participacion_remunerativa]': {
                    porcentajes: "Ingrese un porcentaje valido."
                },
            }
        });
        // Si el formulario es valido enviouna request al servidor.
        if ($('#miembroForm').valid()) {
            var miembroForm = $('#miembroForm');

            //agrego el id-dato-personal (value del combobox proveedores) al form
            let idDatoPersonal = $('#proveedor-selector > option:selected').val();
            miembroForm.append('<input type="hidden" name="form[id_dato_personal]" value='+idDatoPersonal+'>');

            $.ajax(
                    {
                        url: url,
                        method: 'POST',
                        data: miembroForm.serialize()
                    }
            ).done(function (response) {
                var modalForm = $('#agrega-miembro');
                var modalAlert = $("#ask-pass-alert");
                var msg = $("#msg");
                var empleador = (response.data.form['empleador']) ? "Si" : "No";
                switch (response.sts) {
                    case 200:
                        modalForm.modal('toggle');
                        changeTimeLineStep('#timeline_contratos_ute', 'bg-verdin', 'bg-tomate');
                        if (flagEdit === 'false') {
                            $('#tabla-datos-ute').append('<tr id="miembro-ute-row-' + response.data.form['id'] + '"><td id="id" hidden>'+response.data.form['id']+'</td><td id="cuit">' + $("#form_cuit").val() + '</td> <td id="rs">' + $("#form_razon_social_miembro").val() + '</td> <td id="inscripcion">' + $("#form_numero_inscripcion_miembro").val() + '</td> <td id="ganancias">' + $("#form_participacion_ganancias").val() + '</td> <td id="remuneracion">' + $("#form_participacion_remunerativa").val() + '</td><td>' + empleador + '</td><td><a onclick="quitarUte(' + response.data.form['id'] + ')" class="glyphicon glyphicon-remove"></a><a data-target="#agrega-miembro" data-toggle="modal" id="editarUte" class="editar-ute glyphicon glyphicon-pencil"></a></td></tr>');
                        } else {
                            $('#tabla-datos-ute').find('#miembro-ute-row-' + response.data.form['id']).html('<td id="id" hidden>'+response.data.form['id']+'</td><td id="cuit">' + $("#form_cuit").val() + '</td> <td id="rs">' + $("#form_razon_social_miembro").val() + '</td> <td id="inscripcion">' + $("#form_numero_inscripcion_miembro").val() + '</td> <td id="ganancias">' + $("#form_participacion_ganancias").val() + '</td> <td id="remuneracion">' + $("#form_participacion_remunerativa").val() + '</td><td>' + empleador + '</td><td><a onclick="quitarUte(' + response.data.form['id'] + ')" class="glyphicon glyphicon-remove"></a><a data-target="#agrega-miembro" data-toggle="modal" id="editarUte" class="editar-ute glyphicon glyphicon-pencil"></a></td></tr>');
                        }
                        miembroForm.trigger("reset");
                        break;
                    case 201:
                        modalForm.modal('toggle');
                        msg.text('Debe ingresar los datos de contrato');
                        modalAlert.modal();
                        break;
                    case 206:
                        modalForm.modal('toggle');
                        msg.text('El porcentajes de participación de ganancias y/o participación remunerativa no debe superar el 100% entre todos los integrantes');
                        modalAlert.modal();
                        break;
                    case 304:
                        modalForm.modal('toggle');
                        msg.text('Ese cuit ya fue ingresado');
                        modalAlert.modal();
                        break;
                    case 305:
                        modalForm.modal('toggle');
                        msg.text('Este Integrante no existe como proveedor en ADIF');
                        modalAlert.modal();
                        break;
                    default:
                        modalForm.modal('toggle');
                        msg.text('Hubo un error al agregar el miembro, verifique los datos e intente nuevamente');
                        modalAlert.modal();
                        break;
                }
            }).fail(function (xhr, status, errorThrown) {
                if (xhr.status === 403) {
                    location.reload();
                }
            });
        }


    });

    $('#form_fecha_constitucion').datetimepicker({format: 'yyyy-mm-dd', language: 'es', minView: '2', autoclose: true});
    $('#form_fecha_finalizacion').datetimepicker({format: 'yyyy-mm-dd', language: 'es', minView: '2', autoclose: true});

    $('#agregarMiembro').on('click', function (e) {
        $('#agrega-miembro h4').html('Agregar Miembro');
        $('#miembroFormularioSubmit').text('Agregar');
        $('#miembroFormularioSubmit').attr("editar", false);
        $('#miembroForm').trigger("reset");
        $('#miembroFormularioSubmit').show();
        $('#miembroFormularioEditar').hide();

    });

    // Edit para ute.
    $(document).on("click", ".editar-ute", function () {

        // Modifico los textos del modal.
        $('#agrega-miembro h4').html('Editar Miembro');
        $('#miembroFormularioSubmit').text('Editar');

        // Obtengo los datos de la fila.
        var id = $(this).closest("tr").find("#id").text().trim();
        var cuit = $(this).closest("tr").find("#cuit").text().trim();
        var razonSocial = $(this).closest("tr").find("#rs").text().trim();
        var numeroInscripcion = $(this).closest("tr").find("#inscripcion").text().trim();
        var ganancias = $(this).closest("tr").find("#ganancias").text().trim();
        var remunerativa = $(this).closest("tr").find("#remuneracion").text().trim();

        // Cargo los campos del modal.
        $("#form_cuit").val(cuit);
        $("#form_razon_social_miembro").val(razonSocial);
        $("#form_numero_inscripcion_miembro").val(numeroInscripcion);
        $("#form_participacion_ganancias").val(ganancias);
        $("#form_participacion_remunerativa").val(remunerativa);
        $("#miembroFormularioSubmit").attr("editar", id);

    });

    $('#inputDocUte').on('change', function (e) {
        var $label = $('#inputFileNameUte');
        var fileName = '';
        if (this.files[0]) {
            var form_appended = $('#form-group-for-append-ute');
            saveDoc('miembros_ute', 'inputDocUte', function (response) {
                if (response.sts === 200) {
                    form_appended.append('<div class="row" id="savedUteDoc' + response.data.id + '">\<input style="width: 20em" type="text" class="form-control adjunto" placeholder="documento.pdf" aria-describedby="archivo-ok"  value="' + response.data['nombreoriginal'] + '" disabled><i class="fa fa-times fa-2" aria-hidden="true" onclick="quitarDocumentoUte(' + response.data.id + ')" style="cursor: pointer;"></i></div>');
                    $('#inputDocUte').val('');
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


function quitarUte(id) {

    var url = __AJAX_PATH__ + "formulariopreinscripcion/eliminarmiembroute";
    var miembros = false;

    if ($("#tabla-datos-ute > tbody > tr").length > 1) {
        miembros = true;
    } else {
        changeTimeLineStep('#timeline_contratos_ute', 'bg-tomate', 'bg-verdin');
    }

    $.ajax(
            {
                url: url,
                method: 'POST',
                data: {id: id, miembros: miembros, idDatoPersonal: $('#proveedor-selector > option:selected').val()}
            }
    ).done(function (response) {
        if (response.sts === 200) {
            $('#miembro-ute-row-' + id).remove();
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

function quitarDocumentoUte(id) {
    removeDoc(id, function (e) {
        if (e.sts === 200) {
            $("#msg").text('Se ha quitado el documento');
            $("#ask-pass-alert").modal();
            $('#savedUteDoc' + id).remove();
        }
    })
}