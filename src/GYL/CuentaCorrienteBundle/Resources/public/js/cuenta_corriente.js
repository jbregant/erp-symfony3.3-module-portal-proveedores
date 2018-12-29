$( document ).ready(function() {
    
    var btnTextMostrar = "Mostrar historial de pagos";
    var btnTextOcultar = "Ocultar historial de pagos";
    $('#button-compensadas').html(btnTextMostrar);
    
    if($('span').hasClass('notif-activa')){ // verifico si existe alguna notificacion no leida
        $('#notificacion-modal').modal('show');
        $('#proveedor-selector').prop('disabled', true);
        $('#button-compensadas').prop('disabled', true);
    };
    
    $('#button-compensadas').on('click', function(){
        $('.ocultable').toggleClass('hidden');
        if ($('#button-compensadas').html() == btnTextMostrar){
            $('#button-compensadas').html(btnTextOcultar);
        }else{
            $('#button-compensadas').html(btnTextMostrar);
        }
    });
    
    $('#proveedor-selector').on('change', function(){
        var idProveedor = $('#proveedor-selector').val();
        var csrfToken = $('#csrfToken').val();
        var url = __AJAX_PATH__ + 'cuentacorrienteajax';
        $.ajax({
            url: url,
            method: 'POST',
            data: {
                idProveedor: idProveedor,
                csrfToken: csrfToken
            },
            beforeSend: function (){
                $('.loading').show();
            }
        }).done(function(response) {
            $('#button-compensadas').html(btnTextMostrar);
            $('#div-tabla-cuentas').html(response);
        }).fail(function(jqXHR, textStatus, errorThrown) {
            location.reload();
        }).always(function(xhr, status, errorThrown) {
            $('.loading').hide();
        });
    });

    /**
    * Al clickear alguna de las notificaciones verifica si ya fue leida o no para cambiar el status a leida
    **/
    $('#boton-aceptar-notificacion, #boton-notificacion').on('click', function(event){
        event.preventDefault();
        
        var _idNotificacion = event.currentTarget.dataset.id;
        var _titulo = $(this).data("title"); 
        var _text = $(this).data("text"); 
        var _leido = event.currentTarget.dataset.leido; 
        var _url = __AJAX_PATH__ + 'notificacionajax';

        $('.modal-title').html(_titulo); // se llena el modal con el titulo
        $('.modal-body').html(_text); // se llena el modal con el texto del mensaje

        if(_leido == 0){ //no ha sido leido
            $.ajax({
                url: _url,
                method: 'POST',
                data: {
                    idNotificacion: _idNotificacion
                },
                beforeSend: function (){
                    $('.loading').show();
                }
            }).done(function(response) {
                var _etiqueta = $('.badge-notificacion');
                $('.notificacion[data-id='+response.id+']').attr('data-leido', 1).removeClass('unread').addClass('read'); // cambia a leido
                $('#boton-aceptar-notificacion[data-id='+response.id+']').attr('data-leido', 1); // cambia a leido

                if(parseInt(_etiqueta.text()) > 0){ // verifico si la cantidad de notificaciones es mayor a 0
                    notificaciones = parseInt(_etiqueta.text()) - 1; // se resta la notificacion leida a la cantidad total
                    _etiqueta.text(notificaciones); // se le asigna la cantidad actual de notificaciones
                    if(parseInt(_etiqueta.text()) === 0){ // no hay notificaciones nuevas
                        _etiqueta.text(response.notificaciones.length);
                        $('.badge-notificacion').removeClass('notif-activa');
                        $('.badge-notificacion').addClass('notif-inactiva');
                        $('#proveedor-selector').prop('disabled', false); // habilito las ctas corrientes para manipularlas
                        $('#button-compensadas').prop('disabled', false); // habilito las ctas corrientes para manipularlas
                    }
                }

                $('.loading').hide();
            }).fail(function(jqXHR, textStatus, errorThrown) {
                $('.loading').hide();
                location.reload();
            });
        }
    });
    
});

$(window).bind('load', function(){
});
    