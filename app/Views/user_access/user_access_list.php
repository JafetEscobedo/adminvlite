<?=
view(
  "_shared/partial/app_breadcrumb",
  [
    "links" => [
      [
        "text" => "Acceso",
        "href" => base_url("user-access")
      ],
      [
        "text" => "Bitácora",
        "href" => "#"
      ]
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
    <div class="row mb-3 justify-content-between">
      <div class="col-md-3">
        <div class="form-group">
          <label for="userAccessListLength">Registros</label>
          <select id="userAccessListLength" class="opacity-0">
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>
      </div>

      <div class="col-md-3">
        <div class="form-group">
          <label for="userId">Usuario</label>
          <select id="userId" class="opacity-0"></select>
        </div>
      </div>

      <div class="col-md-3">
        <div class="form-group">
          <label for="userAccessFirst">Fecha</label>
          <input class="form-control form-control-sm" type="date" name="userAccessFirst" id="userAccessFirst">
        </div>
      </div>
    </div>

    <table id="userAccessList" class="w-100 table table-striped table-hover table-sm">
      <thead>
        <tr>
          <th scope="col">Usuario</th>
          <th scope="col">Nombre</th>
          <th scope="col">Apellido</th>
          <th scope="col">Entrada</th>
          <th scope="col">Salida</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
</div>

<script defer type="module" src="<?= base_url("public/js/user_access/user_access_list.js?v=") . APP_VERSION ?>"></script>