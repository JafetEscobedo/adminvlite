<!DOCTYPE html>
<html lang="es">

  <?= view("_shared/partial/app_head", ["title" => "Página no encontrada"]) ?>
  <body>
    <section class="content m-md-5 p-md-5 m-sm-3 p-sm-3">
      <div class="error-page">
        <h2 class="headline text-warning"> 404</h2>

        <div class="error-content">
          <h3 class="text-uppercase text-bold"><i class="fas fa-exclamation-triangle text-warning fa-fw"></i>&nbsp;&nbsp;Página no encontrada</h3>

          <p >
            La página a la que intentaste acceder no existe o no tienes permiso para acceder a ella.
            Si lo deseas puedes <a href="<?= base_url() ?>">regresar al inicio</a> o intentar contactar al administrador
          </p>
        </div>
      </div>
    </section>
  </body>
</html>
