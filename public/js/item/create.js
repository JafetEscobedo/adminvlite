import app from "../_shared/app.js";
import requester from "../_shared/requester.js";
import * as selUnitId from "../_shared/select/unit_id.js";

const formItem = document.item;
const btnSave = document.getElementById("btnSave");

window.addEventListener("keyup", e => {
  if (e.key === "F2") btnSave.click();
});

formItem.itemDescription.onkeypress = e => {
  if (e.key === "Enter") {
    e.preventDefault();
    btnSave.click();
  }
};

selUnitId.initSelect2();
selUnitId.enableClearOnCloseSelect2();
formItem.onsubmit = async e => {
  try {
    e.preventDefault();
    app.loading(true);
    const fetched = await requester.submitForm(formItem);

    formItem.reset();
    formItem.itemCode.focus();

    selUnitId.destroySelect2();
    selUnitId.initSelect2();
    selUnitId.enableClearOnCloseSelect2();

    app.renderAlert({
      autohide: true,
      container: "alert",
      message: fetched.message,
      type: "success"
    });
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