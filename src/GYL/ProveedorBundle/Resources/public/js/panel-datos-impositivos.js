jQuery(document).ready(function () {

    $(document).on("submit", "#tipo-iva", function (e) {
        e.preventDefault();
        return false;
    });
    $(document).on("submit", "#tipo-suss", function (e) {
        e.preventDefault();
        return false;
    });
    $(document).on("submit", "#tipo-gcias", function (e) {
        e.preventDefault();
        return false;
    });
    $(document).on("submit", "#tipo-iibb", function (e) {
        e.preventDefault();
        return false;
    });

    $("#form_exento_regimen").keypress(function (e) {

        var maxlengthNumber = parseInt($('#form_exento_regimen').attr('maxlength'));
        var inputValueLength = $('#form_exento_regimen').val().length + 1;
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {

            return false;
        }
        if(maxlengthNumber < inputValueLength) {
            return false;
        }
    });

    //--Regimen de la Seguridad Social (SUSS)--

    //Al presionar Regimen de la Seguridad Social (SUSS) INSCRIPTO.
    $("#form_id_tipo_iva_suss_0").change(function(){
        //Habilito los radiobutton de Con o Sin empleados.
        $('#form_personal_a_cargo_suss_0').css({pointerEvents: "auto"});
        $('#form_personal_a_cargo_suss_1').css({pointerEvents: "auto"}).prop('disabled', false);
        //Deshabilito retencion
        $('#retencion_sussBtn').css({pointerEvents: "none"});
        $("#retencion_sussBtn").attr("aria-pressed", "false");
        $("#retencion_sussBtn").removeClass("active");
        //Deshabilito exento
        $('#exento_sussBtn').css({pointerEvents: "none"});
        $("#exento_sussBtn").attr("aria-pressed", "false");
        $("#exento_sussBtn").removeClass("active");
    });

    //Al presionar Regimen de la Seguridad Social (SUSS) NO INSCRIPTO.
    $("#form_id_tipo_iva_suss_1").change(function(){
        //Deshabilito el radiobutton Con Empleados.
        $('#form_personal_a_cargo_suss_0').prop('checked', true);
        $("#form_personal_a_cargo_suss_1").prop('checked', false).prop('disabled', true).css('pointer-events','none');
        //Deshabilito retencion
        $('#retencion_sussBtn').css({pointerEvents: "none"});
        $("#retencion_sussBtn").attr("aria-pressed", "false");
        $("#retencion_sussBtn").removeClass("active");
        //Deshabilito exento
        $('#exento_sussBtn').css({pointerEvents: "none"});
        $("#exento_sussBtn").attr("aria-pressed", "false");
        $("#exento_sussBtn").removeClass("active");
    });

    //Al presionar Regimen de la Seguridad Social (SUSS) MONOTRIBUTISTA.
    $("#form_id_tipo_iva_suss_2").change(function(){
        //Habilito los radiobutton de Con o Sin empleados.
        $('#form_personal_a_cargo_suss_0').css({pointerEvents: "auto"});
        $('#form_personal_a_cargo_suss_1').css({pointerEvents: "auto"}).prop('disabled', false);
        //Habilito retencion y exento.
        $('#retencion_sussBtn').css({pointerEvents: "auto"});
        $('#exento_sussBtn').css({pointerEvents: "auto"})
    });

    //Se realiza la validacion tanto al cambiar de opcion.
    $('#form_jurisdiccion_iibb').on('change', function() {
        tratamientoExentosIngresosBrutos(this.value);
    });

    //Como al cargar la pagina.
    tratamientoExentosIngresosBrutos($("#form_jurisdiccion_iibb").val());


    $('#inputDocIva').on('change', function (e) {
        var $label = $('#inputFileNameIva');
        var fileName = '';

        if (this.files[0]) {
            var form_appended = $('#form-group-for-append-iva');
            saveDoc('proveedor_datos_impositivos_iva', 'inputDocIva', function (response) {
                if (response.sts === 200) {
                    form_appended.append('<div class="row" id="savedIvaDoc' + response.data.id + '"><input style="width: 20em" type="text" class="form-control adjunto float-l" placeholder="documento.pdf" aria-describedby="archivo-ok"  value="' + response.data['nombreoriginal'] + '" disabled><i class="fa fa-times fa-2 float-l" aria-hidden="true" onclick="quitarDocumentoIva(' + response.data.id + ')" style="cursor: pointer;"></i></div>');
                    $('#inputDocIva').val('');
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

    $('#inputDocSuss').on('change', function (e) {
        var $label = $('#inputFileNameSuss');
        var fileName = '';

        if (this.files[0]) {
            var form_appended = $('#form-group-for-append-suss');
            saveDoc('proveedor_datos_impositivos_suss', 'inputDocSuss', function (response) {
                if (response.sts === 200) {
                    form_appended.append('<div class="row" id="savedSussDoc' + response.data.id + '"><input style="width: 20em" type="text" class="form-control adjunto float-l" placeholder="documento.pdf" aria-describedby="archivo-ok"  value="' + response.data['nombreoriginal'] + '" disabled><i class="fa fa-times fa-2 float-l" aria-hidden="true" onclick="quitarDocumentoSuss(' + response.data.id + ')" style="cursor: pointer;"></i></div>');
                    $('#inputDocSuss').val('');
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

    $('#inputDocGncias').on('change', function (e) {
        var $label = $('#inputFileNameGncias');
        var fileName = '';

        if (this.files[0]) {
            var form_appended = $('#form-group-for-append-gncias');
            saveDoc('proveedor_datos_impositivos_ganancias', 'inputDocGncias', function (response) {
                if (response.sts === 200) {
                    form_appended.append('<div class="row" id="savedGnciasDoc' + response.data.id + '"><input style="width: 20em" type="text" class="form-control adjunto float-l" placeholder="documento.pdf" aria-describedby="archivo-ok"  value="' + response.data['nombreoriginal'] + '" disabled><i class="fa fa-times fa-2 float-l" aria-hidden="true" onclick="quitarDocumentoGncias(' + response.data.id + ')" style="cursor: pointer;"></i></div>');
                    $('#inputDocGncias').val('');
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

    $('#inputDocIibb').on('change', function (e) {
        var $label = $('#inputFileNameIibb');
        var fileName = '';

        if (this.files[0]) {
            var form_appended = $('#form-group-for-append-iibb');
            saveDoc('proveedor_datos_impositivos_iibb', 'inputDocIibb', function (response) {
                if (response.sts === 200) {
                    form_appended.append('<div class="row" id="savedIibbDoc' + response.data.id + '"><input style="width: 20em" type="text" class="form-control adjunto float-l" placeholder="documento.pdf" aria-describedby="archivo-ok"  value="' + response.data['nombreoriginal'] + '" disabled><i class="fa fa-times fa-2 float-l" aria-hidden="true" onclick="quitarDocumentoIibb(' + response.data.id + ')" style="cursor: pointer;"></i></div>');
                    $('#inputDocIibb').val('');
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

    $('#inputDocDatosImpositivos').on('change', function (e) {
        var $label = $('#inputFileNameDatosImpositivos');
        var fileName = '';

        if (this.files[0]) {
            var form_appended = $('#form-group-for-append-datosimpositivos');
            saveDoc('proveedor_datos_impositivos_constancia_inscripcion', 'inputDocDatosImpositivos', function (response) {
                if (response.sts === 200) {
                    form_appended.append('<div class="row" id="savedDatosImpositivosDoc' + response.data.id + '"><input style="width: 20em" type="text" class="form-control adjunto float-l" placeholder="documento.pdf" aria-describedby="archivo-ok"  value="' + response.data['nombreoriginal'] + '" required="required" disabled><i class="fa fa-times fa-2 float-l" aria-hidden="true" onclick="quitarDocumentoDatosImpositivos(' + response.data.id + ')" style="cursor: pointer;"></i></div>');
                    $('#inputDocDatosImpositivos').val('');
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

    $('#form_inputFileNameDdjj').on('change', function (e) {
        var $label = $('#form_inputFileNameDdjj');
        var fileName = '';

        if (this.files[0]) {
            var form_appended = $('#form-group-for-append-ddjj');
            saveDoc('proveedor_datos_impositivos_ddjj', 'form_inputFileNameDdjj', function (response) {
                if (response.sts === 200) {
                    form_appended.append('<br><br><div class="row" id="savedDdjjDoc' + response.data.id + '" style="margin-left:0.2%;"><input style="width: 20em" type="text" class="form-control adjunto ddjjFile float-l" placeholder="documento.pdf" aria-describedby="archivo-ok"  value="' + response.data['nombreoriginal'] + '" disabled><i class="fa fa-times fa-2 float-l" aria-hidden="true" onclick="quitarDocumentoDdjj(' + response.data.id + ')" style="cursor: pointer;"></i></div>');
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

    $('#showModalExcentoIva').on('click', function () {
        if (($('#exento_ivaBtn').attr('aria-pressed') === 'true')) {
            // Modifico los textos del modal.
            $('#label-datos-exento').html('Editar Datos Exento');
            $('#agregarFormExentoBtn').text('Editar');
            
            $('#exentoIva').modal({
                backdrop: 'static',
                keyboard: false
            });
        } else if($('#exento_ivaBtn').attr('aria-pressed') === 'false'){
            $('#label-datos-exento').html('Agregar Datos Exento');
            $('#agregarFormExentoBtn').text('Agregar');
        }
    });

    $('#form_exento_fecha_desde').datetimepicker({
        format: 'yyyy-mm-dd',
        minView: '2',
        language: 'es',
        autoclose: true
    });

    $('#form_exento_fecha_hasta').datetimepicker({
        format: 'yyyy-mm-dd',
        minView: '2',
        language: 'es',
        autoclose: true
    });

    $('#exento_ivaBtn').on('click', function (e) {
        if (!($('#exento_ivaBtn').attr('aria-pressed') === 'true')) {
            $('#label-datos-exento').html('Agregar Datos Exento');
            $('#agregarFormExentoBtn').text('Agregar');
            $('#exentoIva').modal({
                backdrop: 'static',
                keyboard: false
            });
        } else {
            $('#form_exento_porcentaje_exencion').val("");
            $('#form_exento_regimen').val("");
            $('#form_exento_fecha_desde').val("");
            $('#form_exento_fecha_hasta').val("");
            $('#showModalExcentoIva').hide();
            // Modifico los textos del modal.
            $('#label-datos-exento').html('Editar Datos Exento');
            $('#agregarFormExentoBtn').text('Editar');
        }
    });

    $('#agregarFormExentoBtn').on('click', function (e) {
        e.preventDefault();
        $('#tipo-exento').validate({
            rules:{
                'form[exento_porcentaje_exencion]':{
                    required: true,
                    solonumeros: true
                },
                'form[exento_regimen]': {
                    required: true,
                    porcentajes: true
                },
                'form[exento_fecha_desde]:':{
                    required: true,
                    fechaValida: true
                },
                'form[exento_fecha_hasta]':{
                    required: true,
                    fechaValida: true
                },
                'form[exento_otros]': {
                    letrasynumeros: true
                }
            },
            messages:{
                'form[exento_porcentaje_exencion]':{
                    solonumeros: 'Ingrese solo numeros.'
                },
                'form[exento_regimen]': {
                    porcentajes: "Ingrese un porcentaje valido."
                },
                'form[exento_otros]': {
                    letrasynumeros: "Ingrese un valor alfanumerico, caracteres especiales no admitidos."
                }
            }
        });
        if ($('#tipo-exento').valid()){
            $('#exentoIva').modal('toggle');
            $('#showModalExcentoIva').show();
        }

    });

    $('#cerrarModalExento').on('click', function (e) {
        $('#tipo-exento').validate().resetForm();
        //negrada pero no tengo tiempo de fijarme xq el puto resetForm no resetea los val de los campos, fuck it
        $('#form_exento_porcentaje_exencion, #form_exento_regimen, #form_exento_otros').val('');
        $('#exento_ivaBtn').attr('aria-pressed', 'false').removeClass('active');
        $('#showModalExcentoIva').hide();
        $('#exentoIva').modal('toggle');
    });
});

function quitarDocumentoIva(id) {
    removeDoc(id, function (e) {
        if (e.sts === 200) {
            $("#msg").text('Se ha quitado el documento');
            $("#ask-pass-alert").modal();
            $('#savedIvaDoc' + id).remove();
        }
    })
}

function quitarDocumentoSuss(id) {
    removeDoc(id, function (e) {
        if (e.sts === 200) {
            $("#msg").text('Se ha quitado el documento');
            $("#ask-pass-alert").modal();
            $('#savedSussDoc' + id).remove();
        }
    })
}

function quitarDocumentoGncias(id) {
    removeDoc(id, function (e) {
        if (e.sts === 200) {
            $("#msg").text('Se ha quitado el documento');
            $("#ask-pass-alert").modal();
            $('#savedGnciasDoc' + id).remove();
        }
    })
}

function quitarDocumentoIibb(id) {
    removeDoc(id, function (e) {
        if (e.sts === 200) {
            $("#msg").text('Se ha quitado el documento');
            $("#ask-pass-alert").modal();
            $('#savedIibbDoc' + id).remove();
        }
    })
}

function quitarDocumentoDdjj(id) {
    removeDoc(id, function (e) {
        if (e.sts === 200) {
            $("#msg").text('Se ha quitado el documento');
            $("#ask-pass-alert").modal();
            $('#savedDdjjDoc' + id).remove();
            $('#form_inputFileNameDdjj').val('').attr('required',true)
        }
    })
}

function quitarDocumentoDatosImpositivos(id) {
    removeDoc(id, function (e) {
        if (e.sts === 200) {
            $("#msg").text('Se ha quitado el documento');
            $("#ask-pass-alert").modal();
            $('#savedDatosImpositivosDoc' + id).remove();
        }
    })
}

function tratamientoExentosIngresosBrutos(valor)
{
    var opcion_numero = valor; // Esta opcion corresponde a CABA.
    //Si esta seleccionado CABA.
    if(opcion_numero != 1)
    {
        $('#exento_iibbBtn').css({pointerEvents: "none"});
        $("#exento_iibbBtn").attr("aria-pressed", "false");
        $("#exento_iibbBtn").removeClass("active");
    }
    else
        $('#exento_iibbBtn').css({pointerEvents: "auto"});
}

function guardarDatosImpositivos() {

    var formIva = $('#tipo-iva');
    var formSuss = $('#tipo-suss');
    var formGcias = $('#tipo-gcias');
    var formIibb = $('#tipo-iibb');
    var formCae = $('#tipo-cae');
    var formExento = $('#tipo-exento');
    var inputDocDdjj = $('#form_inputFileNameDdjj')[0];

    if(!inputDocDdjj.checkValidity()){
        $("#msg").text('Debe descargar, completar y adjuntar la declaración jurada');
        $("#div-ddjj").focus();
        $("#ask-pass-alert").modal();
        return false
    }

    var exento_ganancias = ($('#exento_gananciasBtn').attr('aria-pressed') === 'true');
    $('#form_exento_ganancias').val(exento_ganancias);

    var exento_iibb = ($('#exento_iibbBtn').attr('aria-pressed') === 'true');
    $('#form_exento_iibb').val(exento_iibb);

    var exento_iva = ($('#exento_ivaBtn').attr('aria-pressed') === 'true');
    $('#form_exento_iva').val(exento_iva);

    var exento_suss = ($('#exento_sussBtn').attr('aria-pressed') === 'true');
    $('#form_exento_suss').val(exento_suss);

    var retencion_ganancias = ($('#retencion_gananciasBtn').attr('aria-pressed') === 'true');
    $('#form_retencion_ganancias').val(retencion_ganancias);

    var retencion_iibb = ($('#retencion_iibbBtn').attr('aria-pressed') === 'true');
    $('#form_retencion_iibb').val(retencion_iibb);

    var retencion_iva = ($('#retencion_ivaBtn').attr('aria-pressed') === 'true');
    $('#form_retencion_iva').val(retencion_iva);

    var retencion_suss = ($('#retencion_sussBtn').attr('aria-pressed') === 'true');
    $('#form_retencion_suss').val(retencion_suss);


    let idDatoPersonal = $('#proveedor-selector > option:selected').val();
    formIva.append('<input type="hidden" name="form[id_dato_personal]" value='+idDatoPersonal+'>');

    var formData = formIva.serialize() + '&' +
        formSuss.serialize() + '&' +
        formGcias.serialize() + '&' +
        formIibb.serialize() + '&' +
        formCae.serialize() + '&' +
        formExento.serialize();

    var url = __AJAX_PATH__ + "formulariopreinscripcion/agregardatosimpositivos";

    formIva.validate({
        errorPlacement: function(error, element) {
            error.appendTo( element.parents(".form-group") );
        },
        rules:{
            'form[otros_iva]':{
                letrasynumeros: true
            }
        },
        messages:{
            'form[otros_iva]':{
                letrasynumeros: "Ingrese un valor alfanumerico, caracteres especiales no admitidos."
            }
        }
    });

    formSuss.validate({
        errorPlacement: function(error, element) {
            error.appendTo( element.parents(".form-group") );
        }
    });

    formGcias.validate({
        errorPlacement: function(error, element) {
            error.appendTo( element.parents(".form-group") );
        },
        rules:{
            'form[otros_ganancias]':{
                letrasynumeros: true
            }
        },
        messages:{
            'form[otros_ganancias]':{
                letrasynumeros: "Ingrese un valor alfanumerico, caracteres especiales no admitidos."
            }
        }
    });

    formIibb.validate({
        errorPlacement: function(error, element) {
            error.appendTo( element.parents(".form-group") );
        },
        rules:{
            'form[numero_inscripcion_iibb]':{
                required: true,
                nroinscripcioniibb: true
            },
            'form[otros_iibb]': {
                letrasynumeros: true
            }
        },
        messages:{
            'form[numero_inscripcion_iibb]':{
                nroinscripcioniibb: "Ingrese un valor numerico, guiones admitidos."
            },
            'form[otros_iibb]': {
                letrasynumeros: "Ingrese un valor alfanumerico, caracteres especiales no admitidos."
            }
        }
    });
    
    formCae.validate({
        errorPlacement: function(error, element) {
            error.appendTo( element.parents(".form-group") );
        },
        rules:{
            'form[otros_dato_impositivo]':{
                letrasynumeros: true
            }
        },
        messages:{
            'form[otros_dato_impositivo]':{
                letrasynumeros: "Ingrese un valor alfanumerico, caracteres especiales no admitidos."
            }
        }
    });

    formIva.valid();
    formSuss.valid();
    formGcias.valid();
    formIibb.valid();
    formCae.valid();

    if (!formIva.valid()){
        $('#errorMsgTipoIva').focus();
        $('#errorMsgTipoIva').trigger('blur');
        return false;
    }

    if (!formSuss.valid()){
        $('#errorMsgTipoSuss').focus();
        $('#errorMsgTipoSuss').trigger('blur');
        return false;
    }

    if (!formGcias.valid()){
        $('#errorMsgTipoGcias').focus();
        $('#errorMsgTipoGcias').trigger('blur');
        return false;
    }
    
    if (!formIibb.valid()){
        $('#errorMsgTipoIibb').focus();
        $('#errorMsgTipoIibb').trigger('blur');
        return false;
    }

    if (!formCae.valid()){
        $('#errorMsgTipoCae').focus();
        $('#errorMsgTipoCae').trigger('blur');
        return false;
    }

    if((formExento.valid() && $('#exento_ivaBtn').attr('aria-pressed') === 'true') ||
        $('#exento_ivaBtn').attr('aria-pressed') === 'false'){
        $.ajax({
            url: url,
            method: 'POST',
            data: formData
        }).done(function (response) {
            if (response.sts === 200) {
                $("#msg").text('Datos Guardados en Borrador');
                $("#ask-pass-alert").modal();
                changeTimeLineStep('#timeline_datos_impositivos', 'bg-verdin', 'bg-tomate');
            } else {
                $("#msg").text('Se ha producido un error al guardar el formulario');
                $("#ask-pass-alert").modal();
            }
        }).fail(function (xhr, status, errorThrown) {
            if (xhr.status === 403) {
                location.reload();
            }
        });
    }
}