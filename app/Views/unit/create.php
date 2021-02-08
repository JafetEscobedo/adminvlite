<?=
view("_shared/partial/app_breadcrumb", ["links" => [
    [
      "text" => "Unidad",
      "href" => base_url("unit")
    ], [
      "text" => "Crear unidad",
      "href" => "#"
    ]
]])
?>

<div class="card pt-3">

  <?= form_open("unit/create/single", "name='unit'") ?>

  <div class="card-body">
    <div id="alert"></div>
    <?= view("_shared/partial/form_unit") ?>
  </div>
  <div class="card-footer">
    <div class="float-right">
      <?= view("_shared/partial/btn_save", ["text" => "Guardar (F2)"]) ?>
    </div>
  </div>

  <?= form_close() ?>
</div>

<script defer type="module" src="<?= base_url("public/js/unit/create.js") ?>"></script>