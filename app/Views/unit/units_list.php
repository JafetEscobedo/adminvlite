<?=
view("_shared/partial/app_breadcrumb", ["links" => [
    [
      "text" => "Unidad",
      "href" => base_url("unit")
    ], [
      "text" => "Lista de unidades",
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
          <select id="unitsListLength" class="opacity-0">
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
              <a href="<?= base_url("unit/view/create") ?>" title="Crear unidad" class="btn btn-sm bg-success">
                <i class="fas fa-fw fa-plus-circle"></i>
              </a>
            </div>
            <input autofocus id="unitsListSearch" type="search" class="form-control form-control-sm" placeholder="Buscar">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-search fa-fw fa-sm"></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <table id="unitsList" class="w-100 table table-striped table-hover table-sm">
      <thead>
        <tr>
          <th scope="col">Singular</th>
          <th scope="col">Plural</th>
          <th scope="col">Estatus</th>
          <th scope="col">Creada</th>
          <th scope="col">Actualizada</th>
          <th scope="col">Inactivada</th>
          <th scope="col">Acciones</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
</div>

<script defer type="module" src="<?= base_url("public/js/unit/units_list.js?v=") . APP_VERSION ?>"></script>