<?=
view("_shared/partial/app_breadcrumb", ["links" => [
    [
      "text" => "Artículo",
      "href" => base_url("item")
    ], [
      "text" => "Lista de artículos",
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
          <select id="itemsListLength" class="opacity-0">
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
              <a href="<?= base_url("item/view/create") ?>" title="Crear artículo" class="btn btn-sm bg-gradient-success">
                <i class="fas fa-fw fa-plus-circle"></i>
              </a>
            </div>
            <input autofocus class="form-control form-control-sm" id="itemsListSearch" type="search" placeholder="Buscar">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-search fa-fw fa-sm"></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <table id="itemsList" class="w-100 table table-striped table-hover table-sm">
      <thead>
        <tr>
          <th scope="col">Código</th>
          <th scope="col">Nombre</th>
          <th scope="col">Descripción</th>
          <th scope="col">Costo</th>
          <th scope="col">Precio</th>
          <th scope="col">Estatus</th>
          <th scope="col">Marca</th>
          <th scope="col">Categoría</th>
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

<script defer type="module" src="<?= base_url("public/js/item/items_list.js?v=") . APP_VERSION ?>"></script>