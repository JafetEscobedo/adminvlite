<div class="row">
  <div class="col-md-12">
    <div id="timeline" class="timeline">
      <div class="d-flex justify-content-end mr-0">
        <i class="elevation-1 fas fa-fw fa-history bg-purple" style="border-radius: 50% !important"></i>

        <div>
          <select class="opacity-0">
            <option value="desc" <?= $order !== "asc" ? "selected" : '' ?>>Movimientos recientes primero</option>
            <option value="asc" <?= $order === "asc" ? "selected" : '' ?>>Movimientos antiguos primero</option>
          </select>
        </div>
      </div>

      <div class="mr-0 pr-0">
        <i class="fas fa-spin fa-spinner bg-gray elevation-1" style="border-radius: 50% !important"></i>
        <div class="timeline-item mr-0">
          <h3 class="timeline-header">Cargando historial del artículo...</h3>
        </div>
      </div>

      <div>
        <i class="elevation-1 far fa-fw fa-clock bg-purple" style="border-radius: 50% !important"></i>
      </div>
    </div>
  </div>
</div>

<?= view("item/history/template/timeline_start") ?>
<?= view("item/history/template/timeline_item") ?>
<?= view("item/history/template/timeline_empty") ?>
<?= view("item/history/template/timeline_end") ?>