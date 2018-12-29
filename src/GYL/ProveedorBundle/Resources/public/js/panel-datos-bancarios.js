jQuery(document).ready(function () {

    $(document).on("submit", "#form-datos-bancarios", function (e) {
        e.preventDefault();
        return false;
    });


    $('#guardarDatosBancarios').on('click', function () {

        var inputComprobanteCBU = $('#inputDocComprobanteCBU')[0];

        if(!inputComprobanteCBU.checkValidity()){
            $("#msg").text('Debe adjuntar el comprobante de CBU');
            $("#div-ddjj").focus();
            $("#ask-pass-alert").modal();
            return false;
        }

        let formDatosBancarios = $('#form-datos-bancarios');
        let idDatoPersonal = $('#proveedor-selector > option:selected').val();
        formDatosBancarios.append('<input type="hidden" name="form[id_dato_personal]" value='+idDatoPersonal+'>');

        $('#form-datos-bancarios').validate({
            rules: {
                'form[cbu_datos_bancario]': {
                    required: true,
                    cbu: true
                },
                'form[sucursal]':{
                    required: true,
                    letrasynumeros: true
                },
                'form[numero_sucursal]': {
                    required: true,
                    nrosucursal: true
                },
                'form[numero_cuenta_dato_bancario]':{
                    required: true,
                    nrocuenta: true
                }
            },
            messages: {
                'form[sucursal]':{
                    letrasynumeros: 'Ingrese un dato válido, solo numeros y letras, caracteres especiales no admitidos.'
                },
                'form[numero_sucursal]': {
                    nrosucursal: 'Ingrese nro de sucursal, maximo 5 numeros enteros.'
                },
                'form[numero_cuenta_dato_bancario]':{
                    nrocuenta: 'Ingrese un dato numerico.'
                }
            }

        });
        
        if(formDatosBancarios.valid()) {
            let url = __AJAX_PATH__ + "formulariopreinscripcion/agregardatobancario";
            let formData = formDatosBancarios.serialize();

            $.ajax(
                {
                    url: url,
                    method: 'POST',
                    data: formData
                }
            ).done(function (response) {
                switch (response.sts) {
                    case 200:
                        $("#msg").text('Datos Guardados en Borrador');
                        $("#ask-pass-alert").modal();
                        changeTimeLineStep('#timeline_datos_bancarios', 'bg-verdin', 'bg-tomate');
                        break;
                    case 201:
                        $("#msg").text(response.msg);
                        $("#ask-pass-alert").modal();
                        break;
                    default:
                        $("#msg").text('Se ha producido un error al guardar los datos');
                        $("#ask-pass-alert").modal();
                        break;
                }
            }).fail(function (xhr, status, errorThrown) {
                if (xhr.status === 403) {
                    location.reload();
                }
            });
        }
    });

    $('#inputDocComprobanteCBU').on('change', function (e) {
        var $label = $('#inputFileNameComprobanteCBU');
        var fileName = '';

        if (this.files[0]) {
            var form_appended = $('#form-group-for-append-comprobantecbu');
            saveDoc('proveedor_datos_bancarios_cbu', 'inputDocComprobanteCBU', function (response) {
                if (response.sts === 200) {
                    form_appended.append('<div class="row" id="savedComprobanteCBUDoc' + response.data.id + '">\<input style="width: 20em" type="text" class="form-control adjunto float-l" placeholder="documento.pdf" aria-describedby="archivo-ok"  value="' + response.data['nombreoriginal'] + '" disabled><i class="fa fa-times fa-2 float-l" aria-hidden="true" onclick="quitarDocumentoComprobanteCBU(' + response.data.id + ')" style="cursor: pointer;"></i></div>');
                    $('#form_inputFileNameDdjj').val('').removeAttr('required');
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

    $('#inputDocDatosBancarios').on('change', function (e) {
        var $label = $('#inputFileNameDatosBancarios');
        var fileName = '';

        if (this.files[0]) {
            var form_appended = $('#form-group-for-append-datosbancarios');
            saveDoc('proveedor_datos_bancarios', 'inputDocDatosBancarios', function (response) {
                if (response.sts === 200) {
                    form_appended.append('<div class="row" id="savedDatosBancariosDoc' + response.data.id + '">\<input style="width: 20em" type="text" class="form-control adjunto float-l" placeholder="documento.pdf" aria-describedby="archivo-ok"  value="' + response.data['nombreoriginal'] + '" disabled><i class="fa fa-times fa-2 float-l" aria-hidden="true" onclick="quitarDocumentoDatosBancarios(' + response.data.id + ')" style="cursor: pointer;"></i></div>');
                    $('#inputDocDatosBancarios').val('');
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

function quitarDocumentoDatosBancarios(id) {
    removeDoc(id, function (e) {
        if (e.sts === 200) {
            $("#msg").text('Se ha quitado el documento');
            $("#ask-pass-alert").modal();
            $('#savedDatosBancariosDoc' + id).remove();
        }
    })
}

function quitarDocumentoComprobanteCBU(id) {
    removeDoc(id, function (e) {
        if (e.sts === 200) {
            $("#msg").text('Se ha quitado el documento');
            $("#ask-pass-alert").modal();
            $('#savedComprobanteCBUDoc' + id).remove();
            $('#inputDocComprobanteCBU').val('').attr('required',true)
        }
    })
}