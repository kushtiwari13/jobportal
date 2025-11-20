<?php
$title = 'Latest Jobs — ProJobs';
$meta_description = 'Browse the latest active and approved job openings. Filter by title, location, and skills.';
require __DIR__ . '/includes/header.php';

// Build search
$q = trim($_GET['q'] ?? '');
$loc = trim($_GET['location'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));

$where = [];
$params = [];
if ($q !== '') {
    $terms = array_values(array_filter(preg_split('/\s+/', $q)));
    foreach ($terms as $i => $term) {
        $placeholders = [];
        foreach (['title','company','location','experience','description'] as $field) {
            $ph = ":{$field}{$i}";
            $placeholders[] = "$field LIKE $ph";
            $params[$ph] = "%$term%";
        }
        $where[] = '(' . implode(' OR ', $placeholders) . ')';
    }
}
if ($loc !== '') { $where[] = 'location LIKE :loc'; $params[':loc'] = "%$loc%"; }

$total = count_jobs(implode(' AND ', $where), $params);
[$limit, $offset, $pages, $page] = paginate(10, $total, $page);
$jobs = search_jobs(implode(' AND ', $where), $params, $limit, $offset);
?>

<section class="lead-block">
  <div class="eyebrow">Fresh IT openings</div>
  <h1 class="page-title">Fast, newspaper-style feed of active roles</h1>
  <p class="page-subhead">Short, scannable listings built for speed and clean readability.</p>
  <form class="filter-bar auto-search" method="get" action="<?php echo e(base_url()); ?>#jobs">
    <input type="text" name="q" value="" placeholder="Keyword, skill, or company">
    <input type="text" name="location" value="<?php echo e($loc); ?>" placeholder="Location or Remote">
    <button type="submit">Update feed</button>
  </form>
</section>

<section class="layout-grid" id="jobs">
  <div class="stream-column">
    <?php if (!$jobs): ?>
      <div class="empty-state">No jobs found. Try a broader keyword or remove filters.</div>
    <?php else: ?>
      <div class="post-stream">
        <?php foreach ($jobs as $job): ?>
          <article class="post-card">
            <a class="post-thumb" href="<?php echo e(base_url()); ?>job/<?php echo (int)$job['id']; ?>/<?php echo e(slugify($job['title'])); ?>">
              <img src="<?php echo e(job_image_url($job)); ?>" alt="<?php echo e($job['title']); ?>">
            </a>
            <div class="post-body">
              <div class="post-meta-top">
                <span class="meta-item"><?php echo e($job['company']); ?></span>
                <span class="meta-divider">•</span>
                <span class="meta-item"><?php echo e($job['location'] ?: 'Remote'); ?></span>
                <span class="meta-divider">•</span>
                <span class="meta-item"><?php echo e($job['salary'] ?: 'Not disclosed'); ?></span>
                <?php if (!empty($job['experience'])): ?>
                  <span class="meta-divider">•</span>
                  <span class="meta-item"><?php echo e($job['experience']); ?></span>
                <?php endif; ?>
                <span class="meta-divider">•</span>
              </div>
              <h2 class="post-title"><a href="<?php echo e(base_url()); ?>job/<?php echo (int)$job['id']; ?>/<?php echo e(slugify($job['title'])); ?>"><?php echo e($job['title']); ?></a></h2>
              <p class="post-snippet"><?php echo e(job_snippet($job['description'], 200)); ?></p>
              <div class="post-actions">
                <a class="btn-link-ghost" href="<?php echo e(base_url()); ?>job/<?php echo (int)$job['id']; ?>/<?php echo e(slugify($job['title'])); ?>">Read post</a>
                <?php if (!empty($job['apply_link']) && valid_url($job['apply_link'])): ?>
                  <a class="btn-link-primary" href="<?php echo e($job['apply_link']); ?>" target="_blank" rel="noopener">Apply</a>
                <?php endif; ?>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>

      <nav class="pager" aria-label="Job pagination">
        <?php $qs = $_GET; ?>
        <a class="pager-link <?php echo $page<=1?'disabled':''; ?>" href="<?php $qs['page']=$page-1; echo '?' . http_build_query($qs); ?>">Prev</a>
        <span class="pager-status"><?php echo $page; ?> / <?php echo $pages; ?></span>
        <a class="pager-link <?php echo $page>=$pages?'disabled':''; ?>" href="<?php $qs['page']=$page+1; echo '?' . http_build_query($qs); ?>">Next</a>
      </nav>
    <?php endif; ?>
  </div>
  <aside class="sidebar">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
  </aside>
</section>

<?php require __DIR__ . '/includes/footer.php';
