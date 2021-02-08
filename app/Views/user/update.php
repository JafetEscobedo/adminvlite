<?=
view("_shared/partial/app_breadcrumb", ["links" => [
    [
      "text" => "Usuario",
      "href" => base_url("user")
    ], [
      "text" => "Lista de usuarios",
      "href" => base_url("user/view/users-list")
    ],
]])
?>

<div class="card pt-3">

  <?= form_open("user/update/single", "name='user'") ?>

  <div class="card-body">
    <div id="alert"></div>
    <input type="hidden" name="userId", id="userId">

    <?= view("_shared/partial/form_user") ?>

    <div class="form-group row">
      <label for="userActive" class="col-sm-2 col-form-label">Estatus</label>
      <div class="col-sm-10">
        <select class="opacity-0" name="userActive" id="userActive" required>
          <option value='' selected>Seleccionar estatus de usuario</option>
          <option value='y'>Activo</option>
          <option value='n'>Inactivo</option>
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
  const USER_ENTITY = JSON.parse("<?= addslashes(json_encode($userEntity)) ?>");
</script>
<script defer type="module" src="<?= base_url("public/js/user/update.js") ?>"></script>