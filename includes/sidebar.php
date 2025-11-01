<aside>
  <div class="card shadow-sm rounded-3 mb-4">
    <div class="card-header bg-white border-0 pb-0">
      <h6 class="m-0 fw-semibold text-primary">Latest Jobs</h6>
    </div>
    <div class="card-body">
      <ul class="list-unstyled small mb-0 latest-jobs">
        <?php foreach (latest_jobs(5) as $j): ?>
          <li class="mb-2"><a class="text-decoration-none" href="<?php echo e(base_url()); ?>job/<?php echo (int)$j['id']; ?>/<?php echo e(slugify($j['title'])); ?>">
            <span class="d-block fw-semibold text-dark"><?php echo e($j['title']); ?></span>
            <span class="text-muted"><?php echo e($j['company']); ?> • <?php echo e($j['location']); ?></span>
          </a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</aside>
