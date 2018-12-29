jQuery(document).ready(function () {

    $(document).on("submit", "#docRubro", function (e) {
        e.preventDefault();
        return false;
    });

    var botonAgregarRubro = $('#boton-agregar-rubro');

    $('#inputDocRubro').on('change', function (e) {
        var $label = $('#inputFileNameRubro');
        var fileName = '';
        if (this.files[0]) {
            var form_appended = $('#form-group-for-append-rubro');
            saveDoc('proveedor_rubro', 'inputDocRubro', function (response) {
                if (response.sts === 200) {
                    form_appended.append('<div class="row" id="savedRubroDoc' + response.data.id + '">\<input style="width: 20em" type="text" class="form-control adjunto" placeholder="documento.pdf" aria-describedby="archivo-ok"  value="' + response.data['nombreoriginal'] + '" disabled><i class="fa fa-times fa-2" aria-hidden="true" onclick="quitarDocumentoRubro(' + response.data.id + ')" style="cursor: pointer;"></i></div>');
                    $('#inputDocRubro').val('');
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

    $('#form_categoria').change(function () {

        var form_rubro = $('#form_rubro');

        if ($(this).val()) {

            var $btn = $(this);

            $btn.button('Cargando rubros');

            var url = __AJAX_PATH__ + "formulariopreinscripcion/listarclases";

            $.ajax(
                {
                    url: url,
                    method: 'POST',
                    data: {id: $(this).val()}
                }
            ).done(function (data) {
                // Si se encontraron al menos un ramal
                if (data.length > 0) {
                    form_rubro.empty();
                    form_rubro.prop('disabled', false);
                    for (var i = 0, total = data.length; i < total; i++) {
                        form_rubro.append('<option value="' + data[i].id + '">' + data[i].denominacion + '</option>');
                    }
                }
            });

        }
        else {
            form_rubro.prop('disabled', true);
            form_rubro.prop('value', null);
        }
    }).trigger('change');

    $('#guardarBorradorBtnRubro').on('click', function (e) {
        if($("#tabla-rubro2 > tbody > tr").length > 0){

            //agrego el id-dato-personal (value del combobox proveedores) al form
            let idDatoPersonal = $('#proveedor-selector > option:selected').val();
            var url = __AJAX_PATH__ + "formulariopreinscripcion/actualiza-galo";
            $.ajax(
                {
                    url: url,
                    method: 'POST',
                    data: idDatoPersonal
                }
            ).done(function (data) {

                $("#msg").text('Datos guardados en el borrador');
                $("#ask-pass-alert").modal();
                changeTimeLineStep('#timeline_rubro', 'bg-verdin', 'bg-tomate');
            });


        }
    })

    botonAgregarRubro.on('click', function (e) {

        var url = __AJAX_PATH__ + "formulariopreinscripcion/agregarclases";

        $.ajax(
            {
                url: url,
                method: 'POST',
                data: {
                    id: $('#form_rubro').val(),
                    idDatoPersonal: $('#proveedor-selector > option:selected').val()
                }
            }
        ).done(function (elem) {
            if (elem.sts !== 500) {
                $('#agrega-rubro').modal('toggle');
                $('#tabla-rubro2').append('<tr id="rubro-row-' + elem.id + '"><td hidden>' + elem.id + '</td><td>' + elem.data[0].categoria + '</td><td>' + elem.data[0].rubro + '</td><td><a onclick="quitarRubro(' + elem.id + ') " class="glyphicon glyphicon-remove"></a></td></tr>');
                changeTimeLineStep('#timeline_rubro', 'bg-verdin', 'bg-tomate');
                $("#msg").text('Rubro agregado correctamente.');
                $("#ask-pass-alert").modal();
            } else {
                $('#agrega-rubro').modal('toggle');
                $("#msg").text('Este rubro ya ha sido agregado');
                $("#ask-pass-alert").modal();
            }
        }).fail(function (xhr, status, errorThrown) {
            if (xhr.status === 403) {
                location.reload();
            }
        });
    })
});


function quitarRubro(id) {
    var url = __AJAX_PATH__ + "formulariopreinscripcion/quitarrubro";
    var rubros = false;

    if ($("#tabla-rubro2 > tbody > tr").length > 1) {
        rubros = true;
    } else {
        changeTimeLineStep('#timeline_rubro', 'bg-tomate', 'bg-verdin');
    }

    $.ajax(
        {
            url: url,
            method: 'POST',
            data: {
                id: id,
                rubros: rubros,
                idDatoPersonal: $('#proveedor-selector > option:selected').val()
            }
        }
    ).done(function (response) {
        if (response.sts === 200) {
            var rowCount = $('#tabla-rubro2 tr').length;
            if (rowCount < 3) {
                changeTimeLineStep('#timeline_rubro', 'bg-tomate', 'bg-verdin');
            }
            $("#msg").text('Rubro eliminado correctamente.');
            $("#ask-pass-alert").modal();
            $('#rubro-row-' + id).remove();
        } else {
            $("#msg").text('Error al eliminar rubro.');
            $("#ask-pass-alert").modal();
        }
    }).fail(function (xhr, status, errorThrown) {

        if (xhr.status === 403) {
            location.reload();
        }
    });
}

function quitarDocumentoRubro(id) {
    removeDoc(id, function (e) {
        if (e.sts === 200) {
            $("#msg").text('Se ha quitado el documento');
            $("#ask-pass-alert").modal();
            $('#savedRubroDoc' + id).remove();
        }
    })
}

