/* global USER_ROLE_ENTITY, CryptoJS */

import app from "../_shared/app.js";
import requester from "../_shared/requester.js";

const $selUserRoleActive = $("#userRoleActive");
const formUserRole = document.userRole;
const btnSave = document.getElementById("btnSave");

formUserRole.userRoleId.value = USER_ROLE_ENTITY.userRoleId;
formUserRole.userRoleName.value = USER_ROLE_ENTITY.userRoleName;

$selUserRoleActive.select2({width: "100%", minimumResultsForSearch: 10});
$selUserRoleActive.select2("val", USER_ROLE_ENTITY.userRoleActive);
$selUserRoleActive.on("select2:select", () => app.rebuildTooltips());
app.rebuildTooltips();

JSON.parse(USER_ROLE_ENTITY.userRoleAccess).forEach(path => {
  const checkId = CryptoJS.MD5(path).toString();
  const check = document.getElementById(checkId);

  if (check) {
    check.checked = true;
  }
});

window.addEventListener("keyup", e => {
  if (e.key === "F2") btnSave.click();
});

formUserRole.onsubmit = async e => {
  try {
    e.preventDefault();
    app.loading(true);
    const fetched = await requester.submitForm(formUserRole);

    formUserRole.userRoleName.focus();
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