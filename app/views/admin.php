<div class="container"><h1 class="page-title">Admin dashboard</h1><form method="post" class="admin-logout"><button name="logout" value="1" class="btn ghost sm">Log out</button></form>

<section id="carousel">
  <h2 class="sec">Hero carousel images</h2>
  <p class="note">Choose which products (and therefore which images) rotate on the right side of the home page hero. Use ▲ / ▼ to order them, or Remove to take one out.</p>

  <form method="post" class="hero-add"><?= csrf_field() ?>
    <input type="hidden" name="product_action" value="hero_add">
    <select name="product_id" required>
      <option value="">Select a product to add…</option>
      <?php foreach ($products as $p): if ($p['hero']) continue; ?><option value="<?= $p['id'] ?>"><?= e($p['name']) ?></option><?php endforeach ?>
    </select>
    <button class="btn sm">Add to carousel</button>
  </form>

  <?php if ($heroProducts): ?>
  <ul class="hero-list">
    <?php foreach ($heroProducts as $p): ?>
    <li>
      <img src="<?= img_url($p) ?>" alt="" class="admin-thumb" onerror="this.style.visibility='hidden'">
      <span class="hero-name"><?= e($p['name']) ?></span>
      <form method="post" class="move-form"><?= csrf_field() ?><input type="hidden" name="product_action" value="hero_move"><input type="hidden" name="id" value="<?= $p['id'] ?>">
        <button name="dir" value="up" class="mv" aria-label="Move earlier">▲</button>
        <button name="dir" value="down" class="mv" aria-label="Move later">▼</button>
      </form>
      <form method="post"><?= csrf_field() ?><input type="hidden" name="product_action" value="hero_remove"><input type="hidden" name="id" value="<?= $p['id'] ?>"><button class="btn sm danger">Remove</button></form>
    </li>
    <?php endforeach ?>
  </ul>
  <?php else: ?>
  <p class="note">No images selected yet. Until you add some, the hero shows the first few products from the shop.</p>
  <?php endif ?>
</section>

<section id="products">
  <h2 class="sec">Products</h2>
  <?php if ($f = flash()): ?><div class="ok"><?= e($f) ?></div><?php endif ?>
  <p class="note">Use ▲ / ▼ to set the order products appear on the shop page (top to bottom). Click <strong>Edit</strong> to change a product, or add a new one below.</p>

  <form method="post" class="form admin-product-form"><?= csrf_field() ?>
    <input type="hidden" name="product_action" value="save">
    <input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
    <h3><?= $edit ? 'Edit product: ' . e($edit['name']) : 'Add a new product' ?></h3>
    <div class="two">
      <label>Product name<input name="name" value="<?= e($edit['name'] ?? '') ?>" required></label>
      <label>Category<select name="category">
        <?php foreach ($catList as $slug => [$nm, $col]): ?><option value="<?= e($slug) ?>" <?= ($edit['category'] ?? '') === $slug ? 'selected' : '' ?>><?= e($nm) ?></option><?php endforeach ?>
      </select></label>
    </div>
    <div class="two">
      <label>Price<input type="number" step="0.01" min="0" name="price" value="<?= e($edit['price'] ?? '') ?>" required></label>
      <label>Badge (e.g. 10ml Vial)<input name="badge" value="<?= e($edit['badge'] ?? '') ?>"></label>
    </div>
    <div class="two">
      <label>Image file in /assets/DRUGS<input name="filename" list="drugfiles" value="<?= e($edit['filename'] ?? '') ?>" placeholder="e.g. Cialis.jpg"></label>
      <label class="check-label"><span>Availability</span><span class="check-line"><input type="checkbox" name="in_stock" <?= ($edit['in_stock'] ?? 1) ? 'checked' : '' ?>> In stock</span></label>
    </div>
    <label>Description<textarea name="description" rows="2"><?= e($edit['description'] ?? '') ?></textarea></label>
    <div class="admin-form-actions">
      <button class="btn"><?= $edit ? 'Save changes' : 'Add product' ?></button>
      <?php if ($edit): ?><a class="btn ghost" href="/admin#products">Cancel</a><?php endif ?>
    </div>
  </form>
  <datalist id="drugfiles"><?php foreach ($drugFiles as $f): ?><option value="<?= e($f) ?>"><?php endforeach ?></datalist>

  <div class="admin-table-wrap">
  <table class="cart-table admin">
    <thead><tr><th>Order</th><th></th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($products as $p): ?>
      <tr>
        <td class="ord">
          <form method="post" class="move-form"><?= csrf_field() ?><input type="hidden" name="product_action" value="move"><input type="hidden" name="id" value="<?= $p['id'] ?>">
            <button name="dir" value="up" class="mv" aria-label="Move up">▲</button>
            <button name="dir" value="down" class="mv" aria-label="Move down">▼</button>
          </form>
        </td>
        <td><img src="<?= img_url($p) ?>" alt="" class="admin-thumb" onerror="this.style.visibility='hidden'"></td>
        <td><?= e($p['name']) ?></td>
        <td><?= e($catList[$p['category']][0] ?? $p['category']) ?></td>
        <td><?= money((float)$p['price']) ?></td>
        <td><?= $p['in_stock'] ? 'In stock' : 'Out' ?></td>
        <td class="admin-actions">
          <a class="btn sm ghost" href="/admin?edit=<?= $p['id'] ?>#products">Edit</a>
          <form method="post" onsubmit="return confirm('Delete this product?');"><?= csrf_field() ?><input type="hidden" name="product_action" value="delete"><input type="hidden" name="id" value="<?= $p['id'] ?>"><button class="btn sm danger">Delete</button></form>
        </td>
      </tr>
    <?php endforeach ?>
    </tbody>
  </table>
  </div>
</section>

<h2 class="sec">Orders</h2>
<table class="cart-table admin"><thead><tr><th>#</th><th>Customer</th><th>Total</th><th>Date</th><th>Status / tracking</th></tr></thead><tbody>
<?php foreach ($orders as $o): ?><tr><td><?= e($o['number']) ?></td><td><?= e($o['name']) ?><br><small><?= e($o['email']) ?> · <?= e($o['address'] . ', ' . $o['city'] . ' ' . $o['zip']) ?></small></td><td><?= money((float)$o['total']) ?></td><td><?= e(substr($o['created_at'], 0, 10)) ?></td>
<td><form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="order_id" value="<?= $o['id'] ?>"><select name="status"><?php foreach (['Processing', 'Shipped', 'Delivered', 'Cancelled'] as $s): ?><option <?= $s === $o['status'] ? 'selected' : '' ?>><?= $s ?></option><?php endforeach ?></select><input name="tracking" value="<?= e($o['tracking']) ?>" placeholder="Tracking #"><button class="btn sm">Save</button></form></td></tr><?php endforeach ?></tbody></table>
<h2 class="sec">Latest messages</h2><?php foreach ($msgs as $m): ?><p><b><?= e($m['name']) ?></b> &lt;<?= e($m['email']) ?>&gt; — <?= e($m['message']) ?></p><?php endforeach ?></div>
