<div class="container narrow">
  <h1 class="page-title">Laboratory Tests</h1>
  <p>Every product sold by Nexus Pharma is tested by independent third-party laboratories to verify purity, potency, and authenticity. Below are our latest test reports from certified laboratories.</p>

  <div class="lab-grid-large">
    <?php
    $labImages = [
      ['img' => '/assets/img/Test-Report-97103-300x300.png', 'report' => 'Test Report #97103', 'lab' => 'Janoshik', 'date' => '15 Aug 2026'],
      ['img' => '/assets/img/Test-Report-97105-300x300.png', 'report' => 'Test Report #97105', 'lab' => 'Janoshik', 'date' => '25 Aug 2026'],
      ['img' => '/assets/img/Test-Report-97108-300x300.png', 'report' => 'Test Report #97108', 'lab' => 'Janoshik', 'date' => '15 Aug 2026'],
      ['img' => '/assets/img/Test-Report-97111-300x300.png', 'report' => 'Test Report #97111', 'lab' => 'Janoshik', 'date' => '19 Aug 2026'],
      ['img' => '/assets/img/Test-Report-97112-300x300.png', 'report' => 'Test Report #97112', 'lab' => 'Janoshik', 'date' => '15 Aug 2026'],
      ['img' => '/assets/img/Test-Report-109559-300x300.png', 'report' => 'Test Report #109559', 'lab' => 'Janoshik', 'date' => '24 Aug 2026'],
    ];
    foreach ($labImages as $test): ?>
      <div class="lab-report-card">
        <img src="<?= e($test['img']) ?>" alt="<?= e($test['report']) ?>" class="lab-report-img">
        <div class="lab-report-info">
          <h3><?= e($test['report']) ?></h3>
          <p><strong>Lab:</strong> <?= e($test['lab']) ?></p>
          <p><strong>Date:</strong> <?= e($test['date']) ?></p>
          <a href="/assets/img/<?= basename($test['img']) ?>" target="_blank" class="btn sm">View Full Report</a>
        </div>
      </div>
    <?php endforeach ?>
  </div>

  <div class="lab-info">
    <h2>About Our Testing Process</h2>
    <p>At Nexus Pharma, we work with certified independent laboratories to test every batch of our products before they reach our customers. Our testing process includes:</p>
    <ul>
      <li><strong>Identity Testing</strong> — Confirming the product is what it claims to be</li>
      <li><strong>Purity Testing</strong> — Ensuring no contaminants or impurities</li>
      <li><strong>Potency Testing</strong> — Verifying the active ingredient concentration matches label claims</li>
      <li><strong>Sterility Testing</strong> — Confirming products are free from microbial contamination</li>
    </ul>
    <p style="margin-top:20px;color:#666">All test reports are available for customer review. Contact us at <a href="mailto:info@nexuspharma.to">info@nexuspharma.to</a> for full lab reports or certificates of analysis.</p>
  </div>
</div>
