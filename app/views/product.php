<div class="container pdp">
  <nav class="crumbs"><a href="/">Home</a> / <a href="/shop">Shop</a> / <a href="/product-category/<?= e($p['category']) ?>"><?= e($p['cat_name']) ?></a> / <?= e($p['name']) ?></nav>
  <div class="pdp-grid">
    <div class="pdp-img"><img src="<?= img_url($p) ?>" alt="<?= e($p['name']) ?>"></div>
    <div class="pdp-info">
      <?php if ($p['badge']): ?><span class="badge static"><?= e($p['badge']) ?></span><?php endif ?>
      <h1><?= e($p['name']) ?></h1><p class="price big"><?= money((float)$p['price']) ?></p><p class="lead"><?= e($p['description']) ?></p>
      <table class="tier-table"><thead><tr><th>Quantity</th><th>Price each</th><th>You save</th></tr></thead><tbody>
      <?php foreach (tiers((float)$p['price']) as $k => $t): ?><tr><td><?= $t['min'] ?><?= $k === 2 ? '+' : '' ?></td><td><?= money($t['price']) ?></td><td><?= $t['price'] < $p['price'] ? money($p['price'] - $t['price']) : '—' ?></td></tr><?php endforeach ?></tbody></table>
      <?php if ($p['in_stock']): ?>
      <form method="post" action="/cart/add" class="buy pdp-buy" ><?= csrf_field() ?><input type="hidden" name="id" value="<?= $p['id'] ?>"><input type="hidden" name="back" value="/cart">
        <div class="qty"><button type="button" data-d="-1">−</button><input type="number" name="qty" value="1" min="1" max="99"><button type="button" data-d="1">+</button></div>
        <button class="btn">Add to cart</button></form>
      <?php else: ?><p class="oos">Currently out of stock</p><?php endif ?>
      <p class="small">Category: <a href="/product-category/<?= e($p['category']) ?>"><?= e($p['cat_name']) ?></a>. Always read the label and use as directed.</p>
    </div>
  </div>
  <?php if ($related): ?><h2 class="sec">Related products</h2><ul class="grid cols-4"><?php foreach ($related as $r) echo product_card($r); ?></ul><?php endif ?>
</div>
