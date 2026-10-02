<div class="container narrow"><h1 class="page-title">Sign in</h1>
<p class="note">Welcome back. Sign in to track your orders and check out faster.</p>
<?php if ($err): ?><div class="alert"><?= e($err) ?></div><?php endif ?>
<form method="post" class="form"><?= csrf_field() ?>
  <input type="hidden" name="next" value="<?= e($next) ?>">
  <label>Email<input type="email" name="email" value="<?= e($email) ?>" required autofocus></label>
  <label>Password<input type="password" name="password" required></label>
  <button class="btn">Sign in</button>
</form>
<p class="auth-alt">New to <?= e(cfg('site_name')) ?>? <a href="/signup<?= $next !== '/account' ? '?next=' . e(urlencode($next)) : '' ?>">Create an account</a></p>
</div>
