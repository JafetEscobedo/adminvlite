<!DOCTYPE html>
<html lang="es">

  <?= view("_shared/partial/app_head", ["title" => "Error del Servidor"]) ?>
  <body>
    <section class="content m-md-5 p-md-5 m-sm-3 p-sm-3">
      <div class="error-page">
        <h2 class="headline text-danger"> 500</h2>

        <div class="error-content">
          <h3 class="text-uppercase text-bold"><i class="fas fa-ban text-danger fa-fw"></i>&nbsp;&nbsp;Error del Servidor</h3>

          <p >
            En este momento hay un error en el servidor, podría ser temporal por favor
            <a href="<?= base_url() ?>">regresa al inicio</a> y si el problema persiste contacta al administrador
          </p>
        </div>
      </div>
    </section>
  </body>
</html>
