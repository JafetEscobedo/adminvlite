<?=
view("_shared/partial/app_breadcrumb", ["links" => [
    [
      "text" => "Unidad",
      "href" => base_url("unit")
    ], [
      "text" => "Lista de unidades",
      "href" => base_url("unit/view/units-list")
    ], [
      "text" => "Actualizar unidad",
      "href" => "#"
    ]
]])
?>

<div class="card pt-3">

  <?= form_open("unit/update/single", ["name" => "unit", "autocomplete" => "off"]) ?>

  <div class="card-body">
    <div id="alert"></div>
    <input type="hidden" name="unitId">

    <?= view("_shared/partial/form_unit") ?>

    <div class="form-group row">
      <label for="unitActive" class="col-sm-2 col-form-label">Estatus</label>
      <div class="col-sm-10">
        <select class="opacity-0" name="unitActive" id="unitActive" required>
          <option value='' selected>Seleccionar estatus de unidad</option>
          <option value='y'>Activa</option>
          <option value='n'>Inactiva</option>
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

<script type="text/javascript">
  const UNIT_ENTITY = JSON.parse("<?= addslashes(json_encode($unitEntity)) ?>");
</script>
<script defer type="module" src="<?= base_url("public/js/unit/update.js?v=") . APP_VERSION ?>"></script>