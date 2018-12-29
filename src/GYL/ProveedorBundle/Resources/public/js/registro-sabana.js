var proveedorNacionalRules = {
    email: {
        required: true,
        email: true
    },
    cuit: {
        cuit: true,
        required: true
    }
};
var proveedorExtanjeroRules = {
    email: {
        required: true,
        email: true
    },
    identificacion_tributaria: {
        required: true
    },
    website: {
        required: true
    }
};


var panelesActualizados = JSON.parse(localStorage.getItem('panelesActualizados'));

if (panelesActualizados === null){
    var panelesActualizados = [];
}



jQuery(document).ready(function () {
    if (panelesActualizados[0] != undefined){
        if(panelesActualizados[0].idDatoPersonal != $('#proveedor-selector > option:selected').val()){
            panelesActualizados = [];
            localStorage.clear();
        }
    }

    if ($('#proveedor-selector option').length > 0) {
        $('#cuenta-corriente-div').attr('hidden', false);
    }

    if ($('#proveedor-selector option').length > 0) {
        $('#cuenta-corriente-div').attr('hidden', false);
    }

    var timeLineStatus = checkTimeLineStatus();

    if (timeLineStatus > 0) {
        $('.btn-checkTimeLineStatus').prop("disabled", true);
    }

    $('input.cambioChx').on('change', function () {
        $('input.cambioChx').not(this).prop('checked', false);
    });

    $(".cambioChx").removeAttr("required");

    $('#alert-info-formulario-sabana').css('pointer-events', 'all');
    $('#loading-gif').remove();
    $('#tipo-proveedor').css('opacity', '1');

    $('.cambioChx').each(function () {
        if (this.checked) {
            switch (this.id) {
                case 'form_persona_fisica':
                    personaFisica(this);
                    break;
                case 'form_persona_juridica':
                    personaJuridica(this);
                    break;
                case 'form_persona_contratos':
                    personaContratos(this);
                    break;
                case 'form_persona_fisica_extranjera_no_residente_del_pais':
                    personaFisicaExtranjeraNoResidente(this);
                    break;
                case 'form_persona_juridica_extranjera':
                    personaJuridicaExtranjera(this);
                    break;
            }
        }
    });

    $('#btn-GeneraPreInscripcion').on('click', function (e) {

        e.preventDefault();

        var cantErrores = checkTimeLineStatus();
        var uid = 1;

        if (cantErrores === 0) {
            var data = {
                uid: uid,
                idDatoPersonal: $('#proveedor-selector > option:selected').val()
            };
            var flagDisabled = 0;
            if ($("#tipo-proveedor input").is(":disabled")) {
                flagDisabled = 1;
                $("#tipo-proveedor input").prop('disabled', false);
                $("#tipo-proveedor input").prop('readonly', true);
            }

            $.ajax({
                type: "POST",
                data: data,
                //async: false,
                url: __AJAX_PATH__ + 'preinscripcion/generar-preinscripcion'

            }).done(function (response) {

                if (response.result === 'OK') {
                    $("#msg").text('Pre-Inscripción generada con exito');
                    $(".close").remove();
                    $("#btnAceptar").click(function () {
                        location.href = __AJAX_PATH__ + 'login';
                    });
                    $("#ask-pass-alert").modal({backdrop: 'static', keyboard: false});
                    $('.btn-checkTimeLineStatus').prop("disabled", true);

                    $("#tipo-proveedor input").prop('disabled', true);
                    $("#tipo-proveedor input").prop('readonly', false);


                } else if (response.result === 'NOK') {
                    $("#msg").text('Ocurrio un error');
                    $("#ask-pass-alert").modal();
                    if (flagDisabled == 1) {
                        $("#tipo-proveedor input").prop('disabled', true);
                        $("#tipo-proveedor input").prop('readonly', false);
                    }
                }
            });
        } else {
            $("#msg").text('Ha ocurrido un error al generar la pre-inscripción, revise los datos.');
            $("#ask-pass-alert").modal();
        }
    });

    $('#btn-ActualizaPreInscripcion').on('click', function (e) {

        e.preventDefault();

        var uid = 1;
        // localStorage.setItem('panelesActualizados', JSON.stringify(panelesActualizados));

        if (uid) {
            var data = {
                uid: uid,
                idDatoPersonal: $('#proveedor-selector > option:selected').val(),
                panelesActualizados: localStorage.getItem('panelesActualizados')
            };
            $.ajax({
                type: "POST",
                data: data,
                //async: false,
                url: __AJAX_PATH__ + 'preinscripcion/actualiza-preinscripcion'

            }).done(function (response) {
                if (response.result === 'OK') {
                    localStorage.removeItem('panelesActualizados');
                    $("#msg").text('Pre-Inscripción actualizada con exito');
                    $(".close").remove();
                    $("#btnAceptar").click(function () {
                        location.href = __AJAX_PATH__ + 'login';
                    });
                    $("#ask-pass-alert").modal({backdrop: 'static', keyboard: false});
                    $('.btn-checkTimeLineStatus').prop("disabled", true);
                } else if (response.result === 'NOK') {
                    $("#msg").text('Ocurrio un error');
                    $("#ask-pass-alert").modal();
                }
            });
        }
    });

    $('#btn-unlock-preinscripcion').on('click', function (e) {
        e.preventDefault();

        $('#btn-unlock-form-cancel').on('click', function (e) {
            e.preventDefault();
            $('#modal-unlock-form').modal('toggle');
        });

        $('#btn-unlock-form').on('click', function (e) {
            e.preventDefault();
            let idDatoPersonal = $('#proveedor-selector > option:selected').val();
            $.ajax({
                type: "POST",
                data: {idDatoPersonal: idDatoPersonal},
                url: __AJAX_PATH__ + 'preinscripcion/backup-datos-preinscripcion'
            }).done(function (response) {
                if (response.result === 'OK') {

                    window.location.assign(__AJAX_PATH__ + "preinscripcion/formulario/" + idDatoPersonal + '/unlock');
                } else if (response.result === 'NOK') {
                    $("#msg").text('Ocurrio un error, consulte con el administrador');
                    $("#ask-pass-alert").modal();
                }
            });

        });
        $('#modal-unlock-form').modal({backdrop: 'static', keyboard: false});
    });

    $('#btn-cancelarActualzacion').on('click', function (e) {

        e.preventDefault();

        var data = {idDatoPersonal: $('#proveedor-selector > option:selected').val()};

        $.ajax({
            type: "POST",
            data: data,
            url: __AJAX_PATH__ + 'preinscripcion/cancelar-modificacion-preinscripcion'

        }).done(function (response) {
            if (response.result === 'OK') {
                $("#msg").text('Se ha cancelado la modificacion. Sus datos han sido restablecidos a su estado anterior.');
                $(".close").remove();
                $("#ask-pass-alert").modal({backdrop: 'static', keyboard: false});
                $("#btnAceptar").on('click', function () {
                    window.location.assign(__AJAX_PATH__ + "preinscripcion/formulario/" + data.idDatoPersonal);
                })
            } else if (response.result === 'NOK') {
                $("#msg").text('Ocurrio un error restaurando sus datos, consulte con el administrador');
                $("#ask-pass-alert").modal();
            }
        });
    });

    $('#btn-agregar-proveedor').on('click', function (e) {
        e.preventDefault();
        validarFormAgregaProveedor();
    });

    $('#btn-plus-proveedor').on('click', function (e) {

        $('#modal-agregar-proveedor').modal();

        $('#cuit_agregar_proveedor').inputmask({
            mask: "99-99999999-9",
            placeholder: "_"
        });
        $('#cuit').focus();
    });

    $('#proveedor-selector').on('change', function (e) {
        window.location.assign(__AJAX_PATH__ + "preinscripcion/formulario/" + $(this).val());
    })
});

function personaFisica(e) {
    tipoProveedor(e, function (res) {
        if (res.sts === 200) {

            if ($('#div-for-check-timeline-default').length > 0) {
                $('#timeline-default-tipo-proveedor').empty();
                $('#timeline-default-tipo-proveedor').append(
                    '<div id="timeline_persona_fisica_div"><div class="timeline-circulo bg-tomate" id="timeline_persona_fisica"></div><h5>Datos Persona Física</h5></div></div>' +
                    '<div hidden id ="timeline_persona_juridica_div"><div class="timeline-circulo bg-tomate" id="timeline_persona_juridica"></div><h5>Datos Persona Jurídica</h5></div></div>' +
                    '<div hidden id ="timeline_contratos_ute_div"><div class="timeline-circulo bg-tomate" id="timeline_contratos_ute"></div><h5>Datos Contratos de colaboración</h5></div></div>' +
                    '<div hidden id ="timeline_persona_fisica_extranjera_div"><div class="timeline-circulo bg-tomate" id="timeline_persona_fisica_extranjera"></div><h5>Datos Persona Física extranjera</h5></div></div>' +
                    '<div hidden id ="timeline_persona_juridica_extranjera_div"><div class="timeline-circulo bg-tomate" id="timeline_persona_juridica_extranjera"></div><h5>Datos Persona Jurídica extranjera</h5></div></div>'
                )
            }

            if ($('#div-for-check-timeline-default-domicilio').length > 0) {

                $('#timeline-default-domicilio-proveedor').empty();
                $('#timeline-default-domicilio-proveedor').append(
                    '<div  id="timeline_domicilio_real_div"><div class="timeline-circulo bg-tomate" id="timeline_domicilio_real"></div><h5>Domicilio Real</h5></div>' +
                    '<div  hidden id="timeline_domicilio_legal_div"><div class="timeline-circulo bg-tomate" id="timeline_domicilio_legal"></div><h5>Domicilio Legal</h5></div>' +
                    '<div  hidden ><div class="timeline-circulo  bg-tomate"  id="timeline_domicilio_exterior"></div><h5>Domicilio Exterior</h5></div>'
                )


            }

            $('#registro_etapas').show();
            $('#panel-persona-fisica').show();
            $('#panel-datos-ute').hide();
            $('#panel-dom-legal').hide();
            $('#panel-dom-real').show();
            $("#panel-persona-juridica-extranjera").hide();
            $('#timeline_persona_fisica_div').show();
            $('#timeline_persona_juridica_div').hide();
            $('#timeline_contratos_ute_div').hide();
            $('#timeline_domicilio_real_div').show();
            $('#timeline_domicilio_legal_div').hide();
            $('#docDdjj').attr('href', 'documentos/FO-DDJJ202-17-PF.pdf')
        } else {
            $("#msg").text('Se ha producido un error obteniendo los datos del proveedor, por favor, intente nuevamente');
            $("#ask-pass-alert").modal();
            $(e).prop('checked', false);
        }
    });
}

function personaJuridica(e) {

    tipoProveedor(e, function (res) {
        if (res.sts === 200) {


            if ($('#div-for-check-timeline-default').length > 0) {
                $('#timeline-default-tipo-proveedor').empty();
                $('#timeline-default-tipo-proveedor').append(
                    '<div hidden id="timeline_persona_fisica_div"><div class="timeline-circulo bg-tomate" id="timeline_persona_fisica"></div><h5>Datos Persona Física</h5></div></div>' +
                    '<div  id ="timeline_persona_juridica_div"><div class="timeline-circulo bg-tomate" id="timeline_persona_juridica"></div><h5>Datos Persona Jurídica</h5></div></div>' +
                    '<div hidden id ="timeline_contratos_ute_div"><div class="timeline-circulo bg-tomate" id="timeline_contratos_ute"></div><h5>Datos Contratos de colaboración</h5></div></div>' +
                    '<div hidden id ="timeline_persona_fisica_extranjera_div"><div class="timeline-circulo bg-tomate" id="timeline_persona_fisica_extranjera"></div><h5>Datos Persona Física extranjera</h5></div></div>' +
                    '<div hidden id ="timeline_persona_juridica_extranjera_div"><div class="timeline-circulo bg-tomate" id="timeline_persona_juridica_extranjera"></div><h5>Datos Persona Jurídica extranjera</h5></div></div>'
                )
            }


            if ($('#div-for-check-timeline-default-domicilio').length > 0) {

                $('#timeline-default-domicilio-proveedor').empty();
                $('#timeline-default-domicilio-proveedor').append(
                    '<div  hidden id="timeline_domicilio_real_div"><div class="timeline-circulo bg-tomate" id="timeline_domicilio_real"></div><h5>Domicilio Real</h5></div>' +
                    '<div  id="timeline_domicilio_legal_div"><div class="timeline-circulo bg-tomate" id="timeline_domicilio_legal"></div><h5>Domicilio Legal</h5></div>' +
                    '<div  hidden ><div class="timeline-circulo  bg-tomate"  id="timeline_domicilio_exterior"></div><h5>Domicilio Exterior</h5></div>'
                )


            }

            $('#registro_etapas').show();
            $('#panel-persona-fisica').hide();
            $('#panel-datos-ute').hide();
            $('#panel-dom-legal').show();
            $('#panel-dom-real').hide();
            $("#panel-persona-juridica-extranjera").show();
            $('#timeline_persona_fisica_div').hide();
            $('#timeline_persona_juridica_div').show();
            $('#timeline_contratos_ute_div').hide();
            $('#timeline_domicilio_real_div').hide();
            $('#timeline_domicilio_legal_div').show();
            $('#docDdjj').attr('href', 'documentos/FO-DDJJ202-17-PJ.pdf')
        } else {
            $(e).prop('checked', false);
            $("#msg").text('Se ha producido un error obteniendo los datos del proveedor, por favor, intente nuevamente');
            $("#ask-pass-alert").modal();
        }
    });
}

function personaContratos(e) {

    tipoProveedor(e, function (res) {
        if (res.sts === 200) {


            if ($('#div-for-check-timeline-default').length > 0) {
                $('#timeline-default-tipo-proveedor').empty();
                $('#timeline-default-tipo-proveedor').append(
                    '<div hidden id="timeline_persona_fisica_div"><div class="timeline-circulo bg-tomate" id="timeline_persona_fisica"></div><h5>Datos Persona Física</h5></div></div>' +
                    '<div hidden id ="timeline_persona_juridica_div"><div class="timeline-circulo bg-tomate" id="timeline_persona_juridica"></div><h5>Datos Persona Jurídica</h5></div></div>' +
                    '<div id ="timeline_contratos_ute_div"><div class="timeline-circulo bg-tomate" id="timeline_contratos_ute"></div><h5>Datos Contratos de colaboración</h5></div></div>' +
                    '<div hidden id ="timeline_persona_fisica_extranjera_div"><div class="timeline-circulo bg-tomate" id="timeline_persona_fisica_extranjera"></div><h5>Datos Persona Física extranjera</h5></div></div>' +
                    '<div hidden id ="timeline_persona_juridica_extranjera_div"><div class="timeline-circulo bg-tomate" id="timeline_persona_juridica_extranjera"></div><h5>Datos Persona Jurídica extranjera</h5></div></div>'
                )
            }

            if ($('#div-for-check-timeline-default-domicilio').length > 0) {

                $('#timeline-default-domicilio-proveedor').empty();
                $('#timeline-default-domicilio-proveedor').append(
                    '<div  hidden id="timeline_domicilio_real_div"><div class="timeline-circulo bg-tomate" id="timeline_domicilio_real"></div><h5>Domicilio Real</h5></div>' +
                    '<div  id="timeline_domicilio_legal_div"><div class="timeline-circulo bg-tomate" id="timeline_domicilio_legal"></div><h5>Domicilio Legal</h5></div>' +
                    '<div  hidden ><div class="timeline-circulo  bg-tomate"  id="timeline_domicilio_exterior"></div><h5>Domicilio Exterior</h5></div>'
                )


            }

            $('#panel-datos-ute').show();
            $('#registro_etapas').show();
            $('#panel-persona-fisica').hide();
            $('#panel-dom-legal').show();
            $('#panel-dom-real').hide();
            $("#panel-persona-juridica-extranjera").hide();
            $('#timeline_persona_fisica_div').hide();
            $('#timeline_persona_juridica_div').hide();
            $('#timeline_contratos_ute_div').show();
            $('#timeline_domicilio_real_div').hide();
            $('#timeline_domicilio_legal_div').show();
            $('#docDdjj').attr('href', 'documentos/FO-DDJJ202-17-PJ.pdf')
        } else {
            $(e).prop('checked', false);
            $("#msg").text('Se ha producido un error obteniendo los datos del proveedor, por favor, intente nuevamente');
            $("#ask-pass-alert").modal();
        }
    });

}

function personaFisicaExtranjeraNoResidente(e) {

    tipoProveedor(e, function (res) {
        if (res.sts === 200) {

            if ($('#div-for-check-timeline-default').length > 0) {
                $('#timeline-default-tipo-proveedor').empty();
                $('#timeline-default-tipo-proveedor').append(
                    '<div hidden id="timeline_persona_fisica_div"><div class="timeline-circulo bg-tomate" id="timeline_persona_fisica"></div><h5>Datos Persona Física</h5></div></div>' +
                    '<div hidden id ="timeline_persona_juridica_div"><div class="timeline-circulo bg-tomate" id="timeline_persona_juridica"></div><h5>Datos Persona Jurídica</h5></div></div>' +
                    '<div hidden id ="timeline_contratos_ute_div"><div class="timeline-circulo bg-tomate" id="timeline_contratos_ute"></div><h5>Datos Contratos de colaboración</h5></div></div>' +
                    '<div  id ="timeline_persona_fisica_extranjera_div"><div class="timeline-circulo bg-tomate" id="timeline_persona_fisica_extranjera"></div><h5>Datos Persona Física extranjera</h5></div></div>' +
                    '<div hidden id ="timeline_persona_juridica_extranjera_div"><div class="timeline-circulo bg-tomate" id="timeline_persona_juridica_extranjera"></div><h5>Datos Persona Jurídica extranjera</h5></div></div>'
                )
            }

            if ($('#div-for-check-timeline-default-domicilio').length > 0) {

                $('#timeline-default-domicilio-proveedor').empty();
                $('#timeline-default-domicilio-proveedor').append(
                    '<div  hidden id="timeline_domicilio_real_div"><div class="timeline-circulo bg-tomate" id="timeline_domicilio_real"></div><h5>Domicilio Real</h5></div>' +
                    '<div  hidden id="timeline_domicilio_legal_div"><div class="timeline-circulo bg-tomate" id="timeline_domicilio_legal"></div><h5>Domicilio Legal</h5></div>' +
                    '<div><div class="timeline-circulo  bg-tomate"  id="timeline_domicilio_exterior"></div><h5>Domicilio Exterior</h5></div>'
                )


            }


            $('#registro_etapas').show();
            $('#panel-persona-juridica-extranjera').hide();
            $('#panel-persona-fisica-extranjera').show();
            $('#timeline_persona_juridica_extranjera_div').hide();
            $('#timeline_persona_fisica_extranjera_div').show();
        } else {
            $(e).prop('checked', false);
            $("#msg").text('Se ha producido un error obteniendo los datos del proveedor, por favor, intente nuevamente');
            $("#ask-pass-alert").modal();
        }
    });

}

function personaJuridicaExtranjera(e) {

    tipoProveedor(e, function (res) {
        if (res.sts === 200) {


            if ($('#div-for-check-timeline-default').length > 0) {
                $('#timeline-default-tipo-proveedor').empty();
                $('#timeline-default-tipo-proveedor').append(
                    '<div hidden id="timeline_persona_fisica_div"><div class="timeline-circulo bg-tomate" id="timeline_persona_fisica"></div><h5>Datos Persona Física</h5></div></div>' +
                    '<div hidden id ="timeline_persona_juridica_div"><div class="timeline-circulo bg-tomate" id="timeline_persona_juridica"></div><h5>Datos Persona Jurídica</h5></div></div>' +
                    '<div hidden id ="timeline_contratos_ute_div"><div class="timeline-circulo bg-tomate" id="timeline_contratos_ute"></div><h5>Datos Contratos de colaboración</h5></div></div>' +
                    '<div hidden id ="timeline_persona_fisica_extranjera_div"><div class="timeline-circulo bg-tomate" id="timeline_persona_fisica_extranjera"></div><h5>Datos Persona Física extranjera</h5></div></div>' +
                    '<div id ="timeline_persona_juridica_extranjera_div"><div class="timeline-circulo bg-tomate" id="timeline_persona_juridica_extranjera"></div><h5>Datos Persona Jurídica extranjera</h5></div></div>'
                )
            }

            if ($('#div-for-check-timeline-default-domicilio').length > 0) {

                $('#timeline-default-domicilio-proveedor').empty();
                $('#timeline-default-domicilio-proveedor').append(
                    '<div  hidden id="timeline_domicilio_real_div"><div class="timeline-circulo bg-tomate" id="timeline_domicilio_real"></div><h5>Domicilio Real</h5></div>' +
                    '<div  hidden id="timeline_domicilio_legal_div"><div class="timeline-circulo bg-tomate" id="timeline_domicilio_legal"></div><h5>Domicilio Legal</h5></div>' +
                    '<div><div class="timeline-circulo  bg-tomate"  id="timeline_domicilio_exterior"></div><h5>Domicilio Exterior</h5></div>'
                )


            }

            $('#registro_etapas').show();
            $('#panel-persona-juridica-extranjera').show();
            $('#panel-persona-fisica-extranjera').hide();
            $('#timeline_persona_juridica_extranjera_div').show();
            $('#timeline_persona_fisica_extranjera_div').hide();
        } else {
            $(e).prop('checked', false);
            $("#msg").text('Se ha producido un error obteniendo los datos del proveedor, por favor, intente nuevamente');
            $("#ask-pass-alert").modal();
        }
    });

}

function saveDoc(nombrepanel, nombreinput, callback) {

    if (typeof(callback) !== 'function') {
        callback = function () {
        };
    }

    var url = __AJAX_PATH__ + "formulariopreinscripcion/subirdocumentacion/" + nombrepanel;
    var inputFile = document.getElementById(nombreinput);

    var file = inputFile.files[0];
    var data = new FormData();
    data.append('doc', file, file.name);
    data.append('idDatoPersonal', $('#proveedor-selector > option:selected').val());
    $.ajax({
        url: url,
        method: "POST",
        data: data,
        processData: false,
        contentType: false,
        cache: false
    }).done(callback).fail(function (xhr, status, errorThrown) {
        if (xhr.status === 403) {
            location.reload();
        }
    });


}

function removeDoc(id, callback) {
    var url = __AJAX_PATH__ + "formulariopreinscripcion/eliminardocumento/" + id;
    $.ajax({
        url: url,
        method: "POST"
    }).done(callback).fail(function (xhr, status, errorThrown) {
        if (xhr.status === 403) {
            location.reload();
        }
    });
}

function quitarInputDocumento(inputid) {
    $(inputid).remove();
}

function tipoProveedor(e, callback) {

    var url = __AJAX_PATH__ + "formulariopreinscripcion/modificartipoproveedor";

    $.ajax(
        {
            url: url,
            method: 'POST',
            data: {
                denom: e.value,
                idDatoPersonal: $('#proveedor-selector > option:selected').val()
            }
        }
    ).done(callback).fail(function (xhr, status, errorThrown) {
        if (xhr.status === 403) {
            $("#msg").text('La sesión ha caducado, por favor vuelva a ingresar al formulario');
            $("#ask-pass-alert").modal();
            location.reload();
        }

    })

}

function changeTimeLineStep(panel, newColor, oldColor) {
    if(oldColor === 'bg-tomate' && newColor === 'bg-verdin') {
        // panelesActualizados = JSON.parse(localStorage.getItem('panelesActualizados'));
        panelesActualizados.push({idDatoPersonal: $('#proveedor-selector > option:selected').val(), panel: panel.substring(1)});
        localStorage.setItem('panelesActualizados', JSON.stringify(panelesActualizados));
    }

    $(panel).addClass(newColor).removeClass(oldColor);
    var timeLineStatus = checkTimeLineStatus();
    if (timeLineStatus === 0) {
        $('.btn-checkTimeLineStatus').prop("disabled", false);
    }
}

function checkTimeLineStatus(){
    var cont = 0;
    $('.timeline-circulo').each(function(){
        if ($(this).hasClass('bg-tomate') && !$(this).parent().prop('hidden')){
            cont++;
        }
    });
    return cont;
}

function addRules(rulesObj) {
    for (var item in rulesObj) {
        console.log($('#'+item));
        $('#' + item).rules('add', rulesObj[item]);
    }
}

function removeRules(rulesObj) {
    for (var item in rulesObj) {
        $('#' + item).rules('remove');
    }
}

function proveedorExtranjero(obj) {

    let form = $('#loginForm');

    if ($('.fg-website').is(":visible")) { //proveedor nacional
        form.trigger('reset');
        $('.fg-website').hide();
        console.log('DEBUG-1');
        // removeRules(proveedorExtanjeroRules);
        console.log('DEBUG-2');
        // addRules(proveedorNacionalRules);
        console.log('DEBUG-3');

        $('#cuit').inputmask({
            mask: "99-99999999-9",
            placeholder: "_"
        });
        $('.fg-identificacion-tributaria').hide();
        $('.fg-cuit').show();
        $(obj).addClass('active');
    } else { //proveedor extranjero
        form.trigger('reset');
        $('.fg-website').show();
        console.log('DEBUG-1');
        // removeRules(proveedorNacionalRules);
        console.log('DEBUG-2');
        // addRules(proveedorExtanjeroRules);
        console.log('DEBUG-3');
        $('cuit').inputmask('remove');
        $('.fg-identificacion-tributaria').show();
        $('.fg-cuit').hide();
        $(obj).removeClass('active');
    }
}

function validarFormAgregaProveedor(obj) {
    let formAgregaProveedor = $('#agregarProveedorForm');
    formAgregaProveedor.validate({
        rules: {
            'cuit': {
                required: true,
                cuit: true
            }
        }
    });

    if (formAgregaProveedor.valid()) {
        var url = $('#agregarProveedorForm').attr('action');
        var data = $('#agregarProveedorForm').serializeArray();
        var extranjero = $("#extranjero").hasClass("active") ? true : false;
        data.push({
            name: 'extranjero',
            value: extranjero
        });
        $.ajax({
            url: url,
            method: 'POST',
            data: data,
            beforeSend: function() {
                $('.loading').show();
            }
        }).done(function(response) {
            switch (response.sts) {
                case 200:
                    $('#modal-agregar-proveedor').modal('hide');
                    $("#msg").html(response.msg);
                    $("#ask-pass-alert").modal();
                    //al apretar ok renderizamos el form con el id dato personal
                    $("#btnAceptar").on('click', function() {
                        window.location.assign(__AJAX_PATH__ + "preinscripcion/formulario/"+response.idDatoPersonal);
                    });
                    break;
                case 202:
                    $('#modal-agregar-proveedor').modal('hide');
                    $("#msg").html(response.msg);
                    $("#ask-pass-alert").modal();
                    break;
                default:
            }
        }).fail(function(xhr, status, errorThrown) {
            $("#msg").html('<strong>ERROR:</strong> Consulte al administrador');
            $("#ask-pass-alert").modal();
            console.log("Error: " + errorThrown);
            console.log("Status: " + status);
            console.dir(xhr);
        }).always(function(xhr, status, errorThrown) {
            $('.loading').hide();
        });
    }
}