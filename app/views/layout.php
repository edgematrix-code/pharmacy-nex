<?php $cats = categories(); $n = cart_count(); $w = count(wishlist()); $nav = ['/' => 'Homepage', '/shop' => 'Shop', '/faq' => 'FAQ', '/contact-us' => 'Contact Us', '/laboratory-tests' => 'Laboratory Tests'];
$cur = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/') ?: '/'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> – <?= e(cfg('site_name')) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="icon" href="/assets/img/logo.png" type="image/png">
<link rel="stylesheet" href="/assets/css/style.css?v=<?= @filemtime(__DIR__ . '/../../public/assets/css/style.css') ?: time() ?>">
</head>
<body class="<?= e($bodyClass) ?>">
<div id="topbar"><nav>
  <a href="/tracking-order"><?= icon('pin') ?>Track Order</a>
  <a href="/faq"><?= icon('help') ?>Help Center</a>
  <a href="/wishlist"><?= icon('heart') ?>Wishlist</a>
  <a href="https://t.me/nexuspharmaceutical" target="_blank" rel="noopener noreferrer" aria-label="Telegram"><?= icon('telegram') ?>Telegram</a>
  <a href="https://www.facebook.com/profile.php?id=61593389735475" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><?= icon('facebook') ?>Facebook</a>
</nav></div>

<header id="site-header">
  <div class="container hdr-main">
    <a class="logo" href="/"><img src="/assets/img/logo-small.png" alt="Nexus Pharma" style="max-width:120px"></a>
    <form class="search" method="get" action="/shop">
      <label class="search-cat"><select name="cat"><option value="0">All Categories</option><?php foreach ($cats as $c): ?><option value="<?= e($c['slug']) ?>"><?= e($c['name']) ?></option><?php endforeach ?></select><?= icon('chev') ?></label>
      <input type="text" name="s" placeholder="Search for anything" autocomplete="off">
      <button aria-label="Search"><?= icon('search') ?></button>
    </form>
    <div class="hdr-icons">
      <a href="/wishlist" class="hi" title="Wishlist"><?= icon('heart') ?><?php if ($w): ?><i><?= $w ?></i><?php endif ?></a>
      <?php if ($me = current_user()): ?>
      <a href="/account" class="hi" title="My account"><?= icon('user') ?><span><?= e(explode(' ', trim($me['name']))[0] ?: 'Account') ?></span></a>
      <?php else: ?>
      <a href="/login" class="hi" title="Sign in"><?= icon('user') ?><span>Sign in</span></a>
      <a href="/signup" class="hi" title="Create account"><span>Sign up</span></a>
      <?php endif ?>
      <a href="/cart" class="hi"><?= icon('cart') ?><span>Cart</span><i><?= $n ?></i></a>
    </div>
    <button class="burger" aria-label="Menu" onclick="document.body.classList.toggle('nav-open')"><?= icon('menu') ?></button>
  </div>
  <div class="container hdr-nav">
    <div class="cat-menu"><button>Shop by Categories <?= icon('chev') ?></button>
      <ul><?php foreach ($cats as $c): ?><li><a href="/product-category/<?= e($c['slug']) ?>"><?= e($c['name']) ?></a></li><?php endforeach ?></ul></div>
    <nav class="primary"><?php foreach ($nav as $h => $l): ?><a href="<?= $h ?>" class="<?= $cur === $h ? 'on' : '' ?>"><?= e($l) ?></a><?php endforeach ?></nav>
    <a class="track" href="/tracking-order">Track Order</a>
  </div>
  <div class="infobar"><div class="container">
    <span><?= icon('truck') ?>Fast domestic shipping</span><span><?= icon('shield') ?>Secure checkout</span>
    <span><?= icon('gift') ?>Bulk product discounts</span><span><?= icon('mail') ?><?= e(cfg('email')) ?></span></div></div>
</header>

<?php if (!empty($flash)): ?><div class="toast" id="toast"><?= e($flash) ?> <a href="/cart">View cart</a></div><?php endif ?>
<main id="content"><?= $content ?></main>

<footer id="site-footer"><div class="container">
  <div class="f-brand"><img src="/assets/img/logo.png" alt="Nexus Pharma" class="f-logo"><p>Every product batch tested and verified. Delivered fast.</p></div>
  <div><h4>Get to Know Us</h4><a href="/about-us">About Us</a><a href="/faq">FAQ</a><a href="/contact-us">Contact Us</a><a href="/laboratory-tests">Laboratory Tests</a><a href="/admin">Admin</a></div>
  <div><h4>Let’s keep in touch</h4><a href="mailto:<?= e(cfg('email')) ?>"><?= e(cfg('email')) ?></a>
    <div class="f-social">
      <a href="https://t.me/nexuspharmaceutical" target="_blank" rel="noopener noreferrer" title="Telegram" aria-label="Telegram"><?= icon('telegram') ?><span>Telegram</span></a>
      <a href="https://www.facebook.com/profile.php?id=61593389735475" target="_blank" rel="noopener noreferrer" title="Facebook" aria-label="Facebook"><?= icon('facebook') ?><span>Facebook</span></a>
    </div>
    <p class="small">Copyright © <?= date('Y') ?>, All rights reserved.</p></div>
  <p class="disclaimer">These statements have not been evaluated as medical advice. Always read the label and use as directed. If symptoms persist, consult a healthcare professional.</p>
</div></footer>
<script src="/assets/js/app.js?v=<?= @filemtime(__DIR__ . '/../../public/assets/js/app.js') ?: time() ?>"></script>
</body></html>
