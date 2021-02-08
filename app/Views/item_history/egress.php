<?=
view("_shared/partial/app_breadcrumb", ["links" => [
    [
      "text" => "Inventario",
      "href" => base_url("item-history")
    ], [
      "text" => "Egreso de inventario",
      "href" => "#"
    ],
]])
?>

<div class="card pt-3">

  <div class="card-body">
    <div class="row">
      <div class="col-12">
        <div id="alert"></div>
      </div>
    </div>

    <?= form_open('', ["name" => "readItem", "data-uri" => "item/read/single-by-code"]) ?>
    <div class="row">
      <div class="col-md-5">
        <div class="form-group">
          <label for="itemCode">Código de artículo</label>
          <input autofocus id="itemCode" type="text" class="form-control form-control-sm" placeholder="12345678910" required>
        </div>
      </div>
      <div class="col-md-5">
        <div class="form-group">
          <label for="itemHistoryEventId">Motivo de salida</label>
          <select name="itemHistoryEventId" id="itemHistoryEventId" class="opacity-0" required>
            <?php foreach ($events as $event): ?>
              <option value="<?= $event->itemHistoryEventId ?>"><?= $event->itemHistoryEventName ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="itemHistoryStockOnMove">Cantidad</label>
          <input type="number" class="form-control form-control-sm" id="itemHistoryStockOnMove" value="" placeholder="10" required>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-10">
        <div class="form-group">
          <label for="itemHistoryNote">Nota</label>
          <textarea class="form-control form-control-sm" name="itemHistoryNote" id="itemHistoryNote" rows="1" placeholder="Cliente notó que estos artículos se encontraban caducados" required></textarea>
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
        <table id="egressBatch" class="w-100 table table-striped table-hover table-sm">
          <thead>
            <tr>
              <th scope="col">Código</th>
              <th scope="col">Artículo</th>
              <th scope="col">Salidas</th>
              <th scope="col">Existencias</th>
              <th scope="col">Costo c/u</th>
              <th scope="col">Precio c/u</th>
              <th scope="col">Últ. Salida manual</th>
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

<script defer type="module" src="<?= base_url("public/js/item_history/egress.js") ?>"></script>