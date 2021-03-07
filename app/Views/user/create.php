<?=
view("_shared/partial/app_breadcrumb",
     ["links" => [
      [
        "text" => "Usuario",
        "href" => base_url("user")
      ], [
        "text" => "Crear usuario",
        "href" => "#"
      ],
  ]])
?>

<div class="card pt-3">

  <?= form_open("user/create/single", ["name" => "user", "autocomplete" => "off"]) ?>

  <div class="card-body">
    <div id="alert"></div>
    <?= view("_shared/partial/form_user") ?>
  </div>
  <div class="card-footer">
    <div class="float-right">
      <?= view("_shared/partial/btn_save", ["text" => "Guardar (F2)"]) ?>
    </div>
  </div>

  <?= form_close() ?>
</div>

<script defer type="module" src="<?= base_url("public/js/user/create.js?v=") . APP_VERSION ?>"></script>