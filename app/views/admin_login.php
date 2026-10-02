<div class="container narrow"><h1 class="page-title">Nexus Pharma – Admin sign in</h1><?php if ($f = flash()): ?><div class="alert"><?= e($f) ?></div><?php endif ?>
<form method="post" class="form"><?= csrf_field() ?><label>Username<input name="user" required></label><label>Password<input type="password" name="pass" required></label><button class="btn">Sign in</button></form></div>
