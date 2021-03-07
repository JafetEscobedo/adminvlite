/* global bootbox, moment */

import app from "./app.js";
import requester from "./requester.js";

export default function uploader(config) {
  const divContainer = document.getElementById(config.containerId);
  const fileInput = document.getElementById(config.inputId);

  divContainer.addEventListener("drop", e => handleDrop(e));
  divContainer.addEventListener("dragover", e => handleDragOver(e));
  fileInput.addEventListener("change", e => handleChange(e));

  const handleDrop = e => {
    e.preventDefault();
    if (e.target.matches(".uploader-file-add")) {
      handleUpload(e.dataTransfer.files);
    }
  }

  const handleChange = e => {
    handleUpload(e.target.files);
  };

  const handleUpload = files => {
    if (files.length < config.minFiles || files.length > config.maxFiles) {
      config.onError("La cantidad de archivos que intenta subir no es válida");
      return;
    }

    bootbox.confirm({
      title: config.confirmTitle,
      message: config.confirmMessage,
      buttons: {
        confirm: {
          label: `<i class="fas fa-fw fa-check-circle"></i>&nbsp;&nbsp;Sí, continuar`,
          className: "btn btn-sm bg-gradient-primary"
        },
        cancel: {
          label: `<i class="fas fa-fw fa-times-circle"></i>&nbsp;&nbsp;No, cancelar`,
          className: "btn btn-sm bg-gradient-secondary"
        }
      },
      callback: result => {
        if (result) storeFilesData(files)
      }
    });
  }

  const handleDragOver = e => {
    e.preventDefault();
  };

  const storeFilesData = async files => {
    try {
      const filesData = new FormData();

      app.loading(true);
      for (const file of files) {
        filesData.append(fileInput.name, file);
      }

      const fetched = await requester.submitData(config.uploadURI, filesData);
      config.onSuccess(fetched);
    } catch (err) {
      config.onError(err);
    } finally {
      app.loading(false);
    }
  }
};