jQuery(document).ready(function () {


    $("#form_acepta").change(function () {
        var url = __AJAX_PATH__ + "formulariopreinscripcion/declaracionjurada";

        //agrego el id-dato-personal (value del combobox proveedores) al form
        let formDDJJ  = $('#formDDJJ');
        let idDatoPersonal = $('#proveedor-selector > option:selected').val();
        formDDJJ.append('<input type="hidden" name="form[id_dato_personal]" value='+idDatoPersonal+'>');
        var formData = formDDJJ.serialize();

        $.ajax(
            {
                url: url,
                method: 'POST',
                data: formData
            }
        ).done(function (response) {
            if (response.sts === 200) {
                if($("#form_acepta").is(':checked')) {  
                    $("#msg").text('Datos guardados en el borrador');
                    $("#ask-pass-alert").modal();
                    changeTimeLineStep('#timeline_ddjj', 'bg-verdin', 'bg-tomate');
                }else{
                    changeTimeLineStep('#timeline_ddjj', 'bg-tomate', 'bg-verdin');
                }
            } else {
                $("#msg").text('Ha ocurrido un error al guardar la declaracion intente nuevamente');
                $("#ask-pass-alert").modal();
            }
        }).fail(function (xhr, status, errorThrown) {
            if (xhr.status === 403) {
                location.reload();
            }
        })

    });


});

