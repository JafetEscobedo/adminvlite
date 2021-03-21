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
        "text" => "Venta nueva",
        "href" => "#",
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
    <?= form_open('', ["name" => "readItem", "data-uri" => "item/read/single-by-code", "autocomplete" => "off"]) ?>
    <div class="row">
      <div class="col-md-2">
        <div class="form-group">
          <label for="itemHistoryStockOnMove">Cantidad</label>
          <input type="number" min="1" class="form-control form-control-sm" id="itemHistoryStockOnMove" value="1" placeholder="1" required>
        </div>
      </div>
      <div class="col-md-8">
        <div class="form-group">
          <label for="itemCode">Código de artículo</label>
          <input autofocus id="itemCode" type="text" class="form-control form-control-sm" placeholder="12345678910" required>
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
        <table id="itemsBatch" class="w-100 table table-striped table-hover table-sm">
          <thead>
            <tr>
              <th scope="col">Código</th>
              <th scope="col">Artículo</th>
              <th scope="col">Cantidad</th>
              <th scope="col">Precio c/u</th>
              <th scope="col">Precio Subtotal</th>
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
      <?= view("_shared/partial/btn_save", ["text" => "Terminar Venta (F2)"]) ?>
    </div>
  </div>
</div>

<div class="modal fade" id="modalConfirm" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <?= form_open("sale/create/single-using-batch", ["name" => "confirmSale", "autocomplete" => "off"]) ?>
      <div class="modal-header">
        <h5 class="modal-title">Confirmación de Venta</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12">
            <div id="alertToConfirmSale"></div>
          </div>
        </div>
        <div class="form-group row">
          <label for="totalToPay" class="col-sm-4 col-form-label text-uppercase d-flex align-items-center">Total:</label>
          <div class="col-sm-8">
            <input type="text" readonly class="text-bold text-success form-control-plaintext form-control-lg" name="totalToPay" id="totalToPay" value="">
          </div>
        </div>
        <div class="form-group row">
          <label for="cash" class="col-sm-4 col-form-label text-uppercase d-flex align-items-center">Efectivo:</label>
          <div class="col-sm-8">
            <input type="number" step="0.01" class="text-bold form-control form-control-lg" name="cash" id="cash" required>
          </div>
        </div>
        <div class="form-group row">
          <label for="cashBack" class="col-sm-4 col-form-label text-uppercase d-flex align-items-center">Cambio</label>
          <div class="col-sm-8">
            <input type="text" readonly class="text-danger text-bold form-control-plaintext form-control-lg" name="cashBack" id="cashBack" value="">
          </div>
        </div>
        <div class="form-group row">
          <label for="printSaleNote" class="col-sm-4 col-form-label text-uppercase d-flex align-items-center cursor-pointer">Imprimir nota</label>
          <div class="col-sm-8 d-flex align-items-center">
            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input" id="printSaleNote">
              <label class="custom-control-label" for="printSaleNote"></label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <?= view("_shared/partial/btn_cancel") ?>
        <?= view("_shared/partial/btn_save", ["id" => "btnConfirm", "text" => "Confirmar"]) ?>
      </div>
      <?= form_close() ?>
    </div>
  </div>
</div>

<div id="storage"></div>

<script defer type="module" src="<?= base_url("public/js/sale/create.js?v=") . APP_VERSION ?>"></script>