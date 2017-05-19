var Meses = new Array('', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

function mysqlFecha2Fecha(str) {
    // Split timestamp into [ Y, M, D, h, m, s ]
    var t = str.split(/[- :]/);
    var txt = t[2] + ' de ' + meses(t[1]) + ' de ' + t[0] + ' a las ' + (t[3] > 12 ? t[3] - 12 : t[3]) + ":" + t[4] + (t[3] > 12 ? "pm" : "am")// + ":" + t[5];
    return txt;
}

function mysql2Date(str) { // 2015-05-31   =>  fecha.dia, fecha.mes, fecha.anio
    var t = str.split(/[- :]/);
    fecha = {dia: t[2], mes: t[1], anio: t[0]};
    return fecha;
}

function meses(m) {
    if (isNaN(m)) {
        return "";
    }
    m = parseInt(m, 10)
    var meses = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    return meses[m];
}

function deparams(str) {
    return str.split('&').reduce(function (params, param) {
        var paramSplit = param.split('=').map(function (value) {
            return decodeURIComponent(value);
        });
        params[paramSplit[0]] = paramSplit[1];
        return params;
    }, {});
}

function bytes2KiloBytes(bytes, decimals) {
    if (bytes == 0)
        return '0 Byte';
    var k = 1024; // 1 kilo = 1000 (Decimal) (or change to 1024 for binary) + Precision parameter
    var dm = decimals + 1 || 3;
    var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    var i = Math.floor(Math.log(bytes) / Math.log(k));
    return (bytes / Math.pow(k, i)).toPrecision(dm) + ' ' + sizes[i];
}

function mostrarMensajeComunicationConElSat(obj) {
    html = '<div style="text-align: center;">' +
            '<img src="' + base_url + 'image/logo_advans.png">' +
            '<img src="' + base_url + 'image/ajax-loader-bar.gif">' +
            '<img src="' + base_url + 'image/logo_sat.png">' +
            '<br>' +
            '<h3>Se esta procesando su solicitud.</h3>' +
            '<p>Esta operación puede tardar varios segundos</p>' +
            '</div>'
    $(obj).html(html).show();
}

function sleep(milliseconds) {
    var start = new Date().getTime();
    for (var i = 0; i < 1e7; i++) {
        if ((new Date().getTime() - start) > milliseconds) {
            break;
        }
    }
}