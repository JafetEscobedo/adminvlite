<?=
view("_shared/partial/app_breadcrumb", ["links" => [
    [
      "text" => "Rol de usuario",
      "href" => base_url("user-role")
    ], [
      "text" => "Lista de roles",
      "href" => base_url("user-role/view/user-roles-list")
    ], [
      "text" => "Actualizar rol",
      "href" => "#"
    ]
]])
?>

<div class="card pt-3">
  <?= form_open("user-role/update/single", "name='userRole'") ?>

  <div class="card-body">
    <div id="alert"></div>
    <input type="hidden" name="userRoleId">
    <div class="form-group row">
      <label for="userRoleActive" class="col-sm-2 col-form-label">Estatus</label>
      <div class="col-sm-10">
        <select class="opacity-0" name="userRoleActive" id="userRoleActive" required>
          <option value='' selected>Seleccionar estatus de rol</option>
          <option value='y'>Activo</option>
          <option value='n'>Inactivo</option>
        </select>
      </div>
    </div>

    <?= view("_shared/partial/form_user_role") ?>
  </div>
  <div class="card-footer">
    <div class="float-right">
      <?= view("_shared/partial/btn_save", ["text" => "Guardar (F2)"]) ?>
    </div>
  </div>
  <?= form_close() ?>
</div>

<script type="text/javascript">
  const USER_ROLE_ENTITY = JSON.parse("<?= addslashes(json_encode($userRoleEntity)) ?>");
</script>
<script defer type="module" src="<?= base_url("public/js/user_role/update.js") ?>"></script>