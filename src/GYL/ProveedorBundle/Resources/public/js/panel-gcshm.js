jQuery(document).ready(function () {

    $(document).on("submit", "#form-datos-gcshm", function (e) {
        e.preventDefault();
        return false;
    });

    // Al cargar la pagina checkeo el estado de los checkbox.
    if ($('#iso9001btnGcshm').attr('aria-pressed') === 'false') {
        var form = $('#form-group-for-append-gcshm-ISO9001');
        
        disableArchivo(form);
    }
    
    if ($('#osha18001btnGcshm').attr('aria-pressed') === 'false') {
        var form = $('#form-group-for-append-gcshm-OSHA18001');
        
        disableArchivo(form);
    }
    
    if ($('#iso14001btnGcshm').attr('aria-pressed') === 'false') {
        var form = $('#form-group-for-append-gcshm-ISO14001');
        
        disableArchivo(form);
    }
    
    // En el click sobre el checkbox enableo/disableo los archivos.
    $('#iso9001btnGcshm').click(function(){
        var form = $('#form-group-for-append-gcshm-ISO9001');
        
        if($('#iso9001btnGcshm').attr('aria-pressed') === 'false'){
            enableArchivo(form);

        }else{
            $("#inputDocGcshmISO9001").val(null);
            $(".row .iso9001").empty();
            $("[id^=savedGcshmDocISO9001]").each(function () {
                quitarDocumentoGcshmISO9001($(this).attr('id-doc'));
            });
            disableArchivo(form);

        }
    });
    
    $('#osha18001btnGcshm').click(function(){
        var form = $('#form-group-for-append-gcshm-OSHA18001');
        
        if($('#osha18001btnGcshm').attr('aria-pressed') === 'false'){
            enableArchivo(form);
        }else{
            $("#inputDocGcshmOSHA18001").val(null);
            $(".row .osha18001").empty();
            $("[id^=savedGcshmDocOSHA18001]").each(function () {
                quitarDocumentoGcshmOSHA18001($(this).attr('id-doc'));
            });
            disableArchivo(form);
        }
    });
    
    $('#iso14001btnGcshm').click(function(){
        var form = $('#form-group-for-append-gcshm-ISO14001');
        
        if($('#iso14001btnGcshm').attr('aria-pressed') === 'false'){
            enableArchivo(form);
        }else{
            $("#inputDocGcshmISO14001").val(null);
            $(".row .iso14001").empty();
            $("[id^=savedGcshmDocISO14001]").each(function () {
                quitarDocumentoGcshmISO14001($(this).attr('id-doc'));
            });
            disableArchivo(form);
        }
    });


    $('#submitGchsm').on('click', function () {


        var isTrueSet9001 = ($('#iso9001btnGcshm').attr('aria-pressed') === 'true');
        if(isTrueSet9001 && $(".iso9001").length==0){
            $("#msg").text('Debe adjuntar Certificación ISO9001 o PGC.');
            $("#ask-pass-alert").modal();
            return false;
        }

        $('#form_certificacion_iso9001').val(isTrueSet9001);

        var isTrueSet14001 = ($('#iso14001btnGcshm').attr('aria-pressed') === 'true');
        if(isTrueSet14001 && $(".iso14001").length==0){
            $("#msg").text('Debe adjuntar Certificación ISO14001 o PGA.');
            $("#ask-pass-alert").modal();
            return false;
        }

        $('#form_certificacion_iso14001').val(isTrueSet14001);

        var isTrueSet18001 = ($('#osha18001btnGcshm').attr('aria-pressed') === 'true');
        if(isTrueSet18001 && $(".osha18001").length==0){
            $("#msg").text('Debe adjuntar Certificación OSHA 18001 o PDS.');
            $("#ask-pass-alert").modal();
            return false;
        }
        
        $('#form_certificacion_osha18001').val(isTrueSet18001);

        var url = __AJAX_PATH__ + "formulariopreinscripcion/agregargcshm";

        //agrego el id-dato-personal (value del combobox proveedores) al form
        let formGchsm  = $('#form-datos-gcshm');
        let idDatoPersonal = $('#proveedor-selector > option:selected').val();
        formGchsm.append('<input type="hidden" name="form[id_dato_personal]" value='+idDatoPersonal+'>');
        var formData = formGchsm.serialize();

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
                changeTimeLineStep('#timeline_gcshm', 'bg-verdin', 'bg-tomate');
            } else {
            }
        }).fail(function (xhr, status, errorThrown) {
            if (xhr.status === 403) {
                location.reload();
            }
        })


    })

    $('#inputDocGcshm').on('change', function (e) {
        var $label = $('#inputFileNameGcshm');
        var fileName = '';

        if (this.files[0]) {
            var form_appended = $('#form-group-for-append-gcshm');
            saveDoc('proveedor_gcshm', 'inputDocGcshm', function (response) {
                if (response.sts === 200) {
                    form_appended.append('<div class="row" id="savedGcshmDoc' + response.data.id + '" id-doc="'+ response.data.id + '"><input style="width: 20em" type="text" class="form-control adjunto float-l" placeholder="documento.pdf" aria-describedby="archivo-ok"  value="' + response.data['nombreoriginal'] + '" disabled><i class="fa fa-times fa-2 float-l" aria-hidden="true" onclick="quitarDocumentoGcshm(' + response.data.id + ')" style="cursor: pointer;"></i></div>');
                    $('#inputDocGcshm').val('');
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

    $('#inputDocGcshmISO9001').on('change', function (e) {
        var $label = $('#inputFileNameGcshmISO9001');
        var fileName = '';

        if (this.files[0]) {
            var form_appended = $('#form-group-for-append-gcshm-ISO9001');
            saveDoc('proveedor_gcshm_iso9001', 'inputDocGcshmISO9001', function (response) {
                if (response.sts === 200) {
                    form_appended.append('<div class="row iso9001" id="savedGcshmDocISO9001' + response.data.id + '" id-doc="'+ response.data.id + '"><input style="width: 20em" type="text" class="form-control adjunto float-l" placeholder="documento.pdf" aria-describedby="archivo-ok"  value="' + response.data['nombreoriginal'] + '" disabled><i class="fa fa-times fa-2 float-l" aria-hidden="true" onclick="quitarDocumentoGcshmISO9001(' + response.data.id + ')" style="cursor: pointer;"></i></div>');
                    $('#inputDocGcshmISO9001').val('');
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

    $('#inputDocGcshmISO14001').on('change', function (e) {
        var $label = $('#inputFileNameGcshmISO14001');
        var fileName = '';

        if (this.files[0]) {
            var form_appended = $('#form-group-for-append-gcshm-ISO14001');
            saveDoc('proveedor_gcshm_iso14001', 'inputDocGcshmISO14001', function (response) {
                if (response.sts === 200) {
                    form_appended.append('<div class="row iso14001" id="savedGcshmDocISO14001' + response.data.id + '" id-doc="'+ response.data.id + '"><input style="width: 20em" type="text" class="form-control adjunto float-l" placeholder="documento.pdf" aria-describedby="archivo-ok"  value="' + response.data['nombreoriginal'] + '" disabled><i class="fa fa-times fa-2 float-l" aria-hidden="true" onclick="quitarDocumentoGcshmISO14001(' + response.data.id + ')" style="cursor: pointer;"></i></div>');
                    $('#inputDocGcshmISO14001').val('');
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

    $('#inputDocGcshmOSHA18001').on('change', function (e) {
        var $label = $('#inputFileNameGcshmOSHA18001');
        var fileName = '';

        if (this.files[0]) {
            var form_appended = $('#form-group-for-append-gcshm-OSHA18001');
            saveDoc('proveedor_gcshm_osha18001', 'inputDocGcshmOSHA18001', function (response) {
                if (response.sts === 200) {
                    form_appended.append('<div class="row osha18001" id="savedGcshmDocOSHA18001' + response.data.id + '" id-doc="'+ response.data.id +'"><input style="width: 20em" type="text" class="form-control adjunto float-l" placeholder="documento.pdf" aria-describedby="archivo-ok"  value="' + response.data['nombreoriginal'] + '" disabled><i class="fa fa-times fa-2 float-l" aria-hidden="true" onclick="quitarDocumentoGcshmOSHA18001(' + response.data.id + ')" style="cursor: pointer;"></i></div>');
                    $('#inputDocGcshmOSHA18001').val('');
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

function enableArchivo(form){
    form.find('input').attr('disabled', false);
    form.find('label').removeClass('disabled');
    form.find('label').css('cursor', 'pointer');
}

function disableArchivo(form){
    form.find('input').attr('disabled', true);
    form.find('label').addClass('disabled');
    form.find('label').css('cursor', 'not-allowed');
}

function quitarDocumentoGcshm(id) {
    removeDoc(id, function (e) {
        if (e.sts === 200) {
            $("#msg").text('Se ha quitado el documento');
            $("#ask-pass-alert").modal();
            $('#savedGcshmDoc' + id).remove();
        }
    })
}

function quitarDocumentoGcshmISO9001(id) {
    removeDoc(id, function (e) {
        if (e.sts === 200) {
            $("#msg").text('Se ha quitado el documento');
            $("#ask-pass-alert").modal();
            $('#savedGcshmDocISO9001' + id).remove();
        }
    })
}

function quitarDocumentoGcshmISO14001(id) {
    removeDoc(id, function (e) {
        if (e.sts === 200) {
            $("#msg").text('Se ha quitado el documento');
            $("#ask-pass-alert").modal();
            $('#savedGcshmDocISO14001' + id).remove();
        }
    })
}

function quitarDocumentoGcshmOSHA18001(id) {
    removeDoc(id, function (e) {
        if (e.sts === 200) {
            $("#msg").text('Se ha quitado el documento');
            $("#ask-pass-alert").modal();
            $('#savedGcshmDocOSHA18001' + id).remove();
        }
    })
}