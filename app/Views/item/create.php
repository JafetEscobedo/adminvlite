<?=
view("_shared/partial/app_breadcrumb", ["links" => [
    [
      "text" => "Artículo",
      "href" => base_url("item")
    ], [
      "text" => "Crear artículo",
      "href" => "#"
    ]
]])
?>

<div class="card pt-3">

  <?= form_open("item/create/single", "name='item'") ?>

  <div class="card-body">
    <div id="alert"></div>
    <?= view("_shared/partial/form_item") ?>
  </div>

  <div class="card-footer">
    <div class="float-right">
      <?= view("_shared/partial/btn_save", ["text" => "Guardar (F2)"]) ?>
    </div>
  </div>

  <?= form_close() ?>
</div>

<script defer type="module" src="<?= base_url("public/js/item/create.js") ?>"></script>