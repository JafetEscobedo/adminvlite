/* global UNIT_ENTITY */

import app from "../_shared/app.js";
import requester from "../_shared/requester.js";

const $selUnitActive = $("#unitActive");
const formUnit = document.unit;
const btnSave = document.getElementById("btnSave");

formUnit.unitId.value = UNIT_ENTITY.unitId;
formUnit.unitSingularName.value = UNIT_ENTITY.unitSingularName;
formUnit.unitPluralName.value = UNIT_ENTITY.unitPluralName;

$selUnitActive.select2({width: "100%", minimumResultsForSearch: 10});
$selUnitActive.select2("val", UNIT_ENTITY.unitActive);
$selUnitActive.on("select2:select", () => app.rebuildTooltips());
app.rebuildTooltips();

window.addEventListener("keyup", e => {
  if (e.key == "F2") btnSave.click();
});

formUnit.onsubmit = async e => {
  try {
    e.preventDefault();
    app.loading(true);
    const fetched = await requester.submitForm(formUnit);

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
      message: typeof err == "string" ? err : "Intentalo de nuevo, si el error persiste contacta al administrador",
      type: "danger"
    });
  } finally {
    app.loading(false);
  }
};