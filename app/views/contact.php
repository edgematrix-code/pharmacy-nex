<div class="container narrow"><h1 class="page-title">Contact Nexus Pharma</h1>
<div class="contact-social">
  <a href="https://t.me/nexuspharmaceutical" target="_blank" rel="noopener noreferrer" aria-label="Telegram"><?= icon('telegram') ?>Telegram</a>
  <a href="https://www.facebook.com/profile.php?id=61593389735475" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><?= icon('facebook') ?>Facebook</a>
</div>
<?php if ($sent): ?><div class="ok">Thanks! Your message was sent. We’ll reply by email soon.</div><?php else: ?>
<?php if ($err): ?><div class="alert"><?= e($err) ?></div><?php endif ?>
<form method="post" class="form"><?= csrf_field() ?><div class="two"><label>Name<input name="name" required></label><label>Email<input type="email" name="email" required></label></div><label>Message<textarea name="message" rows="6" required></textarea></label><button class="btn">Send message</button></form><?php endif ?></div>
