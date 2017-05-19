var selected = $([]), offset = {top: 0, left: 0};
$(document).ready(function () {
    $("#upload_link").click(function (e) {
        e.preventDefault();
        $("#fileupload").trigger("click");
    });
    $('#fileupload').fileupload({
        dataType: 'json',
        dropZone: $("#contenedor-centro"),
        acceptFileTypes: '/(\.|\/)(xml|pdf|zip)$/i',
        add: function (e, data) {
            data.submit();
        },
        submit: function (e, data) {
            carpeta = $("#tree").jstree("get_selected")[0];
            data.formData = {'carpeta': carpeta}
        },
        done: function (e, data) {
            json = data.result;
            ID = parseInt(json.id_archivo, 10);
            if (ID > 0) {
                var arr = [];
                for (var x in json) {
                    arr[x] = json[x];
                }
                obj = crearElemento(arr);
                $(obj).trigger("click").removeClass("nuevo");
                total = $("#archivos .archivo").length
                plural = ((total == 1) ? "elemento" : "elementos");
                $("#archivo_tamano").html(total + " " + plural);
            } else {
                alert(json.error);
            }
        }
    });
    $('#tree').jstree({
        core: {
            multiple: false, // no multiselection
            check_callback: true,
            data: {
                url: base_url + 'filemanager/getDirectoriosDeId/',
                dataType: 'JSON',
                data: function (node) {
                    return node.id;
                }
            }
        },
        contextmenu: {
            select_node: true,
            items: customMenu
        },
        plugins: ['contextmenu', "sort"]
    });
    treeApi = $("#tree").jstree(true);
    $("#archivos").on("click", ".archivo", function (event) {
        $(this).addClass("hover ui-selected");
        if (!event.ctrlKey) {
            $(".archivo.hover").removeClass("hover").removeClass('ui-selected');
            $(this).addClass("hover ui-selected");
            mostrarDetallesDeArchivo(this);
        } else {
            mostrarDetallesDeCarpetasSeleccionadas();
        }
    }).on("dblclick", ".archivo", function (e) {
        e.preventDefault();
        id = $(this).attr("id");
        ext = $(".extension", this).text();
        mostrarPreviewDeArchivo(id, ext);
    }).selectable({
        cancel: '.archivo',
        stop: function (event, ui) {
            mostrarDetallesDeCarpetasSeleccionadas();
        },
        start: function (event, ui) {
            if (!event.ctrlKey) {
                $("#archivos .archivo.hover").removeClass("hover");
            }
        },
        selecting: function (event, ui) {
            $(ui.selecting).addClass("hover");
        },
        unselecting: function (event, ui) {
            $(ui.unselecting).removeClass("hover");
        }
    });
    $(window).on("resize", function () {
        redimensionarElementos();
    });

    $(window).trigger("resize");
    $('#myModal').on('shown.bs.modal', function (e) {
        $("#myModal #nombre_carpeta").val('').focus();
    }).on('hidden.bs.modal', function (e) {
        $("#myModal #nombre_carpeta").val('');
    });
    $('#myModalRename').on('shown.bs.modal', function (e) {
        $("input[type=text]", this).focus();
    });
    $("#myModal input[type=text]").keypress(function (event) {
        if (event.which == 13) {
            event.preventDefault();
            $("#myModal .modal-footer .btn-primary").trigger("click");
        }
    });
    $("#myModalRename input[type=text]").keypress(function (event) {
        if (event.which == 13) {
            event.preventDefault();
            $("#myModalRename .modal-footer .btn-primary").trigger("click");
        }
    });
    $("#myModal .modal-footer #crearCarpeta").click(function () {
        carpetaNombre = $("#myModal #nombre_carpeta").val();
        crearCarpeta(carpetaNombre);
    });
    $("#myModalRename .modal-footer #renombrar").click(function () {
        nuevoNombre = $("#myModalRename #nuevo_nombre").val();
        idArchivo = $("#myModalRename #id_archivo").val();
        viejoNombre = $("#myModalRename #antiguo_nombre").val();
        $("div.archivo[id=" + idArchivo + "] .propiedades .nombre").html('<center><img src="' + base_url + '/image/ajax-loader-mini.gif"></center>')
        renombrarArchivo(nuevoNombre, idArchivo, oldName);
    });
    $("#miniaturas-view").click(function () {
        $("#archivos #header-lista").remove();
        $("#contenedor-centro #archivos").removeClass("lista").addClass("miniaturas");
        $(window).trigger("resize");
        $("#miniaturas-view > img").attr("src", base_url + 'image/miniaturas-view-active.png');
        $("#lista-view > img").attr('src', base_url + 'image/lista-view-inactive.png');
        isotope = aplicarIsotope();
        $("#extensionButton").trigger("change");
    });
    $("#lista-view").click(function () {
        $("#contenedor-centro #archivos").removeClass("miniaturas").addClass("lista");
        crearCabeceraDeModoLista();
        $("#miniaturas-view > img").attr("src", base_url + 'image/miniaturas-view-inactive.png');
        $("#lista-view > img").attr('src', base_url + 'image/lista-view-active.png');
        isotopeGrid.isotope('destroy');
        isotopeGrid = false;
        buscarCoincidencias();
    });
    //$("#lista-view").trigger("click");
    $("#miniaturas-view").trigger("click")
    $("#extensionButton").on('change', function () {
        if (!$("#srch-term").hasClass('ui-autocomplete-input')) {
            if (isotopeGrid) {
                var filterValue = $(".active input:checked", this).attr('data-filter');
                filterValue = filterFns[ filterValue ] || filterValue;
                isotopeGrid.isotope({filter: filterValue});
            } else {
                buscarCoincidencias();
            }
        }
    });
});
function cargarArchivosDeCarpeta(id_carpeta) {
    if (isotopeGrid) {
        isotopeGrid.on('removeComplete', function () {
            $("#contenedor-derecha #panel").hide();
            $("#archivos").html('<div id="empty"><div><h1><img src="' + base_url + 'image/ajax-loader.gif"><span style="color: #5c154d;"> Cargando...</span></h1></div></div>');
        })
        isotopeGrid.isotope('remove', $("#archivos.miniaturas .archivo"));
    } else {
        $("#archivo_tamano").html("Cargando...");
        $("#archivos").html('<div id="empty"><div><h1><img src="' + base_url + 'image/ajax-loader.gif"><span style="color: #5c154d;"> Cargando...</span></h1></div></div>');
    }
    $.post(base_url + 'filemanager/getArchivosDeCarpetaId/' + id_carpeta, function (txt) {
        json = eval(txt);
        if (json.length > 0) {
            $("#archivos").html('');
            for (var i = 0; i < json.length; i++) {
                crearElemento(json[i]);
            }
            total = $("#archivos > .archivo").length;
            plural = ((total == 1) ? "elemento" : "elementos");
            $("#archivo_tamano").html(total + " " + plural);
            crearCabeceraDeModoLista();
        } else {
            $("#archivos").html('<div id="empty"><div><h1>Carpeta vacía</h1></div></div>');
            $("#archivo_tamano").html("Sin archivos");
        }
        $("#archivos > .archivo").draggable({
            zIndex: 50,
            start: function (event, ui) {
                //$(ui.helper).trigger("click");
                if ($(this).hasClass("ui-selected")) {
                    selected = $(".ui-selected").each(function () {
                        var el = $(this);
                        el.data("offset", el.offset());
                    });
                }
                else {
                    selected = $([]);
                    $("#selectable > div").removeClass("ui-selected");
                }
                offset = $(this).offset();
            },
            drag: function (ev, ui) {
                var dt = ui.position.top - offset.top, dl = ui.position.left - offset.left;
                // take all the elements that are selected expect $("this"), which is the element being dragged and loop through each.
                selected.not(this).each(function () {
                    // create the variable for we don't need to keep calling $("this")
                    // el = current element we are on
                    // off = what position was this element at when it was selected, before drag
                    var el = $(this), off = el.data("offset");
                    el.css({top: off.top + dt, left: off.left + dl});
                });
            },
            stop: function (event, ui) {
                //isotopeGrid.isotope('layout')
            }
        });
    });
    return false;
}

function crearElemento(obj) {
    var extension = obj['extension'].toString().toLowerCase();
    var imagen = '<img class="thumbnail center-block" src="' + base_url + 'image/icon-filetype/' + extension + '.png">';
    if (extension == "pdf") {
        $.post(base_url + 'filemanager/createThumbnail/' + obj['id_archivo'], {ancho: 200, alto: 200}, function (json) {
            imagen = '<img class="thumbnail center-block" src="data:image/bmp;base64,' + json.base64 + '" />';
            $(".archivo#" + json.id_archivo + " .icono").html(imagen)
        }, "json");
    }
    var metadatos = "";
    if (typeof obj['metadatos'] != "undefined") {
        metadatos = decodeURIComponent($.param(obj['metadatos'])).replace(/([*+?^!${}()|\[\]\/\\])/g, " ");
    }
    var html = '<div class="archivo" id="' + obj['id_archivo'] + '" metadatos="' + metadatos + '">' +
            '<div class="icono">' + imagen + '</div>' +
            '<div class="propiedades">' +
            '<div class="nombre">' + obj["nombre"] + '</div>' +
            '<div class="extension">' + extension + '</div>' +
            '</div>' +
            '<div class="tamano" title="' + obj['tamano'] + ' bytes" bytes="' + obj['tamano'] + '">' + bytes2KiloBytes(obj["tamano"]) + '</div>' +
            '<div class="tipo">' + obj['mime'] + '</div>' +
            '<div class="fecha_subida">' + obj['fecha_subida'] + '</div>' +
            '</div>';
    if ($("#archivos #empty").length > 0) {
        $("#empty").remove();
    }
    obj = $(html);
    if (isotopeGrid) {
        isotopeGrid.isotope('insert', obj);
    } else {
        $("#archivos").append(obj);
    }
    return obj;
}

function crearCarpeta(carpetaNombre) {
    idPadre = $("#tree").jstree(true).get_selected()[0];
    $.post(base_url + 'filemanager/crearCarpeta', {nombre: carpetaNombre, carpetaPadre: idPadre}, function (json) {
        if (json.success) {
            child = create_folder(carpetaNombre, json.id_carpeta);
            $("#tree").jstree(true).open_node(idPadre)
            seleccionarCarpeta(child)
        } else {
            alert(json.error);
        }
    }, "json");
}

function mostrarDetallesDeCarpeta(carpeta) {
    icono = base_url + "image/folder.png";
    nombre = carpeta;
    tipo = "Carpeta";
    $("#archivo_imagen, #archivo_tipo, #archivo_fecha_carga").parents("tr").show();
    $("#archivo_nombre").html(nombre);
    $("#archivo_extension").html("");
    $("#archivo_tipo").html(tipo);
    $("#archivo_icono").attr("src", icono);
    $("#panel").show();
    $("#archivo_imagen").hide();
    $("#metadatos").empty().hide()
}

function mostrarDetallesDeArchivo(archivo) {
    var id_archivo = $(archivo).attr("id")
    if (id_archivo == archivoSeleccionado) {
        return false;
    }
    archivoSeleccionado = id_archivo;
    $("#archivo_imagen, #archivo_tipo, #archivo_fecha_carga").parents("tr").show();
    ext = $(".extension", archivo).text();
    if (ext.toUpperCase() == "XML") {
        $("#contenedor-derecha #metadatos").html('<img src="' + base_url + 'image/ajax-loader-mini.gif"> Cargando metadatos').show();
    } else {
        $("#contenedor-derecha #metadatos").empty().hide();
    }
    if (ext != "EXT") {
        icono = base_url + "image/icon-filetype/" + ext + ".png";
        nombre = $(".nombre", archivo).html();
        tamano = $(".tamano", archivo).attr("bytes");
        $("#archivo_extension").html(ext);
        tipo = $(".tipo", archivo).html();
        creacion = $(".fecha_subida", archivo).html();
        $.post(base_url + "filemanager/getMetadatosDelArchivo/" + id_archivo, function (metadatos) {
            impuestos = [];
            if (typeof metadatos.uuid != "undefined") {
                if (metadatos.impuestos.trasladados)
                    impuestos.push("Trasladados: $" + metadatos.impuestos.trasladados)
                if (metadatos.impuestos.retenidos)
                    impuestos.push("Retenidos: $" + metadatos.impuestos.retenidos);
                html = '<address><strong>UUID</strong><br>' + metadatos.uuid + '<br></address>' +
                        '<address><strong>Emisor</strong><br>' + metadatos.emisor_nombre + '<br><i style="color:grey">' + metadatos.emisor_rfc + '</i></address>' +
                        '<address><strong>Receptor</strong><br>' + metadatos.receptor_nombre + '<br><i style="color:grey">' + metadatos.receptor_rfc + '</i></address>' +
                        '<address><strong>Total</strong><br>$ ' + metadatos.total + '</address>' +
                        '<address><strong>Impuestos</strong><br>' + impuestos.join("<br>") + '</address>' +
                        '<address><strong>Fecha de emisión</strong><br>' + mysqlFecha2Fecha(metadatos.fecha_emision) + '</address>';
                $("#contenedor-derecha #metadatos").html(html).show();
            }
        }, "json");
        $("#archivo_tamano").html(bytes2KiloBytes(tamano, 2));
        $("#archivo_nombre").html(nombre);
        $("#archivo_icono").attr("src", icono);
        $("#archivo_tipo").html(tipo);
        $("#archivo_icono").attr("src", icono);
        $("#archivo_fecha_carga").html(creacion);
        $("#panel").show();
        ext = $(".extension", archivo).text()
        ext = ext.toUpperCase();
        if (ext == "PDF") {
            $("#archivo_imagen").removeClass("bordear").html('<img class="img-responsive center-block" src="' + base_url + 'image/ajax-loader.gif"><center>Cargando vista preliminar</center>');
            $("#archivo_imagen").show();
            $.post(base_url + 'filemanager/createThumbnail/' + id_archivo, {}, function (json) {
                img = '<img class="img-responsive center-block" src="data:image/bmp;base64,' + json.base64 + '" />';
                $("#archivo_imagen").addClass("bordear").html(img);
            }, "json");
        } else {
            $("#archivo_imagen").hide();
        }
    }
}

function eliminarCarpeta() {
    id = treeApi.get_selected()[0];
    id = parseInt(id, 10);
    console.log(id)
    if (id > 0) {
        $.post(base_url + "filemanager/eliminarCarpeta/" + id, {}, function (result) {
            if (result.success) {
                delete_folder();
            } else {
                alert(result.mensaje);
            }
        }, "json");
    } else {
        alert("La carpeta raíz no puede ser eliminada");
    }
}

function renombrarArchivo(nuevoNombre, idArchivo, viejoNombre) {
    if (nuevoNombre.trim() != "" && parseInt(idArchivo, 10) > 0) {
        $.post(base_url + 'filemanager/renombrarArchivo/', {nombre: nuevoNombre, idArchivo: idArchivo, antiguo: viejoNombre}, function (json) {
            if (json.success) {
                $("div.archivo[id=" + idArchivo + "] .propiedades .nombre").html(json.nuevo_nombre);
                $("#archivo_nombre").html(json.nuevo_nombre);
            } else {
                alert(json.mensaje);
                $("div.archivo[id=" + idArchivo + "] .propiedades .nombre").html(json.viejo_nombre);
            }
        }, "json");
    }
}

function eliminarArchivo(idArchivo) {
    if (parseInt(idArchivo, 10) > 0) {
        $.post(base_url + 'filemanager/eliminarArchivo/' + id, {}, function (json) {
            if (json.success) {
                obj = $("div.archivo[id=" + idArchivo + "]");
                if (obj.next().length > 0) {
                    obj.next().trigger("click");
                } else if (obj.prev().length > 0) {
                    obj.prev().trigger("click");
                } else {
                    idCarpeta = treeApi.get_selected()[0];
                    cargarArchivosDeCarpeta(idCarpeta);
                    mostrarDetallesDeCarpeta(treeApi.get_text(idCarpeta));
                }
                obj.remove();
            } else {
                alert(json.mensaje);
            }
        }, "json");
    }
}

function crearCabeceraDeModoLista() {
    header = '<div class="archivo" id="header-lista">' +
            '<div class="icono"></div>' +
            '<div class="propiedades">' +
            '<div class="nombre"> Nombre del archivo </div>' +
            '<div class="extension">EXT</div>' +
            '</div>' +
            '<div class="tamano">Tamaño</div>' +
            '<div class="tipo">Tipo</div>' +
            '<div class="ultima_modificacion"> ultima modificaciones </div>' +
            '<div class="fecha_subida">Subido el</div>' +
            '</div>';
    if ($("#header-lista").length == 0 && $("#archivos .archivo").length > 0) {
        $("#contenedor-centro #archivos.lista").prepend(header);
    }
    redimensionarElementos();
    if (!$("#srch-term").hasClass('ui-autocomplete-input')) {
        buscarCoincidencias();
    }
}

function redimensionarElementos() {
    var w = $("#archivo_nombre").parents("#contenedor-derecha").width();
    $("#archivo_nombre").css('max-width', w - 110);
    w = $("#contenedor-centro").width();
    $("#archivos.lista .propiedades .nombre").width(w - 325);
    $("#archivos.miniaturas .propiedades .nombre").width(150);
}

function mostrarDetallesDeCarpetasSeleccionadas() {
    archivoSeleccionado = null;
    cantidadSeleccionados = $("#archivos .archivo.hover").length;
    if (cantidadSeleccionados == 0) {
        $("#contenedor-derecha > div").hide();
        return false;
    }
    if (cantidadSeleccionados == 1) {
        mostrarDetallesDeArchivo($("#archivos .archivo.hover"));
    } else {
        icono = base_url + "image/varios_archivos_seleccionados.png";
        nombre = "Archivos selecciondos";
        $("#metadatos").empty();
        $("#archivos .archivo.hover").each(function () {
            html = "<p>" + $(".propiedades .nombre", this).text() + "." + $(".propiedades .extension", this).text() + "</p>";
            $("#metadatos").append(html);
        });
        $("#archivo_tamano").html(cantidadSeleccionados + " archivos");
        $("#archivo_nombre").html(nombre);
        $("#archivo_extension").html("");
        $("#archivo_icono").attr("src", icono);
        $("#archivo_imagen, #archivo_tipo, #archivo_fecha_carga").parents("tr").hide();
        $("#panel, #metadatos").show();
    }
}