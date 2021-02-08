<div class="form-group row">
  <label for="userName" class="col-sm-2 col-form-label">Nombre (s)</label>
  <div class="col-sm-10">
    <input autofocus type="text" class="form-control form-control-sm" id="userName" name="userName" placeholder="<?= session("user_name") ?>" required>
  </div>
</div>

<div class="form-group row">
  <label for="userSurname" class="col-sm-2 col-form-label">Apellido (s)</label>
  <div class="col-sm-10">
    <input type="text" class="form-control form-control-sm" id="userSurname" name="userSurname" placeholder="<?= session("user_surname") ?>" required>
  </div>
</div>