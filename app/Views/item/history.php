<?=
view(
  "_shared/partial/app_breadcrumb",
  [
    "links" => [
      [
        "text" => "Artículo",
        "href" => base_url("item")
      ], [
        "text" => "Lista de artículos",
        "href" => base_url("item/view/items-list")
      ], [
        "text" => $itemEntity->itemName,
        "href" => "#"
      ],
    ]
  ]
)
?>
<div class="row">
  <div class="col-12">
    <div id="alert"></div>
  </div>
</div>
<?= view("item/history/timeline", ["order" => $order]) ?>

<div id="storage"></div>

<script type="text/javascript">
  const ITEM_ENTITY = JSON.parse("<?= addslashes(json_encode($itemEntity)) ?>");
  const ITEM_HISTORY_LIST = JSON.parse("<?= addslashes(json_encode($itemHistoryList)) ?>");
</script>
<script defer type="module" src="<?= base_url("public/js/item/history.js?v=") . APP_VERSION ?>"></script>