<?php require_once __DIR__ . '/auth.php'; require_admin(); ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin — <?php echo e(app_name()); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo e(base_url()); ?>assets/css/style.css" rel="stylesheet">
  </head>
  <body class="news-body">
  <header class="masthead admin-bar">
    <div class="page-container">
      <div class="admin-bar-row">
        <a class="brand-mark" href="<?php echo e(base_url()); ?>admin/">Admin — <?php echo e(app_name()); ?></a>
        <div class="admin-links">
          <a href="<?php echo e(base_url()); ?>" target="_blank">View site</a>
          <a href="<?php echo e(base_url()); ?>admin/logout.php">Logout</a>
        </div>
      </div>
    </div>
  </header>
  <main class="page-shell admin-shell">
    <div class="page-container">
      <?php if ($msg = flash('success')): ?><div class="notice notice-success"><?php echo e($msg); ?></div><?php endif; ?>
      <?php if ($msg = flash('error')): ?><div class="notice notice-error"><?php echo e($msg); ?></div><?php endif; ?>
