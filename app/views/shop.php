<?php $cats = categories(); $curName = 'All products'; foreach ($cats as $c) if ($c['slug'] === $cat) $curName = $c['name']; ?>
<div class="filterbar"><div class="container"><button class="filter-btn" onclick="document.getElementById('filters').classList.toggle('open')"><?= icon('filter') ?> All Filters</button>
  <form id="filters" method="get" action="/shop"><select name="cat" onchange="this.form.submit()"><option value="">All categories</option><?php foreach ($cats as $c): ?><option value="<?= e($c['slug']) ?>" <?= $c['slug'] === $cat ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach ?></select>
  <input name="s" value="<?= e($q) ?>" placeholder="Search…"><select name="sort" onchange="this.form.submit()"><option value="name">Name A–Z</option><option value="price-asc" <?= $sort === 'price-asc' ? 'selected' : '' ?>>Price: low to high</option><option value="price-desc" <?= $sort === 'price-desc' ? 'selected' : '' ?>>Price: high to low</option></select><button class="btn sm">Apply</button></form></div></div>
<div class="container">
  <div class="resultbar"><span>1–<?= $shown ?> of <?= $total ?> Results <?= $cat ? '· ' . e($curName) : '' ?></span>
    <div class="views" id="view-switcher" role="group" aria-label="Product layout">
      <button type="button" data-view="2" aria-label="Two columns" title="2 columns"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="18"/><rect x="14" y="3" width="7" height="18"/></svg></button>
      <button type="button" data-view="3" aria-label="Three columns" title="3 columns"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="4" height="4"/><rect x="10" y="3" width="4" height="4"/><rect x="17" y="3" width="4" height="4"/><rect x="3" y="10" width="4" height="4"/><rect x="10" y="10" width="4" height="4"/><rect x="17" y="10" width="4" height="4"/></svg></button>
      <button type="button" data-view="4" class="on" aria-label="Four columns" title="4 columns"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="4" height="4"/><rect x="10" y="3" width="4" height="4"/><rect x="17" y="3" width="4" height="4"/><rect x="3" y="10" width="4" height="4"/><rect x="10" y="10" width="4" height="4"/><rect x="17" y="10" width="4" height="4"/><rect x="3" y="17" width="4" height="4"/><rect x="10" y="17" width="4" height="4"/><rect x="17" y="17" width="4" height="4"/></svg></button>
      <button type="button" data-view="list" aria-label="List view" title="List"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="5" height="5"/><rect x="11" y="5" width="10" height="1"/><rect x="3" y="10" width="5" height="5"/><rect x="11" y="12" width="10" height="1"/><rect x="3" y="17" width="5" height="5"/><rect x="11" y="19" width="10" height="1"/></svg></button>
    </div>
  </div>
  <?php if (!$products): ?><p class="empty">No products found. <a href="/shop">Reset filters</a></p><?php endif ?>
  <ul class="grid cols-4" id="product-grid" data-page="<?= $pg ?>" data-cat="<?= e($cat) ?>" data-sort="<?= e($sort) ?>" data-q="<?= e($q) ?>"><?php foreach ($products as $p) echo product_card($p); ?></ul>
  <div class="loadmore"><p>Showing <?= $shown ?> of <?= $total ?> items</p>
  <?php if ($shown < $total): ?><button class="btn ghost load-more-btn" data-next-page="<?= $pg + 1 ?>">Load More Products</button><?php endif ?></div>
</div>

<script>
(function () {
  const grid = document.getElementById('product-grid');
  if (!grid) return;

  function bindLoadMore(btn) {
    if (!btn) return;
    btn.addEventListener('click', function (event) {
      event.preventDefault();
      const nextPage = this.dataset.nextPage;
      this.disabled = true;
      this.textContent = 'Loading...';

      const params = new URLSearchParams({ page: nextPage });
      if (grid.dataset.cat) params.set('cat', grid.dataset.cat);
      if (grid.dataset.sort) params.set('sort', grid.dataset.sort);
      if (grid.dataset.q) params.set('s', grid.dataset.q);

      fetch('?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.text())
        .then(html => {
          const doc = new DOMParser().parseFromString(html, 'text/html');
          const incoming = doc.querySelector('#product-grid');
          if (!incoming) throw new Error('No grid in response');

          // Remember where the user was before we touch the DOM.
          const scrollY = window.scrollY || window.pageYOffset;

          const cards = Array.from(incoming.querySelectorAll('.pcard'));
          cards.forEach(c => grid.appendChild(c));
          grid.dataset.page = nextPage;

          // Swap in the refreshed "showing X of Y" block and rebind its button.
          const newMore = doc.querySelector('.loadmore');
          const curMore = document.querySelector('.loadmore');
          if (newMore && curMore) {
            curMore.replaceWith(newMore);
            bindLoadMore(newMore.querySelector('.load-more-btn'));
          }

          // New cards append *below* the current content, so the page must stay
          // exactly where it was — restoring scrollY undoes any layout shift.
          window.scrollTo(0, scrollY);
        })
        .catch(err => {
          console.error('Error loading more products:', err);
          this.disabled = false;
          this.textContent = 'Load More Products';
        });
    });
  }

  bindLoadMore(document.querySelector('.load-more-btn'));
})();
</script>
