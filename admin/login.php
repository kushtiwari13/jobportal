<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!empty($_SESSION['admin_user_id'])) {
    header('Location: ' . base_url() . 'admin/');
    exit;
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = db()->prepare('SELECT id, email, password_hash FROM admin_users WHERE email = :email');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_user_id'] = (int)$user['id'];
        $_SESSION['admin_email'] = $user['email'];
        flash('success', 'Welcome back!');
        header('Location: ' . base_url() . 'admin/');
        exit;
    } else {
        $error = 'Invalid email or password.';
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login — <?php echo e(app_name()); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo e(base_url()); ?>assets/css/style.css" rel="stylesheet">
  </head>
  <body class="bg-light d-flex align-items-center" style="min-height:100vh;">
    <div class="container" style="max-width:420px;">
      <div class="card shadow-sm rounded-3">
        <div class="card-body p-4">
          <h4 class="mb-3 text-center">Admin Login</h4>
          <?php if ($error): ?><div class="alert alert-danger p-2 py-2 small mb-3"><?php echo e($error); ?></div><?php endif; ?>
          <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <button class="btn btn-primary w-100 rounded-pill" type="submit">Login</button>
          </form>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
 </html>
