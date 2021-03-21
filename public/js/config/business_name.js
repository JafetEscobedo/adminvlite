/* global CONFIG_ENTITY */

import app from "../_shared/app.js";
import requester from "../_shared/requester.js";

const $selConfigBusinessNameUc = $("#configBusinessNameUc");
const formConfig = document.config;
const btnSave = document.getElementById("btnSave");

formConfig.configBusinessName.value = CONFIG_ENTITY.configBusinessName;

$selConfigBusinessNameUc.select2({width: "100%", minimumResultsForSearch: 10});
$selConfigBusinessNameUc.select2("val", CONFIG_ENTITY.configBusinessNameUc);
$selConfigBusinessNameUc.on("select2:select", () => app.rebuildTooltips());
app.rebuildTooltips();

window.addEventListener("keyup", e => {
  if (e.key === "F2") btnSave.click();
});

formConfig.onsubmit = async e => {
  try {
    e.preventDefault();
    app.loading(true);
    const fetched = await requester.submitForm(formConfig);
    formConfig.configBusinessName.focus();
    location.replace(app.url("offsite/action/logout"));
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