<section class="faq-hero"><div class="container center"><h1>Have questions?<br>We have answers!</h1><p>Answers to everything you need to know about <strong>Nexus Pharma</strong> products, shipping and billing.</p></div></section>
<div class="container narrow faq">
<?php foreach ($faq as $group => $qs): ?><h2><?= e($group) ?></h2>
<?php foreach ($qs as [$q, $a]): ?><details><summary><?= e($q) ?></summary><div><?= $a /* trusted static HTML from data.php */ ?></div></details><?php endforeach ?><?php endforeach ?></div>
