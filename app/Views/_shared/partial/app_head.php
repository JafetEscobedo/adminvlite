<title><?= $title ?></title>

<!-- Metadatos -->
<?= csrf_meta() ?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Administrador de Inventario Ligero ADMINVLITE">

<!-- Favicon de la aplicación -->
<link rel="shortcut icon" type="<?= session("favicon_mime") ?>" href="<?= base_url("public/img/config/" . session("business_icon")) ?>">

<!-- Validar navegador compatible o detener ejecución -->
<script type="text/javascript" src="<?= base_url("public/js/_shared/validate_browser.js") ?>"></script>

<!-- Estilos generales -->
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.21/af-2.3.5/b-1.6.3/b-colvis-1.6.3/b-flash-1.6.3/b-html5-1.6.3/b-print-1.6.3/cr-1.5.2/fc-3.3.1/fh-3.1.7/kt-2.5.2/r-2.2.5/rg-1.1.2/rr-1.2.7/sc-2.0.2/sp-1.1.1/sl-1.3.1/datatables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/1.12.0/css/OverlayScrollbars.min.css" integrity="sha512-fWgz39WeLATUXnAhIimPHnEUoSuZh5cgcOTteRFeVPW1opB0OPQ2/JwPAAWMDZtHy8IomUEOyaYJsOdCM9tcYg==" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog==" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" integrity="sha512-arEjGlJIdHpZzNfZD2IidQjDZ+QY9r4VFJIm2M/DhXLjvvPyXFj+cIotmo0DLgvL3/DOlIaEDwzEiClEPQaAFQ==" crossorigin="anonymous" />
<link rel="stylesheet" type="text/css" href="<?= base_url("public/src/adminlte/css/adminlte.min.css") ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url("public/css/_shared/app.css") ?>">

<!-- Scripts generales -->
<script defer type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
<script defer type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js" integrity="sha384-1CmrxMRARb6aLqgBO7yyAxTOQE2AKb9GfXnEo760AUcUmFx3ibVJJAzGytlQcNXd" crossorigin="anonymous"></script>
<script defer type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.min.js" integrity="sha512-VZ6m0F78+yo3sbu48gElK4irv2dzPoep8oo9LEjxviigcnnnNvnTOJRSrIhuFk68FMLOpiNz+T77nNY89rnWDg==" crossorigin="anonymous"></script>
<script defer type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/1.12.0/js/jquery.overlayScrollbars.min.js" integrity="sha512-Z8BrxiEnf+IbffE67XlYCq7k8x8/vF7n8AzmW0TZSPvsAj5DlSceYILP1KOvyA3FcllxyJbdZcWEUdbs1IeerA==" crossorigin="anonymous"></script>
<script defer type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js" integrity="sha512-ePSfiGQMIzYzXVQLqWoVC3yxVEHIM5Y3EGh9jPNxpf+hPuLtzPdxJX+lTC3ziPMlDgc5OsM4JThxGwN2DkWEeA==" crossorigin="anonymous"></script>
<script defer type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.20/lodash.min.js" integrity="sha512-90vH1Z83AJY9DmlWa8WkjkV79yfS2n2Oxhsi2dZbIv0nC4E6m5AbH8Nh156kkM7JePmqD6tcZsfad1ueoaovww==" crossorigin="anonymous"></script>
<script defer type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.28.0/moment-with-locales.min.js" integrity="sha512-kcvf1mExE8WCOLBL5re/9hLUHfaj8+LQrKlupTarmme+rwv8asLK4q6Ge32trTMBElPBP5sll4czZKNvps0VvA==" crossorigin="anonymous"></script>
<script defer type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.31/moment-timezone-with-data-10-year-range.min.js" integrity="sha512-Rb9RCtecTEK3SdnnQhrZx4GM1ascb2CNHybgugRDTriP/b1As79OemxeIT5qs6RMJ/fCpeJrDjtpASh7I7EKMQ==" crossorigin="anonymous"></script>
<script defer type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.full.min.js" integrity="sha512-m7x59G4+NdYoUUKUscYq2qKkineVwmjXA/7WfXm8pukxYiFavrh9uFImpPtbmZGAnHR0rouVWWk+dgcHNurQ5g==" crossorigin="anonymous"></script>
<script defer type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/i18n/es.min.js" integrity="sha512-xntXNPHoIOoLxuqmYhDB6MA67yimB0HxKb20FTgBcAO7RUk2jwctNYIkencPjG4hdxde8ee6FHqACJqGYYSiSg==" crossorigin="anonymous"></script>
<script defer type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/react/16.13.1/umd/react.production.min.js" integrity="sha512-SUJujhtUWZUlwsABaZNnTFRlvCu7XGBZBL1VF33qRvvgNk3pBS9E353kcag4JAv05/nsB9sanSXFbdHAUW9+lg==" crossorigin="anonymous"></script>
<script defer type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/react-dom/16.13.1/umd/react-dom.production.min.js" integrity="sha512-SYsXmAblZhruCNUVmTp5/v2a1Fnoia06iJh3+L9B9wUaqpRVjcNBQsqAglQG9b5+IaVCfLDH5+vW923JL5epZA==" crossorigin="anonymous"></script>
<script defer type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js" integrity="sha512-nOQuvD9nKirvxDdvQ9OMqe2dgapbPB7vYAMrzJihw5m+aNcf0dX53m6YxM4LgA9u8e9eg9QX+/+mPu8kCNpV2A==" crossorigin="anonymous"></script>
<script defer type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js" integrity="sha512-RdSPYh1WA6BF0RhpisYJVYkOyTzK4HwofJ3Q7ivt/jkpW6Vc8AurL1R+4AUcvn9IwEKAPm/fk7qFZW3OuiUDeg==" crossorigin="anonymous"></script>
<script defer type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js" integrity="sha512-dRUcT5xAexUevhjrE5AvW+pen2BQizPSwJ2w1wAW5lbQvUYhZcz/aWmDt0ROdo5iJnT43foWTMrNzIrwkAlOEg==" crossorigin="anonymous"></script>
<script defer type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js" integrity="sha512-SfPW3mj98QDwMeCdls0QDKGO2dpOFI4/rNhWnbtod+bP1Ke9k4IeTZPv6PHKBjmGMzu2FRWC/nDY7y9HdS6iRQ==" crossorigin="anonymous"></script>
<script defer type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.21/af-2.3.5/b-1.6.3/b-colvis-1.6.3/b-flash-1.6.3/b-html5-1.6.3/b-print-1.6.3/cr-1.5.2/fc-3.3.1/fh-3.1.7/kt-2.5.2/r-2.2.5/rg-1.1.2/rr-1.2.7/sc-2.0.2/sp-1.1.1/sl-1.3.1/datatables.min.js"></script>
<script defer type="text/javascript" src="<?= base_url("public/src/adminlte/js/adminlte.min.js") ?>"></script>
<script defer type="text/javascript" src="<?= base_url("public/js/_shared/init.js") ?>"></script>

<!-- Guardar en javascript información del servidor -->
<script type="text/javascript">
  const BASE_URL = "<?= base_url() ?>";
  const CSRF_HEADER = "<?= csrf_header() ?>";
  const CSRF_HASH = "<?= csrf_hash() ?>";
</script>