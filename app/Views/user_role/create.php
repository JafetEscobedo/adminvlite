<?=
view("_shared/partial/app_breadcrumb", ["links" => [
    [
      "text" => "Rol de usuario",
      "href" => base_url("user-role")
    ], [
      "text" => "Crear rol",
      "href" => "#"
    ]
]])
?>

<div class="card pt-3">

  <?= form_open("user-role/create/single", "name='userRole'") ?>

  <div class="card-body">
    <div id="alert"></div>
    <?= view("_shared/partial/form_user_role") ?>
  </div>
  <div class="card-footer">
    <div class="float-right">
      <?= view("_shared/partial/btn_save", ["text" => "Guardar (F2)"]) ?>
    </div>
  </div>

  <?= form_close() ?>
</div>

<script defer type="module" src="<?= base_url("public/js/user_role/create.js") ?>"></script>