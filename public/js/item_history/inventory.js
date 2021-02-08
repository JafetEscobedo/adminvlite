/* global moment, _ */

import app from "../_shared/app.js";
import requester from "../_shared/requester.js";

const $selLength = $("#inventoryTableLength");
const totalItems = document.getElementById("totalItems");
const totalCost = document.getElementById("totalCost");
const totalPrice = document.getElementById("totalPrice");
const totalEarning = document.getElementById("totalEarning");
const txtSearch = document.getElementById("inventoryTableSearch");
const dtInventory = $("#inventoryTable").DataTable({
  language: app.dataTableLang,
  responsive: true,
  serverSide: true,
  processing: true,
  fixedHeader: true,
  pageLength: 10,
  dom: "tipr",
  order: [[1, "desc"]],
  columnDefs: [{
      targets: "_all",
      data: null
    }, {
      targets: [7],
      orderable: false
    }],
  ajax: async (table, setdata) => {
    try {
      const config = app.paginationConfig(table);
      const fetchedFromList = await requester.submitData("item/list/active-items", config);
      const fetchedFromRead = await requester.submitSimpleRequest("item/read/items-summary");

      totalItems.innerText = fetchedFromRead.result.totalItems;
      totalCost.innerText = app.toCurrency(fetchedFromRead.result.totalCost);
      totalPrice.innerText = app.toCurrency(fetchedFromRead.result.totalPrice);
      totalEarning.innerText = app.toCurrency(fetchedFromRead.result.totalPrice - fetchedFromRead.result.totalCost);

      setdata({
        recordsTotal: fetchedFromList.result.total,
        recordsFiltered: fetchedFromList.result.filtered,
        data: fetchedFromList.result.data
      });
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
    }
  },
  columns: [{
      data: "itemCode"
    }, {
      width: "200px",
      data: "itemName"
    }, {
      name: "itemCost",
      render: data => app.toCurrency(data.itemCost)
    }, {
      name: "itemPrice",
      render: data => app.toCurrency(data.itemPrice)
    }, {
      data: "itemStock",
      createdCell: (td, cellData, rowData) => {
        if (cellData == 0) {
          td.parentNode.style.fontWeight = "bold";
          td.parentNode.style.color = "#DC3545";
        } else if (rowData.itemLowStock == 'y') {
          td.parentNode.style.fontWeight = "bold";
          td.parentNode.style.color = "#FF851B";
        }
      }
    }, {
      name: "itemLastEntry",
      render: data => !data.itemLastIngress
         ? `<small class="${data.itemStock == 0 ? "text-danger text-bold" : (data.itemLowStock == 'y' ? "text-warning text-bold" : "text-muted")}">
              <i>Sin registros</i>
            </small>`
         : `<span title="${moment(data.itemLastIngress).format(app.dateFormat)}">
              ${moment(data.itemLastIngress).fromNow()}
            </span>`
    }, {
      name: "itemLastEgress",
      render: data => !data.itemLastEgress
         ? `<small class="${data.itemStock == 0 ? "text-danger text-bold" : (data.itemLowStock == 'y' ? "text-warning text-bold" : "text-muted")}">
              <i>Sin registros</i>
            </small>`
         : `<span title="${moment(data.itemLastEgress).format(app.dateFormat)}">
              ${moment(data.itemLastEgress).fromNow()}
            </span>`
    }, {
      render: data => `
        <a title="Actualizar" class="btn btn-default btn-xs" href="${app.url("item/view/items-list/update/" + data.itemId)}")">
          <i class="fas fa-fw fa-pencil-alt text-info"></i>
        </a>
        <a title="Historial" class="btn btn-default btn-xs" href="${app.url("item/view/items-list/history/" + data.itemId)}")">
          <i class="fas fa-fw fa-history text-purple"></i>
        </a>`
    }]
});

const debouncedSearch = _.debounce(needle => dtInventory.search(needle).draw(), 250);

$selLength.select2({width: "100%", minimumResultsForSearch: 10});
$selLength.on("select2:select", e => {
  dtInventory.page.len(e.target.value).draw();
  app.rebuildTooltips();
});

dtInventory.on("responsive-display", () => app.rebuildTooltips());
dtInventory.on("draw", () => app.rebuildTooltips());
txtSearch.onkeyup = e => debouncedSearch(e.target.value);
txtSearch.onsearch = e => debouncedSearch(e.target.value);