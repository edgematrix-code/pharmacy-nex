<?php $t = cart_totals($items); $o = fn($k) => e($old[$k] ?? ''); ?>
<div class="container checkout"><h1 class="page-title">Checkout</h1>
<?php if ($errors): ?><div class="alert"><?php foreach ($errors as $er) echo '<p>' . e($er) . '</p>'; ?></div><?php endif ?>
<div class="co-grid"><form method="post" class="form"><?= csrf_field() ?>
  <h3>Billing &amp; shipping</h3>
  <p class="note">Orders are placed with <strong>Nexus Pharma</strong> – a trusted pharmaceutical supplier.</p>
  <label>Full name<input name="name" value="<?= $o('name') ?>" required></label>
  <div class="two"><label>Email<input type="email" name="email" value="<?= $o('email') ?>" required></label><label>Phone<input name="phone" value="<?= $o('phone') ?>"></label></div>
  <label>Street address<input name="address" value="<?= $o('address') ?>" required></label>
  <div class="two"><label>City<input name="city" value="<?= $o('city') ?>" required></label><label>ZIP / Postal code<input name="zip" value="<?= $o('zip') ?>" required></label></div>
  <label>Country<input name="country" value="<?= $o('country') ?: 'United States' ?>" required></label>
  <label>Order notes (optional)<textarea name="notes" rows="3"><?= $o('notes') ?></textarea></label>
  <h3 id="payment">Payment</h3>
  <?php
    $usdtIcon = '<svg class="crypto-icon" viewBox="0 0 32 32" aria-hidden="true"><circle cx="16" cy="16" r="16" fill="#26A17B"/><path d="M7 9h18v3.4h-6.8v3.05c2.9.18 4.8.68 4.8 1.25v1.3c0 .57-1.9 1.07-4.8 1.25V24h-4.4v-4.75c-2.9-.18-4.8-.68-4.8-1.25v-1.3c0-.57 1.9-1.07 4.8-1.25V12.4H7z" fill="#fff"/></svg>';
    $usdcIcon = '<svg class="crypto-icon" viewBox="0 0 32 32" aria-hidden="true"><circle cx="16" cy="16" r="16" fill="#2775CA"/><text x="16" y="23" text-anchor="middle" font-family="Arial,Helvetica,sans-serif" font-size="21" font-weight="700" fill="#fff">$</text></svg>';
  ?>
  <p class="note">We accept <strong>USDT</strong> and <strong>USDC</strong> on four networks. Send exactly <strong><?= money($t['total']) ?></strong> to the address for the network you choose, then paste your transaction hash below. Your order is shipped once the payment is confirmed on-chain &mdash; usually within minutes.</p>

  <ol class="pay-steps">
    <li><span>1</span><div><b>Pick a token &amp; network</b><p>Choose one of the options below. The network you send from must match the network on the card.</p></div></li>
    <li><span>2</span><div><b>Scan or copy the address</b><p>Scan the QR code with your wallet, or tap <em>Copy</em> to copy the wallet address.</p></div></li>
    <li><span>3</span><div><b>Send the exact amount</b><p>Send exactly <strong><?= money($t['total']) ?></strong> in the chosen token, and cover any network fee on top.</p></div></li>
    <li><span>4</span><div><b>Paste your TXID &amp; place the order</b><p>Copy the transaction hash (TXID) from your wallet into the field at the bottom, then place your order.</p></div></li>
  </ol>

  <div class="crypto-payments">
    <div class="crypto-option">
      <div class="crypto-top"><?= $usdtIcon ?><div class="crypto-meta"><b>USDT</b><span>ERC-20 &middot; Ethereum</span></div></div>
      <div class="crypto-qr"><img src="/assets/tokens/usdt(erc20).jpg" alt="USDT ERC-20 payment QR code" width="120" height="120" loading="lazy"></div>
      <div class="crypto-address"><code>0xaB270D8d31C2fBE1fE0B5D2E9A974c44AA821c10</code><button type="button" class="copy-btn" data-copy="0xaB270D8d31C2fBE1fE0B5D2E9A974c44AA821c10">Copy</button></div>
    </div>

    <div class="crypto-option">
      <div class="crypto-top"><?= $usdtIcon ?><div class="crypto-meta"><b>USDT</b><span>TRC-20 &middot; Tron</span></div></div>
      <div class="crypto-qr"><img src="/assets/tokens/usdt(trc20).jpg" alt="USDT TRC-20 payment QR code" width="120" height="120" loading="lazy"></div>
      <div class="crypto-address"><code>TH7yVRLDtWoBkemNupVKusgNCEzHccnqP7</code><button type="button" class="copy-btn" data-copy="TH7yVRLDtWoBkemNupVKusgNCEzHccnqP7">Copy</button></div>
    </div>

    <div class="crypto-option">
      <div class="crypto-top"><?= $usdtIcon ?><div class="crypto-meta"><b>USDT</b><span>BEP-20 &middot; BNB Smart Chain</span></div></div>
      <div class="crypto-qr"><img src="/assets/tokens/usdt(bsc) (2).jpg" alt="USDT BEP-20 payment QR code" width="120" height="120" loading="lazy"></div>
      <div class="crypto-address"><code>0xaB270D8d31C2fBE1fE0B5D2E9A974c44AA821c10</code><button type="button" class="copy-btn" data-copy="0xaB270D8d31C2fBE1fE0B5D2E9A974c44AA821c10">Copy</button></div>
    </div>

    <div class="crypto-option">
      <div class="crypto-top"><?= $usdcIcon ?><div class="crypto-meta"><b>USDC</b><span>ERC-20 &middot; Ethereum</span></div></div>
      <div class="crypto-qr"><img src="/assets/tokens/usdc(Ethereum) (2).jpg" alt="USDC Ethereum payment QR code" width="120" height="120" loading="lazy"></div>
      <div class="crypto-address"><code>0xaB270D8d31C2fBE1fE0B5D2E9A974c44AA821c10</code><button type="button" class="copy-btn" data-copy="0xaB270D8d31C2fBE1fE0B5D2E9A974c44AA821c10">Copy</button></div>
    </div>
  </div>

  <div class="crypto-warn"><strong>Double-check before sending.</strong> Send only the token shown, on the network shown, to the matching address. Sending a different token or using the wrong network can result in permanent loss of funds. Crypto payments are final and cannot be reversed.</div>

  <?php if (cfg('crypto_guide_url')): ?>
  <div class="pay-video">
    <div class="pay-video-text"><b>New to crypto?</b><p>Not sure how to get USDT or USDC? Watch our short guide on buying crypto with Coinbase, then come back to complete your order.</p></div>
    <a class="btn pay-video-btn" href="<?= e(cfg('crypto_guide_url')) ?>" target="_blank" rel="noopener noreferrer">
      <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M23 7.5a3 3 0 00-2.1-2.1C19 5 12 5 12 5s-7 0-8.9.4A3 3 0 001 7.5 31 31 0 00.6 12 31 31 0 001 16.5a3 3 0 002.1 2.1C5 19 12 19 12 19s7 0 8.9-.4a3 3 0 002.1-2.1A31 31 0 0023.4 12 31 31 0 0023 7.5zM9.8 15.3V8.7l5.7 3.3-5.7 3.3z"/></svg>
      Watch: How to buy USDT / USDC on Coinbase
    </a>
  </div>
  <?php endif ?>

  <h4 class="crypto-sub">Confirm your payment</h4>
  <div class="crypto-notes">
    <label>Paying with<select name="pay_method">
      <option value="USDT-ERC20">USDT &mdash; ERC-20 (Ethereum)</option>
      <option value="USDT-TRC20">USDT &mdash; TRC-20 (Tron)</option>
      <option value="USDT-BEP20">USDT &mdash; BEP-20 (BNB Smart Chain)</option>
      <option value="USDC-ERC20">USDC &mdash; ERC-20 (Ethereum)</option>
    </select></label>
    <label>Amount sent<input type="text" name="amount_sent" value="<?= $o('amount_sent') ?>" placeholder="e.g. 250.00 USDT"></label>
    <label class="span2">Transaction hash (TXID)<input type="text" name="tx_hash" value="<?= $o('tx_hash') ?>" placeholder="0x… (Ethereum / BSC) or Tron transaction ID"></label>
  </div>

  <button class="btn block" style="margin-top:20px">Place order</button></form>
  <aside class="summary"><h3>Your order</h3>
    <?php foreach ($items as $i): ?><div class="sl"><span><?= e($i['name']) ?> × <?= $i['qty'] ?></span><b><?= money($i['line']) ?></b></div><?php endforeach ?>
    <div class="sl"><span>Shipping</span><b><?= $t['shipping'] ? money($t['shipping']) : 'Free' ?></b></div><div class="sl grand"><span>Total</span><b><?= money($t['total']) ?></b></div>
    <p class="pay-total-note">Send exactly <strong><?= money($t['total']) ?></strong> in crypto &mdash; see the payment options on the left.</p></aside></div></div>
