/* global ITEM_ENTITY */

import app from "../_shared/app.js";
import requester from "../_shared/requester.js";
import * as selUnitId from "../_shared/select/unit_id.js";

const $selItemActive = $("#itemActive");
const formItem = document.item;
const btnSave = document.getElementById("btnSave");
const btnItemHistory = document.getElementById("btnItemHistory");

btnItemHistory.setAttribute("href", app.url("item/view/items-list/history/" + ITEM_ENTITY.itemId));
formItem.itemId.value = ITEM_ENTITY.itemId;
formItem.itemName.value = ITEM_ENTITY.itemName;
formItem.itemCode.value = ITEM_ENTITY.itemCode;
formItem.itemDescription.value = ITEM_ENTITY.itemDescription;
formItem.itemCost.value = ITEM_ENTITY.itemCost;
formItem.itemPrice.value = ITEM_ENTITY.itemPrice;
formItem.itemBrand.value = ITEM_ENTITY.itemBrand;
formItem.itemCategory.value = ITEM_ENTITY.itemCategory;

selUnitId.initSelect2();
selUnitId.enableClearOnCloseSelect2();
selUnitId.setSelectedOption(ITEM_ENTITY.unitId, "Cargando nombre de unidad...");

$selItemActive.select2({width: "100%", minimumResultsForSearch: 10});
$selItemActive.select2("val", ITEM_ENTITY.itemActive);
$selItemActive.on("select2:select", () => app.rebuildTooltips());
app.rebuildTooltips();

requester
 .submitSimpleRequest("unit/read/single/" + ITEM_ENTITY.unitId)
 .then(fetched => {
   const result = fetched.result;
   selUnitId.setSelectedOption(result.unitId, `${result.unitSingularName} / ${result.unitPluralName}`);
 })
 .catch(err => {
   console.log(err);
   app.renderAlert({
     autohide: false,
     container: "alert",
     message: typeof err == "string" ? err : "Intentalo de nuevo, si el error persiste contacta al administrador",
     type: "danger"
   });
 });

window.addEventListener("keyup", e => {
  if (e.key == "F2") btnSave.click();
});

formItem.itemDescription.onkeypress = e => {
  if (e.key == "Enter") {
    e.preventDefault();
    btnSave.click();
  }
};

formItem.onsubmit = async e => {
  try {
    e.preventDefault();
    app.loading(true);
    const fetched = await requester.submitForm(formItem);

    formItem.itemCode.focus();
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