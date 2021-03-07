<?=
view("_shared/partial/app_breadcrumb", ["links" => [
    [
      "text" => "Rol de usuario",
      "href" => base_url("user-role")
    ], [
      "text" => "Lista de roles",
      "href" => "#"
    ]
]])
?>

<div class="card pt-3">
  <div class="card-body">
    <div class="row">
      <div class="col-12">
        <div id="alert"></div>
      </div>
    </div>
    <div class="row mb-3 justify-content-between">
      <div class="col-md-3">
        <div class="form-group">
          <select id="userRolesListLength" class="opacity-0">
            <option value="10">10 registros</option>
            <option value="20">20 registros</option>
            <option value="50">50 registros</option>
            <option value="100">100 registros</option>
          </select>
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          <div class="input-group">
            <div class="input-group-prepend">
              <a href="<?= base_url("user-role/view/create") ?>" title="Crear unidad" class="btn btn-sm bg-gradient-success">
                <i class="fas fa-fw fa-plus-circle"></i>
              </a>
            </div>
            <input autofocus id="userRolesListSearch" type="search" class="form-control form-control-sm" placeholder="Buscar por nombre">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-search fa-fw fa-sm"></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <table id="userRolesList" class="w-100 table table-striped table-hover table-sm">
      <thead>
        <tr>
          <th scope="col">Nombre</th>
          <th scope="col">Estatus</th>
          <th scope="col">Accesos</th>
          <th scope="col">Creado</th>
          <th scope="col">Actualizado</th>
          <th scope="col">Inactivado</th>
          <th scope="col">Acciones</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
</div>

<script defer type="module" src="<?= base_url("public/js/user_role/user_roles_list.js?v=") . APP_VERSION ?>"></script>