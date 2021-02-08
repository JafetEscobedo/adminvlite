<?=
view(
  "_shared/partial/app_breadcrumb",
  [
    "links" =>
    [
      [
        "text" => "Venta",
        "href" => base_url("sale"),
      ],
      [
        "text" => "Historial",
        "href" => "#",
      ],
    ]
  ]
)
?>

<div class="row">
  <div class="col-md-4 col-12">
    <div class="info-box">
      <span class="info-box-icon bg-info"><i class="fas fas fa-dollar-sign"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Costo de ventas</span>
        <span class="info-box-number" id="salesTotalCost"><?= view("_shared/partial/app_loader") ?></span>
      </div>
    </div>
  </div>
  <div class="col-md-4 col-12">
    <div class="info-box">
      <span class="info-box-icon bg-success"><i class="fas fa-dollar-sign"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Precio de ventas</span>
        <span class="info-box-number" id="salesTotalPrice"><?= view("_shared/partial/app_loader") ?></span>
      </div>
    </div>
  </div>
  <div class="col-md-4 col-12">
    <div class="info-box">
      <span class="info-box-icon bg-warning"><i class="fas fa-dollar-sign"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Ganancia generada</span>
        <span class="info-box-number" id="salesTotalEarning"><?= view("_shared/partial/app_loader") ?></span>
      </div>
    </div>
  </div>
</div>

<div class="card pt-3">
  <div class="card-body">
    <div class="row">
      <div class="col-12">
        <div id="alert"></div>
      </div>
    </div>
    <div class="row justify-content-between mb-0">
      <div class="col-md-1">
        <div class="form-group">
          <label for="salesListLength">Registros</label>
          <select id="salesListLength" class="opacity-0">
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="salesListStatus">Estado</label>
          <select id="salesListStatus" class="opacity-0">
            <option value=''>Cualquiera</option>
            <option value="canceled">Canceladas</option>
            <option value="not_canceled">No canceladas</option>
          </select>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          <label class="d-flex" for="salesListStartDate">
            <span class="w-50">Vendido entre</span>
            <span class="w-50">y</span>
          </label>
          <div class="input-group">
            <input id="salesListStartDate" type="date" class="form-control form-control-sm">
            <input id="salesListFinalDate" type="date" class="form-control form-control-sm">
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="form-group">
          <label for="salesListSearch">Busqueda general</label>
          <div class="input-group">
            <input id="salesListSearch" type="search" class="form-control form-control-sm" placeholder="Escriba..." autofocus>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-search fa-fw fa-sm"></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <table id="salesList" class="w-100 table table-striped table-hover table-sm">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Usuario</th>
          <th scope="col">Estado</th>
          <th scope="col">Costo</th>
          <th scope="col">Precio</th>
          <th scope="col">Anotación</th>
          <th scope="col">Vendido</th>
          <th scope="col">Cancelado</th>
          <th scope="col">Acciones</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
</div>

<div id="saleDetailsModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detalles de Venta</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="alertToSaleDetailsList"></div>
        <div class="table-responsive">
          <table id="saleDetailsList" class="w-100 table table-striped table-hover teble-sm">
            <thead>
              <tr>
                <th scope="col">Artículo</th>
                <th scope="col">Nombre</th>
                <th scope="col">Descripción</th>
                <th scope="col">Unidades</th>
                <th scope="col">Costo c/u</th>
                <th scope="col">Precio c/u</th>
                <th scope="col">Subtotal costo</th>
                <th scope="col">Subtotal precio</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script defer type="module" src="<?= base_url("public/js/sale/sales_list.js") ?>"></script>