<?=
view("_shared/partial/app_breadcrumb", ["links" => [
    [
      "text" => "Configuración",
      "href" => base_url("config")
    ],
    [
      "text" => "Nombre de empresa",
      "href" => '#'
    ]
]])
?>

<div class="card pt-3">
  <?= form_open("config/update/business-name", ["name" => "config"]) ?>
  <div class="card-body">
    <div class="row">
      <div class="col-12"><div id="alert"></div></div>
    </div>

    <div class="form-group row">
      <label for="configBusinessName" class="col-sm-3 col-form-label">Nombre de empresa</label>
      <div class="col-sm-9">
        <input autofocus class="form-control form-control-sm" name="configBusinessName" placeholder="<?= session("business_name") ?>" required type="text">
      </div>
    </div>

    <div class="form-group row">
      <label for="configBusinessNameUc" class="col-sm-3 col-form-label">Nombre en mayúsculas</label>
      <div class="col-sm-9">
        <select class="opacity-0" name="configBusinessNameUc" id="configBusinessNameUc" required>
          <option value='' selected>Seleccionar opción</option>
          <option value='y'>Sí</option>
          <option value='n'>No</option>
        </select>
      </div>
    </div>
  </div>
  <div class="card-footer">
    <div class="float-right">
      <?= view("_shared/partial/btn_save", ["text" => "Guardar (F2)"]) ?>
    </div>
  </div>

  <?= form_close() ?>
</div>

<template id="dropzone">
  <div class="uploader-file-thumbnail">
    <label class="uploader-file-add" style="font-weight: normal">
      <i class="far fa-fw fa-file-image text-success"></i>&nbsp;&nbsp;Arrastre aquí su nueva imagen
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

<script defer type="module" src="<?= base_url("public/js/config/business_name.js") ?>"></script>