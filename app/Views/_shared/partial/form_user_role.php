<div class="form-group row">
  <label for="userRoleName" class="col-sm-2 col-form-label">Nombre de rol</label>
  <div class="col-sm-10">
    <input autofocus type="text" class="form-control form-control-sm" id="userRoleName" name="userRoleName" placeholder="<?= session("user_role_name") ?>" required>
  </div>
</div>

<div class="form-group row">
  <label for="unitPluralName" class="col-sm-2 col-form-label">Accesos permitidos</label>
  <div class="col-sm-10">
    <ul class="list-unstyled">
      <?php foreach ($templateAllMenus as $mainMenu): ?>
        <li>
          <?= $mainMenu["name"] ?>
          <ul>
            <?php foreach ($mainMenu["menu"] as $menu): ?>
              <?php if (empty($menu["menu"])): ?>
                <li>
                  <div class="custom-control custom-checkbox">
                    <input class="custom-control-input" type="checkbox" id="<?= md5($menu["path"]) ?>" name="<?= md5($menu["path"]) ?>" value="<?= $menu["path"] ?>">
                    <label for="<?= md5($menu["path"]) ?>" class="custom-control-label"><?= $menu["name"] ?></label>
                  </div>
                </li>
                <?php continue ?>
              <?php endif ?>

              <li>
                <?= $menu["name"] ?>
                <ul>
                  <?php foreach ($menu["menu"] as $submenu): ?>
                    <li>
                      <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox" id="<?= md5($submenu["path"]) ?>" name="<?= md5($submenu["path"]) ?>" value="<?= $submenu["path"] ?>">
                        <label for="<?= md5($submenu["path"]) ?>" class="custom-control-label"><?= $submenu["name"] ?></label>
                      </div>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </li>
            <?php endforeach; ?>
          </ul>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>