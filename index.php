<?php
$title = 'Latest Jobs — ProJobs';
$meta_description = 'Browse the latest active and approved job openings. Filter by title, location, and skills.';
require __DIR__ . '/includes/header.php';

// Build search
$q = trim($_GET['q'] ?? '');
$loc = trim($_GET['location'] ?? '');
$skills = trim($_GET['skills'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));

$where = [];
$params = [];
if ($q !== '') { $where[] = '(title LIKE :q OR company LIKE :q)'; $params[':q'] = "%$q%"; }
if ($loc !== '') { $where[] = 'location LIKE :loc'; $params[':loc'] = "%$loc%"; }
if ($skills !== '') { $where[] = 'skills LIKE :skills'; $params[':skills'] = "%$skills%"; }

$total = count_jobs(implode(' AND ', $where), $params);
[$limit, $offset, $pages, $page] = paginate(10, $total, $page);
$jobs = search_jobs(implode(' AND ', $where), $params, $limit, $offset);
?>

<section class="pb-3">
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card shadow-sm rounded-3 mb-3">
        <div class="card-body">
          <form class="row g-2 align-items-end" method="get" action="<?php echo e(base_url()); ?>">
            <div class="col-md-5">
              <label class="form-label">Job title or company</label>
              <input type="text" class="form-control" name="q" value="<?php echo e($q); ?>" placeholder="e.g. PHP Developer">
            </div>
            <div class="col-md-3">
              <label class="form-label">Location</label>
              <input type="text" class="form-control" name="location" value="<?php echo e($loc); ?>" placeholder="City or Remote">
            </div>
            <div class="col-md-3">
              <label class="form-label">Skills</label>
              <input type="text" class="form-control" name="skills" value="<?php echo e($skills); ?>" placeholder="e.g. MySQL, Laravel">
            </div>
            <div class="col-md-1 d-grid">
              <button class="btn btn-primary rounded-pill" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
          </form>
        </div>
      </div>

      <?php if (!$jobs): ?>
        <div class="text-center text-muted py-5">No jobs found.</div>
      <?php else: ?>
        <?php foreach ($jobs as $job): ?>
          <div class="card shadow-sm rounded-3 mb-3">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <h5 class="job-card-title mb-1"><a class="text-decoration-none text-dark" href="<?php echo e(base_url()); ?>job/<?php echo (int)$job['id']; ?>/<?php echo e(slugify($job['title'])); ?>"><?php echo e($job['title']); ?></a></h5>
                  <div class="text-muted-2 small mb-2"><?php echo e($job['company']); ?> • <?php echo e($job['location']); ?> • <?php echo date('M j, Y', strtotime($job['created_at'])); ?></div>
                  <?php if (!empty($job['skills'])): ?>
                    <?php foreach (explode(',', $job['skills']) as $sk): $sk = trim($sk); if (!$sk) continue; ?>
                      <span class="badge badge-skill me-1 mb-1"><?php echo e($sk); ?></span>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
                <div>
                  <a class="btn btn-sm btn-primary rounded-pill" href="<?php echo e(base_url()); ?>job/<?php echo (int)$job['id']; ?>/<?php echo e(slugify($job['title'])); ?>">View</a>
                </div>
              </div>
              <p class="mt-3 mb-0 text-muted"><?php echo e($job['snippet']); ?>...</p>
            </div>
          </div>
        <?php endforeach; ?>

        <nav aria-label="Job pagination">
          <ul class="pagination">
            <?php $qs = $_GET; ?>
            <li class="page-item <?php echo $page<=1?'disabled':''; ?>">
              <?php $qs['page']=$page-1; ?>
              <a class="page-link" href="?<?php echo http_build_query($qs); ?>">Prev</a>
            </li>
            <?php for ($i=1;$i<=$pages;$i++): $qs['page']=$i; ?>
              <li class="page-item <?php echo $i===$page?'active':''; ?>"><a class="page-link" href="?<?php echo http_build_query($qs); ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
            <li class="page-item <?php echo $page>=$pages?'disabled':''; ?>">
              <?php $qs['page']=$page+1; ?>
              <a class="page-link" href="?<?php echo http_build_query($qs); ?>">Next</a>
            </li>
          </ul>
        </nav>
      <?php endif; ?>
    </div>
    <div class="col-lg-4">
      <?php include __DIR__ . '/includes/sidebar.php'; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php';
