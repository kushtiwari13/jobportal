<?php
require_once __DIR__ . '/includes/header.php';

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
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $uploadedImage = $_FILES['image'] ?? null;

    $errors = [];
    if ($titleV === '' || mb_strlen($titleV) > 255) $errors['title'] = 'Title is required (max 255).';
    if ($company === '' || mb_strlen($company) > 255) $errors['company'] = 'Company is required (max 255).';
    if ($location && mb_strlen($location) > 100) $errors['location'] = 'Location too long (max 100).';
    if ($salary && mb_strlen($salary) > 100) $errors['salary'] = 'Salary too long (max 100).';
    if ($experience && mb_strlen($experience) > 50) $errors['experience'] = 'Experience too long (max 50).';
    if ($apply_link && !valid_url($apply_link)) $errors['apply_link'] = 'Provide a valid URL.';
    if ($expires_at && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $expires_at)) $errors['expires_at'] = 'Invalid date (YYYY-MM-DD).';
    if ($description === '') $errors['description'] = 'Description is required.';

    $imagePath = null;
    if ($uploadedImage && !empty($uploadedImage['tmp_name'])) {
        [$imgPath, $imgError] = handle_image_upload($uploadedImage);
        if ($imgError) {
            $errors['image'] = $imgError;
        } else {
            $imagePath = $imgPath;
        }
    }

    if (!$errors) {
        $stmt = db()->prepare('INSERT INTO jobs (title, company, location, salary, experience, description, apply_link, image_path, expires_at, is_active) VALUES (:title, :company, :location, :salary, :experience, :description, :apply_link, :image_path, :expires_at, :is_active)');
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
        ]);
        flash('success', 'Job added successfully.');
        header('Location: ' . base_url() . 'admin/');
        exit;
    } else {
        flash('error', 'Please fix the errors and try again.');
    }
}
?>
<div class="panel">
  <div class="panel-head">
    <h4 class="m-0">Add New Job</h4>
    <a class="btn-link" href="<?php echo e(base_url()); ?>admin/">Back to dashboard</a>
  </div>
  <form method="post" class="form-grid" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
    <label>Title<span class="req">*</span><input class="form-control" name="title" required></label>
    <label>Company<span class="req">*</span><input class="form-control" name="company" required></label>
    <label>Location<input class="form-control" name="location" placeholder="City / Remote"></label>
    <label>Salary<input class="form-control" name="salary" placeholder="₹ / $ range"></label>
    <label>Experience<input class="form-control" name="experience" placeholder="e.g., 3-5 years"></label>
    <label>Apply Link<input class="form-control" name="apply_link" type="url" placeholder="https://"></label>
    <label>Cover Image<input class="form-control" name="image" type="file" accept="image/*"></label>
    <label>Expiry Date<input class="form-control" name="expires_at" type="date"></label>
    <label class="full-row">Description<span class="req">*</span><textarea class="form-control" rows="6" name="description" required></textarea></label>
    <label class="switch-row"><input class="form-check-input" type="checkbox" name="is_active" checked> Active</label>
    <div class="form-actions full-row">
      <button class="btn btn-primary">Save</button>
      <a class="btn btn-outline-secondary" href="<?php echo e(base_url()); ?>admin/">Cancel</a>
    </div>
  </form>
</div>
<?php require __DIR__ . '/includes/footer.php';
