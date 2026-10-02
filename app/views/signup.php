<div class="container narrow"><h1 class="page-title">Create your account</h1>
<p class="note">Sign up to check out faster and keep every order in one place.</p>
<?php if ($err): ?><div class="alert"><?= e($err) ?></div><?php endif ?>
<form method="post" class="form"><?= csrf_field() ?>
  <input type="hidden" name="next" value="<?= e($next) ?>">
  <label>Full name<input name="name" value="<?= e($name) ?>" required autofocus></label>
  <label>Email<input type="email" name="email" value="<?= e($email) ?>" required></label>
  <div class="two">
    <label>Password<input type="password" name="password" minlength="8" required></label>
    <label>Confirm password<input type="password" name="password2" minlength="8" required></label>
  </div>
  <p class="note small">Use at least 8 characters. We store passwords securely hashed.</p>
  <button class="btn">Create account</button>
</form>
<p class="auth-alt">Already have an account? <a href="/login<?= $next !== '/account' ? '?next=' . e(urlencode($next)) : '' ?>">Sign in</a></p>
</div>
