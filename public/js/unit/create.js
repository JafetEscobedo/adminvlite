import app from "../_shared/app.js";
import requester from "../_shared/requester.js";

const formUnit = document.unit;
const btnSave = document.getElementById("btnSave");

window.addEventListener("keyup", e => {
  if (e.key === "F2") btnSave.click();
});

formUnit.unitSingularName.focus();
formUnit.onsubmit = async e => {
  try {
    e.preventDefault();
    app.loading(true);
    const fetched = await requester.submitForm(formUnit);

    formUnit.reset();
    formUnit.unitSingularName.focus();
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