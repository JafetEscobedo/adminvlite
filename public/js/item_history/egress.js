/* global moment */

import app from "../_shared/app.js";
import requester from "../_shared/requester.js";

let currentItem = {};

const batch = [];
const $selItemHistoryEventId = $("#itemHistoryEventId");
const formReadItem = document.readItem;
const txtItemCode = document.getElementById("itemCode");
const txtItemHistoryStockOnMove = document.getElementById("itemHistoryStockOnMove");
const txtareaItemHistoryNote = document.getElementById("itemHistoryNote");
const btnAdd = document.getElementById("btnAdd");
const btnSave = document.getElementById("btnSave");
const tbEgressBatch = document.querySelector("#egressBatch tbody");
const dtEgressBatch = $("#egressBatch").DataTable({
  language: app.dataTableLang,
  responsive: true,
  paging: true,
  deferRender: true,
  pageLength: 100,
  data: [],
  dom: "rt",
  order: [[1, "desc"]],
  columnDefs: [{
      targets: "_all",
      data: null
    }, {
      targets: [7],
      orderable: false
    }],
  columns: [
    {data: "itemCode"},
    {data: "itemName"},
    {render: data => `${data.itemHistoryStockOnMove} ${Number.parseInt(data.itemHistoryStockOnMove) === 1 ? data.unitSingularName : data.unitPluralName}`},
    {render: data => `${data.itemStock} ${Number.parseInt(data.itemStock) === 1 ? data.unitSingularName : data.unitPluralName}`},
    {render: data => app.toCurrency(data.itemCost)},
    {render: data => app.toCurrency(data.itemPrice)},
    {
      render: data => !data.itemLastEgress ? `<small class="text-muted"><i>Sin registros</i></small>` : `
        <span title="${moment(data.itemLastEgress).fromNow()} (${moment(data.itemLastEgress).format(app.dateFormat)})" >
          ${data.itemLastEgress}
        </span>`
    }, {
      render: data => `
        <button title="Remover" class="btn btn-remove-row btn-sm bg-gradient-danger" data-item-id="${data.itemId}" >
          <i class="fas fa-fw fa-times-circle"></i>
        </button>`
    }
  ]
});

const validInputs = () => {
  if (!txtItemCode.value.trim().length) {
    app.renderAlert({
      autohide: false,
      container: "alert",
      message: "Tiene que agregar el código de arículo",
      type: "danger"
    });

    return false;
  }

  if (Number.isNaN(Number.parseInt(txtItemHistoryStockOnMove.value.trim()))) {
    app.renderAlert({
      autohide: false,
      container: "alert",
      message: "La cantidad de artículos no es válida",
      type: "danger"
    });

    return false;
  }

  if (!txtareaItemHistoryNote.value.trim()) {
    app.renderAlert({
      autohide: false,
      container: "alert",
      message: "La nota de salida es obligatoria",
      type: "danger"
    });

    return false;
  }

  if (batch.length > 100) {
    app.renderAlert({
      autohide: false,
      container: "alert",
      message: "No puede realizar más de 100 salidas a la vez",
      type: "danger"
    });

    return false;
  }

  return true;
};

const clearEntries = () => {
  currentItem = {};
  batch.splice(0, batch.length);
  dtEgressBatch.clear().draw();
};

const clearInputs = () => {
  $selItemHistoryEventId.find("option:eq(0)").prop("selected", true);
  $selItemHistoryEventId.select2("destroy");
  $selItemHistoryEventId.select2({width: "100%", minimumResultsForSearch: 10});
  $selItemHistoryEventId.on("select2:select", () => app.rebuildTooltips());

  txtItemCode.value = '';
  txtItemHistoryStockOnMove.value = '';
  txtareaItemHistoryNote.value = '';
  txtItemCode.focus();

  app.rebuildTooltips();
};

window.addEventListener("keyup", e => {
  if (e.key === "F2") btnSave.click();
});

txtareaItemHistoryNote.onkeypress = e => {
  if (e.key === "Enter") {
    e.preventDefault();
    btnAdd.click();
  }
};

formReadItem.onsubmit = async e => {
  try {
    e.preventDefault();
    if (!validInputs()) return;

    app.loading(true);
    const uri = formReadItem.dataset.uri + (txtItemCode.value.trim() ? '/' + txtItemCode.value.trim() : '');
    const fetched = await requester.submitSimpleRequest(uri);

    currentItem = fetched.result;

    // Propiedades adicionales que pueden ser modificadas
    currentItem.itemHistoryStockOnMove = Math.abs(Number.parseInt(txtItemHistoryStockOnMove.value.trim()));
    currentItem.itemHistoryNote = txtareaItemHistoryNote.value.trim();
    currentItem.itemHistoryEventId = $selItemHistoryEventId.val().trim();

    const existing = batch.findIndex(el => Number.parseInt(el.itemId) === Number.parseInt(currentItem.itemId));

    if (existing !== -1) {
      const tr = document.querySelector(`[data-item-id="${currentItem.itemId}"]`).parentNode.parentNode;
      currentItem.itemHistoryStockOnMove += batch[existing].itemHistoryStockOnMove;
      dtEgressBatch.row(tr).data(currentItem).draw(false);
      batch[existing] = currentItem;
    } else {
      dtEgressBatch.row.add(currentItem).draw(false);
      batch.push(currentItem);
    }

    clearInputs();
  } catch (err) {
    txtItemCode.select();
    txtItemCode.focus();
    console.log(err);
    app.renderAlert({
      autohide: false,
      container: "alert",
      message: typeof err === "string" ? err : "Intentalo de nuevo, si el error persiste contacta al administrador",
      type: "danger"
    });
  } finally {
    app.loading(false);
  }
};

tbEgressBatch.onclick = e => {
  let itemId = null;
  let btn = null;

  if (e.target.matches(".btn-remove-row")) {
    itemId = e.target.dataset.itemId;
    btn = e.target;
  }

  if (e.target.matches(".btn-remove-row i")) {
    itemId = e.target.parentNode.dataset.itemId;
    btn = e.target.parentNode;
  }

  if (itemId) {
    const batchIndex = batch.findIndex(entry => Number.parseInt(entry.itemId) === Number.parseInt(itemId));
    txtItemCode.value = batch[batchIndex].itemCode;
    txtItemCode.focus();
    dtEgressBatch.row(btn.parentNode.parentNode).remove().draw(false);
    batch.splice(batchIndex, 1);
    app.rebuildTooltips();
  }
};

btnSave.onclick = async () => {
  try {
    if (batch.length === 0) throw "Tiene que agregar al menos un artículo";

    app.loading(true);
    const data = app.toFormData({itemHistoryJsonString: JSON.stringify(batch)});
    const fetched = await requester.submitData("item-history/create/single-using-batch", data);

    app.renderAlert({
      autohide: true,
      container: "alert",
      message: fetched.message,
      type: "success"
    });

    clearInputs();
    clearEntries();
  } catch (err) {
    console.log(err);
    app.renderAlert({
      autohide: false,
      container: "alert",
      message: typeof err === "string" ? err : "Intentalo de nuevo, si el error persiste contacta al administrador",
      type: "danger"
    });
  } finally {
    app.loading(false);
  }
};

$selItemHistoryEventId.select2({width: "100%", minimumResultsForSearch: 10});
$selItemHistoryEventId.on("select2:select", () => app.rebuildTooltips());
dtEgressBatch.on("responsive-display", () => app.rebuildTooltips());
dtEgressBatch.on("draw", () => app.rebuildTooltips());
app.rebuildTooltips();