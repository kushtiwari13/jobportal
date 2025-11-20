<?php
require_once __DIR__ . '/includes/header.php';
$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: ' . base_url() . 'admin/'); exit; }

$stmt = db()->prepare('SELECT * FROM jobs WHERE id = :id');
$stmt->execute([':id' => $id]);
$job = $stmt->fetch();
if (!$job) { flash('error','Job not found.'); header('Location: ' . base_url() . 'admin/'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $titleV = trim($_POST['title'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $salary = trim($_POST['salary'] ?? '');
    $experience = trim($_POST['experience'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $apply_link = trim($_POST['apply_link'] ?? '');
    $expires_at = trim($_POST['expires_at'] ?? '');
    $uploadedImage = $_FILES['image'] ?? null;
    $remove_image = isset($_POST['remove_image']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $errors = [];
    if ($titleV === '' || mb_strlen($titleV) > 255) $errors['title'] = 'Title is required (max 255).';
    if ($company === '' || mb_strlen($company) > 255) $errors['company'] = 'Company is required (max 255).';
    if ($location && mb_strlen($location) > 100) $errors['location'] = 'Location too long (max 100).';
    if ($salary && mb_strlen($salary) > 100) $errors['salary'] = 'Salary too long (max 100).';
    if ($experience && mb_strlen($experience) > 50) $errors['experience'] = 'Experience too long (max 50).';
    if ($apply_link && !valid_url($apply_link)) $errors['apply_link'] = 'Provide a valid URL.';
    if ($expires_at && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $expires_at)) $errors['expires_at'] = 'Invalid date (YYYY-MM-DD).';
    if ($description === '') $errors['description'] = 'Description is required.';

    $imagePath = $job['image_path'] ?? null;
    if ($uploadedImage && !empty($uploadedImage['tmp_name'])) {
        [$imgPath, $imgError] = handle_image_upload($uploadedImage);
        if ($imgError) {
            $errors['image'] = $imgError;
        } else {
            $imagePath = $imgPath;
        }
    } elseif ($remove_image) {
        $imagePath = null;
    }

    if (!$errors) {
        $stmt = db()->prepare('UPDATE jobs SET title=:title, company=:company, location=:location, salary=:salary, experience=:experience, description=:description, apply_link=:apply_link, image_path=:image_path, expires_at=:expires_at, is_active=:is_active WHERE id=:id');
        $stmt->execute([
            ':title' => $titleV,
            ':company' => $company,
            ':location' => $location,
            ':salary' => $salary ?: null,
            ':experience' => $experience,
            ':description' => $description,
            ':apply_link' => $apply_link ?: null,
            ':image_path' => $imagePath,
            ':expires_at' => $expires_at ?: null,
            ':is_active' => $is_active,
            ':id' => $id,
        ]);
        flash('success', 'Job updated.');
        header('Location: ' . base_url() . 'admin/');
        exit;
    } else {
        flash('error', 'Please fix the errors and try again.');
        $job = array_merge($job, $_POST);
    }
}
?>
<div class="panel">
  <div class="panel-head">
    <h4 class="m-0">Edit Job #<?php echo (int)$job['id']; ?></h4>
    <a class="btn-link" href="<?php echo e(base_url()); ?>admin/">Back to dashboard</a>
  </div>
  <form method="post" class="form-grid" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
    <label>Title<span class="req">*</span><input class="form-control" name="title" value="<?php echo e($job['title']); ?>" required></label>
    <label>Company<span class="req">*</span><input class="form-control" name="company" value="<?php echo e($job['company']); ?>" required></label>
    <label>Location<input class="form-control" name="location" value="<?php echo e($job['location']); ?>"></label>
    <label>Salary<input class="form-control" name="salary" value="<?php echo e($job['salary']); ?>" placeholder="₹ / $ range"></label>
    <label>Experience<input class="form-control" name="experience" value="<?php echo e($job['experience']); ?>"></label>
    <label>Apply Link<input class="form-control" name="apply_link" type="url" value="<?php echo e($job['apply_link']); ?>"></label>
    <label>Cover Image<input class="form-control" name="image" type="file" accept="image/*"></label>
    <?php if (!empty($job['image_path'])): ?>
      <div class="small-text text-muted">Current image: <?php echo e($job['image_path']); ?></div>
      <label class="switch-row"><input class="form-check-input" type="checkbox" name="remove_image"> Remove current image</label>
    <?php endif; ?>
    <label>Expiry Date<input class="form-control" name="expires_at" type="date" value="<?php echo e($job['expires_at']); ?>"></label>
    <label class="full-row">Description<span class="req">*</span><textarea class="form-control" rows="6" name="description"><?php echo e($job['description']); ?></textarea></label>
    <label class="switch-row"><input class="form-check-input" type="checkbox" name="is_active" <?php echo $job['is_active']?'checked':''; ?>> Active</label>
    <div class="form-actions full-row">
      <button class="btn btn-primary">Update</button>
      <a class="btn btn-outline-secondary" href="<?php echo e(base_url()); ?>admin/">Cancel</a>
    </div>
  </form>
</div>
<?php require __DIR__ . '/includes/footer.php';
