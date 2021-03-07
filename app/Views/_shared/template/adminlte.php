<?php
// Obtener controlador y método de la solicitud http
$request  = service("request");
$segment1 = $request->uri->getSegment(1) . " | ";
$segment2 = $request->uri->getSegment(2) . " | ";
$segment3 = $request->uri->getSegment(3) . (empty($request->uri->getSegment(4)) ? '' : " | ");
$segment4 = $request->uri->getSegment(4) ?? '';
?>

<!DOCTYPE html>
<html lang="es">

  <head>
    <?= view("_shared/partial/app_head", ["title" => ucwords($segment1, '-') . ucwords($segment2, '-') . ucwords($segment3, '-') . ucwords($segment4, '-')]) ?>
  </head>

  <body class="hold-transition sidebar-mini layout-fixed text-sm">
    <div class="wrapper">
      <nav class="main-header navbar navbar-expand navbar-light navbar-white">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
          </li>
        </ul>

        <ul class="navbar-nav ml-auto">
          <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
              <img src="<?= base_url("public/img/avatar.png") ?>" class="user-image img-circle elevation-1" alt="User Image">
              <span class="d-none d-md-inline"><?= session("user_name") ?> <?= session("user_surname") ?></span>
            </a>

            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
              <li class="user-header">
                <img src="<?= base_url("public/img/avatar.png") ?>" class="img-circle elevation-1" alt="User Image">

                <p><?= session("user_nickname") ?></p>
              </li>
              <li class="user-footer">
                <a href="<?= base_url("offsite/action/logout") ?>" class="btn btn-default btn-sm float-right">
                  <i class="fas fa-fw fa-sign-out-alt text-danger"></i>&nbsp;&nbsp;Salir
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>

      <aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
        <a href="<?= base_url() ?>" class="brand-link text-center">
          <span class="brand-text font-weight-light <?= session("business_name_uc") == 'y' ? "text-uppercase" : '' ?>">
            <?= session("business_name") ?>
          </span>
        </a>

        <div class="sidebar">
          <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent nav-legacy" data-widget="treeview" role="menu" data-accordion="true">

              <?php foreach ($templateMenu as $link): ?>
                <?php if (in_array($link["path"], json_decode(session("user_role_access")))): ?>
                  <li class="nav-item <?= preg_match('/' . preg_quote($link["path"] . '/', '/') . '/', uri_string()) ? "has-treeview menu-open" : '' ?>">
                    <a href="<?= base_url($link["path"]) ?>" class="nav-link <?= preg_match('/' . preg_quote($link["path"] . '/', '/') . '/', uri_string()) ? "active" : '' ?>">
                      <i class="nav-icon <?= $link["icon"] ?>"></i>
                      <p>
                        <?= $link["name"] ?><i class="right fas fa-angle-left"></i>
                      </p>
                    </a>

                    <?php if (empty($link["menu"])): ?>
                      <?php continue ?>
                    <?php endif ?>

                    <ul class="nav nav-treeview">
                      <?php foreach ($link["menu"] as $_link): ?>
                        <?php if (in_array($_link["path"], json_decode(session("user_role_access")))): ?>
                          <li class="nav-item">
                            <a href="<?= base_url($_link["path"]) ?>" class="nav-link <?= preg_match('/' . preg_quote($_link["path"], '/') . '/', uri_string()) ? "active" : '' ?>">
                              <i class="nav-icon <?= $_link["icon"] ?>"></i>
                              <p><?= $_link["name"] ?></p>
                            </a>
                          </li>
                        <?php endif ?>
                      <?php endforeach ?>
                    </ul>
                  </li>
                <?php endif ?>
              <?php endforeach ?>
            </ul>
          </nav>
        </div>
      </aside>

      <div class="content-wrapper">
        <section class="content">
          <div class="<?= empty($templateFluid) ? "container" : "container-fluid" ?>">
            <?= $templateContent ?>
          </div>
        </section>
      </div>

      <footer class="main-footer">
        <strong>Copyright &copy; 2021 <a href="#">AdminvLite</a></strong>
        <div class="float-right d-none d-sm-inline-block">
          <b>Versión</b> 1.0.1
        </div>
      </footer>
    </div>

    <?= view("_shared/partial/app_loading") ?>
  </body>

</html>