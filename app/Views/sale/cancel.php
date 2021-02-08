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
        "text" => "Cancelar venta",
        "href" => "#",
      ],
    ]
  ]
)
?>

<div class="card pt-3 pb-3">
  <div class="card-body">
    <div class="row">
      <div class="col-md-6">
        <?= form_open("sale/update/cancel-single", "id='sale'") ?>
        <div id="alert"></div>
        <div class="form-group">
          <label for="saleSerial">Número de venta</label>
          <div class="input-group">
            <input autofocus id="saleSerial" name="saleSerial" type="text" class="form-control form-control-sm" placeholder="00001" value="<?= $saleSerial ?? '' ?>" required>
            <div class="input-group-append">
              <button id="checkSale" title="Revisar artículos de esta venta" type="button" class="btn btn-sm btn-default">
                <i class="fas fa-fw fa-cart-arrow-down text-primary"></i>
              </button>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="saleCancelNote">Nota descriptiva</label>
          <textarea class="form-control form-control-sm" id="saleCancelNote" name="saleCancelNote" placeholder="Describa el motivo de la cancelación de la venta" required></textarea>
        </div>

        <div class="form-group d-flex justify-content-end">
          <button type="submit" id="btnSubmitForm" class="btn btn-default btn-sm">
            <i class="fas fa-fw fa-ban text-danger"></i>&nbsp;&nbsp;Cancelar venta
          </button>
        </div>
        <?= form_close() ?>
      </div>

      <div class="col-md-6">
        <ul class="list-group" id="itemsContainer">
          <li class="list-group-item flex-column align-items-start list-group-item-info">
            <div class="d-flex w-100 justify-content-center">
              <h6 class="mb-1 text-uppercase">Artículos vendidos</h6>
            </div>
            <p class="mb-1">Los artículos correspondientes a la venta regresarán al inventario, si alguno de ellos no es apto para vender de nuevo por favor retirelo del inventario manualmente</p>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

<template id="itemsListNotice">
  <li class="list-group-item flex-column align-items-start list-group-item-info">
    <div class="d-flex w-100 justify-content-center">
      <h6 class="mb-1 text-uppercase">Artículos vendidos</h6>
    </div>
    <p class="mb-1">Los artículos correspondientes a la venta regresarán al inventario, si alguno de ellos no es apto para vender de nuevo por favor retirelo del inventario manualmente</p>
  </li>
</template>
<template id="itemsListItem">
  <li class="list-group-item flex-column align-items-start">
    <div class="d-flex w-100 justify-content-between">
      <h6 class="mb-1 text-bold"></h6>
    </div>
    <p class="mb-1"></p>
    <span class="text-bold"></span>
  </li>
</template>

<div id="storage"></div>

<script defer type="module" src="<?= base_url("public/js/sale/cancel.js") ?>"></script>
