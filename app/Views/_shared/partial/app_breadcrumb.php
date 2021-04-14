<div class="content-header px-0">
  <div class="row">
    <div class="col-sm-6">
      <h1 class="m-0 text-dark"><?= $links[count($links) - 1]["text"] ?></h1>
    </div>
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
        <?php foreach ($links as $link): ?>

          <?php if ($link["text"] === $links[count($links) - 1]["text"]): ?>

            <li class="breadcrumb-item active">
              <?= $link["text"] ?>
            </li>

          <?php else: ?>

            <li class="breadcrumb-item">
              <a href="<?= $link["href"] ?>"><?= $link["text"] ?></a>
            </li>

          <?php endif ?>

        <?php endforeach ?>
      </ol>
    </div>
  </div>
</div>