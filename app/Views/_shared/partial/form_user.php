<?= view("_shared/partial/form_user_profile") ?>
<?= view("_shared/partial/form_user_password") ?>

<div class="form-group row">
  <label for="userRoleId" class="col-sm-2 col-form-label">Rol de usuario</label>
  <div class="col-sm-10">
    <select class="opacity-0" name="userRoleId" id="userRoleId" required></select>
  </div>
</div>

<script defer type="module" src="<?= base_url("public/js/_shared/partial/form_user.js") ?>"></script>