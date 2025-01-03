<div class="login-box">
  <div class="login-logo">
    <?= $_ENV('COPYRIGHT') ?? '' ?>
  </div>

  <div class="card">
    <div class="card-body login-card-body">

      <form action="./login" method="post">

        <label for="username"><?= __('username', 'Username') ?></label>
        <div class="input-group mb-3">
          <input type="text" name="username" id="username" class="form-control" required value="<?= e($data['username']) ?>">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>

        <label for="password"><?= __('password', 'Password') ?></label>
        <div class="input-group mb-3">
          <input type="password" name="password" id="password" class="form-control" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>

        <div class="col-4">
          <button type="submit" class="btn btn-primary btn-block"><?= __('login', 'Login') ?></button>
        </div>

      </form>
    </div>
  </div>
</div>
