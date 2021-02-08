import app from "../_shared/app.js";
import requester from "../_shared/requester.js";

const formUserRole = document.userRole;
const btnSave = document.getElementById("btnSave");

window.addEventListener("keyup", e => {
  if (e.key == "F2") btnSave.click();
});

formUserRole.userRoleName.focus();
formUserRole.onsubmit = async e => {
  try {
    e.preventDefault();
    app.loading(true);
    const fetched = await requester.submitForm(formUserRole);

    formUserRole.reset();
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
      message: typeof err == "string" ? err : "Intentalo de nuevo, si el error persiste contacta al administrador",
      type: "danger"
    });
  } finally {
    app.loading(false);
  }
};