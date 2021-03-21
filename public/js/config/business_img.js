/* global CONFIG_ENTITY, moment */

import app from "../_shared/app.js";
import uploader from "../_shared/uploader.js";

const tmplThumbnail = document.getElementById("thumbnail");
const tmplDropzone = document.getElementById("dropzone");

const renderPreview = (containerId, files) => {
  const container = document.getElementById(containerId);
  const fragment = document.createDocumentFragment();
  const dropzone = document.importNode(tmplDropzone.content, true);
  const label = dropzone.querySelector("label");
  const input = container.parentNode.querySelector(`input[type="file"]`);

  label.setAttribute("for", input.id);

  if (files.length === 0) {
    container.innerHTML = '';
    container.appendChild(dropzone);
    return;
  }

  for (let i = 0; i < files.length; i++) {
    const file = files[i];
    const thumbnail = document.importNode(tmplThumbnail.content, true);

    thumbnail.querySelector("img").setAttribute("src", file.src);
    thumbnail.querySelector(".card-title").innerHTML = file.title;
    thumbnail.querySelector(".card-text .text-muted").title = moment(file.date).format(app.dateFormat);
    thumbnail.querySelector(".card-text .text-muted").innerHTML = moment(file.date).fromNow();

    fragment.appendChild(thumbnail);

    if ((i + 1) === files.length) {
      container.innerHTML = '';
      container.appendChild(fragment);
      container.appendChild(dropzone);
      app.rebuildTooltips();
    }
  }
};

uploader({
  containerId: "businessLogo",
  inputId: "configBusinessLogo",
  uploadURI: "config/update/business-logo",
  confirmTitle: "Confirmación de usuario",
  confirmMessage: "¿Seguro que desea actualizar el logo de empresa?",
  minFiles: 1,
  maxFiles: 1,
  onSuccess: fetched => {
    location.replace(app.url("offsite/action/logout"));
    app.renderAlert({
      autohide: true,
      container: "alertForBusinessLogo",
      message: fetched.message,
      type: "success"
    });
    renderPreview("businessLogo", [
      {
        src: app.url("public/img/config/" + fetched.result.configBusinessLogo),
        title: fetched.result.configBusinessLogo,
        date: fetched.result.configUpdatedAt
      }
    ]);
  },
  onError: err => {
    app.renderAlert({
      autohide: false,
      container: "alertForBusinessLogo",
      message: typeof err === "string" ? err : "Intentalo de nuevo, si el error persiste contacta al administrador",
      type: "danger"
    });
  }
});

uploader({
  containerId: "businessIcon",
  inputId: "configBusinessIcon",
  uploadURI: "config/update/business-icon",
  confirmTitle: "Confirmación de usuario",
  confirmMessage: "¿Seguro que desea actualizar el ícono de empresa?",
  minFiles: 1,
  maxFiles: 1,
  onSuccess: fetched => {
    location.replace(app.url("offsite/action/logout"));
    app.renderAlert({
      autohide: true,
      container: "alertForBusinessIcon",
      message: fetched.message,
      type: "success"
    });
    renderPreview("businessIcon", [
      {
        src: app.url("public/img/config/" + fetched.result.configBusinessIcon),
        title: fetched.result.configBusinessIcon,
        date: fetched.result.configUpdatedAt
      }
    ]);
  },
  onError: err => {
    console.log(err);
    app.renderAlert({
      autohide: false,
      container: "alertForBusinessIcon",
      message: typeof err === "string" ? err : "Intentalo de nuevo, si el error persiste contacta al administrador",
      type: "danger"
    });
  }
});

renderPreview("businessLogo", [
  {
    src: app.url("public/img/config/" + CONFIG_ENTITY.configBusinessLogo),
    title: CONFIG_ENTITY.configBusinessLogo,
    date: CONFIG_ENTITY.configUpdatedAt
  }
]);

renderPreview("businessIcon", [
  {
    src: app.url("public/img/config/" + CONFIG_ENTITY.configBusinessIcon),
    title: CONFIG_ENTITY.configBusinessIcon,
    date: CONFIG_ENTITY.configUpdatedAt
  }
]);