<?php
$title = 'Post a Job — ProJobs';
require __DIR__ . '/includes/header.php';

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
        $stmt = db()->prepare('INSERT INTO jobs (title, company, location, experience, skills, description, apply_link, expires_at, is_active, is_approved) VALUES (:title, :company, :location, :experience, :skills, :description, :apply_link, :expires_at, 1, 0)');
        $stmt->execute([
            ':title' => $titleV,
            ':company' => $company,
            ':location' => $location,
            ':experience' => $experience,
            ':skills' => $skills,
            ':description' => $description,
            ':apply_link' => $apply_link ?: null,
            ':expires_at' => $expires_at ?: null,
        ]);

        // Attempt to notify admin (configure in php.ini / hosting)
        @mail('admin@example.com', 'New job submission pending approval', "A new job was submitted: $titleV at $company");

        flash('success', 'Thanks! Your job was submitted for review.');
        header('Location: ' . base_url());
        exit;
    } else {
        flash('error', 'Please fix the errors and try again.');
    }
}
?>

<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card shadow-sm rounded-3">
      <div class="card-body">
        <h3 class="mb-3">Post a Job</h3>
        <form method="post" novalidate>
          <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>"/>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Job Title</label>
              <input type="text" class="form-control <?php echo isset($errors['title'])?'is-invalid':''; ?>" name="title" value="<?php echo e($_POST['title'] ?? ''); ?>">
              <?php if (!empty($errors['title'])): ?><div class="invalid-feedback"><?php echo e($errors['title']); ?></div><?php endif; ?>
            </div>
            <div class="col-md-6">
              <label class="form-label">Company</label>
              <input type="text" class="form-control <?php echo isset($errors['company'])?'is-invalid':''; ?>" name="company" value="<?php echo e($_POST['company'] ?? ''); ?>">
              <?php if (!empty($errors['company'])): ?><div class="invalid-feedback"><?php echo e($errors['company']); ?></div><?php endif; ?>
            </div>
            <div class="col-md-6">
              <label class="form-label">Location</label>
              <input type="text" class="form-control <?php echo isset($errors['location'])?'is-invalid':''; ?>" name="location" value="<?php echo e($_POST['location'] ?? ''); ?>" placeholder="City or Remote">
              <?php if (!empty($errors['location'])): ?><div class="invalid-feedback"><?php echo e($errors['location']); ?></div><?php endif; ?>
            </div>
            <div class="col-md-6">
              <label class="form-label">Experience</label>
              <input type="text" class="form-control <?php echo isset($errors['experience'])?'is-invalid':''; ?>" name="experience" value="<?php echo e($_POST['experience'] ?? ''); ?>" placeholder="e.g. 0-2 years">
              <?php if (!empty($errors['experience'])): ?><div class="invalid-feedback"><?php echo e($errors['experience']); ?></div><?php endif; ?>
            </div>
            <div class="col-12">
              <label class="form-label">Skills (comma separated)</label>
              <input type="text" class="form-control <?php echo isset($errors['skills'])?'is-invalid':''; ?>" name="skills" value="<?php echo e($_POST['skills'] ?? ''); ?>" placeholder="PHP, MySQL, Bootstrap">
              <?php if (!empty($errors['skills'])): ?><div class="invalid-feedback"><?php echo e($errors['skills']); ?></div><?php endif; ?>
            </div>
            <div class="col-12">
              <label class="form-label">Apply Link (URL)</label>
              <input type="url" class="form-control <?php echo isset($errors['apply_link'])?'is-invalid':''; ?>" name="apply_link" value="<?php echo e($_POST['apply_link'] ?? ''); ?>" placeholder="https://...">
              <?php if (!empty($errors['apply_link'])): ?><div class="invalid-feedback"><?php echo e($errors['apply_link']); ?></div><?php endif; ?>
            </div>
            <div class="col-md-6">
              <label class="form-label">Expiry Date</label>
              <input type="date" class="form-control <?php echo isset($errors['expires_at'])?'is-invalid':''; ?>" name="expires_at" value="<?php echo e($_POST['expires_at'] ?? ''); ?>">
              <?php if (!empty($errors['expires_at'])): ?><div class="invalid-feedback"><?php echo e($errors['expires_at']); ?></div><?php endif; ?>
            </div>
            <div class="col-12">
              <label class="form-label">Job Description</label>
              <textarea class="form-control <?php echo isset($errors['description'])?'is-invalid':''; ?>" name="description" rows="6"><?php echo e($_POST['description'] ?? ''); ?></textarea>
              <?php if (!empty($errors['description'])): ?><div class="invalid-feedback"><?php echo e($errors['description']); ?></div><?php endif; ?>
            </div>
          </div>
          <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary rounded-pill" type="submit">Submit for Review</button>
            <a class="btn btn-outline-secondary rounded-pill" href="/">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php';
