jQuery(document).ready(function () {

    $(document).on("submit", "#cod-actividad", function (e) {
        e.preventDefault();
        return false;
    });

    $('#buscar-actividades-btn').on('click', function (e) {

        var codigo = $('#cod-clae').val();

        if (codigo.length === 0) {
            $("#actMsg").text('Ingrese el codigo de actividad').addClass('text-danger unread');
            //$("#ask-pass-alert").modal();
            return false;
        }

        var url = __AJAX_PATH__ + "formulariopreinscripcion/getactividades/" + codigo;
        $.ajax(
            {
                url: url,
                method: 'GET'
            }
        ).done(function (response) {
            if (response.sts === 200) {
                if (response.data.length === 0) {
                    $("#actMsg").text('No se encontraron resultados').addClass('text-danger unread');
                    $('.actividades-search-row').remove();
                } else {
                    $('.actividades-search-row').remove();
                    $("#actMsg").text('Se encontraron ' + response.data.length + ' resultados').removeClass('text-danger unread');
                    $.each(response.data, function (index, actividad) {
                        $('#lista-de-actividades').append('<tr id="actividad-row-' + actividad.id + '" class="actividades-search-row"><td hidden>' + codigo + '</td><td hidden>' + actividad.id + '</td><td>' + actividad.denominacion + '</td><td>' + actividad.denominacion_seccion + '</td><td>' + actividad.denominacion_grupo + '</td><td><button onclick="agregarActividad(' + actividad.id + ')" class="btn btn-secondary">Agregar</button></td></tr>');
                    })
                }
            } else {
                $("#actMsg").text('No se encontraron resultados');
            }
        }).fail(function (xhr, status, errorThrown) {
            if (xhr.status === 403) {
                location.reload();
            }
        })
    });


    $('#guardarBorradorActividades').on('click', function (e) {
        if($("#tabla-actividad > tbody > tr").length > 0){
            $("#msg").text('Datos guardados en el borrador');
        }else{
            $("#msg").text('Debe agregar una actividad');
        }
        $("#ask-pass-alert").modal();
    })  
})
;

function quitarActividad(id) {
    var url = __AJAX_PATH__ + "formulariopreinscripcion/quitaractividad";
    var actividades = false;

    if ($("#tabla-actividad > tbody > tr").length > 1) {
        actividades = true;
    } else {
        changeTimeLineStep('#timeline_actividades', 'bg-tomate', 'bg-verdin');
    }

    $.ajax(
        {
            url: url,
            method: 'POST',
            data: {
                id: id,
                actividades: actividades,
                idDatoPersonal: $('#proveedor-selector > option:selected').val()
            }
        }
    ).done(function (response) {
        if (response.sts === 200) {
            $('#actividad-row-' + id).remove();
            var rowCount = $('#tabla-actividad tr').length;
            if (rowCount < 2) {
                changeTimeLineStep('#timeline_actividades', 'bg-tomate', 'bg-verdin');
            }
            $("#msg").text('Actividad eliminada correctamente');
            $("#ask-pass-alert").modal();
        } else {
            $("#msg").text('Error inténtelo nuevamente');
            $("#ask-pass-alert").modal();
        }
    });
}

function agregarActividad(id) {

    var url = __AJAX_PATH__ + "formulariopreinscripcion/agregaractividad";
    $.ajax(
        {
            url: url,
            method: 'POST',
            data: {
                id: id,
                idDatoPersonal: $('#proveedor-selector > option:selected').val()
            }
        }
    ).done(function (response) {
        if (response.sts === 200) {
            var rowHtml = $('#actividad-row-' + id).html();
            rowHtml = rowHtml.replace("btn btn-secondary", "glyphicon glyphicon-remove");
            rowHtml = rowHtml.replace("button", "a");
            rowHtml = rowHtml.replace("Agregar", "");
            rowHtml = rowHtml.split(id).join(response.data);
            $('#tabla-actividad').append('<tr id="actividad-row-' + response.data + '">' + rowHtml.replace("agregarActividad", "quitarActividad") + '</tr>');
            $("#msg").text('Actividad agregada correctamente');
            $("#ask-pass-alert").modal();
            changeTimeLineStep('#timeline_actividades', 'bg-verdin', 'bg-tomate');
        } else if (response.sts === 304) {
            $("#msg").text('Esta actividad ya ha sido agregada');
            $("#ask-pass-alert").modal();
        } else {
            $("#msg").text('Error inténtelo nuevamente');
            $("#ask-pass-alert").modal();
        }
        $('#agrega-actividad').modal('toggle');
    });


}