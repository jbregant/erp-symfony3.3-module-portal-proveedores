function validateEmail(Email){
    var pattern = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
    return $.trim(Email).match(pattern) ? true : false;
}

$(window).bind("load", function() { console.log("log");
    if(typeof idTiempoRespuesta != 'undefined'){
        $.ajax({
            url: __AJAX_PATH__ + 'tiemporespuesta/guardartiempo',
            method: 'POST',
            data: {
                idTiempoRespuesta: idTiempoRespuesta
            }
        });
    }
});
    