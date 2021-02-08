/* global bsCustomFileInput, moment, Pace */

// Habilitar la animación de cargando al hacer peticiones AJAX
$(document).ajaxStart(Pace.restart);

// Establecer idioma y timezone de moment.js
moment.locale("es");
moment.tz.setDefault("America/Mexico_City");

// Inicializar tooltip
$("[title]").tooltip({
  delay: {show: 1000, hide: 0},
  container: "body",
  template: `<div class="tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner small"></div></div>`
});

// Plugin processing
jQuery.fn.dataTable.Api.register("processing()", function (show) {
  return this.iterator("table", function (ctx) {
    ctx.oApi._fnProcessingDisplay(ctx, show);
  });
});