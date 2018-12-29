jQuery(document).ready(function () {
    $(document).on("submit", "#form-actividades-extranjero-ps", function (e) {
        e.preventDefault();
        return false;
    });

    $('#articulo-de-convenio-aplicable').val($('#form_articulo_de_convenio_aplicable').val());
    $("#form_tipo_prestacion_asistencia_tecnica").removeAttr("required");
    $("#form_articulo_de_convenio_aplicable").prop('disabled', true);

    $('#form_tipo_prestacion_asistencia_tecnica').on('click', function () {
        if ( $("#form_tipo_prestacion_asistencia_tecnica").is(':checked')) {
            $("form_tipo_prestacion_asistencia_tecnica").prop('required',true);
        }
        else {
            $("#form_tipo_prestacion_asistencia_tecnica").removeAttr("required");
        }
    });

    $('#prestacion_servicios_extranjero').on('click', function () {
        if ($(this).attr('aria-pressed') === 'false') {
            $("#panel-prestacion-servicio").show();
        }
        else {
            $("#panel-prestacion-servicio").hide();
        }
    });

    $('#convenio_unilateral').on('click', function () {
        if ($(this).attr('aria-pressed') === 'false') {
            $("#form-convenio-unilateral").show();
        }
        else {
            $("#form-convenio-unilateral").hide();
        }
    });

    $('#tributacion_internacional').on('click', function () {
        if ($(this).attr('aria-pressed') === 'true') {
            $("#form_articulo_de_convenio_aplicable").val('');
            $("#form_articulo_de_convenio_aplicable").prop('disabled', true);
            $("#form_articulo_de_convenio_aplicable").removeAttr("required");
        }
        else {
            $("#form_articulo_de_convenio_aplicable").prop('disabled', false);
            $("#form_articulo_de_convenio_aplicable").prop('required',true);
        }
    });

    $('#form_convenio_unilateral_aplicacion_caba').on('keyup', function(){
        if($('#convenio_unilateral').attr('aria-pressed') === 'true') {
            if($('#form_convenio_unilateral_aplicacion_caba').val() < 0 || $('#form_convenio_unilateral_aplicacion_caba').val() > 100 ) {
                $('#form_convenio_unilateral_aplicacion_caba').parent().addClass('has-error');
                $('#form_convenio_unilateral_aplicacion_caba').focus();
                $('#convenio_unilateral_aplicacion_caba_msj').show().text('El valor debe estar entre 0 y 100');
                return false;
            } else {
                $('#form_convenio_unilateral_aplicacion_caba').parent().removeClass('has-error');
                $('#convenio_unilateral_aplicacion_caba_msj').hide();
            }
        }
    });

    $('#form-actividades-extranjero-tp').validate({
        rules: {
            'form[prestacion_servicio_numero]':{
                required: true
            },
            'form[prestacion_servicio_regimen]':{
                required: true
            },
            'form[tipo_prestacion_otros]': {
                required: false,
                letrasynumeros:true
            }
        },
        messages: {
            'form[tipo_prestacion_otros]': {
                letrasynumeros:"Ingrese una descripcion valida"
            }
        }
    });

    $('#form-actividades-extranjero-ps').validate({
        rules: {
            'form[prestacion_servicio_numero]':{
                required: true
            },
            'form[prestacion_servicio_regimen]':{
                required: true
            },
            'form[prestacion_servicio_porcentaje_excension]': {
                required:true
            },
            'form[prestacion_servicio_fecha_desde]': {
                required:true
            },
            'form[prestacion_servicio_fecha_hasta]': {
                required:true
            },
        }
    });
    $('#form-actividades-extranjero-cu').validate({
        rules: {
            'form[convenio_unilateral_aplicacion_caba]':{
                required: true
            },
            'form[prestacion_servicio_regimen]':{
                required: true
            },
            'form[prestacion_servicio_porcentaje_excension]': {
                required:true
            },
            'form[prestacion_servicio_fecha_desde]': {
                required:true
            },
            'form[prestacion_servicio_fecha_hasta]': {
                required:true
            },
        }
    });

    $('#form-articulo_de_convenio_aplicable').validate({
        rules: {
            'form[articulo_de_convenio_aplicable]': {
                letrasynumeros: true,
                required: false
            }
        },
        messages: {
            'form[articulo_de_convenio_aplicable]': {
                letrasynumeros:"Ingrese una descripcion valida"
            }
        }
    });

    $('#guardarBorradorActividadesExtranjero').on('click', function () {

        if(!$('#form-actividades-extranjero-tp').valid())
            return;

        if ($('#prestacion_servicios_extranjero').attr('aria-pressed') === 'true') {
            document.getElementById("submit-prestacion-btn").click();
            if (!$('#form-actividades-extranjero-ps').valid()) {
                return false;
            }else if($('#convenio_unilateral').attr('aria-pressed') === 'true') {
                if (!$('#form-actividades-extranjero-cu').valid()) {
                    return false;
                }
            }
        }

        if(!$('#form-articulo_de_convenio_aplicable').valid())
            return;

        var isTrueSet1 = ($('#exportacion_bienes_extranjero').attr('aria-pressed') === 'true');
        $('#form_exportacion_bienes_extranjero').val(isTrueSet1);

        var isTrueSet2 = ($('#prestacion_servicios_extranjero').attr('aria-pressed') === 'true');
        $('#form_prestacion_servicios_extranjero').val(isTrueSet2);

        var isTrueSet3 = ($('#convenio_unilateral').attr('aria-pressed') === 'true');
        $('#form_convenio_unilateral').val(isTrueSet3);

        if(isTrueSet3) {
            if($('#form_convenio_unilateral_aplicacion_caba').parent().hasClass('has-error')) {
                $('#form_convenio_unilateral_aplicacion_caba').focus();
                return false;
            }
        }

        var isTrueSet4 = ($('#tributacion_internacional').attr('aria-pressed') === 'true');
        $('#form_convenio_tributacion_internacional').val(isTrueSet4);

        var isTrueSet5 = ($('#establecimiento_argentina').attr('aria-pressed') === 'true');
        $('#form_establecimiento_argentina').val(isTrueSet5);

        // $('#form_articulo_de_convenio_aplicable').val($('#articulo-de-convenio-aplicable').val());


        var url = __AJAX_PATH__ + "formulariopreinscripcion/agregaractividadextranjero";

        let idDatoPersonal = $('#proveedor-selector > option:selected').val();
        $('#form-actividades-extranjero-cbx').append('<input type="hidden" name="form[id_dato_personal]" value='+idDatoPersonal+'>');

        if(isTrueSet1){
            $('#form-actividades-extranjero-cbx').append('<input type="hidden" name="form[exportacion_bienes_extranjero_tipo_id]" value="897">');
        }

        if(isTrueSet2){
            $('#form-actividades-extranjero-cbx').append('<input type="hidden" name="form[prestacion_servicios_extranjero_tipo_id]" value="896">');
        }

        var formData = $('#form-actividades-extranjero-cbx').serialize() + '&'
            + $('#form-actividades-extranjero-ps').serialize() + '&'
            + $('#form-actividades-extranjero-cu').serialize() + '&'
            + $('#form-actividades-extranjero-tp').serialize() + '&'
            + $('#form-articulo_de_convenio_aplicable').serialize();


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
                changeTimeLineStep('#timeline_actividades', 'bg-verdin', 'bg-tomate');
            } else {
            }
        }).fail(function (xhr, status, errorThrown) {
            if (xhr.status === 403) {
                location.reload();
            }
        });

    });

    $("#form_prestacion_servicio_fecha_desde").datetimepicker({
        format: 'yyyy-mm-dd',
        language: 'es',
        minView: '2',
        autoclose: true
    }).on('changeDate', function (selected) {
        var minDate = new Date(selected.date.valueOf());
        var fechaHasta = new Date($('#form_prestacion_servicio_fecha_hasta').val());
        $('#form_prestacion_servicio_fecha_hasta').datetimepicker('setStartDate', minDate);

        if(fechaHasta < minDate) {
            $('#form_prestacion_servicio_fecha_hasta').val('');
        }
    }).on('keydown', function (e) {
        e.preventDefault();
        return false;
    });

    $('#form_prestacion_servicio_fecha_hasta').datetimepicker({
        format: 'yyyy-mm-dd',
        language: 'es',
        minView: '2',
        autoclose: true
    }).on('keydown', function (e) {
        e.preventDefault();
        return false;
    });

    $('#inputDocConvenioUnilateral').on('change', function (e) {
        var $label = $('#inputFileNameConvenioUnilateral');
        var fileName = '';
        if (this.files[0]) {
            var form_appended = $('#form-group-for-append-convenio-unilateral');
            saveDoc('convenio_unilateral', 'inputDocConvenioUnilateral', function (response) {
                if (response.sts === 200) {
                    form_appended.append('<div class="row" id="savedConvenioUnilateralDoc' + response.data.id + '">\<input style="width: 20em" type="text" class="form-control adjunto" placeholder="documento.pdf" aria-describedby="archivo-ok"  value="' + response.data['nombreoriginal'] + '" disabled><i class="fa fa-times fa-2" aria-hidden="true" onclick="quitarDocumentoConvenioUnilateral(' + response.data.id + ')" style="cursor: pointer;"></i></div>');
                    $('#inputDocConvenioUnilateral').val('');
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

    $('#inputDocConvenioTributacionInternacional').on('change', function (e) {
        var $label = $('#inputFileNameConvenioTributacionInternacional');
        var fileName = '';
        if (this.files[0]) {
            var form_appended = $('#form-group-for-append-convenio-tributacion-internacional');
            saveDoc('tributacion_internacional', 'inputDocConvenioTributacionInternacional', function (response) {
                if (response.sts === 200) {
                    form_appended.append('<div class="row" id="savedConvenioTributacionInternacionalDoc' + response.data.id + '">\<input style="width: 20em" type="text" class="form-control adjunto" placeholder="documento.pdf" aria-describedby="archivo-ok"  value="' + response.data['nombreoriginal'] + '" disabled><i class="fa fa-times fa-2" aria-hidden="true" onclick="quitarDocumentoConvenioTributacionInternacional(' + response.data.id + ')" style="cursor: pointer;"></i></div>');
                    $('#inputDocConvenioTributacionInternacional').val('');
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

    $('#inputDocEstablecimientoArgentina').on('change', function (e) {
        var $label = $('#inputFileNameEstablecimientoArgentina');
        var fileName = '';
        if (this.files[0]) {
            var form_appended = $('#form-group-for-append-establecimiento-argentina');
            saveDoc('establecimiento_argentina', 'inputDocEstablecimientoArgentina', function (response) {
                if (response.sts === 200) {
                    form_appended.append('<div class="row" id="savedEstablecimientoArgentinaDoc' + response.data.id + '">\<input style="width: 20em" type="text" class="form-control adjunto" placeholder="documento.pdf" aria-describedby="archivo-ok"  value="' + response.data['nombreoriginal'] + '" disabled><i class="fa fa-times fa-2" aria-hidden="true" onclick="quitarDocumentoEstablecimientoArgentina(' + response.data.id + ')" style="cursor: pointer;"></i></div>');
                    $('#inputDocEstablecimientoArgentina').val('');
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

function quitarDocumentoConvenioUnilateral(id) {
    removeDoc(id, function (e) {
        if (e.sts === 200) {
            $("#msg").text('Se ha quitado el documento');
            $("#ask-pass-alert").modal();
            $('#savedConvenioUnilateralDoc' + id).remove();
        }
    })
}

function quitarDocumentoConvenioTributacionInternacional(id) {
    removeDoc(id, function (e) {
        if (e.sts === 200) {
            $("#msg").text('Se ha quitado el documento');
            $("#ask-pass-alert").modal();
            $('#savedConvenioTributacionInternacionalDoc' + id).remove();
        }
    })
}

function quitarDocumentoEstablecimientoArgentina(id) {
    removeDoc(id, function (e) {
        if (e.sts === 200) {
            $("#msg").text('Se ha quitado el documento');
            $("#ask-pass-alert").modal();
            $('#savedEstablecimientoArgentinaDoc' + id).remove();
        }
    })
}