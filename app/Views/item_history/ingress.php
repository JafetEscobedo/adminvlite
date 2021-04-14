<?=
view(
  "_shared/partial/app_breadcrumb",
  [
    "links" =>
    [
      [
        "text" => "Inventario",
        "href" => base_url("item-history")
      ], [
        "text" => "Ingreso a invetario",
        "href" => "#"
      ],
    ]
  ]
)
?>

<div class="card pt-3">

  <div class="card-body">
    <div class="row">
      <div class="col-12">
        <div id="alert"></div>
      </div>
    </div>

    <?= form_open('', ["name" => "readItem", "data-uri" => "item/read/single-by-code", "autocomplete" => "false"]) ?>
    <div class="row mb-3">
      <div class="col-md-12">
        <div>
          <b>Artículo:</b>
          <span class="text-muted" id="itemSummary">
            <small><i>Ningún artículo en panel de espera</i></small>
          </span>
        </div>
        <div>
          <b>Existencias:</b>
          <span class="text-muted" id="itemStock">
            <small><i>Información no disponible</i></small>
          </span>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for="itemCode">Código de artículo</label>
          <div class="input-group">
            <div class="input-group-prepend">
              <a target="_blank" href="<?= base_url("item/view/create") ?>" title="Crear artículo" class="btn btn-sm bg-gradient-success">
                <i class="fas fa-fw fa-plus-circle"></i>
              </a>
            </div>
            <input autofocus id="itemCode" type="text" class="form-control form-control-sm" placeholder="12345678910" required>
            <div class="input-group-append">
              <button id="btnValidateItemCode" title="Validar código" type="button" class="btn btn-sm bg-gradient-primary">
                <i class="fas fa-fw fa-check-circle"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="itemHistoryStockOnMove">Cantidad</label>
          <input type="number" min="1" class="form-control form-control-sm" id="itemHistoryStockOnMove" value="" placeholder="10" required>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="itemCost">Costo individual</label>
          <input type="number" step="0.01" min="0" class="form-control form-control-sm" id="itemCost" placeholder="120.00" required>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="itemPrice">Precio individual</label>
          <input type="number" step="0.01" min="0" class="form-control form-control-sm" id="itemPrice" placeholder="140.00" required>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-10">
        <div class="form-group">
          <label for="itemHistoryNote">Nota</label>
          <textarea class="form-control form-control-sm" name="itemHistoryNote" id="itemHistoryNote" rows="1" placeholder="La entrada pertenece a la factura No. FXXXXXX"></textarea>
        </div>
      </div>
      <div class="col-md-2 d-flex flex-column justify-content-end align-items-end">
        <div class="form-group w-100">
          <?= view("_shared/partial/btn_add") ?>
        </div>
      </div>
    </div>
    <?= form_close() ?>

    <div class="row">
      <div class="col-12">
        <table id="ingressBatch" class="w-100 table table-striped table-hover table-sm">
          <thead>
            <tr>
              <th scope="col">Código</th>
              <th scope="col">Artículo</th>
              <th scope="col">Entradas</th>
              <th scope="col">Existencias</th>
              <th scope="col">Costo c/u</th>
              <th scope="col">Precio c/u</th>
              <th scope="col">Últ. Entrada</th>
              <th scope="col"></th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="card-footer">
    <div class="float-right">
      <?= view("_shared/partial/btn_save", ["text" => "Guardar (F2)"]) ?>
    </div>
  </div>
</div>

<script defer type="module" src="<?= base_url("public/js/item_history/ingress.js?v=") . APP_VERSION ?>"></script>