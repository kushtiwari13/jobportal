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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo e(base_url()); ?>assets/css/theme.css" rel="stylesheet">
    <link href="<?php echo e(base_url()); ?>assets/css/style.css" rel="stylesheet">
  </head>
  <body class="news-body">
    <?php $header_q = trim($_GET['q'] ?? ''); $header_loc = trim($_GET['location'] ?? ''); ?>
    <header class="masthead">
      <div class="page-container">
        <div class="masthead-row">
          <a class="brand-mark" href="<?php echo e(base_url()); ?>"><?php echo e(app_name()); ?></a>
          <form class="masthead-search auto-search" method="get" action="<?php echo e(base_url()); ?>">
            <input type="text" name="q" value="<?php echo e($header_q); ?>" placeholder="Search roles, tech, or companies" aria-label="Search jobs">
            <input type="text" name="location" value="<?php echo e($header_loc); ?>" placeholder="Location or Remote" aria-label="Filter by location">
            <button type="submit">Search</button>
          </form>
          <nav class="category-links" aria-label="Top categories">
            <?php foreach (['Developer','Testing','DevOps','Data','Security','Mobile','Cloud'] as $cat): ?>
              <a href="<?php echo e(base_url()); ?>?q=<?php echo urlencode($cat); ?>"><?php echo e($cat); ?></a>
            <?php endforeach; ?>
          </nav>
        </div>
      </div>
    </header>
    <main class="page-shell">
      <div class="page-container">
        <?php if ($msg = flash('success')): ?>
          <div class="notice notice-success" role="alert"><?php echo e($msg); ?></div>
        <?php endif; ?>
        <?php if ($msg = flash('error')): ?>
          <div class="notice notice-error" role="alert"><?php echo e($msg); ?></div>
        <?php endif; ?>
