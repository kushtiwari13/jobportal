<?php
$title = 'Job Details — ProJobs';
require __DIR__ . '/includes/header.php';

// Support both /job.php?id= and SEO /job/{id}/{slug}
$id = (int)($_GET['id'] ?? 0);
if (!$id && preg_match('~^/job/(\d+)~', $_SERVER['REQUEST_URI'] ?? '', $m)) {
    $id = (int) $m[1];
}

$job = $id ? find_job($id) : null;
if (!$job || !$job['is_active']) {
    http_response_code(404);
    echo '<div class="container py-5 text-center text-muted">Job not found.</div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}
?>

<div class="layout-grid article-grid">
  <article class="story">
    <p class="eyebrow"><?php echo e($job['company']); ?> • <?php echo e($job['location'] ?: 'Remote'); ?> • Posted <?php echo date('M j, Y', strtotime($job['created_at'])); ?></p>
    <h1 class="story-title"><?php echo e($job['title']); ?></h1>
    <div class="story-image-wrap">
      <img class="story-image" src="<?php echo e(job_image_url($job)); ?>" alt="<?php echo e($job['title']); ?>">
    </div>
    <div class="meta-row story-meta">
      <span class="meta-item"><?php echo e($job['salary'] ?: 'Salary: Not disclosed'); ?></span>
      <?php if (!empty($job['experience'])): ?>
        <span class="meta-divider">•</span>
        <span class="meta-item"><?php echo e($job['experience']); ?></span>
      <?php endif; ?>
      <?php if (!empty($job['apply_link']) && valid_url($job['apply_link'])): ?>
        <span class="meta-divider">•</span>
        <a class="text-link" href="<?php echo e($job['apply_link']); ?>" target="_blank" rel="noopener">Company site</a>
      <?php endif; ?>
    </div>
    <div class="story-body">
      <?php echo format_job_description($job['description']); ?>
    </div>
    <?php if (!empty($job['apply_link']) && valid_url($job['apply_link'])): ?>
      <a class="apply-link" href="<?php echo e($job['apply_link']); ?>" target="_blank" rel="noopener">Apply at source</a>
    <?php endif; ?>
  </article>
  <aside class="sidebar">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
  </aside>
</div>

<?php require __DIR__ . '/includes/footer.php';
