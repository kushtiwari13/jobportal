<?php
require_once __DIR__ . '/includes/header.php';
$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: ' . base_url() . 'admin/'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $stmt = db()->prepare('UPDATE jobs SET is_active = 0 WHERE id = :id');
    $stmt->execute([':id' => $id]);
    flash('success','Job soft-deleted.');
    header('Location: ' . base_url() . 'admin/');
    exit;
}
?>
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card"><div class="card-body">
      <h4 class="mb-3">Confirm Delete</h4>
      <p>Are you sure you want to delete job #<?php echo $id; ?>? This will deactivate the job (soft delete).</p>
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <button class="btn btn-danger">Yes, Delete</button>
        <a class="btn btn-outline-secondary" href="<?php echo e(base_url()); ?>admin/">Cancel</a>
      </form>
    </div></div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</main>
</body>
</html>
