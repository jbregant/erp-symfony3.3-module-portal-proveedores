jQuery(document).ready(function () {

    $(document).on("submit", "#contactoForm", function (e) {
        e.preventDefault();
        return false;
    });

    $('#contactoFormularioSubmit').on('click', function (e) {

        var flagEdit = $(this).attr("editar");

        // Flag para editar.
        if (flagEdit === 'false') {
            var url = __AJAX_PATH__ + "formulariopreinscripcion/agregarcontacto";
        } else {
            var url = __AJAX_PATH__ + "formulariopreinscripcion/editarcontacto/" + flagEdit;
        }
        // Validacion de campos.
        $('#contactoForm').validate({

            rules: {
                'form[email]': {
                    required: true,
                    email: true
                },
                'form[nombre]': {
                    required: true,
                    nyp: true
                },
                'form[apellido]': {
                    required: true,
                    nyp: true
                },
                'form[telefono]': {
                    required: true,
                    telefono:true
                },
                'form[area]': {
                    required: true,
                    nyp:true
                },
                'form[posicion]': {
                    required: true,
                    nyp:true
                }
            },
            messages: {
                'form[nombre]': {
                    nyp: "Ingrese un nombre válido, numeros y caracteres especiales no admitidos."
                },
                'form[apellido]': {
                    nyp:"Ingrese un apellido válido, numeros y caracteres especiales no admitidos."
                },
                'form[area]': {
                    nyp:"Ingrese un dato válido, numeros y caracteres especiales no admitidos."
                },
                'form[posicion]': {
                    nyp:"Ingrese un dato válido, numeros y caracteres especiales no admitidos."
                },
                'form[telefono]': {
                    telefono: "Ingrese un teléfono válido, Ej: +541158764561."
                }
            }
        });
        // Si el formulario es valido enviouna request al servidor.
        if ($('#contactoForm').valid()) {
            //agrego el id-dato-personal (value del combobox proveedores) al form
            let idDatoPersonal = $('#proveedor-selector > option:selected').val();
            $('#contactoForm').append('<input type="hidden" name="form[id_dato_personal]" value='+idDatoPersonal+'>');
            var formData = $('#contactoForm').serialize();
            $.ajax({
                url: url,
                cache: false,
                method: 'POST',
                data: formData
            }).done(function (response) {

                if (response.sts === 200) {
                    $('#agrega-contacto').modal('toggle');
                    if (response.data.form) {
                        if (flagEdit === 'false') {
                            $('#tabla-datos-contacto').append('<tr id="contacto-row' + response.data.form['id'] + '"><td hidden id="id">' + response.data.form['id'] + '</td><td id="nombre">' + response.data.form['nombre'] + '</td><td id="apellido">' + response.data.form['apellido'] + '</td><td id="area">' + response.data.form['area'] + '</td><td id="posicion">' + response.data.form['posicion'] + '</td><td id="email" style="text-transform: none;">' + response.data.form['email'] + '</td><td id="telefono">' + response.data.form['telefono'] + '</td><td><a onclick="quitarContacto(' + response.data.form['id'] + ')" class="glyphicon glyphicon-remove" id="eliminarContacto"  ></a> <a data-target="#agrega-contacto" data-toggle="modal" id="editarContacto" class="editar glyphicon glyphicon-pencil"></a></td></tr>');
                        } else {
                            $('#tabla-datos-contacto').find('#contacto-row' + response.data.form['id']).html('<td hidden id="id">' + response.data.form['id'] + '</td><td id="nombre">' + response.data.form['nombre'] + '</td><td id="apellido">' + response.data.form['apellido'] + '</td><td id="area">' + response.data.form['area'] + '</td><td id="posicion">' + response.data.form['posicion'] + '</td><td id="email"  style="text-transform: none;">' + response.data.form['email'] + '</td><td id="telefono">' + response.data.form['telefono'] + '</td><td><a onclick="quitarContacto(' + response.data.form['id'] + ')" class="glyphicon glyphicon-remove" id="eliminarContacto" ></a> <a data-target="#agrega-contacto" data-toggle="modal" id="editarContacto" class="editar glyphicon glyphicon-pencil"></a></td></tr>');
                        }

                    }
                }
                changeTimeLineStep('#timeline_dato_contacto', 'bg-verdin', 'bg-tomate');
            }).fail(function (xhr, status, errorThrown) {

                if (xhr.status === 403) {
                    location.reload();
                }
            });
        }


    });

    $('#agregarContacto').on('click', function (e) 
    {
        //Se puede realizar con https://jqueryvalidation.org/Validator.resetForm/ pero no funciona en TODOS los casos.
        //Documentacion: https://stackoverflow.com/questions/2086287/how-to-clear-jquery-validation-error-messages
        //Limpia las validaciones que hayan quedado anteriormente dentro del modal.
        $(".help-block").hide();
        $(".form-group").removeClass("has-error");
        // Cambio los textos del modal
        $('#agrega-contacto h4').html('Agregar Contacto');
        $('#contactoFormularioSubmit').text('Agregar');
        $('#contactoFormularioSubmit').attr("editar", false);
        
        // Reseteo el formulario.
        $('#contactoForm').trigger("reset");

        $('#editarContacto').on('click', function (e) {
        $(".help-block").hide();
        $(".form-group").removeClass("has-error");
    });
    });

    $('#editarContacto').on('click', function (e) {
        $(".help-block").hide();
        $(".form-group").removeClass("has-error");
    });

    

    $('.guardar').on('click', function (e) {

        if ($("#tabla-datos-contacto > tbody > tr").length > 0) {
            var url = __AJAX_PATH__ + "formulariopreinscripcion/guardarcontacto";

            $.ajax({
                url: url,
                method: 'POST',
                data: {'idDatoPersonal': $('#proveedor-selector > option:selected').val()}
            }).done(function (response) {
                if (response.sts === 200) {
                    $("#msg").text('Datos guardados en el borrador');
                    $("#ask-pass-alert").modal();
                    changeTimeLineStep('#timeline_dato_contacto', 'bg-verdin', 'bg-tomate');
                }

            })
        } else {
            $("#msg").text('Ha ocurrido un error al guardar el borrador, agregue al menos un contacto.');
            $("#ask-pass-alert").modal();
        }
    });



});


// Edit para contactos.
$(document).on("click", ".editar", function () {

    // Modifico los textos del modal.
    $('#agrega-contacto h4').html('Editar Contacto');
    $('#contactoFormularioSubmit').text('Editar');

    // Obtengo los datos de la fila.
    var id = $(this).closest("tr").find("#id").text().trim();
    var nombre = $(this).closest("tr").find("#nombre").text().trim();
    var apellido = $(this).closest("tr").find("#apellido").text().trim();
    var area = $(this).closest("tr").find("#area").text().trim();
    var posicion = $(this).closest("tr").find("#posicion").text().trim();
    var email = $(this).closest("tr").find("#email").text().trim();
    var telefono = $(this).closest("tr").find("#telefono").text().trim();

    // Cargo los campos del modal.
    $("#form_nombre").val(nombre);
    $("#form_apellido").val(apellido);
    $("#form_area").val(area);
    $("#form_posicion").val(posicion);
    $("#form_email").val(email);
    $("#form_telefono").val(telefono);
    $("#contactoFormularioSubmit").attr("editar", id);
});

function quitarContacto(id) {
    var url = __AJAX_PATH__ + "formulariopreinscripcion/eliminarcontacto";
    var contactos = false;
    
    if ($("#tabla-datos-contacto > tbody > tr").length > 1) {
        contactos = true;
    } else {
        changeTimeLineStep('#timeline_dato_contacto', 'bg-tomate', 'bg-verdin');
    }
    
    if (id) {
        $.ajax(
            {
                url: url,
                method: 'POST',
                data: {id: id, contactos: contactos, idDatoPersonal: $('#proveedor-selector > option:selected').val()}
            }
        ).done(function (response) {
            $('#contacto-row' + id).remove();
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


