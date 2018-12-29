jQuery(document).ready(function () {

    $('#form_fecha_designacion_apod').datetimepicker({
        format: 'yyyy-mm-dd',
        minView: '2',
        language: 'es',
        autoclose: true,
        pickerPosition: 'top-right'
    });

    $('.cuil-mask').inputmask('99-99999999-9');
    $('#form_numero_documento_apod').inputmask('99.999.999');

    $(document).on("submit", "#formRrepresentante", function (e) {
        e.preventDefault();
        return false;
    });

    $('#formRrepresentanteSumbit').on('click', function (e) {
        var flagEdit = $(this).attr("editar");

        // Flag para editar.
        if (flagEdit === 'false') {
            var url = __AJAX_PATH__ + "formulariopreinscripcion/agregarnuevorepresentante";
        } else {
            var url = __AJAX_PATH__ + "formulariopreinscripcion/editarrepresentante/" + flagEdit;
        }

        $('.cuil-mask').inputmask();

        //agrego el id-dato-personal (value del combobox proveedores) al form
        let formRrepresentante = $('#formRrepresentante');
        let idDatoPersonal = $('#proveedor-selector > option:selected').val();
        formRrepresentante.append('<input type="hidden" name="form[id_dato_personal]" value='+idDatoPersonal+'>');
        let formData = formRrepresentante.serialize();
        $('.cuil-mask').inputmask('99-99999999-9');
        var numeroDocumentoRepresentante = $('#form_numero_documento_apod');

        //validate setup
        formRrepresentante.validate({
            rules: {
                'form[nombre_apod]': {
                    required: true,
                    nyp: true
                },
                'form[apellido_apod]': {
                    required: true,
                    nyp: true
                },
                'form[cuit_cuil_apod]': {
                    required: true,
                    cuit: true
                },
                'form[tipo_documento_apod]': {
                    required: true
                },
                'form[numero_documento_apod]': {
                    required: true,
                    dni: true
                },
                'form[fecha_designacion_apod]': {
                    required: true,
                    fechaValida: true
                }
            },
            messages: {
                'form[nombre_apod]': {
                    nyp:"Ingrese un nombre válido, numeros y caracteres especiales no admitidos."
                },
                'form[apellido_apod]': {
                    nyp:"Ingrese un apellido válido, numeros y caracteres especiales no admitidos."
                },
                'form[numero_documento_apod]': {
                    dni:"Ingrese un DNI válido."
                },

            }
        });
        if (formRrepresentante.valid()) {
            $.ajax(
                    {
                        url: url,
                        method: 'POST',
                        data: formData
                    }
            ).done(function (response) {
                if (response.sts === 200) {
                    if (response.msg === 'existente') {
                        $('#agregar-representantes-apoderados').modal('toggle');
                        $("#msg").text('El DNI del representante ya fue ingresado');
                        $("#ask-pass-alert").modal();
                    } else {
                        if (flagEdit === 'false') {
                            $('#tabla-apoderados-representantes').append('<tr id="apoderado-row-' + response.data.form['id'] + '"><td id="id" hidden> ' + response.data.form['id'] + '</td><td id="nombre">' + $('#form_nombre_apod').val() + '</td><td id="apellido">' + $('#form_apellido_apod').val() + '</td><td id="cuit">' + response.data.form['cuit_cuil_apod'] + '</td>\<td>' + $('#form_tipo_documento_apod option:selected').text() + '</td><td id="documento">' + numeroDocumentoRepresentante.val() + '</td><td id="fecha-designacion">' + $('#form_fecha_designacion_apod').val() + '</td><td align="center"><input type="checkbox"></td><td align="center"><input type="checkbox"></td><td align="center"><input type="checkbox"></td><td align="center"><input type="checkbox"></td><td align="center"><input type="checkbox"></td><td align="center"><input type="checkbox"></td><td hidden>' + $('#form_tipo_documento_apod').val() + '</td><td id="quitarRepreTd' + numeroDocumentoRepresentante.val() + '"><a onclick="quitarRepresentanteApoderado(' + response.data.form['id'] + ')" class="glyphicon glyphicon-remove"></a><a data-target="#agregar-representantes-apoderados" data-toggle="modal" id="editarRepresentante" class="editar-representante glyphicon glyphicon-pencil"></a></td></tr>');
                        } else {
                            $('#apoderado-row-' + response.data.form['id']).find('#nombre').html(response.data.form['nombre_apod']);
                            $('#apoderado-row-' + response.data.form['id']).find('#apellido').html(response.data.form['apellido_apod']);
                            $('#apoderado-row-' + response.data.form['id']).find('#cuit').html(response.data.form['cuit_cuil_apod']);
                            $('#apoderado-row-' + response.data.form['id']).find('#documento').html(response.data.form['numero_documento_apod']);
                            $('#apoderado-row-' + response.data.form['id']).find('#fecha-designacion').html(response.data.form['fecha_designacion_apod']);
                        }
                        $('#agregar-representantes-apoderados').modal('toggle');
                        modalReset();
                    }
                } else {
                    $('#agregar-representantes-apoderados').modal('toggle');
                    $("#msg").text('Error verifique los datos e inténtelo nuevamente');
                    $("#ask-pass-alert").modal();
                }
            }).fail(function (xhr, status, errorThrown) {
                if (xhr.status === 403) {
                    location.reload();
                }
            });
        }


    });

    $('#guardarBorradorRepresentantes').on('click', function () {

        var rows = [];
        var trChecked = [];
        var stopFlag = false;
        // $("tbody tr", $("#tabla-apoderados-representantes")).each(function (i, v) {
        $("#tabla-apoderados-representantes > tbody > tr").each(function (i, v) {
            rows[i] = [];
            trChecked[i] = [];
            $(this).children('td').each(function (ii, vv) {
                if ($(this)[0].childNodes[0].type === 'checkbox') {
                    rows[i][ii] = $(this)[0].childNodes[0].checked;
                    trChecked[i].push($(this)[0].childNodes[0].checked);
                } else {
                    rows[i][ii] = $(this).text();
                }
            });
        });
        trChecked.forEach(function (value, index) {
            if (!value.includes(true)) {
                $("#msg").text('Debe seleccionar al menos un rol para cada representante');
                $("#ask-pass-alert").modal();
                stopFlag = true;
            }
            ;
        })
        if (stopFlag)
            return false;

        var url = __AJAX_PATH__ + "formulariopreinscripcion/modificarrepresentantes";
        $.ajax(
                {
                    url: url,
                    method: 'POST',
                    data: {
                        data_array: rows,
                        idDatoPersonal: $('#proveedor-selector > option:selected').val()
                    }
                }
        ).done(function (response) {
            if (response.sts === 200) {
                changeTimeLineStep('#timeline_representantes_apoderados', 'bg-verdin', 'bg-tomate');
                $("#msg").text('Datos Guardados en Borrador');
                $("#ask-pass-alert").modal();
            } else {
                $("#msg").text('Error inténtelo nuevamente');
                $("#ask-pass-alert").modal();
            }
        }).fail(function (xhr, status, errorThrown) {
            if (xhr.status === 403) {
                location.reload();
            }
        })
    });


    $('#inputDocApo').on('change', function (e) {
        var $label = $('#inputFileNameApo');
        var fileName = '';

        if (this.files[0]) {
            var form_appended = $('#form-group-for-append-apoderado');
            saveDoc('proveedor_representante_apoderado', 'inputDocApo', function (response) {
                if (response.sts === 200) {
                    form_appended.append('<div class="row" id="savedApoDoc' + response.data.id + '"><input style="width: 20em" type="text" class="form-control adjunto float-l" placeholder="documento.pdf" aria-describedby="archivo-ok"  value="' + response.data['nombreoriginal'] + '" disabled><i class="fa fa-times fa-2 float-l" aria-hidden="true" onclick="quitarDocumentoApo(' + response.data.id + ')" style="cursor: pointer;"></i></div>');
                    $('#inputDocApo').val('');
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

    $("#agregarRrepresentante").on('click', function () {
        $('#agregar-representantes-apoderados h4').html('Agregar Representante');
        $('#formRrepresentanteSumbit').text('Agregar');
        $('#formRrepresentanteSumbit').attr("editar", false);
        $('#formRrepresentante').trigger("reset");
    });

    // Edit para contactos.
    $(document).on("click", ".editar-representante", function () {

        // Modifico los textos del modal.
        $('#agregar-representantes-apoderados h4').html('Editar Representante');
        $('#formRrepresentanteSumbit').text('Editar');

        // Obtengo los datos de la fila.
        var id = $(this).closest("tr").find("#id").text().trim();
        var nombre = $(this).closest("tr").find("#nombre").text().trim();
        var apellido = $(this).closest("tr").find("#apellido").text().trim();
        var cuit = $(this).closest("tr").find("#cuit").text().trim();
        var documento = $(this).closest("tr").find("#documento").text().trim();
        var fecha = $(this).closest("tr").find("#fecha-designacion").text().trim();
        if(fecha.split('/')[2] != undefined)
            var fecha_adecuada = fecha.split('/')[2]+"-"+fecha.split('/')[1]+"-"+fecha.split('/')[0];
        else
            var fecha_adecuada = fecha;
        // Cargo los campos del modal.
        $("#form_nombre_apod").val(nombre);
        $("#form_apellido_apod").val(apellido);
        $("#form_cuit_cuil_apod").val(cuit);
        $("#form_numero_documento_apod").val(documento);
        $("#form_fecha_designacion_apod").val(fecha_adecuada);
        $("#formRrepresentanteSumbit").attr("editar", id);

    });

    $('#closeModalRepre').on('click', function () {
        $('#formRrepresentante').validate().resetForm();
    })
});

function quitarDocumentoApo(id) {
    removeDoc(id, function (e) {
        if (e.sts === 200) {
            $("#msg").text('Se ha quitado el documento');
            $("#ask-pass-alert").modal();
            $('#savedApoDoc' + id).remove();
        }
    })
}

function quitarRepresentanteApoderado(id) {
    var url = __AJAX_PATH__ + "formulariopreinscripcion/eliminarepresentanteapoderado/";
    var representantesApoderados = false;
    
    if ($("#tabla-apoderados-representantes > tbody > tr").length > 1) {
        representantesApoderados = true;
    } else {
        changeTimeLineStep('#timeline_representantes_apoderados', 'bg-tomate', 'bg-verdin');
    }
    
    if (id) {
        $.ajax(
            {
                url: url,
                method: 'POST',
                data: {id: id, representantesApoderados: representantesApoderados, idDatoPersonal: $('#proveedor-selector > option:selected').val()}
            }
        ).done(function (response) {
            $('#apoderado-row-' + id).remove();

            var rowCount = $('#tabla-apoderados-representantes tr').length;
            if (rowCount < 2) {
                changeTimeLineStep('#timeline_representantes_apoderados', 'bg-tomate', 'bg-verdin');
            }
        }).fail(function (xhr, status, errorThrown) {
            if (xhr.status === 403) {
                location.reload();
            }
        })
    } else {
        $("#msg").text('Seleccione un contacto');
        $("#ask-pass-alert").modal();
    }
}

function modalReset() {
    //elimina los valores de los campos al cerrar el modal
    $('#form_nombre_apod').val("");
    $('#form_apellido_apod').val("");
    $('#form_cuit_cuil_apod').val("");
    $('#form_numero_documento_apod').val("");
}


