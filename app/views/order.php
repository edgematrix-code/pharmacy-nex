<div class="container narrow center"><h1 class="page-title">Thank you — order received</h1>
<p>Your order number is <b><?= e($order['number']) ?></b> from <strong>Nexus Pharma</strong>. We’ll email a tracking number within 24 hours.</p>
<table class="cart-table"><?php foreach ($lines as $l): ?><tr><td><?= e($l['name']) ?> × <?= $l['qty'] ?></td><td><?= money($l['unit_price'] * $l['qty']) ?></td></tr><?php endforeach ?>
<tr><td>Shipping</td><td><?= money((float)$order['shipping']) ?></td></tr><tr class="grand"><td><b>Total</b></td><td><b><?= money((float)$order['total']) ?></b></td></tr></table>
<a class="btn" href="/shop">Continue shopping</a></div>
