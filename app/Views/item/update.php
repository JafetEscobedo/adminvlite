<?=
view("_shared/partial/app_breadcrumb", ["links" => [
    [
      "text" => "Artículo",
      "href" => base_url("item")
    ], [
      "text" => "Lista de artículos",
      "href" => base_url("item/view/items-list"),
    ], [
      "text" => "Actualizar artículo",
      "href" => "#"
    ]
]])
?>

<div class="card pt-3">

  <?= form_open("item/update/single", ["name" => "item", "autocomplete" => "off"]) ?>

  <div class="card-body">
    <div id="alert"></div>
    <input type="hidden" name="itemId">
    <?= view("_shared/partial/form_item") ?>

    <div class="form-group row">
      <label for="itemActive" class="col-sm-2 col-form-label">Estatus</label>
      <div class="col-sm-10">
        <select class="opacity-0" name="itemActive" id="itemActive" required>
          <option value='' selected>Seleccionar estatus de artículo</option>
          <option value='y'>Activo</option>
          <option value='n'>Inactivo</option>
        </select>
      </div>
    </div>
  </div>
  <div class="card-footer">
    <div class="float-left">
      <?= view("_shared/partial/btn_itemhistory") ?>
    </div>
    <div class="float-right">
      <?= view("_shared/partial/btn_save", ["text" => "Guardar (F2)"]) ?>
    </div>
  </div>

  <?= form_close() ?>
</div>

<script type="text/javascript">
  const ITEM_ENTITY = JSON.parse("<?= addslashes(json_encode($itemEntity)) ?>");
</script>
<script defer type="module" src="<?= base_url("public/js/item/update.js?v=") . APP_VERSION ?>"></script>