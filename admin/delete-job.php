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
<div class="panel">
  <div class="panel-head">
    <h4 class="m-0">Confirm delete</h4>
  </div>
  <p>Delete job #<?php echo $id; ?>? This will deactivate the job (soft delete).</p>
  <form method="post" class="d-flex gap-2">
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
    <button class="btn btn-danger">Yes, delete</button>
    <a class="btn btn-outline-secondary" href="<?php echo e(base_url()); ?>admin/">Cancel</a>
  </form>
</div>
<?php require __DIR__ . '/includes/footer.php';
