<?php
require_once __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $titleV = trim($_POST['title'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $experience = trim($_POST['experience'] ?? '');
    $skills = trim($_POST['skills'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $apply_link = trim($_POST['apply_link'] ?? '');
    $expires_at = trim($_POST['expires_at'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $is_approved = isset($_POST['is_approved']) ? 1 : 0;

    $errors = [];
    if ($titleV === '' || mb_strlen($titleV) > 255) $errors['title'] = 'Title is required (max 255).';
    if ($company === '' || mb_strlen($company) > 255) $errors['company'] = 'Company is required (max 255).';
    if ($location && mb_strlen($location) > 100) $errors['location'] = 'Location too long (max 100).';
    if ($experience && mb_strlen($experience) > 50) $errors['experience'] = 'Experience too long (max 50).';
    if ($skills && mb_strlen($skills) > 255) $errors['skills'] = 'Skills too long (max 255).';
    if ($apply_link && !valid_url($apply_link)) $errors['apply_link'] = 'Provide a valid URL.';
    if ($expires_at && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $expires_at)) $errors['expires_at'] = 'Invalid date (YYYY-MM-DD).';
    if ($description === '') $errors['description'] = 'Description is required.';

    if (!$errors) {
        $stmt = db()->prepare('INSERT INTO jobs (title, company, location, experience, skills, description, apply_link, expires_at, is_active, is_approved) VALUES (:title, :company, :location, :experience, :skills, :description, :apply_link, :expires_at, :is_active, :is_approved)');
        $stmt->execute([
            ':title' => $titleV,
            ':company' => $company,
            ':location' => $location,
            ':experience' => $experience,
            ':skills' => $skills,
            ':description' => $description,
            ':apply_link' => $apply_link ?: null,
            ':expires_at' => $expires_at ?: null,
            ':is_active' => $is_active,
            ':is_approved' => $is_approved,
        ]);
        flash('success', 'Job added successfully.');
        header('Location: ' . base_url() . 'admin/');
        exit;
    } else {
        flash('error', 'Please fix the errors and try again.');
    }
}
?>
<div class="row">
  <div class="col-lg-8">
    <div class="card"><div class="card-body">
      <h4 class="mb-3">Add New Job</h4>
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">Title</label><input class="form-control" name="title" required></div>
          <div class="col-md-6"><label class="form-label">Company</label><input class="form-control" name="company" required></div>
          <div class="col-md-6"><label class="form-label">Location</label><input class="form-control" name="location"></div>
          <div class="col-md-6"><label class="form-label">Experience</label><input class="form-control" name="experience"></div>
          <div class="col-12"><label class="form-label">Skills</label><input class="form-control" name="skills" placeholder="PHP, MySQL"></div>
          <div class="col-12"><label class="form-label">Apply Link</label><input class="form-control" name="apply_link" type="url"></div>
          <div class="col-md-6"><label class="form-label">Expiry Date</label><input class="form-control" name="expires_at" type="date"></div>
          <div class="col-12"><label class="form-label">Description</label><textarea class="form-control" rows="6" name="description" required></textarea></div>
          <div class="col-12 form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" checked> <label class="form-check-label">Active</label></div>
          <div class="col-12 form-check form-switch"><input class="form-check-input" type="checkbox" name="is_approved" checked> <label class="form-check-label">Approved</label></div>
        </div>
        <div class="mt-3 d-flex gap-2"><button class="btn btn-primary">Save</button><a class="btn btn-outline-secondary" href="<?php echo e(base_url()); ?>admin/">Cancel</a></div>
      </form>
    </div></div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</main>
</body>
</html>
