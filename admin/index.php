<?php
require_once __DIR__ . '/includes/header.php';

// Summary cards
$total = (int) db()->query("SELECT COUNT(*) FROM jobs")->fetchColumn();
$active = (int) db()->query("SELECT COUNT(*) FROM jobs WHERE is_active = 1 AND (expires_at IS NULL OR expires_at >= CURDATE())")->fetchColumn();
$expired = (int) db()->query("SELECT COUNT(*) FROM jobs WHERE expires_at IS NOT NULL AND expires_at < CURDATE()")->fetchColumn();
// Pending not used in blog mode

// Search & sort
$q = trim($_GET['q'] ?? '');
$sort = $_GET['sort'] ?? 'created_at_desc';
$order = match($sort){
  'title_asc' => 'title ASC',
  'title_desc' => 'title DESC',
  'created_at_asc' => 'created_at ASC',
  default => 'created_at DESC',
};

$whereParts = [];
$params = [];
$terms = array_values(array_filter(preg_split('/\s+/', $q)));
foreach ($terms as $i => $term) {
  $placeholders = [];
  foreach (['title','company','location','experience','description'] as $field) {
    $ph = ":{$field}{$i}";
    $placeholders[] = "$field LIKE $ph";
    $params[$ph] = "%$term%";
  }
  $whereParts[] = '(' . implode(' OR ', $placeholders) . ')';
}
$where = $whereParts ? ('WHERE ' . implode(' AND ', $whereParts)) : '';

$stmt = db()->prepare("SELECT * FROM jobs $where ORDER BY $order LIMIT 100");
$stmt->execute($params);
$rows = $stmt->fetchAll();
?>

<div class="admin-grid">
  <div class="admin-main">
    <div class="admin-metrics">
      <div class="metric-card">
        <span class="small-text">Total Jobs</span>
        <strong><?php echo $total; ?></strong>
      </div>
      <div class="metric-card">
        <span class="small-text">Active</span>
        <strong><?php echo $active; ?></strong>
      </div>
      <div class="metric-card">
        <span class="small-text">Expired</span>
        <strong><?php echo $expired; ?></strong>
      </div>
    </div>

    <div class="panel">
      <div class="panel-head">
        <form class="admin-filter" method="get">
          <input type="text" name="q" placeholder="Search by title, company, location, skill…" value="<?php echo e($q); ?>">
          <select name="sort">
            <option value="created_at_desc" <?php echo $sort==='created_at_desc'?'selected':''; ?>>Newest</option>
            <option value="created_at_asc" <?php echo $sort==='created_at_asc'?'selected':''; ?>>Oldest</option>
            <option value="title_asc" <?php echo $sort==='title_asc'?'selected':''; ?>>Title A-Z</option>
            <option value="title_desc" <?php echo $sort==='title_desc'?'selected':''; ?>>Title Z-A</option>
          </select>
          <button type="submit">Search</button>
        </form>
        <a class="btn-link" href="<?php echo e(base_url()); ?>admin/add-job.php">Add new job</a>
      </div>

      <div class="table-responsive">
        <table class="table align-middle table-striped admin-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Title</th>
              <th>Company</th>
              <th>Location</th>
              <th>Active</th>
              <th>Expires</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!$rows): ?>
              <tr><td colspan="7" class="text-center text-muted">No results.</td></tr>
            <?php else: ?>
              <?php foreach ($rows as $r): ?>
                <tr>
                  <td><?php echo (int)$r['id']; ?></td>
                  <td><?php echo e($r['title']); ?></td>
                  <td><?php echo e($r['company']); ?></td>
                  <td><?php echo e($r['location'] ?: 'Remote'); ?></td>
                  <td><?php echo $r['is_active'] ? 'Yes' : 'No'; ?></td>
                  <td><?php echo e($r['expires_at'] ?: '—'); ?></td>
                  <td class="text-nowrap">
                    <a class="mini-link" target="_blank" href="<?php echo e(base_url()); ?>job/<?php echo (int)$r['id']; ?>/<?php echo e(slugify($r['title'])); ?>">View</a>
                    <a class="mini-link" href="<?php echo e(base_url()); ?>admin/edit-job.php?id=<?php echo (int)$r['id']; ?>">Edit</a>
                    <a class="mini-link text-danger" href="<?php echo e(base_url()); ?>admin/delete-job.php?id=<?php echo (int)$r['id']; ?>">Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <aside class="admin-side">
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>
  </aside>
</div>

<?php require __DIR__ . '/includes/footer.php';
