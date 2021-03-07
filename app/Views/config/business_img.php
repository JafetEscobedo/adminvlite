<?=
view("_shared/partial/app_breadcrumb", ["links" => [
    [
      "text" => "Configuración",
      "href" => base_url("config")
    ],
    [
      "text" => "Imagen de empresa",
      "href" => '#'
    ]
]])
?>

<link rel="stylesheet" type="text/css" href="<?= base_url("public/css/_shared/uploader.css") ?>">

<div class="card pt-3">
  <div class="card-body">

    <div class="form-group row">
      <label for="userName" class="col-sm-3 col-form-label">Logo principal de la empresa</label>
      <div class="col-sm-9">
        <div id="alertForBusinessLogo"></div>
        <div class="uploader-file-container" id="businessLogo"></div>
        <label class="btn btn-sm bg-gradient-success float-right" for="configBusinessLogo" style="font-weight: normal" >
          <i class="fas fa-fw fa-upload"></i>&nbsp;&nbsp;Seleccionar imagen nueva
        </label>
        <input accept="image/*" class="uploader-file-input" id="configBusinessLogo" name="configBusinessLogo" type="file">
      </div>
    </div>

    <div class="form-group row">
      <label for="userName" class="col-sm-3 col-form-label">Ícono principal de la empresa</label>
      <div class="col-sm-9">
        <div id="alertForBusinessIcon"></div>
        <div class="uploader-file-container" id="businessIcon"></div>
        <label class="btn btn-sm bg-gradient-success float-right" for="configBusinessIcon" style="font-weight: normal" >
          <i class="fas fa-fw fa-upload"></i>&nbsp;&nbsp;Seleccionar imagen nueva
        </label>
        <input accept="image/*" class="uploader-file-input" id="configBusinessIcon" name="configBusinessIcon" type="file">
      </div>
    </div>

  </div>
</div>

<template id="dropzone">
  <div class="uploader-file-thumbnail">
    <label class="uploader-file-add" style="font-weight: normal">
      <i class="fas fa-fw fa-upload"></i>&nbsp;&nbsp;Arrastre aquí su nueva imagen
    </label>
  </div>
</template>
<template id="thumbnail">
  <div class="uploader-file-thumbnail">
    <div class="card m-0 h-100 elevation-0" style="border: 1px solid rgba(0,0,0,0.125);">
      <div class="row no-gutters">
        <div class="col-md-4 d-flex justify-content-center">
          <img class="card-img" style="height: 80px; width: auto; max-width: 100%" src="" alt="Vista previa">
        </div>
        <div class="col-md-8">
          <div class="card-body" style="min-height: 80px">
            <h5 class="card-title"></h5>
            <p class="card-text"><small class="text-muted"></small></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script type="text/javascript">
  const CONFIG_ENTITY = JSON.parse("<?= addslashes(json_encode($configEntity)) ?>");
</script>

<script defer type="module" src="<?= base_url("public/js/config/business_img.js?v=") . APP_VERSION ?>"></script>