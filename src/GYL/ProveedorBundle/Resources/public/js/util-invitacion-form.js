function proveedorExtranjero(obj) { 
    if ($('#fg-h').is(":visible")) { 
        $('#fg-h').hide(); 
        $('#cuit-id').show(); 
        $('#cuit-pais-id').hide(); 
        $(obj).toggleClass('active'); 
    } else { 
        $('#fg-h').show(); 
        $('#cuit-pais-id').show(); 
        $('#cuit-id').hide(); 
        $(obj).removeClass('active'); 
    } 
}