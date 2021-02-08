/* global Pace */

import app from "../_shared/app.js";
import requester from "../_shared/requester.js";

const formLogin = document.login;

formLogin.onsubmit = async e => {
  try {
    e.preventDefault();
    app.loading(true);
    const fetched = await requester.submitForm(formLogin);

    formLogin.reset();
    window.location.replace(app.url());
    app.renderAlert({
      autohide: false,
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