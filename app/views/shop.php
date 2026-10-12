<?php $cats = categories(); ?>
<div class="filterbar"><div class="container"><button class="filter-btn" onclick="document.getElementById('filters').classList.toggle('open')"><?= icon('filter') ?> All Filters</button>
  <form id="filters" method="get" action="/shop"><select name="cat" onchange="this.form.submit()"><option value="">All categories</option><?php foreach ($cats as $c): ?><option value="<?= e($c['slug']) ?>" <?= $c['slug'] === $cat ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach ?></select>
  <input name="s" value="<?= e($q) ?>" placeholder="Search…"><select name="sort" onchange="this.form.submit()"><option value="name">Name A–Z</option><option value="price-asc" <?= $sort === 'price-asc' ? 'selected' : '' ?>>Price: low to high</option><option value="price-desc" <?= $sort === 'price-desc' ? 'selected' : '' ?>>Price: high to low</option></select><button class="btn sm">Apply</button></form></div></div>
<div class="container">
  <div class="resultbar views-only">
    <div class="views" id="view-switcher" role="group" aria-label="Product layout">
      <button type="button" data-view="2" aria-label="Two columns" title="2 columns"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="18"/><rect x="14" y="3" width="7" height="18"/></svg></button>
      <button type="button" data-view="3" aria-label="Three columns" title="3 columns"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="4" height="4"/><rect x="10" y="3" width="4" height="4"/><rect x="17" y="3" width="4" height="4"/><rect x="3" y="10" width="4" height="4"/><rect x="10" y="10" width="4" height="4"/><rect x="17" y="10" width="4" height="4"/></svg></button>
      <button type="button" data-view="4" class="on" aria-label="Four columns" title="4 columns"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="4" height="4"/><rect x="10" y="3" width="4" height="4"/><rect x="17" y="3" width="4" height="4"/><rect x="3" y="10" width="4" height="4"/><rect x="10" y="10" width="4" height="4"/><rect x="17" y="10" width="4" height="4"/><rect x="3" y="17" width="4" height="4"/><rect x="10" y="17" width="4" height="4"/><rect x="17" y="17" width="4" height="4"/></svg></button>
      <button type="button" data-view="list" aria-label="List view" title="List"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="5" height="5"/><rect x="11" y="5" width="10" height="1"/><rect x="3" y="10" width="5" height="5"/><rect x="11" y="12" width="10" height="1"/><rect x="3" y="17" width="5" height="5"/><rect x="11" y="19" width="10" height="1"/></svg></button>
    </div>
  </div>
  <?php if (!$products): ?><p class="empty">No products found. <a href="/shop">Reset filters</a></p><?php endif ?>
  <ul class="grid cols-4" id="product-grid"><?php foreach ($products as $p) echo product_card($p); ?></ul>
</div>
