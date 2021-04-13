/* global moment */

import app from "../_shared/app.js";
import requester from "../_shared/requester.js";

let codeValidated = false;
let currentItem = {};

const batch = [];
const formReadItem = document.readItem;
const divItemSummary = document.getElementById("itemSummary");
const divItemStock = document.getElementById("itemStock");
const txtItemHistoryStockOnMove = document.getElementById("itemHistoryStockOnMove");
const txtItemPrice = document.getElementById("itemPrice");
const txtItemCost = document.getElementById("itemCost");
const txtItemCode = document.getElementById("itemCode");
const txtareaItemHistoryNote = document.getElementById("itemHistoryNote");
const btnAdd = document.getElementById("btnAdd");
const btnSave = document.getElementById("btnSave");
const btnValidateItemCode = document.getElementById("btnValidateItemCode");
const tbIngressBatch = document.querySelector("#ingressBatch tbody");
const dtIngressBatch = $("#ingressBatch").DataTable({
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
      render: data => !data.itemLastEntry
          ? `<small class="text-muted"><i>Sin registros</i></small>`
          : `<span title="${moment(data.itemLastEntry).fromNow()} (${moment(data.itemLastEntry).format(app.dateFormat)})">
              ${data.itemLastEntry}
            </span>`
    }, {
      width: "110px",
      render: data => `
        <div data-item-id="${data.itemId}">
          <button title="Remove uno" class="btn btn-xs btn-remove-one bg-gradient-info">
            <i class="fas fa-fw fa-minus-circle"></i>
          </button>
          <button title="Agregar uno" class="btn btn-xs btn-add-one bg-gradient-success">
            <i class="fas fa-fw fa-plus-circle"></i>
          </button>
          <button title="Remover" class="btn btn-xs btn-remove-row bg-gradient-danger">
            <i class="fas fa-fw fa-times-circle"></i>
          </button>
        <div>`
    }
  ]
});

const validInputs = () => {
  if (txtItemCode.value.trim().length === 0 || !codeValidated) {
    app.renderAlert({
      autohide: false,
      container: "alert",
      message: "Tiene que agregar el código de arículo y validarlo",
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

  if (Number.isNaN(txtItemPrice.value.trim())) {
    app.renderAlert({
      autohide: false,
      container: "alert",
      message: "El precio individual no es válido",
      type: "danger"
    });

    return false;
  }

  if (Number.isNaN(txtItemCost.value.trim())) {
    app.renderAlert({
      autohide: false,
      container: "alert",
      message: "El costo individual no es válido",
      type: "danger"
    });

    return false;
  }

  if (batch.length > 100) {
    app.renderAlert({
      autohide: false,
      container: "alert",
      message: "No puede realizar más de 100 entradas a la vez",
      type: "danger"
    });

    return false;
  }

  return true;
};

const clearEntries = () => {
  currentItem = {};
  batch.splice(0, batch.length);
  dtIngressBatch.clear().draw();
};

const clearInputs = () => {
  codeValidated = false;
  divItemSummary.innerHTML = `<small><i>Ningún artículo en el panel de espera</i></small>`;
  divItemStock.innerHTML = `<small><i>Información no disponible</i></small>`;
  txtItemCode.value = '';
  txtItemHistoryStockOnMove.value = '';
  txtItemCost.value = '';
  txtItemPrice.value = '';
  txtareaItemHistoryNote.value = '';
  txtItemCode.focus();
};

const handleAddOne = e => {
  let btn = null;

  if (e.target.matches(".btn-add-one")) btn = e.target;
  if (e.target.matches(".btn-add-one i")) btn = e.target.parentNode;

  if (btn) {
    const tr = btn.closest("tr");
    const itemId = btn.parentNode.dataset.itemId;
    const batchIndex = batch.findIndex(entry => Number.parseInt(entry.itemId) === Number.parseInt(itemId));

    batch[batchIndex].itemHistoryStockOnMove += 1;
    dtIngressBatch.row(tr).data(currentItem).draw(false);
    app.rebuildTooltips();
    txtItemCode.focus();
  }
};

const handleRemoveOne = e => {
  let btn = null;

  if (e.target.matches(".btn-remove-one")) btn = e.target;
  if (e.target.matches(".btn-remove-one i")) btn = e.target.parentNode;

  if (btn) {
    const tr = btn.closest("tr");
    const itemId = btn.parentNode.dataset.itemId;
    const batchIndex = batch.findIndex(entry => Number.parseInt(entry.itemId) === Number.parseInt(itemId));

    if (Number.parseInt(batch[batchIndex].itemHistoryStockOnMove) === 1) return;

    batch[batchIndex].itemHistoryStockOnMove -= 1;
    dtIngressBatch.row(tr).data(currentItem).draw(false);
    app.rebuildTooltips();
    txtItemCode.focus();
  }
};

const handleRemoveRow = e => {
  let btn = null;

  if (e.target.matches(".btn-remove-row")) btn = e.target;
  if (e.target.matches(".btn-remove-row i")) btn = e.target.parentNode;

  if (btn) {
    const tr = btn.closest("tr");
    const itemId = btn.parentNode.dataset.itemId;
    const batchIndex = batch.findIndex(entry => Number.parseInt(entry.itemId) === Number.parseInt(itemId));
    dtIngressBatch.row(tr).remove().draw(false);
    batch.splice(batchIndex, 1);
    app.rebuildTooltips();
    txtItemCode.focus();
  }
};

window.addEventListener("keyup", e => {
  if (e.key === "F2") btnSave.click();
});

txtItemCode.onkeypress = e => {
  if (e.key === "Enter") {
    e.preventDefault();
    codeValidated = false;
    btnValidateItemCode.click();
  }
};

txtareaItemHistoryNote.onkeypress = e => {
  if (e.key === "Enter") {
    e.preventDefault();
    btnAdd.click();
  }
};

btnValidateItemCode.onclick = async () => {
  try {
    if (txtItemCode.value.length === 0) throw "Tiene que agregar el código de artículo";

    app.loading(true);
    const uri = formReadItem.dataset.uri + (txtItemCode.value.trim() ? '/' + txtItemCode.value.trim() : '');
    const fetched = await requester.submitSimpleRequest(uri);

    // El precio y costo se pueden actualizar en este panel
    divItemSummary.innerHTML = `${fetched.result.itemName} - ${fetched.result.itemDescription}`;
    divItemStock.innerHTML = Number.parseInt(fetched.result.itemStock) > 0
      ? `<b class="text-success">${fetched.result.itemStock}</b>`
      : `<b class="text-danger">${fetched.result.itemStock}</b>`;
    txtItemCost.value = fetched.result.itemCost;
    txtItemPrice.value = fetched.result.itemPrice;

    currentItem = fetched.result;
    codeValidated = true;
    txtItemHistoryStockOnMove.focus();
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

formReadItem.onsubmit = e => {
  e.preventDefault();
  if (!validInputs()) return;

  // Propiedades adicionales que pueden ser modificadas
  currentItem.itemHistoryStockOnMove = Math.abs(Number.parseInt(txtItemHistoryStockOnMove.value.trim()));
  currentItem.itemCost = txtItemCost.value.trim();
  currentItem.itemPrice = txtItemPrice.value.trim();
  currentItem.itemHistoryNote = txtareaItemHistoryNote.value.trim();
  currentItem.itemHistoryEventId = null; // Entrada al inventario, en el back se asigna el ID correspondiente

  const existing = batch.findIndex(el => Number.parseInt(el.itemId) === Number.parseInt(currentItem.itemId));

  if (existing !== -1) {
    const tr = document.querySelector(`[data-item-id="${currentItem.itemId}"]`).closest("tr");
    currentItem.itemHistoryStockOnMove += batch[existing].itemHistoryStockOnMove;
    dtIngressBatch.row(tr).data(currentItem).draw(false);
    batch[existing] = currentItem;
  } else {
    dtIngressBatch.row.add(currentItem).draw(false);
    batch.push(currentItem);
  }

  clearInputs();
};

tbIngressBatch.onclick = e => {
  handleRemoveRow(e);
  handleRemoveOne(e);
  handleAddOne(e);
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

dtIngressBatch.on("responsive-display", () => app.rebuildTooltips());
dtIngressBatch.on("draw", () => app.rebuildTooltips());
