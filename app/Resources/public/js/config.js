// override jquery validate plugin defaults
$.validator.setDefaults({
    highlight: function(element) {
        $(element).closest('.form-group').addClass('has-error');
    },
    unhighlight: function(element) {
        $(element).closest('.form-group').removeClass('has-error');
    },
    errorElement: 'span',
    errorClass: 'help-block',
    errorPlacement: function(error, element) {
        if (element.parent('.input-group').length) {
            error.insertAfter(element.parent());
        } else {
            error.insertAfter(element);
        }
    }
});

$.validator.addMethod("cuit", function(value) {
    if (!/^\d{2}-\d{8}-\d{1}$/.test(value)) {
        return false;
    }
    var aMult = '5432765432';
    var aMult = aMult.split('');
    var sCUIT = value.replace(/-/g, "").replace(/_/g, "").replace(/ /g, "");
    if (sCUIT && sCUIT != 0 && sCUIT.length == 11) {
        var aCUIT = sCUIT.split('');
        var iResult = 0;
        for (i = 0; i <= 9; i++) {
            iResult += aCUIT[i] * aMult[i];
        }
        iResult = (iResult % 11);
        iResult = 11 - iResult;
        if (iResult == 11) {
            iResult = 0;
        }
        if (iResult == 10) {
            iResult = 9;
        }
        if (iResult == aCUIT[10]) {
            return true;
        }
    }
    return false;
}, "Formato de CUIT incorrecto");

$.validator.addMethod("cbu", function(cbu) {
    if (!/^\d{22}$/.test(cbu)) {
        return false;
    }
    
    var VEC1 = new Array(7, 1, 3, 9, 7, 1, 3);

    var VEC2 = new Array(3, 9, 7, 1, 3, 9, 7, 1, 3, 9, 7, 1, 3);
 
    var valido = false;
 
    bloque1 = cbu.substring(0, 7);
 
    digitoValidador1 = cbu.substring(7, 8);
 
    bloque2 = cbu.substring(8, 21);
 
    digitoValidador2 = cbu.substring(21);
 
 
    var acum = 0;
 
    for (i = 0; i < 7; i++) {
 
       acum += bloque1.substring(i, i + 1) * VEC1[i];
 
    }
 
    strAcum = (acum + '');
 
    var digitoVCalculado1 = 10 - strAcum.substring(strAcum.length - 1);

    valido = ((digitoVCalculado1 == digitoValidador1) || (digitoVCalculado1 == 10 && digitoValidador1 == 0)) ;

    acum = 0;
 
    for (i = 0; i < 13; i++) {
 
       acum += bloque2.substring(i, i + 1) * VEC2[i];
 
    }
 
    strAcum = (acum + '');
 
    var digitoVCalculado2 = 10 - strAcum.substring(strAcum.length - 1);

    valido = (digitoVCalculado2 == digitoValidador2) && valido;

    return valido;
}, "El CBU es incorrecto");

$.validator.methods.nyp = function( value, element ) {
    return this.optional( element ) || /^([a-zA-ZñÑáéíóúÁÉÍÓÚ']+\s*)+$/.test( value );
};

$.validator.methods.razonsocial = function( value, element ) {
    return this.optional( element ) || /^[^\.|\,]([a-zA-ZñÑáéíóúÁÉÍÓÚ'.,]+\s*)+$/.test( value );
};

$.validator.methods.email = function( value, element ) {
    return this.optional( element ) || /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/.test( value );
};

$.validator.methods.telefono = function( value, element ) {
    return this.optional( element ) || /^\+?\d+$/.test( value );
};

$.validator.methods.letrasynumeros = function( value, element ) {
    return this.optional( element ) || /^([a-zA-ZñÑáéíóúÁÉÍÓÚ0-9]+\s*)+$/.test( value );
};

$.validator.methods.estado = function( value, element ) {
    return this.optional( element ) || /^(?![0-9]*$)[\sa-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]*\b$/.test( value );
};

$.validator.methods.solonumeros = function( value, element ) {
    return this.optional( element ) || /^[0-9]+$/.test( value );
};

$.validator.methods.nrosucursal = function( value, element ) {
    return this.optional( element ) || /^[0-9]{1,5}$/.test( value );
};

$.validator.methods.nrocuenta = function( value, element ) {
    return this.optional( element ) || /^[0-9]{1,14}$/.test( value );
};

$.validator.methods.swift = function( value, element ) {
    return this.optional( element ) || /^[a-zA-Z0-9]{1,11}$/.test( value );
};

$.validator.methods.aba = function( value, element ) {
    return this.optional( element ) || /^[0-9]{1,9}$/.test( value );
};

$.validator.methods.porcentajes = function( value, element ) {
    return this.optional( element ) || /(100|\d{1,2}(\.\d\d)?)/.test( value );
};

$.validator.methods.dni = function( value, element ) {
    return this.optional( element ) || /^[0-9]{2,2}\.[0-9]{3,3}\.[0-9]{3,3}$$/.test( value );
};

$.validator.methods.iban = function( value, element ) {
    return this.optional( element ) || /[a-zA-Z]{2}[0-9]{2}[a-zA-Z0-9]{4}[0-9]{7}([a-zA-Z0-9]?){0,16}/.test( value );
};

$.validator.methods.nroinscripcioniibb = function( value, element ) {
    return this.optional( element ) || /^([0-9]{1,13})$|^([0-9][0-9]\-[0-9]{8}\-[0-9])$/.test( value );
};

$.validator.addMethod("fechaValida", function(value, element) {
    return this.optional(element) || /^\d{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$/.test(value);
}, "El formato de la fecha es inválido.");