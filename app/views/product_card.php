<?php $t = tiers((float)$p['price']); $wish = in_array((int)$p['id'], wishlist()); ?>
<li class="pcard">
  <?php if ($p['badge']): ?><span class="badge"><?= e($p['badge']) ?></span><?php elseif ($p['in_stock']): ?><span class="badge">Bulk Discount</span><?php endif ?>
  <form method="post" action="/wishlist/toggle" class="wish"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $p['id'] ?>"><input type="hidden" name="back" value="<?= e($_SERVER['REQUEST_URI']) ?>"><button class="<?= $wish ? 'on' : '' ?>" aria-label="Wishlist"><?= icon('heart') ?></button></form>
  <a href="/product/<?= e($p['slug']) ?>" class="pimg"><img src="<?= img_url($p) ?>" alt="<?= e($p['name']) ?>" loading="lazy"></a>
  <h2><a href="/product/<?= e($p['slug']) ?>"><?= e($p['name']) ?></a></h2>
  <?php if ($p['in_stock']): ?>
  <form method="post" action="/cart/add" class="buy"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $p['id'] ?>"><input type="hidden" name="back" value="<?= e($_SERVER['REQUEST_URI']) ?>">
    <div class="tier-select" data-base="<?= $p['price'] ?>"><select aria-label="Bulk pricing" class="tiers"><?php foreach ($t as $x): ?><option value="<?= $x['min'] ?>"><?= e($x['label']) ?> &nbsp; <?= money($x['price']) ?></option><?php endforeach ?></select></div>
    <div class="buyrow"><div class="qty"><button type="button" data-d="-1">−</button><input type="number" name="qty" value="1" min="1" max="99"><button type="button" data-d="1">+</button></div>
      <span class="price" data-price><?= money((float)$p['price']) ?></span></div>
    <button class="btn-add" aria-label="Add to cart"><?= icon('cart') ?></button>
  </form>
  <?php else: ?>
  <div class="buyrow"><span class="price"><?= money((float)$p['price']) ?></span></div><button class="btn-add" disabled>Out Of Stock</button>
  <?php endif ?>
</li>
