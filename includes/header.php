<?php require_once __DIR__ . '/db.php'; require_once __DIR__ . '/functions.php'; ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($title ?? app_name()); ?></title>
    <meta name="description" content="<?php echo e($meta_description ?? 'Find and post the latest tech jobs.'); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo e(base_url()); ?>assets/css/style.css" rel="stylesheet">
  </head>
  <body class="bg-light">
    <header class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top shadow-sm">
      <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="<?php echo e(base_url()); ?>"><?php echo e(app_name()); ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link" href="<?php echo e(base_url()); ?>">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo e(base_url()); ?>post-job.php">Post Job</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo e(base_url()); ?>about.php">About</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo e(base_url()); ?>contact.php">Contact</a></li>
          </ul>
        </div>
      </div>
    </header>
    <main class="py-4">
      <div class="container" style="max-width: 1200px;">
        <?php if ($msg = flash('success')): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i><?php echo e($msg); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>
        <?php if ($msg = flash('error')): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i><?php echo e($msg); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>
