<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

// Summary cards
$total = (int) db()->query("SELECT COUNT(*) FROM jobs")->fetchColumn();
$active = (int) db()->query("SELECT COUNT(*) FROM jobs WHERE is_active = 1 AND (expires_at IS NULL OR expires_at >= CURDATE())")->fetchColumn();
$expired = (int) db()->query("SELECT COUNT(*) FROM jobs WHERE expires_at IS NOT NULL AND expires_at < CURDATE()")->fetchColumn();
$pending = (int) db()->query("SELECT COUNT(*) FROM jobs WHERE is_approved = 0")->fetchColumn();

// Search & sort
$q = trim($_GET['q'] ?? '');
$sort = $_GET['sort'] ?? 'created_at_desc';
$order = match($sort){
  'title_asc' => 'title ASC',
  'title_desc' => 'title DESC',
  'created_at_asc' => 'created_at ASC',
  default => 'created_at DESC',
};

$where = '';
$params = [];
if ($q) { $where = 'WHERE title LIKE :q OR company LIKE :q'; $params[':q'] = "%$q%"; }

$stmt = db()->prepare("SELECT * FROM jobs $where ORDER BY $order LIMIT 100");
$stmt->execute($params);
$rows = $stmt->fetchAll();
?>

<div class="mb-4">
  <div class="row g-3">
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="small text-muted">Total Jobs</div><div class="h4 m-0"><?php echo $total; ?></div></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="small text-muted">Active</div><div class="h4 m-0"><?php echo $active; ?></div></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="small text-muted">Expired</div><div class="h4 m-0"><?php echo $expired; ?></div></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="small text-muted">Pending</div><div class="h4 m-0"><?php echo $pending; ?></div></div></div></div>
  </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
  <form class="d-flex gap-2" method="get">
    <input type="text" name="q" class="form-control" placeholder="Search jobs" value="<?php echo e($q); ?>"/>
    <select name="sort" class="form-select">
      <option value="created_at_desc" <?php echo $sort==='created_at_desc'?'selected':''; ?>>Newest</option>
      <option value="created_at_asc" <?php echo $sort==='created_at_asc'?'selected':''; ?>>Oldest</option>
      <option value="title_asc" <?php echo $sort==='title_asc'?'selected':''; ?>>Title A-Z</option>
      <option value="title_desc" <?php echo $sort==='title_desc'?'selected':''; ?>>Title Z-A</option>
    </select>
    <button class="btn btn-primary">Search</button>
  </form>
  <a class="btn btn-success" href="<?php echo e(base_url()); ?>admin/add-job.php"><i class="fa-solid fa-plus me-1"></i>Add New Job</a>
  </div>

<div class="table-responsive">
  <table class="table align-middle table-striped">
    <thead><tr>
      <th>ID</th><th>Title</th><th>Company</th><th>Location</th><th>Active</th><th>Approved</th><th>Expires</th><th>Actions</th>
    </tr></thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?php echo (int)$r['id']; ?></td>
        <td><?php echo e($r['title']); ?></td>
        <td><?php echo e($r['company']); ?></td>
        <td><?php echo e($r['location']); ?></td>
        <td><?php echo $r['is_active'] ? 'Yes' : 'No'; ?></td>
        <td><?php echo $r['is_approved'] ? 'Yes' : 'No'; ?></td>
        <td><?php echo e($r['expires_at']); ?></td>
        <td class="text-nowrap">
          <a class="btn btn-sm btn-outline-primary" target="_blank" href="<?php echo e(base_url()); ?>job/<?php echo (int)$r['id']; ?>/<?php echo e(slugify($r['title'])); ?>">View</a>
          <a class="btn btn-sm btn-outline-secondary" href="<?php echo e(base_url()); ?>admin/edit-job.php?id=<?php echo (int)$r['id']; ?>">Edit</a>
          <a class="btn btn-sm btn-outline-danger" href="<?php echo e(base_url()); ?>admin/delete-job.php?id=<?php echo (int)$r['id']; ?>">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</main>
</body>
</html>
