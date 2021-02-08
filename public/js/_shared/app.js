/* global BASE_URL, Intl, CSRF_HEADER */

export default {
  dataTableLang: {
    decimal: "",
    emptyTable: "No hay información",
    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
    infoEmpty: "Mostrando 0 a 0 de 0 registros",
    infoFiltered: "(Filtrado de _MAX_ registros)",
    infoPostFix: "",
    thousands: ",",
    lengthMenu: "Mostrar _MENU_ registros",
    loadingRecords: `<i class="fas fa-fw fa-spinner fa-spin"></i>&nbsp;&nbsp;Cargando...`,
    processing: `<i class="fas fa-fw fa-spinner fa-spin"></i>&nbsp;&nbsp;Procesando...`,
    search: "Buscar:",
    zeroRecords: "No se encontraron resultados",
    paginate: {
      first: "Primero",
      last: "Último",
      next: `<i class="fas fa-fw fa-angle-double-right"></i>`,
      previous: `<i class="fas fa-fw fa-angle-double-left"></i>`
    }
  },

  ajaxHeaders: {
    "Content-Type": "application/json",
    "X-Requested-With": "XMLHttpRequest",
    [CSRF_HEADER]: CSRF_HASH
  },

  dateFormat: "dddd D [de] MMMM YYYY [a las] h:mm:ss a", // z

  rebuildTooltips() {
    // Forzar el eliminado de cualquier tooltip visible
    $(".tooltip").remove();
    $("[title]").tooltip("dispose");

    // Restablecer plugin tooltip en todos los elementos con title
    $("[title]").tooltip({
      delay: {show: 1000, hide: 0},
      container: "body",
      template: `<div class="tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner small"></div></div>`
    });
  },

  toFormData(iterable) {
    const data = new FormData();

    for (const key in iterable) {
      data.append(key, iterable[key]);
    }

    return data;
  },

  toCurrency(value) {
    return new Intl.NumberFormat("es-MX", {
      currency: "MXN",
      style: "currency"
    }).format(value);
  },

  paginationConfig(table) {
    const config = new FormData();
    const columns = [];
    const orders = [];

    config.append("offset", table.start);
    config.append("limit", table.length);
    config.append("needle", table.search.value);

    table.order.forEach(order => {
      columns.push(table.columns[order.column].data || table.columns[order.column].name);
      orders.push(order.dir);
    });

    config.append("column", columns.join(','));
    config.append("order", orders.join(','));

    return config;
  },

  url(path = '') {
    if (path.length) {
      return `${BASE_URL}/${path}`;
    }

    return BASE_URL;
  },

  renderAlert(config) {
    const container = document.getElementById(config.container);

    container.innerHTML = `
      <div class="alert alert-${config.type} alert alert-dismissible px-4">
        <button type="button" class="p-0 mx-2 my-1 close" data-dismiss="alert" aria-hidden="true">&times;</button>
        ${config.message}
      </div>
    `;

    if (!config.autohide) return;

    window.setTimeout(() => {
      container.innerHTML = '';
    }, config.delay || 5000);
  },

  loading(show = true) {
    const loading = document.getElementById("appLoading");
    if (show) {
      $(loading).show();
    } else {
      $(loading).hide();
  }
  },

  toast(conf = {}) {
    $(document).Toasts("create", {
      autohide: typeof conf.autohide != "undefined" ? conf.autohide : true,
      body: conf.body || "Acción completada",
      class: "bg-" + (conf.className || "success"),
      delay: conf.delay || 5000,
      icon: "fas fa-fw " + (conf.icon || "fa-check-circle"),
      title: conf.title || "Notificación de sistema",
      subtitle: conf.subtitle || ''
    });
  }
}