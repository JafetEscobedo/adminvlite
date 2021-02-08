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
<link rel="stylesheet" type="text/css" href="<?= base_url("public/src/datatables/css/datatables.min.css") ?>"/>
<link rel="stylesheet" type="text/css" href="<?= base_url("public/src/overlayscrollbars/css/overlayscrollbars.min.css") ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url("public/src/fontawesome/css/fontawesome.min.css") ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url("public/src/select2/css/select2.min.css") ?>" />
<link rel="stylesheet" type="text/css" href="<?= base_url("public/src/adminlte/css/adminlte.min.css") ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url("public/css/_shared/app.css") ?>">

<!-- Scripts generales -->
<script defer type="text/javascript" src="<?= base_url("public/src/jquery/js/jquery.min.js") ?>"></script>
<script defer type="text/javascript" src="<?= base_url("public/src/bootstrap/js/bootstrap.bundle.min.js") ?>"></script>
<script defer type="text/javascript" src="<?= base_url("public/src/axios/js/axios.min.js") ?>"></script>
<script defer type="text/javascript" src="<?= base_url("public/src/overlayscrollbars/js/overlayscrollbars.min.js") ?>"></script>
<script defer type="text/javascript" src="<?= base_url("public/src/pace/js/pace.min.js") ?>"></script>
<script defer type="text/javascript" src="<?= base_url("public/src/lodash/js/lodash.min.js") ?>"></script>
<script defer type="text/javascript" src="<?= base_url("public/src/moment/js/moment_with_locales.min.js") ?>"></script>
<script defer type="text/javascript" src="<?= base_url("public/src/moment/js/moment_timezone_with_data_10_year_range.min.js") ?>"></script>
<script defer type="text/javascript" src="<?= base_url("public/src/select2/js/select2.min.js") ?>"></script>
<script defer type="text/javascript" src="<?= base_url("public/src/select2/js/i18n_es.min.js") ?>"></script>
<script defer type="text/javascript" src="<?= base_url("public/src/react/js/react.production.min.js") ?>"></script>
<script defer type="text/javascript" src="<?= base_url("public/src/react/js/react_dom.production.min.js") ?>"></script>
<script defer type="text/javascript" src="<?= base_url("public/src/cryptojs/js/cryptojs.min.js") ?>"></script>
<script defer type="text/javascript" src="<?= base_url("public/src/bootbox/js/bootbox.min.js") ?>"></script>
<script defer type="text/javascript" src="<?= base_url("public/src/pdfmake/js/pdfmake.min.js") ?>"></script>
<script defer type="text/javascript" src="<?= base_url("public/src/pdfmake/js/vfs_fonts.js") ?>"></script>
<script defer type="text/javascript" src="<?= base_url("public/src/datatables/js/datatables.min.js") ?>"></script>
<script defer type="text/javascript" src="<?= base_url("public/src/adminlte/js/adminlte.min.js") ?>"></script>
<script defer type="text/javascript" src="<?= base_url("public/js/_shared/init.js") ?>"></script>

<!-- Guardar en javascript información del servidor -->
<script type="text/javascript">
  const BASE_URL = "<?= base_url() ?>";
  const CSRF_HEADER = "<?= csrf_header() ?>";
  const CSRF_HASH = "<?= csrf_hash() ?>";
</script>