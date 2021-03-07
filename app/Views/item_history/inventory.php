<?=
view("_shared/partial/app_breadcrumb", ["links" => [
    [
      "text" => "Inventario",
      "href" => base_url("item-history"),
    ], [
      "text" => "Artículos",
      "href" => "#",
    ],
]])
?>

<div class="row">
  <div class="col-md-3 col-12">
    <div class="info-box">
      <span class="info-box-icon bg-info"><i class="fas fa-barcode"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Artículos diferentes</span>
        <span class="info-box-number" id="totalItems"><?= view("_shared/partial/app_loader") ?></span>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-12">
    <div class="info-box">
      <span class="info-box-icon bg-success"><i class="fas fa-dollar-sign"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Costo de existencias</span>
        <span class="info-box-number" id="totalCost"><?= view("_shared/partial/app_loader") ?></span>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-12">
    <div class="info-box">
      <span class="info-box-icon bg-warning"><i class="fas fa-dollar-sign"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Precio de existencias</span>
        <span class="info-box-number" id="totalPrice"><?= view("_shared/partial/app_loader") ?></span>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-12">
    <div class="info-box">
      <span class="info-box-icon bg-purple"><i class="fas fa-dollar-sign"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Margen de ganancias</span>
        <span class="info-box-number" id="totalEarning"><?= view("_shared/partial/app_loader") ?></span>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="row">
      <div class="col-12">
        <div id="alert"></div>
      </div>
    </div>
    <div class="row mb-3 justify-content-between">
      <div class="col-md-3">
        <div class="form-group">
          <select id="inventoryTableLength" class="opacity-0">
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
            <input autofocus class="form-control form-control-sm" id="inventoryTableSearch" type="search" placeholder="Buscar">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-search fa-fw fa-sm"></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <table id="inventoryTable" class="w-100 table table-striped table-hover table-sm">
      <thead>
        <tr>
          <th scope="col">Código</th>
          <th scope="col">Nombre</th>
          <th scope="col">Costo</th>
          <th scope="col">Precio</th>
          <th scope="col">Existencias</th>
          <th scope="col">Últ. Entrada</th>
          <th scope="col">Últ. Salida</th>
          <th scope="col">Acciones</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
</div>

<script defer type="module" src="<?= base_url("public/js/item_history/inventory.js?v=") . APP_VERSION ?>"></script>