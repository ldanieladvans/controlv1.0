bootbox.setDefaults({
    locale: 'es',
    closeButton: false
});
//bootbox.addLocale("es", {
//    OK: 'Aceptar',
//    CANCEL: 'Cancelar',
//    CONFIRM: 'Sí'
//})

//alert()
var oAlert = alert;
function alert(txt, title, func) {
    try {
        bootbox.dialog({
            message: txt,
            title: title || "Boveda ADVANS",
            buttons: {
                success: {
                    label: "Aceptar",
                    callback: function () {
                        if (typeof func != 'undefined')
                            func();
                    }
                }
            },
        });
    } catch (e) {
        oAlert(txt);
    }
}
//alert("Hola", "Prueba");

//confirm()
var oConfirm = confirm;
function confirm(txt, title, func) {
    try {
        bootbox.dialog({
            message: txt,
            title: title || "Boveda ADVANS",
            buttons: {cancel: {label: "No", className: 'btn-danger'}, success: {label: "Sí", className: 'btn-success', callback: func}},
        });
    } catch (e) {
        if (oConfirm(txt, title))
            func();
    }
}
//confirm("Hola", "Prueba", function () {
//        alert("Prueba", "Superada");
//});

//prompt()
var oPrompt = prompt;
function prompt(txt, input, title, func) {
    try {
        bootbox.prompt({
            title: title || 'Bóveda ADVANS',
            message: txt,
            value: input,
            callback: function (result) {
                func(result)
            }
        });
    } catch (e) {
        func(prompt(txt, input, title));
    }
}
//prompt("Hola: Escribe tu nombre", "Valor", "Prueba", function (r) {
//    if (r)
//        alert(r);
//});