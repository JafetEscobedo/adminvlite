/* global ReactDOM, React */

import app from "../_shared/app.js";
import requester from "../_shared/requester.js";

const formSale = document.getElementById("sale");
const tmplItemsListNotice = document.getElementById("itemsListNotice").content;
const tmplItemsListItem = document.getElementById("itemsListItem").content;
const txtSalesSerial = document.getElementById("saleSerial");
const txtareaSaleCancelNote = document.getElementById("saleCancelNote");
const btnCheckSale = document.getElementById("checkSale");
const btnSubmitForm = document.getElementById("btnSubmitForm");
const ulItemsContainer = document.getElementById("itemsContainer");

class CancelSale extends React.Component {
  constructor() {
    super();
    this.state = {saleDetails: []};
    this.listSaleDetails = this.listSaleDetails.bind(this);
    this.handleFormSubmit = this.handleFormSubmit.bind(this);
  }

  componentDidMount() {
    btnCheckSale.onclick = this.listSaleDetails;
    formSale.onsubmit = this.handleFormSubmit;

    txtSalesSerial.onkeypress = e => {
      if (e.key === "Enter") {
        e.preventDefault();
        if (txtSalesSerial.value.trim().length === 0) {
          return app.renderAlert({
            autohide: false,
            container: "alert",
            type: "danger",
            message: "El número de venta es obligatorio"
          });
        }
        btnCheckSale.click();
      }
    };

    txtareaSaleCancelNote.onkeypress = e => {
      if (e.key === "Enter") {
        e.preventDefault();
        btnSubmitForm.click();
      }
    };
  }

  async handleFormSubmit(e) {
    try {
      e.preventDefault();
      app.loading(true);

      const fetched = await requester.submitForm(formSale);
      this.setState({saleDetails: fetched.result});

      app.renderAlert({
        autohide: true,
        container: "alert",
        type: "success",
        message: fetched.message
      });

      txtSalesSerial.value = '';
      txtareaSaleCancelNote.value = '';
      txtSalesSerial.focus();
    } catch (err) {
      console.log(err);
      app.renderAlert({
        autohide: false,
        container: "alert",
        type: "danger",
        message: typeof err === "string" ? err : "Intentalo de nuevo, si el error persiste contacta al administrador"
      });
    } finally {
      app.loading(false);
    }
  }

  async listSaleDetails() {
    try {
      app.loading(true);
      const fetched = await requester
              .submitSimpleRequest(
                      `sale-detail/list/sale-details-by-sale-serial${txtSalesSerial.value.trim() ? `/${txtSalesSerial.value.trim()}` : ''}`
                      );
      this.setState({saleDetails: fetched.result});
    } catch (err) {
      this.setState({saleDetails: []});
      console.log(err);
      app.renderAlert({
        autohide: false,
        container: "alert",
        type: "danger",
        message: typeof err === "string" ? err : "Intentalo de nuevo, si el error persiste contacta al administrador"
      });
    } finally {
      app.loading(false);
    }
  }

  render() {
    const fragment = document.createDocumentFragment();
    const notice = document.importNode(tmplItemsListNotice, true);

    fragment.appendChild(notice);
    this.state.saleDetails.forEach(saleDetail => {
      const li = document.importNode(tmplItemsListItem, true);

      li.querySelector("h6").innerHTML = `<b>${saleDetail.itemCode} ${saleDetail.itemName}</b>`;
      li.querySelector("p").innerHTML = saleDetail.itemDescription;
      li.querySelector("span").innerHTML = `${saleDetail.saleCanceled === 'y'
              ? "Venta cancelada"
              : "Venta realizada"} - ${saleDetail.saleDetailStockOnMove} ${Number.parseInt(saleDetail.saleDetailStockOnMove) === 1
              ? saleDetail.unitSingularName
              : saleDetail.unitPluralName} a ${app.toCurrency(saleDetail.saleDetailItemPrice)} c/u`;
      li.querySelector("span").classList.add(`${saleDetail.saleCanceled === 'y' ? "text-danger" : "text-success"}`);
      fragment.appendChild(li);
    });

    ulItemsContainer.innerHTML = '';
    ulItemsContainer.appendChild(fragment);
    return null;
  }
}

ReactDOM.render(React.createElement(CancelSale), document.getElementById("storage"));