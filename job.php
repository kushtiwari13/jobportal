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

// Handle report job
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report'])) {
    verify_csrf();
    $reason = trim($_POST['reason'] ?? '');
    $stmt = db()->prepare('INSERT INTO reports (job_id, reason) VALUES (:job_id, :reason)');
    $stmt->execute([':job_id' => $job['id'], ':reason' => $reason]);
    flash('success', 'Thanks! Your report has been submitted.');
    header('Location: ' . base_url() . 'job/' . (int)$job['id'] . '/' . slugify($job['title']));
    exit;
}
?>

<div class="row g-4">
  <div class="col-lg-8">
    <div class="card shadow-sm rounded-3 mb-3">
      <div class="card-body">
        <h2 class="mb-1"><?php echo e($job['title']); ?></h2>
        <div class="text-muted-2 mb-3"><?php echo e($job['company']); ?> • <?php echo e($job['location']); ?> • Posted <?php echo date('M j, Y', strtotime($job['created_at'])); ?></div>
        <?php if (!empty($job['skills'])): ?>
          <div class="mb-3">
            <?php foreach (explode(',', $job['skills']) as $sk): $sk = trim($sk); if (!$sk) continue; ?>
              <span class="badge badge-skill me-1 mb-1"><?php echo e($sk); ?></span>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <div class="mb-4">
          <?php echo nl2br(e($job['description'])); ?>
        </div>
        <div class="d-flex flex-wrap gap-2">
          <?php if (!empty($job['apply_link']) && valid_url($job['apply_link'])): ?>
            <a class="btn btn-primary rounded-pill" href="<?php echo e($job['apply_link']); ?>" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Apply Now</a>
          <?php endif; ?>
          <a class="btn btn-outline-secondary rounded-pill" href="https://wa.me/?text=<?php echo urlencode($job['title'].' - '.base_url().'job/'.$job['id'].'/'.slugify($job['title'])); ?>" target="_blank"><i class="fa-brands fa-whatsapp me-1"></i> Share</a>
          <a class="btn btn-outline-secondary rounded-pill" href="https://t.me/share/url?url=<?php echo urlencode(base_url().'job/'.$job['id'].'/'.slugify($job['title'])); ?>&text=<?php echo urlencode($job['title']); ?>" target="_blank"><i class="fa-brands fa-telegram me-1"></i> Share</a>
        </div>
      </div>
    </div>

    <div class="card shadow-sm rounded-3">
      <div class="card-body">
        <h6 class="mb-3">Report this job</h6>
        <form method="post">
          <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
          <textarea class="form-control mb-2" name="reason" rows="3" placeholder="Reason (optional)"></textarea>
          <button class="btn btn-outline-danger rounded-pill" type="submit" name="report" value="1"><i class="fa-solid fa-flag me-1"></i> Report Job</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php';
