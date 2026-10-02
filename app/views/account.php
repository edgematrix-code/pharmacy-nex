<div class="container"><h1 class="page-title">My account</h1>
<div class="co-grid">
  <div class="form">
    <h3>Welcome, <?= e($me['name']) ?></h3>
    <p class="note">Signed in as <strong><?= e($me['email']) ?></strong></p>
    <ul class="account-links">
      <li><a href="/shop">Continue shopping</a></li>
      <li><a href="/cart">View cart</a></li>
      <li><a href="/tracking-order">Track an order</a></li>
    </ul>
    <form method="post" action="/logout"><?= csrf_field() ?><button class="btn ghost">Sign out</button></form>
  </div>
  <div>
    <h3>Your orders</h3>
    <?php if (!$orders): ?>
      <p class="note">You haven't placed any orders yet.</p>
    <?php else: ?>
      <table class="admin-table">
        <thead><tr><th>Order</th><th>Date</th><th>Total</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($orders as $o): ?>
          <tr>
            <td><b><?= e($o['number']) ?></b></td>
            <td><?= e(date('M j, Y', strtotime($o['created_at']))) ?></td>
            <td><?= money((float)$o['total']) ?></td>
            <td><span class="badge static"><?= e($o['status']) ?></span></td>
          </tr>
        <?php endforeach ?>
        </tbody>
      </table>
    <?php endif ?>
  </div>
</div>
</div>
