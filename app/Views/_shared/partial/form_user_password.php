<div class="form-group row">
  <label for="userNickname" class="col-sm-2 col-form-label">Usuario</label>
  <div class="col-sm-10">
    <input type="text" class="form-control form-control-sm" id="userNickname" name="userNickname" placeholder="<?= session("user_nickname") ?>" required>
  </div>
</div>

<div class="form-group row">
  <label for="userPassword" class="col-sm-2 col-form-label">Nueva contraseña</label>
  <div class="col-sm-10">
    <input type="password" class="form-control form-control-sm" id="userPassword" name="userPassword" placeholder="Contraseña nueva">
  </div>
</div>

<div class="form-group row">
  <label for="userPasswordConfirm" class="col-sm-2 col-form-label">Confirmar contraseña</label>
  <div class="col-sm-10">
    <input type="password" class="form-control form-control-sm" id="userPasswordConfirm" name="userPasswordConfirm" placeholder="Repita contraseña nueva">
  </div>
</div>