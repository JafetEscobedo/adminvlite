<?php helper("number") ?>

<table border="0" style="font-family: 'Courier New', monospace, consolas; width: 7cm; margin:auto; table-layout: fixed; font-size: 0.85rem">
  <thead>
    <tr>
      <th colspan="3" style="text-align: center; font-size: 1.2rem">Venta <?= $saleEntity->saleSerial ?></th>
    </tr>
    <tr>
      <th colspan="3" style="text-align: center; padding-bottom: 0.5cm">¡Gracias por su compra!</th>
    </tr>
    <tr>
      <th style="text-align: left; word-wrap: break-word">Artículo</th>
      <th style="text-align: right; word-wrap: break-word">Cantidad</th>
      <th style="text-align: right; word-wrap: break-word">Subtotal</th>
    </tr>
  </thead>
  <tbody>
  <col width="3cm" />
  <col width="2cm" />
  <col width="2cm" />

  <?php foreach ($saleEntity->getSaleDetails() as $details) : ?>
    <tr>
      <td style="text-align: left; word-wrap: break-word; vertical-align: top">
        <?= $details->itemName; ?>
      </td>
      <td style="text-align: right; word-wrap: break-word; vertical-align: top">
        <?= $details->saleDetailStockOnMove ?> <?= (int) $details->saleDetailStockOnMove === 1 ? $details->unitSingularName : $details->unitPluralName ?>
      </td>
      <td style="text-align: right; word-wrap: break-word; vertical-align: top">
        <?= number_to_currency($details->saleDetailStockOnMove * $details->saleDetailItemPrice, "MXN", "es_MX", 2) ?>
      </td>
    </tr>
  <?php endforeach ?>

</tbody>
<tfoot>
  <tr>
    <td colspan="2" style="text-align: right; font-weight: bold; padding-top: 0.5cm">Total:</td>
    <td style="text-align: right; padding-top: 0.5cm"><?= $totalToPay ?></td>
  </tr>
  <tr>
    <td colspan="2" style="text-align: right; font-weight: bold">Efectivo:</td>
    <td style="text-align: right"><?= $cash ?></td>
  </tr>
  <tr>
    <td colspan="2" style="text-align: right; font-weight: bold">Cambio:</td>
    <td style="text-align: right"><?= $cashBack ?></td>
  </tr>
  <tr>
    <td colspan="3" style="text-align: center; padding-top: 1cm; font-weight: bold"><?= date('Y-m-d h:i:s a', strtotime($saleEntity->saleCreatedAt)) ?></td>
  </tr>
</tfoot>
</table>