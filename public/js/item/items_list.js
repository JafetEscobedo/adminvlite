/* global moment, _ */

import app from "../_shared/app.js";
import requester from "../_shared/requester.js";

const $selLength = $("#itemsListLength");
const txtSearch = document.getElementById("itemsListSearch");
const dtItemsList = $("#itemsList").DataTable({
  language: app.dataTableLang,
  responsive: true,
  serverSide: true,
  processing: true,
  fixedHeader: true,
  pageLength: 10,
  dom: "Btipr",
  buttons: {
    dom: {
      button: {
        className: ''
      }
    },
    buttons: [{
        extend: "colvis",
        text: `<i class="fas fa-fw fa-table"></i> Columnas`,
        titleAttr: "Mostrar / Ocultar",
        className: "mb-4 btn btn-sm bg-gradient-primary"
      }]
  },
  order: [[1, "desc"]],
  columnDefs: [{
      targets: "_all",
      data: null
    }, {
      targets: [11],
      orderable: false
    }, {
      targets: [6, 7, 8, 9, 10],
      visible: false
    }],
  ajax: async (table, setdata) => {
    try {
      const config = app.paginationConfig(table);
      const fetched = await requester.submitData("item/list/items", config);
      setdata({
        recordsTotal: fetched.result.total,
        recordsFiltered: fetched.result.filtered,
        data: fetched.result.data
      });
    } catch (err) {
      console.log(err);
      app.renderAlert({
        autohide: false,
        container: "alert",
        message: typeof err == "string" ? err : "Intentalo de nuevo, si el error persiste contacta al administrador",
        type: "danger"
      });
      setdata({
        recordsTotal: 0,
        recordsFiltered: 0,
        data: []
      });
    }
  },
  columns: [{
      data: "itemCode"
    }, {
      width: "150px",
      data: "itemName"
    }, {
      width: "200px",
      data: "itemDescription"
    }, {
      name: "itemCost",
      render: data => app.toCurrency(data.itemCost)
    }, {
      name: "itemPrice",
      render: data => app.toCurrency(data.itemPrice)
    }, {
      name: "itemActive",
      render: data => data.itemActive == 'y'
         ? `<span class="badge badge-success">Activo</span>`
         : `<span class="badge badge-danger">Inactivo</span>`
    }, {
      data: "itemBrand"
    }, {
      data: "itemCategory"
    }, {
      name: "itemCreatedAt",
      render: data => `
        <span title="${moment(data.itemCreatedAt).format(app.dateFormat)}">
          ${moment(data.itemCreatedAt).fromNow()}
        </span>`
    }, {
      name: "itemUpdatedAt",
      render: data => `
        <span title="${moment(data.itemUpdatedAt).format(app.dateFormat)}">
          ${moment(data.itemUpdatedAt).fromNow()}
        </span>`
    }, {
      name: "itemInactivatedAt",
      render: data => !data.itemInactivatedAt ? `<small class="text-muted"><i>No aplica</i></small>` : `
        <span title="${moment(data.itemInactivatedAt).format(app.dateFormat)}">
          ${moment(data.itemInactivatedAt).fromNow()}
        </span>`
    }, {
      render: data => `
        <a title="Actualizar" class="btn btn-xs bg-gradient-info" href="${app.url("item/view/items-list/update/" + data.itemId)}")">
          <i class="fas fa-fw fa-pencil-alt"></i>
        </a>
        <a title="Historial" class="btn btn-xs bg-gradient-purple" href="${app.url("item/view/items-list/history/" + data.itemId)}")">
          <i class="fas fa-fw fa-history"></i>
        </a>`
    }]
});

const debouncedSearch = _.debounce(needle => dtItemsList.search(needle).draw(), 250);

$selLength.select2({width: "100%", minimumResultsForSearch: 10});
$selLength.on("select2:select", e => {
  dtItemsList.page.len(e.target.value).draw();
  app.rebuildTooltips();
});

dtItemsList.on("column-visibility.dt", () => dtItemsList.draw(false));
dtItemsList.on("responsive-display", () => app.rebuildTooltips());
dtItemsList.on("draw", () => app.rebuildTooltips());
txtSearch.onkeyup = e => debouncedSearch(e.target.value);
txtSearch.onsearch = e => debouncedSearch(e.target.value);