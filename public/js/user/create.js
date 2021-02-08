import app from "../_shared/app.js";
import requester from "../_shared/requester.js";
import * as selUserRoleId from "../_shared/select/user_role_id.js";

const formUser = document.user;
const btnSave = document.getElementById("btnSave");

window.addEventListener("keyup", e => {
  if (e.key == "F2") btnSave.click();
});

selUserRoleId.initSelect2();
selUserRoleId.enableClearOnCloseSelect2();
formUser.onsubmit = async e => {
  try {
    e.preventDefault();
    app.loading(true);

    // Validar contraseñas
    if (formUser.userPassword.value.trim() != formUser.userPasswordConfirm.value.trim()) {
      throw "Las contraseñas no coinciden";
    }

    const fetched = await requester.submitForm(formUser);

    formUser.reset();
    formUser.userName.focus();

    selUserRoleId.destroySelect2();
    selUserRoleId.initSelect2();
    selUserRoleId.enableClearOnCloseSelect2();

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