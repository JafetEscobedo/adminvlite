/* global ReactDOM, React, _ */

import app from "../_shared/app.js";
import requester from "../_shared/requester.js";

let currentItem = {};

const batch = [];
const $modalConfirm = $("#modalConfirm");
const formReadItem = document.readItem;
const formConfirmSale = document.confirmSale;
const btnSave = document.getElementById("btnSave");
const btnConfirm = document.getElementById("btnConfirm");
const btnAdd = document.getElementById("btnAdd");
const checkPrintSaleNote = document.getElementById("printSaleNote");
const txtCash = document.getElementById("cash");
const txtCashBack = document.getElementById("cashBack");
const txtTotalToPay = document.getElementById("totalToPay");
const txtItemCode = document.getElementById("itemCode");
const txtItemHistoryStockOnMove = document.getElementById("itemHistoryStockOnMove");
const tbItemsBatch = document.querySelector("#itemsBatch tbody");
const dtItemsBatch = $("#itemsBatch").DataTable({
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
      targets: [5],
      orderable: false
    }],
  columns: [
    {data: "itemCode"},
    {data: "itemName"},
    {render: data => `${data.itemHistoryStockOnMove} ${Number.parseInt(data.itemHistoryStockOnMove) === 1 ? data.unitSingularName : data.unitPluralName}`},
    {render: data => app.toCurrency(data.itemPrice)},
    {render: data => app.toCurrency(data.itemHistoryStockOnMove * data.itemPrice)},
    {
      render: data => `
        <button title="Remover" class="btn btn-sm btn-remove-row bg-gradient-danger" data-item-id="${data.itemId}">
          <i class="fas fa-fw fa-times-circle"></i>
        </button>`
    }
  ]
});

const validInputs = () => {
  if (Number.isNaN(Number.parseInt(txtItemHistoryStockOnMove.value.trim()))) {
    app.renderAlert({
      autohide: false,
      container: "alert",
      message: "La cantidad de artículo tiene que ser un número entero",
      type: "danger"
    });

    return false;
  }

  if (txtItemCode.value.trim().length === 0) {
    app.renderAlert({
      autohide: false,
      container: "alert",
      message: "Ingrese un codigo de artículo antes de agregar",
      type: "danger"
    });

    return false;
  }

  return true;
};

const printSaleNote = html => {
  const left = (window.innerWidth / 2) - (900 / 2) + window.screenLeft;
  const top = (window.innerHeight / 2) - (550 / 2) + window.screenTop;
  const newWindow = window.open('', "Imprimir Nota", `scrollbars=yes, width=900, height=550, top=${top}, left=${left}`);

  newWindow.document.documentElement.innerHTML = html;
  newWindow.print();
  newWindow.focus();
};

formReadItem.onsubmit = async e => {
  try {
    e.preventDefault();
    if (!validInputs()) return;

    app.loading(true);
    const uri = formReadItem.dataset.uri + (txtItemCode.value.trim() ? '/' + txtItemCode.value.trim() : '');
    const fetched = await requester.submitSimpleRequest(uri);

    currentItem = fetched.result;
    currentItem.itemHistoryStockOnMove = Math.abs(Number.parseInt(txtItemHistoryStockOnMove.value.trim()));
    currentItem.saleSerial = null;
    currentItem.saleCancelNote = null;

    const existing = batch.findIndex(el => Number.parseInt(el.itemId) === Number.parseInt(currentItem.itemId));

    if (existing !== -1) {
      const tr = document.querySelector(`[data-item-id="${currentItem.itemId}"]`).parentNode.parentNode;
      currentItem.itemHistoryStockOnMove += batch[existing].itemHistoryStockOnMove;
      dtItemsBatch.row(tr).data(currentItem).draw(false);
      batch[existing] = currentItem;
    } else {
      dtItemsBatch.row.add(currentItem).draw(false);
      batch.push(currentItem);
    }

    txtItemCode.value = '';
  } catch (err) {
    txtItemCode.select();
    console.log(err);
    app.renderAlert({
      autohide: false,
      container: "alert",
      message: typeof err === "string" ? err : "Intentalo de nuevo, si el error persiste contacta al administrador",
      type: "danger"
    });
  } finally {
    txtItemCode.focus();
    app.loading(false);
  }
};

tbItemsBatch.onclick = e => {
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
    dtItemsBatch.row(btn.parentNode.parentNode).remove().draw(false);
    batch.splice(batchIndex, 1);
    app.rebuildTooltips();
    txtItemCode.focus();
  }
};

window.addEventListener("keyup", e => {
  if (e.key === "F2") btnSave.click();
});

$modalConfirm.on("shown.bs.modal", () => {
  txtCash.select();
  txtCash.focus();
});

$modalConfirm.on("hidden.bs.modal", () => {
  document.getElementById("alertToConfirmSale").innerHTML = '';
  txtItemHistoryStockOnMove.value = 1;
  txtItemCode.value = '';
  txtItemCode.focus();
});

dtItemsBatch.on("responsive-display", () => app.rebuildTooltips());
dtItemsBatch.on("draw", () => app.rebuildTooltips());

class ConfirmSale extends React.Component {
  constructor() {
    super();
    this.state = {
      totalToPay: 0,
      cash: 0,
      cashBack: 0
    };
  }

  handleModalEvents() {
    btnSave.onclick = () => {
      if (!batch.length) {
        return app.renderAlert({
          autohide: false,
          container: "alert",
          message: "Tiene que agregar al menos un artículo",
          type: "danger"
        });
      }

      const totalToPay = batch.reduce((accumulator, current) => {
        accumulator += current.itemPrice * current.itemHistoryStockOnMove;
        return accumulator;
      }, 0);

      this.setState({
        cash: 0,
        cashBack: 0,
        totalToPay: totalToPay
      });

      $modalConfirm.modal("show");
    };

    formConfirmSale.onsubmit = async e => {
      try {
        e.preventDefault();

        if (this.state.cash < this.state.totalToPay) throw "El efectivo es insuficiente para proceder con la venta";

        app.loading(true);

        const data = new FormData();
        data.append("cash", app.toCurrency(this.state.cash));
        data.append("cashBack", app.toCurrency(this.state.cashBack));
        data.append("totalToPay", app.toCurrency(this.state.totalToPay));
        data.append("saleJsonString", JSON.stringify(batch));
        const fetched = await requester.submitData("sale/create/single-and-details", data);
        $modalConfirm.modal("hide");

        app.renderAlert({
          autohide: true,
          container: "alert",
          message: fetched.message,
          type: "success"
        });

        this.setState({
          cash: 0,
          cashBack: 0,
          totalToPay: 0
        });

        if (checkPrintSaleNote.checked) printSaleNote(fetched.result);

        dtItemsBatch.clear().draw();
        batch.splice(0, batch.length);
        txtItemCode.focus();
      } catch (err) {
        console.log(err);
        app.renderAlert({
          autohide: false,
          container: "alertToConfirmSale",
          message: typeof err === "string" ? err : "Intentalo de nuevo, si el error persiste contacta al administrador",
          type: "danger"
        });
      } finally {
        app.loading(false);
      }
    };

    const handleCashChange = e => {
      let value = e.target.value.trim();

      if (!Number.isNaN(Number.parseFloat(value)))
        value = Math.abs(Number.parseFloat(value));
      else if (value.length === 0)
        value = 0;
      else
        value = this.state.cash;

      this.setState({
        cash: value,
        cashBack: value - this.state.totalToPay
      });
    };

    txtCash.onchange = handleCashChange;
    txtCash.onkeyup = handleCashChange;
  }

  componentDidMount() {
    this.handleModalEvents();
  }

  render() {
    txtCash.value = this.state.cash || '';
    txtCashBack.value = app.toCurrency(this.state.cashBack);
    txtTotalToPay.value = app.toCurrency(this.state.totalToPay);
    return null;
  }
}

ReactDOM.render(React.createElement(ConfirmSale), document.getElementById("storage"));