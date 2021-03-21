<!doctype html>
<html lang="es">
  <head>
    <?= view("_shared/partial/app_head", ["title" => "Acceso al sistema"]) ?>
  </head>

  <body class="hold-transition login-page text-sm">
    <div class="login-box">
      <div class="card card-outline card-primary">
        <div class="card-header border-bottom-0 pb-0">
          <p class="text-center w-100 mb-0">
            <img src="<?= base_url("public/img/config/" . session("business_logo")) ?>" alt="Logotipo de Empresa" height="80">
          </p>
        </div>
        <div class="card-body">
          <p class="<?= session("business_name_uc") === 'y' ? "text-uppercase" : '' ?> login-box-msg h6">
            <?= session("business_name") ?>
          </p>

          <?= form_open("offsite/action/login", ["name" => "login", "autocomplete" => "off"]) ?>
          <div id="alert"></div>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
            <input type="text" name="userNickname" class="form-control form-control-sm" placeholder="Usuario" required autofocus>
          </div>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <div class="input-group-text">
                <span class="fas fa-key"></span>
              </div>
            </div>
            <input type="password" name="userPassword" class="form-control form-control-sm" placeholder="Contraseña" required>
          </div>

          <div class="row">
            <div class="col-12">
              <button type="submit" class="btn btn-sm bg-gradient-primary float-right">
                <i class="fas fa-fw fa-unlock"></i>&nbsp;&nbsp;Acceder
              </button>
            </div>
          </div>
          <?= form_close() ?>
        </div>

        <div class="card-footer">
          <p class="small text-center text-muted"><strong>Copyright &copy; 2020 <a href="#">AdminvLite</a></strong></p>
        </div>
      </div>
    </div>

    <?= view("_shared/partial/app_loading") ?>

    <script defer type="module" src="<?= base_url("public/js/offsite/login.js?v=") . APP_VERSION ?>"></script>
  </body>
</html>
