/* global moment, _ */

import app from "../_shared/app.js";
import requester from "../_shared/requester.js";

const $modalSaleDetails = $("#saleDetailsModal");
const $selLength = $("#salesListLength");
const $selStatus = $("#salesListStatus");
const spanSalesTotalPrice = document.getElementById("salesTotalPrice");
const spanSalesTotalCost = document.getElementById("salesTotalCost");
const spanSalesTotalEarning = document.getElementById("salesTotalEarning");
const txtSearch = document.getElementById("salesListSearch");
const dateStartDate = document.getElementById("salesListStartDate");
const dateFinalDate = document.getElementById("salesListFinalDate");
const tbSalesList = document.querySelector("#salesList tbody");
const dtSalesList = $("#salesList").DataTable({
  language: app.dataTableLang,
  responsive: true,
  serverSide: true,
  processing: true,
  fixedHeader: true,
  pageLength: 10,
  dom: "tipr",
  order: [[0, "desc"]],
  columnDefs: [{
      targets: "_all",
      data: null
    }, {
      targets: [5],
      orderable: false
    }],
  ajax: async (table, setdata) => {
    try {
      const config = app.paginationConfig(table);
      // Configuración adicional que NO proporciona DataTable
      config.append("status", $selStatus.val().trim());
      config.append("sdate", dateStartDate.value.trim());
      config.append("fdate", dateFinalDate.value.trim());

      const fetchedFromSale = await requester.submitData("sale/list/sales", config);
      const fetchedFromSaleDetail = await requester.submitData("sale-detail/read/sales-global-summary", config);

      setdata({
        recordsTotal: fetchedFromSale.result.total,
        recordsFiltered: fetchedFromSale.result.filtered,
        data: fetchedFromSale.result.data
      });

      spanSalesTotalCost.innerHTML = app.toCurrency(fetchedFromSaleDetail.result.salesTotalCost);
      spanSalesTotalPrice.innerHTML = app.toCurrency(fetchedFromSaleDetail.result.salesTotalPrice);
      spanSalesTotalEarning.innerHTML = app.toCurrency(fetchedFromSaleDetail.result.salesTotalEarning);
    } catch (err) {
      console.log(err);
      app.renderAlert({
        autohide: false,
        container: "alert",
        type: "danger",
        message: typeof err == "string" ? err : "Intentalo de nuevo, si el error persiste contacta al administrador"
      });
      setdata({
        recordsTotal: 0,
        recordsFiltered: 0,
        data: []
      });
      spanSalesTotalCost.innerHTML = app.toCurrency(0);
      spanSalesTotalPrice.innerHTML = app.toCurrency(0);
      spanSalesTotalEarning.innerHTML = app.toCurrency(0);
    }
  },
  columns: [{
      data: "saleSerial"
    }, {
      name: "userNickname",
      render: data => `<span title="${data.userFullName}">${data.userNickname}</span>`
    }, {
      name: "saleCanceled",
      render: data => data.saleCanceled == 'y'
         ? `<span class="badge badge-danger">Cancelada</span>`
         : `<span class="badge badge-success">No cancelada</span>`
    }, {
      name: "saleTotalCost",
      render: data => app.toCurrency(data.saleTotalCost)
    }, {
      name: "saleTotalPrice",
      render: data => app.toCurrency(data.saleTotalPrice)
    }, {
      name: "saleCancelNote",
      width: "150px",
      render: data => data.saleCancelNote ? data.saleCancelNote : `<small class="text-muted font-italic">No aplica</small>`
    }, {
      name: "saleCreatedAt",
      render: data => `
        <span title="${moment(data.saleCreatedAt).format(app.dateFormat)}">
          ${moment(data.saleCreatedAt).fromNow()}
        </span>`
    }, {
      name: "saleCanceledAt",
      render: data => !data.saleCanceledAt
         ? `<small class="text-muted font-italic">No aplica</small>`
         : `<span title="${moment(data.saleCanceledAt).format(app.dateFormat)}">
              ${moment(data.saleCanceledAt).fromNow()}
            </span>`
    }, {
      width: "80px",
      render: data => `
        <button data-sale-serial="${data.saleSerial}" title="Ver detalles" type="button" class="btn-sale-details btn btn-xs btn-default">
          <i class="fas fa-fw fa-list text-info"></i>
        </button>
        <a title="Cancelar venta" class="${data.saleCanceled == 'y' ? "disabled" : ''} btn btn-default btn-xs" href="${app.url("sale/view/cancel?saleSerial=" + data.saleSerial)}")">
          <i class="fas fa-fw fa-ban text-danger"></i>
        </a>`
    }]
});

const dtSaleDetailsList = $("#saleDetailsList").DataTable({
  language: app.dataTableLang,
  processing: true,
  pageLength: 100,
  dom: "tir",
  order: [[1, "asc"]],
  columnDefs: [{
      targets: "_all",
      data: null
    }],
  columns: [
    {data: "itemCode"},
    {data: "itemName"},
    {data: "itemDescription"},
    {data: "saleDetailStockOnMove"},
    {render: data => app.toCurrency(data.saleDetailItemCost)},
    {render: data => app.toCurrency(data.saleDetailItemPrice)},
    {render: data => app.toCurrency(data.saleDetailItemCost * data.saleDetailStockOnMove)},
    {render: data => app.toCurrency(data.saleDetailItemPrice * data.saleDetailStockOnMove)}
  ]
});

tbSalesList.onclick = async e => {
  let btn = null;

  if (e.target.matches(".btn-sale-details")) {
    btn = e.target;
  }

  if (e.target.matches(".btn-sale-details i")) {
    btn = e.target.parentNode;
  }

  if (btn) {
    try {
      $modalSaleDetails.modal("show");
      dtSaleDetailsList.clear().draw().processing(true);
      const fetched = await requester.submitSimpleRequest("sale-detail/list/sale-details-by-sale-serial/" + btn.dataset.saleSerial);
      dtSaleDetailsList.rows.add(fetched.result).draw();
    } catch (err) {
      console.log(err);
      app.renderAlert({
        autohide: false,
        container: "alertToSaleDetailsList",
        type: "danger",
        message: typeof err == "string" ? err : "Intentalo de nuevo, si el error persiste contacta al administrador"
      });
    } finally {
      dtSaleDetailsList.processing(false);
    }
  }
};

const debouncedSearch = _.debounce(needle => dtSalesList.search(needle).draw(), 250);

$selLength.select2({width: "100%", minimumResultsForSearch: 10});
$selStatus.select2({width: "100%", minimumResultsForSearch: 10});

$selLength.on("select2:select", e => {
  dtSalesList.page.len(e.target.value).draw();
  app.rebuildTooltips();
});

$selStatus.on("select2:select", () => {
  dtSalesList.ajax.reload();
  app.rebuildTooltips();
});

dtSalesList.on("responsive-display", () => app.rebuildTooltips());
dtSalesList.on("draw", () => app.rebuildTooltips());
dtSaleDetailsList.on("responsive-display", () => app.rebuildTooltips());
dtSaleDetailsList.on("draw", () => app.rebuildTooltips());
dateStartDate.onchange = () => dateFinalDate.value.trim() && dtSalesList.ajax.reload();
dateFinalDate.onchange = () => dateStartDate.value.trim() && dtSalesList.ajax.reload();
txtSearch.onkeyup = e => debouncedSearch(e.target.value);
txtSearch.onsearch = e => debouncedSearch(e.target.value);