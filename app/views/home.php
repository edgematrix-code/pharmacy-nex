<section class="hero"><div class="container">
  <div class="hero-copy">
    <p class="hero-kicker">USA DOMESTIC</p>
    <h1>Say Hello To Your New Favorite Shop</h1>
    <p class="hero-sub">Looking for Fat Loss, Muscle Growth, Faster Recovery, or Anti-Aging Benefits?</p>
    <a class="hero-btn" href="/shop">Shop Now</a>
  </div>
  <div class="hero-art hero-carousel" aria-label="Featured products">
    <?php if (!empty($hero)): ?>
      <?php foreach ($hero as $i => $hp): ?>
      <a class="hero-slide <?= $i === 0 ? 'on' : '' ?>" href="/product/<?= e($hp['slug']) ?>">
        <img src="<?= img_url($hp) ?>" alt="<?= e($hp['name']) ?>" <?= $i === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>>
      </a>
      <?php endforeach ?>
      <div class="hero-dots" aria-hidden="true"><?php foreach ($hero as $i => $hp): ?><span class="<?= $i === 0 ? 'on' : '' ?>"></span><?php endforeach ?></div>
    <?php else: ?>
      <span class="hero-slide on"><img src="/assets/img/logo.png" alt="Nexus Pharma" fetchpriority="high"></span>
    <?php endif ?>
  </div>
</div></section>
<section class="container features">
  <div class="feature-card"><?= icon('lab') ?><h3>Laboratory Tested</h3>
    <p>Every batch is laboratory tested. That way you know exactly what you're getting.</p></div>
  <div class="feature-card"><?= icon('truck') ?><h3>Fast Shipping</h3>
    <p>USA Domestic Store. Orders arrive within 5-7 business days and we automatically provide a tracking number to follow.</p></div>
  <div class="feature-card"><?= icon('bulk') ?><h3>Bulk Pricing</h3>
    <p>Buy More - Save More! We offer bulk pricing for every product!</p>
    <a href="/shop">Check it out!</a></div>
</section>
<section class="container new-labs">
  <h2>New Labs - August</h2>
  <div class="tr-carousel nl-carousel">
    <button class="tr-nav prev" type="button" aria-label="Previous reports"><?= icon('chev') ?></button>
    <div class="tr-track">
      <?php
      $newLabs = [
        ['task' => '#223947', 'received' => "25 AUG '26", 'sample' => 'Test C', 'analysis' => '25 AUG 2026', 'compound' => 'Testosterone Cypionate', 'result' => '295.86 mg/ml', 'key' => 'EFY6SNUAC058'],
        ['task' => '#223943', 'received' => "25 AUG '26", 'sample' => 'Primo', 'analysis' => '25 AUG 2026', 'compound' => 'Methenolone Enanthate', 'result' => '100.64 mg/ml', 'key' => '2M7H3H3HE7'],
        ['task' => '#223950', 'received' => "19 AUG '26", 'sample' => 'Mast E', 'analysis' => '24 AUG 2026', 'compound' => 'Drostanolone Enanthate', 'result' => '195.52 mg/ml', 'key' => 'A8A6ENP8QMP'],
      ];
      foreach ($newLabs as $r): ?>
      <div class="nl-card">
        <div class="nl-head"><span class="nl-brand">TEST REPORT</span>
          <div class="nl-lab">JANOSHIK<br><span>Email: info@janoshik.com<br>Web: www.janoshik.com</span></div></div>
        <div class="nl-meta"><span>Task Number <strong><?= e($r['task']) ?></strong></span><span>Testing ordered <strong>15 AUG '26</strong></span><span>Sample received <strong><?= e($r['received']) ?></strong></span></div>
        <div class="nl-rows">
          <div><span>Client</span><strong>NexusPharma.to</strong></div>
          <div><span>Sample</span><span><?= e($r['sample']) ?></span></div>
          <div><span>Manufacturer</span><span>NEXUS PHARMA</span></div>
          <div><span>Batch</span><span>&nbsp;</span></div>
        </div>
        <div class="nl-sec"><span>Sample description ></span><div class="nl-box">Pictures not included.</div></div>
        <div class="nl-sec"><span>Tests requested ></span><div class="nl-box">Blind common anabolic steroid screening - oils</div></div>
        <div class="nl-results">
          <div class="nl-resleft"><span>Results ></span>
            <table class="nl-table"><tr><td><?= e($r['compound']) ?></td><td><?= e($r['result']) ?></td></tr></table></div>
          <svg class="nl-qr" viewBox="0 0 50 50" role="img" aria-label="QR code"><rect width="50" height="50" fill="#fff"/><g fill="#111"><rect x="4" y="4" width="11" height="11"/><rect x="35" y="4" width="11" height="11"/><rect x="4" y="35" width="11" height="11"/><rect x="20" y="6" width="5" height="5"/><rect x="29" y="10" width="4" height="4"/><rect x="20" y="21" width="10" height="10"/><rect x="36" y="22" width="8" height="6"/><rect x="20" y="36" width="6" height="8"/><rect x="33" y="36" width="6" height="6"/></g></svg>
        </div>
        <div class="nl-sec"><span>Comments ></span><div class="nl-box"></div></div>
        <div class="nl-foot"><div>Analysis conducted > <strong><?= e($r['analysis']) ?></strong></div><div>Signature > <span class="nl-sign">[Signature]</span></div></div>
        <div class="nl-verify">Verify this test at www.janoshik.com/verify/ with the following unique key<div class="nl-key"><?= e($r['key']) ?></div></div>
      </div>
      <?php endforeach ?>
    </div>
    <button class="tr-nav next" type="button" aria-label="Next reports"><?= icon('chev') ?></button>
  </div>
  <div class="nl-dots" aria-hidden="true"><span></span><span></span><span class="on"></span><span></span><span></span></div>
</section>

<section class="container who">
  <div class="who-dots" aria-hidden="true"><span></span><span></span><span></span><span></span><span class="on"></span></div>
  <div class="who-grid">
    <div class="who-text">
      <h2>Who We Are</h2>
      <p>Nexus Pharma is a United States-based company dedicated to providing high-quality, rigorously tested products for athletes. With years of experience in the industry, we have established a reliable supply chain and implement strict testing protocols to ensure product integrity and authenticity.</p>
      <p>Additionally, we prioritize customer privacy and confidentiality, maintaining discretion from the point of purchase to delivery.</p>
      <div class="who-sub">
        <p class="kicker">TOP QUALITY</p>
        <h3>Everything<br>You Need !</h3>
        <p class="purity">100% Purity Tested !</p>
        <a class="who-btn" href="/shop">Explore Now</a>
      </div>
    </div>
    <div class="who-art"><img src="/assets/img/logo.png" alt="Nexus Pharma Logo"></div>
  </div>
</section>


<section class="container cat-tiles"><h2 class="sec">Shop by category</h2><div>
<?php foreach (categories() as $c): ?><a href="/product-category/<?= e($c['slug']) ?>" style="--c:<?= e($c['color']) ?>"><span></span><?= e($c['name']) ?></a><?php endforeach ?></div></section>
<section class="container"><h2 class="sec">Featured Products</h2><ul class="grid cols-4"><?php foreach ($feat as $p) echo product_card($p); ?></ul>
<p class="center"><a class="btn" href="/shop">View all products</a></p></section>

<section class="container top-rated">
  <div class="tr-head"><h2>Top Rated Products</h2><a href="/shop">See All Products</a></div>
  <div class="tr-carousel">
    <button class="tr-nav prev" type="button" aria-label="Previous products"><?= icon('chev') ?></button>
    <div class="tr-track">
      <?php foreach ($top as $p): $t = tiers((float)$p['price']); ?>
      <div class="tr-card">
        <div class="tr-media"><span class="tr-badge">Laboratory Tested</span>
          <a class="tr-img" href="/product/<?= e($p['slug']) ?>"><img src="<?= img_url($p) ?>" alt="<?= e($p['name']) ?>" loading="lazy"></a></div>
        <h3><a href="/product/<?= e($p['slug']) ?>"><?= e($p['name']) ?></a></h3>
        <form method="post" action="/cart/add" class="buy"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $p['id'] ?>"><input type="hidden" name="back" value="<?= e($_SERVER['REQUEST_URI']) ?>">
          <div class="tier-select" data-base="<?= $p['price'] ?>"><select aria-label="Bulk pricing" class="tiers"><?php foreach ($t as $x): ?><option value="<?= $x['min'] ?>"><?= e($x['label']) ?> &nbsp; <?= money($x['price']) ?></option><?php endforeach ?></select></div>
          <div class="tr-actions">
            <div class="qty"><button type="button" data-d="-1">−</button><input type="number" name="qty" value="1" min="1" max="99"><button type="button" data-d="1">+</button></div>
            <span class="price" data-price><?= money((float)$p['price']) ?></span>
            <button class="btn-add" aria-label="Add to cart"><?= icon('cart') ?></button>
          </div>
        </form>
      </div>
      <?php endforeach ?>
    </div>
    <button class="tr-nav next" type="button" aria-label="Next products"><?= icon('chev') ?></button>
  </div>
</section>

<div class="promo-banner"><a href="/shop">CLICK TO GET 5% OFF</a> <button class="promo-close" type="button" aria-label="Close">&times;</button></div>
