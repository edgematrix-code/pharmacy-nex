<div class="container narrow"><h1 class="page-title">Track your order – Nexus Pharma</h1>
<?php if ($err): ?><div class="alert"><?= e($err) ?></div><?php endif ?>
<form method="post" class="form"><?= csrf_field() ?><div class="two"><label>Order number<input name="number" placeholder="NX-XXXXXXX" required></label><label>Email<input type="email" name="email" required></label></div><button class="btn">Track</button></form>
<?php if ($order): ?><div class="track-result"><p>Order <b><?= e($order['number']) ?></b> — status: <span class="badge static"><?= e($order['status']) ?></span></p>
<?php if ($order['tracking']): ?><p>Tracking number: <b><?= e($order['tracking']) ?></b></p><?php endif ?>
<ul><?php foreach ($lines as $l): ?><li><?= e($l['name']) ?> × <?= $l['qty'] ?></li><?php endforeach ?></ul><p>Total: <?= money((float)$order['total']) ?></p></div><?php endif ?></div>
