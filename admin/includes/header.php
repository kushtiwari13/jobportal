<?php require_once __DIR__ . '/auth.php'; require_admin(); ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin — <?php echo e(app_name()); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo e(base_url()); ?>assets/css/style.css" rel="stylesheet">
  </head>
  <body>
  <nav class="navbar navbar-expand-lg navbar-dark" style="background:#111827">
    <div class="container">
      <a class="navbar-brand" href="<?php echo e(base_url()); ?>admin/">Admin — <?php echo e(app_name()); ?></a>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="<?php echo e(base_url()); ?>" target="_blank">View Site</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo e(base_url()); ?>admin/logout.php">Logout</a></li>
      </ul>
    </div>
  </nav>
  <main class="container py-4" style="max-width: 1200px;">
    <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?php echo e($msg); ?></div><?php endif; ?>
    <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?php echo e($msg); ?></div><?php endif; ?>
