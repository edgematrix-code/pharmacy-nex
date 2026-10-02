<div class="container narrow"><h1 class="page-title">Shopping Cart</h1>
<?php if (!$items): ?><div class="empty-cart"><img src="/assets/img/empty-bag.svg" alt="" onerror="this.remove()"><p>No products in the cart.</p><a class="btn" href="/shop">Continue shopping</a></div>
<?php else: $t = cart_totals($items); ?>
<form method="post" action="/cart/update"><?= csrf_field() ?>
<table class="cart-table"><thead><tr><th></th><th>Product</th><th>Price</th><th>Qty</th><th>Total</th><th></th></tr></thead><tbody>
<?php foreach ($items as $i): ?><tr><td><img src="<?= img_url($i) ?>" width="64" alt=""></td><td><a href="/product/<?= e($i['slug']) ?>"><?= e($i['name']) ?></a></td>
<td><?= money($i['unit']) ?><?php if ($i['unit'] < $i['price']): ?><br><s class="small"><?= money((float)$i['price']) ?></s><?php endif ?></td>
<td><input class="qin" type="number" name="qty[<?= $i['id'] ?>]" value="<?= $i['qty'] ?>" min="0" max="99"></td><td><?= money($i['line']) ?></td>
<td><button name="remove" value="<?= $i['id'] ?>" class="x" aria-label="Remove">×</button></td></tr><?php endforeach ?></tbody></table>
<div class="cart-actions"><button class="btn ghost">Update cart</button></div></form>
<div class="totals"><div><span>Subtotal</span><b><?= money($t['subtotal']) ?></b></div><div><span>Shipping</span><b><?= $t['shipping'] ? money($t['shipping']) : 'Free' ?></b></div>
<div class="grand"><span>Total</span><b><?= money($t['total']) ?></b></div>
<?php if ($t['shipping']): ?><p class="small">Add <?= money(cfg('free_ship_over') - $t['subtotal']) ?> more for free shipping.</p><?php endif ?>
<a class="btn block" href="/checkout">Checkout</a></div>
<?php endif ?></div>
