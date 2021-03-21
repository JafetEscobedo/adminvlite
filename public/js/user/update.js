/* global USER_ENTITY */

import app from "../_shared/app.js";
import requester from "../_shared/requester.js";
import * as selUserRoleId from "../_shared/select/user_role_id.js";

const $selUserActive = $("#userActive");
const formUser = document.user;
const btnSave = document.getElementById("btnSave");

formUser.userId.value = USER_ENTITY.userId;
formUser.userName.value = USER_ENTITY.userName;
formUser.userSurname.value = USER_ENTITY.userSurname;
formUser.userNickname.value = USER_ENTITY.userNickname;

selUserRoleId.initSelect2();
selUserRoleId.enableClearOnCloseSelect2();
selUserRoleId.setSelectedOption(USER_ENTITY.userRoleId, "Cargando rol de usuario...");

$selUserActive.select2({width: "100%", minimumResultsForSearch: 10});
$selUserActive.select2("val", USER_ENTITY.userActive);
$selUserActive.on("select2:select", () => app.rebuildTooltips());
app.rebuildTooltips();

requester
        .submitSimpleRequest("user-role/read/single/" + USER_ENTITY.userRoleId)
        .then(fetched => {
          const result = fetched.result;
          selUserRoleId.setSelectedOption(result.userRoleId, result.userRoleName);
        })
        .catch(err => {
          console.log(err);
          app.renderAlert({
            autohide: false,
            container: "alert",
            message: typeof err === "string" ? err : "Intentalo de nuevo, si el error persiste contacta al administrador",
            type: "danger"
          });
        });

window.addEventListener("keyup", e => {
  if (e.key === "F2") btnSave.click();
});

formUser.onsubmit = async e => {
  try {
    e.preventDefault();
    app.loading(true);

    // Validar contraseñas
    if (formUser.userPassword.value.trim() !== formUser.userPasswordConfirm.value.trim()) {
      throw "Las contraseñas no coinciden";
    }

    const fetched = await requester.submitForm(formUser);

    formUser.userPassword.value = '';
    formUser.userPasswordConfirm.value = '';
    formUser.userName.focus();

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